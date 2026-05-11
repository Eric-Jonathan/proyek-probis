@extends('layout.layout')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold m-0 text-dark">Manajemen Penugasan</h3>
        <p class="text-secondary small m-0">Daftar seluruh project survei yang ditugaskan kepada Anda</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
    <div class="card-body p-3">
        <form action="#" method="GET">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari ID Project atau Nama Unit...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option selected>Semua Wilayah</option>
                        <option>Malang</option>
                        <option>Batu</option>
                        <option>Surabaya</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option selected>Urutkan: Terbaru</option>
                        <option>Honor Tertinggi</option>
                        <option>Deadline Terdekat</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr style="font-size: 0.8rem; letter-spacing: 0.5px;">
                    <th class="ps-4 py-3 border-0">ID & UNIT RUANGAN</th>
                    <th class="border-0">WILAYAH</th>
                    <th class="border-0">HONOR PROJECT</th>
                    <th class="border-0">DEADLINE</th>
                    <th class="border-0">PRIORITAS</th>
                    <th class="border-0 text-center pe-4">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allJobs as $job)
                <tr>
                    <td class="ps-4 py-3">
                        <div class="fw-bold text-dark">{{ $job->room }}</div>
                        <small class="text-muted text-uppercase" style="font-size: 0.7rem;">ID: #SRV-{{ $job->id }}</small>
                    </td>
                    <td>
                        <span class="fw-semibold text-secondary"><i class="bi bi-geo-alt me-1"></i>{{ $job->city }}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-primary">Rp {{ number_format($job->fee, 0, ',', '.') }}</div>
                    </td>
                    <td>
                        <div class="small fw-bold text-dark">{{ date('d M Y', strtotime($job->deadline)) }}</div>
                    </td>
                    <td>
                        @if($job->priority == 'High')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">High Priority</span>
                        @elseif($job->priority == 'Medium')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">Medium</span>
                        @else
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">Standard</span>
                        @endif
                    </td>
                    <td class="text-center pe-4">
                        <a href="{{ route('outsource.form', $job->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                            Buka Form <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .bg-danger-subtle { background-color: #fee2e2; }
    .bg-warning-subtle { background-color: #fef9c3; }
    .bg-info-subtle { background-color: #e0f2fe; }
    
    .table tbody tr { transition: all 0.2s ease; }
    .table tbody tr:hover { background-color: #f8fbff; }
</style>
@endsection