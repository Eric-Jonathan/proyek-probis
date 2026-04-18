@extends('layout.layout')
{{-- belum ganti bagian navbar (selected nya) --}}
@section('content')
<style>
    :root {
        --primary-blue: #006ce4;
        --light-blue: #e8f2ff;
        --success-green: #28a745;
        --holiday-red: #ff5e5e;
    }
    html {
        scroll-behavior: smooth;
    }
    /* Agar saat di klik, posisi section tidak tertutup navbar sticky */
    section, .container[id] {
        scroll-margin-top: 140px; /* Jarak total navbar utama + navbar sticky */
    }
    body {
            background-color: #f8f9fa;
        }
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
        }
        .btn-date:hover:not(:disabled) { background-color: #f0f0f0; }
        .btn-date .num { font-weight: 600; font-size: 1rem; color: #444; }
        .btn-date .price { font-size: 0.65rem; font-weight: 600; }
        .btn-date.selected { background-color: var(--primary-blue) !important; color: white !important; }
        .btn-date.selected .num, .btn-date.selected .price { color: white !important; }
        
        .price-low { color: var(--success-green); }
        .price-normal { color: #777; }
        .is-holiday .num { color: var(--holiday-red); }
        .today-label { font-size: 0.55rem; color: var(--primary-blue); font-weight: bold; margin-bottom: -2px; }

        /* --- GUEST MODAL STYLE --- */
        .guest-row { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 0; border-bottom: 1px solid #eee; }
        .guest-row:last-child { border-bottom: none; }
        .btn-stepper {
            width: 38px; height: 38px; border-radius: 50%; border: 1px solid var(--primary-blue);
            background: white; color: var(--primary-blue); display: flex; align-items: center;
            justify-content: center; font-size: 1.2rem; cursor: pointer; transition: 0.2s;
        }
        .btn-stepper:disabled { border-color: #ccc; color: #ccc; cursor: not-allowed; }
        .stepper-value { font-weight: bold; width: 30px; text-align: center; }
        .btn-date:hover:not(:disabled):not(.selected) {
    background-color: #f0f0f0;
}
</style>
<body class="bg-white">
    <div class="sticky-top bg-white border-bottom shadow-sm" style="z-index: 900; top: 70px;"> 
        <div class="container">
            <ul id="main-nav" class="nav nav-tabs border-0 mb-0 text-nowrap flex-nowrap overflow-auto py-2">
                <li class="nav-item">
                    <a class="nav-link active text-primary fw-bold border-0 border-bottom border-primary border-3" href="#section-info">Info Umum</a> 
                </li>
                <li class="nav-item">
                    <a class="nav-link text-muted border-0" href="#section-review">Review</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-muted border-0" href="#section-fasilitas">Fasilitas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-muted border-0" href="#section-lokasi">Lokasi & Peraturan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-muted border-0" href="#section-tentang">Tentang</a>
                </li>
            </ul>
        </div>
    </div>
<div id="section-info" class="container py-4">
<div class="container py-4">
        <div class="row g-2 mb-4">
            <div class="col-md-6">
                <div class="rounded-start overflow-hidden h-100">
                    <img src="pool_image.jpg" class="img-fluid h-100 w-100 object-fit-cover" alt="Pool Area">
                </div>
            </div>
            <div class="col-md-6">  
                <div class="row g-2 h-100">
                    <div class="col-6">
                        <img src="garden.jpg" class="img-fluid rounded w-100" alt="Garden">
                    </div>
                    <div class="col-6">
                        <div class="position-relative h-100">
                            <img src="room_1.jpg" class="img-fluid rounded w-100 h-100" alt="Room">
                            <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-2 rounded-circle shadow-sm">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-6">
                        <img src="room_2.jpg" class="img-fluid rounded w-100" alt="Room 2">
                    </div>
                    <div class="col-6">
                        <div class="position-relative">
                            <img src="outdoor.jpg" class="img-fluid rounded w-100" alt="Outdoor Area">
                            <div class="position-absolute bottom-0 end-0 m-2">
                                <button class="btn btn-dark btn-sm opacity-75">Lihat semua foto</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 1vw">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge border text-muted fw-normal px-2 py-1" style="font-size: 0.75rem;">Hotel</span>
                    <div class="text-warning small d-flex gap-1">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                </div>

                <h2 class="fw-bold mb-2" style="font-size: 2rem; letter-spacing: -0.5px;">{{ $room -> name }}</h2>

                <div class="d-flex align-items-center gap-2 small">
                    <span class="fw-bold">4,5<span class="text-muted fw-normal">/5</span></span>
                    <a href="#" class="text-decoration-none fw-semibold" style="color: #006ce4;">(3.972 review)</a>
                    <span class="text-muted mx-1">•</span>
                    <a href="#" class="text-decoration-none fw-semibold" style="color: #006ce4;">Batu, Malang</a>
                </div>
            </div>

            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <span class="badge bg-danger mb-2">Diskon 20%</span>
                <p class="text-muted small mb-0">Mulai dari (setelah cashback)</p>
                <del class="text-muted small">IDR {{ $room->price }}</del>
                <h3 class="text-danger fw-bold mb-1">IDR {{$room->price*8/10}}</h3>
                <p class="text-muted x-small mb-3">/hari</p>
                <a href="{{ route('booking.show') }}" class="btn btn-primary px-4 fw-bold shadow-sm">
    Lihat Ruangan
</a>
            </div>
        </div>
    </div>
    
<div class="container py-4">
    <ul class="nav nav-tabs border-bottom border-dark mb-4 text-nowrap flex-nowrap overflow-auto"></ul>
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Serunya Nginep di Sini</h4>
    </div>

    <div class="d-flex overflow-auto gap-3 pb-3" style="scrollbar-width: none; -ms-overflow-style: none;">
        
        <div class="card border shadow-sm flex-shrink-0" style="width: 320px; border-radius: 12px;">
            <div class="card-body d-flex gap-3">
                <div class="fs-1 text-warning">☀️</div>
                <div>
                    <h6 class="fw-bold mb-1">Pas buat pencinta wisata alam</h6>
                    <p class="small text-muted mb-0">Cuma 2,2 km ke Coban Lanang dan 2,5 km ke Batu Love Garden</p>
                </div>
            </div>
        </div>

        <div class="card border shadow-sm flex-shrink-0" style="width: 320px; border-radius: 12px;">
            <div class="card-body d-flex gap-3">
                <div class="fs-1 text-primary">🎡</div>
                <div>
                    <h6 class="fw-bold mb-1">Dekat taman bermain</h6>
                    <p class="small text-muted mb-0">Cuma 427 m ke Batu Wonderland Resort dan 818 m ke Batu Secret Zoo</p>
                </div>
            </div>
        </div>

        <div class="card border shadow-sm flex-shrink-0" style="width: 320px; border-radius: 12px;">
            <div class="card-body d-flex gap-3">
                <div class="fs-1 text-danger">🍴</div>
                <div>
                    <h6 class="fw-bold mb-1">Tersedia tempat makan</h6>
                    <p class="small text-muted mb-0">Nggak perlu keluar buat beli makanan! Ada restoran di akomodasi ini.</p>
                </div>
            </div>
        </div>

        <div class="card border shadow-sm flex-shrink-0" style="width: 320px; border-radius: 12px;">
            <div class="card-body d-flex gap-3">
                <div class="fs-1 text-danger">📍</div>
                <div>
                    <h6 class="fw-bold mb-1">Strategis! Dekat transportasi umum</h6>
                    <p class="small text-muted mb-0">Cuma 406 m ke Terminal Kota Batu</p>
                </div>
            </div>
        </div>

        <div class="card border shadow-sm flex-shrink-0" style="width: 320px; border-radius: 12px;">
            <div class="card-body d-flex gap-3">
                <div class="fs-1 text-info">💼</div>
                <div>
                    <h6 class="fw-bold mb-1">Cocok untuk trip bisnis</h6>
                    <p class="small text-muted mb-0">Ada fasilitas bisnis, bisa tetap produktif saat nginep.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="section-review" class="container py-4">
    <ul class="nav nav-tabs border-bottom border-dark mb-4 text-nowrap flex-nowrap overflow-auto"></ul>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Review</h4>
        <a href="#" class="text-primary text-decoration-none fw-bold small">Lihat semua</a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <h1 class="fw-bold mb-0 me-2" style="font-size: 3rem;">4,5<span class="text-muted fs-4">/5</span></h1>
            <div>
                <p class="fw-bold mb-0 fs-5">Fantastis</p>
                <p class="text-muted small mb-0">Dari 3972 review</p>
            </div>
        </div>
    </div>

    <div id="reviewSlider" class="d-flex overflow-auto gap-3 pb-3" 
         style="scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth;">
        
        <div class="card border-0 shadow-sm flex-shrink-0" style="width: 350px; border-radius: 12px; background-color: #fcfcfc;">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold small">5,0<span class="text-muted">/5</span></span>
                    <span class="text-muted x-small" style="font-size: 0.75rem;">6 Apr 2026</span>
                </div>
                <p class="small mb-2"><strong>Ahmad Nafii</strong> • Trip Keluarga</p>
                <p class="text-muted small mb-0">Stunning creative dishes.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm flex-shrink-0" style="width: 350px; border-radius: 12px; background-color: #fcfcfc;">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold small">5,0<span class="text-muted">/5</span></span>
                    <span class="text-muted x-small" style="font-size: 0.75rem;">6 Apr 2026</span>
                </div>
                <p class="small mb-2"><strong>Ahmad Nafii</strong> • Trip Keluarga</p>
                <p class="text-muted small mb-0">Modern fitness facilities.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm flex-shrink-0" style="width: 350px; border-radius: 12px; background-color: #fcfcfc;">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold small">5,0<span class="text-muted">/5</span></span>
                    <span class="text-muted x-small" style="font-size: 0.75rem;">6 Apr 2026</span>
                </div>
                <p class="small mb-2"><strong>mela ariana</strong> • Trip Keluarga</p>
                <p class="text-muted small mb-0">tempatnya unik,dekat dengan tempat wisata dan kota pokoknya langganan aku bgt deh</p>
            </div>
        </div>

    </div>
</div>

<div id="section-fasilitas" class="container py-4">
    <ul class="nav nav-tabs border-bottom border-dark mb-4 text-nowrap flex-nowrap overflow-auto"></ul>

    <h4 class="fw-bold mb-4">Fasilitas Populer</h4>
    
    <div class="row g-4">
        <div class="col-6 col-md-3">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-water me-3 fs-5"></i>
                <span class="small">Kolam Renang</span>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-snow me-3 fs-5"></i>
                <span class="small">AC</span>
            </div>
            <div class="d-flex align-items-center">
                <i class="bi bi-house-heart me-3 fs-5"></i>
                <span class="small">Fasilitas Anak</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-wifi me-3 fs-5"></i>
                <span class="small">WiFi</span>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-bus-front me-3 fs-5"></i>
                <span class="small">Antar Jemput Bandara</span>
            </div>
            <div class="d-flex align-items-center">
                <i class="bi bi-person-wheelchair"></i>
                <span class="small">Akses Kursi Roda</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-p-square me-3 fs-5"></i>
                <span class="small">Parkir</span>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-person-workspace me-3 fs-5"></i>
                <span class="small">Resepsionis 24 Jam</span>
            </div>
            <div class="d-flex align-items-center">
                <i class="bi bi-people me-3 fs-5"></i>
                <span class="small">Fasilitas Rapat</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-check-circle-fill me-3 fs-5 text-dark"></i>
                <span class="small">tiket CLEAN</span>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-egg-fried me-3 fs-5"></i>
                <span class="small">Restoran</span>
            </div>
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-check me-3 fs-5"></i>
                <span class="small">Bukti Vaksin Covid-19</span>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="#" class="text-primary text-decoration-none fw-bold small d-flex align-items-center">
            Lihat semua <i class="bi bi-chevron-down ms-1"></i>
        </a>
    </div>
</div>

<div id="section-lokasi" class="container py-4">    
    <ul class="nav nav-tabs border-bottom border-dark mb-4 text-nowrap flex-nowrap overflow-auto"></ul>

    <h4 class="fw-bold mb-4">Lokasi</h4>
    
    <div class="row g-4">
        <div class="col-md-7">
            <div class="rounded-3 overflow-hidden shadow-sm border" style="height: 400px;">
                <iframe 
                    src={{ $room -> embed_url }}
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <p class="mt-2 text-muted small">
                <i class="bi bi-geo-alt-fill text-danger"></i> Jl. {{ $room->location }}
            </p>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-4 d-flex flex-column">
                    <h6 class="fw-bold mb-4">Detail & Aturan Kamar</h6>
                    
                    <div class="p-3 mb-4 rounded-3 d-flex align-items-center" style="background-color: #f8f9fa; border: 1px dashed #dee2e6;">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                            <i class="bi bi-people-fill text-primary fs-5"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 0.75rem;">Kapasitas Ruangan</p>
                            <p class="fw-bold mb-0 small">{{ $room->capacity }} Orang</p>
                        </div>
                    </div>

                    <div class="flex-grow-1">
                        <p class="fw-bold small mb-3"><i class="bi bi-info-circle me-2"></i>Aturan Penyewaan:</p>
                        <ul class="list-unstyled">
                            @foreach($room->rules as $rule)
                                <li class="d-flex align-items-start mb-2 small text-muted">
                                    <i class="bi bi-check2-circle text-success me-2 mt-1"></i>
                                    <span>{{ $rule }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-auto pt-3 border-top text-center">
                        <p class="text-muted mb-0" style="font-size: 0.7rem;">
                            *Pelanggaran aturan dapat dikenakan denda sesuai kebijakan hotel.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<div id="section-tentang" class="container py-4">
    <ul class="nav nav-tabs border-bottom border-dark mb-4 text-nowrap flex-nowrap overflow-auto"></ul>
    
    <div class="row">
        <div class="col-lg-10">
            <h4 class="fw-bold mb-4 text-dark">Tentang {{ $room -> name }}</h4>
            
            <div class="description-text text-muted" style="line-height: 1.8; font-size: 0.95rem;">
                <p class="mb-4">
                    {{ $room -> description }}
                </p>
            </div>
        </div>
    </div>
</div>

<div id="section-rooms" class="container py-5">
    <h4 class="fw-bold mb-4">Room Type and Price</h4>
    
    <div class="d-flex align-items-center justify-content-between p-3 border rounded-4 shadow-sm bg-white mb-4">
        <div class="d-flex align-items-center flex-grow-1 gap-3 px-2">
            <i class="bi bi-search text-muted"></i>
            
            <div class="d-flex align-items-center">
                <span id="display-date" class="fw-semibold small clickable-box p-2 rounded-3" 
                      data-bs-toggle="modal" data-bs-target="#datePickerModal">
                    Sun, 19 Apr 2026 - Mon, 20 Apr 2026 (1 night)
                </span>
            </div>

            <div class="vr mx-2" style="height: 25px; opacity: 0.1;"></div>
            
            <div class="d-flex align-items-center">
                <span id="display-guests" class="fw-semibold small clickable-box p-2 rounded-3"
                      data-bs-toggle="modal" data-bs-target="#guestPickerModal">
                    1 Room, 1 Adult, 0 Children
                </span>
            </div>
        </div>
        
        <button class="btn btn-sm px-4 fw-bold" 
                style="background-color: var(--light-blue); color: var(--primary-blue); border-radius: 10px;"
                data-bs-toggle="modal" data-bs-target="#datePickerModal">
            Change
        </button>
    </div>
</div>

<div class="modal fade" id="datePickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Set Dates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <h6 class="text-center fw-bold mb-4">April 2026</h6>
                        <div class="calendar-grid" id="april-grid">
                            <div class="calendar-day-head sun">Sun</div><div class="calendar-day-head">Mon</div>
                            <div class="calendar-day-head">Tue</div><div class="calendar-day-head">Wed</div>
                            <div class="calendar-day-head">Thu</div><div class="calendar-day-head">Fri</div>
                            <div class="calendar-day-head">Sat</div>
                            </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-center fw-bold mb-4">May 2026</h6>
                        <div class="calendar-grid" id="may-grid">
                            <div class="calendar-day-head sun">Sun</div><div class="calendar-day-head">Mon</div>
                            <div class="calendar-day-head">Tue</div><div class="calendar-day-head">Wed</div>
                            <div class="calendar-day-head">Thu</div><div class="calendar-day-head">Fri</div>
                            <div class="calendar-day-head">Sat</div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between border-0 px-4 py-3" style="border-radius: 0 0 20px 20px;">
                <div class="small">
                    <span class="badge bg-success" style="width: 12px; height: 12px; padding: 0;">&nbsp;</span>
                    <span class="ms-1 text-muted">Lowest price in IDR</span>
                </div>
                <button type="button" class="btn btn-primary fw-bold px-5 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">Apply</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="guestPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Rooms & Guests</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <div class="guest-row">
                    <span class="fw-bold">Rooms</span>
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn-stepper" onclick="updateGuest('room', -1)" id="room-minus"><i class="bi bi-dash"></i></button>
                        <span class="stepper-value" id="room-val">1</span>
                        <button class="btn-stepper" onclick="updateGuest('room', 1)" id="room-plus"><i class="bi bi-plus"></i></button>
                    </div>
                </div>
                <div class="guest-row">
                    <span class="fw-bold">Adults</span>
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn-stepper" onclick="updateGuest('adult', -1)" id="adult-minus"><i class="bi bi-dash"></i></button>
                        <span class="stepper-value" id="adult-val">1</span>
                        <button class="btn-stepper" onclick="updateGuest('adult', 1)" id="adult-plus"><i class="bi bi-plus"></i></button>
                    </div>
                </div>
                <div class="guest-row border-0">
                    <div>
                        <div class="fw-bold">Children</div>
                        <div class="text-muted small">(below 17 years old)</div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn-stepper" onclick="updateGuest('child', -1)" id="child-minus"><i class="bi bi-dash"></i></button>
                        <span class="stepper-value" id="child-val">0</span>
                        <button class="btn-stepper" onclick="updateGuest('child', 1)" id="child-plus"><i class="bi bi-plus"></i></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button class="btn btn-primary w-100 fw-bold py-3" data-bs-dismiss="modal" style="border-radius: 12px;">Done</button>
            </div>
        </div>
    </div>
</div>

</div>
    
</body>

<script>

document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('#main-nav .nav-link');
    const sections = document.querySelectorAll('#section-info, #section-review, #section-fasilitas, #section-lokasi, #section-tentang');
function populateGrid(gridId, startEmpty, days, monthIndex, monthLabel, year) {
    const grid = document.getElementById(gridId);
    
    // 1. Ambil data waktu saat ini (Real-time)
    const now = new Date();
    const todayDate = now.getDate();
    const todayMonth = now.getMonth();
    const todayYear = now.getFullYear();

    // Reset isi grid agar tidak double saat fungsi dipanggil ulang
    // grid.innerHTML = ''; // Opsional, jika header Sun-Sat ada di HTML, jangan pakai ini.

    // Kotak Kosong
    for(let i=0; i<startEmpty; i++) grid.appendChild(document.createElement('div'));
    
    // Isi Tanggal
    for(let d=1; d<=days; d++) {
        // Cek apakah ini hari ini
        const isToday = (d === todayDate && monthIndex === todayMonth && year === todayYear);
        
        // Cek apakah tanggal sudah lewat (untuk disable klik)
        // Kita buat objek date untuk tanggal yang sedang di-loop
        const loopDate = new Date(year, monthIndex, d);
        const yesterday = new Date(now);
        yesterday.setHours(0,0,0,0); // Set ke awal hari ini

        const isHoliday = (monthIndex === 4 && d === 1); // Contoh 1 Mei libur (Index Mei = 4)
        const isSun = ((d + startEmpty) % 7 === 1);
        
        const btn = document.createElement('button');
        btn.className = `btn-date ${isHoliday || isSun ? 'is-holiday' : ''}`;

        // OTOMATIS DISABLE: Jika tanggal lebih kecil dari hari ini
        if (loopDate < yesterday) {
            btn.disabled = true;
        }

        // Contoh status terpilih awal (bisa kamu sesuaikan)
        if(monthIndex === 3 && (d === 19 || d === 20)) btn.classList.add('selected');

        // Harga Acak
        const price = (Math.random() * (600 - 350) + 350).toFixed(1);
        const isLow = price < 400;

        btn.innerHTML = `
            ${isToday ? '<span class="today-label">Today</span>' : ''}
            <span class="num">${d}</span>
            <span class="price ${isLow ? 'price-low' : 'price-normal'}">${price}K</span>
        `;

        btn.onclick = () => {
            document.querySelectorAll('.btn-date').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            document.getElementById('display-date').innerText = `${d} ${monthLabel} ${year} (1 night)`;
        };
        grid.appendChild(btn);
    }
}

// CARA PANGGIL (Sekarang menggunakan Index Bulan JS: Jan=0, Feb=1, Mar=2, Apr=3, May=4)
// April 2026: Index 3
populateGrid('april-grid', 3, 30, 3, 'Apr', 2026);

// Mei 2026: Index 4
populateGrid('may-grid', 5, 31, 4, 'May', 2026);


    // --- LOGIKA GUEST STEPPER ---
    let guests = { room: 1, adult: 1, child: 0 };

    function updateGuest(type, change) {
        const newVal = guests[type] + change;
        if(type === 'child' && newVal < 0) return;
        if(type !== 'child' && newVal < 1) return;
        
        guests[type] = newVal;
        document.getElementById(`${type}-val`).innerText = newVal;
        
        // Update tampilan utama
        document.getElementById('display-guests').innerText = 
            `${guests.room} Room, ${guests.adult} Adult, ${guests.child} Children`;
            
        // Disable tombol jika minimal
        document.getElementById(`${type}-minus`).disabled = (type === 'child' ? newVal === 0 : newVal === 1);
    }

    function changeActiveMenu() {
        let currentSection = "";

        sections.forEach((section) => {
            const sectionTop = section.offsetTop;
            // Jika scroll sudah melewati posisi section (dikurangi sedikit offset agar lebih responsif)
            if (window.pageYOffset >= sectionTop - 150) {
                currentSection = section.getAttribute("id");
            }
        });

        navLinks.forEach((link) => {
            // Reset semua link ke gaya 'muted'
            link.classList.remove('active', 'text-primary', 'fw-bold', 'border-bottom', 'border-primary', 'border-3');
            link.classList.add('text-muted');

            // Jika href link sesuai dengan section yang sedang aktif
            if (link.getAttribute("href") === `#${currentSection}`) {
                link.classList.add('active', 'text-primary', 'fw-bold', 'border-bottom', 'border-primary', 'border-3');
                link.classList.remove('text-muted');
            }
        });
    }

    window.addEventListener("scroll", changeActiveMenu);
});
</script>
@endsection