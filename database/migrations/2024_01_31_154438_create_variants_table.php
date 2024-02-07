<?php

use App\Models\AttributeValue;
use App\Models\Exchange;
use App\Models\Payment;
use App\Models\Product;
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
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            //            $table->foreignIdFor(\App\Models\Attribute::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            //            $table->foreignIdFor(AttributeValue::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->json('attributes');
            $table->string('sku')->unique();


            $table->decimal('price', 8, 2);
            $table->integer('quantity');
            $table->integer('discount')->default(0);
            $table->decimal('discount_price', 8, 2)->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
