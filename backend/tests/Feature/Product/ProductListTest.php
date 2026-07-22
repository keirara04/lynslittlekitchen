<?php

namespace Tests\Feature\Product;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_only_active_products(): void
    {
        Product::factory()->create(['name' => 'Active Cookie', 'status' => ProductStatus::Active]);
        Product::factory()->create(['name' => 'Hidden Cookie', 'status' => ProductStatus::Inactive]);

        $response = $this->getJson('/api/products');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');
        $this->assertTrue($names->contains('Active Cookie'));
        $this->assertFalse($names->contains('Hidden Cookie'));
    }

    public function test_it_filters_by_search_term(): void
    {
        Product::factory()->create(['name' => 'Chocolate Chip Cookie']);
        Product::factory()->create(['name' => 'Vanilla Sable']);

        $response = $this->getJson('/api/products?search=chocolate');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');
        $this->assertCount(1, $names);
        $this->assertTrue($names->contains('Chocolate Chip Cookie'));
    }

    public function test_it_filters_by_category(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'In Category']);
        Product::factory()->create(['name' => 'Other Category']);

        $response = $this->getJson('/api/products?category='.$category->slug);

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');
        $this->assertCount(1, $names);
        $this->assertTrue($names->contains('In Category'));
    }

    public function test_it_shows_a_single_product_by_slug(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson('/api/products/'.$product->slug);

        $response->assertOk()->assertJsonPath('data.slug', $product->slug);
    }
}
