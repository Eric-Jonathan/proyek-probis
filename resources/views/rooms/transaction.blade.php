@extends('layout.layout')

@section('content')
@php
    $start = \Carbon\Carbon::parse($booking->start_date);
    $end = \Carbon\Carbon::parse($booking->end_date);
    $days = (int) round(max(1, $start->diffInDays($end) + 1));
    
    if (!function_exists('formatDurationText')) {
        function formatDurationText($days) {
            $days = (int) round($days);
            $years = intval($days / 365);
            $remainingDays = $days % 365;
            
            $months = intval($remainingDays / 30);
            $remainingDays = $remainingDays % 30;
            
            $weeks = intval($remainingDays / 7);
            $remainingDays = $remainingDays % 7;
            
            $result = [];
            
            if ($years > 0) {
                $result[] = "{$years} Tahun";
                if ($months > 0) {
                    $result[] = "{$months} Bulan";
                }
                $daysCount = ($weeks * 7) + $remainingDays;
                if ($daysCount > 0) {
                    $result[] = "{$daysCount} Hari";
                }
            } elseif ($months > 0) {
                $result[] = "{$months} Bulan";
                $daysCount = ($weeks * 7) + $remainingDays;
                if ($daysCount > 0) {
                    $result[] = "{$daysCount} Hari";
                }
            } elseif ($weeks > 0) {
                $result[] = "{$weeks} Minggu";
                if ($remainingDays > 0) {
                    $result[] = "{$remainingDays} Hari";
                }
            } else {
                $result[] = "{$days} Hari";
            }
            
            return implode(' ', $result);
        }
    }
    
    // Let's check room price and booking details
    $roomPrice = $booking->roomDetail->room->price ?? 0;
    $jenisHarga = strtolower(trim($booking->roomDetail->room->jenis_harga ?? ''));
    $minPax = $booking->roomDetail->room->min_order ?? 1;
    
    // Calculate total hours if Hourly
    $durationHours = max(1, $start->diffInHours($end));
    if ($durationHours <= 0) {
        $durationHours = 1;
    }
    
    // Reverse calculate actual pax based on price paid
    $actualPax = $minPax;
    if ($roomPrice > 0) {
        $roomBasePrice = $booking->roomDetail->item_price ?? 0;
        if ($jenisHarga === 'pax') {
            $actualPax = (int) round($roomBasePrice / $roomPrice);
        } elseif ($jenisHarga === 'pax_hari') {
            $actualPax = (int) round($roomBasePrice / ($roomPrice * $days));
        } elseif ($jenisHarga === 'pax_jam') {
            $actualPax = (int) round($roomBasePrice / ($roomPrice * $durationHours * $days));
        }
    }
    $actualPax = max(1, $actualPax);
    
    $formulaText = '';
    if ($jenisHarga === 'pax') {
        $formulaText = '(Rp ' . number_format($roomPrice, 0, ',', '.') . ' / Pax x ' . $actualPax . ' Pax)';
    } elseif ($jenisHarga === 'pax_hari') {
        $formulaText = '(Rp ' . number_format($roomPrice, 0, ',', '.') . ' / Pax/Hari x ' . $actualPax . ' Pax x ' . $days . ' Hari)';
    } elseif ($jenisHarga === 'hari') {
        $formulaText = '(Rp ' . number_format($roomPrice, 0, ',', '.') . ' / Hari x ' . $days . ' Hari)';
    } elseif ($jenisHarga === 'jam') {
        $formulaText = '(Rp ' . number_format($roomPrice, 0, ',', '.') . ' / Jam x ' . $durationHours . ' Jam x ' . $days . ' Hari)';
    } elseif ($jenisHarga === 'pax_jam') {
        $formulaText = '(Rp ' . number_format($roomPrice, 0, ',', '.') . ' / Pax/Jam x ' . $actualPax . ' Pax x ' . $durationHours . ' Jam x ' . $days . ' Hari)';
    }
    
    $invoiceCode = 'INV/' . \Carbon\Carbon::parse($booking->created_at)->format('Ymd') . '/TRX/' . str_pad($booking->booking_id, 4, '0', STR_PAD_LEFT);
@endphp
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
        background-color: var(--bs-tertiary-bg);
    }

    .transaction-header {
        background: linear-gradient(135deg, #006ce4, #2b8cff);
        color: white;
        border-radius: 18px;
        padding: 28px;
    }

    .transaction-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 18px;
        background-color: var(--bs-card-bg);
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
        color: var(--bs-body-color);
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
        <a href="{{ route('bookings.history') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-medium">
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
                <p class="mb-1 opacity-75 fw-bold"><i class="bi bi-file-earmark-text me-1"></i> Invoice: {{ $invoiceCode }}</p>
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
                        <div class="label-title">Jumlah Orang (Pax)</div>
                        <div class="value-text">{{ $actualPax }} Orang</div>
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

                {{-- INFORMASI PENGELOLA RUANGAN --}}
                <div class="mt-4 p-3 rounded-4 border d-flex align-items-center justify-content-between flex-wrap gap-3" style="background-color: rgba(13, 110, 253, 0.1) !important; border-color: rgba(13, 110, 253, 0.2) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary text-white p-2 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-person-workspace fs-4"></i>
                        </div>
                        <div>
                            <div class="small text-muted fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">Pengelola Ruangan (Penyedia)</div>
                            <div class="fw-bold text-dark">{{ $booking->roomDetail->room->owner->username ?? 'Pemilik Ruangan' }}</div>
                            <div class="small text-muted">+62 {{ $booking->roomDetail->room->owner->phone ?? '-' }}</div>
                        </div>
                    </div>
                    @if($booking->roomDetail->room->owner->phone)
                        @php
                            $ownerPhone = $booking->roomDetail->room->owner->phone;
                            if (str_starts_with($ownerPhone, '+62')) {
                                $ownerPhone = substr($ownerPhone, 3);
                            } elseif (str_starts_with($ownerPhone, '62')) {
                                $ownerPhone = substr($ownerPhone, 2);
                            } elseif (str_starts_with($ownerPhone, '0')) {
                                $ownerPhone = substr($ownerPhone, 1);
                            }
                            $ownerPhoneFormatted = '62' . $ownerPhone;
                        @endphp
                        <a href="https://wa.me/{{ $ownerPhoneFormatted }}?text=Halo%20{{ urlencode($booking->roomDetail->room->owner->username) }},%20saya%20ingin%20koordinasi%20terkait%20booking%20#{{ $booking->booking_id }}" 
                           target="_blank" class="btn btn-sm btn-success rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-whatsapp"></i> Hubungi Pengelola
                        </a>
                    @endif
                </div>

                @php
                    $isInstallment = str_contains(strtolower($booking->method_payment), 'cicilan');
                    $createdAt = \Carbon\Carbon::parse($booking->created_at);
                    if ($days < 7) {
                        $intervalLabel = "Beberapa Hari (Jatuh Tempo 2 Hari Sekali)";
                        $dueDate1 = $createdAt->copy();
                        $dueDate2 = $createdAt->copy()->addDays(2);
                        $dueDate3 = $createdAt->copy()->addDays(4);
                    } elseif ($days <= 30) {
                        $intervalLabel = "Beberapa Minggu (Jatuh Tempo 1 Minggu Sekali)";
                        $dueDate1 = $createdAt->copy();
                        $dueDate2 = $createdAt->copy()->addWeeks(1);
                        $dueDate3 = $createdAt->copy()->addWeeks(2);
                    } else {
                        $intervalLabel = "Beberapa Bulan (Jatuh Tempo 1 Bulan Sekali)";
                        $dueDate1 = $createdAt->copy();
                        $dueDate2 = $createdAt->copy()->addMonths(1);
                        $dueDate3 = $createdAt->copy()->addMonths(2);
                    }
                @endphp

                @if($isInstallment)
                    <div class="mt-4 p-4 rounded-4 border shadow-sm" style="background-color: var(--bs-tertiary-bg); border-color: var(--bs-border-color) !important;">
                        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-check-fill text-primary"></i> Jadwal Jatuh Tempo Cicilan
                        </h5>
                        <div class="alert alert-info border-0 py-2 px-3 rounded-3 mb-3 d-flex align-items-center gap-2" style="font-size: 0.82rem;">
                            <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
                            <div>
                                Analisis Durasi Booking: <strong>{{ formatDurationText($days) }}</strong> (Kategori: <strong>{{ $intervalLabel }}</strong>)
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 align-middle text-center" style="font-size: 0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">Tahap Pembayaran</th>
                                        <th width="40%">Jatuh Tempo</th>
                                        <th width="25%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold text-start ps-3">Cicilan Ke-1 (DP 1/3 + Deposit)</td>
                                        <td>{{ $dueDate1->translatedFormat('d M Y, H:i') }} WIB</td>
                                        <td>
                                            @if($booking->installments_paid >= 1)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Lunas</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">Menunggu</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start ps-3">Cicilan Ke-2 (1/3 Pokok)</td>
                                        <td>{{ $dueDate2->translatedFormat('d M Y') }} (23:59 WIB)</td>
                                        <td>
                                            @if($booking->installments_paid >= 2)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Lunas</span>
                                            @elseif($booking->installments_paid == 1)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Belum Dibayar</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">Belum Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-start ps-3">Cicilan Ke-3 (Sisa Pokok)</td>
                                        <td>{{ $dueDate3->translatedFormat('d M Y') }} (23:59 WIB)</td>
                                        <td>
                                            @if($booking->installments_paid >= 3)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Lunas</span>
                                            @elseif($booking->installments_paid == 2)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">Belum Dibayar</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">Belum Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="mini-line my-4"></div>

                <div class="small text-muted">
                    ID Transaksi :
                    <span class="fw-bold text-dark">
                        {{ $invoiceCode }}
                    </span>
                </div>
            </div>

            {{-- RIGHT SECTION: INVOICE BREAKDOWN & PAY BUTTON --}}
            <div class="col-lg-4">
                <div class="border rounded-4 p-4 h-100 bg-light d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-3 text-dark">Rincian Invoice</h5>
                        
                        <div class="invoice-item text-muted align-items-start">
                            <div>
                                <span>Biaya Ruangan Utama</span>
                                <span class="d-block small text-muted font-monospace" style="font-size: 0.72rem; line-height: 1.2;">{{ $formulaText }}</span>
                            </div>
                            <span class="fw-semibold text-end">Rp {{ number_format($booking->roomDetail->item_price ?? 0, 0, ',', '.') }}</span>
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

                        @php
                            $baseTotal = ($booking->roomDetail->item_price ?? 0) + $booking->serviceDetails->sum('item_price');
                            $serviceFee = (int) round($baseTotal * 0.05);
                            $renterTotal = $baseTotal + $serviceFee;
                        @endphp
                        <div class="mini-line my-2"></div>
                        <div class="invoice-item text-muted text-primary">
                            <span class="fw-bold">Biaya Layanan (5%)</span>
                            <span class="fw-bold">Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                        </div>

                        <div class="mini-line my-3"></div>

                        @if($booking->status == 4)
                            @php
                                $isInstallment = str_contains(strtolower($booking->method_payment), 'cicilan');
                            @endphp
                            @if($isInstallment)
                                <div class="invoice-item text-muted mb-2">
                                    <span>Skema Pembayaran</span>
                                    <span class="fw-bold text-warning" style="color: #a16207 !important;">Cicilan (3x)</span>
                                </div>
                                <div class="invoice-item text-muted mb-2">
                                    <span>Uang Deposit</span>
                                    <span class="fw-semibold text-secondary">Rp {{ number_format($deposit, 0, ',', '.') }}</span>
                                </div>
                                <div class="invoice-item text-muted mb-2">
                                    <span>Cicilan Ke-1</span>
                                    <span class="fw-semibold text-secondary">Rp {{ number_format((int)ceil($baseTotal / 3), 0, ',', '.') }}</span>
                                </div>
                                <div class="mini-line my-3"></div>
                                <p class="text-muted small mb-2">Pembayaran Awal Harus Dibayar</p>
                                <div class="price-box mb-3 bg-warning-subtle border-warning-subtle text-warning" style="background-color: #fff3cd !important; color: #a16207 !important; border-color: #ffeeba !important;">
                                    Rp {{ number_format($nextPayment, 0, ',', '.') }}
                                </div>
                                @if($booking->installment_due_date)
                                    <div class="alert alert-warning border-0 p-2 text-center rounded-3 mb-3" style="font-size: 0.85rem;">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($booking->installment_due_date)->translatedFormat('d F Y') }}
                                    </div>
                                @endif
                            @else
                                <div class="invoice-item text-muted mb-2">
                                    <span>Skema Pembayaran</span>
                                    <span class="fw-bold text-success">Bayar Lunas (100%)</span>
                                </div>
                                <div class="mini-line my-3"></div>
                                <p class="text-muted small mb-2">Total Pembayaran</p>
                                <div class="price-box mb-3">
                                    Rp {{ number_format($nextPayment, 0, ',', '.') }}
                                </div>
                            @endif
                        @elseif($booking->status == 3)
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
                                <span class="fw-semibold text-danger">Rp {{ number_format($baseTotal - ($booking->paid_amount - $deposit - $serviceFee), 0, ',', '.') }}</span>
                            </div>
                            <div class="mini-line my-3"></div>
                            
                            <p class="text-muted small mb-2">Tagihan Cicilan Berikutnya</p>
                            <div class="price-box mb-3 bg-warning-subtle border-warning-subtle text-warning" style="background-color: #fff3cd !important; color: #a16207 !important; border-color: #ffeeba !important;">
                                Rp {{ number_format($nextPayment, 0, ',', '.') }}
                            </div>
                            @if($booking->installment_due_date)
                                <div class="alert alert-warning border-0 p-2 text-center rounded-3 mb-3" style="font-size: 0.85rem;">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($booking->installment_due_date)->translatedFormat('d F Y') }}
                                </div>
                            @endif
                        @else
                            <p class="text-muted small mb-2">Total Pembayaran</p>
                            <div class="price-box mb-3">
                                Rp {{ number_format($renterTotal, 0, ',', '.') }}
                            </div>
                        @endif

                        {{-- Distribution card removed per client feedback --}}

                        <div class="p-3 rounded-3 mb-4 text-center border" style="background-color: var(--bs-tertiary-bg); border-color: var(--bs-border-color) !important;">
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
                                    @if($booking->status == 4)
                                        <button type="submit" class="btn btn-midtrans btn-confirm-action w-100 mb-3 py-3 bg-success border-0" data-confirm="Apakah Anda yakin ingin membayar pemesanan booking ini sebesar Rp {{ number_format($nextPayment, 0, ',', '.') }} menggunakan Saldo Tempat-In?">
                                            <i class="bi bi-wallet2 me-2"></i> Bayar Sekarang
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-midtrans btn-confirm-action w-100 mb-3 py-3 bg-success border-0" data-confirm="Apakah Anda yakin ingin membayar cicilan booking ini sebesar Rp {{ number_format($nextPayment, 0, ',', '.') }} menggunakan Saldo Tempat-In?">
                                            <i class="bi bi-wallet2 me-2"></i> Bayar Cicilan Sekarang
                                        </button>
                                    @endif
                                </form>
                            @else
                                <div class="alert alert-danger border-0 shadow-sm rounded-3 p-3 mb-3 text-start">
                                    <div class="d-flex gap-2">
                                        <i class="bi bi-exclamation-octagon-fill text-danger fs-5"></i>
                                        <div>
                                            <strong class="text-danger" style="font-size: 0.9rem;">Saldo Tidak Cukup!</strong>
                                            <p class="small text-muted mb-0 mt-1">Saldo Anda tidak mencukupi untuk melakukan pembayaran ini. Silakan top up saldo terlebih dahulu.</p>
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