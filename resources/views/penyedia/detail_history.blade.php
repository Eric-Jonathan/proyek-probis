@extends('layout.layout')

@section('custom_css')
<style>
    body { background-color: #f8f9fa; }
    .card-report { border-radius: 15px; border: none; overflow: hidden; }
    .report-header { 
        background: linear-gradient(135deg, #0064D2 0%, #004a99 100%); 
        color: white; 
        padding: 2rem;
    }
    .info-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
    .info-value { font-size: 1rem; font-weight: 600; color: #1e293b; }
    .divider { border-top: 1px solid #f1f5f9; margin: 1.5rem 0; }
    
    /* Cost Table Styling */
    .cost-item { display: flex; justify-content: space-between; margin-bottom: 0.75rem; }
    .total-row { 
        display: flex; 
        justify-content: space-between; 
        margin-top: 1rem; 
        padding-top: 1rem; 
        border-top: 2px solid #f1f5f9;
        font-size: 1.25rem;
        font-weight: 800;
        color: #0064D2;
    }

    /* Badge Custom */
    .badge-status { font-size: 0.85rem; padding: 0.5rem 1.25rem; border-radius: 50px; font-weight: 700; }
    .bg-selesai-soft { background-color: #dcfce7; color: #15803d; }
    .bg-konfirmasi-soft { background-color: #fef9c3; color: #a16207; }

    @media print {
        .btn-print-hide { display: none !important; }
        .card-report { box-shadow: none !important; border: 1px solid #eee; }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    {{-- Top Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-4 btn-print-hide">
        <div class="d-flex align-items-center">
            <a href="javascript:history.back()" class="btn btn-light rounded-circle me-3 shadow-sm border">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">Laporan Detail Pemesanan</h4>
                <p class="text-secondary mb-0">ID Booking: <span class="text-primary fw-bold">#BK-99281</span></p>
            </div>
        </div>
        <button onclick="window.print()" class="btn btn-primary px-4 rounded-pill shadow-sm fw-bold">
            <i class="bi bi-printer me-2"></i> Cetak Laporan
        </button>
    </div>

    <div class="card card-report shadow-sm">
        {{-- Report Banner --}}
        <div class="report-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-2">Ruang Mawar 01</h3>
                    <p class="mb-0 opacity-75"><i class="bi bi-geo-alt me-2"></i>Gedung Utama, Lantai 2 - Kompleks Perkantoran Batu</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge-status bg-selesai-soft">
                        <i class="bi bi-check-circle-fill me-2"></i>Status: Selesai
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="row">
                {{-- Data Penyewa --}}
                <div class="col-md-6 mb-4">
                    <h5 class="fw-bold mb-3 text-primary">Informasi Penyewa</h5>
                    <div class="mb-3">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value">John Doe</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Kontak / WhatsApp</div>
                        <div class="info-value">+62 812-3456-7890</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Email</div>
                        <div class="info-value">johndoe@email.com</div>
                    </div>
                </div>

                {{-- Waktu Sewa --}}
                <div class="col-md-6 mb-4">
                    <h5 class="fw-bold mb-3 text-primary">Jadwal Penggunaan</h5>
                    <div class="mb-3">
                        <div class="info-label">Tanggal Penggunaan</div>
                        <div class="info-value">Selasa, 12 Mei 2026</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Durasi Waktu</div>
                        <div class="info-value">08:00 - 17:00 (9 Jam)</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Keperluan</div>
                        <div class="info-value">Rapat Koordinasi Tahunan (Internal)</div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- Rincian Biaya --}}
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <h5 class="fw-bold mb-4 text-primary text-md-end">Rincian Pembayaran</h5>
                    <div class="cost-item">
                        <span class="text-secondary">Harga Sewa Ruangan (9 Jam)</span>
                        <span class="fw-bold text-dark">Rp 675.000</span>
                    </div>
                    <div class="cost-item">
                        <span class="text-secondary">Layanan Kebersihan</span>
                        <span class="fw-bold text-dark">Rp 50.000</span>
                    </div>
                    <div class="cost-item">
                        <span class="text-secondary">Biaya Admin Sistem</span>
                        <span class="fw-bold text-dark">Rp 25.000</span>
                    </div>
                    <div class="total-row">
                        <span>Total Pendapatan</span>
                        <span>Rp 750.000</span>
                    </div>
                    <p class="text-md-end text-muted small mt-2">
                        <i class="bi bi-shield-check me-1"></i> Pembayaran Terverifikasi via Midtrans
                    </p>
                </div>
            </div>

            {{-- Footer Laporan --}}
            <div class="mt-5 pt-5 border-top text-center">
                <p class="small text-muted mb-0">Laporan ini dibuat secara otomatis oleh Sistem Marketplace Rental pada {{ date('d F Y, H:i') }}</p>
                <p class="small text-muted">Pastikan unit telah dicek oleh Surveyor sebelum dan sesudah penggunaan.</p>
            </div>
        </div>
    </div>
</div>
@endsection