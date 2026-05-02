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
        Schema::create('debts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('sale_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
