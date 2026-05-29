@extends('layout.layout')

@section('custom_css')
    <style>
        body { background-color: #f8f9fa; }
        .form-select:focus, .form-control:focus {
            background-color: #fff !important;
            border: 1px solid #0064D2 !important;
            box-shadow: none;
            outline: none;
        }
        .btn-primary { background-color: #0064D2; border: none; transition: all 0.3s ease; }
        .btn-primary:hover { background-color: #0056b3; transform: translateY(-2px); }
        label.small { letter-spacing: 0.5px; color: #495057; }

        .facility-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    border-radius: 16px;
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

/* State: Hover (Efek melayang halus) */
.facility-label:hover {
    transform: translateY(-4px);
    border-color: #0064D2;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
}

/* State: Terpilih (Checked) */
.btn-check:checked + .facility-label {
    background-color: #f0f7ff !important; /* Biru sangat muda yang segar */
    border: 2px solid #0064D2 !important; /* Border lebih tebal */
    box-shadow: 0 0 0 1px #0064D2, 0 8px 16px rgba(0, 100, 210, 0.12) !important;
    transform: translateY(-2px) scale(1.02);
}

/* Styling Ikon saat Terpilih */
.btn-check:checked + .facility-label i {
    color: #0064D2 !important;
    transform: scale(1.1);
}

/* Styling Teks saat Terpilih */
.btn-check:checked + .facility-label span {
    color: #0056b3 !important;
    font-weight: 700;
}

/* Tambahan: Indikator centang kecil di pojok kanan atas (Opsional tapi Pro) */
.btn-check:checked + .facility-label::after {
    font-weight: 900;
    position: absolute;
    top: 8px;
    right: 8px;
    color: #0064D2;
    font-size: 0.9rem;
}

/* Transisi Dasar untuk Elemen Internal */
.facility-label i, 
.facility-label span {
    transition: all 0.2s ease;
}
    </style>
@endsection

@section('content')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Header Navigasi -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('rooms.index') }}" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <!-- Judul Dinamis -->
                    <h3 class="fw-bold mb-0">{{ isset($room) ? 'Edit Ruangan' : 'Tambah Ruangan Baru' }}</h3>
                    <p class="text-secondary mb-0">{{ isset($room) ? 'Perbarui informasi unit ruangan Anda' : 'Daftarkan unit ruangan Anda ke dalam sistem' }}</p>
                </div>
            </div>

            <!-- Action Form Dinamis -->
            <form action="{{ isset($room) ? route('rooms.update', $room->room_id) : route('rooms.store') }}" 
                  method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Method Spoofing untuk Update -->
                @if(isset($room))
                    @method('PUT')
                @endif
                
                <!-- Card Petunjuk -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3 fs-1">
                                <i class="bi {{ isset($room) ? 'bi-pencil-square' : 'bi-building-add' }}"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Informasi Inventaris</h5>
                                <p class="mb-0 small opacity-75">Lengkapi formulir di bawah ini. Tanda (*) wajib diisi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Card Utama -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3">Detail Ruangan</h5>
                        
                        <div class="row g-4">
                            <!-- Nama Ruangan -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Nama Ruangan *</label>
                                <input type="text" name="name" class="form-control bg-light py-2 @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $room->name ?? '') }}" placeholder="Contoh: Grand Ballroom">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Status *</label>
                                @php $currentStatus = old('status', $room->status ?? ''); @endphp
                                <select name="status" class="form-select bg-light py-2 @error('status') is-invalid @enderror">
                                    <option value="1" {{ $currentStatus == '1' ? 'selected' : '' }}>Aktif (Tersedia)</option>
                                    <option value="2" {{ $currentStatus == '2' ? 'selected' : '' }}>Nonaktif</option>
                                    <option value="3" {{ $currentStatus == '3' ? 'selected' : '' }}>Maintenance (Perbaikan)</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Lokasi -->
                            <div class="col-md-12 position-relative">
                                <label class="form-label fw-bold small text-uppercase">Alamat Tempat *</label>
                                <input type="text" 
                                    id="address-search" 
                                    class="form-control bg-light py-2" 
                                    data-url="{{ route('autocompleteLocation') }}"
                                    value="{{ old('location', isset($room) ? $room->location : '') }}"
                                    placeholder="Cari lokasi..."
                                    autocomplete="off">
                                
                                <!-- Pesan error untuk validasi -->
                                <div class="invalid-feedback">
                                    Anda harus memilih lokasi dari saran yang tersedia.
                                </div>

                                <div id="autocomplete-results" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; max-height: 250px; overflow-y: auto;"></div>
                                
                                <input type="hidden" name="latitude" id="latitude-input" value="{{ old('latitude', isset($room) ? $room->latitude : '') }}">
                                <input type="hidden" name="longitude" id="longitude-input" value="{{ old('longitude', isset($room) ? $room->longitude : '') }}">
                                <input type="hidden" name="location" id="full-address" value="{{ old('location', isset($room) ? $room->location : '') }}">
                            </div>

                            <!-- Minim Hari Booking -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Minimal Hari Booking *</label>
                                <div class="input-group">
                                    <input type="number" name="day" class="form-control py-2 @error('day') is-invalid @enderror" 
                                        value="{{ old('day', $room->day ?? 1) }}">
                                    <span class="input-group-text">Hari</span>
                                </div>
                                @error('day') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Kapasitas --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Kapasitas (Pax) *</label>
                                <input type="number" name="capacity" class="form-control bg-light py-2 @error('capacity') is-invalid @enderror" 
                                       value="{{ old('capacity', $room->capacity ?? 1) }}">
                                @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Deposit Percent -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Jenis Deposit *</label>
                                <select name="jenis_deposit" id="jenis_deposit" class="form-select bg-light py-2">
                                    <option value="persen">Persen</option>
                                    <option value="nominal">Nominal</option>
                                </select>
                            </div>

                            <div class="col-md-6 form-deposit">
                                <!-- Input Persen -->
                                <div id="wrapper-persen">
                                    <label class="form-label fw-bold small text-uppercase">Deposit (%) *</label>
                                    <div class="input-group">
                                        <input type="number" name="deposit_percent" class="form-control @error('deposit_percent') is-invalid @enderror" 
                                            value="{{ old('deposit_percent', $room->deposit_percent ?? 0) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('deposit_percent') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <!-- Input Nominal (Sembunyikan defaultnya) -->
                                <div id="wrapper-nominal" style="display: none;">
                                    <label class="form-label fw-bold small text-uppercase">Deposit (Rp) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="deposit_nominal" class="form-control @error('deposit_nominal') is-invalid @enderror" 
                                            value="{{ old('deposit_nominal', $room->deposit_nominal ?? 0) }}">
                                    </div>
                                    @error('deposit_nominal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Harga -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Harga Sewa *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="price" class="form-control py-2 @error('price') is-invalid @enderror" 
                                           value="{{ old('price', $room->price ?? 0) }}">
                                </div>
                                @error('price') <small class="text-danger small">{{ $message }}</small> @enderror
                            </div>

                            <!-- Jenis harga -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Harga Per *</label>
                                <select name="jenis_harga" id="jenis_harga" class="form-select bg-light py-2">
                                    <option value="Pax" selected>Pax</option>
                                    <option value="Jam">Jam</option>
                                    <option value="Hari">Hari</option>
                                    <option value="Pax_jam">Pax & Jam</option>
                                </select>
                            </div>

                            <!-- Minimal Order -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Minimal Order *</label>
                                <div class="input-group">
                                    <input type="number" name="min_order" class="form-control @error('min_order') is-invalid @enderror" 
                                            value="{{ old('min_order', $room->min_order ?? 1) }}">
                                    <span class="input-group-text" id="satuan_min_order">Pax</span>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Deskripsi Ruangan</label>
                                <textarea name="description" class="form-control bg-light @error('description') is-invalid @enderror" 
                                          rows="3" placeholder="Jelaskan keunggulan ruangan Anda...">{{ old('description', $room->description ?? '') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Peraturan -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Peraturan Khusus</label>
                                <div id="editor" style="height: 200px;"></div>
                                <input type="hidden" name="rules" id="rules-input" value="{{ old('rules', isset($room) ? $room->rules : '') }}">
                                @error('rules') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Fasilitas --}}
                            <div class="col-12 mt-5">
                                <label class="form-label fw-bold medium text-uppercase">Fasilitas Umum</label>
                                <p class="text-muted small mb-3">Klik untuk memilih fasilitas yang tersedia di ruangan ini atau tambahkan fasilitas kustom Anda sendiri.</p>
                                
                                <div class="row g-3" id="dynamic-facilities-container">
                                    @php
                                        // 1. Daftar fasilitas default/master aplikasi
                                        $masterFacilities = [
                                            ['id' => 'ac', 'label' => 'AC'],
                                            ['id' => 'wifi', 'label' => 'Free Wi-Fi'],
                                            ['id' => 'sound', 'label' => 'Sound System'],
                                            ['id' => 'mic', 'label' => 'Wireless Mic'],
                                            ['id' => 'projector', 'label' => 'Proyektor'],
                                            ['id' => 'snack', 'label' => 'Snack'],
                                            ['id' => 'galon', 'label' => 'Galon'],
                                            ['id' => 'parking', 'label' => 'Area Parkir'],
                                            ['id' => 'musholla', 'label' => 'Musholla'],
                                            ['id' => 'stage', 'label' => 'Panggung'],
                                            ['id' => 'cctv', 'label' => 'Keamanan CCTV'],
                                        ];

                                        // 2. Ambil data yang sudah terpilih di DB (atau dari old input jika validasi gagal)
                                        if (old('facilities')) {
                                            $selectedFacilities = old('facilities');
                                        } elseif (isset($room)) {
                                            // Ambil semua nama fasilitas yang terikat dengan room ini
                                            $selectedFacilities = $room->facilities->pluck('name')->toArray();
                                        } else {
                                            $selectedFacilities = [];
                                        }

                                        // Ambil array berisi list label/nama dari master agar mudah memisahkan data kustom
                                        $masterLabels = array_column($masterFacilities, 'label');
                                    @endphp

                                    {{-- Loop 1: Tampilkan Fasilitas Master (Otomatis Checked jika ada di DB) --}}
                                    @foreach($masterFacilities as $f)
                                        <div class="col-6 col-md-4 col-lg-3 facility-item-wrapper">
                                            <input type="checkbox" name="facilities[]" value="{{ $f['label'] }}" 
                                                class="btn-check" id="fac-{{ $f['id'] }}"
                                                {{ in_array($f['label'], $selectedFacilities) ? 'checked' : '' }}>
                                            <label class="btn btn-outline-light text-dark border shadow-sm w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-4 facility-label" 
                                                for="fac-{{ $f['id'] }}">
                                                <span class="fw-bold text-center">{{ $f['label'] }}</span>
                                            </label>
                                        </div>
                                    @endforeach

                                    {{-- Loop 2: Autofill Fasilitas Kustom (Jika nama fasilitas tidak ada di daftar master, cetak otomatis di sini) --}}
                                    @foreach($selectedFacilities as $savedFacility)
                                        @if(!in_array($savedFacility, $masterLabels))
                                            @php $cleanId = 'custom-' . Str::slug($savedFacility); @endphp
                                            <div class="col-6 col-md-4 col-lg-3 facility-item-wrapper">
                                                <input type="checkbox" name="facilities[]" value="{{ $savedFacility }}" 
                                                    class="btn-check" id="fac-{{ $cleanId }}" checked>
                                                <label class="btn btn-outline-light text-dark border shadow-sm w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-4 facility-label" 
                                                    for="fac-{{ $cleanId }}">
                                                    <span class="fw-bold text-center">{{ $savedFacility }}</span>
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- Tombol Trigger Tambah Fasilitas Lainnya --}}
                                    <div class="col-6 col-md-4 col-lg-3" id="btn-add-facility-wrapper">
                                        <button type="button" 
                                                class="btn btn-outline-dashed border-primary text-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2 rounded-4 h-100" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#customFacilityModal"
                                                style="border-style: dashed !important; background: transparent;">
                                            <span class="fw-bold text-center">+ Fasilitas Lainnya</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Upload gambar --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Foto Ruangan *</label>
                                
                                <div class="d-block mb-3">
                                    <div class="input-group">
                                        <input type="file" 
                                            name="images[]" 
                                            id="image-input" 
                                            class="form-control bg-light py-2 @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" 
                                            accept="image/png, image/jpeg, image/jpg" 
                                            multiple>
                                        <span class="input-group-text bg-light"><i class="bi bi-images"></i></span>
                                    </div>
                                    <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG. Ukuran maks 2MB per foto. (Total minimal 5 foto)</small>

                                    @error('images') 
                                        <div class="text-danger small mt-2 fw-semibold"><i class="bi bi-exclamation-circle-fill me-1"></i>{{ $message }}</div> 
                                    @enderror
                                    @error('images.*') 
                                        <div class="text-danger small mt-2 fw-semibold"><i class="bi bi-exclamation-circle-fill me-1"></i>{{ $message }}</div> 
                                    @enderror
                                </div>

                                <div id="gallery-container" class="d-flex flex-wrap gap-3 p-3 border rounded bg-light align-items-center" style="min-height: 120px;">
                                    
                                    @if(isset($room) && $room->images->count() > 0)
                                        @foreach($room->images as $img)
                                            <div class="image-wrapper old-image text-center position-relative border rounded p-1 bg-white shadow-sm" id="image-card-{{ $img->image_id }}" style="width: 120px;">
                                                <button type="button" class="btn-delete-old-image btn btn-danger btn-sm p-0 position-absolute rounded-circle shadow" 
                                                        data-image-id="{{ $img->image_id }}"
                                                        style="top: -8px; right: -8px; width: 22px; height: 22px; line-height: 18px; z-index: 10; font-size: 11px; font-weight: bold; border: none;">
                                                    &times;
                                                </button>
                                                <img src="{{ asset($img->path) }}" class="rounded w-100" style="height: 85px; object-fit: cover;">
                                                <div class="small text-muted mt-1 text-truncate px-1" style="font-size: 11px;">Foto Tersimpan</div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center w-100 my-3 text-muted placeholder-text">
                                            <i class="bi bi-image fs-3 d-block mb-1"></i>
                                            <span class="small">Belum ada foto yang dipilih</span>
                                        </div>
                                    @endif
                                </div>

                                <div id="deleted-images-inputs"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Tindakan -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-save btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
                        {{ isset($room) ? 'Simpan Perubahan' : 'Simpan & Publikasikan Ruangan' }}
                    </button>
                    <a href="{{ route('rooms.index') }}" class="btn btn-light px-4 py-3 rounded-pill fw-bold shadow-sm text-secondary border">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal insert Fasilitas --}}
<div class="modal fade" id="customFacilityModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-body p-4">
                <h6 class="fw-bold mb-3">Tambah Fasilitas Baru</h6>
                <input type="text" id="custom-facility-input" class="form-control bg-light" placeholder="Nama fasilitas... (misal: Kursi Tambahan)">
                <div class="d-flex gap-2 mt-3 justify-content-end">
                    {{-- Tombol Batal --}}
                    <button type="button" class="btn btn-light rounded-pill btn-sm px-3" data-bs-dismiss="modal" id="btn-close-modal-facility">Batal</button>
                    {{-- Tombol Tambat --}}
                    <button type="button" id="btn-confirm-add-facility" class="btn btn-primary rounded-pill btn-sm px-3">Tambah</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
    <script src="{{asset('custom_js/rooms/form.js')}}"></script>
@endsection