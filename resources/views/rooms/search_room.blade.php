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
                        <form action="{{ route('penyewa.search') }}" method="GET">
                            {{-- hidden sort parameter so sorting is preserved on search submissions --}}
                            <input type="hidden" name="sort" value="{{ request('sort', 'recommended') }}">
                            
                            <div class="search-wrapper d-lg-flex align-items-center">
                                
                                <div class="search-item flex-grow-1">
                                    <label class="search-label">Location</label>
                                    <div class="d-flex align-items-center mt-1">
                                        <i class="bi bi-geo-alt text-primary me-2"></i>
                                        <input type="text" name="location" class="search-input-custom" placeholder="Where are you going?" value="{{ request('location') }}">
                                    </div>
                                </div>

                                <div class="search-item" style="flex: 1.5;">
                                    <label class="search-label">Check-in - Check-out</label>
                                    <div class="d-flex align-items-center mt-1 date-container">
                                        <input type="date" name="start_date" class="search-input-custom" value="{{ request('start_date', '2026-04-18') }}">
                                        <span class="mx-2 text-muted fw-light">|</span>
                                        <input type="date" name="end_date" class="search-input-custom" value="{{ request('end_date', '2026-04-20') }}">
                                    </div>
                                </div>

                                <div class="search-item" style="flex: 1.5;">
                                    <label class="search-label">Guests</label>
                                    <div class="d-flex align-items-center mt-1">
                                        <i class="bi bi-people text-primary me-2"></i>
                                        <input type="number" name="capacity" class="search-input-custom" value="{{ request('capacity', 20) }}" style="max-width: 80px;">
                                        <span class="fw-semibold fs-6 ms-1">People</span>
                                    </div>
                                </div>

                                <div class="ps-lg-2 w-100-mobile">
                                    <button type="submit" class="btn btn-primary btn-search-round shadow-sm w-100-mobile">
                                        <i class="bi bi-search d-none d-lg-inline"></i>
                                        <span class="d-lg-none fw-bold">Search Now</span>
                                    </button>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <span class="me-2 text-muted small">Sort by:</span>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'recommended']) }}" class="badge-filter {{ request('sort', 'recommended') == 'recommended' ? 'active' : '' }} text-decoration-none">Recommended</a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'highest_price']) }}" class="badge-filter {{ request('sort') == 'highest_price' ? 'active' : '' }} text-decoration-none">Highest price</a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'highest_rating']) }}" class="badge-filter {{ in_array(request('sort'), ['highest_rating', 'highest_star']) ? 'active' : '' }} text-decoration-none">Highest rating</a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'lowest_price']) }}" class="badge-filter {{ request('sort') == 'lowest_price' ? 'active' : '' }} text-decoration-none">Lowest price</a>
            </div>

            @forelse($rooms as $room)
            <div class="card hotel-card" data-id="{{ $room->room_id }}">
                <div class="row g-0">
                    <div class="col-md-4 position-relative">
                        <div class="room-img-container">
                            <img src="{{ $room->images->isNotEmpty() ? asset($room->images->first()->path) : 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=600&q=80' }}" class="room-img" alt="{{ $room->name }}">
                        </div>
                        <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-2 rounded-circle btn-favorite" data-id="{{ $room->room_id }}">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body h-100 d-flex flex-column">
                            <div class="row">
                                <div class="col-8">
                                    <h5 class="card-title fw-bold mb-1 text-dark">{{ $room->name }}</h5>
                                    
                                    {{-- Dynamic star rating display --}}
                                    <div class="text-warning mb-1" style="font-size: 0.9rem;">
                                        @php
                                            $fullStars = floor($room->average_rating);
                                            $hasHalf = ($room->average_rating - $fullStars) >= 0.5;
                                            $emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);
                                        @endphp
                                        @for($i = 0; $i < $fullStars; $i++)
                                            <i class="bi bi-star-fill"></i>
                                        @endfor
                                        @if($hasHalf)
                                            <i class="bi bi-star-half"></i>
                                        @endif
                                        @for($i = 0; $i < $emptyStars; $i++)
                                            <i class="bi bi-star"></i>
                                        @endfor
                                        
                                        <span class="text-secondary small ms-2"><i class="bi bi-geo-alt-fill text-danger"></i> {{ \Illuminate\Support\Str::limit($room->location, 35) }}</span>
                                    </div>
                                    
                                    <p class="text-primary small mb-2 fw-bold">Capacity : Max {{ $room->capacity }} people</p>
                                    
                                    @if($room->facilities->isNotEmpty())
                                        <p class="text-success small mb-0 fw-semibold">
                                            {{ implode(', ', $room->facilities->pluck('name')->toArray()) }}
                                        </p>
                                    @else
                                        <p class="text-muted small mb-0 italic">Tidak ada informasi fasilitas</p>
                                    @endif
                                </div>
                                <div class="col-4 text-end">
                                    <div class="fw-bold fs-6">
                                        {{ number_format($room->average_rating, 1) }}/5 
                                        <span class="fw-normal text-muted small" style="font-size: 0.75rem;">({{ $room->rating_count }})</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-end">
                                <div class="small text-primary"></div>
                                <div class="text-end">
                                    <div class="price-text">IDR {{ number_format($room->price, 0, ',', '.') }} <span class="text-muted fw-light">/{{ str_replace('/', '', $room->jenis_harga) }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="card p-5 text-center text-muted border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <i class="bi bi-house-exclamation fs-1 d-block mb-3 text-secondary"></i>
                    <h5 class="fw-bold text-dark">Tidak Ada Ruangan Ditemukan</h5>
                    <p class="small text-secondary mb-0">Coba gunakan kata kunci pencarian atau filter kapasitas yang lain.</p>
                    @if(request()->filled('location') || request()->filled('capacity') || (request()->filled('sort') && request('sort') !== 'recommended'))
                        <a href="{{ route('penyewa.search') }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold mt-3 shadow-sm">Reset Filter</a>
                    @endif
                </div>
            </div>
            @endforelse
        </div>
    </div>
@endsection

@section('custom_js')
    <script src="{{ asset('custom_js/rooms/search_room.js') }}"></script>
@endsection