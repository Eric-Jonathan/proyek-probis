@extends('layout.layout')

@section('content')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Header Navigasi --}}
            <div class="d-flex align-items-center mb-4">
                <a href="javascript:history.back()" class="btn btn-light rounded-circle me-3 shadow-sm border">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Tambah Pengguna Baru</h3>
                    <p class="text-secondary mb-0">Kelola akses sistem dengan mendaftarkan entitas baru ke dalam platform</p>
                </div>
            </div>

            <form action="{{ url('/admin/users/insert') }}" method="POST">
                @csrf
                
                {{-- Card Header Gradien --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3 fs-1">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Manajemen Hak Akses</h5>
                                <p class="mb-0 small opacity-75">Pastikan email dan role yang dipilih sesuai untuk menjaga keamanan integritas data sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Body Form --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3">Informasi Akun</h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Username *</label>
                                <input type="text" name="username" class="form-control bg-light border-0 py-2" placeholder="Masukkan username" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Alamat Email *</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2" placeholder="contoh@email.com" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Password *</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control bg-light border-0 py-2" placeholder="Minimal 6 karakter" required>
                                    <span class="input-group-text bg-light border-0" style="cursor: pointer;" id="togglePassword">
                                        <i class="bi bi-eye-slash text-muted"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control bg-light border-0 py-2" placeholder="0812xxxxxx">
                            </div>

                            <hr class="my-4 opacity-25">
                            <h5 class="fw-bold mb-2 border-start border-success border-4 ps-3 text-success">Level Akses & Organisasi</h5>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Role Akses *</label>
                                <select name="role" id="role" class="form-select bg-light border-0 py-2" required>
                                    <option value="" selected disabled>Pilih Hak Akses</option>
                                    <option value="admin">Admin</option>
                                    <option value="penyedia">Penyedia</option>
                                    <option value="penyewa">Penyewa</option>
                                    <option value="outsource">Outsource (Surveyor)</option>
                                </select>
                            </div>

                            {{-- Dropdown Company - Muncul jika role outsource dipilih --}}
                            <div class="col-md-6" id="company_field" style="display: none;">
                                <label class="form-label small text-uppercase fw-bold">Perusahaan (Company) *</label>
                                <select name="company" id="company" class="form-select bg-light border-0 py-2">
                                    <option value="" selected disabled>Pilih Perusahaan</option>
                                    <option value="DNet">DNet</option>
                                    <option value="Lintasarta">Lintasarta</option>
                                    <option value="Telkom">Telkom</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2 mb-5">
                    <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
                        Simpan Data Pengguna <i class="bi bi-check-circle-fill ms-2"></i>
                    </button>
                    <a href="{{ url('/admin/users') }}" class="btn btn-light px-4 py-3 rounded-pill fw-bold shadow-sm text-secondary border">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const companyField = document.getElementById('company_field');
        const companySelect = document.getElementById('company');
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        // Logika Tampil/Sembunyi Dropdown Company
        roleSelect.addEventListener('change', function () {
            if (this.value === 'outsource') {
                companyField.style.display = 'block';
                companySelect.setAttribute('required', 'required');
            } else {
                companyField.style.display = 'none';
                companySelect.removeAttribute('required');
                companySelect.value = ""; 
            }
        });

        // Toggle Show/Hide Password
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    });
</script>

<style>
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        border: 1px solid #0064D2 !important;
        box-shadow: none;
    }
</style>
@endsection