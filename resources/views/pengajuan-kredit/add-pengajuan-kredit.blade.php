@extends('layouts.template')

@php
$status = ['belum menikah', 'menikah', 'duda', 'janda'];

$sectors = ['perdagangan', 'perindustrian', 'dll'];

function rupiah($angka)
{
if ($angka != null || $angka != '') {
$hasil_rupiah = number_format($angka, 0, ',', '.');
return $hasil_rupiah;
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
@include('layouts.popup')
@include('pengajuan-kredit.modal.notifPengajuanTolak')
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

    .result-aspek-keuangan {
        display: none;
    }
</style>

{{-- <div class="modal fade" id="loading-simpan-perhitungan" aria-labelledby="modelTitleId" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="2" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="display: table; position: relative; margin: 0 auto; top: calc(50% - 24px);">
        <div class="modal-content" style="width: 48px; background-color: transparent; border: none;">
            <span class="fa fa-spinner fa-spin fa-3x"></span>
        </div>
    </div>
</div> --}}

<form id="pengajuan_kredit" action="{{ route('pengajuan-kredit.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id_nasabah" value="" id="id_nasabah">
    <input type="hidden" name="max_kredit" value="{{ $maxKredit != null ? $maxKredit->to : null }}" id="max_kredit">
    <input type="hidden" name="progress" class="progress">

    <div class="form-wizard active" data-index='0' data-done='true' id="wizard-data-umum">
        <div class="row">
            {{-- Input hidden for Produk Kredit --}}
            <input type="hidden" name="skema_kredit" id="skema_kredit" @if($skema != null) value="{{ $skema }}" @endif>
            <input type="hidden" name="produk_kredit_id" id="produk_kredit" @if ($produk !=null) value="{{ $produk ?? '' }}" @endif>
            <input type="hidden" name="skema_kredit_id" @if ($skema != null) value="{{ $skemaId }}" @endif>
            <input type="hidden" name="skema_limit_id" @if ($limit != null) value="{{ $limit }}" @endif>

            <div class="form-group col-md-6">
                <label for="">Nama Lengkap</label>
                <input type="text" name="name" id="nama" class="form-control @error('name') is-invalid @enderror"
                    placeholder="Nama sesuai dengan KTP" value="" required maxlength="255">
                @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="">{{ $itemSP->nama }}</label>
                <input type="hidden" name="id_item_file[{{ $itemSP->id }}]" value="{{ $itemSP->id }}" id="">
                <input type="file" name="upload_file[{{ $itemSP->id }}]" data-id=""
                    placeholder="Masukkan informasi {{ $itemSP->nama }}" class="form-control limit-size" id="foto_sp">
                <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 10 MB</span>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
                <span class="filename" style="display: inline;"></span>
            </div>
            <div class="form-group col-md-4">
                <label for="">Kabupaten</label>
                <select name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror select2"
                    id="kabupaten">
                    <option value="0">---Pilih Kabupaten----</option>
                    @foreach ($dataKabupaten as $item)
                    <option value="{{ $item->id }}">{{ $item->kabupaten }}</option>
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
                    maxlength="255" id="alamat_rumah" cols="30" rows="4"
                    placeholder="Alamat Rumah disesuaikan dengan KTP"></textarea>
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
                    maxlength="255" id="alamat_usaha" cols="30" rows="4" placeholder="Alamat Usaha"></textarea>
                @error('alamat_usaha')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="">Tempat Lahir</label>
                <input type="text" maxlength="255" name="tempat_lahir" id="tempat_lahir"
                    class="form-control @error('tempat_lahir') is-invalid @enderror" placeholder="Tempat Lahir"
                    value="">
                @error('tempat_lahir')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="">Tanggal Lahir</label>
                <input type="text" name="tanggal_lahir" id="tanggal_lahir"
                    class="form-control datepicker @error('tanggal_lahir') is-invalid @enderror"
                    placeholder="dd-mm-yyyy" value="">
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
                    <option value="{{ $sts }}">{{ ucfirst($sts) }}</option>
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
                    onkeydown="return event.keyCode !== 69" name="no_ktp" class="form-control @error('no_ktp') is-invalid @enderror"
                    id="no_ktp" placeholder="Masukkan 16 digit No. KTP" value="">
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
                <label for="">Sektor Kredit</label>
                <select name="sektor_kredit" id="sektor_kredit"
                    class="form-control @error('sektor_kredit') is-invalid @enderror select2">
                    <option value=""> --Pilih Sektor Kredit -- </option>
                    @foreach ($sectors as $sector)
                    <option value="{{ $sector }}">{{ ucfirst($sector) }}</option>
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
                    <option value="{{ $itemJawaban->skor . '-' . $itemJawaban->id }}">
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
                <input type="file" name="upload_file[{{ $itemP->id }}]" id="file_slik" data-id=""
                    placeholder="Masukkan informasi {{ $itemP->nama }}" class="form-control limit-size-slik">
                <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 10 MB</span>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
                <span class="filename" style="display: inline;"></span>
                {{-- <span class="alert alert-danger">Maximum file upload is 5 MB</span> --}}
            </div>
            <div class="form-group col-md-12">
                <label for="">Jenis Usaha</label>
                <textarea name="jenis_usaha" class="form-control @error('jenis_usaha') is-invalid @enderror"
                    maxlength="255" id="" cols="30" rows="4" placeholder="Jenis Usaha secara spesifik"></textarea>
                @error('jenis_usaha')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="">Jumlah Kredit yang diminta</label>
                <input type="text" name="jumlah_kredit" id="jumlah_kredit" class="form-control rupiah" value="">
                {{-- <textarea name="jumlah_kredit" class="form-control @error('jumlah_kredit') is-invalid @enderror"
                    id="" cols="30" rows="4" placeholder="Jumlah Kredit"></textarea> --}}
                @error('jumlah_kredit')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                <div class="info_jumlah_kredit_limit"></div>
            </div>
            <div class="form-group col-md-6">
                <label for="">Tenor Yang Diminta</label>
                {{-- <select name="tenor_yang_diminta" id="tenor_yang_diminta"
                    class="form-control select2 @error('tenor_yang_diminta') is-invalid @enderror" required>
                    <option value="">-- Pilih Tenor --</option>
                    @for ($i = 1; $i <= 10; $i++) <option value="{{ $i }}"> {{ $i . ' tahun' }} </option>
                        @endfor
                </select> --}}
                <div class="input-group">
                    <input type="text" name="tenor_yang_diminta" id="tenor_yang_diminta"
                        class="form-control only-number @error('tenor_yang_diminta') is-invalid @enderror"
                        aria-describedby="addon_tenor_yang_diminta" required maxlength="3" />
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
                    maxlength="255" id="tujuan_kredit" cols="30" rows="4" placeholder="Tujuan Kredit"></textarea>
                @error('tujuan_kredit')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Jaminan yang disediakan</label>
                <textarea name="jaminan" class="form-control @error('jaminan') is-invalid @enderror" maxlength="255"
                    id="" cols="30" rows="4" placeholder="Jaminan yang disediakan"></textarea>
                @error('jaminan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Hubungan Bank</label>
                <textarea name="hubungan_bank" class="form-control @error('hubungan_bank') is-invalid @enderror"
                    maxlength="255" id="hubungan_bank" cols="30" rows="4" placeholder="Hubungan dengan Bank"></textarea>
                @error('hubungan_bank')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Hasil Verifikasi</label>
                <textarea name="hasil_verifikasi" class="form-control @error('hasil_verifikasi') is-invalid @enderror"
                    maxlength="255" id="hasil_verivikasi" cols="30" rows="4"
                    placeholder="Hasil Verifikasi Karakter Umum"></textarea>
                @error('hasil_verifikasi')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
    </div>

    @if ($skema == 'KKB')
    <div class="form-wizard" data-index='1' data-done='true' id="wizard-data-po">
        <div class="row">
            <input type="hidden" name="id_data_po_temp" id="id_data_po_temp">
            <div class="form-group col-md-12">
                <span style="color: black; font-weight: bold; font-size: 18px;">Jenis Kendaraan Roda 2 :</span>
            </div>
            <div class="form-group col-md-6">
                <label>Merk Kendaraan</label>
                <input type="text" name="merk" id="merk" class="form-control @error('merk') is-invalid @enderror"
                    placeholder="Merk kendaraan">
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
                    class="form-control @error('tipe_kendaraan') is-invalid @enderror" placeholder="Tipe kendaraan">
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
                    placeholder="Tahun Kendaraan" value="" min="2000">
                @error('tahun')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="">Warna</label>
                <input type="text" maxlength="25" name="warna" id="warna"
                    class="form-control @error('warna') is-invalid @enderror" placeholder="Warna Kendaraan" value="">
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
                    value="">
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
                    value="">
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
                    value="">
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
        @if ($value->nama == 'Aspek Keuangan')
        <div class="mb-3">
            <button class="btn btn-danger " type="button" id="btn-perhitungan">Perhitungan</button>
            {{-- Aspek Keuangan --}}
            <div id="perhitungan_kredit_with_value">
            </div>
            {{-- End Aspek Keuangan --}}
            <div class="" id="peringatan-pengajuan">
                <br>
                <div class="alert alert-info" role="alert">
                    Perhitungan kredit masih belum ditambahkan, silahkan klik button Perhitungan.
                </div>
            </div>
        </div>
        @endif
        <div class="row">

            @foreach ($dataLevelDua as $item)
            @php
            $idLevelDua = str_replace(' ', '_', strtolower($item->nama));
            @endphp
            {{-- item ijin usaha --}}
            @if ($item->nama == 'Ijin Usaha')
            <div class="row col-md-12">
                <div class="form-group col-md-6">
                    <label for="">{{ $item->nama }}</label>
                    <select name="ijin_usaha" id="ijin_usaha" class="form-control" required>
                        <option value="">-- Pilih Ijin Usaha --</option>
                        <option value="nib">NIB</option>
                        <option value="surat_keterangan_usaha">Surat Keterangan Usaha</option>
                        <option value="tidak_ada_legalitas_usaha">Tidak Ada Legalitas Usaha</option>
                    </select>
                </div>
                <div class="form-group col-md-6" id="npwpsku" style="display:none ">
                    <label for="">Memiliki NPWP</label>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <input type="checkbox" id="isNpwp" class="form-control">
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
                        placeholder="Masukkan informasi" class="form-control" value="">
                </div>

                <div class="form-group col-md-6" id="docNIB">
                    <label for="">{{ $itemNIB->nama }}</label>
                    <input type="hidden" name="id_item_file[{{ $itemNIB->id }}]" value="{{ $itemNIB->id }}"
                        id="docNIB_id">
                    <input type="file" name="upload_file[{{ $itemNIB->id }}]" data-id=""
                        placeholder="Masukkan informasi {{ $itemNIB->nama }}" class="form-control limit-size"
                        id="file_nib">
                    <span class="invalid-tooltip" style="display: none" id="docNIB_text">Besaran file
                        tidak boleh lebih dari 10 MB</span>
                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                    <div class="invalid-feedback">
                        {{ $errors->first('dataLevelTiga.' . $key) }}
                    </div>
                    @endif
                    <span class="filename" style="display: inline;"></span>
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
                    <input type="file" name="upload_file[{{ $itemSKU->id }}]" id="surat_keterangan_usaha_file"
                        data-id="" placeholder="Masukkan informasi {{ $itemSKU->nama }}"
                        class="form-control limit-size">
                    <span class="invalid-tooltip" style="display: none" id="docSKU_text">Besaran file
                        tidak boleh lebih dari 10 MB</span>
                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                    <div class="invalid-feedback">
                        {{ $errors->first('dataLevelTiga.' . $key) }}
                    </div>
                    @endif
                    <span class="filename" style="display: inline;"></span>
                </div>
            </div>
            @elseif($item->nama == 'NPWP')
            <div class="row col-md-12">
                <div class="form-group col-md-6" id="npwp">
                    <label for="">NPWP</label>
                    <input type="hidden" name="id_level[79]" value="79" id="npwp_id">
                    <input type="hidden" name="opsi_jawaban[79]" value="input text" id="npwp_opsi_jawaban">
                    <input type="text" maxlength="255" name="informasi[79]" id="npwp_text"
                        placeholder="Masukkan informasi" class="form-control" value="">
                </div>

                <div class="form-group col-md-6" id="docNPWP">
                    <label for="">{{ $itemNPWP->nama }}</label>
                    <input type="hidden" name="id_item_file[{{ $itemNPWP->id }}]" value="{{ $itemNPWP->id }}"
                        id="docNPWP_id">
                    <input type="file" name="upload_file[{{ $itemNPWP->id }}]" id="npwp_file" data-id=""
                        placeholder="Masukkan informasi {{ $itemNPWP->nama }}" class="form-control limit-size">
                    <span class="invalid-tooltip" style="display: none" id="docNPWP_text">Besaran file
                        tidak boleh lebih dari 10 MB</span>
                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                    <div class="invalid-feedback">
                        {{ $errors->first('dataLevelTiga.' . $key) }}
                    </div>
                    @endif
                    <span class="filename" style="display: inline;"></span>
                </div>
            </div>
            @else
            @if ($item->opsi_jawaban == 'input text')
            @if ($value->nama != 'Aspek Keuangan')
                <div class="form-group col-md-6">
                    <label for="">{{ $item->nama }}</label>
                    <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                    <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                    <input type="text" maxlength="255" name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                        placeholder="Masukkan informasi {{ $item->nama }}" class="form-control" value="">
                </div>
            @endif
            @elseif ($item->opsi_jawaban == 'number')
            @if ($item->nama == 'Repayment Capacity')
            {{-- Aspek Keuangan --}}
            {{-- @php
            $lev1 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)->where('level', 1)->get();
            @endphp
            <div class="form-group col-md-12" id="perhitungan_kredit_no_value">
                <hr>
                <div class="row">
                    @foreach ($lev1 as $item)
                        <div class="form-group col-md-12">
                            <h5>{{ $item->field }} periode :</h5>
                        </div>
                        @php
                        $lev2 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                    ->where('level', 2)
                                                                    ->where('parent_id', $item->id)
                                                                    ->get();
                        @endphp
                        @foreach ($lev2 as $item2)
                            @php
                            $lev3 = \App\Models\MstItemPerhitunganKredit::with('perhitunganKredit')->where('skema_kredit_limit_id', 1)
                                                                        ->where('level', 3)
                                                                        ->where('parent_id', $item2->id)
                                                                        ->get();
                            $perhitunganKreditLev3 = \App\Models\PerhitunganKredit::rightJoin('mst_item_perhitungan_kredit', 'perhitungan_kredit.item_perhitungan_kredit_id', '=', 'mst_item_perhitungan_kredit.id')
                                            ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', 1)
                                            ->where('mst_item_perhitungan_kredit.level', 3)
                                            ->where('mst_item_perhitungan_kredit.parent_id', $item2->id)
                                            ->where('perhitungan_kredit.temp_calon_nasabah_id', 5561)
                                            ->get();
                            @endphp
                            <div class="form-group col-md-6">
                                <table class="table table-bordered">
                                    <tr id="itemPerhitunganKreditLev2">
                                        <th colspan="2">{{ $item2->field }} periode :</th>
                                    </tr>
                                    @foreach ($perhitunganKreditLev3 as $item3)
                                        @php
                                        $lev4 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                                    ->where('level', 4)
                                                                                    ->where('parent_id', $item3->id)
                                                                                    ->get();
                                        @endphp
                                            <tr>
                                                <td width='57%'>{{ $item3->field }}</td>
                                                <td>{{ $item3->nominal }}</td>
                                            </tr>
                                    @endforeach
                                </table>
                            </div>

                        @endforeach
                    @endforeach
                </div>
            </div>
            <div class="form-group col-md-12" id="perhitungan_kredit_no_value2">
                <div class="row">
                    <div class="form-group col-md-12">
                        @php
                      $lev3NoParent = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                      ->where('level', 3)
                      ->whereNull('parent_id')
                      ->get();

                      $results = \App\Models\MstItemPerhitunganKredit::leftJoin('perhitungan_kredit', function($join) {
                                        $join->on('mst_item_perhitungan_kredit.id', '=', 'perhitungan_kredit.item_perhitungan_kredit_id')
                                            ->where('perhitungan_kredit.temp_calon_nasabah_id', '=', '5652');
                                    })
                                    ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', '=', '1')
                                    ->where('mst_item_perhitungan_kredit.level', '=', '3')
                                    ->whereNull('mst_item_perhitungan_kredit.parent_id')
                                    ->whereNull('perhitungan_kredit.temp_calon_nasabah_id')
                                    ->get();

                      @endphp
                        <table class="table table-bordered">
                            @foreach ($results as $item3NoParent)
                            <tr>
                                <td width='50%'>{{ $item3NoParent->field }}</td>
                                <td>0</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="row">
                    @php
                    $lev2NoParent = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                ->where('level', 2)
                                                                ->whereNull('parent_id')
                                                                ->get();
                                                                $indexLev2 = 0;
                    @endphp
                    @foreach ($lev2NoParent as $item2NoParent)
                    @php
                    $lev3NoParent = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                ->where('level', 3)
                                                                ->where('parent_id', $item2NoParent->id)
                                                                ->get();
                    @endphp
                        <div class="form-group col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2">{{ $item2NoParent->field }}</th>
                                </tr>
                                @foreach ($lev3NoParent as $item3NoParent)
                                <tr>
                                    <td width='57%'>{{ $item3NoParent->field }}</td>
                                    <td>{{ 0 }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    @endforeach
                </div>
            </div> --}}
            {{-- <div id="perhitungan_kredit_with_value">
            </div> --}}
            {{-- END --}}
            <div class="form-group col-md-6">
                <label for="">{{ $item->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                <input type="text" maxlength="255" name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                    placeholder="Masukkan informasi {{ $item->nama }}" class="form-control" value="">
            </div>

            @else
            @if ($item->nama == 'Omzet Penjualan' || $item->nama == 'Installment')
                @if ($value->nama != 'Aspek Keuangan')
                <div class="form-group col-md-6">
                    <label for="">{{ $item->nama }}(Perbulan)</label>
                    <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                    <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                    <input type="text" maxlength="255" step="any" name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                    placeholder="Masukkan informasi {{ $item->nama }}" class="form-control rupiah" value="">
                </div>
            @endif
            @else
            @if ($value->nama != 'Aspek Keuangan')
                <div class="form-group col-md-6">
                    <label for="">{{ $item->nama }}</label>
                    <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                    <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                    <input type="text" maxlength="255" step="any" name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                        placeholder="Masukkan informasi {{ $item->nama }}" class="form-control rupiah" value="">
                </div>
            @endif
            @endif
            @endif
            @elseif ($item->opsi_jawaban == 'persen')
            @if ($value->nama != 'Aspek Keuangan')
                <div class="form-group col-md-6">
                    <label for="">{{ $item->nama }}</label>
                    <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                    <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                    <div class="input-group mb-3">
                        <input type="number" step="any" name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                            placeholder="Masukkan informasi {{ $item->nama }}" class="form-control"
                            aria-label="Recipient's username" aria-describedby="basic-addon2" value=""
                            onkeydown="return event.keyCode !== 69">
                        <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon2">%</span>
                        </div>
                    </div>
                </div>

            @endif
            @elseif ($item->opsi_jawaban == 'file')
                @if ($value->nama != 'Aspek Keuangan')
                    <div class="form-group col-md-6">
                        <label for="">{{ $item->nama }}</label>
                        {{-- <input type="hidden" name="opsi_jawaban[]" value="{{ $item->opsi_jawaban }}" --}} {{--
                            id="{{ $idLevelDua }}"> --}}
                        <input type="hidden" name="id_item_file[{{ $item->id }}]" value="{{ $item->id }}" id="">
                        <input type="file" name="upload_file[{{ $item->id }}]" id="{{ $idLevelDua }}" data-id=""
                            placeholder="Masukkan informasi {{ $item->nama }}" class="form-control limit-size">
                        <span class="invalid-tooltip" style="display: none">Maximum upload file size is 10
                            MB</span>
                        <span class="filename" style="display: inline;"></span>
                    </div>
                @endif
            @elseif ($item->opsi_jawaban == 'long text')
            <div class="form-group col-md-6">
                <label for="">{{ $item->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $item->id }}]" value="{{ $item->opsi_jawaban }}" id="">
                <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}" id="">
                <textarea name="informasi[{{ $item->id }}]" rows="4" id="{{ $idLevelDua }}" maxlength="255"
                    class="form-control" placeholder="Masukkan informasi {{ $item->nama }}"></textarea>
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
                        value="{{ ($itemJawaban->skor == null ? 'kosong' : $itemJawaban->skor) . '-' . $itemJawaban->id }}">
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
                <select name="kategori_jaminan_tambahan" id="kategori_jaminan_tambahan" class="form-control" required>
                    <option value="">-- Pilih Kategori Jaminan Tambahan --</option>
                    <option value="Tidak Memiliki Jaminan Tambahan">Tidak Memiliki Jaminan Tambahan
                    </option>
                    <option value="Tanah">Tanah</option>
                    <option value="Kendaraan Bermotor">Kendaraan Bermotor</option>
                    <option value="Tanah dan Bangunan">Tanah dan Bangunan</option>
                </select>
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
                    placeholder="Masukkan informasi" class="form-control" id="{{ $idLevelTiga }}" value="">
            </div>
            @elseif ($itemTiga->opsi_jawaban == 'number')
            <div class="form-group col-md-6">
                <label for="">{{ $itemTiga->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $itemTiga->id }}]" value="{{ $itemTiga->opsi_jawaban }}"
                    id="">
                <input type="hidden" name="id_level[{{ $itemTiga->id }}]" value="{{ $itemTiga->id }}" id="">
                <input type="text" step="any" name="informasi[{{ $itemTiga->id }}]" id="{{ $idLevelTiga }}"
                    placeholder="Masukkan informasi {{ $itemTiga->nama }}" class="form-control rupiah" value="">
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
                        aria-label="Recipient's username" aria-describedby="basic-addon2" value="">
                    <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                </div>
                @endif
            </div>
            @elseif ($itemTiga->opsi_jawaban == 'file')
            <div class="form-group col-md-6 file-wrapper item-{{ $itemTiga->id }}">
                <label for="">{{ $itemTiga->nama }}</label>
                <div class="row file-input">
                    <div class="col-md-9">
                        <input type="hidden" name="id_item_file[{{ $itemTiga->id }}][]" value="{{ $itemTiga->id }}" id="">
                        <input type="file" name="upload_file[{{ $itemTiga->id }}][]" id="{{ $idLevelTiga }}" data-id=""
                            placeholder="Masukkan informasi {{ $itemTiga->nama }}"
                            class="form-control limit-size file-usaha" accept="image/*">
                        <span class="invalid-tooltip" style="display: none">Maximum upload
                            file size is 10 MB</span>
                        <span class="filename" style="display: inline;"></span>
                    </div>
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
                </div>
            </div>
            @elseif ($itemTiga->opsi_jawaban == 'long text')
            <div class="form-group col-md-6">
                <label for="">{{ $itemTiga->nama }}</label>
                <input type="hidden" name="opsi_jawaban[{{ $itemTiga->id }}]" value="{{ $itemTiga->opsi_jawaban }}"
                    id="">
                <input type="hidden" name="id_level[{{ $itemTiga->id }}]" value="{{ $itemTiga->id }}" id="">
                <textarea name="informasi[{{ $itemTiga->id }}]" rows="4" id="{{ $idLevelTiga }}" maxlength="255"
                    class="form-control" placeholder="Masukkan informasi {{ $itemTiga->nama }}"></textarea>
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
                        value="{{ ($itemJawabanTiga->skor == null ? 'kosong' : $itemJawabanTiga->skor) . '-' . $itemJawabanTiga->id }}">
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
                        id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan_/_ht' ? '' : $idLevelEmpat }}"
                        placeholder="Masukkan informasi" class="form-control only-number" value="">
                    <div class="input-group-append">
                        <div class="input-group-text" id="addon_tenor_yang_diminta">
                            Bulan</div>
                    </div>
                </div>
                @else
                <input type="text" maxlength="255" name="informasi[{{ $itemEmpat->id }}]"
                    id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan_/_ht' ? '' : $idLevelEmpat }}"
                    placeholder="Masukkan informasi" class="form-control" value="">
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
                    placeholder="Masukkan informasi {{ $itemEmpat->nama }}" class="form-control rupiah" value="">
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
                        aria-label="Recipient's username" aria-describedby="basic-addon2" value="">
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
                <input type="file" id="{{ $idLevelEmpat }}" name="upload_file[{{ $itemEmpat->id }}]" data-id=""
                    placeholder="Masukkan informasi {{ $itemEmpat->nama }}" class="form-control limit-size">
                <span class="invalid-tooltip" style="display: none">Maximum upload file
                    size is 10 MB</span>
                <span class="filename" style="display: inline;"></span>
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
                        value="{{ ($itemJawabanEmpat->skor == null ? 'kosong' : $itemJawabanEmpat->skor) . '-' . $itemJawabanEmpat->id }}">
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

            @if ($value->nama == 'Aspek Keuangan')
                @php
                    $read_lev1 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)->where('level', 1)->get();
                @endphp
                <div class="row m-2 result-aspek-keuangan">
                    <div class="col"></div>
                </div>
            @endif

            <div class="form-group col-md-12">
                <hr style="border: 0.2px solid #E3E6EA;">
                <label for="">Pendapat dan Usulan {{ $value->nama }}</label>
                <input type="hidden" name="id_aspek[{{ $value->id }}]" value="{{ $value->id }}">
                <textarea name="pendapat_per_aspek[{{ $value->id }}]"
                    class="form-control @error('pendapat_per_aspek') is-invalid @enderror" id="" maxlength="255"
                    cols="30" rows="4" placeholder="Pendapat Per Aspek"></textarea>
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
                <textarea name="komentar_staff" id="komentar_staff" class="form-control @error('komentar_staff') is-invalid @enderror"
                    maxlength="255" id="" cols="30" rows="4"
                    placeholder="Pendapat dan Usulan Staf/Analis Kredit" required></textarea>
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
            <button type="submit" class="btn btn-info btn-simpan" data-redirect="{{ route('pengajuan-kredit.index') }}" id="submit">Simpan <span
                    class="fa fa-save"></span></button>
            {{-- <button class="btn btn-info ">Simpan <span class="fa fa-chevron-right"></span></button> --}}
        </div>
    </div>
</form>
@endsection

@push('custom-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // document.getElementById('file_slik').addEventListener('change', function() {
    //     var fileInput = this;
    //     var fileError = document.getElementById('file_slik_error');
    //     var maxSize = 5 * 1024 * 1024;

    //     if (fileInput.files.length > 0) {
    //         var fileSize = fileInput.files[0].size;
    //         if (fileSize > maxSize) {
    //             fileInput.value = '';
    //             fileError.innerText = 'Ukuran berkas melebihi 5MB.';
    //         } else {
    //             fileError.innerText = '';
    //         }
    //     }
    // });
</script>
<script>

    //var isPincetar = "{{Request::url()}}".includes('pincetar');
    let dataAspekArr;
        $(document).ready(function() {
            let valSkema = $("#skema_kredit").val();
            if (valSkema == null || valSkema == '') {
                $('#exampleModal').modal('show');
            }
            dataAspekArr = <?php echo json_encode($dataAspek); ?>;

            $("#exampleModal").on('click', "#btnSkema", function() {
                let valSkema = $("#skema_kredit").val();
                // //console.log(valSkema);

                $("#skema_kredit").val(valSkema);
            });
        });

        $('#docSKU').hide();
        $('#surat_keterangan_usaha').hide();
        $('#nib').hide();
        $('#docNIB').hide();
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
        $("#jaminan_tambahan").hide();

        let urlCekSubColumn = "{{ route('cek-sub-column') }}";
        let urlGetItemByKategoriJaminanUtama =
            "{{ route('get-item-jaminan-by-kategori-jaminan-utama') }}"; // jaminan tambahan
        let urlGetItemByKategori = "{{ route('get-item-jaminan-by-kategori') }}"; // jaminan tambahan
        var nullValue = []

        var x = 1;

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
                    <input type="file" name="upload_file[{{ $itemKTPIs->id }}]" data-id="" placeholder="Masukkan informasi {{ $itemKTPIs->nama }}" class="form-control limit-size" id="foto_ktp_istri">
                    <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 10 MB</span>
                    @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                        <div class="invalid-feedback">
                            {{ $errors->first('dataLevelDua.' . $key) }}
                        </div>
                    @endif
                    <span class="filename" style="display: inline;"></span>
                `)
                $("#foto-ktp-suami").append(`
                        <label for="">{{ $itemKTPSu->nama }}</label>
                        <input type="hidden" name="id_item_file[{{ $itemKTPSu->id }}]" value="{{ $itemKTPSu->id }}" id="">
                        <input type="file" name="upload_file[{{ $itemKTPSu->id }}]" data-id="" placeholder="Masukkan informasi {{ $itemKTPSu->nama }}" class="form-control limit-size" id="foto_ktp_suami">
                        <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 10 MB</span>
                        @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                            <div class="invalid-feedback">
                                {{ $errors->first('dataLevelDua.' . $key) }}
                            </div>
                        @endif
                        <span class="filename" style="display: inline;"></span>
                `);
            } else {
                $("#foto-ktp-nasabah").addClass('form-group col-md-12')
                $("#foto-ktp-nasabah").append(`
                    @isset($itemKTPNas)
                    <label for="">{{ $itemKTPNas->nama }}</label>
                    <input type="hidden" name="id_item_file[{{ $itemKTPNas->id }}]" value="{{ $itemKTPNas->id }}" id="">
                    <input type="file" name="upload_file[{{ $itemKTPNas->id }}]" data-id="" placeholder="Masukkan informasi {{ $itemKTPNas->nama }}" class="form-control limit-size" id="foto_ktp_nasabah">
                    <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 10 MB</span>
                    @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                        <div class="invalid-feedback">
                            {{ $errors->first('dataLevelDua.' . $key) }}
                        </div>
                    @endif
                    <span class="filename" style="display: inline;"></span>
                    @endisset
                `)
            }
            $('.limit-size').on('change', function() {
                var size = (this.files[0].size / 1024 / 1024).toFixed(2)
                if (size > 10) {
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

        $('#kabupaten').change(function() {
            var kabID = $(this).val();
            if (kabID) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('getKecamatan') }}?kabID=" + kabID,
                    dataType: 'JSON',
                    success: function(res) {
                        //    //console.log(res);
                        if (res) {
                            $("#kecamatan").empty();
                            $("#desa").empty();
                            $("#kecamatan").append('<option value="0">---Pilih Kecamatan---</option>');
                            $("#desa").append('<option value="0">---Pilih Desa---</option>');
                            $.each(res, function(nama, kode) {
                                $('#kecamatan').append(`
                                    <option value="${kode}">${nama}</option>
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
                                    <option value="${kode}">${nama}</option>
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
                                        <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 10 MB</span>
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
                url: `${urlGetItemByKategori}?kategori=${kategoriJaminan}`,
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
                            if (valItem.nama == 'Atas Nama') {
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group col-md-6 aspek_jaminan_kategori">
                                        <label>${valItem.nama}</label>
                                        <input type="hidden" name="id_level[${valItem.id}]" value="${valItem.id}" id="" class="input">
                                        <input type="hidden" name="opsi_jawaban[${valItem.id}]"
                                            value="${valItem.opsi_jawaban}" id="" class="input">
                                        <input type="text" maxlength="255" id="atas_nama" name="informasi[${valItem.id}]" placeholder="Masukkan informasi"
                                            class="form-control input" value="${response.dataJawaban[i]}">
                                    </div>
                                `);
                            } else {
                                if (valItem.nama == 'Foto') {
                                    $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group col-md-6 file-wrapper item-${valItem.id}">
                                        <label for="">${valItem.nama}</label>
                                        <div class="row file-input">
                                            <div class="col-md-9">
                                                <input type="hidden" name="id_item_file[${valItem.id}][]" value="${valItem.id}" id="">
                                                <input type="file" id="${valItem.nama.toString().replaceAll(" ", "_")}" name="upload_file[${valItem.id}][]" data-id=""
                                                    placeholder="Masukkan informasi ${valItem.nama}"
                                                    class="form-control limit-size">
                                                    <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 10 MB</span>
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
                                    </div>`);
                                } else {
                                    if (response.dataJawaban[i] != null && response.dataJawaban[
                                            i] != "") {
                                        if (kategoriJaminan != 'Kendaraan Bermotor') {
                                            isCheck =
                                                "<input type='checkbox' class='checkKategori' checked>"
                                            isDisabled = ""
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
                            skor = valOption.skor;
                            opt = valOption.id;
                            // //console.log(valOption.skor);
                            $('#itemByKategori').append(`
                            <option value="${valOption.skor}-${valOption.id}" selected>
                            ${valOption.option}
                            </option>`);
                        });
                        $("#itemByKategori").val(skor + '-' + opt);
                        $("#select_kategori_jaminan_tambahan").hide()
                        $("#jaminan_tambahan").hide()
                    }
                }
            })
        });
        // end item kategori jaminan tambahan cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan

        // milih ijin usaha
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
        $('#ijin_usaha').change(function(e) {
            let ijinUsaha = $(this).val();
            $('#npwpsku').hide();
            if (ijinUsaha == 'nib') {
                $('#npwpsku').hide();
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
                $('#nib_opsi_jawaban').removeAttr('disabled');

                $('#docNIB').show();
                $('#docNIB_id').removeAttr('disabled');
                $('#docNIB_text').removeAttr('disabled');
                $('#docNIB_upload_file').removeAttr('disabled');
                $('#file_nib').removeAttr('disabled');

                $('#npwp').show();
                $('#npwp_id').removeAttr('disabled');
                $('#npwp_text').removeAttr('disabled');
                $('#npwp_opsi_jawaban').removeAttr('disabled');
                $('#npwp_file').removeAttr('disabled');

                $('#docNPWP').show();
                $('#docNPWP_id').removeAttr('disabled');
                $('#docNPWP_text').removeAttr('disabled');
                $('#docNPWP_text').val('');
                $('#docNPWP_upload_file').removeAttr('disabled');
            } else if (ijinUsaha == 'surat_keterangan_usaha') {
                $('#npwpsku').show();
                $('#nib').hide();
                $('#nib_id').attr('disabled', true);
                $('#nib_text').attr('disabled', true);
                $('#nib_file').attr('disabled', true);
                $('#file_nib').attr('disabled', true);
                $('#docNIB_file').attr('disabled', true);
                $('#nib_text').val('');
                $('#nib_opsi_jawaban').attr('disabled', true);

                $('#docNIB').hide();
                $('#docNIB_id').attr('disabled', true);
                $('#docNIB_text').attr('disabled', true);
                $('#docNIB_upload_file').attr('disabled', true);

                $('#surat_keterangan_usaha').show();
                $('#surat_keterangan_usaha_id').removeAttr('disabled');
                $('#surat_keterangan_usaha_text').removeAttr('disabled');
                $('#surat_keterangan_usaha_file').removeAttr('disabled');
                $('#surat_keterangan_usaha_text').val('');
                $('#surat_keterangan_usaha_opsi_jawaban').removeAttr('disabled');

                $('#docSKU').show();
                $('#docSKU_id').removeAttr('disabled');
                $('#docSKU_text').removeAttr('disabled');
                $('#docSKU_upload_file').removeAttr('disabled');

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
            } else if (ijinUsaha == 'tidak_ada_legalitas_usaha') {
                $('#npwpsku').hide();
                $('#nib').hide();
                $('#nib_id').attr('disabled', true);
                $('#nib_text').attr('disabled', true);
                $('#file_nib').attr('disabled', true);
                $('#nib_text').val('');
                $('#nib_opsi_jawaban').attr('disabled', true);

                $('#docNIB').hide();
                $('#docNIB_id').attr('disabled', true);
                $('#docNIB_text').attr('disabled', true);
                $('#docNIB_file').attr('disabled', true);
                $('#docNIB_text').val('');
                $('#docNIB_upload_file').attr('disabled', true);

                $('#surat_keterangan_usaha').hide();
                $('#surat_keterangan_usaha_id').attr('disabled', true);
                $('#surat_keterangan_usaha_text').attr('disabled', true);
                $('#surat_keterangan_usaha_file').attr('disabled', true);
                $('#surat_keterangan_usaha_text').val('');
                $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);

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
                $('#npwpsku').hide();
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
                $('#npwp_opsi_jawaban').removeAttr('disabled');

                $('#docNPWP').show();
                $('#docNPWP_id').removeAttr('disabled');
                $('#docNPWP_text').removeAttr('disabled');
                $('#docNPWP_text').val('');
                $('#docNPWP_upload_file').removeAttr('disabled');
            }
        });
        // end milih ijin usaha

        // Cek Npwp
        $('#isNpwp').change(function() {
            //console.log($(this).is(':checked'));
            if ($(this).is(':checked')) {
                $('#npwp').show();
                $('#npwp_id').removeAttr('disabled');
                $('#npwp_text').removeAttr('disabled');
                $('#npwp_file').removeAttr('disabled');
                $('#npwp_opsi_jawaban').removeAttr('disabled');

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
            let thls = parseInt($('#thls').val().split('.').join(''));
            let nilaiAsuransi = parseInt($('#nilai_pertanggungan_asuransi').val().split('.').join(''));
            let plafon_usulan = parseInt($('.plafon_usulan').val().split('.').join(''));

            let ratioCoverage = (thls + nilaiAsuransi) / plafon_usulan * 100; //cek rumus nya lagi
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
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
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
            if (size > 10) {
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
        // Limit Upload Slik
        $('.limit-size-slik').on('change', function() {
            var size = (this.files[0].size / 1024 / 1024).toFixed(2)
            if (size > 10) {
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
            $('.limit-size').on('change', function() {
                var size = (this.files[0].size / 1024 / 1024).toFixed(2)
                if (size > 10) {
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

            $('.limit-size-slik').on('change', function() {
                var size = (this.files[0].size / 1024 / 1024).toFixed(2)
                if (size > 10) {
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
                console.log(v.value)
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
            })*/

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
                    var inputId = ids != '' || ids != null || ids != 'undefined' ? ids.toString() : ''
                    inputId = inputId.replaceAll('_', ' ').toLowerCase();
                    if (inputId != '') {
                        // check span filename
                        var spanFile = $('#'+ids).parent().find('.filename');
                        const filename = spanFile.html();
                        if (inputId == "file nib" || inputId == "surat keterangan usaha file" || inputId == "npwp file") {
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
                                    if (inputId == "npwp file") {
                                        const isCheckNpwp = $('#isNpwp').is(':checked')
                                        if (isCheckNpwp) {
                                            if ((v.value == '' || v.value == null) && (filename == '' || filename == null)) {
                                                if (!fileEmpty.includes(inputId))
                                                    fileEmpty.push(inputId)
                                            }
                                        }
                                    }

                                    if (inputId == "surat keterangan usaha file") {
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
                                if (inputId == 'foto sp') {
                                    inputId = 'surat permohonan';
                                }
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
                                item = "jaminan Tambahan"
                            }
                        }

                        if (ijinUsaha == 'nib') {
                            if (v == 'nib text' || v == 'nib_text') {
                                var nibText = $("#nib_text").val()
                                if (nibText == null || nibText == '') {
                                    for(var j = 0; j < nullValue.length; j++){
                                        while(nullValue[j] == v){
                                            nullValue.splice(j, 1)
                                        }
                                    }
                                }
                            }
                        }

                        if (item.includes('text'))
                            item = item.replaceAll('text', '');

                        message += item != '' ? `<li class="text-left">Field `+item +` harus diisi.</li>` : ''
                    })
                    for (var i = 0; i < fileEmpty.length; i++) {
                        var msgItem = fileEmpty[i].includes('file') ? fileEmpty[i].replaceAll('file', '') : fileEmpty[i];
                        msgItem = msgItem.includes('text') ? msgItem.replaceAll('text', '') : msgItem;
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
                } else {
                    $("#loadingModal").modal({
                        keyboard: false
                    });
                    $("#loadingModal").modal("show");
                }
            }
        })


        /*var fotoSp = document.getElementById("foto_sp");
        var selectedFile;

        fotoSp.addEventListener('change', updateImageDisplay);

        function updateImageDisplay() {
            if (fotoSp.files.length == 0) {
                fotoSp.files = selectedFile;
            } else {
                selectedFile = fotoSp.files;
            }

        }*/
        var slik = document.getElementById("file_slik");
        var selectedFile;

        slik.addEventListener('change', updateImageDisplaySlik);

        function updateImageDisplaySlik() {
            if (slik.files.length == 0) {
                slik.files = selectedFile;
            } else {
                selectedFile = slik.files;
            }

        }

        var docNPWP = document.getElementById("npwp_file");
        var selectedFile;

        docNPWP.addEventListener('change', updateImageDisplaydocNPWP);

        function updateImageDisplaydocNPWP() {
            if (docNPWP.files.length == 0) {
                docNPWP.files = selectedFile;
            } else {
                selectedFile = docNPWP.files;
            }

        }

        var docSKU = document.getElementById("surat_keterangan_usaha_file");
        var selectedFile;

        docSKU.addEventListener('change', updateImageDisplaydocSKU);

        function updateImageDisplaydocSKU() {
            if (docSKU.files.length == 0) {
                docSKU.files = selectedFile;
            } else {
                selectedFile = docSKU.files;
            }

        }

        var docNIB = document.getElementById("file_nib");
        var selectedFile;

        docNIB.addEventListener('change', updateImageDisplaydocNIB);

        function updateImageDisplaydocNIB() {
            if (docNIB.files.length == 0) {
                docNIB.files = selectedFile;
            } else {
                selectedFile = docNIB.files;
            }

        }


        // var docKebKre = document.getElementById("perhitungan_kebutuhan_kredit");
        // var selectedFile;

        // docKebKre.addEventListener('change', updateImageDisplaydocKebKre);

        // function updateImageDisplaydocKebKre() {
        //     if (docKebKre.files.length == 0) {
        //         docKebKre.files = selectedFile;
        //     } else {
        //         selectedFile = docKebKre.files;
        //     }

        // }

        // var docKebNet = document.getElementById("perhitungan_net_income");
        // var selectedFile;

        // docKebNet.addEventListener('change', updateImageDisplaydocKebNet);

        // function updateImageDisplaydocKebNet() {
        //     if (docKebNet.files.length == 0) {
        //         docKebNet.files = selectedFile;
        //     } else {
        //         selectedFile = docKebNet.files;
        //     }

        // }

        // var docKebInstll = document.getElementById("perhitungan_installment");
        // var selectedFile;

        // docKebInstll.addEventListener('change', updateImageDisplaydocKebInstll);

        // function updateImageDisplaydocKebInstll() {
        //     if (docKebInstll.files.length == 0) {
        //         docKebInstll.files = selectedFile;
        //     } else {
        //         selectedFile = docKebInstll.files;
        //     }

        // }
        /*$('#skema').change(function() {
        if ($(this).val() == 'KKB' && isPincetar) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "Skema KKB Belum bisa dilakukan"
                }).then(function() {
                    $("#skema").val('').trigger("change");
                })
            }
        });*/

        $("#produk").on("change", function(e){
            var value = $(this).val();

            $.ajax({
                type: "GET",
                url: "{{ route('get-skema-kredit') }}?produkId="+value,
                dataType: "json",
                success: function(res){
                    $("#skema").empty();
                    $("#skema").append(`<option value="">- Pilih Skema Kredit -</option>`)
                    $("#limit").empty();
                    $("#limit").append(`<option value="">- Pilih Limit -</option>`)

                    if(res){
                        $.each(res, function(i, v){
                            $("#skema").append(`<option value="${v.id}">${v.name}</option>`)
                        })
                    }
                }
            })
        });

        $("#skema").change(function(e){
            var value = $(this).val()

            $.ajax({
                type: "GET",
                url: "{{ route('get-skema-limit') }}?skemaId="+value,
                dataType: "JSON",
                success: function(res){
                    if(res != null){
                        $("#limit").empty();
                        $("#limit").append(`<option value="">- Pilih Limit -</option>`)

                        $.each(res, function(i, v){
                            $("#limit").append(`<option value="${v.id}">${formatrupiah(v.from.toString())} - ${formatrupiah(v.to.toString())}</option>`)
                        })
                    }
                }
            })
        })
</script>
@include('pengajuan-kredit.partials.create-save-script')
@include('pengajuan-kredit.modal.perhitungan-modal-copy')
<script src="{{ asset('') }}js/custom.js"></script>
<script>
    function calcForm() {
        cekPlafon();
        cekTenor();
        var allFormData = [];
        var allIdInput = [];
        $('#form-perhitungan input').each(function() {
            var id = $(this).attr('id')
            var formula = $(this).data('formula'); // If your forms have IDs, otherwise you can skip this
            var detail = $(this).data('detail')
            var level = $(this).data('level')
            var inp_class = $(this).attr('class')
            allIdInput.push(id)
            if (formula) {
                // calculate by formula
                formula = formula.replace()
            }
            var formData = $(this).serializeArray();
            allFormData.push({
                id: id,
                formula: formula,
                data: formData,
                detail: detail,
                level: level,
                inp_class: inp_class ? inp_class.replaceAll('form-control rupiah ', '') : '',
            });
        });
        console.log('allFormData')
        console.log(allFormData)
        // console.log("jumlahkredit: " + $("#jumlah_kredit").val());
        $.each(allFormData, function(i, item) {
            var formula = item.formula
            var detail = item.detail
            var id_formula = item.id
            var level = item.level
            var inp_class = item.inp_class

            if (typeof formula != 'undefined' && formula != '') {
                // check if have detail
                if (formula.includes('sum')) {
                    console.log("formula " + formula);
                    var child_id = formula.replaceAll('sum(', '')
                    child_id = child_id.replaceAll(')', '')
                    if (detail) {
                        var parent_content = $(`#${id_formula}`).parent()
                        var table = parent_content.find('table')
                        var input = table.find(`[id^="${child_id}"]`)
                        var result = 0
                        input.each(function() {
                            var val = parseInt($(this).val().replaceAll('.',''))
                            val = isNaN(val) ? 0 : val
                            result += val
                        })
                        $(`#${id_formula}`).val(isNaN(result) ? '' : formatrupiah(parseInt(result).toString()))
                    } else{
                        var table = $(this).parent().parent().parent()
                        var input = $("#table_item").find(`[id^="${child_id}"]`)
                        var result = 0
                        input.each(function() {
                            var val = parseInt($(this).val().replaceAll('.',''))
                            // console.log("VAL Angsurang" + val);
                            val = isNaN(val) ? 0 : val
                            result += val
                        })
                        $(`#${id_formula}`).val(isNaN(result) ? '' : formatrupiah(parseInt(result).toString()))
                    }
                }
                else {
                    if (formula.includes('inp')) {
                        // $.each(allIdInput,  function(j, id){
                            // console.log(`index: ${j} id: ${id}`);
                            if (level == 4) {
                                $.each(allIdInput,  function(j, id){
                                    var inp_arr = $(`.${inp_class}`)
                                    // console.log('inp arr')
                                    // console.log(inp_arr)
                                    $.each(inp_arr, function(k, val) {
                                        // console.log('inp arr id')
                                        var input_arr_id = $(this).attr('id')
                                        var input_arr_class = $(this).attr('class')
                                        // $(this).parent().parent().attr('.inp_14').val()
                                        var item_formula = $(this).data('formula')
                                        if (item_formula.includes('inp')) {

                                        }
                                        // console.log($(this).parent().parent().find('.inp_14').attr('id'))
                                        var plafon = $(this).parent().parent().find('.inp_13').val()
                                        var tenor = $(this).parent().parent().find('.inp_14').val()
                                        // var input_val = $(`#${id}`).val().replaceAll('.', '')
                                        var input_val = plafon.replaceAll('.', '')
                                        input_val = isNaN(input_val) ? 0 : input_val
                                        formula = item_formula.replaceAll(id, input_val)
                                        var resultAngsuran = parseInt(plafon.replaceAll(".", "")) / parseInt(tenor.replaceAll(".", ""))
                                        $(this).val(formatrupiah(parseInt(resultAngsuran).toString()))
                                    })
                                })
                            }
                            else {
                                let formulaSplitted = formula.split(/[+-\/\*]/);
                                $.each(allIdInput,  function(j, id){
                                    // console.log(`formula splitted:`);
                                    // console.log(formulaSplitted);
                                    if (stringContainsValueFromArray(formula, formulaSplitted)) {
                                        try {
                                            $.each(formulaSplitted, function(k, replaced){
                                                // console.log(`replaced: ${replaced}`);
                                                if(!isNaN(replaced)){
                                                    var input_val = parseInt(replaced)
                                                } else{
                                                    var input_val = typeof $(`#${replaced}`).val() != 'undefined' && $(`#${replaced}`).val() != '' ? $(`#${replaced}`).val().replaceAll('.', '') : 0
                                                    input_val = isNaN(input_val) ? 0 : input_val
                                                }
                                                // if(j == 46){
                                                //     console.log('input val 46 ' + id + " " + formula);
                                                //     console.log(input_val);
                                                // }
                                                // console.log(`formula include : ${input_val} formula:${formula}  id: ${id} index: ${j} id_item: ${id_formula} replaced: ${replaced}`);
                                                if(replaced != "100"){
                                                    formula = formula.replace(replaced, input_val)
                                                }
                                                // console.log(`formula after replaced: ${formula}`);
                                                // // check if formula contain id from other input
                                                var other_id = alphaOnly(formula)
                                                if (other_id && $(`#${other_id}`).val()) {
                                                    var input_val = $(`#${other_id}`).val().replaceAll('.', '')
                                                    formula = formula.replaceAll(other_id, input_val)
                                                }
                                                // console.log('hasil formula')
                                                // console.log(formula)
                                                var result = calculateFormula(formula)
                                                if(id_formula != 'inp_68'){
                                                    result = result < 0 ? `(${formatrupiah(parseInt(result).toString())})` : formatrupiah(parseInt(result).toString())
                                                } else{
                                                    $("#repayment_capacity").val(result)
                                                }
                                                $(`#${id_formula}`).val(result)
                                                $(`#${id_formula}_label`).html(result)
                                            })
                                        } catch (error) {
                                            console.log(`formula error : ${error}`)
                                        }
                                    }
                                })
                            }
                        // })
                        // check input array or not
                    } else {
                        let formulaSplitted = formula.split(/[+-\/\*]/);
                        $.each(allIdInput,  function(j, id){
                            // console.log(`formula splitted:`);
                            // console.log(formulaSplitted);
                            if (stringContainsValueFromArray(formula, formulaSplitted)) {
                                try {
                                    $.each(formulaSplitted, function(k, replaced){
                                        // console.log(`replaced: ${replaced}`);
                                        var input_val = typeof $(`#${replaced}`).val() != 'undefined' && $(`#${replaced}`).val() != '' ? $(`#${replaced}`).val().replaceAll('.', '') : 0
                                        input_val = isNaN(input_val) ? 0 : input_val
                                        // if(j == 46){
                                        //     console.log('input val 46 ' + id + " " + formula);
                                        //     console.log(input_val);
                                        // }
                                        // console.log(`formula include : ${input_val} formula:${formula}  id: ${id} index: ${j} id_item: ${id_formula} replaced: ${replaced}`);
                                        if(replaced != "100"){
                                            formula = formula.replace(replaced, input_val)
                                        }
                                        // check if formula contain id from other input
                                        var other_id = alphaOnly(formula)
                                        if (other_id && $(`#${other_id}`).val()) {
                                            var input_val = $(`#${other_id}`).val().replaceAll('.', '')
                                            formula = formula.replaceAll(other_id, input_val)
                                        }
                                        // console.log('hasil formula')
                                        // console.log(formula)
                                        var result = calculateFormula(formula)
                                        if(id_formula != 'inp_67'){
                                            result = formatrupiah(parseInt(result).toString())
                                        }
                                        $(`#${id_formula}`).val(result)
                                        $(`#${id_formula}_label`).html(result)
                                    })
                                } catch (error) {
                                    console.log(`formula error : ${error}`)
                                }
                            }
                        })
                    }
                }
            }
        })
    }

    $("#btn-perhitungan").on('click', function() {
        $('#loading-simpan-perhitungan').hide();
        $('#perhitunganModalAfterLoading').show();
        $("#perhitunganModal").modal('show')
        calcForm()
    })

    function stringContainsValueFromArray(inputString, searchArray) {
            for (let i = 0; i < searchArray.length; i++) {
                if (inputString.includes(searchArray[i])) {
                return true; // Return true if a match is found
                }
            }
            return false; // Return false if no match is found
        }

    var indexBtnSimpan = 0;
    var periodePerhitunganKreditId;
    var periodePerhitunganKreditLastId;
    var selectValueElementBulan;
    var selectElementTahun;
    $("#btnSimpanPerhitungan").on('click',function(e){
        console.log('test');
        indexBtnSimpan += 1;
        let data = {
            idCalonNasabah: $("id_nasabah").val()
        }
        $("#perhitunganModal input").each(function(){
            let input = $(this);

            data[input.attr("name")] = input.val();
            data['idCalonNasabah'] = $("#id_nasabah").val();
        });

        $('#peringatan-pengajuan').empty();

        var selectElementBulan = $("#periode").find(":selected").text();
        selectValueElementBulan = $("#periode").val();
        selectElementTahun = $("#periode_tahun").find(":selected").text();
        var titlePeriode = ``;

        if (indexBtnSimpan == 1) {
            $('#perhitungan_kredit_with_value').append(`
                <br>
                <h5>Periode : ${selectElementBulan} - ${selectElementTahun}</h5>
                <div class="row" id="row_perhitungan_kredit">
                </div>
                <div class="row" id="table_perhitungan_kredit_lev3_noparent">
                </div>
                <div class="row" id="row_max_pembiayaan">
                </div>
                <br>
                <div class="row" id="row_plafon">
                </div>
            `);
        }else{
            $('#perhitungan_kredit_with_value').empty();
            $('#perhitungan_kredit_with_value').append(`
                <br>
                <h5>Periode : ${selectElementBulan} - ${selectElementTahun}</h5>
                <div class="row" id="row_perhitungan_kredit">
                </div>
                <div class="row" id="table_perhitungan_kredit_lev3_noparent">
                </div>
                <div class="row" id="row_max_pembiayaan">
                </div>
                <br>
                <div class="row" id="row_plafon">
                </div>
            `);
        }
        var fieldValues = [];
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
        }

        function getDataPerhitunganKreditLev2(element2, idClnNasabah) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: "{{ route('pengajuan-kredit.get-data-perhitungan-kredit-lev3') }}",
                    type: "GET",
                    data: {
                        parent_id: element2.id,
                        id_nasabah: idClnNasabah,
                    },
                    beforeSend: function() {
                        $('#loading-simpan-perhitungan').show();
                        $('#perhitunganModalAfterLoading').hide();
                    },
                    success: function(res) {
                        resolve(res);
                        setTimeout(function(){
                            $('#loading-simpan-perhitungan').hide();
                            $('.modal').modal('hide');
                        }, 1000);
                    },
                    error: function(err) {
                        reject(err);
                    }
                });
            });
        }

        async function getDataPerhitunganKreditLev1() {
            try {
                const res1 = await $.ajax({
                    url: "{{ route('pengajuan-kredit.save-data-perhitungan-temp') }}",
                    type: "POST",
                    data: data,
                    beforeSend: function() {
                        $('#loading-simpan-perhitungan').show();
                        $('#perhitunganModalAfterLoading').hide();
                    },
                });
                console.log(res1);

                const res2 = await $.ajax({
                    url: '{{ route('pengajuan-kredit.get-data-perhitungan-kredit-lev1') }}',
                    type: "GET",
                    beforeSend: function() {
                        $('#loading-simpan-perhitungan').show();
                        $('#perhitunganModalAfterLoading').hide();
                    },
                });
                console.log(res2);
                if (indexBtnSimpan == 1) {
                    $.ajax({
                        url: '{{ route('pengajuan-kredit.save-data-periode-aspek-keuangan') }}',
                        type: 'POST',
                        data: {
                            perhitungan_kredit_id: res1.lastId,
                            bulan: selectValueElementBulan,
                            tahun: selectElementTahun,
                        },
                        beforeSend: function() {
                            $('#loading-simpan-perhitungan').show();
                            $('#perhitunganModalAfterLoading').hide();
                        },
                        success: function (response2) {
                            console.log(response2);
                            periodePerhitunganKreditId = response2.lastId;
                            periodePerhitunganKreditLastId = response2.result.perhitungan_kredit_id;
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }else{
                    console.log(periodePerhitunganKreditId);
                    $.ajax({
                        url: '{{ route('pengajuan-kredit.update-data-periode-aspek-keuangan') }}' + '?id=' + periodePerhitunganKreditId,
                        type: 'PUT',
                        data: {
                            perhitungan_kredit_id: periodePerhitunganKreditLastId,
                            bulan: selectValueElementBulan,
                            tahun: selectElementTahun,
                        },
                        beforeSend: function() {
                            $('#loading-simpan-perhitungan').show();
                            $('#perhitunganModalAfterLoading').hide();
                        },
                        success: function (response2) {
                            console.log("PERIODE = " + JSON.stringify(response2));
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }
                var lev1Count = 0;
                for (const element of res2.result) {
                    lev1Count += 1;
                    if (lev1Count > 1) {
                        if (element.field == "Laba Rugi") {
                            $('#row_perhitungan_kredit').append(`
                                <div class="form-group col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">${element.field}</h5>
                                        <div class="card-body">
                                            <table class="table table-bordered" id="lev1_count_dua">
                                            </table>
                                        </div>
                                    </div>
                                </div>
                        `   );
                        }
                    }else{
                        $('#row_perhitungan_kredit').append(`
                            <div class="form-group col-md-12">
                                <div class="card">
                                    <h5 class="card-header">${element.field}</h5>
                                    <div class="card-body">
                                        <div class="row" id="lev_count_satu">
                                        </div>
                                    </div>
                                </div>
                            </div>
                    `   );
                    }

                    const res3 = await $.ajax({
                        url: '{{ route('pengajuan-kredit.get-data-perhitungan-kredit-lev2') }}?parent_id=' + element.id,
                        type: "GET",
                        beforeSend: function() {
                            $('#loading-simpan-perhitungan').show();
                            $('#perhitunganModalAfterLoading').hide();
                        },
                    });
                    console.log(res3);
                    var lev2Count = 0;
                    for (const element2 of res3.result) {
                        lev2Count += 1;
                        var uniqueTableId = `itemPerhitunganKreditLev2_${element2.id}`;
                        var uniqueTableId2 = `lev1_count_dua_${element2.id}`;

                        if (lev1Count > 1) {
                            if (element.field == 'Laba Rugi') {
                                var row = $('<tr>');
                                row.append($("<th>").text(element2.field));
                                row.append($("<th>").text(''));
                                if (lev2Count === 1) {
                                    row.append($("<th>").text("Sebelum Kredit"));
                                    row.append($("<th>").text("Sesudah Kredit"));
                                }else{
                                    row.append($("<th>").attr("colspan", 2));
                                }
                                $('#lev1_count_dua').append(row);
                            }else{
                                if(element2.field != "Maksimal Pembiayaan" && element2.field != "Plafon dan Tenor"){
                                    $('#table_perhitungan_kredit_lev3_noparent').append(`
                                        <div class="col-md-${element2.field === 'Kebutuhan Modal Kerja' || element2.field === 'Modal Kerja Sekarang' ? `6` : `12`}">
                                            <div class="card">
                                                <h5 class="card-header">${element2.field}</h5>
                                                <div class="card-body">
                                                    <table class="table table-bordered" id="${uniqueTableId2}">
                                                    </table>
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                `   );
                                }else{
                                    if (element2.field == "Maksimal Pembiayaan") {
                                        $('#row_max_pembiayaan').append(`
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <h5 class="card-header">${element2.field}</h5>
                                                    <div class="card-body">
                                                        <table class="table table-bordered" id="table_max_pembiayaan">
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                    `   );
                                    }else{
                                        $('#row_plafon').append(`
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <h5 class="card-header">${element2.field}</h5>
                                                    <div class="card-body">
                                                        <table class="table table-bordered" id="table_plafon">
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                    `   );
                                    }
                                }
                            }
                        }else{
                            $('#lev_count_satu').append(`
                                <div class="form-group col-md-6">
                                    <table class="table table-bordered" id="${uniqueTableId}">
                                        <tr>
                                            <th colspan="2">${element2.field}</th>
                                        </tr>
                                    </table>
                                    <div class="d-flex w-100" style="padding: 0">
                                        <div class="w-100">
                                            <hr style="border: none; height: 1px; color: #333; background-color: #333;">
                                        </div>
                                        <div class="w-0 ms-2">
                                            +
                                        </div>
                                    </div>
                                    <table class="table table-bordered" id="total_lev1${element2.id}">
                                    </table>
                                </div>
                            `);
                        }

                        const res4 = await getDataPerhitunganKreditLev2(element2, res1.request.idCalonNasabah);
                        console.log(res4);

                        var angsuranPokokSetiapBulanCount = 0;
                        var lev3Count = 0;
                        var maxRowCount = 0;
                        var lengthPlafonUsulan = 0;
                        if (lev1Count > 1) {
                            $.each(res4.result, function(index, itemAspekKeuangan3) {
                                var fieldValue = itemAspekKeuangan3.field;
                                var nominal = itemAspekKeuangan3.nominal;
                                lev3Count += 1;
                                console.log(itemAspekKeuangan3);

                                if (element.field == 'Laba Rugi') {
                                    if (!fieldValues.includes(fieldValue)) {
                                        var rowLevel3 = `
                                            <tr>
                                                <td>${fieldValue}</td>
                                                <td style="text-align: center">:</td>
                                                <td class="text-${itemAspekKeuangan3.align}">Rp ${formatRupiah(String(nominal), '')}</td>
                                                <td class="text-${itemAspekKeuangan3.align}">`;

                                        var isFirstNominalDisplayed = false;

                                        res4.result.forEach(function(item) {
                                            if (item.field === fieldValue) {
                                                if (isFirstNominalDisplayed) {
                                                    rowLevel3 += `Rp ${formatRupiah(String(item.nominal), '')}`;
                                                } else {
                                                    isFirstNominalDisplayed = true;
                                                }
                                            }
                                        });

                                        rowLevel3 += `
                                                </td>
                                            </tr>
                                        `;

                                        $('#lev1_count_dua').append(rowLevel3);
                                        fieldValues.push(fieldValue);
                                    }
                                }else{
                                    if(element2.field != "Maksimal Pembiayaan" && element2.field != "Plafon dan Tenor"){
                                        $(`#${uniqueTableId2}`).append(`
                                            <tr>
                                                <td width="47%">${fieldValue}</td>
                                                <td width="6%" style="text-align: center">:</td>
                                                ${itemAspekKeuangan3.add_on === "Bulan" ? `
                                                    <td class="text-${itemAspekKeuangan3.align}">${nominal} ${itemAspekKeuangan3.add_on}</td>
                                                ` : `
                                                    <td class="text-${itemAspekKeuangan3.align}">Rp ${formatRupiah(String(nominal), '')}</td>
                                                `}

                                          </tr>
                                        `);
                                    }else{
                                        if (element2.field == "Maksimal Pembiayaan") {
                                            if (fieldValue != 'Kebutuhan Kredit') {
                                                $('#table_max_pembiayaan').append(`
                                                    <tr>
                                                        <td width="47%">${fieldValue}</td>
                                                        <td width="6%" style="text-align: center">:</td>
                                                        <td class="text-${itemAspekKeuangan3.align}">Rp ${formatRupiah(String(nominal), '')}</td>
                                                    </tr>
                                                `);
                                            }else{
                                                $('#table_max_pembiayaan').after(`
                                                    <table class="table table-borderless" style="margin: 0 auto; padding: 0 auto;">
                                                        <tr>
                                                            <td width="47%"></td>
                                                            <td width="6%"></td>
                                                            <td width="" style="padding: 0">
                                                                <div class="d-flex w-100">
                                                                    <div class="w-100">
                                                                        <hr style="border: none; height: 1px; color: #333; background-color: #333;">
                                                                    </div>
                                                                    <div class="w-0 ms-2">
                                                                        +
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <td width="47%">${fieldValue}</td>
                                                            <td width="6%" style="text-align: center">:</td>
                                                            <td class="text-${itemAspekKeuangan3.align}">Rp ${formatRupiah(String(nominal), '')}</td>
                                                        </tr>
                                                    </table>
                                                `);
                                            }
                                        }else{
                                            lengthPlafonUsulan += 1;
                                            if (fieldValue != "Bunga Anuitas Usulan (P.a)") {
                                                $('#table_plafon').append(`
                                                    <tr id="plafon_tenor${lengthPlafonUsulan}">
                                                        <td width="47%">${fieldValue}</td>
                                                        <td width="6%" style="text-align: center">:</td>
                                                        ${itemAspekKeuangan3.add_on === "Bulan" || itemAspekKeuangan3.add_on === "%" ? `
                                                            <td class="text-${itemAspekKeuangan3.align}">${nominal} ${itemAspekKeuangan3.add_on}</td>
                                                        ` : `
                                                            <td class="text-${itemAspekKeuangan3.align}">Rp ${formatRupiah(String(nominal), '')}</td>
                                                        `}
                                                    </tr>
                                                `);
                                            }else{
                                                $('#plafon_tenor1').after(`
                                                    <tr id="plafon_tenor${lengthPlafonUsulan}">
                                                        <td width="47%">${fieldValue}</td>
                                                        <td width="6%" style="text-align: center">:</td>
                                                        ${itemAspekKeuangan3.add_on === "Bulan" || itemAspekKeuangan3.add_on === "%" ? `
                                                            <td class="text-${itemAspekKeuangan3.align}">${nominal} ${itemAspekKeuangan3.add_on}</td>
                                                        ` : `
                                                            <td class="text-${itemAspekKeuangan3.align}">Rp ${formatRupiah(String(nominal), '')}</td>
                                                        `}
                                                    </tr>
                                                `);
                                            }
                                        }
                                    }
                                }
                            });
                        }else{
                            for (const element3 of res4.result) {
                                if (element3.field != "Total Angsuran") {
                                    if (element3.field === "Total") {
                                        $(`#total_lev1${element2.id}`).append(`
                                            <tr>
                                                <td width='57%'>${element3.field}</td>
                                                <td class="text-${element3.align}">Rp ${ formatRupiah(String(element3.nominal), '') }</td>
                                            </tr>
                                        `);
                                    }else{
                                        $(`#${uniqueTableId}`).append(`
                                            <tr>
                                                <td width='57%'>${element3.field}</td>
                                                <td class="text-${element3.align}">Rp ${ formatRupiah(String(element3.nominal), '') }</td>
                                            </tr>
                                        `);
                                    }
                                }
                            }
                        }
                    }
                }

            } catch (error) {
                console.error(error);
                $('#perhitunganModalAfterLoading').hide();
                $('#loading-simpan-perhitungan').hide();
                  $('.modal').modal('hide');
          }
        }


        getDataPerhitunganKreditLev1();
        // $('#perhitunganModalAfterLoading').hide();
        // setTimeout(function(){
        //     $('#loading-simpan-perhitungan').hide();
        // }, 2000);
        // setTimeout(function(){
        //     $('.modal').modal('hide');
        // }, 2000);

    });

    $("#jumlah_kredit").keyup(function(){
        var maxKredit = parseInt($("#max_kredit").val())
        var jumlahKredit = parseInt($("#jumlah_kredit").val() != '' ? $("#jumlah_kredit").val().replaceAll('.', '') : 0);

        if(jumlahKredit > maxKredit){
            $(".info_jumlah_kredit_limit").empty();
            $(".info_jumlah_kredit_limit").append(`
            <div class="alert alert-danger" role="alert">
                Jumlah kredit yang diminta tidak boleh melebihi limit kredit.
            </div>
            `)
        } else {
            $(".info_jumlah_kredit_limit").empty()
        }
    })
</script>
@endpush
