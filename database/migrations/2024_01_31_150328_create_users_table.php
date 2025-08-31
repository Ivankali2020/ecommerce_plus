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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Seller::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Tenant::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('firebase_uid')->nullable();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            $table->string('google_id')->nullable();

            $table->text('fmc_token')->nullable();

            $table->string('referral_code')->nullable();
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete();

            $table->bigInteger('points')->default();
            $table->rememberToken();
            $table->timestamps();

            // Performance indexes
            $table->index(['seller_id', 'created_at'], 'users_seller_created_idx');
            $table->index(['tenant_id', 'created_at'], 'users_tenant_created_idx');
            $table->index(['phone', 'seller_id'], 'users_phone_seller_idx');
            $table->index(['referral_code'], 'users_referral_code_idx');
            $table->index(['points', 'seller_id'], 'users_points_seller_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
};
