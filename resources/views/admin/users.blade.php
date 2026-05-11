@extends('layout.layout')

@section('custom_css')
    <style>
        /* Base Styling */
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

        /* Animation & Hover */
        .stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }

        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        }
        
        /* Table Styling */
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

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
        }

        /* Responsive */
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
            <div class="col-12 col-lg-6 header-text">
                <h2 class="fw-bold mb-1">Manajemen Pengguna</h2>
                <p class="text-muted mb-0">Kelola hak akses dan data profil pengguna sistem</p>
            </div>
            <div class="col-12 col-lg-6 text-md-end mt-3 mt-md-0 header-action">
                <a class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium" href="{{ route('admin.formPeople') }}">
                    <i class="bi bi-person-plus-fill me-2"></i> Tambah Pengguna
                </a>
            </div>
        </div>

        <!-- Stats Grid (Responsive) -->
        <div class="row g-3 mb-4">
            @php
                $stats = [
                    ['label' => 'Total Pengguna', 'val' => $totalUser, 'color' => 'primary', 'icon' => 'bi-people'],
                    ['label' => 'Pengguna Aktif', 'val' => $totalActive, 'color' => 'success', 'icon' => 'bi-check-circle'],
                    ['label' => 'Pengguna Nonaktif', 'val' => $totalInactive, 'color' => 'danger', 'icon' => 'bi-person-x'],
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
            
            <!-- Filters & Search -->
            <div class="card-header bg-white border-0 py-4 px-4">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-4">
                        <h5 class="fw-bold mb-0">Daftar Pengguna <span class="badge bg-light text-dark ms-2 fw-normal fs-6 px-3">{{ $totalUser }} User</span></h5>
                    </div>
                    <div class="col-12 col-lg-8">
                        <form method="GET" class="row g-2 justify-content-lg-end">
                            <div class="col-12 col-md-6 col-xl-7">
                                <div class="input-group input-group-search">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted small"></i></span>
                                    <input type="text" class="form-control bg-light border-0 small" placeholder="Cari nama atau email...">
                                </div>
                            </div>
                            <div class="col-6 col-md-3 col-xl-3">
                                <select class="form-select bg-light border-0 small text-muted">
                                    <option value="">Role</option>
                                    <option>Admin</option>
                                    <option>Penyewa</option>
                                    <option>Penyedia</option>
                                    <option>Outsource</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3 col-xl-2">
                                <button type="submit" class="btn btn-dark w-100 fw-medium shadow-sm">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableUser" style="min-width: 900px;">
                    <thead class="text-secondary">
                        <tr>
                            <th class="ps-4 py-3">Pengguna</th>
                            <th class="py-3">Email</th>
                            <th class="py-3 text-center">Role</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="ps-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="fw-bold text-dark">{{ $user->username }}</div>
                                    </div>
                                </td>
                                <td><span class="text-muted small fw-medium">{{ $user->email }}</span></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-primary-soft px-3 py-2 small fw-medium">{{ $user->role }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($user->status == 1)
                                        <span class="badge rounded-pill bg-success-soft px-3 py-2 small fw-medium">Aktif</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger-soft px-3 py-2 small fw-medium">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm rounded-3 shadow-sm border overflow-hidden">
                                        <button class="btn btn-white border-0 px-3" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-white border-0 px-3 text-warning" title="Reset Password"><i class="bi bi-key"></i></button>
                                        <button class="btn btn-white border-0 px-3 text-danger" title="Hapus"><i class="bi bi-trash"></i></button>
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

@section('custom_js')
    <script src="{{asset('custom_js/admin/users.js')}}"></script>
@endsection