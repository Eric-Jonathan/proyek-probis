@extends('layout.layout')

@section('custom_css')
<style>
    :root {
        --primary-blue: #0064D2;
        --light-blue: #f0f7ff;
        --success-green: #28a745;
        --holiday-red: #ff5e5e;
    }

    html { scroll-behavior: smooth; }

    section, .container[id] {
        scroll-margin-top: 140px;
    }

    body { background-color: #f8f9fa; }

    .clickable-box {
        background-color: #f8f9fa;
        color: #444;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        display: inline-block;
    }

    .clickable-box:hover {
        background-color: var(--light-blue) !important;
        color: var(--primary-blue) !important;
        border-color: #cfe2ff !important;
    }

    /* --- KALENDER GRID STYLE --- */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        text-align: center;
    }

    .calendar-day-head {
        font-size: 0.75rem;
        font-weight: bold;
        color: #999;
        padding-bottom: 10px;
    }

    .calendar-day-head.sun { color: var(--holiday-red); }

    .btn-date {
        border: none;
        background: transparent;
        padding: 8px 0;
        border-radius: 8px;
        transition: 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 60px;
        position: relative;
        margin-top: 20px;
    }

    .btn-date:hover:not(:disabled) { background-color: #f0f0f0; }
    .btn-date:disabled { cursor: default; background: transparent !important; }
    .btn-date:disabled .num, .btn-date:disabled .price { color: #ccc !important; }
    .btn-date:disabled .today-label { color: var(--primary-blue); }
    .btn-date .num { font-weight: 600; font-size: 1rem; color: #444; }
    .btn-date .price { font-size: 0.65rem; font-weight: 600; }

    .btn-date.selected {
        background-color: var(--primary-blue) !important;
        color: white !important;
    }

    .btn-date.selected .num, .btn-date.selected .price { color: white !important; }
    .is-holiday .num { color: var(--holiday-red); }

    .today-label {
        position: absolute;
        top: -25px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.8rem;
        color: var(--primary-blue);
        font-weight: bold;
        white-space: nowrap;
    }

    .gallery-item { cursor: pointer; background-color: #000; }
    .gallery-img { transition: opacity 0.3s ease; display: block; }
    .gallery-item:hover .gallery-img { opacity: 0.7; }

    #galleryModal .modal-dialog { max-width: 95%; margin: 20px auto; }
    #galleryModal .modal-content { border: none; border-radius: 12px; overflow: hidden; }
    .gallery-sidebar { background-color: #fff; padding: 20px; height: 90vh; overflow-y: auto; }

    .btn-wishlist-float {
        position: absolute !important;
        top: 15px;
        right: 22px;
        width: 38px;
        height: 38px;
        background-color: white !important;
        border-radius: 50% !important;
        border: none !important;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
        z-index: 10;
        transition: all 0.2s ease;
        padding: 0;
    }
    .btn-wishlist-float:hover { transform: scale(1.1); }
    .btn-wishlist-float i { font-size: 1.2rem; color: #555; line-height: 0; }
    .btn-wishlist-float i.text-danger { color: #ff385c !important; }

    .nav-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.6) !important;
        color: white;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 100;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }
    .nav-arrow:hover { background: rgba(0, 0, 0, 0.8) !important; transform: translateY(-50%) scale(1.1); }
    .nav-arrow.prev { left: 20px; }
    .nav-arrow.next { right: 20px; }

    .gallery-grid-item {
        cursor: pointer;
        aspect-ratio: 1/1;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: 0.2s;
        width: 100%;
    }
    .gallery-grid-item:hover { opacity: 0.8; }
    .gallery-grid-item.active { border: 3px solid var(--primary-blue); }

    .gallery-main-view {
        background-color: #1a1a1a;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 0;
    }
    .main-img-full { max-width: 100%; max-height: 90vh; object-fit: contain; }
    .gallery-info-overlay { position: absolute; bottom: 20px; left: 20px; color: white; text-shadow: 0 1px 4px rgba(0,0,0,0.5); }

    .custom-btn-opacity {
        background-color: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(4px);
        font-weight: 500;
        transition: background-color 0.3s ease;
        border-radius: 12px !important;
        border: none !important;
        font-size: 0.85rem;
        white-space: nowrap;
    }
    .custom-btn-opacity:hover { background-color: rgba(0, 0, 0, 0.8) !important; color: white; transform: scale(1.05); }
</style>
@endsection

@section('content')
<body class="bg-white">
    <!-- Sticky Sub-Navbar -->
    <div class="sticky-top bg-white border-bottom shadow-sm" style="z-index: 900; top: 70px;">
        <div class="container">
            <ul id="main-nav" class="nav nav-tabs border-0 mb-0 text-nowrap flex-nowrap overflow-auto py-2">
                <li class="nav-item"><a class="nav-link active text-primary fw-bold border-0 border-bottom border-primary border-3" href="#section-info">Info Umum</a></li>
                <li class="nav-item"><a class="nav-link text-muted border-0" href="#section-review">Review</a></li>
                <li class="nav-item"><a class="nav-link text-muted border-0" href="#section-fasilitas">Fasilitas</a></li>
                <li class="nav-item"><a class="nav-link text-muted border-0" href="#section-lokasi">Lokasi & Peraturan</a></li>
                <li class="nav-item"><a class="nav-link text-muted border-0" href="#section-tentang">Tentang</a></li>
            </ul>
        </div>
    </div>

    <!-- Section Info & Gambar Dinamis -->
    <div id="section-info" class="container py-3">
        <div class="row g-2 mb-4">
            @php
                $images = $room->images->pluck('path')->toArray();
                if(count($images) == 0) {
                    $images = ['rooms/default.png', 'rooms/default.png', 'rooms/default.png', 'rooms/default.png', 'rooms/default.png'];
                }
            @endphp
            
            <div class="col-md-6">
                <div class="h-100">
                    <img src="{{ asset($images[0]) }}" class="img-fluid rounded object-fit-cover w-100" alt="Main Image" style="height: 408px;">
                </div>
            </div>

            <div class="col-md-6">
                <div class="row g-2">
                    <div class="col-6"><img src="{{ asset($images[1] ?? $images[0]) }}" class="img-fluid rounded object-fit-cover w-100" style="height: 200px;" alt="Gallery 1"></div>
                    <div class="col-6 position-relative">
                        <img src="{{ asset($images[2] ?? $images[0]) }}" class="img-fluid rounded object-fit-cover w-100" style="height: 200px;" alt="Gallery 2">
                        <button type="button" class="btn btn-wishlist-float" id="btn-wishlist">
                            <i class="bi bi-heart" id="icon-wishlist"></i>
                        </button>
                    </div>
                    <div class="col-6"><img src="{{ asset($images[3] ?? $images[0]) }}" class="img-fluid rounded object-fit-cover w-100" style="height: 200px;" alt="Gallery 3"></div>
                    <div class="col-6">
                        <div class="gallery-item position-relative overflow-hidden rounded shadow-sm h-100">
                            <img src="{{ asset($images[4] ?? $images[0]) }}" class="img-fluid object-fit-cover w-100 gallery-img" style="height: 200px;" alt="Gallery Room">
                            <div class="position-absolute bottom-0 end-0 m-2">
                                <button class="btn text-white px-3 py-2 custom-btn-opacity" data-bs-toggle="modal" data-bs-target="#galleryModal">
                                    Lihat semua foto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-start">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="text-warning small d-flex gap-1">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1" style="font-size: 2.2rem; letter-spacing: -0.5px;">{{ $room->name }}</h2>
                <div class="d-flex align-items-center gap-2 small">
                    <span class="fw-bold">4.8<span class="text-muted fw-normal">/5</span></span>
                    <a href="#section-review" class="text-decoration-none fw-semibold" style="color: var(--primary-blue);">(32 Review)</a>
                    <span class="text-muted mx-1">•</span>
                    <span class="text-muted fw-medium"><i class="bi bi-geo-alt"></i> {{ $room->location }}</span>
                </div>
            </div>

            <div class="col-md-4 text-md-end">
                <!-- Badge Status -->
                <p class="text-muted small mb-1">Biaya Sewa Ruangan</p>
                
                <!-- Blok Harga Dinamis Sejajar Satuan -->
                <div class="d-md-flex align-items-baseline justify-content-md-end flex-wrap gap-2 mb-1">
                    <h2 class="text-primary fw-bold mb-0" style="font-size: 2.2rem; letter-spacing: -0.5px;">
                        IDR {{ number_format($room->price, 0, ',', '.') }}
                    </h2>
                    <span class="text-dark fw-bold text-uppercase" style="font-size: 1.2rem; color: #222 !important;">
                        / {{ $room->jenis_harga }}
                    </span>
                </div>
                
                <!-- Keterangan Minimal Order yang Diperjelas -->
                <p class="text-muted small mb-4">
                    <i class="bi bi-info-circle me-1 text-secondary"></i>Min. Order: <span class="fw-semibold text-dark">{{ $room->min_order }} {{ $room->jenis_harga }}</span>
                </p>
                
                <!-- Tombol Aksi -->
                <a href="#section-rooms" class="btn btn-primary px-5 py-2 fw-bold shadow-sm rounded-pill w-100 w-md-auto" style="background-color: var(--primary-blue);">
                    Pesan Sekarang
                </a>
            </div>
        </div>
    </div>

    <!-- Section Review -->
    <div id="section-review" class="container py-4">
        <ul class="nav nav-tabs border-bottom border-light mb-4"></ul>
        <h4 class="fw-bold mb-4">Review Pengguna</h4>
        <div class="d-flex overflow-auto gap-3 pb-3" style="scrollbar-width: none;">
            <div class="card border-0 shadow-sm flex-shrink-0" style="width: 350px; border-radius: 12px; background-color: #fcfcfc;">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span class="fw-bold small">5.0<span class="text-muted">/5</span></span><span class="text-muted" style="font-size: 0.75rem;">Mei 2026</span></div>
                    <p class="small mb-2"><strong>Ahmad Nafii</strong> • Acara Korporat</p>
                    <p class="text-muted small mb-0">Sangat puas dengan fasilitas dan koordinasi dari tim pengelola ruangan!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Fasilitas -->
    <div id="section-fasilitas" class="container py-4">
        <ul class="nav nav-tabs border-bottom border-light mb-4"></ul>
        <h4 class="fw-bold mb-4">Fasilitas yang Tersedia</h4>
        @if($room->facilities->count() > 0)
            <div class="row g-4">
                @foreach($room->facilities as $facility)
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-patch-check-fill me-3 fs-5 text-primary"></i>
                            <span class="small fw-semibold">{{ $facility->name }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-light border shadow-sm py-3 text-center text-muted">
                <i class="bi bi-info-circle me-1"></i> Tidak ada fasilitas khusus terdaftar untuk ruangan ini.
            </div>
        @endif
    </div>

    <!-- Section Lokasi & Aturan -->
    <div id="section-lokasi" class="container py-4">
        <ul class="nav nav-tabs border-bottom border-light mb-4"></ul>
        <h4 class="fw-bold mb-4">Lokasi & Aturan</h4>
        <div class="row g-4">
            <div class="col-md-7">
                <div class="rounded-3 overflow-hidden shadow-sm border" style="height: 380px;">
                    <iframe width="100%" 
                        height="100%" 
                        style="border:0;" 
                        loading="lazy" 
                        allowfullscreen 
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://maps.google.com/maps?q={{ urlencode($room->location) }}&z=16&output=embed">
                    </iframe>
                </div>
                <p class="mt-2 text-muted small fw-medium"><i class="bi bi-geo-alt-fill text-danger"></i> {{ $room->location }}</p>
            </div>

            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: #fff;">
                    <div class="card-body p-4 d-flex flex-column">
                        <h6 class="fw-bold mb-4 border-start border-primary border-3 ps-2">Informasi & Aturan</h6>
                        <div class="p-3 mb-4 rounded-3 d-flex align-items-center" style="background-color: #f8f9fa; border: 1px dashed #dee2e6;">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3"><i class="bi bi-people-fill text-primary fs-5"></i></div>
                            <div>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Maksimal Kapasitas</p>
                                <p class="fw-bold mb-0 small">{{ $room->capacity }} Orang (Pax)</p>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="fw-bold small mb-2"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Aturan Khusus Ruangan:</p>
                            <div class="text-muted small">
                                @if($room->rules) {!! $room->rules !!} @else <p class="text-muted italic">Mengikuti standar peraturan umum penyewaan gedung.</p> @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Tentang -->
    <div id="section-tentang" class="container py-4">
        <ul class="nav nav-tabs border-bottom border-light mb-4"></ul>
        <div class="row">
            <div class="col-lg-10">
                <h4 class="fw-bold mb-4 text-dark">Tentang Ruangan</h4>
                <div class="description-text text-muted" style="line-height: 1.8; font-size: 0.95rem;">
                    <p>{{ $room->description ?? 'Tidak ada deskripsi tambahan mengenai ruangan ini.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Booking Trigger -->
    <div id="section-rooms" class="container py-5">
        <h4 class="fw-bold mb-4">Pilihan Tanggal Penggunaan</h4>
        <div class="d-flex align-items-center justify-content-between p-3 border rounded-4 shadow-sm bg-white mb-4">
            <div class="d-flex align-items-center flex-grow-1 gap-3 px-2">
                <i class="bi bi-calendar3 text-primary"></i>
                
                <!-- TAMBAHKAN data-min-day DI SINI -->
                <span id="display-date" 
                    class="fw-semibold small clickable-box p-2 rounded-3" 
                    data-bs-toggle="modal" 
                    data-bs-target="#datePickerModal"
                    data-min-day="{{ $room->day ?? 1 }}"
                    data-room-id="{{ $room->room_id }}">
                    Pilih tanggal penyewaan...
                </span>
            </div>
            <a href="" id="btn-trigger-booking" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background-color: var(--primary-blue);">
                Booking Ruangan
            </a>
        </div>
    </div>

    <!-- Modal Kalender -->
    <div class="modal fade" id="datePickerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold">Atur Tanggal Sewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 border-end position-relative">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <button type="button" class="btn btn-sm position-absolute start-0" id="prevMonthBtn"><i class="bi bi-chevron-left"></i></button>
                                <h5 id="labelMonthLeft" class="fw-bold mb-0"></h5>
                            </div>
                            <div class="calendar-grid" id="gridLeft"></div>
                        </div>
                        <div class="col-md-6 position-relative">
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <h5 id="labelMonthRight" class="fw-bold mb-0"></h5>
                                <button type="button" class="btn btn-sm position-absolute end-0" id="nextMonthBtn"><i class="bi bi-chevron-right"></i></button>
                            </div>
                            <div class="calendar-grid" id="gridRight"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-end border-0 px-4 py-3" style="border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-primary fw-bold px-5 py-2 rounded-pill" data-bs-dismiss="modal">Terapkan Tanggal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Galeri Pop-up -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0">
                <div class="row g-0">
                    <div class="col-md-4 col-lg-3 gallery-sidebar bg-white" style="height: 90vh; overflow-y: auto;">
                        <div class="p-3">
                            <h5 class="fw-bold mb-3">Koleksi Foto</h5>
                            <div class="row g-2" id="gallery-grid-injector">
                                @foreach($images as $key => $path)
                                    <div class="col-6">
                                        <img src="{{ asset($path) }}" class="gallery-grid-item item-akomodasi img-fluid {{ $key === 0 ? 'active' : '' }}" data-index="{{ $key }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-9 gallery-main-view bg-dark position-relative">
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                        <button class="nav-arrow prev" id="prevPhoto"><i class="bi bi-chevron-left"></i></button>
                        <button class="nav-arrow next" id="nextPhoto"><i class="bi bi-chevron-right"></i></button>
                        <img src="{{ asset($images[0]) }}" id="mainGalleryImage" class="main-img-full w-100 h-100 object-fit-contain">
                        <div class="gallery-info-overlay position-absolute bottom-0 start-0 p-4">
                            <p class="mb-0 small text-white-50" id="photoCounter">1/{{ count($images) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
@endsection

@section('custom_js')
    <!-- Panggil berkas JavaScript eksternal yang sudah di-jQuery kan -->
    <script src="{{ asset('custom_js/rooms/room_detail.js') }}"></script>
@endsection