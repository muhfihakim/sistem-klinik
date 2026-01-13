<div>

    <!-- Content wrapper -->
    <h4 class="mb-4">Dashboard</h4>

    <!-- Content -->
    <div class="mb-4">

        <div class="row gy-6">

            <!-- Congratulations card -->
            <div class="col-md-12 col-lg-4">
                <div class="card position-relative">
                    <div class="card-body text-nowrap">
                        <h5 class="card-title mb-0 flex-wrap text-nowrap">Halo, {{ auth()->user()->name }}! 👨‍⚕️</h5>
                        <p class="mb-2">Pasien selesai hari ini</p>

                        <h4 class="text-primary mb-0">{{ $completed_today }} Pasien</h4>

                        <p class="mb-2">
                            @if ($total_queue_today > 0)
                                {{ round(($completed_today / $total_queue_today) * 100) }}% dari total antrean 🚀
                            @else
                                Belum ada antrean hari ini
                            @endif
                        </p>

                        <a href="{{ route('queue.index') }}" wire:navigate class="btn btn-sm btn-primary">Lihat
                            Antrean</a>
                    </div>
                    <img src="https://demos.themeselection.com/materio-bootstrap-html-laravel-admin-template-free/demo/assets/img/illustrations/trophy.png"
                        class="position-absolute bottom-0 end-0 me-5 mb-5" width="83" alt="trophy" />
                </div>
            </div>
            <!--/ Congratulations card -->

            <!-- Transactions -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">Statistik Klinik</h5>
                            <div class="dropdown">
                                <button class="btn text-body-secondary p-0" type="button" id="transactionID"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="icon-base ri ri-more-2-line icon-24px"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                    <a class="dropdown-item" href="javascript:void(0);"
                                        wire:click="$refresh">Refresh</a>
                                </div>
                            </div>
                        </div>
                        <p class="small mb-0">Ringkasan data operasional klinik bulan ini</p>
                    </div>
                    <div class="card-body pt-lg-10">
                        <div class="row g-6">
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-primary rounded shadow-xs">
                                            <i class="icon-base ri ri-group-line icon-24px"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-0">Pengguna</p>
                                        <h5 class="mb-0">{{ number_format($total_users) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-success rounded shadow-xs">
                                            <i class="icon-base ri ri-user-heart-line icon-24px"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-0">Pasien</p>
                                        <h5 class="mb-0">{{ number_format($total_patients) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-warning rounded shadow-xs">
                                            <i class="icon-base ri ri-capsule-line icon-24px"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-0">Stok Obat</p>
                                        <h5 class="mb-0">{{ number_format($total_medicines) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar">
                                        <div
                                            class="avatar-initial bg-info rounded shadow-xs d-flex align-items-center justify-content-center">
                                            <i class="ri-list-ordered-2 icon-24px"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-0">Antrean Hari Ini</p>
                                        <h5 class="mb-0">{{ number_format($total_queue_today) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Transactions -->

            <!-- Sales by Countries -->
            <div class="col-xl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Obat Paling Laku</h5>
                    </div>
                    <div class="card-body">
                        @forelse($topMedicines as $med)
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-4">
                                        {{-- Mengambil 2 huruf pertama nama obat sebagai avatar --}}
                                        <div
                                            class="avatar-initial bg-label-{{ ['primary', 'success', 'warning', 'info', 'danger'][$loop->index % 5] }} rounded-circle">
                                            {{ strtoupper(substr($med->name, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $med->name }}</h6>
                                        <small class="text-body-secondary">{{ $med->unit }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-1">{{ number_format($med->total_used) }}</h6>
                                    <small class="text-body-secondary">Terpakai</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">Belum ada data obat digunakan</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <!--/ Sales by Countries -->

            <!-- Deposit / Withdraw -->
            <div class="col-xl-8">
                <div class="card-group">
                    <div class="card mb-0">
                        <div class="card-body card-separator">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                                <h5 class="m-0 me-2">Transaksi Terbaru</h5>
                                <a class="fw-medium" href="{{ route('billing.index') }}" wire:navigate>Lihat Semua</a>
                            </div>
                            <div class="deposit-content pt-2">
                                <ul class="p-0 m-0">
                                    @forelse($recentTransactions as $trx)
                                        <li class="d-flex mb-4 align-items-center pb-2">
                                            <div class="avatar me-3">
                                                <div class="avatar-initial bg-info rounded shadow-xs">
                                                    <i class="icon-base ri ri-money-dollar-circle-line icon-24px"></i>
                                                </div>
                                            </div>

                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0">{{ $trx->patient->name }}</h6>
                                                    <p class="mb-0 small text-muted">{{ $trx->invoice_number }}</p>
                                                </div>
                                                <h6 class="text-success mb-0">
                                                    +Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                                                </h6>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="text-center text-muted small">Belum ada transaksi hari ini</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                                <h5 class="m-0 me-2">Ringkasan Kas</h5>
                            </div>
                            <div class="withdraw-content pt-2">
                                <ul class="p-0 m-0">
                                    <li class="d-flex mb-4 align-items-center pb-2">
                                        <div class="flex-shrink-0 me-4">
                                            <div class="avatar bg-light-success rounded d-flex align-items-center justify-content-center"
                                                style="width: 42px; height: 42px;">
                                                <i class="ri-money-dollar-circle-line text-success"
                                                    style="font-size: 24px; line-height: 1;"></i>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Lunas Hari Ini</h6>
                                                <p class="mb-0 small text-muted">Update: {{ now()->format('d M Y') }}
                                                </p>
                                            </div>
                                            <h6 class="text-dark mb-0">
                                                Rp {{ number_format($revenueToday, 0, ',', '.') }}
                                            </h6>
                                        </div>
                                    </li>
                                </ul>

                                <div class="mt-4 pt-4 border-top text-center">
                                    <p class="small text-muted mb-1">Akumulasi Pendapatan Lunas:</p>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <h4 class="text-primary mb-0 fw-bold">
                                            Rp {{ number_format($total_revenue, 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Deposit / Withdraw -->

        </div>
    </div>
</div>


</div>
