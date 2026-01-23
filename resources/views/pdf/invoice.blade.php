<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice Klinik - {{ $billing->invoice_number }}</title>
    <style>
        /* Paksa semua elemen menggunakan Helvetica */
        * {
            font-family: 'Helvetica', Arial, sans-serif !important;
        }

        body {
            font-size: 13px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            padding: 30px;
        }

        /* --- LAYOUT UTAMA (TABLE BASED) --- */
        .w-100 {
            width: 100%;
            border-collapse: collapse;
        }

        .text-right {
            text-align: right;
        }

        .text-accent {
            color: #4a6fa5;
        }

        .text-gray {
            color: #7f8c8d;
        }

        .valign-top {
            vertical-align: top;
        }

        /* --- HEADER --- */
        .invoice-header {
            margin-bottom: 30px;
        }

        .clinic-name {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }

        .clinic-tagline {
            color: #7f8c8d;
            font-size: 11px;
            margin-bottom: 10px;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #4a6fa5;
            text-transform: uppercase;
            margin-top: 15px;
        }

        .accent-line {
            width: 120px;
            height: 3px;
            background-color: #4a6fa5;
            margin-top: 5px;
        }

        /* --- INFO BOX --- */
        .info-label {
            font-size: 10px;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }

        /* Patient Info Box */
        .patient-box {
            background-color: #f8fafc;
            padding: 15px;
            border-left: 4px solid #4a6fa5;
            margin: 25px 0;
        }

        /* --- TABLE DETAIL --- */
        .main-table {
            margin-bottom: 20px;
        }

        .main-table th {
            padding: 12px;
            background-color: #f1f5f9;
            color: #4a6fa5;
            font-size: 11px;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }

        .main-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
            /* Memastikan ukuran font item seragam */
        }

        .total-row td {
            border-top: 2px solid #e2e8f0;
            border-bottom: none;
            font-weight: bold;
            font-size: 15px;
        }

        /* Badge Status */
        .status-badge {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 12px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 11px;
            display: inline-block;
        }
    </style>
</head>

<body>

    <div class="invoice-container">
        <div class="invoice-header">
            <h1 class="clinic-name">KLINIK SEHAT TERPADU</h1>
            <div class="clinic-tagline">Jalan Kesehatan No. 123, Subang | Telp: (021) 1234-5678</div>
            <br>
            <div class="invoice-title">INVOICE PEMBAYARAN</div>
            <div class="accent-line"></div>
        </div>

        <table class="w-100" style="margin-bottom: 20px;">
            <tr>
                <td width="33%" class="valign-top">
                    <div class="info-label">No. Invoice</div>
                    <div class="info-value">#{{ $billing->invoice_number }}</div>
                </td>
                <td width="33%" class="valign-top">
                    <div class="info-label">Tanggal</div>
                    <div class="info-value">{{ $billing->updated_at->format('d/m/Y') }}</div>
                </td>
                <td width="33%" class="valign-top">
                    <div class="info-label">Pembayaran</div>
                    <div class="info-value text-accent">LUNAS</div>
                </td>
            </tr>
        </table>

        <div class="patient-box">
            <table class="w-100">
                <tr>
                    <td width="12%" class="info-label">PASIEN:</td>
                    <td class="info-value" style="font-size: 16px;">
                        {{ $billing->patient->name }}
                        <span style="font-weight: normal; font-size: 13px; color: #7f8c8d;">
                            (RM: {{ $billing->patient->no_rm }})
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <table class="w-100 main-table">
            <thead>
                <tr>
                    <th class="text-left">Deskripsi Layanan</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jasa Konsultasi Dokter</td>
                    <td class="text-right">Rp 50.000</td>
                </tr>
                @foreach ($billing->appointment->medicalRecord->prescriptions as $p)
                    <tr>
                        <td>
                            {{ $p->medicine->name }}
                            <span class="text-gray" style="font-size: 11px;">({{ $p->quantity }} buah)</span>
                        </td>
                        <td class="text-right">
                            Rp {{ number_format($p->quantity * $p->medicine->price, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td class="text-right" style="padding-right: 20px;">GRAND TOTAL</td>
                    <td class="text-right text-accent">
                        Rp {{ number_format($billing->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="w-100" style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 15px;">
            <tr>
                <td width="60%" class="valign-top">
                    <div class="text-gray" style="font-size: 10px;">
                        Invoice ini sah diproses secara digital.<br>
                        Terima kasih atas kunjungan Anda.
                    </div>
                </td>
                <td width="40%" class="text-right valign-top">
                    <div class="status-badge">LUNAS</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
