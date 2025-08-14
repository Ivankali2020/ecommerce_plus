<?php

namespace Database\Seeders;

use App\Models\Gift;
use App\Models\Seller;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first seller and tenant for demo data
        $seller = Seller::first();
        $tenant = Tenant::first();

        if (!$seller || !$tenant) {
            $this->command->warn('No seller or tenant found. Please create sellers and tenants first.');
            return;
        }

        $gifts = [
            [
                'name' => 'Company Coffee Mug',
                'description' => 'Premium ceramic mug with company logo. Perfect for your daily coffee.',
                'points_required' => 100,
                'stock_quantity' => 50,
                'additional_data' => [
                    'color' => 'White',
                    'material' => 'Ceramic',
                    'capacity' => '350ml'
                ]
            ],
            [
                'name' => 'Logo T-Shirt',
                'description' => 'Comfortable cotton t-shirt with premium logo print.',
                'points_required' => 250,
                'stock_quantity' => 30,
                'additional_data' => [
                    'material' => '100% Cotton',
                    'sizes' => ['S', 'M', 'L', 'XL'],
                    'colors' => ['Black', 'Navy', 'White']
                ]
            ],
            [
                'name' => 'Baseball Cap',
                'description' => 'Stylish baseball cap with embroidered logo.',
                'points_required' => 150,
                'stock_quantity' => 25,
                'additional_data' => [
                    'material' => 'Cotton/Polyester',
                    'color' => 'Navy Blue',
                    'adjustable' => true
                ]
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse - great for office work.',
                'points_required' => 500,
                'stock_quantity' => 15,
                'additional_data' => [
                    'brand' => 'TechPro',
                    'connectivity' => 'Wireless 2.4GHz',
                    'battery' => 'AA x 1'
                ]
            ],
            [
                'name' => 'Gift Voucher - $10',
                'description' => '$10 voucher that can be used for any purchase in our store.',
                'points_required' => 800,
                'stock_quantity' => 100,
                'additional_data' => [
                    'value' => '$10',
                    'expiry' => '6 months',
                    'terms' => 'Cannot be exchanged for cash'
                ]
            ]
        ];

        foreach ($gifts as $giftData) {
            Gift::create([
                'seller_id' => $seller->id,
                'tenant_id' => $tenant->id,
                'name' => $giftData['name'],
                'description' => $giftData['description'],
                'points_required' => $giftData['points_required'],
                'stock_quantity' => $giftData['stock_quantity'],
                'is_active' => true,
                'additional_data' => $giftData['additional_data']
            ]);
        }

        $this->command->info('Created ' . count($gifts) . ' demo gifts successfully!');
    }
}
