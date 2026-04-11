<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            // Which showroom
            $table->foreignId('showroom_id')->constrained()->cascadeOnDelete();

            // Which tyre
            $table->foreignId('tyre_id')->constrained()->cascadeOnDelete();

            // Quantity change (+ for stock in, - for stock out)
            $table->integer('quantity');

            // Movement type
            $table->string('type'); 
            // invoice | sale | adjustment

            // Reference id (invoice_id or sale_id)
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};