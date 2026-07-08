@extends('layout.layout')

@section('custom_css')
<style>
    body { background-color: #f8f9fa; }
    
    .kpi-card {
        border-radius: 16px;
        border: none;
        color: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }
    
    .kpi-icon {
        font-size: 2.2rem;
        opacity: 0.8;
    }

    .analytics-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 4px 25px rgba(0,0,0,0.04);
        background: #ffffff;
    }
    
    .table-container th {
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem !important;
    }
    
    .table-container td {
        padding: 1.25rem 1rem !important;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #334155;
    }

    .insight-card {
        border-radius: 16px;
        background-color: #f8fafc;
        border-left: 5px solid #059669;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    
    {{-- Header --}}
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-8">
            <h2 class="fw-bold mb-1 text-dark">Laporan Keuangan & Kontribusi</h2>
            <p class="text-secondary mb-0">Analisis kontribusi finansial, rata-rata pendapatan sewa, dan tren pertumbuhan MoM</p>
        </div>
        <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end gap-2">
            <div class="d-inline-flex align-items-center gap-2 bg-white p-2 rounded-pill shadow-sm border">
                <span class="small text-muted ps-2 fw-semibold"><i class="bi bi-funnel me-1"></i>Periode:</span>
                <select id="filterPeriod" class="form-select border-0 bg-transparent py-1 pe-4 fw-bold text-primary" style="width: auto; box-shadow: none; outline: none;">
                    <option value="30" {{ $filter === '30' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="90" {{ $filter === '90' ? 'selected' : '' }}>90 Hari Terakhir</option>
                    <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                </select>
            </div>
            <a href="{{ route('penyedia.finance.pdf', ['filter' => $filter]) }}" class="btn btn-danger rounded-pill fw-bold shadow-sm px-4 py-2">
                <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">
        <!-- Total Pendapatan -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Total Pendapatan</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 2rem; font-weight: 800;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Total dana masuk dari persewaan ruangan</div>
            </div>
        </div>

        <!-- Rata-rata ARPB -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Rata-Rata Tiket</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 2rem; font-weight: 800;">Rp {{ number_format($avgArpb, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-calculator"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Rata-rata pendapatan per satu pemesanan</div>
            </div>
        </div>

        <!-- Pertumbuhan MoM -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Tren MoM (Bulan Ini)</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 2.2rem; font-weight: 800;">
                            {{ $latestGrowth >= 0 ? '+' : '' }}{{ $latestGrowth }}%
                        </h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Perbandingan pertumbuhan dengan bulan lalu</div>
            </div>
        </div>
    </div>

    {{-- Main Financial Analytics --}}
    <div class="row g-4 mb-4">
        {{-- Line Chart MoM --}}
        <div class="col-lg-7">
            <div class="card analytics-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-graph-up text-primary me-2"></i>Tren Pendapatan Bulanan (Tahun ini)</h5>
                <div style="height: 300px; position: relative;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Donut Chart Contribution --}}
        <div class="col-lg-5">
            <div class="card analytics-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Kontribusi Pendapatan Ruangan</h5>
                <div style="height: 250px; position: relative;" class="d-flex justify-content-center">
                    @if(count($donutValues) > 0)
                        <canvas id="contributionChart"></canvas>
                    @else
                        <div class="align-self-center text-muted small py-5">Tidak ada data kontribusi untuk ditampilkan</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Table & Financial Insights --}}
    <div class="row g-4">
        {{-- Data Table --}}
        <div class="col-lg-8">
            <div class="card analytics-card p-4">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-cash-stack text-success me-2"></i>Rincian Kontribusi Finansial</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-container mb-0">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Nama Ruangan</th>
                                <th class="text-end" style="width: 25%;">Total Pendapatan</th>
                                <th class="text-center" style="width: 20%;">Porsi Kontribusi</th>
                                <th class="text-end" style="width: 20%;">ARPB</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roomStats as $stats)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $stats['room']->name }}</div>
                                    <small class="text-muted"><i class="bi bi-tag"></i> {{ $stats['booking_count'] }} Booking</small>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($stats['revenue'], 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-1.5 rounded-pill">
                                        {{ $stats['share'] }}%
                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-secondary">
                                    Rp {{ number_format($stats['arpb'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    Belum ada data persewaan ruangan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Financial Insights --}}
        <div class="col-lg-4">
            <div class="card analytics-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-journal-text text-primary me-2"></i>Financial Insight</h5>
                
                @php
                    // Find cash cow room (highest revenue)
                    $cashCow = null;
                    $highestRev = -1;
                    foreach($roomStats as $stats) {
                        if ($stats['revenue'] > $highestRev) {
                            $highestRev = $stats['revenue'];
                            $cashCow = $stats['room'];
                        }
                    }
                @endphp

                @if($cashCow && $highestRev > 0)
                    <div class="p-4 insight-card mb-4 border-0">
                        <div class="d-flex align-items-center gap-2 mb-2 text-success">
                            <i class="bi bi-trophy-fill fs-5"></i>
                            <span class="fw-bold">Cash-Cow Utama Properti</span>
                        </div>
                        <p class="mb-0 text-secondary small" style="line-height: 1.6;">
                            Ruangan <strong>"{{ $cashCow->name }}"</strong> merupakan kontributor finansial terbesar Anda dengan total perolehan <strong>Rp {{ number_format($highestRev, 0, ',', '.') }}</strong>. 
                        </p>
                        <hr class="my-3 text-secondary opacity-25">
                        <p class="mb-0 text-secondary small" style="line-height: 1.6;">
                            <strong>Saran Keputusan:</strong> Prioritaskan perawatan berkala, kenyamanan, dan kualitas fasilitas pada ruangan ini untuk mempertahankan kepuasan renter VVIP Anda.
                        </p>
                    </div>

                    <!-- Growth Insight -->
                    <div class="p-3 bg-light rounded-4">
                        <h6 class="fw-bold small text-dark"><i class="bi bi-arrow-up-right-circle text-primary me-1"></i>Analisis Tren MoM</h6>
                        <p class="text-muted small mb-0 mt-1" style="line-height: 1.5;">
                            @if($latestGrowth > 0)
                                Keuangan bisnis properti Anda bulan ini menunjukkan tren pertumbuhan positif (+{{ $latestGrowth }}%). Pertahankan promosi pemasaran Anda!
                            @elseif($latestGrowth < 0)
                                Terjadi penurunan pendapatan sebesar ({{ $latestGrowth }}%) dibanding bulan lalu. Evaluasi kembali ketersediaan ruangan dan harga sewa dasar.
                            @else
                                Pendapatan bulan ini berjalan stabil dibanding bulan lalu. Coba berikan program bundling layanan addon untuk mendongkrak omset.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-activity fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Belum ada data pendapatan yang cukup untuk memproses insight finansial.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
{{-- Chart JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle filter change
        document.getElementById('filterPeriod').addEventListener('change', function() {
            window.location.href = `{{ route('penyedia.report.finance') }}?filter=${this.value}`;
        });

        // Donut Chart - Contribution
        const donutLabels = @json($donutLabels);
        const donutValues = @json($donutValues);

        if (donutLabels.length > 0) {
            const ctxDonut = document.getElementById('contributionChart').getContext('2d');
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: donutLabels,
                    datasets: [{
                        data: donutValues,
                        backgroundColor: [
                            '#059669', // emerald
                            '#3b82f6', // blue
                            '#f59e0b', // amber
                            '#8b5cf6', // violet
                            '#ec4899', // pink
                            '#64748b'  // slate
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = Math.round((context.raw / total) * 100);
                                    return ` ${context.label}: Rp ${context.raw.toLocaleString('id-ID')} (${percentage}%)`;
                                }
                            },
                            backgroundColor: '#1e293b',
                            padding: 10,
                            borderRadius: 6
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        // Line Chart - Monthly Trend
        const trendLabels = @json($growthLabels);
        const trendValues = @json($growthValues);

        if (trendLabels.length > 0) {
            const ctxLine = document.getElementById('revenueTrendChart').getContext('2d');
            
            // Create gradient for the line area
            const lineGradient = ctxLine.createLinearGradient(0, 0, 0, 300);
            lineGradient.addColorStop(0, '#4f46e533');
            lineGradient.addColorStop(1, '#4f46e500');

            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Pendapatan Bulanan',
                        data: trendValues,
                        borderColor: '#4f46e5',
                        borderWidth: 3,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.3,
                        fill: true,
                        backgroundColor: lineGradient
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` Pendapatan: Rp ${context.raw.toLocaleString('id-ID')}`;
                                }
                            },
                            backgroundColor: '#1e293b',
                            padding: 12,
                            borderRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'M';
                                    if (value >= 1000) return 'Rp ' + (value / 1000) + 'K';
                                    return 'Rp ' + value;
                                },
                                color: '#64748b',
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
