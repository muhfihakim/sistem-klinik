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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained();
            $table->foreignId('patient_id')->constrained();
            $table->string('invoice_number')->unique();
            $table->integer('total_amount');
            $table->enum('status', ['unpaid', 'paid', 'expired'])->default('unpaid');
            $table->string('snap_token')->nullable(); // Dari Midtrans
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
