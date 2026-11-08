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
        Schema::table('payments', function (Blueprint $table) {
            //Drop old foreign key constraint
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');

            // Remove receipt_id column
            $table->dropColumn('receipt_id');

            // Add new foreign key constraint with cascade on delete
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // 1. Drop the added booking_id foreign key and column
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');

            // 2. Restore receipt_id (adjust type/nullable according to your original schema)
            $table->foreignId('receipt_id')->nullable(); 

            // 3. Restore invoice_id foreign key (adjust constrained table if needed)
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->cascadeOnDelete();
        });
    }
};
