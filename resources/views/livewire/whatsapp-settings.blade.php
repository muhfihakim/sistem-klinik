{{-- Tambahkan polling di sini agar status otomatis update dari Node.js setiap 3-5 detik --}}
<div wire:poll.3s="checkStatus">

    <h4 class="mb-4">WhatsApp Gateway</h4>

    {{-- Alert konsisten --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Status Koneksi</h5>
                    <button wire:click="checkStatus" class="btn btn-sm btn-outline-primary">
                        <i class="ri-refresh-line me-1"></i> Refresh Manual
                    </button>
                </div>

                <div class="card-body text-center py-5">
                    {{-- 1. KONDISI TERHUBUNG --}}
                    @if ($status === 'connected')
                        <div class="avatar avatar-xl mb-3 mx-auto">
                            <span class="avatar-initial rounded-circle bg-label-success">
                                <i class="ri-whatsapp-line ri-4x"></i>
                            </span>
                        </div>
                        <h4 class="text-success mb-1">Terhubung</h4>
                        <p class="text-muted">WhatsApp Anda siap mengirim pesan.</p>

                        <div class="mt-4 p-3 bg-light rounded border text-start">
                            <div class="d-flex align-items-center mb-1">
                                <i class="ri-smartphone-line me-2"></i>
                                <span><strong>Perangkat Terhubung:</strong></span>
                            </div>
                            <div class="ms-4">
                                <span class="badge bg-label-primary fs-6">{{ $user['name'] ?? '-' }}</span>
                            </div>
                        </div>

                        <button wire:click="logout" wire:loading.attr="disabled" class="btn btn-danger mt-4 w-100">
                            <span wire:loading wire:target="logout"
                                class="spinner-border spinner-border-sm me-2"></span>
                            <i class="ri-logout-box-line me-2" wire:loading.remove wire:target="logout"></i>
                            Putuskan Koneksi (Logout)
                        </button>

                        {{-- 2. KONDISI DISCONNECTED (MENAMPILKAN QR) --}}
                    @elseif($status === 'disconnected' && $qrCode)
                        <div class="mb-4 text-center">
                            <h5 class="mb-3 fw-bold">Scan QR Code</h5>
                            <div class="d-inline-block p-3 border rounded bg-white shadow-sm">
                                {{-- Render QR Code dari string yang dikirim Node.js --}}
                                {!! QrCode::size(250)->margin(1)->generate($qrCode) !!}
                            </div>
                            <p class="mt-4 text-muted">
                                Buka WhatsApp > Perangkat Tertaut > Tautkan Perangkat
                            </p>
                            <div class="alert alert-info py-2 small mx-3">
                                <i class="ri-information-line me-1"></i> Halaman akan otomatis berubah jika scan
                                berhasil.
                            </div>
                        </div>

                        {{-- 3. KONDISI SERVER MATI / LOADING --}}
                    @else
                        <div class="avatar avatar-xl mb-3 mx-auto">
                            <span class="avatar-initial rounded-circle bg-label-danger">
                                <i class="ri-error-warning-line ri-4x"></i>
                            </span>
                        </div>
                        <h4 class="text-danger mb-1">Server Offline</h4>
                        <p class="text-muted px-4">Pastikan service Node.js sudah dijalankan (<code>node
                                index.js</code>).</p>
                        <button wire:click="checkStatus" class="btn btn-primary mt-3">
                            <i class="ri-refresh-line me-2"></i> Coba Hubungkan Kembali
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-none bg-label-primary h-100">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="ri-shield-keyhole-line me-2"></i>Tips Keamanan</h5>
                    <ul class="list-unstyled mb-0 mt-3">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="ri-checkbox-circle-line me-2 text-primary"></i>
                            <span>Gunakan nomor khusus klinik (bukan pribadi) untuk menghindari risiko banned.</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="ri-checkbox-circle-line me-2 text-primary"></i>
                            <span>Jangan mengirim pesan terlalu banyak secara bersamaan (spamming).</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="ri-checkbox-circle-line me-2 text-primary"></i>
                            <span>Pastikan koneksi internet server stabil agar pengiriman tidak tertunda.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
