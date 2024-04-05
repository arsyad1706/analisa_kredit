@extends('layouts.template')
@section('content')
    @push('extraStyle')
        <style>
            .text-sm span {
                font-size: 14px;
                margin: 0;
            }
            .text-color-soft {
                color: rgba(0, 0, 0, 0.3);
            }
        </style>
    @endpush
    @include('components.notification')
    <div class="row justify-content-between">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <a href="{{ $btnLink }}" class="btn btn-primary px-3"><i class="fa fa-plus-circle"></i>
                        {{ $btnText }}</a>
                    <a href="{{ route('pengajuan-kredit-draft') }}">
                        <button type="button" class="btn btn-default"><i class="fa fa-list-alt"></i> Draft</button>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <form action="" class="d-inline-flex">
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
                <input required type="search" value="{{ Request()->query('search') }}" name="search"
                    class="form-control mb-2" placeholder="Cari Nama Nasabah" value="" required>
                <button type="submit" class="btn btn-sm btn-primary mb-2 ml-2">
                    <i class="fa fa-search"></i> Cari
                </button>
                <button type="button" class="btn btn-sm btn-primary mb-2 ml-2" data-toggle="modal"
                    data-target="#exampleModal">
                    <i class="fa fa-filter"></i> Filter
                </button>
                @if (Request()->query() != null)
                    <a href="{{ url()->current() }}" class="btn btn-warning mb-2 ml-2"><i class="fa fa-undolar"></i> Reset
                        Filter</a>
                @endif
            </form>
            {{-- <div class="row d-flex justify-content-end">
                @if (Request()->query() == null)
                    <div class="col-sm-4 d-inline-flex">
                    @else
                        <div class="col-sm-5 d-inline-flex">
                @endif
                <form action="" class="d-inline-flex">
                    <input required type="search" value="{{ Request()->query('search') }}" name="search"
                        class="form-control mb-2" placeholder="Cari Nama Nasabah" value="" required>
                    <button type="submit" class="btn btn-sm btn-primary mb-2 ml-2">
                        <i class="fa fa-search"></i> Cari
                    </button>
                </form>
                <button type="button" class="btn btn-sm btn-primary mb-2 ml-2" data-toggle="modal"
                    data-target="#exampleModal">
                    <i class="fa fa-filter"></i> Filter
                </button>
                @if (Request()->query() != null)
                    <a href="{{ url()->current() }}" class="btn btn-warning mb-2 ml-2"><i class="fa fa-undolar"></i> Reset
                        Filter</a>
                @endif
            </div>
        </div>
    </div> --}}
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-custom">
            <thead>
                <tr class="table-primary">
                    <th class="text-center">#</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Nama Nasabah</th>
                    <th class="text-center">Posisi</th>
                    <th>Durasi</th>
                    <th>Skor</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
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
                                <span>Selesai</span>
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
                                        <font class="text-success">{{ $res . ' hari' }}</font>
                                    @elseif ($res == 4 || $res == 5 || $res == 6)
                                        <font class="text-warning">{{ $res . ' hari' }}</font>
                                    @else
                                        <font class="text-danger">{{ $res . ' hari' }}</font>
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
                                <font class="text-success">
                                    {{ $avgResult }}
                                </font>
                            @elseif ($status == 'kuning')
                                <font class="text-warning">
                                    {{ $avgResult }}
                                </font>
                            @elseif ($status == 'merah')
                                <font class="text-danger">
                                    {{ $avgResult }}
                                </font>
                            @else
                                <font class="text-secondary">
                                    {{ $avgResult }}
                                </font>
                            @endif
                        </td>
                        <td>
                            @if ($item->posisi == 'Selesai')
                                <font class="text-success">Selesai</font>
                            @elseif ($item->posisi == 'Ditolak')
                                <font class="text-success">Ditolak</font>
                            @else
                                <font class="text-warning">On Progress</font>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex">
                                <div class="btn-group">
                                    <button type="button" data-toggle="dropdown" class="btn btn-link">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16"
                                            style="color: black">
                                            <path
                                                d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if ($item->posisi == 'Proses Input Data')
                                            <a href="{{ route('pengajuan-kredit.edit', $item->id_pengajuan) }}"
                                                class="dropdown-item">
                                                Edit data
                                            </a>
                                            <a href="#"
                                                onclick="showTindakLanjut({{ $item->id_pengajuan }},'penyelia kredit')"
                                                class="dropdown-item">Tindak lanjut Review Penyelia</a>
                                            <a target="_blank" href="{{ route('cetak', $item->id_pengajuan) }}"
                                                class="dropdown-item">Cetak</a>
                                            @if ($item->skema_kredit == 'KKB')
                                                <a target="_blank" href="{{ route('cetak-sppk', $item->id_pengajuan) }}"
                                                    class="dropdown-item">Cetak SPPK</a>
                                                <a target="_blank" href="{{ route('cetak-po', $item->id_pengajuan) }}"
                                                    class="dropdown-item">Cetak PO</a>
                                                <a target="_blank" href="{{ route('cetak-pk', $item->id_pengajuan) }}"
                                                    class="dropdown-item">Cetak PK</a>
                                            @endif
                                        @else
                                            <a target="_blank" href="{{ route('cetak', $item->id_pengajuan) }}"
                                                class="dropdown-item">Cetak</a>
                                            @if ($item->posisi == 'Selesai')
                                                @php
                                                    $tglCetak = DB::table('log_cetak')
                                                        ->where('id_pengajuan', $item->id_pengajuan)
                                                        ->first();
                                                @endphp
                                                @if ($tglCetak?->tgl_cetak_sppk == null || $tglCetak == null)
                                                    <a target="_blank"
                                                        href="{{ route('cetak-sppk', $item->id_pengajuan) }}"
                                                        class="dropdown-item">Cetak SPPK</a>
                                                @elseif($tglCetak?->tgl_cetak_sppk != null && $item->sppk == null)
                                                    <a href="#" class="dropdown-item" data-toggle="modal"
                                                        data-id="{{ $item->id_pengajuan }}"
                                                        data-target="#uploadSPPKModal-{{ $item->id_pengajuan }}">Upload
                                                        File SPPK</a>
                                                @endif

                                                @if ($item->skema_kredit == 'KKB')
                                                    @if ($item->sppk != null && $tglCetak?->tgl_cetak_sppk != null && $tglCetak?->tgl_cetak_po == null)
                                                        <a target="_blank"
                                                            href="{{ route('cetak-po', $item->id_pengajuan) }}"
                                                            class="dropdown-item">Cetak PO</a>
                                                    @elseif($item->sppk != null && $tglCetak?->tgl_cetak_po != null && $item->po == null)
                                                        <a href="#" class="dropdown-item" data-toggle="modal"
                                                            data-id="{{ $item->id_pengajuan }}"
                                                            data-target="#uploadPOModal-{{ $item->id_pengajuan }}">Upload File
                                                            PO</a>
                                                    @endif
                                                        @if ($item->po != null && $tglCetak?->tgl_cetak_po != null && $tglCetak?->tgl_cetak_pk == null)
                                                        <a target="_blank"
                                                            href="{{ route('cetak-pk', $item->id_pengajuan) }}"
                                                            class="dropdown-item">Cetak PK</a>
                                                    @elseif($item->po != null && $tglCetak?->tgl_cetak_pk != null && $item->pk == null)
                                                        <a href="#" class="dropdown-item" data-toggle="modal"
                                                            data-id="{{ $item->id_pengajuan }}"
                                                            data-target="#uploadPKModal-{{ $item->id_pengajuan }}">Upload File
                                                            PK</a>
                                                    @endif
                                                @else
                                                    @if ($item->sppk != null && $tglCetak?->tgl_cetak_sppk != null && $tglCetak?->tgl_cetak_pk == null)
                                                        <a target="_blank"
                                                            href="{{ route('cetak-pk', $item->id_pengajuan) }}"
                                                            class="dropdown-item">Cetak PK</a>
                                                    @elseif($item->sppk != null && $tglCetak?->tgl_cetak_pk != null && $item->pk == null)
                                                        <a href="#" class="dropdown-item" data-toggle="modal"
                                                            data-id="{{ $item->id_pengajuan }}"
                                                            data-target="#uploadPKModal-{{ $item->id_pengajuan }}">Upload File
                                                            PK</a>
                                                    @endif
                                                @endif
                                            @endif
                                        @endif
                                    </div>
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
        <div>
            {{ $data_pengajuan->links() }}
            Menampilkan
            {{ $data_pengajuan->firstItem() }}
            -
            {{ $data_pengajuan->lastItem() }}
            dari
            {{ $data_pengajuan->total() }} Data
        </div>
        <div class="pull-right">
        </div>
    </div>
@endsection
@include('pengajuan-kredit.modal-filter')
@include('pengajuan-kredit.modal.pilih-penyelia')
@include('layouts.popup-upload-sppk')
@include('layouts.popup-upload-po')
@include('layouts.popup-upload-pk')
