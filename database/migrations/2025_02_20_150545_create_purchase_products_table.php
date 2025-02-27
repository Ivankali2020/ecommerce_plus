<?php

use App\Models\Product;
use App\Models\Purchase;
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
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Purchase::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Variant::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->json('attributes');
            $table->bigInteger('stocks');
            $table->decimal('buy_price',18,2);
            $table->decimal('sell_price',18,2)->nullable();
            $table->decimal('total',18,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_products');
    }
};
