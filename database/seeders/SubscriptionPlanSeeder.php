<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {

        // Basic Plan - 1 months
        SubscriptionPlan::create([
            'name' => 'Free Plan',
            'description' => 'Perfect for small businesses just getting started with e-commerce.',
            'price' => 0,
            'duration_months' => 1,
            'features' => [
                'Up to 100 Products',
                '2 Staff Accounts',
                'Basic Analytics',
                'Standard Support',
            ],
            'is_active' => false,
        ]);

        // Basic Plan - 3 months
        SubscriptionPlan::create([
            'name' => 'Basic Plan',
            'description' => 'Perfect for small businesses just getting started with e-commerce.',
            'price' => 99000,
            'duration_months' => 3,
            'features' => [
                'Up to 100 Products',
                '2 Staff Accounts',
                'Basic Analytics',
                'Standard Support',
                'Mobile App Access',
            ],
            'is_active' => true,
        ]);

        // Standard Plan - 6 months
        SubscriptionPlan::create([
            'name' => 'Standard Plan',
            'description' => 'Ideal for growing businesses with expanding product catalogs.',
            'price' => 179000,
            'duration_months' => 6,
            'features' => [
                'Up to 500 Products',
                '5 Staff Accounts',
                'Advanced Analytics',
                'Priority Support',
                'Mobile App Access',
                'Bulk Import/Export',
                'API Access',
            ],
            'is_active' => true,
        ]);

        // Premium Plan - 12 months
        SubscriptionPlan::create([
            'name' => 'Premium Plan',
            'description' => 'Our most comprehensive plan for established businesses seeking maximum value.',
            'price' => 299000,
            'duration_months' => 12,
            'features' => [
                'Unlimited Products',
                'Unlimited Staff Accounts',
                'Premium Analytics Dashboard',
                '24/7 Priority Support',
                'Mobile App Access',
                'Bulk Import/Export',
                'Full API Access',
                'Custom Branding',
                'Email Marketing Integration',
                'Advanced Reporting',
            ],
            'is_active' => true,
        ]);
    }
}
