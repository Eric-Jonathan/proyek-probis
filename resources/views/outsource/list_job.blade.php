@extends('layout.layout')

@section('custom_css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    body { background-color: var(--bs-tertiary-bg); }
    .rounded-4 { border-radius: 1rem !important; }
    .stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; border: none; background-color: var(--bs-card-bg); }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .table tbody tr:hover { background-color: rgba(0, 108, 228, 0.05) !important; }
    .modal-content { border-radius: 1.5rem !important; }

    /* ==========================================================================
       STYLE TABEL UTAMA (SERAGAM DENGAN HALAMAN LAIN)
       ========================================================================== */
    table.dataTable thead th {
        background-color: var(--bs-tertiary-bg) !important;
        border-bottom: 1px solid var(--bs-border-color) !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--bs-body-color);
        padding: 1.25rem 1rem !important;
    }
    .table tbody td { 
        padding: 1.25rem 1rem !important; 
        vertical-align: middle; 
        font-size: 0.9rem; 
    }

    /* ==========================================================================
       CSS OVERRIDE PAGINATION DATATABLES (SERAGAM DAN BERJARAK)
       ========================================================================== */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        background: none !important;
        display: inline !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        border: none !important;
        background: none !important;
    }
    .dataTables_wrapper .dataTables_paginate .pagination .page-item {
        width: 40px;
        height: 40px;
        margin: 0 4px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .dataTables_wrapper .dataTables_paginate .pagination .page-item .page-link {
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 12px !important;
        box-sizing: border-box !important;
    }
    .dataTables_wrapper .dataTables_paginate .pagination {
        border-radius: 0 !important;
        box-shadow: none !important;
    }
</style>
@endsection

@section('content')
<div class="container py-3">
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

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h3 class="fw-bold m-0 text-dark">Manajemen Penugasan</h3>
            <p class="text-secondary small m-0">Konfirmasi tugas Anda dan mulai pengisian laporan.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
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
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0 text-center" id="tableJobs" style="width: 100%;">
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
                    @forelse($allJobs as $job)
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
 
                                <div class="modal fade text-start" id="modalAmbil{{ $job->id }}" tabindex="-1" aria-hidden="true">
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
                                                    <form action="{{ route('outsource.job.take', $job->id) }}" method="POST">
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
                    @empty
                    <tr>
                        <td colspan="5" class="py-5 text-center text-muted">
                            <i class="bi bi-inboxes fs-2 d-block mb-2 text-secondary"></i>
                            <span class="fw-semibold">Tidak ada penugasan yang tersedia saat ini.</span>
                            <p class="small text-muted mb-0">Silakan hubungi admin atau tunggu hingga penugasan baru dibuat.</p>
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
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tableJobs').DataTable({
            responsive: true,
            // Mengatur layout DOM grid sistem Bootstrap 5
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari tugas...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            },
            columnDefs: [
                { orderable: false, targets: [4] } // Matikan sorting untuk kolom aksi
            ]
        });
    });
</script>
@endsection