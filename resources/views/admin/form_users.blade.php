@extends('layout.layout')

@section('content')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.users') }}" class="btn btn-light rounded-circle me-3 shadow-sm border">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">{{ isset($user) ? 'Edit Informasi Pengguna' : 'Tambah Pengguna Baru' }}</h3>
                    <p class="text-secondary mb-0">{{ isset($user) ? 'Perbarui hak akses dan data profil entitas sistem' : 'Kelola akses sistem dengan mendaftarkan entitas baru ke dalam platform' }}</p>
                </div>
            </div>

            <form action="{{ url('/admin/users/insert') }}" method="POST">
                @csrf
                
                {{-- Lempar Hidden ID jika dalam kondisi Edit Data --}}
                @if(isset($user))
                    <input type="hidden" name="user_id" value="{{ $user->user_id }}">
                @endif
                
                {{-- Banner Error Handling --}}
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 p-3">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3">Informasi Akun</h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Username *</label>
                                <input type="text" name="username" value="{{ old('username', $user->username ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="Masukkan username" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Alamat Email *</label>
                                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="contoh@email.com" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Password {{ isset($user) ? '(Kosongkan jika tidak diubah)' : '*' }}</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control bg-light border-0 py-2" placeholder="Minimal 6 karakter" {{ isset($user) ? '' : 'required' }}>
                                    <span class="input-group-text bg-light border-0" style="cursor: pointer;" id="togglePassword">
                                        <i class="bi bi-eye-slash text-muted"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Nomor Telepon</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-control bg-light border-0 py-2" placeholder="0812xxxxxx">
                            </div>

                            <hr class="my-4 opacity-25">
                            <h5 class="fw-bold mb-2 border-start border-success border-4 ps-3 text-success">Level Akses & Organisasi</h5>

                            <div class="col-md-6">
                                <label class="form-label small text-uppercase fw-bold">Role Akses *</label>
                                <select name="role" id="role" class="form-select bg-light border-0 py-2" required>
                                    @php $currentRole = old('role', $user->role ?? ''); @endphp
                                    <option value="" disabled {{ $currentRole == '' ? 'selected' : '' }}>Pilih Hak Akses</option>
                                    <option value="admin" {{ $currentRole == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="penyedia" {{ $currentRole == 'penyedia' ? 'selected' : '' }}>Penyedia</option>
                                    <option value="penyewa" {{ $currentRole == 'penyewa' ? 'selected' : '' }}>Penyewa</option>
                                    <option value="outsource" {{ $currentRole == 'outsource' ? 'selected' : '' }}>Outsource (Surveyor)</option>
                                </select>
                            </div>

                            {{-- Dropdown Company Dinamis dari database outsources --}}
                            <div class="col-md-6" id="company_field" style="display: {{ $currentRole === 'outsource' ? 'block' : 'none' }};">
                                <label class="form-label small text-uppercase fw-bold">Perusahaan Naungan *</label>
                                <select name="company" id="company" class="form-select bg-light border-0 py-2">
                                    @php $currentCompany = old('company', $user->outsource_id ?? ''); @endphp
                                    <option value="" disabled {{ $currentCompany == '' ? 'selected' : '' }}>Pilih Perusahaan Mitra...</option>
                                    @foreach($companies as $c)
                                        <option value="{{ $c->outsource_id }}" {{ $currentCompany == $c->outsource_id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mb-5">
                    <button type="submit" class="btn {{ isset($user) ? 'btn-warning text-dark' : 'btn-primary' }} px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
                        {{ isset($user) ? 'Perbarui Data Pengguna' : 'Simpan Data Pengguna' }} <i class="bi bi-check-circle-fill ms-2"></i>
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn btn-light px-4 py-3 rounded-pill fw-bold shadow-sm text-secondary border">
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

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    });
</script>

<style>
    .form-control:focus, .form-select:focus { background-color: #fff !important; border: 1px solid #0064D2 !important; box-shadow: none; }
</style>
@endsection