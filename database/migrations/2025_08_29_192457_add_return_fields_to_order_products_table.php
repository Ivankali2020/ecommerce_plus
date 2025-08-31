<?php

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
        Schema::table('order_products', function (Blueprint $table) {
            $table->boolean('is_returned')->default(false)->after('sub_price');
            $table->integer('returned_quantity')->default(0)->after('is_returned');
            $table->decimal('total_refunded_amount', 15, 2)->default(0)->after('returned_quantity');

            // Performance index for return queries
            $table->index(['is_returned', 'returned_quantity'], 'order_products_return_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropIndex('order_products_return_idx');
            $table->dropColumn(['is_returned', 'returned_quantity', 'total_refunded_amount']);
        });
    }
};
