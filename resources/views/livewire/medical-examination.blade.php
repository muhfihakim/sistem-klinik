<div class="container-xxl">
    <div class="row">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5>Antrean Periksa</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach ($queues as $q)
                        <button wire:click="examine({{ $q->id }})"
                            class="list-group-item list-group-item-action {{ $selectedAppointment?->id == $q->id ? 'active' : '' }}">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">{{ $q->queue_number }}. {{ $q->patient->name }}</span>
                                <small>{{ $q->status }}</small>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                @if ($selectedAppointment)
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">Periksa: {{ $selectedAppointment->patient->name }}
                            ({{ $selectedAppointment->patient->no_rm }})</h5>
                    </div>
                    <div class="card-body mt-3">
                        <form wire:submit.prevent="store">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary">SUBJECTIVE (S)</label>
                                <textarea wire:model="subjective" class="form-control" placeholder="Keluhan utama pasien..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-info">OBJECTIVE (O)</label>
                                <textarea wire:model="objective" class="form-control" placeholder="Tensi, Suhu, Nadi, Pemeriksaan Fisik..."></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-danger">DIAGNOSIS CODE (ICD-10)</label>
                                    <input wire:model="diagnosis_code" type="text" class="form-control"
                                        placeholder="Contoh: A00.0">
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold text-danger">ASSESSMENT (A)</label>
                                    <textarea wire:model="assessment" class="form-control" placeholder="Hasil diagnosa dokter..."></textarea>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-success">PLAN (P)</label>
                                <textarea wire:model="plan" class="form-control" placeholder="Resep obat, instruksi, atau tindakan..."></textarea>
                            </div>
                            <div class="mt-4 p-3 border rounded bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold text-dark">E-RESEP / OBAT</label>
                                    <button type="button" wire:click="addMedicine"
                                        class="btn btn-sm btn-outline-primary">+ Tambah Obat</button>
                                </div>

                                @foreach ($prescriptions as $index => $item)
                                    <div class="row g-2 mb-2 align-items-end">
                                        <div class="col-md-5">
                                            <select wire:model="prescriptions.{{ $index }}.medicine_id"
                                                class="form-select form-select-sm">
                                                <option value="">-- Pilih Obat --</option>
                                                @foreach ($allMedicines as $med)
                                                    <option value="{{ $med->id }}">{{ $med->name }} (Stok:
                                                        {{ $med->stock }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number"
                                                wire:model="prescriptions.{{ $index }}.quantity"
                                                class="form-control form-control-sm" placeholder="Jml">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text"
                                                wire:model="prescriptions.{{ $index }}.instruction"
                                                class="form-control form-control-sm" placeholder="Aturan Pakai (3x1)">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" wire:click="removeMedicine({{ $index }})"
                                                class="btn btn-sm btn-text-danger">x</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Simpan Rekam Medis & Selesai</button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i class="ri-user-search-line ri-4x text-muted"></i>
                        <p class="mt-3">Pilih pasien dari daftar antrean untuk memulai pemeriksaan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
