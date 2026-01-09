<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Patient;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ClinicQueue extends Component
{
    #[Layout('layouts.klinik')]
    public string $searchPatient = '';
    public string $searchQueue = '';
    public ?string $complaint = null;

    public ?int $cancelId = null;

    public function render()
    {
        $today = now()->toDateString();

        // daftar antrean hari ini (untuk tabel)
        $queuesQuery = Appointment::with('patient')
            ->whereDate('date', $today)
            ->orderBy('queue_number', 'asc');

        if (strlen($this->searchQueue) > 0) {
            $s = $this->searchQueue;

            $queuesQuery->where(function ($q) use ($s) {
                $q->where('queue_number', 'like', "%{$s}%")
                    ->orWhere('status', 'like', "%{$s}%")
                    ->orWhereHas('patient', function ($p) use ($s) {
                        $p->where('name', 'like', "%{$s}%")
                            ->orWhere('no_rm', 'like', "%{$s}%");
                    });
            });
        }

        $queues = $queuesQuery->get();

        // ✅ ambil patient_id yang sedang aktif (waiting/checking)
        $activePatientIds = Appointment::whereDate('date', $today)
            ->whereIn('status', ['waiting', 'checking'])
            ->pluck('patient_id')
            ->toArray();

        // hasil cari pasien untuk ditambahkan
        $patients = [];
        if (strlen($this->searchPatient) >= 3) {
            $patients = Patient::query()
                ->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchPatient . '%')
                        ->orWhere('no_rm', 'like', '%' . $this->searchPatient . '%');
                })
                ->take(5)
                ->get();
        }

        return view('livewire.clinic-queue', compact('queues', 'patients', 'activePatientIds'));
    }


    public function addToQueue(int $patientId)
    {
        $today = now()->toDateString();

        // Hanya blokir kalau antrean masih aktif
        $activeExists = Appointment::where('patient_id', $patientId)
            ->whereDate('date', $today)
            ->whereIn('status', ['waiting', 'checking'])
            ->exists();

        if ($activeExists) {
            session()->flash('error', 'Pasien sudah dalam antrean (waiting/checking) hari ini.');
            return;
        }

        $lastNumber = Appointment::whereDate('date', $today)->max('queue_number') ?? 0;

        Appointment::create([
            'patient_id'   => $patientId,
            'date'         => $today,
            'queue_number' => $lastNumber + 1,
            'complaint'    => $this->complaint,
            'status'       => 'waiting',
        ]);

        $this->reset(['searchPatient', 'complaint']);
        session()->flash('success', 'Pasien berhasil ditambahkan ke antrean.');
    }


    // === BATALKAN ANTREAN ===

    public function confirmCancel(int $queueId)
    {
        $this->cancelId = $queueId;
        $this->dispatch('open-cancel-queue-modal');
    }

    public function cancelQueue()
    {
        if (!$this->cancelId) return;

        $appointment = Appointment::findOrFail($this->cancelId);

        // Optional safety: hanya bisa batalkan yang masih waiting
        if ($appointment->status !== 'waiting') {
            session()->flash('error', 'Antrean ini tidak bisa dibatalkan karena statusnya bukan waiting.');
            $this->dispatch('close-cancel-queue-modal');
            $this->cancelId = null;
            return;
        }

        $appointment->update([
            'status' => 'cancelled',
        ]);

        $this->dispatch('close-cancel-queue-modal');
        $this->cancelId = null;

        session()->flash('success', 'Antrean berhasil dibatalkan.');
    }
}
