<?php

use App\Models\Product;
use App\Models\Region;
use App\Models\User;
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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Variant::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('price',18,2);
            $table->decimal('total',18,2);
            $table->bigInteger('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('carts');
        Schema::enableForeignKeyConstraints();
    }
};
