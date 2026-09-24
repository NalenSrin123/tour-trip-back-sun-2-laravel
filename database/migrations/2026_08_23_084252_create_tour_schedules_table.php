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
        Schema::create('tour_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->dateTime('booking_cutoff_datetime');
            $table->integer('min_capacity');
            $table->integer('max_capacity');
            $table->integer('current_booked')->default(0);
            $table->integer('version')->default(1);
            $table->decimal('price_override', 10, 2)->nullable();
            $table->enum('status', [
                'published',
                'confirmed',
                'canceled',
                'completed',
            ])->default('published');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_schedules');
    }
};
