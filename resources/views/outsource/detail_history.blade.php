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
                    <h4 class="fw-bold mb-0">Rp 200.000</h4>
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
                        
                        {{-- Facilities checklist comparison for owner --}}
                        @if(isset($allFacilitiesList))
                            <h6 class="mt-4 fw-bold">Fasilitas Diajukan:</h6>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach($allFacilitiesList as $facility)
                                    @if(in_array($facility, $job->pengaju->facilities ?? []))
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i> {{ $facility }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-3 py-2 fw-normal" style="font-size: 0.75rem;">
                                            <i class="bi bi-x-circle me-1"></i> {{ $facility }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <h6>Media Lampiran:</h6>
                        <div class="row g-2">
                            @foreach($job->pengaju->media as $m)
                                @if($m['type'] == 'image')
                                    <div class="col-4">
                                        <img src="{{ $m['url'] }}" class="img-fluid rounded-3 shadow-sm" style="height: 80px; width: 100%; object-fit: cover;">
                                    </div>
                                @endif
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
                        
                        {{-- Facilities checklist comparison for surveyor --}}
                        @if(isset($allFacilitiesList))
                            <h6 class="mt-4 fw-bold">Fasilitas Terverifikasi:</h6>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach($allFacilitiesList as $facility)
                                    @if(in_array($facility, $job->surveyor->facilities ?? []))
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i> {{ $facility }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-3 py-2 fw-normal" style="font-size: 0.75rem;">
                                            <i class="bi bi-x-circle me-1"></i> {{ $facility }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <h6>Galeri Temuan Lapangan:</h6>
                        <div class="row g-2 mb-3">
                            @foreach($job->surveyor->media as $m)
                                @if($m['type'] == 'image')
                                    <div class="col-4">
                                        <img src="{{ $m['url'] }}" class="img-fluid rounded-3 shadow-sm" style="height: 80px; width: 100%; object-fit: cover;">
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        {{-- HTML5 Surveyor Video Player --}}
                        @if(!empty($job->surveyor->video))
                            <h6 class="fw-bold text-dark mb-2">Video Hasil Survei:</h6>
                            <video controls class="rounded w-100 shadow-sm" style="max-height: 200px; object-fit: contain;">
                                <source src="{{ $job->surveyor->video }}" type="video/mp4">
                            </video>
                        @endif
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

                {{-- Surveyor verified facilities checklist in single view --}}
                @if(isset($allFacilitiesList))
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">Fasilitas Terverifikasi:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($allFacilitiesList as $facility)
                            @if(in_array($facility, $job->surveyor->facilities ?? []))
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.75rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i> {{ $facility }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border rounded-pill px-3 py-2 fw-normal" style="font-size: 0.75rem;">
                                    <i class="bi bi-x-circle me-1"></i> {{ $facility }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <h6 class="fw-bold text-dark mb-3">Dokumentasi Media:</h6>
                <div class="row g-3 mb-4">
                    @foreach($job->surveyor->media as $m)
                        @if($m['type'] == 'image')
                            <div class="col-md-3 col-6">
                                <img src="{{ $m['url'] }}" class="img-fluid rounded-4 shadow-sm w-100" style="height: 160px; object-fit: cover;">
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Surveyor Video Player in single view --}}
                @if(!empty($job->surveyor->video))
                <div class="mb-2">
                    <h6 class="fw-bold text-dark mb-2">Video Hasil Survei:</h6>
                    <video controls class="rounded w-100 shadow-sm" style="max-height: 250px; object-fit: contain;">
                        <source src="{{ $job->surveyor->video }}" type="video/mp4">
                    </video>
                </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Tombol Cetak (Tetap ada untuk kedua role) --}}
    <a href="{{ route('outsource.history.pdf', $job->id) }}" target="_blank" class="btn btn-white w-100 py-3 rounded-pill fw-bold shadow-sm mb-4 border text-secondary text-decoration-none d-block text-center">
        <i class="bi bi-file-earmark-pdf-fill me-2 text-danger"></i> Cetak Laporan Resmi (PDF)
    </a>

    {{-- Konfirmasi Keputusan Admin (Hanya muncul jika Role Admin dan Status Pending) --}}
    @if(Auth::user()->role == 'admin' && $job->status == 'Pending')
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-5 border-start border-warning border-4">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0 text-center text-md-start">
                <h6 class="fw-bold mb-1 text-primary">Konfirmasi Keputusan Admin</h6>
                <p class="text-muted small mb-0">Bandingkan laporan di atas dan tentukan kelayakan unit.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="d-inline-flex justify-content-center justify-content-md-end gap-2 w-100">
                    <form action="{{ route('admin.room.reject', $job->room_id ?? $job->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menolak pengajuan sewa ruangan ini?">
                        @csrf
                        <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Tolak
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.room.approve', $job->room_id ?? $job->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menyetujui pengajuan sewa ruangan ini?">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Setujui
                        </button>
                    </form>
                </div>
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