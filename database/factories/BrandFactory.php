<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $data = ['Nike','Adidas','Purma','Apple','Sansumg','Huwai',''];
        return [
            'name' => $data[random_int(0,count($data)-1)]
        ];
    }
}
