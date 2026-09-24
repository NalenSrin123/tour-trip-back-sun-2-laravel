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
        Schema::table('bookings', function (Blueprint $table) {
            // 1. Make tour_schedule_id nullable so 'set null' can function properly
            $table->unsignedBigInteger('tour_schedule_id')->nullable()->change();

            // 2. Apply foreign key constraint with 'set null' on delete
            $table->foreign('tour_schedule_id')
                ->references('id')
                ->on('tour_schedules')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['tour_schedule_id']);
            $table->unsignedBigInteger('tour_schedule_id')->nullable(false)->change();
        });
    }
};
