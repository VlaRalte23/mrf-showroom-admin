<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showrooms', function (Blueprint $table) {
            $table->id();

            $table->string('name');   // Aizawl, Lunglei, Champhai
            $table->string('location')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showrooms');
    }
};