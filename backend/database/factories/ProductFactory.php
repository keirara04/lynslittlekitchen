<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => ucwords($name),
            'slug' => (string) str($name)->slug(),
            'description' => fake()->sentence(12),
            'ingredients' => fake()->words(6, true),
            'allergens' => fake()->randomElement(['Contains gluten, eggs, milk', 'Contains peanuts', 'Contains milk, soy']),
            'price' => fake()->randomFloat(2, 8, 45),
            'stock' => fake()->numberBetween(10, 100),
            'status' => ProductStatus::Active,
        ];
    }
}
