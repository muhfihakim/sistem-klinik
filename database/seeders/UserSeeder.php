<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin Klinik',
            'email' => 'admin@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        \App\Models\User::create([
            'name' => 'dr. Budi Kusuma',
            'email' => 'dokter1@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'doctor',
        ]);

        \App\Models\User::create([
            'name' => 'dr. Siti Aminah',
            'email' => 'dokter2@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'doctor',
        ]);

        \App\Models\User::create([
            'name' => 'dr. Andi Wijaya',
            'email' => 'dokter3@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'doctor',
        ]);

        \App\Models\User::create([
            'name' => 'dr. Rina Pratama',
            'email' => 'dokter4@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'doctor',
        ]);

        \App\Models\User::create([
            'name' => 'dr. Farhan Malik',
            'email' => 'dokter5@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'doctor',
        ]);

        // Dummy Staff
        \App\Models\User::create([
            'name' => 'Andini Putri (Admin)',
            'email' => 'staff1@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);

        \App\Models\User::create([
            'name' => 'Bambang Heru',
            'email' => 'staff2@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);

        \App\Models\User::create([
            'name' => 'Citra Lestari',
            'email' => 'staff3@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);

        \App\Models\User::create([
            'name' => 'Dedi Kurniawan',
            'email' => 'staff4@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);

        \App\Models\User::create([
            'name' => 'Eka Wahyuni',
            'email' => 'staff5@mail.id',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);
    }
}
