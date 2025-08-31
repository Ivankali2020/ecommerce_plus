<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Exchange;
use App\Models\Seller;
use App\Models\Supplier;
use App\Models\Tenant;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Seller::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Supplier::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignIdFor(Brand::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Exchange::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->longText('description');

            $table->json('attributes')->nullable();

            $table->integer('MOQ')->default(1);
            $table->string('sku')->nullable();

            $table->decimal('buy_price', 15, 2)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('discount')->default(0)->nullable();
            $table->decimal('discount_price')->default(0)->nullable();

            $table->decimal('mmk_buy_price', 15, 2)->nullable();
            $table->decimal('mmk_price', 15, 2)->nullable();
            $table->decimal('mmk_discount_price', 15, 2)->default(0)->nullable();

            $table->boolean('has_variant');
            $table->boolean('is_public');
            $table->boolean('is_recommended');

            $table->boolean('is_whole_sale')->default(false);

            $table->boolean('is_return')->default(false);
            $table->boolean('is_shipped')->default(true);

            $table->dateTime('expired_date')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Make SKU unique per tenant
            $table->unique(['sku', 'tenant_id']);

            // Performance indexes
            $table->index(['seller_id', 'is_public', 'deleted_at'], 'products_seller_public_deleted_idx');
            $table->index(['category_id', 'is_public', 'deleted_at'], 'products_category_public_deleted_idx');
            $table->index(['brand_id', 'is_public', 'deleted_at'], 'products_brand_public_deleted_idx');
            $table->index(['is_public', 'is_recommended', 'deleted_at'], 'products_public_recommended_deleted_idx');
            $table->index(['tenant_id', 'is_public', 'deleted_at'], 'products_tenant_public_deleted_idx');
            $table->index(['name', 'tenant_id'], 'products_name_tenant_idx');
            $table->index(['price', 'tenant_id'], 'products_price_tenant_idx');
            $table->index(['mmk_price', 'tenant_id'], 'products_mmk_price_tenant_idx');
            $table->index(['quantity', 'tenant_id'], 'products_quantity_tenant_idx');
            $table->index(['created_at', 'tenant_id'], 'products_created_tenant_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('products');
        Schema::enableForeignKeyConstraints();
    }
};
