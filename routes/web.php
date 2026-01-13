<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// 1. GUEST ROUTES (Bisa diakses tanpa login)
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // 1. SEMUA ROLE (Admin, Doctor, Staff)
    // Dashboard biasanya berisi ringkasan yang relevan untuk semua
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // 2. KHUSUS ADMIN (Full Access)
    // Admin bisa mengakses semua menu termasuk manajemen user
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', \App\Livewire\UserManagement::class)->name('users.index');
        // Admin biasanya juga memantau semua data master
    });

    // 3. KHUSUS DOKTER (Fokus Medis)
    Route::middleware(['role:doctor,admin'])->group(function () {
        Route::get('/examination', \App\Livewire\MedicalExamination::class)->name('medical.examination');
        Route::get('/medicines', \App\Livewire\Medicine::class)->name('medicines.index');
        Route::get('/prescriptions', \App\Livewire\Prescription::class)->name('prescriptions.index');
    });

    // 4. KHUSUS STAFF (Administrasi & Kasir)
    Route::middleware(['role:staff,admin'])->group(function () {
        Route::get('/patients', \App\Livewire\PatientManagement::class)->name('patients.index');
        Route::get('/queue', \App\Livewire\ClinicQueue::class)->name('queue.index');
        Route::get('/billing', \App\Livewire\ClinicBilling::class)->name('billing.index');
        Route::get('/invoice/download/{id}', [InvoiceController::class, 'download'])->name('invoice.download');
    });

    Route::post('/logout', function () {
        Auth::logout();

        // Gunakan helper request() bukannya Facade
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    })->name('logout');
});

require __DIR__ . '/auth.php';
