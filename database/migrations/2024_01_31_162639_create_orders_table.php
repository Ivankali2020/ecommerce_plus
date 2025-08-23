<?php

use App\Helper\OrderStatus;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Seller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserAddress;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignIdFor(Seller::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Tenant::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Seller::class, 'staff_id')->nullable()->constrained('sellers', 'id')->cascadeOnUpdate()->cascadeOnDelete();

            $table->boolean('walk_in_customer')->default(0);
            $table->boolean('is_cod')->default(0);

            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(UserAddress::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignIdFor(Payment::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(PaymentAccount::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('status')->default(OrderStatus::New);

            $table->decimal('sub_total', 20, 2);
            $table->decimal('delivery_fees');
            $table->decimal('total_price', 20, 2);

            $table->string('payment_slip')->nullable();

            $table->decimal('tax')->default(0);
            $table->decimal('tax_price')->default(0);

            $table->decimal('given_amount', 20, 2)->default(0);
            $table->decimal('change_amount', 20, 2)->default(0);
            $table->text('note')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'seller_id', 'is_cod']);
            $table->index('created_at');
            
            // Additional performance indexes
            $table->index(['seller_id', 'status', 'deleted_at'], 'orders_seller_status_deleted_idx');
            $table->index(['tenant_id', 'status', 'deleted_at'], 'orders_tenant_status_deleted_idx');
            $table->index(['status', 'created_at', 'deleted_at'], 'orders_status_created_deleted_idx');
            $table->index(['payment_id', 'status'], 'orders_payment_status_idx');
            $table->index(['user_id', 'created_at'], 'orders_user_created_idx');
            $table->index(['total_price', 'seller_id'], 'orders_total_seller_idx');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('orders');
        Schema::enableForeignKeyConstraints();
    }
};
