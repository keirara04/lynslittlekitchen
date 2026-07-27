<?php

namespace Tests\Feature\Order;

use App\Models\DeliveryZone;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
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
            'delivery_date' => now()->addDays(3)->toDateString(),
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

    public function test_a_delivery_date_less_than_three_days_out_is_rejected(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $response = $this->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'pickup',
            'delivery_date' => now()->addDays(2)->toDateString(),
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('delivery_date');
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
            'delivery_date' => now()->addDays(3)->toDateString(),
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
            'delivery_date' => now()->addDays(3)->toDateString(),
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
            'delivery_date' => now()->addDays(3)->toDateString(),
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
            'delivery_date' => now()->addDays(3)->toDateString(),
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
            'delivery_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_an_order_below_the_minimum_amount_is_rejected(): void
    {
        StoreSetting::current()->update(['min_order_amount' => 50]);
        $product = Product::factory()->create(['price' => 10, 'stock' => 5]);

        $response = $this->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'pickup',
            'delivery_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertUnprocessable()->assertJsonPath('message', 'The minimum order amount is RM50.00.');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }

    public function test_an_order_meeting_the_minimum_amount_succeeds(): void
    {
        StoreSetting::current()->update(['min_order_amount' => 50]);
        $product = Product::factory()->create(['price' => 30, 'stock' => 5]);

        $response = $this->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'pickup',
            'delivery_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertCreated();
    }

    public function test_pickup_is_rejected_when_disabled_in_settings(): void
    {
        StoreSetting::current()->update(['allow_pickup' => false]);
        $product = Product::factory()->create(['stock' => 5]);

        $response = $this->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'pickup',
            'delivery_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertUnprocessable()->assertJsonPath('message', 'Pickup is currently unavailable.');
    }

    public function test_delivery_is_rejected_when_disabled_in_settings(): void
    {
        StoreSetting::current()->update(['allow_delivery' => false]);
        $product = Product::factory()->create(['stock' => 5]);
        $zone = DeliveryZone::factory()->create();

        $response = $this->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'delivery',
            'delivery_zone_id' => $zone->id,
            'delivery_address' => 'No. 12, Jalan Anggerik, Jasin, Melaka',
            'delivery_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertUnprocessable()->assertJsonPath('message', 'Delivery is currently unavailable.');
    }

    public function test_a_guest_can_look_up_their_order_by_reference_and_phone(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $create = $this->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0123456789',
            'delivery_method' => 'pickup',
            'delivery_date' => now()->addDays(3)->toDateString(),
        ]);

        $reference = $create->json('data.order_reference');

        $response = $this->getJson("/api/orders/{$reference}?phone=0123456789");

        $response->assertOk()->assertJsonPath('data.order_reference', $reference);
    }
}
