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
                                    placeholder="Cari lokasi..."
                                    autocomplete="off">
                                
                                <!-- Pesan error untuk validasi -->
                                <div class="invalid-feedback">
                                    Anda harus memilih lokasi dari saran yang tersedia.
                                </div>

                                <div id="autocomplete-results" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; max-height: 250px; overflow-y: auto;"></div>
                                
                                <input type="hidden" name="latitude" id="lat">
                                <input type="hidden" name="longitude" id="lon">
                                <input type="hidden" name="location" id="full-address">
                            </div>

                            <!-- Kapasitas -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Kapasitas (Pax) *</label>
                                <input type="number" name="capacity" class="form-control bg-light py-2 @error('capacity') is-invalid @enderror" 
                                       value="{{ old('capacity', $room->capacity ?? 1) }}">
                                @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Deposit Percent -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Jenis Deposit *</label>
                                <select name="jenis_deposit" id="jenis_deposit" class="form-select bg-light py-2">
                                    <option value="persen">Persen</option>
                                    <option value="nominal">Nominal</option>
                                </select>
                            </div>

                            <div class="col-md-4 form-deposit">
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
                                <label class="form-label fw-bold small text-uppercase">Jenis Harga *</label>
                                <select name="jenis_harga" id="jenis_harga" class="form-select bg-light py-2">
                                    <option value="Pax" selected>Pax</option>
                                    <option value="Jam">Jam</option>
                                    <option value="Hari">Hari</option>
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
                                <label class="form-label fw-bold small text-uppercase">Peraturan Khusus *</label>
                                <div id="editor" style="height: 200px;"></div>
                                <input type="hidden" name="rules" id="rules-input" value="{{ old('rules') }}">
                                @error('rules') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Upload Gambar -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Foto Ruangan</label>
                                
                                {{-- 1. Tampilkan foto lama (Mode Edit) --}}
                                @if(isset($room) && $room->images->count() > 0)
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @foreach($room->images as $img)
                                            <div class="position-relative">
                                                {{-- Langsung panggil path dari database --}}
                                                <img src="{{ asset($img->path) }}" class="rounded shadow-sm" style="width: 100px; height: 80px; object-fit: cover;">
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">Lama</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="alert alert-info py-2 small">
                                        <i class="bi bi-info-circle me-1"></i> Foto baru akan ditambahkan ke koleksi yang sudah ada.
                                    </div>
                                @endif

                                {{-- 2. Input File (Jangan masukkan preview ke sini) --}}
                                <div class="input-group">
                                    <input type="file" 
                                        name="images[]" 
                                        id="image-input" 
                                        class="form-control bg-light py-2 @error('images.*') is-invalid @enderror" 
                                        accept="image/png, image/jpeg, image/jpg" 
                                        multiple>
                                    <span class="input-group-text bg-light"><i class="bi bi-images"></i></span>
                                </div>
                                <small class="text-muted mt-1 d-block">Format: JPG, JPEG, PNG. Tekan CTRL untuk pilih banyak foto.</small>
                                
                                @error('images.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                {{-- 3. Container untuk Preview Foto Baru (DI LUAR input-group) --}}
                                <div id="preview-container" class="d-flex flex-wrap gap-3 mt-3"></div>
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
@endsection

@section('custom_js')
    <script src="{{asset('custom_js/rooms/form.js')}}"></script>
@endsection