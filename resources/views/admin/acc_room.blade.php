@extends('layout.layout')

@section('custom_css')
    <style>
        /* Base Styling */
        body {
            background-color: #f8f9fa;
            color: #334155;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        /* Utility Classes for Better Visuals */
        .rounded-4 { border-radius: 1rem !important; }
        .bg-success-soft { background-color: #dcfce7 !important; color: #15803d !important; }
        .bg-warning-soft { background-color: #fef9c3 !important; color: #a16207 !important; }
        .bg-danger-soft { background-color: #fee2e2 !important; color: #b91c1c !important; }
        .bg-primary-soft { background-color: #e0e7ff !important; color: #4338ca !important; }

        /* Stats Card Interaction */
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        /* Table & Action Styling */
        .table thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: #fcfcfd;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .table tbody tr { transition: background-color 0.2s; }
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

        /* Search Input */
        .input-group-search .form-control:focus {
            box-shadow: none;
            border-color: #cbd5e1 !important;
        }

        /* Responsive Mobile Adjustments */
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
                <h2 class="fw-bold mb-1">List Pengajuan Ruangan</h2>
                <p class="text-muted mb-0">Kelola dan monitor status penyewaan unit</p>
            </div>
        </div>

        <!-- Stats Grid (Responsive: 1 col on Mobile, 2 on Tablet, 4 on Laptop) -->
        <div class="row g-3 mb-4">
            @php
                $stats = [
                    ['label' => 'Total Ruangan', 'val' => 24, 'color' => 'primary', 'icon' => 'bi-door-open'],
                    ['label' => 'Unit Aktif', 'val' => 18, 'color' => 'success', 'icon' => 'bi-check-circle'],
                    ['label' => 'Perawatan', 'val' => 3, 'color' => 'warning', 'icon' => 'bi-tools'],
                    ['label' => 'Nonaktif', 'val' => 3, 'color' => 'danger', 'icon' => 'bi-power'],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="col-12 col-sm-6 col-lg-3">
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

        <!-- Table Card Container -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            
            <!-- Filters & Search Header -->
            <div class="card-header bg-white border-0 py-4 px-4">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-4">
                        <h5 class="fw-bold mb-0">Daftar Unit <span class="badge bg-light text-dark ms-2 fw-normal fs-6 px-3">24 Total</span></h5>
                    </div>
                    <div class="col-12 col-lg-8">
                        <form method="GET" class="row g-2 justify-content-lg-end">
                            <div class="col-12 col-md-6 col-xl-5">
                                <div class="input-group input-group-search">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted small"></i></span>
                                    <input type="text" class="form-control bg-light border-0 small" placeholder="Cari nama atau lantai...">
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-xl-2">
                                <select class="form-select bg-light border-0 small text-muted">
                                    <option value="">Status</option>
                                    <option>Aktif</option>
                                    <option>Maintenance</option>
                                </select>
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
                <table class="table table-hover align-middle mb-0" style="min-width: 850px;">
                    <thead class="text-secondary">
                        <tr>
                            <th class="ps-4 py-3">Informasi Ruangan</th>
                            <th class="py-3">Lokasi</th>
                            <th class="py-3">Kapasitas</th>
                            <th class="py-3">Tarif / Hari</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dummy Row 1 -->
                        <tr>
                            <td class="ps-4 py-4">
                                <div>
                                    <div class="fw-bold text-dark">Cozy Meeting Room</div>
                                    <div class="small text-muted">Ruang rapat dengan AC dan WiFi</div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary fw-medium px-3 py-2">Lantai 1</span></td>
                            <td>
                                <div class="text-dark fw-medium small"><i class="bi bi-people me-1"></i> 8 Pax</div>
                            </td>
                            <td><span class="fw-bold text-dark">Rp 500.000</span></td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-success-soft px-3 py-2 small fw-medium">Aktif</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm rounded-3 shadow-sm border overflow-hidden">
                                    <button class="btn btn-white border-0 px-3" title="Lihat"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-white border-0 px-3 text-success" title="Terima"><i class="bi bi-check-circle"></i></button>
                                    <button class="btn btn-white border-0 px-3 text-danger" title="Tolak"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <!-- Repeat Row with Status: Maintenance -->
                        <tr>
                            <td class="ps-4 py-4">
                                <div>
                                    <div class="fw-bold text-dark">Diponegoro Hotel - Ballroom</div>
                                    <div class="small text-muted">Fasilitas lengkap untuk event</div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary fw-medium px-3 py-2">Lantai 2</span></td>
                            <td>
                                <div class="text-dark fw-medium small"><i class="bi bi-people me-1"></i> 15 Pax</div>
                            </td>
                            <td><span class="fw-bold text-dark">Rp 350.000</span></td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-warning-soft px-3 py-2 small fw-medium">Maintenance</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm rounded-3 shadow-sm border overflow-hidden">
                                    <button class="btn btn-white border-0 px-3" title="Lihat"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-white border-0 px-3 text-success" title="Terima"><i class="bi bi-check-circle"></i></button>
                                    <button class="btn btn-white border-0 px-3 text-danger" title="Tolak"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination Footer -->
            <div class="card-footer bg-white border-0 py-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <span class="text-muted small mb-3 mb-md-0">Showing 1 to 10 of 24 results</span>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item"><a class="page-link border-0 text-muted"><i class="bi bi-chevron-left"></i></a></li>
                        <li class="page-item active"><a class="page-link border-0 shadow-sm rounded-2 mx-1">1</a></li>
                        <li class="page-item"><a class="page-link border-0 mx-1">2</a></li>
                        <li class="page-item"><a class="page-link border-0 mx-1">3</a></li>
                        <li class="page-item"><a class="page-link border-0 text-muted"><i class="bi bi-chevron-right"></i></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection