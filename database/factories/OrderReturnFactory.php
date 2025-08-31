<?php

namespace Database\Factories;

use App\Helper\ReturnStatus;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\Seller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderReturn>
 */
class OrderReturnFactory extends Factory
{
    protected $model = OrderReturn::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_number' => 'RET-'.now()->format('Ymd').'-'.str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'order_id' => Order::factory(),
            'seller_id' => Seller::factory(),
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(ReturnStatus::cases()),
            'reason' => fake()->sentence(),
            'admin_notes' => fake()->optional()->paragraph(),
            'total_refund_amount' => fake()->randomFloat(2, 10, 1000),
            'restocking_decision' => fake()->optional()->randomElement(['restock', 'damage', 'wrong_product', 'other']),
            'should_restock' => fake()->boolean(),
            'payment_slip' => fake()->optional()->word().'.jpg',
            'requested_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'approved_at' => fake()->optional()->dateTimeBetween('-20 days', 'now'),
            'completed_at' => fake()->optional()->dateTimeBetween('-10 days', 'now'),
        ];
    }

    /**
     * Indicate that the return is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReturnStatus::Pending,
            'approved_at' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the return is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReturnStatus::Approved,
            'approved_at' => fake()->dateTimeBetween('-10 days', 'now'),
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the return is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReturnStatus::Completed,
            'approved_at' => fake()->dateTimeBetween('-20 days', '-10 days'),
            'completed_at' => fake()->dateTimeBetween('-10 days', 'now'),
            'should_restock' => true,
        ]);
    }

    /**
     * Indicate that the return is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReturnStatus::Rejected,
            'admin_notes' => 'Return rejected: '.fake()->sentence(),
            'approved_at' => null,
            'completed_at' => null,
        ]);
    }
}
