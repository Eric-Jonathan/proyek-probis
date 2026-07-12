<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kinerja Mitra Outsource</title>
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
                    <p class="header-subtitle">Laporan Kinerja &amp; Kecepatan Surveyor Lapangan</p>
                </td>
                <td class="header-meta">
                    <strong>Nama Surveyor:</strong> {{ $username }}<br>
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
                    <div class="kpi-label">Survei Selesai</div>
                    <div class="kpi-val">{{ $completedCount }} Tugas</div>
                </div>
            </td>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Total Fee Surveyor</div>
                    <div class="kpi-val" style="color: #16a34a;">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Rata SLA Kecepatan</div>
                    <div class="kpi-val" style="color: #1e3a8a;">{{ $avgSla }} Jam</div>
                </div>
            </td>
            <td>
                <div class="kpi-card">
                    <div class="kpi-label">Akurasi Kelayakan</div>
                    <div class="kpi-val">{{ $accuracy }}%</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Feasibility Breakdown -->
    <div class="section-title">Ringkasan Hasil Rekomendasi Fisik</div>
    <table class="table-data">
        <thead>
            <tr>
                <th width="50%">Kesimpulan Rekomendasi Lapangan</th>
                <th width="50%" style="text-align: center;">Jumlah Ruangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Layak Disewakan</strong> (Sesuai Standar Platform)</td>
                <td style="text-align: center; font-weight: bold; color: #16a34a;">{{ $layakCount }} Ruangan</td>
            </tr>
            <tr>
                <td><strong>Tidak Layak Disewakan</strong> (Membutuhkan Perbaikan/Renovasi)</td>
                <td style="text-align: center; font-weight: bold; color: #dc2626;">{{ $tidakLayakCount }} Ruangan</td>
            </tr>
        </tbody>
    </table>

    <!-- Regional Workload Analysis -->
    <div class="section-title">Analisis Beban Kerja Regional &amp; Kecepatan Rata-Rata</div>
    <table class="table-data">
        <thead>
            <tr>
                <th width="40%">Wilayah / Kota Survei</th>
                <th width="20%" style="text-align: center;">Jumlah Tugas</th>
                <th width="20%" style="text-align: center;">Persentase Beban</th>
                <th width="20%" style="text-align: right;">Rata-Rata SLA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($regionsList as $region)
                <tr>
                    <td><strong>{{ $region->name }}</strong></td>
                    <td style="text-align: center;">{{ $region->count }}</td>
                    <td style="text-align: center;">{{ $region->percentage }}%</td>
                    <td style="text-align: right; font-weight: bold; color: #1e3a8a;">{{ $region->avg_hours }} Jam</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #94a3b8;">Belum ada data persebaran wilayah.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Analysis & Business Recommendations -->
    <div class="rec-box">
        <div class="rec-box-title">💡 Analisis Operasional &amp; Keputusan Bisnis</div>
        @foreach($recommendations as $rec)
            <div class="rec-item">{{ $rec }}</div>
        @endforeach
    </div>

    <div class="footer">
        Laporan Kinerja Surveyor Tempat-In - Dicetak secara otomatis oleh sistem manajemen internal platform
    </div>
</body>
</html>
