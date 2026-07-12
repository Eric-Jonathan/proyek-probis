@extends('layout.layout')

@section('custom_css')
<style>
    body {
        background-color: #f8f9fa;
    }
    .report-card {
        border-radius: 16px;
        border: none;
    }
    .kpi-card {
        border: none;
        border-radius: 16px;
        transition: transform 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
    }
    .text-success-custom {
        color: #198754;
    }
    .bg-success-subtle-custom {
        background-color: #e8f5e9;
    }
    .chart-container {
        position: relative;
        height: 250px;
    }
    .insight-card {
        border-left: 5px solid #006ce4;
        background-color: #f0f7ff;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    {{-- Header with Back Button --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Laporan Analisis Kinerja & Alokasi Wilayah</h3>
            <p class="text-secondary small mb-0">Kelola produktivitas tim surveyor lapangan, evaluasi kualitas verifikasi, dan optimalkan alokasi wilayah tugas</p>
        </div>
        <div>
            <a href="{{ route('outsource.report.pdf') }}" class="btn btn-outline-danger rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2 fw-bold" id="downloadPdfBtn">
                <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Jobs --}}
        <div class="col-md-3">
            <div class="card kpi-card shadow-sm p-4 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.75rem;">Tugas Selesai</span>
                    <div class="rounded-circle p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-briefcase fs-6"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $completedCount }}</h3>
                <span class="text-muted small" style="font-size: 0.72rem;">Total seluruh laporan verifikasi</span>
            </div>
        </div>

        {{-- Total Earnings --}}
        <div class="col-md-3">
            <div class="card kpi-card shadow-sm p-4 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.75rem;">Pendapatan</span>
                    <div class="rounded-circle p-2 bg-success-subtle-custom text-success-custom d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-wallet2 fs-6"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</h3>
                <span class="text-muted small" style="font-size: 0.72rem;">Tarif flat Rp 200.000 / tugas</span>
            </div>
        </div>

        {{-- SLA completion --}}
        <div class="col-md-3">
            <div class="card kpi-card shadow-sm p-4 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.75rem;">Rata-rata SLA</span>
                    <div class="rounded-circle p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-clock-history fs-6"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $avgSla }} Jam</h3>
                <span class="text-muted small" style="font-size: 0.72rem;">Hingga kirim laporan (Independen dari ACC Admin)</span>
            </div>
        </div>

        {{-- Accuracy --}}
        <div class="col-md-3">
            <div class="card kpi-card shadow-sm p-4 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.75rem;">Akurasi Laporan</span>
                    <div class="rounded-circle p-2 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-patch-check fs-6"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $accuracy }}%</h3>
                <span class="text-muted small" style="font-size: 0.72rem;">Laporan disetujui Admin</span>
            </div>
        </div>
    </div>

    {{-- Middle row: Charts & Recommendations --}}
    <div class="row g-4 mb-4">
        {{-- Trend Chart --}}
        <div class="col-lg-4">
            <div class="card report-card shadow-sm p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Tren Tugas Selesai</h5>
                <p class="text-secondary small mb-3">Volume tugas selesai pada tahun ini</p>
                <div class="chart-container">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Feasibility breakdown --}}
        <div class="col-lg-4">
            <div class="card report-card shadow-sm p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Rekomendasi Kelayakan</h5>
                <p class="text-secondary small mb-4">Proporsi rekomendasi hasil survei lapangan</p>
                <div class="d-flex flex-column justify-content-center pt-2">
                    @if($completedCount > 0)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold text-success" style="font-size: 0.85rem;">Layak</span>
                                <span class="fw-bold text-dark small">{{ $layakCount }} Ruangan ({{ round(($layakCount / $completedCount) * 100) }}%)</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($layakCount / $completedCount) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold text-danger" style="font-size: 0.85rem;">Tidak Layak</span>
                                <span class="fw-bold text-dark small">{{ $tidakLayakCount }} Ruangan ({{ round(($tidakLayakCount / $completedCount) * 100) }}%)</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($tidakLayakCount / $completedCount) * 100 }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted small">Belum ada data hasil survei.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Strategic Insights --}}
        <div class="col-lg-4">
            <div class="card report-card shadow-sm p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Rekomendasi Keputusan</h5>
                <p class="text-secondary small mb-3">Berdasarkan sebaran beban wilayah & performa tim</p>
                
                @php
                    $highVolumeRegion = collect($regionsList)->first();
                    $slowRegion = collect($regionsList)->sortByDesc('avg_sla')->first();
                @endphp

                <div class="d-flex flex-column gap-2" style="max-height: 250px; overflow-y: auto;">
                    {{-- Rekomendasi 1 --}}
                    @if($highVolumeRegion)
                    <div class="insight-card p-2 border-start border-primary border-4" style="font-size: 0.78rem;">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.82rem;"><i class="bi bi-geo-alt-fill text-primary me-1"></i>Penempatan: {{ $highVolumeRegion->name }}</h6>
                        <p class="text-secondary small mb-0 mt-1">
                            Kota ini menyumbang <strong>{{ $highVolumeRegion->percentage }}%</strong> tugas. Disarankan menugaskan <strong>surveyor menetap</strong> di sini.
                        </p>
                    </div>
                    @endif

                    {{-- Rekomendasi 2 --}}
                    @if($slowRegion && $slowRegion->avg_sla > 48)
                    <div class="insight-card p-2 border-start border-danger border-4" style="border-left-color: #dc3545 !important; background-color: #fff8f8; font-size: 0.78rem;">
                        <h6 class="fw-bold text-danger mb-0" style="font-size: 0.82rem;"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>SLA Lambat: {{ $slowRegion->name }}</h6>
                        <p class="text-secondary small mb-0 mt-1">
                            Rata-rata pengerjaan <strong>{{ $slowRegion->avg_sla }} Jam</strong>. Direkomendasikan menambah surveyor tambahan.
                        </p>
                    </div>
                    @else
                    <div class="insight-card p-2 border-start border-success border-4" style="border-left-color: #198754 !important; background-color: #f4faf6; font-size: 0.78rem;">
                        <h6 class="fw-bold text-success mb-0" style="font-size: 0.82rem;"><i class="bi bi-check-circle-fill text-success me-1"></i>SLA Optimal</h6>
                        <p class="text-secondary small mb-0 mt-1">
                            Respon pengerjaan di seluruh wilayah terpantau di bawah 48 jam.
                        </p>
                    </div>
                    @endif

                    {{-- Rekomendasi 3 --}}
                    @if($accuracy < 85)
                    <div class="insight-card p-2 border-start border-warning border-4" style="border-left-color: #ffc107 !important; background-color: #fffdf5; font-size: 0.78rem;">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.82rem;"><i class="bi bi-mortarboard-fill text-warning me-1"></i>Akurasi: {{ $accuracy }}%</h6>
                        <p class="text-secondary small mb-0 mt-1">
                            Akurasi di bawah target 90%. Disarankan kalibrasi ulang kriteria survei.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Regional Allocation Table --}}
    <div class="card report-card shadow-sm p-4 bg-white">
        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;">Analisis Beban Kerja Sebaran Wilayah</h5>
        <p class="text-secondary small mb-4">Sebaran wilayah pengerjaan verifikasi ruangan beserta performa SLA penyelesaian per wilayah</p>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-4">Nama Kota / Wilayah</th>
                        <th class="py-3 text-center">Jumlah Tugas</th>
                        <th class="py-3 text-center">Persentase Kontribusi</th>
                        <th class="py-3 text-center">Rata-rata SLA</th>
                        <th class="py-3 text-center">Tingkat Akurasi</th>
                        <th class="py-3 px-4">Keputusan Rekomendasi Alokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($regionsList as $region)
                        <tr>
                            <td class="py-3 px-4 fw-bold text-dark">{{ $region->name }}</td>
                            <td class="py-3 text-center">{{ $region->count }} Tugas</td>
                            <td class="py-3 text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress" style="width: 70px; height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $region->percentage }}%"></div>
                                    </div>
                                    <span class="small fw-semibold text-secondary">{{ $region->percentage }}%</span>
                                </div>
                            </td>
                            <td class="py-3 text-center">
                                <span class="fw-semibold text-dark">{{ $region->avg_sla }} Jam</span>
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge {{ $region->accuracy >= 90 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} px-2.5 py-1.5 rounded fw-semibold">
                                    {{ $region->accuracy }}%
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge bg-{{ $region->badge }}-subtle text-{{ $region->badge }} border border-{{ $region->badge }} px-2.5 py-1.5 rounded-pill fw-semibold" style="font-size: 0.72rem;">
                                    {{ $region->suggestion }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-geo fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada data penugasan selesai untuk menganalisis wilayah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Tugas Selesai',
                    data: {!! json_encode($chartValues) !!},
                    borderColor: '#006ce4',
                    backgroundColor: 'rgba(0, 108, 228, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#006ce4',
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1
                        },
                        grid: {
                            color: '#f3f4f6'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
