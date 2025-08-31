<?php

use App\Helper\ReturnStatus;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Tenant;
use App\Models\User;
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
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();

            $table->foreignIdFor(Order::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Seller::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('status')->default(ReturnStatus::Pending);
            $table->text('reason');
            $table->text('admin_notes')->nullable();

            $table->decimal('total_refund_amount', 20, 2)->default(0);
            $table->enum('restocking_decision', ['restock', 'damage', 'wrong_product', 'other'])->nullable();
            $table->boolean('should_restock')->default(false);

            $table->string('payment_slip')->nullable();

            $table->dateTime('requested_at');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Performance indexes
            $table->index(['order_id', 'status', 'deleted_at'], 'order_returns_order_status_deleted_idx');
            $table->index(['seller_id', 'status', 'deleted_at'], 'order_returns_seller_status_deleted_idx');
            $table->index(['tenant_id', 'status', 'deleted_at'], 'order_returns_tenant_status_deleted_idx');
            $table->index(['user_id', 'status'], 'order_returns_user_status_idx');
            $table->index(['requested_at', 'status'], 'order_returns_requested_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};
