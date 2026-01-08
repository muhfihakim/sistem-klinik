<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Laporan E-Resep (Farmasi)</h5>
        </div>
        <div class="card-body">
            <div class="mb-3 col-md-4">
                <input type="text" class="form-control" placeholder="Cari Nama/No RM..." wire:model.live="search">
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tgl Periksa</th>
                            <th>Pasien</th>
                            <th>Daftar Obat & Aturan Pakai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $record)
                            <tr>
                                <td>{{ $record->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $record->patient->name }}</strong><br>
                                    <small class="text-primary">{{ $record->patient->no_rm }}</small>
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($record->prescriptions as $p)
                                            <li class="mb-1 border-bottom pb-1">
                                                <i class="ri-checkbox-circle-line text-success"></i>
                                                <strong>{{ $p->medicine->name }}</strong>
                                                ({{ $p->quantity }} {{ $p->medicine->unit }})
                                                <br><small class="text-muted">Aturan: {{ $p->instruction }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">Siap Disiapkan</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
