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
    section,
    .container[id] {
        scroll-margin-top: 140px;
        /* Jarak total navbar utama + navbar sticky */
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

    .calendar-day-head.sun {
        color: var(--holiday-red);
    }

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

    .btn-date:hover:not(:disabled) {
        background-color: #f0f0f0;
    }

    .btn-date:disabled {
    cursor: default;
    background: transparent !important;
}

    .btn-date:disabled .num,
    .btn-date:disabled .price {
        color: #ccc !important; 
    }

    .btn-date:disabled .today-label {
        color: var(--primary-blue);
    }

    .btn-date .num {
        font-weight: 600;
        font-size: 1rem;
        color: #444;
    }

    .btn-date .price {
        font-size: 0.65rem;
        font-weight: 600;
    }

    .btn-date.selected {
        background-color: var(--primary-blue) !important;
        color: white !important;
    }

    .btn-date.selected .num,
    .btn-date.selected .price {
        color: white !important;
    }

    .price-low {
        color: var(--success-green);
    }

    .price-normal {
        color: #777;
    }

    .is-holiday .num {
        color: var(--holiday-red);
    }

    .today-label {
        position: absolute;
        top: -25px;         /* Mengangkat label keluar dari blok biru */
        left: 50%;
        transform: translateX(-50%); /* Menengahkannya secara presisi */
        font-size: 0.8rem;  /* Sesuaikan ukuran font sesuai gambar */
        color: var(--primary-blue); 
        font-weight: bold; 
        white-space: nowrap;
    }

    /* --- GUEST MODAL STYLE --- */
    .guest-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.2rem 0;
        border-bottom: 1px solid #eee;
    }

    .guest-row:last-child {
        border-bottom: none;
    }

    .btn-stepper {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1px solid var(--primary-blue);
        background: white;
        color: var(--primary-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-stepper:disabled {
        border-color: #ccc;
        color: #ccc;
        cursor: not-allowed;
    }

    .stepper-value {
        font-weight: bold;
        width: 30px;
        text-align: center;
    }

    .btn-date:hover:not(:disabled):not(.selected) {
        background-color: #f0f0f0;
    }

    .gallery-item {
        cursor: pointer;
        background-color: #000;
    }

    .gallery-img {
        transition: opacity 0.3s ease;
        display: block;
    }

    /* Saat pembungkus dihover, gambar jadi sedikit gelap */
    .gallery-item:hover .gallery-img {
        opacity: 0.7;
        /* Menghasilkan efek gelap dari background parent */
    }

    /* Styling tombol agar tetap tajam dan konsisten */
    .gallery-item .btn {
        z-index: 2;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }

    /* Mengatur overlay agar menutupi seluruh gambar (secara default tersembunyi) */
    .gallery-item .overlay-view {
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0);
        /* Awalnya transparan */
        transition: all 0.3s ease;
        /* Efek transisi halus */
        opacity: 0;
        /* Awalnya tidak terlihat */
    }

    /* Menampilkan overlay dan tombol SAAT DIHOVER */
    .gallery-item:hover .overlay-view {
        background-color: rgba(0, 0, 0, 0.4);
        /* Overlay gelap saat hover */
        opacity: 1;
        /* Menampilkan overlay */
    }

    /* Memastikan tombol tidak bergerak-gerak */
    .gallery-item:hover .overlay-view .btn {
        transform: none;
    }

    #galleryModal .modal-dialog {
    max-width: 95%; /* Modal hampir full screen */
    margin: 20px auto;
}

#galleryModal .modal-content {
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

/* Bagian Kiri (Grid Foto) */
.gallery-sidebar {
    background-color: #fff;
    padding: 20px;
    height: 90vh; /* Tinggi mengikuti layar */
    overflow-y: auto; /* Bisa di-scroll */
}

.btn-close-white {
    background-color: rgba(0, 0, 0, 0.5) !important;
    padding: 10px;
    border-radius: 50%;
    opacity: 1; /* Agar tidak transparan bawaan bootstrap */
}

#btn-wishlist {
    transition: transform 0.2s ease;
    z-index: 10; /* Pastikan berada di atas gambar */
}

.btn-wishlist-float {
    position: absolute !important;
    top: 10px; /* Jarak dari atas gambar */
    right: 18px; /* Jarak dari kanan gambar */
    width: 38px;
    height: 38px;
    background-color: white !important; /* Putih pekat */
    border-radius: 50% !important; /* Membuat bulat sempurna */
    border: none !important;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15) !important; /* Bayangan halus */
    z-index: 10;
    transition: all 0.2s ease;
    padding: 0;
}

.btn-wishlist-float i {
    font-size: 1.2rem;
    color: #555; /* Warna ikon default */
    line-height: 0;
}

.btn-wishlist-float:hover {
    transform: scale(1.1);
}

/* Warna saat Hati Aktif (Merah) */
.btn-wishlist-float i.text-danger {
    color: #ff385c !important; /* Warna merah khas travel app */
}

#btn-wishlist:active {
    transform: scale(0.85); /* Efek mengecil sebentar saat ditekan */
}

#icon-wishlist {
    transition: color 0.2s ease;
}

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

.nav-arrow:hover {
    background: rgba(0, 0, 0, 0.8) !important;
    transform: translateY(-50%) scale(1.1);
}

.nav-arrow.prev { left: 20px; }
.nav-arrow.next { right: 20px; }

/* Styling Tab Nav */
.nav-pills .nav-link { color: #666; }
.nav-pills .nav-link.active { background-color: #fff; color: #000; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

.gallery-grid-item.active { border: 3px solid #006ce4; }

.gallery-grid-item {
    cursor: pointer;
    transition: opacity 0.2s;
    aspect-ratio: 1/1; /* Membuat foto kotak */
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
    transition: 0.2s; 
}

.gallery-grid-item:hover {
    opacity: 0.8;
}

.gallery-grid-item.active {
    border: 3px solid var(--primary-blue); /* Tanda foto yang sedang dipilih */
}

/* Bagian Kanan (Foto Besar) */
.gallery-main-view {
    background-color: #1a1a1a; /* Background gelap */
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 0;
}

.main-img-full {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain; /* Memastikan foto utuh terlihat */
}

.gallery-info-overlay {
    position: absolute;
    bottom: 20px;
    left: 20px;
    color: white;
    text-shadow: 0 1px 4px rgba(0,0,0,0.5);
}

#galleryModal .btn-close-white {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 10;
}

    .custom-btn-opacity {
        /* Nilai 0.6 untuk opacity yang lebih rendah/transparan */
        background-color: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(4px);
        /* border: 1px solid rgba(255, 255, 255, 0.3) !important; Border tipis agar lebih tegas */
        /* transition: all 0.3s ease; */
        /* font-size: 0.85rem; */
        font-weight: 500;
        /* Opsional: memberikan efek blur pada gambar di belakang tombol */
        transition: background-color 0.3s ease;
    
    /* Membuat sudut melengkung seperti di gambar */
    border-radius: 12px !important; 
    
    /* Menghilangkan border default */
    border: none !important;
    
    /* Ukuran font sedikit lebih kecil agar elegan */
    font-size: 0.85rem;
    font-weight: 500;
    
    /* Efek transisi halus saat disentuh */
    transition: background-color 0.3s ease;
    
    /* Agar teks tidak pecah */
    white-space: nowrap;
    }
.custom-btn-opacity:focus {
    box-shadow: none !important;
}
    

    /* Efek saat tombol itu sendiri di-hover */
    .custom-btn-opacity:hover {
        background-color: rgba(0, 0, 0, 0.8) !important;
        color: white;
        transform: scale(1.05);
    }

    .bi-heart-fill {
        color: #ff5e5e; /* Warna merah */
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

    <div id="section-info" class="container py-3">
    <div class="row g-2 mb-4">
        <div class="col-md-6">
            <div class="h-100">
                <img src="{{ asset('upload_room/tes/tes_1.webp') }}" class="img-fluid rounded object-fit-cover w-100" alt="Main Image" style="height: 408px;">
            </div>
        </div>

        <div class="col-md-6">
            <div class="row g-2">
                <div class="col-6">
                    <img src="{{ asset('upload_room/tes/tes_2.webp') }}" class="img-fluid rounded object-fit-cover w-100" style="height: 200px;" alt="Gallery 1">
                </div>
                <div class="col-6 position-relative">
    <img src="{{ asset('upload_room/tes/tes_3.webp') }}" class="img-fluid rounded object-fit-cover w-100" style="height: 200px;" alt="Gallery 2">
    
    <button type="button" class="btn btn-wishlist-float" id="btn-wishlist">
        <i class="bi bi-heart" id="icon-wishlist"></i>
    </button>
</div>
                <div class="col-6">
                    <img src="{{ asset('upload_room/tes/tes_4.webp') }}" class="img-fluid rounded object-fit-cover w-100" style="height: 200px;" alt="Gallery 3">
                </div>
                <div class="col-6">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow-sm h-100">
                        <img src="{{ asset('upload_room/tes/tes_5.webp') }}" class="img-fluid object-fit-cover w-100 gallery-img" style="height: 200px;" alt="Gallery Room">
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
                <span class="badge border text-muted fw-normal px-2 py-1" style="font-size: 0.75rem;">Hotel</span>
                <div class="text-warning small d-flex gap-1">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                </div>
            </div>

            <h2 class="fw-bold mb-1" style="font-size: 2.2rem; letter-spacing: -0.5px;">{{ $room->name }}</h2>

            <div class="d-flex align-items-center gap-2 small">
                <span class="fw-bold">4,5<span class="text-muted fw-normal">/5</span></span>
                <a href="#" class="text-decoration-none fw-semibold" style="color: #006ce4;">(3.972 review)</a>
                <span class="text-muted mx-1">•</span>
                <a href="#" class="text-decoration-none fw-semibold" style="color: #006ce4;">Batu, Malang</a>
            </div>
        </div>

        <div class="col-md-4 text-md-end">
            <span class="badge bg-danger mb-2">Diskon 20%</span>
            <p class="text-muted small mb-0">Mulai dari (setelah cashback)</p>
            <del class="text-muted small">IDR {{ number_format($room->price, 0, ',', '.') }}</del>
            <h2 class="text-danger fw-bold mb-0">IDR {{ number_format($room->price * 0.8, 0, ',', '.') }}</h2>
            <p class="text-muted small mb-3">/hari</p>
            <a href="#section-rooms" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
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

            <div id="reviewSlider" class="d-flex overflow-auto gap-3 pb-3" style="scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth;">

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
                <i class="bi bi-wind me-3 fs-5"></i>
                <span class="small">AC</span>
            </div>
            <div class="d-flex align-items-center">
                <i class="bi bi-ev-front me-3 fs-5"></i> <span class="small">Lift</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-wifi me-3 fs-5"></i>
                <span class="small">WiFi</span>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-person-arms-up me-3 fs-5"></i> <span class="small">Spa</span>
            </div>
            <div class="d-flex align-items-center">
                <i class="bi bi-cup-hot me-3 fs-5"></i>
                <span class="small">Restoran</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-bicycle me-3 fs-5"></i>
                <span class="small">Pusat Kebugaran</span>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-bus-front me-3 fs-5"></i>
                <span class="small">Antar Jemput Bandara</span>
            </div>
            <div class="d-flex align-items-center">
                <i class="bi bi-house-heart me-3 fs-5"></i>
                <span class="small">Fasilitas Anak</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-leaf me-3 fs-5 text-success"></i>
                <span class="small">tiket Green</span>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-bell me-3 fs-5"></i>
                <span class="small">Resepsionis 24 Jam</span>
            </div>
            <div class="d-flex align-items-center">
                <i class="bi bi-tsunami me-3 fs-5"></i>
                <span class="small">Pemandangan Laut</span>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="#" class="text-primary text-decoration-none fw-bold small d-flex align-items-center" 
           data-bs-toggle="modal" data-bs-target="#modalFasilitas">
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
                        <iframe src={{ $room -> embed_url }} width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
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
                        <span id="display-date" class="fw-semibold small clickable-box p-2 rounded-3" data-bs-toggle="modal" data-bs-target="#datePickerModal">
                            Loading date...
                        </span>
                    </div>

                    {{-- <div class="vr mx-2" style="height: 25px; opacity: 0.1;"></div>

                    <div class="d-flex align-items-center">
                        <span id="display-guests" class="fw-semibold small clickable-box p-2 rounded-3" data-bs-toggle="modal" data-bs-target="#guestPickerModal">
                            1 Room, 1 Adult, 0 Children
                        </span>
                    </div> --}}
                </div>

                <a href="{{ route('booking.show') }}"  class="btn btn-success">
                    Book
                </a>
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
                            <div class="col-md-6 border-end position-relative">
                                <div class="d-flex justify-content-center align-items-center mb-4">
                                    <button type="button" class="btn btn-sm position-absolute start-0" id="prevMonthBtn">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <h5 id="labelMonthLeft" class="fw-bold mb-0"></h5>
                                </div>
                                <div class="calendar-grid" id="gridLeft"></div>
                            </div>
                        
                            <div class="col-md-6 position-relative">
                                <div class="d-flex justify-content-center align-items-center mb-4">
                                    <h5 id="labelMonthRight" class="fw-bold mb-0"></h5>
                                    <button type="button" class="btn btn-sm position-absolute end-0" id="nextMonthBtn">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                                <div class="calendar-grid" id="gridRight"></div>
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

        <div class="modal fade" id="modalFasilitas" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content" style="border-radius: 12px;">
                    <div class="modal-header border-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">Fasilitas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 pb-4">

                        <div class="mb-5">
                            <h6 class="fw-bold mb-3">Fasilitas Umum <span class="badge bg-light text-muted ms-1">8</span></h6>
                            <div class="row g-3 text-muted small">
                                <div class="col-md-3">ATM/Bank</div>
                                <div class="col-md-3">Kolam Renang</div>
                                <div class="col-md-3">AC</div>
                                <div class="col-md-3">Lift</div>
                                <div class="col-md-3">WiFi Publik</div>
                                <div class="col-md-3">Restoran</div>
                                <div class="col-md-3">Pantai</div>
                                <div class="col-md-3">Parkir (Gratis)</div>
                            </div>
                        </div>
                    
                        <hr class="opacity-10">
                    
                        <div class="mb-5">
                            <h6 class="fw-bold mb-3">Olahraga & Rekreasi <span class="badge bg-light text-muted ms-1">3</span></h6>
                            <div class="row g-3 text-muted small">
                                <div class="col-md-3">Fasilitas Gym</div>
                                <div class="col-md-3">Taman</div>
                                <div class="col-md-3">Kolam Renang Anak</div>
                            </div>
                        </div>
                    
                        <hr class="opacity-10">
                    
                        <div class="mb-5">
                            <h6 class="fw-bold mb-3">Layanan Hotel <span class="badge bg-light text-muted ms-1">8</span></h6>
                            <div class="row g-3 text-muted small">
                                <div class="col-md-3">Layanan Pernikahan</div>
                                <div class="col-md-3">Resepsionis 24 Jam</div>
                                <div class="col-md-3">Staf Multibahasa</div>
                                <div class="col-md-3">Layanan Concierge</div>
                                <div class="col-md-3">Penitipan Bagasi</div>
                                <div class="col-md-3">Tur</div>
                                <div class="col-md-3">Layanan Laundry/Dry Cleaning</div>
                                <div class="col-md-3">Brankas Hotel</div>
                            </div>
                        </div>
                    
                        <hr class="opacity-10">
                    
                        <div class="mb-5">
                            <h6 class="fw-bold mb-3">Transportasi <span class="badge bg-light text-muted ms-1">5</span></h6>
                            <div class="row g-3 text-muted small">
                                <div class="col-md-3">Antar/Jemput Bandara (Biaya Tambahan)</div>
                                <div class="col-md-3">Penyewaan Mobil</div>
                                <div class="col-md-3">Pemesanan Tur/Tiket</div>
                                <div class="col-md-3">Layanan Taksi</div>
                                <div class="col-md-3">Parkir Valet (Gratis)</div>
                            </div>
                        </div>
                    
                        <div class="mb-5">
                            <h6 class="fw-bold mb-3">Fasilitas Bisnis <span class="badge bg-light text-muted ms-1">6</span></h6>
                            <div class="row g-3 text-muted small">
                                <div class="col-md-3">Pusat Bisnis</div>
                                <div class="col-md-3">Toko Souvenir/Kios Koran</div>
                                <div class="col-md-3">Ruang Rapat</div>
                                <div class="col-md-3">Mesin Foto Kopi</div>
                                <div class="col-md-3">Ruang Konferensi</div>
                                <div class="col-md-3">Proyektor</div>
                            </div>
                        </div>
                    
                        <div class="mb-5">
                            <h6 class="fw-bold mb-3">Kesehatan & Kecantikan <span class="badge bg-light text-muted ms-1">4</span></h6>
                            <div class="row g-3 text-muted small">
                                <div class="col-md-3">Salon</div>
                                <div class="col-md-3">Sauna</div>
                                <div class="col-md-3">Spa</div>
                                <div class="col-md-3">Pijat</div>
                            </div>
                        </div>
                    
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal" style="border-radius: 8px;">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0">
            <div class="row g-0">
                
                <div class="col-md-4 col-lg-3 gallery-sidebar bg-white" style="height: 90vh; overflow-y: auto;">
                    <div class="p-3">
                        <h5 class="fw-bold mb-3">Galeri</h5>
                        
                        <ul class="nav nav-pills nav-fill bg-light rounded-3 p-1 mb-4" id="galleryTab">
                            <li class="nav-item">
                                <button class="nav-link active small fw-bold" id="tab-akomodasi" data-bs-toggle="pill" data-bs-target="#content-akomodasi">Dari Akomodasi</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link small fw-bold" id="tab-tamu" data-bs-toggle="pill" data-bs-target="#content-tamu">Dari Tamu</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="content-akomodasi">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <img src="{{ asset('upload_room/tes/tes_10.jpg') }}" class="gallery-grid-item item-akomodasi img-fluid active" data-category="akomodasi">
                                    </div>
                                    <div class="col-6">
                                        <img src="{{ asset('upload_room/tes/tes_11.jpg') }}" class="gallery-grid-item item-akomodasi img-fluid" data-category="akomodasi">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="content-tamu">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <img src="{{ asset('upload_room/tes/user_tes_1.jpg') }}" class="gallery-grid-item item-tamu img-fluid" data-category="tamu">
                                    </div>
                                    <div class="col-6">
                                        <img src="{{ asset('upload_room/tes/user_tes_2.jpg') }}" class="gallery-grid-item item-tamu img-fluid" data-category="tamu">
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                </div>

                <div class="col-md-8 col-lg-9 gallery-main-view bg-dark position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                    
                    <button class="nav-arrow prev" id="prevPhoto"><i class="bi bi-chevron-left"></i></button>
                    <button class="nav-arrow next" id="nextPhoto"><i class="bi bi-chevron-right"></i></button>
                    
                    <img src="{{ asset('upload_room/tes_1.jpg') }}" id="mainGalleryImage" class="main-img-full w-100 h-100 object-fit-contain">
                    
                    <div class="gallery-info-overlay position-absolute bottom-0 start-0 p-4">
                        <p class="mb-0 small text-white-50" id="photoCounter">1/2</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('#main-nav .nav-link');
        const sections = document.querySelectorAll('#section-info, #section-review, #section-fasilitas, #section-lokasi, #section-tentang');

        let today = new Date();
        let startDate = null;
        let endDate = null;
        let currentViewDate = new Date(today.getFullYear(), today.getMonth(), 1);

        const options = { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' };

        // Fungsi untuk memperbarui teks tanggal di atas (Display Date)
        function updateDisplayDate() {
            const displayElement = document.getElementById('display-date');
            if (startDate && !endDate) {
                displayElement.innerText = startDate.toLocaleDateString('id-ID', options);
            } else if (startDate && endDate) {
                const startStr = startDate.toLocaleDateString('id-ID', options);
                const endStr = endDate.toLocaleDateString('id-ID', options);
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                displayElement.innerText = `${startStr} - ${endStr} (${diffDays} hari)`;
            } else {
                displayElement.innerText = today.toLocaleDateString('id-ID', options);
            }
        }

        function renderSingleMonth(gridId, labelId, dateObj) {
            const grid = document.getElementById(gridId);
            const label = document.getElementById(labelId);
            const year = dateObj.getFullYear();
            const month = dateObj.getMonth();

            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", 
                                "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            label.innerText = `${monthNames[month]} ${year}`;

            const daysHeader = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            grid.innerHTML = '';
            daysHeader.forEach((day, index) => {
                const div = document.createElement('div');
                div.className = `calendar-day-head ${index === 0 ? 'sun' : ''}`;
                div.innerText = day;
                grid.appendChild(div);
            });

            const firstDay = new Date(year, month, 1).getDay();
            const totalDays = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) grid.appendChild(document.createElement('div'));

            const now = new Date();
            const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());

            for (let d = 1; d <= totalDays; d++) {
                const loopDate = new Date(year, month, d);
                const isToday = (d === now.getDate() && month === now.getMonth() && year === now.getFullYear());
                const isSun = (loopDate.getDay() === 0);

                const btn = document.createElement('button');
                btn.className = `btn-date ${isSun ? 'is-holiday' : ''}`;

                if (loopDate < startOfToday) {
                    btn.disabled = true;
                }

                // --- LOGIKA VISUAL SELECTION ---
                // Cek apakah tanggal ini harus berwarna biru
                if (startDate && !endDate) {
                    if (loopDate.getTime() === startDate.getTime()) btn.classList.add('selected');
                } else if (startDate && endDate) {
                    if (loopDate >= startDate && loopDate <= endDate) btn.classList.add('selected');
                }

                const price = (Math.random() * (9 - 4) + 4).toFixed(1);

                btn.innerHTML = `
                    ${isToday ? '<span class="today-label">Hari Ini</span>' : ''}
                    <span class="num">${d}</span>
                    <span class="price">${price}jt</span>
                `;

                btn.onclick = () => {
                    const clickedDate = new Date(year, month, d);

                    if (!startDate || (startDate && endDate)) {
                        // Reset dan pilih tanggal pertama
                        startDate = clickedDate;
                        endDate = null;
                    } else if (startDate && !endDate) {
                        if (clickedDate.getTime() === startDate.getTime()) {
                            // Cancel jika klik tanggal yang sama
                            startDate = null;
                        } else if (clickedDate < startDate) {
                            // Jika klik tanggal yang lebih lampau, geser startDate
                            startDate = clickedDate;
                        } else {
                            // Pilih tanggal kedua (Range terbentuk)
                            endDate = clickedDate;
                        }
                    } else if (startDate && endDate && clickedDate.getTime() === endDate.getTime()) {
                        // Fitur Cancel End Date: Jika klik lagi tgl akhir, balik ke single date
                        endDate = null;
                    }

                    updateDisplayDate();
                    renderDoubleCalendar(); // Render ulang agar semua grid terupdate warnanya
                };

                grid.appendChild(btn);
            }
        }

        function renderDoubleCalendar() {
            renderSingleMonth('gridLeft', 'labelMonthLeft', new Date(currentViewDate));
            let nextMonthDate = new Date(currentViewDate);
            nextMonthDate.setMonth(nextMonthDate.getMonth() + 1);
            renderSingleMonth('gridRight', 'labelMonthRight', nextMonthDate);

            const prevBtn = document.getElementById('prevMonthBtn');
            if (currentViewDate.getMonth() === today.getMonth() && currentViewDate.getFullYear() === today.getFullYear()) {
                prevBtn.style.visibility = 'hidden';
            } else {
                prevBtn.style.visibility = 'visible';
            }
        }

        document.getElementById('nextMonthBtn').onclick = () => {
            currentViewDate.setMonth(currentViewDate.getMonth() + 1);
            renderDoubleCalendar();
        };

        document.getElementById('prevMonthBtn').onclick = () => {
            currentViewDate.setMonth(currentViewDate.getMonth() - 1);
            renderDoubleCalendar();
        };

        // Inisialisasi awal
        updateDisplayDate();
        renderDoubleCalendar();

        const mainImage = document.getElementById('mainGalleryImage');
    const photoCounter = document.getElementById('photoCounter');
    
    let currentCategory = 'akomodasi'; // Default kategori awal
    let photoList = [];
    let currentIndex = 0;

    // Fungsi untuk memperbarui daftar foto berdasarkan kategori yang aktif
    function refreshGallery(category) {
        currentCategory = category;
        // Ambil hanya foto yang memiliki class kategori tersebut
        const filteredImgs = document.querySelectorAll(`.item-${category}`);
        photoList = Array.from(filteredImgs).map(img => img.src);
        
        // Reset index ke 0 setiap kali pindah tab
        updateView(0);
    }

    function updateView(index) {
        if (photoList.length === 0) return;
        
        currentIndex = index;
        mainImage.src = photoList[currentIndex];
        photoCounter.innerText = `${currentIndex + 1}/${photoList.length}`;

        // Beri border biru hanya pada foto yang aktif di grid
        document.querySelectorAll('.gallery-grid-item').forEach(img => img.classList.remove('active'));
        const activeGridImg = document.querySelector(`.item-${currentCategory}[src="${photoList[currentIndex]}"]`);
        if (activeGridImg) activeGridImg.classList.add('active');
    }

    // Listener saat Tab diklik
    document.getElementById('tab-akomodasi').addEventListener('click', () => refreshGallery('akomodasi'));
    document.getElementById('tab-tamu').addEventListener('click', () => refreshGallery('tamu'));

    // Klik pada foto di grid
    document.querySelectorAll('.gallery-grid-item').forEach((img) => {
        img.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            const src = this.getAttribute('src');
            const indexInFiltered = photoList.indexOf(src);
            updateView(indexInFiltered);
        });
    });

    // Navigasi Panah
    document.getElementById('nextPhoto').addEventListener('click', () => {
        let nextIndex = (currentIndex + 1) % photoList.length;
        updateView(nextIndex);
    });

    document.getElementById('prevPhoto').addEventListener('click', () => {
        let prevIndex = (currentIndex - 1 + photoList.length) % photoList.length;
        updateView(prevIndex);
    });

    // Jalankan inisialisasi pertama
    refreshGallery('akomodasi');

    const btn = document.getElementById('btn-wishlist');
    const icon = document.getElementById('icon-wishlist');

    if (btn) {
        btn.addEventListener('click', function() {
            // 1. Cek apakah sedang 'kosong'
            if (icon.classList.contains('bi-heart')) {
                // Ganti ke Hati Merah Terisi
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill', 'text-danger');
            } else {
                // Ganti ke Hati Kosong Biasa
                icon.classList.remove('bi-heart-fill', 'text-danger');
                icon.classList.add('bi-heart');
            }
        });
    }

        // --- LOGIKA GUEST STEPPER ---
        let guests = {
            room: 1,
            adult: 1,
            child: 0
        };

        window.updateGuest = function(type, change) {
            const newVal = guests[type] + change;
            if (type === 'child' && newVal < 0) return;
            if (type !== 'child' && newVal < 1) return;

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