<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Order::with(['items.product', 'items.productVariant', 'deliveryZone'])->latest();

        if ($status = $request->string('order_status')->value()) {
            $query->where('order_status', $status);
        }

        return OrderResource::collection($query->paginate($request->integer('per_page', 20)));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): OrderResource
    {
        $order->update(['order_status' => $request->validated('order_status')]);

        return new OrderResource($order->load(['items.product', 'items.productVariant', 'deliveryZone']));
    }
}
