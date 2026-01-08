<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['patient_id', 'date', 'queue_number', 'status', 'complaint'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // TAMBAHKAN INI: Relasi ke Billing (Satu antrean punya satu tagihan)
    public function billing()
    {
        return $this->hasOne(Billing::class);
    }

    // TAMBAHKAN INI: Relasi ke Medical Record (Satu antrean punya satu rekam medis)
    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }
}
