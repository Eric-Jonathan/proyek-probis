@extends('layout.layout')

@section('custom_css')
    <style>
        /* Base Styling */
        body { background-color: #f8f9fa; color: #334155; font-family: 'Inter', sans-serif; }
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
        <h2 class="fw-bold mb-1">Verifikasi Pengajuan Unit</h2>
        <p class="text-muted mb-0">Pantau hasil survei lapangan dan tentukan kelayakan unit.</p>
    </div>

    @php
        // Data Dummy untuk Statistik & Tabel
        $allRooms = collect([
            (object)['id' => 101, 'name' => 'Cozy Meeting Room', 'floor' => 'Lantai 1', 'price' => '500.000', 'status' => 'pending', 'outsource' => 'Budi Santoso', 'rek' => 'Layak'],
            (object)['id' => 102, 'name' => 'Grand Ballroom Kencana', 'floor' => 'Lantai 3', 'price' => '5.500.000', 'status' => 'pending', 'outsource' => 'Siti Aminah', 'rek' => 'Layak'],
            (object)['id' => 103, 'name' => 'Diponegoro Suite', 'floor' => 'Lantai 2', 'price' => '750.000', 'status' => 'verified', 'outsource' => 'Budi Santoso', 'rek' => 'Layak'],
            (object)['id' => 104, 'name' => 'Studio Foto Malang', 'floor' => 'Lantai 1', 'price' => '300.000', 'status' => 'rejected', 'outsource' => 'Siti Aminah', 'rek' => 'Tidak Layak'],
        ]);

        $stats = [
            ['label' => 'Total Pengajuan', 'val' => $allRooms->count(), 'color' => 'primary', 'icon' => 'bi-list-check'],
            ['label' => 'Menunggu di Setujui', 'val' => $allRooms->where('status', 'pending')->count(), 'color' => 'warning', 'icon' => 'bi-clock-history'],
            ['label' => 'Disetujui', 'val' => $allRooms->where('status', 'verified')->count(), 'color' => 'success', 'icon' => 'bi-check-all'],
            ['label' => 'Ditolak', 'val' => $allRooms->where('status', 'rejected')->count(), 'color' => 'danger', 'icon' => 'bi-x-circle'],
        ];

        $pendingRooms = $allRooms->where('status', 'pending');
        $processedRooms = $allRooms->whereIn('status', ['verified', 'rejected']);
    @endphp

    <div class="row g-3 mb-5">
        @foreach($stats as $s)
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-{{ $s['color'] }} bg-opacity-10 text-{{ $s['color'] }} rounded-3 p-3 me-3">
                        <i class="bi {{ $s['icon'] }} fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 small text-uppercase">{{ $s['label'] }}</small>
                        <h4 class="fw-bold mb-0">{{ $s['val'] }}</h4>
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
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-secondary text-center">
                    <tr>
                        <th class="ps-4 text-start">Informasi Unit</th>
                        <th>Surveyor</th>
                        <th>Rekomendasi Mitra</th>
                        <th>Status</th>
                        <th class="pe-4">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRooms as $room)
                    <tr>
                        <td class="ps-4 py-4">
                            <div class="fw-bold text-dark">{{ $room->name }}</div>
                            <small class="text-muted">{{ $room->floor }}</small>
                        </td>
                        <td class="text-center small">{{ $room->outsource }}</td>
                        <td class="text-center">
                            <span class="badge {{ $room->rek == 'Layak' ? 'bg-success-soft' : 'bg-danger-soft' }} rounded-pill px-3 py-2 fw-medium">
                                {{ $room->rek }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-warning-soft px-3 py-2 small fw-medium">Menunggu Disetujui</span>
                        </td>
                        <td class="text-center pe-4">
                            <a href="{{ route('outsource.history.detail', $room->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-shield-check me-2"></i> Periksa
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-divider mt-5">
        <h5 class="fw-bold mb-1">Histori Keputusan</h5>
        <p class="small text-muted mb-0">Daftar pengajuan yang sudah Anda Terima atau Tolak.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th class="ps-4 text-start">Informasi Unit</th>
                        <th>Tarif / Hari</th>
                        <th>Keputusan Akhir</th>
                        <th class="pe-4">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($processedRooms as $room)
                    <tr>
                        <td class="ps-4 py-4">
                            <div class="fw-bold text-dark">{{ $room->name }}</div>
                            <small class="text-muted">{{ $room->floor }}</small>
                        </td>
                        <td class="text-center fw-bold">Rp {{ $room->price }}</td>
                        <td class="text-center">
                            @if($room->status == 'verified')
                                <span class="badge rounded-pill bg-success-soft px-3 py-2 fw-medium">Disetujui</span>
                            @else
                                <span class="badge rounded-pill bg-danger-soft px-3 py-2 fw-medium">Ditolak</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <a href="{{ route('outsource.history.detail', $room->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-medium border shadow-sm">
                                <i class="bi bi-eye me-2"></i> Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection