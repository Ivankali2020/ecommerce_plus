<?php

use App\Models\Seller;
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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Seller::class, 'parent_id')->nullable()->constrained('sellers', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->mediumText('description')->nullable();
            $table->foreignId('subscription_plan_id')->nullable();
            $table->timestamp('subscription_started_at')->nullable();
            $table->timestamp('subscription_expired_at')->nullable();
            $table->string('subscription_status')->default('inactive');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->boolean('enabled_google_auth')->default(false);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            
            // Performance indexes
            $table->index(['tenant_id', 'deleted_at'], 'sellers_tenant_deleted_idx');
            $table->index(['subscription_status', 'subscription_expired_at'], 'sellers_subscription_status_expired_idx');
            $table->index(['parent_id', 'deleted_at'], 'sellers_parent_deleted_idx');
            $table->index(['phone', 'deleted_at'], 'sellers_phone_deleted_idx');
            $table->index(['created_at', 'deleted_at'], 'sellers_created_deleted_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
