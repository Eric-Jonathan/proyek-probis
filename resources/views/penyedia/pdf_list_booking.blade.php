<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan_Daftar_Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            line-height: 1.4;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #0064D2;
            color: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            opacity: 0.85;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
        }
        .text-center {
            text-align: center !important;
        }
        .text-right {
            text-align: right !important;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
        }
        .badge-batal { background-color: #fee2e2; color: #b91c1c; }
        .badge-lunas { background-color: #dcfce7; color: #15803d; }
        .badge-selesai { background-color: #e0f2fe; color: #0369a1; }
        .badge-cicilan { background-color: #fef9c3; color: #a16207; }
        .badge-pending { background-color: #f1f5f9; color: #475569; }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Daftar Pemesanan Ruangan</h2>
        <p>Dicetak pada: {{ date('d F Y, H:i') }} WIB | Total Data: {{ $bookings->count() }} Transaksi</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 10%;" class="text-center">ID Booking</th>
                <th style="width: 25%;">Ruangan</th>
                <th style="width: 20%;">Penyewa / Acara</th>
                <th style="width: 20%;" class="text-center">Tanggal Sewa</th>
                <th style="width: 10%;" class="text-right">Total</th>
                <th style="width: 10%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $index => $b)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center fw-bold">#TRX-{{ $b->booking_id }}</td>
                    <td>{{ $b->details->room->name ?? 'N/A' }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $b->user->username ?? 'Guest' }}</div>
                        <div style="color: #64748b; font-size: 9px;">{{ $b->event }}</div>
                    </td>
                    <td class="text-center">
                        {{ date('d M Y', strtotime($b->start_date)) }}
                        <div style="font-size: 9px; color: #64748b;">
                            {{ date('H:i', strtotime($b->start_date)) }} - {{ date('H:i', strtotime($b->end_date)) }}
                        </div>
                    </td>
                    <td class="text-right">Rp {{ number_format($b->total, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($b->status == 0)
                            <span class="badge badge-batal">Batal</span>
                        @elseif($b->status == 1)
                            <span class="badge badge-lunas">Lunas</span>
                        @elseif($b->status == 2)
                            <span class="badge badge-selesai">Selesai</span>
                        @elseif($b->status == 3)
                            <span class="badge badge-cicilan">Cicilan ({{ $b->installments_paid }}/3)</span>
                        @elseif($b->status == 4)
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">Tidak ada data pemesanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini diterbitkan oleh Tempat-In marketplace.</p>
    </div>

</body>
</html>
