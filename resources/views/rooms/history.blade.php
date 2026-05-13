@extends('layout.layout')

@section('custom_css')
<style>
    body { background-color: #f8f9fa; }

    /* Statistik Card Style sesuai image_38699c.png */
    .stat-card {
        border-radius: 12px;
        border: none;
        transition: transform 0.2s;
        background: #ffffff;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-value { font-size: 2rem; font-weight: 800; color: #334155; }
    .stat-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    
    /* Table Styling */
    .table-container { border-radius: 15px; background: #ffffff; }
    table.dataTable thead th {
        background-color: #fcfcfd;
        border-bottom: 1px solid #f1f5f9 !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        padding: 1.25rem 1rem !important;
    }
    .table tbody td { padding: 1.25rem 1rem; vertical-align: middle; font-size: 0.9rem; }
    
    /* Badge Status */
    .badge-selesai { background-color: #dcfce7; color: #15803d; }
    .badge-konfirmasi { background-color: #fef9c3; color: #a16207; }

    /* Custom Button Detail sesuai image_2e786d.png */
    .btn-detail-custom {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        border-radius: 50px; 
        padding: 6px 20px;
        font-weight: 500;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-detail-custom:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.08);
    }

    /* DataTables Pagination Styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0064D2 !important;
        color: white !important;
        border-radius: 50px;
        border: none;
    }
</style>
{{-- Load CSS DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Riwayat Persewaan</h2>
        <p class="text-secondary">Daftar seluruh transaksi dan status pemesanan ruangan Anda</p>
    </div>

    {{-- Statistik Row sesuai image_38699c.png --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">TOTAL TRANSAKSI</div>
                <div class="stat-value">1,280</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">SELESAI</div>
                <div class="stat-value text-success">1,150</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">BERJALAN</div>
                <div class="stat-value text-primary">45</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center text-md-start">
                <div class="stat-label">DIBATALKAN</div>
                <div class="stat-value text-danger">85</div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card table-container border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle" id="tableHistory" style="width:100%">
                    <thead>
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
                        {{-- Row 1 --}}
                        <tr>
                            <td class="fw-bold">#BK-99281</td>
                            <td>
                                <div class="fw-bold">Ruang Mawar 01</div>
                                <small class="text-muted">Gedung Utama, Lt 2</small>
                            </td>
                            <td>John Doe</td>
                            <td>
                                <div class="small">12 Mei 2026</div>
                                <div class="text-muted small">08:00 - 17:00</div>
                            </td>
                            <td class="fw-bold text-dark">Rp 750.000</td>
                            <td class="text-center">
                                <span class="badge rounded-pill badge-selesai px-3 py-2">Selesai</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('penyedia.detail_history', ['id' => 99281]) }}" class="btn-detail-custom">
                                    <i class="bi bi-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                        {{-- Row 2 --}}
                        <tr>
                            <td class="fw-bold">#BK-99285</td>
                            <td>
                                <div class="fw-bold">Aula Serbaguna</div>
                                <small class="text-muted">Gedung B, Lt 1</small>
                            </td>
                            <td>Alice Smith</td>
                            <td>
                                <div class="small">15 Mei 2026</div>
                                <div class="text-muted small">10:00 - 20:00</div>
                            </td>
                            <td class="fw-bold text-dark">Rp 2.500.000</td>
                            <td class="text-center">
                                <span class="badge rounded-pill badge-konfirmasi px-3 py-2">Menunggu Konfirmasi</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('penyedia.detail_history', ['id' => 99285]) }}" class="btn-detail-custom">
                                    <i class="bi bi-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
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
            columnDefs: [
                { orderable: false, targets: [6] } // Matikan sorting kolom aksi
            ]
        });
    });
</script>
@endsection