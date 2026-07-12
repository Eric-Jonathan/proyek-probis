@extends('layout.layout')

@section('custom_css')
    <style>
        .hotel-card {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--bs-border-color);
            margin-bottom: 20px;
            transition: box-shadow 0.3s;
            cursor: pointer;
            background-color: var(--bs-card-bg);
        }
        .hotel-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .badge-filter {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid var(--bs-border-color);
            background-color: var(--bs-card-bg);
            color: var(--bs-body-color);
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-block;
            margin: 2px;
        }
        .badge-filter.active {
            background-color: var(--bs-primary-bg-subtle);
            border-color: var(--bs-primary);
            color: var(--bs-primary);
        }
        .price-text {
            color: #ff5e1f;
            font-weight: 700;
            font-size: 1.25rem;
        }
        .tax-text {
            font-size: 0.75rem;
            color: var(--bs-secondary-color);
        }
        .hotel-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .sidebar-section {
            border: 1px solid var(--bs-border-color);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .search-card {
            background-color: var(--bs-card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--bs-border-color);
            padding: 24px;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .filter-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--bs-secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .filter-input-group {
            background-color: var(--bs-tertiary-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 10px;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
            height: 45px;
        }

        .filter-input-group:focus-within {
            border-color: #0064D2;
            box-shadow: 0 0 0 3px rgba(0, 100, 210, 0.15);
            background-color: var(--bs-body-bg);
        }

        .filter-input-field {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--bs-body-color);
            padding: 0;
        }

        .filter-input-field::placeholder {
            color: #adb5bd;
        }

        .filter-input-field:focus {
            outline: none;
            box-shadow: none;
        }

        .filter-select {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--bs-body-color);
            padding: 0;
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .filter-select:focus {
            outline: none;
            box-shadow: none;
        }

        .btn-search-premium {
            background-color: #0064D2;
            border-color: #0064D2;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            height: 45px;
            transition: all 0.2s ease;
        }

        .btn-search-premium:hover {
            background-color: #0053b0;
            border-color: #0053b0;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 100, 210, 0.2);
        }

        .btn-search-premium:active {
            transform: translateY(0);
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
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="">
            <div class="container mb-5 mt-4">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="search-card shadow-sm">
                            <form action="{{ route('penyewa.search') }}" method="GET">
                                {{-- hidden sort parameter so sorting is preserved on search submissions --}}
                                <input type="hidden" name="sort" value="{{ request('sort', 'recommended') }}">
                                
                                <div class="row g-3">
                                    <!-- Lokasi / Nama Tempat -->
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <label class="filter-label">
                                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>Lokasi / Nama Ruangan
                                        </label>
                                        <div class="filter-input-group">
                                            <input type="text" name="location" class="filter-input-field" placeholder="Cari lokasi atau nama ruangan..." value="{{ request('location') }}">
                                        </div>
                                    </div>

                                    <!-- Tanggal Mulai -->
                                    <div class="col-lg-2 col-md-3 col-6">
                                        <label class="filter-label">
                                            <i class="bi bi-calendar3 text-primary me-2"></i>Mulai
                                        </label>
                                        <div class="filter-input-group">
                                            <input type="date" name="start_date" class="filter-input-field text-center" value="{{ request('start_date', date('Y-m-d')) }}">
                                        </div>
                                    </div>

                                    <!-- Tanggal Selesai -->
                                    <div class="col-lg-2 col-md-3 col-6">
                                        <label class="filter-label">
                                            <i class="bi bi-calendar3-fill text-primary me-2"></i>Selesai
                                        </label>
                                        <div class="filter-input-group">
                                            <input type="date" name="end_date" class="filter-input-field text-center" value="{{ request('end_date', date('Y-m-d')) }}">
                                        </div>
                                    </div>

                                    <!-- Tipe Sewa -->
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <label class="filter-label">
                                            <i class="bi bi-wallet2 text-primary me-2"></i>Tipe Sewa
                                        </label>
                                        <div class="filter-input-group position-relative">
                                            <select name="jenis_harga" class="filter-select w-100 pe-4">
                                                <option value="all" {{ request('jenis_harga') == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                                                <option value="Hari" {{ request('jenis_harga') == 'Hari' ? 'selected' : '' }}>Harian</option>
                                                <option value="Jam" {{ request('jenis_harga') == 'Jam' ? 'selected' : '' }}>Per Jam</option>
                                                <option value="Pax" {{ request('jenis_harga') == 'Pax' ? 'selected' : '' }}>Per Pax</option>
                                                <option value="Pax_hari" {{ request('jenis_harga') == 'Pax_hari' ? 'selected' : '' }}>Pax & Hari</option>
                                                <option value="Pax_jam" {{ request('jenis_harga') == 'Pax_jam' ? 'selected' : '' }}>Pax & Jam</option>
                                            </select>
                                            <i class="bi bi-chevron-down text-secondary position-absolute end-0 me-3" style="pointer-events: none;"></i>
                                        </div>
                                    </div>

                                    <!-- Range Harga -->
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <label class="filter-label">
                                            <i class="bi bi-cash-stack text-primary me-2"></i>Range Harga (Min - Max)
                                        </label>
                                        <div class="filter-input-group d-flex align-items-center">
                                            <span class="text-muted small me-1">Rp</span>
                                            <input type="text" name="min_price" class="filter-input-field text-end px-1 thousand-separator" placeholder="Min" value="{{ request('min_price') }}">
                                            <span class="mx-2 text-muted fw-light">-</span>
                                            <span class="text-muted small me-1">Rp</span>
                                            <input type="text" name="max_price" class="filter-input-field text-end px-1 thousand-separator" placeholder="Max" value="{{ request('max_price') }}">
                                        </div>
                                    </div>

                                    <!-- Kapasitas Minimum -->
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <label class="filter-label">
                                            <i class="bi bi-people-fill text-primary me-2"></i>Kapasitas Minimum
                                        </label>
                                        <div class="filter-input-group">
                                            <input type="number" name="capacity" class="filter-input-field" placeholder="5" value="{{ request('capacity') }}" autocomplete="off">
                                            <span class="text-muted small ms-2 fw-semibold">orang</span>
                                        </div>
                                    </div>

                                    <!-- Tombol Cari -->
                                    <div class="col-lg-4 col-md-6 col-12 d-flex align-items-end">
                                        <button type="submit" class="btn btn-search-premium w-100 d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-search"></i> Cari Ruangan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
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
                                    
                                    <p class="text-primary small mb-2 fw-bold">Kapasitas : Maks. {{ $room->capacity }} orang</p>
                                    
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
                                        @if($room->rating_count > 0)
                                            {{ number_format($room->average_rating, 1) }}/5 
                                            <span class="fw-normal text-muted small" style="font-size: 0.75rem;">({{ $room->rating_count }})</span>
                                        @else
                                            <span class="fw-normal text-muted small" style="font-size: 0.8rem;">Belum ada review</span>
                                        @endif
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