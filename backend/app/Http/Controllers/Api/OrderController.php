<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidOrderItemException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\StorePaymentProofRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function store(StoreOrderRequest $request): JsonResponse|OrderResource
    {
        try {
            $order = $this->orderService->create(
                items: $request->validated('items'),
                attributes: $request->validated(),
                user: $request->user(),
            );
        } catch (InsufficientStockException|InvalidOrderItemException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $reference): OrderResource
    {
        $request->validate(['phone' => ['required', 'string']]);

        $order = $this->findByReferenceAndPhone($reference, $request->string('phone')->value());

        return new OrderResource($order);
    }

    public function submitProof(StorePaymentProofRequest $request, string $reference): JsonResponse|OrderResource
    {
        $order = $this->findByReferenceAndPhone($reference, $request->validated('phone'));

        if ($order->payment_status === PaymentStatus::Paid) {
            return response()->json(['message' => 'This order is already marked as paid.'], 422);
        }

        $order->update([
            'payment_proof_url' => $request->validated('proof_url'),
            'payment_proof_submitted_at' => now(),
        ]);

        return new OrderResource($order->fresh(['items.product', 'items.productVariant', 'deliveryZone']));
    }

    private function findByReferenceAndPhone(string $reference, string $phone): Order
    {
        return Order::with(['items.product', 'items.productVariant', 'deliveryZone'])
            ->where('order_reference', $reference)
            ->where('guest_phone', $phone)
            ->firstOrFail();
    }
}
