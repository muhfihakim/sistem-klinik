<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $billing = Billing::where('invoice_number', $request->order_id)->first();
                if ($billing) {
                    $billing->update(['status' => 'paid']);
                }
            }
        }
        return response()->json(['status' => 'success']);
    }
}
