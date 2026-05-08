@extends('layout.layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Header Navigasi -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('bookings.index') }}" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0 text-danger">Pengajuan Denda / Penalti</h3>
                    <p class="text-secondary mb-0">Kirim tagihan tambahan atas kerusakan atau pelanggaran aturan</p>
                </div>
            </div>

            <form action="{{ route('penyedia.denda.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- Card Info Ringkas (Gaya Report, Warna Merah Danger) -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="row">
                            <div class="col-md-6 border-end border-white border-opacity-25">
                                <label class="small opacity-75">ID BOOKING</label>
                                <h5 class="fw-bold">#{{ $booking->booking_id }}</h5>
                                <label class="small opacity-75 mt-2">NAMA PENYEWA</label>
                                <p class="mb-0 fw-bold">{{ $booking->customer_name }}</p>
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <label class="small opacity-75">NAMA RUANGAN</label>
                                <h5 class="fw-bold">{{ $booking->room_name }}</h5>
                                <label class="small opacity-75 mt-2">STATUS TINDAKAN</label>
                                <p class="mb-0 small fw-bold text-warning"><i class="bi bi-exclamation-triangle-fill"></i> Penagihan Tambahan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Input Denda -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-danger border-4 ps-3">Detail Pelanggaran</h5>
                        
                        <div class="row g-4">
                            <!-- Kategori Denda -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Kategori Pelanggaran</label>
                                <select name="kategori" class="form-select bg-light border-0 py-2" required>
                                    <option value="" selected disabled>Pilih kategori...</option>
                                    <option value="kerusakan">Kerusakan Properti/Fasilitas</option>
                                    <option value="kebersihan">Kebersihan Ekstra (Sampah/Noda)</option>
                                    <option value="waktu">Overtime (Kelebihan Durasi)</option>
                                    <option value="aturan">Pelanggaran Aturan Lainnya</option>
                                </select>
                            </div>

                            <!-- Nominal Denda -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Jumlah Denda (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 fw-bold">Rp</span>
                                    <input type="number" name="nominal" class="form-control bg-light border-0 py-2 fw-bold text-danger" placeholder="Contoh: 500000" required>
                                </div>
                            </div>

                            <!-- Deskripsi Pelanggaran -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Deskripsi Detail</label>
                                <textarea name="deskripsi" class="form-control bg-light border-0" rows="4" placeholder="Jelaskan secara rinci kronologi atau jenis kerusakan yang terjadi..." required></textarea>
                            </div>

                            <!-- Bukti Foto -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Bukti Foto Kerusakan</label>
                                <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light border-secondary-subtle">
                                    <i class="bi bi-camera-fill fs-1 text-secondary mb-2"></i>
                                    <input type="file" name="bukti_foto" class="form-control mt-2">
                                    <div class="form-text">Unggah foto sebagai bukti fisik pelanggaran (Maks 2MB).</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Tindakan -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
                        Kirim Tagihan Denda
                    </button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-light px-4 py-3 rounded-pill fw-bold shadow-sm text-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8f9fa; }
    .border-dashed { border-style: dashed !important; }
    
    /* Focus State - Warna Merah */
    .form-select:focus, .form-control:focus {
        background-color: #fff !important;
        border: 1px solid #dc3545 !important;
        box-shadow: none;
    }

    /* Button Danger Custom */
    .btn-danger {
        background-color: #dc3545;
        border: none;
        transition: all 0.3s ease;
    }
    .btn-danger:hover {
        background-color: #bb2d3b;
        transform: translateY(-2px);
    }
</style>
@endsection