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
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Tenant::class)->nullable();
            $table->foreignIdFor(\App\Models\Seller::class)->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('connection_type', ['network', 'usb'])->default('network');
            $table->string('ip_address')->nullable();
            $table->integer('port')->default(9100)->nullable();
            $table->string('usb_path')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index for performance
            $table->index(['seller_id', 'is_default']);
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};
