<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>KWITANSI PEMBAYARAN KLINIK</h2>
        <p>No. Invoice: {{ $billing->invoice_number }} | Tgl: {{ $billing->updated_at->format('d/m/Y') }}</p>
    </div>
    <p>Nama Pasien: <strong>{{ $billing->patient->name }}</strong> ({{ $billing->patient->no_rm }})</p>
    <table>
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Jasa Konsultasi Dokter</td>
                <td>Rp 50.000</td>
            </tr>
            @foreach ($billing->appointment->medicalRecord->prescriptions as $p)
                <tr>
                    <td>{{ $p->medicine->name }} ({{ $p->quantity }}x)</td>
                    <td>Rp {{ number_format($p->quantity * $p->medicine->price) }}</td>
                </tr>
            @endforeach
            <tr style="background: #eee;">
                <td><strong>GRAND TOTAL</strong></td>
                <td><strong>Rp {{ number_format($billing->total_amount) }}</strong></td>
            </tr>
        </tbody>
    </table>
    <p style="text-align: right; margin-top: 30px;">Status: <strong>LUNAS</strong></p>
</body>

</html>
