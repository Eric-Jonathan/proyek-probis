@extends('layout.layout')

@section('custom_css')
<style>
    body { background-color: #f8fafc; font-family: 'Outfit', 'Inter', sans-serif; }
    
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

    .insight-box {
        border-radius: 16px;
        background-color: #f0fdf4;
        border-left: 5px solid #10b981;
    }
    
    .insight-box.warning {
        background-color: #fffbeb;
        border-left-color: #f59e0b;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    
    {{-- Header --}}
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-8">
            <h2 class="fw-bold mb-1 text-dark">Laporan Profitabilitas Platform</h2>
            <p class="text-secondary mb-0">Analisis total transaksi (GMV), pendapatan bagi-hasil komisi platform, dan rata-rata nilai transaksi</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-inline-flex align-items-center gap-2 bg-white p-2 rounded-pill shadow-sm border">
                <span class="small text-muted ps-2 fw-semibold"><i class="bi bi-funnel me-1"></i>Periode:</span>
                <select id="filterPeriod" class="form-select border-0 bg-transparent py-1 pe-4 fw-bold text-primary" style="width: auto; box-shadow: none; outline: none;">
                    <option value="30" {{ $filter === '30' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="90" {{ $filter === '90' ? 'selected' : '' }}>90 Hari Terakhir</option>
                    <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                </select>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">
        <!-- Total GMV -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Gross Merchandise Value (GMV)</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 1.8rem; font-weight: 800;">Rp {{ number_format($totalGmv, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-piggy-bank"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Akumulasi nilai transaksi persewaan lunas & aktif</div>
            </div>
        </div>

        <!-- Commission Fee (10%) -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Komisi Platform (10%)</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 1.8rem; font-weight: 800;">Rp {{ number_format($commissionFee, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Estimasi total bagi-hasil pendapatan bersih platform</div>
            </div>
        </div>

        <!-- Average Transaction Value (ATV) -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Average Transaction Value (ATV)</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 1.8rem; font-weight: 800;">Rp {{ number_format($avgTransactionValue, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-calculator"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Nilai rata-rata per satu transaksi sewa ruangan</div>
            </div>
        </div>
    </div>

    {{-- Main Financial Analytics --}}
    <div class="row g-4 mb-4">
        {{-- Line Chart Monthly Commissions --}}
        <div class="col-lg-7">
            <div class="card analytics-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-graph-up text-primary me-2"></i>Tren Pendapatan Komisi Bulanan (Tahun ini)</h5>
                <div style="height: 300px; position: relative;">
                    <canvas id="commissionTrendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Profitability Insights --}}
        <div class="col-lg-5">
            <div class="card analytics-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-journal-check text-primary me-2"></i>Analisis & Keputusan Strategis</h5>
                
                @if($totalGmv > 0)
                    <!-- ATV Recommendation -->
                    @php
                        $isAtvLow = $avgTransactionValue < 500000;
                    @endphp
                    <div class="p-3 insight-box {{ $isAtvLow ? 'warning' : '' }} mb-3 border-0">
                        <div class="d-flex align-items-center gap-2 mb-2 {{ $isAtvLow ? 'text-warning' : 'text-success' }}">
                            <i class="bi {{ $isAtvLow ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' }} fs-5"></i>
                            <span class="fw-bold">Analisis ATV (Rata-Rata Transaksi)</span>
                        </div>
                        <p class="mb-0 text-secondary small" style="line-height: 1.6;">
                            @if($isAtvLow)
                                Rata-rata nilai transaksi sewa (ATV) berada di bawah target (Rp {{ number_format($avgTransactionValue, 0, ',', '.') }}). 
                                <strong>Rekomendasi:</strong> Admin dapat menyarankan provider untuk menyediakan paket bundling addon (misal: gratis proyektor/makanan ringan) atau menyetel durasi sewa minimal 3 jam untuk menaikkan nilai sewa.
                            @else
                                Rata-rata nilai transaksi sewa (ATV) berada pada angka yang ideal (Rp {{ number_format($avgTransactionValue, 0, ',', '.') }}). 
                                <strong>Rekomendasi:</strong> Pertahankan model harga dan terus dorong iklan untuk properti premium berkapasitas besar guna mempertahankan nilai transaksi.
                            @endif
                        </p>
                    </div>

                    <!-- Business Model Tip -->
                    <div class="p-3 bg-light rounded-4">
                        <h6 class="fw-bold small text-dark"><i class="bi bi-lightbulb text-warning me-1"></i>Tip Komisi Platform</h6>
                        <p class="text-muted small mb-0 mt-1" style="line-height: 1.5;">
                            Pendapatan komisi saat ini didasarkan pada flat fee 10%. Untuk menarik lebih banyak properti kelas atas, pertimbangkan menerapkan <strong>Skema Komisi Berjenjang (Tiered Commission)</strong>: 8% untuk provider dengan GMV di atas Rp 20 juta/bulan guna memicu volume booking yang lebih besar.
                        </p>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-activity fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Belum ada data transaksi yang cukup untuk memproses rekomendasi finansial.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Detail Room Performance Table --}}
    <div class="card analytics-card p-4">
        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-building-up text-primary me-2"></i>Properti Kontributor Komisi Teratas</h5>
        <div class="table-responsive">
            <table class="table align-middle table-container mb-0 text-center">
                <thead>
                    <tr>
                        <th class="text-start" style="width: 40%;">Nama Properti</th>
                        <th style="width: 20%;">Jumlah Transaksi</th>
                        <th class="text-end" style="width: 20%;">Total Omset (GMV)</th>
                        <th class="text-end" style="width: 20%;">Komisi Bersih Platform</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topRooms as $room)
                    <tr>
                        <td class="text-start">
                            <div class="fw-bold text-dark">{{ $room->name }}</div>
                        </td>
                        <td class="fw-semibold">{{ $room->booking_count }}x sewa</td>
                        <td class="text-end fw-semibold text-secondary">
                            Rp {{ number_format($room->revenue, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold text-primary">
                            Rp {{ number_format($room->commission, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            Belum ada data transaksi properti.
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
{{-- Chart JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle filter change
        document.getElementById('filterPeriod').addEventListener('change', function() {
            window.location.href = `{{ route('admin.report.profitability') }}?filter=${this.value}`;
        });

        // Setup Line Chart - Monthly commission
        const trendLabels = @json($chartLabels);
        const trendValues = @json($chartCommissionValues);

        if (trendLabels.length > 0) {
            const ctxLine = document.getElementById('commissionTrendChart').getContext('2d');
            
            // Create nice gradient for the line area
            const lineGradient = ctxLine.createLinearGradient(0, 0, 0, 300);
            lineGradient.addColorStop(0, '#2563eb33');
            lineGradient.addColorStop(1, '#2563eb00');

            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Pendapatan Komisi',
                        data: trendValues,
                        borderColor: '#2563eb',
                        borderWidth: 3,
                        pointBackgroundColor: '#2563eb',
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
                                    return ` Komisi: Rp ${context.raw.toLocaleString('id-ID')}`;
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
