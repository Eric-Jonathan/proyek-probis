<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempat-In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
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

    @yield('custom_js')
</body>
</html>