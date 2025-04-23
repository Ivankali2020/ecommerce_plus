<?php

namespace Database\Seeders;

use App\Helper\OrderStatus;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $sellers = Seller::pluck('id')->toArray();
        $products = Product::pluck('id')->toArray();

        if (empty($sellers) || empty($products)) {
            $this->command->warn('Please seed Sellers and Products before running OrderSeeder.');
            return;
        }

        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subMonths(18); // Approximately 1.5 years ago

        DB::beginTransaction();

        try {
            for ($i = 0; $i < 150; $i++) { // Create 150 orders, adjust as needed
                $sellerId = $faker->randomElement($sellers);
                $createdAt = $faker->dateTimeBetween($startDate, $endDate);

                // Create Order
                $order = Order::create([
                    'invoice_number' => 'INV-' . strtoupper(uniqid()),
                    'seller_id' => $sellerId,
                    'staff_id' => null, // Assuming walk-in doesn't have staff ID, adjust if needed
                    'walk_in_customer' => true,
                    'is_cod' => $faker->boolean(70), // 70% chance of being COD for walk-in
                    'user_id' => null, // No registered user for walk-in
                    'user_address_id' => null, // No address for walk-in
                    'payment_id' => null, // Adjust if walk-in uses specific payment methods
                    'payment_account_id' => null,
                    'status' => $faker->randomElement([OrderStatus::Delivered, OrderStatus::Processing]), // Example statuses
                    'sub_total' => 0, // Will be calculated based on products
                    'delivery_fees' => 0, // No delivery for walk-in
                    'total_price' => 0, // Will be calculated based on products
                    'payment_slip' => null,
                    'tax' => 0, // Example tax
                    'tax_price' => 0, // Example tax price
                    'given_amount' => 0, // Will be calculated
                    'change_amount' => 0, // Will be calculated
                    'note' => $faker->optional()->sentence,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Create Order Products
                $orderSubTotal = 0;
                $numberOfProducts = $faker->numberBetween(1, 5);
                $selectedProducts = $faker->randomElements($products, $numberOfProducts);

                foreach ($selectedProducts as $productId) {
                    $product = Product::find($productId); // Fetch product details for price
                    if (!$product) continue; // Skip if product not found

                    $quantity = $faker->numberBetween(1, 3);
                    // Use product's price or generate a random one if not available
                    $price = $product->price ?? $faker->randomFloat(2, 10, 200);
                    $discount = $faker->numberBetween(0, 20); // 0-20% discount
                    $discountPrice = $price * (1 - ($discount / 100));
                    $subPrice = $discountPrice * $quantity;
                    $profit = ($discountPrice - ($product->buy_price ?? $price * 0.6)) * $quantity; // Example profit calculation

                    OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'variant_id' => null, // Assuming no variants for simplicity
                        'attributes' => json_encode([]), // Assuming no attributes
                        'price' => $price,
                        'discount_price' => $discountPrice,
                        'discount' => $discount,
                        'quantity' => $quantity,
                        'profit' => $profit,
                        'original_buy_price' => $product->buy_price ?? $price * 0.6, // Example original buy price
                        'original_price' => $price,
                        'original_discount_price' => $discountPrice,
                        'currency_id' => null, // Assuming base currency
                        'exchange_rate' => null,
                        'sub_price' => $subPrice,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    $orderSubTotal += $subPrice;
                }

                // Update Order totals
                $totalPrice = $orderSubTotal; // Add tax, delivery if applicable
                $givenAmount = $totalPrice + $faker->randomFloat(2, 0, 50); // Simulate overpayment
                $changeAmount = $givenAmount - $totalPrice;

                $order->update([
                    'sub_total' => $orderSubTotal,
                    'total_price' => $totalPrice,
                    'given_amount' => $givenAmount,
                    'change_amount' => $changeAmount,
                ]);
            }

            DB::commit();
            $this->command->info('OrderSeeder completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('OrderSeeder failed: ' . $e->getMessage());
        }
    }
}
