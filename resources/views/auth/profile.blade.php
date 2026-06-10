@extends('layout.layout')

@section('custom_css')
<style>
    body {
        background-color: #f8f9fa;
    }
    .profile-card {
        border-radius: 16px;
        border: none;
        overflow: hidden;
    }
    .profile-header-gradient {
        background: linear-gradient(135deg, #006ce4 0%, #004b9e 100%);
        height: 120px;
    }
    .avatar-wrapper {
        margin-top: -60px;
    }
    .avatar-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: #e8f2ff;
        color: #006ce4;
        font-size: 3rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        text-transform: uppercase;
    }
    .form-label {
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #4b5563;
    }
    .form-control:focus {
        border-color: #006ce4;
        box-shadow: 0 0 0 0.25rem rgba(0, 108, 228, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-9">
            {{-- Header with Back Button --}}
            <div class="d-flex align-items-center mb-4">
                <a href="javascript:history.back()" class="btn btn-light rounded-circle me-3 shadow-sm border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">Profil Pengguna</h3>
                    <p class="text-secondary small mb-0">Kelola detail akun dan pengaturan keamanan Anda</p>
                </div>
            </div>
            
            {{-- Alert Success --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3 p-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div>
                            <strong>Berhasil!</strong> {{ session('success') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Alert Error --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3 p-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>
                            <strong class="d-block mb-1">Gagal memperbarui profil:</strong>
                            <ul class="mb-0 ps-3 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card profile-card shadow-sm mb-4">
                {{-- Decorative Header --}}
                <div class="profile-header-gradient"></div>
                
                <div class="card-body p-4 p-md-5">
                    {{-- Avatar & Identity --}}
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end gap-3 mb-5 avatar-wrapper">
                        <div class="avatar-circle">
                            {{ substr($user->username, 0, 2) }}
                        </div>
                        <div class="text-center text-md-start mb-2">
                            <h3 class="fw-bold mb-1 text-dark">{{ $user->username }}</h3>
                            <p class="text-secondary mb-1">
                                <i class="bi bi-envelope me-1"></i> {{ $user->email }}
                            </p>
                            <div>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold text-uppercase" style="font-size: 0.75rem;">Administrator</span>
                                @elseif($user->role === 'penyedia')
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold text-uppercase" style="font-size: 0.75rem;">Penyedia Ruangan</span>
                                @elseif($user->role === 'outsource')
                                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill fw-bold text-uppercase" style="font-size: 0.75rem;">Mitra Outsource</span>
                                @else
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold text-uppercase" style="font-size: 0.75rem;">Penyewa Ruangan</span>
                                @endif
                                
                                @if($user->status == 1)
                                    <span class="badge bg-success-subtle text-success border border-success px-2.5 py-1.5 rounded-pill fw-semibold ms-2" style="font-size: 0.7rem;">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1.5 rounded-pill fw-semibold ms-2" style="font-size: 0.7rem;">Nonaktif</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Form Edit Profil --}}
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3 text-dark">Informasi Akun</h5>
                        
                        <div class="row g-4 mb-5">
                            {{-- Username --}}
                            <div class="col-md-6">
                                <label for="username" class="form-label">Nama Pengguna</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="username" id="username" class="form-control border-start-0" value="{{ old('username', $user->username) }}" placeholder="Masukkan nama Anda" required>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control border-start-0" value="{{ old('email', $user->email) }}" placeholder="nama@email.com" required>
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6">
                                <label for="phone" class="form-label">No. Telepon / WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="phone" id="phone" class="form-control border-start-0" value="{{ old('phone', $user->phone) }}" placeholder="Masukkan nomor telepon" required>
                                </div>
                            </div>

                            {{-- Role (Readonly) --}}
                            <div class="col-md-6">
                                <label class="form-label">Peran Pengguna (Role)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light text-capitalize" value="{{ $user->role }}" readonly disabled>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3 text-dark">Keamanan (Ganti Password)</h5>
                        <p class="text-muted small mb-4 mt-n2">Kosongkan kolom di bawah jika Anda tidak ingin mengganti kata sandi.</p>

                        <div class="row g-4 mb-4">
                            {{-- Password Lama --}}
                            <div class="col-12">
                                <label for="old_password" class="form-label">Kata Sandi Lama</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
                                    <input type="password" name="old_password" id="old_password" class="form-control border-start-0" placeholder="Masukkan kata sandi saat ini">
                                </div>
                            </div>

                            {{-- Password Baru --}}
                            <div class="col-md-6">
                                <label for="password" class="form-label">Kata Sandi Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                    <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Minimal 8 karakter">
                                </div>
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0" placeholder="Ulangi kata sandi baru">
                                </div>
                            </div>
                        </div>

                        <div class="text-end pt-3">
                            <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
