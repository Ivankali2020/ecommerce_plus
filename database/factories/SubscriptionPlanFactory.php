<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Plan',
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(50000, 500000),
            'duration_months' => $this->faker->randomElement([3, 6, 12]),
            'features' => $this->faker->randomElements([
                'Unlimited Products',
                'Priority Support',
                'Custom Domain',
                'Analytics Dashboard',
                'API Access',
                'Multiple Staff Accounts',
                'Advanced Reporting',
                'Bulk Import/Export',
                'Custom Branding',
                'Email Marketing Integration'
            ], $this->faker->numberBetween(3, 8)),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}