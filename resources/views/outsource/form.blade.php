@extends('layout.layout')

@section('custom_css')
    <style>
        .form-select:focus, .form-control:focus {
            background-color: #fff !important;
            border: 1px solid #0064D2 !important;
            box-shadow: none;
            outline: none;
        }
        .btn-outline-success:checked + label, .btn-check:checked + .btn-outline-success {
            background-color: #198754 !important;
            color: white !important;
        }
        
        /* Facility Checkbox Styling */
        .facility-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.1rem;
            border-radius: 12px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            min-height: 70px;
        }
        .facility-label:hover {
            transform: translateY(-2px);
            border-color: #0064D2;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.04);
        }
        .btn-check:checked + .facility-label {
            background-color: #f0f7ff !important;
            border: 2px solid #0064D2 !important;
            transform: translateY(-1px);
        }
        .btn-check:checked + .facility-label span {
            color: #0056b3 !important;
            font-weight: bold;
        }

        /* Image & Video Preview styles */
        .preview-card {
            width: 100px;
            height: 100px;
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .preview-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .preview-card .btn-remove {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 20px;
            height: 20px;
            background-color: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            line-height: 1;
            z-index: 5;
        }
        .btn-outline-dashed {
            border: 1.5px dashed #0064D2 !important;
            color: #0064D2 !important;
            background: transparent;
            transition: all 0.2s;
        }
        .btn-outline-dashed:hover {
            background-color: rgba(0, 100, 210, 0.05);
            transform: translateY(-2px);
        }
    </style>
@endsection

@section('content')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('outsource.job') }}" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">Input Laporan Kelayakan</h3>
                    <p class="text-secondary mb-0">Lengkapi data hasil inspeksi lapangan Anda secara akurat.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4 text-white" style="border-radius: 20px; background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <label class="small fw-bold opacity-75 text-uppercase">Objek Survei</label>
                            <h4 class="fw-bold mb-1">{{ $assignment->room->name ?? 'N/A' }}</h4>
                            <p class="mb-0 small opacity-75"><i class="bi bi-geo-alt-fill me-1"></i> {{ $assignment->room->location ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">PROJECT ID: #SRV-{{ $assignment->assignment_id }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('outsource.job.submit', $assignment->assignment_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3">Detail Penilaian Teknis</h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Kondisi Bangunan *</label>
                                <select name="kondisi" class="form-select bg-light border-0 py-2 shadow-none" required>
                                    <option value="" selected disabled>Pilih Kondisi...</option>
                                    <option value="Sangat Baik">Sangat Baik (Tanpa Cacat)</option>
                                    <option value="Baik">Baik (Perlu Perbaikan Cat)</option>
                                    <option value="Buruk">Buruk (Kerusakan Struktur)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Tingkat Kebersihan *</label>
                                <select name="kebersihan" class="form-select bg-light border-0 py-2 shadow-none" required>
                                    <option value="Sangat Bersih">Sangat Bersih</option>
                                    <option value="Cukup Bersih">Cukup Bersih</option>
                                    <option value="Kotor / Berantakan">Kotor / Berantakan</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Deskripsi & Temuan Fasilitas</label>
                                <textarea name="catatan" class="form-control bg-light border-0 py-2 shadow-none" rows="4" placeholder="Contoh: AC berfungsi normal, Wifi stabil, terdapat noda di karpet pojok kanan..."></textarea>
                            </div>

                            {{-- VERIFIKASI FASILITAS --}}
                            <div class="col-12 mt-4">
                                <label class="form-label fw-bold small text-uppercase">Verifikasi Fasilitas Umum</label>
                                <p class="text-muted small mb-3">Centang fasilitas yang terbukti ada di lapangan dan tambahkan jika menemukan fasilitas lain.</p>
                                
                                <div class="row g-3" id="dynamic-facilities-container">
                                    @php
                                        // Daftar fasilitas default
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
                                        
                                        // Cari apa saja fasilitas yang diajukan oleh penyedia pada ruangan ini
                                        $selectedFacilities = isset($assignment->room) ? $assignment->room->facilities->pluck('name')->toArray() : [];
                                        $masterLabels = array_column($masterFacilities, 'label');
                                    @endphp

                                    {{-- Loop Fasilitas Master --}}
                                    @foreach($masterFacilities as $f)
                                        <div class="col-6 col-md-4 col-lg-3 facility-item-wrapper">
                                            <input type="checkbox" name="facilities[]" value="{{ $f['label'] }}" 
                                                class="btn-check" id="fac-{{ $f['id'] }}"
                                                {{ in_array($f['label'], $selectedFacilities) ? 'checked' : '' }}>
                                            <label class="btn btn-outline-light text-dark border shadow-sm w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-4 facility-label" 
                                                for="fac-{{ $f['id'] }}">
                                                <span class="fw-bold text-center" style="font-size: 0.8rem;">{{ $f['label'] }}</span>
                                            </label>
                                        </div>
                                    @endforeach

                                    {{-- Loop Fasilitas Kustom yang didaftarkan room tapi tidak masuk master list --}}
                                    @foreach($selectedFacilities as $savedFacility)
                                        @if(!in_array($savedFacility, $masterLabels))
                                            @php $cleanId = 'custom-' . Str::slug($savedFacility); @endphp
                                            <div class="col-6 col-md-4 col-lg-3 facility-item-wrapper">
                                                <input type="checkbox" name="facilities[]" value="{{ $savedFacility }}" 
                                                    class="btn-check" id="fac-{{ $cleanId }}" checked>
                                                <label class="btn btn-outline-light text-dark border shadow-sm w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-4 facility-label" 
                                                    for="fac-{{ $cleanId }}">
                                                    <span class="fw-bold text-center" style="font-size: 0.8rem;">{{ $savedFacility }}</span>
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- Tombol tambah fasilitas kustom --}}
                                    <div class="col-6 col-md-4 col-lg-3" id="btn-add-facility-wrapper">
                                        <button type="button" 
                                                class="btn btn-outline-dashed border-primary text-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-2 rounded-4 h-100" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#customFacilityModal"
                                                style="border-style: dashed !important; background: transparent; min-height: 75px;">
                                            <span class="fw-bold text-center" style="font-size: 0.8rem;">+ Fasilitas Lainnya</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- DOKUMENTASI FOTO --}}
                            <div class="col-12 mt-4">
                                <label class="form-label fw-bold small text-uppercase">Unggah Foto Lapangan (Minimal 3) *</label>
                                <div class="input-group">
                                    <input type="file" name="fotos[]" id="foto-input" class="form-control bg-light border-0 py-2" accept="image/*" multiple required>
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-camera-fill text-primary"></i></span>
                                </div>
                                <small class="text-muted d-block mt-1">Gunakan format JPEG, PNG, JPG, atau WebP. Maks 2MB per gambar.</small>
                                
                                <div id="foto-preview-container" class="d-flex flex-wrap gap-2.5 p-3 border rounded bg-light mt-3 align-items-center" style="min-height: 110px;">
                                    <div class="text-center w-100 text-muted" id="foto-placeholder">
                                        <i class="bi bi-images fs-3 d-block mb-1"></i>
                                        <span class="small">Belum ada foto yang dipilih</span>
                                    </div>
                                </div>
                            </div>

                            {{-- DOKUMENTASI VIDEO --}}
                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold small text-uppercase">Unggah Video Lapangan (Opsional)</label>
                                <div class="input-group">
                                    <input type="file" name="video" id="video-input" class="form-control bg-light border-0 py-2" accept="video/*">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-play-btn-fill text-primary"></i></span>
                                </div>
                                <small class="text-muted d-block mt-1">Format: MP4, WebM. Maksimal ukuran file: 10MB.</small>
                                
                                <div id="video-preview-container" class="mt-3 p-3 border rounded bg-light d-none position-relative">
                                    <button type="button" id="btn-remove-video" class="btn btn-danger btn-sm p-0 position-absolute rounded-circle shadow" 
                                            style="top: -8px; right: -8px; width: 22px; height: 22px; line-height: 18px; z-index: 10; font-size: 11px; font-weight: bold; border: none;">
                                        &times;
                                    </button>
                                    <video id="video-preview" controls class="rounded w-100" style="max-height: 250px; object-fit: contain;"></video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4 text-center">
                        <h6 class="fw-bold small text-uppercase text-secondary mb-3">Kesimpulan Akhir Surveyor</h6>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <input type="radio" class="btn-check" name="rekomendasi" id="layak" value="layak" required>
                            <label class="btn btn-outline-success rounded-pill px-5 fw-bold" for="layak">LAYAK SEWA</label>

                            <input type="radio" class="btn-check" name="rekomendasi" id="tidak" value="tidak">
                            <label class="btn btn-outline-danger rounded-pill px-5 fw-bold" for="tidak">TIDAK LAYAK</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mb-5">
                    <button type="submit" class="btn btn-primary flex-grow-1 py-3 rounded-pill fw-bold shadow-sm" id="btn-submit-report">
                        Kirim Laporan Kerja <i class="bi bi-send-fill ms-2"></i>
                    </button>
                    <a href="{{ route('outsource.job') }}" class="btn btn-light px-4 py-3 rounded-pill fw-bold shadow-sm text-secondary border">
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
            <div class="modal-body p-4 text-start">
                <h6 class="fw-bold mb-3 text-dark">Tambah Fasilitas Baru</h6>
                <input type="text" id="custom-facility-input" class="form-control bg-light" placeholder="Nama fasilitas... (misal: Kursi Tambahan)">
                <div class="d-flex gap-2 mt-3 justify-content-end">
                    <button type="button" class="btn btn-light rounded-pill btn-sm px-3" data-bs-dismiss="modal" id="btn-close-modal-facility">Batal</button>
                    <button type="button" id="btn-confirm-add-facility" class="btn btn-primary rounded-pill btn-sm px-3">Tambah</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
    <script src="{{ asset('custom_js/outsource/form.js') }}"></script>
@endsection