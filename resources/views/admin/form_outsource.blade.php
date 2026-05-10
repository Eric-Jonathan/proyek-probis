@extends('layout.layout')

@section('content')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex align-items-center mb-4">
                <a href="javascript:history.back()" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Registrasi Surveyor On-Demand</h3>
                    <p class="text-secondary mb-0">Daftarkan mitra survei independen untuk penugasan per project</p>
                </div>
            </div>

            <form action="{{ route('admin.outsource.form') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3 fs-1">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Sistem Mitra Independen</h5>
                                <p class="mb-0 small opacity-75">Data ini digunakan untuk verifikasi keamanan saat penugasan pengecekan unit di lapangan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3">Data Pribadi & Wilayah</h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nama Lengkap (Sesuai KTP) *</label>
                                <input type="text" name="name" class="form-control bg-light border-0 py-2" placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor NIK KTP *</label>
                                <input type="number" name="nik" class="form-control bg-light border-0 py-2" placeholder="16 Digit NIK" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Kota Domisili Saat Ini *</label>
                                <input type="text" name="city" class="form-control bg-light border-0 py-2" placeholder="Contoh: Malang / Surabaya" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor WhatsApp Aktif *</label>
                                <input type="text" name="phone" class="form-control bg-light border-0 py-2" placeholder="0812xxxx" required>
                            </div>

                            <hr class="my-4 opacity-25">
                            <h5 class="fw-bold mb-2 border-start border-success border-4 ps-3 text-success">Informasi Pembayaran (Honorarium)</h5>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nama Bank *</label>
                                <select name="bank_name" class="form-select bg-light border-0 py-2">
                                    <option value="BCA">BCA</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="BNI">BNI</option>
                                    <option value="BRI">BRI</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor Rekening *</label>
                                <input type="number" name="bank_account" class="form-control bg-light border-0 py-2" placeholder="Masukkan nomor rekening" required>
                            </div>

                            <hr class="my-4 opacity-25">
                            <h5 class="fw-bold mb-2 border-start border-info border-4 ps-3 text-info">Dokumen Verifikasi</h5>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Unggah Foto KTP *</label>
                                <input type="file" name="ktp_photo" class="form-control bg-light border-0 py-2" accept="image/*" required>
                                <div class="form-text small">Pastikan foto jelas dan tidak buram.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Foto Diri Terbaru *</label>
                                <input type="file" name="profile_photo" class="form-control bg-light border-0 py-2" accept="image/*" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
                        Daftarkan Sebagai Surveyor <i class="bi bi-check-circle-fill ms-2"></i>
                    </button>
                    <a href="javascript:history.back()" class="btn btn-light px-4 py-3 rounded-pill fw-bold shadow-sm text-secondary border">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Styling input agar senada dengan form sebelumnya */
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        border: 1px solid #0064D2 !important;
        box-shadow: none;
    }
</style>
@endsection