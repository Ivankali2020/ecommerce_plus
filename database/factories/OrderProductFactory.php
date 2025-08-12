<?php

namespace Database\Factories;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProduct>
 */
class OrderProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::select('id')->get()->random()->id,
            'price' => random_int(0, 100),
            'quantity' => random_int(10, 100),
            'discount' => random_int(0, 50),
            'discount_price' => random_int(10, 5500),
            'sub_price' => random_int(10, 5500),
            'attributes' => [
                ['attribute_id' => Attribute::get()->random()->id, 'attribute_value_id' => AttributeValue::get()->random()->id],
            ],
        ];
    }
}
