<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE debts MODIFY COLUMN status ENUM('pending', 'partial', 'paid', 'overdue', 'unpaid') NOT NULL DEFAULT 'unpaid'");
        DB::statement("UPDATE debts SET status = 'unpaid' WHERE status IN ('pending', 'partial', 'overdue')");
        DB::statement("ALTER TABLE debts MODIFY COLUMN status ENUM('paid', 'unpaid') NOT NULL DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE debts MODIFY COLUMN status ENUM('pending', 'partial', 'paid', 'overdue') NOT NULL DEFAULT 'pending'");
        DB::statement("UPDATE debts SET status = 'pending' WHERE status = 'unpaid'");
    }
};
