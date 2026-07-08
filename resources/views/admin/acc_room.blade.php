@extends('layout.layout')

@section('custom_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        /* Base Styling */
        body { background-color: var(--bs-tertiary-bg); color: var(--bs-body-color); font-family: 'Inter', sans-serif; }
        .rounded-4 { border-radius: 1rem !important; }
        
        /* Subtle Badge Colors */
        .bg-success-soft { background-color: #dcfce7 !important; color: #15803d !important; }
        .bg-warning-soft { background-color: #fffbeb !important; color: #b45309 !important; }
        .bg-danger-soft { background-color: #fee2e2 !important; color: #b91c1c !important; }
        .bg-primary-soft { background-color: #e0e7ff !important; color: #4338ca !important; }

        /* Card Animation */
        .stat-card { transition: transform 0.2s ease; border: none; }
        .stat-card:hover { transform: translateY(-5px); }

        /* Table Styling */
        .table thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: #fcfcfd;
            border-bottom: 1px solid #f1f5f9;
        }
        .table tbody tr:hover { background-color: #f8fafc !important; }

        .section-divider {
            border-left: 4px solid #0064D2;
            padding-left: 15px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper py-3 px-4">
    
    <div class="mb-4">
        <h2 class="fw-bold mb-1 text-dark">Verifikasi Pengajuan Unit</h2>
        <p class="text-secondary mb-0">Pantau hasil survei lapangan dan tentukan kelayakan unit secara akurat.</p>
    </div>


    <div class="row g-3 mb-5">
        @foreach($stats as $s)
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-{{ $s['color'] }} bg-opacity-10 text-{{ $s['color'] }} rounded-3 p-3 me-3">
                        <i class="bi {{ $s['icon'] }} fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 small text-uppercase" style="font-size: 0.65rem;">{{ $s['label'] }}</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $s['val'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="section-divider">
        <h5 class="fw-bold mb-1">Menunggu Keputusan Admin</h5>
        <p class="small text-muted mb-0">Laporan survei outsource yang harus segera Anda tinjau.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0 text-center" id="tablePending" style="width: 100%;">
                <thead class="text-secondary text-uppercase" style="font-size: 0.7rem;">
                    <tr>
                        <th class="ps-4 text-start">Informasi Unit</th>
                        <th>Surveyor</th>
                        <th>Rekomendasi Mitra</th>
                        <th>Status Review</th>
                        <th class="pe-4Action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRooms as $room)
                    <tr>
                        <td class="ps-4 py-4 text-start">
                            <div class="fw-bold text-dark">{{ $room->room }}</div>
                            <small class="text-muted">{{ $room->floor }}</small>
                        </td>
                        <td class="small">{{ $room->outsource }}</td>
                        <td>
                            <span class="badge {{ $room->rek == 'Layak' ? 'bg-success-soft' : 'bg-danger-soft' }} rounded-pill px-3 py-2 fw-medium">
                                {{ $room->rek }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-warning-soft px-3 py-2 small fw-medium">Pending Admin</span>
                        </td>
                        <td class="pe-4 text-center">
                            <a href="{{ route('outsource.history.detail', $room->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-shield-check me-2"></i> Periksa
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-4 text-muted small">Tidak ada laporan yang menunggu persetujuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-divider mt-5">
        <h5 class="fw-bold mb-1">Histori Keputusan Laporan</h5>
        <p class="small text-muted mb-0">Arsip pengajuan yang sudah diproses oleh manajemen.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0 text-center" id="tableProcessed" style="width: 100%;">
                <thead class="table-light text-secondary text-uppercase" style="font-size: 0.7rem;">
                    <tr>
                        <th class="ps-4 text-start">Informasi Unit</th>
                        <th>Tarif Per Hari</th>
                        <th>Keputusan Akhir</th>
                        <th class="pe-4">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($processedRooms as $room)
                    <tr>
                        <td class="ps-4 py-4 text-start">
                            <div class="fw-bold text-dark">{{ $room->room }}</div>
                            <small class="text-muted">{{ $room->floor }}</small>
                        </td>
                        <td class="fw-bold text-dark">Rp {{ $room->price }}</td>
                        <td>
                            @if($room->status == 'Diterima')
                                <span class="badge rounded-pill bg-success-soft px-3 py-2 fw-medium">Unit Disetujui</span>
                            @else
                                <span class="badge rounded-pill bg-danger-soft px-3 py-2 fw-medium">Unit Ditolak</span>
                            @endif
                        </td>
                        <td class="pe-4">
                            <a href="{{ route('outsource.history.detail', $room->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-medium border shadow-sm text-decoration-none">
                                <i class="bi bi-eye me-2"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 text-muted small">Belum ada histori pengajuan.</td></tr>
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
            $('#tablePending').DataTable({
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
                    { orderable: false, targets: [4] } // Matikan sorting kolom aksi
                ]
            });
            $('#tableProcessed').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari riwayat...",
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
        });
    </script>
@endsection