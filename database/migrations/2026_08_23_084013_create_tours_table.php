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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->onDelete('set null'); //  to 'set null' if the referenced destination is deleted

            $table->foreignId('destination_id')
                ->nullable()
                ->constrained('destinations')
                ->onDelete('set null'); //  to 'set null' if the referenced destination is deleted

            $table->string('title');
            $table->string('slug')->unique();
            $table->integer('duration_days');
            $table->integer('duration_nights');
            $table->decimal('base_price', 10, 2);
            $table->decimal('price_override', 10, 2)->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
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
