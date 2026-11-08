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
        Schema::table('invoices', function (Blueprint $table) {
             // Drop the new foreign key constraint
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');

            // Re-add the old foreign key constraint
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');

            // Re-add the new foreign key constraint
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
        });
    }
};
