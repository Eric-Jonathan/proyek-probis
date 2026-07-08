@extends('layout.layout')

@section('custom_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        /* Base Styling - Konsisten dengan sistem sebelumnya */
        body {
            background-color: var(--bs-tertiary-bg);
            color: var(--bs-body-color);
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
                    ['label' => 'Total Mitra', 'val' => $totalMitra ?? 0, 'color' => 'primary', 'icon' => 'bi-briefcase'],
                    ['label' => 'Mitra Aktif', 'val' => $mitraAktif ?? 0, 'color' => 'success', 'icon' => 'bi-check-circle'],
                    ['label' => 'Mitra Nonaktif', 'val' => $mitraNonaktif ?? 0, 'color' => 'danger', 'icon' => 'bi-x-octagon'],
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
                                <small class="text-muted fw-medium d-block mb-1 small text-uppercase">{{ $stat['label'] }}</small>
                                <h4 class="fw-bold mb-0 text-dark">{{ $stat['val'] }}</h4>
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
                    <div class="col-12">
                        <h5 class="fw-bold mb-0">List Vendor <span class="badge bg-light text-dark ms-2 fw-normal fs-6 px-3">{{ $totalMitra }} Perusahaan</span></h5>
                    </div>
                </div>
            </div>

            <!-- Table Body -->
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle mb-0" id="tableOutsource" style="width: 100%; min-width: 950px;">
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
                        @if($partners->count() > 0)
                            @foreach($partners as $p)
                            <tr>
                                <td class="ps-4 py-4">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $p->company_name }}</div>
                                        <div class="small text-muted">{{ Str::limit($p->company_address, 40) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium small">{{ $p->pic_name }} ({{ $p->pic_position }})</div>
                                    <div class="text-muted small">{{ $p->pic_phone ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="text-dark small">{{ $p->created_at->translatedFormat('d M Y') }}</div>
                                </td>
                                <td class="text-center">
                                    @if($p->status == 1)
                                        <span class="badge rounded-pill bg-success-soft px-3 py-2 small fw-medium">Aktif</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger-soft px-3 py-2 small fw-medium">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm rounded-3 shadow-sm border overflow-hidden">
                                        <button class="btn btn-white border-0 px-3 btn-detail" 
                                                title="Detail"
                                                data-company="{{ $p->company_name }}"
                                                data-nib="{{ $p->nib }}"
                                                data-npwp="{{ $p->npwp }}"
                                                data-type="{{ $p->business_type }}"
                                                data-address="{{ $p->company_address }}"
                                                data-pic="{{ $p->pic_name }}"
                                                data-position="{{ $p->pic_position }}"
                                                data-email="{{ $p->pic_email }}"
                                                data-phone="{{ $p->pic_phone }}"
                                                data-bank="{{ $p->bank_name }}"
                                                data-account="{{ $p->bank_account }}">
                                            <i class="bi bi-info-circle"></i>
                                        </button>

                                        <a href="{{ route('admin.outsource.edit', $p->outsource_id) }}" 
                                                class="btn btn-white border-0 px-3 pt-2" 
                                                title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.outsource.terminate', $p->outsource_id) }}" 
                                                method="POST" 
                                                onsubmit="return confirm('Apakah Anda yakin ingin {{ $p->status == 1 ? 'menonaktifkan' : 'mengaktifkan' }} vendor ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-white border-0 px-3 text-danger" title="Ubah Status Kemitraan">
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center p-5 text-muted">
                                    <i class="bi bi-building-x fs-2 d-block mb-2"></i> Belum ada mitra vendor outsource yang terdaftar dalam sistem.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>


        </div>
    </div>

    <div class="modal fade" id="modalDetail" data-bs-backdrop="static" p-4 tabIndex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-building me-2 text-primary"></i> Detail Informasi Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small">Nama Perusahaan</small>
                            <p class="fw-bold text-dark id-company">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small">Alamat Kantor</small>
                            <p class="text-dark id-address">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small">Nomor NIB</small>
                            <p class="text-dark id-nib">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small">Nomor NPWP</small>
                            <p class="text-dark id-npwp">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small">Bidang Jasa</small>
                            <p class="text-dark id-type">-</p>
                        </div>
                        <hr class="my-2">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small text-warning">Nama PIC</small>
                            <p class="fw-medium text-dark id-pic">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small text-warning">Jabatan PIC</small>
                            <p class="text-dark id-position">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small text-warning">Email Bisnis</small>
                            <p class="text-dark id-email">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small text-warning">No. WhatsApp</small>
                            <p class="text-dark id-phone">-</p>
                        </div>
                        <hr class="my-2">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small text-success">Nama Bank Keuangan</small>
                            <p class="text-dark id-bank">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small text-success">Nomor Rekening Corporate</small>
                            <p class="fw-bold text-dark id-account">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('custom_js/admin/outsource.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#tableOutsource').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari vendor...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    paginate: {
                        previous: "<i class='bi bi-chevron-left'></i>",
                        next: "<i class='bi bi-chevron-right'></i>"
                    }
                },
                columnDefs: [
                    { orderable: false, targets: [4] } // Matikan sorting kolom aksi
                ]
            });
        });
    </script>
@endsection