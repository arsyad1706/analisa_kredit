<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<html ng-app="AngularApp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Analisa Kredit | BANK UMKM JATIM</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('') }}vendor/select2/select2.min.css" />
    <link rel="stylesheet"
        href="{{ asset('') }}vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" href="{{ asset('') }}vendor/sweetalert-master/dist/sweetalert.css" />
    <link href="{{ asset('') }}build/please-wait.css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://unpkg.com/popper.js@1.12.8/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tooltip.js@1.3.1/dist/umd/tooltip.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular.min.js"></script> --}}
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    {{-- <link href="assets/build/css/default.css" rel="stylesheet"> --}}

    <link rel="stylesheet" href="{{ asset('') }}css/custom.css" />
    <link rel="icon" href="{{ asset('') }}img/favicon.png" type="image/gif" sizes="16x6" />
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
    @stack('extraStyle')
</head>

<body ng-controller="MainCtrl">
    @php
        $user = DB::table('users')
            ->where('id', auth()->user()->id)
            ->first();
        $cabang = DB::table('cabang')
            ->where('id', $user->id_cabang)
            ->first();
    @endphp
    <div class="inner" ng-view>
    </div>
    <div class="container custom">
        <nav class="navbar navbar-expand-lg py-3 navbar-dark mt-4">
            <div class="container custom">

                <a class="navbar-brand font-weight-bold " href="#">
                    <p class="p-0 m-0"><img src="{{ asset('img/logo.png') }}" alt="logo.png" style="width:200px;"></p>
                    {{-- <img src="{{ asset('') }}img/logo2.png" height="50px" width="150px" class="mr-2" alt=""> --}}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item" id="navDashboard">
                            <a class="nav-link {{ Request::segment(1) == 'dashboard' ? 'active' : '' }}"
                                href="{{ url('/dashboard') }}"><span class="fa fa-home mr-1"></span> Dashboard <span
                                    class="sr-only">(current)</span></a>
                        </li>
                        @if (Request::segment(1) == 'pengajuan-kredit' &&
                                (auth()->user()->role == 'Staf Analis Kredit' || auth()->user()->role == 'PBO / PBP'))
                            <li class="nav-item" id="navAnalisa">
                                <a class="nav-link {{ Request::segment(1) == 'pengajuan-kredit' ? 'active' : '' }}"
                                    href="{{ url('pengajuan-kredit') }}"><span class="fa fa-credit-card mr-1"></span>
                                    Analisa Kredit</a>
                            </li>
                        @else
                            <li class="nav-item" id="navAnalisa">
                                <a class="nav-link {{ Request::segment(1) == 'pengajuan-kredit' ? 'active' : '' }}"
                                    href="{{ url('pengajuan-kredit') }}"><span class="fa fa-credit-card mr-1"></span>
                                    Analisa Kredit</a>
                            </li>
                        @endif
                        @if (auth()->user()->role == 'Administrator')
                            <li class="nav-item dropdown" id="navAdmin">
                                <a class="nav-link dropdown-toggle {{ Request::segment(1) == 'rekap' ? 'active' : '' }}"
                                    href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span class="fa fa-file-alt"></span> Data Master
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="dropdown-item" href="{{ route('cabang.index') }}">Master Kantor
                                        Cabang</a>
                                    <a class="dropdown-item" href="{{ route('kabupaten.index') }}">Master Kabupaten</a>
                                    <a class="dropdown-item" href="{{ route('kecamatan.index') }}">Master Kecamatan</a>
                                    <a class="dropdown-item" href="{{ route('desa.index') }}">Master Desa</a>
                                    <a class="dropdown-item" href="{{ route('user.index') }}">Master User</a>
                                    <a class="dropdown-item" href="{{ route('master-item.index') }}">Master Item</a>
                                    <a class="dropdown-item" href="{{ route('merk.index') }}">Master Merk</a>
                                    <a class="dropdown-item" href="{{ route('tipe.index') }}">Master Tipe</a>
                                    <a class="dropdown-item" href="{{ route('index-session') }}">Master Session</a>
                                    <a class="dropdown-item" href="{{ route('index-api-session') }}">Master API Session</a>
                                    <a class="dropdown-item" href="{{ route('skema-kredit.index') }}">Master Skema Kredit</a>
                                    <a class="dropdown-item" href="{{ route('produk-kredit.index') }}">Master Produk Kredit</a>
                                    <a class="dropdown-item" href="{{ route('skema-limit.index') }}">Master Skema Limit</a>
                                </div>
                            </li>
                        @endif
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="fa fa-user"></span>
                                {{-- @if (auth()->user()->role == 'Administrator')
                                    {{ auth()->user()->role }}
                                @else
                                    {{ auth()->user()->role }} - {{ $cabang->cabang }}
                                @endif --}}
                                @php
                                    $name_karyawan = App\Http\Controllers\DashboardController::getKaryawan();
                                @endphp
                                @if (auth()->user()->nip)
                                {{ $name_karyawan ? $name_karyawan : auth()->user()->name }}
                                @else
                                {{auth()->user()->name}}
                                @endif
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="{{ route('change_password') }}">Ganti Password</a>
                                {{-- <a class="dropdown-item" data-toggle="modal" data-target="#logout">Logout</a> --}}
                                <a class="dropdown-item" onclick="logouts()" href="#">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        @yield('dashboard')
        <div class="my-4">
            @if (Request::segment(1) == 'pengajuan-kredit' ||
                    (Request::segment(1) == 'lanjutkan-draft' &&
                        (auth()->user()->role == 'Staf Analis Kredit' || auth()->user()->role == 'PBO / PBP')))
                @if (request()->routeIs('pengajuan-kredit.index') == 'pengajuan-kredit')
                    @include('layouts.full-card')
                @else
                    @include('layouts.side-card')
                @endif
            @else
                @if (Request::segment(1) == 'pengajuan-kredit' && auth()->user()->role == 'Penyelia Kredit')
                    @if (request()->routeIs('pengajuan-kredit.index') == 'pengajuan-kredit')
                        @include('layouts.full-card')
                    @else
                        @include('layouts.penyelia-side-card')
                    @endif
                @else
                    @if (Request::segment(1) == 'pengajuan-kredit' && auth()->user()->role == 'PBP')
                        @if (request()->routeIs('pengajuan-kredit.index') == 'pengajuan-kredit')
                            @include('layouts.full-card')
                        @else
                            @include('layouts.pbp-side-card')
                        @endif
                    @else
                        @include('layouts.full-card')
                    @endif
                @endif

            @endif
        </div>

    </div>

    {{-- <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Apakah anda yakin?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Anda akan keluar dari Aplikasi Analisa Kredit
                </div>
                <div class="modal-footer">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" id="logouts" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    @yield('modal')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script>
    <script src="{{ asset('') }}vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('') }}vendor/sweetalert-master/dist/sweetalert.min.js"></script>
    <script src="{{ asset('') }}js/select2.full.min.js"></script>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular.min.js"></script> --}}
    <script src="{{ asset('') }}build/please-wait.min.js"></script>
    <script type="text/javascript">
        var loading_screen = pleaseWait({
            logo: "{{ asset('img/load.png') }}",
            // logo: "Loading...",
            backgroundColor: '#112042f1',
            loadingHtml: "<div class='spinner'><div class='double-bounce1'></div><div class='double-bounce2'></div><div class='double-bounce3'></div></div>"
        });
        window.loading_screen.finish();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script>
        function logouts() {
            // console.log("helloworld");
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda akan keluar dari Aplikasi Analisa Kredit",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#112042',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Logout',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#logout-form").submit()
                }
            })
        }
    </script>
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
        $(".select2").select2();
        $(".datepicker").datepicker({
            format: 'dd-mm-yyyy',
            todayHighlight: true,
        });
        $(".delete").click(function(e) {
            e.preventDefault()
            swal({
                    title: "Apakah anda yakin?",
                    text: 'Anda akan menghapus penugasan',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    confirmButtonText: 'Yakin',
                    closeOnConfirm: false,
                    cancelButtonText: 'Batal',
                },
                function() {
                    $("#delete-penugasan").submit()
                }
            );
            console.log('bisa');
        });
        
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

    @stack('custom-script')
</body>

</html>
