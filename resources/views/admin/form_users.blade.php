@extends('layout.layout')

@section('custom_css')
    <style>
        /* Desain Header Form - Biru Muda Soft sesuai Tema */
        .card-header-form {
            background-color: #dbeafe !important;
            border-bottom: 1px solid #bfdbfe !important;
            padding: 1.25rem 1.5rem !important;
        }
        
        .card-header-form h5 {
            color: #1e3a8a !important;
            font-weight: 700;
            margin-bottom: 0;
            font-size: 1.1rem;
        }

        /* Styling Input agar Clean */
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            border-radius: 0.6rem;
            font-size: 0.9rem;
            background-color: #fcfcfd;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: #4338ca;
            box-shadow: 0 0 0 4px rgba(67, 56, 202, 0.1);
            outline: none;
        }

        /* Button Styling */
        .btn-save {
            background-color: #1e3a8a !important;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.6rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-save:hover {
            background-color: #172554 !important;
            transform: translateY(-1px);
        }

        .btn-cancel {
            background-color: #f1f5f9;
            color: #64748b;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.6rem;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-2">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin/users') }}" class="text-decoration-none" style="color: #1e3a8a;">Daftar Pengguna</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Pengguna</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header-form">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-plus-fill me-2 fs-5" style="color: #1e3a8a;"></i>
                        <h5>Tambah Pengguna</h5>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <form action="{{ url('/admin/users/insert') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="contoh@email.com" required>
                            </div>

                            <div class="col-md-12">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control border-end-0" id="password" name="password" placeholder="Minimal 6 karakter" required>
                                    <span class="input-group-text bg-transparent border-start-0" style="cursor: pointer;">
                                        <i class="bi bi-eye-slash text-muted"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="0812xxxxxx">
                            </div>

                            <div class="col-md-6">
                                <label for="role" class="form-label">Role Akses</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="" selected disabled>Pilih Hak Akses</option>
                                    <option value="admin">Admin</option>
                                    <option value="penyedia">Penyedia</option>
                                    <option value="penyewa">Penyewa</option>
                                    <option value="outsource">Outsource</option>
                                </select>
                            </div>

                            <div class="col-12 mt-5 d-flex justify-content-end gap-2">
                                <a href="{{ url('/admin/users') }}" class="btn btn-cancel">Batal</a>
                                <button type="submit" class="btn btn-primary btn-save text-white shadow-sm">
                                    <i class="bi bi-check2-circle me-2"></i>Simpan Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection