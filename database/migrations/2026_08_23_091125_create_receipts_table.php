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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->string('receipt_no', 100)->unique();
            $table->timestamp('issued_at')->useCurrent();
            $table->string('tour_title', 255)->nullable();
            $table->date('tour_date')->nullable();
            $table->integer('num_travelers')->nullable();
            $table->decimal('sub_total', 10, 2)->nullable();
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('total_paid', 10, 2)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('pdf_url', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};