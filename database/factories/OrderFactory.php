<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
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
            'payment_id' => Payment::get()->random()->id,
            'user_address_id' => UserAddress::get()->random()->id,
            'sub_total' => random_int(200,100000),
            'delivery_fees' => random_int(300,2000),
            'total_price' => random_int(30000,100000),
            'created_at' => now()->subDays(random_int(0,60))
        ];
    }
}
