<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pasien</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#patientModal" wire:click="resetInput">
                <i class="ri-add-line"></i> Tambah Pasien
            </button>
        </div>

        <div class="card-body">
            <div class="mb-3 col-md-4">
                <input type="text" class="form-control" placeholder="Cari Nama / No. RM / NIK..."
                    wire:model.live="search">
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. RM</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>L/P</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($patients as $p)
                            <tr>
                                <td><span class="badge bg-label-primary">{{ $p->no_rm }}</span></td>
                                <td><strong>{{ $p->name }}</strong></td>
                                <td>{{ $p->nik }}</td>
                                <td>{{ $p->gender }}</td>
                                <td>
                                    <button wire:click="edit({{ $p->id }})"
                                        class="btn btn-sm btn-icon btn-text-warning rounded-pill" data-bs-toggle="modal"
                                        data-bs-target="#patientModal">
                                        <i class="ri-edit-box-line"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $p->id }})"
                                        class="btn btn-sm btn-icon btn-text-danger rounded-pill">
                                        <i class="ri-delete-bin-7-line"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $patients->links() }}
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="patientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="store">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? 'Edit Data Pasien' : 'Registrasi Pasien Baru' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">NIK</label>
                                <input type="text" class="form-control" wire:model="nik">
                                @error('nik')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" wire:model="name">
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select" wire:model="gender">
                                    <option value="">Pilih</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" wire:model="birth_date">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" wire:model="address"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Data?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data pasien ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" wire:click="delete" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
