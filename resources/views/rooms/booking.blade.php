@extends('layout.layout')

@section('content')
<div class="container py-3">
    <button class="btn btn-white rounded-pill shadow-sm border px-3 mb-4 fw-medium" onclick="history.back()">
        <i class="bi bi-chevron-left me-1"></i> Back
    </button>
    
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="fw-bold mb-1">Finalize Your Event Booking</h2>
            <p class="text-secondary">Selesaikan detail pemesanan gedung untuk kelancaran acara Anda.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <img src="{{ asset('upload_room/1778432205_bryankisinjpg.jpg') }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">{{ $room->name }}</h5>
                    <p class="text-muted small mb-3"><i class="bi bi-geo-alt"></i> {{ $room->location }}</p>
                    
                    <div class="row g-2 small text-dark">
                        <div class="col-6"><i class="bi bi-people me-1 text-primary"></i> 500 Pax</div>
                        <div class="col-6"><i class="bi bi-mic me-1 text-primary"></i> Sound System</div>
                        <div class="col-6"><i class="bi bi-lightning me-1 text-primary"></i> 5000 Watt</div>
                        <div class="col-6"><i class="bi bi-wifi me-1 text-primary"></i> High Speed</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-primary border-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase small text-muted mb-3">Jadwal Acara</h6>
                    <div class="row">
                        <div class="col-6 border-end">
                            <p class="mb-0 small fw-bold">Tanggal Acara</p>
                            <p class="mb-0 fw-bold text-primary">12 Mei 2026</p>
                        </div>
                        <div class="col-6 ps-3">
                            <p class="mb-0 small fw-bold">Durasi Sewa</p>
                            <p class="mb-0 fw-bold">08:00 - 16:00</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold small text-muted text-uppercase mb-3">Ringkasan Biaya</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sewa Gedung Utama</span>
                        <span>Rp 5.000.000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success fw-medium">
                        <span>Potongan Paket</span>
                        <span>- Rp 250.000</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0 text-primary">Total</h4>
                        <div class="text-end">
                            <h4 class="fw-bold mb-0">Rp 4.750.000</h4>
                            <small class="text-muted">Termasuk pajak & biaya teknis</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4 rounded-4 bg-light p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 45px; height: 45px;">
                        <i class="bi bi-person-check fs-5"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold">Anda masuk sebagai {{ Auth::user()->name }}</p>
                        <p class="mb-0 text-muted small">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-4">Detail Pemesanan Acara</h4>
                
                <form action="#" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap / Instansi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Masukkan nama pemesan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jenis Acara <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="" disabled selected>Pilih jenis acara...</option>
                                <option>Wedding / Pernikahan</option>
                                <option>Seminar / Corporate</option>
                                <option>Social Gathering</option>
                                <option>Lainnya</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Nomor Telepon (WhatsApp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="number" class="form-control" placeholder="8123xxxx" required>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-uppercase small text-muted">Layanan Tambahan (Opsional)</h6>
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="catering">
                                    <label class="form-check-label fw-medium" for="catering">
                                        Layanan Katering (Prasmanan)
                                    </label>
                                    <p class="small text-muted mb-0">Hubungkan saya dengan vendor katering mitra.</p>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="decor">
                                    <label class="form-check-label fw-medium" for="decor">
                                        Paket Dekorasi & Lighting
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="it_support">
                                    <label class="form-check-label fw-medium" for="it_support">
                                        Support Teknis (Streaming/Operator IT)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label small fw-bold">Permintaan Khusus / Layout Ruangan</label>
                            <textarea class="form-control" rows="4" placeholder="Contoh: Pengaturan kursi teater, butuh akses loading barang H-1 malam, dsb."></textarea>
                        </div>

                        <div class="col-12 text-end mt-5">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                                Konfirmasi Pemesanan <i class="bi bi-chevron-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection