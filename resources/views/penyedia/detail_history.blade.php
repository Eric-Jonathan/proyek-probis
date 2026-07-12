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
@php
    $start = \Carbon\Carbon::parse($booking->start_date);
    $end = \Carbon\Carbon::parse($booking->end_date);
    $days = (int) round(max(1, $start->diffInDays($end) + 1));
    
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
<div class="container py-4">
    {{-- Top Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-4 btn-print-hide">
        <div class="d-flex align-items-center">
            <a href="javascript:history.back()" class="btn btn-light rounded-circle me-3 shadow-sm border">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0">Invoice Pendapatan Penyedia</h4>
                <p class="text-secondary mb-0">No. Invoice: <span class="text-primary fw-bold">{{ $invoiceCode }}</span></p>
            </div>
        </div>
        <a href="{{ route('penyedia.booking.pdf', $booking->booking_id) }}" class="btn btn-danger px-4 rounded-pill shadow-sm fw-bold">
            <i class="bi bi-file-earmark-pdf me-2"></i> Unduh PDF
        </a>
    </div>

    <div class="card card-report shadow-sm">
        {{-- Report Banner --}}
        <div class="report-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="small text-white-50 text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem; letter-spacing: 1px;">Gedung / Ruangan Utama</span>
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
                    @elseif($booking->status == 4)
                        <span class="badge-status bg-warning-soft text-warning" style="color: #a16207 !important;">
                            <i class="bi bi-clock-history me-2"></i>Menunggu Pembayaran
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

            @php
                $isInstallment = str_contains(strtolower($booking->method_payment), 'cicilan');
            @endphp

            @if($isInstallment)
                <div class="divider"></div>
                <div class="row">
                    <div class="col-12 mb-4">
                        <h5 class="fw-bold mb-3 text-warning"><i class="bi bi-clock-history me-2"></i>Status & Jatuh Tempo Cicilan</h5>
                        <div class="p-4 rounded-4 border bg-warning-subtle" style="background-color: #fffbeb !important; border-color: #fde68a !important;">
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="mb-2">
                                        <span class="info-label d-block text-secondary">Cicilan Terbayar</span>
                                        <span class="info-value text-dark fw-bold" style="font-size: 1.15rem;">{{ $booking->installments_paid }} dari 3 Kali Pembayaran</span>
                                    </div>
                                    <div>
                                        <span class="info-label d-block text-secondary">Batas Waktu Pembayaran (Jatuh Tempo)</span>
                                        @if($booking->installment_due_date)
                                            <span class="info-value text-danger fw-bold" style="font-size: 1.15rem;">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ \Carbon\Carbon::parse($booking->installment_due_date)->translatedFormat('l, d F Y') }}
                                            </span>
                                        @else
                                            <span class="info-value text-muted fw-bold" style="font-size: 1.15rem;">Belum ditentukan oleh Anda</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    @if($booking->status == 3 || $booking->status == 4)
                                        <form action="{{ route('penyedia.booking.update_due_date', $booking->booking_id) }}" method="POST" class="d-inline-block text-start w-100" style="max-width: 350px;">
                                            @csrf
                                            <label for="installment_due_date" class="form-label small fw-bold text-secondary mb-1">Atur / Ubah Tanggal Jatuh Tempo</label>
                                            <div class="input-group">
                                                <input type="date" class="form-control rounded-start-pill" id="installment_due_date" name="installment_due_date" 
                                                       value="{{ $booking->installment_due_date ?? '' }}" required min="{{ date('Y-m-d') }}">
                                                <button class="btn btn-warning text-dark fw-bold rounded-end-pill px-3" type="submit">
                                                    <i class="bi bi-save me-1"></i> Simpan
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Cicilan Sudah Selesai/Lunas</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="divider"></div>

            {{-- Rincian Biaya --}}
            <div class="row justify-content-end">
                <div class="col-md-6 col-lg-5">
                    <h5 class="fw-bold mb-4 text-primary text-md-end">Rincian Pendapatan</h5>
                    <div class="cost-item align-items-start">
                        <div>
                            <span class="text-secondary">Harga Sewa Ruangan Utama</span>
                            <span class="d-block small text-muted font-monospace" style="font-size: 0.72rem; line-height: 1.2;">{{ $formulaText }}</span>
                        </div>
                        <span class="fw-bold text-dark text-end">Rp {{ number_format($booking->roomDetail->item_price ?? 0, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($booking->serviceDetails->count() > 0)
                        @foreach($booking->serviceDetails as $service)
                            <div class="cost-item">
                                <span class="text-secondary">{{ $service->item_name }}</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($service->item_price, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    @endif

                    @php
                        $baseTotal = ($booking->roomDetail->item_price ?? 0) + $booking->serviceDetails->sum('item_price');
                        $commission = (int) round($baseTotal * 0.05);
                        $netEarnings = $baseTotal - $commission;
                    @endphp
                    <div class="mini-line my-2"></div>
                    <div class="cost-item">
                        <span class="text-secondary">Subtotal Biaya Sewa</span>
                        <span class="fw-semibold text-dark">Rp {{ number_format($baseTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="cost-item">
                        <span class="text-secondary">Komisi Tempat-In (5%)</span>
                        <span class="fw-semibold text-danger">- Rp {{ number_format($commission, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="total-row mt-3">
                        <span>Total Pendapatan Bersih</span>
                        <span>Rp {{ number_format($netEarnings, 0, ',', '.') }}</span>
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