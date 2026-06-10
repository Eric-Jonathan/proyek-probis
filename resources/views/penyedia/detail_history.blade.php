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
    .bg-booked-soft { background-color: #dbeafe; color: #1d4ed8; }
    .bg-warning-soft { background-color: #fef9c3; color: #a16207; }
    .bg-batal-soft { background-color: #fee2e2; color: #b91c1c; }

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
                <p class="text-secondary mb-0">ID Booking: <span class="text-primary fw-bold">#TRX-{{ $booking->booking_id }}</span></p>
            </div>
        </div>
        {{-- <button onclick="window.print()" class="btn btn-primary px-4 rounded-pill shadow-sm fw-bold">
            <i class="bi bi-printer me-2"></i> Cetak Laporan
        </button> --}}
    </div>

    <div class="card card-report shadow-sm">
        {{-- Report Banner --}}
        <div class="report-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-2">{{ $booking->roomDetail->room->name ?? 'Gedung' }}</h3>
                    <p class="mb-0 opacity-75"><i class="bi bi-geo-alt me-2"></i>{{ $booking->roomDetail->room->location ?? 'Lokasi tidak tersedia' }}</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @if($booking->status == 2)
                        <span class="badge-status bg-selesai-soft">
                            <i class="bi bi-check-circle-fill me-2"></i>Selesai
                        </span>
                    @elseif($booking->status == 1)
                        @if(strtotime($booking->end_date) < time())
                            <span class="badge-status bg-selesai-soft">
                                <i class="bi bi-check-circle-fill me-2"></i>Selesai (Acara Lewat)
                            </span>
                        @else
                            <span class="badge-status bg-booked-soft">
                                <i class="bi bi-calendar-event-fill me-2"></i>Booked
                            </span>
                        @endif
                    @elseif($booking->status == 3)
                        <span class="badge-status bg-warning-soft text-warning" style="color: #a16207 !important;">
                            <i class="bi bi-clock-history me-2"></i>Cicilan ({{ $booking->installments_paid }}/3)
                        </span>
                    @elseif($booking->status == 0)
                        <span class="badge-status bg-batal-soft">
                            <i class="bi bi-x-circle-fill me-2"></i>Dibatalkan
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="row">
                {{-- Data Penyewa --}}
                <div class="col-md-6 mb-4">
                    <h5 class="fw-bold mb-3 text-primary">Informasi Penyewa</h5>
                    <div class="mb-3">
                        <div class="info-label">Nama Pengguna</div>
                        <div class="info-value">{{ $booking->user->username ?? 'Guest' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Kontak / WhatsApp</div>
                        <div class="info-value">+62 {{ $booking->phone }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $booking->user->email ?? '-' }}</div>
                    </div>
                </div>

                {{-- Waktu Sewa --}}
                <div class="col-md-6 mb-4">
                    <h5 class="fw-bold mb-3 text-primary">Jadwal Penggunaan</h5>
                    <div class="mb-3">
                        <div class="info-label">Tanggal Penggunaan</div>
                        <div class="info-value">
                            @if(date('Y-m-d', strtotime($booking->start_date)) === date('Y-m-d', strtotime($booking->end_date)))
                                {{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('l, d F Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($booking->end_date)->translatedFormat('d F Y') }}
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Durasi Waktu</div>
                        <div class="info-value">
                            {{ date('H:i', strtotime($booking->start_date)) }} - {{ date('H:i', strtotime($booking->end_date)) }} WIB
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Kegiatan / Keperluan</div>
                        <div class="info-value">{{ $booking->event }}</div>
                    </div>
                </div>

                @if($booking->notes)
                    <div class="col-12 mb-4">
                        <div class="info-label">Catatan Khusus</div>
                        <div class="info-value fw-normal text-muted" style="font-size: 0.95rem;">
                            {{ $booking->notes }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="divider"></div>

            {{-- Rincian Biaya --}}
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <h5 class="fw-bold mb-4 text-primary text-md-end">Rincian Pembayaran</h5>
                    <div class="cost-item">
                        <span class="text-secondary">Harga Sewa Ruangan Utama</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($booking->roomDetail->item_price ?? 0, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($booking->serviceDetails->count() > 0)
                        @foreach($booking->serviceDetails as $service)
                            <div class="cost-item">
                                <span class="text-secondary">{{ $service->item_name }}</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($service->item_price, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    @endif

                    <div class="total-row">
                        <span>Total Pendapatan</span>
                        <span>Rp {{ number_format($booking->total, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-md-end text-muted small mt-2">
                        <i class="bi bi-shield-check me-1 text-success"></i> Metode Pembayaran: {{ $booking->method_payment }}
                    </p>
                </div>
            </div>

            {{-- Footer Laporan --}}
            <div class="mt-5 pt-5 border-top text-center">
                <p class="small text-muted mb-0">Laporan ini dibuat secara otomatis oleh Sistem Marketplace Tempat-In pada {{ date('d F Y, H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection