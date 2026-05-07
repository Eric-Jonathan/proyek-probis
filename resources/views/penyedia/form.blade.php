@extends('layout.layout')

@php
    // Deteksi Mode Edit berdasarkan URL segment 2 (rooms/{id}/edit)
    $roomIdFromUrl = request()->segment(2);
    $isEdit = is_numeric($roomIdFromUrl);
@endphp

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="mb-4 text-center">
                {{-- Judul berdasarkan variabel $isEdit --}}
                <h3 class="fw-bold">{{ $isEdit ? 'Edit Ruangan' : 'Daftarkan Ruangan Anda' }}</h3>
                <p class="text-secondary">{{ $isEdit ? 'Perbarui data ballroom anda untuk menarik lebih banyak penyewa' : 'Gunakan formulir ini untuk mengajukan tempat penyewaan baru' }}</p>
            </div>

            {{-- Action Route dinamis menggunakan ID dari URL jika sedang Edit --}}
            <form action="{{ $isEdit ? route('rooms.update', $roomIdFromUrl) : route('rooms.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                
                {{-- Method Spoofing untuk Update --}}
                @if($isEdit)
                    @method('PUT')
                @endif
                
                <!-- Section 1: Informasi Dasar -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="section-title mb-4">Informasi Dasar</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Nama Ballroom</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ old('name', $room->name ?? '') }}" placeholder="Contoh: Grand Convention Hall" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Lokasi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo-alt text-primary"></i></span>
                                    <input type="text" name="location" class="form-control border-start-0 @error('location') is-invalid @enderror" 
                                        value="{{ old('location', $room->location ?? '') }}" placeholder="Alamat lengkap lokasi..." required>
                                </div>
                                @error('location') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Foto Ruangan -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="section-title mb-4">Foto Ruangan</h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                {{-- Pratinjau foto hanya muncul jika dalam mode edit dan data foto ada --}}
                                @if($isEdit && isset($room->image))
                                    <div class="mb-3">
                                        <label class="d-block mb-2 text-muted small">Foto Saat Ini:</label>
                                        <img src="{{ asset('storage/' . $room->image) }}" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                @endif
                                <label class="form-label fw-semibold">Unggah Foto Baru</label>
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                                <div class="form-text">Format: JPG, PNG, WEBP. Maksimal 2MB.</div>
                                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kapasitas, Biaya & Status -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="section-title mb-4">Kapasitas, Biaya & Status</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kapasitas</label>
                                <div class="input-group">
                                    <input type="number" name="capacity" class="form-control" 
                                        value="{{ old('capacity', $room->capacity ?? '') }}" placeholder="0">
                                    <span class="input-group-text bg-light">Orang</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Harga Sewa</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" name="price" class="form-control" 
                                        value="{{ old('price', $room->price ?? '') }}" placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Deposit (%)</label>
                                <input type="number" name="deposit_percent" class="form-control" 
                                    value="{{ old('deposit_percent', $room->deposit_percent ?? 0) }}">
                            </div>
                            
                            <div class="col-md-12 mt-4">
                                <label class="form-label fw-semibold">Status Ruangan</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="1" {{ old('status', $room->status ?? '') == 1 ? 'selected' : '' }}>Diajukan (Review Admin)</option>
                                    <option value="2" {{ old('status', $room->status ?? '') == 2 ? 'selected' : '' }}>Tersedia / Aktif</option>
                                    <option value="0" {{ old('status', $room->status ?? '') == 0 ? 'selected' : '' }}>Nonaktif / Perbaikan</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Detail Properti -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="section-title mb-4">Detail Properti</h5>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan fasilitas utama, luas ruangan, dan keunggulan lainnya...">{{ old('description', $room->description ?? '') }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-danger">Aturan Ruangan</label>
                            <textarea name="rules" class="form-control border-danger-subtle bg-light" rows="3" placeholder="Contoh: Dilarang membawa makanan luar, maksimal durasi penggunaan, dll...">{{ old('rules', $room->rules ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mb-5">
                    <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary px-4 rounded-pill fw-bold text-decoration-none py-2">Batal</a>
                    <button type="submit" class="btn btn-tiket shadow-sm px-5">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Kirim Pengajuan' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection