@extends('layout.layout')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold m-0">Kelola Pesanan Ruangan</h3>
        <p class="text-secondary small m-0">Manajemen reservasi, pembayaran, dan status okupansi unit</p>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row g-3 mb-4 text-center">
    <div class="col-md">
        <div class="card shadow-sm border-0 p-3">
            <h6 class="text-muted small fw-bold mb-1">Total Pesanan</h6>
            <h2 class="fw-bold text-primary mb-0">{{ $totalOrder }}</h2>
        </div>
    </div>
    <div class="col-md">
        <div class="card shadow-sm border-0 p-3">
            <h6 class="text-muted small fw-bold mb-1 text-warning">Belum Bayar</h6>
            <h2 class="fw-bold text-warning mb-0">{{ $unpaidOrder }}</h2>
        </div>
    </div>
    <div class="col-md">
        <div class="card shadow-sm border-0 p-3">
            <h6 class="text-muted small fw-bold mb-1 text-primary">Booked (Menunggu)</h6>
            <h2 class="fw-bold text-primary mb-0">{{ $pendingOrder }}</h2>
        </div>
    </div>
    <div class="col-md">
        <div class="card shadow-sm border-0 p-3">
            <h6 class="text-muted small fw-bold mb-1 text-success">Occupied (Aktif)</h6>
            <h2 class="fw-bold text-success mb-0">{{ $successOrder }}</h2>
        </div>
    </div>
    <div class="col-md">
        <div class="card shadow-sm border-0 p-3">
            <h6 class="text-muted small fw-bold mb-1 text-danger">Dibatalkan</h6>
            <h2 class="fw-bold text-danger mb-0">{{ $cancelOrder }}</h2>
        </div>
    </div>
</div>

<!-- Daftar Pesanan -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Riwayat Booking</h5>
    </div>
    <div class="card-body">
        <!-- Search & Filter Area -->
        <form action="{{ route('bookings.index') }}" method="GET">
            <div class="row g-2 mb-4">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari nama penyewa atau ID pesanan..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Cicilan (Belum Lunas)</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Booked</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Occupied</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Canceled</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Search</button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
                        Clear
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Informasi Pesanan</th>
                        <th>Metode Bayar</th>
                        <th>Waktu Sewa</th>
                        <th>Total Tarif</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $item)
                    <tr>
                        <td class="ps-3 py-3">
                            <!-- Relasi ke model People/User -->
                            <div class="fw-bold text-dark">{{ $item->user->username ?? 'Guest' }}</div>
                            <!-- Relasi ke model BookingDetail -> Room -->
                            <small class="text-muted"><i class="bi bi-building"></i> {{ $item->details->room->name ?? 'Deleted Room' }}</small>
                        </td>
                        <td>
                            <span class="small fw-semibold text-secondary">{{ $item->method_payment }}</span>
                        </td>
                        <td>
                            <div class="small fw-bold text-dark">{{ date('d M Y', strtotime($item->start_date)) }}</div>
                            <small class="text-muted">{{ date('H:i', strtotime($item->start_date)) }} - {{ date('H:i', strtotime($item->end_date)) }}</small>
                        </td>
                        <td class="fw-bold text-primary">
                            Rp {{ number_format($item->total, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                             @if($item->status == 2)
                                 <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Occupied</span>
                             @elseif($item->status == 1)
                                 <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">Booked</span>
                             @elseif($item->status == 3)
                                 <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2" style="color: #a16207 !important;">Cicilan ({{ $item->installments_paid }}/3)</span>
                             @elseif($item->status == 0)
                                 <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2">Canceled</span>
                             @else
                                 <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">Unknown</span>
                             @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <!-- Tombol Detail (Aktif untuk semua status) -->
                                <a href="{{ route('penyedia.detail_history', $item->booking_id) }}" 
                                   class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold d-flex align-items-center shadow-sm" 
                                   style="font-size: 0.75rem;">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                                
                                <!-- Tombol Denda (Aktif untuk Status 2, disembunyikan untuk Status 3/Belum Bayar) -->
                                @if($item->status != 3 && $item->status != 0 && $item->status != 1)
                                    <a href="{{ $item->status == 2 ? route('bookings.denda', $item->booking_id) : 'javascript:void(0)' }}" 
                                       class="btn {{ $item->status == 2 ? 'btn-danger' : 'btn-light text-muted border' }} btn-sm rounded-pill px-3 fw-bold d-flex align-items-center shadow-sm" 
                                       style="font-size: 0.75rem; {{ $item->status != 2 ? 'pointer-events: none; opacity: 0.5; cursor: not-allowed;' : '' }}"
                                       @if($item->status != 2) title="Denda hanya tersedia setelah pesanan selesai" @endif>
                                        <i class="bi bi-exclamation-octagon me-1"></i> Denda
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada pesanan ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <p class="small text-muted mb-0">Menampilkan {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }} data</p>
            <div>
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection