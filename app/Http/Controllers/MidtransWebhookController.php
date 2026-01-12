<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');

        // Gunakan format gross_amount dari request langsung tanpa modifikasi
        // agar signature_key cocok dengan kiriman Midtrans
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;

        $signature = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signature == $request->signature_key) {
            $status = $request->transaction_status;

            if ($status == 'settlement' || $status == 'capture') {
                $billing = Billing::where('invoice_number', $orderId)->first();
                if ($billing) {
                    $billing->update(['status' => 'paid']);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
