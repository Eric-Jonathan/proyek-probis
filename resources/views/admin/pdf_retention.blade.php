<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Retensi &amp; Loyalitas Penyewa</title>
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
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
        }
        .badge-success { background-color: #dcfce7; color: #16a34a; }
        .badge-warning { background-color: #fef9c3; color: #ca8a04; }
        .badge-danger { background-color: #fee2e2; color: #dc2626; }
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
                    <p class="header-subtitle">Analisis Loyalitas, Retensi &amp; CLV (Customer Lifetime Value)</p>
                </td>
                <td class="header-meta">
                    <strong>Tanggal Cetak:</strong> {{ date('d M Y H:i') }}<br>
                    <strong>Klasifikasi:</strong> Rahasia Perusahaan
                </td>
            </tr>
        </table>
    </div>

    <!-- KPI Metrics -->
    <table class="kpi-container">
        <tr>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Penyewa Terdaftar</div>
                    <div class="kpi-val">{{ number_format($totalRenters, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Penyewa Aktif</div>
                    <div class="kpi-val">{{ number_format($totalActiveRenters, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Repeat Renter Rate</div>
                    <div class="kpi-val" style="color: #1e3a8a;">{{ $repeatRenterRate }}%</div>
                </div>
            </td>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Rata Pengeluaran CLV</div>
                    <div class="kpi-val">Rp {{ number_format($avgLifetimeSpent, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Renter Segmentations -->
    <div class="section-title">Segmentasi Profil Penyewa</div>
    <table class="table-data">
        <thead>
            <tr>
                <th width="35%">Segmentasi User</th>
                <th width="35%">Kriteria Aktivitas</th>
                <th width="30%" style="text-align: center;">Jumlah Akun (Renter)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge badge-success">LOYAL USER</span></td>
                <td>Melakukan pemesanan sukses 3 kali atau lebih</td>
                <td style="text-align: center; font-weight: bold;">{{ $loyalCount }} Akun</td>
            </tr>
            <tr>
                <td><span class="badge badge-warning">OCCASIONAL USER</span></td>
                <td>Melakukan pemesanan sukses 1 sampai 2 kali</td>
                <td style="text-align: center; font-weight: bold;">{{ $occasionalCount }} Akun</td>
            </tr>
            <tr>
                <td><span class="badge badge-danger">INACTIVE USER</span></td>
                <td>Penyewa terdaftar yang belum pernah memesan</td>
                <td style="text-align: center; font-weight: bold;">{{ $inactiveCount }} Akun</td>
            </tr>
        </tbody>
    </table>

    <!-- Top 5 Renters by Spent (CLV) -->
    <div class="section-title">5 Akun Penyewa dengan Nilai Kontribusi Tertinggi (Top CLV)</div>
    <table class="table-data">
        <thead>
            <tr>
                <th width="35%">Nama Penyewa (Renter)</th>
                <th width="30%">Alamat Email</th>
                <th width="15%" style="text-align: center;">Total Booking</th>
                <th width="20%" style="text-align: right;">Total Nilai Belanja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topRenters as $renter)
                <tr>
                    <td><strong>{{ $renter->username }}</strong></td>
                    <td>{{ $renter->email }}</td>
                    <td style="text-align: center;">{{ $renter->booking_count }}</td>
                    <td style="text-align: right; color: #1e3a8a; font-weight: bold;">Rp {{ number_format($renter->total_spent, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #94a3b8;">Belum ada data transaksi renter aktif.</td>
                </tr>
            @endforelse
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
        Laporan Retensi &amp; CLV Tempat-In - Dicetak secara otomatis oleh sistem manajemen internal platform
    </div>
</body>
</html>
