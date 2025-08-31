<?php

use App\Models\OrderProduct;
use App\Models\OrderReturn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_return_products', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(OrderReturn::class)->constrained('order_returns')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(OrderProduct::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->integer('quantity_requested'); // How many units customer wants to return
            $table->integer('quantity_approved')->default(0); // How many units admin approved
            $table->text('individual_reason')->nullable(); // Specific reason for this product
            $table->text('condition_notes')->nullable(); // Admin notes about product condition

            $table->decimal('unit_refund_amount', 15, 2)->default(0); // Refund amount per unit
            $table->decimal('total_refund_amount', 15, 2)->default(0); // Total refund for this product line

            $table->boolean('is_restocked')->default(false); // Whether this specific product was restocked
            $table->text('admin_notes')->nullable(); // Admin notes for this specific product

            $table->timestamps();

            // Performance indexes
            $table->index(['order_return_id', 'order_product_id'], 'order_return_products_composite_idx');
            $table->index(['is_restocked'], 'order_return_products_restocked_idx');

            // Ensure unique combination - one return product entry per order product per return
            $table->unique(['order_return_id', 'order_product_id'], 'order_return_products_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_return_products');
    }
};
