@section('Css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <!-- Row Group CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
@endsection

<div class="container-xxl flex-grow-1 container-p-y">



    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-datatable text-nowrap">
            <table class="datatables-basic table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th class="d-flex align-items-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Modal to add new record -->
    <div class="offcanvas offcanvas-end" id="add-new-record">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">New Record</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="add-new-record pt-0 row g-3" id="form-add-new-record" onsubmit="return false">
                <div class="col-sm-12 form-control-validation">
                    <div class="input-group input-group-merge">
                        <span id="basicFullname2" class="input-group-text">
                            <i class="icon-base ri ri-user-line icon-18px"></i>
                        </span>
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="basicFullname" class="form-control dt-full-name"
                                name="basicFullname" placeholder="John Doe" aria-label="John Doe"
                                aria-describedby="basicFullname2" />
                            <label for="basicFullname">Full Name</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 form-control-validation">
                    <div class="input-group input-group-merge">
                        <span id="basicPost2" class="input-group-text">
                            <i class="icon-base ri ri-briefcase-line icon-18px"></i>
                        </span>
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="basicPost" name="basicPost" class="form-control dt-post"
                                placeholder="Web Developer" aria-label="Web Developer" aria-describedby="basicPost2" />
                            <label for="basicPost">Post</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 form-control-validation">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text">
                            <i class="icon-base ri ri-mail-line icon-18px"></i>
                        </span>
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="basicEmail" name="basicEmail" class="form-control dt-email"
                                placeholder="john.doe@example.com" aria-label="john.doe@example.com" />
                            <label for="basicEmail">Email</label>
                        </div>
                    </div>
                    <div class="form-text">You can use letters, numbers & periods</div>
                </div>
                <div class="col-sm-12 form-control-validation">
                    <div class="input-group input-group-merge">
                        <span id="basicDate2" class="input-group-text">
                            <i class="icon-base ri ri-calendar-2-line icon-18px"></i>
                        </span>
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control dt-date" id="basicDate" name="basicDate"
                                aria-describedby="basicDate2" placeholder="MM/DD/YYYY" aria-label="MM/DD/YYYY" />
                            <label for="basicDate">Joining Date</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 form-control-validation">
                    <div class="input-group input-group-merge">
                        <span id="basicSalary2" class="input-group-text">
                            <i class="icon-base ri ri-money-dollar-circle-line icon-18px"></i>
                        </span>
                        <div class="form-floating form-floating-outline">
                            <input type="number" id="basicSalary" name="basicSalary" class="form-control dt-salary"
                                placeholder="12000" aria-label="12000" aria-describedby="basicSalary2" />
                            <label for="basicSalary">Salary</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary data-submit me-sm-4 me-1">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary"
                        data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <!--/ DataTable with Buttons -->



    <h4 class="fw-bold py-3 mb-4">Manajemen Pengguna</h4>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <input type="text" wire:model.live="search" class="form-control w-25" placeholder="Cari nama...">
            <button wire:click="create()" class="btn btn-primary">Tambah Pengguna</button>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-label-info">{{ strtoupper($user->role) }}</span></td>
                            <td>
                                <button wire:click="edit({{ $user->id }})"
                                    class="btn btn-sm btn-warning">Edit</button>
                                <button onclick="confirm('Yakin hapus?') || event.stopImmediatePropagation()"
                                    wire:click="delete({{ $user->id }})"
                                    class="btn btn-sm btn-danger">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $users->links() }}</div>
        </div>
    </div>

    @if ($isOpen)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $userId ? 'Edit User' : 'Tambah User' }}</h5>
                    </div>
                    <form wire:submit.prevent="store">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama</label>
                                <input type="text" wire:model="name" class="form-control">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" wire:model="email" class="form-control">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label>Role</label>
                                <select wire:model="role" class="form-control">
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin">Admin</option>
                                    <option value="doctor">Dokter</option>
                                    <option value="staff">Staf/Kasir</option>
                                </select>
                                @error('role')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label>Password {{ $userId ? '(Kosongkan jika tidak ganti)' : '' }}</label>
                                <input type="password" wire:model="password" class="form-control">
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal()" class="btn btn-secondary">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@section('Scripts')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}" data-navigate-once></script>
    <script src="{{ asset('assets/js/tables-datatables-basic.js') }}" data-navigate-once></script>
@endsection
