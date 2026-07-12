<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan_Pemesanan_TRX-{{ $booking->booking_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            line-height: 1.4;
            font-size: 12px;
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
            font-size: 20px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            opacity: 0.85;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0064D2;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.info-table td {
            padding: 6px 4px;
            vertical-align: top;
        }
        table.info-table td.label {
            font-weight: bold;
            color: #64748b;
            width: 30%;
        }
        table.info-table td.value {
            color: #1e293b;
        }
        table.cost-table {
            margin-top: 10px;
        }
        table.cost-table th, table.cost-table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
        }
        table.cost-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
        }
        .text-right {
            text-align: right !important;
        }
        .total-box {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 12px;
            margin-top: 15px;
            text-align: right;
            border-radius: 4px;
        }
        .total-title {
            font-size: 11px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #0064D2;
            margin-top: 4px;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $booking->roomDetail->room->name ?? 'Gedung / Ruangan' }}</h2>
        <p>Laporan Detail Pemesanan | ID Booking: #TRX-{{ $booking->booking_id }}</p>
        <p style="font-size: 11px;">Lokasi: {{ $booking->roomDetail->room->location ?? 'N/A' }}</p>
    </div>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                <div class="section-title">Informasi Penyewa</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Nama Pengguna</td>
                        <td class="value">: {{ $booking->user->username ?? 'Guest' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kontak / WA</td>
                        <td class="value">: +62 {{ $booking->phone }}</td>
                    </tr>
                    <tr>
                        <td class="label">Email</td>
                        <td class="value">: {{ $booking->user->email ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 15px;">
                <div class="section-title">Jadwal Penggunaan</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Tanggal</td>
                        <td class="value">: 
                            @if(date('Y-m-d', strtotime($booking->start_date)) === date('Y-m-d', strtotime($booking->end_date)))
                                {{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('l, d F Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($booking->end_date)->translatedFormat('d F Y') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Durasi</td>
                        <td class="value">: {{ date('H:i', strtotime($booking->start_date)) }} - {{ date('H:i', strtotime($booking->end_date)) }} WIB</td>
                    </tr>
                    <tr>
                        <td class="label">Kegiatan</td>
                        <td class="value">: {{ $booking->event }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($booking->notes)
        <div class="section-title">Catatan Khusus</div>
        <p style="margin: 0 0 15px 0; background-color: #f8fafc; padding: 10px; border-left: 3px solid #cbd5e1; font-style: italic;">
            "{{ $booking->notes }}"
        </p>
    @endif

    <div class="section-title">Rincian Pendapatan</div>
    <table class="cost-table">
        <thead>
            <tr>
                <th>Deskripsi Item / Layanan</th>
                <th class="text-right" style="width: 30%;">Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Harga Sewa Ruangan Utama</td>
                <td class="text-right">Rp {{ number_format($booking->roomDetail->item_price ?? 0, 0, ',', '.') }}</td>
            </tr>
            @if($booking->serviceDetails->count() > 0)
                @foreach($booking->serviceDetails as $service)
                    <tr>
                        <td>{{ $service->item_name }}</td>
                        <td class="text-right">Rp {{ number_format($service->item_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif
            
            @php
                $baseTotal = ($booking->roomDetail->item_price ?? 0) + $booking->serviceDetails->sum('item_price');
                $commission = (int) round($baseTotal * 0.05);
                $netEarnings = $baseTotal - $commission;
            @endphp
            
            <tr style="font-weight: bold; background-color: #fafafa;">
                <td class="text-right">Subtotal Biaya Sewa:</td>
                <td class="text-right">Rp {{ number_format($baseTotal, 0, ',', '.') }}</td>
            </tr>
            <tr style="color: #b91c1c;">
                <td class="text-right">Komisi Tempat-In (5%):</td>
                <td class="text-right">- Rp {{ number_format($commission, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        <div class="total-title">Total Pendapatan Bersih (Owner Share)</div>
        <div class="total-amount">Rp {{ number_format($netEarnings, 0, ',', '.') }}</div>
        <div style="font-size: 10px; margin-top: 5px; color: #64748b;">
            Metode Pembayaran: {{ $booking->method_payment }}
        </div>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Marketplace Tempat-In pada {{ date('d F Y, H:i') }} WIB</p>
    </div>

</body>
</html>
