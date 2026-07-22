<?php

namespace Tests\Feature\Order\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_guest_fulfilment_details(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'guest_name' => 'Aina Syazwani',
            'guest_phone' => '0123456789',
            'guest_email' => 'aina@example.test',
        ]);

        $this->actingAs($admin, 'sanctum')
            ->getJson("/api/admin/orders/{$order->id}")
            ->assertOk()
            ->assertJsonPath('data.customer.type', 'guest')
            ->assertJsonPath('data.customer.name', 'Aina Syazwani')
            ->assertJsonPath('data.customer.phone', '0123456789')
            ->assertJsonPath('data.customer.email', 'aina@example.test');
    }

    public function test_customer_cannot_view_admin_order_detail(): void
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create();

        $this->actingAs($customer, 'sanctum')
            ->getJson("/api/admin/orders/{$order->id}")
            ->assertForbidden();
    }

    public function test_public_order_tracking_does_not_expose_customer_contact_details(): void
    {
        $order = Order::factory()->create([
            'guest_name' => 'Private Customer',
            'guest_phone' => '0123456789',
            'guest_email' => 'private@example.test',
        ]);

        $this->getJson("/api/orders/{$order->order_reference}?phone=0123456789")
            ->assertOk()
            ->assertJsonMissingPath('data.customer')
            ->assertJsonMissing(['private@example.test']);
    }
}
