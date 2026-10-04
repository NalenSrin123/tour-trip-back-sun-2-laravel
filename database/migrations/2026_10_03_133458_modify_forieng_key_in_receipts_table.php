<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\After;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Drop the foreign key constraint and payment_id column
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');

            // Add the new foreign key constraint with cascade on delete
            $table->foreignId('invoice_id')->after('id')->constrained('invoices')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Drop the new foreign key constraint and invoice_id column
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');

            // Re-add the old foreign key constraint with cascade on delete
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();

        });
    }
};
