<?php

namespace Tests\Feature\Order;

use App\Models\DeliveryZone;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_can_place_an_order_and_stock_is_decremented(): void
    {
        $product = Product::factory()->create(['price' => 10, 'stock' => 5]);
        $zone = DeliveryZone::factory()->create(['price' => 4]);

        $response = $this->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'guest_email' => 'jane@example.com',
            'delivery_method' => 'delivery',
            'delivery_zone_id' => $zone->id,
            'delivery_address' => 'No. 12, Jalan Anggerik, Jasin, Melaka',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertCreated();
        $this->assertEquals(24.0, $response->json('data.total'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
        $this->assertDatabaseHas('orders', [
            'order_status' => 'pending',
            'payment_status' => 'unpaid',
            'delivery_address' => 'No. 12, Jalan Anggerik, Jasin, Melaka',
        ]);
    }

    public function test_a_delivery_order_without_an_address_is_rejected(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $zone = DeliveryZone::factory()->create();

        $response = $this->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'delivery',
            'delivery_zone_id' => $zone->id,
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('delivery_address');
    }

    public function test_a_pickup_order_does_not_require_an_address(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $response = $this->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'pickup',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertCreated();
    }

    public function test_a_guest_can_order_a_specific_product_variant(): void
    {
        $product = Product::factory()->create(['price' => 18, 'stock' => 5]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'label' => '1kg (40 pcs)',
            'price' => 50,
            'stock' => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->id, 'product_variant_id' => $variant->id, 'quantity' => 2],
            ],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'pickup',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertCreated();
        $this->assertEquals(100.0, $response->json('data.total'));
        $this->assertEquals('1kg (40 pcs)', $response->json('data.items.0.variant_label'));
        $this->assertDatabaseHas('product_variants', ['id' => $variant->id, 'stock' => 8]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }

    public function test_ordering_a_variant_that_belongs_to_a_different_product_is_rejected(): void
    {
        $product = Product::factory()->create();
        $otherProduct = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $otherProduct->id]);

        $response = $this->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->id, 'product_variant_id' => $variant->id, 'quantity' => 1],
            ],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'pickup',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_ordering_more_than_available_stock_is_rejected(): void
    {
        $product = Product::factory()->create(['price' => 10, 'stock' => 1]);

        $response = $this->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'pickup',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_a_guest_can_look_up_their_order_by_reference_and_phone(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $create = $this->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'pickup',
            'delivery_date' => now()->addDay()->toDateString(),
        ]);

        $reference = $create->json('data.order_reference');

        $response = $this->getJson("/api/orders/{$reference}?phone=0123456789");

        $response->assertOk()->assertJsonPath('data.order_reference', $reference);
    }
}
