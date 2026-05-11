@extends('layout.layout')

@section('custom_css')
    <style>
        /* Base Styling */
        body { background-color: #f8f9fa; color: #334155; font-family: 'Inter', sans-serif; }
        .rounded-4 { border-radius: 1rem !important; }
        
        /* Stats Card Interactivity */
        .stat-card { 
            border: none; 
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        }

        /* Table Aesthetics */
        .table thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background-color: #fcfcfd;
            border-bottom: 1px solid #f1f5f9;
        }
        .table tbody tr:hover { background-color: #f8fafc !important; }

        /* Progress & Avatar */
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

        /* Form Styling */
        .form-assign {
            font-size: 0.85rem;
            border-radius: 50px;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
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
        @php
            $stats = [
                ['label' => 'Belum di Assign', 'val' => 5, 'color' => 'danger', 'icon' => 'bi-door-open'],
                ['label' => 'Sudah di Assign', 'val' => 8, 'color' => 'warning', 'icon' => 'bi-clock-history'],
                ['label' => 'Surveyor Aktif', 'val' => 3, 'color' => 'primary', 'icon' => 'bi-person-badge'],
                ['label' => 'Target Tercapai', 'val' => '92%', 'color' => 'success', 'icon' => 'bi-graph-up-arrow'],
            ];
        @endphp

        @foreach($stats as $s)
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-{{ $s['color'] }} bg-opacity-10 text-{{ $s['color'] }} rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi {{ $s['icon'] }} fs-4"></i>
                    </div>
                    
                    <div>
                        <small class="text-muted fw-medium d-block mb-1 text-uppercase small" style="font-size: 0.65rem;">
                            {{ $s['label'] }}
                        </small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $s['val'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="section-divider">
        <h5 class="fw-bold mb-1">Penugasan Baru</h5>
        <p class="small text-muted mb-0">Daftar tempat yang diajukan penyedia dan menunggu surveyor.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-secondary">
                    <tr>
                        <th class="ps-4 py-3">Unit / Properti</th>
                        <th>Wilayah</th>
                        <th class="text-center">Assign Surveyor</th>
                        <th class="pe-4 text-end">Konfirmasi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $incoming = [
                            (object)['id' => 201, 'name' => 'Grand Ballroom Kencana', 'owner' => 'Hotel Kencana', 'city' => 'Batu'],
                            (object)['id' => 202, 'name' => 'Studio Foto Malang', 'owner' => 'Bpk. Hendra', 'city' => 'Malang'],
                        ];
                        $mitra = ['Budi Santoso', 'Siti Aminah', 'Andi Wijaya'];
                    @endphp

                    @foreach($incoming as $item)
                    <tr>
                        <td class="ps-4 py-4">
                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                            <small class="text-muted">Penyedia: {{ $item->owner }}</small>
                        </td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-medium">{{ $item->city }}</span></td>
                        <td>
                            <select class="form-select form-assign shadow-none mx-auto" style="max-width: 250px;">
                                <option selected disabled>Pilih Surveyor...</option>
                                @foreach($mitra as $m)
                                    <option>{{ $m }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                Tugaskan <i class="bi bi-send-fill ms-1"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-divider mt-5">
        <h5 class="fw-bold mb-1 text-warning">Monitor Progres Lapangan</h5>
        <p class="small text-muted mb-0">Memantau progres pengerjaan surveyor di lapangan.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-secondary">
                    <tr>
                        <th class="ps-4 py-3">Unit Sedang Dicek</th>
                        <th>Surveyor Aktif</th>
                        <th width="220">Status & Progres</th>
                        <th class="pe-4 text-end">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $monitoring = [
                            ['unit' => 'Ballroom Graha Cendana', 'surveyor' => 'Andi Wijaya', 'progress' => 45, 'status' => 'Pengisian Data'],
                            ['unit' => 'Meeting Room - Hub', 'surveyor' => 'Siti Aminah', 'progress' => 15, 'status' => 'Menuju Lokasi'],
                        ];
                    @endphp

                    @foreach($monitoring as $m)
                    <tr>
                        <td class="ps-4 py-4">
                            <div class="fw-bold text-dark">{{ $m['unit'] }}</div>
                            <small class="text-muted small">ID Tugas: #JOB-{{ rand(100, 999) }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-2">{{ substr($m['surveyor'], 0, 1) }}</div>
                                <span class="small fw-medium">{{ $m['surveyor'] }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="small fw-bold text-warning" style="font-size: 0.65rem;">{{ $m['status'] }}</span>
                                <span class="small text-muted" style="font-size: 0.65rem;">{{ $m['progress'] }}%</span>
                            </div>
                            <div class="progress progress-thin bg-light">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $m['progress'] }}%"></div>
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle border shadow-sm" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                    <li><a class="dropdown-item small" href="#"><i class="bi bi-telephone me-2 text-primary"></i> Hubungi Surveyor</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item small text-danger" href="#"><i class="bi bi-x-circle me-2"></i> Batalkan Tugas</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection