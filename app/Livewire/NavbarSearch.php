<?php

namespace App\Livewire;

use Livewire\Component;

class NavbarSearch extends Component
{
    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $role = auth()->user()->role;

        // Daftar menu disesuaikan dengan sidebar Anda
        $menuList = [
            ['title' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'ri-home-smile-line', 'roles' => ['admin', 'staff', 'doctor']],

            // Data Master
            ['title' => 'Data Master: Pengguna', 'route' => 'users.index', 'icon' => 'bi-people-fill', 'roles' => ['admin']],
            ['title' => 'Data Master: Pasien', 'route' => 'patients.index', 'icon' => 'bi-person-heart', 'roles' => ['admin', 'staff']],
            ['title' => 'Data Master: Obat', 'route' => 'medicines.index', 'icon' => 'bi-capsule', 'roles' => ['admin', 'staff']],

            // Pelayanan
            ['title' => 'Layanan: Antrean', 'route' => 'queue.index', 'icon' => 'bi-list-task', 'roles' => ['admin', 'staff']],
            ['title' => 'Layanan: Medical (RME)', 'route' => 'medical.examination', 'icon' => 'bi-file-medical', 'roles' => ['admin', 'doctor']],
            ['title' => 'Layanan: Resep Obat', 'route' => 'prescriptions.index', 'icon' => 'bi-prescription', 'roles' => ['admin', 'staff', 'doctor']],
            ['title' => 'Layanan: Pembayaran', 'route' => 'billing.index', 'icon' => 'bi-credit-card-2-back', 'roles' => ['admin', 'staff']],
        ];

        $this->results = collect($menuList)
            ->filter(function ($menu) use ($role) {
                // Filter berdasarkan pencarian judul DAN izin akses role
                return str_contains(strtolower($menu['title']), strtolower($this->query))
                    && in_array($role, $menu['roles']);
            })
            ->take(5)
            ->toArray();
    }

    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.navbar-search');
    }
}
