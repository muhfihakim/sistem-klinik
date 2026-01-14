<x-klinik-layout>
    <h4 class="mb-4">Pengaturan Profil</h4>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-2 gap-lg-0" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-account">
                            <i class="ri-user-3-line me-1_5"></i> Akun
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-security">
                            <i class="ri-lock-line me-1_5"></i> Keamanan
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content p-0" style="background: none; border: none; box-shadow: none;">
                <div class="tab-pane fade show active" id="navs-pills-account" role="tabpanel">
                    <livewire:profile.update-profile-information-form />
                </div>

                <div class="tab-pane fade" id="navs-pills-security" role="tabpanel">
                    <livewire:profile.update-password-form />
                </div>
            </div>
        </div>
    </div>
</x-klinik-layout>
