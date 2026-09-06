<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tour_schedules', function (Blueprint $table) {
            // 1. Drop the existing foreign key constraint first
            $table->dropForeign('tour_schedules_tour_id_foreign');
        });

        Schema::table('tour_schedules', function (Blueprint $table) {
            // 2. Make the column nullable so 'set null' can function
            $table->foreignId('tour_id')->nullable()->change();

            // 3. Re-apply the constraint with the 'set null' behavior
            $table->foreign('tour_id')
                ->references('id')
                ->on('tours')
                ->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_schedules', function (Blueprint $table) {
            // 1. Drop the existing foreign key constraint first
            $table->dropForeign('tour_schedules_tour_id_foreign');
            
        });
    }
};
