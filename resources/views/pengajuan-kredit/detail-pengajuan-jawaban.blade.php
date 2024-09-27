@extends('layouts.template')
@php
    $dataIndex = match ($dataUmum->skema_kredit) {
        'PKPJ' => 1,
        'KKB' => 2,
        'Talangan Umroh' => 1,
        'Prokesra' => 1,
        'Kusuma' => 1,
        null => 1,
    };

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

    <form id="pengajuan_kredit" action="{{ route('pengajuan.insertkomentar') }}" method="post">
        @csrf
        {{-- <div class="form-wizard active" data-index='0' data-done='true'>
            <div class="row col-md-12 table-responsive mb-3">
                <label for="">Riwayat Pengembalian Data</label>
                <div class="col-md-12">
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
                                <input type="hidden" id="val_pengembalian" value="1">
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $itemPengembalian->alasan }}</td>
                                    <td>{{ $itemPengembalian->dari }}</td>
                                    <td>{{ $itemPengembalian->ke }}</td>
                                    <td>{{ date_format($itemPengembalian->created_at, 'd M Y') }}</td>
                                    <td>{{ getKaryawan($itemPengembalian->nip) }}</td>
                                </tr>
                            @empty
                                <input type="hidden" id="val_pengembalian" value="0">
                                <tr>
                                    <td colspan="6" class="text-center">Tidak Ada Riwayat Pengembalian Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div> --}}
        <div class="form-wizard active" data-index='0' data-done='true'>
            <div class="row">
                @php
                    $dataLevelDua = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable', 'is_hide')
                        ->where('level', 2)
                        ->where('id_parent', $itemSP->id)
                        ->where('nama', 'Surat Permohonan')
                        ->get();
                    //dd($dataLevelDua);
                @endphp
                @foreach ($dataLevelDua as $item)
                    @if ($item->opsi_jawaban == 'file')
                        @php
                            $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                ->where('jawaban_text.id_jawaban', $item->id)
                                ->get();
                            //dd($dataDetailJawabanText);
                        @endphp
                        @foreach ($dataDetailJawabanText as $itemTextDua)
                            @php
                                $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text);
                            @endphp
                            <div class="row col-md-12">
                                <div class="form-group col-md-12 mb-0">
                                    <label for="">{{ $item->nama }}</label>
                                </div>
                                <div class="col-md-12 form-group">
                                    <b>Jawaban:</b>
                                    <div class="mt-2">
                                        @if ($file_parts['extension'] == 'pdf')
                                            <iframe
                                                src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                                width="100%" height="800px"></iframe>
                                        @else
                                            <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                                alt="" width="800px">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endforeach
                <div class="form-group col-md-12">
                    <label for="">Nama Lengkap</label>
                    <input type="text" disabled name="name" id="nama"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $dataUmumNasabah->nama) }}" placeholder="Nama sesuai dengan KTP">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Kabupaten</label>
                    <select name="kabupaten" disabled class="form-control @error('name') is-invalid @enderror select2"
                        id="kabupaten">
                        <option value="">---Pilih Kabupaten----</option>
                        @foreach ($allKab as $item)
                            <option value="{{ old('id', $item->id) }}"
                                {{ old('id', $item->id) == $dataUmumNasabah->id_kabupaten ? 'selected' : '' }}>
                                {{ $item->kabupaten }}</option>
                        @endforeach
                    </select>
                    @error('kabupaten')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Kecamatan</label>
                    <select name="kec" disabled id="kecamatan"
                        class="form-control @error('kec') is-invalid @enderror  select2">
                        <option value="">---Pilih Kecamatan----</option>
                        @foreach ($allKec as $kec)
                            <option value="{{ $kec->id }}"
                                {{ $kec->id == $dataUmumNasabah->id_kecamatan ? 'selected' : '' }}>{{ $kec->kecamatan }}
                            </option>
                        @endforeach
                    </select>
                    @error('kec')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Desa</label>
                    <select disabled name="desa" id="desa"
                        class="form-control @error('desa') is-invalid @enderror select2">
                        <option value="">---Pilih Desa----</option>
                        @foreach ($allDesa as $desa)
                            <option value="{{ $desa->id }}"
                                {{ $desa->id == $dataUmumNasabah->id_desa ? 'selected' : '' }}>{{ $desa->desa }}
                            </option>
                        @endforeach
                    </select>
                    @error('desa')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Alamat Rumah</label>
                    <textarea disabled name="alamat_rumah" class="form-control @error('alamat_rumah') is-invalid @enderror" id=""
                        cols="30" rows="4" placeholder="Alamat Rumah disesuaikan dengan KTP">{{ old('alamat_rumah', $dataUmumNasabah->alamat_rumah) }}</textarea>
                    @error('alamat_rumah')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <hr>
                </div>
                <div class="form-group col-md-12">
                    <label for="">Alamat Usaha</label>
                    <textarea disabled name="alamat_usaha" class="form-control @error('alamat_usaha') is-invalid @enderror" id=""
                        cols="30" rows="4" placeholder="Alamat Usaha disesuaikan dengan KTP">{{ old('alamat_usaha', $dataUmumNasabah->alamat_usaha) }}</textarea>
                    @error('alamat_usaha')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Tempat</label>
                    <input disabled type="text" name="tempat_lahir" id=""
                        class="form-control @error('tempat_lahir') is-invalid @enderror"
                        value="{{ old('tempat_lahir', $dataUmumNasabah->tempat_lahir) }}" placeholder="Tempat Lahir">
                    @error('tempat_lahir')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Tanggal Lahir</label>
                    <input disabled type="date" name="tanggal_lahir" id=""
                        class="form-control @error('tanggal_lahir') is-invalid @enderror"
                        value="{{ old('tanggal_lahir', $dataUmumNasabah->tanggal_lahir) }}" placeholder="Tempat Lahir">
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Status</label>
                    <select disabled name="status" id=""
                        class="form-control @error('status') is-invalid @enderror select2">
                        <option value=""> --Pilih Status --</option>
                        <option value="menikah"
                            {{ old('status', $dataUmumNasabah->status) == 'menikah' ? 'selected' : '' }}>
                            Menikah</option>
                        <option value="belum menikah"
                            {{ old('status', $dataUmumNasabah->status) == 'belum menikah' ? 'selected' : '' }}>Belum
                            Menikah
                        </option>
                        <option value="duda" {{ old('status', $dataUmumNasabah->status) == 'duda' ? 'selected' : '' }}>
                            Duda
                        </option>
                        <option value="janda" {{ old('status', $dataUmumNasabah->status) == 'janda' ? 'selected' : '' }}>
                            Janda
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">No. KTP</label>
                    <input disabled type="text" name="no_ktp"
                        class="form-control @error('no_ktp') is-invalid @enderror" id=""
                        value="{{ old('no_ktp', $dataUmumNasabah->no_ktp) }}" placeholder="Masukkan 16 digit No. KTP">
                    @error('no_ktp')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                @if ($dataUmumNasabah->status == 'menikah')
                    @php
                        $dataLevelDua = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable')
                            ->where('level', 2)
                            ->where('id_parent', $itemKTPSu->id)
                            ->where('nama', 'Foto KTP Suami')
                            ->get();
                    @endphp
                    @foreach ($dataLevelDua as $item)
                        @if ($item->opsi_jawaban == 'file')
                            @php
                                $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                                    ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                    ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                    ->where('jawaban_text.id_jawaban', $item->id)
                                    ->get();
                            @endphp
                            @foreach ($dataDetailJawabanText as $itemTextDua)
                                @php
                                    $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text);
                                @endphp
                                <div class="form-group col-md-12">
                                    <label for="">{{ $item->nama }}</label>
                                    <div class="col-md-12 form-group">
                                        <b>Jawaban:</b>
                                        <div class="mt-2">
                                            @if ($file_parts['extension'] == 'pdf')
                                                <iframe
                                                    src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                                    width="100%" height="400px"></iframe>
                                            @else
                                                <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                                    alt="" width="400px">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                    @php
                        $dataLevelDua = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable')
                            ->where('level', 2)
                            ->where('id_parent', $itemKTPSu->id)
                            ->where('nama', 'Foto KTP Istri')
                            ->get();
                    @endphp
                    @foreach ($dataLevelDua as $item)
                        @if ($item->opsi_jawaban == 'file')
                            @php
                                $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                                    ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                    ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                    ->where('jawaban_text.id_jawaban', $item->id)
                                    ->get();
                            @endphp
                            @foreach ($dataDetailJawabanText as $itemTextDua)
                                @php
                                    $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text);
                                @endphp
                                <div class="form-group col-md-12">
                                    <label for="">{{ $item->nama }}</label>
                                    <div class="form-group">
                                        <b>Jawaban:</b>
                                        <div class="mt-2">
                                            @if ($file_parts['extension'] == 'pdf')
                                                <iframe
                                                    src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                                    width="100%" height="400px"></iframe>
                                            @else
                                                <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                                    alt="" width="400px">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                @else
                    @php
                        $dataLevelDua = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable')
                            ->where('level', 2)
                            ->where('id_parent', $itemKTPSu->id)
                            ->where('nama', 'Foto KTP Nasabah')
                            ->get();
                    @endphp
                    @foreach ($dataLevelDua as $item)
                        @if ($item->opsi_jawaban == 'file')
                            @php
                                $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                                    ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                    ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                    ->where('jawaban_text.id_jawaban', $item->id)
                                    ->get();
                            @endphp
                            @foreach ($dataDetailJawabanText as $itemTextDua)
                                @php
                                    $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text);
                                @endphp
                                <div class="form-group col-md-12">
                                    <label for="">{{ $item->nama }}</label>
                                    <div class="form-group col-md-12">
                                        <b>Jawaban:</b>
                                        <div class="mt-2">
                                            @if ($file_parts['extension'] == 'pdf')
                                                <iframe
                                                    src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                                    width="100%" height="400px"></iframe>
                                            @else
                                                <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                                    alt="" width="400px">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                @endif
                <div class="form-group col-md-12">
                    <label for="">Pekerjaan</label>
                    <input disabled type="text" name="pekerjaan"
                        class="form-control @error('pekerjaan') is-invalid @enderror" id=""
                        value="{{ old('pekerjaan', $dataUmumNasabah?->pekerjaan ?? '') }}" placeholder="Masukkan Pekerjaan">
                    @error('pekerjaan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Sektor Kredit</label>
                    <select disabled name="sektor_kredit" id=""
                        class="form-control @error('sektor_kredit') is-invalid @enderror select2">
                        <option value=""> --Pilih Sektor Kredit -- </option>
                        <option value="perdagangan"
                            {{ old('sektor_kredit', $dataUmumNasabah->sektor_kredit) == 'perdagangan' ? 'selected' : '' }}>
                            Perdagangan</option>
                        <option value="perindustrian"
                            {{ old('sektor_kredit', $dataUmumNasabah->sektor_kredit) == 'perindustrian' ? 'selected' : '' }}>
                            Perindustrian</option>
                        <option value="dll"
                            {{ old('sektor_kredit', $dataUmumNasabah->sektor_kredit) == 'dll' ? 'selected' : '' }}>dll
                        </option>
                    </select>
                    @error('sektor_kredit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">{{ $itemSlik?->nama }}</label>
                    <br>
                    <b>Jawaban : </b>
                    <div class="jawaban-responsive">
                        {{ $itemSlik?->option }}
                    </div>
                    @php
                        $komentarSlik = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', '=', 'detail_komentar.id_komentar')
                            ->where('id_pengajuan', $dataUmum->id)
                            ->where('id_item', $itemSlik?->id_item)
                            ->first();
                    @endphp
                    <div class="input-group input-b-bottom">
                        <input type="hidden" name="id_item[]" value="{{ $itemSlik?->id_item }}">
                        <input type="hidden" name="id_option[]" value="{{ $itemSlik?->id_jawaban }}">
                        <input type="text" class="form-control komentar" name="komentar_penyelia[]"
                            placeholder="Masukkan Komentar"
                            value="{{ isset($komentarSlik->komentar) ? $komentarSlik->komentar : '' }}">
                        <div class="input-skor">
                            @php
                                $skorSlik = $itemSlik?->skor_penyelia ? $itemSlik?->skor_penyelia : $itemSlik?->skor;
                            @endphp
                            <input type="number" class="form-control" placeholder="" name="skor_penyelia[]"
                                onKeyUp="if(this.value>4){this.value='4';}else if(this.value<=0){this.value='1';}"
                                min="0"
                                max="4"
                                {{ $itemSlik?->status_skor == 0 ? 'readonly' : '' }}
                                value="{{ $skorSlik || $skorSlik > 0 ? $skorSlik : null }}">
                        </div>
                    </div>
                </div>
                @php
                    // $key += 1;
                    // check level 2
                    $dataLS = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable')
                        ->where('level', 2)
                        ->where('id_parent', $itemSP->id)
                        ->where('nama', 'Laporan SLIK')
                        ->get();
                @endphp
                @foreach ($dataLS as $item)
                    @if ($item->opsi_jawaban == 'file')
                        @php
                            $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                ->where('jawaban_text.id_jawaban', $item->id)
                                ->get();
                        @endphp
                        @foreach ($dataDetailJawabanText as $itemTextDua)
                            @php
                                $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text);
                            @endphp
                            <div class="form-group col-md-12">
                                <label for="">{{ $item->nama }}</label>
                            </div>
                            <div class="col-md-12 form-group">
                                <b>Jawaban:</b>
                                <div class="mt-2 pl-3">
                                    @if ($file_parts['extension'] == 'pdf')
                                        <iframe
                                            src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                            width="100%" height="800px"></iframe>
                                    @else
                                        <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                            alt="" width="800px">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endforeach
                <div class="form-group col-md-12">
                    <label for="">Jenis Usaha</label>
                    <textarea disabled name="jenis_usaha" class="form-control @error('jenis_usaha') is-invalid @enderror" id=""
                        cols="30" rows="4" placeholder="Jenis Usaha secara spesifik">{{ old('jenis_usaha', $dataUmumNasabah->jenis_usaha) }}</textarea>
                    @error('jenis_usaha')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Jumlah Kredit yang diminta</label>
                    <input type="text" disabled name="jumlah_kredit"
                        class="form-control @error('jumlah_kredit') is-invalid @enderror"
                        placeholder="Jumlah Kredit"
                        value="{{ old('jumlah_kredit', 'Rp ' . number_format($dataUmumNasabah->jumlah_kredit ? $dataUmumNasabah->jumlah_kredit : 0, 2, ',', '.')) }}">
                    @error('jumlah_kredit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Tenor Yang Diminta</label>
                    <input type="text" disabled name="tenor_yang_diminta"
                        class="form-control @error('tenor_yang_diminta') is-invalid @enderror"
                        placeholder="Tenor Yang Diminta"
                        value="{{ old('tenor_yang_diminta', $dataUmumNasabah->tenor_yang_diminta) }} Bulan">
                    @error('tenor_yang_diminta')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Tujuan Kredit</label>
                    <textarea disabled name="tujuan_kredit" class="form-control @error('tujuan_kredit') is-invalid @enderror"
                        id="" cols="30" rows="4" placeholder="Tujuan Kredit">{{ old('tujuan_kredit', $dataUmumNasabah->tujuan_kredit) }}</textarea>
                    @error('tujuan_kredit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Jaminan yang disediakan</label>
                    <textarea disabled name="jaminan" class="form-control @error('jaminan') is-invalid @enderror" id=""
                        cols="30" rows="4" placeholder="Jaminan yang disediakan">{{ old('jaminan', $dataUmumNasabah->jaminan_kredit) }}</textarea>
                    @error('jaminan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Hubungan Bank</label>
                    <textarea disabled name="hubungan_bank" class="form-control @error('hubungan_bank') is-invalid @enderror"
                        id="" cols="30" rows="4" placeholder="Hubungan dengan Bank">{{ old('hubungan_bank', $dataUmumNasabah->hubungan_bank) }}</textarea>
                    @error('hubungan_bank')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Hasil Verifikasi</label>
                    <textarea disabled name="hasil_verifikasi" class="form-control @error('hasil_verifikasi') is-invalid @enderror"
                        id="" cols="30" rows="4" placeholder="Hasil Verifikasi Karakter Umum">{{ old('hasil_verifikasi', $dataUmumNasabah->verifikasi_umum) }}</textarea>
                    @error('hasil_verifikasi')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <hr>
                </div>
            </div>
        </div>

        @if ($dataUmumNasabah->skema_kredit == 'KKB')
            @php
                $keterangan = $dataPO->keterangan;
                $pemesanan = str_replace('Pemesanan ', '', $keterangan);
            @endphp
            <div class="form-wizard" data-index='1' data-done='true' id="data-po">
            {{--  <div class="row" id="data-po">  --}}
                <div class="form-group col-md-12">
                    <span style="color: black; font-weight: bold; font-size: 18px;">Jenis Kendaraan Roda 2 :</span>
                </div>
                <div class="form-group col-md-12">
                    <label>Merk Kendaraan</label>
                    <input type="text" name="merk" id="merk" class="form-control @error('merk') is-invalid @enderror" value="{{ $dataPO->merk }}" placeholder="Merk kendaraan" readonly>
                    @error('merk')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label>Tipe Kendaraan</label>
                    <input type="text" name="tipe_kendaraan" id="tipe_kendaraan" class="form-control @error('tipe_kendaraan') is-invalid @enderror" value="{{ $dataPO->tipe }}" placeholder="Tipe kendaraan" readonly>
                    @error('tipe_kendaraan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Tahun</label>
                    <input type="number" name="tahun" id="tahun"
                        class="form-control @error('tahun') is-invalid @enderror" placeholder="Tahun Kendaraan"
                        value="{{ $dataPO?->tahun_kendaraan ?? '' }}" min="2000" disabled>
                    @error('tahun')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Warna</label>
                    <input type="text" name="warna" id="warna"
                        class="form-control @error('warna') is-invalid @enderror" placeholder="Warna Kendaraan"
                        value="{{ $dataPO?->warna ?? '' }}" disabled>
                    @error('warna')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <span style="color: black">Keterangan :</span>
                </div>
                <div class="form-group col-md-12">
                    <label for="">Pemesanan</label>
                    <input type="text" name="pemesanan" id="pemesanan"
                        class="form-control @error('pemesanan') is-invalid @enderror"
                        placeholder="Pemesanan Kendaraan" value="{{ $pemesanan ?? '' }}" disabled>
                    @error('pemesanan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Sejumlah</label>
                    <input type="number" name="sejumlah" id="sejumlah"
                        class="form-control @error('sejumlah') is-invalid @enderror" placeholder="Jumlah Kendaraan"
                        value="{{ $dataPO?->jumlah ?? '' }}" disabled>
                    @error('sejumlah')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Harga</label>
                    <input type="text" name="harga" id="harga"
                        class="form-control rupiah @error('harga') is-invalid @enderror"
                        placeholder="Harga Kendaraan" value="{{ 'Rp ' . number_format($dataPO?->harga ?? '', 2, ',', '.') }}" disabled>
                    @error('harga')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        @endif

        <input type="hidden" id="jumlahData" name="jumlahData" hidden value="{{ $dataUmumNasabah->skema_kredit == 'KKB' ? count($dataAspek) + $dataIndex + 1 : count($dataAspek) + $dataIndex }}">
        <input type="hidden" id="id_pengajuan" name="id_pengajuan" value="{{ $dataUmum->id }}">
        @php
            $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor')
                ->where('id_pengajuan', $dataUmum->id)
                ->get();
        @endphp
        @foreach ($dataDetailJawaban as $itemJawabanDetail)
            <input type="hidden" name="id_jawaban[]" value="{{ $itemJawabanDetail->id }}" id="">
        @endforeach
        @foreach ($dataAspek as $key => $value)
            @php
                if ($dataUmumNasabah->skema_kredit == 'KKB')
                    $key += ($dataIndex + 1);
                else
                    $key += $dataIndex;

                // check level 2
                $dataLevelDua = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable', 'is_hide')
                    ->where('level', 2)
                    ->where('id_parent', $value->id)
                    ->get();

                // check level 4
                $dataLevelEmpat = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable', 'is_hide')
                    ->where('level', 4)
                    ->where('id_parent', $value->id)
                    ->get();
                $pendapatStafPerAspek = \App\Models\PendapatPerAspek::where('id_pengajuan', $dataUmum->id)
                    ->whereNotNull('id_staf')
                    ->where('id_aspek', $value->id)
                    ->first();
                $pendapatPenyeliaPerAspek = \App\Models\PendapatPerAspek::where('id_pengajuan', $dataUmum->id)
                    ->whereNotNull('id_penyelia')
                    ->where('id_aspek', $value->id)
                    ->first();
            @endphp
            {{-- level level 2 --}}

            <div class="form-wizard {{$key}}" data-index='{{ $key }}' data-done='true'>
                <div class="">
                    @foreach ($dataLevelDua as $item)
                        @if ($item->opsi_jawaban != 'option')
                            @if (!$item->is_hide)
                                @php
                                    $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                                        ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                        ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                        ->where('jawaban_text.id_jawaban', $item->id)
                                        ->get();
                                @endphp
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    <div class="row">
                                        <div class="form-group col-md-12 mb-0">
                                            <label for="">{{ $item->nama }}</label>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <b>Jawaban: </b>
                                                @if ($item->opsi_jawaban == 'file')
                                                    @php
                                                        $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text);
                                                    @endphp
                                                    @if ($file_parts['extension'] == 'pdf')
                                                        <iframe
                                                            src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                                            width="100%" height="800px"></iframe>
                                                    @else
                                                        <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}"
                                                            alt="" width="800px">
                                                    @endif
                                                    {{-- Rupiah data dua --}}
                                                @elseif ($item->opsi_jawaban == 'number' && $item->id != 143)
                                                        <div class="jawaban-responsive">
                                                            Rp. {{ number_format((int) $itemTextDua->opsi_text, 2, ',', '.') }}
                                                        </div>
                                                    @if ($itemTextDua->is_commentable)
                                                        <input type="hidden" name="id_item[]" value="{{ $item->id }}">
                                                        <div class="input-k-bottom">
                                                            <input type="text" class="form-control komentar"
                                                                name="komentar_penyelia[]" placeholder="Masukkan Komentar">
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="jawaban-responsive">
                                                        {{ str_replace('_', ' ', $itemTextDua->opsi_text) }} {{ $item->opsi_jawaban == 'persen' ? '%' : '' }}
                                                    </div>
                                                    @if ($itemTextDua->is_commentable)
                                                        <input type="hidden" name="id_item[]" value="{{ $item->id }}">
                                                        <div class="input-k-bottom">
                                                            <input type="text" class="form-control komentar"
                                                                name="komentar_penyelia[]" placeholder="Masukkan Komentar">
                                                        </div>
                                                    @endif
                                                @endif
                                        </div>
                                    </div>

                                    <input type="text" hidden class="form-control mb-3" placeholder="Masukkan komentar"
                                        name="komentar_penyelia" value="{{ $itemTextDua->nama }}" disabled>
                                    <input type="text" hidden class="form-control mb-3" placeholder="Masukkan komentar"
                                        name="komentar_penyelia" value="{{ $itemTextDua->opsi_text }}" disabled>
                                    <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextDua->id }}">
                                    <input type="hidden" name="id[]" value="{{ $itemTextDua->id_item }}">
                                @endforeach
                            @endif
                        @endif
                        @php
                            $dataJawaban = \App\Models\OptionModel::where('option', '!=', '-')
                                ->where('id_item', $item->id)
                                ->get();
                            $dataOption = \App\Models\OptionModel::where('option', '=', '-')
                                ->where('id_item', $item->id)
                                ->get();

                            $getKomentar = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', '=', 'detail_komentar.id_komentar')
                                ->where('id_pengajuan', $dataUmum->id)
                                ->where('id_item', $item->id)
                                ->where('id_user', Auth::user()->id)
                                ->first();

                            // check level 3
                            $dataLevelTiga = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable', 'is_hide')
                                ->where('level', 3)
                                ->where('id_parent', $item->id)
                                ->get();
                        @endphp
                        @foreach ($dataOption as $itemOption)
                            @if ($itemOption->option == '-')
                                @if (!$item->is_hide)
                                    @if ($item->nama != "Ijin Usaha")
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <h4>{{ $item->nama }}</h4>
                                            </div>
                                            @if ($item->nama == 'Ijin Usaha' && $countIjin == 0)
                                                <div class="row col-md-12 mb-0 ml-1">
                                                    <div class="col-md-12 form-group">
                                                        <b>Jawaban: </b>
                                                        <div class="jawaban-responsive">
                                                            Tidak Ada Legalitas Usaha
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            @endif
                        @endforeach

                        @if (count($dataJawaban) != 0)
                            @if (!$item->is_hide)
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="">{{ $item->nama }}</label>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                @foreach ($dataJawaban as $key => $itemJawaban)
                                    @php
                                        $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                            ->where('id_pengajuan', $dataUmum->id)
                                            ->get();
                                        $count = count($dataDetailJawaban);
                                        for ($i = 0; $i < $count; $i++) {
                                            $data[] = $dataDetailJawaban[$i]['id_jawaban'];
                                        }
                                        $getSkorPenyelia = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                            ->where('id_pengajuan', $dataUmum->id)
                                            ->where('id_jawaban', $itemJawaban->id)
                                            ->first();
                                    @endphp
                                    @if (in_array($itemJawaban->id, $data))
                                        @if (isset($data))
                                            <div class="col-md-12 form-group">
                                                @if (!$item->is_hide)
                                                    @if ($item->nama)
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <b>Jawaban : </b>
                                                                <div class="jawaban-responsive">
                                                                    {{ $itemJawaban->option }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="input-group input-b-bottom">
                                                        <input type="hidden" name="id_item[]"
                                                            value="{{ $item->id }}">
                                                        <input type="hidden" name="id_option[]"
                                                            value="{{ $itemJawaban->id }}">
                                                        @if ($item->is_commentable == 'Ya')
                                                            <input type="text" class="form-control komentar"
                                                                name="komentar_penyelia[]" placeholder="Masukkan Komentar"
                                                                value="{{ isset($getKomentar->komentar) ? $getKomentar->komentar : '' }}">
                                                            <div class="input-skor">
                                                                @php
                                                                    $skorInput2 = null;
                                                                    $skorInput2 = $getSkorPenyelia->skor_penyelia ? $getSkorPenyelia->skor_penyelia : $itemJawaban->skor;
                                                                @endphp
                                                                <input type="number" class="form-control" placeholder=""
                                                                    name="skor_penyelia[]"
                                                                    min="0"
                                                                    max="4"
                                                                    onKeyUp="if(this.value>4){this.value='4';}else if(this.value<=0){this.value='1';}"
                                                                    {{ $item->status_skor == 0 ? 'readonly' : '' }}
                                                                    value="{{ $skorInput2 || $skorInput2 > 0 ? $skorInput2 : null }}">
                                                            </div>
                                                        @else
                                                            <input type="hidden" name="komentar_penyelia[]"
                                                                value="{{ isset($getKomentar->komentar) ? $getKomentar->komentar : '' }}">
                                                            <input type="hidden" name="skor_penyelia[]"
                                                                value="null">
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="input-group input-b-bottom">
                                                        <input type="hidden" name="id_item[]"
                                                            value="{{ $item->id }}">
                                                        <input type="hidden" name="id_option[]"
                                                            value="{{ $itemJawaban->id }}">
                                                        @if ($item->is_commentable == 'Ya')
                                                            <input type="hidden" name="komentar_penyelia[]" value="{{ isset($getKomentar->komentar) ? $getKomentar->komentar : '' }}">
                                                            <div>
                                                                @php
                                                                    $skorInput2 = null;
                                                                    $skorInput2 = $getSkorPenyelia->skor_penyelia ? $getSkorPenyelia->skor_penyelia : $itemJawaban->skor;
                                                                @endphp
                                                                <input type="hidden"
                                                                    name="skor_penyelia[]"
                                                                    value="{{ $skorInput2 || $skorInput2 > 0 ? $skorInput2 : null }}">
                                                            </div>
                                                        @else
                                                            <input type="hidden" name="komentar_penyelia[]"
                                                                value="{{ isset($getKomentar->komentar) ? $getKomentar->komentar : '' }}">
                                                            <input type="hidden" name="skor_penyelia[]"
                                                                value="null">
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="text" hidden class="form-control mb-3"
                                                placeholder="Masukkan komentar" name="komentar_penyelia"
                                                value="{{ $itemJawaban->option }}" disabled>
                                            <input type="text" hidden class="form-control mb-3"
                                                placeholder="Masukkan komentar" name="komentar_penyelia"
                                                value="{{ $itemJawaban->skor }}" disabled>
                                            <input type="hidden" name="id[]" value="{{ $item->id }}">
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        @foreach ($dataLevelTiga as $keyTiga => $itemTiga)
                            @if (!$itemTiga->is_hide)
                                @if ($itemTiga->opsi_jawaban != 'option')
                                    @php
                                        $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                                            ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                            ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                            ->where('jawaban_text.id_jawaban', $itemTiga->id)
                                            ->get();

                                            $jumlahDataDetailJawabanText = $dataDetailJawabanText ? count($dataDetailJawabanText) : 0;
                                    @endphp
                                    @foreach ($dataDetailJawabanText as $itemTextTiga)
                                        @if ($itemTextTiga->nama != 'Ratio Tenor Asuransi')
                                            <div class="row">
                                                <div class="form-group col-md-12 mb-0">
                                                    @if ($itemTiga->opsi_jawaban == 'file')
                                                        @if ($jumlahDataDetailJawabanText > 1)
                                                            <label for="">{{ $itemTextTiga->nama }} {{$loop->iteration}}</label>
                                                        @else
                                                            <label for="">{{ $itemTextTiga->nama }}</label>
                                                        @endif
                                                    @else
                                                            <label for="">{{ $itemTextTiga->nama }}</label>
                                                    @endif
                                                </div>
                                                <div class="col-md-12">
                                                    <b>Jawaban: </b>
                                                        @if ($itemTiga->opsi_jawaban == 'file')
                                                            @php
                                                                $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $itemTiga->id . '/' . $itemTextTiga->opsi_text);
                                                            @endphp
                                                            @if ($file_parts['extension'] == 'pdf')
                                                                <iframe
                                                                    style="border: 5px solid #dc3545;"
                                                                    src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $itemTiga->id . '/' . $itemTextTiga->opsi_text }}"
                                                                    width="100%" height="800px"></iframe>
                                                            @else
                                                                <img style="border: 5px solid #dc3545;" src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $itemTiga->id . '/' . $itemTextTiga->opsi_text }}"
                                                                    alt="" width="800px">
                                                            @endif
                                                            {{-- Rupiah data tiga --}}
                                                        @elseif ($itemTiga->opsi_jawaban == 'number')
                                                            <div class="jawaban-responsive">
                                                                Rp.{{ number_format((int) $itemTextTiga->opsi_text, 2, ',', '.') }}
                                                            </div>
                                                            @if ($item->is_commentable == 'Ya')
                                                                <div class="input-k-bottom">
                                                                    <input type="hidden" name="id_item[]"
                                                                        value="{{ $item->id }}">
                                                                    <input type="text" class="form-control komentar"
                                                                        name="komentar_penyelia[]"
                                                                        placeholder="Masukkan Komentar">
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="jawaban-responsive">
                                                                {{ $itemTextTiga->opsi_text }}{{ $itemTiga->opsi_jawaban == 'persen' ? '%' : '' }}
                                                            </div>
                                                            @if ($item->is_commentable == 'Ya')
                                                                <div class="input-k-bottom">
                                                                    <input type="hidden" name="id_item[]"
                                                                        value="{{ $item->id }}">
                                                                    <input type="text" class="form-control komentar"
                                                                        name="komentar_penyelia[]"
                                                                        placeholder="Masukkan Komentar">
                                                                </div>
                                                            @endif
                                                        @endif
                                                </div>
                                            </div>

                                            <input type="hidden" class="form-control mb-3" placeholder="Masukkan komentar"
                                                name="komentar_penyelia" value="{{ $itemTextTiga->nama }}" disabled>
                                            <input type="hidden" class="form-control mb-3" placeholder="Masukkan komentar"
                                                name="komentar_penyelia" value="{{ $itemTextTiga->opsi_text }}" disabled>

                                            <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextTiga->id }}">
                                            <input type="hidden" name="id[]" value="{{ $itemTextTiga->id_item }}">
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
                                    $getKomentar = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', '=', 'detail_komentar.id_komentar')
                                        ->where('id_pengajuan', $dataUmum->id)
                                        ->where('id_item', $itemTiga->id)
                                        ->first();
                                    // check level empat
                                    $dataLevelEmpat = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable', 'is_hide')
                                        ->where('level', 4)
                                        ->where('id_parent', $itemTiga->id)
                                        ->get();
                                    // check jawaban kelayakan
                                    $checkJawabanKelayakan = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor')
                                        ->where('id_pengajuan', $dataUmum->id)
                                        ->whereIn('id_jawaban', ['183', '184'])
                                        ->first();
                                @endphp

                                @foreach ($dataOptionTiga as $itemOptionTiga)
                                    @if (!$itemTiga->is_hide)
                                        @if ($itemOptionTiga->option == '-')
                                            @if (isset($checkJawabanKelayakan))
                                            @else
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <h5> {{ $itemTiga->nama }}</h5>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach

                                @if (count($dataJawabanLevelTiga) != 0)
                                    @if ($itemTiga->nama == 'Ratio Tenor Asuransi Opsi')
                                    @else
                                        @if (isset($checkJawabanKelayakan))
                                            @if ($itemTiga->nama != 'Kelayakan Usaha')
                                            {{-- @else --}}
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label for="">{{ $itemTiga->nama }}</label>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            @if ($itemTiga->nama != 'Kelayakan Usaha')
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label for="">{{ $itemTiga->nama }}</label>
                                                    </div>
                                                </div>
                                            @else
                                            @endif
                                        @endif
                                        <div class="row">
                                            @foreach ($dataJawabanLevelTiga as $key => $itemJawabanLevelTiga)
                                                {{--  @if (!$itemTiga->is_hide)  --}}
                                                    @php
                                                        $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                            ->where('id_pengajuan', $dataUmum->id)
                                                            ->get();

                                                        $getSkorPenyelia = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                            ->where('id_pengajuan', $dataUmum->id)
                                                            ->where('id_jawaban', $itemJawabanLevelTiga->id)
                                                            ->first();
                                                        $count = count($dataDetailJawaban);
                                                        for ($i = 0; $i < $count; $i++) {
                                                            $data[] = $dataDetailJawaban[$i]['id_jawaban'];
                                                        }
                                                    @endphp
                                                    @if (in_array($itemJawabanLevelTiga->id, $data))
                                                        @if (isset($data))
                                                            <div class="col-md-12 form-group">
                                                                @if ($itemTiga->nama != 'Ratio Coverage Opsi')
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <b>Jawaban : </b>
                                                                            <div class="jawaban-responsive">
                                                                                {{ $itemJawabanLevelTiga->option }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="input-group input-b-bottom">
                                                                    <input type="hidden" name="id_item[]"
                                                                        value="{{ $itemTiga->id }}">
                                                                    <input type="hidden" name="id_option[]"
                                                                        value="{{ $itemJawabanLevelTiga->id }}">
                                                                    @php
                                                                        $skorInput3 = null;
                                                                        $skorInput3 = $getSkorPenyelia?->skor_penyelia ? $getSkorPenyelia?->skor_penyelia : $itemJawabanLevelTiga->skor;
                                                                    @endphp
                                                                    @if ($itemTiga->is_commentable == 'Ya')
                                                                        <input type="text" class="form-control komentar"
                                                                            name="komentar_penyelia[]"
                                                                            placeholder="Masukkan Komentar"
                                                                            value="{{ isset($getKomentar->komentar) ? $getKomentar->komentar : '' }}">
                                                                        <div class="input-skor">
                                                                            <input type="number" class="form-control"
                                                                                min="0"
                                                                                max="4"
                                                                                placeholder="" name="skor_penyelia[]"
                                                                                onKeyUp="if(this.value>4){this.value='4';}else if(this.value<=0){this.value='1';}"
                                                                                {{ $itemTiga->status_skor == 0 ? 'readonly' : '' }}
                                                                                value="{{ $skorInput3 || $skorInput3 > 0 ? $skorInput3 : null }}">
                                                                        </div>
                                                                    @else
                                                                        <input type="hidden" name="komentar_penyelia[]"
                                                                            value="{{ isset($getKomentar->komentar) ? $getKomentar->komentar : '' }}">
                                                                        <input type="hidden" name="skor_penyelia[]"
                                                                            value="null">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <input type="text" hidden class="form-control mb-3"
                                                                placeholder="Masukkan komentar" name="komentar_penyelia"
                                                                value="{{ $itemJawabanLevelTiga->option }}" disabled>
                                                            <input type="text" hidden class="form-control mb-3"
                                                                placeholder="Masukkan komentar" name="skor_penyelia"
                                                                value="{{ $itemJawabanLevelTiga->skor }}" disabled>
                                                            <input type="hidden" name="id[]"
                                                                value="{{ $itemTiga->id }}">
                                                        @endif
                                                    @endif
                                                {{--  @endif  --}}
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                                @foreach ($dataLevelEmpat as $keyEmpat => $itemEmpat)
                                    @if (!$itemEmpat->is_hide)
                                        @if ($itemEmpat->opsi_jawaban != 'option')
                                            @php
                                                $dataDetailJawabanTextEmpat = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                                                    ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                                    ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                                    ->where('jawaban_text.id_jawaban', $itemEmpat->id)
                                                    ->get();
                                            @endphp
                                            @foreach ($dataDetailJawabanTextEmpat as $itemTextEmpat)
                                                <div class="row">
                                                    <div class="form-group col-md-12 mb-0">
                                                        <label for="">{{ $itemTextEmpat->nama }}</label>
                                                        <br>
                                                        <b>Jawaban:</b>
                                                                @if ($itemEmpat->opsi_jawaban == 'file')
                                                                    @if (intval($itemTextEmpat->opsi_text) > 1)
                                                                        @php
                                                                            $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $itemEmpat->id . '/' . $itemTextEmpat->opsi_text);
                                                                        @endphp
                                                                        @if ($file_parts['extension'] == 'pdf')
                                                                            <iframe
                                                                                src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $itemEmpat->id . '/' . $itemTextEmpat->opsi_text }}"
                                                                                width="100%" height="400"></iframe>
                                                                        @else
                                                                            <img style="border: 5px solid #c2c7cf"
                                                                                src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $itemEmpat->id . '/' . $itemTextEmpat->opsi_text }}"
                                                                                alt="" width="400">
                                                                        @endif
                                                                    @else
                                                                        @php
                                                                            $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $itemEmpat->id . '/' . $itemTextEmpat->opsi_text);
                                                                        @endphp
                                                                        @if ($file_parts['extension'] == 'pdf')
                                                                            <iframe
                                                                                src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $itemEmpat->id . '/' . $itemTextEmpat->opsi_text }}"
                                                                                width="100%" height="500px"></iframe>
                                                                        @else
                                                                            <img style="border: 5px solid #c2c7cf"
                                                                                src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $itemEmpat->id . '/' . $itemTextEmpat->opsi_text }}"
                                                                                alt="" width="500px">
                                                                        @endif
                                                                    @endif
                                                                    {{-- Rupiah data empat --}}
                                                                @elseif ($itemEmpat->opsi_jawaban == 'number' && $itemEmpat->id != 130)
                                                                    <div class="jawaban-responsive">
                                                                        Rp.{{ number_format((int) $itemTextEmpat->opsi_text, 2, ',', '.') }}
                                                                    </div>
                                                                    @if ($itemTextEmpat->is_commentable == 'Ya')
                                                                        <div class="input-k-bottom">
                                                                            <input type="hidden" name="id_item[]"
                                                                                value="{{ $item->id }}">
                                                                            <input type="text"
                                                                                class="form-control komentar"
                                                                                name="komentar_penyelia[]"
                                                                                placeholder="Masukkan Komentar">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <div class="jawaban-responsive">
                                                                        {{ $itemTextEmpat->opsi_text }}
                                                                            @if ($itemEmpat->opsi_jawaban == 'persen')
                                                                                %
                                                                            @elseif($itemEmpat->id == 130)
                                                                                Bulan
                                                                            @else
                                                                            @endif
                                                                    </div>
                                                                    @if ($itemTextEmpat->is_commentable == 'Ya')
                                                                        <div class="input-k-bottom">
                                                                            <input type="hidden" name="id_item[]"
                                                                                value="{{ $item->id }}">
                                                                            <input type="text"
                                                                                class="form-control komentar"
                                                                                name="komentar_penyelia[]"
                                                                                placeholder="Masukkan Komentar">
                                                                        </div>
                                                                    @endif
                                                                @endif

                                                    </div>
                                                </div>

                                                <input type="hidden" class="form-control mb-3"
                                                    placeholder="Masukkan komentar" name="komentar_penyelia"
                                                    value="{{ $itemTextEmpat->nama }}" disabled>
                                                <input type="hidden" class="form-control mb-3"
                                                    placeholder="Masukkan komentar" name="komentar_penyelia"
                                                    value="{{ $itemTextEmpat->opsi_text }}" disabled>
                                                <input type="hidden" name="id_jawaban_text[]"
                                                    value="{{ $itemTextEmpat->id }}">
                                                <input type="hidden" name="id[]" value="{{ $itemTextEmpat->id_item }}">
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
                                            $isJawabanExist = \App\Models\OptionModel::join('jawaban', 'jawaban.id_jawaban', 'option.id')
                                                ->where('jawaban.id_pengajuan', $dataUmum->id)
                                                ->where('id_item', $itemEmpat->id)
                                                ->count();

                                            $getKomentar = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', '=', 'detail_komentar.id_komentar')
                                                ->where('id_pengajuan', $dataUmum->id)
                                                ->where('id_item', $itemEmpat->id)
                                                ->first();
                                            // echo "<pre>";
                                            // print_r ($dataOptionEmpat);
                                            // echo "</pre>";
                                            // ;
                                        @endphp
                                        @if ($itemEmpat->opsi_jawaban == 'option' && $isJawabanExist > 0)
                                            @if ($itemEmpat->nama != "Tidak Memiliki Jaminan Tambahan")
                                                <div class="row">
                                                    <div class="form-group col-md-12 mb-0">
                                                        <label for="">{{ $itemEmpat->nama }}</label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif

                                        {{-- Data jawaban Level Empat --}}
                                        @if (count($dataJawabanLevelEmpat) != 0)
                                            <div class="row">
                                                @foreach ($dataJawabanLevelEmpat as $key => $itemJawabanLevelEmpat)
                                                    @php
                                                        $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                            ->where('id_pengajuan', $dataUmum->id)
                                                            ->get();
                                                        $count = count($dataDetailJawaban);
                                                        for ($i = 0; $i < $count; $i++) {
                                                            $data[] = $dataDetailJawaban[$i]['id_jawaban'];
                                                        }
                                                        $getSkorPenyelia = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                            ->where('id_pengajuan', $dataUmum->id)
                                                            ->where('id_jawaban', $itemJawabanLevelEmpat->id)
                                                            ->first();
                                                    @endphp
                                                    @if (in_array($itemJawabanLevelEmpat->id, $data))
                                                        @if (isset($data))
                                                            <div class="col-md-12 form-group">
                                                                @if ($itemEmpat->nama != "Tidak Memiliki Jaminan Tambahan")
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <b>Jawaban : </b>
                                                                            <div class="jawaban-responsive">
                                                                                {{ $itemJawabanLevelEmpat->option }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="input-group input-b-bottom">
                                                                    <input type="hidden" name="id_item[]"
                                                                        value="{{ $itemEmpat->id }}">
                                                                    <input type="hidden" name="id_option[]"
                                                                        value="{{ $itemJawabanLevelEmpat->id }}">
                                                                    @if ($itemEmpat->is_commentable == 'Ya')
                                                                        <input type="text" class="form-control komentar"
                                                                            name="komentar_penyelia[]"
                                                                            placeholder="Masukkan Komentar"
                                                                            value="{{ isset($getKomentar->komentar) ? $getKomentar->komentar : '' }}">
                                                                        <div class="input-skor">
                                                                            @php
                                                                                $skorInput4 = null;
                                                                                $skorInput4 = $getSkorPenyelia?->skor_penyelia ? $getSkorPenyelia?->skor_penyelia : $itemJawabanLevelEmpat->skor;
                                                                            @endphp
                                                                            <input type="number" class="form-control"
                                                                                placeholder="" name="skor_penyelia[]"
                                                                                min="0"
                                                                                max="4"
                                                                                onKeyUp="if(this.value>4){this.value='4';}else if(this.value<=0){this.value='1';}"
                                                                                {{ $itemEmpat->status_skor == 0 ? 'readonly' : '' }}
                                                                                value="{{ $skorInput4 || $skorInput4 > 0 ? $skorInput4 : null }}">
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <input type="hidden" class="form-control mb-3"
                                                                placeholder="Masukkan komentar" name="komentar_penyelia"
                                                                value="{{ $itemJawabanLevelEmpat->option }}" disabled>
                                                            <input type="hidden" name="id[]"
                                                                value="{{ $itemEmpat->id }}">
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endforeach
                    @if (Auth::user()->role == 'PBP')
                        @php
                            $getPendapatPerAspek = \App\Models\PendapatPerAspek::where('id_pengajuan', $dataUmum->id)
                                ->where('id_aspek', $value->id)
                                ->where('id_pbp', Auth::user()->id)
                                ->first();
                        @endphp
                        <div class="form-group col-md-12">
                            <label for="">Pendapat dan Usulan {{ $value->nama }}</label>
                            <input type="hidden" name="id_aspek[]" value="{{ $value->id }}">
                            <textarea name="pendapat_per_aspek[]" class="form-control @error('pendapat_per_aspek') is-invalid @enderror"
                                id="pendapat_per_aspek[]" cols="30" rows="4" placeholder="Pendapat Per Aspek">{{ isset($getPendapatPerAspek->pendapat_per_aspek) ? $getPendapatPerAspek->pendapat_per_aspek : '' }}</textarea>
                            @error('pendapat_per_aspek')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                </div>
                <hr>
                <hr>
                <div class="form-group col-md-12">
                    <label for="">Pendapat dan Usulan Staf Kredit</label>
                    <p>{{ $pendapatStafPerAspek?->pendapat_per_aspek }}</p>
                </div>
                <div class="form-group col-md-12">
                    <label for="">Pendapat dan Usulan Penyelia Kredit</label>
                    <p>{{ $pendapatPenyeliaPerAspek?->pendapat_per_aspek }}</p>
                </div>
                @if ($dataUmumNasabah->id_pbo)
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan PBO</label>
                        <p>{{ $pendapatDanUsulanPBO?->komentar_pbo }}</p>
                    </div>
                @endif
            @elseif (Auth::user()->role == 'PBO')
                    @php
                        $getPendapatPerAspek = \App\Models\PendapatPerAspek::where('id_pengajuan', $dataUmum->id)
                            ->where('id_aspek', $value->id)
                            ->where('id_pbo', Auth::user()->id)
                            ->first();
                    @endphp
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan {{ $value->nama }}</label>
                        <input type="hidden" name="id_aspek[]" value="{{ $value->id }}">
                        <textarea name="pendapat_per_aspek[]" class="form-control @error('pendapat_per_aspek') is-invalid @enderror"
                            id="pendapat_per_aspek[]" cols="30" rows="4" placeholder="Pendapat Per Aspek">{{ isset($getPendapatPerAspek->pendapat_per_aspek) ? $getPendapatPerAspek->pendapat_per_aspek : '' }}</textarea>
                        @error('pendapat_per_aspek')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
            </div>
            <hr>
            <hr>
            <div class="form-group col-md-12">
                <label for="">Pendapat dan Usulan Staf Kredit</label>
                <p>{{ $pendapatStafPerAspek?->pendapat_per_aspek }}</p>
            </div>
            <div class="form-group col-md-12">
                <label for="">Pendapat dan Usulan Penyelia Kredit</label>
                <p>{{ $pendapatPenyeliaPerAspek?->pendapat_per_aspek }}</p>
            </div>
            @else
                @php
                    $getPendapatPerAspek = \App\Models\PendapatPerAspek::where('id_pengajuan', $dataUmum->id)
                        ->where('id_aspek', $value->id)
                        ->where('id_penyelia', Auth::user()->id)
                        ->first();
                @endphp
                <div class="form-group col-md-12">
                    <label for="">Pendapat dan Usulan {{ $value->nama }}</label>
                    <input type="hidden" name="id_aspek[]" value="{{ $value->id }}">
                    <textarea name="pendapat_per_aspek[]" class="form-control @error('pendapat_per_aspek') is-invalid @enderror"
                        id="pendapat_per_aspek[]" cols="30" rows="4" placeholder="Pendapat Per Aspek">{{ isset($getPendapatPerAspek->pendapat_per_aspek) ? $getPendapatPerAspek->pendapat_per_aspek : '' }}</textarea>
                    @error('pendapat_per_aspek')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="form-group col-md-12">
                <label for="">Pendapat dan Usulan Staf Kredit</label>
                <p>{{ $pendapatStafPerAspek?->pendapat_per_aspek }}</p>
            </div>
        @endif
        </div>
        @endforeach
        {{-- pendapat dan usulan --}}
        {{-- <div class="row col-md-12 table-responsive mb-3">
            <label for="">Riwayat Pengembalian Data</label>
            <div class="col-md-12">
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
                                <td colspan="6" class="text-center">Tidak Ada Riwayat Pengembalian Data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div> --}}
        @if (Auth::user()->role == 'Penyelia Kredit')
            <div class="form-wizard" data-index='{{ $dataUmumNasabah->skema_kredit == 'KKB' ? count($dataAspek) + $dataIndex + 1 : count($dataAspek) + $dataIndex }}' data-done='true'>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan Staf Kredit</label>
                        <br>
                        <span>
                            {{ $pendapatDanUsulanStaf?->komentar_staff }}
                        </span>
                        <hr>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan Penyelia</label>
                        <textarea name="komentar_penyelia_keseluruhan"
                            class="form-control @error('komentar_penyelia_keseluruhan') is-invalid @enderror" id="komentar_penyelia_keseluruhan" cols="30"
                            rows="4" placeholder="Pendapat dan Usulan Penyelia">{{ isset($pendapatDanUsulanPenyelia->komentar_penyelia) ? $pendapatDanUsulanPenyelia->komentar_penyelia : '' }}</textarea>
                        @error('komentar_penyelia_keseluruhan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <hr>
                    </div>
                </div>
            </div>
        @elseif (Auth::user()->role == 'PBO')
            <div class="form-wizard" data-index='{{ $dataUmumNasabah->skema_kredit == 'KKB' ? count($dataAspek) + $dataIndex + 1 : count($dataAspek) + $dataIndex }}' data-done='true'>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan Staf Kredit</label>
                        <br>
                        <span>
                            {{ $pendapatDanUsulanStaf?->komentar_staff }}
                        </span>
                        <hr>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan Penyelia Kredit</label>
                        <br>
                        <span>
                            {{ $pendapatDanUsulanPenyelia?->komentar_penyelia }}
                        </span>
                        <hr>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan PBO</label>
                        <textarea name="komentar_pbo_keseluruhan"
                            class="form-control @error('komentar_pbo_keseluruhan') is-invalid @enderror" id="komentar_pbo_keseluruhan" cols="30"
                            rows="4" placeholder="Pendapat dan Usulan Penyelia Kredit" >{{ isset($pendapatDanUsulanPBO->komentar_pbO) ? $pendapatDanUsulanPBO?->komentar_pbO : '' }}</textarea>
                        @error('komentar_pbo_keseluruhan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <hr>
                    </div>
                </div>
            </div>
        @else
            <div class="form-wizard" data-index='{{ $dataUmumNasabah->skema_kredit == 'KKB' ? count($dataAspek) + $dataIndex + 1 : count($dataAspek) + $dataIndex }}' data-done='true'>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan Staf Kredit</label>
                        <br>
                        <span>
                            {{ $pendapatDanUsulanStaf?->komentar_staff }}
                        </span>
                        <hr>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan Penyelia Kredit</label>
                        <br>
                        <span>
                            {{ $pendapatDanUsulanPenyelia?->komentar_penyelia }}
                        </span>
                        <hr>
                    </div>
                    @if ($dataUmumNasabah->id_pbo)
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan PBO</label>
                        <br>
                        <span>
                            {{ $pendapatDanUsulanPBO?->komentar_pbo }}
                        </span>
                        <hr>
                    </div>
                    @endif
                    <div class="form-group col-md-12">
                        <label for="">Pendapat dan Usulan PBP</label>
                        <textarea name="komentar_pbp_keseluruhan"
                            class="form-control @error('komentar_pbp_keseluruhan') is-invalid @enderror" id="komentar_pbp_keseluruhan" cols="30"
                            rows="4" placeholder="Pendapat dan Usulan Penyelia Kredit" >{{ isset($pendapatDanUsulanPBP->komentar_pbp) ? $pendapatDanUsulanPBP->komentar_pbp : '' }}</textarea>
                        @error('komentar_pbp_keseluruhan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <hr>
                    </div>
                </div>
            </div>
        @endif
        {{-- end pendapat dan usulan --}}
        <div class="row form-group">
            <div class="col text-right">
                <button class="btn btn-default btn-prev"><span class="fa fa-chevron-left"></span> Sebelumnya</button>
                <button class="btn btn-danger btn-next">Selanjutnya <span class="fa fa-chevron-right"></span></button>
                <button type="submit" class="btn btn-info btn-simpan" id="submit">Simpan <span
                        class="fa fa-save"></span></button>
                {{-- <button class="btn btn-info ">Simpan <span class="fa fa-chevron-right"></span></button> --}}
            </div>
        </div>
    </form>
@endsection

@push('custom-script')
    <script>
        // disabled scrol on input type number
        $(document).on("wheel", "input[type=number]", function (e) {
            $(this).blur();
        });

        let aspekArr;
        $(window).on('load', function() {
            $("#id_merk").trigger("change");
            aspekArr = <?php echo json_encode($dataAspek); ?>;
        });

        $(document).ready(function() {
            const nullValue = []

            function cekValueKosong(formIndex) {
                var skema = $("#skema_kredit").val()
                var form = ".form-wizard[data-index=" + formIndex + "]";
                var inputFile = $(form + " input[type=file]")
                var inputText = $(form + " input[type=text]")
                var inputNumber = $(form + " input[type=number]")
                var select = $(form + " select")
                var textarea = $(form + " textarea")

                $.each(inputFile, function(i, v) {
                    if (v.value == '' && !$(this).prop('disabled') && $(this).closest('.filename') == '') {
                        if (form == ".form-wizard[data-index='2']") {
                            var ijin = $(form + " select[name=ijin_usaha]")
                            if (ijin != "tidak_ada_legalitas_usaha") {
                                let val = $(this).attr("id").toString();
                                nullValue.push(val.replaceAll("_", " "))
                            }
                        } else {
                            let val = $(this).attr("id").toString();
                            nullValue.push(val.replaceAll("_", " "))
                        }
                    } else if (v.value != '') {
                        let val = $(this).attr("id").toString().replaceAll("_", " ");
                        for (var i = 0; i < nullValue.length; i++) {
                            while (nullValue[i] == val) {
                                nullValue.splice(i, 1)
                                break;
                            }
                        }
                    }
                })

                $.each(inputText, function(i, v) {
                    if (v.value == '' && !$(this).prop('disabled')) {
                        let val = $(this).attr("id").toString();
                        //console.log(val)
                        nullValue.push(val.replaceAll("_", " "))
                    } else if (v.value != '') {
                        let val = $(this).attr("id").toString().replaceAll("_", " ");
                        for (var i = 0; i < nullValue.length; i++) {
                            while (nullValue[i] == val) {
                                nullValue.splice(i, 1)
                                break;
                            }
                        }
                    }
                })

                $.each(inputNumber, function(i, v) {
                    if (v.value == '' && !$(this).prop('disabled')) {
                        let val = $(this).attr("id").toString();
                        //console.log(val)
                        nullValue.push(val.replaceAll("_", " "))
                    } else if (v.value != '') {
                        let val = $(this).attr("id").toString().replaceAll("_", " ");
                        for (var i = 0; i < nullValue.length; i++) {
                            while (nullValue[i] == val) {
                                nullValue.splice(i, 1)
                                break;
                            }
                        }
                    }
                })

                $.each(select, function(i, v) {
                    if (v.value == '' && !$(this).prop('disabled')) {
                        let val = $(this).attr("id").toString();
                        if (val != "persentase_kebutuhan_kredit_opsi" && val != "ratio_tenor_asuransi_opsi" && val !=
                            "ratio_coverage_opsi") {
                            //console.log(val)
                            nullValue.push(val.replaceAll("_", " "))
                        }
                    } else if (v.value != '') {
                        let val = $(this).attr("id").toString().replaceAll("_", " ");
                        for (var i = 0; i < nullValue.length; i++) {
                            while (nullValue[i] == val) {
                                nullValue.splice(i, 1)
                                break;
                            }
                        }
                    }
                })

                $.each(textarea, function(i, v) {
                    if (v.value == '' && !$(this).prop('disabled')) {
                        let val = $(this).attr("id").toString();
                        //console.log(val)
                        nullValue.push(val.replaceAll("_", " "))
                    } else if (v.value != '') {
                        let val = $(this).attr("id").toString().replaceAll("_", " ");
                        for (var i = 0; i < nullValue.length; i++) {
                            while (nullValue[i] == val) {
                                nullValue.splice(i, 1)
                                break;
                            }
                        }
                    }
                })

                //console.log(nullValue);
            }

            $(".btn-simpan").on('click', function(e) {
                const role = "{{Auth::user()->role}}"
                if (role == 'Penyelia Kredit') {
                    const pendapatPerAspek = $("textarea[id^=pendapat_per_aspek]");
                    var msgPendapat = '';
                    for (var i = 0; i < pendapatPerAspek.length; i++) {
                        const value = pendapatPerAspek[i].value;
                        if (!value) {
                            const aspek = aspekArr[i].nama
                            msgPendapat += '<li class="text-left">Pendapat pada '+aspek+' harus diisi.</li>';
                        }
                    }

                    if (msgPendapat != '') {
                        console.log(msgPendapat)
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: '<ul>'+msgPendapat+'</ul>'
                        })
                        e.preventDefault()
                    }
                    else {
                        if ($('#komentar_penyelia_keseluruhan').val() == '') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: "Field Pendapat dan usulan harus diisi"
                            })
                            e.preventDefault()
                        }
                        else {
                            if (nullValue.length > 0) {
                                let message = "";
                                $.each(nullValue, (i, v) => {
                                    message += v != '' ? v + ", " : ''
                                })
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: "Field " + message + " harus diisi terlebih dahulu"
                                })
                                e.preventDefault()
                            }
                        }
                    }
                }
                else if (role == 'PBO') {
                    if ($('#komentar_pbo_keseluruhan').val() == '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: "Field Pendapat dan usulan harus diisi"
                        })
                        e.preventDefault()
                    }
                    else {
                        if (nullValue.length > 0) {
                            let message = "";
                            $.each(nullValue, (i, v) => {
                                console.log('validasi')
                                console.log(v)
                                console.log('end validasi')
                                message += v != '' ? v + ", " : ''
                            })
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: "Field " + message + " harus diisi terlebih dahulu"
                            })
                            e.preventDefault()
                        }
                    }
                }
                else if (role == 'PBP') {
                    if ($('#komentar_pbp_keseluruhan').val() == '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: "Field Pendapat dan usulan harus diisi"
                        })
                        e.preventDefault()
                    }
                    else {
                        if (nullValue.length > 0) {
                            let message = "";
                            $.each(nullValue, (i, v) => {
                                console.log('validasi')
                                console.log(v)
                                console.log('end validasi')
                                message += v != '' ? v + ", " : ''
                            })
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: "Field " + message + " harus diisi terlebih dahulu"
                            })
                            e.preventDefault()
                        }
                    }
                }
                else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Tidak memiliki hak akses untuk melakukan aktivitas ini"
                    })
                    e.preventDefault()
                }
            })

            if ($('#val_pengembalian').val() == 0) {
                $(".side-wizard li[data-index='0'] a span i").html("0%");
            }else{
                $(".side-wizard li[data-index='0'] a span i").html("100%");
            }

            if ($("#komentar_penyelia_keseluruhan").val() == '') {
                $(".side-wizard li[data-index=9] a span i").html("0%")
            } else {
                $(".side-wizard li[data-index=9] a span i").html("100%")
            }
        })

        @if ($dataUmum->skema_kredit == 'KKB')
            $("#id_merk").change(function() {
                let val = $(this).val();

                $.ajax({
                    type: "get",
                    url: "{{ route('get-tipe-kendaraan') }}?id_merk=" + val,
                    dataType: "json",
                    success: (res) => {
                        if (res) {
                            $("#id_tipe").empty();
                            $("#id_tipe").append(`<option>Pilih Tipe</option>`)

                            $.each(res.tipe, function(i, value) {
                                $("#id_tipe").append(`
                                <option value="${value.id}" ${(value.id == {{ $dataPO->id_type }}) ? 'selected' : ''}>${value.tipe}</option>
                            `);
                            })
                        }
                    }
                });
            });
        @endif

        var jumlahData = $('#jumlahData').val();
        console.log('Jumlah data : '+jumlahData);
        for (let index = 0; index <= parseInt(jumlahData); index++) {
            // for (let index = 0; index <= parseInt(jumlahData); index++) {
                var selected = index == parseInt(jumlahData) ? ' selected' : ''
                $(".side-wizard li[data-index='" + index + "']").addClass('active' + selected)
                $(".side-wizard li[data-index='" + index + "'] a span i").removeClass('fa fa-ban')
                if ($(".side-wizard li[data-index='" + index + "'] a span i").html() == '' || $(
                        ".side-wizard li[data-index='" + index + "'] a span i").html() == '0%') {
                    $(".side-wizard li[data-index='" + index + "'] a span i").html('0%')
                }
            // }

            var form = ".form-wizard[data-index='" + index + "']"

            var input = $(form + " input:disabled");
            var select = $(form + " select")
            var textarea = $(form + " textarea")

            var ttlInput = 0;
            var ttlInputFilled = 0;
            $.each(input, function(i, v) {
                ttlInput++
                if (v.value != '') {
                    ttlInputFilled++
                }
            })
            var ttlSelect = 0;
            var ttlSelectFilled = 0;
            $.each(select, function(i, v) {
                ttlSelect++
                if (v.value != '') {
                    ttlSelectFilled++
                }
            })

            var ttlTextarea = 0;
            var ttlTextareaFilled = 0;
            $.each(textarea, function(i, v) {
                ttlTextarea++
                if (v.value != '') {
                    ttlTextareaFilled++
                }
            })

            if (index == 1) {
                var allInput = ttlInput - 1
                var allInputFilled = ttlInputFilled
            }
            else if (index == 2) {
                if (ttlInput == 6 && ttlInputFilled == 3) {
                    var allInput = 6;
                    var allInputFilled = 6;
                }
                else {
                    var allInput = ttlInput;
                    var allInputFilled = ttlInputFilled;
                }
                if (allInput == 0 && allInputFilled == 0) {
                    allInput = 1;
                    allInputFilled = 1;
                }
            }
            else if (index == 3) {
                var allInput = ttlInput - 3
                var allInputFilled = ttlInputFilled
            }
            else if (index == 4) {
                var allInput = ttlInput
                var allInputFilled = ttlInputFilled
            }
            else if (index == 5) {
                var allInput = ttlInput - 2
                var allInputFilled = ttlInputFilled
            }
            else if (index == 6) {
                var allInput = ttlInput - 1
                var allInputFilled = ttlInputFilled
            }
            else{
                var allInput = ttlInput
                var allInputFilled = ttlInputFilled
            }

            var percentage = parseInt(allInputFilled / allInput * 100);
            percentage = Number.isNaN(percentage) ? 0 : percentage;
            percentage = percentage > 100 ? 100 : percentage;
            percentage = percentage < 0 ? 0 : percentage;

            if (index == 7) {
                if ($("textarea[name=komentar_penyelia_keseluruhan]").val() == '') {
                    $(".side-wizard li[data-index='" + index + "'] a span i").html("0%")
                } else {
                    $(".side-wizard li[data-index='" + index + "'] a span i").html("100%")
                }
            } else {
                $(".side-wizard li[data-index='" + index + "'] a span i").html(Number.isNaN(percentage) ? 0 + "%" : percentage +
                    "%")
            }
            // $(".side-wizard li[data-index='"+index+"'] input.answer").val(allInput);
            // $(".side-wizard li[data-index='"+index+"'] input.answerFilled").val(allInputFilled);
        }

        // $('textarea[name=komentar_penyelia_keseluruhan]').on('change', function() {
        $('#komentar_penyelia_keseluruhan').on('change', function() {
            if ($("textarea[name=komentar_penyelia_keseluruhan]").val() == '') {
                $(".side-wizard li[data-index=9] a span i").html("0%")
            } else {
                $(".side-wizard li[data-index=9] a span i").html("100%")
            }
        })

        function cekBtn() {
            var indexNow = $(".form-wizard.active").data('index')
            var prev = parseInt(indexNow) - 1
            var next = parseInt(indexNow) + 1

            $(".btn-prev").hide()
            $(".btn-simpan").hide()

            $(".progress").prop('disabled', true);
            if ($(".form-wizard[data-index='" + prev + "']").length == 1) {
                $(".btn-prev").show()
            }

            if (parseInt(indexNow) == parseInt(jumlahData)) {
                // $(".btn-next").click(function(e) {
                //     if (parseInt(indexNow) != parseInt(jumlahData)) {
                //         $(".btn-next").show()

                //     }
                $(".btn-simpan").show()
                $(".progress").prop('disabled', false);
                $(".btn-next").hide()
                // });
                // $(".btn-next").show()

            } else {
                $(".btn-next").show()
                $(".btn-simpan").hide()
            }
        }

        function cekWizard(isNext = false) {
            var indexNow = $(".form-wizard.active").data('index')
            // console.log(indexNow);
            if (isNext) {
                $(".side-wizard li").removeClass('active')
            }

            $(".side-wizard li").removeClass('selected')

            for (let index = 0; index <= parseInt(indexNow); index++) {
                var selected = index == parseInt(indexNow) ? ' selected' : ''
                $(".side-wizard li[data-index='" + index + "']").addClass('active' + selected)
                $(".side-wizard li[data-index='" + index + "'] a span i").removeClass('fa fa-ban')
                if ($(".side-wizard li[data-index='" + index + "'] a span i").html() == '' || $(
                        ".side-wizard li[data-index='" + index + "'] a span i").html() == '0%') {
                    $(".side-wizard li[data-index='" + index + "'] a span i").html('0%')
                }
            }

        }
        cekBtn()
        cekWizard()

        $(".side-wizard li a").click(function() {
            var dataIndex = $(this).closest('li').data('index')
            if ($(this).closest('li').hasClass('active')) {
                $(".form-wizard").removeClass('active')
                $(".form-wizard[data-index='" + dataIndex + "']").addClass('active')
                cekWizard()
                cekBtn(true)
            }
        })

        function setPercentage(formIndex) {
            var form = ".form-wizard[data-index='" + formIndex + "']"
        }

        $(".btn-next").click(function(e) {
            e.preventDefault();
            var indexNow = $(".form-wizard.active").data('index')
            var next = parseInt(indexNow) + 1
            // \($(".form-wizard[data-index='"+next+"']").length==1);
            // console.log($(".form-wizard[data-index='"+  +"']"));
            if ($(".form-wizard[data-index='" + next + "']").length == 1) {
                // console.log(indexNow);
                $(".form-wizard").removeClass('active')
                $(".form-wizard[data-index='" + next + "']").addClass('active')
                $(".form-wizard[data-index='" + indexNow + "']").attr('data-done', 'true')
            }

            console.log(next);
            cekWizard()
            cekBtn()
            setPercentage(indexNow)
        })
        setPercentage(0)

        $(".btn-prev").click(function(e) {
            event.preventDefault(e);
            var indexNow = $(".form-wizard.active").data('index')
            var prev = parseInt(indexNow) - 1
            if ($(".form-wizard[data-index='" + prev + "']").length == 1) {
                $(".form-wizard").removeClass('active')
                $(".form-wizard[data-index='" + prev + "']").addClass('active')
            }
            cekWizard()
            cekBtn()
            e.preventDefault();
        })

        // Penyelia
        var skorPenyeliaInput1 = document.getElementsByClassName('skorPenyeliaInput1')
        for(var i = 0; i < skorPenyeliaInput1.length; i++) {
            skorPenyeliaInput1[i].addEventListener('wheel', function(event){
                if (event.deltaY < 0)
                {
                    var valueInput = parseInt(this.value)+1;
                    if (valueInput > 4) {
                        this.value = 4-1
                    }
                }
                else if (event.deltaY > 0)
                {
                    var valueInput = parseInt(this.value)-1;
                    if (valueInput<0) {
                        this.value=0+1
                    }
                }
            });
        }

        var skorPenyeliaInput2 = document.getElementsByClassName('skorPenyeliaInput2')
        for(var i = 0; i < skorPenyeliaInput2.length; i++) {
            skorPenyeliaInput2[i].addEventListener('wheel', function(event){
                if (event.deltaY < 0)
                {
                    var valueInput = parseInt(this.value)+1;
                    if (valueInput > 4) {
                        this.value = 4-1
                    }
                }
                else if (event.deltaY > 0)
                {
                    var valueInput = parseInt(this.value)-1;
                    if (valueInput<0) {
                        this.value=0+1
                    }
                }
            });
        }

        var skorPenyeliaInput3 = document.getElementsByClassName('skorPenyeliaInput3')
        for(var i = 0; i < skorPenyeliaInput3.length; i++) {
            skorPenyeliaInput3[i].addEventListener('wheel', function(event){
                if (event.deltaY < 0)
                {
                    var valueInput = parseInt(this.value)+1;
                    if (valueInput > 4) {
                        this.value = 4-1
                    }
                }
                else if (event.deltaY > 0)
                {
                    var valueInput = parseInt(this.value)-1;
                    if (valueInput<0) {
                        this.value=0+1
                    }
                }
            });
        }

        var skorPenyeliaInput4 = document.getElementsByClassName('skorPenyeliaInput4')
        for(var i = 0; i < skorPenyeliaInput4.length; i++) {
            skorPenyeliaInput4[i].addEventListener('wheel', function(event){
                if (event.deltaY < 0)
                {
                    var valueInput = parseInt(this.value)+1;
                    if (valueInput > 4) {
                        this.value = 4-1
                    }
                }
                else if (event.deltaY > 0)
                {
                    var valueInput = parseInt(this.value)-1;
                    if (valueInput<0) {
                        this.value=0+1
                    }
                }
            });
        }


    </script>
@endpush
