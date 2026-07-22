<?php

namespace Tests\Feature\Order\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_move_a_paid_order_to_the_next_valid_status(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['order_status' => 'pending', 'payment_status' => 'paid']);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/orders/{$order->id}/status", ['order_status' => 'preparing']);

        $response->assertOk()->assertJsonPath('data.order_status', 'preparing');
    }

    public function test_admin_cannot_skip_ahead_to_a_non_adjacent_status(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['order_status' => 'pending', 'payment_status' => 'paid']);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/orders/{$order->id}/status", ['order_status' => 'completed']);

        $response->assertUnprocessable()->assertJsonValidationErrors('order_status');
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'order_status' => 'pending']);
    }

    public function test_a_terminal_status_cannot_be_changed(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['order_status' => 'completed']);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/orders/{$order->id}/status", ['order_status' => 'preparing']);

        $response->assertUnprocessable()->assertJsonValidationErrors('order_status');
    }

    public function test_a_customer_cannot_update_order_status(): void
    {
        $customer = User::factory()->create();
        $order = Order::factory()->create(['order_status' => 'pending']);

        $response = $this->actingAs($customer, 'sanctum')
            ->patchJson("/api/admin/orders/{$order->id}/status", ['order_status' => 'preparing']);

        $response->assertForbidden();
    }

    public function test_an_unpaid_delivery_order_cannot_move_to_preparing(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'order_status' => 'pending',
            'payment_status' => 'unpaid',
            'delivery_method' => 'delivery',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/orders/{$order->id}/status", ['order_status' => 'preparing']);

        $response->assertUnprocessable()->assertJsonValidationErrors('order_status');
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'order_status' => 'pending']);
    }

    public function test_an_unpaid_pickup_order_can_move_to_preparing(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'order_status' => 'pending',
            'payment_status' => 'unpaid',
            'delivery_method' => 'pickup',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/admin/orders/{$order->id}/status", ['order_status' => 'preparing']);

        $response->assertOk()->assertJsonPath('data.order_status', 'preparing');
    }
}
