<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Classic Cookies', 'Stuffed Cookies', 'Seasonal Specials', 'Gift Boxes'] as $name) {
            Category::updateOrCreate(
                ['slug' => (string) str($name)->slug()],
                ['name' => $name],
            );
        }
    }
}
