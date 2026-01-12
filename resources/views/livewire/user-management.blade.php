<div>

    <h4 class="mb-4">Manajemen Pengguna</h4>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <input type="text" wire:model.live="search" class="form-control w-25" placeholder="Cari Pengguna...">
            <button class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#userModal"
                type="button">
                <i class="bi bi-plus-lg me-2"></i>Tambah Pengguna
            </button>
        </div>

        <div wire:loading.flex wire:target="nextPage,previousPage,gotoPage,search"
            class="position-absolute top-50 start-50 translate-middle z-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div wire:loading.class="opacity-50" wire:target="nextPage,previousPage,gotoPage,search"
            class="table-responsive text-nowrap">
            <table class="datatables-basic table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th style="text-align: center">No</th>
                        <th style="text-align: center">Nama</th>
                        <th style="text-align: center">Email</th>
                        <th style="text-align: center">Role</th>
                        <th style="text-align: center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td style="text-align: center">{{ $loop->iteration }}</td>
                            <td style="text-align: center">{{ $user->name }}</td>
                            <td style="text-align: center">{{ $user->email }}</td>
                            <td style="text-align: center"><span
                                    class="badge bg-label-info">{{ strtoupper($user->role) }}</span></td>
                            <td style="text-align: center">
                                <button wire:click="edit({{ $user->id }})" data-bs-toggle="modal"
                                    data-bs-target="#userModal" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square me-2"></i>Edit
                                </button>
                                <button wire:click="confirmDelete({{ $user->id }})" data-bs-toggle="modal"
                                    data-bs-target="#userDeleteModal" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash3 me-2"></i>Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 d-flex justify-content-center">
            {{ $users->onEachSide(1)->links('livewire.layout.pagination-outline-primary') }}
        </div>


    </div>

    <div wire:ignore.self class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="store">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-floating form-floating-outline mb-6">
                            <input id="namaLengkap" type="text" wire:model="name" class="form-control"
                                placeholder="Masukkan Nama Lengkap Anda" autocomplete="off">
                            <label for="namaLengkap">Nama Lengkap</label>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <input id="alamatEmail" type="email" wire:model="email" class="form-control"
                                placeholder="Masukkan Alamat Email Anda" autocomplete="off">
                            <label for="alamatEmail">Alamat Email</label>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <select wire:model="role" class="form-select h-100" id="pilihRole">
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="doctor">Dokter</option>
                                <option value="staff">Staf/Kasir</option>
                            </select>
                            <label for="pilihRole">Role</label>
                            @error('role')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <input id="password" type="password" wire:model="password" class="form-control"
                                placeholder="Masukkan Password Anda" autocomplete="off">
                            <label for="password">Password {{ $userId ? '(Kosongkan jika tidak ganti)' : '' }}</label>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="userDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin menghapus pengguna ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" wire:click="destroy">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>

</div>
@section('Scripts')
    <script data-navigate-once>
        // Sebenarnya bagian ini bisa dikosongkan jika app.blade.php sudah benar.
        // Tapi jika ingin memastikan listener modal spesifik ada di sini:
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-user-modal', () => {
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('userModal'));
                modal.show();
            });

            // Listener 'close-modal' secara otomatis sudah dihandle di app.blade.php
        });
    </script>
@endsection
