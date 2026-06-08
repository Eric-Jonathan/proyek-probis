@extends('layout.layout')

@section('custom_css')
<style>
    :root {
        --primary-blue: #0064D2;
        --dark-blue: #004a99;
        --light-bg: #f8f9fa;
    }

    body { background-color: var(--light-bg); font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Sidebar Styling */
    .sidebar-outsource {
        min-height: 100vh;
        background: #1a1d20;
        color: white;
        transition: all 0.3s;
    }
    .nav-link-custom {
        color: rgba(255,255,255,0.7);
        padding: 12px 20px;
        border-radius: 12px;
        margin: 5px 15px;
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: 0.3s;
    }
    .nav-link-custom:hover, .nav-link-custom.active {
        background: var(--primary-blue);
        color: white;
    }

    /* Card & UI Styling */
    .card-custom { border-radius: 20px; border: none; transition: transform 0.2s; }
    .card-custom:hover { transform: translateY(-5px); }
    
    .badge-subtle { font-weight: 600; padding: 6px 12px; border-radius: 50px; }
    .bg-success-subtle { background-color: #dcfce7; color: #15803d; }
    .bg-warning-subtle { background-color: #fef9c3; color: #854d0e; }
    
    .btn-pill { border-radius: 50px; font-weight: 700; padding: 10px 25px; transition: 0.3s; }
</style>
@endsection

@section('content')
<div class="container-fluid p-0 d-flex">
    
    <div class="flex-grow-1 p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Halo, {{ Auth::user()->username ?? 'Mitra Outsource' }} 👋</h3>
                <p class="text-secondary small">Anda memiliki <strong>{{ $activeJobs->count() }} project berjalan</strong> yang membutuhkan survei lokasi.</p>
            </div>
            <div class="d-flex align-items-center bg-white p-2 rounded-pill shadow-sm px-3">
                <div class="text-end me-3">
                    <small class="text-muted d-block" style="font-size: 0.7rem;">STATUS MITRA</small>
                    <span class="badge bg-{{ $activeJobs->count() > 0 ? 'success' : 'secondary' }} rounded-pill">
                        {{ $activeJobs->count() > 0 ? 'Aktif / On-Project' : 'Standby' }}
                    </span>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->username ?? 'Outsource') }}&background=0064D2&color=fff" class="rounded-circle" width="45">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-custom shadow-sm p-4 text-white h-100" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);">
                    <p class="small mb-1 opacity-75 fw-bold text-uppercase">Honor Diterima</p>
                    <h2 class="fw-bold mb-0">Rp {{ number_format($totalHonor, 0, ',', '.') }}</h2>
                    <div class="mt-3 small"><i class="bi bi-info-circle me-1"></i> Total dari {{ $completedCount }} project selesai</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom shadow-sm p-4 h-100 bg-white">
                    <p class="small mb-1 text-secondary fw-bold text-uppercase">Project Berjalan</p>
                    <h2 class="fw-bold mb-0 text-dark">{{ sprintf("%02d", $activeJobs->count()) }}</h2>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ $activeJobs->count() > 0 ? '100' : '0' }}%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom shadow-sm p-4 h-100 bg-white">
                    <p class="small mb-1 text-secondary fw-bold text-uppercase">Akurasi Laporan</p>
                    <h2 class="fw-bold mb-0 text-dark">{{ $accuracy }}%</h2>
                    <div class="mt-3 text-warning small">
                        @php $stars = $accuracy >= 90 ? 5 : ($accuracy >= 70 ? 4 : 3); @endphp
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $stars ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-custom shadow-sm h-100 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 border-start border-primary border-4 ps-3">Tugas Survei Lokasi</h5>
                        <a href="{{ route('outsource.job') }}" class="text-primary small fw-bold text-decoration-none">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr class="small text-uppercase text-muted">
                                    <th class="border-0 px-3 py-3">Unit</th>
                                    <th class="border-0">Lokasi</th>
                                    <th class="border-0">Honor</th>
                                    <th class="border-0 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeJobs as $job)
                                <tr>
                                    <td class="px-3">
                                        <div class="fw-bold text-dark">{{ $job->room }}</div>
                                        <small class="text-muted">ID: #SRV-{{ $job->assignment_id }}</small>
                                    </td>
                                    <td>
                                        <div class="small fw-bold"><i class="bi bi-geo-alt-fill text-danger"></i> {{ $job->city }}</div>
                                        <div class="text-muted small">{{ $job->address }}</div>
                                    </td>
                                    <td class="fw-bold text-primary">Rp {{ number_format($job->fee, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('outsource.form', $job->assignment_id) }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                            Lapor Sekarang
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-muted">
                                        <i class="bi bi-check2-circle fs-2 d-block mb-1 text-success"></i>
                                        <span class="small">Semua tugas survei selesai dikerjakan!</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-custom shadow-sm h-100 bg-white p-4">
                    <h5 class="fw-bold mb-4 border-start border-secondary border-4 ps-3">Riwayat Kerja</h5>
                    <div class="list-group list-group-flush">
                        @forelse($recentHistory as $item)
                        @php
                            $roomStatus = $item->room->status ?? 0;
                            if ($roomStatus == 2) {
                                $icon = 'bi-check-circle-fill';
                                $color = 'success';
                                $statusLabel = 'Laporan Diterima';
                            } elseif ($roomStatus == 3) {
                                $icon = 'bi-x-circle-fill';
                                $color = 'danger';
                                $statusLabel = 'Laporan Ditolak';
                            } else {
                                $icon = 'bi-clock-history';
                                $color = 'warning';
                                $statusLabel = 'Proses Review';
                            }
                        @endphp
                        <a href="{{ route('outsource.history.detail', $item->assignment_id) }}" class="list-group-item list-group-item-action px-0 py-3 border-bottom border-light">
                            <div class="d-flex align-items-center">
                                <div class="bg-{{ $color }}-subtle p-2 rounded-3 me-3 text-{{ $color }}">
                                    <i class="bi {{ $icon }} fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1 small">{{ $item->room->name ?? 'N/A' }}</h6>
                                    <span class="badge-subtle bg-{{ $color }}-subtle d-inline-block" style="font-size: 0.65rem;">{{ $statusLabel }}</span>
                                </div>
                                <div class="text-end">
                                    <small class="d-block text-muted" style="font-size: 0.7rem;">{{ $item->report->created_at ? $item->report->created_at->format('d M') : '' }}</small>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="py-4 text-center text-muted">
                            <i class="bi bi-file-earmark-lock2 fs-3 d-block mb-1 text-secondary"></i>
                            <span class="small">Belum ada riwayat kerja</span>
                        </div>
                        @endforelse
                    </div>
                    <a href="{{ route('outsource.history') }}" class="btn btn-light w-100 mt-3 rounded-pill btn-sm fw-bold text-secondary text-decoration-none">
                        Lihat Semua Riwayat
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection