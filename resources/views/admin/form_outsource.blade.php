@extends('layout.layout')

@section('content')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- KONDISIONAL HEADER: Deteksi mode insert atau edit --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.outsource') }}" class="btn btn-light rounded-circle me-3 shadow-sm border">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">{{ isset($vendor) ? 'Edit Data Perusahaan Mitra (Vendor)' : 'Registrasi Perusahaan Mitra (Vendor)' }}</h3>
                    <p class="text-secondary mb-0">{{ isset($vendor) ? 'Ubah informasi legalitas dan penanggung jawab badan usaha' : 'Daftarkan badan usaha penyedia tenaga surveyor independen' }}</p>
                </div>
            </div>

            {{-- KONDISIONAL ROUTE ACTION --}}
            <form action="{{ isset($vendor) ? route('admin.outsource.update', $vendor->outsource_id) : route('admin.outsource.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- KONDISIONAL METHOD: Wajib PUT jika sedang mengedit data --}}
                @if(isset($vendor))
                    @method('PUT')
                @endif
                
                {{-- Banner Error Handling --}}
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 p-3">
                        <div class="d-flex">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 fs-5"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Gagal Menyimpan Data Vendor:</h6>
                                <ul class="mb-0 small ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                
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
                                <input type="text" name="company_name" value="{{ old('company_name', $vendor->company_name ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="Contoh: PT. ABC" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor Induk Berusaha (NIB) *</label>
                                <input type="number" name="nib" value="{{ old('nib', $vendor->nib ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="Masukkan 13 Digit NIB" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor NPWP Perusahaan *</label>
                                <input type="number" name="npwp" value="{{ old('npwp', $vendor->npwp ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="Masukkan Nomor NPWP" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Bidang Usaha *</label>
                                <select name="business_type" class="form-select bg-light border-0 py-2" required>
                                    @php $selectedType = old('business_type', $vendor->business_type ?? ''); @endphp
                                    <option value="Jasa Survey" {{ $selectedType == 'Jasa Survey' ? 'selected' : '' }}>Jasa Survey & Pemetaan</option>
                                    <option value="Manajemen SDM" {{ $selectedType == 'Manajemen SDM' ? 'selected' : '' }}>Manajemen SDM (Outsourcing)</option>
                                    <option value="Konsultan" {{ $selectedType == 'Konsultan' ? 'selected' : '' }}>Konsultan Teknis</option>
                                    <option value="Lainnya" {{ $selectedType == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small text-uppercase fw-bold">Alamat Kantor Pusat *</label>
                                <textarea name="company_address" class="form-control bg-light border-0 py-2" rows="2" placeholder="Alamat lengkap perusahaan..." required>{{ old('company_address', $vendor->company_address ?? '') }}</textarea>
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
                                <input type="text" name="pic_name" value="{{ old('pic_name', $vendor->pic_name ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="Nama manager / perwakilan" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Jabatan PIC *</label>
                                <input type="text" name="pic_position" value="{{ old('pic_position', $vendor->pic_position ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="Contoh: Operasional Manager" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Email Bisnis *</label>
                                <input type="email" name="pic_email" value="{{ old('pic_email', $vendor->pic_email ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="email@company.com" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor WhatsApp PIC *</label>
                                <input type="number" name="pic_phone" value="{{ old('pic_phone', $vendor->pic_phone ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="0812xxxx" required>
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
                                    @php $selectedBank = old('bank_name', $vendor->bank_name ?? ''); @endphp
                                    <option value="BCA" {{ $selectedBank == 'BCA' ? 'selected' : '' }}>BCA (Corporate)</option>
                                    <option value="Mandiri" {{ $selectedBank == 'Mandiri' ? 'selected' : '' }}>Mandiri (Corporate)</option>
                                    <option value="BNI" {{ $selectedBank == 'BNI' ? 'selected' : '' }}>BNI (Corporate)</option>
                                    <option value="BRI" {{ $selectedBank == 'BRI' ? 'selected' : '' }}>BRI (Corporate)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor Rekening Perusahaan *</label>
                                <input type="number" name="bank_account" value="{{ old('bank_account', $vendor->bank_account ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="Masukkan nomor rekening perusahaan" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="d-flex gap-2 mb-5">
                    <button type="submit" class="btn {{ isset($vendor) ? 'btn-warning text-dark' : 'btn-primary' }} px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
                        {{ isset($vendor) ? 'Simpan Perubahan Data' : 'Daftarkan Perusahaan Mitra' }} 
                        <i class="bi {{ isset($vendor) ? 'bi-check-circle-fill' : 'bi-building-add' }} ms-2"></i>
                    </button>
                    <a href="{{ route('admin.outsource') }}" class="btn btn-light px-4 py-3 rounded-pill fw-bold shadow-sm text-secondary border">
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