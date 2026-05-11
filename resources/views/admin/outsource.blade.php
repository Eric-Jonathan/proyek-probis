@extends('layout.layout')

@section('custom_css')
    <style>
        /* Base Styling - Konsisten dengan sistem sebelumnya */
        body {
            background-color: #f8f9fa;
            color: #334155;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        /* Utility Classes */
        .rounded-4 { border-radius: 1rem !important; }
        .bg-success-soft { background-color: #dcfce7 !important; color: #15803d !important; }
        .bg-warning-soft { background-color: #fef9c3 !important; color: #a16207 !important; }
        .bg-danger-soft { background-color: #fee2e2 !important; color: #b91c1c !important; }
        .bg-primary-soft { background-color: #e0e7ff !important; color: #4338ca !important; }

        .stat-card { 
            border: none; 
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        }
        
        .table thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: #fcfcfd;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .table tbody tr:hover { background-color: #f8fafc !important; }

        .btn-white {
            background-color: #fff;
            border: 1px solid #e2e8f0;
            color: #64748b;
        }
        .btn-white:hover {
            background-color: #f8fafc;
            color: #1e293b;
        }

        .company-logo {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Responsive Mobile */
        @media (max-width: 576px) {
            .content-wrapper { padding: 1rem !important; }
            .header-text { text-align: center; margin-bottom: 1rem; }
            .header-action { width: 100%; }
            .header-action .btn { width: 100%; }
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper py-3 px-4">
        
        <!-- Page Header -->
        <div class="row align-items-center mb-4">
            <div class="col-12 col-md-6 header-text">
                <h2 class="fw-bold mb-1">Daftar Mitra Outsource</h2>
                <p class="text-muted mb-0">Kelola data vendor dan tenaga kerja eksternal</p>
            </div>
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0 header-action">
                <a href="{{ route('admin.outsource.form') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-medium d-inline-flex align-items-center">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Mitra
                </a>
            </div>
        </div>

        <!-- Stats Grid (Responsive) -->
        <div class="row g-3 mb-4">
            @php
                $stats = [
                    ['label' => 'Total Mitra', 'val' => 12, 'color' => 'primary', 'icon' => 'bi-briefcase'],
                    ['label' => 'Mitra Aktif', 'val' => 10, 'color' => 'success', 'icon' => 'bi-check-circle'],
                    ['label' => 'Mitra Nonaktif', 'val' => 0, 'color' => 'danger', 'icon' => 'bi-x-octagon'],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100 stat-card">
                    <div class="d-flex align-items-center">
                        <div class="bg-{{ $stat['color'] }} bg-opacity-10 text-{{ $stat['color'] }} rounded-3 p-3 me-3">
                            <i class="bi {{ $stat['icon'] }} fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-medium d-block mb-1 small uppercase">{{ $stat['label'] }}</small>
                            <h4 class="fw-bold mb-0">{{ $stat['val'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Main Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            
            <!-- Filters & Search Header -->
            <div class="card-header bg-white border-0 py-4 px-4">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-4">
                        <h5 class="fw-bold mb-0">List Vendor <span class="badge bg-light text-dark ms-2 fw-normal fs-6 px-3">12 Perusahaan</span></h5>
                    </div>
                    <div class="col-12 col-lg-8">
                        <form method="GET" class="row g-2 justify-content-lg-end">
                            <div class="col-12 col-md-9 col-xl-10">
                                <div class="input-group input-group-search">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted small"></i></span>
                                    <input type="text" class="form-control bg-light border-0 small" placeholder="Cari nama vendor atau layanan...">
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-xl-2">
                                <button type="submit" class="btn btn-dark w-100 fw-medium shadow-sm">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table Body -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 950px;">
                    <thead class="text-secondary">
                        <tr>
                            <th class="ps-4 py-3">Perusahaan / Vendor</th>
                            <th class="py-3">Kontak</th>
                            <th class="py-3">Tanggal Kerjasama</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row 1 -->
                        <tr>
                            <td class="ps-4 py-4">
                                <div>
                                    <div class="fw-bold text-dark">PT Surveyor Indonesia</div>
                                    <div class="small text-muted">Jakarta, DKI Jakarta</div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark fw-medium small">Budi Santoso</div>
                                <div class="text-muted small">0812-3456-7321</div>
                            </td>
                            <td>
                                <div class="text-dark small">12 Jan 2026</div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-success-soft px-3 py-2 small fw-medium">Aktif</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm rounded-3 shadow-sm border overflow-hidden">
                                    <button class="btn btn-white border-0 px-3" title="Detail"><i class="bi bi-info-circle"></i></button>
                                    <button class="btn btn-white border-0 px-3" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-white border-0 px-3 text-danger" title="Putus Kontrak"><i class="bi bi-slash-circle"></i></button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 2 -->
                        <tr>
                            <td class="ps-4 py-4">
                                <div>
                                    <div class="fw-bold text-dark">PT Sucofindo</div>
                                    <div class="small text-muted">Jakarta Selatan, DKI Jakarta</div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark fw-medium small">Agus Wijaya</div>
                                <div class="text-muted small">0857-1122-5574</div>
                            </td>
                            <td>
                                <div class="text-black small">30 Apr 2026</div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-danger-soft px-3 py-2 small fw-medium">Nonaktif</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm rounded-3 shadow-sm border overflow-hidden">
                                    <button class="btn btn-white border-0 px-3" title="Detail"><i class="bi bi-info-circle"></i></button>
                                    <button class="btn btn-white border-0 px-3" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-white border-0 px-3 text-danger" title="Putus Kontrak"><i class="bi bi-slash-circle"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="card-footer bg-white border-0 py-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <span class="text-muted small mb-3 mb-md-0">Menampilkan 1 sampai 10 dari 12 mitra</span>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item"><a class="page-link border-0 text-muted"><i class="bi bi-chevron-left"></i></a></li>
                        <li class="page-item active"><a class="page-link border-0 shadow-sm rounded-2 mx-1">1</a></li>
                        <li class="page-item"><a class="page-link border-0 mx-1">2</a></li>
                        <li class="page-item"><a class="page-link border-0 text-muted"><i class="bi bi-chevron-right"></i></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection