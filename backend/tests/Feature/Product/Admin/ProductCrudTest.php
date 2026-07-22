<?php

namespace Tests\Feature\Product\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_create_a_product(): void
    {
        $response = $this->postJson('/api/admin/products', ['name' => 'Test Cookie']);

        $response->assertUnauthorized();
    }

    public function test_a_customer_cannot_create_a_product(): void
    {
        $customer = User::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->postJson('/api/admin/products', [
            'name' => 'Test Cookie',
            'price' => 10,
            'stock' => 5,
            'status' => 'active',
        ]);

        $response->assertForbidden();
    }

    public function test_an_admin_can_create_a_product(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/products', [
            'name' => 'Admin Cookie',
            'price' => 15.5,
            'stock' => 20,
            'status' => 'active',
        ]);

        $response->assertCreated()->assertJsonPath('data.slug', 'admin-cookie');
        $this->assertDatabaseHas('products', ['name' => 'Admin Cookie']);
    }

    public function test_an_admin_can_update_a_product(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/admin/products/{$product->id}", [
            'stock' => 99,
        ]);

        $response->assertOk()->assertJsonPath('data.stock', 99);
    }

    public function test_an_admin_can_delete_a_product(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/admin/products/{$product->id}");

        $response->assertOk();
        $this->assertSoftDeleted($product);
    }

    public function test_admin_product_list_includes_inactive_products(): void
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->create(['name' => 'Active One', 'status' => 'active']);
        Product::factory()->create(['name' => 'Hidden One', 'status' => 'inactive']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/products');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');
        $this->assertTrue($names->contains('Active One'));
        $this->assertTrue($names->contains('Hidden One'));
    }

    public function test_admin_product_list_can_be_filtered_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->create(['name' => 'Active One', 'status' => 'active']);
        Product::factory()->create(['name' => 'Hidden One', 'status' => 'inactive']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/products?status=inactive');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');
        $this->assertCount(1, $names);
        $this->assertTrue($names->contains('Hidden One'));
    }

    public function test_a_customer_cannot_list_admin_products(): void
    {
        $customer = User::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->getJson('/api/admin/products');

        $response->assertForbidden();
    }

    public function test_an_admin_can_view_an_inactive_product_for_editing(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['status' => 'inactive']);

        $this->actingAs($admin, 'sanctum')
            ->getJson("/api/admin/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.status', 'inactive');
    }

    public function test_a_customer_cannot_view_an_admin_product(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($customer, 'sanctum')
            ->getJson("/api/admin/products/{$product->id}")
            ->assertForbidden();
    }
}
