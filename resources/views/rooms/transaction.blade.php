@extends('layout.layout')

@section('content')
<style>
    :root {
        --primary-blue: #006ce4;
        --light-blue: #e8f2ff;
        --success-green: #28a745;
        --soft-gray: #f8f9fa;
        --text-dark: #1f2937;
        --warning-orange: #ff9800;
        --warning-light: #fff3cd;
    }

    body {
        background: #f8f9fa;
    }

    .transaction-header {
        background: linear-gradient(135deg, #006ce4, #2b8cff);
        color: white;
        border-radius: 18px;
        padding: 28px;
    }

    .transaction-card {
        border: 1px solid #eef2f7;
        border-radius: 18px;
        background: white;
        transition: 0.25s ease;
    }

    .label-title {
        font-size: .78rem;
        color: #8b98a7;
        margin-bottom: 4px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .value-text {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 1rem;
    }

    .badge-status {
        background: rgba(255, 152, 0, 0.1);
        color: var(--warning-orange);
        padding: 8px 16px;
        border-radius: 999px;
        font-size: .85rem;
        font-weight: 700;
        border: 1px solid rgba(255, 152, 0, 0.2);
    }

    .price-box {
        background: var(--light-blue);
        color: var(--primary-blue);
        padding: 14px 18px;
        border-radius: 14px;
        font-size: 1.4rem;
        font-weight: 800;
        text-align: center;
        border: 1px solid rgba(0, 108, 228, 0.1);
    }

    .btn-midtrans {
        background: var(--primary-blue);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px 18px;
        font-weight: 700;
        transition: .2s;
        box-shadow: 0 4px 10px rgba(0, 108, 228, 0.2);
    }

    .btn-midtrans:hover {
        background: #0056b8;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(0, 108, 228, 0.3);
    }

    .mini-line {
        border-top: 1px dashed #e5e7eb;
    }

    .invoice-item {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
</style>

<div class="container py-5">
    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('bookings.history') }}" class="btn btn-light rounded-pill border px-3 fw-medium">
            <i class="bi bi-chevron-left me-1"></i> Kembali ke Riwayat Booking
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-octagon-fill me-2 fs-5 text-danger"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="transaction-header shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="mb-1 opacity-75">Detail Transaksi Penyewaan</p>
                <h2 class="fw-bold mb-0">Rincian Pembayaran</h2>
            </div>
            @if($booking->status == 1)
                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2 border border-success-subtle fw-bold">
                    <i class="bi bi-check-circle-fill me-1"></i> Terjadwal (Lunas)
                </span>
            @elseif($booking->status == 3)
                <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2 border border-warning-subtle fw-bold" style="color: #a16207 !important;">
                    <i class="bi bi-clock-history me-1"></i> Cicilan ({{ $booking->installments_paid }}/3)
                </span>
            @else
                <span class="badge-status">
                    <i class="bi bi-clock-history me-1"></i> Menunggu Pembayaran
                </span>
            @endif
        </div>
    </div>

    {{-- CARD --}}
    <div class="transaction-card p-4 shadow-sm">
        <div class="row g-4">
            {{-- LEFT SECTION: BOOKING DETAILS --}}
            <div class="col-lg-8">
                <h5 class="fw-bold mb-4 text-dark border-start border-primary border-3 ps-2">Informasi Pemesanan</h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="label-title">Nama Pemesan / Instansi</div>
                        <div class="value-text">{{ $booking->event }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-title">Tanggal Pemesanan</div>
                        <div class="value-text">
                            {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') }} WIB
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-title">Gedung / Ruangan</div>
                        <div class="value-text text-primary">{{ $room->name ?? 'Gedung' }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-title">WhatsApp Aktif</div>
                        <div class="value-text">+62 {{ $booking->phone }}</div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-title">Waktu Mulai Sewa</div>
                        <div class="value-text">
                            {{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('d F Y, H:i') }} WIB
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="label-title">Waktu Selesai Sewa</div>
                        <div class="value-text">
                            {{ \Carbon\Carbon::parse($booking->end_date)->translatedFormat('d F Y, H:i') }} WIB
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="label-title">Catatan Pemohon</div>
                        <div class="value-text fw-normal text-muted" style="font-size: 0.95rem;">
                            {{ $booking->notes ?? 'Tidak ada catatan tambahan.' }}
                        </div>
                    </div>
                </div>

                <div class="mini-line my-4"></div>

                <div class="small text-muted">
                    ID Transaksi :
                    <span class="fw-bold text-dark">
                        #TRX-{{ $booking->booking_id }}
                    </span>
                </div>
            </div>

            {{-- RIGHT SECTION: INVOICE BREAKDOWN & PAY BUTTON --}}
            <div class="col-lg-4">
                <div class="border rounded-4 p-4 h-100 bg-light d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-3 text-dark">Rincian Invoice</h5>
                        
                        <div class="invoice-item text-muted">
                            <span>Biaya Ruangan Utama</span>
                            <span class="fw-semibold">Rp {{ number_format($booking->roomDetail->item_price ?? 0, 0, ',', '.') }}</span>
                        </div>

                        @if($booking->serviceDetails->count() > 0)
                            <div class="mini-line my-2"></div>
                            <span class="d-block text-muted small fw-bold text-uppercase mb-2">Layanan Tambahan</span>
                            @foreach($booking->serviceDetails as $service)
                                <div class="invoice-item text-muted">
                                    <span>{{ $service->item_name }}</span>
                                    <span class="fw-semibold">Rp {{ number_format($service->item_price, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        @endif

                        <div class="mini-line my-3"></div>

                        @if($booking->status == 3)
                            <div class="invoice-item text-muted mb-2">
                                <span>Status Cicilan</span>
                                <span class="fw-bold text-warning" style="color: #a16207 !important;">Cicilan ({{ $booking->installments_paid }}/3)</span>
                            </div>
                            <div class="invoice-item text-muted mb-2">
                                <span>Uang Deposit</span>
                                <span class="fw-semibold text-secondary">Rp {{ number_format($deposit, 0, ',', '.') }}</span>
                            </div>
                            <div class="invoice-item text-muted mb-2">
                                <span>Sudah Dibayar</span>
                                <span class="fw-semibold text-success">
                                    Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}
                                    <small class="text-muted" style="font-size: 10px;">(inc. deposit)</small>
                                </span>
                            </div>
                            <div class="invoice-item text-muted mb-2">
                                <span>Sisa Tagihan Pokok</span>
                                <span class="fw-semibold text-danger">Rp {{ number_format($booking->total - ($booking->paid_amount - $deposit), 0, ',', '.') }}</span>
                            </div>
                            <div class="mini-line my-3"></div>
                            
                            <p class="text-muted small mb-2">Tagihan Cicilan Berikutnya</p>
                            <div class="price-box mb-3 bg-warning-subtle border-warning-subtle text-warning" style="background-color: #fff3cd !important; color: #a16207 !important; border-color: #ffeeba !important;">
                                Rp {{ number_format($nextPayment, 0, ',', '.') }}
                            </div>
                        @else
                            <p class="text-muted small mb-2">Total Pembayaran</p>
                            <div class="price-box mb-3">
                                Rp {{ number_format($booking->total, 0, ',', '.') }}
                            </div>
                        @endif

                        <div class="p-3 rounded-3 mb-4 text-center border" style="background-color: #fff;">
                            <div class="small text-muted mb-1"><i class="bi bi-wallet2 me-1"></i> Saldo Tempat-In Anda</div>
                            <div class="fw-bold text-dark fs-5">Rp {{ number_format(Auth::user()->saldo, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div>
                        @if($booking->status == 1 || $booking->status == 2)
                            <div class="alert alert-success border-0 shadow-sm rounded-3 p-3 mb-3 text-start">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <div>
                                        <strong class="text-success" style="font-size: 0.9rem;">Pembayaran Lunas</strong>
                                        <p class="small text-muted mb-0 mt-1">Pemesanan ini sudah dilunasi sepenuhnya dan terjadwal dengan aman.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            @if(Auth::user()->saldo >= $nextPayment)
                                <form action="{{ route('booking.pay', $booking->booking_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-midtrans w-100 mb-3 py-3 bg-success border-0" onclick="return confirm('Apakah Anda yakin ingin membayar cicilan booking ini sebesar Rp {{ number_format($nextPayment, 0, ',', '.') }} menggunakan Saldo Tempat-In?')">
                                        <i class="bi bi-wallet2 me-2"></i> Bayar Cicilan Sekarang
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-danger border-0 shadow-sm rounded-3 p-3 mb-3 text-start">
                                    <div class="d-flex gap-2">
                                        <i class="bi bi-exclamation-octagon-fill text-danger fs-5"></i>
                                        <div>
                                            <strong class="text-danger" style="font-size: 0.9rem;">Saldo Tidak Cukup!</strong>
                                            <p class="small text-muted mb-0 mt-1">Saldo Anda tidak mencukupi untuk melakukan pembayaran cicilan ini. Silakan top up saldo terlebih dahulu.</p>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('topup.show') }}" class="btn btn-primary w-100 mb-3 py-3 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center justify-content-center" style="text-decoration: none;">
                                    <i class="bi bi-plus-lg me-2"></i> Top Up Saldo
                                </a>
                            @endif
                        @endif
                        <div class="small text-muted text-center" style="font-size: 11px;">
                            Pembayaran instan dipotong langsung dari saldo Tempat-In Anda.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection