<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempat-In</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons (bi bi-...) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Font Awesome 4.7 (fa fa-...) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css">

    {{-- Quill JS --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    
    <style>
        /* CSS Dasar untuk Layout Sidebar + Content */
        body { overflow-x: hidden; }
        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar-wrapper {
            min-width: 250px; 
            max-width: 250px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        #page-content-wrapper {
            width: 100%;
            background: #f8f9fa;
            margin-left: 250px;
        }
        #sidebar-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px; /* sesuaikan */
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 250px; /* HARUS sama dengan lebar sidebar */
            right: 0;
            z-index: 1000;
        }
        .contain{
            padding-top: 70px !important;
        }
        
        .dt-footer {
            display: flex !important;
            justify-content: space-between !important; /* Memisahkan ke kiri dan kanan */
            align-items: center !important;
            padding: 1.5rem !important;
            background-color: #ffffff;
            border-top: 1px solid #f1f5f9;
        }

        /* Style untuk bagian 'Show entries' (Kiri) */
        .dataTables_length {
            font-size: 0.85rem;
            color: #64748b;
        }

        .dataTables_length select {
            margin: 0 5px;
            padding: 0.25rem 0.5rem !important;
            border-radius: 0.5rem !important;
            border: 1px solid #e2e8f0 !important;
            outline: none;
        }

        /* Style untuk navigasi (Kanan) */
        .dataTables_paginate {
            display: flex;
            align-items: center;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.35rem 0.85rem !important;
            margin-left: 5px !important;
            border-radius: 0.5rem !important;
            border: 1px solid #e2e8f0 !important;
            background-color: #fff !important;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #1e3a8a !important; /* Biru gelap sesuai Armada */
            color: white !important;
            border-color: #1e3a8a !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background-color: #f1f5f9 !important;
        }

        /* Hilangkan garis fokus biru standar DataTables */
        .paginate_button:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
    @yield('custom_css')
</head>
<body>

    <div id="wrapper">
        @include('layout.sidebar')

        <div id="page-content-wrapper">
            @include('layout.navbar')

            <div class="container-fluid p-4 contain">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    {{-- Jquery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    
    {{-- DataTables --}}
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>

    {{-- Quill JS --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    @yield('custom_js')
</body>
</html>