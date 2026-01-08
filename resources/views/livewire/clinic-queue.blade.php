<div class="container-xxl">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftarkan Antrean</h5>
                </div>
                <div class="card-body">
                    <label class="form-label">Cari Pasien (Nama/No RM)</label>
                    <input wire:model.live="search" type="text" class="form-control"
                        placeholder="Ketik min. 3 huruf...">

                    @if (!empty($patients))
                        <ul class="list-group mt-2">
                            @foreach ($patients as $p)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="d-block fw-bold">{{ $p->no_rm }}</small>
                                        {{ $p->name }}
                                    </div>
                                    <button wire:click="addToQueue({{ $p->id }})"
                                        class="btn btn-sm btn-primary">Pilih</button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Antrean Hari Ini ({{ date('d M Y') }})</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pasien</th>
                                <th>No. RM</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($queues as $q)
                                <tr>
                                    <td>
                                        <h4 class="mb-0 text-primary">{{ $q->queue_number }}</h4>
                                    </td>
                                    <td><strong>{{ $q->patient->name }}</strong></td>
                                    <td>{{ $q->patient->no_rm }}</td>
                                    <td>
                                        <span
                                            class="badge bg-label-{{ $q->status == 'waiting' ? 'warning' : 'success' }}">
                                            {{ ucfirst($q->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
