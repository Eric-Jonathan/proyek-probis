@extends('layout.layout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('bookings.index') }}" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Laporan Penggunaan Ruangan</h3>
                    <p class="text-secondary mb-0">Lengkapi detail kondisi ruangan pasca pemakaian</p>
                </div>
            </div>

            <form action="{{ route('penyedia.report.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Card Info Ringkas -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
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
                                <label class="small opacity-75 mt-2">WAKTU SEWA</label>
                                <p class="mb-0 small">{{ date('d M Y', strtotime($booking->start_date)) }} ({{ date('H:i', strtotime($booking->start_date)) }} - {{ date('H:i', strtotime($booking->end_date)) }})</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-primary border-4 ps-3">Checklist Kondisi</h5>
                        
                        <div class="row g-4">
                            <!-- Status Kebersihan -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Tingkat Kebersihan</label>
                                <select name="kondisi_kebersihan" class="form-select bg-light border-0 py-2">
                                    <option value="sangat_bersih">Sangat Bersih (Seperti Semula)</option>
                                    <option value="normal">Normal (Perlu Pembersihan Ringan)</option>
                                    <option value="kotor">Kotor (Perlu Pembersihan Ekstra)</option>
                                </select>
                            </div>

                            <!-- Status Kerusakan -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Kondisi Fasilitas</label>
                                <select name="kondisi_fasilitas" class="form-select bg-light border-0 py-2">
                                    <option value="baik">Semua Berfungsi Baik</option>
                                    <option value="ada_kerusakan">Ada Kerusakan Kecil</option>
                                    <option value="rusak_berat">Ada Kerusakan Berat</option>
                                </select>
                            </div>

                            <!-- Catatan Tambahan -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Catatan Lapangan</label>
                                <textarea name="catatan" class="form-control bg-light border-0" rows="4" placeholder="Tuliskan jika ada barang tertinggal, kerusakan spesifik, atau keluhan penyewa..."></textarea>
                            </div>

                            <!-- Foto Kondisi Pasca Pakai -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Dokumentasi Foto</label>
                                <div class="input-group">
                                    <input type="file" name="foto_laporan[]" class="form-control" multiple>
                                    <span class="input-group-text bg-light"><i class="bi bi-camera"></i></span>
                                </div>
                                <div class="form-text mt-2">Unggah foto ruangan setelah dikosongkan (Bisa lebih dari 1 foto).</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Tindakan -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
                        Simpan Laporan & Selesaikan Pesanan
                    </button>
                    <a href="{{ route('bookings.denda', $booking->booking_id) }}" class="btn btn-outline-danger px-4 py-3 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-exclamation-triangle"></i> Lapor Denda
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8f9fa; }
    .form-select:focus, .form-control:focus {
        background-color: #fff !important;
        border: 1px solid #0064D2 !important;
        box-shadow: none;
    }
    .btn-primary { background-color: #0064D2; border: none; }
    .btn-primary:hover { background-color: #0056b3; }
</style>
@endsection