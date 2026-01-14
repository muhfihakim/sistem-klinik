<?php

use App\Livewire\Actions\Logout;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $password = ''; // Tambahkan untuk konfirmasi delete
    public $photo;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
        session()->flash('success', 'Profil berhasil diperbarui.');
    }

    // Fungsi Hapus Akun
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="card mb-6">
        <div class="card-body">
            <div class="d-flex align-items-start align-items-sm-center gap-6">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&color=719e37&background=e5f8ed"
                    alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
            </div>
        </div>
        <div class="card-body pt-0">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form wire:submit="updateProfileInformation">
                <div class="row mt-1 g-5">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input wire:model="name" class="form-control @error('name') is-invalid @enderror"
                                type="text" id="name" placeholder="Nama Lengkap" autocomplete="off" />
                            <label for="name">Nama Lengkap</label>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input wire:model="email" class="form-control @error('email') is-invalid @enderror"
                                type="email" id="email" placeholder="john.doe@example.com" autocomplete="off" />
                            <label for="email">E-mail</label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="btn btn-primary btn-sm me-3">Simpan Perubahan</button>
                    <span x-data="{ show: false }"
                        x-on:profile-updated.window="show = true; setTimeout(() => show = false, 2000)" x-show="show"
                        class="text-success small" style="display: none;">
                        <i class="ri-check-line"></i> Berhasil disimpan.
                    </span>
                </div>
            </form>
        </div>
    </div>

    <div class="card" x-data="{ confirmed: false }">
        <h5 class="card-header text-danger">Hapus Akun</h5>
        <div class="card-body">
            <div class="mb-6 col-12">
                <div class="alert alert-warning">
                    <h6 class="alert-heading mb-1">Apakah Anda yakin ingin menghapus akun?</h6>
                    <p class="mb-0">Sekali Anda menghapus akun, data tidak bisa dikembalikan.</p>
                </div>
            </div>

            <div class="form-check mb-6 ms-3">
                <input class="form-check-input" type="checkbox" id="accountActivation" x-model="confirmed" />
                <label class="form-check-label" for="accountActivation">Saya mengkonfirmasi penghapusan akun</label>
            </div>

            <button type="button" class="btn btn-danger btn-sm" x-bind:disabled="!confirmed" data-bs-toggle="modal"
                data-bs-target="#confirmDeleteModal">
                Hapus Akun
            </button>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit="deleteUser">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Akun Permanen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Silakan masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun ini secara
                            permanen.</p>
                        <div class="form-floating form-floating-outline">
                            <input wire:model="password" type="password" id="confirm_password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Masukkan Password Anda" />
                            <label for="confirm_password">Password</label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
