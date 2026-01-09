<div>

    <h4 class="mb-4">Manajemen Obat</h4>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <input type="text" wire:model.live="search" class="form-control w-25" placeholder="Cari Nama Obat...">

            <button class="btn btn-primary" wire:click="create" type="button">
                <i class="bi bi-capsule me-2"></i> Tambah Obat
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
                            <th style="text-align:center; width:70px;">No</th>
                            <th style="text-align:center;">Nama Obat</th>
                            <th style="text-align:center;">Satuan</th>
                            <th style="text-align:center;">Harga (Rp)</th>
                            <th style="text-align:center;">Stok</th>
                            <th style="text-align:center; width:170px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($medicines as $m)
                            <tr>
                                <td style="text-align:center;">
                                    {{ $medicines->firstItem() + $loop->index }}
                                </td>

                                <td style="text-align:center;">
                                    {{ $m->name }}
                                </td>

                                <td style="text-align:center;">
                                    {{ $m->unit }}
                                </td>

                                <td style="text-align:center;">
                                    {{ number_format($m->price, 0, ',', '.') }}
                                </td>

                                <td style="text-align:center;">
                                    <span class="badge bg-label-{{ $m->stock <= 10 ? 'danger' : 'success' }}">
                                        {{ $m->stock }}
                                    </span>
                                </td>

                                <td style="text-align:center;">
                                    <button wire:click="edit({{ $m->id }})" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square me-2"></i> Edit
                                    </button>

                                    <button wire:click="confirmDelete({{ $m->id }})"
                                        class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash3 me-2"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Data obat tidak ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination center + template sama --}}
            <div class="pt-3 d-flex justify-content-center" wire:loading.remove
                wire:target="nextPage,previousPage,gotoPage">
                {{ $medicines->onEachSide(1)->links('livewire.layout.pagination-outline-primary') }}
            </div>

        </div>
    </div>


    {{-- MODAL OBAT (form-floating sama persis) --}}
    <div wire:ignore.self class="modal fade" id="medicineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <form wire:submit.prevent="store">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $medicineId ? 'Edit Obat' : 'Tambah Obat Baru' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="form-floating form-floating-outline mb-6">
                            <input id="medicineName" type="text" wire:model="name" class="form-control"
                                placeholder="Masukkan Nama Obat" autocomplete="off">
                            <label for="medicineName">Nama Obat</label>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <select wire:model="unit" class="form-select h-100" id="medicineUnit">
                                <option value="">-- Pilih Satuan --</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Strip">Strip</option>
                                <option value="Botol">Botol</option>
                                <option value="Pcs">Pcs</option>
                            </select>
                            <label for="medicineUnit">Satuan</label>
                            @error('unit')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <input id="medicineStock" type="number" wire:model="stock" class="form-control"
                                placeholder="Masukkan Stok" min="0">
                            <label for="medicineStock">Stok</label>
                            @error('stock')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-6">
                            <input id="medicinePrice" type="number" wire:model="price" class="form-control"
                                placeholder="Masukkan Harga Jual" min="0">
                            <label for="medicinePrice">Harga Jual (Rp)</label>
                            @error('price')
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


    {{-- MODAL DELETE (samakan gaya seperti user) --}}
    <div wire:ignore.self class="modal fade" id="medicineDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0">Yakin ingin menghapus obat ini?</p>
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
            if (window.__medicineModalBound) return;
            window.__medicineModalBound = true;

            const getModal = (id) => {
                const el = document.getElementById(id);
                return el ? bootstrap.Modal.getOrCreateInstance(el) : null;
            };

            Livewire.on('open-medicine-modal', () => getModal('medicineModal')?.show());
            Livewire.on('close-medicine-modal', () => getModal('medicineModal')?.hide());

            Livewire.on('open-medicine-delete-modal', () => getModal('medicineDeleteModal')?.show());
            Livewire.on('close-medicine-delete-modal', () => getModal('medicineDeleteModal')?.hide());
        });
    </script>
@endsection
