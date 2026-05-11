@extends('layout.layout')

@section('content')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('outsource.history') }}" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold m-0">Detail Laporan #SRV-{{ $job->id }}</h4>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #0064D2 0%, #004a99 100%);">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h5 class="fw-bold mb-1">{{ $job->room }}</h5>
                            <p class="mb-0 small opacity-75"><i class="bi bi-geo-alt me-1"></i> {{ $job->address }}</p>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <div class="small opacity-75">HONOR PROJECT</div>
                            <h4 class="fw-bold mb-0">Rp {{ number_format($job->fee, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4 text-center">
                        <div class="col-md-4 border-end">
                            <h6 class="text-muted small fw-bold text-uppercase">Kondisi Fisik</h6>
                            <p class="fw-bold mb-0">{{ $job->kondisi }}</p>
                        </div>
                        <div class="col-md-4 border-end">
                            <h6 class="text-muted small fw-bold text-uppercase">Kebersihan</h6>
                            <p class="fw-bold mb-0 text-success">{{ $job->kebersihan }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small fw-bold text-uppercase">Tanggal Kirim</h6>
                            <p class="fw-bold mb-0">{{ date('d M Y', strtotime($job->tgl_kirim)) }}</p>
                        </div>

                        <hr class="my-2 opacity-25">

                        <div class="col-12 text-start">
                            <h6 class="text-muted small fw-bold text-uppercase mb-2">Catatan Lapangan</h6>
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-0 text-secondary italic">"{{ $job->catatan }}"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> Cetak Dokumen Laporan
            </button>

        </div>
    </div>
</div>
@endsection