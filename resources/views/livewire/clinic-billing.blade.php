    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Tagihan Tertunda</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($pendingBills as $bill)
                            <button wire:click="selectPatient({{ $bill->id }})"
                                class="list-group-item list-group-item-action {{ $selectedAppointment?->id == $bill->id ? 'active' : '' }}">
                                <strong>{{ $bill->patient->name }}</strong><br>
                                <small>{{ $bill->patient->no_rm }}</small>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    @if ($selectedAppointment)
                        <div class="card-body">
                            <h5>Rincian Invoice: {{ $selectedAppointment->patient->name }}</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td>Jasa Konsultasi Dokter</td>
                                    <td class="text-end">Rp {{ number_format($consultation_fee, 0, ',', '.') }}</td>
                                </tr>
                                @foreach ($selectedAppointment->medicalRecord->prescriptions as $p)
                                    <tr>
                                        <td>{{ $p->medicine->name }} ({{ $p->quantity }} x Rp
                                            {{ number_format($p->medicine->price, 0, ',', '.') }})</td>
                                        <td class="text-end">Rp
                                            {{ number_format($p->quantity * $p->medicine->price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-primary fw-bold">
                                    <td>TOTAL TAGIHAN</td>
                                    <td class="text-end">Rp
                                        {{ number_format($total_medicine_cost + $consultation_fee, 0, ',', '.') }}</td>
                                </tr>
                            </table>

                            <div class="btn-group w-100 mt-3">
                                {{-- tombol utama (default) --}}
                                <button type="button" class="btn btn-success w-100" wire:click="payCash">
                                    <i class="ri-cash-line me-2"></i> Tunai
                                </button>

                                {{-- tombol dropdown split --}}
                                <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end w-100">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                            wire:click="payCash">
                                            <i class="ri-cash-line me-2"></i> Tunai
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="javascript:void(0)"
                                            wire:click="processPayment">
                                            <i class="ri-wallet-line me-2"></i> Non Tunai (Midtrans)
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    @else
                        <div class="card-body text-center py-5">
                            <i class="ri-bill-line ri-3x text-muted"></i>
                            <p class="mt-3">Pilih pasien untuk melihat rincian tagihan.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Transaksi Selesai (Hari Ini)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Pasien</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\Billing::where('status', 'paid')->whereDate('created_at', now())->get() as $paid)
                                <tr>
                                    <td>{{ $paid->patient->name }}</td>
                                    <td>Rp {{ number_format($paid->total_amount) }}</td>
                                    <td>
                                        <a href="{{ route('invoice.download', $paid->id) }}"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="ri-file-pdf-line"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Script diletakkan di dalam DIV Root --}}
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
        </script>

        <script>
            // Pastikan script jalan baik saat refresh maupun pindah halaman (Livewire SPA)
            function initMidtrans() {
                window.addEventListener('show-snap-modal', event => {
                    console.log('Token diterima:', event.detail.token); // Untuk cek di console
                    window.snap.pay(event.detail.token, {
                        onSuccess: function(result) {
                            window.location.reload();
                        },
                        onPending: function(result) {
                            alert("Menunggu pembayaran!");
                        },
                        onError: function(result) {
                            alert("Pembayaran gagal!");
                        }
                    });
                });
            }

            // Jalankan untuk Livewire v3
            document.addEventListener('livewire:navigated', initMidtrans);
            // Jalankan untuk load pertama kali
            document.addEventListener('DOMContentLoaded', initMidtrans);
        </script>
    </div>
