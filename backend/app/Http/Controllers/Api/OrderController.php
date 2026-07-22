<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidOrderItemException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
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

        $order = Order::with(['items.product', 'items.productVariant', 'deliveryZone'])
            ->where('order_reference', $reference)
            ->where('guest_phone', $request->string('phone')->value())
            ->firstOrFail();

        return new OrderResource($order);
    }
}
