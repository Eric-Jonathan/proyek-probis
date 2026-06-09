@extends('layout.layout')

@section('content')
<div class="container-fluid p-4 pt-3">
    {{-- Header: Menggunakan flex-wrap agar tidak tembus saat layar kecil/zoom --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Kelola Ruangan</h2>
            <p class="text-muted mb-0">Manajemen inventaris dan status ketersediaan unit</p>
        </div>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary px-4 rounded-pill shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Tambah Ruangan Baru
        </a>
    </div>

    {{-- Stats Cards: col-6 pada HP, col-md-3 pada Desktop --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center h-100">
                <span class="text-muted small fw-bold text-uppercase">Total Ruangan</span>
                <h3 class="fw-bold text-primary mb-0 mt-1">{{ $totalRooms }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center h-100">
                <span class="text-muted small fw-bold text-uppercase">Unit Aktif</span>
                <h3 class="fw-bold text-success mb-0 mt-1">{{ $activeRooms }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center h-100">
                <span class="text-muted small fw-bold text-uppercase">Nonaktif</span>
                <h3 class="fw-bold text-danger mb-0 mt-1">{{ $inactiveRooms }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center h-100">
                <span class="text-muted small fw-bold text-uppercase">Diajukan</span>
                <h3 class="fw-bold text-warning mb-0 mt-1">{{ $diajukan }}</h3>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="tableRoom" style="width: 100%;">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th class="ps-4" width="50">#</th>
                        <th style="min-width: 250px;">Informasi Ruangan</th>
                        <th style="min-width: 200px;">Lokasi</th>
                        <th>Kapasitas</th>
                        <th>Tarif Sewa</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $i => $room)
                    <tr>
                        <td class="ps-4 text-muted">{{ $rooms->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $room->name }}</div>
                            <small class="text-muted">{{ Str::limit($room->description, 45) }}</small>
                        </td>
                        <td class="pe-3">
                            {{-- Mengambil 2 data depan koma --}}
                            <span class="badge bg-light text-dark border fw-normal" style="white-space: normal; text-align: left;">
                                {{ implode(', ', array_slice(explode(',', $room->location), 0, 1)) }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <i class="bi bi-people me-1"></i> {{ $room->capacity }} Orang
                        </td>
                        <td class="text-nowrap fw-bold text-primary">
                            Rp {{ number_format($room->price, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @php
                                $statusMap = [
                                    1 => ['Diajukan', 'warning'],
                                    2 => ['Aktif', 'success'],
                                    3 => ['Maintenance', 'secondary'],
                                    0 => ['Nonaktif', 'danger']
                                ];
                                $badge = $statusMap[$room->status] ?? ['Unknown', 'dark'];
                            @endphp
                            <span class="badge rounded-pill bg-{{ $badge[1] }}-subtle text-{{ $badge[1] }} px-3 py-2">
                                {{ $badge[0] }}
                            </span>
                        </td>
                        <td class="pe-4">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{route('rooms.show', $room->room_id)}}" 
                                    class="btn btn-sm btn-light text-primary rounded-circle shadow-sm" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('rooms.edit', $room->room_id) }}" 
                                   class="btn btn-sm btn-light text-warning rounded-circle shadow-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('rooms.destroy', $room->room_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruangan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center p-5 text-muted">
                            <i class="bi bi-inboxes fs-2 d-block mb-2"></i> Belum ada data ruangan terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Tabel --}}
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <p class="text-muted small mb-0">
                    Menampilkan <b>{{ $rooms->firstItem() }}</b> ke <b>{{ $rooms->lastItem() }}</b> dari <b>{{ $rooms->total() }}</b> unit
                </p>
                <div class="pagination-responsive">
                    {{ $rooms->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
    <script src="{{ asset('custom_js/rooms/room.js') }}"></script>
@endsection