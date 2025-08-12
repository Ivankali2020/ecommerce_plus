<?php

use App\Models\ExpanseCategory;
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
        Schema::create('expanses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ExpanseCategory::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('amount', 18, 2);
            $table->text('description')->nullable();
            $table->string('payment_method');
            $table->foreignId('seller_id')->constrained('sellers', 'id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expanses');
    }
};
