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

        span.filenameBukti {
            position: absolute;
            top: 10px;
            left: 139px;
            background: white;
        }

        .file-wrapper span.filenameBukti {
            top: 10px;
        }
    </style>
    <form id="pengajuan_kredit" action="{{ route('pengajuan-kredit.update', $dataUmum->id) }}" enctype="multipart/form-data"
        method="post">
        @method('PUT')
        @csrf
        <input type="hidden" name="progress" class="progress">
        <input type="hidden" name="id_nasabah" value="{{ $dataUmum->id_calon_nasabah }}">
        <div class="form-wizard active" data-index='0' data-done='true'>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="">Nama Lengkap</label>
                    <input type="text" name="name" id="nama" maxlength="255"
                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $dataUmum->nama) }}"
                        placeholder="Nama sesuai dengan KTP">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="">{{ $itemSP->nama }}</label>
                    @php
                        $jawabanFotoSP = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                            ->where('id_jawaban', 145)
                            ->first();
                    @endphp
                    <input type="hidden" name="id_file_text[]" value="{{ $itemSP->id }}">
                    <label for="update_file" style="display: none" id="nama_file">{{ $jawabanFotoSP?->opsi_text }}</label>
                    <input type="file" name="update_file[]" value="{{ $jawabanFotoSP?->opsi_text }}"
                        id="surat_permohonan" placeholder="Masukkan informasi {{ $itemSP?->nama }}"
                        class="form-control limit-size">
                    <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                    <input type="hidden" name="id_update_file[]" value="{{ $jawabanFotoSP?->id }}">
                    @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                        <div class="invalid-feedback">
                            {{ $errors->first('dataLevelDua.' . $key) }}
                        </div>
                    @endif
                    <span class="filename" style="display: inline;">{{ $jawabanFotoSP?->opsi_text }}</span>
                </div>
                <div class="form-group col-md-4">
                    <label for="">Kabupaten</label>
                    <select name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror select2"
                        id="kabupaten">
                        <option value="0">---Pilih Kabupaten----</option>
                        @foreach ($allKab as $item)
                            <option value="{{ $item->id }}"
                                {{ $item->id == $dataUmum->id_kabupaten ? 'selected' : '' }}>
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
                    <select name="kec" id="kecamatan" class="form-control @error('kec') is-invalid @enderror  select2">
                        <option value="0">---Pilih Kecamatan----</option>
                        @foreach ($allKec as $kec)
                            <option value="{{ $kec->id }}"
                                {{ $kec->id == $dataUmum->id_kecamatan ? 'selected' : '' }}>
                                {{ $kec->kecamatan }}</option>
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
                    <select name="desa" id="desa" class="form-control @error('desa') is-invalid @enderror select2">
                        <option value="0">---Pilih Desa----</option>
                        @foreach ($allDesa as $desa)
                            <option value="{{ $desa->id }}" {{ $desa->id == $dataUmum->id_desa ? 'selected' : '' }}>
                                {{ $desa->desa }}</option>
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
                    <textarea name="alamat_rumah" class="form-control @error('alamat_rumah') is-invalid @enderror" maxlength="255"
                        id="" cols="30" rows="4" placeholder="Alamat Rumah disesuaikan dengan KTP">{{ old('alamat_rumah', $dataUmum->alamat_rumah) }}</textarea>
                    @error('alamat_rumah')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <hr>
                </div>
                <div class="form-group col-md-12">
                    <label for="">Alamat Usaha</label>
                    <textarea name="alamat_usaha" class="form-control @error('alamat_usaha') is-invalid @enderror" maxlength="255"
                        id="" cols="30" rows="4" placeholder="Alamat Usaha disesuaikan dengan KTP">{{ old('alamat_usaha', $dataUmum->alamat_usaha) }}</textarea>
                    @error('alamat_usaha')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">No. KTP</label>
                    <input type="text" name="no_ktp" maxlength="255"
                        class="form-control @error('no_ktp') is-invalid @enderror" id=""
                        value="{{ old('no_ktp', $dataUmum->no_ktp) }}" placeholder="Masukkan 16 digit No. KTP"  onkeydown="return event.keyCode !== 69" name="no_ktp">
                    @error('no_ktp')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                @if ($dataUmum->status == 'menikah')
                    <div class="form-group col-md-6" id="foto-ktp-suami">
                        @php
                            $jawabanFotoKTPSu = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                ->where('id_jawaban', 150)
                                ->first();
                        @endphp
                        <label for="">Foto KTP Suami</label>
                        <input type="hidden" name="id_file_text[]" value="150" id="">
                        @if (isset($jawabanFotoKTPSu->opsi_text) != null)
                            <label for="update_file" style="display: none"
                                id="nama_file">{{ $jawabanFotoKTPSu->opsi_text }}</label>
                            <input type="file" name="update_file[]" id=""
                                placeholder="Masukkan informasi Foto KTP Suami" class="form-control limit-size"
                                value="{{ $jawabanFotoKTPSu->opsi_text }}">
                            <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                boleh lebih dari 5 MB</span>
                        @else
                            <label for="update_file" style="display: none" id="nama_file">Belum Upload Foto KTP
                                Suami</label>
                            <input type="file" name="update_file[]" id=""
                                placeholder="Masukkan informasi Foto KTP Suami" class="form-control limit-size"
                                value="Belum Upload Foto KTP Suami">
                            <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                boleh lebih dari 5 MB</span>
                        @endif
                        <input type="hidden" name="id_update_file[]" value="{{ $jawabanFotoKTPSu->id ?? '' }}">
                        @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                            <div class="invalid-feedback">
                                {{ $errors->first('dataLevelDua.' . $key) }}
                            </div>
                        @endif
                        <span class="filename" style="display: inline;">{{ $jawabanFotoKTPSu?->opsi_text }}</span>
                        {{-- <span class="alert alert-danger">Maximum file upload is 5 MB</span> --}}
                    </div>
                    <div class="form-group col-md-6" id="foto-ktp-istri">
                        @php
                            $jawabanFotoKTPIs = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                ->where('id_jawaban', 151)
                                ->first();
                        @endphp
                        <label for="">Foto KTP Istri</label>
                        <input type="hidden" name="id_file_text[]" value="151" id="">
                        @if (isset($jawabanFotoKTPIs->opsi_text) != null)
                            <label for="update_file" style="display: none"
                                id="nama_file">{{ $jawabanFotoKTPIs->opsi_text }}</label>
                            <input type="file" name="update_file[]" id=""
                                placeholder="Masukkan informasi Foto KTP Istri" class="form-control limit-size"
                                value="{{ $jawabanFotoKTPIs->opsi_text }}">
                            <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                boleh lebih dari 5 MB</span>
                        @else
                            <label for="update_file" style="display: none" id="nama_file">Belum Upload Foto KTP
                                Istri</label>
                            <input type="file" name="update_file[]" id=""
                                placeholder="Masukkan informasi Foto KTP Istri" class="form-control limit-size"
                                value="Belum Upload Foto KTP Istri">
                            <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                boleh lebih dari 5 MB</span>
                        @endif
                        <input type="hidden" name="id_update_file[]" value="{{ $jawabanFotoKTPIs->id ?? '' }}">
                        @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                            <div class="invalid-feedback">
                                {{ $errors->first('dataLevelDua.' . $key) }}
                            </div>
                        @endif
                        <span class="filename" style="display: inline;">{{ $jawabanFotoKTPIs?->opsi_text }}</span>
                        {{-- <span class="alert alert-danger">Maximum file upload is 5 MB</span> --}}
                    </div>
                @else
                    <div class="form-group col-md-12" id="foto-ktp-nasabah">
                        @php
                            $jawabanFotoKTPNas = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                ->where('id_jawaban', $itemKTPNas->id)
                                ->first();
                        @endphp
                        <label for="">Foto KTP Nasabah</label>
                        <input type="hidden" name="id_file_text[]" value="{{ $itemKTPNas->id }}" id="">
                        @if (isset($jawabanFotoKTPNas->opsi_text) != null)
                            <label for="update_file" style="display: none"
                                id="nama_file">{{ $jawabanFotoKTPNas->opsi_text }}</label>
                            <input type="file" name="update_file[]" id=""
                                placeholder="Masukkan informasi Foto KTP Nasabah" class="form-control limit-size"
                                value="{{ $jawabanFotoKTPNas->opsi_text }}">
                            <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                boleh lebih dari 5 MB</span>
                        @else
                            <label for="update_file" style="display: none" id="nama_file">Belum Upload Foto KTP
                                Nasabah</label>
                            <input type="file" name="update_file[]" id=""
                                placeholder="Masukkan informasi Foto KTP Nasabah" class="form-control limit-size"
                                value="Belum Upload Foto KTP Suami">
                            <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                boleh lebih dari 5 MB</span>
                        @endif
                        <input type="hidden" name="id_update_file[]" value="{{ $jawabanFotoKTPNas->id ?? '' }}">
                        @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                            <div class="invalid-feedback">
                                {{ $errors->first('dataLevelDua.' . $key) }}
                            </div>
                        @endif
                        <span class="filename" style="display: inline;">{{ $jawabanFotoKTPNas?->opsi_text }}</span>
                    </div>
                @endif
                <div class="" id="foto-ktp-suami">
                </div>
                <div class="" id="foto-ktp-istri">
                </div>
                <div class="" id="foto-ktp-nasabah">
                </div>
                <div class="form-group col-md-12">
                    <label for="">Pekerjaan</label>
                    <input type="text" maxlength="255" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror"
                        id="pekerjaan" placeholder="Masukkan Pekerjaan" value="{{ $dataUmum?->pekerjaan ?? '' }}">
                    @error('pekerjaan')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Tempat</label>
                    <input type="text" name="tempat_lahir" maxlength="255" id=""
                        class="form-control @error('tempat_lahir') is-invalid @enderror"
                        value="{{ old('tempat_lahir', $dataUmum->tempat_lahir) }}" placeholder="Tempat Lahir">
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
                        value="{{ old('tanggal_lahir', $dataUmum->tanggal_lahir) }}" placeholder="dd-mm-yyyy">
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Status</label>
                    <select name="status" id=""
                        class="form-control @error('status') is-invalid @enderror select2">
                        <option value=""> --Pilih Status --</option>
                        <option value="menikah" {{ old('status', $dataUmum->status) == 'menikah' ? 'selected' : '' }}>
                            Menikah</option>
                        <option value="belum menikah"
                            {{ old('status', $dataUmum->status) == 'belum menikah' ? 'selected' : '' }}>Belum Menikah
                        </option>
                        <option value="duda" {{ old('status', $dataUmum->status) == 'duda' ? 'selected' : '' }}>Duda
                        </option>
                        <option value="janda" {{ old('status', $dataUmum->status) == 'janda' ? 'selected' : '' }}>Janda
                        </option>
                    </select>
                    @error('status')
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
                        <option value="perdagangan"
                            {{ old('sektor_kredit', $dataUmum->sektor_kredit) == 'perdagangan' ? 'selected' : '' }}>
                            Perdagangan</option>
                        <option value="perindustrian"
                            {{ old('sektor_kredit', $dataUmum->sektor_kredit) == 'perindustrian' ? 'selected' : '' }}>
                            Perindustrian</option>
                        <option value="dll"
                            {{ old('sektor_kredit', $dataUmum->sektor_kredit) == 'dll' ? 'selected' : '' }}>dll</option>
                    </select>
                    @error('sektor_kredit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="">{{ $itemSlik->nama }}</label>
                    @php
                        $jawabanSlik = \App\Models\JawabanPengajuanModel::whereIn('id_jawaban', [71, 72, 73, 74])
                            ->orderBy('id', 'DESC')
                            ->where('id_pengajuan', $dataUmum->id)
                            ->get();

                        for ($i = 0; $i < count($jawabanSlik); $i++) {
                            $data[] = $jawabanSlik[$i]['id_jawaban'];
                        }
                        if (count($jawabanSlik) == 0) {
                            $data[] = null;
                        }
                    @endphp
                    <select name="dataLevelDua[]" id="dataLevelDua" class="form-control select2"
                        data-id_item={{ $itemSlik->id }}>
                        <option value=""> --Pilih Data -- </option>
                        @foreach ($itemSlik->option as $itemJawaban)
                            <option value="{{ $itemJawaban->skor . '-' . $itemJawaban->id }}"
                                {{ in_array($itemJawaban->id, $data) ? 'selected' : '' }}>
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
                    @php
                        $jawabanLaporanSlik = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                            ->where('id_jawaban', 146)
                            ->first();
                    @endphp
                    <label for="">Laporan SLIK</label>
                    <input type="hidden" name="id_file_text[]" value="146" id="">
                    <label for="update_file" style="display: none"
                        id="nama_file">{{ $jawabanLaporanSlik?->opsi_text }}</label>
                    <input type="file" name="update_file[]" id="laporan_slik"
                        placeholder="Masukkan informasi Laporan SLIK" class="form-control limit-size"
                        value="{{ $jawabanLaporanSlik?->opsi_text }}">
                    <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                    <input type="hidden" name="id_update_file[]" value="{{ $jawabanLaporanSlik?->id }}">
                    @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                        <div class="invalid-feedback">
                            {{ $errors->first('dataLevelDua.' . $key) }}
                        </div>
                    @endif
                    <span class="filename" style="display: inline;">{{ $jawabanLaporanSlik?->opsi_text }}</span>
                    {{-- <span class="alert alert-danger">Maximum file upload is 5 MB</span> --}}
                </div>
                <div class="form-group col-md-12">
                    <label for="">Jenis Usaha</label>
                    <textarea name="jenis_usaha" class="form-control @error('jenis_usaha') is-invalid @enderror" maxlength="255"
                        id="" cols="30" rows="4" placeholder="Jenis Usaha secara spesifik">{{ old('jenis_usaha', $dataUmum->jenis_usaha) }}</textarea>
                    @error('jenis_usaha')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="">Jumlah Kredit yang diminta</label>
                    <input type="text" name="jumlah_kredit"
                        class="form-control rupiah @error('jumlah_kredit') is-invalid @enderror" id="jumlah_kredit"
                        cols="30" rows="4" placeholder="Jumlah Kredit"
                        value="{{ old('jumlah_kredit', $dataUmum->jumlah_kredit) }}">
                    @error('jumlah_kredit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="">Tenor Yang Diminta</label>
                    {{--  <select name="tenor_yang_diminta" id="tenor_yang_diminta"
                        class="form-control select2 @error('tenor_yang_diminta') is-invalid @enderror" required>
                        <option value="">-- Pilih Tenor --</option>
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ ($i == $dataUmum->tenor_yang_diminta) ? 'selected' : '' }}> {{ $i . ' tahun' }} </option>
                        @endfor
                    </select>  --}}
                    <div class="input-group">
                        <input type="text" name="tenor_yang_diminta" id="tenor_yang_diminta"
                            class="form-control only-number @error('tenor_yang_diminta') is-invalid @enderror"
                            aria-describedby="addon_tenor_yang_diminta" value="{{ $dataUmum->tenor_yang_diminta }}"
                            required maxlength="3" />
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
                    <textarea name="tujuan_kredit" class="form-control @error('tujuan_kredit') is-invalid @enderror" maxlength="255"
                        id="" cols="30" rows="4" placeholder="Tujuan Kredit">{{ old('tujuan_kredit', $dataUmum->tujuan_kredit) }}</textarea>
                    @error('tujuan_kredit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Jaminan yang disediakan</label>
                    <textarea name="jaminan" class="form-control @error('jaminan') is-invalid @enderror" maxlength="255" id=""
                        cols="30" rows="4" placeholder="Jaminan yang disediakan">{{ old('jaminan', $dataUmum->jaminan_kredit) }}</textarea>
                    @error('jaminan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Hubungan Bank</label>
                    <textarea name="hubungan_bank" class="form-control @error('hubungan_bank') is-invalid @enderror" maxlength="255"
                        id="" cols="30" rows="4" placeholder="Hubungan dengan Bank">{{ old('hubungan_bank', $dataUmum->hubungan_bank) }}</textarea>
                    @error('hubungan_bank')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Hasil Verifikasi</label>
                    <textarea name="hasil_verifikasi" class="form-control @error('hasil_verifikasi') is-invalid @enderror"
                        maxlength="255" id="" cols="30" rows="4" placeholder="Hasil Verifikasi Karakter Umum">{{ old('hasil_verifikasi', $dataUmum->verifikasi_umum) }}</textarea>
                    @error('hasil_verifikasi')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <hr>
                </div>
            </div>
        </div>

        @if ($dataUmum->skema_kredit == 'KKB')
            @php
                $keterangan = $dataPO->keterangan;
                $pemesanan = str_replace('Pemesanan ', '', $keterangan);
            @endphp
            <div class="form-wizard" data-index='1' data-done='true'>
                <div class="row" id="data-po">
                    <div class="form-group col-md-12">
                        <span style="color: black; font-weight: bold; font-size: 18px;">Jenis Kendaraan Roda 2 :</span>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Merk Kendaraan</label>
                        <input type="text" name="merk" id="merk" class="form-control @error('merk') is-invalid @enderror"
                            placeholder="Merk kendaraan" value="{{$dataPO->merk}}">
                        @error('merk')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        {{--  <select name="id_merk" id="id_merk" class="select2 form-control" style="width: 100%;"
                            required>
                            <option value="">Pilih Merk Kendaraan</option>
                            @foreach ($dataMerk as $item)
                                <option value="{{ $item->id }}"
                                    {{ $dataPOMerk->id_merk == $item->id ? 'selected' : '' }}>{{ $item->merk }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_merk')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror  --}}
                    </div>
                    <div class="form-group col-md-6">
                        <label>Tipe Kendaraan</label>
                        <input type="text" name="tipe_kendaraan" id="tipe_kendaraan" class="form-control @error('tipe_kendaraan') is-invalid @enderror" placeholder="Tipe kendaraan" value="{{$dataPO->tipe}}">
                        @error('tipe_kendaraan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        {{--  <select name="id_tipe" id="id_tipe" class="select2 form-control" style="width: 100%;"
                            required>
                            <option value="">Pilih Tipe</option>
                        </select>
                        @error('id_tipe')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror  --}}
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Tahun</label>
                        <input type="number" name="tahun" id="tahun"
                            class="form-control @error('tahun') is-invalid @enderror" placeholder="Tahun Kendaraan"
                            value="{{ $dataPO?->tahun_kendaraan ?? '' }}" min="2000">
                        @error('tahun')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Warna</label>
                        <input type="text" name="warna" id="warna" maxlength="255"
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
                            class="form-control @error('pemesanan') is-invalid @enderror"
                            placeholder="Pemesanan Kendaraan" value="{{ $pemesanan ?? '' }}">
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
                            class="form-control rupiah @error('harga') is-invalid @enderror"
                            placeholder="Harga Kendaraan" value="{{ $dataPO?->harga ?? '' }}">
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
        @php
            $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban')
                ->where('id_pengajuan', $dataUmum->id)
                ->get();
        @endphp
        @foreach ($dataDetailJawaban as $itemId)
            <input type="hidden" name="id[]" value="{{ $itemId->id }}">
        @endforeach
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

                            $dataIjin = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                ->orderBy('id', 'DESC')
                                ->whereIn('item.nama', ['nib', 'surat keterangan usaha', 'tidak ada legalitas usaha'])
                                ->first();

                            $dataDetailJawabanTextnpwp = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                ->where('item.nama', 'npwp')
                                ->first();

                            $jawabanDokNPWP = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                ->where('id_jawaban', $itemNPWP->id)
                                ->first();
                            // dump($dataIjin);
                        @endphp
                        {{-- item ijin usaha --}}
                        @if ($item->nama == 'Ijin Usaha')
                            <div class="row col-md-12">
                                <div class="form-group col-md-6">
                                    <label for="">{{ $item?->nama }}</label>
                                    <select name="ijin_usaha" id="ijin_usaha" class="form-control" required>
                                        <option value="">-- Pilih Ijin Usaha --</option>
                                        <option value="nib" {{ $dataIjin?->nama == 'NIB' ? 'selected' : '' }}>NIB
                                        </option>
                                        <option value="surat_keterangan_usaha"
                                            {{ $dataIjin?->nama == 'Surat Keterangan Usaha' ? 'selected' : '' }}>Surat
                                            Keterangan Usaha</option>
                                         <option value="tidak_ada_legalitas_usaha" {{ !$dataIjin ? 'selected' : '' }}>
                                            Tidak Ada Legalitas Usaha</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6" id="npwpsku"
                                    style="display: {{ $dataIjin?->nama == 'Surat Keterangan Usaha' ? '' : 'none' }}">
                                    <label for="">Memiliki NPWP</label>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="checkbox" name="isNpwp" id="isNpwp" class="form-control"
                                                @if (isset($dataDetailJawabanTextnpwp->opsi_text) != null) checked @endif>
                                            <input type="hidden" name="isNpwp" id="statusNpwp" class="form-control"
                                                value="{{ isset($dataDetailJawabanTextnpwp->opsi_text) != null ? '1' : '0' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row col-md-12">
                                <div class="form-group col-md-6" id="nib">
                                    <label for="">NIB</label>
                                    <input type="hidden" name="id_level[]" value="77" id="nib_id">
                                    <input type="hidden" name="opsi_jawaban[]" value="input text"
                                        id="nib_opsi_jawaban">
                                    <input type="text" maxlength="255" name="info_text[]" id="nib_text"
                                        id="" placeholder="Masukkan informasi" class="form-control"
                                        value="{{ $dataIjin?->nama == 'NIB' ? $dataIjin?->opsi_text : '' }}">
                                    <input type="hidden" name="skor_penyelia_text[]"
                                        value="{{ $dataIjin?->nama == 'NIB' ? $dataIjin?->skor_penyelia : null }}">
                                    <input type="hidden"name="id_text[]" id="id_nib_text" value="77">
                                    <input type="hidden" name="id_jawaban_text[]" id="id_jawaban_nib"
                                        value="{{ $dataIjin?->nama == 'NIB' ? $dataIjin?->id : '' }}">
                                </div>

                                <div class="form-group col-md-6" id="docNIB">
                                    @php
                                        $jawabanDokNIB = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                            ->where('id_jawaban', $itemNIB->id)
                                            ->first();
                                    @endphp
                                    <label for="">Dokumen NIB</label>
                                    <input type="hidden" name="id_file_text[]" value="152" id="docNIB_id">
                                    @if (isset($jawabanDokNIB->opsi_text) != null)
                                        <label for="update_file" style="display: none"
                                            id="docNIBnama_file">{{ $jawabanDokNIB->opsi_text }}</label>
                                        <input type="file" name="update_file[]" id="docNIB_update_file"
                                            placeholder="Masukkan informasi Dokumen NIB" class="form-control limit-size"
                                            value="{{ $jawabanDokNIB->opsi_text }}">

                                        <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                            boleh lebih dari 5 MB</span>
                                    @else
                                        <label for="update_file" style="display: none" id="docNIBnama_file">Belum Upload
                                            Dokumen NIB</label>
                                        <input type="file" name="update_file[]" id="docNIB_update_file"
                                            placeholder="Masukkan informasi Dokumen NIB" class="form-control limit-size"
                                            value="Belum Upload Dokumen NIB">

                                        <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                            boleh lebih dari 5 MB</span>
                                    @endif
                                    <input type="hidden" id="id_update_nib" name="id_update_file[]"
                                        value="{{ $jawabanDokNIB?->id }}">
                                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('dataLevelTiga.' . $key) }}
                                        </div>
                                    @endif
                                    {{-- <span class="alert alert-danger">Maximum file upload is 5 MB</span> --}}
                                    <span class="filename"
                                        style="display: inline;">{{ $jawabanDokNIB?->opsi_text }}</span>
                                </div>

                                <div class="form-group col-md-6" id="surat_keterangan_usaha">
                                    <label for="">Surat Keterangan Usaha</label>
                                    <input type="hidden" name="id_level[]" value="78"
                                        id="surat_keterangan_usaha_id">
                                    <input type="text" maxlength="255" name="info_text[]"
                                        placeholder="Masukkan informasi" id="surat_keterangan_usaha_text"
                                        class="form-control"
                                        value="{{ $dataIjin?->nama == 'Surat Keterangan Usaha' ? $dataIjin?->opsi_text : null }}">
                                    <input type="hidden" name="skor_penyelia_text[]" id="surat_keterangan_usaha_text"
                                        value="{{ $dataIjin?->nama == 'Surat Keterangan Usaha' ? $dataIjin?->skor_penyelia : null }}">
                                    <input type="hidden" name="id_text[]" id="id_surat_keterangan_usaha_text"
                                        value="78">
                                    <input type="hidden" name="id_jawaban_text[]" id="id_jawaban_sku"
                                        value="{{ $dataIjin?->nama == 'Surat Keterangan Usaha' ? $dataIjin?->id : '' }}">
                                </div>

                                <div class="form-group col-md-6" id="docSKU">
                                    @php
                                        $jawabanDokSKU = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                            ->where('id_jawaban', $itemSKU->id)
                                            ->first();
                                    @endphp
                                    <label for="">Surat Keterangan Usaha</label>
                                    <input type="hidden" name="id_file_text[]" value="155" id="docSKU_id">
                                    @if (isset($jawabanDokSKU->opsi_text) != null)
                                        <label for="update_file" style="display: none"
                                            id="docSKUnama_file">{{ $jawabanDokSKU->opsi_text }}</label>
                                        <input type="file" name="update_file[]" id="docSKU_update_file"
                                            placeholder="Masukkan informasi Dokumen SKU" class="form-control limit-size"
                                            value="{{ $jawabanDokSKU->opsi_text }}">

                                        <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                            boleh lebih dari 5 MB</span>
                                    @else
                                        <label for="update_file" style="display: none" id="docSKUnama_file">Belum Upload
                                            Dokumen SKU</label>
                                        <input type="file" name="update_file[]" id="docSKU_update_file"
                                            placeholder="Masukkan informasi Dokumen SKU" class="form-control limit-size"
                                            value="Belum Upload Dokumen SKU">

                                        <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                            boleh lebih dari 5 MB</span>
                                    @endif
                                    <input type="hidden" id="id_update_sku" id="id_update_sku" name="id_update_file[]"
                                        value="{{ $jawabanDokSKU->id ?? '' }}">
                                    <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                        boleh lebih dari 5 MB</span>
                                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('dataLevelTiga.' . $key) }}
                                        </div>
                                    @endif
                                    <span class="filename"
                                        style="display: inline;">{{ $jawabanDokSKU?->opsi_text }}</span>
                                    {{-- <span class="alert alert-danger">Maximum file upload is 5 MB</span> --}}
                                </div>
                            </div>
                        @elseif($item->nama == 'NPWP')
                            <div class="row col-md-12" id="npwp">
                                @if ($dataDetailJawabanTextnpwp?->opsi_text)
                                    <div class="form-group col-md-6">
                                        <label for="">NPWP</label>
                                        <input type="hidden" name="id_text[]" value="79" id="npwp_id">
                                        <input type="hidden" name="opsi_jawaban[]" value="input text"
                                            id="npwp_opsi_jawaban">
                                        <input type="text" maxlength="255" name="info_text[]" id="npwp_text"
                                            placeholder="Masukkan informasi" class="form-control"
                                            value="{{ $dataDetailJawabanTextnpwp != null ? $dataDetailJawabanTextnpwp?->opsi_text : '' }}">
                                        <input type="hidden" name="skor_penyelia_text[]" id="npwp_text"
                                            value="{{ $dataDetailJawabanTextnpwp?->skor_penyelia }}">
                                        <input type="hidden" name="id_jawaban_text[]" id="id_jawaban_npwp"
                                            value="{{ $dataDetailJawabanTextnpwp != null ? $dataDetailJawabanTextnpwp->id : null }}"
                                            @if ($dataDetailJawabanTextnpwp == null) disabled="disabled" @endif>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">Dokumen NPWP</label>
                                        <input type="hidden" name="id_file_text[]" value="153" id="docNPWP_id">
                                        @if (isset($jawabanDokNPWP->opsi_text) != null)
                                            <input type="hidden" name="id_update_file[]" value="{{$jawabanDokNPWP->id ?? ''}}"
                                                id="npwp_opsi_jawaban">
                                            <label for="update_file" style="display: none"
                                                id="docNPWPnama_file">{{ $jawabanDokNPWP->opsi_text }}</label>
                                            <input type="file" name="update_file[]" id="docNPWP_update_file"
                                                placeholder="Masukkan informasi Dokumen NPWP" class="form-control limit-size"
                                                value="{{ $jawabanDokNPWP->opsi_text }}">

                                            <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                                boleh lebih dari 5 MB</span>
                                        @else
                                            <input type="hidden" name="id_update_file[]" value="{{$jawabanDokNPWP->id ?? ''}}"
                                                id="npwp_opsi_jawaban">
                                            <label for="update_file" style="display: none" id="docNPWPnama_file">Belum Upload
                                                Dokumen NPWP</label>
                                            <input type="file" name="update_file[]" id="docNPWP_update_file"
                                                placeholder="Masukkan informasi Dokumen NPWP" class="form-control limit-size">

                                            <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                                boleh lebih dari 5 MB</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="form-group col-md-6">
                                        <label for="">NPWP</label>
                                        <input type="hidden" name="id_text[]" value="79" id="npwp_id">
                                        <input type="hidden" name="opsi_jawaban[]" value="input text"
                                            id="npwp_opsi_jawaban">
                                        <input type="text" maxlength="255" name="info_text[]" id="npwp_text"
                                            placeholder="Masukkan informasi" class="form-control"
                                            value="{{ $dataDetailJawabanTextnpwp != null ? $dataDetailJawabanTextnpwp?->opsi_text : '' }}">
                                        <input type="hidden" name="skor_penyelia_text[]" id="npwp_text"
                                            value="{{ $dataDetailJawabanTextnpwp?->skor_penyelia }}">
                                        <input type="hidden" name="id_jawaban_text[]" id="id_jawaban_npwp"
                                            value="{{ $dataDetailJawabanTextnpwp != null ? $dataDetailJawabanTextnpwp->id : null }}"
                                            @if ($dataDetailJawabanTextnpwp == null) disabled="disabled" @endif>
                                    </div>

                                    {{-- file dokumen npwp --}}
                                    <div class="form-group col-md-6" id="docNPWP">
                                        <label for="">Dokumen NPWP</label>
                                        @if (isset($jawabanDokNPWP->opsi_text) != null)
                                        <input type="hidden" name="id_file_text[]" value="153" id="docNPWP_id">
                                        <input type="hidden" name="id_update_file[]" value="{{$jawabanDokNPWP->id ?? ''}}"
                                            id="npwp_opsi_jawaban">
                                        <label for="update_file" style="display: none"
                                            id="docNPWPnama_file">{{ $jawabanDokNPWP->opsi_text }}</label>
                                        <input type="file" name="update_file[]" id="docNPWP_update_file"
                                            placeholder="Masukkan informasi Dokumen NPWP" class="form-control limit-size"
                                            value="{{ $jawabanDokNPWP->opsi_text }}">

                                        <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                            boleh lebih dari 5 MB</span>
                                        @else
                                        <input type="hidden" name="id_file_text[]" value="153" id="docNPWP_id">
                                        <input type="hidden" name="id_update_file[]" value="{{$jawabanDokNPWP->id ?? ''}}"
                                            id="npwp_opsi_jawaban">
                                        <label for="update_file" style="display: none" id="docNPWPnama_file">Belum Upload
                                            Dokumen NPWP</label>
                                        <input type="file" name="update_file[]" id="docNPWP_update_file"
                                            placeholder="Masukkan informasi Dokumen NPWP" class="form-control limit-size">

                                        <span class="invalid-tooltip" style="display: none">Besaran file tidak
                                            boleh lebih dari 5 MB</span>
                                        @endif
                                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('dataLevelTiga.' . $key) }}
                                        </div>
                                    @endif
                                    <span class="filename"
                                        style="display: inline;">{{ $jawabanDokNPWP?->opsi_text }}</span>
                                    {{-- <span class="alert alert-danger">Maximum file upload is 5 MB</span> --}}
                                </div>
                                @endif

                            </div>
                        @else
                            @php
                                $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                    ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                    ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                    ->where('jawaban_text.id_jawaban', $item->id)
                                    ->get();
                            @endphp
                            @if ($item->opsi_jawaban == 'input text')
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $item->nama }}</label>
                                        <input type="hidden" name="id_text[]" value="{{ $itemTextDua->id_item }}">
                                        <input type="text" maxlength="255" name="info_text[]"
                                            id="{{ $idLevelDua }}"
                                            placeholder="Masukkan informasi {{ $item->nama }}"
                                            value="{{ $itemTextDua != null ? $itemTextDua->opsi_text : null }}"
                                            class="form-control {{ $item->nama == 'Modal Awal Sendiri' || $item->nama == 'Modal Pinjaman' ? 'rupiah' : '' }}">
                                        <input type="hidden" name="skor_penyelia_text[]"
                                            value="{{ $itemTextDua->skor_penyelia }}">
                                        <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextDua->id }}">
                                    </div>
                                @endforeach
                            @elseif ($item->opsi_jawaban == 'number')
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    @if (
                                        $itemTextDua->nama == 'Omzet Penjualan' ||
                                            $itemTextDua->nama == 'Installment' ||
                                            $itemTextDua->nama == 'Kebutuhan Kredit')
                                        <div class="form-group col-md-6">
                                            <label for="">{{ $item->nama }}</label>
                                            <input type="hidden" name="id_text[]" value="{{ $itemTextDua->id_item }}">
                                            <input type="text" name="info_text[]" id="{{ $idLevelDua }}"
                                                placeholder="Masukkan informasi {{ $item->nama }}"
                                                value="{{ $itemTextDua->opsi_text }}" class="form-control rupiah">
                                            <input type="hidden" name="skor_penyelia_text[]"
                                                value="{{ $itemTextDua->skor_penyelia }}">
                                            <input type="hidden" name="id_jawaban_text[]"
                                                value="{{ $itemTextDua->id }}">
                                        </div>
                                    @else
                                        <div class="form-group col-md-6">
                                            <label for="">{{ $item->nama }}</label>
                                            <input type="text" maxlength="255" name="info_text[]"
                                                id="{{ $idLevelDua }}"
                                                placeholder="Masukkan informasi {{ $item->nama }}"
                                                value="{{ $itemTextDua != null ? $itemTextDua->opsi_text : null }}"
                                                class="form-control">
                                            <input type="hidden" name="skor_penyelia_text[]"
                                                value="{{ $itemTextDua->skor_penyelia }}">
                                            <input type="hidden" name="id_text[]" value="{{ $itemTextDua->id_item }}">
                                            <input type="hidden" name="id_jawaban_text[]"
                                                value="{{ $itemTextDua->id }}">
                                        </div>
                                    @endif
                                @endforeach
                            @elseif ($item->opsi_jawaban == 'persen')
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $item->nama }}</label>
                                        <div class="input-group mb-3">
                                            <input type="number" step="any" name="info_text[]"
                                                id="{{ $idLevelDua }}"
                                                placeholder="Masukkan informasi {{ $item->nama }}"
                                                class="form-control" aria-label="Recipient's username"
                                                aria-describedby="basic-addon2"
                                                value="{{ $itemTextDua != null ? $itemTextDua->opsi_text : null }}"  onkeydown="return event.keyCode !== 69" name="no_ktp">
                                            <input type="hidden" name="skor_penyelia_text[]"
                                                value="{{ $itemTextDua->skor_penyelia }}">
                                            <input type="hidden" name="id_text[]" value="{{ $itemTextDua->id_item }}">
                                            <input type="hidden" name="id_jawaban_text[]"
                                                value="{{ $itemTextDua->id }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">%</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif ($item->opsi_jawaban == 'file')
                                @forelse ($dataDetailJawabanText as $itemTextDua)
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $item->nama }}</label>
                                        {{-- <input type="hidden" name="opsi_jawaban[]" value="{{ $item->opsi_jawaban }}" --}}
                                        {{-- id="{{ $idLevelDua }}"> --}}
                                        <input type="hidden" name="id_file_text[]"
                                            value="{{ $itemTextDua->id_item }}">
                                        <label for="update_file" style="display: none"
                                            id="nama_file">{{ $itemTextDua->opsi_text }}</label>
                                        <input type="file" name="update_file[]" id="{{ $idLevelDua . 'file' }}"
                                            placeholder=" {{ old($item->nama, $itemTextDua->opsi_text) }}"
                                            value=" {{ $itemTextDua != null ? $itemTextDua->opsi_text : null }} "
                                            class="form-control" title="{{ $item->opsi_text }}">
                                        <input type="hidden" name="skor_penyelia_text[]"
                                            value="{{ $itemTextDua->skor_penyelia }}">
                                        <input type="hidden" name="id_update_file[]" value="{{ $itemTextDua->id }}">
                                        <span class="filename"
                                            style="display: inline;">{{ $itemTextDua?->opsi_text }}</span>
                                    </div>
                                @empty
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $item->nama }}</label>
                                        {{-- <input type="hidden" name="opsi_jawaban[]" value="{{ $item->opsi_jawaban }}" --}}
                                        {{-- id="{{ $idLevelDua }}"> --}}
                                        <input type="hidden" name="id_file_text[]" value="{{ $item->id }}">
                                        <label for="update_file" style="display: none" id="nama_file"></label>
                                        <input type="file" name="update_file[]" id="{{ $idLevelDua . 'file' }}"
                                            placeholder="" value="" class="form-control"
                                            title="{{ $item->opsi_text }}">
                                        <input type="hidden" name="skor_penyelia_text[]"
                                            value="{{ $itemTextDua->skor_penyelia }}">
                                        <input type="hidden" name="id_update_file[]" value="">
                                    </div>
                                @endforelse
                            @elseif ($item->opsi_jawaban == 'long text')
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $item->nama }}</label>
                                        <input type="hidden" name="id_text[]" value="{{ $itemTextDua->id_item }}">
                                        <textarea name="info_text[]" rows="4" id="{{ $idLevelDua }}" maxlength="255" class="form-control"
                                            placeholder="Masukkan informasi {{ $item->nama }}">{{ $itemTextDua != null ? $itemTextDua->opsi_text : null }}</textarea>
                                        <input type="hidden" name="skor_penyelia_text[]"
                                            value="{{ $itemTextDua->skor_penyelia }}">
                                        <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextDua->id }}">
                                    </div>
                                @endforeach
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
                                <div class="form-group col-md-6" id="{{ $idLevelDua }}_option">
                                    <label for="">{{ $item->nama }}</label>
                                    <select name="dataLevelDua[]" id="dataLevelDua" class="form-control">
                                        <option value=""> -- Pilih Data -- </option>
                                        @foreach ($dataJawaban as $key => $itemJawaban)
                                            @php
                                                $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban')
                                                    ->where('id_pengajuan', $dataUmum->id)
                                                    ->get();
                                                $count = count($dataDetailJawaban);
                                                for ($i = 0; $i < $count; $i++) {
                                                    $data[] = $dataDetailJawaban[$i]['id_jawaban'];
                                                }
                                            @endphp
                                            <option
                                                value="{{ ($itemJawaban->skor == null ? 'kosong' : $itemJawaban->skor) . '-' . $itemJawaban->id }}"
                                                {{ in_array($itemJawaban->id, $data) ? 'selected' : '' }}>
                                                {{ $itemJawaban->option }}</option>
                                        @endforeach
                                    </select>
                                    @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('dataLevelDua.' . $key) }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @foreach ($dataLevelTiga as $keyTiga => $itemTiga)
                                @php
                                    $idLevelTiga = str_replace(' ', '_', strtolower($itemTiga->nama));

                                    $jaminanUtama = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                        ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                        ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                        ->orderBy('id', 'DESC')
                                        ->whereIn('item.id', [101, 102, 103, 104, 105, 106, 107])
                                        ->get();

                                    $detailJawabanOption = \App\Models\JawabanPengajuanModel::where('id_pengajuan', $dataUmum->id)
                                        ->whereIn('id_jawaban', [136, 137, 141, 142])
                                        ->select('id_jawaban')
                                        ->orderBy('id', 'ASC')
                                        ->get();

                                    $jaminanTambahan = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                        ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                        ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                        ->orderBy('id', 'DESC')
                                        ->whereIn('item.id', [115, 116, 117, 118, 119])
                                        ->get();

                                    $detailJawabanOptionTambahan = \App\Models\JawabanPengajuanModel::where('id_pengajuan', $dataUmum->id)
                                        ->whereIn('id_jawaban', [145, 146, 150, 151])
                                        ->select('id_jawaban')
                                        ->orderBy('id', 'DESC')
                                        ->get();
                                @endphp

                                @if ($itemTiga->nama == 'Kategori Jaminan Utama' || $itemTiga->nama == 'Pengikatan Jaminan Utama')
                                    {{-- <div class="form-group col-md-6">
                                        @php
                                            $checkKelayakan = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor')
                                                ->where('id_pengajuan', $dataUmum->id)
                                                ->whereIn('id_jawaban', ['183','184'])
                                                ->first();
                                        @endphp
                                        @if (isset($checkKelayakan))
                                            <label for="">Kelayakan Usaha</label>
                                            <select name="dataLevelTiga[]" id="kelayakan_usaha" class="form-control cek-sub-column" data-id_item={{ $itemTiga->id }}>
                                                <option value="kosong-183" {{ $checkKelayakan->id_jawaban == 183 ? 'selected' : '' }}>Layak</option>
                                                <option value="kosong-184" {{ $checkKelayakan->id_jawaban == 184 ? 'selected' : '' }}>Tidak Layak</option>
                                            </select>
                                        @else
                                            <label for="">{{ $itemTiga->nama }}</label>
                                            <select name="kategori_jaminan_utama" id="kategori_jaminan_utama"
                                                class="form-control" required>
                                                <option value="">-- Pilih Kategori Jaminan Utama --</option>
                                                @if (count($detailJawabanOption) > 0)
                                                    <option value="Tanah"{{ ($detailJawabanOption[0]->id_jawaban == 136 || $detailJawabanOption[0]->id_jawaban == 137) ? 'selected' : '' }}>Tanah</option>
                                                    <option value="Tanah dan Bangunan" {{ ($detailJawabanOption[0]->id_jawaban == 141 || $detailJawabanOption[0]->id_jawaban == 142) ? 'selected' : '' }}>Tanah dan Bangunan</option>
                                                @else
                                                    <option value="Tanah">Tanah</option>
                                                    <option value="Tanah dan Bangunan">Tanah dan Bangunan</option>
                                                @endif
                                                <option value="Kendaraan Bermotor"{{ ($jaminanUtama[0]->id_item == 106) ? 'selected' : '' }}>Kendaraan Bermotor</option>
                                                <option value="Stock" {{ ($jaminanUtama[0]->id_item == 101) ? 'selected' : '' }}>Stock</option>
                                                <option value="Piutang" {{ ($jaminanUtama[0]->id_item == 102) ? 'selected' : '' }}>Piutang</option>
                                            </select>

                                        @endif
                                        <input type="hidden" name="id_level[]" value="{{ $itemTiga->id }}" id="">
                                        <input type="hidden" name="opsi_jawaban[]" value="{{ $itemTiga->opsi_jawaban }}"
                                            id="">
                                        <input type="text" name="info_text[]" id="" placeholder="Masukkan informasi"
                                            class="form-control">
                                    </div>

                                    <div class="form-group col-md-6" id="select_kategori_jaminan_utama">

                                    </div> --}}
                                @elseif ($itemTiga->nama == 'Kategori Jaminan Tambahan')
                                    @php
                                        $getJaminanTambahan = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                                            ->where('id_jawaban', 110)
                                            ->first();
                                    @endphp
                                    <div class="form-group col-md-6">
                                        <input type="hidden" name="id_kategori_jaminan_tambahan"
                                            value="{{ $getJaminanTambahan?->id ?? null }}">
                                        <label for="">{{ $itemTiga->nama }}</label>
                                        <select name="kategori_jaminan_tambahan" id="kategori_jaminan_tambahan"
                                            class="form-control" required>
                                            <option value="" {{ !$getJaminanTambahan ? 'selected' : '' }}>--
                                                Pilih Kategori Jaminan Tambahan --</option>
                                            <option value="Tidak Memiliki Jaminan Tambahan"
                                                {{ $getJaminanTambahan?->opsi_text == 'Tidak Memiliki Jaminan Tambahan' ? 'selected' : '' }}>
                                                Tidak Memiliki Jaminan Tambahan</option>
                                            <option value="Tanah"
                                                {{ $getJaminanTambahan?->opsi_text == 'Tanah' ? 'selected' : '' }}>Tanah
                                            </option>
                                            <option value="Kendaraan Bermotor"
                                                {{ $getJaminanTambahan?->opsi_text == 'Kendaraan Bermotor' ? 'selected' : '' }}>
                                                Kendaraan Bermotor</option>
                                            <option value="Tanah dan Bangunan"
                                                {{ $getJaminanTambahan?->opsi_text == 'Tanah dan Bangunan' ? 'selected' : '' }}>
                                                Tanah dan Bangunan</option>
                                        </select>
                                        {{-- <input type="hidden" name="id_level[]" value="{{ $itemTiga->id }}" id="">
                                        <input type="hidden" name="opsi_jawaban[]" value="{{ $itemTiga->opsi_jawaban }}"
                                            id="">
                                        <input type="text" name="info_text[]" id="" placeholder="Masukkan informasi"
                                            class="form-control"> --}}
                                    </div>

                                    <div class="form-group col-md-6" id="select_kategori_jaminan_tambahan">

                                    </div>
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
                                    @php
                                        $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                            ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                            ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                            ->where('jawaban_text.id_jawaban', $itemTiga->id)
                                            ->get();
                                    @endphp
                                    @if ($itemTiga->opsi_jawaban == 'input text')
                                        <div class="form-group col-md-6">
                                            @foreach ($dataDetailJawabanText as $itemTextTiga)
                                                <label for="">{{ $itemTiga->nama }}</label>
                                                <input type="hidden" name="id_text[]"
                                                    value="{{ $itemTextTiga->id_item }}">
                                                <input type="text" maxlength="255" name="info_text[]"
                                                    placeholder="Masukkan informasi" class="form-control"
                                                    id="{{ $idLevelTiga }}"
                                                    value="{{ $itemTextTiga->opsi_text != null ? $itemTextTiga->opsi_text : null }}">
                                                <input type="hidden" name="skor_penyelia_text[]"
                                                    value="{{ $itemTextTiga->skor_penyelia }}">
                                                <input type="hidden" name="id_jawaban_text[]"
                                                    value="{{ $itemTextTiga->id }}">
                                            @endforeach
                                        </div>
                                    @elseif ($itemTiga->opsi_jawaban == 'number')
                                        @foreach ($dataDetailJawabanText as $itemTextTiga)
                                            <div class="form-group col-md-6">
                                                <label for="">{{ $itemTiga->nama }}</label>
                                                <input type="hidden" name="id_text[]"
                                                    value="{{ $itemTextTiga->id_item }}">
                                                <input type="text" name="info_text[]" placeholder="Masukkan informasi"
                                                    class="form-control rupiah" id="{{ $idLevelTiga }}"
                                                    value="{{ $itemTextTiga->opsi_text != null ? $itemTextTiga->opsi_text : null }}">
                                                <input type="hidden" name="skor_penyelia_text[]"
                                                    value="{{ $itemTextTiga->skor_penyelia }}">
                                                <input type="hidden" name="id_jawaban_text[]"
                                                    value="{{ $itemTextTiga->id }}">
                                            </div>
                                        @endforeach
                                    @elseif ($itemTiga->opsi_jawaban == 'persen')
                                        @foreach ($dataDetailJawabanText as $itemTextTiga)
                                            <div class="form-group col-md-6">
                                                {{-- @if ($itemTiga->nama == 'Ratio Tenor Asuransi')

                                                @else --}}
                                                <label for="">{{ $itemTiga->nama }}</label>
                                                <div class="input-group mb-3">
                                                    <input type="number" step="any" name="info_text[]"
                                                        id="{{ $idLevelTiga }}"
                                                        placeholder="Masukkan informasi {{ $itemTiga->nama }}"
                                                        class="form-control" aria-label="Recipient's username"
                                                        aria-describedby="basic-addon2"
                                                        value="{{ $itemTextTiga->opsi_text != null ? $itemTextTiga->opsi_text : null }}">
                                                    <input type="hidden" name="skor_penyelia_text[]"
                                                        value="{{ $itemTextTiga->skor_penyelia }}">
                                                    <input type="hidden" name="id_jawaban_text[]"
                                                        value="{{ $itemTextTiga->id }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">%</span>
                                                    </div>
                                                    <input type="hidden" name="id_text[]"
                                                        value="{{ $itemTextTiga->id_item }}">
                                                </div>
                                                {{-- @endif --}}
                                            </div>
                                        @endforeach
                                    @elseif ($itemTiga->opsi_jawaban == 'file')
                                        @forelse ($dataDetailJawabanText as $itemTextTiga)
                                            <div class="form-group col-md-6 file-wrapper">
                                                <label for="">{{ $itemTiga->nama }}</label>
                                                <div class="row file-input">
                                                    <div class="col-md-9">
                                                        <label for="update_file" style="display: none"
                                                            id="nama_file">{{ $itemTextTiga->opsi_text }}</label>
                                                        <input type="file" name="update_file[]"
                                                            placeholder="Masukkan informasi" class="form-control"
                                                            id="{{ $idLevelTiga . 'file' }}"
                                                            value="{{ $itemTextTiga->opsi_text != null ? $itemTextTiga->opsi_text : null }}"
                                                            title="{{ $itemTextTiga->opsi_text }}">
                                                        <input type="hidden" name="id_file_text[]"
                                                            value="{{ $itemTextTiga->id_item }}">
                                                        <input type="hidden" name="skor_penyelia_text[]"
                                                            value="{{ $itemTextTiga->skor_penyelia }}">
                                                        <input type="hidden" name="id_update_file[]"
                                                            value="{{ $itemTextTiga->id }}">
                                                        <span class="filename"
                                                            style="display: inline;">{{ $itemTextTiga?->opsi_text }}</span>
                                                    </div>
                                                    @if (in_array(trim($itemTiga->nama), $multipleFiles))
                                                        <div class="col-1">
                                                            <button class="btn btn-sm btn-success btn-add-file"
                                                                type="button" data-id="{{ $itemTiga->id }}">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                        <div class="col-1">
                                                            <button class="btn btn-sm btn-danger btn-del-file"
                                                                type="button" data-id="{{ $itemTiga->id }}">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="form-group col-md-6 file-wrapper">
                                                <label for="">{{ $itemTiga->nama }}</label>

                                                <div class="row file-input">
                                                    <div class="col-md-9">
                                                        <label for="update_file" style="display: none"
                                                            id="nama_file"></label>
                                                        <input type="file" name="update_file[]"
                                                            placeholder="Masukkan informasi" class="form-control"
                                                            id="{{ $idLevelTiga . 'file' }}" value=""
                                                            title="">
                                                        <input type="hidden" name="id_file_text[]"
                                                            value="{{ $itemTiga->id }}">
                                                        <input type="hidden" name="skor_penyelia_text[]" value="">
                                                        <input type="hidden" name="id_update_file[]" value="">
                                                    </div>
                                                    @if (in_array(trim($itemTiga->nama), $multipleFiles))
                                                        <div class="col-1">
                                                            <button class="btn btn-sm btn-success btn-add-file"
                                                                type="button" data-id="{{ $itemTiga->id }}">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                        <div class="col-1">
                                                            <button class="btn btn-sm btn-danger btn-del-file"
                                                                type="button" data-id="{{ $itemTiga->id }}">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforelse
                                    @elseif ($itemTiga->opsi_jawaban == 'long text')
                                        @foreach ($dataDetailJawabanText as $itemTextTiga)
                                            <div class="form-group col-md-6">
                                                <label for="">{{ $itemTiga->nama }}</label>
                                                <textarea name="info_text[]" rows="4" id="{{ $idLevelTiga }}" maxlength="255" class="form-control"
                                                    placeholder="Masukkan informasi {{ $itemTiga->nama }}">{{ $itemTextTiga->opsi_text != null ? $itemTextTiga->opsi_text : null }}</textarea>
                                                <input type="hidden" name="id_jawaban_text[]"
                                                    value="{{ $itemTextTiga->id }}">
                                                <input type="hidden" name="skor_penyelia_text[]"
                                                    value="{{ $itemTextTiga->skor_penyelia }}">
                                                <input type="hidden" name="id_jawaban_text[]"
                                                    value="{{ $itemTextTiga->id }}">

                                                <input type="hidden" name="id_text[]"
                                                    value="{{ $itemTextTiga->id_item }}">
                                            </div>
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
                                        <div class="form-group col-md-6" id="{{ $idLevelTiga }}_option">
                                            <label for="">{{ $itemTiga->nama }}</label>
                                            <select name="dataLevelTiga[]" id="" class="form-control">
                                                <option value=""> --Pilih Opsi-- </option>
                                                @foreach ($dataJawabanLevelTiga as $itemJawabanTiga)
                                                    @php
                                                        $dataDetailJawabanTiga = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban')
                                                            ->where('id_pengajuan', $dataUmum->id)
                                                            ->get();
                                                        $count = count($dataDetailJawabanTiga);
                                                        for ($i = 0; $i < $count; $i++) {
                                                            $dataTiga[] = $dataDetailJawabanTiga[$i]['id_jawaban'];
                                                        }
                                                    @endphp
                                                    <option
                                                        value="{{ ($itemJawabanTiga->skor == null ? 'kosong' : $itemJawabanTiga->skor) . '-' . $itemJawabanTiga->id }}"
                                                        {{ in_array($itemJawabanTiga->id, $dataTiga) ? 'selected' : '' }}>
                                                        {{ $itemJawabanTiga->option }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    @foreach ($dataLevelEmpat as $keyEmpat => $itemEmpat)
                                        @php
                                            $idLevelEmpat = str_replace(' ', '_', strtolower($itemEmpat->nama));
                                        @endphp

                                        @php
                                            $dataDetailJawabanTextEmpat = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                                ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                                ->where('jawaban_text.id_jawaban', $itemEmpat->id)
                                                ->get();
                                        @endphp
                                        @if ($itemEmpat->opsi_jawaban == 'input text')
                                            @foreach ($dataDetailJawabanTextEmpat as $itemTextEmpat)
                                                <div class="form-group col-md-6">
                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                    @if ($itemEmpat->nama == 'Masa Berlaku Asuransi Penjaminan')
                                                        <div class="input-group">
                                                            <input type="text" maxlength="255" name="info_text[]"
                                                                id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan' ? '' : $idLevelEmpat }}"
                                                                placeholder="Masukkan informasi"
                                                                class="form-control only-number"
                                                                value="{{ $itemTextEmpat->opsi_text != null ? $itemTextEmpat->opsi_text : null }}">
                                                            <div class="input-group-append">
                                                                <div class="input-group-text"
                                                                    id="addon_tenor_yang_diminta">Bulan</div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <input type="text" maxlength="255" name="info_text[]"
                                                            id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan' ? '' : $idLevelEmpat }}"
                                                            placeholder="Masukkan informasi" class="form-control"
                                                            value="{{ $itemTextEmpat->opsi_text != null ? $itemTextEmpat->opsi_text : null }}">
                                                    @endif
                                                    <input type="hidden" name="skor_penyelia_text[]"
                                                        value="{{ $itemTextEmpat->skor_penyelia }}">
                                                    <input type="hidden" name="id_jawaban_text[]"
                                                        value="{{ $itemTextEmpat->id }}">
                                                    <input type="hidden" name="id_text[]"
                                                        value="{{ $itemTextEmpat->id_item }}">

                                                </div>
                                            @endforeach
                                        @elseif ($itemEmpat->opsi_jawaban == 'number')
                                            @foreach ($dataDetailJawabanTextEmpat as $itemTextEmpat)
                                                <div class="form-group col-md-6">
                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                    <input type="text" name="info_text[]"
                                                        id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan_/_ht' ? 'nilai_asuransi_penjaminan' : $idLevelEmpat }}"
                                                        placeholder="Masukkan informasi" class="form-control rupiah"
                                                        value="{{ $itemTextEmpat->opsi_text != null ? $itemTextEmpat->opsi_text : null }}">
                                                    <input type="hidden" name="skor_penyelia_text[]"
                                                        value="{{ $itemTextEmpat->skor_penyelia }}">
                                                    <input type="hidden" name="id_jawaban_text[]"
                                                        value="{{ $itemTextEmpat->id }}">
                                                    <input type="hidden" name="id_text[]"
                                                        value="{{ $itemTextEmpat->id_item }}">
                                                </div>
                                            @endforeach
                                        @elseif ($itemEmpat->opsi_jawaban == 'persen')
                                            @foreach ($dataDetailJawabanTextEmpat as $itemTextEmpat)
                                                <div class="form-group col-md-6">
                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" step="any" name="info_text[]"
                                                            id="{{ $idLevelEmpat }}"
                                                            placeholder="Masukkan informasi {{ $itemEmpat->nama }}"
                                                            class="form-control" aria-label="Recipient's username"
                                                            aria-describedby="basic-addon2"value="{{ $itemTextEmpat->opsi_text != null ? $itemTextEmpat->opsi_text : null }}">
                                                        <input type="hidden" name="id_jawaban_text[]"
                                                            value="{{ $itemTextEmpat->id }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
                                                        </div>
                                                        <input type="hidden" name="skor_penyelia_text[]"
                                                            value="{{ $itemTextTiga->skor_penyelia }}">
                                                        <input type="hidden" name="id_jawaban_text[]"
                                                            value="{{ $itemTextEmpat->id }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        @elseif ($itemEmpat->opsi_jawaban == 'file')
                                            @forelse ($dataDetailJawabanTextEmpat as $itemTextEmpat)
                                                <div class="form-group col-md-6">
                                                    <label for="">{{ $itemEmpat->nama }}ccd</label>
                                                    {{-- <input type="hidden" name="opsi_jawaban[]"
                                                        value="{{ $itemEmpat->opsi_jawaban }}" id=""> --}}
                                                    <input type="hidden" name="id_file_text[]"
                                                        value="{{ $itemTextEmpat->id_item }}">
                                                    <label for="update_file" style="display: none"
                                                        id="nama_file">{{ $itemTextEmpat->opsi_text }}</label>
                                                    <input type="file" name="update_file[]"
                                                        id="{{ $idLevelEmpat . 'file' }}"
                                                        placeholder="Masukkan informasi" class="form-control hidden"
                                                        value="{{ $itemTextEmpat->opsi_text != null ? $itemTextEmpat->opsi_text : null }}"
                                                        title="{{ $itemTextEmpat->opsi_text }}">
                                                    <input type="hidden" name="skor_penyelia_text[]"
                                                        value="{{ $itemTextEmpat->skor_penyelia }}">
                                                    <input type="hidden" name="id_update_file[]"
                                                        value="{{ $itemTextEmpat->id }}">
                                                    <span class="filename"
                                                        style="display: inline;">{{ $itemTextEmpat?->opsi_text }}</span>
                                                </div>
                                            @empty
                                                <div class="form-group col-md-6">
                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                    {{-- <input type="hidden" name="opsi_jawaban[]"
                                                        value="{{ $itemEmpat->opsi_jawaban }}" id=""> --}}
                                                    <input type="hidden" name="id_file_text[]"
                                                        value="{{ $itemEmpat->id }}">
                                                    <label for="update_file" style="display: none"
                                                        id="nama_file"></label>
                                                    <input type="file" name="update_file[]"
                                                        id="{{ $idLevelEmpat . 'file' }}"
                                                        placeholder="Masukkan informasi" class="form-control hidden"
                                                        value="">
                                                    <input type="hidden" name="skor_penyelia_text[]" value="">
                                                    <input type="hidden" name="id_update_file[]" value="">
                                                </div>
                                            @endforelse
                                        @elseif ($itemEmpat->opsi_jawaban == 'long text')
                                            @foreach ($dataDetailJawabanTextEmpat as $itemTextEmpat)
                                                <div class="form-group col-md-6">
                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                    <textarea name="info_text[]" rows="4" id="{{ $idLevelEmpat }}" maxlength="255" class="form-control"
                                                        placeholder="Masukkan informasi {{ $itemEmpat->nama }}">{{ $itemTextEmpat->opsi_text != null ? $itemTextEmpat->opsi_text : null }}</textarea>
                                                    <input type="hidden" name="skor_penyelia_text[]"
                                                        value="{{ $itemTextEmpat->skor_penyelia }}">
                                                    <input type="hidden" name="id_text[]"
                                                        value="{{ $itemTextEmpat->id_item }}">
                                                    <input type="hidden" name="id_jawaban_text[]"
                                                        value="{{ $itemTextEmpat->id }}">
                                                </div>
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

                                        @foreach ($dataOptionEmpat as $itemOptionEmpat)
                                            @if ($itemOptionEmpat->option == '-')
                                                <div class="form-group col-md-12">
                                                    <h6>{{ $itemEmpat->nama }}</h6>
                                                </div>
                                            @endif
                                        @endforeach

                                        @if (count($dataJawabanLevelEmpat) != 0)
                                            <div class="form-group col-md-6" id="{{ $idLevelEmpat }}_option">
                                                <label for="">{{ $itemEmpat->nama }}</label>
                                                <select name="dataLevelEmpat[]" id="" class="form-control">
                                                    <option value=""> --Pilih Opsi -- </option>
                                                    @foreach ($dataJawabanLevelEmpat as $itemJawabanEmpat)
                                                        @php
                                                            $dataDetailJawabanEmpat = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban')
                                                                ->where('id_pengajuan', $dataUmum->id)
                                                                ->get();
                                                            $count = count($dataDetailJawabanEmpat);
                                                            for ($i = 0; $i < $count; $i++) {
                                                                $dataEmpat[] = $dataDetailJawabanEmpat[$i]['id_jawaban'];
                                                            }
                                                        @endphp
                                                        <option
                                                            value="{{ ($itemJawabanEmpat->skor == null ? 'kosong' : $itemJawabanEmpat->skor) . '-' . $itemJawabanEmpat->id }}"
                                                            {{ in_array($itemJawabanEmpat->id, $dataEmpat) ? 'selected' : '' }}>
                                                            {{ $itemJawabanEmpat->option }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @php
                        $dataPendapatAspek = \App\Models\PendapatPerAspek::where('id_pengajuan', $dataUmum->id)
                            ->join('item', 'item.id', 'pendapat_dan_usulan_per_aspek.id_aspek')
                            ->select('pendapat_dan_usulan_per_aspek.id', 'pendapat_dan_usulan_per_aspek.id_aspek', 'pendapat_dan_usulan_per_aspek.pendapat_per_aspek', 'item.id as id_item', 'item.nama')
                            ->where('item.id', $value->id)
                            ->first();
                    @endphp

                    <div class="form-group col-md-12">
                        <hr style="border: 0.2px solid #E3E6EA;">
                        <label for="">Pendapat dan Usulan {{ $value->nama }}</label>
                        <input type="hidden" name="id_aspek[]" value="{{ $value->id }}">
                        <textarea name="pendapat_per_aspek[]" maxlength="255"
                            class="form-control @error('pendapat_per_aspek') is-invalid @enderror" id="" cols="30"
                            rows="4" placeholder="Pendapat Per Aspek">{{ $dataPendapatAspek?->pendapat_per_aspek }}</textarea>
                        <input type="hidden" name="id_jawaban_aspek[]" value="{{ $dataPendapatAspek?->id }}">
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

        @php
            $detailKomentar = \App\Models\KomentarModel::where('id_pengajuan', $dataUmum->id)->first();
        @endphp
        <div class="form-wizard" data-index='{{ count($dataAspek) + $dataIndex }}' data-done='true'>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="">Pendapat dan Usulan</label>
                    <textarea name="komentar_staff" class="form-control @error('komentar_staff') is-invalid @enderror"
                        maxlength="255" id="" cols="30" rows="4"
                        placeholder="Pendapat dan Usulan Staf/Analis Kredit" required>{{ $detailKomentar?->komentar_staff }}</textarea>
                    <input type="hidden" name="id_komentar_staff_text" value="{{ $detailKomentar?->id }}">
                    @error('komentar_staff')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <hr>
                </div>
            </div>
        </div>

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
        let dataAspekArr;
        $(window).on('load', function() {
            // $("#id_merk").trigger("change");
            dataAspekArr = <?php echo json_encode($dataAspek); ?>;
        })
        $('#nib').hide();
        $('#docNIB').hide();
        $('#docSKU').hide();
        $('#surat_keterangan_usaha').hide();
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
        $('#ratio_coverage_opsi_option').hide();
        $('#ratio_tenor_asuransi_opsi_option').hide();
        $('#persentase_kebutuhan_kredit_opsi_option').hide();
        $('#repayment_capacity_opsi_option').hide();

        let urlCekSubColumn = "{{ route('cek-sub-column') }}";
        let urlGetItemByKategoriJaminanUtama =
            "{{ route('get-item-jaminan-by-kategori-jaminan-utama') }}";
        // jaminan tambahan
        let urlGetItemByKategori = "{{ route('get-item-jaminan-by-kategori') }}";
        let urlGetIjin = "{{ route('get-ijin-usaha') }}";
        let id = parseInt("{{ $dataUmum->id }}");
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
                @php
                    $jawabanFotoKTPIs = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                        ->where('id_jawaban', 151)
                        ->first();
                @endphp
                    <label for="">Foto KTP Istri</label>
                    <input type="hidden" name="id_file_text[]" value="151" id="">
                        @if (isset($jawabanFotoKTPIs?->opsi_text) != null)
                            <label for="update_file" style="display: none" id="nama_file">{{ $jawabanFotoKTPIs?->opsi_text }}</label>
                            <input type="file" name="update_file[]" id="" placeholder="Masukkan informasi Foto KTP Istri" class="form-control limit-size" value="{{ $jawabanFotoKTPIs?->opsi_text }}">

                        <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                        @else
                            <label for="update_file" style="display: none" id="nama_file">Belum Upload Foto KTP Istri</label>
                            <input type="file" name="update_file[]" id="" placeholder="Masukkan informasi Foto KTP Istri" class="form-control limit-size" value="Belum Upload Foto KTP Istri">

                        <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                        @endif
                    <input type="hidden" name="id_update_file[]" value="{{ $jawabanFotoKTPIs?->id ?? '' }}">
                    @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                        <div class="invalid-feedback">
                            {{ $errors->first('dataLevelDua.' . $key) }}
                        </div>
                    @endif
                `)
                $("#foto-ktp-suami").append(`
                @php
                    $jawabanFotoKTPSu = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                        ->where('id_jawaban', 150)
                        ->first();
                @endphp
                    <label for="">Foto KTP Suami</label>
                    <input type="hidden" name="id_file_text[]" value="150" id="">
                        @if (isset($jawabanFotoKTPSu?->opsi_text) != null)
                            <label for="update_file" style="display: none" id="nama_file">{{ $jawabanFotoKTPSu?->opsi_text }}</label>
                            <input type="file" name="update_file[]" id="" placeholder="Masukkan informasi Foto KTP Suami" class="form-control limit-size" value="{{ $jawabanFotoKTPSu?->opsi_text }}">

                        <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                        @else
                            <label for="update_file" style="display: none" id="nama_file">Belum Upload Foto KTP Suami</label>
                            <input type="file" name="update_file[]" id="" placeholder="Masukkan informasi Foto KTP Suami" class="form-control limit-size" value="Belum Upload Foto KTP Suami">

                        <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                        @endif
                    <input type="hidden" name="id_update_file[]" value="{{ $jawabanFotoKTPSu?->id ?? '' }}">
                    @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                        <div class="invalid-feedback">
                            {{ $errors->first('dataLevelDua.' . $key) }}
                        </div>
                    @endif
                `);
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
            } else {
                $("#foto-ktp-nasabah").addClass('form-group col-md-12')
                $("#foto-ktp-nasabah").append(`
                    @php
                        $jawabanFotoKTPNas = \App\Models\JawabanTextModel::where('id_pengajuan', $dataUmum->id)
                            ->where('id_jawaban', $itemKTPNas->id)
                            ->first();
                    @endphp
                    <label for="">Foto KTP Nasabah</label>
                    <input type="hidden" name="id_file_text[]" value="156" id="">
                        @if (isset($jawabanFotoKTPNas?->opsi_text) != null)
                            <label for="update_file" style="display: none" id="nama_file">{{ $jawabanFotoKTPNas?->opsi_text }}</label>
                            <input type="file" name="update_file[]" id="" placeholder="Masukkan informasi Foto KTP Suami" class="form-control limit-size" value="{{ $jawabanFotoKTPNas?->opsi_text }}">

                        <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                        @else
                            <label for="update_file" style="display: none" id="nama_file">Belum Upload Foto KTP Suami</label>
                            <input type="file" name="update_file[]" id="" placeholder="Masukkan informasi Foto KTP Suami" class="form-control limit-size" value="Belum Upload Foto KTP Suami">

                        <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                        @endif
                    <input type="hidden" name="id_update_file[]" value="{{ $jawabanFotoKTPNas?->id ?? '' }}">
                    @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                        <div class="invalid-feedback">
                            {{ $errors->first('dataLevelDua.' . $key) }}
                        </div>
                    @endif
                `)
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
        });

        let x = 1;
        // jaminan tambahan
        getJaminanutama();
        getIjinUsaha();
        getJaminanTambahan();
        hitungRatioCoverage();
        hitungRatioTenorAsuransi();
        hitungRepaymentCapacity();

        {{--  @if ($dataUmum->skema_kredit == 'KKB')
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
                })
            })
        @endif  --}}

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
                                $("#kecamatan").append('<option value="' + kode + '">' + nama +
                                    '</option>');
                            });
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
                                $("#desa").append('<option value="' + kode + '">' + nama +
                                    '</option>');
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
                                        <input type="hidden" name="skor_penyelia_text[]"
                                            value="">
                        <input type="text" name="jawaban_sub_column[]" placeholder="Masukkan informasi tambahan" class="form-control" required>
                    </div>
                    `);
                    } else {
                        $(`#item${idItem}`).empty();
                    }
                }
            });
        });

        function getIjinUsaha() {
            let ijinUsaha = $('#ijin_usaha').val();
            if (ijinUsaha == 'nib') {
                $('#npwpsku').hide();
                $('#surat_keterangan_usaha').hide();
                $('#surat_keterangan_usaha_id').attr('disabled', true);
                $('#surat_keterangan_usaha_text').attr('disabled', true);
                $('#surat_keterangan_usaha_text').attr('disabled', true);
                $('#id_surat_keterangan_usaha_text').attr('disabled', true);
                $('#id_jawaban_surat_keterangan_usaha').attr('disabled', true);
                $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);
                $('#id_jawaban_sku').attr('disabled', true);

                $('#docSKU').hide();
                $('#docSKU_id').attr('disabled', true);
                $('#docSKUnama_file').attr('disabled', true);
                $('#docSKU_update_file').attr('disabled', true);
                $('#id_update_sku').attr('disabled', true);

                $('#nib').show();
                $('#nib_id').removeAttr('disabled');
                $('#nib_text').removeAttr('disabled');
                $('#nib_opsi_jawaban').removeAttr('disabled');
                $('#id_jawaban_nib').removeAttr('disabled');
                $('#id_nib_text').removeAttr('disabled');

                $('#docNIB').show();
                $('#docNIB_id').removeAttr('disabled');
                $('#id_nib_text').removeAttr('disabled');
                $('#id_jawaban_nib').removeAttr('disabled');
                $('#docNIBnama_file').removeAttr('disabled');
                $('#docNIB_update_file').removeAttr('disabled');
                $('#id_update_nib').removeAttr('disabled');

                $('#npwp').show();
                $('#npwp_id').removeAttr('disabled', true);
                $('#npwp_text').removeAttr('disabled', true);
                $('#npwp_text').show();
                $('#id_jawaban_npwp').removeAttr('disabled', true);
                $('#npwp_opsi_jawaban').removeAttr('disabled', true);

                $('#docNPWP').show();
                $('#docNPWP_id').removeAttr('disabled', true);
                $('#docNPWPnama_file').removeAttr('disabled', true);
                $('#docNPWP_update_file').removeAttr('disabled', true);
            } else if (ijinUsaha == 'surat_keterangan_usaha') {
                $('#npwpsku').show();
                $('#nib').hide()
                $('#nib_id').attr('disabled', true);
                $('#nib_text').attr('disabled', true);
                $('#id_nib_text').attr('disabled', true);
                $('#id_jawaban_nib').attr('disabled', true);
                $('#nib_opsi_jawaban').attr('disabled', true);
                $('#id_jawaban_nib').attr('disabled', true);

                $('#docNIB').hide();
                $('#docNIB_id').attr('disabled', true);
                $('#docNIBnama_file').attr('disabled', true);
                $('#docNIB_update_file').attr('disabled', true);
                $('#id_update_nib').attr('disabled', true);

                $('#surat_keterangan_usaha').show();
                $('#surat_keterangan_usaha_id').removeAttr('disabled');
                $('#id_surat_keterangan_usaha_text').removeAttr('disabled');
                $('#surat_keterangan_usaha_text').removeAttr('disabled');
                $('#docSKUnama_file').removeAttr('disabled');
                $('#surat_keterangan_usaha_opsi_jawaban').removeAttr('disabled');
                $('#id_jawaban_sku').removeAttr('disabled');

                $('#docSKU').show();
                $('#docSKU_id').removeAttr('disabled');
                $('#docSKU_text').removeAttr('disabled');
                $('#docSKU_update_file').removeAttr('disabled');
                var cekNpwp = "{{ isset($jawabanDokNPWP->opsi_text) != null ? 'true' : 'false' }}"
                if (cekNpwp == 'true') {
                    $('#npwp').show();
                    $('#npwp_id').removeAttr('disabled', true);
                    $('#npwp_text').removeAttr('disabled', true);
                    $('#npwp_opsi_jawaban').removeAttr('disabled', true);

                    $('#docNPWP').show();
                    $('#docNPWP_id').removeAttr('disabled', true);
                    $('#docNPWPnama_file').removeAttr('disabled', true);
                    $('#docNPWP_update_file').removeAttr('disabled', true);
                    $('#id_update_npwp').removeAttr('disabled', true);
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
                    $('#id_update_npwp').attr('disabled', true);
                }
            } else if (ijinUsaha == 'tidak_ada_legalitas_usaha') {
                $('#npwpsku').hide();
                $('#nib').hide();
                $('#nib_id').attr('disabled', true);
                $('#nib_text').attr('disabled', true);
                $('#nib_opsi_jawaban').attr('disabled', true);

                $('#docNIB').hide();
                $('#docNIB_id').attr('disabled', true);
                $('#docNIBnama_file').attr('disabled', true);
                $('#docNIB_update_file').attr('disabled', true);
                $('#id_update_nib').attr('disabled', true);
                $('#id_jawaban_nib').attr('disabled', true);
                $('#id_nib_text').attr('disabled', true);

                $('#surat_keterangan_usaha').hide();
                $('#surat_keterangan_usaha_id').attr('disabled', true);
                $('#id_surat_keterangan_usaha_text').attr('disabled', true);
                $('#surat_keterangan_usaha_text').attr('disabled', true);
                $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);
                $('#id_update_sku').attr('disabled', true);
                $('#id_jawaban_sku').attr('disabled', true);

                $('#docSKU').hide();
                $('#docSKU_id').attr('disabled', true);
                $('#docSKUnama_file').attr('disabled', true);
                $('#docSKU_update_file').attr('disabled', true);

                $('#npwp').hide();
                $('#npwp_id').attr('disabled', true);
                $('#npwp_text').attr('disabled', true);
                $('#npwp_opsi_jawaban').attr('disabled', true);

                $('#docNPWP').hide();
                $('#docNPWP_id').attr('disabled', true);
                $('#docNPWPnama_file').attr('disabled', true);
                $('#docNPWP_update_file').attr('disabled', true);
                $('#id_jawaban_npwp').attr('disabled', true);
                $('#id_update_npwp').attr('disabled', true);
            } else {
                $('#nib').hide();
                $('#nib_id').attr('disabled', true);
                $('#nib_text').attr('disabled', true);
                $('#nib_opsi_jawaban').attr('disabled', true);
                $('#id_jawaban_sku').attr('disabled', true);
                $('#id_jawaban_nib').attr('disabled', true);
                $('#id_jawaban_npwp').attr('disabled', true);
                $('#id_update_npwp').attr('disabled', true);

                $('#docNIB').hide();
                $('#docNIB_id').attr('disabled', true);
                $('#docNIBnama_file').attr('disabled', true);
                $('#docNIB_update_file').attr('disabled', true);

                $('#surat_keterangan_usaha').hide();
                $('#surat_keterangan_usaha_id').attr('disabled', true);
                $('#surat_keterangan_usaha_text').attr('disabled', true);
                $('#id_surat_keterangan_usaha_text').attr('disabled', true);
                $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);

                $('#docSKU').hide();
                $('#docSKU_id').attr('disabled', true);
                $('#docSKUnama_file').attr('disabled', true);
                $('#docSKU_update_file').attr('disabled', true);

                $('#npwp').show();
                $('#npwp_id').removeAttr('disabled', true);
                $('#npwp_text').removeAttr('disabled', true);
                $('#npwp_opsi_jawaban').removeAttr('disabled', true);

                $('#docNPWP').show();
                $('#docNPWP_id').removeAttr('disabled', true);
                $('#docNPWPnama_file').removeAttr('disabled', true);
                $('#docNPWP_update_file').removeAttr('disabled', true);
            }
        }

        // Cek Npwp
        $('#isNpwp').change(function() {
            //console.log($(this).is(':checked'));
            if ($(this).is(':checked')) {
                $("#statusNpwp").val('1')
                $('#npwp').show();
                $('#npwp_id').removeAttr('disabled');
                $('#npwp_text').removeAttr('disabled');
                $('#npwp_opsi_jawaban').removeAttr('disabled');

                $('#docNPWP').show();
                $('#docNPWP_id').removeAttr('disabled');
                $('#docNPWPnama_file').removeAttr('disabled');
                $('#docNPWP_update_file').removeAttr('disabled');
                $('#id_jawaban_npwp').removeAttr('disabled');
                $('#id_update_npwp').removeAttr('disabled');
            } else {
                $("#statusNpwp").val('0')
                $('#npwp').hide();
                $('#npwp_id').attr('disabled', true);
                $('#npwp_text').attr('disabled', true);
                $('#npwp_opsi_jawaban').attr('disabled', true);

                $('#docNPWP').hide();
                $('#docNPWP_id').attr('disabled', true);
                $('#docNPWPnama_file').attr('disabled', true);
                $('#docNPWP_update_file').attr('disabled', true);
                $('#id_jawaban_npwp').attr('disabled', true);
                $('#id_update_npwp').attr('disabled', true);
            }
        });

        function getJaminanutama() {
            //clear item
            $('#select_kategori_jaminan_utama').empty();

            // clear bukti pemilikan
            $('#bukti_pemilikan_jaminan_utama').empty();

            //get item by kategori
            let kategoriJaminanUtama = $('#kategori_jaminan_utama').val();

            $.ajax({
                type: "get",
                url: `${urlGetItemByKategoriJaminanUtama}?kategori=${kategoriJaminanUtama}&id=${id}`,
                dataType: "json",
                success: function(response) {
                    // jika kategori bukan stock dan piutang
                    if (kategoriJaminanUtama != 'Stock' && kategoriJaminanUtama != 'Piutang') {
                        if (response.dataDetailJawabanText.length > 0) {
                            if (response.belum.length > 0) {
                                $.each(response.belum, function(i, valItem) {
                                    if (valItem.nama != 'Foto') {
                                        $('#bukti_pemilikan_jaminan_tambahan').append(`
                                        <div class="form-group col-md-6 aspek_jaminan_kategori">
                                            <label>${isCheck} ${valItem.nama}</label>
                                            <input type="hidden" name="id_text[]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                            <input type="hidden" name="opsi_jawaban[]"
                                                value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                            <input type="text" name="info_text[]" placeholder="Masukkan informasi ${valItem.nama}"
                                                class="form-control input" ${isDisabled}>
                                            <input type="hidden" name="id_jawaban_text[]" class="input" value="" ${isDisabled}>
                                        </div>`);
                                    } else {
                                        $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group col-md-6 aspek_jaminan_kategori">
                                        <label>${valItem.nama}</label>
                                        <input type="hidden" name="id_update_file[]" value="" id="" class="input">
                                        <input type="hidden" name="id_file_text[]" value="${valItem.id}" id="" class="input">
                                        <input type="file" name="update_file[]" value="" id="${valItem.nama}file" class="form-control">
                                        <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                    </div>`);
                                    }
                                })
                            }
                        }
                    }
                }
            });
        }

        function getJaminanTambahan() {

            //clear item
            $('#select_kategori_jaminan_tambahan').empty();

            // clear bukti pemilikan
            $('#bukti_pemilikan_jaminan_tambahan').empty();

            //get item by kategori
            let kategoriJaminan = $('#kategori_jaminan_tambahan').val();

            $.ajax({
                type: "get",
                url: `${urlGetItemByKategori}?kategori=${kategoriJaminan}&id=${id}`,
                dataType: "json",
                success: function(response) {
                    var fotoArr1 = new Array();
                    var fotoArr2 = new Array();
                    var fotoArr3 = new Array();
                    if (kategoriJaminan != "Tidak Memiliki Jaminan Tambahan") {
                        $("#jaminan_tambahan").show()
                        // add item by kategori
                        $('#select_kategori_jaminan_tambahan').append(`
                            <label for="">${response.item.nama}</label>
                            <select name="dataLevelEmpat[]" id="itemByKategori" class="form-control cek-sub-column"
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
                            <option value="${valOption.skor}-${valOption.id}"` + (valOption.id === response
                                .detailJawabanOption?.id_jawaban ? 'selected="selected"' : '') + `>
                            ${valOption.option}
                            </option>`);
                        });

                        // add item bukti pemilikan
                        var isCheck = kategoriJaminan != 'Kendaraan Bermotor' ?
                            "<input type='checkbox' class='checkKategori'>" : ""
                        var isChecked = kategoriJaminan != 'Kendaraan Bermotor' ?
                            "<input type='checkbox' checked class='checkKategoriJaminanUtama'>" : ""
                        var isDisabled = kategoriJaminan != 'Kendaraan Bermotor' ? 'disabled' : ''

                        if (response.belum.length > 0) {
                            $.each(response.belum, function(i, valItem) {
                                if (valItem.nama != 'Foto') {
                                    $('#bukti_pemilikan_jaminan_tambahan').append(`
                                        <div class="form-group col-md-6 aspek_jaminan_kategori">
                                            <label>${isCheck} ${valItem.nama}</label>
                                            <input type="hidden" name="id_text[]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                            <input type="hidden" name="opsi_jawaban[]"
                                                value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                            <input type="text" maxlength="255" name="info_text[]" placeholder="Masukkan informasi ${valItem.nama}"
                                                class="form-control input" ${isDisabled}>
                                            <input type="hidden" name="id_jawaban_text[]" class="input" value="" ${isDisabled}>
                                        </div>`);
                                } else {
                                    fotoArr1.push(valItem)
                                    x++;
                                }
                            })
                        }
                        if (response.dataDetailJawabanText.length > 0) {

                            $.each(response.dataDetailJawabanText, function(i, valItem) {
                                if ($('#kategori_jaminan_tambahan').val() == 'Kendaraan Bermotor') {
                                    if (![118, 120, 148].includes(valItem.id_jawaban)) return;
                                }

                                if (valItem.nama == 'Atas Nama') {
                                    $('#bukti_pemilikan_jaminan_tambahan').append(`
                                        <div class="form-group col-md-6 aspek_jaminan_kategori atas-nama">
                                            <label>${valItem.nama}</label>
                                            <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                                            <input type="hidden" name="opsi_jawaban[]"
                                                value="${valItem.opsi_jawaban}" id="" class="input">
                                            <input type="text" maxlength="255" name="info_text[]"
                                                class="form-control input" value="${valItem.opsi_text}">
                                            <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                            <input type="hidden" name="id_jawaban_text[]" value="${valItem.id}">
                                        <input type="hidden" name="id_text[]" value="${valItem.id_item}">

                                        </div>
                                    `);
                                } else {
                                    if (valItem.nama == 'Foto') {
                                        fotoArr2.push(valItem)
                                        x++;
                                    } else {
                                        $('#bukti_pemilikan_jaminan_tambahan').append(`
                                            <div class="form-group col-md-6 aspek_jaminan_kategori">
                                                <label>${isChecked} ${valItem.nama}</label>
                                                <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                                <input type="hidden" name="opsi_jawaban[]"
                                                    value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                                <input type="text" maxlength="255" name="info_text[]"
                                                    class="form-control input" value="${valItem.opsi_text}">
                                                <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                                <input type="hidden" name="id_jawaban_text[]" value="${valItem.id}">
                                        <input type="hidden" name="id_text[]" value="${valItem.id_item}">
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
                            $.each(response.item.option, function(i, valOption) {
                                // //console.log(valOption.skor);
                                $('#itemByKategori').append(`
                            <option value="${valOption.skor}-${valOption.id}"` + (valOption.id === response
                                    .detailJawabanOption.id_jawaban ? 'selected="selected"' : ''
                                    ) + `>
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
                                    <div class="form-group col-md-6 aspek_jaminan_kategori atas-nama">
                                        <label>${valItem.nama}</label>
                                            <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                                            <input type="hidden" name="opsi_jawaban[]"
                                                value="${valItem.opsi_jawaban}" id="" class="input">
                                                <input type="text" maxlength="255" name="info_text[]" placeholder="Masukkan informasi"
                                                class="form-control input" value="${valItem.opsi_text}">
                                            <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                            <input type="hidden" name="id_jawaban_text[]" value="${valItem.id}">
                                        <input type="hidden" name="id_text[]" value="${valItem.id}">
                                    </div>
                                `);
                                } else {
                                    if (valItem.nama == 'Foto') {
                                        fotoArr3.push(valItem)
                                        x++;
                                    } else {
                                        $('#bukti_pemilikan_jaminan_tambahan').append(`
                                        <div class="form-group col-md-6 aspek_jaminan_kategori">
                                            <label>${valItem.nama}</label>
                                            <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                                            <input type="hidden" name="opsi_jawaban[]"
                                                value="${valItem.opsi_jawaban}" id="" class="input">
                                                <input type="text" maxlength="255" name="info_text[]" placeholder="Masukkan informasi"
                                                class="form-control input" value="${valItem.opsi_text}">
                                            <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                            <input type="hidden" name="id_jawaban_text[]" value="">
                                        <input type="hidden" name="id_text[]" value="${valItem.id}">
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
                        }

                        if (kategoriJaminan == "Tidak Memiliki Jaminan Tambahan") {
                            $("#select_kategori_jaminan_tambahan").hide()
                            $("#jaminan_tambahan").hide()
                            $("#itemByKategori").val('0-188')
                            $("#bukti_pemilikan_jaminan_tambahan").empty()
                        } else {
                            $("#select_kategori_jaminan_tambahan").show()
                            $("#jaminan_tambahan").show()
                        }

                        for (var i = 0; i < fotoArr1.length; i++) {
                            $('#bukti_pemilikan_jaminan_tambahan').last().append(`
                            <div class="form-group col-md-6 aspek_jaminan_kategori foto">
                                <label>${fotoArr1[i].nama}</label>
                                <div class="row">
                                    <div class="col-md-9">
                                        <input type="hidden" name="id_update_file[]" value="" id="" class="input">
                                        <input type="hidden" name="id_file_text[]" value="${fotoArr1[i].id}" id="" class="input">
                                        <input type="file" name="update_file[]" value="" id="${fotoArr1[i].nama}file" class="form-control">
                                        <input type="hidden" name="skor_penyelia_text[]" value="${(fotoArr1[i].skor_penyelia != null) ? fotoArr1[i].skor_penyelia : null}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-success plus" id="btnTambahBukti"><i class="fa fa-plus"></i></button>
                                    </div>
                                    <div class="col-md-1 ml-1">
                                        <button type="button" class="btn btn-danger minus" id="btnHapusBukti"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>`)
                        }
                        for (var i = 0; i < fotoArr2.length; i++) {
                            $('#bukti_pemilikan_jaminan_tambahan').append(`
                                <div class="form-group col-md-6 aspek_jaminan_kategori">
                                    <label>${fotoArr2[i].nama}</label>
                                    <div class="row">
                                        <div class="col-md-9">
                                            <input type="hidden" name="id_update_file[]" value="${fotoArr2[i].id}" id="" class="input">
                                            <label for="update_file" style="display: none" id="nama_file">${fotoArr2[i].opsi_text}</label>
                                            <input type="hidden" name="id_file_text[]" value="${fotoArr2[i].id_item}" id="" class="input">
                                            <input type="file" name="update_file[]" value="${fotoArr2[i].opsi_text}" id="${fotoArr2[i].nama}file" class="form-control">
                                            <input type="hidden" name="skor_penyelia_text[]" value="${(fotoArr2[i].skor_penyelia != null) ? fotoArr2[i].skor_penyelia : null}">
                                            <span class="filenameBukti" style="display: inline;">${fotoArr2[i].opsi_text}</span>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-success plus" id="btnTambahBukti"><i class="fa fa-plus"></i></button>
                                        </div>
                                        <div class="col-md-1 ml-1">
                                            <button type="button" class="btn btn-danger minus" id="btnHapusBukti"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                </div>`)
                            showFile();
                        }
                        for (var i = 0; i < fotoArr3.length; i++) {
                            $('#bukti_pemilikan_jaminan_tambahan').append(`
                            <div class="form-group col-md-6 aspek_jaminan_kategori">
                                <label>${fotoArr3[i].nama}</label>
                                <div class="row">
                                    <div class="col-md-9">
                                        <input type="hidden" name="id_update_file[]" value="" id="" class="input">
                                        <input type="hidden" name="id_file_text[]" value="${fotoArr3[i].id_item}" id="" class="input">
                                        <input type="hidden" name="opsi_jawaban[]"
                                            value="${fotoArr3[i].opsi_jawaban}" id="" class="input">
                                            <input type="file" name="update_file[]" placeholder="Masukkan informasi"
                                            class="form-control input" value="${fotoArr3[i].opsi_text}">
                                        <input type="hidden" name="skor_penyelia_text[]" value="${(fotoArr3[i].skor_penyelia != null) ? fotoArr3[i].skor_penyelia : null}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-success plus" id="btnTambahBukti"><i class="fa fa-plus"></i></button>
                                    </div>
                                    <div class="col-md-1 ml-1">
                                        <button type="button" class="btn btn-danger minus" id="btnHapusBukti"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>`);
                        }
                    } else {
                        $("#jaminan_tambahan").hide()
                        var opt = 0;
                        var skor = 0;
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
                        <option value="${valOption.skor}-${valOption.id}" ${(response.dataSelect == valOption.id) ? 'selected' : ''}>
                        ${valOption.option}
                        </option>`);
                        });
                        $("#itemByKategori").val(skor + '-' + opt);
                        $("#select_kategori_jaminan_tambahan").hide()
                    }
                    $('input:file').on('change', function(e) {
                        var filename = $(this).parent().find('.filename');
                        var selectedPathFile = $(this).val()
                        console.log(selectedPathFile)
                        if (selectedPathFile) {
                            if (selectedPathFile.includes("\\")) {
                                var arr = selectedPathFile.split("\\")
                                selectedPathFile = arr.slice(-1)
                            }
                            if (selectedPathFile.includes("/")) {
                                var arr = selectedPathFile.split("/")
                                selectedPathFile = arr.slice(-1)
                            }
                            filename.html(selectedPathFile)
                        }
                        if (selectedPathFile) {
                            var filename = $(this).parent().find('.filenameBukti');
                            console.log(filename)
                            if (selectedPathFile.includes("\\")) {
                                var arr = selectedPathFile.split("\\")
                                selectedPathFile = arr.slice(-1)
                            }
                            if (selectedPathFile.includes("/")) {
                                var arr = selectedPathFile.split("/")
                                selectedPathFile = arr.slice(-1)
                            }
                            filename.html(selectedPathFile)
                        }
                    })
                }
            });
        }

        //item kategori jaminan utama cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan
        $('#kategori_jaminan_utama').change(function(e) {
            getJaminanutama();
        });
        // end item kategori jaminan utama cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan

        //item kategori jaminan tambahan cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan
        $('#kategori_jaminan_tambahan').change(function(e) {
            getJaminanTambahan();
        });
        // end item kategori jaminan tambahan cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan

        // milih ijin usaha
        $('#ijin_usaha').change(function(e) {
            getIjinUsaha();
        });
        // end milih ijin usaha

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
            let thls = parseInt($('#thls').val());
            let nilaiAsuransi = parseInt($('#nilai_pertanggungan_asuransi').val());
            let kreditYangDiminta = parseInt($('#jumlah_kredit').val());

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

        // //triger hitung Persentase Kebutuhan Kredit
        // $('#kebutuhan_kredit').change(function(e) {
        //     hitungPersentaseKebutuhanKredit();
        // });
        // //end triger hitung Persentase Kebutuhan Kredit

        // //triger hitung Persentase Kebutuhan Kredit
        // $('#jumlah_kredit').change(function(e) {
        //     hitungPersentaseKebutuhanKredit();
        // });
        //end triger hitung Persentase Kebutuhan Kredit

        // hitung Persentase Kebutuhan Kredit
        // function hitungPersentaseKebutuhanKredit() {
        //     let kebutuhanKredit = parseInt($('#kebutuhan_kredit').val());
        //     let jumlahKredit = parseInt($('#jumlah_kredit').val());

        //     let persentaseKebutuhanKredit = parseInt(jumlahKredit / kebutuhanKredit * 100); //cek rumusnya lagi

        //     $('#persentase_kebutuhan_kredit').val(persentaseKebutuhanKredit);

        //     if (persentaseKebutuhanKredit <= 80 && !isNaN(persentaseKebutuhanKredit)) {
        //         $('#persentase_kebutuhan_kredit_opsi_0').attr('selected', true);
        //         $('#persentase_kebutuhan_kredit_opsi_1').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_2').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_3').removeAttr('selected');
        //     } else if (persentaseKebutuhanKredit >= 81 && persentaseKebutuhanKredit <= 89) {
        //         $('#persentase_kebutuhan_kredit_opsi_0').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_1').attr('selected', true);
        //         $('#persentase_kebutuhan_kredit_opsi_2').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_3').removeAttr('selected');
        //     } else if (persentaseKebutuhanKredit >= 90 && persentaseKebutuhanKredit <= 100) {
        //         $('#persentase_kebutuhan_kredit_opsi_0').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_1').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_2').attr('selected', true);
        //         $('#persentase_kebutuhan_kredit_opsi_3').removeAttr('selected');
        //     } else if (persentaseKebutuhanKredit > 100 && !isNaN(persentaseKebutuhanKredit)) {
        //         $('#persentase_kebutuhan_kredit_opsi_0').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_1').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_2').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_3').attr('selected', true);
        //     } else {
        //         $('#persentase_kebutuhan_kredit_opsi_0').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_1').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_2').removeAttr('selected');
        //         $('#persentase_kebutuhan_kredit_opsi_3').removeAttr('selected');
        //     }
        // }
        //end Persentase Kebutuhan Kredit

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

            //console.log(omzetPenjualan);

            let repaymentCapacity = parseFloat(persentaseNetIncome * omzetPenjualan * (1 + rencanaPeningkatan) /
                installment).toFixed(2); //cek rumusnya lagi

            $('#repayment_capacity').val(repaymentCapacity);

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
    </script>
    <script>
        var firstLoad = true;

        $('.rupiah').each(function() {
            var inp = $(this).val()
            $(this).val(formatrupiah(inp))
        })

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
        var jumlahData = $('#jumlahData').val();

        showFile();

        function showFile() {
            var label = document.querySelectorAll('label[for="update_file"]')
            var labelContent = []
            const fileInput = document.querySelectorAll('input[type="file"]');

            for (i = 0; i < label.length; i++) {
                labelContent.push(label[i].textContent)
            }

            for (i = 0; i < fileInput.length; i++) {
                // Create a new File object
                const myFile = new File(['Hello World!'], labelContent[i], {
                    type: 'text/plain',
                    lastModified: new Date(),
                });

                // Now let's create a DataTransfer to get a FileList
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(myFile);
                fileInput[i].files = dataTransfer.files;

            }
        }

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
            // //console.log(indexNow);
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
                cekBtn()
            }
        })

        for (let i = 0; i <= parseInt(jumlahData); i++) {
            var selected = i == parseInt(jumlahData) ? ' selected' : ''
            $(".side-wizard li[data-index='" + i + "']").addClass('active' + selected)
            $(".side-wizard li[data-index='" + i + "'] a span i").removeClass('fa fa-ban')
            if ($(".side-wizard li[data-index='" + i + "'] a span i").html() == '' || $(
                    ".side-wizard li[data-index='" + i + "'] a span i").html() == '0%') {
                $(".side-wizard li[data-index='" + i + "'] a span i").html('0%')
            }
            setPercent(i);
        }

        function setPercent(formIndex) {
            var form = ".form-wizard[data-index='" + formIndex + "']"
            var inputText = $(form + " .row input[type=text]")
            var inputNumber = $(form + " input[type=number]")
            var inputDisabled = $(form + " input:disabled")
            var inputReadonly = $(form + " input").find("readonly")
            var inputHidden = $(form + " input[type=hidden]")
            var inputFile = $(form + " .filename")
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

            if (formIndex == 2) {
                var ijinUsahaSelect = $(form).find("#ijin_usaha");
                if (ijinUsahaSelect.length > 0) {
                    if (ijinUsahaSelect[0].value == 'nib' || ijinUsahaSelect[0].value == 'surat_keterangan_usaha') {
                        if (!$("#isNpwp").attr('checked')) {
                            subtotalInput -= 4;
                        }
                        subtotalInput -= 2;
                    }
                    if (ijinUsahaSelect[0].value == 'tidak_ada_legalitas_usaha') {
                        subtotalInput -= 6;
                    }
                }
            }

            if (formIndex == 3) {
                subtotalInput -= firstLoad ? 4 : 8;
            }

            if (formIndex == 6) {
                subtotalInput -= 1;
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
                if (v.innerHTML != '') {
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
                var data = v.value;
                if (data != "" && data != '' && data != null && data != ' --Pilih Opsi-- ' && data !=
                    '--Pilih Opsi--') {
                    ttlSelectFilled++
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
            //console.log("=============index : "+formIndex+"=============")
            //console.log('total input : ' + subtotalInput)
            //console.log('total input filled : ' + subtotalFilled)
            //console.log("===============================================")

            var percentage = parseInt(subtotalFilled / subtotalInput * 100);
            percentage = Number.isNaN(percentage) ? 0 : percentage;
            percentage = percentage > 100 ? 100 : percentage;
            percentage = percentage < 0 ? 0 : percentage;

            $(".side-wizard li[data-index='" + formIndex + "'] a span i").html(percentage + "%")

            $(".side-wizard li[data-index='" + formIndex + "'] input.answer").val(subtotalInput);
            $(".side-wizard li[data-index='" + formIndex + "'] input.answerFilled").val(subtotalFilled);

            $('.progress').val(percentage);
        }

        $(".btn-next").click(function(e) {
            e.preventDefault();
            var indexNow = $(".form-wizard.active").data('index')
            var next = parseInt(indexNow) + 1
            // //console.log($(".form-wizard[data-index='"+next+"']").length==1);
            // //console.log($(".form-wizard[data-index='"+  +"']"));
            if ($(".form-wizard[data-index='" + next + "']").length == 1) {
                // //console.log(indexNow);
                $(".form-wizard").removeClass('active')
                $(".form-wizard[data-index='" + next + "']").addClass('active')
                $(".form-wizard[data-index='" + indexNow + "']").attr('data-done', 'true')
            }


            cekWizard()
            cekBtn(true)
            setPercent(indexNow)
        })

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


        $("#bukti_pemilikan_jaminan_tambahan").on("click", "#btnTambahBukti", function() {
            $("#bukti_pemilikan_jaminan_tambahan").append(`
        <div class="form-group col-md-6 aspek_jaminan_kategori">
            <label>Foto</label>
            <div class="row">
                <div class="col-md-9">
                    <input type="hidden" name="id_update_file[]" value="" id="" class="input">
                    <input type="hidden" name="id_file_text[]" value="148" id="" class="input">
                    <input type="hidden" name="opsi_jawaban[]"
                        value="foto" id="" class="input">
                        <input type="file" name="update_file[]" placeholder="Masukkan informasi"
                        class="form-control input" value="">
                    <input type="hidden" name="skor_penyelia_text[]" value="">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-success plus" id="btnTambahBukti"><i class="fa fa-plus"></i></button>
                </div>
                <div class="col-md-1 ml-1">
                    <button type="button" class="btn btn-danger minus" id="btnHapusBukti"><i class="fa fa-minus"></i></button>
                </div>
            </div>
        </div>`);
            x++
        })

        $("#bukti_pemilikan_jaminan_tambahan").on("click", "#btnHapusBukti", function() {
            if (x > 1) {
                $(this).closest('.aspek_jaminan_kategori').remove();
                const wrapper = $(this).parent().parent().parent();
                const formKredit = $('#pengajuan_kredit');
                const fileID = wrapper.find('input[name="id_update_file[]"]').val();
                //console.log("file-delete: "+fileID);

                wrapper.remove();
                formKredit.append(`
                <input type="hidden" name="id_delete_file[]" value="${fileID}">
            `);
                x--
            }
        })

        $('input:file').on('change', function(e) {
            var filename = $(this).parent().find('.filename');
            var selectedPathFile = $(this).val()
            if (selectedPathFile) {
                if (selectedPathFile.includes("\\")) {
                    var arr = selectedPathFile.split("\\")
                    selectedPathFile = arr.slice(-1)
                }
                if (selectedPathFile.includes("/")) {
                    var arr = selectedPathFile.split("/")
                    selectedPathFile = arr.slice(-1)
                }
                filename.html(selectedPathFile)
            }
            if (selectedPathFile) {
                var filename = $(this).parent().find('.filenameBukti');
                if (selectedPathFile.includes("\\")) {
                    var arr = selectedPathFile.split("\\")
                    selectedPathFile = arr.slice(-1)
                }
                if (selectedPathFile.includes("/")) {
                    var arr = selectedPathFile.split("/")
                    selectedPathFile = arr.slice(-1)
                }
                filename.html(selectedPathFile)
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

        $('body').on('click', '.file-wrapper .btn-del-file', function(e) {
            if ($('.file-wrapper').get().length < 2) return;

            const wrapper = $(this).parent().parent().parent();
            const formKredit = $('#pengajuan_kredit');
            const fileID = wrapper.find('input[name="id_update_file[]"]').val();
            //console.log("file-delete: "+fileID);

            wrapper.remove();
            formKredit.append(`
            <input type="hidden" name="id_delete_file[]" value="${fileID}">
        `);
        });

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
                        if (inputId == "docnib update file" || inputId == "docsku update file" || inputId == "docnpwp update file") {
                            if (ijinUsaha && ijinUsaha != 'tidak_ada_legalitas_usaha') {
                                if (ijinUsaha == "nib") {
                                    if (inputId == "docnib update file") {
                                        if ((v.value == '' || v.value == null || v.value.includes('Belum Upload')) && (filename == '' || filename == null || filename.includes('Belum Upload'))) {
                                            inputId = 'nib';
                                            if (!fileEmpty.includes(inputId))
                                                fileEmpty.push(inputId)
                                        }
                                    }
                                    if (inputId == 'docnpwp update file') {
                                        if ((v.value == '' || v.value == null || v.value.includes('Belum Upload')) && (filename == '' || filename == null || filename.includes('Belum Upload'))) {
                                            inputId = 'npwp';
                                            if (!fileEmpty.includes(inputId))
                                                fileEmpty.push(inputId)
                                        }
                                    }
                                }
                                else if (ijinUsaha == "surat_keterangan_usaha") {
                                    if (inputId == "docnpwp update file") {
                                        const isCheckNpwp = $('#isNpwp').is(':checked')
                                        if (isCheckNpwp) {
                                            if ((v.value == '' || v.value == null || v.value.includes('Belum Upload')) && (filename == '' || filename == null || filename.includes('Belum Upload'))) {
                                                inputId = 'npwp';
                                                if (!fileEmpty.includes(inputId))
                                                    fileEmpty.push(inputId)
                                            }
                                        }
                                    }
                                    if (inputId == "docsku update file") {
                                        if ((v.value == '' || v.value == null || v.value.includes('Belum Upload')) && (filename == '' || filename == null || filename.includes('Belum Upload'))) {
                                            inputId = 'surat keterangan usaha';
                                            if (!fileEmpty.includes(inputId))
                                                fileEmpty.push(inputId)
                                        }
                                    }
                                }
                            }
                        }
                        else {
                            if ((v.value == '' || v.value == null || v.value.includes('Belum Upload')) && (filename == '' || filename == null || filename.includes('Belum Upload'))) {
                                if (inputId == 'foto sp')
                                    inputId = 'surat permohonan';
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
    <script>
        $(document).ready(function() {
            $('input:file').on('change', function(e) {
                var filename = $(this).parent().find('.filename');
                var selectedPathFile = $(this).val()
                console.log(selectedPathFile)
                if (selectedPathFile) {
                    if (selectedPathFile.includes("\\")) {
                        var arr = selectedPathFile.split("\\")
                        selectedPathFile = arr.slice(-1)
                    }
                    if (selectedPathFile.includes("/")) {
                        var arr = selectedPathFile.split("/")
                        selectedPathFile = arr.slice(-1)
                    }
                    filename.html(selectedPathFile)
                }
                if (selectedPathFile) {
                    var filename = $(this).parent().find('.filenameBukti');
                    console.log(filename)
                    if (selectedPathFile.includes("\\")) {
                        var arr = selectedPathFile.split("\\")
                        selectedPathFile = arr.slice(-1)
                    }
                    if (selectedPathFile.includes("/")) {
                        var arr = selectedPathFile.split("/")
                        selectedPathFile = arr.slice(-1)
                    }
                    filename.html(selectedPathFile)
                }
            })
        })
    </script>
@endpush
