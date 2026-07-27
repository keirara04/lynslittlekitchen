<?php

namespace Tests\Feature\Order\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_orders_by_payment_status_and_delivery_method(): void
    {
        $admin = User::factory()->admin()->create();
        $paidPickup = Order::factory()->create([
            'payment_status' => 'paid',
            'delivery_method' => 'pickup',
            'payment_proof_submitted_at' => now(),
        ]);
        Order::factory()->create([
            'payment_status' => 'unpaid',
            'delivery_method' => 'delivery',
            'payment_proof_submitted_at' => now(),
        ]);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/orders?payment_status=paid&delivery_method=pickup')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $paidPickup->id);
    }

    public function test_admin_can_search_orders_by_guest_name_case_insensitively(): void
    {
        $admin = User::factory()->admin()->create();
        $match = Order::factory()->create(['guest_name' => 'Aina Syazwani', 'payment_proof_submitted_at' => now()]);
        Order::factory()->create(['guest_name' => 'Different Customer', 'payment_proof_submitted_at' => now()]);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/orders?search=AINA')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id)
            ->assertJsonPath('data.0.customer.name', 'Aina Syazwani');
    }

    public function test_admin_can_filter_orders_by_order_status(): void
    {
        $admin = User::factory()->admin()->create();
        $preparing = Order::factory()->create(['order_status' => 'preparing', 'payment_proof_submitted_at' => now()]);
        Order::factory()->create(['order_status' => 'pending', 'payment_proof_submitted_at' => now()]);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/orders?order_status=preparing')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $preparing->id);
    }

    public function test_orders_without_a_submitted_receipt_are_hidden_from_the_admin_list(): void
    {
        $admin = User::factory()->admin()->create();
        $awaitingReceipt = Order::factory()->create(['payment_proof_submitted_at' => null]);
        $withReceipt = Order::factory()->create(['payment_proof_submitted_at' => now()]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/orders');

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($withReceipt->id));
        $this->assertFalse($ids->contains($awaitingReceipt->id));
    }
}
