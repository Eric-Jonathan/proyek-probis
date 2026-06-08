@extends('layout.layout')

@section('custom_css')
    <style>
        /* Base Styling */
        body { background-color: #f8fafc; color: #334155; font-family: 'Outfit', 'Inter', sans-serif; }
        .rounded-4 { border-radius: 1rem !important; }
        
        /* Subtle Badge Colors */
        .bg-success-soft { background-color: #dcfce7 !important; color: #166534 !important; }
        .bg-warning-soft { background-color: #fef3c7 !important; color: #92400e !important; }
        .bg-danger-soft { background-color: #fee2e2 !important; color: #991b1b !important; }
        .bg-primary-soft { background-color: #e0e7ff !important; color: #3730a3 !important; }
        .bg-info-soft { background-color: #e0f2fe !important; color: #075985 !important; }

        /* Card Animation & Design */
        .stat-card { 
            border: none; 
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.05) !important;
        }

        /* Section Divider */
        .section-divider {
            border-left: 4px solid #0064D2;
            padding-left: 15px;
            margin-bottom: 25px;
            margin-top: 35px;
        }

        /* Table Design */
        .table-responsive {
            border-radius: 12px;
            background: #ffffff;
        }
        .table thead th {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background-color: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
            color: #64748b;
            padding: 1rem 1.25rem;
        }
        .table tbody tr {
            transition: background-color 0.2s ease;
        }
        .table tbody tr:hover { 
            background-color: #f8fafc !important; 
        }
        .table tbody td {
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .progress-thin { height: 6px; border-radius: 10px; }
        .avatar-circle {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #0064D2 0%, #004a99 100%); 
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: bold; border-radius: 50%;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);
            border-radius: 1.25rem;
            padding: 2.25rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 100, 210, 0.15);
        }
        
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Filter Widget styling */
        .filter-selects {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper py-3 px-1">
    
    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Dashboard Welcome Header --}}
    <div class="dashboard-header mb-4 shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8 text-center text-md-start">
                <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 mb-2 fw-medium text-primary" style="font-size: 0.75rem;">
                    <i class="bi bi-calendar-event me-1 text-primary"></i> <span class="text-primary">{{ date('d M Y') }}</span>
                </span>
                <h2 class="fw-bold mb-1">Selamat Datang, {{ Auth::user()->username }}!</h2>
                <p class="mb-0 opacity-80 small">Panel Manajemen Tempat-In: Pantau pengajuan properti, kelola penugasan survei outsource, dan verifikasi keputusan persetujuan sewa.</p>
            </div>
            <div class="col-md-4 text-center text-md-end mt-3 mt-md-0 d-none d-md-block">
                <i class="bi bi-speedometer2" style="font-size: 4.5rem; opacity: 0.15;"></i>
            </div>
        </div>
    </div>

    {{-- Top Statistics Row --}}
    <div class="row g-3 mb-4">
        {{-- Total Rooms --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 text-uppercase" style="font-size: 0.65rem;">Total Unit Properti</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countTotalRooms }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Surveyor Assignment --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3 me-3">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 text-uppercase" style="font-size: 0.65rem;">Belum Di-Assign</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countWaiting }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Check Progress --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                        <i class="bi bi-geo-alt-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 text-uppercase" style="font-size: 0.65rem;">Pengecekan Aktif</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countActive }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Admin Decision --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 text-uppercase" style="font-size: 0.65rem;">Menunggu Keputusan</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countPendingReports }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 1: ANALYTICS CHART & SIDEBAR ASSIGNMENTS --}}
    <div class="row g-4 mb-4">
        
        {{-- Dynamic Report Chart (Left Column) --}}
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold m-0 text-dark"><i class="bi bi-graph-up text-primary me-2"></i>Grafik Statistik Laporan & Pengajuan</h6>
                        <small class="text-muted text-xs">Visualisasi tren pengajuan baru vs laporan survei selesai</small>
                    </div>
                    
                    {{-- Filter Controls --}}
                    <div class="filter-selects">
                        {{-- Period Dropdown --}}
                        <select id="chartPeriod" class="form-select form-select-sm shadow-none" style="width: 120px; border-radius: 20px; font-size: 0.8rem;">
                            <option value="day">Per Hari</option>
                            <option value="week">Per Minggu</option>
                            <option value="month" selected>Per Bulan</option>
                            <option value="year">Per Tahun</option>
                        </select>

                        {{-- Picker 1: Date Input (for day) --}}
                        <input type="date" id="pickerDate" class="form-control form-control-sm shadow-none d-none" style="width: 150px; border-radius: 20px; font-size: 0.8rem;" value="{{ date('Y-m-d') }}">

                        {{-- Picker 2: Week Input (for week) --}}
                        <input type="week" id="pickerWeek" class="form-control form-control-sm shadow-none d-none" style="width: 170px; border-radius: 20px; font-size: 0.8rem;" value="{{ date('Y') }}-W{{ date('W') }}">

                        {{-- Picker 3: Month & Year (for month) --}}
                        <div id="pickerMonthWrapper" class="d-flex gap-1">
                            <select id="pickerMonth" class="form-select form-select-sm shadow-none" style="width: 100px; border-radius: 20px; font-size: 0.8rem;">
                                @php
                                    $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    $currentMonth = (int)date('m');
                                @endphp
                                @foreach($months as $idx => $m)
                                    <option value="{{ $idx + 1 }}" {{ ($idx + 1) == $currentMonth ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                            <select id="pickerMonthYear" class="form-select form-select-sm shadow-none" style="width: 80px; border-radius: 20px; font-size: 0.8rem;">
                                @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Picker 4: Year Select (for year) --}}
                        <select id="pickerYear" class="form-select form-select-sm shadow-none d-none" style="width: 90px; border-radius: 20px; font-size: 0.8rem;">
                            @for($y = date('Y') - 3; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                
                {{-- Canvas for Chart --}}
                <div class="card-body p-4 pt-1">
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="dashboardChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Assignments & Active Monitor Stack (Right Column) --}}
        <div class="col-xl-4">
            <div class="row g-4">
                
                {{-- Antrean Pengajuan Baru (Card Summary with Redirect Button) --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
                        <div class="d-flex align-items-center mb-3 justify-content-between">
                            <div class="bg-white bg-opacity-20 rounded-3 p-3">
                                <i class="bi bi-file-earmark-plus fs-3 text-primary"></i>
                            </div>
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold" style="font-size: 0.72rem;">
                                {{ $countWaiting }} PENDING
                            </span>
                        </div>
                        <h5 class="fw-bold mb-1">Antrean Pengajuan Properti</h5>
                        <p class="mb-4 small opacity-80">Terdapat {{ $countWaiting }} unit properti baru dari penyedia yang belum memiliki penugasan surveyor lapangan.</p>
                        <a href="/admin/assign_outsource" class="btn btn-white w-100 py-2.5 rounded-pill fw-bold text-primary shadow-sm border-0 d-flex align-items-center justify-content-center gap-2" style="background-color: white; transition: transform 0.2s;">
                            Kelola Penugasan Baru <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>

                {{-- Active checking surveyor list --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-3 overflow-hidden">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-activity text-warning me-2"></i>Progres Survei Aktif</h6>
                        <div class="table-responsive border-0 shadow-none">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.82rem;">
                                <tbody>
                                    @forelse($monitoring->take(3) as $m)
                                    @php
                                        $statusLabels = [
                                            'on_the_way' => 'Menuju Lokasi',
                                            'checking' => 'Pemeriksaan'
                                        ];
                                    @endphp
                                    <tr>
                                        <td class="py-2.5 border-0 ps-0">
                                            <div class="fw-bold text-dark">{{ $m->room->name ?? 'N/A' }}</div>
                                            <small class="text-muted text-xs">Surveyor: {{ $m->surveyor->username ?? 'Unknown' }}</small>
                                        </td>
                                        <td class="py-2.5 border-0 pe-0" style="width: 140px;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="text-warning fw-bold" style="font-size: 0.65rem;">{{ $statusLabels[$m->assignment_status] ?? $m->assignment_status }}</span>
                                                <span class="text-muted" style="font-size: 0.65rem;">{{ $m->progress }}%</span>
                                            </div>
                                            <div class="progress progress-thin bg-light">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $m->progress }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-muted small border-0">
                                            <i class="bi bi-check-circle text-success fs-3 mb-2 d-block opacity-60"></i>
                                            Tidak ada surveyor yang aktif di lapangan.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- SECTION 2: KONFIRMASI KEPUTUSAN ADMIN --}}
    <div class="section-divider">
        <h5 class="fw-bold mb-1">Konfirmasi Keputusan Admin (Hasil Laporan Outsource)</h5>
        <p class="small text-muted mb-0">Tinjau laporan penilaian kelayakan lapangan dan putuskan persetujuan unit sewa.</p>
    </div>

    {{-- Table 2: Menunggu Keputusan Admin --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold m-0 text-dark"><i class="bi bi-clipboard-x me-2 text-danger"></i>Menunggu Keputusan Anda</h6>
            <small class="text-muted text-xs">Pilihlah salah satu unit di bawah ini untuk memeriksa laporan komparasi lengkap dan memberi keputusan</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead>
                    <tr>
                        <th class="text-start">Informasi Unit</th>
                        <th>Surveyor Lapangan</th>
                        <th>Rekomendasi Outsource</th>
                        <th>Status Review</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingReports as $room)
                    <tr>
                        <td class="text-start">
                            <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $room->room }}</div>
                            <small class="text-muted">{{ $room->floor }}</small>
                        </td>
                        <td class="small">{{ $room->outsource }}</td>
                        <td>
                            <span class="badge {{ $room->rek == 'Layak' ? 'bg-success-soft' : 'bg-danger-soft' }} rounded-pill px-3 py-2 fw-medium" style="font-size: 0.72rem;">
                                {{ $room->rek }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-warning-soft px-3 py-2 small fw-medium" style="font-size: 0.72rem;">
                                Pending Admin
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('outsource.history.detail', $room->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" style="font-size: 0.8rem;">
                                <i class="bi bi-shield-check me-1"></i> Periksa Laporan
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-check-all d-block fs-2 mb-2 text-success opacity-80"></i>
                            <span class="fw-medium d-block text-dark mb-1">Seluruh Laporan Selesai Ditinjau</span>
                            <small class="text-muted d-block text-xs">Tidak ada laporan hasil survei yang sedang menumpuk.</small>
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
    {{-- Include Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    {{-- Isolated Dashboard Chart Logic --}}
    <script src="{{ asset('custom_js/admin/dashboard.js') }}"></script>
@endsection