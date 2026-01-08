<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Medicine as Medicines;
use Livewire\Component;


class Medicine extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    #[Layout('layouts.klinik')]

    public $search = '';
    public $name, $unit, $price, $stock, $selected_id;
    public $isEdit = false;

    public function render()
    {
        $medicines = Medicines::where('name', 'like', '%' . $this->search . '%')
            ->latest()->paginate(10);
        return view('livewire.medicine', compact('medicines'));
    }

    public function resetInput()
    {
        $this->reset(['name', 'unit', 'price', 'stock', 'selected_id', 'isEdit']);
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3',
            'unit' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        Medicines::updateOrCreate(['id' => $this->selected_id], [
            'name' => $this->name,
            'unit' => $this->unit,
            'price' => $this->price,
            'stock' => $this->stock,
        ]);

        $this->dispatch('closeModal', modalId: '#medicineModal');
        $this->resetInput();
        session()->flash('message', $this->selected_id ? 'Data Obat Diperbarui.' : 'Obat Baru Berhasil Ditambahkan.');
    }

    public function edit($id)
    {
        $medicine = Medicines::findOrFail($id);
        $this->selected_id = $id;
        $this->name = $medicine->name;
        $this->unit = $medicine->unit;
        $this->price = $medicine->price;
        $this->stock = $medicine->stock;
        $this->isEdit = true;
    }

    public function confirmDelete($id)
    {
        $this->selected_id = $id;
        $this->dispatch('openModal', modalId: '#deleteMedicineModal');
    }

    public function delete()
    {
        Medicines::findOrFail($this->selected_id)->delete();
        $this->dispatch('closeModal', modalId: '#deleteMedicineModal');
        $this->resetInput();
    }
}
