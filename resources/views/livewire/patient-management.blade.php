<div>

    <h4 class="mb-4">Manajemen Pasien</h4>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <input type="text" wire:model.live="search" class="form-control w-25"
                placeholder="Cari Nama / No. RM / NIK...">
            <button class="btn btn-primary" wire:click="create" type="button">
                <i class="bi bi-plus-lg me-2"></i>Tambah Pasien
            </button>
        </div>
        <div class="card-body position-relative">
            {{-- Spinner overlay saat pagination/search --}}
            <div wire:loading.flex wire:target="nextPage,previousPage,gotoPage,search"
                class="position-absolute top-50 start-50 translate-middle z-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div wire:loading.class="opacity-50" wire:target="nextPage,previousPage,gotoPage,search"
                class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align:center; width:80px;">No</th>
                            <th style="text-align:center;">No. RM</th>
                            <th style="text-align:center;">Nama</th>
                            <th style="text-align:center;">NIK</th>
                            <th style="text-align:center;">L/P</th>
                            <th style="text-align:center; width:140px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($patients as $p)
                            <tr>
                                <td style="text-align:center;">
                                    {{ $patients->firstItem() + $loop->index }}
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge bg-label-primary">{{ $p->no_rm }}</span>
                                </td>
                                <td style="text-align:center;">
                                    {{ $p->name }}
                                </td>
                                <td style="text-align:center;">
                                    {{ $p->nik }}
                                </td>
                                <td style="text-align:center;">
                                    {{ $p->gender }}
                                </td>
                                <td style="text-align:center;">
                                    <button wire:click="edit({{ $p->id }})" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square me-2"></i> Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $p->id }})"
                                        class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash3 me-2"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Data pasien tidak ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination center (pakai template yang sama) --}}
            <div class="pt-3 d-flex justify-content-center" wire:loading.remove
                wire:target="nextPage,previousPage,gotoPage">
                {{ $patients->onEachSide(1)->links('livewire.layout.pagination-outline-primary') }}
            </div>

        </div>
    </div>

    {{-- MODAL PATIENT --}}
    <div wire:ignore.self class="modal fade" id="patientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form wire:submit.prevent="store">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $patientId ? 'Edit Data Pasien' : 'Registrasi Pasien Baru' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="form-floating form-floating-outline mb-6">
                            <input type="text" id="noRm" class="form-control" wire:model="no_rm"
                                placeholder="Masukkan No. Rekam Medis" autocomplete="off">
                            <label for="noRm">No. Rekam Medis</label>
                            @error('no_rm')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <input type="text" id="nik" class="form-control" wire:model="nik"
                                placeholder="Masukkan NIK" autocomplete="off">
                            <label for="nik">NIK</label>
                            @error('nik')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <input type="text" id="namaLengkap" class="form-control" wire:model="name"
                                placeholder="Masukkan Nama Lengkap" autocomplete="off">
                            <label for="namaLengkap">Nama Lengkap</label>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <select wire:model="gender" class="form-select h-100" id="jenisKelamin">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <label for="jenisKelamin">Jenis Kelamin</label>
                            @error('gender')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <input type="date" id="tanggalLahir" class="form-control" wire:model="birth_date">
                            <label for="tanggalLahir">Tanggal Lahir</label>
                            @error('birth_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <textarea id="alamat" class="form-control" style="height: 100px" wire:model="address"
                                placeholder="Masukkan Alamat Lengkap"></textarea>
                            <label for="alamat">Alamat</label>
                            @error('address')
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

    {{-- MODAL DELETE (sama seperti user-management) --}}
    <div wire:ignore.self class="modal fade" id="patientDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0">Yakin ingin menghapus data pasien ini?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" wire:click="destroy">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@section('Scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            if (window.__patientModalBound) return;
            window.__patientModalBound = true;

            const getModal = (id) => {
                const el = document.getElementById(id);
                return el ? bootstrap.Modal.getOrCreateInstance(el) : null;
            };

            Livewire.on('open-patient-modal', () => getModal('patientModal')?.show());
            Livewire.on('close-patient-modal', () => getModal('patientModal')?.hide());

            Livewire.on('open-patient-delete-modal', () => getModal('patientDeleteModal')?.show());
            Livewire.on('close-patient-delete-modal', () => getModal('patientDeleteModal')?.hide());

        });
    </script>
@endsection
