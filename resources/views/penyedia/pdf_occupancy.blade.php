<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan_Okupansi_Ruangan</title>
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
        .stats-container {
            margin-bottom: 20px;
        }
        .stats-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .stats-title {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .stats-value {
            font-size: 16px;
            font-weight: bold;
            color: #0064D2;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
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
        .recommendation-box {
            background-color: #fffbeb;
            border-left: 4px solid #d97706;
            padding: 10px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .recommendation-title {
            font-weight: bold;
            color: #b45309;
            margin-bottom: 5px;
        }
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
        <h2>Laporan Analisis Okupansi Ruangan</h2>
        <p>Dicetak pada: {{ date('d F Y, H:i') }} WIB | Periode Analisis: {{ $filter === 'all' ? 'Semua Periode' : $filter . ' Hari Terakhir' }}</p>
    </div>

    <table style="width: 100%; margin-bottom: 20px; border: none;">
        <tr style="border: none;">
            <td style="width: 33%; border: none; padding: 0 5px 0 0;">
                <div class="stats-box">
                    <div class="stats-title">Total Pemesanan Aktif</div>
                    <div class="stats-value">{{ $totalBookingsCount }} Kali</div>
                </div>
            </td>
            <td style="width: 33%; border: none; padding: 0 5px;">
                <div class="stats-box">
                    <div class="stats-title">Total Waktu Terpakai</div>
                    <div class="stats-value">{{ $totalHoursAllRooms }} Jam</div>
                </div>
            </td>
            <td style="width: 34%; border: none; padding: 0 0 0 5px;">
                <div class="stats-box">
                    <div class="stats-title">Rata-rata Okupansi</div>
                    <div class="stats-value">{{ $avgOccupancyRate }}%</div>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 35%;">Nama Ruangan</th>
                <th style="width: 20%;" class="text-center">Jumlah Pemesanan</th>
                <th style="width: 20%;" class="text-center">Total Jam Sewa</th>
                <th style="width: 20%;" class="text-center">Tingkat Okupansi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($roomStats as $stats)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td style="font-weight: bold;">{{ $stats['room']->name }}</td>
                    <td class="text-center">{{ $stats['booking_count'] }} Kali</td>
                    <td class="text-center">{{ $stats['total_hours'] }} Jam</td>
                    <td class="text-center fw-bold text-primary">{{ $stats['occupancy_rate'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(count($recommendations) > 0)
        <div class="recommendation-box">
            <div class="recommendation-title"><i class="bi bi-lightbulb-fill"></i> Rekomendasi Optimasi Bisnis</div>
            <ul style="margin: 0; padding-left: 20px; line-height: 1.5;">
                @foreach($recommendations as $rec)
                    <li>
                        <strong>{{ $rec['room_name'] }}</strong>: {{ $rec['text'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="footer">
        <p>Laporan analisis ini dihasilkan secara otomatis oleh Tempat-In marketplace.</p>
    </div>

</body>
</html>
