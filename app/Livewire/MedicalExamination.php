<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Prescription;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class MedicalExamination extends Component
{
    #[Layout('layouts.klinik')]
    public $selectedAppointment;
    public $subjective, $objective, $diagnosis_code, $assessment, $plan;

    // Properti baru untuk E-Resep
    public $prescriptions = [];
    public $allMedicines;

    public function mount()
    {
        // Ambil data obat yang stoknya tersedia
        $this->allMedicines = Medicine::where('stock', '>', 0)->orderBy('name', 'asc')->get();
    }

    public function render()
    {
        $queues = Appointment::with('patient')
            ->whereDate('date', now()->toDateString())
            ->whereIn('status', ['waiting', 'checking'])
            ->orderBy('queue_number', 'asc')
            ->get();

        return view('livewire.medical-examination', compact('queues'));
    }

    public function examine($id)
    {
        $this->selectedAppointment = Appointment::with('patient')->findOrFail($id);
        $this->selectedAppointment->update(['status' => 'checking']);
        $this->prescriptions = []; // Reset resep saat ganti pasien
    }

    // Fungsi Tambah Baris Obat
    public function addMedicine()
    {
        $this->prescriptions[] = ['medicine_id' => '', 'quantity' => 1, 'instruction' => ''];
    }

    // Fungsi Hapus Baris Obat
    public function removeMedicine($index)
    {
        unset($this->prescriptions[$index]);
        $this->prescriptions = array_values($this->prescriptions);
    }

    public function store()
    {
        $this->validate([
            'subjective' => 'required',
            'objective' => 'required',
            'diagnosis_code' => 'required',
            'assessment' => 'required',
            'prescriptions.*.medicine_id' => 'required',
            'prescriptions.*.quantity' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () {
            // 1. Simpan Rekam Medis
            $record = MedicalRecord::create([
                'patient_id' => $this->selectedAppointment->patient_id,
                'appointment_id' => $this->selectedAppointment->id,
                'user_id' => auth()->id(),
                'subjective' => $this->subjective,
                'objective' => $this->objective,
                'diagnosis_code' => $this->diagnosis_code,
                'assessment' => $this->assessment,
                'plan' => $this->plan,
            ]);

            // 2. Simpan Resep & Kurangi Stok
            foreach ($this->prescriptions as $item) {
                Prescription::create([
                    'medical_record_id' => $record->id,
                    'medicine_id' => $item['medicine_id'],
                    'quantity' => $item['quantity'],
                    'instruction' => $item['instruction'],
                ]);

                // Update Stok Obat
                Medicine::find($item['medicine_id'])->decrement('stock', $item['quantity']);
            }

            // 3. Update Status Antrean
            $this->selectedAppointment->update(['status' => 'finished']);
        });

        $this->reset(['selectedAppointment', 'subjective', 'objective', 'diagnosis_code', 'assessment', 'plan', 'prescriptions']);
        session()->flash('success', 'Rekam medis dan E-Resep berhasil disimpan.');
    }
}
