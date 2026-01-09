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

    public ?int $medicineId = null;   // pengganti selected_id
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
            'unit'  => ['required', Rule::in(['Tablet', 'Strip', 'Botol', 'Pcs'])], // sesuaikan jika unit kamu beda
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
        $this->dispatch('open-medicine-modal');
    }

    public function edit(int $id)
    {
        $medicine = Medicines::findOrFail($id);

        $this->medicineId = $medicine->id;
        $this->name = (string) $medicine->name;
        $this->unit = (string) $medicine->unit;
        $this->price = (int) $medicine->price;
        $this->stock = (int) $medicine->stock;

        $this->resetValidation();
        $this->dispatch('open-medicine-modal');
    }

    public function store()
    {
        $this->validate();

        if ($this->medicineId) {
            $medicine = Medicines::findOrFail($this->medicineId);
            $medicine->update([
                'name'  => $this->name,
                'unit'  => $this->unit,
                'price' => $this->price,
                'stock' => $this->stock,
            ]);

            session()->flash('message', 'Data Obat Diperbarui.');
        } else {
            Medicines::create([
                'name'  => $this->name,
                'unit'  => $this->unit,
                'price' => $this->price,
                'stock' => $this->stock,
            ]);

            session()->flash('message', 'Obat Baru Berhasil Ditambahkan.');
        }

        $this->dispatch('close-medicine-modal');
        $this->resetInputFields();
    }

    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->dispatch('open-medicine-delete-modal');
    }

    public function destroy()
    {
        if (!$this->deleteId) return;

        Medicines::findOrFail($this->deleteId)->delete();

        session()->flash('message', 'Data Obat Berhasil Dihapus.');

        $this->dispatch('close-medicine-delete-modal');
        $this->deleteId = null;

        $this->resetPage();
    }
}
