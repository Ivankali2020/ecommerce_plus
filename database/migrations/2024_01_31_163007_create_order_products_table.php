<?php

use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Order;
use App\Models\Product;
use App\Models\Variant;
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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Variant::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('attributes');

            $table->decimal('price',15,2);
            $table->decimal('discount_price',15,2)->default(0);
            $table->integer('discount')->default(0);
            $table->integer('quantity');

            $table->decimal('original_buy_price',15,2)->default(0);
            $table->decimal('original_price',15,2)->default(0);
            $table->decimal('original_discount_price',15,2)->default(0);
            $table->foreignIdFor(Currency::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('exchange_rate')->nullable();

            $table->decimal('sub_price',15,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
