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

    public ?int $patientId = null;   // pengganti selected_id
    public ?int $deleteId  = null;   // khusus delete

    public string $no_rm = '';       // kalau kolom ini memang ada & dipakai di form
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
            // kalau no_rm diisi otomatis dari sistem, kamu bisa jadikan nullable & remove dari form
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
        $this->dispatch('open-patient-modal');
    }

    public function edit(int $id)
    {
        $patient = Patient::findOrFail($id);

        $this->patientId   = $patient->id;
        $this->no_rm       = (string) ($patient->no_rm ?? '');
        $this->nik         = (string) $patient->nik;
        $this->name        = (string) $patient->name;
        $this->gender      = (string) $patient->gender;
        $this->birth_date  = (string) $patient->birth_date;
        $this->address     = (string) $patient->address;
        $this->phone       = (string) ($patient->phone ?? '');

        $this->resetValidation();
        $this->dispatch('open-patient-modal');
    }

    public function store()
    {
        $this->validate();

        if ($this->patientId) {
            $patient = Patient::findOrFail($this->patientId);

            $patient->update([
                'no_rm'      => $this->no_rm,
                'nik'        => $this->nik,
                'name'       => $this->name,
                'gender'     => $this->gender,
                'birth_date' => $this->birth_date,
                'address'    => $this->address,
                'phone'      => $this->phone,
            ]);

            session()->flash('message', 'Data pasien berhasil diperbarui.');
        } else {
            Patient::create([
                'no_rm'      => $this->no_rm,
                'nik'        => $this->nik,
                'name'       => $this->name,
                'gender'     => $this->gender,
                'birth_date' => $this->birth_date,
                'address'    => $this->address,
                'phone'      => $this->phone,
            ]);

            session()->flash('message', 'Data pasien berhasil ditambahkan.');
        }

        $this->dispatch('close-patient-modal');
        $this->resetInputFields();
    }

    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->dispatch('open-patient-delete-modal');
    }

    public function destroy()
    {
        if (!$this->deleteId) return;

        Patient::findOrFail($this->deleteId)->delete();

        session()->flash('message', 'Data pasien berhasil dihapus.');

        $this->dispatch('close-patient-delete-modal');
        $this->deleteId = null;

        $this->resetPage();
    }
}
