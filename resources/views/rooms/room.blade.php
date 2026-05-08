@extends('layout.layout')

@section('content')
<div class="content-wrapper p-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Kelola Ruangan</h2>
            <p class="text-muted">Manajemen inventaris dan status ketersediaan unit</p>
        </div>
            <a href="{{ route('rooms.create') }}" class="btn btn-primary px-4 rounded-pill">
                <i class="bi bi-plus-circle me-2"></i>Tambah Ruangan Baru
            </a>    
        </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <span class="text-muted small fw-bold">Total Ruangan</span>
                <h3 class="fw-bold text-primary mb-0">{{ $totalRooms }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <span class="text-muted small fw-bold">Unit Aktif</span>
                <h3 class="fw-bold text-success mb-0">{{ $activeRooms }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <span class="text-muted small fw-bold">Nonaktif</span>
                <h3 class="fw-bold text-danger mb-0">{{ $inactiveRooms }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <span class="text-muted small fw-bold">Perawatan</span>
                <h3 class="fw-bold text-warning mb-0">{{ $maintenanceRooms }}</h3>
            </div>
        </div>
    </div>

    {{-- Main List --}}
    <div class="card border-0 shadow-sm p-4">
        <form method="GET" action="{{ route('rooms.index') }}" class="row g-2 mb-4">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau lokasi..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Perawatan</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
                <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary px-3">Clear</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Informasi Ruangan</th>
                        <th>Lokasi</th>
                        <th>Kapasitas</th>
                        <th>Tarif Sewa</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $i => $room)
                    <tr>
                        <td>{{ $rooms->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $room->name }}</div>
                            <small class="text-muted">{{ Str::limit($room->description, 40) }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $room->location }}</span></td>
                        <td>{{ $room->capacity }} <small>pax</small></td>
                        <td class="fw-bold text-primary">Rp{{ number_format($room->price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @php
                                $map = [1 => ['Aktif', 'success'], 2 => ['Nonaktif', 'danger'], 3 => ['Perawatan', 'warning']];
                                $badge = $map[$room->status] ?? ['Unknown', 'secondary'];
                            @endphp
                            <span class="badge rounded-pill bg-{{ $badge[1] }}-subtle text-{{ $badge[1] }} px-3 py-2">
                                {{ $badge[0] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-light text-primary rounded-circle shadow-sm"><i class="bi bi-eye"></i></button>
                                <a href="{{ route('rooms.edit', $room->room_id) }}" 
                                   class="btn btn-sm btn-light text-warning rounded-circle shadow-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-light text-danger rounded-circle shadow-sm"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center">
            <p class="text-muted small mb-0">Menampilkan <b>{{ $rooms->firstItem() }}</b> ke <b>{{ $rooms->lastItem() }}</b> dari <b>{{ $rooms->total() }}</b> unit</p>
            {{ $rooms->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection