<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'label' => fake()->randomElement(['300g (12 pcs)', '500g (20 pcs)', '1kg (40 pcs)']),
            'price' => fake()->randomFloat(2, 8, 60),
            'stock' => fake()->numberBetween(5, 50),
            'sort_order' => 0,
        ];
    }
}
