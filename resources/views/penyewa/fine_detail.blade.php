@extends('layout.layout')

@section('custom_css')
<style>
    body { background-color: #f8f9fa; }
    .detail-card {
        border-radius: 20px;
        border: none;
        background: #ffffff;
    }
    .proof-thumbnail {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: 0.2s;
    }
    .proof-thumbnail:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .btn-pay {
        border-radius: 50px;
        padding: 14px 28px;
        font-weight: 700;
        transition: all 0.2s;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('bookings.history') }}" class="btn btn-light rounded-circle me-3 shadow-sm border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0 text-dark">Detail Tagihan Denda</h3>
            <p class="text-secondary small mb-0">Rincian laporan pelanggaran dan pembayaran denda Tempat-In</p>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card detail-card shadow-sm p-4 p-md-5 mb-4">
                {{-- Status Banner --}}
                @if($fine->is_paid == 1)
                    <div class="alert alert-success border-0 rounded-4 p-3 d-flex align-items-center mb-4">
                        <i class="bi bi-check-circle-fill me-3 fs-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Denda Telah Dilunasi</h6>
                            <p class="small mb-0">Terima kasih, tagihan denda ini sudah berhasil dibayar.</p>
                        </div>
                    </div>
                @else
                    <div class="alert alert-danger border-0 rounded-4 p-3 d-flex align-items-center mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Menunggu Pembayaran</h6>
                            <p class="small mb-0">Harap lakukan pelunasan denda agar status persewaan Anda bersih.</p>
                        </div>
                    </div>
                @endif

                {{-- Fine Details Info --}}
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Ruangan Gedung</span>
                        <h5 class="fw-bold text-dark mb-1">{{ $fine->booking->roomDetail->item_name ?? 'Ruangan' }}</h5>
                        <p class="text-muted small mb-0"><i class="bi bi-geo-alt"></i> {{ $fine->booking->roomDetail->room->location ?? 'Lokasi tidak tersedia' }}</p>
                    </div>
                    <div class="col-md-6 border-start ps-md-4">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Penyewa (Terdenda)</span>
                        <h5 class="fw-bold text-danger mb-1">{{ $fine->booking->user->username }}</h5>
                        <p class="text-muted small mb-0"><i class="bi bi-whatsapp"></i> +62{{ $fine->booking->phone }}</p>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Jenis Pelanggaran</span>
                        <span class="badge bg-danger-subtle text-danger px-3 py-2 text-capitalize fw-bold rounded-pill mt-1">
                            {{ $fine->jenis_denda }}
                        </span>
                    </div>
                    <div class="col-md-6 border-start ps-md-4">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Nominal Denda</span>
                        <h4 class="fw-extrabold text-danger mb-0 mt-1">Rp {{ number_format($fine->nominal_denda, 0, ',', '.') }}</h4>
                    </div>

                    <div class="col-12 border-top pt-4">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-2" style="font-size: 10px; letter-spacing: 0.5px;">Kronologi Pelanggaran</span>
                        <div class="p-3 rounded border bg-light small" style="white-space: pre-line;">
                            {{ $fine->keterangan }}
                        </div>
                    </div>

                    <div class="col-12 border-top pt-4">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-2" style="font-size: 10px; letter-spacing: 0.5px;">Bukti Foto Pelanggaran</span>
                        <div class="d-flex flex-wrap gap-2">
                            @if(is_array($fine->bukti_denda) && count($fine->bukti_denda) > 0)
                                @foreach($fine->bukti_denda as $img)
                                    <a href="{{ asset($img) }}" target="_blank">
                                        <img src="{{ asset($img) }}" class="proof-thumbnail shadow-sm" alt="Bukti Foto">
                                    </a>
                                @endforeach
                            @else
                                <span class="text-muted small italic">Tidak ada foto bukti terlampir.</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action / Payment Box --}}
                @if($fine->is_paid == 0)
                    <div class="border-top pt-4 mt-4">
                        <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <p class="mb-1 text-muted small"><i class="bi bi-wallet2 me-1"></i> Saldo Tempat-In Anda</p>
                                    <h5 class="fw-bold mb-0 text-dark">
                                        Rp {{ number_format(Auth::user()->saldo, 0, ',', '.') }}
                                    </h5>
                                </div>
                                @if(Auth::user()->saldo < $fine->nominal_denda)
                                    <div>
                                        <a href="{{ route('topup.show') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3" target="_blank">
                                            <i class="bi bi-plus-lg me-1"></i> Top Up Saldo
                                        </a>
                                    </div>
                                @endif
                            </div>
                            @if(Auth::user()->saldo < $fine->nominal_denda)
                                <div class="mt-2 text-danger small">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Saldo tidak mencukupi untuk melunasi denda ini.
                                </div>
                            @else
                                <div class="mt-2 text-muted small">
                                    Pembayaran denda akan memotong saldo Tempat-In Anda secara langsung.
                                </div>
                            @endif
                        </div>

                        @if(Auth::user()->saldo >= $fine->nominal_denda)
                            <button type="button" class="btn btn-danger btn-pay w-100 py-3 d-flex align-items-center justify-content-center gap-2 shadow" data-bs-toggle="modal" data-bs-target="#modalConfirmPay">
                                <i class="bi bi-credit-card-2-back-fill fs-5"></i> Bayar Denda Sekarang
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-pay w-100 py-3 d-flex align-items-center justify-content-center gap-2 shadow" disabled>
                                <i class="bi bi-credit-card-2-back-fill fs-5"></i> Saldo Tidak Mencukupi
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL CONFIRMATION --}}
@if($fine->is_paid == 0)
<div class="modal fade" id="modalConfirmPay" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0 text-center">
                <div class="w-100">
                    <div class="text-danger mb-3">
                        <i class="bi bi-exclamation-octagon-fill" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-0">Konfirmasi Pembayaran</h4>
                </div>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-secondary mb-0">
                    Apakah Anda yakin ingin membayar denda sebesar <strong>Rp {{ number_format($fine->nominal_denda, 0, ',', '.') }}</strong> menggunakan saldo Tempat-In Anda? 
                </p>
                <p class="text-danger small mt-2">
                    <i class="bi bi-info-circle me-1"></i> Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 py-2.5 fw-bold flex-grow-1 border" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger rounded-pill px-4 py-2.5 fw-bold flex-grow-1" id="btn-confirm-pay">Ya, Bayar Denda</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('custom_js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if($fine->is_paid == 0)
        $('#btn-confirm-pay').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...');
            
            $.ajax({
                url: '{{ route("penyewa.fine.pay", $fine->fine_id) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            confirmButtonColor: '#0064D2'
                        }).then(function() {
                            window.location.href = '{{ route("bookings.history") }}';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Pembayaran gagal.',
                            confirmButtonColor: '#0064D2'
                        });
                        btn.prop('disabled', false).text('Ya, Bayar Denda');
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Terjadi kesalahan saat membayar denda.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg,
                        confirmButtonColor: '#0064D2'
                    });
                    btn.prop('disabled', false).text('Ya, Bayar Denda');
                }
            });
        });
        @endif
    });
</script>
@endsection
