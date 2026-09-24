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
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('full_name', 255);
            $table->string('license_number', 255)->unique();
            $table->string('email', 255);
            $table->string('phone_number', 255);
            $table->string('languages', 150);
            $table->text('specialties')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_image_url', 255)->nullable();
            $table->enum('status', [
                'active',
                'inactive',
                'on_lease'
            ])->default('inactive');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};















