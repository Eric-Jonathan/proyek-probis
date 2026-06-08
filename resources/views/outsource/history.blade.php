@extends('layout.layout')

@section('content')
<div class="container py-3">

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h3 class="fw-bold m-0 text-dark">Histori Laporan</h3>
            <p class="text-secondary small m-0">Daftar seluruh laporan survei yang telah Anda kirimkan.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach($stats as $s)
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100 border-0">
                <div class="d-flex align-items-center">
                    <div class="bg-{{ $s['color'] }} bg-opacity-10 text-{{ $s['color'] }} rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi {{ $s['icon'] }} fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 text-uppercase small" style="font-size: 0.65rem;">
                            {{ $s['label'] }}
                        </small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $s['val'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-3">
            <form action="{{ route('outsource.history') }}" method="GET">
                <div class="row g-2">
                    <div class="col-md-7">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0 shadow-none" placeholder="Cari laporan berdasarkan nama ruangan atau ID..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select form-select-sm shadow-none">
                            <option value="Semua Status" {{ request('status') == 'Semua Status' ? 'selected' : '' }}>Semua Status</option>
                            <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold rounded-pill shadow-sm">Filter</button>
                            @if(request()->filled('search') || (request()->filled('status') && request('status') !== 'Semua Status'))
                                <a href="{{ route('outsource.history') }}" class="btn btn-light btn-sm rounded-pill d-flex align-items-center justify-content-center px-3" title="Reset Filter">
                                    <i class="bi bi-x-circle text-secondary"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 text-start border-0">ID & Unit Ruangan</th>
                        <th class="border-0">Tgl Kirim</th>
                        <th class="border-0">Honor</th>
                        <th class="border-0">Status Admin</th>
                        <th class="pe-4 text-end border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allJobs as $job)
                    <tr>
                        <td class="ps-4 py-4 text-start">
                            <div class="fw-bold text-dark">{{ $job->room }}</div>
                            <small class="text-muted small">#SRV-{{ $job->id }}</small>
                        </td>
                        <td class="small fw-medium">{{ date('d M Y', strtotime($job->tgl_kirim)) }}</td>
                        <td class="fw-bold text-primary">Rp {{ number_format($job->fee, 0, ',', '.') }}</td>
                        <td>
                            @if($job->status == 'Diterima')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold" style="font-size: 0.65rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i> DISETUJUI
                                </span>
                            @elseif($job->status == 'Ditolak')
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 fw-bold" style="font-size: 0.65rem;">
                                    <i class="bi bi-x-circle-fill me-1"></i> DITOLAK
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-bold" style="font-size: 0.65rem;">
                                    <i class="bi bi-clock-fill me-1"></i> PENDING
                                </span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('outsource.history.detail', $job->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-5 text-center text-muted">
                            <i class="bi bi-file-earmark-x fs-1 d-block mb-2 text-secondary"></i>
                            <span class="fw-semibold">Tidak ada data histori laporan ditemukan</span>
                            @if(request()->filled('search') || (request()->filled('status') && request('status') !== 'Semua Status'))
                                <p class="small text-secondary mt-1">Coba sesuaikan kata kunci pencarian atau status filter Anda.</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8f9fa; }
    .rounded-4 { border-radius: 1rem !important; }
    .stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; border: none; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .table tbody tr:hover { background-color: #f8fbff !important; }
</style>
@endsection