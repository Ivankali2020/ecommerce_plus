<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Seller;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(3)
            ->state([
                'seller_id' => Seller::get()->random()->id
            ])
            ->has(UserAddress::factory(random_int(2,4)),'addresses')
            ->has(
                Order::factory(random_int(2,10))->state([
                    'seller_id' => Seller::get()->random()->id,
                    'slip' => fake()->imageUrl(),
                ])->has(
                    OrderProduct::factory(random_int(2,5)),'products'
                )
            )->create();
    }
}
