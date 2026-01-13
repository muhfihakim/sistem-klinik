<div>

    <h4 class="mb-4">Layanan E-Resep (Farmasi)</h4>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <input type="text" wire:model.live="search" class="form-control w-25" placeholder="Cari Nama / No. RM...">
        </div>
        <div class="card-body position-relative">
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
                            <th style="text-align: center">No</th>
                            <th style="width: 170px; text-align: center">Tgl Periksa</th>
                            <th style="width: 260px; text-align: center">Pasien</th>
                            <th style="text-align: center">Daftar Obat & Aturan Pakai</th>
                            <th style="width: 170px; text-align: center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr>
                                <td style="text-align: center">{{ $loop->iteration }}</td>
                                <td style="text-align: center">{{ $record->created_at->format('d/m/Y H:i') }}</td>
                                <td style="text-align: center">
                                    <strong>{{ $record->patient->name }}</strong><br>
                                    <small class="text-primary">{{ $record->patient->no_rm }}</small>
                                </td>
                                <td style="text-align: center">
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($record->prescriptions as $p)
                                            <li class="mb-1 border-bottom pb-1">
                                                <i class="ri-checkbox-circle-line text-success me-1"></i>
                                                <strong>{{ $p->medicine->name }}</strong>
                                                ({{ $p->quantity }} {{ $p->medicine->unit }})
                                                <br>
                                                <small class="text-muted">Aturan: {{ $p->instruction }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td style="text-align: center">
                                    <span class="badge bg-label-info">Siap Disiapkan</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Data tidak ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 d-flex justify-content-center">
                {{ $records->onEachSide(1)->links('livewire.layout.pagination-outline-primary') }}
            </div>
        </div>
    </div>
</div>
