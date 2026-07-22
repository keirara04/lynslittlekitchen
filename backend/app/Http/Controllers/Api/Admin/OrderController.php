<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\AdminOrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Order::with(['user', 'items.product', 'items.productVariant', 'deliveryZone'])->latest();

        if ($search = $request->string('search')->trim()->value()) {
            $needle = '%'.mb_strtolower($search).'%';

            $query->where(function ($query) use ($needle) {
                $query->whereRaw('LOWER(order_reference) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(guest_name) LIKE ?', [$needle])
                    ->orWhereHas('user', fn ($user) => $user
                        ->whereRaw('LOWER(name) LIKE ?', [$needle]));
            });
        }

        foreach (['payment_status', 'order_status', 'delivery_method'] as $filter) {
            if ($value = $request->string($filter)->value()) {
                $query->where($filter, $value);
            }
        }

        return AdminOrderResource::collection($query->paginate($request->integer('per_page', 20)));
    }

    public function show(Order $order): AdminOrderResource
    {
        return new AdminOrderResource(
            $order->load(['user', 'items.product', 'items.productVariant', 'deliveryZone'])
        );
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): AdminOrderResource
    {
        $order->update(['order_status' => $request->validated('order_status')]);

        return new AdminOrderResource(
            $order->load(['user', 'items.product', 'items.productVariant', 'deliveryZone'])
        );
    }
}
