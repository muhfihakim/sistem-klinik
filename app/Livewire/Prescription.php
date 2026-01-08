<?php

namespace App\Livewire;

use App\Models\MedicalRecord;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class Prescription extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    #[Layout('layouts.klinik')]

    public $search = '';

    public function render()
    {
        // Mengambil Rekam Medis yang memiliki Resep (E-Resep)
        $records = MedicalRecord::with(['patient', 'prescriptions.medicine'])
            ->whereHas('patient', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('no_rm', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.prescription', compact('records'));
    }
}
