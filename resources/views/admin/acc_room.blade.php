@extends('layout.layout')

@section('content')
<div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-header mb-3">
            <div class="header-text">
                <h1>List Pengajuan Ruangan</h1>
                <p>Manajemen pengajuan ruangan untuk disewa</p>
            </div>
            {{-- <div class="header-action">
                <a href="#" class="btn btn-primary btn-add-room">
                    <i class="bi bi-plus"></i> Tambah Ruangan Baru
                </a>
            </div> --}}
        </div>
 
        <!-- Stats Grid -->
        <div class="row g-3 mb-3">
            <!-- Card Total -->
            <div class="col-md-3">
                <div class="card p-3 h-100 stat-card total shadow-sm border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="stat-info">
                            <span class="label d-block small fw-bold">Total Ruangan</span>
                            <span class="value h3 fw-bold mb-0 text-primary">24</span>
                        </div>
                        <div class="stat-icon fs-2"><i class="bi bi-door-open"></i></div>
                    </div>
                </div>
            </div>
 
            <!-- Card Aktif -->
            <div class="col-md-3">
                <div class="card p-3 h-100 stat-card active shadow-sm border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="stat-info">
                            <span class="label d-block small fw-bold">Unit Aktif</span>
                            <span class="value h3 fw-bold mb-0 text-success">18</span>
                        </div>
                        <div class="stat-icon fs-2"><i class="bi bi-check-circle-fill"></i></div>
                    </div>
                </div>
            </div>
 
            <!-- Card Maintenance -->
            <div class="col-md-3">
                <div class="card p-3 h-100 stat-card maintenance shadow-sm border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="stat-info">
                            <span class="label d-block small fw-bold">Perawatan</span>
                            <span class="value h3 fw-bold mb-0 text-warning">3</span>
                        </div>
                        <div class="stat-icon fs-2"><i class="bi bi-tools"></i></div>
                    </div>
                </div>
            </div>
 
            <!-- Card Inaktif -->
            <div class="col-md-3">
                <div class="card p-3 h-100 stat-card inactive shadow-sm border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="stat-info">
                            <span class="label d-block small fw-bold">Nonaktif</span>
                            <span class="value h3 fw-bold mb-0 text-danger">3</span>
                        </div>
                        <div class="stat-icon fs-2"><i class="bi bi-power"></i></div>
                    </div>
                </div>
            </div>
        </div>
 
        <!-- Main Table Card -->
        <div class="main-card">
            <div class="card-header">
                <div class="title-section">
                    <h3><i class="fas fa-th-list"></i> Daftar Ruangan</h3>
                    <span class="badge-count">24 Unit</span>
                </div>
 
                <form method="GET" class="filter-wrapper row w-100">
                    <div class="search-box col-12 col-md-5">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control" name="search" placeholder="Cari nama atau lantai...">
                    </div>
                    <div class="col-12 col-md-4">
                        <select name="status" class="custom-select form-select">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                            <option value="maintenance">Perawatan</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 text-md-end">
                        <button type="submit" class="btn btn-primary btn-filter me-3"><i class="bi bi-search"></i> Search</button>
                        <a href="#" class="btn btn-outline-secondary btn-reset" title="Reset"><i class="bi bi-backspace"></i> Clear</a>
                    </div>
                </form>
            </div>
 
            <div class="table-responsive mt-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Informasi Ruangan</th>
                            <th>Lokasi</th>
                            <th>Kapasitas</th>
                            <th>Tarif Sewa</th>
                            <th>Status</th>
                            <th class="text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row 1 -->
                        <tr>
                            <td class="text-muted">1</td>
                            <td style="width: 35%">
                                <div class="room-profile">
                                    <div class="room-img-box shadow-sm">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="room-details">
                                        <span class="room-name">Meeting Room A</span>
                                        <span class="room-desc">Ruang rapat dengan AC dan WiFi</span>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 13%"><span class="floor-tag">Lantai 1</span></td>
                            <td>
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i> 8 <small>pax</small>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">Rp500.000</div>
                            </td>
                            <td>
                                <span class="status-indicator st-active">Aktif</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm view btn-info" title="Lihat">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm acc btn-success" title="Terima">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </a>
                                    <button class="btn btn-sm decline btn-danger" title="Tolak" onclick="alert('Yakin mau hapus Meeting Room A?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
 
                        <!-- Row 2 -->
                        <tr>
                            <td class="text-muted">2</td>
                            <td style="width: 35%">
                                <div class="room-profile">
                                    <div class="room-img-box shadow-sm">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="room-details">
                                        <span class="room-name">Conference Room B</span>
                                        <span class="room-desc">Ruang konferensi premium dengan video call</span>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 13%"><span class="floor-tag">Lantai 2</span></td>
                            <td>
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i> 15 <small>pax</small>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">Rp1.000.000</div>
                            </td>
                            <td>
                                <span class="status-indicator st-active">Aktif</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm view btn-info" title="Lihat">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </a>
                                    {{-- <a href="#" class="btn btn-sm acc btn-success" title="Terima">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </a>
                                    <button class="btn btn-sm decline btn-danger" title="Tolak" onclick="alert('Yakin mau hapus Conference Room B?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
 
                        <!-- Row 3 -->
                        <tr>
                            <td class="text-muted">3</td>
                            <td style="width: 35%">
                                <div class="room-profile">
                                    <div class="room-img-box shadow-sm">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="room-details">
                                        <span class="room-name">Training Room</span>
                                        <span class="room-desc">Ruang pelatihan dengan projector dan whiteboard</span>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 13%"><span class="floor-tag">Lantai 3</span></td>
                            <td>
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i> 25 <small>pax</small>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">Rp750.000</div>
                            </td>
                            <td>
                                <span class="status-indicator st-maintenance">Maintenance</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm view btn-info" title="Lihat">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm acc btn-success" title="Terima">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </a>
                                    <button class="btn btn-sm decline btn-danger" title="Tolak" onclick="alert('Yakin mau hapus Training Room?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
 
                        <!-- Row 4 -->
                        <tr>
                            <td class="text-muted">4</td>
                            <td style="width: 35%">
                                <div class="room-profile">
                                    <div class="room-img-box shadow-sm">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="room-details">
                                        <span class="room-name">Executive Suite</span>
                                        <span class="room-desc">Suite mewah dengan fasilitas lengkap</span>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 13%"><span class="floor-tag">Lantai 4</span></td>
                            <td>
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i> 20 <small>pax</small>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">Rp1.500.000</div>
                            </td>
                            <td>
                                <span class="status-indicator st-active">Aktif</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm view btn-info" title="Lihat">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm acc btn-success" title="Terima">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </a>
                                    <button class="btn btn-sm decline btn-danger" title="Tolak" onclick="alert('Yakin mau hapus Executive Suite?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
 
                        <!-- Row 5 -->
                        <tr>
                            <td class="text-muted">5</td>
                            <td style="width: 35%">
                                <div class="room-profile">
                                    <div class="room-img-box shadow-sm">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="room-details">
                                        <span class="room-name">Brainstorm Studio</span>
                                        <span class="room-desc">Studio kreatif dengan interactive board</span>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 13%"><span class="floor-tag">Lantai 2</span></td>
                            <td>
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i> 12 <small>pax</small>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">Rp600.000</div>
                            </td>
                            <td>
                                <span class="status-indicator st-inactive">Nonaktif</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm view btn-info" title="Lihat">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm acc btn-success" title="Terima">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </a>
                                    <button class="btn btn-sm decline btn-danger" title="Tolak" onclick="alert('Yakin mau hapus Brainstorm Studio?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
 
                        <!-- Row 6 -->
                        <tr>
                            <td class="text-muted">6</td>
                            <td style="width: 35%">
                                <div class="room-profile">
                                    <div class="room-img-box shadow-sm">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="room-details">
                                        <span class="room-name">Board Room</span>
                                        <span class="room-desc">Ruang rapat papan direksi eksklusif</span>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 13%"><span class="floor-tag">Lantai 5</span></td>
                            <td>
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i> 10 <small>pax</small>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">Rp800.000</div>
                            </td>
                            <td>
                                <span class="status-indicator st-active">Aktif</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm view btn-info" title="Lihat">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm acc btn-success" title="Terima">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </a>
                                    <button class="btn btn-sm decline btn-danger" title="Tolak" onclick="alert('Yakin mau hapus Board Room?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
 
                        <!-- Row 7 -->
                        <tr>
                            <td class="text-muted">7</td>
                            <td style="width: 35%">
                                <div class="room-profile">
                                    <div class="room-img-box shadow-sm">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="room-details">
                                        <span class="room-name">Private Office</span>
                                        <span class="room-desc">Ruang kerja pribadi yang tenang dan nyaman</span>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 13%"><span class="floor-tag">Lantai 1</span></td>
                            <td>
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i> 4 <small>pax</small>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">Rp300.000</div>
                            </td>
                            <td>
                                <span class="status-indicator st-active">Aktif</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm view btn-info" title="Lihat">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm acc btn-success" title="Terima">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </a>
                                    <button class="btn btn-sm decline btn-danger" title="Tolak" onclick="alert('Yakin mau hapus Private Office?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
 
                        <!-- Row 8 -->
                        <tr>
                            <td class="text-muted">8</td>
                            <td style="width: 35%">
                                <div class="room-profile">
                                    <div class="room-img-box shadow-sm">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="room-details">
                                        <span class="room-name">Collaboration Hub</span>
                                        <span class="room-desc">Ruang kolaborasi modern dengan lounge</span>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 13%"><span class="floor-tag">Lantai 3</span></td>
                            <td>
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i> 16 <small>pax</small>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">Rp700.000</div>
                            </td>
                            <td>
                                <span class="status-indicator st-maintenance">Maintenance</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm view btn-info" title="Lihat">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm acc btn-success" title="Terima">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </a>
                                    <button class="btn btn-sm decline btn-danger" title="Tolak" onclick="alert('Yakin mau hapus Collaboration Hub?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
 
                        <!-- Row 9 -->
                        <tr>
                            <td class="text-muted">9</td>
                            <td style="width: 35%">
                                <div class="room-profile">
                                    <div class="room-img-box shadow-sm">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="room-details">
                                        <span class="room-name">Client Reception</span>
                                        <span class="room-desc">Ruang penerimaan klien dengan lounge</span>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 13%"><span class="floor-tag">Lantai 1</span></td>
                            <td>
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i> 6 <small>pax</small>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">Rp400.000</div>
                            </td>
                            <td>
                                <span class="status-indicator st-inactive">Nonaktif</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm view btn-info" title="Lihat">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm acc btn-success" title="Terima">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </a>
                                    <button class="btn btn-sm decline btn-danger" title="Tolak" onclick="alert('Yakin mau hapus Client Reception?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
 
                        <!-- Row 10 -->
                        <tr>
                            <td class="text-muted">10</td>
                            <td style="width: 35%">
                                <div class="room-profile">
                                    <div class="room-img-box shadow-sm">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="room-details">
                                        <span class="room-name">Virtual Studio</span>
                                        <span class="room-desc">Studio untuk recording dan streaming</span>
                                    </div>
                                </div>
                            </td>
                            <td style="width: 13%"><span class="floor-tag">Lantai 4</span></td>
                            <td>
                                <div class="capacity-info">
                                    <i class="fas fa-users"></i> 5 <small>pax</small>
                                </div>
                            </td>
                            <td>
                                <div class="price-tag">Rp650.000</div>
                            </td>
                            <td>
                                <span class="status-indicator st-active">Aktif</span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-sm view btn-info" title="Lihat">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm acc btn-success" title="Terima">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </a>
                                    <button class="btn btn-sm decline btn-danger" title="Tolak" onclick="alert('Yakin mau hapus Virtual Studio?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
 
            <!-- Pagination -->
            <div class="pagination">
                <p>Showing <b>1</b> to <b>10</b> of <b>24</b> entries</p>
                <div class="page-nav">
                    <span class="disabled">&laquo;</span>
                    <a href="#" class="active">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#">&raquo;</a>
                </div>
            </div>
        </div>
    </div>
@endsection