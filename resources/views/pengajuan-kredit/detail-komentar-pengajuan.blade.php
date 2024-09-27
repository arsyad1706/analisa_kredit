@extends('layouts.template')
@php
// $dataIndex = match ($dataUmum->skema_kredit) {
//     'PKPJ' => 1,
//     'KKB' => 2,
//     'Talangan Umroh' => 1,
//     'Prokesra' => 1,
//     'Kusuma' => 1,
//     null => 1
// };

if ($dataUmum->id_cabang == 1) {
    $roles = [
        'Staf Analis Kredit',
        'Penyelia Kredit',
        'PBO',
        'PBP',
        'Pincab',
    ];
    $idRoles = [
        'id_staf',
        'id_penyelia',
        'id_pbo',
        'id_pbp',
        'id_pincab'
    ];
} else {
    $roles = [
        'Staf Analis Kredit',
        'Penyelia Kredit',
        'Pincab',
    ];
    $idRoles = [
        'id_staf',
        'id_penyelia',
        'id_pincab'
    ];
}

function getKaryawan($nip){
    $host = env('HCS_HOST','https://hcs.bankumkm.id');
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $host . '/api/v1/karyawan/' . $nip,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ]);

    $response = curl_exec($curl);

    curl_close($curl);
    $json = json_decode($response);

    if ($json) {
        if ($json->data)
            return $json->data->nama_karyawan;
    }
}

@endphp
@section('content')
    @include('components.notification')

    <style>
        .sub label:not(.info) {
            font-weight: 400;
        }

        h4 {
            color: #1f1d62;
            font-weight: 600 !important;
            font-size: 20px;
            /* border-bottom: 1px solid #dc3545; */
        }

        h5 {
            color: #1f1d62;
            font-weight: 600 !important;
            font-size: 18px;
            /* border-bottom: 1px solid #dc3545; */
        }

        .form-wizard h6 {
            color: #c2c7cf;
            font-weight: 600 !important;
            font-size: 16px;
            /* border-bottom: 1px solid #dc3545; */
        }

    </style>
    @php
        $userPBO = \App\Models\User::select('id')->where('role', 'PBO')->where('id_cabang', $dataUmum->id_cabang)->first();
    @endphp
    <div class="">
        <form action="{{ route('pengajuan.check.pincab.status.detail.post') }}" method="POST">
            @csrf

            @if (Auth::user()->role == 'SPI' || Auth::user()->role == 'Kredit Umum' || Auth::user()->role == 'Pincab' || auth()->user()->role == 'Direksi')
                @include('pengajuan-kredit.log_pengajuan')
            @endif

            {{--  riwayat pengembalian data  --}}

            {{-- <div class="card mb-3">
                <div class="card-header bg-info color-white font-weight-bold" data-toggle="collapse" href="#cardRiwayatPengembalian">
                    Riwayat Pengembalian Data
                </div>
                <div class="card-body collapse multi-collapse show" id="cardRiwayatPengembalian">
                    <div class="row mb-3">
                        <div class="col-12 p-0">
                            <div class="table-responsive">
                                <table style="width: 100%" class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Alasan Pengembalian</th>
                                            <th>Dari</th>
                                            <th>Ke</th>
                                            <th>Tanggal</th>
                                            <th>User</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($alasanPengembalian as $key => $itemPengembalian)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $itemPengembalian->alasan }}</td>
                                                <td>{{ $itemPengembalian->dari }}</td>
                                                <td>{{ $itemPengembalian->ke }}</td>
                                                <td>{{ date_format($itemPengembalian->created_at, 'd M Y') }}</td>
                                                <td>{{ getKaryawan($itemPengembalian->nip) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">Tidak Ada Riwayat Pengembalian Data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- calon nasabah --}}
            <div class="card mb-3">
                <div class="card-header bg-info color-white font-weight-bold" data-toggle="collapse" href="#cardDataUmum">
                    Data Umum
                </div>
                <div class="card-body collapse multi-collapse show" id="cardDataUmum">
                    @php
                        $dataLevelDua = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable', 'is_hide')
                        ->where('level', 2)
                        ->where('id_parent', $itemSP->id)
                        ->where('nama', 'Surat Permohonan')
                        ->get();
                    @endphp
                    @foreach ($dataLevelDua as $item)
                        @php
                            $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                ->where('jawaban_text.id_jawaban', $item->id)
                                ->get();
                        @endphp
                        @foreach ($dataDetailJawabanText as $itemTextDua)
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">{{ $item->nama }}</label>
                                <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                    <div class="d-flex justify-content-end">
                                        <div style="width: 20px">
                                            :
                                        </div>
                                    </div>
                                </label>
                                <div class="col">
                                    @php
                                        $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text);
                                    @endphp
                                    @if ($file_parts['extension'] == 'pdf')
                                        <iframe src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}" width="100%" height="600px"></iframe>
                                    @else
                                        <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}" alt="" width="600px">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Nama Lengkap</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah->nama }}">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        {{-- alamat rumah --}}
                        <label for="staticEmail" class="col-sm-3 col-form-label">Alamat Rumah</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah->alamat_rumah }}">
                            <p class="form-control-plaintext text-justify">{{ $dataNasabah->alamat_rumah }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        {{-- alamat usaha --}}
                        <label for="staticEmail" class="col-sm-3 col-form-label">Alamat Usaha</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah->alamat_usaha }}">
                            <p class="form-control-plaintext text-justify">{{ $dataNasabah->alamat_usaha }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        {{-- No KTP --}}
                        <label for="staticEmail" class="col-sm-3 col-form-label">No. KTP</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah->no_ktp }}">
                        </div>
                    </div>
                    @php
                        $ktpSuami = \DB::table('jawaban_text')
                                        ->select('id', 'id_jawaban', 'opsi_text')
                                        ->where('id_pengajuan', $dataUmum->id)
                                        ->where('id_jawaban', 150)
                                        ->first();
                        $ktpIstri = \DB::table('jawaban_text')
                                        ->select('id', 'id_jawaban', 'opsi_text')
                                        ->where('id_pengajuan', $dataUmum->id)
                                        ->where('id_jawaban', 151)
                                        ->first();
                        $ktpNasabah = \DB::table('jawaban_text')
                                        ->select('id', 'id_jawaban', 'opsi_text')
                                        ->where('id_pengajuan', $dataUmum->id)
                                        ->where('id_jawaban', 156)
                                        ->first();
                    @endphp
                    @if ($ktpSuami && $ktpIstri)
                    <div class="form-group row">
                        {{-- KTP Suami --}}
                        <label for="staticEmail" class="col-sm-3 col-form-label">KTP Suami</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            @php
                                if ($ktpSuami) {
                                    $path = "../upload/$dataUmum->id/$ktpSuami->id_jawaban/$ktpSuami->opsi_text";
                                }
                            @endphp
                            @if ($ktpSuami)
                                <img src="{{ asset($path) }}" width="100%">
                            @else
                                Tidak ada foto ktp.
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        {{-- KTP Istri --}}
                        <label for="staticEmail" class="col-sm-3 col-form-label">KTP Istri</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            @php

                                if ($ktpIstri) {
                                    $path = "../upload/$dataUmum->id/$ktpIstri->id_jawaban/$ktpIstri->opsi_text";
                                }
                            @endphp
                            @if ($ktpIstri)
                                <img src="{{ asset($path) }}" width="100%">
                            @else
                                Tidak ada foto ktp.
                            @endif
                        </div>
                    </div>
                    @elseif ($ktpNasabah)
                    <div class="form-group row">
                        {{-- KTP Nasabah --}}
                        <label for="staticEmail" class="col-sm-3 col-form-label">KTP Nasabah</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            @php
                                if ($ktpNasabah) {
                                    $path = "../upload/$dataUmum->id/$ktpNasabah->id_jawaban/$ktpNasabah->opsi_text";
                                }
                            @endphp
                            @if ($ktpNasabah)
                                <img src="{{ asset($path) }}" width="100%">
                            @else
                                Tidak ada foto ktp.
                            @endif
                        </div>
                    </div>
                    @endif
                    <hr>
                    <div class="form-group row">
                        {{-- Pekerjaan --}}
                        <label for="staticEmail" class="col-sm-3 col-form-label">Pekerjaan</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah?->pekerjaan ?? '-' }}">
                            <p class="form-control-plaintext text-justify">{{ $dataNasabah?->pekerjaan ?? '-' }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        {{-- Tempat tanggal lahir --}}
                        <label for="staticEmail" class="col-sm-3 col-form-label">Tempat, Tanggal lahir/Status</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col ">
                            <div class="d-flex justify-content-start ">
                                <div class="m-0" style="width: 100%">
                                    <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                        value="{{ $dataNasabah->tempat_lahir . ', ' . date('d-m-Y', strtotime($dataNasabah->tanggal_lahir)) . '/' . $dataNasabah->status}}">
                                </div>
                            </div>

                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Sektor Kredit</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah->sektor_kredit }}">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label for="slik" class="col-sm-3 col-form-label">SLIK</label>
                        <label for="slik" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                @if ($itemSlik != null)
                                    value="{{ $itemSlik?->option }}"
                                @else
                                    value="-"
                                @endif
                            >
                        </div>
                    </div>
                    @php
                        if ($itemSlik != null) {
                            $komentarSlikPenyelia = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', '=', 'detail_komentar.id_komentar')
                                ->where('id_pengajuan', $dataUmum->id)
                                ->where('id_item', $itemSlik->id_item)
                                ->where('id_user', $comment->id_penyelia)
                                ->first();
                            $komentarSlikPBO = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', '=', 'detail_komentar.id_komentar')
                                ->where('id_pengajuan', $dataUmum->id)
                                ->where('id_item', $itemSlik->id_item)
                                ->where('id_user', $comment->id_pbo)
                                ->first();
                            if ($dataUmum->id_cabang == 1) {
                                $komentarSlikPBP = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', '=', 'detail_komentar.id_komentar')
                                    ->where('id_pengajuan', $dataUmum->id)
                                    ->where('id_item', $itemSlik->id_item)
                                    ->where('id_user', $comment->id_pbp)
                                    ->first();
                            }
                        }
                    @endphp
                        <div class="row form-group sub pl-4">
                            <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px"></div>
                                </div>
                            </label>
                            <div class="col">
                                <div class="form-group row">
                                    <label for="slik" class="col-sm-4 col-form-label">SKOR</label>
                                    <label for="slik" class="col-sm-1 col-form-label px-0">
                                        <div class="d-flex justify-content-end">
                                            <div style="width: 20px">
                                                :
                                            </div>
                                        </div>
                                    </label>
                                    <div class="col">
                                        <p class="badge badge-info text-lg"><b>
                                                {{ ($itemSlik != null) ? $itemSlik->skor_penyelia != null ? $itemSlik->skor_penyelia : $itemSlik->skor : '-' }}
                                            </b>
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="slik" class="col-sm-4 col-form-label">Komentar Penyelia</label>
                                    <label for="slik" class="col-sm-1 col-form-label px-0">
                                        <div class="d-flex justify-content-end">
                                            <div style="width: 20px">
                                                :
                                            </div>
                                        </div>
                                    </label>
                                    <div class="col">
                                        <h6 class="font-italic">{{ ($itemSlik != null) ? $komentarSlikPenyelia?->komentar : '-' }}</h6>
                                    </div>
                                </div>
                                {{-- <div class="d-flex">
                                    <div class="">
                                        <p><strong>Skor : </strong></p>
                                    </div>
                                    <div class="px-2">
                                        <p class="badge badge-info text-lg"><b>
                                                {{ ($itemSlik != null) ? $itemSlik->skor_penyelia != null ? $itemSlik->skor_penyelia : $itemSlik->skor : '-' }}
                                            </b>
                                        </p>
                                    </div>
                                </div> --}}

                            </div>
                        </div>
                        {{-- <div class="row form-group sub pl-4">
                            <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">

                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                <div class="d-flex">
                                    <div style="width: 30%">
                                        <p class="p-0 m-0"><strong>Komentar Penyelia : </strong></p>
                                    </div>
                                    <h6 class="font-italic">{{ ($itemSlik != null) ? $komentarSlikPenyelia?->komentar : '-' }}</h6>
                                    <input type="text" readonly class="form-control-plaintext font-italic" id="komentar" value="{{ $itemKomentar->komentar }}">
                                </div>
                            </div>
                        </div> --}}
                        @if ($userPBO)
                        <div class="row form-group sub pl-4">
                            <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">

                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                <div class="d-flex">
                                    <div style="width: 30%">
                                        <p class="p-0 m-0"><strong>Komentar PBO : </strong></p>
                                    </div>
                                    <h6 class="font-italic">{{ $itemSlik != null ? $komentarSlikPBO?->komentar : '-' }}</h6>
                                    {{-- <input type="text" readonly class="form-control-plaintext font-italic" id="komentar" value="{{ $itemKomentar->komentar }}"> --}}

                                </div>
                            </div>
                        </div>
                        @endif
                        @if ($dataUmum->id_cabang == 1)
                            <div class="row form-group sub pl-4">
                                <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                    <div class="d-flex justify-content-end">
                                        <div style="width: 20px">

                                        </div>
                                    </div>
                                </label>
                                <div class="col pt--3">
                                    <div class="form-group row">
                                        <label for="slik" class="col-sm-4 col-form-label">Komentar PBP</label>
                                        <label for="slik" class="col-sm-1 col-form-label px-0">
                                            <div class="d-flex justify-content-end">
                                                <div style="width: 20px">
                                                    :
                                                </div>
                                            </div>
                                        </label>
                                        <div class="col">
                                            <h6 class="font-italic">{{ $itemSlik ? $komentarSlikPBP?->komentar : '-' }}</h6>
                                        </div>
                                    </div>
                                    {{-- <div class="d-flex">
                                        <div style="width: 30%">
                                            <p class="p-0 m-0"><strong>Komentar PBP : </strong></p>
                                        </div>
                                        <h6 class="font-italic">{{ $itemSlik ? $komentarSlikPBP?->komentar : '-' }}</h6>
                                        <input type="text" readonly class="form-control-plaintext font-italic" id="komentar" value="{{ $itemKomentar->komentar }}">

                                    </div> --}}
                                </div>
                            </div>
                        @endif
                        <hr>
                    @php
                        $dataLaporanSLIK = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable', 'is_hide')
                        ->where('level', 2)
                        ->where('id_parent', $itemSP->id)
                        ->where('nama', 'Laporan SLIK')
                        ->get();
                    @endphp
                    @foreach ($dataLaporanSLIK as $item)
                        @php
                            $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                ->where('jawaban_text.id_jawaban', $item->id)
                                ->get();
                        @endphp
                        @foreach ($dataDetailJawabanText as $itemTextDua)
                            @if (!$item->is_hide)
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">{{ $item->nama }}</label>
                                <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                    <div class="d-flex justify-content-end">
                                        <div style="width: 20px">
                                            :
                                        </div>
                                    </div>
                                </label>
                                <div class="col">
                                    @php
                                        $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text);
                                    @endphp
                                    @if ($file_parts['extension'] == 'pdf')
                                        <iframe src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}" width="100%" height="700px"></iframe>
                                    @else
                                        <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}" alt="" width="700px">
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @endforeach

                    <hr>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Jenis Usaha</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah->jenis_usaha }}">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Jumlah Kredit yang diminta </label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                value="Rp.{{ number_format($dataNasabah->jumlah_kredit, 2, '.', ',') }}">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Tenor yang diminta </label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{$dataNasabah->tenor_yang_diminta}} Bulan">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Tujuan Kredit</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah->tujuan_kredit }}">
                            <p class="form-control-plaintext text-justify">{{ $dataNasabah->tujuan_kredit }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Jaminan yang disediakan</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah->jaminan_kredit }}">
                            <p class="form-control-plaintext text-justify">{{ $dataNasabah->jaminan_kredit }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Hubungan dengan Bank</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah->hubungan_bank }}">
                            <p class="form-control-plaintext text-justify">{{ $dataNasabah->hubungan_bank }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-3 col-form-label">Hasil Verifikasi Karakter Umum</label>
                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                            <div class="d-flex justify-content-end">
                                <div style="width: 20px">
                                    :
                                </div>
                            </div>
                        </label>
                        <div class="col">
                            <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                value="{{ $dataNasabah->verifikasi_umum }}">
                            <p class="form-control-plaintext text-justify">{{ $dataNasabah->verifikasi_umum }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($dataUmum->skema_kredit == 'KKB')
                <div class="card mb-3">
                    <div class="card-header bg-info font-weight-bold color-white" data-toggle="collapse" href="#cardDataPO">
                        Data PO
                    </div>
                    <div class="card-body collapse multi-collapse" id="cardDataPO">
                        <p class="fs-6">Jenis Kendaraan Roda 2 : </p>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Merk/Type</label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">
                                        :
                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                    value="{{ $dataPO?->merk }} {{ $dataPO?->tipe }}">
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Tahun</label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">
                                        :
                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                    value="{{ $dataPO?->tahun_kendaraan }}">
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Warna</label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">
                                        :
                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                    value="{{ $dataPO?->warna }}">
                            </div>
                        </div>
                        {{-- <hr> --}}
                        <p class="fs-6">Keterangan : </p>
                        <div class="form-group row">
                            @php
                                $keterangan = $dataPO?->keterangan;
                                $pemesanan = str_replace("Pemesanan ", "", $keterangan);
                            @endphp
                            <label for="staticEmail" class="col-sm-3 col-form-label">Pemesanan</label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">
                                        :
                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                    value="{{ $pemesanan }}">
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Sejumlah</label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">
                                        :
                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                    value="{{ $dataPO?->jumlah }}">
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Harga</label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">
                                        :
                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                    value="Rp.{{ number_format($dataPO?->harga, 2, '.', ',') }}">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            {{-- aspek management --}}
            @foreach ($dataAspek as $itemAspek)
                @php
                    // check level 2
                    $dataLevelDua = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable')
                        ->where('level', 2)
                        ->where('id_parent', $itemAspek->id)
                        ->get();
                    // check level 4
                    $dataLevelEmpat = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable')
                        ->where('level', 4)
                        ->where('id_parent', $itemAspek->id)
                        ->get();
                @endphp
                <div class="card mb-3">
                    <div class="card-header bg-info font-weight-bold color-white" data-toggle="collapse"
                        href="#cardData{{ $loop->iteration }}">
                        {{ $itemAspek->nama }}
                    </div>
                    <div class="card-body collapse multi-collapse" id="cardData{{ $loop->iteration }}">
                        @foreach ($dataLevelDua as $item)
                            @if ($item->opsi_jawaban != 'option')
                                @php
                                    $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama', 'item.status_skor', 'item.is_commentable')
                                        ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                        ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                        ->where('jawaban_text.id_jawaban', $item->id)
                                        ->get();
                                @endphp
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    @php
                                        $getKomentar = \App\Models\DetailKomentarModel::select('detail_komentar.id', 'detail_komentar.id_komentar', 'detail_komentar.id_user', 'detail_komentar.id_item', 'detail_komentar.komentar')
                                            ->where('detail_komentar.id_item', $itemTextDua->id_item)
                                            ->get();
                                    @endphp

                                    @if ($itemTextDua->opsi_text != "tidak_ada_legalitas_usaha")
                                        <div class="row form-group sub pl-4">
                                            <label for="staticEmail"
                                                class="col-sm-3 col-form-label font-weight-bold">{{ $item->nama }}</label>
                                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                <div class="d-flex justify-content-end">
                                                    <div style="width: 20px">
                                                        :
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="col">
                                                @if ($item->opsi_jawaban == 'file')
                                                <br>
                                                    @php
                                                        $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text);
                                                    @endphp
                                                    @if ($file_parts['extension'] == 'pdf')
                                                        <iframe src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}" width="100%" height="700px"></iframe>
                                                    @else
                                                        <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}" alt="" width="700px">
                                                    @endif
                                                    @elseif ($item->opsi_jawaban == 'number' && $item->id != 143)
                                                    <p class="badge badge-info text-lg"><b>
                                                            Rp. {{ number_format((int) $itemTextDua->opsi_text, 2, ',', '.') }}
                                                        </b></p>
                                                @else
                                                    @if (is_numeric($itemJawaban->option) && strlen($itemJawaban->option) > 3)
                                                        {{--  <input type="text" readonly
                                                            class="form-control-plaintext font-weight-bold" id="staticEmail"
                                                            value="{{ $itemTextDua->opsi_text }}">  --}}
                                                        <input type="hidden" name="id[]" value="{{ $itemAspek->id }} {{$itemTiga->opsi_jawaban == 'persen' ? '%' : ''}} {{$item->opsi_jawaban == 'persen' ? '%' : ''}}">
                                                        <input type="hidden" class="form-control-plaintext" id="staticEmail"
                                                        value="{{ $itemTextDua->opsi_text }}">
                                                        <p class="form-control-plaintext text-justify">{{ $itemTextDua->opsi_text }}</p>
                                                    @else
                                                        <input type="text" readonly class="form-control-plaintext font-weight-bold"
                                                            id="staticEmail" value="{{ $itemTextDua->opsi_text }} {{$itemTiga->opsi_jawaban == 'persen' ? '%' : ''}} {{$item->opsi_jawaban == 'persen' ? '%' : ''}}">
                                                        <input type="hidden" name="id[]" value="{{ $itemAspek->id }}">
                                                        {{-- <p class="form-control-plaintext text-justify">{{ $itemTextDua->opsi_text }} {{$itemTiga->opsi_jawaban == 'persen' ? '%' : ''}} {{$item->opsi_jawaban == 'persen' ? '%' : ''}}</p> --}}
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    @if ($itemTextDua->status_skor == 1)
                                        <div class="p-3">
                                            <div class="row form-group sub pl-4">
                                                <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                    <div class="d-flex justify-content-end">
                                                        <div style="width: 20px">

                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <label for="slik" class="col-sm-4 col-form-label">Skor</label>
                                                        <label for="slik" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">
                                                                    :
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <p class="badge badge-info text-lg"><b>
                                                            {{ $itemTextDua->skor_penyelia }}</b></p>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="d-flex">
                                                        <div class="">
                                                            <p><strong>Skor : </strong></p>
                                                        </div>
                                                        <div class="px-2">


                                                            <p class="badge badge-info text-lg"><b>
                                                                    {{ $itemTextDua->skor_penyelia }}</b></p>

                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            @if ($itemTextDua->is_commentable != null)
                                                @foreach ($getKomentar as $itemKomentar)
                                                    <div class="row form-group sub pl-4">
                                                        <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">

                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <div class="d-flex">
                                                                <div style="width: 15%">
                                                                    <p class="p-0 m-0"><strong>Komentar : </strong>
                                                                    </p>
                                                                </div>
                                                                <h6 class="font-italic">{{ $itemKomentar->komentar ?? '' }}
                                                                </h6>
                                                                {{-- <input type="text" readonly class="form-control-plaintext font-italic" id="komentar" value="{{ $itemKomentar->komentar }}"> --}}

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                    @if ($item->nama == 'Repayment Capacity')
                                    @else
                                        @if ($itemTextDua->opsi_text != "tidak_ada_legalitas_usaha")
                                            <hr>
                                        @endif
                                    @endif
                                @endforeach
                                @if ($item->nama == 'Ijin Usaha' && $countIjin == 0)
                                        <div class="row form-group sub pl-4">
                                            <label for="staticEmail"
                                                class="col-sm-3 col-form-label font-weight-bold">Ijin Usaha</label>
                                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                <div class="d-flex justify-content-end">
                                                    <div style="width: 20px">
                                                        :
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="col">
                                                <input type="text" readonly
                                                    class="form-control-plaintext font-weight-bold" id="staticEmail"
                                                    value="Tidak ada legalitas usaha">
                                            </div>
                                        </div>
                                    @endif
                            @endif
                            @php
                                $dataJawaban = \App\Models\OptionModel::where('option', '!=', '-')
                                    ->where('id_item', $item->id)
                                    ->get();
                                $dataOption = \App\Models\OptionModel::where('option', '=', '-')
                                    ->where('id_item', $item->id)
                                    ->get();

                                // check level 3
                                $dataLevelTiga = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'is_hide')
                                    ->where('level', 3)
                                    ->where('id_parent', $item->id)
                                    ->get();
                            @endphp
                            @if ($item->id_parent == 10 && $item->nama != 'Hubungan Dengan Supplier')
                                <div class="row form-group sub pl-4">
                                    <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">{{ $item->nama }}</label>
                                    <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                </div>
                                <hr>
                            @endif
                            @if (count($dataJawaban) != 0)
                                @if ($item->nama == 'Persentase Kebutuhan Kredit Opsi' || $item->nama == 'Repayment Capacity Opsi')

                                @else
                                    <div class="row form-group sub pl-4">
                                        <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">{{ $item->nama }}</label>
                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                            <div class="d-flex justify-content-end">
                                                <div style="width: 20px">
                                                    :
                                                </div>
                                            </div>
                                        </label>
                                        <div class="col">
                                            @foreach ($dataJawaban as $key => $itemJawaban)
                                                @php
                                                    $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                        ->where('id_pengajuan', $dataUmum->id)
                                                        ->get();
                                                    $count = count($dataDetailJawaban);
                                                    for ($i = 0; $i < $count; $i++) {
                                                        $data[] = $dataDetailJawaban[$i]['id_jawaban'];
                                                    }
                                                @endphp
                                                @if (in_array($itemJawaban->id, $data))
                                                    @if (isset($data))
                                                        @if (is_numeric($itemJawaban->option) && strlen($itemJawaban->option) > 3)
                                                        <input type="text" readonly
                                                            class="form-control-plaintext font-weight-bold" id="staticEmail"
                                                            value="{{ $itemJawaban->option }}">
                                                        <input type="hidden" name="id[]" value="{{ $itemAspek->id }}">
                                                        @else
                                                        <input type="text" readonly
                                                            class="form-control-plaintext font-weight-bold" id="staticEmail"
                                                            value="{{ $itemJawaban->option }}">
                                                        <input type="hidden" name="id[]" value="{{ $itemAspek->id }}">
                                                        @endif
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if ($item->nama == 'Persentase Kebutuhan Kredit Opsi')

                                @else
                                    <div class="row form-group sub pl-4">
                                        <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                            <div class="d-flex justify-content-end">
                                                <div style="width: 20px">

                                                </div>
                                            </div>
                                        </label>
                                        <div class="col">
                                            @foreach ($dataJawaban as $key => $itemJawaban)
                                                @php
                                                    $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                        ->where('id_pengajuan', $dataUmum->id)
                                                        ->get();
                                                    $getKomentarPenyelia = null;
                                                    $getKomentarPBP = null;
                                                    $count = count($dataDetailJawaban);
                                                    for ($i = 0; $i < $count; $i++) {
                                                        $data[] = $dataDetailJawaban[$i]['id_jawaban'];
                                                    }
                                                @endphp
                                                @if (in_array($itemJawaban->id, $data))
                                                    @if (isset($data))
                                                        @php
                                                            $dataDetailJawabanskor = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                                ->where('id_pengajuan', $dataUmum->id)
                                                                ->where('id_jawaban', $itemJawaban->id)
                                                                ->get();
                                                            $getKomentarPenyelia = \App\Models\DetailKomentarModel::select('detail_komentar.*')
                                                                ->join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                                ->where('detail_komentar.id_komentar', $comment->id)
                                                                ->where('detail_komentar.id_item', $item->id)
                                                                ->where('detail_komentar.id_user', $comment->id_penyelia)
                                                                ->get();
                                                            if ($dataUmum->id_cabang == 1) {
                                                                $getKomentarPBP = \App\Models\DetailKomentarModel::select('detail_komentar.*')
                                                                    ->join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                                    ->where('detail_komentar.id_komentar', $comment->id)
                                                                    ->where('detail_komentar.id_item', $item->id)
                                                                    ->where('detail_komentar.id_user', $comment->id_pbp)
                                                                    ->get();
                                                            }
                                                        @endphp
                                                        @foreach ($dataDetailJawabanskor as $item)
                                                            @if ($item->skor_penyelia != null && $item->skor_penyelia != '')
                                                                <div class="form-group row">
                                                                    <label for="slik" class="col-sm-4 col-form-label">Skor</label>
                                                                    <label for="slik" class="col-sm-1 col-form-label px-0">
                                                                        <div class="d-flex justify-content-end">
                                                                            <div style="width: 20px">
                                                                                :
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                    <div class="col">
                                                                        <p class="badge badge-info text-lg"><b>
                                                                                {{ $item->skor_penyelia }}</b></p>
                                                                    </div>
                                                                </div>
                                                                {{-- <div class="d-flex">
                                                                    <div class="">
                                                                        <p><strong>Skor : </strong></p>
                                                                    </div>
                                                                    <div class="px-2">
                                                                        <p class="badge badge-info text-lg"><b>
                                                                                {{ $item->skor_penyelia }}</b></p>
                                                                    </div>
                                                                </div> --}}
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @if (isset($getKomentarPenyelia))
                                        @foreach ($getKomentarPenyelia as $itemKomentarPenyelia)
                                            <div class="row form-group sub pl-4">
                                                <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                    <div class="d-flex justify-content-end">
                                                        <div style="width: 20px">

                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <label for="slik" class="col-sm-4 col-form-label">Komentar Penyelia</label>
                                                        <label for="slik" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">
                                                                    :
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <h6 class="font-italic">{{ $itemKomentarPenyelia->komentar ?? ''}}</h6>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="d-flex">
                                                        <div style="width: 30%">
                                                            <p class="p-0 m-0"><strong>Komentar Penyelia : </strong></p>
                                                        </div>
                                                        <h6 class="font-italic">{{ $itemKomentarPenyelia->komentar ?? ''}}</h6>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if ($dataUmum->id_cabang == 1 && $getKomentarPBP != null)
                                        @foreach ($getKomentarPBP as $itemKomentarPBP)
                                            <div class="row form-group sub pl-4">
                                                <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                    <div class="d-flex justify-content-end">
                                                        <div style="width: 20px">

                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="col">
                                                    <div class="d-flex">
                                                        <div style="width: 30%">
                                                            <p class="p-0 m-0"><strong>Komentar PBP : </strong></p>
                                                        </div>
                                                        <h6 class="font-italic">{{ $itemKomentarPBP->komentar ?? ''}}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if ($item->nama == 'Persentase Kebutuhan Kredit Opsi')

                                    @else
                                        <hr>
                                    @endif
                                @endif
                            @endif
                            @php
                                $no = 0;
                            @endphp
                            @foreach ($dataLevelTiga as $keyTiga => $itemTiga)
                                @if ($itemTiga->opsi_jawaban != 'option')
                                    @php
                                        $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama', 'item.is_commentable', 'item.status_skor', 'item.opsi_jawaban')
                                            ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                            ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                            ->where('jawaban_text.id_jawaban', $itemTiga->id)
                                            ->get();
                                        $jumlahDataDetailJawabanText = $dataDetailJawabanText ? count($dataDetailJawabanText) : 0;
                                        $getKomentar2 = \App\Models\DetailKomentarModel::select('*')
                                            ->join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                            ->where('detail_komentar.id_item', $itemTiga->id)
                                            ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                            ->get();
                                    @endphp
                                    @foreach ($dataDetailJawabanText as $itemTextTiga)
                                        @php
                                            $getKomentar2 = \App\Models\DetailKomentarModel::select('*')
                                                ->join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                ->where('detail_komentar.id_item', $itemTextTiga->id_item)
                                                ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                                ->get();
                                        @endphp
                                        @if ($itemTextTiga->nama == 'NIB' || $itemTextTiga->nama == 'Surat Keterangan Usaha')
                                            <div class="row form-group sub pl-4">
                                            <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">
                                                @if ($jumlahDataDetailJawabanText > 1)
                                                    {{ $itemTextTiga->nama }} {{$loop->iteration}}
                                                @else
                                                    {{ $itemTextTiga->nama }}
                                                @endif
                                            </label>
                                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                        @else
                                            <div class="row form-group sub pl-5">
                                            <label for="staticEmail" class="col-sm-3 col-form-label">
                                                @if($jumlahDataDetailJawabanText > 1)
                                                    {{ $itemTextTiga->nama }} {{$loop->iteration}}
                                                @else
                                                    {{ $itemTextTiga->nama }}
                                                @endif
                                            </label>
                                            <label for="staticEmail" class="col-sm-1 col-form-label">
                                        @endif
                                                <div class="d-flex justify-content-end">
                                                    <div style="width: 20px">
                                                        :
                                                    </div>
                                                </div>
                                            </label>
                                            @if ($itemTextTiga->nama == 'NIB' || $itemTextTiga->nama == 'Surat Keterangan Usaha')
                                                <div class="col">
                                            @else
                                                <div class="col" style="padding: 0px">
                                            @endif
                                                @if ($itemTextTiga->opsi_jawaban == 'file')
                                                <br>
                                                    @php
                                                        $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $itemTextTiga->id_jawaban . '/' . $itemTextTiga->opsi_text);
                                                    @endphp
                                                    @if ($file_parts['extension'] == 'pdf')
                                                        <iframe src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $itemTextTiga->id_jawaban . '/' . $itemTextTiga->opsi_text }}" width="100%" height="700px"></iframe>
                                                    @else
                                                        <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $itemTextTiga->id_jawaban . '/' . $itemTextTiga->opsi_text }}" alt="" width="700px">
                                                    @endif
                                                {{-- Rupiah data tiga --}}
                                                    @elseif ($itemTiga->opsi_jawaban == 'number')
                                                        <p class="badge badge-info text-lg"><b>
                                                                Rp.
                                                                {{ number_format((int) $itemTextTiga->opsi_text, 2, ',', '.') }}
                                                            </b>
                                                        </p>
                                                    @else
                                                    <input type="text" readonly class="form-control-plaintext font-weight-bold"
                                                        id="staticEmail" value="{{ $itemTextTiga->opsi_text }} {{$itemTiga->opsi_jawaban == 'persen' ? '%' : ''}}">
                                                    <input type="hidden" name="id[]" value="{{ $itemAspek->id }}">
                                                @endif
                                            </div>
                                        </div>
                                        @if ($itemTextTiga->status_skor == 1)
                                            <div class="row form-group sub pl-5">
                                                <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                    <div class="d-flex justify-content-end">
                                                        <div style="width: 20px">

                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <label for="slik" class="col-sm-4 col-form-label">Skor</label>
                                                        <label for="slik" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">
                                                                    :
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <p class="badge badge-info text-lg">
                                                                <b>{{ $itemTextTiga->skor_penyelia }}</b></p>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="d-flex">
                                                        <div class="">
                                                            <p><strong>Skor : </strong></p>
                                                        </div>
                                                        <div class="px-2">
                                                            <p class="badge badge-info text-lg">
                                                                <b>{{ $itemTextTiga->skor_penyelia }}</b></p>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        @endif

                                        @foreach ($getKomentar2 as $itemKomentar2)
                                            <div class="row form-group sub pl-5">
                                                <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                    <div class="d-flex justify-content-end">
                                                        <div style="width: 20px">

                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="col">
                                                    <div class="form-group row">
                                                        <label for="slik" class="col-sm-4 col-form-label">Komentar</label>
                                                        <label for="slik" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">
                                                                    :
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <h6 class="font-italic">{{ $itemKomentar2->komentar ?? '' }}</h6>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="d-flex">
                                                        <div style="width: 15%">
                                                            <p class="p-0 m-0"><strong>Komentar : </strong></p>
                                                        </div>
                                                        <h6 class="font-italic">{{ $itemKomentar2->komentar ?? '' }}</h6>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        @endforeach
                                        @if ($itemTiga->nama == 'Ratio Coverage')

                                        @else
                                            <hr>
                                        @endif
                                    @endforeach
                                @endif
                                @php
                                    // check  jawaban level tiga
                                    $dataJawabanLevelTiga = \App\Models\OptionModel::where('option', '!=', '-')
                                        ->where('id_item', $itemTiga->id)
                                        ->get();
                                    $dataOptionTiga = \App\Models\OptionModel::where('option', '=', '-')
                                        ->where('id_item', $itemTiga->id)
                                        ->get();
                                    // check level empat
                                    $dataLevelEmpat = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'is_hide')
                                        ->where('level', 4)
                                        ->where('id_parent', $itemTiga->id)
                                        ->get();
                                @endphp

                                @if (count($dataJawabanLevelTiga) != 0)
                                    @if (!$itemTiga->is_hide)
                                        @if ($itemTiga->nama == 'Ratio Tenor Asuransi Opsi')

                                        @else
                                            @if ( $itemTiga->nama == 'Ratio Coverage Opsi')

                                            @else
                                                <div class="row form-group sub pl-5">
                                                    <label for="staticEmail"
                                                        class="col-sm-3 col-form-label">{{ $itemTiga->nama }}</label>
                                                    <label for="staticEmail" class="col-sm-1 col-form-label">
                                                        <div class="d-flex justify-content-end">
                                                            <div style="width: 20px">
                                                                :
                                                            </div>
                                                        </div>
                                                    </label>
                                                    <div class="col" style="padding: 0px">
                                                        @foreach ($dataJawabanLevelTiga as $key => $itemJawabanLevelTiga)
                                                            @php
                                                                $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor')
                                                                    ->where('id_pengajuan', $dataUmum->id)
                                                                    ->get();
                                                                $count = count($dataDetailJawaban);
                                                                for ($i = 0; $i < $count; $i++) {
                                                                    $data[] = $dataDetailJawaban[$i]['id_jawaban'];
                                                                }
                                                            @endphp
                                                            @if (in_array($itemJawabanLevelTiga->id, $data))
                                                                @if (isset($data))
                                                                    <input type="text" readonly
                                                                        class="form-control-plaintext font-weight-bold"
                                                                        id="staticEmail" value="{{ $itemJawabanLevelTiga->option }}">
                                                                    <input type="hidden" name="id[]" value="{{ $itemAspek->id }}">
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="row form-group sub pl-4">
                                                <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                    <div class="d-flex justify-content-end">
                                                        <div style="width: 20px">

                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="col">
                                                    @foreach ($dataJawabanLevelTiga as $key => $itemJawabanTiga)
                                                        @php
                                                            $dataDetailJawabanTiga;
                                                            $getKomentarPenyelia3;
                                                            $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                                ->where('id_pengajuan', $dataUmum->id)
                                                                ->get();
                                                            $count = count($dataDetailJawaban);
                                                            for ($i = 0; $i < $count; $i++) {
                                                                $data[] = $dataDetailJawaban[$i]['id_jawaban'];
                                                            }
                                                        @endphp
                                                        @if (in_array($itemJawabanTiga->id, $data))
                                                            @if (isset($data))
                                                                @php
                                                                    $dataDetailJawabanTiga = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                                        ->where('id_pengajuan', $dataUmum->id)
                                                                        ->where('id_jawaban', $itemJawabanTiga->id)
                                                                        ->get();
                                                                    $getKomentarPenyelia3 = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                                        ->where('id_item', $itemJawabanTiga->id_item)
                                                                        ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                                                        ->where('detail_komentar.id_user', $comment->id_penyelia)
                                                                        ->get();
                                                                    $getKomentarPBO3 = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                                        ->where('id_item', $itemJawabanTiga->id_item)
                                                                        ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                                                        ->where('detail_komentar.id_user', $comment->id_pbo)
                                                                        ->first();
                                                                @endphp
                                                                @foreach ($dataDetailJawabanTiga as $item)
                                                                    @if ($item->skor_penyelia != null && $item->skor_penyelia != '')
                                                                        <div class="form-group row">
                                                                            <label for="slik" class="col-sm-4 col-form-label">Skor</label>
                                                                            <label for="slik" class="col-sm-1 col-form-label px-0">
                                                                                <div class="d-flex justify-content-end">
                                                                                    <div style="width: 20px">
                                                                                        :
                                                                                    </div>
                                                                                </div>
                                                                            </label>
                                                                            <div class="col">
                                                                               <p class="badge badge-info text-lg"><b>
                                                                                        {{ $item->skor_penyelia }}</b></p>
                                                                            </div>
                                                                        </div>
                                                                        {{-- <div class="d-flex">
                                                                            <div class="">
                                                                                <p><strong>Skor : </strong></p>
                                                                            </div>
                                                                            <div class="px-2">
                                                                                <p class="badge badge-info text-lg"><b>
                                                                                        {{ $item->skor_penyelia }}</b></p>
                                                                            </div>
                                                                        </div> --}}
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endif
                                                        @php
                                                            if ($dataUmum->id_cabang == 1) {
                                                                $getKomentarPBP3 = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                                    ->where('detail_komentar.id_item', $itemJawabanTiga->id_item)
                                                                    ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                                                    ->where('detail_komentar.id_user', $comment->id_pbp)
                                                                    ->get();
                                                            }
                                                        @endphp
                                                    @endforeach
                                                </div>
                                            </div>
                                            @if (isset($getKomentarPenyelia3))
                                                @foreach ($getKomentarPenyelia3 as $itemKomentar3)
                                                    <div class="row form-group sub pl-4">
                                                        <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">

                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <div class="form-group row">
                                                                <label for="slik" class="col-sm-4 col-form-label">Komentar Penyelia</label>
                                                                <label for="slik" class="col-sm-1 col-form-label px-0">
                                                                    <div class="d-flex justify-content-end">
                                                                        <div style="width: 20px">
                                                                            :
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                                <div class="col">
                                                                    <h6 class="font-italic">{{ $itemKomentar3->komentar ?? '' }}</h6>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="d-flex">
                                                                <div style="width: 30%">
                                                                    <p class="p-0 m-0"><strong>Komentar Penyelia: </strong></p>
                                                                </div>
                                                                <h6 class="font-italic">{{ $itemKomentar3->komentar ?? '' }}</h6>
                                                                <input type="text" readonly class="form-control-plaintext font-italic" id="komentar" value="{{ $itemKomentar->komentar }}">

                                                            </div> --}}
                                                            {{-- <input type="text" readonly class="form-control-plaintext" id="komentar" value="{{ $itemKomentar3->komentar }}"> --}}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            @if ($userPBO)
                                                <div class="row form-group sub pl-4">
                                                    <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                    <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                        <div class="d-flex justify-content-end">
                                                            <div style="width: 20px">

                                                            </div>
                                                        </div>
                                                    </label>
                                                    <div class="col">
                                                        <div class="d-flex">
                                                            <div style="width: 30%">
                                                                <p class="p-0 m-0"><strong>Komentar PBO: </strong></p>
                                                            </div>
                                                            <h6 class="font-italic">{{ $getKomentarPBO3->komentar ?? '' }}</h6>
                                                            {{-- <input type="text" readonly class="form-control-plaintext font-italic" id="komentar" value="{{ $itemKomentar->komentar }}"> --}}

                                                        </div>
                                                        {{-- <input type="text" readonly class="form-control-plaintext" id="komentar" value="{{ $itemKomentar3->komentar }}"> --}}
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($dataUmum->id_cabang == 1 && $getKomentarPBP3 != null)
                                                @foreach ($getKomentarPBP3 as $itemKomentar3)
                                                    <div class="row form-group sub pl-4">
                                                        <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">

                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <div class="d-flex">
                                                                <div style="width: 30%">
                                                                    <p class="p-0 m-0"><strong>Komentar PBP: </strong></p>
                                                                </div>
                                                                <h6 class="font-italic">{{ $itemKomentar3->komentar ?? '' }}</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <hr>
                                        @endif
                                    @endif
                                @endif

                                @foreach ($dataLevelEmpat as $keyEmpat => $itemEmpat)
                                    @if (!$itemEmpat->is_hide)
                                        @if ($itemEmpat->opsi_jawaban != 'option')
                                            @php
                                                $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.opsi_jawaban', 'item.nama', 'item.is_commentable', 'item.status_skor')
                                                    ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                                    ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                                    ->where('jawaban_text.id_jawaban', $itemEmpat->id)
                                                    ->get();
                                            @endphp
                                            @foreach ($dataDetailJawabanText as $itemTextEmpat)
                                                @php
                                                    $getKomentar4 = \App\Models\DetailKomentarModel::select('*')
                                                        ->join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                        ->where('id_item', $itemTextEmpat->id_item)
                                                        ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                                        ->get();
                                                @endphp
                                                @if ($itemEmpat->id_parent == '95')
                                                    <div class="row form-group sub pl-4">" class="col-sm-3 col-form-label font-weight-bold">Jaminan Utama</label>
                                                        {{-- @elseif ($itemEmpat->id_paret == '110')
                                                        <label for="staticEmail" class="col-sm-3 col-form-label">Jaminan Tambahan</label> --}}
                                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                @else
                                                    <div class="row form-group sub pl-5">
                                                        <label for="staticEmail" class="col-sm-3 col-form-label">{{ $itemEmpat->nama }}</label>
                                                        <label for="staticEmail" class="col-sm-1 col-form-label">
                                                @endif
                                                        <div class="d-flex justify-content-end">
                                                            <div style="width: 20px">
                                                                :
                                                            </div>
                                                        </div>
                                                    </label>
                                                    @if ($itemEmpat->id_parent == '95')
                                                        <div class="col">
                                                    @else
                                                        <div class="col" style="padding: 0px">
                                                    @endif
                                                        @if ($itemTextEmpat->opsi_jawaban == 'file')
                                                        <br>
                                                            @php
                                                                $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $itemEmpat->id . '/' . $itemTextEmpat->opsi_text);
                                                                $filepath = "../upload/$dataUmum->id/$itemTextEmpat->id_jawaban/$itemTextEmpat->opsi_text";
                                                                @endphp
                                                            @if ($file_parts['extension'] == 'pdf')
                                                                <iframe src="{{ asset($filepath) }}" width="100%" height="700px"></iframe>
                                                            @else
                                                                <img src="{{ asset($filepath) }}"
                                                                    alt="" width="700px">
                                                            @endif
                                                            {{-- Rupiah data empat --}}
                                                            @elseif ($itemEmpat->opsi_jawaban == 'number' && $itemEmpat->id != 130)
                                                                <p class="badge badge-info text-lg"><b>
                                                                        Rp.
                                                                        {{ number_format((int) $itemTextEmpat->opsi_text, 2, ',', '.') }}
                                                                    </b></p>
                                                            @else
                                                            @if ($itemEmpat->id == 101)
                                                                <input type="text" readonly
                                                                    class="form-control-plaintext font-weight-bold" id="staticEmail"
                                                                    value="{{ $itemEmpat->nama . '       : ' . $itemTextEmpat->opsi_text }} {{$itemEmpat->opsi_jawaban == 'persen' ? '%' : ''}}">
                                                                <input type="hidden" name="id[]" value="{{ $itemAspek->id }}">
                                                            @elseif ($itemEmpat->id == 130)
                                                                <input type="text" readonly
                                                                    class="form-control-plaintext font-weight-bold" id="staticEmail"
                                                                    value="{{$itemTextEmpat->opsi_text.' Bulan'}}">
                                                                <input type="hidden" name="id[]" value="{{ $itemAspek->id }}">
                                                            @else
                                                                <input type="text" readonly
                                                                    class="form-control-plaintext font-weight-bold" id="staticEmail"
                                                                    value="{{ $itemTextEmpat->opsi_text }} {{$itemEmpat->opsi_jawaban == 'persen' ? '%' : ''}}">
                                                                <input type="hidden" name="id[]" value="{{ $itemAspek->id }}">
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                                @if ($itemTextEmpat->status_skor != null && $itemTextEmpat == false)
                                                    <div class="row form-group sub" style="padding-left: 5rem !important">
                                                        <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">
                                                                    :
                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <div class="form-group row">
                                                                <label for="slik" class="col-sm-4 col-form-label">Skor</label>
                                                                <label for="slik" class="col-sm-1 col-form-label px-0">
                                                                    <div class="d-flex justify-content-end">
                                                                        <div style="width: 20px">
                                                                            :
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                                <div class="col">
                                                                    <p class="badge badge-info text-lg"><b>
                                                                            {{ $itemTextEmpat->skor_penyelia }}</b></p>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="d-flex">
                                                                <div class="">
                                                                    <p><strong>Skor : </strong></p>
                                                                </div>
                                                                <div class="px-2">
                                                                    <p class="badge badge-info text-lg"><b>
                                                                            {{ $itemTextEmpat->skor_penyelia }}</b></p>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                @endif

                                                @foreach ($getKomentar4 as $itemKomentar4)
                                                    <div class="row form-group sub" style="padding-left: 5rem !important">
                                                        <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">

                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <div class="d-flex">
                                                                <div style="width: 15%">
                                                                    <p class="p-0 m-0"><strong>Komentar : </strong></p>
                                                                </div>
                                                                <h6 class="font-italic">{{ $itemKomentar4->komentar ?? '' }}
                                                                </h6>
                                                                {{-- <input type="text" readonly class="form-control-plaintext font-italic" id="komentar" value="{{ $itemKomentar->komentar }}"> --}}

                                                            </div>
                                                            {{-- <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="{{ $itemKomentar4->komentar }}"> --}}
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <hr>
                                            @endforeach
                                        @endif
                                        @php
                                            // check level empat
                                            $dataJawabanLevelEmpat = \App\Models\OptionModel::where('option', '!=', '-')
                                                ->where('id_item', $itemEmpat->id)
                                                ->get();

                                            $dataOptionEmpat = \App\Models\OptionModel::where('option', '=', '-')
                                                ->where('id_item', $itemEmpat->id)
                                                ->get();
                                        @endphp
                                        {{-- Data jawaban Level Empat --}}
                                        @if (count($dataJawabanLevelEmpat) != 0)
                                            @php
                                                $dataDetailJawabanTest = \App\Models\JawabanPengajuanModel::select('jawaban.id', 'jawaban.id_pengajuan', 'jawaban.id_jawaban', 'item.id as id_item', 'item.nama', 'item.is_commentable', 'item.status_skor')
                                                    ->join('option', 'option.id', 'jawaban.id_jawaban')
                                                    ->join('item', 'option.id_item', 'item.id')
                                                    ->where('jawaban.id_pengajuan', $dataUmum->id)
                                                    ->where('option.id_item', $itemEmpat->id)
                                                    ->get();
                                            @endphp
                                            @if (!$dataDetailJawabanTest->isEmpty())
                                                <div class="row form-group sub pl-4">
                                                    @if ($itemEmpat->id_parent == '110')
                                                        <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">Jaminan Tambahan</label>
                                                    @elseif ($itemEmpat->id_parent == '95')
                                                        <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">Jaminan Utama</label>
                                                    @else
                                                        <label for="staticEmail" class="col-sm-3 col-form-label">{{ $itemEmpat->nama }}</label>
                                                    @endif
                                                    <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                        <div class="d-flex justify-content-end">
                                                            <div style="width: 20px">
                                                                :
                                                            </div>
                                                        </div>
                                                    </label>
                                                    <div class="col" style="padding: 0px">
                                                        <label for="staticEmail" class="col-sm-4 col-form-label font-weight-bold">{{ $itemEmpat->nama }}</label>
                                                    </div>
                                                </div>
                                                <div class="row form-group sub pl-4">
                                                    <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                    <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                        <div class="d-flex justify-content-end">
                                                            <div style="width: 20px">

                                                            </div>
                                                        </div>
                                                    </label>
                                                    <div class="col">
                                                        @foreach ($dataJawabanLevelEmpat as $key => $itemJawabanLevelEmpat)
                                                            @php
                                                                $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor')
                                                                    ->where('id_pengajuan', $dataUmum->id)
                                                                    ->get();
                                                                $count = count($dataDetailJawaban);
                                                                for ($i = 0; $i < $count; $i++) {
                                                                    $data[] = $dataDetailJawaban[$i]['id_jawaban'];
                                                                }
                                                            @endphp
                                                            @if (in_array($itemJawabanLevelEmpat->id, $data))
                                                                @if (isset($data))
                                                                    <input type="text" readonly
                                                                        class="form-control-plaintext font-weight-bold"
                                                                        id="staticEmail"
                                                                        value="{{ $itemJawabanLevelEmpat->option }}">
                                                                    <input type="hidden" name="id[]"
                                                                        value="{{ $itemAspek->id }}">
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="row form-group sub pl-4">
                                                    <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                    <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                        <div class="d-flex justify-content-end">
                                                            <div style="width: 20px">

                                                            </div>
                                                        </div>
                                                    </label>
                                                    <div class="col">
                                                        @php
                                                            $getKomentar5 = '';
                                                        @endphp
                                                        @foreach ($dataJawabanLevelEmpat as $key => $itemJawabanEmpat)
                                                            @php
                                                                $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                                    ->where('id_pengajuan', $dataUmum->id)
                                                                    ->get();
                                                                $count = count($dataDetailJawaban);
                                                                for ($i = 0; $i < $count; $i++) {
                                                                    $data[] = $dataDetailJawaban[$i]['id_jawaban'];
                                                                }
                                                            @endphp
                                                            @if (in_array($itemJawabanEmpat->id, $data))
                                                                @if (isset($data))
                                                                    @php

                                                                        $dataDetailJawabanEmpat = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                                            ->where('id_pengajuan', $dataUmum->id)
                                                                            ->where('id_jawaban', $itemJawabanEmpat->id)
                                                                            ->get();
                                                                        $getKomentarPenyelia5 = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                                            ->where('detail_komentar.id_item', $itemJawabanEmpat->id_item)
                                                                            ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                                                            ->where('detail_komentar.id_user', $comment->id_penyelia)
                                                                            ->first();
                                                                        $getKomentarPBO5 = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                                            ->where('detail_komentar.id_item', $itemJawabanEmpat->id_item)
                                                                            ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                                                            ->where('detail_komentar.id_user', $comment->id_pbo)
                                                                            ->first();
                                                                        if ($dataUmum->id_cabang == 1) {
                                                                            $getKomentarPBP5 = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                                            ->where('detail_komentar.id_item', $itemJawabanEmpat->id_item)
                                                                            ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                                                            ->where('detail_komentar.id_user', $comment->id_pbp)
                                                                            ->first();
                                                                        }
                                                                    @endphp
                                                                    @foreach ($dataDetailJawabanEmpat as $item)
                                                                        @if ($item->skor_penyelia != null && $item->skor_penyelia != '')
                                                                            <div class="form-group row">
                                                                                <label for="slik" class="col-sm-4 col-form-label">Skor</label>
                                                                                <label for="slik" class="col-sm-1 col-form-label px-0">
                                                                                    <div class="d-flex justify-content-end">
                                                                                        <div style="width: 20px">
                                                                                            :
                                                                                        </div>
                                                                                    </div>
                                                                                </label>
                                                                                <div class="col">
                                                                                    <p class="badge badge-info text-lg"><b>
                                                                                            {{ $item->skor_penyelia }}</b></p>
                                                                                </div>
                                                                            </div>
                                                                            {{-- <div class="d-flex">
                                                                                <div class="">
                                                                                    <p><strong>Skor : </strong></p>
                                                                                </div>
                                                                                <div class="px-2">
                                                                                    <p class="badge badge-info text-lg"><b>
                                                                                            {{ $item->skor_penyelia }}</b>
                                                                                    </p>
                                                                                </div>
                                                                            </div> --}}
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @if ($getKomentarPenyelia5)
                                                    <div class="row form-group sub pl-4">
                                                        <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">

                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <div class="form-group row">
                                                                <label for="slik" class="col-sm-4 col-form-label">Komentar Penyelia</label>
                                                                <label for="slik" class="col-sm-1 col-form-label px-0">
                                                                    <div class="d-flex justify-content-end">
                                                                        <div style="width: 20px">
                                                                            :
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                                <div class="col">
                                                                    <h6 class="font-italic">{{ $getKomentarPenyelia5->komentar ?? '' }}</h6>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="d-flex">
                                                                <div style="width: 30%">
                                                                    <p class="p-0 m-0"><strong>Komentar Penyelia : </strong>
                                                                    </p>
                                                                </div>
                                                                <h6 class="font-italic">
                                                                    {{ $getKomentarPenyelia5->komentar ?? '' }}</h6>
                                                                <input type="text" readonly class="form-control-plaintext font-italic" id="komentar" value="{{ $itemKomentar->komentar }}">

                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($userPBO)
                                                    @if ($getKomentarPBO5)
                                                        <div class="row form-group sub pl-4">
                                                            <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                                <div class="d-flex justify-content-end">
                                                                    <div style="width: 20px">

                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <div class="col">
                                                                <div class="form-group row">
                                                                <label for="slik" class="col-sm-4 col-form-label">Komentar PBO</label>
                                                                <label for="slik" class="col-sm-1 col-form-label px-0">
                                                                    <div class="d-flex justify-content-end">
                                                                        <div style="width: 20px">
                                                                            :
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                                <div class="col">
                                                                     <h6 class="font-italic">
                                                                        {{ $getKomentarPBO5->komentar ?? '' }}</h6>
                                                                </div>
                                                            </div>
                                                                {{-- <div class="d-flex">
                                                                    <div style="width: 30%">
                                                                        <p class="p-0 m-0"><strong>Komentar PBO : </strong>
                                                                        </p>
                                                                    </div>
                                                                    <h6 class="font-italic">
                                                                        {{ $getKomentarPBO5->komentar ?? '' }}</h6>
                                                                    <input type="text" readonly class="form-control-plaintext font-italic" id="komentar" value="{{ $itemKomentar->komentar }}">

                                                                </div> --}}
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                                @if ($dataUmum->id_cabang == 1 && $getKomentarPBP5 != null)
                                                    <div class="row form-group sub pl-4">
                                                        <label for="staticEmail" class="col-sm-3 col-form-label"></label>
                                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                            <div class="d-flex justify-content-end">
                                                                <div style="width: 20px">

                                                                </div>
                                                            </div>
                                                        </label>
                                                        <div class="col">
                                                            <div class="form-group row">
                                                                <label for="slik" class="col-sm-4 col-form-label">Komentar PBP</label>
                                                                <label for="slik" class="col-sm-1 col-form-label px-0">
                                                                    <div class="d-flex justify-content-end">
                                                                        <div style="width: 20px">
                                                                            :
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                                <div class="col">
                                                                    <h6 class="font-italic">{{ $getKomentarPBP5->komentar ?? '' }}</h6>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="d-flex">
                                                                <div style="width: 30%">
                                                                    <p class="p-0 m-0"><strong>Komentar PBP : </strong>
                                                                    </p>
                                                                </div>
                                                                <h6 class="font-italic">
                                                                    {{ $getKomentarPBP5->komentar ?? '' }}</h6>
                                                                <input type="text" readonly class="form-control-plaintext font-italic" id="komentar" value="{{ $itemKomentar->komentar }}">

                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                @endif
                                                <hr>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                            @endforeach
                        @endforeach

                        @php
                            $pendapatUsulanStaf = \App\Models\PendapatPerAspek::select('*')
                                ->where('id_staf', '!=', null)
                                ->where('id_aspek', $itemAspek->id)
                                ->where('id_pengajuan', $dataUmum->id)
                                ->get();
                            $pendapatUsulanPenyelia = \App\Models\PendapatPerAspek::select('*')
                                ->where('id_penyelia', '!=', null)
                                ->where('id_pengajuan', $dataUmum->id)
                                ->get();
                            $userPBO = \App\Models\User::select('id')
                                                        ->where('id_cabang', $dataUmum->id_cabang)
                                                        ->where('role', 'PBO')
                                                        ->first();

                            if ($userPBO) {
                                $pendapatUsulanPbo = \App\Models\PendapatPerAspek::select('*')
                                    ->where('id_pbo', '!=', null)
                                    ->where('id_pengajuan', $dataUmum->id)
                                    ->get();
                            }
                            if ($dataUmum->id_cabang == 1) {
                                $pendapatUsulanPBP = \App\Models\PendapatPerAspek::select('*')
                                    ->where('id_pbp', '!=', null)
                                    ->where('id_pengajuan', $dataUmum->id)
                                    ->get();
                            }
                        @endphp
                        {{-- @php
                    echo "<pre>"; print_r($pendapatUsulanStaf);echo "</pre>";
                @endphp --}}
                        @foreach ($pendapatUsulanStaf as $item)
                            @if ($item->id_aspek == $itemAspek->id)
                                <div class="alert alert-success">
                                    <div class="form-group row sub mb-0" style="">
                                        <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">Pendapat
                                            & Usulan <br> (Staff)</label>
                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                            <div class="d-flex justify-content-end">
                                                <div style="width: 20px">
                                                    :
                                                </div>
                                            </div>
                                        </label>
                                        <div class="col">
                                            <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                                value="{{ $item->pendapat_per_aspek }}">
                                            <p class="form-control-plaintext text-justify">{{ $item->pendapat_per_aspek }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @foreach ($pendapatUsulanPenyelia as $item)
                            @if ($item->id_aspek == $itemAspek->id)
                                <div class="alert alert-success ">
                                    <div class="form-group row sub mb-0" style="">
                                        <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">Pendapat
                                            & Usulan <br> (Penyelia)</label>
                                        <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                            <div class="d-flex justify-content-end">
                                                <div style="width: 20px">
                                                    :
                                                </div>
                                            </div>
                                        </label>
                                        <div class="col">
                                            <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                                value="{{ $item->pendapat_per_aspek }}">
                                            <p class="form-control-plaintext text-justify">{{ $item->pendapat_per_aspek }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if ($userPBO)
                            @foreach ($pendapatUsulanPbo as $item)
                                @if ($item->id_aspek == $itemAspek->id)
                                    <div class="alert alert-success ">
                                        <div class="form-group row sub mb-0" style="">
                                            <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">Pendapat
                                                & Usulan <br> (PBO)</label>
                                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                <div class="d-flex justify-content-end">
                                                    <div style="width: 20px">
                                                        :
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="col">
                                                <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                                    value="{{ $item->pendapat_per_aspek }}">
                                                <p class="form-control-plaintext text-justify">{{ $item->pendapat_per_aspek }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        @if ($dataUmum->id_cabang == 1)
                            @foreach ($pendapatUsulanPBP as $item)
                                @if ($item->id_aspek == $itemAspek->id)
                                    <div class="alert alert-success ">
                                        <div class="form-group row sub mb-0" style="">
                                            <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">Pendapat
                                                & Usulan <br> (PBP)</label>
                                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                                <div class="d-flex justify-content-end">
                                                    <div style="width: 20px">
                                                        :
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="col">
                                                <input type="hidden" readonly class="form-control-plaintext" id="staticEmail"
                                                    value="{{ $item->pendapat_per_aspek }}">
                                                <p class="form-control-plaintext text-justify">{{ $item->pendapat_per_aspek }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
            @php
                $userPBO = \App\Models\User::select('id')
                    ->where('id_cabang', $dataUmum->id_cabang)
                    ->where('role', 'PBO')
                    ->first();

                $userPBP = \App\Models\User::select('id')
                    ->where('id_cabang', $dataUmum->id_cabang)
                    ->where('role', 'PBP')
                    ->whereNotNull('nip')
                    ->first();
            @endphp
            <div class="card mb-3">
                <div class="card-header bg-info color-white font-weight-bold" data-toggle="collapse" href=#cardPendapatUsulan>
                    Pendapat & Usulan
                </div>
                <div class="card-body collapse multi-collapse show" id="cardPendapatUsulan">
                    <div class="alert alert-success ">
                        <div class="form-group row sub mb-0" style="">
                            <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">Pendapat
                                & Usulan <br> (Staff)</label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">
                                        :
                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                {{--  <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                    value="{{ $pendapatDanUsulan->komentar_staff }}">  --}}
                                <input type="hidden" class="form-control-plaintext" id="staticEmail"
                                    value="{{ $pendapatDanUsulan->komentar_staff }}">
                                <p class="form-control-plaintext text-justify">{{ $pendapatDanUsulan->komentar_staff }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-success ">
                        <div class="form-group row sub mb-0" style="">
                            <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">Pendapat
                                & Usulan <br> (Penyelia)</label>
                            <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">
                                        :
                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                {{--  <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                    value="{{ $pendapatDanUsulan->komentar_penyelia }}">  --}}
                                <input type="hidden" class="form-control-plaintext" id="staticEmail"
                                    value="{{ $pendapatDanUsulan->komentar_penyelia }}">
                                <p class="form-control-plaintext text-justify">{{ $pendapatDanUsulan->komentar_penyelia }}</p>
                            </div>
                        </div>
                    </div>
                    @if ($userPBO)
                        <div class="alert alert-success ">
                            <div class="form-group row sub mb-0" style="">
                                <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">Pendapat
                                    & Usulan <br> (PBO)</label>
                                <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                    <div class="d-flex justify-content-end">
                                        <div style="width: 20px">
                                            :
                                        </div>
                                    </div>
                                </label>
                                <div class="col">
                                    {{--  <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                        value="{{ $pendapatDanUsulan->komentar_pbo }}">  --}}
                                    <input type="hidden" class="form-control-plaintext" id="staticEmail"
                                        value="{{ $pendapatDanUsulan->komentar_pbo }}">
                                    <p class="form-control-plaintext text-justify">{{ $pendapatDanUsulan->komentar_pbo }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($dataUmum->id_cabang == 1)
                        @if ($userPBP)
                            <div class="alert alert-success">
                                <div class="form-group row sub mb-0">
                                    <label for="staticEmail" class="col-sm-3 col-form-label font-weight-bold">Pendapat
                                        & Usulan <br> (PBP)</label>
                                    <label for="staticEmail" class="col-sm-1 col-form-label px-0">
                                        <div class="d-flex justify-content-end">
                                            <div style="width: 20px">
                                                :
                                            </div>
                                        </div>
                                    </label>
                                    <div class="col">
                                        {{--  <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                            value="{{ $pendapatDanUsulan->komentar_pbp }}">  --}}
                                        <input type="hidden" class="form-control-plaintext" id="staticEmail"
                                            value="{{ $pendapatDanUsulan->komentar_pbp }}">
                                        <p class="form-control-plaintext text-justify">{{ $pendapatDanUsulan->komentar_pbp }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    <div class="alert alert-success ">
                        <div class="form-group row">
                            <label for="komentar_pincab" class="col-sm-3 col-form-label">Pendapat & Usulan Pimpinan Cabang</label>
                            <label for="komentar_pincab" class="col-sm-1 col-form-label px-0">
                                <div class="d-flex justify-content-end">
                                    <div style="width: 20px">
                                        :
                                    </div>
                                </div>
                            </label>
                            <div class="col">
                                @if (Auth::user()->role == 'Pincab')
                                    <input type="hidden" name="id_pengajuan" id="" value="{{ $dataUmum->id }}">
                                    <textarea name="komentar_pincab" class="form-control" id="komentar_pincab" cols="5" rows="3"
                                        placeholder="Masukkan Pendapat Pemimpin Cabang">{{ $pendapatDanUsulan->komentar_pincab }}</textarea>
                                @endif
                                @if (Auth::user()->role == 'SPI' || Auth::user()->role == 'Kredit Umum' || auth()->user()->role == 'Direksi')
                                    {{--  <input type="text" readonly class="form-control-plaintext" id="staticEmail"
                                    value="{{ $pendapatDanUsulan->komentar_pincab }}">  --}}
                                    <input type="hidden" class="form-control-plaintext" id="staticEmail"
                                    value="{{ $pendapatDanUsulan->komentar_pincab }}">
                                    <p class="form-control-plaintext text-justify">{{ $pendapatDanUsulan->komentar_pincab }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (Auth::user()->role == 'Pincab')
                <button type="submit" class="btn btn-primary btn-simpan mr-2"><i class="fa fa-save"></i> Simpan</button>
                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Reset</button>
            @endif
        </form>
    </div>
@endsection
@push('custom-script')
    <script>
        $(".btn-simpan").on('click', function(e) {
            const role = "{{auth()->user()->role}}"
            if (role == 'Pincab') {
                const komentarPincabVal = $("#komentar_pincab").val()
                if (!komentarPincabVal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Field Pendapat dan usulan harus diisi"
                    })
                    e.preventDefault()
                }
            }
        })
    </script>
@endpush
