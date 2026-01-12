<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    #[Layout('layouts.klinik')]
    public $name = '', $email = '', $password = '', $role = '';
    public $userId = null;
    public $search = '';
    public $deleteId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function rules()
    {
        return [
            'name' => ['required', 'min:3'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'role' => ['required', Rule::in(['admin', 'doctor', 'staff'])],
            'password' => [
                $this->userId ? 'nullable' : 'required',
                'min:6',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::where('name', 'like', '%' . $this->search . '%')
                ->orderBy('id', 'desc')
                ->paginate(10)
        ]);
    }

    private function resetInputFields()
    {
        $this->reset(['name', 'email', 'password', 'role', 'userId', 'deleteId']);
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetInputFields();
        // Menggunakan listener global 'open-modal'
        $this->dispatch('open-modal', modalId: '#userModal');
    }

    public function edit($id)
    {
        $this->resetInputFields();
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name   = $user->name;
        $this->email  = $user->email;
        $this->role   = $user->role;
        $this->password = '';

        $this->dispatch('open-modal', modalId: '#userModal');
    }

    public function store()
    {
        $this->validate();

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $data = [
                'name'  => $this->name,
                'email' => $this->email,
                'role'  => $this->role,
            ];

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);
            session()->flash('message', 'User Diperbarui.');
        } else {
            User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'role'     => $this->role,
                'password' => Hash::make($this->password),
            ]);
            session()->flash('message', 'User Dibuat.');
        }

        // Menggunakan listener global 'close-modal'
        $this->dispatch('close-modal', modalId: '#userModal');
        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('open-modal', modalId: '#userDeleteModal');
    }

    public function destroy()
    {
        if (!$this->deleteId) return;

        if (auth()->check() && auth()->id() == $this->deleteId) {
            session()->flash('message', 'Tidak bisa menghapus akun sendiri.');
            $this->dispatch('close-modal', modalId: '#userDeleteModal');
            return;
        }

        User::findOrFail($this->deleteId)->delete();

        session()->flash('message', 'User Berhasil Dihapus.');
        $this->dispatch('close-modal', modalId: '#userDeleteModal');
        $this->resetInputFields();
        $this->resetPage();
    }
}
