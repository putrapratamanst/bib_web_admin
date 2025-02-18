<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name') }}</title>
        <link rel="shortcut icon" href="{{ asset('img/favicon/favicon.ico') }}">
        <link rel="stylesheet" href="{{ asset("assets/bootstrap/css/bootstrap.min.css") }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/jquery.ui.datepicker.min.css') }}" />
        <link rel="stylesheet" href="{{ asset("assets/sweetalert2/sweetalert2.min.css") }}" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

        @livewireStyles
    </head>
    <body>
        <x-navigation-menu />
        {{ $slot }}
        <script src="{{ asset("assets/js/jquery.min.js") }}"></script>
        <script data-navigate-once src="{{ asset("assets/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
        <script src="{{ asset("assets/js/jquery-ui.min.js") }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset("assets/sweetalert2/sweetalert2.min.js") }}"></script>
        <script src="{{ asset("assets/js/autoNumeric.min.js") }}"></script>
        @livewireScripts
        @livewire('wire-elements-modal')
        @stack('scripts')
        <script>
            $(".datepicker").datepicker({
                dateFormat: "dd-mm-yy",
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+5",
                showOtherMonths: true,
                selectOtherMonths: true
            });

            $('.rp2').autoNumeric({
                aSep: ".",
                aDec: ",",
                mDec: 2
            });

            // rp2-discount
            $('.rp2-discount').autoNumeric({
                aSep: ".",
                aDec: ",",
                mDec: 0,
                vMax: 100
            });

            $(".select2").select2({
                theme: "bootstrap-5",
                width: "100%"
            });
        </script>

        <script data-navigate-once>
            let Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            Livewire.on('swal:toast', param => {
                Toast.fire({
                    icon: param[0]['icon'],
                    title: param[0]['title'],
                });
            });
        </script>
    </body>
</html>