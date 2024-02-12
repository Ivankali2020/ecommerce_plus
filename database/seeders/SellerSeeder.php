<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Exchange;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Township;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Variant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
           RegionSeeder::class,
           CurrencySeeder::class,
        ]);

        Seller::factory(5)
            ->has(Exchange::factory(3))
            ->has(Attribute::factory(3)->has(AttributeValue::factory(3),'values'))
            ->has(Payment::factory(10)->has(PaymentAccount::factory(3),'accounts'))
            ->has(Product::factory(5)->has(Variant::factory(random_int(2,5)),'variants'))
            ->create();

        User::factory(3)
            ->state([
                'seller_id' => Seller::get()->random()->id
            ])
            ->has(UserAddress::factory(random_int(2,4)),'addresses')
            ->has(
                Order::factory(random_int(2,10))->state([
                    'seller_id' => Seller::get()->random()->id,
                ])->has(
                    OrderProduct::factory(random_int(2,5)),'products'
                )
            )->create();
    }
}
