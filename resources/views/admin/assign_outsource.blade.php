@extends('layout.layout')

@section('custom_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        body { background-color: var(--bs-tertiary-bg); color: var(--bs-body-color); font-family: 'Inter', sans-serif; }
        .rounded-4 { border-radius: 1rem !important; }
        .stat-card { background-color: var(--bs-card-bg); }
        
        .stat-card { 
            border: none; 
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        }

        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: #fcfcfd;
            border-bottom: 1px solid #f1f5f9;
            color: #64748b;
        }
        .table tbody tr:hover { background-color: #f8fafc !important; }

        .progress-thin { height: 6px; border-radius: 10px; }
        .avatar-circle {
            width: 32px; height: 32px;
            background-color: #6366f1; color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: bold; border-radius: 50%;
        }

        .section-divider {
            border-left: 4px solid #0064D2;
            padding-left: 15px;
            margin-bottom: 20px;
        }

        .form-assign {
            font-size: 0.85rem;
            border-radius: 50px;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
        }

        .empty-table-state {
            padding: 3rem 1.5rem !important;
            text-align: center;
            color: #94a3b8;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper py-3 px-4">
    
    <div class="mb-4 text-center text-md-start">
        <h2 class="fw-bold mb-1 text-dark">Manajemen Penugasan Outsource</h2>
        <p class="text-secondary">Kelola alur penugasan dan pantau progres pengerjaan di lapangan.</p>
    </div>

    <div class="row g-3 mb-5 justify-content-center justify-content-md-start">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi bi-door-open fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 text-uppercase" style="font-size: 0.65rem;">Belum di Assign</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countWaiting ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 text-uppercase" style="font-size: 0.65rem;">Sudah di Assign</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countActive ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 text-uppercase" style="font-size: 0.65rem;">Mitra Outsource</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countSurveyor ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-divider">
        <h5 class="fw-bold mb-1">Penugasan Baru</h5>
        <p class="small text-muted mb-0">Daftar tempat yang diajukan penyedia dan menunggu surveyor.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0" id="tableIncoming" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-4 py-3" width="35%">Unit / Properti</th>
                        <th width="20%">Wilayah</th>
                        <th class="text-center" width="25%">Assign Mitra Outsource</th>
                        <th class="pe-4 text-end" width="20%">Konfirmasi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($incoming) && $incoming->count() > 0)
                        @foreach($incoming as $item)
                            <tr>
                                <td class="ps-4 py-4">
                                    <div class="fw-bold text-dark">{{ $item->name ?? 'N/A' }}</div>
                                    <small class="text-muted">ID Ruangan: #RM-{{ str_pad($item->room_id, 4, '0', STR_PAD_LEFT) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-medium">
                                        {{ Str::limit(implode(', ', array_slice(explode(',', $item->location), 0, 1)), 25) }}
                                    </span>
                                </td>
                                <td>
                                    <select class="form-select form-assign shadow-none select-surveyor mx-auto" 
                                            style="max-width: 250px;" 
                                            data-room-id="{{ $item->room_id }}" 
                                            required>
                                        <option selected disabled value="">Pilih Mitra Outsource...</option>
                                        @foreach($mitra as $m)
                                            <option value="{{ $m->outsource_id }}">{{ $m->company_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="pe-4 text-end">
                                    <button type="button" 
                                            class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm btn-submit-assign" 
                                            data-room-id="{{ $item->room_id }}" 
                                            style="white-space: nowrap;">
                                        Tugaskan <i class="bi bi-send-fill ms-1"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        <form id="hidden-global-assign-form" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="outsource_id" id="hidden-outsource-id">
                        </form>
                    @else
                        <tr>
                            <td colspan="4" class="empty-table-state">
                                <i class="bi bi-inboxes fs-2 d-block mb-2 text-muted"></i>
                                <span class="fw-medium d-block mb-1 text-dark">Belum Ada Pengajuan Baru</span>
                                <small class="text-muted">Seluruh antrean ruangan dari penyedia telah selesai ditugaskan ke tim outsource.</small>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-divider mt-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
            <div>
                <h5 class="fw-bold mb-1 text-warning">Monitor Progres Lapangan</h5>
                <p class="small text-muted mb-0">Memantau progres pengerjaan surveyor di lapangan.</p>
            </div>
            <!-- Legend / Keterangan Progres -->
            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-2 rounded-pill" style="font-size: 0.72rem;">
                    <strong>15%</strong>: Menuju Lokasi (+15%)
                </span>
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-2 rounded-pill" style="font-size: 0.72rem;">
                    <strong>30%</strong>: Pengisian Data (+15%)
                </span>
                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-2 rounded-pill" style="font-size: 0.72rem;">
                    <strong>80%</strong>: Menunggu Verifikasi Admin (+50%)
                </span>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-2 rounded-pill" style="font-size: 0.72rem;">
                    <strong>100%</strong>: Selesai (+20%)
                </span>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0" id="tableMonitoring" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-4 py-3" width="35%">Unit Sedang Dicek</th>
                        <th width="25%">Mitra Outsource</th>
                        <th width="25%">Status & Progress</th>
                        <th class="pe-4 text-end" width="15%">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($monitoring) && $monitoring->count() > 0)
                        @foreach($monitoring as $m)
                        @php
                            $statusLabels = [
                                'on_the_way' => 'Menuju Lokasi',
                                'checking' => 'Pengisian Data'
                            ];
                            $statusColors = [
                                'on_the_way' => 'primary',
                                'checking' => 'warning'
                            ];
                            
                            if ($m->assignment_status === 'completed') {
                                if ($m->progress < 100) {
                                    $label = 'Menunggu Verifikasi Admin';
                                    $currColor = 'info';
                                } else {
                                    $label = 'Selesai';
                                    $currColor = 'success';
                                }
                            } else {
                                $label = $statusLabels[$m->assignment_status] ?? $m->assignment_status;
                                $currColor = $statusColors[$m->assignment_status] ?? 'warning';
                            }
                        @endphp
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="fw-bold text-dark">{{ $m->room->name ?? 'N/A' }}</div>
                                <small class="text-muted small">ID Tugas: #JOB-{{ $m->assignment_id }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">{{ substr($m->company->company_name ?? 'C', 0, 1) }}</div>
                                    <span class="small fw-medium">{{ $m->company->company_name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small fw-bold text-{{ $currColor }}" style="font-size: 0.65rem;">
                                        {{ $label }}
                                    </span>
                                    <span class="small text-muted" style="font-size: 0.65rem;">{{ $m->progress }}%</span>
                                </div>
                                <div class="progress progress-thin bg-light">
                                    <div class="progress-bar bg-{{ $currColor }}" role="progressbar" style="width: {{ $m->progress }}%"></div>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle border shadow-sm" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                        <li><a class="dropdown-item small" href="tel:{{ $m->company->pic_phone ?? '#' }}"><i class="bi bi-telephone me-2 text-primary"></i> Hubungi Outsource</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('outsource.cancel', $m->assignment_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan tugas ini?')">
                                                @csrf
                                                <button type="submit" class="dropdown-item small text-danger"><i class="bi bi-x-circle me-2"></i> Batalkan Tugas</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="empty-table-state">
                                <i class="bi bi-activity fs-2 d-block mb-2 text-muted"></i>
                                <span class="fw-medium d-block mb-1 text-dark">Tidak Ada Aktivitas Lapangan</span>
                                <small class="text-muted">Saat ini tidak ada surveyor outsource yang sedang melakukan pengecekan di lokasi.</small>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('custom_js/admin/assign_outsource.js') }}"></script>
    <script>
        $(document).ready(function() {
            @if(isset($incoming) && count($incoming) > 0)
            $('#tableIncoming').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari pengajuan...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    paginate: {
                        previous: "<i class='bi bi-chevron-left'></i>",
                        next: "<i class='bi bi-chevron-right'></i>"
                    }
                },
                columnDefs: [
                    { orderable: false, targets: [2, 3] } // Matikan sorting kolom assign
                ]
            });
            @endif

            @if(isset($monitoring) && count($monitoring) > 0)
            $('#tableMonitoring').DataTable({
                responsive: true,
                order: [], // Preserve backend sorting by progress ascending
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari progres...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    paginate: {
                        previous: "<i class='bi bi-chevron-left'></i>",
                        next: "<i class='bi bi-chevron-right'></i>"
                    }
                },
                columnDefs: [
                    { orderable: false, targets: [3] } // Matikan sorting kolom aksi
                ]
            });
            @endif
        });
    </script>
@endsection