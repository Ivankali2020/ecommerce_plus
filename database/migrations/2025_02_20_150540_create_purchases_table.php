<?php

use App\Helper\PurchaseStatus;
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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Seller::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Supplier::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('status')->default(PurchaseStatus::Waiting);
            $table->decimal('total_amount', 18, 2);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->decimal('remaining_amount', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
