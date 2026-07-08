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
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: left 0.3s ease;
        }
        #page-content-wrapper {
            width: 100%;
            background: #f8f9fa;
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 250px; /* HARUS sama dengan lebar sidebar */
            right: 0;
            z-index: 1000;
            transition: left 0.3s ease;
            height: 56px;
        }
        .sidebar-heading {
            height: 56px;
            display: flex;
            align-items: center;
            box-sizing: border-box;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            margin-bottom: 20px;
            padding-left: 80px; /* Make space for fixed toggle button */
        }
        
        /* CSS State saat Sidebar Terlipat (Toggled) */
        #wrapper.toggled #sidebar-wrapper {
            left: -250px;
        }
        #wrapper.toggled #page-content-wrapper {
            margin-left: 0;
        }
        #wrapper.toggled .navbar {
            left: 0;
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

        /* Fixed Toggle Button styling */
        #menu-toggle {
            position: fixed;
            top: 11px;
            left: 15px;
            z-index: 1100; /* Higher than sidebar (1000) and navbar (1000) */
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background-color: #212529; /* Match bg-dark sidebar */
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        #menu-toggle:hover {
            background-color: #2c3034;
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Styling when Sidebar is Collapsed (Toggled) */
        #wrapper.toggled #menu-toggle {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        #wrapper.toggled #menu-toggle:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }
    </style>
    @yield('custom_css')
</head>
<body>

    <div id="wrapper">
        <!-- Fixed panel toggle button -->
        <button id="menu-toggle">
            <i class="bi bi-layout-sidebar-inset fs-5"></i>
        </button>

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

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Global Thousand Separator Script --}}
    <script>
        $(document).ready(function() {
            // Toggle Sidebar Click Listener
            $('#menu-toggle').on('click', function(e) {
                e.preventDefault();
                $('#wrapper').toggleClass('toggled');
            });

            function formatThousand(val) {
                if (val === null || val === undefined) return '';
                let clean = val.toString().replace(/[^0-9]/g, '');
                return clean.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Format initial values on page load
            $('.thousand-separator').each(function() {
                let val = $(this).val();
                if (val !== undefined && val !== '') {
                    $(this).val(formatThousand(val));
                }
            });

            // Format real-time on input
            $(document).on('input', '.thousand-separator', function() {
                let selectionStart = this.selectionStart;
                let originalLen = this.value.length;
                
                let formatted = formatThousand(this.value);
                this.value = formatted;
                
                let newLen = formatted.length;
                this.setSelectionRange(selectionStart + (newLen - originalLen), selectionStart + (newLen - originalLen));
            });

            // Strip separators before form submit
            $(document).on('submit', 'form', function() {
                $(this).find('.thousand-separator').each(function() {
                    let rawVal = $(this).val().replace(/[^0-9]/g, '');
                    $(this).val(rawVal);
                });
            });
        });
    </script>

    @yield('custom_js')
</body>
</html>