<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'user_id',
        'appointment_id',
        'subjective',
        'objective',
        'diagnosis_code',
        'assessment',
        'plan'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
