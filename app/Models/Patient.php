<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                // 1. Hapus semua karakter selain angka (spasi, dash, plus)
                $number = preg_replace('/[^0-9]/', '', $value);

                // 2. Ubah format 08 menjadi 628
                if (str_starts_with($number, '0')) {
                    $number = '62' . substr($number, 1);
                }

                // 3. Jika user menginput +62..., angka + sudah dibuang di langkah 1
                return $number;
            },
        );
    }
}
