<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download($id)
    {
        $billing = Billing::with(['patient', 'appointment.medicalRecord.prescriptions.medicine'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.invoice', compact('billing'));
        return $pdf->download('Invoice-' . $billing->invoice_number . '.pdf');
    }
}
