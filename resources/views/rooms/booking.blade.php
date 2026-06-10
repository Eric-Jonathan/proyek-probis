@extends('layout.layout')

@section('content')
<div class="container py-3">
    <button class="btn btn-white rounded-pill shadow-sm border px-3 mb-4 fw-medium" onclick="history.back()">
        <i class="bi bi-chevron-left me-1"></i> Kembali
    </button>
    
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="fw-bold mb-1">Finalize Your Event Booking</h2>
            <p class="text-secondary">Selesaikan detail pemesanan gedung untuk kelancaran acara Anda.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="position-relative">
                    <img src="{{ asset($room->images->first()->path ?? 'upload_room/default.png') }}" 
                        class="card-img-top" 
                        style="height: 200px; object-fit: cover;" 
                        alt="Foto {{ $room->name }}">
                    <span class="badge bg-dark position-absolute bottom-0 end-0 m-3 opacity-75">Max {{ $room->capacity }} Orang</span>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">{{ $room->name }}</h5>
                    <p class="text-muted small mb-3"><i class="bi bi-geo-alt"></i> {{ $room->location }}</p>
                    
                    <div class="row g-2 small text-dark border-top pt-3">
                        @foreach($room->facilities as $facility)
                            <div class="col-6"><i class="bi bi-patch-check text-primary me-1"></i> {{ $facility->name }}</div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-primary border-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase small text-muted mb-3">Jadwal Terpilih</h6>
                    <div class="row mb-2">
                        <div class="col-6 border-end">
                            <p class="mb-0 small text-muted">Mulai Sewa</p>
                            <p class="mb-0 fw-bold text-dark">{{ $startDate->translatedFormat('d M Y') }}</p>
                        </div>
                        <div class="col-6 ps-3">
                            <p class="mb-0 small text-muted">Selesai Sewa</p>
                            <p class="mb-0 fw-bold text-dark">{{ $endDate->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="pt-2 border-top">
                        <p class="mb-0 small">Durasi Penggunaan: <strong class="text-primary">{{ $totalDays }} Hari</strong></p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 bg-white">
                <div class="card-body p-4">
                    <h6 class="fw-bold small text-muted text-uppercase mb-3">Ringkasan Biaya</h6>
                    
                    @php 
                        $basePrice = $room->price * ($room->jenis_harga === 'Pax' ? $room->min_order : $totalDays);
                    @endphp

                    <div class="d-flex justify-content-between mb-2">
                        <span>Sewa Ruangan Utama</span>
                        <span class="fw-bold" id="render-base-price" 
                            data-jenis-harga="{{ $room->jenis_harga }}" 
                            data-raw-price="{{ $room->price }}"
                            data-min-order="{{ $room->min_order }}"
                            data-total-days="{{ $totalDays }}"
                            data-base="{{ $basePriceCalculated }}"
                            data-deposit-percent="{{ $room->deposit_percent }}">
                            Rp {{ number_format($basePriceCalculated, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    <div id="render-extra-services-cost">
                        </div>

                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold mb-0 text-primary">Total Bayar</h5>
                        <div class="text-end">
                            <h4 class="fw-bold mb-0 text-dark" id="render-total-final">Rp {{ number_format($basePrice, 0, ',', '.') }}</h4>
                            <small class="text-muted" style="font-size: 11px;">Termasuk jaminan & biaya teknis</small>
                        </div>
                    </div>
                    <div class="d-none mt-3 border-top pt-3" id="installment-container">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small">Cicilan Pokok (1/3)</span>
                            <span class="fw-semibold text-dark" id="render-installment-pax">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small">Uang Deposit ({{ $room->deposit_percent }}%)</span>
                            <span class="fw-semibold text-dark" id="render-deposit-amount">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 border-top pt-2">
                            <h6 class="fw-bold mb-0 text-success">Total Bayar Awal</h6>
                            <h5 class="fw-bold mb-0 text-success" id="render-initial-payment">Rp 0</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4 rounded-4 p-3" style="background-color: #f0f7ff;">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                        <i class="bi bi-person-check fs-5"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold">Pemesan Ter autentikasi: {{ Auth::user()->name }}</p>
                        <p class="mb-0 text-muted small">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-4 border-bottom pb-2">Detail Formulir Acara</h4>
                
                <form action="{{ route('booking.store', $room->room_id) }}" method="POST" id="main-booking-form">
                    @csrf
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap / Instansi Pemesan <span class="text-danger">*</span></label>
                            <input type="text" name="instansi" class="form-control py-2" placeholder="Masukkan nama pemesan" value="{{ old('instansi') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jenis Kegiatan Acara <span class="text-danger">*</span></label>
                            <select name="jenis_acara" class="form-select py-2" required>
                                <option value="" disabled {{ old('jenis_acara') ? '' : 'selected' }}>Pilih jenis acara...</option>
                                <option value="Wedding" {{ old('jenis_acara') === 'Wedding' ? 'selected' : '' }}>Wedding / Pernikahan</option>
                                <option value="Seminar" {{ old('jenis_acara') === 'Seminar' ? 'selected' : '' }}>Seminar / Corporate Meeting</option>
                                <option value="Gathering" {{ old('jenis_acara') === 'Gathering' ? 'selected' : '' }}>Social Gathering / Pesta</option>
                                <option value="Lainnya" {{ old('jenis_acara') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+62</span>
                                <input type="number" name="phone" class="form-control py-2" placeholder="8123xxxx" value="{{ old('phone') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Estimasi Jumlah Pax / Tamu Hadir <span class="text-danger">*</span></label>
                            <input type="number" name="total_capacity" id="input-capacity" class="form-control py-2" 
                                   max="{{ $room->capacity }}" placeholder="Maks. {{ $room->capacity }} orang" value="{{ old('total_capacity') }}" required>
                            <div class="form-text text-muted" style="font-size: 11px;">Tidak boleh melebihi kapasitas maksimal ruangan.</div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="form-label small fw-bold">Skema Waktu Sewa <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 p-2 border rounded bg-light">
                                <div class="form-check">
                                    <input class="form-check-input select-tipe-sewa" type="radio" name="sewa_tipe" id="tipe-hari" value="harian" {{ old('sewa_tipe', 'harian') === 'harian' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="tipe-hari">Sewa Full Day (Harian)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input select-tipe-sewa" type="radio" name="sewa_tipe" id="tipe-jam" value="jam" {{ old('sewa_tipe') === 'jam' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="tipe-jam">Sewa Sistem Jam (Hourly)</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 row g-2 mt-1 d-none" id="container-input-jam">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Waktu Mulai Acara</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="{{ old('jam_mulai', '08:00') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Waktu Selesai Acara</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" value="{{ old('jam_selesai', '16:00') }}">
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-uppercase small text-muted">Layanan Tambahan (Penyesuaian Biaya)</h6>
                            <div class="p-3 bg-white rounded-3 border">
                                <div class="form-check mb-2">
                                    <input class="form-check-input addon-service-checkbox" type="checkbox" name="services[]" id="catering" value="Catering" data-price="50000" {{ is_array(old('services')) && in_array('Catering', old('services')) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="catering">
                                        Layanan Paket Katering Konsumsi (+Rp 50.000 / Pax)
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input addon-service-checkbox" type="checkbox" name="services[]" id="decor" value="Dekorasi" data-price="1500000" {{ is_array(old('services')) && in_array('Dekorasi', old('services')) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="decor">
                                        Paket Dekorasi Panggung & Artis Gedung (+Rp 1.500.000)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input addon-service-checkbox" type="checkbox" name="services[]" id="it_support" value="IT" data-price="750000" {{ is_array(old('services')) && in_array('IT', old('services')) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="it_support">
                                        Operator Teknis & Sound Live Streaming (+Rp 750.000)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="form-label small fw-bold">Skema Pembayaran <span class="text-danger">*</span></label>
                            <select name="payment_scheme" id="payment_scheme" class="form-select" required>
                                <option value="full" {{ old('payment_scheme', 'full') === 'full' ? 'selected' : '' }}>Bayar Lunas (100%)</option>
                                <option value="installment" {{ old('payment_scheme') === 'installment' ? 'selected' : '' }}>Bayar Cicilan 3x (Awal: 1/3 dari total)</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="card border-0 rounded-3 p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <p class="mb-1 text-muted small"><i class="bi bi-wallet2 me-1"></i> Saldo Tempat-In Anda</p>
                                        <h5 class="fw-bold mb-0 text-dark" id="user-saldo" data-saldo="{{ Auth::user()->saldo }}">
                                            Rp {{ number_format(Auth::user()->saldo, 0, ',', '.') }}
                                        </h5>
                                    </div>
                                    <div>
                                        <a href="{{ route('topup.show') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3" target="_blank">
                                            <i class="bi bi-plus-lg me-1"></i> Top Up Saldo
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-2 text-muted small" id="payment-scheme-summary">
                                    Pembayaran akan langsung memotong saldo Tempat-In Anda.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="form-label small fw-bold">Permintaan Khusus / Catatan Tambahan Gedung</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Butuh penataan meja melingkar, request penambahan kursi cadangan, dsb.">{{ old('notes') }}</textarea>
                        </div>

                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                Konfirmasi & Bayar <i class="bi bi-chevron-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('custom_js/rooms/booking.js') }}"></script>
@endsection