<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        $paidOrders = Order::where('payment_status', PaymentStatus::Paid);

        return [
            'todays_sales' => (float) (clone $paidOrders)->whereDate('created_at', today())->sum('total'),
            'total_orders' => Order::count(),
            'monthly_revenue' => (float) (clone $paidOrders)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
            'best_selling_product' => $this->bestSellingProduct(),
            'low_stock_products' => $this->lowStockAlerts(),
            'orders_by_status' => $this->ordersByStatus(),
            'recent_orders' => $this->recentOrders(),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function ordersByStatus(): array
    {
        $counts = Order::query()
            ->select('order_status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('order_status')
            ->pluck('aggregate', 'order_status');

        return collect(OrderStatus::cases())
            ->mapWithKeys(fn (OrderStatus $status) => [
                $status->value => (int) ($counts[$status->value] ?? 0),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function recentOrders(): array
    {
        return Order::query()
            ->with('user:id,name')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'order_reference' => $order->order_reference,
                'customer_name' => $order->user?->name ?? $order->guest_name ?? 'Guest',
                'total' => (float) $order->total,
                'payment_status' => $order->payment_status->value,
                'order_status' => $order->order_status->value,
                'created_at' => $order->created_at?->toIso8601String(),
            ])
            ->all();
    }

    /**
     * Best seller is based on quantity sold across paid orders only — unpaid/cancelled
     * orders haven't actually resulted in a sale yet.
     *
     * @return array<string, mixed>|null
     */
    private function bestSellingProduct(): ?array
    {
        $row = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', PaymentStatus::Paid)
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('order_items.product_id')
            ->orderByDesc('total_sold')
            ->first();

        if (! $row) {
            return null;
        }

        return [
            'product' => Product::find($row->product_id)?->name,
            'total_sold' => (int) $row->total_sold,
        ];
    }

    /**
     * Products with variants are alerted per-variant (each pack size has its own stock);
     * products without variants fall back to their own base stock.
     *
     * @return array<int, array{product: string, variant_label: string|null, stock: int}>
     */
    private function lowStockAlerts(int $threshold = 10): array
    {
        $variantAlerts = ProductVariant::where('stock', '<=', $threshold)
            ->with('product:id,name')
            ->get()
            ->map(fn (ProductVariant $variant) => [
                'product' => $variant->product->name,
                'variant_label' => $variant->label,
                'stock' => $variant->stock,
            ]);

        $productAlerts = Product::doesntHave('variants')
            ->where('stock', '<=', $threshold)
            ->get(['name', 'stock'])
            ->map(fn (Product $product) => [
                'product' => $product->name,
                'variant_label' => null,
                'stock' => $product->stock,
            ]);

        return $variantAlerts->concat($productAlerts)
            ->sortBy('stock')
            ->values()
            ->all();
    }
}
