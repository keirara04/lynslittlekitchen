<?php

namespace Tests\Feature\Customer\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListCustomersTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_from_the_same_guest_email_are_grouped_into_one_customer(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->create(['guest_email' => 'siti@example.com', 'guest_name' => 'Siti', 'total' => 50]);
        Order::factory()->create(['guest_email' => 'siti@example.com', 'guest_name' => 'Siti', 'total' => 30]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/customers');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertSame(2, $response->json('data.0.orders_count'));
        $this->assertEquals(80, $response->json('data.0.total_spent'));
    }

    public function test_orders_from_a_registered_user_are_grouped_by_user(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create(['name' => 'Ahmad Faizal']);
        Order::factory()->create(['user_id' => $customer->id, 'total' => 40]);
        Order::factory()->create(['user_id' => $customer->id, 'total' => 60]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/customers');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertSame('Ahmad Faizal', $response->json('data.0.name'));
        $this->assertSame(2, $response->json('data.0.orders_count'));
    }

    public function test_search_filters_by_name_email_or_phone(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->create(['guest_name' => 'Nurul Huda', 'guest_email' => 'huda@example.com', 'guest_phone' => '0179998776']);
        Order::factory()->create(['guest_name' => 'Daniel Wong', 'guest_email' => 'daniel@example.com', 'guest_phone' => '0165501122']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/customers?search=huda');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertSame('Nurul Huda', $response->json('data.0.name'));
    }

    public function test_status_filter_separates_active_and_inactive_customers(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->create(['guest_email' => 'recent@example.com', 'created_at' => now()->subDays(5)]);
        Order::factory()->create(['guest_email' => 'stale@example.com', 'created_at' => now()->subDays(120)]);

        $active = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/customers?status=active');
        $inactive = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/customers?status=inactive');

        $active->assertOk();
        $this->assertCount(1, $active->json('data'));
        $this->assertSame('recent@example.com', $active->json('data.0.email'));

        $inactive->assertOk();
        $this->assertCount(1, $inactive->json('data'));
        $this->assertSame('stale@example.com', $inactive->json('data.0.email'));
    }

    public function test_a_non_admin_cannot_list_customers(): void
    {
        $customer = User::factory()->create();
        Order::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->getJson('/api/admin/customers');

        $response->assertForbidden();
    }
}
