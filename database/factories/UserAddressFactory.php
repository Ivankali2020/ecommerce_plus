<?php

namespace Database\Factories;

use App\Models\Region;
use App\Models\Township;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'region_id' => Region::get()->random()->id,
            'township_id' => Township::get()->random()->id,
            'address' => fake()->streetAddress()
        ];
    }
}
