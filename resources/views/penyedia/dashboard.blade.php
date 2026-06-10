@extends('layout.layout')

@section('content')
<style>
    :root {
        --primary-blue: #006ce4;
        --light-blue: #e8f2ff;
        --success-green: #28a745;
        --warning-orange: #ff9800;
        --danger-red: #dc3545;
        --text-dark: #1f2937;
    }

    body {
        background-color: #f8f9fa;
    }

    .dashboard-card {
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .rating-star {
        color: #ffc107;
        font-size: 1.1rem;
    }
</style>

<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Dashboard Mitra</h2>
            <p class="text-secondary mb-0">Kelola dan pantau performa seluruh properti Anda dalam satu panel.</p>
        </div>
    </div>

    {{-- Row 1: High Level Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card dashboard-card border-0 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Total Ruangan</p>
                        <h2 class="fw-bold mb-0 text-dark">{{ $totalRooms }}</h2>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dashboard-card border-0 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Ruangan Aktif</p>
                        <h2 class="fw-bold mb-0 text-success">{{ $activeRooms }}</h2>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dashboard-card border-0 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Total Pendapatan</p>
                        <h3 class="fw-bold mb-0 text-primary">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Customer Quality Ratings --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card dashboard-card border-0 shadow-sm p-4 border-start border-primary border-5" style="border-radius: 12px;">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Rata-Rata Kebersihan</h6>
                <div class="d-flex align-items-center gap-2">
                    <h3 class="fw-bold mb-0 text-dark">{{ $avgKebersihan }}</h3>
                    <div class="d-flex align-items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($avgKebersihan) ? 'bi-star-fill text-warning' : 'bi-star text-muted opacity-50' }} rating-star"></i>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dashboard-card border-0 shadow-sm p-4 border-start border-success border-5" style="border-radius: 12px;">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Rata-Rata Pelayanan</h6>
                <div class="d-flex align-items-center gap-2">
                    <h3 class="fw-bold mb-0 text-dark">{{ $avgPelayanan }}</h3>
                    <div class="d-flex align-items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($avgPelayanan) ? 'bi-star-fill text-warning' : 'bi-star text-muted opacity-50' }} rating-star"></i>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dashboard-card border-0 shadow-sm p-4 border-start border-warning border-5" style="border-radius: 12px;">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Rata-Rata Kenyamanan</h6>
                <div class="d-flex align-items-center gap-2">
                    <h3 class="fw-bold mb-0 text-dark">{{ $avgKenyamanan }}</h3>
                    <div class="d-flex align-items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($avgKenyamanan) ? 'bi-star-fill text-warning' : 'bi-star text-muted opacity-50' }} rating-star"></i>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Graphic Chart & Table List --}}
    <div class="row g-4 mb-4">
        {{-- LINE CHART --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 16px;">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" id="chartTitle">Grafik Pendapatan Bulanan</h5>
                        <small class="text-muted" id="chartSubtitle">Tahun ini</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <select id="chartFilter" class="form-select form-select-sm border-secondary-subtle rounded-pill px-3" style="width: 130px; font-size: 0.85rem;">
                            <option value="day">Hari</option>
                            <option value="week">Minggu</option>
                            <option value="month" selected>Bulan</option>
                            <option value="year">Tahun</option>
                        </select>
                    </div>
                </div>
                <div style="height: 320px; position: relative;">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- ROOM LIST --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark">Daftar Properti Ruangan</h5>
                    <a href="{{ route('rooms.index') }}" class="small text-primary fw-semibold text-decoration-none">Lihat Semua</a>
                </div>
                <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="font-size: 0.8rem;">Nama</th>
                                <th style="font-size: 0.8rem;">Harga</th>
                                <th style="font-size: 0.8rem; width: 25%;">Status</th>
                                <th style="font-size: 0.8rem; width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $r)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 140px; font-size: 0.9rem;">{{ $r->name }}</div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 140px;"><i class="bi bi-geo-alt"></i> {{ $r->location }}</small>
                                </td>
                                <td class="fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    Rp {{ number_format($r->price, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($r->status == 2)
                                        <span class="badge bg-success-subtle text-success" style="font-size: 0.75rem;">Aktif</span>
                                    @elseif($r->status == 1)
                                        <span class="badge bg-warning-subtle text-warning" style="font-size: 0.75rem;">Review</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger" style="font-size: 0.75rem;">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('rooms.show', $r->room_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" style="font-size: 0.75rem;">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">Belum ada ruangan terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 4: Booking Masuk Terjadwal --}}
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Booking Masuk Terjadwal</h5>
                        <p class="text-secondary small mb-0">Daftar booking aktif yang akan datang atau sedang berlangsung (tidak termasuk selesai & dibatalkan).</p>
                    </div>
                    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm">
                        Kelola Semua Booking
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">ID Booking</th>
                                <th style="width: 25%;">Ruangan</th>
                                <th style="width: 20%;">Penyewa</th>
                                <th style="width: 25%;">Waktu Sewa</th>
                                <th style="width: 10%;">Skema Pembayaran</th>
                                <th class="text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($incomingBookings as $b)
                            <tr>
                                <td class="fw-bold">#{{ $b->booking_id }}</td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">{{ $b->roomDetail->item_name ?? 'Ruangan' }}</div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 180px;"><i class="bi bi-geo-alt"></i> {{ $b->roomDetail->room->location ?? 'Lokasi' }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $b->user->username ?? 'Guest' }}</div>
                                    <small class="text-muted"><i class="bi bi-whatsapp"></i> +62{{ $b->phone }}</small>
                                </td>
                                <td>
                                    @php
                                        $startDateFormatted = date('d M Y', strtotime($b->start_date));
                                        $endDateFormatted = date('d M Y', strtotime($b->end_date));
                                    @endphp
                                    <div class="small fw-bold text-dark">
                                        @if(date('Y-m-d', strtotime($b->start_date)) === date('Y-m-d', strtotime($b->end_date)))
                                            {{ $startDateFormatted }}
                                        @else
                                            {{ date('d M', strtotime($b->start_date)) }} - {{ $endDateFormatted }}
                                        @endif
                                    </div>
                                    <div class="text-muted small">{{ date('H:i', strtotime($b->start_date)) }} - {{ date('H:i', strtotime($b->end_date)) }}</div>
                                </td>
                                <td>
                                    @if($b->status == 1)
                                        <span class="badge rounded-pill bg-success-subtle text-success px-2.5 py-1.5 fw-semibold" style="font-size: 0.75rem;">Lunas</span>
                                    @elseif($b->status == 3)
                                        <span class="badge rounded-pill bg-warning-subtle text-warning px-2.5 py-1.5 fw-semibold" style="font-size: 0.75rem;">Cicilan ({{ $b->installments_paid }}/3)</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary px-2.5 py-1.5 fw-semibold" style="font-size: 0.75rem;">Status: {{ $b->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('penyedia.detail_history', $b->booking_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">Tidak ada booking masuk yang sedang berjalan atau akan datang.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart JS Library --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('earningsChart').getContext('2d');
        let currentChart = null;

        const filterSelect = document.getElementById('chartFilter');
        const chartTitle = document.getElementById('chartTitle');
        const chartSubtitle = document.getElementById('chartSubtitle');

        const filterDetails = {
            day: { title: 'Grafik Pendapatan Harian', subtitle: '7 Hari Terakhir' },
            week: { title: 'Grafik Pendapatan Mingguan', subtitle: 'Bulan ini' },
            month: { title: 'Grafik Pendapatan Bulanan', subtitle: 'Tahun ini' },
            year: { title: 'Grafik Pendapatan Tahunan', subtitle: '5 Tahun Terakhir' }
        };

        function updateChart(filterType) {
            fetch(`{{ route('penyedia.dashboard.chart') }}?filter=${filterType}`)
                .then(response => response.json())
                .then(data => {
                    chartTitle.textContent = filterDetails[filterType].title;
                    chartSubtitle.textContent = filterDetails[filterType].subtitle;

                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(0, 108, 228, 0.35)');
                    gradient.addColorStop(1, 'rgba(0, 108, 228, 0.00)');

                    if (currentChart) {
                        currentChart.destroy();
                    }

                    currentChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Pendapatan (IDR)',
                                data: data.values,
                                borderColor: '#006ce4',
                                borderWidth: 3,
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#006ce4',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
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
                                    padding: 12,
                                    backgroundColor: '#1f2937',
                                    titleColor: '#fff',
                                    bodyColor: '#e5e7eb',
                                    titleFont: { weight: 'bold', size: 13 },
                                    bodyFont: { size: 12 },
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#9ca3af',
                                        font: { size: 11 }
                                    }
                                },
                                y: {
                                    grid: {
                                        color: '#f3f4f6'
                                    },
                                    ticks: {
                                        color: '#9ca3af',
                                        font: { size: 11 },
                                        callback: function(value) {
                                            if (value >= 1000000) {
                                                return 'Rp ' + (value / 1000000) + 'jt';
                                            } else if (value >= 1000) {
                                                return 'Rp ' + (value / 1000) + 'k';
                                            }
                                            return 'Rp ' + value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }

        // Initialize with 'month'
        updateChart('month');

        // Add change listener
        filterSelect.addEventListener('change', function() {
            updateChart(this.value);
        });
    });
</script>
@endsection