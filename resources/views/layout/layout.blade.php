<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <script>
        (function() {
            const getStoredTheme = () => localStorage.getItem('theme');
            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme();
                if (storedTheme) return storedTheme;
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            const theme = getPreferredTheme();
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
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
        body { 
            overflow-x: hidden; 
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
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
            transition: left 0.3s ease, background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        #page-content-wrapper {
            width: 100%;
            background-color: var(--bs-tertiary-bg);
            margin-left: 250px;
            transition: margin-left 0.3s ease, background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 250px; /* HARUS sama dengan lebar sidebar */
            right: 0;
            z-index: 1000;
            transition: left 0.3s ease, background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            height: 56px;
        }
        
        /* Transition for general cards, lists, tables, sub-navbar */
        .card, .list-group-item, .table, .dropdown-menu, #sub-navbar, .btn-date {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Micro-animation for theme toggle button */
        #theme-toggle-icon {
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.3s ease;
        }
        #theme-toggle-btn:hover #theme-toggle-icon {
            transform: scale(1.2) rotate(30deg);
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
            background-color: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            color: var(--bs-body-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        #wrapper.toggled #menu-toggle:hover {
            background-color: var(--bs-tertiary-bg);
            color: var(--bs-body-color);
            border-color: var(--bs-border-color-translucent);
        }

        /* Global Dark Mode Overrides for hardcoded classes */
        [data-bs-theme="dark"] .text-dark {
            color: var(--bs-body-color) !important;
        }
        /* Preserve dark text contrast on light/warning elements in dark mode */
        [data-bs-theme="dark"] .bg-warning .text-dark,
        [data-bs-theme="dark"] .btn-warning .text-dark,
        [data-bs-theme="dark"] .badge.text-dark,
        [data-bs-theme="dark"] .badge.bg-warning,
        [data-bs-theme="dark"] .badge.bg-warning .text-dark {
            color: #1a1d20 !important;
        }
        [data-bs-theme="dark"] .bg-white {
            background-color: var(--bs-card-bg) !important;
        }
        [data-bs-theme="dark"] .bg-light {
            background-color: var(--bs-tertiary-bg) !important;
        }
        [data-bs-theme="dark"] .text-muted {
            color: var(--bs-secondary-color) !important;
        }
        [data-bs-theme="dark"] .table-responsive {
            background-color: var(--bs-card-bg) !important;
        }
        [data-bs-theme="dark"] .card {
            background-color: var(--bs-card-bg) !important;
            border-color: var(--bs-border-color) !important;
        }
        [data-bs-theme="dark"] .table-light {
            --bs-table-color: var(--bs-body-color) !important;
            --bs-table-bg: var(--bs-tertiary-bg) !important;
            --bs-table-border-color: var(--bs-border-color) !important;
            background-color: var(--bs-tertiary-bg) !important;
            color: var(--bs-body-color) !important;
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

            // Theme Toggle Logic
            const themeToggleBtn = $('#theme-toggle-btn');
            const themeToggleIcon = $('#theme-toggle-icon');

            const updateThemeIcon = (theme) => {
                if (theme === 'dark') {
                    themeToggleIcon.removeClass('bi-moon-stars text-secondary').addClass('bi-sun-fill text-warning');
                } else {
                    themeToggleIcon.removeClass('bi-sun-fill text-warning').addClass('bi-moon-stars text-secondary');
                }
            }

            // Sync icon on load
            if (themeToggleIcon.length) {
                const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                updateThemeIcon(currentTheme);
            }

            themeToggleBtn.on('click', function(e) {
                e.preventDefault();
                const activeTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);
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