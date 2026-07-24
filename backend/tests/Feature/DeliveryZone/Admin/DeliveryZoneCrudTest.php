<?php

namespace Tests\Feature\DeliveryZone\Admin;

use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryZoneCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_create_a_delivery_zone(): void
    {
        $response = $this->postJson('/api/admin/delivery-zones', ['name' => 'Jasin', 'price' => 5]);

        $response->assertUnauthorized();
    }

    public function test_a_customer_cannot_create_a_delivery_zone(): void
    {
        $customer = User::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->postJson('/api/admin/delivery-zones', [
            'name' => 'Jasin',
            'price' => 5,
        ]);

        $response->assertForbidden();
    }

    public function test_an_admin_can_list_delivery_zones(): void
    {
        $admin = User::factory()->admin()->create();
        DeliveryZone::factory()->create(['name' => 'Melaka Tengah', 'price' => 8]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/delivery-zones');

        $response->assertOk();
        $this->assertTrue(collect($response->json('data'))->pluck('name')->contains('Melaka Tengah'));
    }

    public function test_an_admin_can_create_a_delivery_zone(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/delivery-zones', [
            'name' => 'Jasin',
            'price' => 5,
        ]);

        $response->assertCreated()->assertJsonPath('data.name', 'Jasin');
        $this->assertDatabaseHas('delivery_zones', ['name' => 'Jasin']);
    }

    public function test_an_admin_can_update_a_delivery_zone(): void
    {
        $admin = User::factory()->admin()->create();
        $zone = DeliveryZone::factory()->create(['price' => 5]);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/admin/delivery-zones/{$zone->id}", [
            'price' => 12,
        ]);

        $response->assertOk()->assertJsonPath('data.price', 12);
    }

    public function test_an_admin_can_delete_a_delivery_zone(): void
    {
        $admin = User::factory()->admin()->create();
        $zone = DeliveryZone::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/admin/delivery-zones/{$zone->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('delivery_zones', ['id' => $zone->id]);
    }

    public function test_deleting_a_delivery_zone_nulls_it_on_existing_orders(): void
    {
        $admin = User::factory()->admin()->create();
        $zone = DeliveryZone::factory()->create();
        $order = Order::factory()->create(['delivery_zone_id' => $zone->id]);

        $this->actingAs($admin, 'sanctum')->deleteJson("/api/admin/delivery-zones/{$zone->id}")->assertOk();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'delivery_zone_id' => null]);
    }

    public function test_delivery_zone_validation_rejects_negative_price(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/delivery-zones', [
            'name' => 'Jasin',
            'price' => -5,
        ]);

        $response->assertUnprocessable();
    }
}
