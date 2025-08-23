<?php

use App\Models\Product;
use App\Models\Seller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Variant;
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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Seller::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Variant::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('price', 18, 2);
            $table->decimal('total', 18, 2);
            $table->bigInteger('quantity');
            $table->timestamps();
            
            // Performance indexes for cart operations
            $table->index(['user_id', 'seller_id'], 'carts_user_seller_idx');
            $table->index(['user_id', 'tenant_id'], 'carts_user_tenant_idx');
            $table->index(['seller_id', 'created_at'], 'carts_seller_created_idx');
            $table->index(['product_id', 'user_id'], 'carts_product_user_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('carts');
        Schema::enableForeignKeyConstraints();
    }
};
