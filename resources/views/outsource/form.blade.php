@extends('layout.layout')

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
                            <h4 class="fw-bold mb-1">Kontena Hotel - Ball Room</h4>
                            <p class="mb-0 small opacity-75"><i class="bi bi-geo-alt-fill me-1"></i> Jl. KH. Agus Salim No.106, Kota Batu</p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">PROJECT ID: #SRV-101</span>
                        </div>
                    </div>
                </div>
            </div>

            <form action="#" method="POST" enctype="multipart/form-data">
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
                                    <option value="Bersih">Sangat Bersih</option>
                                    <option value="Cukup">Cukup Bersih</option>
                                    <option value="Kotor">Kotor / Berantakan</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Deskripsi & Temuan Fasilitas</label>
                                <textarea name="catatan" class="form-control bg-light border-0 py-2 shadow-none" rows="4" placeholder="Contoh: AC berfungsi normal, Wifi stabil, terdapat noda di karpet pojok kanan..."></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Unggah Foto Lapangan (Minimal 3) *</label>
                                <div class="input-group">
                                    <input type="file" name="fotos[]" class="form-control bg-light border-0 py-2" multiple required>
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-camera-fill text-primary"></i></span>
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
                    <button type="submit" class="btn btn-primary flex-grow-1 py-3 rounded-pill fw-bold shadow-sm">
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

<style>
    .form-select:focus, .form-control:focus {
        background-color: #fff !important;
        border: 1px solid #0064D2 !important;
    }
    .btn-outline-success:checked + label, .btn-check:checked + .btn-outline-success {
        background-color: #198754 !important;
        color: white !important;
    }
</style>
@endsection