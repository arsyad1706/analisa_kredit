@extends('layouts.tailwind-template')
@include('components.new.modal.loading')
@push('script-inject')
    <script>
        $('#page_length').on('change', function() {
            $('#form').submit()
        })
    </script>
@endpush
@section('content')
    <section class="p-5 overflow-y-auto mt-5">
        <div class="head lg:flex grid grid-cols-1 justify-between w-full font-poppins">
            <div class="heading flex-auto">
                <p class="text-theme-primary font-semibold font-poppins text-xs">
                    Master Session
                </p>
                <h2 class="font-bold tracking-tighter text-2xl text-theme-text">
                    Session
                </h2>
            </div>
        </div>
        <div class="body-pages">
            <div class="table-wrapper border bg-white mt-8">
                <form id="form" method="get">
                    <div class="layout-wrapping p-3 lg:flex grid grid-cols-1 justify-center lg:justify-between">
                        <div class="left-layout lg:w-auto w-full lg:block flex justify-center">
                            <div class="flex gap-5 p-2">
                                <span class="mt-[10px] text-sm">Show</span>
                                <select name="page_length"
                                    class="border border-gray-300 rounded appearance-none text-center px-4 py-2 outline-none"
                                    id="page_length">
                                    <option value="10"
                                        @isset($_GET['page_length']) {{ $_GET['page_length'] == 10 ? 'selected' : '' }} @endisset>
                                        10</option>
                                    <option value="20"
                                        @isset($_GET['page_length']) {{ $_GET['page_length'] == 20 ? 'selected' : '' }} @endisset>
                                        20</option>
                                    <option value="50"
                                        @isset($_GET['page_length']) {{ $_GET['page_length'] == 50 ? 'selected' : '' }} @endisset>
                                        50</option>
                                    <option value="100"
                                        @isset($_GET['page_length']) {{ $_GET['page_length'] == 100 ? 'selected' : '' }} @endisset>
                                        100</option>
                                </select>
                                <span class="mt-[10px] text-sm">Entries</span>
                            </div>
                        </div>
                        <div class="right-layout lg:w-auto w-full">
                            <div class="input-search flex gap-2">
                                <input type="search" placeholder="Cari nama... " name="q" id="q"
                                    class="w-full px-8 outline-none text-sm p-3 border"
                                    value="{{ isset($_GET['q']) ? $_GET['q'] : '' }}" />
                                <button class="px-5 py-2 bg-theme-primary rounded text-white text-lg">
                                    <iconify-icon icon="ic:sharp-search" class="mt-2 text-lg"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="tables">
                            <thead>
                                <th class="text-center">No</th>
                                <th>Id Address</th>
                                <th>Email</th>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Cabang</th>
                                <th>Lama Login</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @php
                                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                    $page_length = isset($_GET['page_length']) ? $_GET['page_length'] : 10;
                                    $start = $page == 1 ? 1 : $page * $page_length - $page_length + 1;
                                    $end = $page == 1 ? $page_length : $start + $page_length - 1;
                                    $i = $page == 1 ? 1 : $start;
                                @endphp
                                @foreach ($data as $item)
                                    @php
                                        $cabang = '-';
                                        if ($item->id_cabang) {
                                            $dataCabang = DB::table('cabang')
                                                ->where('id', $item->id_cabang)
                                                ->first();
                                            $cabang = $dataCabang->cabang;
                                        }

                                        // Waktu login pengguna
                                        $startTime = new DateTime($item->created_at);

                                        // Waktu saat ini
                                        $endTime = new DateTime('now');

                                        // Hitung perbedaan waktu
                                        $interval = $endTime->diff($startTime);

                                        // Format waktu
                                        $hours = $interval->h;
                                        $minutes = $interval->i;
                                        $seconds = $interval->s;
                                    @endphp
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item->ip_address }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            {{ $item->karyawan ? $item->karyawan['nama'] : $item->name }}
                                        </td>
                                        <td>{{ $item->role }}</td>
                                        <td>{{ $cabang }}</td>
                                        <td>
                                            <span class="clock_{{$item->id}}"></span>
                                            <script>
                                                currentTime({{$hours}}, {{$minutes}}, {{$seconds}}, "clock_{{$item->id}}")
                                                function currentTime(h, m, s, widget_id) {
                                                    let hh = parseInt(h);
                                                    let mm = parseInt(m);
                                                    let ss = parseInt(s);
                                                    ss++;

                                                    if (ss > 59) {
                                                        mm++;
                                                        ss = 0;
                                                    }

                                                    if (mm > 59) {
                                                        hh++;
                                                        mm = 0;
                                                    }

                                                    hh = (hh < 10) ? "0" + hh : hh;
                                                    mm = (mm < 10) ? "0" + mm : mm;
                                                    ss = (ss < 10) ? "0" + ss : ss;

                                                    let time = hh + ":" + mm + ":" + ss;
                                                    document.querySelector(`.${widget_id}`).innerHTML = time;
                                                    var t = setTimeout(function(){ currentTime(hh, mm, ss, `${widget_id}`) }, 1000);
                                                }
                                            </script>
                                        </td>
                                        <td><h5 class="badge badge-info">Aktif</h5></td>
                                        <td>
                                            @if (auth()->user()->id != $item->user_id)
                                                <form action="{{ route('reset-session', $item->user_id) }}" method="post">
                                                    @csrf
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmResetSession-{{$key}}">
                                                        Reset
                                                    </button>
                                                    <div class="modal fade" id="confirmResetSession-{{$key}}" tabindex="-1" role="dialog" aria-labelledby="confirmResetSessionLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Apakah Anda yakin akan mereset sesi ini?</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                    <button type="submit" id="btn-hapus" class="btn btn-danger">Reset</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="footer-table p-2">
                        <div class="flex justify-between pb-3">
                            <div class="mt-5 ml-5 text-sm font-medium text-gray-500">
                                <p>Showing {{ $start }} - {{ $end }} from {{ $data->total() }} entries
                                </p>
                            </div>
                            {{ $data->links('pagination::tailwind') }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@push('custom-script')
    <script>
        function resetPassword(name, id) {
            Swal.fire({
                title: 'Perhatian!!',
                text: "Apakah anda yakin mereset password " + name + " ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#112042',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'ResetPassword',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#resetPasswordForm" + id).submit()
                }
            })
        }


    </script>
@endpush