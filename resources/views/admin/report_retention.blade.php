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
            <h2 class="fw-bold mb-1 text-dark">Laporan Retensi & Loyalitas Renter</h2>
            <p class="text-secondary mb-0">Analisis rasio renter berulang, segmentasi keaktifan penyewa, dan nilai loyalitas (Customer Lifetime Value)</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.report.retention.pdf') }}" class="btn btn-outline-danger rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2 fw-bold" id="downloadPdfBtn">
                <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">
        <!-- Repeat Renter Rate -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Repeat Renter Rate</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 2.2rem; font-weight: 800;">{{ $repeatRenterRate }}%</h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Persentase renter yang memesan kembali (>= 2x)</div>
            </div>
        </div>

        <!-- Active Renters -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Renter Aktif / Terdaftar</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 2.2rem; font-weight: 800;">{{ $totalActiveRenters }} <span style="font-size: 1.2rem; font-weight: 500;">/ {{ $totalRenters }}</span></h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Renter dengan minimal satu pesanan berhasil</div>
            </div>
        </div>

        <!-- Average Lifetime Value -->
        <div class="col-md-4">
            <div class="card kpi-card p-4" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Rata-rata Nilai LTV</span>
                        <h2 class="fw-extrabold mb-0 mt-1" style="font-size: 2rem; font-weight: 800;">Rp {{ number_format($avgLifetimeSpent, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon text-white-50">
                        <i class="bi bi-gem"></i>
                    </div>
                </div>
                <div class="mt-2 small text-white-50">Nilai rata-rata pengeluaran renter selama aktif</div>
            </div>
        </div>
    </div>

    {{-- Main Financial Analytics --}}
    <div class="row g-4 mb-4">
        {{-- Donut Chart Segmentation --}}
        <div class="col-lg-5">
            <div class="card analytics-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Segmentasi Keaktifan Renter</h5>
                <div style="height: 230px; position: relative;" class="d-flex justify-content-center">
                    @if($totalRenters > 0)
                        <canvas id="segmentationChart"></canvas>
                    @else
                        <div class="align-self-center text-muted small py-5">Tidak ada data segmentasi untuk ditampilkan</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Retention Recommendations --}}
        <div class="col-lg-7">
            <div class="card analytics-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Rekomendasi Retensi & Loyalitas</h5>
                
                @if($totalActiveRenters > 0)
                    @php
                        $isRepeatRateLow = $repeatRenterRate < 35.0;
                    @endphp
                    <div class="p-4 insight-box {{ $isRepeatRateLow ? 'insight-box warning' : 'insight-box' }} mb-3 border-0">
                        <div class="d-flex align-items-center gap-2 mb-2 {{ $isRepeatRateLow ? 'text-warning' : 'text-success' }}">
                            <i class="bi {{ $isRepeatRateLow ? 'bi-exclamation-triangle-fill' : 'bi-shield-check-fill' }} fs-5"></i>
                            <span class="fw-bold">Rasio Pembelian Berulang (Repeat Rate)</span>
                        </div>
                        <p class="mb-0 text-secondary small" style="line-height: 1.6;">
                            @if($isRepeatRateLow)
                                Rasio pembelian berulang renter berada di bawah target sehat ({{ $repeatRenterRate }}%). 
                                <strong>Rekomendasi:</strong> Admin disarankan meluncurkan kampanye loyalitas (remarketing), seperti memicu kode kupon diskon 15% untuk pemesanan kedua bagi renter baru yang telah menyelesaikan sewa pertama mereka.
                            @else
                                Rasio pembelian berulang renter berada dalam kondisi sehat ({{ $repeatRenterRate }}%). 
                                <strong>Rekomendasi:</strong> Tingkat retensi renter stabil. Admin dapat meluncurkan program reward-point platform (sistem loyalitas tempat-in) agar renter terus melakukan booking di platform kita daripada kompetitor.
                            @endif
                        </p>
                    </div>

                    <!-- Inactive segment warning -->
                    @if($inactiveCount > 0)
                        <div class="p-3 bg-light rounded-4">
                            <h6 class="fw-bold small text-dark"><i class="bi bi-envelope-exclamation text-danger me-1"></i>Reaktivasi Renter Pasif</h6>
                            <p class="text-muted small mb-0 mt-1" style="line-height: 1.5;">
                                Terdapat <strong>{{ $inactiveCount }}</strong> akun penyewa pasif (belum pernah melakukan booking sama sekali). Kirimkan email promosi penawaran khusus "Pemesanan Pertama Bebas Deposit" untuk meningkatkan rasio aktivasi mereka.
                            </p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-activity fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Belum ada data penyewa yang aktif untuk dianalisis.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Detail VIP Renter Table --}}
    <div class="card analytics-card p-4">
        <h5 class="fw-bold text-dark mb-4"><i class="bi bi-star-fill text-warning me-2"></i>Daftar VIP Renters (Customer Lifetime Value Teratas)</h5>
        <div class="table-responsive">
            <table class="table align-middle table-container mb-0 text-center">
                <thead>
                    <tr>
                        <th class="text-start" style="width: 40%;">Username / Email</th>
                        <th style="width: 20%;">Jumlah Pemesanan</th>
                        <th class="text-end" style="width: 20%;">Rata-rata Transaksi</th>
                        <th class="text-end" style="width: 20%;">Total Lifetime spent (CLV)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topRenters as $renter)
                    <tr>
                        <td class="text-start">
                            <div class="fw-bold text-dark">{{ $renter->username }}</div>
                            <small class="text-muted">{{ $renter->email }}</small>
                        </td>
                        <td class="fw-semibold">{{ $room->booking_count ?? $renter->booking_count }}x sewa</td>
                        <td class="text-end fw-semibold text-secondary">
                            Rp {{ number_format($renter->booking_count > 0 ? round($renter->total_spent / $renter->booking_count) : 0, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold text-success">
                            Rp {{ number_format($renter->clv, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            Belum ada data transaksi penyewa.
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
        // Setup Donut Chart for segmentation
        const loyalCount = {{ $loyalCount }};
        const occasionalCount = {{ $occasionalCount }};
        const inactiveCount = {{ $inactiveCount }};

        if (loyalCount > 0 || occasionalCount > 0 || inactiveCount > 0) {
            const ctxDonut = document.getElementById('segmentationChart').getContext('2d');
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Loyal (>=3 Booking)', 'Occasional (1-2 Booking)', 'Inactive (0 Booking)'],
                    datasets: [{
                        data: [loyalCount, occasionalCount, inactiveCount],
                        backgroundColor: [
                            '#4f46e5', // loyal (indigo)
                            '#0891b2', // occasional (cyan)
                            '#64748b'  // inactive (slate)
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
                                    return ` ${context.label}: ${context.raw} Renter (${percentage}%)`;
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
    });
</script>
@endsection
