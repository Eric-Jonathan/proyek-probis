@extends('layout.layout')
{{-- belum ganti bagian navbar (selected nya) --}}
@section('content')
<body class="bg-white">
    <div class="sticky-top bg-white border-bottom shadow-sm" style="top: 0; z-index: 1020;"> 
            <div class="container">
                <ul class="nav nav-tabs border-0 mb-0 text-nowrap flex-nowrap overflow-auto py-2">
                    <li class="nav-item">
                        <a class="nav-link active text-primary fw-bold border-0 border-bottom border-primary border-3" href="#">Info Umum</a> 
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
                <del class="text-muted small">IDR 359.503</del>
                <h3 class="text-danger fw-bold mb-1">IDR 289.315</h3>
                <p class="text-muted x-small mb-3">/hari</p>
                <button class="btn btn-primary px-4 fw-bold shadow-sm">Lihat Ruangan</button>
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

</body>
@endsection