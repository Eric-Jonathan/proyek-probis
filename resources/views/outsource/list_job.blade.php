@extends('layout.layout')

@section('content')
<div class="container py-3">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h3 class="fw-bold m-0 text-dark">Manajemen Penugasan</h3>
            <p class="text-secondary small m-0">Konfirmasi tugas Anda dan mulai pengisian laporan.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @php
            $stats = [
                ['label' => 'Tugas Tersedia', 'val' => 5, 'color' => 'primary', 'icon' => 'bi-briefcase'],
                ['label' => 'Sedang Berjalan', 'val' => 2, 'color' => 'warning', 'icon' => 'bi-clock-history'],
                ['label' => 'Total Honor', 'val' => 'Rp 1.2M', 'color' => 'success', 'icon' => 'bi-wallet2'],
                ['label' => 'Perlu Tindakan', 'val' => 1, 'color' => 'danger', 'icon' => 'bi-bell'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100 border-0">
                <div class="d-flex align-items-center">
                    <div class="bg-{{ $s['color'] }} bg-opacity-10 text-{{ $s['color'] }} rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi {{ $s['icon'] }} fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 text-uppercase small" style="font-size: 0.65rem;">{{ $s['label'] }}</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $s['val'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 text-start border-0">ID & Unit Ruangan</th>
                        <th class="border-0">Wilayah</th>
                        <th class="border-0">Honor</th>
                        <th class="border-0">Deadline</th>
                        <th class="pe-4 text-end border-0">Konfirmasi & Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $allJobs = collect([
                            (object)['id' => 101, 'room' => 'Cozy Meeting Room', 'city' => 'Batu', 'fee' => 500000, 'deadline' => '2026-05-15', 'is_taken' => true],
                            (object)['id' => 102, 'room' => 'Grand Ballroom Kencana', 'city' => 'Batu', 'fee' => 5500000, 'deadline' => '2026-05-18', 'is_taken' => false],
                            (object)['id' => 105, 'room' => 'Studio Foto Malang', 'city' => 'Malang', 'fee' => 175000, 'deadline' => '2026-05-20', 'is_taken' => false],
                        ]);
                    @endphp

                    @foreach($allJobs as $job)
                    <tr>
                        <td class="ps-4 py-4 text-start">
                            <div class="fw-bold text-dark">{{ $job->room }}</div>
                            <small class="text-muted small">ID: #SRV-{{ $job->id }}</small>
                        </td>
                        <td><span class="small fw-medium text-secondary"><i class="bi bi-geo-alt me-1 text-primary"></i>{{ $job->city }}</span></td>
                        <td class="fw-bold text-primary">Rp {{ number_format($job->fee, 0, ',', '.') }}</td>
                        <td class="small fw-bold">{{ date('d M Y', strtotime($job->deadline)) }}</td>
                        <td class="pe-4 text-end">
                            @if(!$job->is_taken)
                                <button type="button" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAmbil{{ $job->id }}">
                                    Ambil Tugas <i class="bi bi-plus-circle ms-1"></i>
                                </button>

                                <div class="modal fade" id="modalAmbil{{ $job->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <div class="modal-body p-4 text-center">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                                                    <i class="bi bi-question-circle fs-1"></i>
                                                </div>
                                                <h5 class="fw-bold mb-2">Konfirmasi Pengambilan</h5>
                                                <p class="text-muted mb-4 small">Apakah Anda bersedia melakukan survei untuk <strong>{{ $job->room }}</strong>? Setelah diambil, tugas akan masuk ke daftar aktif Anda.</p>
                                                
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                                                    <form action="#" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Ya, Saya Siap!</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('outsource.form', $job->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm text-decoration-none">
                                    Buka Form Laporan <i class="bi bi-pencil-square ms-1"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
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
    .modal-content { border-radius: 1.5rem !important; }
</style>
@endsection