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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id'); // Dokter yang memeriksa
            $table->foreignId('appointment_id')->constrained(); // Link ke antrean

            // Form SOAP
            $table->text('subjective'); // Keluhan pasien
            $table->text('objective');  // Tensi, suhu, nadi, dll
            $table->string('diagnosis_code'); // Kode ICD-10
            $table->text('assessment'); // Diagnosis/Hasil analisa dokter
            $table->text('plan');       // Resep atau tindakan lanjut

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
