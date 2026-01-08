<?php

namespace App\Livewire;

use App\Models\Patient;
use App\Models\Appointment;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ClinicQueue extends Component
{
    #[Layout('layouts.klinik')]
    public $search = '';
    public $complaint;

    public function render()
    {
        // Ambil antrean hari ini
        $queues = Appointment::with('patient')
            ->whereDate('date', now()->toDateString())
            ->orderBy('queue_number', 'asc')
            ->get();

        // Cari pasien untuk didaftarkan ke antrean
        $patients = [];
        if (strlen($this->search) >= 3) {
            $patients = Patient::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('no_rm', 'like', '%' . $this->search . '%')
                ->take(5)->get();
        }

        return view('livewire.clinic-queue', compact('queues', 'patients'));
    }

    public function addToQueue($patientId)
    {
        $today = now()->toDateString();

        // Cek apakah pasien sudah terdaftar di antrean hari ini
        $exists = Appointment::where('patient_id', $patientId)->whereDate('date', $today)->exists();
        if ($exists) {
            session()->flash('error', 'Pasien sudah ada dalam antrean hari ini.');
            return;
        }

        $lastNumber = Appointment::whereDate('date', $today)->max('queue_number') ?? 0;

        Appointment::create([
            'patient_id' => $patientId,
            'date' => $today,
            'queue_number' => $lastNumber + 1,
            'complaint' => $this->complaint,
            'status' => 'waiting'
        ]);

        $this->reset(['search', 'complaint']);
        session()->flash('success', 'Pasien berhasil ditambahkan ke antrean.');
    }
}
