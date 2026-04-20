@extends('layout.layout')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h3 class="fw-bold">Ringkasan Performa Properti</h3>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 border-start border-primary border-5">
            <h6 class="text-muted fw-bold">RATA-RATA KEBERSIHAN</h6>
            <h2 class="fw-bold">4.8 <span class="fs-6 text-warning">⭐</span></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 border-start border-success border-5">
            <h6 class="text-muted fw-bold">RATA-RATA PELAYANAN</h6>
            <h2 class="fw-bold">4.5 <span class="fs-6 text-warning">⭐</span></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 border-start border-warning border-5">
            <h6 class="text-muted fw-bold">RATA-RATA KENYAMANAN</h6>
            <h2 class="fw-bold">4.7 <span class="fs-6 text-warning">⭐</span></h2>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Ruangan Anda</h5>
        <a href="#" class="btn btn-sm btn-primary">+ Tambah Ruangan</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Ruangan</th>
                        <th>Lokasi</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Creative Hub</strong></td>
                        <td><small><i class="bi bi-geo-alt"></i> Semampir, Surabaya</small></td>
                        <td>Rp {{ number_format(200000, 0, ',', '.') }}</td>
                        <td><span class="badge bg-info">Diterima</span></td>
                        <td>
                            <button class="btn btn-sm btn-light border"><i class="bi bi-pencil-square"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection