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
                    <h3 class="fw-bold mb-0">Pengajuan Denda</h3>
                    <p class="text-secondary mb-0">Laporkan kerusakan atau pelanggaran oleh penyewa</p>
                </div>
            </div>

            <form action="{{ route('penyedia.denda.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">

                <!-- Card Info Ringkas (Kontras Tinggi) -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="row">
                            <div class="col-md-6 border-end border-white border-opacity-25">
                                <label class="small fw-bold text-uppercase d-block mb-1" style="color: rgba(255,255,255,0.9); letter-spacing: 1px;">ID Booking</label>
                                <h5 class="fw-bold mb-3">#{{ $booking->booking_id }}</h5>
                                
                                <label class="small fw-bold text-uppercase d-block mb-1" style="color: rgba(255,255,255,0.9); letter-spacing: 1px;">Penyewa</label>
                                <p class="mb-0 fw-bold fs-5">{{ $booking->user->username }}</p>
                            </div>
                            <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
                                <label class="small fw-bold text-uppercase d-block mb-1" style="color: rgba(255,255,255,0.9); letter-spacing: 1px;">Unit Ruangan</label>
                                <h5 class="fw-bold mb-3">{{ $booking->details->room->name }}</h5>
                                
                                <label class="small fw-bold text-uppercase d-block mb-1" style="color: rgba(255,255,255,0.9); letter-spacing: 1px;">Total Sewa Sebelumnya</label>
                                <p class="mb-0 fw-bold">Rp {{ number_format($booking->total, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Detail Denda -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-start border-danger border-4 ps-3">Detail Pelanggaran</h5>
                        
                        <div class="row g-4">
                            <!-- Jenis Pelanggaran -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Jenis Pelanggaran *</label>
                                <select name="jenis_denda" class="form-select bg-light border-0 py-2" required>
                                    <option value="" selected disabled>Pilih Jenis...</option>
                                    <option value="kerusakan">Kerusakan Fasilitas/Properti</option>
                                    <option value="kebersihan">Pelanggaran Kebersihan Berat</option>
                                    <option value="overtime">Keterlambatan Check-out (Overtime)</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            <!-- Nilai Denda -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Nominal Denda (Rp) *</label>
                                <input type="text" name="nominal_denda" class="form-control bg-light border-0 py-2 thousand-separator" placeholder="Contoh: 500.000" required>
                            </div>

                            <!-- Penjelasan -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Kronologi & Penjelasan *</label>
                                <textarea name="keterangan" class="form-control bg-light border-0" rows="4" placeholder="Jelaskan secara rinci kerusakan atau aturan yang dilanggar..." required></textarea>
                            </div>

                            <!-- Bukti Foto -->
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase">Bukti Foto Pelanggaran *</label>
                                <div class="input-group">
                                    <input type="file" name="bukti_denda[]" class="form-control bg-light border-0 py-2" multiple required>
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-camera-fill text-danger"></i></span>
                                </div>
                                <div class="form-text mt-2 text-danger small">* Wajib melampirkan foto bukti kerusakan atau pelanggaran.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger px-5 py-3 rounded-pill fw-bold shadow-sm flex-grow-1">
                        Kirim Pengajuan Denda
                    </button>
                    <a href="{{ url()->previous() }}" class="btn btn-light px-4 py-3 rounded-pill fw-bold shadow-sm text-secondary border">
                        Batal
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
        border: 1px solid #dc3545 !important;
        box-shadow: none;
    }
    .btn-danger { background-color: #dc3545; border: none; }
    .btn-danger:hover { background-color: #a71d2a; transform: translateY(-2px); transition: all 0.2s; }
</style>
@endsection