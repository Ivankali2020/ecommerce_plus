<?php

use App\Models\Seller;
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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Seller::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('parent_id')->nullable()->constrained('categories', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->mediumText('description')->nullable();
            $table->string('name');
            $table->boolean('is_sub_category')->default(false);
            $table->softDeletes();
            $table->timestamps();
            
            // Performance indexes for category operations
            $table->index(['seller_id', 'deleted_at'], 'categories_seller_deleted_idx');
            $table->index(['tenant_id', 'deleted_at'], 'categories_tenant_deleted_idx');
            $table->index(['parent_id', 'deleted_at'], 'categories_parent_deleted_idx');
            $table->index(['is_sub_category', 'seller_id'], 'categories_sub_seller_idx');
            $table->index(['name', 'tenant_id'], 'categories_name_tenant_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('categories');
        Schema::enableForeignKeyConstraints();
    }
};
