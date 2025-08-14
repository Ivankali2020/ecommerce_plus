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
        Schema::create('tenant_seller', function (Blueprint $table) {
            $table->id();
             $table->foreignIdFor(Tenant::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Seller::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_seller');
    }
};
