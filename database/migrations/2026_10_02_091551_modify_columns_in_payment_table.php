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
            // Drop the new foreign key constraint
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');

            // Re-add the old foreign key constraint
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

            // Drop the receipt_id column
            $table->dropColumn('receipt_id');
            
        });
    }
};
