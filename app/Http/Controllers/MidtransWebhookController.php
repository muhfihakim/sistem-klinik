<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Penting untuk debugging

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');

        // Ambil data asli dari request Midtrans
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;
        $signatureKey = $request->signature_key;

        // Hitung Signature Lokal
        $localSignature = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

        // --- DEBUGGING: Cek di storage/logs/laravel.log jika status tidak berubah ---
        Log::info("Midtrans Webhook Masuk: " . $orderId);

        if ($localSignature !== $signatureKey) {
            Log::error("Signature Mismatch!", [
                'order_id' => $orderId,
                'lokal' => $localSignature,
                'dari_midtrans' => $signatureKey
            ]);
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        $transactionStatus = $request->transaction_status;

        if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
            // Cari billing berdasarkan invoice_number
            $billing = Billing::where('invoice_number', $orderId)->first();

            if ($billing) {
                $billing->update(['status' => 'paid']);
                Log::info("Billing $orderId BERHASIL diupdate ke status PAID");
            } else {
                Log::warning("Billing $orderId tidak ditemukan di database.");
            }
        }

        return response()->json(['status' => 'success']);
    }
}
