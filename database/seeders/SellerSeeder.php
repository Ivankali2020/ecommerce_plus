<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Seller::factory(5)
            ->has(User::factory(3))
            ->has(Brand::factory(3))
            ->has(Category::factory(2))
            ->has(Attribute::factory(3)->has(AttributeValue::factory(3),'values'))
            ->has(Payment::factory(10)->has(PaymentAccount::factory(3),'accounts'))
            ->create();
    }
}
