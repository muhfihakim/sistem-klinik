<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Component
{
    use WithPagination;

    #[Layout('layouts.klinik')] // Pastikan ini layout template custom Anda

    public $name, $email, $password, $role, $userId;
    public $isOpen = false; // Untuk kontrol modal (jika template pakai modal)
    public $search = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'role' => 'required',
        'password' => 'required|min:6',
    ];

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::where('name', 'like', '%' . $this->search . '%')
                ->orderBy('id', 'desc')
                ->paginate(10)
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }
    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = '';
        $this->userId = '';
    }

    public function store()
    {
        $this->validate($this->userId ? [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required',
        ] : $this->rules);

        User::updateOrCreate(['id' => $this->userId], [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'password' => $this->password ? Hash::make($this->password) : User::find($this->userId)->password,
        ]);

        session()->flash('message', $this->userId ? 'User Diperbarui.' : 'User Dibuat.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = ''; // Kosongkan password saat edit
        $this->openModal();
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'User Berhasil Dihapus.');
    }
}
