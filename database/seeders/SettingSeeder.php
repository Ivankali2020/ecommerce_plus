<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can seed the settings table with default values here.
        // For example, you might want to create a default setting for a seller or tenant.

        // Example:
        // \App\Models\Setting::create([
        //     'seller_id' => 1, // Assuming you have a seller with ID 1
        //     'tenant_id' => 1, // Assuming you have a tenant with ID 1
        //     'data' => [
        //         'point_percentage' => 5, // 5% point reward 
        //         'tax_percentage' => 10, // 10% tax
        //         'referral_points' => 100, // 100 points for referral
        //     ],
        // ]);
    }
}
