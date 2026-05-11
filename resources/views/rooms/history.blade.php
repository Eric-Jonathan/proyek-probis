@extends('layout.layout')

@section('content')
<div class="container-fluid p-4">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-0">Riwayat Persewaan</h2>
            <p class="text-muted mb-0">Daftar seluruh transaksi dan status pemesanan ruangan</p>
        </div>
    </div>

    {{-- Stats Ringkas --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <span class="text-muted small fw-bold text-uppercase">Total Transaksi</span>
                <h4 class="fw-bold mb-0 mt-1">1,280</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <span class="text-muted small fw-bold text-uppercase text-success">Selesai</span>
                <h4 class="fw-bold mb-0 mt-1">1,150</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <span class="text-muted small fw-bold text-uppercase text-warning">Berjalan</span>
                <h4 class="fw-bold mb-0 mt-1">45</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <span class="text-muted small fw-bold text-uppercase text-danger">Dibatalkan</span>
                <h4 class="fw-bold mb-0 mt-1">85</h4>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle" id="tableHistory" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>ID Booking</th>
                            <th>Ruangan</th>
                            <th>Penyewa</th>
                            <th>Tanggal Sewa</th>
                            <th>Total Bayar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data ini nantinya diload via AJAX/DataTable --}}
                        <tr>
                            <td class="fw-bold">#BK-99281</td>
                            <td>
                                <div class="fw-bold">Ruang Mawar 01</div>
                                <small class="text-muted">Gedung Utama, Lt 2</small>
                            </td>
                            <td>
                                <span>John Doe</span>
                            </td>
                            <td>
                                <div class="small">12 Mei 2026</div>
                                <div class="text-muted small">08:00 - 17:00</div>
                            </td>
                            <td class="fw-bold text-dark">Rp 750.000</td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Selesai</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm text-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm text-dark" title="Cetak Invoice">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </td>
                        </tr>
                        {{-- Contoh Status Menunggu --}}
                        <tr>
                            <td class="fw-bold">#BK-99285</td>
                            <td>
                                <div class="fw-bold">Aula Serbaguna</div>
                                <small class="text-muted">Gedung B, Lt 1</small>
                            </td>
                            <td>
                                <span>Alice Smith</span>
                            </td>
                            <td>
                                <div class="small">15 Mei 2026</div>
                                <div class="text-muted small">10:00 - 20:00</div>
                            </td>
                            <td class="fw-bold text-dark">Rp 2.500.000</td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2">Menunggu Konfirmasi</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm text-primary"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm text-dark"><i class="bi bi-printer"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Load CSS DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    /* Styling agar match dengan desain Kelola Ruangan sebelumnya */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0d6efd !important;
        color: white !important;
        border-radius: 50px;
        border: none;
    }
    table.dataTable thead th {
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 10px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .dataTables_filter input {
        border-radius: 50px;
        padding: 5px 15px;
        border: 1px solid #dee2e6;
    }
</style>
@endpush

@section('custom_js')
{{-- Load JS DataTables --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tableHistory').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari riwayat...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    previous: "<i class='bi bi-chevron-left'></i>",
                    next: "<i class='bi bi-chevron-right'></i>"
                }
            },
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [6] } // Matikan sorting untuk kolom aksi
            ]
        });
    });
</script>
@endsection