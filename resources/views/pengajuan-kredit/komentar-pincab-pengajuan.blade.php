    @extends('layouts.tailwind-template')

@section('modal')
    @include('dagulir.modal.filter')

    @include('dagulir.pengajuan-kredit.modal.pilih-penyelia')
    {{-- @include('dagulir.modal.konfirmSendToPinca') --}}
    @include('dagulir.modal.cetak-file-sppk')
    @include('dagulir.modal.cetak-file-pk')
    @include('dagulir.modal.approval')
    @include('dagulir.modal.approvalSipde')
    @include('dagulir.modal.kembalikan')
    @include('components.new.modal.loading')
    @include('dagulir.modal.lanjutkan-penyelia')
    @include('pengajuan-kredit.modal.modal-log-pengajuan')
    @include('pengajuan-kredit.modal.modal-hapus')
    @include('pengajuan-kredit.modal.modal-restore')
    @include('pengajuan-kredit.modal.modal-kembalikan-new')
@endsection

@section('content')
    <section class="p-5 overflow-y-auto mt-5">
        <div class="head space-y-5 w-full font-poppins">
            <div class="heading flex-auto">
                <p class="text-theme-primary font-semibold font-poppins text-xs">
                    Analisa Kredit
                </p>
                <h2 class="font-bold tracking-tighter text-2xl text-theme-text">
                    Analisa Kredit
                </h2>
            </div>
            <div class="{{Auth::user()->role == 'Administrator' ? 'layout lg:flex grid grid-cols-1 lg:mt-0 justify-between w-full gap-5' : ''}}">
                @if (Auth::user()->role == 'Administrator')
                    <div class="left-button gap-2 flex lg:justify-end">
                        <div class="tab-table-wrapper p-0">
                            <button data-tab="dagulir" id="pincetar-button" class="tab-button tab-button-start {{ Request()->query('search_tab') == "" || Request()->query('search_tab') == "pincetar" ? 'active' : '' }}">
                                <iconify-icon icon="tabler:database-dollar" class="mt-1"></iconify-icon>Data Pengajuan
                            </button>
                            <button data-tab="sipde" id="sipde-button" class="tab-button tab-button-end {{ Request()->query('search_tab') == "sipde" ? 'active' : '' }}">
                                <iconify-icon icon="solar:dollar-minimalistic-linear" class="mt-1"></iconify-icon>Sampah Pengajuan
                            </button>
                        </div>
                    </div>
                @endif
                <div class="right-button gap-2 flex lg:justify-end">
                    @if (Request()->query() != null)
                    <a href="{{ url()->current() }}"
                        class="px-7 py-2 rounded flex justify-center items-center font-semibold bg-theme-primary border text-white btn-reset">
                        <span class="mt-1 mr-3">
                            <iconify-icon icon="pajamas:repeat"></iconify-icon>
                        </span>
                        <span class="ml-1 text-sm"> Reset </span>
                    </a>
                    @endif
                        <a data-modal-id="modal-filter" href="#"
                            class="open-modal px-7 py-2 flex font-poppins justify-center items-center rounded font-semibold bg-white border text-theme-secondary">
                            <span class="">
                                <svg xmlns="http://www.w3.org/2000/svg" class="lg:w-[24px] w-[19px]" viewBox="-2 -2 24 24">
                                    <path fill="currentColor"
                                        d="m2.08 2l6.482 8.101A2 2 0 0 1 9 11.351V18l2-1.5v-5.15a2 2 0 0 1 .438-1.249L17.92 2H2.081zm0-2h15.84a2 2 0 0 1 1.561 3.25L13 11.35v5.15a2 2 0 0 1-.8 1.6l-2 1.5A2 2 0 0 1 7 18v-6.65L.519 3.25A2 2 0 0 1 2.08 0z" />
                                </svg>
                            </span>
                            <span class="ml-3 text-sm"> Filter </span>
                        </a>
                </div>
            </div>
        </div>
        <div class="body-pages">
            <div class="table-wrapper-tab">
                {{-- data-pengajuan --}}
                <div id="tab-dagulir" class="tab-content {{ Request()->query('search_tab') == "" || Request()->query('search_tab') == "pincetar" ? 'active' : '' }}">
                    <div class="table-wrapper border bg-white mt-3">
                        <form id="form" method="get">
                            <div class="layout-wrapping p-3 lg:flex grid grid-cols-1 justify-center lg:justify-between">
                                <div class="left-layout lg:w-auto w-full lg:block flex justify-center">
                                    <form action="{{ route('pengajuan-kredit.index') }}" method="GET">
                                        <div class="flex gap-5 p-2">
                                            <span class="mt-[10px] text-sm">Show</span>
                                            <select name="page_length"
                                                class="border border-gray-300 rounded appearance-none text-center px-4 py-2 outline-none"
                                                id="page_length">
                                                <option value="10"
                                                    @isset($_GET['page_length']) {{ $_GET['page_length'] == 1 ? 'selected' : '' }} @endisset>
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
                                    </form>
                                </div>
                                <div class="right-layout lg:w-auto w-full">
                                @if (Request()->tAwal != null)
                                    <input type="text" name="tAwal" value="{{ Request()->tAwal }}" hidden>
                                @endif
                                @if (Request()->tAkhir != null)
                                    <input type="text" name="tAkhir" value="{{ Request()->tAkhir }}" hidden>
                                @endif
                                @if (Request()->cbg != null)
                                    <input type="text" name="cbg" value="{{ Request()->cbg }}" hidden>
                                @endif
                                @if (Request()->pss != null)
                                    <input type="text" name="pss" value="{{ Request()->pss }}" hidden>
                                @endif
                                @if (Request()->score != null)
                                    <input type="text" name="score" value="{{ Request()->score }}" hidden>
                                @endif
                                @if (Request()->sts != null)
                                    <input type="text" name="sts" value="{{ Request()->sts }}" hidden>
                                @endif
                                    <div class="input-search flex gap-2">
                                        <input type="search" name="search" value="{{ Request()->query('search') }}"  placeholder="Cari nama nasabah... "
                                            class="w-full px-8 outline-none text-sm p-3 border" />
                                        <button class="px-5 py-2 bg-theme-primary rounded text-white text-lg">
                                            <iconify-icon icon="ic:sharp-search" class="mt-2 text-lg"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="tables">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Nama Nasabah</th>
                                        <th>Posisi</th>
                                        <th>Durasi</th>
                                        <th>Skor</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                        $page_length = isset($_GET['page_length']) ? $_GET['page_length'] : 10;
                                        $start = $page == 1 ? 1 : $page * $page_length - $page_length + 1;
                                        $end = $page == 1 ? $page_length : $start + $page_length - 1;
                                        $i = $page == 1 ? 1 : $start;
                                        $status = config('dagulir.status');
                                        $status_color = config('dagulir.status_color');
                                        $jenis_usaha = config('dagulir.jenis_usaha');
                                        $tipe_pengajuan = config('dagulir.tipe_pengajuan');
                                    @endphp
                                    @forelse ($data_pengajuan as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->tanggal }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td class="text-center text-sm">
                                            @if ($item->posisi == 'Proses Input Data')
                                                @php
                                                    $nama_pemroses = 'undifined';
                                                    $user = \App\Models\User::select('nip')->where('id', $item->id_staf)->first();
                                                    if ($user)
                                                        $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($user->nip);
                                                    else {
                                                        $check_log = \DB::table('log_pengajuan')
                                                                        ->select('nip')
                                                                        ->where('id_pengajuan', $item->id)
                                                                        ->where('activity', 'LIKE', 'Staf%')
                                                                        ->orderBy('id', 'DESC')
                                                                        ->first();
                                                        if ($check_log)
                                                            $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($check_log->nip);
                                                    }
                                                @endphp
                                                <span>Staff</span>
                                                <br>
                                                <span class="text-color-soft">({{$nama_pemroses}})</span>
                                            @elseif ($item->posisi == 'Review Penyelia')
                                                @php
                                                    $nama_pemroses = 'undifined';
                                                    $user = \App\Models\User::select('nip')->where('id', $item->id_penyelia)->first();
                                                    if ($user)
                                                        $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($user->nip);
                                                    else {
                                                        $check_log = \DB::table('log_pengajuan')
                                                                        ->select('nip')
                                                                        ->where('id_pengajuan', $item->id)
                                                                        ->where('activity', 'LIKE', 'Penyelia%')
                                                                        ->orderBy('id', 'DESC')
                                                                        ->first();
                                                        if ($check_log)
                                                            $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($check_log->nip);
                                                    }
                                                @endphp
                                                <span>Penyelia</span>
                                                <br>
                                                <span class="text-color-soft">({{$nama_pemroses}})</span>
                                            @elseif ($item->posisi == 'PBO')
                                                @php
                                                    $nama_pemroses = 'undifined';
                                                    $user = \App\Models\User::select('nip')->where('id', $item->id_pbo)->first();
                                                    if ($user)
                                                        $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($user->nip);
                                                    else {
                                                        $check_log = \DB::table('log_pengajuan')
                                                                        ->select('nip')
                                                                        ->where('id_pengajuan', $item->id)
                                                                        ->where('activity', 'LIKE', 'PBO%')
                                                                        ->orderBy('id', 'DESC')
                                                                        ->first();
                                                        if ($check_log)
                                                            $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($check_log->nip);
                                                    }
                                                @endphp
                                                <span>PBO</span>
                                                <br>
                                                <span class="text-color-soft">({{$nama_pemroses}})</span>
                                            @elseif ($item->posisi == 'PBP')
                                                @php
                                                    $nama_pemroses = 'undifined';
                                                    $user = \App\Models\User::select('nip')->where('id', $item->id_pbp)->first();
                                                    if ($user)
                                                        $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($user->nip);
                                                    else {
                                                        $check_log = \DB::table('log_pengajuan')
                                                                        ->select('nip')
                                                                        ->where('id_pengajuan', $item->id)
                                                                        ->where('activity', 'LIKE', 'PBP%')
                                                                        ->orderBy('id', 'DESC')
                                                                        ->first();
                                                        if ($check_log)
                                                            $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($check_log->nip);
                                                    }
                                                @endphp
                                                <span>PBP</span>
                                                <br>
                                                <span class="text-color-soft">({{$nama_pemroses}})</span>
                                            @elseif ($item->posisi == 'Pincab')
                                                @php
                                                    $nama_pemroses = 'undifined';
                                                    $user = \App\Models\User::select('nip')->where('id', $item->id_pincab)->first();
                                                    if ($user)
                                                        $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($user->nip);
                                                    else {
                                                        $check_log = \DB::table('log_pengajuan')
                                                                        ->select('nip')
                                                                        ->where('id_pengajuan', $item->id)
                                                                        ->where('activity', 'LIKE', 'Pincab%')
                                                                        ->orderBy('id', 'DESC')
                                                                        ->first();
                                                        if ($check_log)
                                                            $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($check_log->nip);
                                                    }
                                                @endphp
                                                <span>Pincab</span>
                                                <br>
                                                <span class="text-color-soft">({{$nama_pemroses}})</span>
                                            @else
                                                @if ($item->posisi == 'Selesai')
                                                    <span>Disetujui</span>
                                                @else
                                                    <span>Ditolak</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->posisi == 'Selesai')
                                                @php
                                                    $awal = date_create($item->tanggal);
                                                    $akhir = date_create($item->tanggal_review_pincab);
                                                    $interval = $akhir->diff($awal);
                                                    $res = $interval->format('%a');
                                                @endphp

                                                @if ($res != 0)
                                                    @if ($res == 1 || $res == 2 || $res == 3)
                                                        <font class="text-green-500">{{ $res . ' hari' }}</font>
                                                    @elseif ($res == 4 || $res == 5 || $res == 6)
                                                        <font class="text-yellow-500">{{ $res . ' hari' }}</font>
                                                    @else
                                                        <font class="text-red-500">{{ $res . ' hari' }}</font>
                                                    @endif
                                                @else
                                                    {{ '-' }}
                                                @endif
                                            @else
                                                {{ '-' }}
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $avgResult = $item->average_by_sistem;
                                                if ($item->posisi == 'Review Penyelia')
                                                    $avgResult = $item->average_by_penyelia ? $item->average_by_penyelia : $item->average_by_sistem;
                                                else if ($item->posisi == 'PBO')
                                                    $avgResult = $item->average_by_pbo ? $item->average_by_pbo : $item->average_by_penyelia;
                                                else if ($item->posisi == 'PBP')
                                                    $avgResult = $item->average_by_pbp ? $item->average_by_pbp : $item->average_by_pbo;
                                                else if ($item->posisi == 'Pincab') {
                                                    if (!$item->average_by_penyelia && !$item->average_by_pbo && $item->average_by_pbp)
                                                        $avgResult = $item->average_by_pbp;
                                                    else if (!$item->average_by_penyelia && $item->average_by_pbo && !$item->average_by_pbp)
                                                        $avgResult = $item->average_by_pbo;
                                                    else if ($item->average_by_penyelia && !$item->average_by_pbo && !$item->average_by_pbp)
                                                        $avgResult = $item->average_by_penyelia;
                                                }
                                                else if ($item->posisi == 'Ditolak') {
                                                    if (!$item->average_by_penyelia && !$item->average_by_pbo && $item->average_by_pbp)
                                                        $avgResult = $item->average_by_pbp;
                                                    else if (!$item->average_by_penyelia && $item->average_by_pbo && !$item->average_by_pbp)
                                                        $avgResult = $item->average_by_pbo;
                                                    else if ($item->average_by_penyelia && !$item->average_by_pbo && !$item->average_by_pbp)
                                                        $avgResult = $item->average_by_penyelia;
                                                }
                                                else if ($item->posisi == 'Selesai') {
                                                    if (!$item->average_by_penyelia && !$item->average_by_pbo && $item->average_by_pbp)
                                                        $avgResult = $item->average_by_pbp;
                                                    else if (!$item->average_by_penyelia && $item->average_by_pbo && !$item->average_by_pbp)
                                                        $avgResult = $item->average_by_pbo;
                                                    else if ($item->average_by_penyelia && !$item->average_by_pbo && !$item->average_by_pbp)
                                                        $avgResult = $item->average_by_penyelia;
                                                }
                                                if ($avgResult > 0 && $avgResult <= 2) {
                                                    $status = "merah";
                                                } elseif ($avgResult > 2 && $avgResult <= 3) {
                                                    $status = "kuning";
                                                } elseif ($avgResult > 3) {
                                                    $status = "hijau";
                                                } else {
                                                    $status = "merah";
                                                }
                                            @endphp
                                            @if ($status == 'hijau')
                                                <font class="text-green-500">
                                                    {{ $avgResult }}
                                                </font>
                                            @elseif ($status == 'kuning')
                                                <font class="text-yellow-500">
                                                    {{ $avgResult }}
                                                </font>
                                            @elseif ($status == 'merah')
                                                <font class="text-red-500">
                                                    {{ $avgResult }}
                                                </font>
                                            @else
                                                <font class="text-neutral-800">
                                                    {{ $avgResult }}
                                                </font>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->posisi == 'Selesai')
                                                <font class="text-green-500">Disetujui</font>
                                            @elseif ($item->posisi == 'Ditolak')
                                                <font class="text-red-500">Ditolak</font>
                                            @else
                                                <font class="text-yellow-500">On Progress</font>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex">
                                                <div class="dropdown-tb">
                                                    <button
                                                    type="button"
                                                    class="dropdown-tb-toggle border rounded px-4 py-2 hover:bg-gray-100 hover:text-gray-500"
                                                    >
                                                    <iconify-icon
                                                        icon="ph:dots-three-outline-vertical-fill"
                                                        class="mt-2"
                                                    ></iconify-icon>
                                                    </button>
                                                    <ul class="dropdown-tb-menu hidden">
                                                        @if (Auth::user()->role == 'Staf Analis Kredit' && $item->posisi == 'Proses Input Data')
                                                            @if ($item->average_by_sistem)
                                                                @if (!$item->id_penyelia)
                                                                    <a href="#" onclick="showTindakLanjut({{ $item->id }},'penyelia kredit')" class="w-full cursor-pointer">
                                                                        <li class="item-tb-dropdown">
                                                                            Tindak lanjut Review Penyelia
                                                                        </li>
                                                                    </a>
                                                                @endif
                                                            @else
                                                                <a href="{{route('dagulir.pengajuan.create')}}?dagulir={{$item->id}}" class="w-full cursor-pointer">
                                                                    <li class="item-tb-dropdown">
                                                                        Tindak Lanjut
                                                                    </li>
                                                                </a>
                                                            @endif
                                                        @endif
                                                        @if (Auth::user()->role == 'Staf Analis Kredit' && $item->posisi == 'Selesai')
                                                            @php
                                                                $tglCetak = DB::table('log_cetak')
                                                                    ->where('id_pengajuan', $item->id)
                                                                    ->first();
                                                            @endphp

                                                            @if ($tglCetak == null || !$tglCetak->tgl_cetak_sppk)
                                                                <a target="_blank" href="{{ route('dagulir.cetak-sppk-dagulir', $item->id) }}" class="w-full cursor-pointer">
                                                                    <li class="item-tb-dropdown">
                                                                        Cetak SPPK
                                                                    </li>
                                                                </a>
                                                            @elseif (!$item->sppk && $tglCetak->tgl_cetak_sppk)
                                                                <a href="#" class="w-full cursor-pointer show-upload-sppk" data-toggle="modal" data-target="uploadSPPKModal" data-id="{{ $item->id }}" data-kode_pendaftaran="{{$item->kode_pendaftaran}}">
                                                                    <li class="item-tb-dropdown">
                                                                        Upload File SPPK
                                                                    </li>
                                                                </a>
                                                            @elseif (!$tglCetak->tgl_cetak_pk && $item->sppk && $tglCetak->tgl_cetak_sppk )
                                                                <a target="_blank" href="{{ route('dagulir.cetak-pk-dagulir', $item->id) }}" class="w-full cursor-pointer">
                                                                    <li class="item-tb-dropdown">
                                                                        Cetak PK
                                                                    </li>
                                                                </a>
                                                            @elseif (!$item->pk && $tglCetak->tgl_cetak_pk && $item->sppk)
                                                                <a href="#" class="w-full cursor-pointer show-upload-pk" data-toggle="modal" data-target="uploadPKModal" data-id="{{ $item->id }}" data-kode_pendaftaran="{{$item->kode_pendaftaran}}">
                                                                    <li class="item-tb-dropdown">
                                                                        Realisasi Kredit
                                                                    </li>
                                                                </a>
                                                            @endif
                                                        @endif
                                                        @if ((Auth()->user()->role == 'Penyelia Kredit'))
                                                            @if ($item->posisi == 'Review Penyelia')
                                                                <a href="{{ route('dagulir.detailjawaban', $item->id) }}" class="w-full cursor-pointer">
                                                                    <li class="item-tb-dropdown">
                                                                        Review
                                                                    </li>
                                                                </a>
                                                                <a href="#" class="w-full cursor-pointer">
                                                                    <li class="item-tb-dropdown kembalikan-modal cursor-pointer" data-id="{{ $item->id }}" data-backto="staf" >
                                                                        Kembalikan ke Staff
                                                                    </li>
                                                                </a>
                                                            @endif
                                                            @if ($item->posisi == 'Review Penyelia' && $item->tanggal_review_penyelia)
                                                                <a href="javascript:void(0)" id="modalConfirmPincab" data-id_pengajuan="{{$item->id}}" data-nama="{{$item->nama}}" class="w-full cursor-pointer">
                                                                    <li class="item-tb-dropdown">
                                                                        Lanjutkan Ke Pincab
                                                                    </li>
                                                                </a>
                                                            @endif
                                                        @elseif ((Auth()->user()->role == 'PBO'))
                                                            @if ($item->posisi == 'PBO' && $item->tanggal_review_penyelia
                                                                && $item->id_pbo)
                                                                <a href="{{ route('dagulir.detailjawaban', $item->id) }}" class="w-full cursor-pointer">
                                                                    <li class="item-tb-dropdown">
                                                                        Review
                                                                    </li>
                                                                </a>
                                                                <a href="#" class="w-full cursor-pointer">
                                                                    <li class="item-tb-dropdown kembalikan-modal cursor-pointer" data-id="{{ $item->id }}" data-backto="penyelia">
                                                                        Kembalikan ke Penyelia
                                                                    </li>
                                                                </a>
                                                            @endif
                                                            @if ($item->posisi == 'PBO' && $item->tanggal_review_pbo
                                                                && $item->id_pbo)
                                                                <a href="javascript:void(0)" id="modalConfirmPincab" data-id_pengajuan="{{$item->id}}" data-nama="{{$item->nama}}" class="w-full cursor-pointer">
                                                                    <li class="item-tb-dropdown">
                                                                        Lanjutkan Ke Pincab
                                                                    </li>
                                                                </a>
                                                            @endif
                                                        @elseif ((Auth()->user()->role == 'PBP'))
                                                            @if ($item->posisi == 'PBP' && $item->tanggal_review_pbp
                                                                && $item->id_pbp)
                                                                <a href="{{ route('dagulir.detailjawaban', $item->id) }}" class="w-full cursor-pointer">
                                                                    <li class="item-tb-dropdown">
                                                                        Review
                                                                    </li>
                                                                </a>
                                                                <a class="w-full cursor-pointer" href="#" data-id="{{ $item->id }}" data-backto="{{$item->id_pbo ? 'pbo' : 'penyelia'}}">
                                                                    <li class="item-tb-dropdown kembalikan-modal" cursor-pointer>
                                                                        Kembalikan ke {{$item->id_pbo ? 'PBO' : 'Penyelia'}}
                                                                    </li>
                                                                </a>
                                                            @endif
                                                            @if ($item->posisi == 'PBP' && $item->tanggal_review_penyelia
                                                                && ($item->id_pbo && $item->tanggal_review_pbo)
                                                                && ($item->id_pbp && $item->tanggal_review_pbp))
                                                                    <a href="javascript:void(0)" id="modalConfirmPincab" data-id_pengajuan="{{$item->id}}" data-nama="{{$item->nama}}" class="w-full cursor-pointer">
                                                                        <li class="item-tb-dropdown">
                                                                            Lanjutkan Ke Pincab
                                                                        </li>
                                                                    </a>
                                                            @endif
                                                        @elseif ((Auth()->user()->role == 'Pincab'))
                                                            @if ($item->posisi == 'Pincab')
                                                                @if ($item->id_pincab)
                                                                <a href="{{ route('pengajuan.check.pincab.status.detail', $item->id_pengajuan) }}"
                                                                    class="w-full cursor-pointer review-pincab">
                                                                    <li class="item-tb-dropdown">
                                                                        Review
                                                                    </li>
                                                                </a>
                                                                @php
                                                                $userPBO = \App\Models\User::select('id')
                                                                    ->where('id_cabang', $item->id_cabang)
                                                                    ->where('role', 'PBO')
                                                                    ->whereNotNull('nip')
                                                                    ->first();

                                                                $userPBP = \App\Models\User::select('id')
                                                                    ->where('id_cabang', $item->id_cabang)
                                                                    ->where('role', 'PBP')
                                                                    ->whereNotNull('nip')
                                                                    ->first();
                                                                @endphp
                                                                    @if ($userPBP)
                                                                        <a href="#" class="w-full cursor-pointer kembalikan_pengajuan" data-id="{{ $item->id }}" data-backto="PBP" data-target="modalKembalikan">
                                                                            <li class="item-tb-dropdown open-modal">
                                                                                Kembalikan ke PBP
                                                                            </li>
                                                                        </a>
                                                                    @elseif ($userPBO)
                                                                        <a href="#" class="w-full cursor-pointer kembalikan_pengajuan" data-id="{{ $item->id }}" data-backto="PBO" data-target="modalKembalikan">
                                                                            <li class="item-tb-dropdown open-modal">
                                                                                Kembalikan ke PBO
                                                                            </li>
                                                                        </a>
                                                                    @else
                                                                        <a href="#" class="w-full cursor-pointer kembalikan_pengajuan" data-id="{{ $item->id }}" data-backto="Penyelia">
                                                                            <li class="item-tb-dropdown kembalikan-modal cursor-pointer">
                                                                                Kembalikan ke Penyelia
                                                                            </li>
                                                                        </a>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @else

                                                        <a href="{{ route('cetak', $item->id_pengajuan) }}"
                                                            class="cursor-pointer w-full" target="_blank">
                                                            <li class="item-tb-dropdown">
                                                                Cetak
                                                            </li>
                                                        </a>
                                                        @endif
                                                        @if (Auth::user()->role == 'Administrator')
                                                            <a href="#" class="w-full cursor-pointer log-pengajuan" data-toggle="modal" data-target="#modalLogPengajuan-{{ $item->id }}">
                                                                <li class="item-tb-dropdown">
                                                                    Log Pengajuan
                                                                </li>
                                                            </a>
                                                            <a href="javascript:void(0)" class="w-full cursor-pointer text-red-400 show-hapus-pengajuan" data-toggle="modal" data-nama="{{$item->nama}}" data-id="{{ $item->id }}" data-target="#modalHapusPengajuan">
                                                                <li class="item-tb-dropdown">
                                                                    Hapus
                                                                </li>
                                                            </a>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    @empty
                                    <td colspan="7" class="text-center" style="background: rgba(71, 145,254,0.05) !important">Data
                                        Kosong</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="footer-table p-4">
                            <div class="flex justify-between pb-3">
                                <div class="mt-5 ml-5 text-sm font-medium text-gray-500">
                                <p>Showing {{ $start }} - {{ $end }} from {{ $data_pengajuan->total() }} entries</p>
                                </div>
                                {{ $data_pengajuan->links('pagination::tailwind') }}
                            </div>
                        </div>
                    </div>
                </div>
                {{-- data-sampah --}}
                @if (Auth::user()->role == 'Administrator')
                <div id="tab-sipde" class="tab-content {{ Request()->query('search_tab') == "sipde" ? 'active' : '' }}">
                    <div class="table-wrapper border bg-white">
                        <form id="form" method="get">
                            <div class="layout-wrapping p-3 lg:flex grid grid-cols-1 justify-center lg:justify-between">
                                <div class="left-layout lg:w-auto w-full lg:block flex justify-center">
                                    <form action="{{ route('pengajuan-kredit.index') }}" method="GET">
                                        <div class="flex gap-5 p-2">
                                            <span class="mt-[10px] text-sm">Show</span>
                                            <select name="page_length"
                                                class="border border-gray-300 rounded appearance-none text-center px-4 py-2 outline-none"
                                                id="page_length">
                                                <option value="10"
                                                    @isset($_GET['page_length']) {{ $_GET['page_length'] == 1 ? 'selected' : '' }} @endisset>
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
                                    </form>
                                </div>
                                <div class="right-layout lg:w-auto w-full">
                                @if (Request()->tAwal != null)
                                    <input type="text" name="tAwal" value="{{ Request()->tAwal }}" hidden>
                                @endif
                                @if (Request()->tAkhir != null)
                                    <input type="text" name="tAkhir" value="{{ Request()->tAkhir }}" hidden>
                                @endif
                                @if (Request()->cbg != null)
                                    <input type="text" name="cbg" value="{{ Request()->cbg }}" hidden>
                                @endif
                                @if (Request()->pss != null)
                                    <input type="text" name="pss" value="{{ Request()->pss }}" hidden>
                                @endif
                                @if (Request()->score != null)
                                    <input type="text" name="score" value="{{ Request()->score }}" hidden>
                                @endif
                                @if (Request()->sts != null)
                                    <input type="text" name="sts" value="{{ Request()->sts }}" hidden>
                                @endif
                                    <div class="input-search flex gap-2">
                                        <input type="search" name="search" value="{{ Request()->query('search') }}"  placeholder="Cari nama nasabah... "
                                            class="w-full px-8 outline-none text-sm p-3 border" />
                                        <button class="px-5 py-2 bg-theme-primary rounded text-white text-lg">
                                            <iconify-icon icon="ic:sharp-search" class="mt-2 text-lg"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive pl-5 pr-5">
                            <table class="tables">
                                <thead>
                                    <tr class="table-primary">
                                        <th class="text-center">#</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Nama Nasabah</th>
                                        <th>Cabang</th>
                                        <th class="text-center">Posisi</th>
                                        <th>Status</th>
                                        <th>Tanggal Hapus</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                        $page_length = isset($_GET['page_length']) ? $_GET['page_length'] : 10;
                                        $start = $page == 1 ? 1 : $page * $page_length - $page_length + 1;
                                        $end = $page == 1 ? $page_length : $start + $page_length - 1;
                                        $i = $page == 1 ? 1 : $start;
                                    @endphp
                                    @foreach ($sampah_pengajuan as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->tanggal }}</td>
                                            <td>{{ $item->nama }}</td>
                                            @if (auth()->user()->role == 'Administrator')
                                                @php
                                                    $c = \App\Models\Cabang::where('id', $item->id_cabang)->first();
                                                @endphp
                                                <td>{{ $c->cabang }}</td>
                                            @endif
                                            <td class="text-center text-sm">
                                                @if ($item->posisi == 'Proses Input Data')
                                                    @php
                                                        $nama_pemroses = 'undifined';
                                                        $user = \App\Models\User::select('nip')->where('id', $item->id_staf)->first();
                                                        if ($user)
                                                            $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($user->nip);
                                                        else {
                                                            $check_log = \DB::table('log_pengajuan')
                                                                            ->select('nip')
                                                                            ->where('id_pengajuan', $item->id)
                                                                            ->where('activity', 'LIKE', 'Staf%')
                                                                            ->orderBy('id', 'DESC')
                                                                            ->first();
                                                            if ($check_log)
                                                                $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($check_log->nip);
                                                        }
                                                    @endphp
                                                    <span>Staff</span>
                                                    <br>
                                                    <span class="text-red-500">({{$nama_pemroses}})</span>
                                                @elseif ($item->posisi == 'Review Penyelia')
                                                    @php
                                                        $nama_pemroses = 'undifined';
                                                        $user = \App\Models\User::select('nip')->where('id', $item->id_penyelia)->first();
                                                        if ($user)
                                                            $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($user->nip);
                                                        else {
                                                            $check_log = \DB::table('log_pengajuan')
                                                                            ->select('nip')
                                                                            ->where('id_pengajuan', $item->id)
                                                                            ->where('activity', 'LIKE', 'Penyelia%')
                                                                            ->orderBy('id', 'DESC')
                                                                            ->first();
                                                            if ($check_log)
                                                                $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($check_log->nip);
                                                        }
                                                    @endphp
                                                    <span>Penyelia</span>
                                                    <br>
                                                    <span class="text-red-500">({{$nama_pemroses}})</span>
                                                @elseif ($item->posisi == 'PBO')
                                                    @php
                                                        $nama_pemroses = 'undifined';
                                                        $user = \App\Models\User::select('nip')->where('id', $item->id_pbo)->first();
                                                        if ($user)
                                                            $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($user->nip);
                                                        else {
                                                            $check_log = \DB::table('log_pengajuan')
                                                                            ->select('nip')
                                                                            ->where('id_pengajuan', $item->id)
                                                                            ->where('activity', 'LIKE', 'PBO%')
                                                                            ->orderBy('id', 'DESC')
                                                                            ->first();
                                                            if ($check_log)
                                                                $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($check_log->nip);
                                                        }
                                                    @endphp
                                                    <span>PBO</span>
                                                    <br>
                                                    <span class="text-red-500">({{$nama_pemroses}})</span>
                                                @elseif ($item->posisi == 'PBP')
                                                    @php
                                                        $nama_pemroses = 'undifined';
                                                        $user = \App\Models\User::select('nip')->where('id', $item->id_pbp)->first();
                                                        if ($user)
                                                            $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($user->nip);
                                                        else {
                                                            $check_log = \DB::table('log_pengajuan')
                                                                            ->select('nip')
                                                                            ->where('id_pengajuan', $item->id)
                                                                            ->where('activity', 'LIKE', 'PBP%')
                                                                            ->orderBy('id', 'DESC')
                                                                            ->first();
                                                            if ($check_log)
                                                                $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($check_log->nip);
                                                        }
                                                    @endphp
                                                    <span>PBP</span>
                                                    <br>
                                                    <span class="text-red-500">({{$nama_pemroses}})</span>
                                                @elseif ($item->posisi == 'Pincab')
                                                    @php
                                                        $nama_pemroses = 'undifined';
                                                        $user = \App\Models\User::select('nip')->where('id', $item->id_pincab)->first();
                                                        if ($user)
                                                            $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($user->nip);
                                                        else {
                                                            $check_log = \DB::table('log_pengajuan')
                                                                            ->select('nip')
                                                                            ->where('id_pengajuan', $item->id)
                                                                            ->where('activity', 'LIKE', 'Pincab%')
                                                                            ->orderBy('id', 'DESC')
                                                                            ->first();
                                                            if ($check_log)
                                                                $nama_pemroses = \App\Http\Controllers\PengajuanKreditController::getKaryawanFromAPIStatic($check_log->nip);
                                                        }
                                                    @endphp
                                                    <span>Pincab</span>
                                                    <br>
                                                    <span class="text-red-500">({{$nama_pemroses}})</span>
                                                @else
                                                    <span>Selesai</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->posisi == 'Selesai')
                                                    <font class="text-green-500">Disetujui</font>
                                                @elseif ($item->posisi == 'Ditolak')
                                                    <font class="text-red-500">Ditolak</font>
                                                @else
                                                    <font class="text-yellow-500">On Progress</font>
                                                @endif
                                            </td>
                                            <td>
                                                {{$item->deleted_at}}
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                   <a href="javascript:void(0)" class="btn bg-yellow-200 show-restore" style="text-decoration: none;" data-toggle="modal" data-target="#modalRestore" data-nama="{{ $item->nama }}" data-id="{{ $item->id }}">
                                                        Kembalikan
                                                   </a>
                                                </div>
                                            </td>
                                        </tr>


                                    @endforeach
                                </tbody>
                            </table>
                                {{-- {{ $sampah_pengajuan->links() }}
                                Menampilkan
                                {{ $sampah_pengajuan->firstItem() }}
                                -
                                {{ $sampah_pengajuan->lastItem() }}
                                dari
                                {{ $sampah_pengajuan->total() }} Data
                                <div class="pull-right">
                                </div> --}}
                        </div>
                        <div class="footer-table p-2">
                            <div class="flex justify-between pb-3">
                                <div class="mt-5 ml-5 text-sm font-medium text-gray-500">
                                <p>Showing {{ $start }} - {{ $end }} from {{ $sampah_pengajuan->total() }} entries</p>
                                </div>
                                {{ $sampah_pengajuan->links('pagination::tailwind') }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
@endsection
@push('script-inject')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('.review-pincab').on('click', function(){
        $("#preload-data").removeClass("hidden");
    })

    $('.btn-kembalikan-pengajuan').on('click', function(){
        $("#modalKembalikan").addClass("hidden");
        $("#preload-data").removeClass("hidden");

    })
    $('.btn-reset').on('click', function(){
        $("#preload-data").removeClass("hidden");
    })

    $(`#modal-filter .btn-submit`).on('click', function () {
        $("#preload-data").removeClass("hidden");
        $('#form-filter').submit();
    })

    $('.kembalikan_pengajuan').on('click', function(){
        const target = '#modalKembalikan';
        const id = $(this).data('id');
        const backto = $(this).data('backto')

        $(`${target} #id_pengajuan`).val(id)
        $(`${target} #text_backto`).html(backto)
        $(`${target}`).removeClass('hidden')
    })
    // tab pane
    $(".tab-table-wrapper .tab-button").click(function(e) {
        e.preventDefault();
        var tabID = $(this).data('tab');
        $(this).addClass('active').siblings().removeClass('active');
        $('#tab-'+tabID).addClass('active').siblings().removeClass('active');

        if(tabID == 'dagulir'){
            $('#title-table').html('Dagulir')
            $('#add-pengajuan').removeClass('hidden');
        }else{
            $('#title-table').html('Dagulir')
            $('#add-pengajuan').addClass('hidden');
        }
    });
    // action
    $('.log-pengajuan').on('click', function() {
        const target = $(this).data('target');
        $(`${target}`).removeClass('hidden')
    })
    $('.show-hapus-pengajuan').on('click',function() {
        const target = $(this).data('target');
        const nama = $(this).data('nama');

        $(`${target} #content`).html(`
                <p>Apakah Anda Yakin Ingin Menghapus Pengajuan Kredits, Atas Nama <b>${nama}</b>?</p>
                <p>Note! Data Yang Dihapus Akan Di Pindah Ke Sampah.</p>
        `)
        var idPengajuan = $(this).data('id');
        $(`${target} #idPengajuan`).val(idPengajuan);
        var url = '{{ url('') }}'
        var deleteUrl = url + '/delete-pengajuan-kredit/' + idPengajuan;
        $('#form-delete-cabang').attr('action', deleteUrl);
        $('#form-delete-cabang').attr('method', 'POST'); // Diubah ke POST untuk Laravel

        $(`${target}`).removeClass('hidden')

    })
    // kembalikan prose
    $('.show-restore').on('click',function(e) {
        const target = $(this).data('target');
        console.log(target);
        const nama = $(this).data('nama');
        const id = $(this).data('id');
        $(`${target} #content`).html(`
        <p> Apakah Anda yakin ingin mengembalikan data pengajuan atas nama <b>${nama}</b>?</p>
        `)

        $(`${target} #idPengajuan`).val(id);
        $(`${target}`).removeClass('hidden');
    })

</script>
<script>
    $('#page_length').on('change', function() {
        $('#form').submit()
    })

    var token = "gTWx1U1bVhtz9h51cRNoiluuBfsHqty5MCdXRdmWthFDo9RMhHgHIwrU9DBFVaNj";

    $(document).ready(function () {
        var cbgValue = '{{ Request()->query('cbg') }}';
        $.ajax({
            type: "GET",
            url: "/api/v1/get-cabang",
            headers: {
                'token': token,
            },
            success: function (response) {
                $.map(response.data, function (item, i) {
                    $('#cabang').append(
                        `<option value="${item.kode_cabang}" ${cbgValue == item.kode_cabang ? 'selected' : ''}>${item.cabang}</option>`
                    );
                });
            }
        });
    });
</script>
@endpush
