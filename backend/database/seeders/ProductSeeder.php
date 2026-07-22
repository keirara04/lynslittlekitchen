<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classic = Category::where('slug', 'classic-cookies')->first();
        $stuffed = Category::where('slug', 'stuffed-cookies')->first();
        $seasonal = Category::where('slug', 'seasonal-specials')->first();
        $gifts = Category::where('slug', 'gift-boxes')->first();

        // Preserve existing product IDs (and any order-item relationships) when
        // development catalogue names are replaced with the real products.
        foreach ([
            'original-chocolate-chip' => 'choc-chip-crunch',
            'double-chocolate-fudge' => 'dubai-chewy-cookie',
        ] as $legacySlug => $currentSlug) {
            $legacyProduct = Product::withTrashed()->where('slug', $legacySlug)->first();

            if (! $legacyProduct) {
                continue;
            }

            if (Product::withTrashed()->where('slug', $currentSlug)->exists()) {
                $legacyProduct->delete();

                continue;
            }

            $legacyProduct->update(['slug' => $currentSlug]);
            $legacyProduct->restore();
        }

        // Products with pack-size variants have their base 'price'/'stock' set to the
        // smallest variant (used as the "from RM x" display price on listing cards).
        $products = [
            [
                'category_id' => $classic?->id, 'name' => 'Choc Chip Crunch', 'price' => 18.00, 'stock' => 12,
                'ingredients' => 'Flour, butter, brown sugar, chocolate chunks, eggs, vanilla', 'allergens' => 'Contains gluten, eggs, milk',
                'variants' => [
                    ['label' => '300g (12 pcs)', 'price' => 18.00, 'stock' => 20],
                    ['label' => '500g (20 pcs)', 'price' => 28.00, 'stock' => 15],
                    ['label' => '1kg (40 pcs)', 'price' => 50.00, 'stock' => 10],
                ],
            ],
            [
                'category_id' => $stuffed?->id, 'name' => 'Dubai Chewy Cookie', 'price' => 24.00, 'stock' => 30,
                'ingredients' => 'Flour, cocoa, butter, chocolate, pistachio cream, kataifi, eggs', 'allergens' => 'Contains gluten, eggs, milk, nuts',
            ],
            ['category_id' => $stuffed?->id, 'name' => 'Nutella Stuffed Cookie', 'price' => 24.00, 'stock' => 30, 'ingredients' => 'Flour, butter, sugar, hazelnut spread, eggs', 'allergens' => 'Contains gluten, eggs, milk, nuts'],
            ['category_id' => $stuffed?->id, 'name' => 'Biscoff Stuffed Cookie', 'price' => 24.00, 'stock' => 30, 'ingredients' => 'Flour, butter, sugar, Biscoff spread, eggs', 'allergens' => 'Contains gluten, eggs, milk, soy'],
            ['category_id' => $seasonal?->id, 'name' => 'Raya Kurma Cookie', 'price' => 22.00, 'stock' => 25, 'ingredients' => 'Flour, butter, sugar, date paste, walnuts', 'allergens' => 'Contains gluten, milk, nuts'],
            ['category_id' => $gifts?->id, 'name' => 'Assorted Gift Box (12pcs)', 'price' => 45.00, 'stock' => 15, 'ingredients' => 'Assorted cookies', 'allergens' => 'Contains gluten, eggs, milk, nuts, soy'],
        ];

        foreach ($products as $productData) {
            $variants = $productData['variants'] ?? [];
            unset($productData['variants']);

            $product = Product::updateOrCreate(
                ['slug' => (string) str($productData['name'])->slug()],
                [
                    ...$productData,
                    'description' => 'Freshly baked '.$productData['name'].', made to order in Jasin, Melaka.',
                    'status' => ProductStatus::Active,
                ],
            );

            $variantLabels = array_column($variants, 'label');
            if ($variantLabels === []) {
                $product->variants()->delete();
            } else {
                $product->variants()->whereNotIn('label', $variantLabels)->delete();
            }

            foreach ($variants as $index => $variant) {
                $product->variants()->updateOrCreate(
                    ['label' => $variant['label']],
                    [...$variant, 'sort_order' => $index],
                );
            }
        }
    }
}
