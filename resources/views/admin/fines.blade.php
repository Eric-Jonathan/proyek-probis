@extends('layout.layout')

@section('custom_css')
<style>
    body { background-color: #f8f9fa; }
    .stat-card {
        border-radius: 12px;
        border: none;
        transition: transform 0.2s;
        background: #ffffff;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-value { font-size: 2rem; font-weight: 800; color: #334155; }
    .stat-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .table-container { border-radius: 15px; background: #ffffff; }
    table.dataTable thead th {
        background-color: #fcfcfd;
        border-bottom: 1px solid #f1f5f9 !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        padding: 1.25rem 1rem !important;
    }
    .table tbody td { padding: 1.25rem 1rem; vertical-align: middle; font-size: 0.9rem; }
    
    .bg-pending { background-color: #fef9c3; color: #a16207; }
    .bg-approved { background-color: #dcfce7; color: #15803d; }
    .bg-rejected { background-color: #fee2e2; color: #991b1b; }
    
    .proof-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: 0.2s;
    }
    .proof-thumbnail:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Manajemen Laporan Denda</h2>
        <p class="text-secondary">Tinjau, setujui, atau tolak laporan pelanggaran dan denda dari penyedia properti</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Statistik Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">TOTAL LAPORAN</div>
                <div class="stat-value">{{ $fines->count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">PENDING REVIEW</div>
                <div class="stat-value text-warning">{{ $fines->where('status', 0)->count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">DISETUJUI</div>
                <div class="stat-value text-success">{{ $fines->where('status', 1)->count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">DITOLAK</div>
                <div class="stat-value text-danger">{{ $fines->where('status', 2)->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card table-container border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle" id="tableFines" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 10%;">ID Booking</th>
                            <th style="width: 25%;">Ruangan</th>
                            <th style="width: 20%;">Pelanggar (Renter)</th>
                            <th style="width: 15%;">Jenis Denda</th>
                            <th style="width: 15%;">Nominal</th>
                            <th class="text-center" style="width: 10%;">Status</th>
                            <th class="text-center" style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fines as $f)
                        <tr>
                            <td class="fw-bold">#{{ $f->booking_id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $f->booking->roomDetail->item_name ?? 'Ruangan' }}</div>
                                <small class="text-muted"><i class="bi bi-person"></i> Penyedia: {{ $f->booking->roomDetail->room->user->username ?? 'Provider' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $f->booking->user->username ?? 'Guest' }}</div>
                                <small class="text-muted"><i class="bi bi-whatsapp"></i> +62{{ $f->booking->phone }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light border text-capitalize text-secondary px-2.5 py-1.5 small">
                                    {{ $f->jenis_denda }}
                                </span>
                            </td>
                            <td class="fw-bold text-dark">Rp {{ number_format($f->nominal_denda, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($f->status == 0)
                                    <span class="badge rounded-pill bg-pending px-3 py-2">Pending</span>
                                @elseif($f->status == 1)
                                    <span class="badge rounded-pill bg-approved px-3 py-2">Approved</span>
                                @elseif($f->status == 2)
                                    <span class="badge rounded-pill bg-rejected px-3 py-2">Rejected</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $f->fine_id }}">
                                        Tinjau
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODALS DETAIL & REVIEW --}}
@foreach($fines as $f)
<div class="modal fade" id="modalDetail{{ $f->fine_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h4 class="fw-bold mb-0">Tinjau Laporan Denda</h4>
                    <p class="text-secondary small mb-0">Booking #{{ $f->booking_id }} | Dilaporkan pada {{ date('d M Y H:i', strtotime($f->created_at)) }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4">
                    {{-- Ruangan & Penyewa --}}
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Informasi Booking</label>
                        <div class="p-3 rounded bg-light mt-1">
                            <h5 class="fw-bold mb-1 text-dark">{{ $f->booking->roomDetail->item_name ?? 'Ruangan' }}</h5>
                            <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $f->booking->roomDetail->room->location ?? 'Lokasi tidak tersedia' }}</p>
                            <p class="text-muted small mb-0"><i class="bi bi-person-circle me-1"></i> Penyedia: <strong>{{ $f->booking->roomDetail->room->user->username ?? 'Provider' }}</strong></p>
                        </div>
                    </div>

                    {{-- Pelanggar / Renter --}}
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Penyewa (Terdenda)</label>
                        <div class="p-3 rounded bg-light mt-1">
                            <h5 class="fw-bold mb-1 text-danger">{{ $f->booking->user->username ?? 'Renter' }}</h5>
                            <p class="text-muted small mb-2"><i class="bi bi-whatsapp me-1"></i> +62{{ $f->booking->phone }}</p>
                            <p class="text-muted small mb-0"><i class="bi bi-envelope me-1"></i> {{ $f->booking->user->email ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Jenis & Nominal --}}
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Detail Denda</label>
                        <div class="p-3 rounded bg-light mt-1">
                            <div class="mb-2">
                                <span class="small text-muted d-block">Jenis Pelanggaran:</span>
                                <span class="fw-bold text-dark text-capitalize">{{ $f->jenis_denda }}</span>
                            </div>
                            <div>
                                <span class="small text-muted d-block">Nominal Denda:</span>
                                <span class="fw-bold text-danger fs-5">Rp {{ number_format($f->nominal_denda, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Status Review --}}
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Status Saat Ini</label>
                        <div class="p-3 rounded bg-light mt-1">
                            <div class="mb-2">
                                <span class="small text-muted d-block">Status Persetujuan:</span>
                                @if($f->status == 0)
                                    <span class="badge bg-warning text-dark px-2.5 py-1.5">Menunggu Review Admin</span>
                                @elseif($f->status == 1)
                                    <span class="badge bg-success text-white px-2.5 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Disetujui</span>
                                @elseif($f->status == 2)
                                    <span class="badge bg-danger text-white px-2.5 py-1.5"><i class="bi bi-x-circle-fill me-1"></i> Ditolak</span>
                                @endif
                            </div>
                            <div>
                                <span class="small text-muted d-block">Notifikasi Renter:</span>
                                @if($f->status == 1)
                                    <span class="text-{{ $f->is_dismissed ? 'success' : 'warning' }} fw-bold small">
                                        <i class="bi {{ $f->is_dismissed ? 'bi-check2-all' : 'bi-clock' }} me-1"></i>
                                        {{ $f->is_dismissed ? 'Sudah Dibaca & Di-dismiss' : 'Belum Dibaca (Warning aktif)' }}
                                    </span>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Kronologi --}}
                    <div class="col-12">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Kronologi Pelanggaran</label>
                        <div class="p-3 rounded border bg-white mt-1 small" style="white-space: pre-line;">
                            {{ $f->keterangan }}
                        </div>
                    </div>

                    {{-- Bukti Foto --}}
                    <div class="col-12">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Bukti Foto</label>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            @if(is_array($f->bukti_denda) && count($f->bukti_denda) > 0)
                                @foreach($f->bukti_denda as $img)
                                    <a href="{{ asset($img) }}" target="_blank">
                                        <img src="{{ asset($img) }}" class="proof-thumbnail shadow-sm" alt="Bukti Foto">
                                    </a>
                                @endforeach
                            @else
                                <span class="text-muted small italic">Tidak ada foto bukti terlampir.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                @if($f->status == 0)
                    <div class="d-flex w-100 gap-2">
                        <form action="{{ route('admin.fines.approve', $f->fine_id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Apakah Anda yakin menyetujui pengajuan denda ini?')">
                                <i class="bi bi-check-lg me-1"></i> Setujui & Kirim Warning
                            </button>
                        </form>
                        <form action="{{ route('admin.fines.reject', $f->fine_id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 py-2.5 rounded-pill fw-bold shadow-sm" onclick="return confirm('Apakah Anda yakin menolak pengajuan denda ini?')">
                                <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                @else
                    <button type="button" class="btn btn-secondary w-100 py-2.5 rounded-pill fw-bold" data-bs-dismiss="modal">
                        Tutup Review
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('custom_js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tableFines').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari laporan denda...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    previous: "<i class='bi bi-chevron-left'></i>",
                    next: "<i class='bi bi-chevron-right'></i>"
                }
            },
            columnDefs: [
                { orderable: false, targets: [6] }
            ]
        });
    });
</script>
@endsection
