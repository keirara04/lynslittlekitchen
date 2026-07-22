<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_best_selling_product_only_counts_paid_orders(): void
    {
        $admin = User::factory()->admin()->create();

        $popularButUnpaid = Product::factory()->create(['name' => 'Unpaid Favourite']);
        $paidProduct = Product::factory()->create(['name' => 'Paid Seller']);

        $unpaidOrder = Order::factory()->create(['payment_status' => 'unpaid']);
        OrderItem::factory()->create(['order_id' => $unpaidOrder->id, 'product_id' => $popularButUnpaid->id, 'quantity' => 100]);

        $paidOrder = Order::factory()->create(['payment_status' => 'paid']);
        OrderItem::factory()->create(['order_id' => $paidOrder->id, 'product_id' => $paidProduct->id, 'quantity' => 1]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/dashboard');

        $response->assertOk()->assertJsonPath('best_selling_product.product', 'Paid Seller');
    }

    public function test_low_stock_alerts_include_variant_level_stock(): void
    {
        $admin = User::factory()->admin()->create();

        $productWithVariants = Product::factory()->create(['name' => 'Chocolate Chip', 'stock' => 999]);
        ProductVariant::factory()->create([
            'product_id' => $productWithVariants->id,
            'label' => '1kg (40 pcs)',
            'stock' => 3,
        ]);

        $productWithoutVariants = Product::factory()->create(['name' => 'Plain Cookie', 'stock' => 2]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/dashboard');

        $response->assertOk();
        $alerts = collect($response->json('low_stock_products'));

        $this->assertTrue($alerts->contains(fn ($a) => $a['product'] === 'Chocolate Chip' && $a['variant_label'] === '1kg (40 pcs)' && $a['stock'] === 3));
        $this->assertTrue($alerts->contains(fn ($a) => $a['product'] === 'Plain Cookie' && $a['variant_label'] === null && $a['stock'] === 2));
        $this->assertFalse($alerts->contains(fn ($a) => $a['product'] === 'Chocolate Chip' && $a['variant_label'] === null));
    }

    public function test_dashboard_includes_order_status_counts_and_recent_orders(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = Order::factory()->create([
            'guest_name' => 'Pending Customer',
            'order_status' => 'pending',
        ]);
        Order::factory()->create([
            'guest_name' => 'Completed Customer',
            'order_status' => 'completed',
        ]);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('orders_by_status.pending', 1)
            ->assertJsonPath('orders_by_status.completed', 1)
            ->assertJsonCount(2, 'recent_orders')
            ->assertJsonFragment([
                'id' => $pending->id,
                'customer_name' => 'Pending Customer',
            ])
            ->assertJsonStructure([
                'recent_orders' => [[
                    'id',
                    'order_reference',
                    'customer_name',
                    'total',
                    'payment_status',
                    'order_status',
                    'created_at',
                ]],
            ]);
    }
}
