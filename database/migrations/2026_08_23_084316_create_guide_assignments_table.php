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
        Schema::create('guide_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('tour_schedules');
            $table->foreignId('guide_id')->constrained('guides');
            $table->timestamp('assigned_at');
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**=
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_assignments');
    }
};


