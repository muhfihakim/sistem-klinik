<div>

    <div class="navbar-nav align-items-center w-100" style="position: relative;">
        <div class="nav-item d-flex align-items-center w-100">
            <i class="icon-base ri ri-search-line icon-lg lh-0"></i>
            <input type="text" wire:model.live="query" class="form-control border-0 shadow-none ps-3"
                placeholder="Cari menu atau layanan..." autocomplete="off">
        </div>

        @if (!empty($results))
            <div class="card shadow-lg border mt-2"
                style="position: absolute; top: 100%; left: 0; width: 100%; max-width: 400px; z-index: 1060;">
                <div class="list-group list-group-flush">
                    @foreach ($results as $result)
                        <a href="{{ route($result['route']) }}" wire:navigate wire:click="$set('query', '')"
                            class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="{{ $result['icon'] }}"></i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <small class="text-muted line-height-1 mb-1">Navigasi ke</small>
                                <span class="fw-semibold text-heading">{{ $result['title'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Overlay transparan untuk menutup dropdown saat klik di luar --}}
            <div wire:click="$set('query', '')"
                style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 1050; background: transparent;">
            </div>
        @endif
    </div>

</div>
