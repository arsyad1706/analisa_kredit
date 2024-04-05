<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- meta tags --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Analisa Kredit | BANK UMKM JATIM</title>
    {{-- font google --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/gif" sizes="16x6" />
    {{-- tailwind configuration css --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/owl-carousel/owl.theme.default.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/lightbox/css/lightbox.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/0.5.7/perfect-scrollbar.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @stack('css')
</head>
<body class="bg-theme-body font-poppins" id="app">
    {{-- section modal only --}}
    @yield('modal')
    {{-- wrapping all layout --}}
    <div class="layout-wrapper flex justify-center">
        {{-- sidebar --}}

        <div class="layout-sidebar flex-auto">
            @if(\Request::route()->getName() != 'change_password')
                @include('components.new.sidebar')
            @endif
        </div>
        {{-- pages --}}
        <div class="layout-pages flex-auto w-full h-screen overflow-y-auto">
            {{-- header --}}
            @include('components.new.header')
            @yield('content')
        </div>
    </div>
    @include('components.new.modal.logout')
    @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9"])
</body>
    {{-- javascript plugins --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/0.5.7/perfect-scrollbar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('plugins/lightbox/js/lightbox.js') }}"></script>
    <script src="{{ asset('plugins/owl-carousel/owl.carousel.js') }}"></script>
    <script src="{{ asset('plugins/owl-carousel/owl.carousel.min.js') }}"></script>

    {{-- app.js is custom global js --}}
    <script src="{{ asset('js/app.js') }}"></script>
    {{-- inject javascript from view  --}}
    <script>
        function generateCsrfToken() {
            var token = "{{csrf_token()}}"
            if (token == '') {
                generateCsrfToken();
            }
            else {
                return token;
            }
        }
        function calculateFormula(formula) {
            try {
                // console.log('calculate with formula')
                // console.log(formula)
                if (formula.includes('inp_'))
                    return ''
                else {
                    var result = formula.replace(/[^-()\d/*+.]/g, '');
                    return eval(result);
                }
            }
            catch (e) {
                // console.log(e)
                return 0
            }
        }
        function alphaOnly(string) {
            string = string.replace(/[^A-Za-z_]/g, '');

            return string;
        }
    </script>
    @stack('script-inject')
</html>
