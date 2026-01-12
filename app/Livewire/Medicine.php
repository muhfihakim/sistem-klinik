<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Medicine as Medicines;
use Illuminate\Validation\Rule;

class Medicine extends Component
{
    use WithPagination;

    #[Layout('layouts.klinik')]
    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public ?int $medicineId = null;
    public ?int $deleteId = null;

    public string $name = '';
    public string $unit = '';
    public $price = 0;
    public $stock = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function rules()
    {
        return [
            'name'  => ['required', 'min:3'],
            'unit'  => ['required', Rule::in(['Tablet', 'Strip', 'Botol', 'Pcs'])],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ];
    }

    private function resetInputFields(): void
    {
        $this->reset(['medicineId', 'deleteId', 'name', 'unit', 'price', 'stock']);
        $this->resetValidation();
    }

    public function render()
    {
        $medicines = Medicines::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate(10);

        return view('livewire.medicine', compact('medicines'));
    }

    public function create()
    {
        $this->resetInputFields();
        $this->dispatch('open-modal', modalId: '#medicineModal');
    }

    public function edit(int $id)
    {
        $this->resetInputFields();
        $medicine = Medicines::findOrFail($id);

        $this->medicineId = $medicine->id;
        $this->name = (string) $medicine->name;
        $this->unit = (string) $medicine->unit;
        $this->price = (int) $medicine->price;
        $this->stock = (int) $medicine->stock;

        $this->dispatch('open-modal', modalId: '#medicineModal');
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name'  => $this->name,
            'unit'  => $this->unit,
            'price' => $this->price,
            'stock' => $this->stock,
        ];

        if ($this->medicineId) {
            Medicines::find($this->medicineId)->update($data);
            session()->flash('message', 'Data Obat Diperbarui.');
        } else {
            Medicines::create($data);
            session()->flash('message', 'Obat Baru Berhasil Ditambahkan.');
        }

        $this->dispatch('close-modal', modalId: '#medicineModal');
        $this->resetInputFields();
    }

    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->dispatch('open-modal', modalId: '#medicineDeleteModal');
    }

    public function destroy()
    {
        if (!$this->deleteId) return;

        Medicines::findOrFail($this->deleteId)->delete();
        session()->flash('message', 'Data Obat Berhasil Dihapus.');

        $this->dispatch('close-modal', modalId: '#medicineDeleteModal');
        $this->resetInputFields();
        $this->resetPage();
    }
}
