@extends('layout.layout')

@section('content')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex align-items-center mb-4">
                <a href="javascript:history.back()" class="btn btn-light rounded-circle me-3 shadow-sm border">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Registrasi Perusahaan Mitra (Vendor)</h3>
                    <p class="text-secondary mb-0">Daftarkan badan usaha penyedia tenaga surveyor independen</p>
                </div>
            </div>

            <form action="{{ route('admin.outsource.form') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- Card Banner --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3 fs-1">
                                <i class="bi bi-building-fill-check"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Kemitraan Badan Usaha</h5>
                                <p class="mb-0 small opacity-75">Akun ini akan memiliki akses untuk mengelola data surveyor yang mereka miliki dan melihat progress project yang ditugaskan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 1: Profil Perusahaan --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3">Profil & Legalitas Perusahaan</h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nama Perusahaan (Sesuai NIB/NPWP) *</label>
                                <input type="text" name="company_name" class="form-control bg-light border-0 py-2" placeholder="Contoh: PT. Survey Indonesia Sejahtera" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor Induk Berusaha (NIB) *</label>
                                <input type="number" name="nib" class="form-control bg-light border-0 py-2" placeholder="Masukkan 13 Digit NIB" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor NPWP Perusahaan *</label>
                                <input type="text" name="npwp" class="form-control bg-light border-0 py-2" placeholder="Masukkan Nomor NPWP" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Bidang Usaha *</label>
                                <select name="business_type" class="form-select bg-light border-0 py-2" required>
                                    <option value="Jasa Survey">Jasa Survey & Pemetaan</option>
                                    <option value="Manajemen SDM">Manajemen SDM (Outsourcing)</option>
                                    <option value="Konsultan">Konsultan Teknis</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small text-uppercase fw-bold">Alamat Kantor Pusat *</label>
                                <textarea name="company_address" class="form-control bg-light border-0 py-2" rows="2" placeholder="Alamat lengkap perusahaan..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Person In Charge (PIC) --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-warning border-4 ps-3 text-warning">Data Penanggung Jawab (PIC)</h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nama Lengkap PIC *</label>
                                <input type="text" name="pic_name" class="form-control bg-light border-0 py-2" placeholder="Nama manager / perwakilan" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Jabatan PIC *</label>
                                <input type="text" name="pic_position" class="form-control bg-light border-0 py-2" placeholder="Contoh: Operasional Manager" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Email Bisnis *</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2" placeholder="email@company.com" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor WhatsApp PIC *</label>
                                <input type="text" name="phone" class="form-control bg-light border-0 py-2" placeholder="0812xxxx" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Informasi Finansial --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-success border-4 ps-3 text-success">Rekening Pembayaran (Atas Nama Perusahaan)</h5>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nama Bank *</label>
                                <select name="bank_name" class="form-select bg-light border-0 py-2">
                                    <option value="BCA">BCA (Corporate)</option>
                                    <option value="Mandiri">Mandiri (Corporate)</option>
                                    <option value="BNI">BNI (Corporate)</option>
                                    <option value="BRI">BRI (Corporate)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor Rekening Perusahaan *</label>
                                <input type="number" name="bank_account" class="form-control bg-light border-0 py-2" placeholder="Masukkan nomor rekening perusahaan" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Upload Dokumen --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-info border-4 ps-3 text-info">Unggah Dokumen Pendukung (PDF/JPG)</h5>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Sertifikat NIB / SIUP *</label>
                                <input type="file" name="file_nib" class="form-control bg-light border-0 py-2" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Salinan NPWP Perusahaan *</label>
                                <input type="file" name="file_npwp" class="form-control bg-light border-0 py-2" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Company Profile / Portofolio</label>
                                <input type="file" name="file_compro" class="form-control bg-light border-0 py-2">
                                <div class="form-text small">Opsional: Membantu verifikasi kredibilitas perusahaan.</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="d-flex gap-2 mb-5">
                    <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
                        Daftarkan Perusahaan Mitra <i class="bi bi-building-add ms-2"></i>
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
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        border: 1px solid #0064D2 !important;
        box-shadow: none;
    }
</style>
@endsection