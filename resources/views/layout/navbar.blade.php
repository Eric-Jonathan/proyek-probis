<nav class="navbar navbar-expand-lg border-bottom shadow-sm fixed-top bg-body" style="z-index: 1000">
    <div class="container-fluid">

        <div class="ms-auto d-flex align-items-center">
            <!-- Theme Toggle Button
            <button class="btn btn-link p-0 me-3 border-0 shadow-none d-flex align-items-center justify-content-center" id="theme-toggle-btn" style="text-decoration: none;">
                <i class="bi bi-moon-stars fs-5 text-secondary" id="theme-toggle-icon"></i>
            </button> -->
            
            <span class="me-3 text-muted">Welcome, {{ Auth::user()->username }}</span>
            <div class="dropdown" style="cursor: pointer">
                <i class="bi bi-person-circle fs-4 cursor-pointer" data-bs-toggle="dropdown"></i>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>