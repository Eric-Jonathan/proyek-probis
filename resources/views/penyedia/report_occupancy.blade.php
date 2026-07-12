@php
    if (!function_exists('formatHoursToDaysAndHours')) {
        function formatHoursToDaysAndHours($hours) {
            if ($hours <= 0) return '0 Jam';
            $days = floor($hours / 24);
            $rem = $hours % 24;
            $res = [];
            if ($days > 0) $res[] = $days . ' Hari';
            if ($rem > 0) $res[] = $rem . ' Jam';
            return implode(' ', $res);
        }
    }
@endphp
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
    
    .recommendation-item {
        border-radius: 12px;
        border-left: 5px solid;
        padding: 1rem;
        margin-bottom: 1rem;
        background-color: #fcfcfd;
    }
    
    .recommendation-item.warning {
        border-left-color: #f59e0b;
        background-color: #fffbeb;
    }
    
    .recommendation-item.success {
        border-left-color: #10b981;
        background-color: #f0fdf4;
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

    .badge-occupancy {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    
    {{-- Header --}}
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-8">
            <h2 class="fw-bold mb-1 text-dark">Laporan Okupansi Ruangan</h2>
            <p class="text-secondary mb-0">Analisis performa utilitas dan kapasitas penggunaan properti Anda untuk optimasi bisnis</p>
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
            <a href="{{ route('penyedia.occupancy.pdf', ['filter' => $filter]) }}" class="btn btn-danger rounded-pill fw-bold shadow-sm px-4 py-2">
                <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">
        <!-- Rata-rata Okupansi -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Rata-rata Okupansi</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 2.2rem; font-weight: 800;">{{ $avgOccupancyRate }}%</h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Rasio total jam terpakai vs kapasitas maksimal</div>
            </div>
        </div>

        <!-- Total Booking -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Total Pemesanan</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 2.2rem; font-weight: 800;">{{ $totalBookingsCount }}</h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Pemesanan selesai & aktif dalam periode ini</div>
            </div>
        </div>

        <!-- Total Jam Terpakai -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #b45309 0%, #92400e 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Waktu Pemakaian</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 2.2rem; font-weight: 800; white-space: nowrap;">{{ formatHoursToDaysAndHours($totalHoursAllRooms) }}</h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Akumulasi durasi sewa oleh seluruh penyewa</div>
            </div>
        </div>
    </div>

    {{-- Main Analytics --}}
    <div class="row g-4">
        {{-- Charts & Table --}}
        <div class="col-lg-8">
            <!-- Chart Card -->
            <div class="card analytics-card p-4 mb-4">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Tingkat Okupansi Per Ruangan (%)</h5>
                <div style="height: 320px; position: relative;">
                    <canvas id="occupancyChart"></canvas>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card analytics-card p-4">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-list-ul text-primary me-2"></i>Rincian Statistik Utilitas</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-container mb-0">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Nama Ruangan</th>
                                <th class="text-center" style="width: 20%;">Jumlah Booking</th>
                                <th class="text-center" style="width: 20%;">Total Jam Sewa</th>
                                <th class="text-center" style="width: 20%;">Okupansi Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roomStats as $stats)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $stats['room']->name }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $stats['room']->location }}</small>
                                </td>
                                <td class="text-center fw-semibold">{{ $stats['booking_count'] }}x</td>
                                <td class="text-center fw-semibold">{{ formatHoursToDaysAndHours($stats['total_hours']) }}</td>
                                <td class="text-center">
                                    @php
                                        $rate = $stats['occupancy_rate'];
                                        $badgeClass = 'bg-danger-subtle text-danger';
                                        if ($rate >= 50) $badgeClass = 'bg-success-subtle text-success';
                                        elseif ($rate >= 15) $badgeClass = 'bg-warning-subtle text-warning';
                                    @endphp
                                    <span class="badge-occupancy {{ $badgeClass }}">
                                        {{ $rate }}%
                                    </span>
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

        {{-- Recommendations Side Card --}}
        <div class="col-lg-4">
            <div class="card analytics-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Rekomendasi Bisnis</h5>
                
                @if(count($recommendations) > 0)
                    <p class="text-muted small mb-4">Saran keputusan operasional dan pricing terotomatisasi berdasarkan performa utilitas properti Anda:</p>
                    
                    @foreach($recommendations as $rec)
                        <div class="recommendation-item {{ $rec['class'] }}">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                @if($rec['type'] === 'promo')
                                    <span class="badge bg-warning text-dark fw-bold small"><i class="bi bi-percent me-1"></i>Weekday Promo</span>
                                @else
                                    <span class="badge bg-success text-white fw-bold small"><i class="bi bi-graph-up-arrow me-1"></i>Peak Adjust</span>
                                @endif
                                <span class="fw-bold text-dark small">{{ $rec['room_name'] }}</span>
                            </div>
                            <p class="mb-0 text-secondary small" style="line-height: 1.5;">{{ $rec['text'] }}</p>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-bookmark-check text-success fs-3"></i>
                        </div>
                        <h6 class="fw-bold">Okupansi Ruangan Stabil</h6>
                        <p class="text-muted small px-3">Tingkat penggunaan semua ruangan Anda berada dalam batas normal. Pertahankan performa layanan dan harga saat ini!</p>
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
            window.location.href = `{{ route('penyedia.report.occupancy') }}?filter=${this.value}`;
        });

        // Setup Chart
        const labels = @json($chartLabels);
        const dataValues = @json($chartValues);

        if (labels.length > 0) {
            const ctx = document.getElementById('occupancyChart').getContext('2d');
            
            // Create nice blue gradient for the bars
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, '#0284c7');
            gradient.addColorStop(1, '#0284c788');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tingkat Okupansi (%)',
                        data: dataValues,
                        backgroundColor: gradient,
                        borderColor: '#0284c7',
                        borderWidth: 1.5,
                        borderRadius: 8,
                        barPercentage: 0.5,
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
                                    return `Okupansi: ${context.raw}%`;
                                }
                            },
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            borderRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
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
                                font: { size: 11, weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
