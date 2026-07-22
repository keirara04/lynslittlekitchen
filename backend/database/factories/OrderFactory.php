<?php

namespace Database\Factories;

use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\DeliveryZone;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'delivery_zone_id' => DeliveryZone::factory(),
            'guest_name' => fake()->name(),
            'guest_phone' => fake()->numerify('01#########'),
            'guest_email' => fake()->safeEmail(),
            'order_reference' => 'LLK-'.fake()->unique()->numerify('######'),
            'total' => fake()->randomFloat(2, 20, 200),
            'delivery_fee' => fake()->randomFloat(2, 0, 15),
            'payment_status' => PaymentStatus::Unpaid,
            'order_status' => OrderStatus::Pending,
            'delivery_method' => DeliveryMethod::Delivery,
            'delivery_date' => fake()->dateTimeBetween('+1 day', '+7 days'),
        ];
    }
}
