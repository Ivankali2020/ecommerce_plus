<?php

namespace Database\Factories;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory()->state(['seller_id' => Seller::get()->random()->id ])->create(),
            'exchange_id' => Exchange::get()->random()->id,
            'category_id' => Category::factory()->state(['seller_id' => Seller::get()->random()->id ])->create(),
            'name' => fake()->name(),
            'description' => fake()->paragraph(),
            'has_variant' => fake()->boolean(),
            'is_public' => fake()->boolean(),
            'is_recommended' => fake()->boolean(),

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
