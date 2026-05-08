@extends('layout.layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Header Navigasi -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('bookings.index') }}" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Laporan Penggunaan Ruangan</h3>
                    <p class="text-secondary mb-0">Lengkapi detail kondisi ruangan pasca pemakaian</p>
                </div>
            </div>

            <!-- Form Laporan -->
            <form action="{{ route('penyedia.report.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Hidden Input untuk ID Booking -->
                <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                
                <!-- Card Info Ringkas (Data dari Eloquent) -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="row">
                            <!-- Sisi Kiri -->
                            <div class="col-md-6 border-end border-white border-opacity-25">
                                <label class="small fw-bold text-uppercase d-block mb-1" style="color: rgba(255,255,255,0.8); letter-spacing: 1px;">ID Booking</label>
                                <h5 class="fw-bold mb-3">#{{ $booking->booking_id }}</h5>
                                
                                <label class="small fw-bold text-uppercase d-block mb-1" style="color: rgba(255,255,255,0.8); letter-spacing: 1px;">Nama Penyewa</label>
                                <p class="mb-0 fw-bold fs-5">{{ $booking->user->username }}</p>
                            </div>
                            
                            <!-- Sisi Kanan -->
                            <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
                                <label class="small fw-bold text-uppercase d-block mb-1" style="color: rgba(255,255,255,0.8); letter-spacing: 1px;">Nama Ruangan</label>
                                <h5 class="fw-bold mb-3">{{ $booking->details->room->name }}</h5>
                                
                                <label class="small fw-bold text-uppercase d-block mb-1" style="color: rgba(255,255,255,0.8); letter-spacing: 1px;">Waktu Sewa</label>
                                <p class="mb-0 fw-bold">
                                    {{ date('d M Y', strtotime($booking->start_date)) }} 
                                    <span class="fw-normal opacity-100">({{ date('H:i', strtotime($booking->start_date)) }} - {{ date('H:i', strtotime($booking->end_date)) }})</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Checklist Kondisi -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3">Checklist Kondisi</h5>
                        
                        <div class="row g-4">
                            <!-- Status Kebersihan -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Tingkat Kebersihan *</label>
                                <select name="kondisi_kebersihan" class="form-select bg-light border-0 py-2 @error('kondisi_kebersihan') is-invalid @enderror" required>
                                    <option value="" selected disabled>Pilih Kondisi...</option>
                                    <option value="sangat_bersih">Sangat Bersih (Seperti Semula)</option>
                                    <option value="normal">Normal (Perlu Pembersihan Ringan)</option>
                                    <option value="kotor">Kotor (Perlu Pembersihan Ekstra)</option>
                                </select>
                                @error('kondisi_kebersihan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Status Kerusakan -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Kondisi Fasilitas *</label>
                                <select name="kondisi_fasilitas" class="form-select bg-light border-0 py-2 @error('kondisi_fasilitas') is-invalid @enderror" required>
                                    <option value="" selected disabled>Pilih Kondisi...</option>
                                    <option value="baik">Semua Berfungsi Baik</option>
                                    <option value="ada_kerusakan">Ada Kerusakan Kecil</option>
                                    <option value="rusak_berat">Ada Kerusakan Berat</option>
                                </select>
                                @error('kondisi_fasilitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Catatan Tambahan -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Catatan Lapangan</label>
                                <textarea name="catatan" class="form-control bg-light border-0 @error('catatan') is-invalid @enderror" rows="4" 
                                          placeholder="Tuliskan detail kondisi, misal: 'AC agak bising' atau 'Kunci tertinggal di meja'...">{{ old('catatan') }}</textarea>
                                @error('catatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Foto Dokumentasi -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Dokumentasi Foto Pasca Pakai</label>
                                <div class="input-group @error('foto_laporan') is-invalid @enderror">
                                    <input type="file" name="foto_laporan[]" class="form-control bg-light border-0 py-2" multiple>
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-camera"></i></span>
                                </div>
                                @error('foto_laporan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text mt-2">Format: JPG, PNG (Maks 2MB per file). Anda dapat memilih lebih dari 1 foto sekaligus.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Tindakan -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
                        Simpan Laporan & Selesaikan Pesanan
                    </button>
                    <!-- Tombol Denda (Melewati report dan langsung ke denda jika perlu) -->
                    <a href="{{ route('bookings.denda', $booking->booking_id) }}" class="btn btn-outline-danger px-4 py-3 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-exclamation-triangle me-1"></i> Ajukan Denda
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8f9fa; }
    
    /* Input Styling */
    .form-select:focus, .form-control:focus {
        background-color: #fff !important;
        border: 1px solid #0064D2 !important;
        box-shadow: none;
        outline: none;
    }

    /* Button Styling */
    .btn-primary { 
        background-color: #0064D2; 
        border: none; 
        transition: all 0.3s ease; 
    }
    .btn-primary:hover { 
        background-color: #0056b3; 
        transform: translateY(-2px); 
    }
    
    .btn-outline-danger {
        border-width: 2px;
    }
    .btn-outline-danger:hover {
        transform: translateY(-2px);
    }

    /* Typography */
    label.small {
        letter-spacing: 0.5px;
        color: #495057;
    }
</style>
@endsection