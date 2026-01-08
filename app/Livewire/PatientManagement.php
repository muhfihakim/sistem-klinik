<?php

namespace App\Livewire;

use App\Models\Patient;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class PatientManagement extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    #[Layout('layouts.klinik')]

    public $search = '';
    public $nik, $name, $gender, $birth_date, $address, $phone, $selected_id;
    public $isEdit = false;

    public function render()
    {
        $patients = Patient::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('no_rm', 'like', '%' . $this->search . '%')
            ->orWhere('nik', 'like', '%' . $this->search . '%')
            ->latest()->paginate(10);

        return view('livewire.patient-management', compact('patients'));
    }

    public function resetInput()
    {
        $this->reset(['nik', 'name', 'gender', 'birth_date', 'address', 'phone', 'selected_id', 'isEdit']);
    }

    public function store()
    {
        $this->validate([
            'nik' => 'required|digits:16|unique:patients,nik,' . $this->selected_id,
            'name' => 'required|min:3',
            'gender' => 'required',
            'birth_date' => 'required|date',
            'address' => 'required',
        ]);

        Patient::updateOrCreate(['id' => $this->selected_id], [
            'nik' => $this->nik,
            'name' => $this->name,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'phone' => $this->phone,
        ]);

        // Dispatch event untuk menutup modal via JS
        $this->dispatch('closeModal', modalId: '#patientModal');
        $this->resetInput();

        // Notifikasi Toast/Alert (Opsional)
        $this->dispatch('swal', title: 'Berhasil!', text: 'Data pasien telah disimpan.', icon: 'success');
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        $this->selected_id = $id;
        $this->nik = $patient->nik;
        $this->name = $patient->name;
        $this->gender = $patient->gender;
        $this->birth_date = $patient->birth_date;
        $this->address = $patient->address;
        $this->phone = $patient->phone;
        $this->isEdit = true;
    }

    // Fungsi untuk memicu konfirmasi hapus
    public function confirmDelete($id)
    {
        $this->selected_id = $id;
        $this->dispatch('openModal', modalId: '#deleteModal');
    }

    public function delete()
    {
        Patient::findOrFail($this->selected_id)->delete();
        $this->dispatch('closeModal', modalId: '#deleteModal');
        $this->resetInput();
        $this->dispatch('swal', title: 'Dihapus!', text: 'Data pasien telah dihapus.', icon: 'warning');
    }
}
