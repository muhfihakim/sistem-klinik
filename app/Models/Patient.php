<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['no_rm', 'nik', 'name', 'gender', 'birth_date', 'address', 'phone'];

    protected static function booted()
    {
        static::creating(function ($patient) {
            $lastPatient = self::orderBy('id', 'desc')->first();
            $nextId = $lastPatient ? $lastPatient->id + 1 : 1;
            $patient->no_rm = 'RM-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        });
    }
}
