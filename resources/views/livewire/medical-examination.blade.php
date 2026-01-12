<div>



    <h4 class="mb-4">Manajemen Pemeriksaan</h4>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="row g-4">

        {{-- KIRI: Antrean Periksa --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Antrean Periksa</h5>
                </div>

                <div class="card-body position-relative">

                    <div class="form-floating form-floating-outline mb-6">
                        <input id="searchQueue" type="text" wire:model.live="searchQueue" class="form-control"
                            placeholder="Cari antrean" autocomplete="off">
                        <label for="searchQueue">Cari Antrean (Nama / No. RM / No)</label>
                    </div>

                    <div wire:loading.flex wire:target="examine,searchQueue"
                        class="position-absolute top-50 start-50 translate-middle z-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <div wire:loading.class="opacity-50" wire:target="examine,searchQueue">
                        <div class="list-group list-group-flush">
                            @forelse ($queues as $q)
                                <button type="button" wire:click="examine({{ $q->id }})"
                                    class="list-group-item list-group-item-action {{ $selectedAppointment?->id == $q->id ? 'active' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ $q->queue_number }}. {{ $q->patient->name }}</span>
                                        <small class="text-muted">{{ $q->patient->no_rm }}</small>
                                    </div>
                                    <div class="mt-1">
                                        <span
                                            class="badge bg-label-{{ $q->status === 'waiting' ? 'warning' : 'info' }}">
                                            {{ ucfirst($q->status) }}
                                        </span>
                                    </div>
                                </button>
                            @empty
                                <div class="text-center text-muted py-4">Tidak ada antrean.</div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- KANAN: Form Pemeriksaan --}}
        <div class="col-md-8">
            <div class="card">

                @if ($selectedAppointment)
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Periksa: {{ $selectedAppointment->patient->name }}</h5>
                            <small class="text-muted">
                                No. RM: {{ $selectedAppointment->patient->no_rm }} | No Antrean:
                                {{ $selectedAppointment->queue_number }}
                            </small>
                        </div>
                    </div>

                    <div class="card-body position-relative">

                        <div wire:loading.flex
                            wire:target="store,openPrescriptionCanvas,addPrescriptionItem,removeMedicine"
                            class="position-absolute top-50 start-50 translate-middle z-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <div wire:loading.class="opacity-50"
                            wire:target="store,openPrescriptionCanvas,addPrescriptionItem,removeMedicine">

                            <form wire:submit.prevent="store">

                                <div class="form-floating form-floating-outline mb-6">
                                    <textarea id="subjective" wire:model="subjective" class="form-control" placeholder="Keluhan utama pasien..."
                                        style="height:110px"></textarea>
                                    <label for="subjective">SUBJECTIVE (S)</label>
                                    @error('subjective')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-floating form-floating-outline mb-6">
                                    <textarea id="objective" wire:model="objective" class="form-control" placeholder="Tensi, Suhu, Nadi..."
                                        style="height:110px"></textarea>
                                    <label for="objective">OBJECTIVE (O)</label>
                                    @error('objective')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline mb-6">
                                            <input id="diagnosisCode" type="text" wire:model="diagnosis_code"
                                                class="form-control" placeholder="Contoh: A00.0" autocomplete="off">
                                            <label for="diagnosisCode">DIAGNOSIS CODE (ICD-10)</label>
                                            @error('diagnosis_code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-floating form-floating-outline mb-6">
                                            <textarea id="assessment" wire:model="assessment" class="form-control" placeholder="Hasil diagnosa dokter..."
                                                style="height:110px"></textarea>
                                            <label for="assessment">ASSESSMENT (A)</label>
                                            @error('assessment')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-floating form-floating-outline mb-6">
                                    <textarea id="plan" wire:model="plan" class="form-control" placeholder="Resep obat, instruksi, tindakan..."
                                        style="height:110px"></textarea>
                                    <label for="plan">PLAN (P)</label>
                                    @error('plan')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- E-RESEP --}}
                                <div class="p-3 border rounded bg-light mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="fw-bold mb-0">E-RESEP / OBAT</label>

                                        <button type="button" data-bs-toggle="offcanvas"
                                            data-bs-target="#prescriptionCanvas" wire:click="openPrescriptionCanvas"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-lg me-2"></i>Tambah Obat
                                        </button>
                                    </div>

                                    @if (count($prescriptions) === 0)
                                        <div class="text-muted">Belum ada obat ditambahkan.</div>
                                    @else
                                        <ul class="list-group">
                                            @foreach ($prescriptions as $index => $item)
                                                @php
                                                    $medName = optional(
                                                        $allMedicines->firstWhere('id', $item['medicine_id']),
                                                    )->name;
                                                @endphp

                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold">{{ $medName ?? 'Obat dipilih' }}</div>
                                                        <small class="text-muted">
                                                            Qty: {{ $item['quantity'] }} | {{ $item['instruction'] }}
                                                        </small>
                                                    </div>

                                                    <button type="button"
                                                        wire:click="removeMedicine({{ $index }})"
                                                        class="btn btn-sm btn-danger"><i
                                                            class="bi bi-trash3 me-2"></i>
                                                        Hapus
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        Simpan Rekam Medis & Selesai
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i class="ri-user-search-line ri-4x text-muted"></i>
                        <p class="mt-3 mb-0">Pilih pasien dari antrean untuk memulai pemeriksaan.</p>
                    </div>
                @endif

            </div>
        </div>

    </div>

    {{-- OFFCANVAS: Tambah Obat --}}
    <div wire:ignore.self class="offcanvas offcanvas-end" tabindex="-1" id="prescriptionCanvas"
        aria-labelledby="prescriptionCanvasLabel">
        <div class="offcanvas-header">
            <h5 id="prescriptionCanvasLabel" class="offcanvas-title">Tambah Obat</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">

            <div wire:loading.flex wire:target="addPrescriptionItem,openPrescriptionCanvas"
                class="justify-content-center my-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div wire:loading.class="opacity-50" wire:target="addPrescriptionItem,openPrescriptionCanvas">

                <div class="form-floating form-floating-outline mb-4">
                    <select class="form-select h-100" id="medicinePick" wire:model="canvas_medicine_id">
                        <option value="">-- Pilih Obat --</option>
                        @foreach ($allMedicines as $med)
                            <option value="{{ $med->id }}">
                                {{ $med->name }} (Stok: {{ $med->stock }})
                            </option>
                        @endforeach
                    </select>
                    <label for="medicinePick">Obat</label>
                    @error('canvas_medicine_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-floating form-floating-outline mb-4">
                    <input type="number" min="1" class="form-control" id="medicineQty"
                        wire:model="canvas_quantity" placeholder="Qty">
                    <label for="medicineQty">Jumlah</label>
                    @error('canvas_quantity')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-floating form-floating-outline mb-4">
                    <input type="text" class="form-control" id="medicineInstruction"
                        wire:model="canvas_instruction" placeholder="Aturan pakai">
                    <label for="medicineInstruction">Aturan Pakai</label>
                    @error('canvas_instruction')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="button" class="btn btn-primary w-100 mb-2" wire:click="addPrescriptionItem">
                    Tambah ke Resep
                </button>

                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="offcanvas">
                    Batal
                </button>

            </div>
        </div>
    </div>

</div>

@section('Scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            if (window.__prescriptionCanvasBound) return;
            window.__prescriptionCanvasBound = true;

            const getCanvas = () => {
                const el = document.getElementById('prescriptionCanvas');
                return el ? bootstrap.Offcanvas.getOrCreateInstance(el) : null;
            };

            Livewire.on('open-prescription-canvas', () => getCanvas()?.show());
            Livewire.on('close-prescription-canvas', () => getCanvas()?.hide());
        });
    </script>
@endsection
