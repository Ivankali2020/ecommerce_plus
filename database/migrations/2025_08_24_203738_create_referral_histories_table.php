<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->integer('points_awarded')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            // Performance indexes
            $table->index(['referrer_id', 'created_at'], 'referral_histories_referrer_created_idx');
            $table->index(['referred_id', 'created_at'], 'referral_histories_referred_created_idx');
            $table->index(['seller_id', 'created_at'], 'referral_histories_seller_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_histories');
    }
};
