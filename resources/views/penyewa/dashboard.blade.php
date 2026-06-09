@extends('layout.layout')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-12">
        <h3 class="fw-bold m-0">Dashboard Penyewa</h3>
        <p class="text-muted small">Selamat datang kembali! Pantau pemesanan ruangan dan berikan penilaian Anda.</p>
    </div>
</div>

{{-- Statistik Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-white transition-transform">
            <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">TOTAL PERSEWAAN</div>
            <div class="fs-3 fw-bold text-dark mt-1">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-white transition-transform">
            <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">SEWA AKTIF</div>
            <div class="fs-3 fw-bold text-primary mt-1">{{ $stats['active'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-white transition-transform">
            <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">SELESAI</div>
            <div class="fs-3 fw-bold text-success mt-1">{{ $stats['completed'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 rounded-3 bg-white transition-transform">
            <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">DIBATALKAN</div>
            <div class="fs-3 fw-bold text-danger mt-1">{{ $stats['cancelled'] }}</div>
        </div>
    </div>
</div>

<div class="row mb-3 align-items-center">
    <div class="col-6">
        <h5 class="fw-bold m-0 text-dark">Aktivitas Pemesanan Terbaru</h5>
    </div>
    <div class="col-6 text-end">
        <a href="/bookings/history" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold text-decoration-none">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3" style="width: 10%;">ID Booking</th>
                        <th style="width: 35%;">Ruangan & Lokasi</th>
                        <th style="width: 25%;">Waktu Sewa</th>
                        <th style="width: 15%;">Status</th>
                        <th class="text-center" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $b)
                    <tr>
                        <td class="ps-4 fw-bold">#{{ $b->booking_id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $b->roomDetail->item_name ?? 'Ruangan' }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $b->roomDetail->room->location ?? 'Lokasi tidak tersedia' }}</small>
                        </td>
                        <td>
                            @php
                                $startDateFormatted = date('d M Y', strtotime($b->start_date));
                                $endDateFormatted = date('d M Y', strtotime($b->end_date));
                            @endphp
                            <div class="small fw-bold">
                                @if(date('Y-m-d', strtotime($b->start_date)) === date('Y-m-d', strtotime($b->end_date)))
                                    {{ $startDateFormatted }}
                                @else
                                    {{ date('d M', strtotime($b->start_date)) }} - {{ $endDateFormatted }}
                                @endif
                            </div>
                            <div class="text-muted small">{{ date('H:i', strtotime($b->start_date)) }} - {{ date('H:i', strtotime($b->end_date)) }}</div>
                        </td>
                        <td>
                            @if($b->status == 1)
                                @if(strtotime($b->end_date) < time())
                                    <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Selesai (Acara Lewat)</span>
                                @else
                                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">Booked</span>
                                @endif
                            @elseif($b->status == 2)
                                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Selesai</span>
                            @elseif($b->status == 0)
                                <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2">Batal</span>
                            @endif
                        </td>
                        <td class="pe-3">
                            <div class="d-flex gap-2 justify-content-center align-items-center">
                                @if($b->status == 0)
                                    <a href="/bookings/history?detail={{ $b->booking_id }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold text-decoration-none">Detail</a>
                                @else
                                    @php
                                        $isCompleted = ($b->status == 2 || strtotime($b->end_date) < time());
                                    @endphp
                                    @if($isCompleted)
                                        @if($b->rating)
                                            <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check-circle-fill text-success me-1"></i> Sudah Dinilai</span>
                                        @else
                                            <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 py-1 fw-bold shadow-sm" 
                                                    data-bs-toggle="modal" data-bs-target="#modalRating{{ $b->booking_id }}">
                                                <i class="bi bi-star-fill me-1"></i> Beri Rating
                                            </button>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-primary border px-3 py-2">Terjadwal</span>
                                    @endif
                                    <a href="/bookings/history?detail={{ $b->booking_id }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-bold text-decoration-none">Detail</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-house-exclamation fs-1 d-block mb-3 text-secondary"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Transaksi Pemesanan</h6>
                            <p class="small text-secondary mb-0">Temukan ruangan terbaik Anda sekarang juga!</p>
                            <a href="/penyewa/search" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold mt-3 shadow-sm">Cari Ruangan</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL RATING BIASA (UNTUK SETIAP BARIS) --}}
@foreach($recentBookings as $b)
    @php
        $isCompleted = ($b->status == 2 || strtotime($b->end_date) < time());
    @endphp
    @if($isCompleted && !$b->rating)
    <div class="modal fade" id="modalRating{{ $b->booking_id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 p-4 pb-0">
                    <div>
                        <h4 class="fw-bold mb-0">Beri Penilaian</h4>
                        <p class="text-secondary small mb-0">{{ $b->roomDetail->item_name ?? 'Ruangan' }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('ratings.store') }}" method="POST" class="rating-form" data-id="{{ $b->booking_id }}">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $b->booking_id }}">
                    <input type="hidden" name="item_id" value="{{ $b->roomDetail->item_id ?? 0 }}">
                    <input type="hidden" name="item_type" value="1">
                    
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            @foreach(['kebersihan' => 'Kebersihan', 'pelayanan' => 'Pelayanan', 'kenyamanan' => 'Kenyamanan'] as $key => $label)
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px;">{{ $label }}</label>
                                <div class="rating-container p-3 rounded-3 d-flex align-items-center justify-content-between" style="background-color: #f8f9fa;">
                                    <div class="star-rating d-flex flex-row-reverse">
                                        @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="{{ $key }}-{{ $i }}-{{ $b->booking_id }}" name="{{ $key }}" value="{{ $i }}" required>
                                        <label for="{{ $key }}-{{ $i }}-{{ $b->booking_id }}"><i class="bi bi-star-fill"></i></label>
                                        @endfor
                                    </div>
                                    <span class="rating-text small fw-bold text-muted text-uppercase" id="text-{{ $key }}-{{ $b->booking_id }}">Pilih</span>
                                </div>
                            </div>
                            @endforeach

                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Ulasan Singkat</label>
                                <textarea name="komentar" class="form-control bg-light border-0" rows="3" placeholder="Bagikan ulasan Anda mengenai fasilitas/kebersihan/kenyamanan ruangan..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm submit-rating" disabled style="background-color: #0064D2; border: none;">
                            Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

{{-- MODAL RATING OTOMATIS (POP-UP NOTIFIKASI JIKA ADA ACARA SELESAI BELUM DINILAI) --}}
@if(isset($unratedBooking) && $unratedBooking)
<div class="modal fade" id="modalRatingAuto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <div class="badge bg-warning text-dark mb-2 px-3 py-1.5 fw-bold" style="font-size: 10px;"><i class="bi bi-bell-fill me-1"></i> ULASAN DIBUTUHKAN</div>
                    <h4 class="fw-bold mb-0">Bagaimana Acara Anda?</h4>
                    <p class="text-secondary small mb-0">Silakan beri penilaian untuk penyewaan: <strong>{{ $unratedBooking->roomDetail->item_name ?? 'Ruangan' }}</strong></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('ratings.store') }}" method="POST" class="rating-form" data-id="auto">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $unratedBooking->booking_id }}">
                <input type="hidden" name="item_id" value="{{ $unratedBooking->roomDetail->item_id ?? 0 }}">
                <input type="hidden" name="item_type" value="1">
                
                <div class="modal-body p-4">
                    <div class="row g-4">
                        @foreach(['kebersihan' => 'Kebersihan', 'pelayanan' => 'Pelayanan', 'kenyamanan' => 'Kenyamanan'] as $key => $label)
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px;">{{ $label }}</label>
                            <div class="rating-container p-3 rounded-3 d-flex align-items-center justify-content-between" style="background-color: #f8f9fa;">
                                <div class="star-rating d-flex flex-row-reverse">
                                    @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="{{ $key }}-{{ $i }}-auto" name="{{ $key }}" value="{{ $i }}" required>
                                    <label for="{{ $key }}-{{ $i }}-auto"><i class="bi bi-star-fill"></i></label>
                                    @endfor
                                </div>
                                <span class="rating-text small fw-bold text-muted text-uppercase" id="text-{{ $key }}-auto">Pilih</span>
                            </div>
                        </div>
                        @endforeach

                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase">Ulasan Singkat</label>
                            <textarea name="komentar" class="form-control bg-light border-0" rows="3" placeholder="Tulis masukan Anda mengenai kebersihan, kenyamanan, atau pelayanan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm submit-rating" disabled style="background-color: #0064D2; border: none;">
                        Kirim Penilaian & Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
    /* Star Rating CSS */
    .star-rating input { display: none; }
    .star-rating label { font-size: 1.6rem; color: #ddd; cursor: pointer; transition: 0.2s; margin: 0 2px; }
    .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #FFC107; }
    
    /* Subtle Badge */
    .bg-primary-subtle { background-color: #e7f1ff; color: #0064D2; }
    .bg-success-subtle { background-color: #e1f7ed; color: #198754; }
    .bg-danger-subtle { background-color: #fce8e6; color: #dc3545; }

    .btn-primary:disabled { background-color: #cbd5e1 !important; opacity: 0.7; cursor: not-allowed; }
    .rating-text.selected { color: #0064D2 !important; }
    
    .transition-transform {
        transition: transform 0.2s ease-in-out;
    }
    .transition-transform:hover {
        transform: translateY(-3px);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = { 1: 'Buruk', 2: 'Kurang', 3: 'Cukup', 4: 'Baik', 5: 'Sangat Baik' };

    // Validasi pemilihan bintang di semua forms rating
    document.querySelectorAll('.rating-form').forEach(form => {
        const bookingId = form.dataset.id;
        const radios = form.querySelectorAll('input[type="radio"]');
        const submitBtn = form.querySelector('.submit-rating');

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Update Teks Label
                const category = this.name;
                const textElem = document.getElementById(`text-${category}-${bookingId}`);
                if (textElem) {
                    textElem.textContent = labels[this.value];
                    textElem.classList.add('selected');
                }

                // Validasi: Apakah semua kategori (3 kategori) sudah diisi?
                const checked = form.querySelectorAll('input[type="radio"]:checked');
                if (checked.length === 3) {
                    submitBtn.disabled = false;
                }
            });
        });
    });

    // Pemicu Notifikasi Rating Otomatis jika ada booking selesai belum di-rate
    @if(isset($unratedBooking) && $unratedBooking)
        setTimeout(function() {
            var autoModalElement = document.getElementById('modalRatingAuto');
            if (autoModalElement) {
                var autoModal = new bootstrap.Modal(autoModalElement);
                autoModal.show();
            }
        }, 800);
    @endif
});
</script>
@endsection