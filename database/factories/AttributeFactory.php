<?php

namespace Database\Factories;

use App\Helper\AttributeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $data = ['Color', 'Size', 'Unit', 'Weight', 'Taste'];

        return [
            'name' => $data[random_int(0, count($data) - 1)],
            'type' => AttributeType::Text,
        ];
    }
}
