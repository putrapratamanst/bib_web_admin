<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="shortcut icon" href="{{ asset('img/favicon/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset("assets/bootstrap/css/bootstrap.min.css") }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.ui.datepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset("assets/sweetalert2/sweetalert2.min.css") }}" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- <link rel="stylesheet" href="{{ asset("assets/datatables/datatables.min.css") }}" /> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body class="mb-4">
    <x-navbar />

    @yield('content')

    <script src="{{ asset("assets/js/jquery.min.js") }}"></script>
    <script src="{{ asset("assets/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
    <script src="{{ asset("assets/js/jquery-ui.min.js") }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset("assets/sweetalert2/sweetalert2.min.js") }}"></script>
    <script src="{{ asset("assets/js/autoNumeric.min.js") }}"></script>
    <!-- <script src="{{ asset("assets/datatables/datatables.min.js") }}"></script> -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(function() {
            $('.datepicker').datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-10:+10',
            });

            $('.autonumeric').autoNumeric('init', {
                aSep: '.',
                aDec: ',',
                aForm: true,
            });

            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
            });
        });
    </script>
    @stack('scripts')
    @livewireScripts
</body>
</html>
