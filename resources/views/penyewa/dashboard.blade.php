@extends('layout.layout')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-12">
        <h3 class="fw-bold m-0">Status Pesanan Saya</h3>
        <p class="text-muted small">Pantau jadwal dan berikan penilaian setelah masa sewa selesai.</p>
    </div>
</div>

@php
    // Data Dummy untuk simulasi
    $bookings_dummy = [
        (object)[
            'booking_id' => 101,
            'room_name' => 'Kontena Hotel - Ball Room',
            'location' => 'KH. Agus Salim No.106, Kota Batu',
            'start_date' => '2026-05-12 09:00:00',
            'end_date' => '2026-05-12 12:00:00',
            'status' => 1, // Booked
            'room_id' => 1
        ],
        (object)[
            'booking_id' => 99,
            'room_name' => 'Kontena Hotel - Meeting Room',
            'location' => 'KH. Agus Salim No.106, Kota Batu',
            'start_date' => '2026-05-10 14:00:00',
            'end_date' => '2026-05-10 16:00:00',
            'status' => 2, // Occupied / Selesai
            'room_id' => 1
        ]
    ];
@endphp

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th>Ruangan & Lokasi</th>
                        <th>Waktu Sewa</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings_dummy as $b)
                    <tr>
                        <td class="ps-4 fw-bold">#{{ $b->booking_id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $b->room_name }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $b->location }}</small>
                        </td>
                        <td>
                            <div class="small fw-bold">{{ date('d M Y', strtotime($b->start_date)) }}</div>
                            <div class="text-muted small">{{ date('H:i', strtotime($b->start_date)) }} - {{ date('H:i', strtotime($b->end_date)) }}</div>
                        </td>
                        <td>
                            @if($b->status == 1)
                                <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">Booked</span>
                            @elseif($b->status == 2)
                                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Selesai</span>
                            @endif
                        </td>
                        <td class="text-center pe-3">
                            @if($b->status == 1)
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">Detail</button>
                            @elseif($b->status == 2)
                                <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold shadow-sm" 
                                        data-bs-toggle="modal" data-bs-target="#modalRating{{ $b->booking_id }}">
                                    <i class="bi bi-star-fill me-1"></i> Beri Rating
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL RATING --}}
@foreach($bookings_dummy as $b)
<div class="modal fade" id="modalRating{{ $b->booking_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h4 class="fw-bold mb-0">Beri Penilaian</h4>
                    <p class="text-secondary small mb-0">{{ $b->room_name }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('ratings.store') }}" method="POST" class="rating-form" data-id="{{ $b->booking_id }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-4">
                        @foreach(['kebersihan', 'pelayanan', 'kenyamanan'] as $category)
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px;">{{ $category }}</label>
                            <div class="rating-container p-3 rounded-3 d-flex align-items-center justify-content-between" style="background-color: #f8f9fa;">
                                <div class="star-rating d-flex flex-row-reverse">
                                    @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="{{ $category }}-{{ $i }}-{{ $b->booking_id }}" name="{{ $category }}" value="{{ $i }}" required>
                                    <label for="{{ $category }}-{{ $i }}-{{ $b->booking_id }}"><i class="bi bi-star-fill"></i></label>
                                    @endfor
                                </div>
                                <span class="rating-text small fw-bold text-muted text-uppercase" id="text-{{ $category }}-{{ $b->booking_id }}">Pilih</span>
                            </div>
                        </div>
                        @endforeach

                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase">Ulasan Singkat</label>
                            <textarea name="komentar" class="form-control bg-light border-0" rows="3" placeholder="Apa yang membuat Anda puas?"></textarea>
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
@endforeach

<style>
    /* Star Rating CSS */
    .star-rating input { display: none; }
    .star-rating label { font-size: 1.6rem; color: #ddd; cursor: pointer; transition: 0.2s; margin: 0 2px; }
    .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #FFC107; }
    
    /* Subtle Badge */
    .bg-primary-subtle { background-color: #e7f1ff; color: #0064D2; }
    .bg-success-subtle { background-color: #e1f7ed; color: #198754; }

    .btn-primary:disabled { background-color: #cbd5e1 !important; opacity: 0.7; cursor: not-allowed; }
    .rating-text.selected { color: #0064D2 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = { 1: 'Buruk', 2: 'Kurang', 3: 'Cukup', 4: 'Baik', 5: 'Sangat Baik' };

    document.querySelectorAll('.rating-form').forEach(form => {
        const bookingId = form.dataset.id;
        const radios = form.querySelectorAll('input[type="radio"]');
        const submitBtn = form.querySelector('.submit-rating');

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Update Teks Label
                const category = this.name;
                const textElem = document.getElementById(`text-${category}-${bookingId}`);
                textElem.textContent = labels[this.value];
                textElem.classList.add('selected');

                // Validasi: Apakah semua kategori (3 kategori) sudah diisi?
                const checked = form.querySelectorAll('input[type="radio"]:checked');
                if (checked.length === 3) {
                    submitBtn.disabled = false;
                }
            });
        });
    });
});
</script>
@endsection