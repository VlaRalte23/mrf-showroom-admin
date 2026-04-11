<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tyres', function (Blueprint $table) {
            $table->id();

            $table->string('tyre_size');     // Example: 10.00-20
            $table->string('pattern')->nullable(); // Example: S3H8
            $table->string('category')->nullable(); // Truck / Car / Tractor

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tyres');
    }
};