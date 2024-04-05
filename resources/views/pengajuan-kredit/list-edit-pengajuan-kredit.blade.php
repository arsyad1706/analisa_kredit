@extends('layouts.tailwind-template')

@section('modal')
    @include('dagulir.modal.filter')

    @include('dagulir.pengajuan-kredit.modal.pilih-penyelia')
    {{-- @include('dagulir.modal.konfirmSendToPinca') --}}
    @include('dagulir.modal.cetak-file-sppk')
    @include('dagulir.modal.cetak-file-po')
    @include('dagulir.modal.cetak-file-pk')
    @include('dagulir.modal.approval')
    @include('dagulir.modal.approvalSipde')
    @include('dagulir.modal.kembalikan')
    @include('components.new.modal.loading')
    @include('dagulir.modal.lanjutkan-penyelia')
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
            <div class="layout lg:flex grid grid-cols-1 lg:mt-0 justify-between w-full gap-5">
                <div class="left-button gap-2 flex lg:justify-end">
                    @if (Request()->query() != null)
                    <a href="{{ url()->current() }}"
                        class="px-7 py-2 rounded flex justify-center items-center font-semibold bg-theme-primary border text-white btn-reset">
                        <span class="mt-1 mr-3">
                            <iconify-icon icon="pajamas:repeat"></iconify-icon>
                        </span>
                        <span class="ml-1 text-sm"> Reset </span>
                    </a>
                    @endif
                        <a  data-modal-id="modal-filter" href="#"
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
                <div class="right-button gap-2 flex lg:justify-start">
                    <a
                        href="{{ route('pengajuan-kredit-draft') }}"
                        class="px-7 py-2 flex font-poppins justify-center items-center rounded font-semibold bg-white border text-theme-secondary">
                        <span class="">
                            <iconify-icon icon="fluent:drafts-16-regular"></iconify-icon>
                        </span>
                        <span class="ml-3 text-sm"> Draft </span>
                    </a>
                    <a href="{{ route('pengajuan-kredit.create') }}"
                        class="px-7 py-2 rounded flex justify-center items-center font-semibold bg-theme-primary border text-white">
                        <span class="mt-1 mr-3">
                            <iconify-icon icon="fa6-solid:plus"></iconify-icon>
                        </span>
                        <span class="ml-1 text-sm"> Tambah pengajuan </span>
                    </a>
                </div>
            </div>
        </div>
        <div class="body-pages">
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
                            @forelse ($data_pengajuan as $item)
                            @php
                                $nama_pemroses = 'undifined';
                                $user = \App\Models\User::select('nip')->where('id', $item->id_staf)->first();
                                if ($item->posisi == 'Proses Input Data') {
                                    $user = \App\Models\User::select('nip')->where('id', $item->id_staf)->first();
                                } else if ($item->posisi == 'Review Penyelia') {
                                    $user = \App\Models\User::select('nip')->where('id', $item->id_penyelia)->first();
                                } else if ($item->posisi == 'PBO') {
                                    $user = \App\Models\User::select('nip')->where('id', $item->id_pbo)->first();
                                } else if ($item->posisi == 'PBP') {
                                    $user = \App\Models\User::select('nip')->where('id', $item->id_pbp)->first();
                                } else if ($item->posisi == 'Pincab' || $item->posisi == 'Selesai' || $item->posisi == 'Ditolak') {
                                    $user = \App\Models\User::select('nip')->where('id', $item->id_pincab)->first();
                                }

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
                                $item->nama_pemroses = $nama_pemroses;

                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->tanggal }}</td>
                                <td>{{ $item->nama }}</td>
                                <td class="text-center text-sm">
                                    @if ($item->pk && $item->posisi == 'Selesai')
                                       Disetujui
                                    @elseif ($item->posisi == 'Ditolak')
                                       Ditolak
                                    @else
                                        @php
                                            $alasan = DB::table('alasan_pengembalian_data')
                                                    ->where('ke', 'Proses Input Data')
                                                    ->where('id_pengajuan', $item->id)
                                                    ->count();
                                        @endphp
                                        @if ($item->posisi == 'Proses Input Data' && $alasan > 0)
                                            Pengembalian Data
                                        @else
                                            {{$item->posisi == 'Selesai' ? 'Disetujui' : $item->posisi}}
                                        @endif
                                    @endif
                                    @if ($item->posisi != 'Selesai' && $item->posisi != 'Ditolak')
                                        <p class="text-red-500">{{ $item->nama_pemroses }}</p>
                                    @endif
                                </td>
                                {{-- durasi --}}
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
                                    $status_skor = "";
                                    if ($avgResult > 0 && $avgResult <= 2) {
                                        $status_skor = "merah";
                                    } elseif ($avgResult > 2 && $avgResult <= 3) {
                                        $status_skor = "kuning";
                                    } elseif ($avgResult > 3) {
                                        $status_skor = "hijau";
                                    } else {
                                        $status_skor = "merah";
                                    }
                                @endphp
                                @if ($status_skor == 'hijau')
                                    <font class="text-green-500">
                                        {{ $avgResult }}
                                    </font>
                                @elseif ($status_skor == 'kuning')
                                    <font class="text-yellow-500">
                                        {{ $avgResult }}
                                    </font>
                                @elseif ($status_skor == 'merah')
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
                                    <div class="flex justify-center">
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
                                                    <a href="{{ route('dagulir.pengajuan.edit', $item->id_pengajuan) }}"
                                                        class="w-full cursor-pointer review-penyelia">
                                                        <li class="item-tb-dropdown">
                                                            Edit data
                                                        </li>
                                                    </a>
                                                    @if ($item->average_by_sistem)
                                                        @if (!$item->id_penyelia)
                                                            <a href="#" onclick="showTindakLanjut({{ $item->id }},'penyelia kredit')" class="w-full cursor-pointer">
                                                                <li class="item-tb-dropdown">
                                                                    Tindak lanjut Review Penyelia
                                                                </li>
                                                            </a>
                                                        @else
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
                                                    @if ($item->skema_kredit == 'KKB')
                                                        @if ($tglCetak == null || !$tglCetak->tgl_cetak_sppk)
                                                            <a target="_blank" href="{{ route('dagulir.cetak-sppk-dagulir', $item->id) }}" class="w-full cursor-pointer" id="download-sppk">
                                                                <li class="item-tb-dropdown">
                                                                    Download SPPK
                                                                </li>
                                                            </a>
                                                        @elseif (!$item->sppk && $tglCetak->tgl_cetak_sppk)
                                                            <a href="#" class="w-full cursor-pointer show-upload-sppk" data-toggle="modal" data-target="uploadSPPKModal" data-id="{{ $item->id }}">
                                                                <li class="item-tb-dropdown">
                                                                    Upload File SPPK
                                                                </li>
                                                            </a>
                                                        @elseif (!$tglCetak->tgl_cetak_po && $item->sppk && $tglCetak->tgl_cetak_sppk )
                                                            <a target="_blank" href="{{ route('cetak-po', $item->id) }}" class="w-full cursor-pointer" id="download-pk">
                                                                <li class="item-tb-dropdown">
                                                                    Download PO
                                                                </li>
                                                            </a>
                                                        @elseif (!$item->po && $tglCetak->tgl_cetak_po && $item->sppk)
                                                            <a href="#" class="w-full cursor-pointer show-upload-po" data-toggle="modal" data-target="uploadPOModal" data-skema="{{$item->skema_kredit}}" data-id="{{ $item->id }}">
                                                                <li class="item-tb-dropdown">
                                                                    Upload File PO
                                                                </li>
                                                            </a>
                                                        @elseif (!$tglCetak->tgl_cetak_pk && $item->po && $tglCetak->tgl_cetak_po )
                                                            <a target="_blank" href="{{ route('dagulir.cetak-pk-dagulir', $item->id) }}" class="w-full cursor-pointer" id="download-pk">
                                                                <li class="item-tb-dropdown">
                                                                    Download PK
                                                                </li>
                                                            </a>
                                                        @elseif (!$item->pk && $tglCetak->tgl_cetak_pk && $item->po)
                                                            <a href="#" class="w-full cursor-pointer show-upload-pk" data-toggle="modal" data-target="uploadPKModal" data-skema="{{$item->skema_kredit}}" data-id="{{ $item->id }}">
                                                                <li class="item-tb-dropdown">
                                                                    Realisasi Kredit
                                                                </li>
                                                            </a>
                                                        @endif
                                                    @else
                                                        @if ($tglCetak == null || !$tglCetak->tgl_cetak_sppk)
                                                            <a target="_blank" href="{{ route('dagulir.cetak-sppk-dagulir', $item->id) }}" class="w-full cursor-pointer" id="download-sppk">
                                                                <li class="item-tb-dropdown">
                                                                    Download SPPK
                                                                </li>
                                                            </a>
                                                        @elseif (!$item->sppk && $tglCetak->tgl_cetak_sppk)
                                                            <a href="#" class="w-full cursor-pointer show-upload-sppk" data-toggle="modal" data-target="uploadSPPKModal" data-id="{{ $item->id }}">
                                                                <li class="item-tb-dropdown">
                                                                    Upload File SPPK
                                                                </li>
                                                            </a>
                                                        @elseif (!$tglCetak->tgl_cetak_pk && $item->sppk && $tglCetak->tgl_cetak_sppk )
                                                            <a target="_blank" href="{{ route('dagulir.cetak-pk-dagulir', $item->id) }}" class="w-full cursor-pointer" id="download-pk">
                                                                <li class="item-tb-dropdown">
                                                                    Download PK
                                                                </li>
                                                            </a>
                                                        @elseif (!$item->pk && $tglCetak->tgl_cetak_pk && $item->sppk)
                                                            <a href="#" class="w-full cursor-pointer show-upload-pk" data-toggle="modal" data-target="uploadPKModal" data-skema="{{$item->skema_kredit}}" data-id="{{ $item->id }}">
                                                                <li class="item-tb-dropdown">
                                                                    Realisasi Kredit
                                                                </li>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                                @if ((Auth()->user()->role == 'Penyelia Kredit'))
                                                    @if ($item->posisi == 'Review Penyelia')
                                                        <a href="{{ route('dagulir.detailjawaban', $item->id) }}" class="w-full cursor-pointer review-pincab">
                                                            <li class="item-tb-dropdown">
                                                                Review
                                                            </li>
                                                        </a>
                                                        <a href="#" class="w-full cursor-pointer">
                                                            <li class="item-tb-dropdown kembalikan-modal" cursor-pointer data-id="{{ $item->id }}" data-backto="staf" >
                                                                Kembalikan ke Staff
                                                            </li>
                                                        </a>
                                                        @if ($userPBO)
                                                             <a class="w-full cursor-pointer" href="{{ route('pengajuan.check.pincab', $item->id_pengajuan) }}?to=pbo">
                                                                <li class="item-tb-dropdown">
                                                                    Lanjutkan ke PBO
                                                                </li>
                                                            </a>
                                                        @else
                                                            @if ($userPBP)
                                                                 <a class="w-full cursor-pointer" href="{{ route('pengajuan.check.pincab', $item->id_pengajuan) }}?to=pbb">
                                                                    <li class="item-tb-dropdown">
                                                                        Lanjutkan ke PBP
                                                                    </li>
                                                                </a>
                                                            @else
                                                                @if ($item->posisi == 'Review Penyelia' && $item->tanggal_review_penyelia)
                                                                    <a href="javascript:void(0)" id="modalConfirmPincab" data-id_pengajuan="{{$item->id}}" data-nama="{{$item->nama}}" class="w-full cursor-pointer">
                                                                        <li class="item-tb-dropdown">
                                                                            Lanjutkan Ke Pincab
                                                                        </li>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        @endif
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
                                                            <li class="item-tb-dropdown kembalikan-modal" cursor-pointer data-id="{{ $item->id }}" data-backto="penyelia">
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
                                                        <a href="{{ route('dagulir.detailjawaban', $item->id) }}" class="w-full cursor-pointer review-pincab">
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
                                                        <a href="{{ route('dagulir.detailjawaban_pincab', $item->id) }}"
                                                            class="w-full cursor-pointer review-pincab">
                                                            <li class="item-tb-dropdown">
                                                                Review
                                                            </li>
                                                        </a>
                                                        <a href="#" class="w-full cursor-pointer">
                                                            <li class="item-tb-dropdown kembalikan-modal" cursor-pointer
                                                                data-id="{{ $item->id }}" data-backto="{{$item->id_pbp ? 'pbp' : 'penyelia'}}">
                                                                Kembalikan ke {{$item->id_pbp ? 'PBP' : 'Penyelia'}}
                                                            </li>
                                                        </a>
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
                    <div class="flex justify-between">
                        <div class="mt-3 mr-5 text-sm font-medium text-gray-500">
                            <p>
                                Menampilkan
                                {{ $data_pengajuan->firstItem() }}
                                -
                                {{ $data_pengajuan->lastItem() }}
                                dari
                                {{ $data_pengajuan->total() }}
                                Data
                            </p>
                        </div>
                        {{ $data_pengajuan  ->links('pagination::tailwind') }}
                        {{-- <div class="pagination">
                            <a href="" class="item-pg item-pg-prev">
                                Prev
                            </a>
                            <a href="#" class="item-pg active-pg">
                                1
                            </a>
                            <a href="#" class="item-pg">
                                2
                            </a>
                            <a href="#" class="item-pg">
                                3
                            </a>
                            <a href="#" class="item-pg">
                                4
                            </a>
                            <a href="#" class="item-pg of-the-data">
                                of 100
                            </a>
                            <a href="" class="item-pg item-pg-next">
                                Next
                            </a>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script-inject')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $('#download-pk').on('click',function(e) {
        // Refresh the page after a delay (adjust as needed)
        setTimeout(function() {
                location.reload();
            }, 2000);
    })
    $('#download-sppk').on('click',function(e) {
        // Refresh the page after a delay (adjust as needed)
        setTimeout(function() {
                location.reload();
            }, 2000);
    })
    if (document.getElementById('modalConfirmPincab')) {
        document.getElementById('modalConfirmPincab').addEventListener('click', function () {
            document.getElementById('confirmationModal').classList.remove('hidden');
            document.getElementById('confirmationModal').classList.add('h-full');
            var nama = $('#modalConfirmPincab').data('nama');
            var namaHtml = nama.toLowerCase();
            var idPengajuan = $('#modalConfirmPincab').data('id_pengajuan');
            console.log(idPengajuan);
            $('#nama_pengajuan').html(namaHtml.toUpperCase());
            $('[name="id_pengajuan"]').val(idPengajuan);
        });
    }

    if (document.getElementById('cancelAction')) {
        document.getElementById('cancelAction').addEventListener('click', function () {
            document.getElementById('confirmationModal').classList.add('hidden');
            document.getElementById('confirmationModal').classList.remove('flex');
        });
    }

    $(document).on('click', '.kembalikan-modal', function() {
        const id = $(this).data('id')
        const backto = $(this).data('backto')

        // Show modal
        $('#modal-kembalikan').removeClass('hidden')

        // Set form variables
        $('#modal-kembalikan #id_pengajuan').val(id)
        $('#modal-kembalikan #backto').val(backto)
    })

    $('.review-penyelia').on('click', function(){
        $("#preload-data").removeClass("hidden");
    })
    $('.review-pincab').on('click', function(){
        $("#preload-data").removeClass("hidden");
    })
    $('.submit-confirmation-modal').on('click', function(){
        $("#preload-data").removeClass("hidden");
    })
    $('.submit-confirmation-modal-staff').on('click', function(){
        $("#preload-data").removeClass("hidden");
    })
    $('.edit-pengajuan').on('click', function(){
        $("#preload-data").removeClass("hidden");
    })

    $('.btn-reset').on('click', function(){
        $("#preload-data").removeClass("hidden");
    })

    $(`#modal-filter .btn-submit`).on('click', function () {
        $("#preload-data").removeClass("hidden");
        $('#form-filter').submit();
    })

    $('.show-upload-sppk').on('click', function() {
        const target = $(this).data('target')
        const id = $(this).data('id')
        const url_form = "{{url('/post-file')}}/"+id
        const url_cetak = "{{url('/pengajuan-dagulir/cetak-sppk')}}/"+id
        var token = generateCsrfToken()

        $(`#${target} #form-sppk`).attr('action', url_form)
        $(`#${target} #token`).val(token)
        $(`#${target} #btn-cetak-file`).attr('href', url_cetak)
        $(`#${target}`).removeClass('hidden');
    })
    $('.show-upload-po').on('click', function() {
        const target = $(this).data('target')
        const id = $(this).data('id')
        const url_form = "{{url('/post-file')}}/"+id
        const url_cetak = "{{url('/cetak-po')}}/"+id
        var token = generateCsrfToken()

        $(`#${target} #form-po`).attr('action', url_form)
        $(`#${target} #token`).val(token)
        $(`#${target} #btn-cetak-file`).attr('href', url_cetak)
        $(`#${target}`).removeClass('hidden');
    })

    $('.simpan-sppk').on('click', function(){
        $("#uploadSPPKModal").addClass("hidden");
        $("#preload-data").removeClass("hidden");
    })
    $('.simpan-pk').on('click', function(){
        $("#uploadPKModal").addClass("hidden");
        $("#preload-data").removeClass("hidden");
    })
    $('.simpan-po').on('click', function(){
        $("#uploadPOModal").addClass("hidden");
        $("#preload-data").removeClass("hidden");
    })

    $('.show-upload-pk').on('click', function() {
        const target = $(this).data('target')
        const id = $(this).data('id')
        const kode_pendaftaran = $(this).data('kode_pendaftaran')
        const skema = $(this).data('skema')
        const url_form = "{{url('/post-file')}}/"+id
        const url_cetak = "{{url('/pengajuan-dagulir/cetak-pk')}}/"+id
        var token = generateCsrfToken()

        if (skema == 'Dagulir') {
            $(`#${target} #div_no_loan`).removeClass('hidden')
        } else {
            $(`#${target} #div_no_loan`).addClass('hidden')
        }

        $(`#${target} #form-pk`).attr('action', url_form)
        $(`#${target} #token`).val(token)
        $(`#${target} #btn-cetak-file`).attr('href', url_cetak)
        $(`#${target} #kode_pendaftaran`).val(kode_pendaftaran)
        $(`#${target} #skema`).val(skema)
        $(`#${target}`).removeClass('hidden');
    })
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

    $('#btn-filter').on('click', function (e) {
        e.preventDefault()
        let tAwal = document.getElementById('tAwal');
        let tAkhir = document.getElementById('tAkhir');
        console.log(tAkhir.value);
        if ($("#form-filter")[0].checkValidity()){
            $('#form-filter').submit()
        }else{
            if(tAwal.value == "" && tAkhir.value == ""){
                $('#form-filter').submit()
            }else if(tAwal.value == ""){
                $("#reqAwal").show();
            }else if(tAkhir.value == ""){
                $("#reqAkhir").show();
            }else{
                $("#reqAkhir").hide();
                $("#reqAwal").hide();
            }
        }
    })

    $("#tAwal").on("change", function() {
        var result = $(this).val();
        if (result != null) {
            $("#tAkhir").prop("required", true)
        }
    });

    var token = "gTWx1U1bVhtz9h51cRNoiluuBfsHqty5MCdXRdmWthFDo9RMhHgHIwrU9DBFVaNj";
    var cbgValue = '{{ Request()->query('cbg') }}';

    $(document).ready(function() {
        $("#errorAkhir").hide();

        $.ajax({
            type: "GET",
            url: "/api/v1/get-cabang",
            headers: {
                'token': token,
            },
            success: function (response) {
                response.data.forEach(element => {
                    $('#cabang').append(
                        `<option value="${element.kode_cabang}" ${cbgValue == element.kode_cabang ? 'selected' : ''}>${element.cabang}</option>`
                    );
                });
            }
        });
    })

    $("#tAkhir").on("change", function() {
        var tAkhir = $(this).val();
        var tAwal = $("#tAwal").val();
        if (Date.parse(tAkhir) < Date.parse(tAwal)) {
            $("#tAkhir").val('');
            $("#errorAkhir").show();
        } else {
            $("#errorAkhir").hide();
        }
    })

    $(".confirmationModalPenyelia").on("click", function(){
        var target = $(this).data('target');
        var id_pengajuan = $(this).data('id-pengajuan');
        var nama = $(this).data('nama');
        var id_penyelia = $(this).data('id-penyelia');
        console.log(target);
        $(`${target} #id-pengajuan`).val(id_pengajuan)
        $(`${target} #id-penyelia`).val(id_penyelia)
        $(`${target} #nama`).html(nama)
        $(target).removeClass('hidden');
    })
    </script>
@endpush
