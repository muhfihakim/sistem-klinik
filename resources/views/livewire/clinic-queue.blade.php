<div>

    <h4 class="mb-4">Layanan Antrean</h4>

    {{-- Alert konsisten --}}
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- CARD: Daftarkan Antrean --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Daftarkan Antrean</h5>
        </div>

        <div class="card-body position-relative">

            {{-- Spinner saat cari pasien / pilih --}}
            <div wire:loading.flex wire:target="searchPatient,addToQueue"
                class="position-absolute top-50 start-50 translate-middle z-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div wire:loading.class="opacity-50" wire:target="searchPatient,addToQueue">

                <div class="form-floating form-floating-outline mb-6">
                    <input id="searchPatient" wire:model.live="searchPatient" type="text" class="form-control"
                        placeholder="Cari Pasien" autocomplete="off">
                    <label for="searchPatient">Cari Pasien (Nama / No. RM)</label>
                </div>

                @if (strlen($searchPatient ?? '') > 0 && strlen($searchPatient ?? '') < 3)
                    <small class="text-muted d-block mb-3">
                        Ketik minimal 3 karakter untuk mencari pasien.
                    </small>
                @endif

                @if (!empty($patients))
                    <ul class="list-group">
                        @foreach ($patients as $p)
                            @php
                                $isInQueue = in_array($p->id, $activePatientIds ?? []);
                            @endphp

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="d-block fw-bold">{{ $p->no_rm }}</small>
                                    {{ $p->name }}

                                    @if ($isInQueue)
                                        <div><small class="text-danger">Sudah dalam antrean</small></div>
                                    @endif
                                </div>

                                @if ($isInQueue)
                                    <button type="button" class="btn btn-sm btn-secondary" disabled>
                                        Sudah antrean
                                    </button>
                                @else
                                    <button type="button" wire:click="addToQueue({{ $p->id }})"
                                        class="btn btn-sm btn-primary">
                                        Pilih
                                    </button>
                                @endif
                            </li>
                        @endforeach

                    </ul>
                @endif

            </div>
        </div>
    </div>

    {{-- CARD: Antrean Hari Ini --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Antrean Hari Ini ({{ date('d M Y') }})</h5>

            {{-- Search antrean (kanan) --}}
            <input type="text" wire:model.live="searchQueue" class="form-control w-25" placeholder="Cari antrean...">
        </div>

        <div class="card-body position-relative p-0">

            {{-- Spinner saat filter antrean --}}
            <div wire:loading.flex wire:target="nextPage,previousPage,gotoPage,searchQueue"
                class="position-absolute top-50 start-50 translate-middle z-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div wire:loading.class="opacity-50" wire:target="nextPage,previousPage,gotoPage,searchQueue"
                class="table-responsive text-nowrap">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th style="text-align:center; width:90px;">No</th>
                            <th style="text-align:center;">Nama Pasien</th>
                            <th style="text-align:center;">No. RM</th>
                            <th style="text-align:center;">No. Antrean</th>
                            <th style="text-align:center; width:140px;">Status</th>
                            <th style="text-align:center; width:140px;">Aksi</th>

                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($queues as $q)
                            <tr>
                                <td style="text-align:center;">{{ $q->queue_number }}
                                </td>
                                <td style="text-align:center;">
                                    {{ $q->patient->name }}
                                </td>
                                <td style="text-align:center;">
                                    {{ $q->patient->no_rm }}
                                </td>
                                <td style="text-align:center;">
                                    {{ $q->queue_number }}
                                </td>
                                <td style="text-align:center;">
                                    <span
                                        class="badge bg-label-
          {{ $q->status === 'waiting' ? 'warning' : ($q->status === 'cancelled' ? 'danger' : 'success') }}">
                                        {{ ucfirst($q->status) }}
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    @if ($q->status === 'waiting')
                                        <button wire:click="confirmCancel({{ $q->id }})" data-bs-toggle="modal"
                                            data-bs-target="#cancelQueueModal" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-circle me-2"></i>Batalkan
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Belum ada antrean hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 d-flex justify-content-center">
                {{ $queues->onEachSide(1)->links('livewire.layout.pagination-outline-primary') }}
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="cancelQueueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Batalkan Antrean</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0">
                        Yakin ingin membatalkan antrean pasien ini?
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="cancelQueue">
                        Ya, Batalkan
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

@section('Scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            if (window.__queueCancelBound) return;
            window.__queueCancelBound = true;

            const getModal = (id) => {
                const el = document.getElementById(id);
                return el ? bootstrap.Modal.getOrCreateInstance(el) : null;
            };

            Livewire.on('open-cancel-queue-modal', () => getModal('cancelQueueModal')?.show());
            Livewire.on('close-cancel-queue-modal', () => getModal('cancelQueueModal')?.hide());
        });
    </script>
@endsection
