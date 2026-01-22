<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Patient;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Component;

class ClinicQueue extends Component
{
    use WithPagination; // 2. Wajib digunakan agar pagination bekerja tanpa reload

    #[Layout('layouts.klinik')]
    public string $searchPatient = '';
    public string $searchQueue = '';
    public ?string $complaint = null;
    public ?int $cancelId = null;

    // 3. Reset halaman ke nomor 1 setiap kali user mengetik di kolom pencarian antrean
    public function updatingSearchQueue()
    {
        $this->resetPage();
    }

    public function render()
    {
        $today = now()->toDateString();

        // Daftar antrean hari ini
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

        // 4. Ubah ->get() menjadi ->paginate(angka_per_halaman)
        $queues = $queuesQuery->paginate(10);

        // Pasien aktif
        $activePatientIds = Appointment::whereDate('date', $today)
            ->whereIn('status', ['waiting', 'checking'])
            ->pluck('patient_id')
            ->toArray();

        // Cari pasien (ini tetap menggunakan get karena untuk dropdown/list kecil)
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


    // public function addToQueue(int $patientId)
    // {
    //     $today = now()->toDateString();
    //     $activeExists = Appointment::where('patient_id', $patientId)
    //         ->whereDate('date', $today)
    //         ->whereIn('status', ['waiting', 'checking'])
    //         ->exists();

    //     if ($activeExists) {
    //         session()->flash('error', 'Pasien sudah dalam antrean.');
    //         return;
    //     }

    //     $lastNumber = Appointment::whereDate('date', $today)->max('queue_number') ?? 0;

    //     Appointment::create([
    //         'patient_id'   => $patientId,
    //         'date'         => $today,
    //         'queue_number' => $lastNumber + 1,
    //         'complaint'    => $this->complaint,
    //         'status'       => 'waiting',
    //     ]);

    //     $this->reset(['searchPatient', 'complaint']);
    //     session()->flash('success', 'Pasien berhasil ditambahkan ke antrean.');
    // }

    public function addToQueue(int $patientId, WhatsAppService $waService)
    {
        $today = now()->toDateString();

        // 1. Cek duplikasi antrean
        $activeExists = Appointment::where('patient_id', $patientId)
            ->whereDate('date', $today)
            ->whereIn('status', ['waiting', 'checking'])
            ->exists();

        if ($activeExists) {
            session()->flash('error', 'Pasien sudah dalam antrean.');
            return;
        }

        // 2. Hitung nomor antrean
        $lastNumber = Appointment::whereDate('date', $today)->max('queue_number') ?? 0;
        $newQueueNumber = $lastNumber + 1;

        // 3. Simpan ke database
        $appointment = Appointment::create([
            'patient_id'   => $patientId,
            'date'         => $today,
            'queue_number' => $newQueueNumber,
            'complaint'    => $this->complaint,
            'status'       => 'waiting',
        ]);

        // 4. Ambil data pasien & Kirim Notifikasi via Service
        $patient = $appointment->patient;

        if ($patient && $patient->phone) {
            $pesan = "*NOMOR ANTREAN KLINIK*\n\n" .
                "Halo *" . $patient->name . "*,\n" .
                "Pendaftaran Anda berhasil. Berikut detail antrean Anda:\n\n" .
                "No. Antrean: *" . $newQueueNumber . "*\n" .
                "Tanggal: " . now()->format('d/m/Y') . "\n" .
                "Status: Menunggu\n\n" .
                "Mohon menunggu di ruang tunggu. Kami akan memanggil Anda saat giliran tiba.\n" .
                "Terima kasih.";

            // Panggil fungsi dari service yang sudah kita buat
            $waService->sendMessage($patient->phone, $pesan);
        }

        $this->reset(['searchPatient', 'complaint']);
        session()->flash('success', 'Pasien berhasil ditambahkan ke antrean.');
    }

    public function confirmCancel(int $queueId)
    {
        $this->cancelId = $queueId;
        // Gunakan listener global
        $this->dispatch('open-modal', modalId: '#cancelQueueModal');
    }

    public function cancelQueue()
    {
        if (!$this->cancelId) return;

        $appointment = Appointment::findOrFail($this->cancelId);

        if ($appointment->status !== 'waiting') {
            session()->flash('error', 'Antrean tidak bisa dibatalkan.');
            $this->dispatch('close-modal', modalId: '#cancelQueueModal');
            return;
        }

        $appointment->update(['status' => 'cancelled']);

        // Gunakan listener global
        $this->dispatch('close-modal', modalId: '#cancelQueueModal');
        $this->cancelId = null;

        session()->flash('success', 'Antrean berhasil dibatalkan.');
    }
}
