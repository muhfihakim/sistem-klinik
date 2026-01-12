<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MedicalExamination extends Component
{
    #[Layout('layouts.klinik')]
    public ?Appointment $selectedAppointment = null;

    public string $searchQueue = '';

    public ?string $subjective = null;
    public ?string $objective = null;
    public ?string $diagnosis_code = null;
    public ?string $assessment = null;
    public ?string $plan = null;
    public ?int $canvas_medicine_id = null;
    public int $canvas_quantity = 1;
    public string $canvas_instruction = '';


    public array $prescriptions = [];
    public $allMedicines;

    public function mount()
    {
        $this->refreshMedicines();
    }

    private function refreshMedicines(): void
    {
        $this->allMedicines = Medicine::where('stock', '>', 0)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function render()
    {
        $today = now()->toDateString();

        $queuesQuery = Appointment::with('patient')
            ->whereDate('date', $today)
            ->whereIn('status', ['waiting', 'checking'])
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

        return view('livewire.medical-examination', compact('queues'));
    }

    public function examine(int $id)
    {
        $appointment = Appointment::with('patient')->findOrFail($id);

        // hanya ubah ke checking kalau masih waiting
        if ($appointment->status === 'waiting') {
            $appointment->update(['status' => 'checking']);
        }

        $this->selectedAppointment = $appointment;

        // reset form saat ganti pasien
        $this->reset([
            'subjective',
            'objective',
            'diagnosis_code',
            'assessment',
            'plan',
            'prescriptions'
        ]);

        $this->resetValidation();
    }

    public function addMedicine()
    {
        $this->prescriptions[] = [
            'medicine_id' => '',
            'quantity' => 1,
            'instruction' => '',
        ];
    }

    public function removeMedicine(int $index)
    {
        unset($this->prescriptions[$index]);
        $this->prescriptions = array_values($this->prescriptions);
    }

    public function store()
    {
        if (!$this->selectedAppointment) {
            session()->flash('error', 'Pilih antrean terlebih dahulu.');
            return;
        }

        $this->validate([
            'subjective' => 'required',
            'objective' => 'required',
            'diagnosis_code' => 'required',
            'assessment' => 'required',
            'plan' => 'nullable',

            'prescriptions' => 'array',
            'prescriptions.*.medicine_id' => 'required',
            'prescriptions.*.quantity' => 'required|integer|min:1',
            'prescriptions.*.instruction' => 'nullable|string',
        ]);

        // Validasi stok (qty tidak boleh melebihi stock)
        foreach ($this->prescriptions as $i => $item) {
            $med = Medicine::find($item['medicine_id']);
            if (!$med) {
                $this->addError("prescriptions.$i.medicine_id", "Obat tidak valid.");
                return;
            }

            if ((int)$item['quantity'] > (int)$med->stock) {
                $this->addError("prescriptions.$i.quantity", "Stok obat tidak cukup. Sisa stok: {$med->stock}");
                return;
            }
        }

        DB::transaction(function () {
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

            foreach ($this->prescriptions as $item) {
                Prescription::create([
                    'medical_record_id' => $record->id,
                    'medicine_id' => $item['medicine_id'],
                    'quantity' => (int) $item['quantity'],
                    'instruction' => $item['instruction'],
                ]);

                Medicine::where('id', $item['medicine_id'])
                    ->decrement('stock', (int) $item['quantity']);
            }

            $this->selectedAppointment->update(['status' => 'finished']);
        });

        $this->reset([
            'selectedAppointment',
            'subjective',
            'objective',
            'diagnosis_code',
            'assessment',
            'plan',
            'prescriptions'
        ]);

        $this->refreshMedicines();

        session()->flash('success', 'Rekam medis dan E-Resep berhasil disimpan.');
    }

    public function openPrescriptionCanvas()
    {
        // reset field canvas tiap buka
        $this->reset(['canvas_medicine_id', 'canvas_quantity', 'canvas_instruction']);
        $this->canvas_quantity = 1;

        $this->resetValidation();
        $this->dispatch('open-prescription-canvas');
    }

    public function addPrescriptionItem()
    {
        $this->validate([
            'canvas_medicine_id' => 'required|exists:medicines,id',
            'canvas_quantity' => 'required|integer|min:1',
            'canvas_instruction' => 'nullable|string',
        ]);

        $med = \App\Models\Medicine::find($this->canvas_medicine_id);

        if (!$med) {
            $this->addError('canvas_medicine_id', 'Obat tidak valid.');
            return;
        }

        if ($this->canvas_quantity > $med->stock) {
            $this->addError('canvas_quantity', "Stok tidak cukup. Sisa stok: {$med->stock}");
            return;
        }

        $this->prescriptions[] = [
            'medicine_id' => (int) $this->canvas_medicine_id,
            'quantity' => (int) $this->canvas_quantity,
            'instruction' => $this->canvas_instruction,
        ];

        // Gunakan listener global agar konsisten
        $this->dispatch('close-modal', modalId: '#prescriptionCanvas');

        $this->reset(['canvas_medicine_id', 'canvas_quantity', 'canvas_instruction']);
    }
}
