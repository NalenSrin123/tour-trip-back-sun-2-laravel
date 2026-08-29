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
         Schema::create('tours', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->foreignId('destination_id')
                ->constrained('destinations')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->unique();

            $table->integer('duration_days');
            $table->integer('duration_nights');

            $table->decimal('base_price', 10, 2);
            $table->decimal('price_override', 10, 2)->nullable();

            $table->enum('status', [
                'DRAFT',
                'PUBLISHED',
                'ARCHIVED'
            ])->default('DRAFT');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
