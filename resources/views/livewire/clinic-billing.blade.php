<div>

    <h4 class="mb-4">Layanan Pembayaran (Kasir)</h4>

    {{-- Alert konsisten --}}
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-label-warning">
                    <h5 class="mb-0">Tagihan Tertunda</h5>
                </div>
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse ($pendingBills as $bill)
                        <button wire:click="selectPatient({{ $bill->id }})"
                            class="list-group-item list-group-item-action {{ $selectedAppointment?->id == $bill->id ? 'active' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $bill->patient->name }}</strong><br>
                                    <small
                                        class="d-block {{ $selectedAppointment?->id == $bill->id ? '' : 'text-muted' }}">
                                        {{ $bill->patient->no_rm }}
                                    </small>
                                </div>
                                <i class="ri-arrow-right-s-line"></i>
                            </div>
                        </button>
                    @empty
                        <div class="list-group-item text-center text-muted py-3">Tidak ada tagihan tertunda</div>
                    @endforelse
                </div>
            </div>

            <div wire:loading.flex wire:target="nextPage,previousPage,gotoPage,searchBill"
                class="position-absolute top-50 start-50 translate-middle z-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div class="card" wire:poll.10s>
                <div class="card-header bg-label-success">
                    <h5 class="mb-0">Transaksi Selesai</h5>
                </div>
                <div wire:loading.class="opacity-50" wire:target="nextPage,previousPage,gotoPage,searchBill"
                    class="table-responsive text-nowrap">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Pasien</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paidTransactions as $index => $paid)
                                <tr>
                                    {{-- Penomoran yang sinkron dengan halaman --}}
                                    <td>{{ $paidTransactions->firstItem() + $index }}</td>
                                    <td>
                                        <small class="fw-bold d-block">{{ $paid->patient->name }}</small>
                                        <small class="text-muted">Rp
                                            {{ number_format($paid->total_amount, 0, ',', '.') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('invoice.download', $paid->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-filetype-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">Belum ada transaksi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Navigasi Pagination --}}
                <div class="p-3 d-flex justify-content-center">
                    {{ $paidTransactions->onEachSide(1)->links('livewire.layout.pagination-outline-primary') }}
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card h-100">
                @if ($selectedAppointment)
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="mb-0">Rincian Invoice: {{ $selectedAppointment->patient->name }}</h5>
                        <span class="badge bg-label-primary">{{ $selectedAppointment->patient->no_rm }}</span>
                    </div>
                    <div class="card-body pt-4">
                        <table class="table table-borderless">
                            <tr>
                                <td>Jasa Konsultasi Dokter</td>
                                <td class="text-end">Rp {{ number_format($consultation_fee, 0, ',', '.') }}</td>
                            </tr>
                            @foreach ($selectedAppointment->medicalRecord->prescriptions as $p)
                                <tr>
                                    <td>
                                        {{ $p->medicine->name }}
                                        <small class="text-muted d-block">{{ $p->quantity }} x Rp
                                            {{ number_format($p->medicine->price, 0, ',', '.') }}</small>
                                    </td>
                                    <td class="text-end align-bottom">
                                        Rp {{ number_format($p->quantity * $p->medicine->price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="border-top fw-bold">
                                <td class="pt-3 text-primary">TOTAL TAGIHAN</td>
                                <td class="pt-3 text-end text-primary h5">
                                    Rp {{ number_format($total_medicine_cost + $consultation_fee, 0, ',', '.') }}
                                </td>
                            </tr>
                        </table>

                        <div class="mt-4">
                            <button type="button" class="btn btn-outline-success w-100 p-2"
                                wire:click="sendWhatsAppBilling" wire:loading.attr="disabled">
                                <span wire:loading wire:target="sendWhatsAppBilling"
                                    class="spinner-border spinner-border-sm me-2"></span>
                                <i class="ri-whatsapp-line me-2" wire:loading.remove
                                    wire:target="sendWhatsAppBilling"></i>
                                Kirim Tagihan ke WhatsApp Pasien
                            </button>
                        </div>

                        <div class="mt-5">
                            <label class="mb-2 fw-bold text-uppercase"
                                style="font-size: 0.8rem; letter-spacing: 1px;">Metode Pembayaran:</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <button type="button"
                                        class="btn btn-success w-100 py-3 fw-bold d-flex align-items-center justify-content-center"
                                        wire:click="payCash" wire:loading.attr="disabled">
                                        <span wire:loading wire:target="payCash"
                                            class="spinner-border spinner-border-sm me-2"></span>
                                        <i class="ri-cash-line ri-lg me-2" wire:loading.remove
                                            wire:target="payCash"></i>
                                        BAYAR TUNAI (CASH)
                                    </button>
                                </div>

                                <div class="col-md-6">
                                    <button type="button"
                                        class="btn btn-primary w-100 py-3 fw-bold d-flex align-items-center justify-content-center"
                                        wire:click="processPayment" wire:loading.attr="disabled">
                                        <span wire:loading wire:target="processPayment"
                                            class="spinner-border spinner-border-sm me-2"></span>
                                        <i class="ri-wallet-line ri-lg me-2" wire:loading.remove
                                            wire:target="processPayment"></i>
                                        NON TUNAI (MIDTRANS)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                        <i class="ri-bill-line ri-4x text-light mb-3"></i>
                        <h6 class="text-muted">Pilih pasien di sisi kiri untuk memproses tagihan.</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('Scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
    </script>

    <script>
        function initMidtrans() {
            console.log('Inisialisasi Midtrans Snap...');

            window.addEventListener('show-snap-modal', event => {
                const snapToken = event.detail.token;
                const patientName = event.detail.patient_name;

                if (snapToken) {
                    window.snap.pay(snapToken, {
                        onSuccess: function(result) {
                            console.log('Pembayaran Berhasil:', result);

                            // Kirim sinyal ke server
                            Livewire.dispatch('payment-finished', {
                                status: 'success',
                                name: patientName
                            });
                        },
                        onPending: function(result) {
                            alert("Menunggu pembayaran. Silakan cek aplikasi Anda.");
                        },
                        onError: function(result) {
                            alert("Pembayaran gagal, silakan coba lagi.");
                        },
                        onClose: function() {
                            console.log('User menutup popup.');
                        }
                    });
                }
            });
        }

        document.addEventListener('livewire:navigated', initMidtrans);
        document.addEventListener('DOMContentLoaded', initMidtrans);
    </script>
@endpush
