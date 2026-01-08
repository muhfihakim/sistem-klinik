<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Inventaris Obat / Farmasi</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#medicineModal" wire:click="resetInput">
                <i class="ri-add-line"></i> Tambah Obat
            </button>
        </div>
        <div class="card-body">
            <div class="mb-3 col-md-4">
                <input type="text" class="form-control" placeholder="Cari Nama Obat..." wire:model.live="search">
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Obat</th>
                            <th>Satuan</th>
                            <th>Harga (Rp)</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($medicines as $m)
                            <tr>
                                <td><strong>{{ $m->name }}</strong></td>
                                <td>{{ $m->unit }}</td>
                                <td>{{ number_format($m->price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $m->stock <= 10 ? 'danger' : 'success' }}">
                                        {{ $m->stock }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $m->id }})"
                                        class="btn btn-sm btn-icon btn-text-warning rounded-pill" data-bs-toggle="modal"
                                        data-bs-target="#medicineModal">
                                        <i class="ri-edit-box-line"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $m->id }})"
                                        class="btn btn-sm btn-icon btn-text-danger rounded-pill">
                                        <i class="ri-delete-bin-7-line"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $medicines->links() }}</div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="medicineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="store">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? 'Edit Obat' : 'Tambah Obat Baru' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nama Obat</label>
                                <input type="text" class="form-control" wire:model="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Satuan</label>
                                <select class="form-select" wire:model="unit">
                                    <option value="">Pilih Satuan</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Strip">Strip</option>
                                    <option value="Botol">Botol</option>
                                    <option value="Pcs">Pcs</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stok Awal</label>
                                <input type="number" class="form-control" wire:model="stock">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Harga Jual (Rp)</label>
                                <input type="number" class="form-control" wire:model="price">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteMedicineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Hapus Obat?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
