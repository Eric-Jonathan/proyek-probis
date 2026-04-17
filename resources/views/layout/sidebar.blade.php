<div class="bg-dark text-white p-3" id="sidebar-wrapper">
    <div class="sidebar-heading fs-4 fw-bold border-bottom pb-3 mb-3">Tempat-In</div>
    @if (Auth::user()->role == "admin")
        <div class="list-group list-group-flush">
            <a href="/admin/dashboard" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="/admin/acc_room" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-door-open me-2"></i> Pengajuan Ruangan
            </a>
            {{-- <a href="/rooms" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-door-open me-2"></i> list Ruangan
            </a> --}}
            {{-- <a href="/makan" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-door-open me-2"></i> List Ruangan
            </a> --}}
        </div>
    @elseif (Auth::user()->role == "penyedia")
        <div class="list-group list-group-flush">
            <a href="/penyedia/dashboard" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="/rooms" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-door-open me-2"></i> List Ruangan
            </a>
            {{-- <a href="/makan" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-door-open me-2"></i> Pesan Ruangan
            </a> --}}
        </div>
    @else
        <div class="list-group list-group-flush">
            <a href="/penyewa/dashboard" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="/rooms" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-door-open me-2"></i> Ruangan
            </a>
            <a href="/makan" class="list-group-item list-group-item-action bg-dark text-white border-0 py-2">
                <i class="bi bi-door-open me-2"></i> Pesan Ruangan
            </a>
        </div>
    @endif
</div>