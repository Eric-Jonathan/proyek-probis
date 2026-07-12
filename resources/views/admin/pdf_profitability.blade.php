<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Profitabilitas Platform</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #334155;
            font-size: 10pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header table {
            width: 100%;
        }
        .header-title {
            font-size: 20pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .header-subtitle {
            font-size: 9pt;
            color: #64748b;
            margin-top: 4px;
            margin-bottom: 0;
        }
        .header-meta {
            text-align: right;
            font-size: 9pt;
            color: #64748b;
        }
        .kpi-container {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .kpi-container td {
            width: 25%;
            padding: 0 8px;
        }
        .kpi-container td:first-child {
            padding-left: 0;
        }
        .kpi-container td:last-child {
            padding-right: 0;
        }
        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }
        .kpi-val {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 5px;
        }
        .kpi-label {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 15px;
            margin-top: 10px;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table-data th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 9pt;
            border-bottom: 1px solid #cbd5e1;
        }
        .table-data td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9pt;
        }
        .table-data tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .rec-box {
            background-color: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 15px;
            border-radius: 4px;
            margin-top: 25px;
        }
        .rec-box-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 10px;
        }
        .rec-item {
            margin-bottom: 8px;
            padding-left: 15px;
            position: relative;
            font-size: 9pt;
        }
        .rec-item::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #0284c7;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="header-title">Tempat-In</h1>
                    <p class="header-subtitle">Laporan Profitabilitas &amp; Kinerja Keuangan Platform</p>
                </td>
                <td class="header-meta">
                    <strong>Filter Periode:</strong> {{ $filter === 'all' ? 'Seluruh Waktu' : $filter . ' Hari Terakhir' }}<br>
                    <strong>Tanggal Cetak:</strong> {{ date('d M Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- KPI Metrics -->
    <table class="kpi-container">
        <tr>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Total Pemesanan</div>
                    <div class="kpi-val">{{ number_format($bookingsCount, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Total GMV</div>
                    <div class="kpi-val">Rp {{ number_format($totalGmv, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Pendapatan Komisi</div>
                    <div class="kpi-val" style="color: #16a34a;">Rp {{ number_format($commissionFee, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Nilai Rata Transaksi</div>
                    <div class="kpi-val">Rp {{ number_format($avgTransactionValue, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Top Performing Properties -->
    <div class="section-title">5 Unit Paling Menguntungkan (Kontribusi Komisi 10%)</div>
    <table class="table-data">
        <thead>
            <tr>
                <th width="45%">Nama Unit Ruangan</th>
                <th width="15%" style="text-align: center;">Jumlah Booking</th>
                <th width="20%" style="text-align: right;">Total Omset (GMV)</th>
                <th width="20%" style="text-align: right;">Kontribusi Komisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topRooms as $room)
                <tr>
                    <td><strong>{{ $room->name }}</strong></td>
                    <td style="text-align: center;">{{ $room->booking_count }}</td>
                    <td style="text-align: right;">Rp {{ number_format($room->revenue, 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #16a34a; font-weight: bold;">Rp {{ number_format($room->commission, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #94a3b8;">Belum ada data pemesanan terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Monthly Commission Trends -->
    <div class="section-title">Tren Komisi Bulanan (Tahun Berjalan)</div>
    <table class="table-data">
        <thead>
            <tr>
                <th>Bulan</th>
                <th style="text-align: right;">Volume Transaksi (GMV)</th>
                <th style="text-align: right;">Pendapatan Komisi Platform (10%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyTrends as $trend)
                @if($trend->gmv > 0)
                <tr>
                    <td>{{ $trend->month_name }}</td>
                    <td style="text-align: right;">Rp {{ number_format($trend->gmv, 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #16a34a; font-weight: bold;">Rp {{ number_format($trend->commission, 0, ',', '.') }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Analysis & Business Recommendations -->
    <div class="rec-box">
        <div class="rec-box-title">💡 Analisis &amp; Rekomendasi Keputusan Bisnis</div>
        @foreach($recommendations as $rec)
            <div class="rec-item">{{ $rec }}</div>
        @endforeach
    </div>

    <div class="footer">
        Laporan Keuangan &amp; Profitabilitas Tempat-In - Dicetak secara otomatis oleh sistem manajemen internal platform
    </div>
</body>
</html>
