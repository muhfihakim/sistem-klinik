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

    // protected string $paginationTheme = 'bootstrap';

    #[Layout('layouts.klinik')]
    public $name = '', $email = '', $password = '', $role = '';
    public $userId = null;

    public $search = '';
    public $deleteId = null;

    // Biar search reset pagination
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
        $this->reset(['name', 'email', 'password', 'role', 'userId']);
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->dispatch('open-user-modal');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name   = $user->name;
        $this->email  = $user->email;
        $this->role   = $user->role;
        $this->password = ''; // kosongkan, biar tidak ketimpa

        $this->resetValidation();
        $this->dispatch('open-user-modal');
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

        $this->dispatch('close-user-modal');
        $this->resetInputFields();
    }

    // === HAPUS VIA MODAL ===
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('open-delete-modal');
    }

    public function destroy()
    {
        if (!$this->deleteId) return;

        // optional: cegah hapus akun sendiri
        if (auth()->check() && auth()->id() == $this->deleteId) {
            session()->flash('message', 'Tidak bisa menghapus akun yang sedang digunakan.');
            $this->dispatch('close-delete-modal');
            $this->deleteId = null;
            return;
        }

        User::findOrFail($this->deleteId)->delete();

        session()->flash('message', 'User Berhasil Dihapus.');
        $this->dispatch('close-delete-modal');
        $this->deleteId = null;

        // supaya tidak nyangkut di page kosong setelah delete
        $this->resetPage();
    }
}
