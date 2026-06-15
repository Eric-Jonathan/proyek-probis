<div class="bg-dark text-white p-3" id="sidebar-wrapper">
    <div class="sidebar-heading fs-4 fw-bold border-bottom pb-3 mb-3">Tempat-In</div>
    @if(Auth::user()->role !== 'outsource')
        <div class="p-3 py-2.5 mb-3 bg-secondary bg-opacity-10 rounded mx-1 mt-n2 border border-secondary border-opacity-25">
            <div class="small opacity-75" style="font-size: 0.75rem;"><i class="bi bi-wallet2 me-1"></i> Saldo Anda</div>
            <div class="fs-5 fw-bold text-truncate text-warning mt-1">Rp {{ number_format(Auth::user()->saldo, 0, ',', '.') }}</div>
            @if(Auth::user()->role !== 'admin')
                <a href="{{ route('topup.show') }}" class="btn btn-sm btn-primary w-100 rounded-pill mt-2 py-1 fw-bold" style="font-size: 0.72rem; background-color: #006ce4; border: none;">
                    <i class="bi bi-plus-lg me-1"></i> Top Up Saldo
                </a>
            @endif
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'penyedia')
                <a href="{{ route('withdraw.show') }}" class="btn btn-sm btn-success w-100 rounded-pill mt-2 py-1 fw-bold" style="font-size: 0.72rem; border: none;">
                    <i class="bi bi-cash-coin me-1"></i> Cairkan Saldo
                </a>
            @endif
        </div>
    @endif
    @if (Auth::user()->role == "admin")
        <div class="list-group list-group-flush">
            <a href="/admin/dashboard" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="/admin/assign_outsource" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-door-open me-2"></i> List Pengajuan
            </a>
            <a href="/admin/acc_room" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-file-earmark-text"></i> Laporan Outsource
            </a>
            <a href="/admin/users" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="fa fa-user-o me-2"></i> Daftar Pengguna
            </a>
            <a href="/admin/outsource" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="fa fa-handshake-o me-2"></i> Daftar Outsource
            </a>
            <a href="{{ route('admin.fines') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-exclamation-octagon me-2"></i> Manajemen Denda
            </a>
            <a href="{{ route('admin.report.profitability') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-graph-up-arrow me-2"></i> Laporan Profitabilitas
            </a>
            <a href="{{ route('admin.report.retention') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-people me-2"></i> Laporan Retensi Renter
            </a>
        </div>
    @elseif (Auth::user()->role == "penyedia")
        <div class="list-group list-group-flush">
            <a href="/penyedia/dashboard" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="{{ route('rooms.index') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-door-open me-2"></i> List Ruangan
            </a>
            <a href="{{ route('bookings.index') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi-clipboard-data me-2"></i> List Booking
            </a>
            <a href="/bookings/history" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-card-checklist me-2"></i> History Persewaan
            </a>
            <a href="{{ route('penyedia.fines.history') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-clock-history me-2"></i> History Denda
            </a>
            <a href="{{ route('penyedia.report.occupancy') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-bar-chart-line me-2"></i> Laporan Okupansi
            </a>
            <a href="{{ route('penyedia.report.finance') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-cash-coin me-2"></i> Laporan Keuangan
            </a>
        </div>
    @elseif (Auth::user()->role == "outsource")
        <div class="list-group list-group-flush">
            <a href="/outsource" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="{{ route('outsource.job') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-door-open me-2"></i> List Job
            </a>
            <a href="{{ route('outsource.history') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi-clipboard-data me-2"></i> Riwayat Laporan
            </a>
            <a href="{{ route('outsource.report') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-bar-chart-line me-2"></i> Laporan Kinerja
            </a>
        </div>
    @else
        <div class="list-group list-group-flush">
            <a href="/penyewa/dashboard" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="/penyewa/search" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-search me-2"></i> Cari Ruangan
            </a>
            <a href="/bookings/history" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-card-checklist me-2"></i> History Persewaan
            </a>
        </div>
    @endif
</div>