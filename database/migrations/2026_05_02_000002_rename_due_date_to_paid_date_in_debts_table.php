<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->renameColumn('due_date', 'paid_date');
        });
    }

    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->renameColumn('paid_date', 'due_date');
        });
    }
};
