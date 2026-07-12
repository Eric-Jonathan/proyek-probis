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

        /* Custom Theme Switch Toggle styles to match user reference image */
        .theme-switch-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .theme-switch-btn {
            position: relative;
            width: 48px;
            height: 24px;
            background-color: #374151; /* Dark grey by default */
            border-radius: 50px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: background-color 0.3s, border-color 0.3s;
            padding: 0;
            display: flex;
            align-items: center;
            outline: none;
        }
        
        [data-bs-theme="light"] .theme-switch-btn {
            background-color: #e5e7eb;
            border-color: #9ca3af;
        }

        .theme-switch-knob {
            position: absolute;
            top: 1px;
            left: 1px;
            width: 18px;
            height: 18px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        /* Slide knob to right in dark mode */
        [data-bs-theme="dark"] .theme-switch-knob {
            transform: translateX(24px);
            background-color: #1f2937;
        }
        
        .theme-switch-knob i {
            font-size: 10px;
            transition: color 0.3s, transform 0.4s ease;
        }
        
        .theme-switch-btn:hover .theme-switch-knob i {
            transform: scale(1.15) rotate(15deg);
        }
        
        [data-bs-theme="dark"] .theme-switch-knob i {
            color: #fbbf24; /* yellow sun in dark mode */
        }
        
        [data-bs-theme="light"] .theme-switch-knob i {
            color: #4b5563; /* grey moon in light mode */
        }

        .theme-switch-text {
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            margin-top: 4px;
            text-transform: uppercase;
            transition: color 0.3s;
            line-height: 1;
        }
        
        [data-bs-theme="dark"] .theme-switch-text {
            color: #9ca3af;
        }
        
        [data-bs-theme="light"] .theme-switch-text {
            color: #4b5563;
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

        /* ========================================================================= */
        /* GLOBAL DATATABLES STYLING OVERRIDES (BOOTSTRAP 5 INTEGRATION)             */
        /* ========================================================================= */
        /* 1. Show Entries Dropdown (Left side) */
        .dataTables_length {
            font-size: 0.82rem;
            color: var(--bs-secondary-color);
        }
        .dataTables_length select {
            margin: 0 6px;
            padding: 0.375rem 1.75rem 0.375rem 0.75rem !important;
            border-radius: 0.5rem !important;
            border: 1px solid var(--bs-border-color) !important;
            background-color: var(--bs-body-bg) !important;
            color: var(--bs-body-color) !important;
            outline: none;
            font-size: 0.82rem;
            cursor: pointer;
            display: inline-block;
            width: auto;
        }
        .dataTables_length select:focus {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
        }
        
        /* 2. Search Box (Right side) */
        .dataTables_filter {
            font-size: 0.82rem;
            color: var(--bs-secondary-color);
        }
        .dataTables_filter input {
            margin-left: 8px;
            padding: 0.375rem 0.75rem !important;
            border-radius: 0.5rem !important;
            border: 1px solid var(--bs-border-color) !important;
            background-color: var(--bs-body-bg) !important;
            color: var(--bs-body-color) !important;
            outline: none;
            font-size: 0.82rem;
            min-width: 200px;
            display: inline-block;
            width: auto;
        }
        .dataTables_filter input:focus {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
        }

        /* 3. Table Header Styling consistency */
        table.dataTable thead th {
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            color: var(--bs-secondary-color) !important;
            background-color: var(--bs-tertiary-bg) !important;
            border-bottom: 2px solid var(--bs-border-color) !important;
            padding: 0.75rem 1rem !important;
        }
        
        /* 4. Reset default DataTables pagination button styles to avoid BS5 clash */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            background: none !important;
            display: inline !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            border: none !important;
            background: none !important;
        }

        /* 5. Pagination Buttons (Bootstrap 5 styling) */
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem !important;
            display: flex;
            justify-content: flex-end;
        }
        .dataTables_wrapper .dataTables_paginate .pagination {
            margin-bottom: 0 !important;
            gap: 4px;
            display: flex;
            flex-wrap: wrap;
        }
        .dataTables_wrapper .dataTables_paginate .pagination .page-item .page-link {
            padding: 0.35rem 0.75rem !important;
            border-radius: 0.5rem !important;
            color: var(--bs-body-color) !important;
            background-color: var(--bs-body-bg) !important;
            border: 1px solid var(--bs-border-color) !important;
            font-size: 0.82rem !important;
            font-weight: 500 !important;
            transition: all 0.2s ease;
            box-shadow: none !important;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
        }
        .dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }
        .dataTables_wrapper .dataTables_paginate .pagination .page-item:hover:not(.active) .page-link {
            background-color: var(--bs-tertiary-bg) !important;
            color: var(--bs-body-color) !important;
        }
        .dataTables_wrapper .dataTables_paginate .pagination .page-item.disabled .page-link {
            background-color: var(--bs-tertiary-bg) !important;
            border-color: var(--bs-border-color) !important;
            color: var(--bs-secondary-color) !important;
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* 6. Info Text (Left bottom) */
        .dataTables_info {
            font-size: 0.82rem !important;
            color: var(--bs-secondary-color) !important;
            padding-top: 1rem !important;
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
            const themeToggleText = $('#theme-toggle-text');

            const updateThemeIcon = (theme) => {
                if (theme === 'dark') {
                    themeToggleIcon.removeClass('bi-moon-stars text-secondary').addClass('bi-sun-fill text-warning');
                    if (themeToggleText.length) themeToggleText.text('DARK MODE');
                } else {
                    themeToggleIcon.removeClass('bi-sun-fill text-warning').addClass('bi-moon-stars text-secondary');
                    if (themeToggleText.length) themeToggleText.text('LIGHT MODE');
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

            // Intercept form submissions that have a data-confirm attribute
            $(document).on('submit', 'form[data-confirm]', function(e) {
                e.preventDefault();
                const form = this;
                const message = $(form).data('confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                
                Swal.fire({
                    title: 'Konfirmasi Tindakan',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#006ce4',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    heightAuto: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        const tempConfirm = form.getAttribute('data-confirm');
                        form.removeAttribute('data-confirm');
                        form.submit();
                        form.setAttribute('data-confirm', tempConfirm);
                    }
                });
            });

            // Intercept button clicks that have data-confirm
            $(document).on('click', '.btn-confirm-action', function(e) {
                e.preventDefault();
                const button = this;
                const form = $(button).closest('form')[0];
                const message = $(button).data('confirm') || 'Apakah Anda yakin ingin melanjutkan?';
                
                Swal.fire({
                    title: 'Konfirmasi Pembayaran',
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Bayar Sekarang',
                    cancelButtonText: 'Batal',
                    heightAuto: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (form) {
                            form.submit();
                        } else if (button.href) {
                            window.location.href = button.href;
                        }
                    }
                });
            });
        });
    </script>

    @if(session('success'))
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    heightAuto: false
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: "{{ session('error') }}",
                    heightAuto: false
                });
            });
        </script>
    @endif

    @yield('custom_js')
</body>
</html>