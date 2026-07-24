<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidOrderItemException;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * @param  array<int, array{product_id: int, product_variant_id?: int|null, quantity: int}>  $items
     * @param  array<string, mixed>  $attributes
     *
     * @throws InsufficientStockException
     * @throws InvalidOrderItemException
     */
    public function create(array $items, array $attributes, ?User $user = null): Order
    {
        return DB::transaction(function () use ($items, $attributes, $user) {
            $productIds = array_column($items, 'product_id');
            $variantIds = array_filter(array_column($items, 'product_variant_id'));

            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
            $variants = $variantIds
                ? ProductVariant::whereIn('id', $variantIds)->lockForUpdate()->get()->keyBy('id')
                : collect();

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    throw new InsufficientStockException("#{$item['product_id']}");
                }

                if (! empty($item['product_variant_id'])) {
                    $variant = $variants->get($item['product_variant_id']);

                    if (! $variant || $variant->product_id !== $product->id) {
                        throw InvalidOrderItemException::variantMismatch($item['product_variant_id'], $item['product_id']);
                    }

                    if ($variant->stock < $item['quantity']) {
                        throw new InsufficientStockException("{$product->name} ({$variant->label})");
                    }
                } elseif ($product->stock < $item['quantity']) {
                    throw new InsufficientStockException($product->name);
                }
            }

            $deliveryFee = 0;
            if (! empty($attributes['delivery_zone_id'])) {
                $deliveryFee = (float) DeliveryZone::findOrFail($attributes['delivery_zone_id'])->price;
            }

            $order = Order::create([
                'user_id' => $user?->id,
                'delivery_zone_id' => $attributes['delivery_zone_id'] ?? null,
                'guest_name' => $attributes['guest_name'] ?? null,
                'guest_phone' => $attributes['guest_phone'] ?? null,
                'guest_email' => $attributes['guest_email'] ?? null,
                'order_reference' => $this->generateReference(),
                'total' => 0,
                'delivery_fee' => $deliveryFee,
                'payment_status' => PaymentStatus::Unpaid,
                'order_status' => OrderStatus::Pending,
                'delivery_method' => $attributes['delivery_method'],
                'delivery_address' => $attributes['delivery_address'] ?? null,
                'delivery_date' => $attributes['delivery_date'],
                'notes' => $attributes['notes'] ?? null,
            ]);

            $total = $deliveryFee;

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                $variant = empty($item['product_variant_id']) ? null : $variants->get($item['product_variant_id']);

                $price = $variant?->price ?? $product->price;
                $subtotal = $price * $item['quantity'];
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'variant_label' => $variant?->label,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                if ($variant) {
                    $variant->decrement('stock', $item['quantity']);
                } else {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            $order->update(['total' => $total]);

            return $order->fresh(['items.product', 'items.productVariant', 'deliveryZone']);
        });
    }

    /**
     * Returns each of the order's items to stock (e.g. when an order is rejected or
     * cancelled after stock was already decremented at creation time).
     */
    public function restock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $items = $order->items()->get(['id', 'product_id', 'product_variant_id', 'quantity']);

            $productIds = $items->pluck('product_id')->all();
            $variantIds = $items->pluck('product_variant_id')->filter()->all();

            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
            $variants = $variantIds
                ? ProductVariant::whereIn('id', $variantIds)->lockForUpdate()->get()->keyBy('id')
                : collect();

            foreach ($items as $item) {
                if ($item->product_variant_id) {
                    $variants->get($item->product_variant_id)?->increment('stock', $item->quantity);
                } else {
                    $products->get($item->product_id)?->increment('stock', $item->quantity);
                }
            }
        });
    }

    private function generateReference(): string
    {
        return 'LLK-'.now()->format('Y').'-'.Str::padLeft((string) random_int(1, 999999), 6, '0');
    }
}
