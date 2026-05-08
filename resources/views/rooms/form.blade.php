@extends('layout.layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
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
                                <input type="text" name="name" class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $room->name ?? '') }}" placeholder="Contoh: Grand Ballroom" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Lokasi -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Lokasi Spesifik *</label>
                                <input type="text" name="location" class="form-control bg-light border-0 py-2 @error('location') is-invalid @enderror" 
                                       value="{{ old('location', $room->location ?? '') }}" placeholder="Contoh: Lantai 2, Sayap Barat" required>
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Lantai -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Lantai (Angka) *</label>
                                <input type="number" name="floor" class="form-control bg-light border-0 py-2 @error('floor') is-invalid @enderror" 
                                       value="{{ old('floor', $room->floor ?? '') }}" placeholder="1" required>
                                @error('floor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Kapasitas -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Kapasitas (Pax) *</label>
                                <input type="number" name="capacity" class="form-control bg-light border-0 py-2 @error('capacity') is-invalid @enderror" 
                                       value="{{ old('capacity', $room->capacity ?? '') }}" required>
                                @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Deposit Percent -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Deposit (%) *</label>
                                <input type="number" name="deposit_percent" class="form-control bg-light border-0 py-2 @error('deposit_percent') is-invalid @enderror" 
                                       value="{{ old('deposit_percent', $room->deposit_percent ?? 0) }}" required>
                                @error('deposit_percent') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Harga -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Harga Sewa *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">Rp</span>
                                    <input type="number" name="price" class="form-control bg-light border-0 py-2 @error('price') is-invalid @enderror" 
                                           value="{{ old('price', $room->price ?? '') }}" required>
                                </div>
                                @error('price') <small class="text-danger small">{{ $message }}</small> @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Status *</label>
                                @php $currentStatus = old('status', $room->status ?? ''); @endphp
                                <select name="status" class="form-select bg-light border-0 py-2 @error('status') is-invalid @enderror" required>
                                    <option value="1" {{ $currentStatus == '1' ? 'selected' : '' }}>Aktif (Tersedia)</option>
                                    <option value="2" {{ $currentStatus == '2' ? 'selected' : '' }}>Nonaktif</option>
                                    <option value="3" {{ $currentStatus == '3' ? 'selected' : '' }}>Maintenance (Perbaikan)</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Deskripsi Ruangan</label>
                                <textarea name="description" class="form-control bg-light border-0 @error('description') is-invalid @enderror" 
                                          rows="3" placeholder="Jelaskan keunggulan ruangan Anda...">{{ old('description', $room->description ?? '') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Peraturan -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Peraturan Khusus *</label>
                                <textarea name="rules" class="form-control bg-light border-0 @error('rules') is-invalid @enderror" 
                                          rows="3" placeholder="Contoh: Dilarang merokok..." required>{{ old('rules', $room->rules ?? '') }}</textarea>
                                @error('rules') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Upload Gambar -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Foto Ruangan</label>
                                                        
                                @if(isset($room))
                                    {{-- Mode Edit: Tampilkan foto lama & beri info --}}
                                    <div class="mb-3 p-3 bg-light rounded border">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . $room->image) }}" class="rounded shadow-sm me-3" style="width: 80px; height: 60px; object-fit: cover;">
                                            <div>
                                                <span class="badge bg-secondary mb-1">Mode Read-Only</span>
                                                <p class="mb-0 small text-muted">Foto tidak dapat diubah melalui form ini.</p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Mode Add: Tampilkan input file seperti biasa --}}
                                    <div class="input-group">
                                        <input type="file" name="image" class="form-control bg-light border-0 py-2">
                                        <span class="input-group-text bg-light border-0"><i class="bi bi-image"></i></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Tindakan -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
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