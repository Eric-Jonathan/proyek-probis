@extends('layout.layout')

@section('custom_css')
    <style>
        body { background-color: #f8f9fa; color: #334155; font-family: 'Inter', sans-serif; }
        .rounded-4 { border-radius: 1rem !important; }
        .bg-success-soft { background-color: #dcfce7 !important; color: #15803d !important; }
        .bg-danger-soft { background-color: #fee2e2 !important; color: #b91c1c !important; }
        .bg-primary-soft { background-color: #e0e7ff !important; color: #4338ca !important; }
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
        .table thead th { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; background-color: #fcfcfd; border-bottom: 1px solid #f1f5f9; }
        .btn-white { background-color: #fff; border: 1px solid #e2e8f0; color: #64748b; }
        .btn-white:hover { background-color: #f8fafc; color: #1e293b; }
    </style>
@endsection

@section('content')
    <div class="content-wrapper py-3 px-4">
        
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">{{ session('success') }}</div>
        @endif

        <div class="row align-items-center mb-4">
            <div class="col-12 col-lg-6">
                <h2 class="fw-bold mb-1">Manajemen Pengguna</h2>
                <p class="text-muted mb-0">Kelola hak akses dan data profil pengguna sistem</p>
            </div>
            <div class="col-12 col-lg-6 text-md-end mt-3 mt-md-0">
                <a class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium" href="{{ route('admin.formPeople') }}">
                    <i class="bi bi-person-plus-fill me-2"></i> Tambah Pengguna
                </a>
            </div>
        </div>

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
                            <small class="text-muted fw-medium d-block mb-1 small text-uppercase">{{ $stat['label'] }}</small>
                            <h4 class="fw-bold mb-0 text-dark">{{ $stat['val'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-4 px-4">
                <h5 class="fw-bold mb-0">Daftar Pengguna Aktif</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableUser">
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
                        @forelse ($users as $user)
                            <tr>
                                <td class="ps-4 py-4">
                                    <div class="fw-bold text-dark">{{ $user->username }}</div>
                                </td>
                                <td><span class="text-muted small fw-medium">{{ $user->email }}</span></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-primary-soft px-3 py-2 small fw-medium text-uppercase">{{ $user->role }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($user->status == 1)
                                        <span class="badge rounded-pill bg-success-soft px-3 py-2 small fw-medium">Aktif</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger-soft px-3 py-2 small fw-medium">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm rounded-3 shadow-sm border overflow-hidden">
                                        <button class="btn btn-white border-0 px-3 btn-view-user" 
                                                title="View Detail"
                                                data-username="{{ $user->username }}"
                                                data-email="{{ $user->email }}"
                                                data-phone="{{ $user->phone ?? 'Tidak ada' }}"
                                                data-role="{{ $user->role }}"
                                                data-company="{{ $user->partner->company_name ?? 'Anggota Internal' }}">
                                            <i class="bi bi-info-circle"></i>
                                        </button>

                                        <a href="{{ route('admin.formPeople', ['edit' => $user->user_id]) }}" class="btn btn-white border-0 px-3 pt-2" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ url('/admin/users/delete/'.$user->user_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-white border-0 px-3 text-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-5 text-muted">
                                    <i class="bi bi-people fs-2 d-block mb-2"></i> Belum ada data pengguna terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalViewUser" tabIndex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-vcard me-2 text-primary"></i> Profil Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small">Username Akun</small>
                            <p class="fw-bold text-dark mb-0 field-username">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small">Alamat Email</small>
                            <p class="text-dark mb-0 field-email">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small">Nomor Telepon</small>
                            <p class="text-dark mb-0 field-phone">-</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted text-uppercase d-block fw-bold small">Hak Akses Sistem (Role)</small>
                            <span class="badge bg-primary bg-opacity-10 text-primary text-uppercase field-role">-</span>
                        </div>
                        <div class="border-top pt-3">
                            <small class="text-muted text-uppercase d-block fw-bold small text-success">Perusahaan Naungan</small>
                            <p class="fw-medium text-success mb-0 field-company">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
    <script src="{{ asset('custom_js/admin/users.js') }}"></script>
@endsection