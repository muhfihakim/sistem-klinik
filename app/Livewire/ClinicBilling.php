<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Billing;
use App\Services\WhatsAppService;
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
    public function handlePaymentFinished($status, $name, WhatsAppService $waService)
    {
        if ($status === 'success') {
            // Cari billing yang terkait dengan appointment yang sedang dipilih dan statusnya masih unpaid
            $billing = Billing::where('appointment_id', $this->selectedAppointment->id)
                ->where('status', 'unpaid')
                ->first();

            if ($billing) {
                // 1. Update status di database
                $billing->update([
                    'status' => 'paid',
                    'payment_method' => 'midtrans',
                    'paid_at' => now()
                ]);

                // 2. Siapkan data WhatsApp
                $downloadUrl = route('invoice.download', $billing->id);
                $patient = $billing->patient;

                if ($patient && $patient->phone) {
                    // Template disamakan dengan kuitansi tunai
                    $pesan = "*KUITANSI PEMBAYARAN LUNAS (ONLINE)*\n\n" .
                        "Halo *" . $patient->name . "*,\n" .
                        "Pembayaran online Anda telah berhasil kami terima.\n\n" .
                        "No. Invoice: " . $billing->invoice_number . "\n" .
                        "Total: *Rp " . number_format($billing->total_amount, 0, ',', '.') . "*\n" .
                        "Status: *LUNAS*\n\n" .
                        "Unduh kuitansi PDF di sini:\n" . $downloadUrl . "\n\n" .
                        "Terima kasih, semoga lekas sembuh.";

                    $waService->sendMessage($patient->phone, $pesan);
                }

                session()->flash('success', 'Pembayaran online berhasil & Kuitansi WA terkirim.');
            }

            $this->selectedAppointment = null;
            return redirect()->route('billing.index');
        }
    }

    public function payCash(WhatsAppService $waService)
    {
        if (!$this->selectedAppointment) {
            session()->flash('error', 'Pilih pasien terlebih dahulu.');
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

            $downloadUrl = route('invoice.download', $billing->id);
            $patient = $this->selectedAppointment->patient;

            if ($patient && $patient->phone) {
                // Template disamakan dengan kuitansi online
                $pesan = "*KUITANSI PEMBAYARAN LUNAS (CASH)*\n\n" .
                    "Halo *" . $patient->name . "*,\n" .
                    "Pembayaran tunai Anda telah kami terima.\n\n" .
                    "No. Invoice: " . $billing->invoice_number . "\n" .
                    "Total: *Rp " . number_format($total, 0, ',', '.') . "*\n" .
                    "Status: *LUNAS*\n\n" .
                    "Unduh kuitansi PDF di sini:\n" . $downloadUrl . "\n\n" .
                    "Terima kasih, semoga lekas sembuh.";

                $waService->sendMessage($patient->phone, $pesan);
            }

            session()->flash('success', 'Pembayaran tunai berhasil & Kuitansi WA terkirim.');
            $this->selectedAppointment = null;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function sendWhatsAppBilling(WhatsAppService $waService)
    {
        if (!$this->selectedAppointment) {
            session()->flash('error', 'Pilih pasien dulu.');
            return;
        }

        try {
            $total = $this->total_medicine_cost + $this->consultation_fee;

            // 1. Pastikan Billing sudah ada di database atau buat baru
            $billing = Billing::updateOrCreate(
                ['appointment_id' => $this->selectedAppointment->id],
                [
                    'patient_id' => $this->selectedAppointment->patient_id,
                    'invoice_number' => 'INV-' . time(),
                    'total_amount' => $total,
                    'status' => 'unpaid'
                ]
            );

            // 2. Generate Link Midtrans (hanya link, tidak buka modal di kasir)
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = false;

            $params = [
                'transaction_details' => [
                    'order_id' => $billing->invoice_number,
                    'gross_amount' => (int) $total,
                ],
                'customer_details' => [
                    'first_name' => $this->selectedAppointment->patient->name,
                    'phone' => $this->selectedAppointment->patient->phone,
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $paymentUrl = "https://app.sandbox.midtrans.com/snap/v2/vtweb/" . $snapToken;

            // 3. Kirim Pesan WA
            $patient = $this->selectedAppointment->patient;
            if ($patient && $patient->phone) {
                $pesan = "*TAGIHAN PEMERIKSAAN SIKLINIK*\n\n" .
                    "Halo *" . $patient->name . "*,\n" .
                    "Rincian tagihan Anda:\n" .
                    "Total: *Rp " . number_format($total, 0, ',', '.') . "*\n\n" .
                    "Metode Pembayaran:\n" .
                    "1. *TUNAI*: Langsung ke Kasir Klinik.\n" .
                    "2. *ONLINE*: Klik link berikut: \n" . $paymentUrl . "\n\n" .
                    "Abaikan jika sudah membayar di kasir.";

                $waService->sendMessage($patient->phone, $pesan);
                session()->flash('success', 'Tagihan berhasil dikirim ke WhatsApp pasien.');
            } else {
                session()->flash('error', 'Nomor WA pasien tidak ditemukan.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim WA: ' . $e->getMessage());
        }
    }

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
