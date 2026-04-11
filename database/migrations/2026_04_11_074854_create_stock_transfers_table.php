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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_showroom_id');
            $table->unsignedBigInteger('to_showroom_id');
            $table->unsignedBigInteger('tyre_id');
            $table->integer('quantity');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('from_showroom_id')->references('id')->on('showrooms')->onDelete('cascade');
            $table->foreign('to_showroom_id')->references('id')->on('showrooms')->onDelete('cascade');
            $table->foreign('tyre_id')->references('id')->on('tyres')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
