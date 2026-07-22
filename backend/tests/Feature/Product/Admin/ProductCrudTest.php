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
}
