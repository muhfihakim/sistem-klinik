<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Billing;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Midtrans\Config;
use Midtrans\Snap;

class ClinicBilling extends Component
{
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

    // public function processPayment()
    // {
    //     $total = $this->total_medicine_cost + $this->consultation_fee;

    //     // 1. Buat/Update Record Billing
    //     $billing = Billing::updateOrCreate(
    //         ['appointment_id' => $this->selectedAppointment->id],
    //         [
    //             'patient_id' => $this->selectedAppointment->patient_id,
    //             'invoice_number' => 'INV-' . time(),
    //             'total_amount' => $total,
    //             'status' => 'unpaid'
    //         ]
    //     );

    //     // 2. Konfigurasi Midtrans
    //     Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    //     Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
    //     Config::$isSanitized = true;
    //     Config::$is3ds = true;

    //     $params = [
    //         'transaction_details' => [
    //             'order_id' => $billing->invoice_number,
    //             'gross_amount' => $total,
    //         ],
    //         'customer_details' => [
    //             'first_name' => $this->selectedAppointment->patient->name,
    //         ],
    //     ];

    //     // 3. Dapatkan Snap Token
    //     $snapToken = Snap::getSnapToken($params);
    //     $billing->update(['snap_token' => $snapToken]);

    //     // 4. Kirim Token ke Browser (JavaScript)
    //     $this->dispatch('show-snap-modal', token: $snapToken);
    // }


    public function processPayment()
    {
        // Pengaman: Jika belum pilih pasien tapi klik tombol bayar
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
                    'status' => 'unpaid'
                ]
            );

            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $billing->invoice_number,
                    'gross_amount' => (int) $total, // Pastikan harus Integer
                ],
                'customer_details' => [
                    'first_name' => $this->selectedAppointment->patient->name,
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $billing->update(['snap_token' => $snapToken]);

            $this->dispatch('show-snap-modal', token: $snapToken);
        } catch (\Exception $e) {
            // Ini akan memberitahu Anda error aslinya di browser tanpa Error 500
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $pendingBills = Appointment::with(['patient', 'medicalRecord.prescriptions.medicine'])
            ->where('status', 'finished')
            ->where(function ($query) {
                $query->whereDoesntHave('billing')
                    ->orWhereHas('billing', function ($q) {
                        $q->where('status', 'unpaid');
                    });
            })->get();

        return view('livewire.clinic-billing', compact('pendingBills'));
    }
}
