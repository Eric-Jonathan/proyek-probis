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

    {{-- Simulation Mode Notice --}}
    @if($isSimulated)
        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center" style="background-color: var(--warning-light); border-left: 5px solid var(--warning-orange) !important;">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-3 text-warning"></i>
            <div>
                <strong class="text-dark">Mode Simulasi Pembayaran Aktif</strong><br>
                <span class="text-muted small">Midtrans Client/Server Keys belum dikonfigurasi di file <code>.env</code>. Menekan tombol bayar akan langsung mensimulasikan pembayaran berhasil di database lokal Anda.</span>
            </div>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="transaction-header shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="mb-1 opacity-75">Detail Transaksi Penyewaan</p>
                <h2 class="fw-bold mb-0">Rincian Pembayaran</h2>
            </div>
            <span class="badge-status">
                <i class="bi bi-clock-history me-1"></i> Menunggu Pembayaran
            </span>
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

                        <p class="text-muted small mb-2">Total Pembayaran</p>
                        <div class="price-box mb-4">
                            Rp {{ number_format($booking->total, 0, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <button id="pay-button" class="btn btn-midtrans w-100 mb-3 py-3">
                            <i class="bi bi-wallet2 me-2"></i> Bayar Sekarang (Midtrans)
                        </button>
                        <div class="small text-muted text-center" style="font-size: 11px;">
                            Secure payment gateway by Midtrans Sandbox.<br>Jaminan keamanan transaksi Anda.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MIDTRANS SNAP INTEGRATION --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.getElementById('pay-button').onclick = function (e) {
        e.preventDefault();
        
        const isSimulated = {{ $isSimulated ? 'true' : 'false' }};
        
        if (isSimulated) {
            // Jalankan simulation flow dengan AJAX post ke server
            $.post('{{ route("booking.payment_callback", ["booking_id" => $booking->booking_id]) }}', {
                _token: '{{ csrf_token() }}',
                status: 'success'
            }).done(function(response) {
                alert("Simulasi Pembayaran Midtrans Sandbox Berhasil! Menghubungkan ke database...");
                window.location.href = '{{ route("bookings.history") }}?success=1';
            }).fail(function() {
                alert("Simulasi gagal. Terjadi kesalahan pada server.");
            });
        } else {
            // Jalankan real Midtrans Sandbox Snap Flow
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    $.post('{{ route("booking.payment_callback", ["booking_id" => $booking->booking_id]) }}', {
                        _token: '{{ csrf_token() }}',
                        status: 'success'
                    }).done(function() {
                        alert("Pembayaran Berhasil! Status pemesanan Anda telah aktif.");
                        window.location.href = '{{ route("bookings.history") }}?success=1';
                    });
                },
                onPending: function(result){
                    alert("Menunggu Pembayaran...");
                },
                onError: function(result){
                    alert("Pembayaran Gagal! Silakan coba lagi.");
                },
                onClose: function(){
                    alert("Anda menutup jendela pembayaran sebelum menyelesaikan transaksi.");
                }
            });
        }
    };
</script>
@endsection