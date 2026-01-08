<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'invoice_number',
        'total_amount',
        'status',
        'snap_token'
    ];

    // Relasi ke Antrean/Kunjungan
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Relasi ke Pasien
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
