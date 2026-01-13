<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Billing;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Midtrans\Config;
use Midtrans\Snap;

class ClinicBilling extends Component
{
    use WithPagination;

    #[Layout('layouts.klinik')]
    public $selectedAppointment;
    public $total_medicine_cost = 0;
    public $consultation_fee = 50000;

    public function selectPatient($id)
    {
        $this->selectedAppointment = Appointment::with(['patient', 'medicalRecord.prescriptions.medicine'])->find($id);
        $this->total_medicine_cost = 0;
        foreach ($this->selectedAppointment->medicalRecord->prescriptions as $p) {
            $this->total_medicine_cost += ($p->quantity * $p->medicine->price);
        }
    }

    public function processPayment()
    {
        // 1. Pengaman: Jika belum pilih pasien
        if (!$this->selectedAppointment) {
            session()->flash('error', 'Silakan pilih pasien terlebih dahulu.');
            return;
        }

        try {
            $total = $this->total_medicine_cost + $this->consultation_fee;

            // 2. Gunakan updateOrCreate untuk menghindari duplikasi invoice jika diklik ulang
            $billing = Billing::updateOrCreate(
                ['appointment_id' => $this->selectedAppointment->id],
                [
                    'patient_id' => $this->selectedAppointment->patient_id,
                    'invoice_number' => 'INV-' . time(), // Invoice baru jika belum ada
                    'total_amount' => $total,
                    'status' => 'unpaid'
                ]
            );

            // 3. Konfigurasi Midtrans
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $billing->invoice_number,
                    'gross_amount' => (int) $total,
                ],
                'customer_details' => [
                    'first_name' => $this->selectedAppointment->patient->name,
                ],
            ];

            // 4. Ambil Token dari Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // 5. PENTING: Tambahkan 'patient_name' di dispatch
            // agar JavaScript bisa menangkapnya untuk pesan sukses nanti
            $this->dispatch(
                'show-snap-modal',
                token: $snapToken,
                patient_name: $this->selectedAppointment->patient->name
            );
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan Midtrans: ' . $e->getMessage());
        }
    }

    #[On('payment-finished')]
    public function handlePaymentFinished($status, $name)
    {
        if ($status === 'success') {
            // 1. Simpan pesan ke session
            session()->flash('success', 'Pembayaran Non-Tunai berhasil untuk pasien: ' . $name);

            // 2. Reset properti agar tampilan bersih
            $this->selectedAppointment = null;
            $this->total_medicine_cost = 0;

            // 3. Redirect murni (Hard Redirect) untuk membersihkan Query String Midtrans
            return redirect()->route('billing.index');
        }
    }

    public function payCash()
    {
        if (!$this->selectedAppointment) {
            session()->flash('error', 'Silakan pilih pasien terlebih dahulu.');
            return;
        }

        try {
            $total = $this->total_medicine_cost + $this->consultation_fee;

            $billing = Billing::updateOrCreate(
                ['appointment_id' => $this->selectedAppointment->id],
                [
                    'patient_id' => $this->selectedAppointment->patient_id,
                    'invoice_number' => 'INV-' . time(),
                    'total_amount' => $total,
                    'status' => 'paid',
                    'payment_method' => 'cash',
                    'paid_at' => now(),
                ]
            );

            // Gunakan 'success' agar sesuai dengan alert di view
            session()->flash('success', 'Pembayaran tunai berhasil diproses untuk pasien ' . $this->selectedAppointment->patient->name);

            // Reset state agar form rincian tertutup dan kembali ke "Pilih Pasien"
            $this->selectedAppointment = null;
            $this->total_medicine_cost = 0;
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // public function render()
    // {
    //     $pendingBills = Appointment::with(['patient', 'medicalRecord.prescriptions.medicine'])
    //         ->where('status', 'finished')
    //         ->where(function ($query) {
    //             $query->whereDoesntHave('billing')
    //                 ->orWhereHas('billing', function ($q) {
    //                     $q->where('status', 'unpaid');
    //                 });
    //         })->get();

    //     return view('livewire.clinic-billing', compact('pendingBills'));
    // }

    public function render()
    {
        // 1. Tagihan Tertunda (Tetap menggunakan get() atau limit jika ingin dibatasi)
        $pendingBills = Appointment::with(['patient', 'medicalRecord.prescriptions.medicine'])
            ->where('status', 'finished')
            ->where(function ($query) {
                $query->whereDoesntHave('billing')
                    ->orWhereHas('billing', function ($q) {
                        $q->where('status', 'unpaid');
                    });
            })
            ->latest()
            ->get();

        // 2. Transaksi Selesai (Menggunakan Pagination)
        $paidTransactions = Billing::with('patient')
            ->where('status', 'paid')
            ->whereDate('created_at', now())
            ->latest()
            ->paginate(5); // Hanya ini yang dipaginasi

        return view('livewire.clinic-billing', [
            'pendingBills' => $pendingBills,
            'paidTransactions' => $paidTransactions
        ]);
    }
}
