<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    // Tambahkan medical_record_id ke dalam array fillable
    protected $fillable = [
        'medical_record_id',
        'medicine_id',
        'quantity',
        'instruction'
    ];

    // Relasi ke Medical Record
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    // Relasi ke Medicine (untuk mengambil nama obat dan harga)
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
