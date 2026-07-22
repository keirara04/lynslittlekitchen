<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            'Jasin' => 3.00,
            'Melaka Tengah' => 6.00,
            'Alor Gajah' => 8.00,
            'Outside Melaka' => 15.00,
        ];

        foreach ($zones as $name => $price) {
            DeliveryZone::updateOrCreate(['name' => $name], ['price' => $price]);
        }
    }
}
