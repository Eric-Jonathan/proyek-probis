@extends('layout.layout')

@section('custom_css')
    <style>
        .hotel-card {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
            transition: box-shadow 0.3s;
            cursor: pointer;
        }
        .hotel-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .badge-filter {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid #dee2e6;
            background: white;
            color: #495057;
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-block;
            margin: 2px;
        }
        .badge-filter.active {
            background-color: #e7f1ff;
            border-color: #0d6efd;
            color: #0d6efd;
        }
        .price-text {
            color: #ff5e1f;
            font-weight: 700;
            font-size: 1.25rem;
        }
        .tax-text {
            font-size: 0.75rem;
            color: #6c757d;
        }
        .hotel-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .sidebar-section {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .search-wrapper {
            background: #fff;
            border-radius: 50px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 8px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .search-item {
            padding: 5px 15px;
            border-right: 1px solid #eee;
        }

        /* Hilangkan border kanan di item terakhir dan di mobile */
        .search-item:last-child {
            border-right: none;
        }

        .search-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #717171;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .search-input-custom {
            border: none;
            padding: 0;
            font-size: 0.9rem;
            font-weight: 500;
            width: 100%;
            outline: none;
            color: #222;
            background: transparent;
        }

        .btn-search-round {
            border-radius: 50% !important;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Container gambar untuk memastikan aspect ratio tetap terjaga */
        .room-img-container {
            width: 100%;
            height: 250px; /* Tinggi paten untuk desktop */
            overflow: hidden;
        }

        .room-img {
            width: 100%;
            height: 100%;
            /* KUNCI UTAMA: gambar akan menutupi area tanpa distorsi */
            object-fit: cover; 
            /* Menjaga fokus gambar tetap di tengah */
            object-position: center; 
        }

        /* Responsive: Di mobile (layar kecil), tinggi gambar disesuaikan */
        @media (max-width: 767.98px) {
            .room-img-container {
                height: 200px; /* Lebih pendek sedikit untuk layar HP */
            }
        }

        /* RESPONSIVE BREAKPOINT */
        @media (max-width: 991.98px) {
            .search-wrapper {
                border-radius: 15px; /* Kurangi roundness di mobile */
                flex-direction: column; /* Tumpuk ke bawah */
                padding: 15px;
            }
            
            .search-item {
                border-right: none;
                border-bottom: 1px solid #eee; /* Ganti border ke bawah */
                width: 100% !important;
                padding: 10px 0;
            }

            .search-item:last-of-type {
                border-bottom: none;
            }

            .btn-search-round {
                border-radius: 10px !important; /* Kotak sedikit tumpul di mobile */
                width: 100%;
                margin-top: 10px;
            }
            
            /* Merapikan input date di mobile agar tidak berantakan */
            .date-container {
                flex-direction: row;
                justify-content: space-between;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="">
            <div class="container mb-5 mt-4">
                <div class="row justify-content-center">
                    <div class="col-lg-11 col-xl-10">
                        <div class="search-wrapper d-lg-flex align-items-center">
                            
                            <div class="search-item flex-grow-1">
                                <label class="search-label">Location</label>
                                <div class="d-flex align-items-center mt-1">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    <input type="text" class="search-input-custom" placeholder="Where are you going?" value="">
                                </div>
                            </div>

                            <div class="search-item" style="flex: 1.5;">
                                <label class="search-label">Check-in - Check-out</label>
                                <div class="d-flex align-items-center mt-1 date-container">
                                    <input type="date" class="search-input-custom" value="2026-04-18">
                                    <span class="mx-2 text-muted fw-light">|</span>
                                    <input type="date" class="search-input-custom" value="2026-04-20">
                                </div>
                            </div>

                            <div class="search-item" style="flex: 1.5;">
                                <label class="search-label">Guests</label>
                                <div class="d-flex align-items-center mt-1">
                                    <i class="bi bi-people text-primary me-2"></i>
                                    <input type="text" class="search-input-custom" value="20">
                                    <span class="fw-semibold fs-6">People</span>
                                </div>
                            </div>

                            <div class="ps-lg-2 w-100-mobile">
                                <button class="btn btn-primary btn-search-round shadow-sm w-100-mobile">
                                    <i class="bi bi-search d-none d-lg-inline"></i>
                                    <span class="d-lg-none fw-bold">Search Now</span> </button>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <span class="me-2 text-muted small">Sort by:</span>
                <span class="badge-filter active">Recommended</span>
                <span class="badge-filter">Highest price</span>
                <span class="badge-filter">Highest star</span>
                <span class="badge-filter">Highest rating</span>
                <span class="badge-filter">Lowest price</span>
            </div>

            <div class="card hotel-card">
                <div class="row g-0">
                    <div class="col-md-4 position-relative">
                        <img src="{{ asset('upload_room/great_diponegoro.jpg') }}" class="room-img" alt="Room Image">
                        <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-2 rounded-circle btn-favorite" data-id="1">
                            <i class="bi bi-heart-fill text-danger"></i>
                        </button>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body h-100 d-flex flex-column">
                            <div class="row">
                                <div class="col-8">
                                    <h5 class="card-title fw-bold mb-1">Great Diponegoro Ballroom</h5>
                                    <div class="text-warning mb-1">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                        <span class="text-muted small">Diponegoro, Surabaya</span>
                                    </div>
                                    <p class="text-primary small mb-2 fw-bold">Capacity : Max 20 people</p>
                                    <p class="text-success small mb-0">Speaker, Microphone, Free Snack, Free Wifi</p>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="fw-bold">4.5/5 <span class="fw-normal text-muted small">(1,238)</span></div>
                                </div>
                            </div>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-end">
                                <div class="small text-primary"></div>
                                <div class="text-end">
                                    <div class="price-text">IDR 300,000 <span class="text-muted fw-light">/pax</span></div>
                                    {{-- <div class="tax-text">(after taxes: IDR 1,932,380)</div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card hotel-card">
                <div class="row g-0">
                    <div class="col-md-4 position-relative">
                        <img src="{{ asset('upload_room/bg_junction.jpg') }}" class="room-img" alt="Room Image">
                        <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-2 rounded-circle btn-favorite" data-id="2">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body h-100 d-flex flex-column">
                            <div class="row">
                                <div class="col-8">
                                    <h5 class="card-title fw-bold mb-1">BG Junction Ballroom</h5>
                                    <div class="text-warning mb-1">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                        <span class="text-muted small">Bubutan, Surabaya</span>
                                    </div>
                                    <p class="text-primary small mb-2 fw-bold">Capacity : Max 50 people</p>
                                    <p class="text-success small mb-0">Speaker, Microphone, Free Wifi</p>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="fw-bold">4.7/5 <span class="fw-normal text-muted small">(996)</span></div>
                                </div>
                            </div>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-end">
                                <div class="small text-primary"></div>
                                <div class="text-end">
                                    <div class="price-text">IDR 200,000 <span class="text-muted fw-light">/pax</span></div>
                                    {{-- <div class="tax-text">(after taxes: IDR 1,932,380)</div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
    <script src="{{ asset('custom_js/rooms/search_room.js') }}"></script>
@endsection