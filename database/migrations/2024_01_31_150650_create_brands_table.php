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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Seller::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Performance indexes
            $table->index(['seller_id', 'deleted_at'], 'brands_seller_deleted_idx');
            $table->index(['tenant_id', 'deleted_at'], 'brands_tenant_deleted_idx');
            $table->index(['name', 'tenant_id'], 'brands_name_tenant_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
