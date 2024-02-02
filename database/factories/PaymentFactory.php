<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $data = ['K Pay','Wave Pay','CB Pay','AYA Pay','KBZ Banking','CB Banking','AYA Bank',];
        return [
            'currency_id' => Currency::get()->random()->id,
            'name' => $data[random_int(0,count($data)-1)],
        ];
    }
}
