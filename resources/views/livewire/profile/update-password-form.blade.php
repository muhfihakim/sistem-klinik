<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Variabel untuk kontrol mata (server-side)
    public bool $showCurrent = false;
    public bool $showNew = false;
    public bool $showConfirm = false;

    public function toggleShow($field)
    {
        $this->$field = !$this->$field;
    }

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
        session()->flash('success-password', 'Kata sandi berhasil diperbarui.');
    }
}; ?>

<div class="card">
    <h5 class="card-header">Ganti Kata Sandi</h5>
    <div class="card-body">
        <p class="mb-5 text-muted small">Pastikan akun Anda tetap aman.</p>

        @if (session('success-password'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success-password') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form wire:submit="updatePassword">
            <div class="row g-5">
                <div class="col-md-12">
                    <div class="input-group input-group-merge">
                        <div class="form-floating form-floating-outline">
                            <input wire:model="current_password" type="{{ $showCurrent ? 'text' : 'password' }}"
                                id="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                placeholder="············" autocomplete="off" />
                            <label for="current_password">Kata Sandi Saat Ini</label>
                        </div>
                    </div>
                    @error('current_password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <div class="input-group input-group-merge">
                        <div class="form-floating form-floating-outline">
                            <input wire:model="password" type="{{ $showNew ? 'text' : 'password' }}" id="new_password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="············"
                                autocomplete="off" />
                            <label for="new_password">Kata Sandi Baru</label>
                        </div>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <div class="input-group input-group-merge">
                        <div class="form-floating form-floating-outline">
                            <input wire:model="password_confirmation" type="{{ $showConfirm ? 'text' : 'password' }}"
                                id="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                placeholder="············" autocomplete="off" />
                            <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        </div>
                    </div>
                    @error('password_confirmation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="btn btn-primary btn-sm me-3">Simpan Kata Sandi</button>
            </div>
        </form>
    </div>
</div>
