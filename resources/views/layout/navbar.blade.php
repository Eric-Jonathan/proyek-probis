<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm fixed-top">
    <div class="container-fluid">
        <button class="btn btn-outline-primary btn-sm" id="menu-toggle">
            <i class="bi bi-list"></i>
        </button>
        
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3 text-muted">Welcome, {{ Auth::user()->username }}</span>
            <div class="dropdown">
                <i class="bi bi-person-circle fs-4 cursor-pointer" data-bs-toggle="dropdown"></i>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>