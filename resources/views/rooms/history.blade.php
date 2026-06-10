@extends('layout.layout')

@section('custom_css')
<style>
    body { background-color: #f8f9fa; }

    /* Statistik Card Style */
    .stat-card {
        border-radius: 12px;
        border: none;
        transition: transform 0.2s;
        background: #ffffff;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-value { font-size: 2rem; font-weight: 800; color: #334155; }
    .stat-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    
    /* Table Styling */
    .table-container { border-radius: 15px; background: #ffffff; }
    table.dataTable thead th {
        background-color: #fcfcfd;
        border-bottom: 1px solid #f1f5f9 !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        padding: 1.25rem 1rem !important;
    }
    .table tbody td { padding: 1.25rem 1rem; vertical-align: middle; font-size: 0.9rem; }
    
    /* Badge Status */
    .badge-selesai { background-color: #dcfce7; color: #15803d; }
    .badge-konfirmasi { background-color: #fef9c3; color: #a16207; }
    
    /* Star Rating CSS */
    .star-rating input { display: none; }
    .star-rating label { font-size: 1.6rem; color: #ddd; cursor: pointer; transition: 0.2s; margin: 0 2px; }
    .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #FFC107; }
    
    .bg-primary-subtle { background-color: #e7f1ff; color: #0064D2; }
    .bg-danger-subtle { background-color: #fce8e6; color: #dc3545; }
    .btn-primary:disabled { background-color: #cbd5e1 !important; opacity: 0.7; cursor: not-allowed; }
    .rating-text.selected { color: #0064D2 !important; }
</style>
{{-- Load CSS DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Riwayat Persewaan</h2>
        <p class="text-secondary">Daftar seluruh transaksi dan status pemesanan ruangan Anda</p>
    </div>

    {{-- Statistik Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">TOTAL TRANSAKSI</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">SELESAI</div>
                <div class="stat-value text-success">{{ $stats['completed'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">TERJADWAL</div>
                <div class="stat-value text-primary">{{ $stats['active'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">DIBATALKAN</div>
                <div class="stat-value text-danger">{{ $stats['cancelled'] }}</div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card table-container border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle" id="tableHistory" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 10%;">ID Booking</th>
                            <th style="width: 25%;">Ruangan</th>
                            <th style="width: 20%;">Kegiatan / Kontak</th>
                            <th style="width: 20%;">Tanggal Sewa</th>
                            <th style="width: 10%;">Total Bayar</th>
                            <th class="text-center" style="width: 10%;">Status</th>
                            <th class="text-center" style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $b)
                        <tr>
                            <td class="fw-bold">#{{ $b->booking_id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $b->roomDetail->item_name ?? 'Ruangan' }}</div>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $b->roomDetail->room->location ?? 'Lokasi tidak tersedia' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $b->event }}</div>
                                <small class="text-muted"><i class="bi bi-whatsapp"></i> +62{{ $b->phone }}</small>
                            </td>
                            <td>
                                @php
                                    $startDateFormatted = date('d M Y', strtotime($b->start_date));
                                    $endDateFormatted = date('d M Y', strtotime($b->end_date));
                                @endphp
                                <div class="small fw-semibold">
                                    @if(date('Y-m-d', strtotime($b->start_date)) === date('Y-m-d', strtotime($b->end_date)))
                                        {{ $startDateFormatted }}
                                    @else
                                        {{ date('d M', strtotime($b->start_date)) }} - {{ $endDateFormatted }}
                                    @endif
                                </div>
                                <div class="text-muted small">{{ date('H:i', strtotime($b->start_date)) }} - {{ date('H:i', strtotime($b->end_date)) }}</div>
                            </td>
                            <td class="fw-bold text-dark">Rp {{ number_format($b->total, 0, ',', '.') }}</td>
                            <td class="text-center">
                                 @if($b->status == 1)
                                     @if(strtotime($b->end_date) < time())
                                         <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Selesai (Acara Lewat)</span>
                                     @else
                                         <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">Terjadwal</span>
                                     @endif
                                 @elseif($b->status == 2)
                                     <span class="badge rounded-pill badge-selesai px-3 py-2">Selesai</span>
                                 @elseif($b->status == 3)
                                     <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2">Belum Bayar</span>
                                 @elseif($b->status == 0)
                                     <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2">Batal</span>
                                 @endif
                            </td>
                             <td class="text-center">
                                 <div class="d-flex gap-2 justify-content-center align-items-center">
                                     @if($b->status == 0)
                                         <span class="text-muted small italic">Tidak ada tindakan</span>
                                     @else
                                         <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $b->booking_id }}">
                                             Detail
                                         </button>
                                         @if($b->status == 3)
                                             <a href="{{ route('booking.transaction', ['booking_id' => $b->booking_id]) }}" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold">
                                                 <i class="bi bi-wallet2 me-1"></i> Bayar
                                             </a>
                                         @endif
                                         @php
                                             $isCompleted = ($b->status == 2 || strtotime($b->end_date) < time());
                                         @endphp
                                         @if($isCompleted && $b->status != 3 && Auth::user()->role !== 'penyedia')
                                             @if($b->rating)
                                                 <span class="badge bg-light text-secondary border px-3 py-2"><i class="bi bi-check-circle-fill text-success me-1"></i> Sudah Dinilai</span>
                                             @else
                                                 <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 py-1.5 fw-bold shadow-sm" 
                                                         data-bs-toggle="modal" data-bs-target="#modalRating{{ $b->booking_id }}">
                                                     <i class="bi bi-star-fill me-1"></i> Rate
                                                 </button>
                                             @endif
                                         @endif
                                     @endif
                                 </div>
                             </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL RATING UNTUK SETIAP HISTORI PEMESANAN --}}
@foreach($bookings as $b)
    @php
        $isCompleted = ($b->status == 2 || strtotime($b->end_date) < time());
    @endphp
    @if($isCompleted && !$b->rating && $b->status != 3 && Auth::user()->role !== 'penyedia')
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
                                <textarea name="komentar" class="form-control bg-light border-0" rows="3" placeholder="Tulis masukan Anda mengenai kebersihan, kenyamanan, atau pelayanan..."></textarea>
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

{{-- MODAL DETAIL PEMESANAN --}}
@foreach($bookings as $b)
<div class="modal fade" id="modalDetail{{ $b->booking_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h4 class="fw-bold mb-0">Detail Pemesanan #{{ $b->booking_id }}</h4>
                    <p class="text-secondary small mb-0">Dibuat pada {{ date('d M Y H:i', strtotime($b->created_at)) }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4">
                    {{-- Ruangan & Lokasi --}}
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Ruangan</label>
                        <div class="p-3 rounded bg-light mt-1">
                            <h5 class="fw-bold mb-1 text-dark">{{ $b->roomDetail->item_name ?? 'Ruangan' }}</h5>
                            <p class="text-muted small mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $b->roomDetail->room->location ?? 'Lokasi tidak tersedia' }}</p>
                        </div>
                    </div>

                    {{-- Informasi Acara & Kontak --}}
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Acara & Kontak</label>
                        <div class="p-3 rounded bg-light mt-1">
                            <h6 class="fw-bold mb-1 text-dark">{{ $b->event }}</h6>
                            <p class="text-muted small mb-1"><i class="bi bi-whatsapp text-success me-1"></i> +62{{ $b->phone }}</p>
                            <p class="text-muted small mb-0"><i class="bi bi-credit-card me-1"></i> Metode: {{ $b->method_payment }}</p>
                        </div>
                    </div>

                    {{-- Waktu Sewa --}}
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Jadwal Persewaan</label>
                        <div class="p-3 rounded bg-light mt-1">
                            <p class="mb-1 fw-bold text-primary">
                                <i class="bi bi-calendar3 me-1"></i>
                                @if(date('Y-m-d', strtotime($b->start_date)) === date('Y-m-d', strtotime($b->end_date)))
                                    {{ date('d M Y', strtotime($b->start_date)) }}
                                @else
                                    {{ date('d M Y', strtotime($b->start_date)) }} - {{ date('d M Y', strtotime($b->end_date)) }}
                                @endif
                            </p>
                            <p class="text-muted small mb-0"><i class="bi bi-clock me-1"></i> Jam: {{ date('H:i', strtotime($b->start_date)) }} - {{ date('H:i', strtotime($b->end_date)) }}</p>
                        </div>
                    </div>

                    {{-- Status Pemesanan --}}
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Status</label>
                        <div class="p-3 rounded bg-light mt-1">
                             @if($b->status == 1)
                                 @if(strtotime($b->end_date) < time())
                                     <span class="badge rounded-pill bg-success text-white px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Selesai (Acara Lewat)</span>
                                 @else
                                     <span class="badge rounded-pill bg-primary text-white px-3 py-2"><i class="bi bi-calendar-event-fill me-1"></i> Terjadwal</span>
                                 @endif
                             @elseif($b->status == 2)
                                 <span class="badge rounded-pill bg-success text-white px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Selesai</span>
                             @elseif($b->status == 3)
                                 <span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="bi bi-wallet2 me-1"></i> Belum Bayar</span>
                             @elseif($b->status == 0)
                                 <span class="badge rounded-pill bg-danger text-white px-3 py-2"><i class="bi bi-x-circle-fill me-1"></i> Dibatalkan</span>
                             @endif
                            @if(Auth::user()->role !== 'penyedia')
                            <div class="text-muted small mt-2 text-capitalize">
                                @if($b->rating)
                                    <span class="text-success fw-semibold"><i class="bi bi-star-fill text-warning me-1"></i> Sudah memberikan ulasan</span>
                                @elseif($b->status != 0 && (strtotime($b->end_date) < time() || $b->status == 2))
                                    <span class="text-warning fw-semibold"><i class="bi bi-exclamation-circle me-1"></i> Belum memberikan ulasan</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Catatan Tambahan --}}
                    <div class="col-12">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Permintaan Khusus / Catatan</label>
                        <div class="p-3 rounded border bg-white mt-1 small">
                            {{ $b->notes ? $b->notes : 'Tidak ada catatan khusus.' }}
                        </div>
                    </div>

                    {{-- Rincian Biaya --}}
                    <div class="col-12">
                        <label class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px;">Rincian Pembayaran</label>
                        <div class="border rounded p-3 mt-1 bg-white">
                            {{-- Ruangan --}}
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <span class="fw-bold text-dark">{{ $b->roomDetail->item_name ?? 'Sewa Ruangan' }}</span>
                                    <span class="badge bg-light text-primary border ms-2">Utama</span>
                                </div>
                                <span class="fw-semibold text-dark">Rp {{ number_format($b->roomDetail->item_price ?? 0, 0, ',', '.') }}</span>
                            </div>

                            {{-- Layanan Tambahan / Addons --}}
                            @if($b->serviceDetails->isNotEmpty())
                                <hr class="my-2 border-dashed">
                                <div class="text-muted small fw-bold mb-2">LAYANAN TAMBAHAN</div>
                                @foreach($b->serviceDetails as $service)
                                <div class="d-flex justify-content-between mb-2 small text-secondary">
                                    <span>+ {{ $service->item_name }}</span>
                                    <span>Rp {{ number_format($service->item_price, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                            @endif

                            <hr class="my-2">
                            {{-- Grand Total --}}
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-6 text-dark">Total Pembayaran</span>
                                <span class="fw-bold fs-5 text-primary">Rp {{ number_format($b->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary w-100 py-2.5 rounded-pill fw-bold" data-bs-dismiss="modal">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('custom_js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        $('#tableHistory').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari riwayat...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    previous: "<i class='bi bi-chevron-left'></i>",
                    next: "<i class='bi bi-chevron-right'></i>"
                }
            },
            columnDefs: [
                { orderable: false, targets: [6] } // Matikan sorting kolom aksi
            ]
        });

        // Validasi pemilihan bintang di semua forms rating
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

        // Pemicu otomatis modal detail pemesanan dari redirect dashboard
        const urlParams = new URLSearchParams(window.location.search);
        const detailId = urlParams.get('detail');
        if (detailId) {
            var detailModalElement = document.getElementById('modalDetail' + detailId);
            if (detailModalElement) {
                var detailModal = new bootstrap.Modal(detailModalElement);
                detailModal.show();
            }
        }
    });
</script>
@endsection