<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Patient;
use App\Models\Medicine;
use App\Models\Billing;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
    #[Layout('layouts.klinik')]
    public function render()
    {
        // Ambil 5 transaksi terakhir yang lunas berdasarkan waktu update terbaru
        $recentTransactions = Billing::with('patient')
            ->where('status', 'paid')
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Pendapatan Hari Ini menggunakan updated_at
        $revenueToday = Billing::where('status', 'paid')
            ->whereDate('updated_at', now())
            ->sum('total_amount');

        // Total Tagihan yang Belum Dibayar (Status unpaid)
        $unpaidTotal = Billing::where('status', 'unpaid')
            ->sum('total_amount');

        // Gabungkan dengan stats yang sudah ada sebelumnya
        $data = [
            'total_users' => User::count(),
            'total_patients' => Patient::count(),
            'total_medicines' => Medicine::count(),
            'total_revenue' => Billing::where('status', 'paid')->sum('total_amount'),
            'completed_today' => Appointment::where('status', 'finished')->whereDate('updated_at', now())->count(),
            'total_queue_today' => Appointment::whereDate('created_at', now())->count(),
        ];

        $topMedicines = Medicine::withCount(['prescriptions as total_used'])
            ->orderBy('total_used', 'desc')
            ->take(5)
            ->get();

        return view('livewire.dashboard', array_merge($data, [
            'topMedicines' => $topMedicines,
            'recentTransactions' => $recentTransactions,
            'revenueToday' => $revenueToday,
            'unpaidTotal' => $unpaidTotal
        ]));
    }
}
