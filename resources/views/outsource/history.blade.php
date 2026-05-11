@extends('layout.layout')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col">
        <h3 class="fw-bold m-0 text-dark">Riwayat Laporan Kerja</h3>
        <p class="text-secondary small m-0">Arsip seluruh survei yang telah Anda selesaikan</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4 py-3 border-0">Project & Unit</th>
                    <th class="border-0">Tanggal Kirim</th>
                    <th class="border-0">Rekomendasi</th>
                    <th class="border-0">Status Review</th>
                    <th class="border-0 text-center pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $histories = [
                        (object)['id' => 101, 'unit' => 'Kontena Hotel - Ballroom', 'date' => '2026-05-10', 'rek' => 'Layak', 'status' => 'Diterima'],
                        (object)['id' => 105, 'unit' => 'Studio Foto Malang', 'date' => '2026-05-08', 'rek' => 'Tidak Layak', 'status' => 'Revisi'],
                    ];
                @endphp

                @foreach($histories as $h)
                <tr>
                    <td class="ps-4 py-3">
                        <div class="fw-bold text-dark">{{ $h->unit }}</div>
                        <small class="text-muted">ID: #SRV-{{ $h->id }}</small>
                    </td>
                    <td>{{ date('d M Y', strtotime($h->date)) }}</td>
                    <td>
                        <span class="badge {{ $h->rek == 'Layak' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3" style="font-size: 0.7rem;">
                            {{ $h->rek }}
                        </span>
                    </td>
                    <td>
                        @if($h->status == 'Diterima')
                            <div class="text-success small fw-bold"><i class="bi bi-check-all me-1"></i> Disetujui Pusat</div>
                        @else
                            <div class="text-warning small fw-bold"><i class="bi bi-arrow-clockwise me-1"></i> Perlu Revisi</div>
                        @endif
                    </td>
                    <td class="text-center pe-4">
                        <a href="{{ route('outsource.history.detail', $h->id) }}" class="btn btn-light btn-sm rounded-pill px-4 border fw-bold shadow-sm">
                            <i class="bi bi-eye me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection