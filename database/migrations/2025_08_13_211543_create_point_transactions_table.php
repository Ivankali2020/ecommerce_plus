<?php

use App\Models\User;
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
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Seller::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->enum('type', ['earned', 'spent', 'expired', 'refunded']);
            $table->integer('points'); // positive for earned/refunded, negative for spent/expired
            $table->integer('balance_after'); // user's point balance after this transaction

            // Reference to what earned/spent the points
            $table->string('reference_type')->nullable(); // 'order', 'gift', 'referral', 'manual', etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // order_id, gift_id, etc.

            $table->text('description')->nullable(); // Human readable description
            $table->json('metadata')->nullable(); // Additional data (order details, gift info, etc.)

            $table->timestamp('expires_at')->nullable(); // For point expiration
            $table->timestamps();

            // Index for performance
            $table->index(['user_id', 'type']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
