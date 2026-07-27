<?php

namespace Tests\Feature\Category\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_create_a_category(): void
    {
        $response = $this->postJson('/api/admin/categories', ['name' => 'Cakes']);

        $response->assertUnauthorized();
    }

    public function test_a_customer_cannot_create_a_category(): void
    {
        $customer = User::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')->postJson('/api/admin/categories', [
            'name' => 'Cakes',
        ]);

        $response->assertForbidden();
    }

    public function test_an_admin_can_list_categories(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Cookies']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/categories');

        $response->assertOk();
        $this->assertTrue(collect($response->json('data'))->pluck('name')->contains('Cookies'));
    }

    public function test_an_admin_can_create_a_category_with_an_auto_generated_slug(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/categories', [
            'name' => 'Signature Cakes',
        ]);

        $response->assertCreated()->assertJsonPath('data.slug', 'signature-cakes');
        $this->assertDatabaseHas('categories', ['name' => 'Signature Cakes', 'slug' => 'signature-cakes']);
    }

    public function test_creating_a_category_with_a_duplicate_slug_gets_a_unique_suffix(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Cookies', 'slug' => 'cookies']);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/categories', [
            'name' => 'Cookies',
        ]);

        $response->assertCreated()->assertJsonPath('data.slug', 'cookies-1');
    }

    public function test_an_admin_can_update_a_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Cookies']);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/admin/categories/{$category->id}", [
            'name' => 'Biscuits',
        ]);

        $response->assertOk()->assertJsonPath('data.name', 'Biscuits');
    }

    public function test_an_admin_can_delete_a_category_without_products(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/admin/categories/{$category->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_an_admin_cannot_delete_a_category_with_products(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/admin/categories/{$category->id}");

        $response->assertUnprocessable();
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_category_validation_rejects_a_duplicate_slug_on_create(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['slug' => 'cookies']);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/categories', [
            'name' => 'Something Else',
            'slug' => 'cookies',
        ]);

        $response->assertUnprocessable();
    }
}
