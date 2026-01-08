<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/dashboard', Dashboard::class)->name('dashboard');
// });

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::middleware(['auth'])->group(function () {

    // Halaman yang bisa diakses SEMUA role yang sudah login
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // Khusus Admin
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/users', \App\Livewire\UserManagement::class)->name('users.index');
        Route::get('/patients', \App\Livewire\PatientManagement::class)->name('patients.index');
        Route::get('/queue', \App\Livewire\ClinicQueue::class)->name('queue.index');
        Route::get('/examination', \App\Livewire\MedicalExamination::class)->name('medical.examination');
        Route::get('/medicines', \App\Livewire\Medicine::class)->name('medicines.index');
        Route::get('/prescriptions', \App\Livewire\Prescription::class)->name('prescriptions.index');
        Route::get('/billing', \App\Livewire\ClinicBilling::class)->name('billing.index');
        Route::get('/invoice/download/{id}', [InvoiceController::class, 'download'])->name('invoice.download');
    });

    // Khusus Dokter
    Route::middleware(['role:doctor'])->group(function () {
        // Route::get('/pemeriksaan', Pemeriksaan::class)->name('pemeriksaan');
    });
});

require __DIR__ . '/auth.php';
