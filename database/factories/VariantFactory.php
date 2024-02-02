<?php

namespace Database\Factories;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
class VariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => Str::upper(uniqid()),
            'price' => random_int(0,100),
            'quantity' => random_int(10,100),
            'discount' => random_int(0,50),
            'discount_price' => random_int(10,5500),
            'attributes' => [
                ['attribute_id' => Attribute::get()->random()->id, 'attribute_value_id' => AttributeValue::get()->random()->id,]
            ],
        ];
    }
}
