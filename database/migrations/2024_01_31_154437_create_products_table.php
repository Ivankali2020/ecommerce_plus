<?php

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Exchange;
use App\Models\Review;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Seller::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Brand::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Exchange::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->mediumText('description');

            $table->json('attributes')->nullable();

            $table->string('sku')->nullable()->unique();
            $table->decimal('price',8,4)->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('discount')->default(0)->nullable();
            $table->decimal('discount_price',8,4)->default(0)->nullable();

            $table->boolean('has_variant');
            $table->boolean('is_public');
            $table->boolean('is_recommended');

            $table->boolean('is_return')->default(false);
            $table->boolean('is_shipped')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
