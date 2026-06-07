@extends('layout.layout')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-light rounded-circle me-3 shadow-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold m-0">Detail Laporan #SRV-{{ $job->id }}</h4>
    </div>

    {{-- Info Utama Ruangan --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="p-4 text-white" style="background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h5 class="fw-bold mb-1">{{ $job->room }}</h5>
                    <p class="mb-0 small opacity-75"><i class="bi bi-geo-alt me-1"></i> {{ $job->address }}</p>
                </div>
                <div class="col-md-5 text-md-end">
                    <div class="small opacity-75">HONOR PROJECT</div>
                    <h4 class="fw-bold mb-0">Rp 500.000</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- LOGIKA TAMPILAN BERDASARKAN ROLE --}}
    @if(Auth::user()->role == 'admin')
        {{-- TAMPILAN SIDE BY SIDE (KOMPARASI) UNTUK ADMIN --}}
        <div class="row g-4 mb-4">
            {{-- Sisi Pengaju --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold m-0 text-primary"><i class="bi bi-person-fill me-2"></i>Laporan Pengaju</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row text-center border-top border-bottom py-3 mb-3">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px">Kondisi</small>
                                <span class="fw-bold">{{ $job->pengaju->kondisi }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px">Kebersihan</small>
                                <span class="fw-bold text-success">{{ $job->pengaju->kebersihan }}</span>
                            </div>
                        </div>
                        <p class="text-secondary small bg-light p-3 rounded-3 mb-3 italic">"{{ $job->pengaju->catatan }}"</p>
                        
                        <h6>Media Lampiran:</h6>
                        <div class="row g-2">
                            @foreach($job->pengaju->media as $m)
                                <div class="col-4">
                                    <img src="{{ $m['url'] }}" class="img-fluid rounded-3 shadow-sm" style="height: 80px; width: 100%; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sisi Surveyor (Outsource) --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 border-primary" style="border-width: 2px !important;">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold m-0 text-primary"><i class="bi bi-shield-check-fill me-2"></i>Laporan Surveyor</h6>
                        <span class="badge bg-primary rounded-pill">{{ $job->tgl_kirim }}</span>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row text-center border-top border-bottom py-3 mb-3">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px">Kondisi Fisik</small>
                                <span class="fw-bold">{{ $job->surveyor->kondisi }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px">Kebersihan</small>
                                <span class="fw-bold text-success">{{ $job->surveyor->kebersihan }}</span>
                            </div>
                        </div>
                        <p class="text-secondary small bg-light p-3 rounded-3 mb-3 italic border-start border-primary border-4">"{{ $job->surveyor->catatan }}"</p>
                        
                        <h6>Galeri Temuan Lapangan:</h6>
                        <div class="row g-2">
                            @foreach($job->surveyor->media as $m)
                                <div class="col-4 position-relative">
                                    @if($m['type'] == 'image')
                                        <img src="{{ $m['url'] }}" class="img-fluid rounded-3 shadow-sm" style="height: 80px; width: 100%; object-fit: cover;">
                                    @else
                                        <video class="img-fluid rounded-3 shadow-sm" style="height: 80px; width: 100%; object-fit: cover;">
                                            <source src="{{ $m['url'] }}" type="video/mp4">
                                        </video>
                                        <div class="position-absolute top-50 start-50 translate-middle text-white">
                                            <i class="bi bi-play-circle-fill fs-4 shadow"></i>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- TAMPILAN NORMAL UNTUK OUTSOURCE (Hanya Laporan Surveyor) --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0 text-primary"><i class="bi bi-file-earmark-text-fill me-2"></i>Hasil Laporan Anda</h6>
                <span class="badge bg-primary rounded-pill">Terkirim: {{ $job->tgl_kirim }}</span>
            </div>
            <div class="card-body">
                <div class="row text-center border-top border-bottom py-4 mb-4">
                    <div class="col-md-4 border-end">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1">Kondisi Fisik</small>
                        <h5 class="fw-bold mb-0">{{ $job->surveyor->kondisi }}</h5>
                    </div>
                    <div class="col-md-4 border-end">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1">Status Kebersihan</small>
                        <h5 class="fw-bold mb-0 text-success">{{ $job->surveyor->kebersihan }}</h5>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1">Status Laporan</small>
                        <h5 class="fw-bold mb-0 text-primary">{{ $job->status }}</h5>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">Catatan Lapangan:</h6>
                    <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                        <p class="mb-0 text-secondary italic">"{{ $job->surveyor->catatan }}"</p>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3">Dokumentasi Media:</h6>
                <div class="row g-3">
                    @foreach($job->surveyor->media as $m)
                        <div class="col-md-3 col-6 position-relative">
                            @if($m['type'] == 'image')
                                <img src="{{ $m['url'] }}" class="img-fluid rounded-4 shadow-sm w-100" style="height: 160px; object-fit: cover;">
                            @else
                                <video class="img-fluid rounded-4 shadow-sm w-100" style="height: 160px; object-fit: cover;">
                                    <source src="{{ $m['url'] }}" type="video/mp4">
                                </video>
                                <div class="position-absolute top-50 start-50 translate-middle text-white">
                                    <i class="bi bi-play-circle-fill fs-1 shadow"></i>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Tombol Cetak (Tetap ada untuk kedua role) --}}
    <button class="btn btn-white w-100 py-3 rounded-pill fw-bold shadow-sm mb-4 border" onclick="window.print()">
        <i class="bi bi-printer me-2"></i> Cetak Laporan Resmi
    </button>

    {{-- Konfirmasi Keputusan Admin (Hanya muncul jika Role Admin dan Status Pending) --}}
    @if(Auth::user()->role == 'admin' && $job->status == 'Pending')
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-5 border-start border-warning border-4">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0 text-center text-md-start">
                <h6 class="fw-bold mb-1 text-primary">Konfirmasi Keputusan Admin</h6>
                <p class="text-muted small mb-0">Bandingkan laporan di atas dan tentukan kelayakan unit.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <button class="btn btn-danger rounded-pill px-4 py-2 fw-bold me-2 shadow-sm">
                    <i class="bi bi-x-circle me-1"></i> Tolak
                </button>
                <button class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm">
                    <i class="bi bi-check-circle me-1"></i> Setujui
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .italic { font-style: italic; }
    .btn-white { background: white; color: #666; transition: all 0.3s; }
    .btn-white:hover { background: #f8f9fa; color: #0064D2; border-color: #0064D2 !important; }
    @media print { 
        .btn, .card-header span, .badge, .btn-light, .shadow-sm { display: none !important; } 
        .card { border: 1px solid #eee !important; box-shadow: none !important; }
    }
</style>
@endsection