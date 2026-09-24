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
        Schema::table('tours', function (Blueprint $table) {
            if (!Schema::hasColumn('tours', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            }

            if (!Schema::hasColumn('tours', 'destination_id')) {
                $table->foreignId('destination_id')->nullable()->after('category_id')->constrained('destinations')->nullOnDelete();
            }

            if (!Schema::hasColumn('tours', 'slug')) {
                $table->string('slug')->nullable()->after('title');
            }

            if (!Schema::hasColumn('tours', 'duration_days')) {
                $table->integer('duration_days')->default(1)->after('slug');
            }

            if (!Schema::hasColumn('tours', 'duration_nights')) {
                $table->integer('duration_nights')->default(0)->after('duration_days');
            }

            if (!Schema::hasColumn('tours', 'price_override')) {
                $table->decimal('price_override', 10, 2)->nullable()->after('base_price');
            }

            if (!Schema::hasColumn('tours', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            if (Schema::hasColumn('tours', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }

            if (Schema::hasColumn('tours', 'destination_id')) {
                $table->dropForeign(['destination_id']);
                $table->dropColumn('destination_id');
            }

            if (Schema::hasColumn('tours', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            $columnsToDrop = array_filter(['slug', 'duration_days', 'duration_nights', 'price_override'], function ($col) {
                return Schema::hasColumn('tours', $col);
            });

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
