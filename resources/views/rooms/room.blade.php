@extends('layout.layout')

@section('content')
<div class="content-wrapper">
    {{-- Page Header --}}
    <div class="page-header">
        <div class="header-text">
            <h1>Kelola Ruangan</h1>
            <p>Manajemen inventaris dan status ketersediaan unit</p>
        </div>
        <div class="header-action">
            <a href="{{ route('rooms.create') }}" class="btn btn-add-room">
                <i class="fas fa-plus-circle"></i> Tambah Ruangan Baru
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="row g-3 mb-3">
        {{-- Card Total --}}
        <div class="col-md-3">
            <div class="card p-3 h-100 total shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="stat-info">
                        <span class="label d-block text-muted small fw-bold">Total Ruangan</span>
                        <span class="value h3 fw-bold mb-0 text-primary">{{ $totalRooms }}</span>
                    </div>
                    <div class="stat-icon fs-2 opacity-50"><i class="fas fa-door-open"></i></div>
                </div>
            </div>
        </div>

        {{-- Card Aktif --}}
        <div class="col-md-3">
            <div class="card p-3 h-100 active shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="stat-info">
                        <span class="label d-block text-muted small fw-bold">Unit Aktif</span>
                        <span class="value h3 fw-bold mb-0 text-success">{{ $activeRooms }}</span>
                    </div>
                    <div class="stat-icon fs-2 opacity-50"><i class="fas fa-check-double"></i></div>
                </div>
            </div>
        </div>

        {{-- Card Maintenance --}}
        <div class="col-md-3">
            <div class="card p-3 h-100 maintenance shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="stat-info">
                        <span class="label d-block text-muted small fw-bold">Perawatan</span>
                        <span class="value h3 fw-bold mb-0 text-warning">{{ $maintenanceRooms }}</span>
                    </div>
                    <div class="stat-icon fs-2 opacity-50"><i class="fas fa-tools"></i></div>
                </div>
            </div>
        </div>

        {{-- Card Inaktif --}}
        <div class="col-md-3">
            <div class="card p-3 h-100 inactive shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="stat-info">
                        <span class="label d-block text-muted small fw-bold">Nonaktif</span>
                        <span class="value h3 fw-bold mb-0 text-danger">{{ $inactiveRooms }}</span>
                    </div>
                    <div class="stat-icon fs-2 opacity-50"><i class="fas fa-power-off"></i></div>
                </div>
            </div>
        </div>

    </div>

    {{-- Main Table Card --}}
    <div class="main-card">
        <div class="card-header">
            <div class="title-section">
                <h3><i class="fas fa-th-list"></i> Daftar Ruangan</h3>
                <span class="badge-count">{{ $rooms->total() }} Unit</span>
            </div>
            
            <form method="GET" action="" class="filter-wrapper row w-100">
                <div class="search-box col-5">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama atau lantai...">
                </div>
                <div class="col-4">
                    <select name="status" class="custom-select form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status')=='active' ? 'selected':'' }}>Aktif</option>
                        <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>Nonaktif</option>
                        <option value="maintenance" {{ request('status')=='maintenance' ? 'selected':'' }}>Perawatan</option>
                    </select>
                </div>
                <div class="col-3 text-end">
                    <button type="submit" class="btn btn-primary btn-filter me-3"><i class="bi bi-search"></i> Search</button>
                    <a href="" class="btn btn-outline-secondary btn-reset" title="Reset"><i class="bi bi-backspace"></i> Clear</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Informasi Ruangan</th>
                        <th>Lokasi</th>
                        <th>Kapasitas</th>
                        <th>Tarif Sewa</th>
                        <th>Status</th>
                        <th class="text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rooms as $i => $room)
                <tr>
                    <td class="text-muted">{{ $rooms->firstItem() + $i }}</td>

                    {{-- INFO ROOM --}}
                    <td>
                        <div class="room-profile">
                            <div class="room-img-box shadow-sm">
                                <i class="fas fa-building text-secondary"></i>
                            </div>
                            <div class="room-details">
                                <span class="room-name">{{ $room->name }}</span>
                                <span class="room-desc">{{ \Illuminate\Support\Str::limit($room->description, 35) }}</span>
                            </div>
                        </div>
                    </td>

                    {{-- LOKASI --}}
                    <td><span class="floor-tag">{{ $room->location }}</span></td>

                    {{-- KAPASITAS --}}
                    <td>
                        <div class="capacity-info">
                            <i class="fas fa-users"></i> {{ $room->capacity }} <small>pax</small>
                        </div>
                    </td>

                    {{-- HARGA --}}
                    <td>
                        <div class="price-tag">
                            <small>Rp</small>{{ number_format($room->price, 0, ',', '.') }}
                        </div>
                    </td>

                    {{-- STATUS (INT → LABEL) --}}
                    <td>
                        @php
                            $statusClass = [
                                1 => 'st-active',
                                2 => 'st-maintenance',
                                0 => 'st-inactive'
                            ][$room->status] ?? 'st-inactive';

                            $statusLabel = [
                                1 => 'Aktif',
                                2 => 'Maintenance',
                                0 => 'Nonaktif'
                            ][$room->status] ?? 'Unknown';
                        @endphp

                        <span class="status-indicator {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>

                    {{-- ACTION --}}
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="act-btn view">
                                <i class="bi bi-eye"></i>
                            </a>

                            <a href="{{ route('rooms.edit', $room->room_id) }}" class="act-btn edit">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{ route('rooms.destroy', $room->room_id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button class="act-btn delete"
                                    onclick="return confirm('Yakin mau hapus {{ $room->name }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-state text-center">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80">
                        <p>Data ruangan tidak ditemukan</p>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Enhanced Pagination --}}
        @if($rooms->hasPages())
        <div class="pagination">
            <p>Showing <b>{{ $rooms->firstItem() }}</b> to <b>{{ $rooms->lastItem() }}</b> of <b>{{ $rooms->total() }}</b> entries</p>
            <div class="page-nav">
                {{ $rooms->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection