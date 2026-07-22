<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renames_legacy_products_without_duplicating_the_catalogue(): void
    {
        Product::factory()->create([
            'name' => 'Original Chocolate Chip',
            'slug' => 'original-chocolate-chip',
        ]);
        $legacyDubaiCookie = Product::factory()->create([
            'name' => 'Double Chocolate Fudge',
            'slug' => 'double-chocolate-fudge',
        ]);
        $legacyDubaiCookie->variants()->create([
            'label' => '500g (20 pcs)',
            'price' => 31,
            'stock' => 12,
            'sort_order' => 0,
        ]);

        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $this->assertSame(6, Product::count());
        $this->assertDatabaseHas('products', ['slug' => 'choc-chip-crunch', 'deleted_at' => null]);
        $this->assertDatabaseHas('products', ['slug' => 'dubai-chewy-cookie', 'deleted_at' => null]);
        $this->assertDatabaseMissing('products', ['slug' => 'original-chocolate-chip', 'deleted_at' => null]);
        $this->assertDatabaseMissing('products', ['slug' => 'double-chocolate-fudge', 'deleted_at' => null]);
        $this->assertSame(0, Product::where('slug', 'dubai-chewy-cookie')->firstOrFail()->variants()->count());
    }
}
