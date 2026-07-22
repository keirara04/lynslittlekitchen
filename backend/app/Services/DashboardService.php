<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
            'low_stock_products' => $this->lowStockProducts(),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function bestSellingProduct(): ?array
    {
        $row = OrderItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
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
     * @return array<int, array{id: int, name: string, stock: int}>
     */
    private function lowStockProducts(int $threshold = 10): array
    {
        return Product::where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->get(['id', 'name', 'stock'])
            ->toArray();
    }
}
