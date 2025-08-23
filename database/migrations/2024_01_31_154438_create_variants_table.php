<?php

use App\Models\Product;
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
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->json('attributes');
            $table->string('sku');

            $table->integer('MOQ')->default(1);
            $table->string('name');

            $table->decimal('buy_price', 15, 2)->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('quantity');
            $table->integer('discount')->default(0);
            $table->decimal('discount_price', 15, 2)->default(0);

            $table->decimal('mmk_buy_price', 15, 2)->nullable();
            $table->decimal('mmk_price', 15)->nullable();
            $table->decimal('mmk_discount_price', 15)->default(0)->nullable();

            $table->boolean('is_whole_sale')->default(false);
            $table->dateTime('expired_date')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Make SKU unique per product (since variants belong to products which belong to tenants)
            $table->unique(['sku', 'product_id']);
            
            // Performance indexes
            $table->index(['product_id', 'deleted_at'], 'variants_product_deleted_idx');
            $table->index(['price', 'product_id'], 'variants_price_product_idx');
            $table->index(['mmk_price', 'product_id'], 'variants_mmk_price_product_idx');
            $table->index(['quantity', 'product_id'], 'variants_quantity_product_idx');
            $table->index(['is_whole_sale', 'product_id'], 'variants_wholesale_product_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('variants');
        Schema::enableForeignKeyConstraints();
    }
};
