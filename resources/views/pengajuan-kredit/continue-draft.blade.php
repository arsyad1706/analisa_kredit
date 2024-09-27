@extends('layouts.template')

@php
$status = ['belum menikah', 'menikah', 'duda', 'janda'];

$sectors = ['perdagangan', 'perindustrian', 'dll'];

function rupiah($angka)
{
if ($angka != null || $angka != '') {
if (!is_numeric($angka)) {
return null;
} else {
if (!is_numeric($angka)) {
$hasil_rupiah = null;
} else {
$hasil_rupiah = number_format($angka, 0, ',', '.');
}
return $hasil_rupiah;
}
}
}

$dataIndex = match ($skema) {
'PKPJ' => 1,
'KKB' => 2,
'Talangan Umroh' => 1,
'Prokesra' => 1,
'Kusuma' => 1,
null => 1,
};
// dd($dataIndex);
@endphp

@section('content')
@include('components.notification')
@include('components.loadingPost')
<style>
    .form-wizard .sub label:not(.info) {
        font-weight: 400;
    }

    .form-wizard h4 {
        color: #1f1d62;
        font-weight: 600 !important;
        font-size: 20px;
        /* border-bottom: 1px solid #dc3545; */
    }

    .form-wizard h5 {
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

    span.filename {
        position: absolute;
        top: 40px;
        left: 139px;
        background: white;
    }

    .file-wrapper span.filename {
        top: 10px;
    }
</style>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <form action="{{ route('save-skema-kredit-draft', $duTemp->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">SKEMA KREDIT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="skema">Pilih Skema Kredit</label>
                    <select class="form-control" id="skema" name="skema">
                        <option value="">- Pilih Skema Kredit -</option>
                        <option value="PKPJ" {{$skema == 'PKPJ' ? 'selected' : ''}}>PKPJ</option>
                        <option value="KKB" {{$skema == 'KKB' ? 'selected' : ''}}>KKB</option>
                        <option value="Talangan Umroh" {{$skema == 'Talangan Umroh' ? 'selected' : ''}}>Talangan Umroh</option>
                        <option value="Prokesra" {{$skema == 'Prokesra' ? 'selected' : ''}}>Prokesra</option>
                        <option value="Kusuma" {{$skema == 'Kusuma' ? 'selected' : ''}}>Kusuma</option>
                    </select>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnSkema" onclick="$('#exampleModal').modal('hide')">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form id="pengajuan_kredit" action="{{ route('pengajuan-kredit.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id_nasabah" id="idCalonNasabah" value="{{ $duTemp?->id }}">
    <input type="hidden" name="progress" class="progress">

    <div class="form-wizard active" data-index='0' data-done='true' id="wizard-data-umum">
        <div class="row">
            {{-- Input hidden for Skema Kredit --}}
            <input type="hidden" name="skema_kredit" id="skema_kredit" @if ($skema !=null) value="{{ $skema ?? '' }}"
                @elseif($duTemp->skema_kredit != null) value="{{ $duTemp->skema_kredit ?? '' }}" @endif>

            <div class="form-group col-md-6">
                <label for="">Nama Lengkap</label>
                <input type="text" name="name" id="nama" class="form-control @error('name') is-invalid @enderror"
                    placeholder="Nama sesuai dengan KTP" value="{{ $duTemp?->nama ?? '' }}" required maxlength="255">
                @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="">{{ $itemSP->nama }}</label>
                <input type="hidden" name="id_item_file[{{ $itemSP->id }}]" value="{{ $itemSP->id }}" id="">
                <input type="file" name="upload_file[{{ $itemSP->id }}]" id="surat_permohonan"
                    data-id="{{ temporary($duTemp->id, $itemSP->id)?->id }}"
                    placeholder="Masukkan informasi {{ $itemSP->nama }}" class="form-control limit-size">
                <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
                <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemSP->id)?->opsi_text
                    }}</span>
            </div>
            <div class="form-group col-md-4">
                <label for="">Kabupaten</label>
                <select name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror select2"
                    id="kabupaten">
                    <option value="0">---Pilih Kabupaten----</option>
                    @foreach ($dataKabupaten as $item)
                    <option {{ $item->id == $duTemp?->id_kabupaten ?? '' ? 'selected' : '' }}
                        value="{{ $item->id }}">{{ $item->kabupaten }}</option>
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
                <select name="kec" id="kecamatan" class="form-control @error('kec') is-invalid @enderror  select2">
                    <option value="0">---Pilih Kecamatan----</option>
                </select>
                @error('kec')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="">Desa</label>
                <select name="desa" id="desa" class="form-control @error('desa') is-invalid @enderror select2">
                    <option value="0">---Pilih Desa----</option>
                </select>
                @error('desa')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Alamat Rumah</label>
                <textarea name="alamat_rumah" class="form-control @error('alamat_rumah') is-invalid @enderror"
                    maxlength="255" id="" cols="30" rows="4"
                    placeholder="Alamat Rumah disesuaikan dengan KTP">{{ $duTemp?->alamat_rumah ?? '' }}</textarea>
                @error('alamat_rumah')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                <hr>
            </div>
            <div class="form-group col-md-12">
                <label for="">Alamat Usaha</label>
                <textarea name="alamat_usaha" class="form-control @error('alamat_usaha') is-invalid @enderror"
                    maxlength="255" id="" cols="30" rows="4"
                    placeholder="Alamat Usaha">{{ $duTemp?->alamat_usaha ?? '' }}</textarea>
                @error('alamat_usaha')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="">Tempat Lahir</label>
                <input type="text" maxlength="255" name="tempat_lahir" id=""
                    class="form-control @error('tempat_lahir') is-invalid @enderror" placeholder="Tempat Lahir"
                    value="{{ $duTemp?->tempat_lahir ?? '' }}">
                @error('tempat_lahir')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="">Tanggal Lahir</label>
                <input type="text" name="tanggal_lahir" id=""
                    class="form-control datepicker @error('tanggal_lahir') is-invalid @enderror"
                    placeholder="dd-mm-yyyy" value="{{ $duTemp?->tanggal_lahir ?? '' }}">
                @error('tanggal_lahir')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="">Status</label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror select2">
                    <option value=""> --Pilih Status --</option>
                    @foreach ($status as $sts)
                    <option value="{{ $sts }}" {{ $sts==$duTemp?->status ?? '' ? 'selected' : null }}>
                        {{ ucfirst($sts) }}</option>
                    @endforeach
                </select>
                @error('alamat_rumah')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">No. KTP</label>
                <input type="number" maxlength="16"
                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                    name="no_ktp" class="form-control @error('no_ktp')
is-invalid
@enderror" id="" placeholder="Masukkan 16 digit No. KTP" value="{{ $duTemp?->no_ktp ?? '' }}">
                @error('no_ktp')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="" id="foto-ktp-suami">
            </div>
            <div class="" id="foto-ktp-istri">
            </div>
            <div class="" id="foto-ktp-nasabah">
            </div>
            <div class="form-group col-md-12">
                <label for="">Pekerjaan</label>
                <input type="text" maxlength="255" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror"
                    id="pekerjaan" placeholder="Masukkan Pekerjaan" value="{{ $duTemp?->pekerjaan ?? '' }}">
                @error('pekerjaan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Sektor Kredit</label>
                <select name="sektor_kredit" id=""
                    class="form-control @error('sektor_kredit') is-invalid @enderror select2">
                    <option value=""> --Pilih Sektor Kredit -- </option>
                    @foreach ($sectors as $sector)
                    <option value="{{ $sector }}" {{ $sector==$duTemp?->sektor_kredit ?? '' ? 'selected' : '' }}>{{
                        ucfirst($sector) }}
                    </option>
                    @endforeach
                </select>
                @error('sektor_kredit')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="">{{ $itemSlik->nama }}</label>
                <select name="dataLevelDua[{{ $itemSlik->id }}]" id="dataLevelDua" class="form-control select2"
                    data-id_item={{ $itemSlik->id }}>
                    <option value=""> --Pilih Data -- </option>
                    @foreach ($itemSlik->option as $itemJawaban)
                    <option value="{{ $itemJawaban->skor . '-' . $itemJawaban->id }}" {{ temporary_select($itemSlik->id,
                        $duTemp->id)?->id_jawaban == $itemJawaban->id ? 'selected' : '' }}>
                        {{ $itemJawaban->option }}</option>
                    @endforeach
                </select>
                <div id="item{{ $itemSlik->id }}">

                </div>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
            </div>
            <div class="form-group col-md-6">
                <label for="">{{ $itemP->nama }}</label>
                <input type="hidden" name="id_item_file[{{ $itemP->id }}]" value="{{ $itemP->id }}" id="">
                <input type="file" name="upload_file[{{ $itemP->id }}]" id="file_slik"
                    data-id="{{ temporary($duTemp->id, $itemP->id)?->id }}"
                    placeholder="Masukkan informasi {{ $itemP->nama }}" class="form-control limit-size">
                <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
                <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemP->id)?->opsi_text
                    }}</span>
                {{-- <span class="alert alert-danger">Maximum file upload is 5 MB</span> --}}
            </div>
            <div class="form-group col-md-12">
                <label for="">Jenis Usaha</label>
                <textarea name="jenis_usaha" class="form-control @error('jenis_usaha') is-invalid @enderror"
                    maxlength="255" id="" cols="30" rows="4"
                    placeholder="Jenis Usaha secara spesifik">{{ $duTemp?->jenis_usaha ?? '' }}</textarea>
                @error('jenis_usaha')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="">Jumlah Kredit yang diminta</label>
                <input type="text" name="jumlah_kredit" id="jumlah_kredit" class="form-control rupiah"
                    value="{{ rupiah($duTemp?->jumlah_kredit) ?? '' }}">
                {{-- <textarea name="jumlah_kredit" class="form-control @error('jumlah_kredit') is-invalid @enderror"
                    id="" cols="30" rows="4" placeholder="Jumlah Kredit"></textarea> --}}
                @error('jumlah_kredit')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="">Tenor Yang Diminta</label>
                {{-- <select name="tenor_yang_diminta" id="tenor_yang_diminta"
                    class="form-control select2 @error('tenor_yang_diminta') is-invalid @enderror" required>
                    <option value="">-- Pilih Tenor --</option>
                    @for ($i = 1; $i <= 10; $i++) <option value="{{ $i }}" {{ $i==$duTemp?->tenor_yang_diminta ?? '' ?
                        'selected' : '' }}> {{ $i . ' tahun' }}
                        </option>
                        @endfor
                </select> --}}
                <div class="input-group">
                    <input type="text" name="tenor_yang_diminta" id="tenor_yang_diminta"
                        class="form-control only-number @error('tenor_yang_diminta') is-invalid @enderror"
                        aria-describedby="addon_tenor_yang_diminta" value="{{ $duTemp?->tenor_yang_diminta }}" required
                        maxlength="3" />
                    <div class="input-group-append">
                        <div class="input-group-text" id="addon_tenor_yang_diminta">Bulan</div>
                    </div>
                </div>
                @error('tenor_yang_diminta')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Tujuan Kredit</label>
                <textarea name="tujuan_kredit" class="form-control @error('tujuan_kredit') is-invalid @enderror"
                    maxlength="255" id="" cols="30" rows="4"
                    placeholder="Tujuan Kredit">{{ $duTemp?->tujuan_kredit ?? '' }}</textarea>
                @error('tujuan_kredit')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Jaminan yang disediakan</label>
                <textarea name="jaminan" class="form-control @error('jaminan') is-invalid @enderror" maxlength="255"
                    id="" cols="30" rows="4"
                    placeholder="Jaminan yang disediakan">{{ $duTemp?->jaminan_kredit }}</textarea>
                @error('jaminan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Hubungan Bank</label>
                <textarea name="hubungan_bank" class="form-control @error('hubungan_bank') is-invalid @enderror"
                    maxlength="255" id="" cols="30" rows="4"
                    placeholder="Hubungan dengan Bank">{{ $duTemp?->hubungan_bank }}</textarea>
                @error('hubungan_bank')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Hasil Verifikasi</label>
                <textarea name="hasil_verifikasi" class="form-control @error('hasil_verifikasi') is-invalid @enderror"
                    maxlength="255" id="" cols="30" rows="4"
                    placeholder="Hasil Verifikasi Karakter Umum">{{ $duTemp?->verifikasi_umum }}</textarea>
                @error('hasil_verifikasi')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
    </div>

    @if ($skema == 'KKB' || $duTemp?->skema_kredit == 'KKB')
    <div class="form-wizard" data-index='1' data-done='true' id="wizard-data-po">
        <div class="row">
            <input type="hidden" name="id_data_po_temp" class="id-data-po-temp" id="id_data_po_temp" value="">
            <div class="form-group col-md-12">
                <span style="color: black; font-weight: bold; font-size: 18px;">Jenis Kendaraan Roda 2 :</span>
            </div>
            <div class="form-group col-md-6">
                <label>Merk Kendaraan</label>
                <input type="text" name="merk" id="merk" class="form-control @error('merk') is-invalid @enderror"
                    placeholder="Merk kendaraan" value="{{$dataPO?->merk}}">
                @error('merk')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                {{-- <select name="id_merk" id="id_merk" class="select2 form-control" style="width: 100%;" required>
                    <option value="">Pilih Merk Kendaraan</option>
                    @foreach ($dataMerk as $item)
                    <option value="{{ $item->id }}">{{ $item->merk }}</option>
                    @endforeach
                </select>
                @error('id_merk')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror --}}
            </div>
            <div class="form-group col-md-6">
                <label>Tipe Kendaraan</label>
                <input type="text" name="tipe_kendaraan" id="tipe_kendaraan"
                    class="form-control @error('tipe_kendaraan') is-invalid @enderror" placeholder="Tipe kendaraan"
                    value="{{$dataPO?->tipe}}">
                @error('tipe_kendaraan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                {{-- <select name="id_tipe" id="id_tipe" class="select2 form-control" style="width: 100%;" required>
                    <option value="">Pilih Tipe</option>
                </select>
                @error('id_tipe')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror --}}
            </div>
            <div class="form-group col-md-6">
                <label for="">Tahun</label>
                <input type="number" name="tahun" id="tahun" class="form-control @error('tahun') is-invalid @enderror"
                    placeholder="Tahun Kendaraan" value="{{ $dataPO?->tahun_kendaraan ?? '' }}" min="2000">
                @error('tahun')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="">Warna</label>
                <input type="text" maxlength="255" name="warna" id="warna"
                    class="form-control @error('warna') is-invalid @enderror" placeholder="Warna Kendaraan"
                    value="{{ $dataPO?->warna ?? '' }}">
                @error('warna')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <span style="color: black">Keterangan :</span>
            </div>
            <div class="form-group col-md-6">
                <label for="">Pemesanan</label>
                <input type="text" maxlength="255" name="pemesanan" id="pemesanan"
                    class="form-control @error('pemesanan') is-invalid @enderror" placeholder="Pemesanan Kendaraan"
                    value="{{ $dataPO?->keterangan ?? '' }}">
                @error('pemesanan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="">Sejumlah</label>
                <input type="number" name="sejumlah" id="sejumlah"
                    class="form-control @error('sejumlah') is-invalid @enderror" placeholder="Jumlah Kendaraan"
                    value="{{ $dataPO?->jumlah ?? '' }}">
                @error('sejumlah')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="">Harga</label>
                <input type="text" name="harga" id="harga"
                    class="form-control rupiah @error('harga') is-invalid @enderror" placeholder="Harga Kendaraan"
                    value="{{ $dataPO?->harga ?? '' }}">
                @error('harga')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
    </div>
    @endif

    <input type="text" id="jumlahData" name="jumlahData" hidden value="{{ count($dataAspek) + $dataIndex }}">

    @foreach ($dataAspek as $key => $value)
    @php

    $key += $dataIndex;
    // check level 2
    $dataLevelDua = \App\Models\ItemModel::select('id', 'nama', 'level', 'opsi_jawaban', 'id_parent')
    ->where('level', 2)
    ->where('id_parent', $value->id)
    ->get();
    // check level 4
    $dataLevelEmpat = \App\Models\ItemModel::select('id', 'nama', 'level', 'opsi_jawaban', 'id_parent')
    ->where('level', 4)
    ->where('id_parent', $value->id)
    ->get();
    @endphp
    {{-- level level 2 --}}
    <div class="form-wizard" data-index='{{ $key }}' data-done='true'>

        <div class="row">
            @foreach ($dataLevelDua as $item)
            @php
            $idLevelDua = str_replace(' ', '_', strtolower($item->nama));
            $nib = temporary($duTemp->id, 77)?->opsi_text;
            $sku = temporary($duTemp->id, 78)?->opsi_text;
            $npwp = temporary($duTemp->id, 79)?->opsi_text;
            @endphp
            {{-- item ijin usaha --}}
            @if ($item->nama == 'Ijin Usaha')
            <div class="row col-md-12">
                <div class="form-group col-md-6">
                    <label for="">{{ $item->nama }}</label>
                    <select name="ijin_usaha" id="ijin_usaha" class="form-control" required>
                        <option value="">-- Pilih Ijin Usaha --</option>
                        <option value="nib" {{ $nib !='' ? 'selected' : '' }}>NIB</option>
                        <option value="surat_keterangan_usaha" {{ $sku !='' ? 'selected' : '' }}>Surat
                            Keterangan Usaha</option>
                        <option value="tidak_ada_legalitas_usaha" {{ $nib=='' && $sku=='' && $npwp=='' ? 'selected' : ''
                            }}>
                            Tidak Ada Legalitas Usaha</option>
                    </select>
                </div>
                <div class="form-group col-md-6" id="npwpsku" style="display: {{ $npwp == '' ? '' : 'none' }}">
                    <label for="">Memiliki NPWP</label>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <input type="checkbox" name="isNpwp" id="isNpwp" class="form-control" @if ($npwp !=null)
                                checked @endif>
                            <input type="hidden" name="isNpwp" id="statusNpwp" class="form-control"
                                value="{{ $npwp != null ? '1' : '0' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row col-md-12">
                <div class="form-group col-md-6" id="nib">
                    <label for="">NIB</label>
                    <input type="hidden" name="id_level[77]" value="77" id="nib_id">
                    <input type="hidden" name="opsi_jawaban[77]" value="input text" id="nib_opsi_jawaban">
                    <input type="text" maxlength="255" name="informasi[77]" id="nib_text"
                        placeholder="Masukkan informasi" class="form-control"
                        value="{{ temporary($duTemp->id, 77)?->opsi_text }}">
                </div>

                <div class="form-group col-md-6" id="docNIB">
                    <label for="">{{ $itemNIB->nama }}</label>
                    <input type="hidden" name="id_item_file[{{ $itemNIB->id }}]" value="{{ $itemNIB->id }}"
                        id="docNIB_id">
                    <input type="file" name="upload_file[{{ $itemNIB->id }}]"
                        data-id="{{ temporary($duTemp->id, $itemNIB->id)?->id }}"
                        placeholder="Masukkan informasi {{ $itemNIB->nama }}" id="file_nib" class="form-control limit-size">
                    <span class="invalid-tooltip" style="display: none" id="docNIB_text">Besaran file
                        tidak boleh lebih dari 5 MB</span>
                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                    <div class="invalid-feedback">
                        {{ $errors->first('dataLevelTiga.' . $key) }}
                    </div>
                    @endif
                    <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemNIB->id)?->opsi_text
                        }}</span>
                </div>

                <div class="form-group col-md-6" id="surat_keterangan_usaha">
                    <label for="">Surat Keterangan Usaha</label>
                    <input type="hidden" name="id_level[78]" value="78" id="surat_keterangan_usaha_id">
                    <input type="hidden" name="opsi_jawaban[78]" value="input text"
                        id="surat_keterangan_usaha_opsi_jawaban">
                    <input type="text" maxlength="255" name="informasi[78]" id="surat_keterangan_usaha_text"
                        placeholder="Masukkan informasi" class="form-control">
                </div>

                <div class="form-group col-md-6" id="docSKU">
                    <label for="">{{ $itemSKU->nama }}</label>
                    <input type="hidden" name="id_item_file[{{ $itemSKU->id }}]" value="{{ $itemSKU->id }}"
                        id="docSKU_id">
                    <input type="file" name="upload_file[{{ $itemSKU->id }}]"
                        data-id="{{ temporary($duTemp->id, $itemSKU->id)?->id }}"
                        placeholder="Masukkan informasi {{ $itemSKU->nama }}" id="file_sku" class="form-control limit-size">
                    <span class="invalid-tooltip" style="display: none" id="docSKU_text">Besaran file
                        tidak boleh lebih dari 5 MB</span>
                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                    <div class="invalid-feedback">
                        {{ $errors->first('dataLevelTiga.' . $key) }}
                    </div>
                    @endif
                    <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemSKU->id)?->opsi_text
                        }}</span>
                </div>
            </div>
            @elseif($item->nama == 'NPWP')
            <div class="row col-md-12">
                <div class="form-group col-md-6" id="npwp">
                    <label for="">NPWP</label>
                    <input type="hidden" name="id_level[79]" value="79" id="npwp_id">
                    <input type="hidden" name="opsi_jawaban[79]" value="input text" id="npwp_opsi_jawaban">
                    <input type="text" maxlength="255" name="informasi[79]" id="npwp_text"
                        placeholder="Masukkan informasi" class="form-control"
                        value="{{ temporary($duTemp->id, 79)?->opsi_text }}">
                </div>

                <div class="form-group col-md-6" id="docNPWP">
                    <label for="">{{ $itemNPWP->nama }}</label>
                    <input type="hidden" name="id_item_file[{{ $itemNPWP->id }}]" value="{{ $itemNPWP->id }}"
                        id="docNPWP_id">
                    <input type="file" name="upload_file[{{ $itemNPWP->id }}]"
                        data-id="{{ temporary($duTemp->id, $itemNPWP->id)?->id }}"
                        placeholder="Masukkan informasi {{ $itemNPWP->nama }}" class="form-control limit-size"
                        id="docNPWP_upload_file">
                    <span class="invalid-tooltip" style="display: none" id="docNPWP_text">Besaran file
                        tidak boleh lebih dari 5 MB</span>
                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                    <div class="invalid-feedback">
                        {{ $errors->first('dataLevelTiga.' . $key) }}
                    </div>
                    @endif
                    <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemNPWP->id)?->opsi_text
                        }}</span>
                </div>
            </div>
            @else
            @if ($item->opsi_jawaban == 'input text')
            <div class="form-group col-md-6">
                <label for="">{{ $item->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                <input type="text" maxlength="255" name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                    placeholder="Masukkan informasi {{ $item->nama }}" class="form-control"
                    value="{{ temporary($duTemp->id, $item->id)?->opsi_text }}">
            </div>
            @elseif ($item->opsi_jawaban == 'number')
            @if ($item->nama == 'Repayment Capacity')
            <div class="form-group col-md-6">
                <label for="">{{ $item->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                <input type="text" maxlength="255" name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                    placeholder="Masukkan informasi {{ $item->nama }}" class="form-control"
                    value="{{ temporary($duTemp->id, $item->id)?->opsi_text }}">
            </div>
            @else
            @if ($item->nama == 'Omzet Penjualan' || $item->nama == 'Installment')
            <div class="form-group col-md-6">
                <label for="">{{ $item->nama }}(Perbulan)</label>
                <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                <input type="text" maxlength="255" step="any" name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                    placeholder="Masukkan informasi {{ $item->nama }}" class="form-control rupiah"
                    value="{{ rupiah(temporary($duTemp->id, $item->id)?->opsi_text) }}">
            </div>
            @else
            <div class="form-group col-md-6">
                <label for="">{{ $item->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                <input type="text" maxlength="255" step="any" name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                    placeholder="Masukkan informasi {{ $item->nama }}" class="form-control rupiah"
                    value="{{ rupiah(temporary($duTemp->id, $item->id)?->opsi_text) }}">
            </div>
            @endif
            @endif
            @elseif ($item->opsi_jawaban == 'persen')
            <div class="form-group col-md-6">
                <label for="">{{ $item->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                <div class="input-group mb-3">
                    <input type="number" step="any" name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                        placeholder="Masukkan informasi {{ $item->nama }}" class="form-control"
                        aria-label="Recipient's username" aria-describedby="basic-addon2"
                        value="{{ temporary($duTemp->id, $item->id)?->opsi_text }}">
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
            </div>
            @elseif ($item->opsi_jawaban == 'file')
            <div class="form-group col-md-6">
                <label for="">{{ $item->nama }}</label>
                {{-- <input type="hidden" name="opsi_jawaban[]" value="{{ $item->opsi_jawaban }}" --}} {{--
                    id="{{ $idLevelDua }}"> --}}
                <input type="hidden" name="id_item_file[{{ $item->id }}]" value="{{ $item->id }}" id="">
                <input type="file" name="upload_file[{{ $item->id }}]"
                    data-id="{{ temporary($duTemp->id, $item->id)?->id }}"
                    placeholder="Masukkan informasi {{ $item->nama }}" class="form-control limit-size"
                    id="{{ $idLevelDua }}">
                <span class="invalid-tooltip" style="display: none">Besaran file tidak
                    boleh lebih dari 5 MB</span>
                <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $item->id)?->opsi_text
                    }}</span>
            </div>
            @elseif ($item->opsi_jawaban == 'long text')
            <div class="form-group col-md-6">
                <label for="">{{ $item->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                <textarea name="informasi[{{ $item->id }}]" rows="4" id="{{ $idLevelDua }}" maxlength="255"
                    class="form-control"
                    placeholder="Masukkan informasi {{ $item->nama }}">{{ temporary($duTemp->id, $item->id)?->opsi_text }}</textarea>
            </div>
            @endif

            @php
            $dataJawaban = \App\Models\OptionModel::where('option', '!=', '-')
            ->where('id_item', $item->id)
            ->get();
            $dataOption = \App\Models\OptionModel::where('option', '=', '-')
            ->where('id_item', $item->id)
            ->get();
            // check level 3
            $dataLevelTiga = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent')
            ->where('level', 3)
            ->where('id_parent', $item->id)
            ->get();
            @endphp

            @foreach ($dataOption as $itemOption)
            @if ($itemOption->option == '-')
            <div class="form-group col-md-12">
                <h4>{{ $item->nama }}</h4>
            </div>
            @endif
            @endforeach

            @if (count($dataJawaban) != 0)
            {{-- <div class="form-group col-md-6">
                <label for="">{{ $item->nama }}</label>
                <select name="dataLevelDua[]" id="dataLevelDua" class="form-control cek-sub-column" data-id_item={{
                    $item->id }}>
                    <option value=""> --Pilih Data -- </option>
                    @foreach ($dataJawaban as $itemJawaban)
                    <option
                        value="{{ ($itemJawaban->skor == null ? 'kosong' : $itemJawaban->skor) . '-' . $itemJawaban->id }}">
                        {{ $itemJawaban->option }}</option>
                    @endforeach
                </select>
                <div id="item{{ $item->id }}">

                </div>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
            </div> --}}

            <div
                class="{{ $idLevelDua == 'persentase_kebutuhan_kredit_opsi' || $idLevelDua == 'repayment_capacity_opsi' ? '' : 'form-group col-md-6' }}">
                <label for="" id="{{ $idLevelDua . '_label' }}">{{ $item->nama }}</label>

                <select name="dataLevelDua[{{ $item->id }}]" id="{{ $idLevelDua }}" class="form-control cek-sub-column"
                    data-id_item={{ $item->id }}>
                    <option value=""> --Pilih Opsi-- </option>
                    @foreach ($dataJawaban as $key => $itemJawaban)
                    <option id="{{ $idLevelDua . '_' . $key }}"
                        value="{{ ($itemJawaban->skor == null ? 'kosong' : $itemJawaban->skor) . '-' . $itemJawaban->id }}"
                        {{ temporary_select($item->id, $duTemp->id)?->id_jawaban == $itemJawaban->id ? 'selected' : ''
                        }}>
                        {{ $itemJawaban->option }}</option>
                    @endforeach
                </select>
                <div id="item{{ $item->id }}">

                </div>
            </div>
            @endif

            @foreach ($dataLevelTiga as $keyTiga => $itemTiga)
            @php
            $idLevelTiga = str_replace(' ', '_', strtolower($itemTiga->nama));
            @endphp
            @if ($itemTiga->nama == 'Kategori Jaminan Utama')
            {{-- <div class="form-group col-md-6">
                <label for="">{{ $itemTiga->nama }}</label>
                <select name="kategori_jaminan_utama" id="kategori_jaminan_utama" class="form-control">
                    <option value="">-- Pilih Kategori Jaminan Utama --</option>
                    <option value="Tanah">Tanah</option>
                    <option value="Kendaraan Bermotor">Kendaraan Bermotor</option>
                    <option value="Tanah dan Bangunan">Tanah dan Bangunan</option>
                    <option value="Stock">Stock</option>
                    <option value="Piutang">Piutang</option>
                </select>
                {{-- <input type="hidden" name="id_level[]" value="{{ $itemTiga->id }}" id="">
                <input type="hidden" name="opsi_jawaban[]" value="{{ $itemTiga->opsi_jawaban }}" id="">
                <input type="text" name="informasi[]" id="" placeholder="Masukkan informasi" class="form-control">
            </div>

            <div class="form-group col-md-6" id="select_kategori_jaminan_utama">

            </div> --}}
            @elseif ($itemTiga->nama == 'Kategori Jaminan Tambahan')
            <div class="form-group col-md-6">
                <label for="">{{ $itemTiga->nama }}</label>
                <select name="kategori_jaminan_tambahan" id="kategori_jaminan_tambahan" class="form-control">
                    <option value="">-- Pilih Kategori Jaminan Tambahan --</option>
                    <option value="Tidak Memiliki Jaminan Tambahan" {{ $duTemp?->jaminan_tambahan == 'Tidak Memiliki
                        Jaminan Tambahan' ? 'selected' : '' }}>
                        Tidak Memiliki Jaminan Tambahan</option>
                    <option value="Tanah" {{ $duTemp?->jaminan_tambahan == 'Tanah' ? 'selected' : '' }}>Tanah
                    </option>
                    <option value="Kendaraan Bermotor" {{ $duTemp?->jaminan_tambahan == 'Kendaraan Bermotor' ?
                        'selected' : '' }}>
                        Kendaraan Bermotor</option>
                    <option value="Tanah dan Bangunan" {{ $duTemp?->jaminan_tambahan == 'Tanah dan Bangunan' ?
                        'selected' : '' }}>
                        Tanah dan Bangunan</option>
                </select>
                {{-- <input type="hidden" name="id_level[]" value="{{ $itemTiga->id }}" id="">
                <input type="hidden" name="opsi_jawaban[]" value="{{ $itemTiga->opsi_jawaban }}" id="">
                <input type="text" name="informasi[]" id="" placeholder="Masukkan informasi" class="form-control"> --}}
            </div>
            <div class="form-group col-md-6" id="select_kategori_jaminan_tambahan"></div>
            @elseif ($itemTiga->nama == 'Bukti Pemilikan Jaminan Utama')
            {{-- <div class="form-group col-md-12">
                <h5>{{ $itemTiga->nama }}</h5>
            </div>
            <div id="bukti_pemilikan_jaminan_utama" class="form-group col-md-12 row">

            </div> --}}
            @elseif ($itemTiga->nama == 'Bukti Pemilikan Jaminan Tambahan')
            <div class="form-group col-md-12" id="jaminan_tambahan">
                <h5>{{ $itemTiga->nama }}</h5>
            </div>
            <div id="bukti_pemilikan_jaminan_tambahan" class="form-group col-md-12 row">

            </div>
            @else
            @if ($itemTiga->opsi_jawaban == 'input text')
            <div class="form-group col-md-6">
                <label for="">{{ $itemTiga->nama }}</label>
                <input type="hidden" name="id_level[{{ $itemTiga->id }}]" value="{{ $itemTiga->id }}" id="">
                <input type="hidden" name="opsi_jawaban[{{ $itemTiga->id }}]" value="{{ $itemTiga->opsi_jawaban }}"
                    id="">
                <input type="text" maxlength="255" name="informasi[{{ $itemTiga->id }}]"
                    placeholder="Masukkan informasi" class="form-control" id="{{ $idLevelTiga }}"
                    value="{{ temporary($duTemp->id, $itemTiga->id)?->opsi_text }}">
            </div>
            @elseif ($itemTiga->opsi_jawaban == 'number')
            <div class="form-group col-md-6">
                <label for="">{{ $itemTiga->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $itemTiga->id }}]" value="{{ $itemTiga->opsi_jawaban }}"
                    id="">
                <input type="hidden" name="id_level[{{ $itemTiga->id }}]" value="{{ $itemTiga->id }}" id="">
                <input type="text" step="any" name="informasi[{{ $itemTiga->id }}]" id="{{ $idLevelTiga }}"
                    placeholder="Masukkan informasi {{ $itemTiga->nama }}" class="form-control rupiah"
                    value="{{ rupiah(temporary($duTemp->id, $itemTiga->id)?->opsi_text) }}">
            </div>
            @elseif ($itemTiga->opsi_jawaban == 'persen')
            <div class="form-group col-md-6">
                @if ($itemTiga->nama == 'Ratio Tenor Asuransi')
                @else
                <label for="">{{ $itemTiga->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $itemTiga->id }}]" value="{{ $itemTiga->opsi_jawaban }}"
                    id="">
                <input type="hidden" name="id_level[{{ $itemTiga->id }}]" value="{{ $itemTiga->id }}" id="">
                <div class="input-group mb-3">
                    <input type="number" step="any" name="informasi[{{ $itemTiga->id }}]" id="{{ $idLevelTiga }}"
                        placeholder="Masukkan informasi {{ $itemTiga->nama }}" class="form-control"
                        aria-label="Recipient's username" aria-describedby="basic-addon2"
                        value="{{ temporary($duTemp->id, $itemTiga->id)?->opsi_text }}">
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                @endif
            </div>
            @elseif ($itemTiga->opsi_jawaban == 'file')
            @forelse (temporary($duTemp->id, $itemTiga->id, true) as $tempData)
            <div class="form-group col-md-6 file-wrapper item-{{ $itemTiga->id }}">
                <label for="">{{ $itemTiga->nama }}</label>
                <div class="row file-input">
                    <div class="col-md-9">
                        <input type="hidden" name="id_item_file[{{ $itemTiga->id }}][]" value="{{ $itemTiga->id }}" id="">
                        <input type="file" name="upload_file[{{ $itemTiga->id }}][]" data-id="{{ $tempData->id }}"
                            placeholder="Masukkan informasi {{ $itemTiga->nama }}[]" class="form-control limit-size"
                            id="{{ $idLevelTiga }}">
                        <span class="invalid-tooltip" style="display: none">Besaran file
                            tidak boleh lebih dari 5 MB</span>
                        <span class="filename" style="display: inline;">{{ $tempData->opsi_text }}</span>
                    </div>
                    @if (in_array(trim($itemTiga->nama), $multipleFiles))
                    <div class="col-1">
                        <button class="btn btn-sm btn-success btn-add-file" type="button" data-id="{{ $itemTiga->id }}">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <div class="col-1">
                        <button class="btn btn-sm btn-danger btn-del-file" type="button" data-id="{{ $itemTiga->id }}">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="form-group col-md-6 file-wrapper item-{{ $itemTiga->id }}">
                <label for="">{{ $itemTiga->nama }}</label>
                <div class="row file-input">
                    <div class="col-md-9">
                        <input type="hidden" name="id_item_file[{{ $itemTiga->id }}][]" value="{{ $itemTiga->id }}" id="">
                        <input type="file" name="upload_file[{{ $itemTiga->id }}][]"
                            data-id="{{ temporary($duTemp->id, $itemTiga->id)?->id }}"
                            placeholder="Masukkan informasi {{ $itemTiga->nama }}" class="form-control limit-size">
                        <span class="invalid-tooltip" style="display: none">Besaran file
                            tidak boleh lebih dari 5 MB</span>
                        <span class="filename" style="display: inline;">{{ temporary($duTemp->id,
                            $itemTiga->id)?->opsi_text }}</span>
                    </div>
                    @if (in_array(trim($itemTiga->nama), $multipleFiles))
                    <div class="col-1">
                        <button class="btn btn-sm btn-success btn-add-file" type="button" data-id="{{ $itemTiga->id }}">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <div class="col-1">
                        <button class="btn btn-sm btn-danger btn-del-file" type="button" data-id="{{ $itemTiga->id }}">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @endforelse
            @elseif ($itemTiga->opsi_jawaban == 'long text')
            <div class="form-group col-md-6">
                <label for="">{{ $itemTiga->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $itemTiga->id }}]" value="{{ $itemTiga->opsi_jawaban }}"
                    id="">
                <input type="hidden" name="id_level[{{ $itemTiga->id }}]" value="{{ $itemTiga->id }}" id="">
                <textarea name="informasi[{{ $itemTiga->id }}]" rows="4" id="{{ $idLevelTiga }}" maxlength="255"
                    class="form-control"
                    placeholder="Masukkan informasi {{ $itemTiga->nama }}">{{ temporary($duTemp->id, $itemTiga->id)?->opsi_text }}</textarea>
            </div>
            @endif

            @php
            // check jawaban level tiga
            $dataJawabanLevelTiga = \App\Models\OptionModel::where('option', '!=', '-')
            ->where('id_item', $itemTiga->id)
            ->get();
            $dataOptionTiga = \App\Models\OptionModel::where('option', '=', '-')
            ->where('id_item', $itemTiga->id)
            ->get();
            // check level empat
            $dataLevelEmpat = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent')
            ->where('level', 4)
            ->where('id_parent', $itemTiga->id)
            ->get();
            @endphp

            @foreach ($dataOptionTiga as $itemOptionTiga)
            @if ($itemOptionTiga->option == '-')
            <div class="form-group col-md-12">
                <h5>{{ $itemTiga->nama }}</h5>
            </div>
            @endif
            @endforeach
            {{-- @foreach ($dataOptionEmpat as $itemOptionEmpat)
            @if ($itemOptionEmpat->option == '-')
            <div class="form-group col-md-12">
                <h5>{{ $itemTiga->nama }}</h5>
            </div>
            @endif
            @endforeach --}}
            @if (count($dataJawabanLevelTiga) != 0)
            @if ($itemTiga->nama != 'Pengikatan Jaminan Utama')
            <div
                class="{{ $idLevelTiga == 'ratio_tenor_asuransi_opsi' || $idLevelTiga == 'ratio_coverage_opsi' ? '' : 'form-group col-md-6' }}">
                <label for="" id="{{ $idLevelTiga . '_label' }}">{{ $itemTiga->nama }}</label>

                <select name="dataLevelTiga[{{ $itemTiga->id }}]" id="{{ $idLevelTiga }}"
                    class="form-control cek-sub-column" data-id_item={{ $itemTiga->id }}>
                    <option value=""> --Pilih Opsi-- </option>
                    @foreach ($dataJawabanLevelTiga as $key => $itemJawabanTiga)
                    <option id="{{ $idLevelTiga . '_' . $key }}"
                        value="{{ ($itemJawabanTiga->skor == null ? 'kosong' : $itemJawabanTiga->skor) . '-' . $itemJawabanTiga->id }}"
                        {{ temporary_select($itemTiga->id, $duTemp->id)?->id_jawaban == $itemJawabanTiga->id ?
                        'selected' : '' }}>
                        {{ $itemJawabanTiga->option }}</option>
                    @endforeach
                </select>
                <div id="item{{ $itemTiga->id }}">

                </div>
            </div>
            @endif
            @endif

            @foreach ($dataLevelEmpat as $keyEmpat => $itemEmpat)
            @php
            $idLevelEmpat = str_replace(' ', '_', strtolower($itemEmpat->nama));
            @endphp

            @if ($itemEmpat->opsi_jawaban == 'input text')
            <div class="form-group col-md-6">
                <label for="">{{ $itemEmpat->nama }}</label>
                <input type="hidden" name="id_level[{{ $itemEmpat->id }}]" value="{{ $itemEmpat->id }}" id="">
                <input type="hidden" name="opsi_jawaban[{{ $itemEmpat->id }}]" value="{{ $itemEmpat->opsi_jawaban }}"
                    id="">
                @if ($itemEmpat->nama == 'Masa Berlaku Asuransi Penjaminan')
                <div class="input-group">
                    <input type="text" maxlength="255" name="informasi[{{ $itemEmpat->id }}]"
                        id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan_/_ht' ? 'nilai_asuransi_penjaminan' : $idLevelEmpat }}"
                        placeholder="Masukkan informasi" class="form-control only-number"
                        value="{{ temporary($duTemp->id, $itemEmpat->id)?->opsi_text }}">
                    <div class="input-group-append">
                        <div class="input-group-text" id="addon_tenor_yang_diminta">Bulan</div>
                    </div>
                </div>
                @else
                <input type="text" maxlength="255" name="informasi[{{ $itemEmpat->id }}]"
                    id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan_/_ht' ? 'nilai_asuransi_penjaminan' : $idLevelEmpat }}"
                    placeholder="Masukkan informasi" class="form-control"
                    value="{{ temporary($duTemp->id, $itemEmpat->id)?->opsi_text }}">
                @endif
            </div>
            @elseif ($itemEmpat->opsi_jawaban == 'number')
            <div class="form-group col-md-6">
                <label for="">{{ $itemEmpat->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $itemEmpat->id }}]" value="{{ $itemEmpat->opsi_jawaban }}"
                    id="">
                <input type="hidden" name="id_level[{{ $itemEmpat->id }}]" value="{{ $itemEmpat->id }}" id="">
                <input type="text" step="any" name="informasi[{{ $itemEmpat->id }}]"
                    id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan_/_ht' ? 'nilai_asuransi_penjaminan' : $idLevelEmpat }}"
                    placeholder="Masukkan informasi {{ $itemEmpat->nama }}" class="form-control rupiah"
                    value="{{ rupiah(temporary($duTemp->id, $itemEmpat->id)?->opsi_text) }}">
            </div>
            @elseif ($itemEmpat->opsi_jawaban == 'persen')
            <div class="form-group col-md-6">
                <label for="">{{ $itemEmpat->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $itemEmpat->id }}]" value="{{ $itemEmpat->opsi_jawaban }}"
                    id="">
                <input type="hidden" name="id_level[{{ $itemEmpat->id }}]" value="{{ $itemEmpat->id }}" id="">
                <div class="input-group mb-3">
                    <input type="number" step="any" name="informasi[{{ $itemEmpat->id }}]" id="{{ $idLevelEmpat }}"
                        placeholder="Masukkan informasi {{ $itemEmpat->nama }}" class="form-control"
                        aria-label="Recipient's username" aria-describedby="basic-addon2"
                        value="{{ temporary($duTemp->id, $itemEmpat->id)?->opsi_text }}">
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
            </div>
            @elseif ($itemEmpat->opsi_jawaban == 'file')
            <div class="form-group col-md-6">
                <label for="">{{ $itemEmpat->nama }}</label>
                {{-- <input type="hidden" name="opsi_jawaban[]" value="{{ $itemEmpat->opsi_jawaban }}" id=""> --}}
                <input type="hidden" name="id_item_file[{{ $itemEmpat->id }}]" value="{{ $itemEmpat->id }}" id="">
                <input type="file" name="upload_file[{{ $itemEmpat->id }}]"
                    data-id="{{ temporary($duTemp->id, $itemEmpat->id)?->id }}"
                    placeholder="Masukkan informasi {{ $itemEmpat->nama }}" class="form-control limit-size"
                    id="{{ $idLevelEmpat }}">
                <span class="invalid-tooltip" style="display: none">Besaran file tidak
                    boleh lebih dari 5 MB</span>
                <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemEmpat->id)?->opsi_text
                    }}</span>
            </div>
            @elseif ($itemEmpat->opsi_jawaban == 'long text')
            <div class="form-group col-md-6">
                <label for="">{{ $itemEmpat->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $itemEmpat->id }}]" value="{{ $itemEmpat->opsi_jawaban }}"
                    id="">
                <input type="hidden" name="id_level[{{ $itemEmpat->id }}]" value="{{ $itemEmpat->id }}" id="">
                <textarea name="informasi[{{ $itemEmpat->id }}]" rows="4" id="{{ $idLevelEmpat }}" maxlength="255"
                    class="form-control" placeholder="Masukkan informasi {{ $itemEmpat->nama }}"></textarea>
            </div>
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

            @foreach ($dataOptionEmpat as $itemOptionEmpat)
            @if ($itemOptionEmpat->option == '-')
            <div class="form-group col-md-12">
                <h6>{{ $itemEmpat->nama }}</h6>
            </div>
            @endif
            @endforeach

            @if (count($dataJawabanLevelEmpat) != 0)
            <div class="form-group col-md-6">
                <label for="">{{ $itemEmpat->nama }}</label>
                <select name="dataLevelEmpat[{{ $itemEmpat->id }}]" id="{{ $idLevelEmpat }}"
                    class="form-control cek-sub-column" data-id_item={{ $itemEmpat->id }}>
                    <option value=""> --Pilih Opsi -- </option>
                    @foreach ($dataJawabanLevelEmpat as $itemJawabanEmpat)
                    <option id="{{ $idLevelEmpat . '_' . $key }}"
                        value="{{ ($itemJawabanEmpat->skor == null ? 'kosong' : $itemJawabanEmpat->skor) . '-' . $itemJawabanEmpat->id }}"
                        {{ temporary_select($itemEmpat->id, $duTemp->id)?->id_jawaban == $itemJawabanEmpat->id ?
                        'selected' : '' }}>
                        {{ $itemJawabanEmpat->option }}</option>
                    @endforeach
                </select>
                <div id="item{{ $itemEmpat->id }}">

                </div>
            </div>
            @endif
            @endforeach
            @endif
            @endforeach
            @endif
            @endforeach

            <div class="form-group col-md-12">
                <hr style="border: 0.2px solid #E3E6EA;">
                <label for="">Pendapat dan Usulan {{ $value->nama }}</label>
                <input type="hidden" name="id_aspek[{{ $value->id }}]" value="{{ $value->id }}">
                <textarea name="pendapat_per_aspek[{{ $value->id }}]"
                    class="form-control @error('pendapat_per_aspek') is-invalid @enderror" id="" maxlength="255"
                    cols="30" rows="4"
                    placeholder="Pendapat Per Aspek">{{ temporary_usulan($value->id, $duTemp->id)?->usulan }}</textarea>
                @error('pendapat_per_aspek')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

    </div>
    @endforeach
    {{-- pendapat dan usulan --}}
    <div class="form-wizard" data-index='{{ count($dataAspek) + $dataIndex }}' data-done='true'>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="">Pendapat dan Usulan</label>
                <textarea name="komentar_staff" class="form-control @error('komentar_staff') is-invalid @enderror"
                    maxlength="255" id="komentar_staff" cols="30" rows="4"
                    placeholder="Pendapat dan Usulan Staf/Analis Kredit"></textarea>
                @error('komentar_staff')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                <hr>
            </div>
        </div>
    </div>
    {{-- end pendapat dan usulan --}}
    <div class="row form-group">
        <div class="col text-right">
            <a href="{{ route('pengajuan-kredit-draft') }}">
                <button class="btn btn-warning" type="button"><span class="fa fa-arrow-left"></span>
                    Kembali</button>
            </a>
            <button class="btn btn-default btn-prev" type="button"><span class="fa fa-chevron-left"></span>
                Sebelumnya</button>
            <button class="btn btn-danger btn-next" type="button">Selanjutnya <span
                    class="fa fa-chevron-right"></span></button>
            <button type="submit" class="btn btn-info btn-simpan" id="submit">Simpan <span
                    class="fa fa-save"></span></button>
            {{-- <button class="btn btn-info ">Simpan <span class="fa fa-chevron-right"></span></button> --}}
        </div>
    </div>
</form>
@endsection

@push('custom-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let dataAspekArr;
    $(document).ready(function() {
        const skema = $('#skema_kredit').val()
        dataAspekArr = <?php echo json_encode($dataAspek); ?>;

        $('.side-wizard').on('click', function() {
            if (skema == 'KKB') {
                for (let index = 0; index < 9; index++) {
                    setPercentage(index);
                }
            }
            else {
                for (let index = 0; index < 8; index++) {
                    setPercentage(index);
                }
            }
        })
        var jumlahData = $('#jumlahData').val();

        let valSkema = $("#skema").val();
        cekStatusNikah()
        setPercentage(0);
        visibiltyInputNPWP()

        @if ($skema == null)
            if (valSkema == null || valSkema == '') {
                $('#exampleModal').modal('show');
            }
        @endif

        $("#exampleModal").on('click', "#btnSkema", function() {
            let valSkema = $("#skema").val();
            //console.log(valSkema);

            $("#skema_kredit").val(valSkema);
        });
    });

    function setPercentage(formIndex) {
        let valSkema = $("#skema").val();

        var form = ".form-wizard[data-index='" + formIndex + "']"
        var inputText = $(form + " .row input[type=text]")
        var inputNumber = $(form + " input[type=number]")
        var inputDisabled = $(form + " input:disabled")
        var inputReadonly = $(form + " input").find("readonly")
        var inputHidden = $(form + " input[type=hidden]")
        var inputFile = $(form + " input[type=file]");
        var inputFilename = $(form).find('.filename');
        var inputDate = $(form + " input[type=date]")
        var select = $(form + " select")
        var textarea = $(form + " textarea")
        var totalText = inputText ? inputText.length : 0
        var totalNumber = inputNumber ? inputNumber.length : 0
        var totalDisabled = inputDisabled ? inputDisabled.length : 0
        var totalReadonly = inputReadonly ? inputReadonly.length : 0
        var totalHidden = inputHidden ? inputHidden.length : 0
        var totalFile = inputFile ? inputFile.length : 0
        var totalDate = inputDate ? inputDate.length : 0
        var totalSelect = select ? select.length : 0
        var totalTextArea = textarea ? textarea.length : 0

        var subtotalInput = (totalText + totalNumber + totalFile + totalDate + totalSelect + totalTextArea)

        if (valSkema == "KKB") {
            if (formIndex == 3) {
                // var ijinUsahaSelect = $(form).find("#ijin_usaha");
                var ijinUsahaSelect = $("#ijin_usaha").val();
                console.log("length : " + ijinUsahaSelect);
                if (ijinUsahaSelect != "" || ijinUsahaSelect != null) {
                    if (ijinUsahaSelect == "nib") {
                        subtotalInput -= 2;
                    }
                    if (ijinUsahaSelect == "surat_keterangan_usaha") {
                        if ($("#isNpwp").is(":checked")) {
                            subtotalInput -= 2;
                        } else {
                            subtotalInput -= 4;
                        }
                    }
                    if (ijinUsahaSelect == "tidak_ada_legalitas_usaha") {
                        subtotalInput -= 6;
                    }
                }
            }
    
            if (formIndex == 4) {
                var jaminanTambSel = $("#kategori_jaminan_tambahan").val();
                if (jaminanTambSel == "Tanah dan Bangunan") {
                    subtotalInput -= 5;
                } else if (jaminanTambSel == "Tanah") {
                    subtotalInput -= 5;
                } else {
                    subtotalInput -= 2;
                }
            }
    
            if (formIndex == 7) {
                subtotalInput -= 1;
            }
        }
        else {
            if (formIndex == 2) {
                // var ijinUsahaSelect = $(form).find("#ijin_usaha");
                var ijinUsahaSelect = $("#ijin_usaha").val();
                console.log("length : " + ijinUsahaSelect);
                if (ijinUsahaSelect != "" || ijinUsahaSelect != null) {
                    if (ijinUsahaSelect == "nib") {
                        if ($("#statusNpwp").val() == 1) {
                            subtotalInput -= 2;
                        } else {
                            subtotalInput -= 4;
                        }
                    }
                    if (ijinUsahaSelect == "surat_keterangan_usaha") {
                        if ($("#statusNpwp").val() == 1) {
                            subtotalInput -= 2;
                        } else {
                            subtotalInput -= 4;
                        }
                    }
                    if (ijinUsahaSelect == "tidak_ada_legalitas_usaha") {
                        subtotalInput -= 6;
                    }
                }
            }
    
            if (formIndex == 3) {
                var jaminanTambSel = $("#kategori_jaminan_tambahan").val();
                if (jaminanTambSel == "Tanah dan Bangunan") {
                    subtotalInput -= 5;
                } else if (jaminanTambSel == "Tanah") {
                    subtotalInput -= 5;
                } else {
                    subtotalInput -= 2;
                }
            }
    
            if (formIndex == 6) {
                subtotalInput -= 1;
            }
        }

        var ttlInputTextFilled = 0;
        $.each(inputText, function(i, v) {
            if (v.value != '') {
                ttlInputTextFilled++
            }
        })
        var ttlInputNumberFilled = 0;
        $.each(inputNumber, function(i, v) {
            if (v.value != '') {
                ttlInputNumberFilled++
            }
        })
        var ttlInputFileFilled = 0;
        $.each(inputFile, function(i, v) {
            var filename = inputFilename[i].innerHTML
            //if (v.innerHTML != '') {
            if (v.value != "" || filename != "") {
                ttlInputFileFilled++
            }
        })
        var ttlInputDateFilled = 0;
        $.each(inputDate, function(i, v) {
            if (v.value != '') {
                ttlInputDateFilled++
            }
        })
        var ttlSelectFilled = 0;
        $.each(select, function(i, v) {
            /*var data = v.value;
            if (data != "" && data != '' && data != null && data != ' --Pilih Opsi-- ' && data !=
                '--Pilih Opsi--') {
                ttlSelectFilled++
            }*/
            var data = v.value;
            var displayValue = "";
            if (v.id != '')
                displayValue = $('#'+v.id).css('display');

            if (
                data != "" &&
                data != "" &&
                data != null &&
                !data.includes(" --Pilih Opsi-- ") &&
                !data.includes(" --Pilih Status --") &&
                !data.includes("-- Pilih Kategori Jaminan Tambahan --") &&
                !data.includes("---Pilih Kabupaten----") &&
                !data.includes("---Pilih Kecamatan----") &&
                !data.includes("---Pilih Desa----") &&
                displayValue != "" &&
                displayValue != "none"
            ) {
                ttlSelectFilled++;
            }
        })
        var ttlTextAreaFilled = 0;
        $.each(textarea, function(i, v) {
            if (v.value != '') {
                ttlTextAreaFilled++
            }
        })

        var subtotalFilled = ttlInputTextFilled + ttlInputNumberFilled + ttlInputFileFilled + ttlInputDateFilled +
            ttlSelectFilled + ttlTextAreaFilled;
        if (formIndex == 0) {
            let value = $("#status").val();
            //console.log('status : '+value)
            if (value == "menikah") {
                // subtotalInput += 2;
                subtotalFilled += 2;
            } else {
                // subtotalInput += 1;
                subtotalFilled += 2;
            }
        }
        // console.log("=============index : " + formIndex + "=============")
        // console.log('total input : ' + subtotalInput)
        // console.log('total input filled : ' + subtotalFilled)
        // console.log("===============================================")

        var percentage = parseInt(subtotalFilled / subtotalInput * 100);
        percentage = Number.isNaN(percentage) ? 0 : percentage;
        percentage = percentage > 100 ? 100 : percentage;
        percentage = percentage < 0 ? 0 : percentage;

        $(".side-wizard li[data-index='" + formIndex + "'] a span i").html(percentage + "%")
    }

    function cekStatusNikah() {
        let value = $("#status").val();
        $("#foto-ktp-istri").empty();
        $("#foto-ktp-suami").empty();
        $("#foto-ktp-nasabah").empty();
        $("#foto-ktp-istri").removeClass('form-group col-md-6');
        $("#foto-ktp-suami").removeClass('form-group col-md-6');
        $("#foto-ktp-nasabah").removeClass('form-group col-md-6');

        if (value == "menikah") {
            $("#foto-ktp-istri").addClass('form-group col-md-6')
            $("#foto-ktp-suami").addClass('form-group col-md-6')
            $("#foto-ktp-istri").append(`
        <label for="">{{ $itemKTPIs->nama }}</label>
            <input type="hidden" name="id_item_file[{{ $itemKTPIs->id }}]" value="{{ $itemKTPIs->id }}" id="">
            <input type="file" name="upload_file[{{ $itemKTPIs->id }}]" id="Foto_KTP_Istri" data-id="{{ temporary($duTemp->id, $itemKTPIs->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPIs->nama }}" class="form-control limit-size">
            <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
            @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
            @endif
            <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemKTPIs->id)?->opsi_text }}</span>
        `)
            $("#foto-ktp-suami").append(`
        <label for="">{{ $itemKTPSu->nama }}</label>
            <input type="hidden" name="id_item_file[{{ $itemKTPSu->id }}]" value="{{ $itemKTPSu->id }}" id="">
            <input type="file" name="upload_file[{{ $itemKTPSu->id }}]" id="Foto_KTP_Suami" data-id="{{ temporary($duTemp->id, $itemKTPSu->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPSu->nama }}" class="form-control limit-size">
            <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
            @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
            @endif
            <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemKTPSu->id)?->opsi_text }}</span>
        `);
        } else {
            $("#foto-ktp-nasabah").addClass('form-group col-md-12')
            $("#foto-ktp-nasabah").append(`
            <label for="">{{ $itemKTPNas->nama }}</label>
            <input type="hidden" name="id_item_file[{{ $itemKTPNas->id }}]" value="{{ $itemKTPNas->id }}" id="">
            <input type="file" name="upload_file[{{ $itemKTPNas->id }}]" data-id="{{ temporary($duTemp->id, $itemKTPNas->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPNas->nama }}" class="form-control limit-size">
            <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
            @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
            @endif
            <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemKTPNas->id)?->opsi_text }}</span>
        `)
        }
        // Limit Upload
        $('.limit-size').on('change', function() {
            var size = (this.files[0].size / 1024 / 1024).toFixed(2)
            if (size > 5) {
                $(this).next().css({
                    "display": "block"
                });
                this.value = ''
            } else {
                $(this).next().css({
                    "display": "none"
                });
            }
        })
    }
    $("#status").change(function() {
        let value = $(this).val();
        $("#foto-ktp-istri").empty();
        $("#foto-ktp-suami").empty();
        $("#foto-ktp-nasabah").empty();
        $("#foto-ktp-istri").removeClass('form-group col-md-6');
        $("#foto-ktp-suami").removeClass('form-group col-md-6');
        $("#foto-ktp-nasabah").removeClass('form-group col-md-6');

        if (value == "menikah") {
            $("#foto-ktp-istri").addClass('form-group col-md-6')
            $("#foto-ktp-suami").addClass('form-group col-md-6')
            $("#foto-ktp-istri").append(`
        <label for="">{{ $itemKTPIs->nama }}</label>
            <input type="hidden" name="id_item_file[{{ $itemKTPIs->id }}]" value="{{ $itemKTPIs->id }}" id="">
            <input type="file" name="upload_file[{{ $itemKTPIs->id }}]" id="Foto_KTP_Istri" data-id="{{ temporary($duTemp->id, $itemKTPIs->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPIs->nama }}" class="form-control limit-size">
            <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
            @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
            @endif
            <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemKTPIs->id)?->opsi_text }}</span>
        `)
            $("#foto-ktp-suami").append(`
        <label for="">{{ $itemKTPSu->nama }}</label>
            <input type="hidden" name="id_item_file[{{ $itemKTPSu->id }}]" value="{{ $itemKTPSu->id }}" id="">
            <input type="file" name="upload_file[{{ $itemKTPSu->id }}]" id="Foto_KTP_Suami" data-id="{{ temporary($duTemp->id, $itemKTPSu->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPSu->nama }}" class="form-control limit-size">
            <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
            @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
            @endif
            <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemKTPSu->id)?->opsi_text }}</span>
        `);
        } else {
            $("#foto-ktp-nasabah").addClass('form-group col-md-12')
            $("#foto-ktp-nasabah").append(`
            <label for="">{{ $itemKTPNas->nama }}</label>
            <input type="hidden" name="id_item_file[{{ $itemKTPNas->id }}]" value="{{ $itemKTPNas->id }}" id="">
            <input type="file" name="upload_file[{{ $itemKTPNas->id }}]" data-id="{{ temporary($duTemp->id, $itemKTPNas->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPNas->nama }}" class="form-control limit-size">
            <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
            @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
            @endif
            <span class="filename" style="display: inline;">{{ temporary($duTemp->id, $itemKTPNas->id)?->opsi_text }}</span>
        `)
        }

        // Limit Upload
        $('.limit-size').on('change', function() {
            var size = (this.files[0].size / 1024 / 1024).toFixed(2)
            if (size > 5) {
                $(this).next().css({
                    "display": "block"
                });
                this.value = ''
            } else {
                $(this).next().css({
                    "display": "none"
                });
            }
        })
    });

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
                        <option value="${value.id}">${value.tipe}</option>
                    `);
                    })
                }
            }
        })
    })

    @if ($nib != '')
        $('#docSKU').hide();
        $('#surat_keterangan_usaha').hide();
    @elseif ($sku != '')
        $('#nib').hide();
        $('#docNIB').hide();
    @elseif ($sku == '' && $npwp == '' && $nib == '')
        $('#docSKU').hide();
        $('#surat_keterangan_usaha').hide();
        $('#nib').hide();
        $('#docNIB').hide();
        $('#npwp').hide();
        $('#docNPWP').hide();
        $('#ijin_usaha').val('tidak_ada_legalitas_usaha')
    @endif
    $(window).on('load', () => {
        $("#ijin_usaha").trigger("change")
    })
    //make input readonly
    $('#ratio_coverage').attr('readonly', true);
    $('#ratio_tenor_asuransi').attr('readonly', true);
    $('#persentase_kebutuhan_kredit').attr('readonly', true);
    $('#repayment_capacity').attr('readonly', true);

    // make select option hidden
    $('#ratio_coverage_opsi_label').hide();
    $('#ratio_tenor_asuransi_opsi_label').hide();
    $('#ratio_coverage_opsi').hide();
    $('#ratio_tenor_asuransi_opsi').hide();
    $('#persentase_kebutuhan_kredit_opsi_label').hide();
    $('#persentase_kebutuhan_kredit_opsi').hide();
    $('#repayment_capacity_opsi_label').hide();
    $('#repayment_capacity_opsi').hide();
    $("#jaminan_tambahan").hide()
    var nullValue = []
    hitungRatioCoverage()
    hitungRepaymentCapacity()

    let urlCekSubColumn = "{{ route('cek-sub-column') }}";
    let urlGetItemByKategoriJaminanUtama =
        "{{ route('get-item-jaminan-by-kategori-jaminan-utama') }}"; // jaminan tambahan
    let urlGetItemByKategori = "{{ route('get-item-jaminan-by-kategori') }}"; // jaminan tambahan

    var x = 1;

    $('#kabupaten').change(function() {
        var kabID = $(this).val();
        if (kabID) {
            $.ajax({
                type: "GET",
                url: "{{ route('getKecamatan') }}?kabID=" + kabID,
                dataType: 'JSON',
                success: function(res) {
                    if (res) {
                        $("#kecamatan").empty();
                        $("#desa").empty();
                        $("#kecamatan").append('<option value="0">---Pilih Kecamatan---</option>');
                        $("#desa").append('<option value="0">---Pilih Desa---</option>');
                        $.each(res, function(nama, kode) {
                            $('#kecamatan').append(`
                            <option value="${kode}" ${kode == {{ $duTemp?->id_kecamatan ?? 'null' }} ? 'selected' : '' }>${nama}</option>
                        `);
                        });

                        $('#kecamatan').trigger('change');
                    } else {
                        $("#kecamatan").empty();
                        $("#desa").empty();
                    }
                }
            });
        } else {
            $("#kecamatan").empty();
            $("#desa").empty();
        }
    });

    $('#kecamatan').change(function() {
        var kecID = $(this).val();
        // //console.log(kecID);
        if (kecID) {
            $.ajax({
                type: "GET",
                url: "{{ route('getDesa') }}?kecID=" + kecID,
                dataType: 'JSON',
                success: function(res) {
                    //    //console.log(res);
                    if (res) {
                        $("#desa").empty();
                        $("#desa").append('<option value="0">---Pilih Desa---</option>');
                        $.each(res, function(nama, kode) {
                            $('#desa').append(`
                            <option value="${kode}" ${kode == {{ $duTemp?->id_desa ?? 'null' }} ? 'selected' : '' }>${nama}</option>
                        `);
                        });
                    } else {
                        $("#desa").empty();
                    }
                }
            });
        } else {
            $("#desa").empty();
        }
    });

    //cek apakah opsi yg dipilih memiliki sub column
    $('.cek-sub-column').change(function(e) {
        let idOption = $(this).val();
        let idItem = $(this).data('id_item');
        // cek option apakah ada turunan
        $(`#item${idItem}`).empty();
        $.ajax({
            type: "get",
            url: `${urlCekSubColumn}?idOption=${idOption}`,
            dataType: "json",
            success: function(response) {
                if (response.sub_column != null) {
                    $(`#item${idItem}`).append(`
                <div class="form-group sub mt-2">
                    <label for="">${response.sub_column}</label>
                    <input type="hidden" name="id_option_sub_column[]" value="${idOption}">
                    <input type="text" name="jawaban_sub_column[]" placeholder="Masukkan informasi tambahan" class="form-control">
                </div>
                `);
                } else {
                    $(`#item${idItem}`).empty();
                }
            }
        });
    });

    //item kategori jaminan utama cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan
    $('#kategori_jaminan_utama').change(function(e) {
        //clear item
        $('#select_kategori_jaminan_utama').empty();

        // clear bukti pemilikan
        $('#bukti_pemilikan_jaminan_utama').empty();

        //get item by kategori
        let kategoriJaminanUtama = $(this).val();

        $.ajax({
            type: "get",
            url: `${urlGetItemByKategoriJaminanUtama}?kategori=${kategoriJaminanUtama}`,
            dataType: "json",
            success: function(response) {
                // jika kategori bukan stock dan piutang
                if (kategoriJaminanUtama != 'Stock' && kategoriJaminanUtama != 'Piutang') {
                    // add item by kategori
                    $('#select_kategori_jaminan_utama').append(`
                    <label for="">${response.item.nama}</label>
                    <select name="dataLevelEmpat[]" id="itemByKategoriJaminanUtama" class="form-control cek-sub-column"
                        data-id_item="${response.item.id}">
                        <option value=""> --Pilih Opsi -- </option>
                        </select>

                    <div id="item${response.item.id}">

                    </div>
                `);
                    // add opsi dari item
                    $.each(response.item.option, function(i, valOption) {
                        // //console.log(valOption.skor);
                        $('#itemByKategoriJaminanUtama').append(`
                    <option value="${valOption.skor}-${valOption.id}">
                    ${valOption.option}
                    </option>`);
                    });

                    // add item bukti pemilikan
                    var isCheck = kategoriJaminanUtama != 'Kendaraan Bermotor' &&
                        kategoriJaminanUtama != 'Stock' && kategoriJaminanUtama != 'Piutang' ?
                        "<input type='checkbox' class='checkKategoriJaminanUtama'>" : ""
                    var isDisabled = kategoriJaminanUtama != 'Kendaraan Bermotor' &&
                        kategoriJaminanUtama != 'Stock' && kategoriJaminanUtama != 'Piutang' ?
                        'disabled' : ''
                    $.each(response.itemBuktiPemilikan, function(i, valItem) {
                        if (valItem.nama == 'Atas Nama') {
                            $('#bukti_pemilikan_jaminan_utama').append(`
                        <div class="form-group col-md-6 aspek_jaminan_kategori_jaminan_utama">
                            <label>${valItem.nama}</label>
                            <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                            <input type="hidden" name="opsi_jawaban[]"
                                value="${valItem.opsi_jawaban}" id="" class="input">
                            <input type="text" name="informasi[]" placeholder="Masukkan informasi"
                                class="form-control input">
                        </div>
                    `);
                            m
                        } else {
                            if (valItem.nama == 'Foto') {
                                $('#bukti_pemilikan_jaminan_utama').append(`
                            <div class="form-group col-md-6 aspek_jaminan_kategori_jaminan_utama">
                                <label>${valItem.nama}</label>
                                <input type="hidden" name="id_item_file[${valItem.id}]" value="${valItem.id}" id="" class="input">
                                <input type="file" name="upload_file[${valItem.id}]" data-id="" class="form-control limit-size">
                                <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                                <span class="filename" style="display: inline;"></span>
                            </div>`);
                            } else {
                                $('#bukti_pemilikan_jaminan_utama').append(`
                            <div class="form-group col-md-6 aspek_jaminan_kategori_jaminan_utama">
                                <label>${isCheck} ${valItem.nama}</label>
                                <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                <input type="hidden" name="opsi_jawaban[]"
                                    value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                <input type="text" name="informasi[]" placeholder="Masukkan informasi ${valItem.nama}"
                                    class="form-control input" ${isDisabled}>
                            </div>`);
                            }
                        }
                    });

                    $(".checkKategoriJaminanUtama").click(function() {
                        var input = $(this).closest('.form-group').find(".input")
                        // var input_id = $(this).closest('.form-group').find("input_id").last()
                        // var input_opsi_jawaban = $(this).closest('.form-group').find("input_opsi_jawaban").last()
                        if ($(this).is(':checked')) {
                            input.prop('disabled', false)
                            // input_id.prop('disabled',false)
                            // input_opsi_jawaban.prop('disabled',false)
                        } else {
                            input.val('')
                            input.prop('disabled', true)
                            // input_id.prop('disabled',true)
                            // input_opsi_jawaban.prop('disabled',true)
                        }
                    })
                }
                // jika kategori = stock dan piutang
                else {
                    $.each(response.itemBuktiPemilikan, function(i, valItem) {
                        if (valItem.nama == 'Atas Nama') {
                            $('#select_kategori_jaminan_utama').append(`
                        <div class="aspek_jaminan_kategori_jaminan_utama">
                            <label>${valItem.nama}</label>
                            <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                            <input type="hidden" name="opsi_jawaban[]"
                                value="${valItem.opsi_jawaban}" id="" class="input">
                            <input type="text" name="informasi[]" placeholder="Masukkan informasi"
                                class="form-control input">
                        </div>
                    `);
                        } else {
                            $('#select_kategori_jaminan_utama').append(`
                        <div class="aspek_jaminan_kategori_jaminan_utama">
                            <label>${valItem.nama}</label>
                            <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" >
                            <input type="hidden" name="opsi_jawaban[]"
                                value="${valItem.opsi_jawaban}" id="" class="input">
                            <input type="text" name="informasi[]" placeholder="Masukkan informasi ${valItem.nama}"
                                class="form-control input">
                        </div>
                    `);
                        }
                    });
                }
            }
        });
    });
    // end item kategori jaminan utama cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan

    //item kategori jaminan tambahan cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan
    $('#kategori_jaminan_tambahan').change(function(e) {
        //clear item
        $('#select_kategori_jaminan_tambahan').empty();

        // clear bukti pemilikan
        $('#bukti_pemilikan_jaminan_tambahan').empty();

        //get item by kategori
        let kategoriJaminan = $(this).val();

        $.ajax({
            type: "get",
            url: `${urlGetItemByKategori}?kategori=${kategoriJaminan}&idCalonNasabah={{ $duTemp?->id }}`,
            dataType: "json",
            success: function(response) {
                if (kategoriJaminan != "Tidak Memiliki Jaminan Tambahan") {
                    $("#select_kategori_jaminan_tambahan").show()
                    $("#jaminan_tambahan").show()
                    // add item by kategori
                    $('#select_kategori_jaminan_tambahan').append(`
                    <label for="">${response.item.nama}</label>
                    <select name="dataLevelEmpat[${response.item.id}]" id="itemByKategori" class="form-control cek-sub-column"
                        data-id_item="${response.item.id}">
                        <option value=""> --Pilih Opsi -- </option>
                        </select>

                    <div id="item${response.item.id}">

                    </div>
                `);
                    // add opsi dari item
                    $.each(response.item.option, function(i, valOption) {
                        // //console.log(valOption.skor);
                        $('#itemByKategori').append(`
                    <option value="${valOption.skor}-${valOption.id}" ${(response.dataSelect == valOption.id) ? 'selected' : ''}>
                    ${valOption.option}
                    </option>`);
                    });

                    // add item bukti pemilikan
                    var isCheck = kategoriJaminan != 'Kendaraan Bermotor' ?
                        "<input type='checkbox' class='checkKategori'>" : ""
                    var isDisabled = kategoriJaminan != 'Kendaraan Bermotor' ? 'disabled' : ''
                    $.each(response.itemBuktiPemilikan, function(i, valItem) {
                        //console.log('test');
                        if (valItem.nama == 'Atas Nama') {
                            $('#bukti_pemilikan_jaminan_tambahan').append(`
                            <div class="form-group col-md-6 aspek_jaminan_kategori">
                                <label>${valItem.nama}</label>
                                <input type="hidden" name="id_level[${valItem.id}]" value="${valItem.id}" id="" class="input">
                                <input type="hidden" name="opsi_jawaban[${valItem.id}]"
                                    value="${valItem.opsi_jawaban}" id="" class="input">
                                <input type="text" maxlength="255" id="${valItem.nama.toString().replaceAll(" ", "_")}" name="informasi[${valItem.id}]" placeholder="Masukkan informasi"
                                    class="form-control input" value="${response.dataJawaban[i]}">
                            </div>
                        `);
                        } else {
                            if (valItem.nama == 'Foto') {
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                            @forelse (temporary($duTemp->id, 148, true) as $tempData)
                            <div class="form-group col-md-6 file-wrapper item-${valItem.id}">
                                <label for="">${valItem.nama}</label>
                                <div class="row file-input">
                                    <div class="col-md-9">
                                        <input type="hidden" name="id_item_file[${valItem.id}][]" value="${valItem.id}" id="">
                                        <input type="file" name="upload_file[${valItem.id}][]" data-id="{{ $tempData->id }}"
                                            placeholder="Masukkan informasi ${valItem.nama}"
                                            class="form-control limit-size" id="${valItem.nama.toString().replaceAll(" ", "_").toLowerCase()}"
                                            value="{{$tempData->opsi_text}}">
                                            <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                                        <span class="filename" style="display: inline;">{{ $tempData->opsi_text }}</span>
                                    </div>
                                    <div class="col-1">
                                        <button class="btn btn-sm btn-success btn-add-file" type="button" data-id="${valItem.id}">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="col-1">
                                        <button class="btn btn-sm btn-danger btn-del-file" type="button" data-id="${valItem.id}">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="form-group col-md-6 file-wrapper item-${valItem.id}">
                                <label for="">${valItem.nama}</label>
                                <div class="row file-input">
                                    <div class="col-md-9">
                                        <input type="hidden" name="id_item_file[${valItem.id}][]" value="${valItem.id}" id="">
                                        <input type="file" name="upload_file[${valItem.id}][]" data-id=""
                                            placeholder="Masukkan informasi ${valItem.nama}"
                                            class="form-control limit-size" id="${valItem.nama.toString().replaceAll(" ", "_").toLowerCase()}">
                                            <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                                        <span class="filename" style="display: inline;"></span>
                                    </div>
                                    <div class="col-1">
                                        <button class="btn btn-sm btn-success btn-add-file" type="button" data-id="${valItem.id}">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="col-1">
                                        <button class="btn btn-sm btn-danger btn-del-file" type="button" data-id="${valItem.id}">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforelse
                            `);
                            } else {
                                if (response.dataJawaban[i] != null && response.dataJawaban[
                                        i] != "") {
                                    if (kategoriJaminan != 'Kendaraan Bermotor') {
                                        isCheck =
                                            "<input type='checkbox' class='checkKategori' checked>"
                                        isDisabled = ""
                                    }
                                } else {
                                    if (kategoriJaminan != 'Kendaraan Bermotor') {
                                        isCheck =
                                            "<input type='checkbox' class='checkKategori'>"
                                        isDisabled = "disabled"
                                    }
                                }
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                <div class="form-group col-md-6 aspek_jaminan_kategori">
                                    <label>${isCheck} ${valItem.nama}</label>
                                    <input type="hidden" name="id_level[${valItem.id}]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                    <input type="hidden" name="opsi_jawaban[${valItem.id}]"
                                        value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                    <input type="text" maxlength="255" id="${valItem.nama.toString().replaceAll(" ", "_")}" name="informasi[${valItem.id}]" placeholder="Masukkan informasi"
                                        class="form-control input" ${isDisabled} value="${response.dataJawaban[i]}">
                                </div>
                            `);
                            }
                        }
                    });

                    $(".checkKategori").click(function() {
                        var input = $(this).closest('.form-group').find(".input")
                        // var input_id = $(this).closest('.form-group').find("input_id").last()
                        // var input_opsi_jawaban = $(this).closest('.form-group').find("input_opsi_jawaban").last()
                        if ($(this).is(':checked')) {
                            input.prop('disabled', false)
                            // input_id.prop('disabled',false)
                            // input_opsi_jawaban.prop('disabled',false)
                        } else {
                            input.val('')
                            input.prop('disabled', true)
                            // input_id.prop('disabled',true)
                            // input_opsi_jawaban.prop('disabled',true)
                        }
                    })
                } else {
                    var skor = 0;
                    var opt = 0;
                    $('#select_kategori_jaminan_tambahan').append(`
                    <label for="">${response.item.nama}</label>
                    <select name="dataLevelEmpat[${response.item.id}]" id="itemByKategori" class="form-control cek-sub-column"
                        data-id_item="${response.item.id}">
                        <option value=""> --Pilih Opsi -- </option>
                        </select>

                    <div id="item${response.item.id}">

                    </div>
                `);
                    // add opsi dari item
                    $.each(response.item.option, function(i, valOption) {
                        var skor = valOption.skor;
                        var opt = valOption.id;
                        // //console.log(valOption.skor);
                        $('#itemByKategori').append(`
                    <option value="${valOption.skor}-${valOption.id}" selected>
                    ${valOption.option}
                    </option>`);
                    });
                    $("#itemByKategori").val(skor + '-' + opt)
                    $("#select_kategori_jaminan_tambahan").hide()
                    $("#jaminan_tambahan").hide()
                }
            }
        });
        // Limit Upload
        $('.limit-size').on('change', function() {
            var size = (this.files[0].size / 1024 / 1024).toFixed(2)
            if (size > 5) {
                $(this).next().css({
                    "display": "block"
                });
                this.value = ''
            } else {
                $(this).next().css({
                    "display": "none"
                });
            }
        })
    });
    // end item kategori jaminan tambahan cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan

    function visibiltyInputNPWP() {
        const ijinUsaha = $('#ijin_usaha').val();
        const checkboxNPWP = $('#isNpwp').is(':checked')
        if (checkboxNPWP) {
            if (ijinUsaha == '-- Pilih Ijin Usaha --') {
                $('#npwp').hide();
                $('#docNPWP').hide();
            }
            else if (ijinUsaha == 'nib' || ijinUsaha == 'surat_keterangan_usaha') {
                $('#npwp').show();
                $('#docNPWP').show();
            }
            else {
                $('#npwp').hide();
                $('#docNPWP').hide();
            }
        }
        else {
            $('#npwp').hide();
            $('#docNPWP').hide();
        }
    }
    // milih ijin usaha
    $('#ijin_usaha').change(function(e) {
        let ijinUsaha = $(this).val();
        $('#isNpwp').hide();
        $('#npwpsku').hide();
        $('#npwp').hide();
        $('#docNPWP').hide();
        if (ijinUsaha == 'nib') {
            $('#isNpwp').show();
            $('#npwpsku').show();
            $('#surat_keterangan_usaha').hide();
            $('#surat_keterangan_usaha_id').attr('disabled', true);
            $('#surat_keterangan_usaha_text').attr('disabled', true);
            $('#surat_keterangan_usaha_file').attr('disabled', true);
            $('#surat_keterangan_usaha_text').val("");
            $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);

            $('#docSKU').hide();
            $('#docSKU_id').attr('disabled', true);
            $('#docSKU_text').attr('disabled', true);
            $('#docSKU_upload_file').attr('disabled', true);

            $('#nib').show();
            $('#nib_id').removeAttr('disabled');
            $('#nib_text').removeAttr('disabled');
            $('#nib_text').val('{{ temporary($duTemp->id, 77)?->opsi_text }}');
            $('#nib_opsi_jawaban').removeAttr('disabled');

            $('#docNIB').show();
            $('#docNIB_id').removeAttr('disabled');
            $('#docNIB_text').removeAttr('disabled');
            $('#docNIB_upload_file').removeAttr('disabled');
            $('#file_nib').removeAttr('disabled');

            $('#npwp').show();
            $('#npwp_id').removeAttr('disabled');
            $('#npwp_text').removeAttr('disabled');
            $('#npwp_text').val('{{ temporary($duTemp->id, 79)?->opsi_text }}');
            $('#npwp_opsi_jawaban').removeAttr('disabled');
            $('#npwp_file').removeAttr('disabled');

            $('#docNPWP').show();
            $('#docNPWP_id').removeAttr('disabled');
            $('#docNPWP_text').removeAttr('disabled');
            $('#docNPWP_text').val('');
            $('#docNPWP_upload_file').removeAttr('disabled');
        } else if (ijinUsaha == 'surat_keterangan_usaha') {
            $('#isNpwp').show();
            $('#npwpsku').show();
            $('#npwp').show();
            $('#docNPWP').show();
            $('#nib').hide();
            $('#nib_id').attr('disabled', true);
            $('#nib_text').attr('disabled', true);
            $('#nib_text').val('');
            $('#nib_opsi_jawaban').attr('disabled', true);

            $('#docNIB').hide();
            $('#docNIB_id').attr('disabled', true);
            $('#docNIB_text').attr('disabled', true);
            $('#docNIB_text').val('');
            $('#docNIB_upload_file').attr('disabled', true);
            $('#nib_file').attr('disabled', true);
            $('#file_nib').attr('disabled', true);
            $('#docNIB_file').attr('disabled', true);

            $('#surat_keterangan_usaha').show();
            $('#surat_keterangan_usaha_id').removeAttr('disabled');
            $('#surat_keterangan_usaha_text').removeAttr('disabled');
            $('#surat_keterangan_usaha_text').val('{{ temporary($duTemp->id, 78)?->opsi_text }}');
            $('#surat_keterangan_usaha_opsi_jawaban').removeAttr('disabled');
            $('#surat_keterangan_usaha_file').removeAttr('disabled');

            $('#docSKU').show();
            $('#docSKU_id').removeAttr('disabled');
            $('#docSKU_text').removeAttr('disabled');
            $('#docSKU_upload_file').removeAttr('disabled');

            var cekNpwp = "{{ temporary($duTemp->id, 79)?->opsi_text }}";
            if (cekNpwp != '') {
                $('#npwp').show();
                $('#npwp_id').removeAttr('disabled');
                $('#npwp_text').removeAttr('disabled');
                $('#npwp_text').val('{{ temporary($duTemp->id, 79)?->opsi_text }}');
                $('#npwp_opsi_jawaban').removeAttr('disabled');
                $('#npwp_file').removeAttr('disabled');

                $('#docNPWP').show();
                $('#docNPWP_id').removeAttr('disabled');
                $('#docNPWP_text').removeAttr('disabled');
                $('#docNPWP_text').val('');
                $('#docNPWP_upload_file').removeAttr('disabled');
            } else {
                $('#npwp').hide();
                $('#npwp_id').attr('disabled', true);
                $('#npwp_text').attr('disabled', true);
                $('#npwp_file').attr('disabled', true);
                $('#npwp_text').val('');
                $('#npwp_opsi_jawaban').attr('disabled', true);

                $('#docNPWP').hide();
                $('#docNPWP_id').attr('disabled', true);
                $('#docNPWP_text').attr('disabled', true);
                $('#docNPWP_text').val('');
                $('#docNPWP_upload_file').attr('disabled', true);
            }

        } else if (ijinUsaha == 'tidak_ada_legalitas_usaha') {
            $('#isNpwp').hide();
            $('#npwpsku').hide();
            $('#npwp').hide();
            $('#docNPWP').hide();
            $('#nib').hide();
            $('#nib_id').attr('disabled', true);
            $('#nib_text').attr('disabled', true);
            $('#nib_text').val('');
            $('#nib_opsi_jawaban').attr('disabled', true);
            $('#file_nib').attr('disabled', true);

            $('#docNIB').hide();
            $('#docNIB_id').attr('disabled', true);
            $('#docNIB_text').attr('disabled', true);
            $('#docNIB_text').val('');
            $('#docNIB_file').attr('disabled', true);
            $('#docNIB_upload_file').attr('disabled', true);

            $('#surat_keterangan_usaha').hide();
            $('#surat_keterangan_usaha_id').attr('disabled', true);
            $('#surat_keterangan_usaha_text').attr('disabled', true);
            $('#surat_keterangan_usaha_text').val('');
            $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);
            $('#surat_keterangan_usaha_file').attr('disabled', true);

            $('#docSKU').hide();
            $('#docSKU_id').attr('disabled', true);
            $('#docSKU_text').attr('disabled', true);
            $('#docSKU_text').val('');
            $('#docSKU_upload_file').attr('disabled', true);

            $('#npwp').hide();
            $('#npwp_id').attr('disabled', true);
            $('#npwp_text').attr('disabled', true);
            $('#npwp_file').attr('disabled', true);
            $('#npwp_text').val('');
            $('#npwp_opsi_jawaban').attr('disabled', true);

            $('#docNPWP').hide();
            $('#docNPWP_id').attr('disabled', true);
            $('#docNPWP_text').attr('disabled', true);
            $('#docNPWP_text').val('');
            $('#docNPWP_upload_file').attr('disabled', true);
        } else {
            $('#isNpwp').hide();
            $('#npwpsku').hide();
            $('#npwp').hide();
            $('#docNPWP').hide();

            $('#nib').hide();
            $('#nib_id').attr('disabled', true);
            $('#nib_text').attr('disabled', true);
            $('#nib_text').val('');
            $('#nib_opsi_jawaban').attr('disabled', true);

            $('#docNIB').hide();
            $('#docNIB_id').attr('disabled', true);
            $('#docNIB_text').attr('disabled', true);
            $('#docNIB_text').val('');
            $('#docNIB_upload_file').attr('disabled', true);

            $('#surat_keterangan_usaha').hide();
            $('#surat_keterangan_usaha_id').attr('disabled', true);
            $('#surat_keterangan_usaha_text').attr('disabled', true);
            $('#surat_keterangan_usaha_text').val('');
            $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);

            $('#npwp').show();
            $('#npwp_id').removeAttr('disabled');
            $('#npwp_text').removeAttr('disabled');
            $('#npwp_text').val('{{ temporary($duTemp->id, 79)?->opsi_text }}');
            $('#npwp_opsi_jawaban').removeAttr('disabled');

            $('#docNPWP').show();
            $('#docNPWP_id').removeAttr('disabled');
            $('#docNPWP_text').removeAttr('disabled');
            $('#docNPWP_text').val('');
            $('#docNPWP_upload_file').removeAttr('disabled');
        }
        visibiltyInputNPWP()
    });
    // end milih ijin usaha


    // Cek Npwp
    $('#isNpwp').change(function() {
        //console.log($(this).is(':checked'));
        if ($(this).is(':checked')) {
            $("#statusNpwp").val('1');
            $('#npwp').show();
            $('#npwp_id').removeAttr('disabled', true);
            $('#npwp_text').removeAttr('disabled', true);
            $('#npwp_opsi_jawaban').removeAttr('disabled', true);

            $('#docNPWP').show();
            $('#docNPWP_id').removeAttr('disabled', true);
            $('#docNPWPnama_file').removeAttr('disabled', true);
            $('#docNPWP_upload_file').removeAttr('disabled', true);
            $('#docNPWP_text').removeAttr('disabled', true);
            $('#id_jawaban_npwp').removeAttr('disabled', true);
        } else {
            $("#statusNpwp").val('0');
            $('#npwp').hide();
            $('#npwp_id').attr('disabled', true);
            $('#npwp_text').attr('disabled', true);
            $('#npwp_opsi_jawaban').attr('disabled', true);

            $('#docNPWP').hide();
            $('#docNPWP_id').attr('disabled', true);
            $('#docNPWPnama_file').attr('disabled', true);
            $('#docNPWP_upload_file').attr('disabled', true);
            $('#docNPWP_text').attr('disabled', true);
            $('#id_jawaban_npwp').attr('disabled', true);
        }
    });

    //triger hitung ratio coverage
    $('#thls').change(function(e) {
        hitungRatioCoverage();
    });
    //end triger hitung ratio covarege

    //triger hitung ratio coverage
    $('#nilai_pertanggungan_asuransi').change(function(e) {
        hitungRatioCoverage();
    });
    //end triger hitung ratio covarege

    //triger hitung ratio coverage
    $('#jumlah_kredit').change(function(e) {
        hitungRatioCoverage();
    });
    //end triger hitung ratio covarege

    // hitung ratio covarege
    function hitungRatioCoverage() {
        let thls = $('#thls').val() ? parseInt($('#thls').val().split('.').join('')) : '';
        let nilaiAsuransi = $('#nilai_pertanggungan_asuransi').val() ? parseInt($('#nilai_pertanggungan_asuransi').val()
            .split('.').join('')) : '';
        let kreditYangDiminta = $('#jumlah_kredit').val() ? parseInt($('#jumlah_kredit').val().split('.').join('')) :
            '';

        let ratioCoverage = (thls + nilaiAsuransi) / kreditYangDiminta * 100; //cek rumus nya lagi
        $('#ratio_coverage').val(ratioCoverage);

        if (ratioCoverage >= 150) {
            $('#ratio_coverage_opsi_0').attr('selected', true);
            $('#ratio_coverage_opsi_1').removeAttr('selected');
            $('#ratio_coverage_opsi_2').removeAttr('selected');
            $('#ratio_coverage_opsi_3').removeAttr('selected');
        } else if (ratioCoverage >= 131 && ratioCoverage < 150) {
            $('#ratio_coverage_opsi_0').removeAttr('selected');
            $('#ratio_coverage_opsi_1').attr('selected', true);
            $('#ratio_coverage_opsi_2').removeAttr('selected');
            $('#ratio_coverage_opsi_3').removeAttr('selected');
        } else if (ratioCoverage >= 110 && ratioCoverage <= 130) {
            $('#ratio_coverage_opsi_0').removeAttr('selected');
            $('#ratio_coverage_opsi_1').removeAttr('selected');
            $('#ratio_coverage_opsi_2').attr('selected', true);
            $('#ratio_coverage_opsi_3').removeAttr('selected');
        } else if (ratioCoverage < 110 && !isNaN(ratioCoverage)) {
            $('#ratio_coverage_opsi_0').removeAttr('selected');
            $('#ratio_coverage_opsi_1').removeAttr('selected');
            $('#ratio_coverage_opsi_2').removeAttr('selected');
            $('#ratio_coverage_opsi_3').attr('selected', true);
        } else {
            $('#ratio_coverage_opsi_0').removeAttr('selected');
            $('#ratio_coverage_opsi_1').removeAttr('selected');
            $('#ratio_coverage_opsi_2').removeAttr('selected');
            $('#ratio_coverage_opsi_3').removeAttr('selected');
        }
    }
    //end hitung ratio covarege

    //triger hitung ratio Tenor Asuransi
    $('#masa_berlaku_asuransi_penjaminan').change(function(e) {
        hitungRatioTenorAsuransi();
    });
    //end triger hitung ratio Tenor Asuransi

    //triger hitung ratio Tenor Asuransi
    $('#tenor_yang_diminta').change(function(e) {
        hitungRatioTenorAsuransi();
    });
    //end triger hitung ratio Tenor Asuransi

    // hitung ratio Tenor Asuransi
    function hitungRatioTenorAsuransi() {
        let masaBerlakuAsuransi = parseInt($('#masa_berlaku_asuransi_penjaminan').val());
        let tenorYangDiminta = parseInt($('#tenor_yang_diminta').val());

        let ratioTenorAsuransi = parseInt(masaBerlakuAsuransi / tenorYangDiminta * 100); //cek rumusnya lagi

        $('#ratio_tenor_asuransi').val(ratioTenorAsuransi);

        if (ratioTenorAsuransi >= 200) {
            $('#ratio_tenor_asuransi_opsi_0').attr('selected', true);
            $('#ratio_tenor_asuransi_opsi_1').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_2').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_3').removeAttr('selected');
        } else if (ratioTenorAsuransi >= 150 && ratioTenorAsuransi < 200) {
            $('#ratio_tenor_asuransi_opsi_0').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_1').attr('selected', true);
            $('#ratio_tenor_asuransi_opsi_2').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_3').removeAttr('selected');
        } else if (ratioTenorAsuransi >= 100 && ratioTenorAsuransi < 150) {
            $('#ratio_tenor_asuransi_opsi_0').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_1').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_2').attr('selected', true);
            $('#ratio_tenor_asuransi_opsi_3').removeAttr('selected');
        } else if (ratioTenorAsuransi < 100 && !isNaN(ratioTenorAsuransi)) {
            $('#ratio_tenor_asuransi_opsi_0').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_1').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_2').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_3').attr('selected', true);
        } else {
            $('#ratio_tenor_asuransi_opsi_0').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_1').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_2').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_3').removeAttr('selected');
        }
    }
    //end hitung ratio covarege

    //triger hitung Repayment Capacity
    $('#persentase_net_income').change(function(e) {
        hitungRepaymentCapacity();
    });
    //end triger hitung Repayment Capacity

    //triger hitung Repayment Capacity
    $('#omzet_penjualan').change(function(e) {
        hitungRepaymentCapacity();
    });
    //end triger hitung Repayment Capacity

    //triger hitung Repayment Capacity
    $('#rencana_peningkatan').change(function(e) {
        hitungRepaymentCapacity();
    });
    //end triger hitung Repayment Capacity

    //triger hitung Repayment Capacity
    $('#installment').change(function(e) {
        hitungRepaymentCapacity();
    });
    //end triger hitung Repayment Capacity

    // hitung Repayment Capacity
    function hitungRepaymentCapacity() {
        let omzetPenjualan = parseInt($('#omzet_penjualan').val().split('.').join(''));
        let persentaseNetIncome = parseInt($('#persentase_net_income').val()) / 100;
        let rencanaPeningkatan = parseInt($('#rencana_peningkatan').val()) / 100;
        let installment = parseInt($('#installment').val().split('.').join(''));

        let repaymentCapacity = parseFloat(persentaseNetIncome * omzetPenjualan * (1 + rencanaPeningkatan) /
            installment); //cek rumusnya lagi

        $('#repayment_capacity').val(repaymentCapacity.toFixed(2));

        if (repaymentCapacity > 2) {
            $('#repayment_capacity_opsi_0').attr('selected', true);
            $('#repayment_capacity_opsi_1').removeAttr('selected');
            $('#repayment_capacity_opsi_2').removeAttr('selected');
            $('#repayment_capacity_opsi_3').removeAttr('selected');
        } else if (repaymentCapacity >= 1.5 && repaymentCapacity < 2) {
            $('#repayment_capacity_opsi_0').removeAttr('selected');
            $('#repayment_capacity_opsi_1').attr('selected', true);
            $('#repayment_capacity_opsi_2').removeAttr('selected');
            $('#repayment_capacity_opsi_3').removeAttr('selected');
        } else if (repaymentCapacity >= 1.25 && repaymentCapacity < 1.5) {
            $('#repayment_capacity_opsi_0').removeAttr('selected');
            $('#repayment_capacity_opsi_1').removeAttr('selected');
            $('#repayment_capacity_opsi_2').attr('selected', true);
            $('#repayment_capacity_opsi_3').removeAttr('selected');
        } else if (repaymentCapacity < 1.25 && !isNaN(repaymentCapacity)) {
            $('#repayment_capacity_opsi_0').removeAttr('selected');
            $('#repayment_capacity_opsi_1').removeAttr('selected');
            $('#repayment_capacity_opsi_2').removeAttr('selected');
            $('#repayment_capacity_opsi_3').attr('selected', true);
        } else {
            $('#repayment_capacity_opsi_0').removeAttr('selected');
            $('#repayment_capacity_opsi_1').removeAttr('selected');
            $('#repayment_capacity_opsi_2').removeAttr('selected');
            $('#repayment_capacity_opsi_3').removeAttr('selected');
        }
    }
    //end Repayment Capacity

    $('.rupiah').keyup(function(e) {
        var input = $(this).val()
        $(this).val(formatrupiah(input))
    });

    function formatrupiah(angka, prefix) {
        var number_string = angka ? angka.replace(/[^,\d]/g, '').toString() : angka,
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }
    // End Format Rupiah

    // Limit Upload
    $('.limit-size').on('change', function() {
        var size = (this.files[0].size / 1024 / 1024).toFixed(2)
        //console.log(size);
        if (size > 5) {
            $(this).next().css({
                "display": "block"
            });
            this.value = ''
        } else {
            $(this).next().css({
                "display": "none"
            });
        }
    })

    $('body').on('click', '.file-wrapper .btn-add-file', function(e) {
        const wrapper = $(this).parent().parent().parent();
        const $clone = wrapper.clone();

        $clone.find('[type="file"]').attr('data-id', '');
        $clone.find('[type="file"]').val('');
        $clone.find('.filename').html('');
        $clone.insertAfter(wrapper);
    });

    $('body').on('click', '.file-wrapper .btn-del-file', function(e) {
        const inputData = $(this).parent().parent().find('input[type="file"]');
        const wrapperEl = $(this).parent().parent().parent();

        $.ajax({
            url: '{{ route('pengajuan-kredit.temp.file') }}',
            method: 'DELETE',
            data: {
                answer_id: inputData.data('id'),
            },
            success: (res) => {
                inputData.parent().find('.filename').text('');
                inputData.val('');

                if (wrapperEl.siblings('.file-wrapper').get().length < 1) return;
                wrapperEl.remove();
            }
        });
    });
    // End Limit Upload

    @if (count($errors->all()))
        Swal.fire({
            icon: 'error',
            title: 'Error Validation',
            html: `
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        @foreach ($errors->all() as $error)
        <ul>
            <li>{{ $error }}</li>
        </ul>
        @endforeach
    </div>
    `
        });
    @endif

    function cekValueKosong(formIndex) {
        var skema = $("#skema_kredit").val()
        var form = ".form-wizard[data-index=" + formIndex + "]";
        var inputFile = $(form + " input[type=file]")
        var inputText = $(form + " input[type=text]")
        var inputNumber = $(form + " input[type=number]")
        var select = $(form + " select")
        var textarea = $(form + " textarea")

        /*$.each(inputFile, function(i, v) {
            if (v.value == '' && !$(this).prop('disabled') && $(this).closest('.filename') == '') {
                if (form == ".form-wizard[data-index='2']") {
                    var ijin = $(form + " select[name=ijin_usaha]")
                    if (ijin != "tidak_ada_legalitas_usaha") {
                        let val = $(this).attr("id");
                        if (val)
                            val = $(this).attr("id").toString()
                        nullValue.push(val.replaceAll("_", " "))
                    }
                } else {
                    let val = $(this).attr("id");
                    if (val)
                        val = $(this).attr("id").toString();
                    nullValue.push(val.replaceAll("_", " "))
                }
            } else if (v.value != '') {
                let val = $(this).attr("id");
                if (val)
                    val = $(this).attr("id").toString().replaceAll("_", " ");
                for (var i = 0; i < nullValue.length; i++) {
                    while (nullValue[i] == val) {
                        nullValue.splice(i, 1)
                    }
                }
            }
        })*/

        $.each(inputText, function(i, v) {
            if (v.value == '' && !$(this).prop('disabled')) {
                let val = $(this).attr("id");
                if (val)
                    val = $(this).attr("id").toString();
                //console.log(val)
                nullValue.push(val.replaceAll("_", " "))
            } else if (v.value != '') {
                let val = $(this).attr("id");
                if (val)
                    val = $(this).attr("id").toString().replaceAll("_", " ");
                for (var i = 0; i < nullValue.length; i++) {
                    while (nullValue[i] == val) {
                        nullValue.splice(i, 1)
                    }
                }
            }
        })

        $.each(inputNumber, function(i, v) {
            if (v.value == '' && !$(this).prop('disabled')) {
                let val = $(this).attr("id");
                if (val)
                    val = $(this).attr("id").toString();
                //console.log(val)
                nullValue.push(val.replaceAll("_", " "))
            } else if (v.value != '') {
                let val = $(this).attr("id").toString().replaceAll("_", " ");
                for (var i = 0; i < nullValue.length; i++) {
                    while (nullValue[i] == val) {
                        nullValue.splice(i, 1)
                    }
                }
            }
        })

        $.each(select, function(i, v) {
            if (v.value == '' && !$(this).prop('disabled')) {
                let val = $(this).attr("id");
                if (val)
                    val = $(this).attr("id").toString();
                if (val != "persentase_kebutuhan_kredit_opsi" && val != "ratio_tenor_asuransi_opsi" && val !=
                    "ratio_coverage_opsi") {
                    //console.log(val)
                    nullValue.push(val.replaceAll("_", " "))
                }
            } else if (v.value != '') {
                let val = $(this).attr("id");
                if (val)
                    val = $(this).attr("id").toString().replaceAll("_", " ");
                for (var i = 0; i < nullValue.length; i++) {
                    while (nullValue[i] == val) {
                        nullValue.splice(i, 1)
                    }
                }
            }
        })

        $.each(textarea, function(i, v) {
            if (v.value == '' && !$(this).prop('disabled')) {
                let val = $(this).attr("id");
                if (val)
                    val = $(this).attr("id").toString();
                //console.log(val)
                nullValue.push(val.replaceAll("_", " "))
            } else if (v.value != '') {
                let val = $(this).attr("id");
                if (val)
                    val = $(this).attr("id").toString().replaceAll("_", " ");
                for (var i = 0; i < nullValue.length; i++) {
                    while (nullValue[i] == val) {
                        nullValue.splice(i, 1)
                    }
                }
            }
        })

        //console.log(nullValue);
    }

    $(".btn-simpan").on('click', function(e) {
        if ($('#komentar_staff').val() == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "Field Pendapat dan usulan harus diisi"
            })
            e.preventDefault()
        }
        else {
            nullValue = []
            for (var i = 0; i < dataAspekArr.length; i++) {
                cekValueKosong(i)
            }
            const ijinUsaha = $("#ijin_usaha").val();

            // cek input files
            var inputFiles = $('input[type=file]')
            var fileEmpty = [];
            $.each(inputFiles, function(i, v) {
                var ids = $(this).attr("id");
                var inputId = ids ? ids.toString() : ''
                inputId = inputId.replaceAll('_', ' ').toLowerCase();
                if (inputId != '') {
                    // check span filename
                    var spanFile = $('#'+ids).parent().find('.filename');
                    const filename = spanFile.html();
                    if (inputId == "file nib" || inputId == "file sku" || inputId == "docnpwp upload file") {
                        if (ijinUsaha && ijinUsaha != 'tidak_ada_legalitas_usaha') {
                            if (ijinUsaha == "nib") {
                                if (inputId == "file nib") {
                                    if ((v.value == '' || v.value == null) && (filename == '' || filename == null)) {
                                        if (!fileEmpty.includes(inputId))
                                            fileEmpty.push(inputId)
                                    }
                                }
                            }
                            else if (ijinUsaha == "surat_keterangan_usaha") {
                                if (inputId == "docnpwp upload file") {
                                    const isCheckNpwp = $('#isNpwp').is(':checked')
                                    if (isCheckNpwp) {
                                        if ((v.value == '' || v.value == null) && (filename == '' || filename == null)) {
                                            if (!fileEmpty.includes(inputId))
                                                fileEmpty.push(inputId)
                                        }
                                    }
                                }
        
                                if (inputId == "file sku") {
                                    if ((v.value == '' || v.value == null) && (filename == '' || filename == null)) {
                                        if (!fileEmpty.includes(inputId))
                                            fileEmpty.push(inputId)
                                    }
                                }
                            }
                        }
                    }
                    else {
                        if ((v.value == '' || v.value == null) && (filename == '' || filename == null)) {
                            if (inputId == 'foto sp')
                                inputId = 'surat permohonan';
                            if (inputId == 'docnpwp upload file')
                                inputId = 'npwp'
                            if (!fileEmpty.includes(inputId))
                                fileEmpty.push(inputId)
                        }
                    }
                }
            })
            // end cek input file

            if (nullValue.length > 0 || fileEmpty.length > 0) {
                let message = "";
                $.each(nullValue, (i, v) => {
                    var item = v;
                    if (v == 'dataLevelDua')
                        item = 'slik';

                    if (v == 'itemByKategori')
                        item = 'jaminan tambahan';

                    if (v == 'itemByKategori'){
                        if($("#kategori_jaminan_tambahan").val() == "Tidak Memiliki Jaminan Tambahan"){
                            for(var j = 0; j < nullValue.length; j++){
                                while(nullValue[j] == v){
                                    nullValue.splice(j, 1)
                                }
                            }
                        } else {
                            item = "Jaminan Tambahan"
                        }
                    }
                    if (v == 'npwp text') {
                        if ($("#statusNpwp").val() != "1") {
                            for(var j = 0; j < nullValue.length; j++){
                                while(nullValue[j] == v){
                                    nullValue.splice(j, 1)
                                }
                            }
                        }
                    }

                    if (v == 'undifined') {
                        for(var j = 0; j < nullValue.length; j++){
                            while(nullValue[j] == v){
                                nullValue.splice(j, 1)
                            }
                        }
                    }

                    if (item.includes('text'))
                        item = item.replaceAll('text', '');

                    message += item != '' ? `<li class="text-left">Field `+item +` harus diisi.</li>` : ''
                })
                for (var i = 0; i < fileEmpty.length; i++) {
                    var msgItem = fileEmpty[i].includes('file') ? fileEmpty[i].replaceAll('file', '') : fileEmpty[i];
                    message += `<li class="text-left">File `+msgItem+` harus diisi.</li>`;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: '<ul>'+message+'</ul>'
                })
                nullValue = [];
                fileEmpty = [];
                e.preventDefault()
            }
            else {
                $("#loadingModal").modal({
                    keyboard: false
                });
                $("#loadingModal").modal("show");
            }
        }
    })

    for (let i = 0; i <= parseInt(jumlahData); i++) {
        cekValueKosong(i);
    }
</script>
@include('pengajuan-kredit.partials.save-script')
<script src="{{ asset('') }}js/custom.js"></script>
@endpush
