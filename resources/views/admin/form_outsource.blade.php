@extends('layout.layout')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="d-flex align-items-center mb-4">
                <a href="javascript:history.back()" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Laporan Survei Kelayakan</h3>
                    <p class="text-secondary mb-0">Input hasil inspeksi lapangan untuk validasi unit</p>
                </div>
            </div>

            <form action="{{ route('admin.outsource') }}"  enctype="multipart/form-data">
                @csrf
                
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <label class="small opacity-75 text-uppercase fw-bold">Unit Ruangan</label>
                                <h4 class="fw-bold mb-0">Kencana Meeting Room A</h4>
                                <p class="small mb-0 opacity-75"><i class="bi bi-geo-alt"></i> Surabaya, Jawa Timur</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">ID PENGAJUAN: #SRV-2026</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3 text-primary">Hasil Inspeksi Lapangan</h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Kondisi Fisik Bangunan *</label>
                                <select name="fisik_score" class="form-select bg-light border-0 py-2" required>
                                    <option value="" selected disabled>Pilih Kondisi...</option>
                                    <option value="10">Sangat Baik (Tanpa Cacat)</option>
                                    <option value="7">Baik (Perlu Cat Ulang Ringan)</option>
                                    <option value="5">Cukup (Ada Retak/Kebocoran)</option>
                                    <option value="2">Buruk (Kerusakan Struktur)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Aksesibilitas & Parkir *</label>
                                <select name="akses_score" class="form-select bg-light border-0 py-2" required>
                                    <option value="mudah">Mudah Diakses (Pinggir Jalan Raya)</option>
                                    <option value="sedang">Sedang (Masuk Gang/Jalan Kecil)</option>
                                    <option value="sulit">Sulit (Akses Terbatas)</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Detail Fasilitas (AC, Wifi, Listrik, dll) *</label>
                                <textarea name="catatan_fasilitas" class="form-control bg-light border-0" rows="4" 
                                          placeholder="Tuliskan detail fasilitas yang tersedia dan fungsinya..." required></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Foto Dokumentasi (Maks 5 Foto) *</label>
                                <div class="input-group">
                                    <input type="file" name="survey_photos[]" class="form-control bg-light border-0 py-2" multiple required>
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-camera-fill text-primary"></i></span>
                                </div>
                                <div class="form-text mt-2 small text-muted">Ambil foto dari berbagai sudut (depan, dalam, dan fasilitas utama).</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bold mb-3 small text-uppercase text-secondary">Rekomendasi Kelayakan Surveyor</h5>
                        <div class="d-flex justify-content-center gap-3">
                            <input type="radio" class="btn-check" name="rekomendasi" id="layak" value="layak" autocomplete="off" required>
                            <label class="btn btn-outline-success px-4 py-2 rounded-pill fw-bold" for="layak">LAYAK SEWA</label>

                            <input type="radio" class="btn-check" name="rekomendasi" id="perbaikan" value="perbaikan" autocomplete="off">
                            <label class="btn btn-outline-warning px-4 py-2 rounded-pill fw-bold" for="perbaikan">PERLU PERBAIKAN</label>

                            <input type="radio" class="btn-check" name="rekomendasi" id="tolak" value="tolak" autocomplete="off">
                            <label class="btn btn-outline-danger px-4 py-2 rounded-pill fw-bold" for="tolak">TIDAK LAYAK</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mb-5">
                    Kirim Laporan ke Kantor Pusat <i class="bi bi-send ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Radio Button Custom Styling */
    .btn-check:checked + .btn-outline-success { background-color: #198754; color: white; }
    .btn-check:checked + .btn-outline-warning { background-color: #ffc107; color: black; }
    .btn-check:checked + .btn-outline-danger { background-color: #dc3545; color: white; }
    
    .form-select:focus, .form-control:focus {
        background-color: #fff !important;
        border: 1px solid #0064D2 !important;
        box-shadow: none;
    }
</style>
@endsection 