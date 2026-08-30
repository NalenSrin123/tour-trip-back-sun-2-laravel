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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('tour_schedule_id');
            $table->decimal('total_price', 10, 2);
            $table->text('special_requests')->nullable();
            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'completed'
            ])->default('pending');
            $table->enum('type', [
                'Individual',
                'Family',
                'Team'
            ])->default('Individual');
            $table->integer('members_count')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
