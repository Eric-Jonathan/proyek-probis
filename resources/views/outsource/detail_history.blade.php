@extends('layout.layout')

@section('content')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Header Navigasi --}}
            <div class="d-flex align-items-center mb-4">
                <a href="javascript:void(0)" onclick="history.back()" class="btn btn-light rounded-circle me-3 shadow-sm border">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold m-0 text-dark">Detail Laporan #SRV-{{ $job->id }}</h4>
            </div>

            {{-- Card Utama Laporan --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                {{-- Header Card Berwarna --}}
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h5 class="fw-bold mb-1">{{ $job->room }}</h5>
                            <p class="mb-0 small opacity-75"><i class="bi bi-geo-alt me-1"></i> {{ $job->address }}</p>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <div class="small opacity-75 fw-bold">HONOR PROJECT</div>
                            <h4 class="fw-bold mb-0">Rp {{ number_format($job->fee, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                {{-- Body Card Statistik Laporan --}}
                <div class="card-body p-4 text-center">
                    <div class="row g-4">
                        <div class="col-md-4 border-end">
                            <h6 class="text-muted small fw-bold text-uppercase">Kondisi Fisik</h6>
                            <p class="fw-bold mb-0 text-dark">{{ $job->kondisi }}</p>
                        </div>
                        <div class="col-md-4 border-end">
                            <h6 class="text-muted small fw-bold text-uppercase">Kebersihan</h6>
                            <p class="fw-bold mb-0 text-success">{{ $job->kebersihan }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small fw-bold text-uppercase">Tgl Terbit</h6>
                            <p class="fw-bold mb-0 text-dark">{{ date('d M Y', strtotime($job->tgl_kirim)) }}</p>
                        </div>

                        <hr class="my-2 opacity-25">

                        <div class="col-12 text-start px-4">
                            <h6 class="text-muted small fw-bold text-uppercase mb-2">Catatan Lapangan Surveyor</h6>
                            <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                                <p class="mb-0 text-secondary italic small">"{{ $job->catatan }}"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL CETAK (Kondisional Berdasarkan Status) --}}
            <div class="mb-3">
                <button class="btn btn-light w-100 py-3 rounded-pill fw-bold shadow-sm border {{ $job->status != 'Diterima' ? 'disabled opacity-50' : '' }}" 
                    onclick="window.print()" 
                    {{ $job->status != 'Diterima' ? 'disabled' : '' }}>
                    <i class="bi bi-printer me-2"></i> 
                    @if($job->status != 'Diterima')
                        Cetak Laporan
                    @else
                        Cetak Laporan
                    @endif
                </button>
            </div>

            {{-- BAGIAN KHUSUS ADMIN: PERSYARATAN & KEPUTUSAN --}}
            @if(Auth::user()->role == 'admin' && !in_array($job->status, ['Diterima', 'Ditolak']))
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-5" style="background-color: #f8faff; border: 1px solid #e0e7ff !important;">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                            <h6 class="fw-bold mb-1 text-primary">Konfirmasi Keputusan Admin</h6>
                            <p class="text-muted small mb-0">Tentukan kelayakan unit untuk dipublikasikan ke sistem.</p>
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <div class="d-flex justify-content-center justify-content-md-end gap-2">
                                {{-- Form Tolak --}}
                                <form action="#" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm">
                                        <i class="bi bi-x-circle me-1"></i> Tolak Laporan
                                    </button>
                                </form>
                                
                                {{-- Form Setuju --}}
                                <form action="#" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i> Setujui Unit
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- FEEDBACK STATUS (Muncul jika sudah diproses) --}}
            @if(in_array($job->status, ['Diterima', 'Ditolak']))
                <div class="alert {{ $job->status == 'Diterima' ? 'alert-success' : 'alert-danger' }} rounded-4 border-0 shadow-sm p-3 text-center mb-5">
                    <i class="bi {{ $job->status == 'Diterima' ? 'bi-patch-check-fill' : 'bi-exclamation-octagon-fill' }} me-2"></i>
                    Laporan ini telah status: <strong>{{ strtoupper($job->status) }}</strong>. 
                    @if($job->status == 'Diterima')
                        Dokumen sekarang tersedia untuk dicetak dan dipublikasikan.
                    @else
                        Silakan hubungi surveyor untuk revisi laporan jika diperlukan.
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .alert, .dropdown, a { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        body { background-color: #fff !important; }
    }
    .italic { font-style: italic; }
</style>
@endsection