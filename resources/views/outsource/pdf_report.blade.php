<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Survei Kelayakan #SRV-{{ $job->id }}</title>
    <style>
        @page {
            margin: 30px 40px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #334155;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px solid #0064D2;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo-text {
            font-size: 20px;
            font-weight: bold;
            color: #0064D2;
            text-transform: uppercase;
        }
        .report-title {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 120px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: white;
            background-color: #0064D2;
            padding: 6px 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .comparison-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: left;
        }
        .comparison-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            vertical-align: top;
        }
        .comparison-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .badge-warning {
            background-color: #fffbeb;
            color: #b45309;
        }
        .badge-info {
            background-color: #e0e7ff;
            color: #4338ca;
        }
        .gallery-table {
            width: 100%;
            margin-top: 10px;
        }
        .gallery-table td {
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }
        .gallery-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }
        .gallery-img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
        }
        .no-image {
            height: 140px;
            background-color: #f1f5f9;
            color: #64748b;
            text-align: center;
            line-height: 140px;
            border-radius: 4px;
            border: 1px dashed #cbd5e1;
        }
        .footer-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .footer-table td {
            width: 50%;
            text-align: center;
        }
        .signature-line {
            margin-top: 50px;
            width: 150px;
            border-bottom: 1px solid #334155;
            display: inline-block;
        }
        .recommendation-banner {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 10px;
            border-radius: 6px;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .recommendation-banner.not-eligible {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
        }
        .recommendation-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="logo-text">SPOTSHARE</td>
            <td class="report-title">
                LAPORAN KOMPARASI KELAYAKAN UNIT<br>
                <span style="font-size: 10px; font-weight: normal; color: #64748b;">ID Penugasan: #SRV-{{ $job->id }}</span>
            </td>
        </tr>
    </table>

    <!-- Meta Info -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Nama Ruangan</td>
            <td>: {{ $job->room }}</td>
            <td class="meta-label">Tanggal Inspeksi</td>
            <td>: {{ $job->tgl_kirim }}</td>
        </tr>
        <tr>
            <td class="meta-label">Alamat Lokasi</td>
            <td>: {{ $job->address }}</td>
            <td class="meta-label">Surveyor Mitra</td>
            <td>: {{ $job->surveyor_name ?? 'Outsource Partner' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Status Kelayakan</td>
            <td>: 
                @if($job->status == 'Diterima')
                    <span class="badge badge-success">DISETUJUI ADMIN</span>
                @elseif($job->status == 'Ditolak')
                    <span class="badge badge-danger">DITOLAK ADMIN</span>
                @else
                    <span class="badge badge-warning">PENDING REVIEW</span>
                @endif
            </td>
            <td class="meta-label">Honor Surveyor</td>
            <td>: Rp 200.000 (Flat Fee)</td>
        </tr>
    </table>

    <!-- Recommendation Banner -->
    @php
        $isLayak = strtolower($job->surveyor->rekomendasi ?? 'layak') === 'layak';
    @endphp
    <div class="recommendation-banner {{ $isLayak ? '' : 'not-eligible' }}">
        <div class="recommendation-title" style="color: {{ $isLayak ? '#15803d' : '#b91c1c' }};">
            REKOMENDASI MITRA SURVEYOR: {{ $isLayak ? 'LAYAK SEWA' : 'TIDAK LAYAK SEWA' }}
        </div>
        <div style="color: #475569; font-size: 10px;">
            Berdasarkan hasil pengecekan lapangan oleh pihak ketiga independen (Outsource Surveyor).
        </div>
    </div>

    <!-- Comparison Section -->
    <div class="section-title">PERBANDINGAN DATA INSPEKSI</div>
    
    <table class="comparison-table">
        <thead>
            <tr>
                <th style="width: 20%;">Kriteria Penilaian</th>
                <th style="width: 40%;">Laporan Pengaju (Penyedia)</th>
                <th style="width: 40%;">Laporan Surveyor (Outsource)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Kondisi Bangunan</strong></td>
                <td>{{ $job->pengaju->kondisi }}</td>
                <td><strong>{{ $job->surveyor->kondisi }}</strong></td>
            </tr>
            <tr>
                <td><strong>Tingkat Kebersihan</strong></td>
                <td>{{ $job->pengaju->kebersihan }}</td>
                <td><strong>{{ $job->surveyor->kebersihan }}</strong></td>
            </tr>
            <tr>
                <td><strong>Catatan Temuan</strong></td>
                <td style="font-style: italic;">"{{ $job->pengaju->catatan }}"</td>
                <td style="font-style: italic; font-weight: bold;">"{{ $job->surveyor->catatan }}"</td>
            </tr>
            <tr>
                <td><strong>Verifikasi Fasilitas</strong></td>
                <td style="padding: 6px;">
                    @if(isset($allFacilitiesList))
                        @foreach($allFacilitiesList as $f)
                            <div style="display: inline-block; margin: 2px 3px; padding: 2px 5px; background-color: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 3px; font-size: 8px;">
                                @if(in_array($f, $job->pengaju->facilities ?? []))
                                    <span style="color: #15803d; font-weight: bold;">&#10003;</span> {{ $f }}
                                @else
                                    <span style="color: #94a3b8; text-decoration: line-through;">&#10005; {{ $f }}</span>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <span style="color: #94a3b8;">Tidak ada data fasilitas</span>
                    @endif
                </td>
                <td style="padding: 6px;">
                    @if(isset($allFacilitiesList))
                        @foreach($allFacilitiesList as $f)
                            <div style="display: inline-block; margin: 2px 3px; padding: 2px 5px; background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 3px; font-size: 8px;">
                                @if(in_array($f, $job->surveyor->facilities ?? []))
                                    <span style="color: #15803d; font-weight: bold;">&#10003;</span> {{ $f }}
                                @else
                                    <span style="color: #ef4444; font-weight: bold;">&#10005;</span> <span style="color: #94a3b8; text-decoration: line-through;">{{ $f }}</span>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <span style="color: #94a3b8;">Tidak ada data verifikasi</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <div style="page-break-before: always;"></div>

    <!-- Documentation Section -->
    <div class="section-title">DOKUMENTASI FOTO PERBANDINGAN</div>
    
    <table class="gallery-table">
        <tr>
            <td>
                <div class="gallery-title">Foto Pengaju (Penyedia)</div>
                @if(isset($job->pengaju->media) && count($job->pengaju->media) > 0)
                    <img src="{{ $job->pengaju->media[0]['url_local'] ?? $job->pengaju->media[0]['url'] }}" class="gallery-img">
                @else
                    <div class="no-image">Tidak ada foto lampiran pengaju</div>
                @endif
            </td>
            <td>
                <div class="gallery-title">Foto Temuan Surveyor (Outsource)</div>
                @if(isset($job->surveyor->media) && count($job->surveyor->media) > 0)
                    <img src="{{ $job->surveyor->media[0]['url_local'] ?? $job->surveyor->media[0]['url'] }}" class="gallery-img">
                @else
                    <div class="no-image">Tidak ada foto lampiran surveyor</div>
                @endif
            </td>
        </tr>
        @if((isset($job->pengaju->media) && count($job->pengaju->media) > 1) || (isset($job->surveyor->media) && count($job->surveyor->media) > 1))
        <tr>
            <td>
                @if(isset($job->pengaju->media) && count($job->pengaju->media) > 1)
                    <img src="{{ $job->pengaju->media[1]['url_local'] ?? $job->pengaju->media[1]['url'] }}" class="gallery-img">
                @endif
            </td>
            <td>
                @if(isset($job->surveyor->media) && count($job->surveyor->media) > 1)
                    <img src="{{ $job->surveyor->media[1]['url_local'] ?? $job->surveyor->media[1]['url'] }}" class="gallery-img">
                @endif
            </td>
        </tr>
        @endif
    </table>

    <!-- Signature Sign-off -->
    <table class="footer-table">
        <tr>
            <td>
                <p>Mitra Kerja Lapangan (Surveyor),</p>
                <div class="signature-line"></div>
                <p style="font-weight: bold; margin-top: 5px;">{{ $job->surveyor_name ?? 'Outsource Surveyor' }}</p>
            </td>
            <td>
                <p>Manajemen Approval (Admin),</p>
                <div class="signature-line"></div>
                <p style="font-weight: bold; margin-top: 5px;">Spotshare Administrator</p>
            </td>
        </tr>
    </table>

</body>
</html>
