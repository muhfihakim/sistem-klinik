<?php

namespace App\Livewire;

use App\Models\Patient;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class PatientManagement extends Component
{
    use WithPagination;

    #[Layout('layouts.klinik')]
    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public ?int $patientId = null;
    public ?int $deleteId  = null;

    public string $no_rm = '';
    public string $nik = '';
    public string $name = '';
    public string $gender = '';
    public string $birth_date = '';
    public string $address = '';
    public string $phone = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function rules()
    {
        return [
            'no_rm' => ['nullable', 'string', 'max:50'],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('patients', 'nik')->ignore($this->patientId),
            ],
            'name' => ['required', 'min:3'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'birth_date' => ['required', 'date'],
            'address' => ['required'],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }

    private function resetInputFields(): void
    {
        $this->reset([
            'patientId',
            'deleteId',
            'no_rm',
            'nik',
            'name',
            'gender',
            'birth_date',
            'address',
            'phone',
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        $patients = Patient::query()
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('no_rm', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.patient-management', compact('patients'));
    }

    public function create()
    {
        $this->resetInputFields();
        // Membuka modal via dispatch (opsional jika sudah pakai data-bs-toggle)
        $this->dispatch('open-modal', modalId: '#patientModal');
    }

    public function edit(int $id)
    {
        $this->resetInputFields(); // Bersihkan state lama
        $patient = Patient::findOrFail($id);

        $this->patientId   = $patient->id;
        $this->no_rm       = (string) ($patient->no_rm ?? '');
        $this->nik         = (string) $patient->nik;
        $this->name        = (string) $patient->name;
        $this->gender      = (string) $patient->gender;
        $this->birth_date  = (string) $patient->birth_date;
        $this->address     = (string) $patient->address;
        $this->phone       = (string) ($patient->phone ?? '');

        // Dispatch untuk membuka modal edit
        $this->dispatch('open-modal', modalId: '#patientModal');
    }

    public function store()
    {
        $this->validate();

        $data = [
            'no_rm'      => $this->no_rm,
            'nik'        => $this->nik,
            'name'       => $this->name,
            'gender'     => $this->gender,
            'birth_date' => $this->birth_date,
            'address'    => $this->address,
            'phone'      => $this->phone,
        ];

        if ($this->patientId) {
            Patient::find($this->patientId)->update($data);
            session()->flash('message', 'Data pasien berhasil diperbarui.');
        } else {
            Patient::create($data);
            session()->flash('message', 'Data pasien berhasil ditambahkan.');
        }

        // Tutup modal menggunakan listener global
        $this->dispatch('close-modal', modalId: '#patientModal');
        $this->resetInputFields();
    }

    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->dispatch('open-modal', modalId: '#patientDeleteModal');
    }

    public function destroy()
    {
        if ($this->deleteId) {
            Patient::find($this->deleteId)->delete();

            // Tutup modal hapus menggunakan listener global
            $this->dispatch('close-modal', modalId: '#patientDeleteModal');

            $this->resetInputFields();
            session()->flash('message', 'Data pasien berhasil dihapus.');
        }
    }
}
