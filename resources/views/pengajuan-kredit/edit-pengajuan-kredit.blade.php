@extends('layouts.template')
@section('content')
    @include('components.notification')
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

    </style>
    <form id="pengajuan_kredit" action="{{ route('pengajuan-kredit.update', $dataUmum->id) }}" method="post">
        @method('PUT')
        @csrf
        <input type="hidden" name="progress" class="progress">
        <input type="hidden" name="id_nasabah" value="{{ $dataUmum->id_calon_nasabah }}">
        <div class="form-wizard active" data-index='0' data-done='true'>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="">Nama Lengkap</label>
                    <input type="text" name="name" id="nama" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $dataUmum->nama) }}" placeholder="Nama sesuai dengan KTP">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Kabupaten</label>
                    <select name="kabupaten" class="form-control @error('name') is-invalid @enderror select2" id="kabupaten">
                        <option value="">---Pilih Kabupaten----</option>
                        @foreach ($allKab as $item)
                            <option value="{{ old('id', $item->id) }} }}"
                                {{ old('id', $item->id) == $dataUmum->id_kabupaten ? 'selected' : '' }}>
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
                        <option value="">---Pilih Kecamatan----</option>
                        @foreach ($allKec as $kec)
                            <option value="{{ $kec->id }}" {{ $kec->id == $dataUmum->id_kecamatan ? 'selected' : '' }}>
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
                        <option value="">---Pilih Desa----</option>
                        @foreach ($allDesa as $desa)
                            <option value="{{ $desa->id }}"
                                {{ $desa->id == $dataUmum->id_kecamatan ? 'selected' : '' }}>{{ $desa->desa }}</option>
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
                    <textarea name="alamat_rumah" class="form-control @error('alamat_rumah') is-invalid @enderror" id="" cols="30" rows="4"
                        placeholder="Alamat Rumah disesuaikan dengan KTP">{{ old('alamat_rumah', $dataUmum->alamat_rumah) }}</textarea>
                    @error('alamat_rumah')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <hr>
                </div>
                <div class="form-group col-md-12">
                    <label for="">Alamat Usaha</label>
                    <textarea name="alamat_usaha" class="form-control @error('alamat_usaha') is-invalid @enderror" id="" cols="30" rows="4"
                        placeholder="Alamat Usaha disesuaikan dengan KTP">{{ old('alamat_usaha', $dataUmum->alamat_usaha) }}</textarea>
                    @error('alamat_usaha')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">No. KTP</label>
                    <input type="text" name="no_ktp" class="form-control @error('no_ktp') is-invalid @enderror" id=""
                        value="{{ old('no_ktp', $dataUmum->no_ktp) }}" placeholder="Masukkan 16 digit No. KTP">
                    @error('no_ktp')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Tempat</label>
                    <input type="text" name="tempat_lahir" id=""
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
                    <input type="date" name="tanggal_lahir" id=""
                        class="form-control @error('tanggal_lahir') is-invalid @enderror"
                        value="{{ old('tanggal_lahir', $dataUmum->tanggal_lahir) }}" placeholder="Tempat Lahir">
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label for="">Status</label>
                    <select name="status" id="" class="form-control @error('status') is-invalid @enderror select2">
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
                <div class="form-group col-md-12">
                    <label for="">Jenis Usaha</label>
                    <textarea name="jenis_usaha" class="form-control @error('jenis_usaha') is-invalid @enderror" id="" cols="30" rows="4"
                        placeholder="Jenis Usaha secara spesifik">{{ old('jenis_usaha', $dataUmum->jenis_usaha) }}</textarea>
                    @error('jenis_usaha')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Jumlah Kredit yang diminta</label>
                    <textarea name="jumlah_kredit" class="form-control @error('jumlah_kredit') is-invalid @enderror" id="" cols="30"
                        rows="4"
                        placeholder="Jumlah Kredit">{{ old('jumlah_kredit', $dataUmum->jumlah_kredit) }}</textarea>
                    @error('jumlah_kredit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Tujuan Kredit</label>
                    <textarea name="tujuan_kredit" class="form-control @error('tujuan_kredit') is-invalid @enderror" id="" cols="30"
                        rows="4"
                        placeholder="Tujuan Kredit">{{ old('tujuan_kredit', $dataUmum->tujuan_kredit) }}</textarea>
                    @error('tujuan_kredit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Jaminan yang disediakan</label>
                    <textarea name="jaminan" class="form-control @error('jaminan') is-invalid @enderror" id="" cols="30" rows="4"
                        placeholder="Jaminan yang disediakan">{{ old('jaminan', $dataUmum->jaminan_kredit) }}</textarea>
                    @error('jaminan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Hubungan Bank</label>
                    <textarea name="hubungan_bank" class="form-control @error('hubungan_bank') is-invalid @enderror" id="" cols="30"
                        rows="4"
                        placeholder="Hubungan dengan Bank">{{ old('hubungan_bank', $dataUmum->hubungan_bank) }}</textarea>
                    @error('hubungan_bank')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="">Hasil Verifikasi</label>
                    <textarea name="hasil_verifikasi" class="form-control @error('hasil_verifikasi') is-invalid @enderror" id="" cols="30"
                        rows="4"
                        placeholder="Hasil Verifikasi Karakter Umum">{{ old('hasil_verifikasi', $dataUmum->verifikasi_umum) }}</textarea>
                    @error('hasil_verifikasi')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
        <input type="text" id="jumlahData" name="jumlahData" hidden value="{{ count($dataAspek) + 1 }}">
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
                $key += 1;
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

                            $dataDetailJawabanTextsku = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                    ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                    ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                    // ->where('jawaban_text.id_jawaban', $item->id)
                                    ->where('item.nama', 'Surat Keterangan Usaha')
                                    ->first();

                            $dataDetailJawabanTextnib = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                    ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                    ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                    ->where('jawaban_text.id_jawaban', $item->id)
                                    ->where('item.nama', 'nib')
                                    ->first(); 

                            $dataDetailJawabanTextnpwp = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                    ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                    ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                    ->where('jawaban_text.id_jawaban', $item->id)
                                    ->where('item.nama', 'npwp')
                                    ->first();       

                            // dump($dataDetailJawabanText);
                        @endphp
                        {{-- item ijin usaha --}}
                        @if ($item->nama == 'Ijin Usaha')
                            <div class="form-group col-md-6">
                                <label for="">{{ $item->nama }}</label>
                                <select name="ijin_usaha" id="ijin_usaha" class="form-control" required>
                                    <option value="">-- Pilih Ijin Usaha --</option>
                                    <option value="nib">NIB</option>
                                    <option value="surat_keterangan_usaha">Surat Keterangan Usaha</option>
                                </select>
                            </div>

                            @if($dataDetailJawabanTextnib != null)
                                <div class="form-group col-md-6" id="nib">
                                    <label for="">NIB</label>
                                    <input type="hidden" name="id_level[]" value="77" id="nib_id">
                                    <input type="hidden" name="opsi_jawaban[]" value="input text" id="nib_opsi_jawaban">
                                    <input type="hidden" name="id_text[]" value="{{ ($dataDetailJawabanTextnib != null) ? $item->id_item : null }}">
                                    <input type="hidden" name="skor_penyelia_text[]"
                                        value="{{ ($dataDetailJawabanTextnib != null) ? $dataDetailJawabanTextnib->skor_penyelia : null  }}">
                                    <input type="hidden" name="id_jawaban_text[]" value="{{ ($dataDetailJawabanTextnib != null) ? 
                                        $item->id : null }}">
                                    <input type="text" name="info_text[]" id="nib_text" placeholder="Masukkan informasi"
                                        class="form-control" value="{{ ($dataDetailJawabanTextnib != null) ? $dataDetailJawabanTextnib->opsi_text : "" }}">
                                </div> 
                                <div class="form-group col-md-6" id="surat_keterangan_usaha">
                                    <label for="">Surat Keterangan Usaha</label>
                                    <input type="hidden" name="id_level[]" value="78" id="surat_keterangan_usaha_id">
                                    <input type="hidden" name="opsi_jawaban[]" value="input text"
                                        id="surat_keterangan_usaha_opsi_jawaban">
                                    <input type="text" name="info_text[]" id="surat_keterangan_usaha_text"
                                        placeholder="Masukkan informasi" value="{{ ($dataDetailJawabanTextsku != null) ? $dataDetailJawabanTextsku->opsi_text : "" }}" class="form-control">
                                    <input type="hidden" value="{{ ($dataDetailJawabanTextsku != null) ?       
                                        $item->id_item : null }}">
                                    <input type="hidden">
                                    <input type="hidden" name="id_text[]" value="{{ ($dataDetailJawabanTextsku != null) ?       
                                        $item->id_item : null }}">
                                    <input type="hidden" name="skor_penyelia_text[]" value="{{ ($dataDetailJawabanTextsku != null) ? 
                                        $dataDetailJawabanTextsku->skor_penyelia : null }}">
                                </div>
                            @elseif($dataDetailJawabanTextsku != null)
                                <div class="form-group col-md-6" id="surat_keterangan_usaha">
                                    <label for="">Surat Keterangan Usaha</label>
                                    <input type="hidden" name="id_level[]" value="78" id="surat_keterangan_usaha_id">
                                    <input type="hidden" name="opsi_jawaban[]" value="input text"
                                        id="surat_keterangan_usaha_opsi_jawaban">
                                    <input type="text" name="info_text[]" id="surat_keterangan_usaha_text"
                                        placeholder="Masukkan informasi" value="{{ ($dataDetailJawabanTextsku != null) ? $dataDetailJawabanTextsku->opsi_text : "" }}" class="form-control">
                                    <input type="hidden" name="id_text[]" value="{{ ($dataDetailJawabanTextsku != null) ?       
                                        $item->id_item : null }}">
                                    <input type="hidden" name="id_jawaban_text[]" value="{{ ($dataDetailJawabanTextsku != null) ? 
                                        $item->id : null }}">
                                    <input type="hidden" name="skor_penyelia_text[]" value="{{ ($dataDetailJawabanTextsku != null) ? 
                                        $dataDetailJawabanTextsku->skor_penyelia : null }}">
                                </div>
                                <div class="form-group col-md-6" id="nib">
                                    <label for="">NIB</label>
                                    <input type="hidden" name="id_level[]" value="77" id="nib_id">
                                    <input type="hidden" name="opsi_jawaban[]" value="input text" id="nib_opsi_jawaban">
                                    <input type="hidden"value="{{ ($dataDetailJawabanTextnib != null) ? $item->id_item : null }}">
                                    <input type="hidden" name="skor_penyelia_text[]"
                                        value="{{ ($dataDetailJawabanTextnib != null) ? $dataDetailJawabanTextnib->skor_penyelia : null  }}">
                                    <input type="hidden">
                                    <input type="hidden" name="id_text[]" value="{{ ($dataDetailJawabanTextsku != null) ?       
                                        $item->id_item : null }}">
                                    <input type="text" name="info_text[]" id="nib_text" placeholder="Masukkan informasi"
                                        class="form-control" value="{{ ($dataDetailJawabanTextnib != null) ? $dataDetailJawabanTextnib->opsi_text : "" }}">
                                </div> 
                            @endif

                        @elseif($item->nama == 'NPWP')
                            <div class="form-group col-md-6">
                                <label for="">NPWP</label>
                                <input type="hidden" name="id_level[]" value="79" id="">
                                <input type="hidden" name="opsi_jawaban[]" value="input text" id="">
                                <input type="hidden" name="id_text[]" value="{{ ($dataDetailJawabanTextnpwp != null) ? $item->id_item : null }}">
                                <input type="text" name="info_text[]" id="npwp" placeholder="Masukkan informasi"
                                    class="form-control" value="{{ ($dataDetailJawabanTextnpwp != null) ? $dataDetailJawabanTextnpwp->opsi_text : "" }}">
                                <input type="hidden" name="skor_penyelia_text[]"
                                    value="{{ $dataDetailJawabanTextnpwp->skor_penyelia }}">
                                    <input type="hidden" name="id_jawaban_text[]" value="{{ ($dataDetailJawabanTextnpwp != null) ? 
                                        $item->id : null }}">
                            </div>
                        @else
                            @if ($item->opsi_jawaban == 'input text')
                                @php
                                    $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                        ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                        ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                        ->where('jawaban_text.id_jawaban', $item->id)
                                        ->get();
                                @endphp
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $item->nama }}</label>
                                        <input type="hidden" name="id_text[]" value="{{ $itemTextDua->id_item }}">
                                        <input type="text" name="info_text[]" id="{{ $idLevelDua }}"
                                            placeholder="Masukkan informasi {{ $item->nama }}" value="{{ ($itemTextDua != null) ? $itemTextDua->opsi_text : null }}" class="form-control">
                                        <input type="hidden" name="skor_penyelia_text[]"
                                            value="{{ $itemTextDua->skor_penyelia }}">
                                            <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextDua->id }}">
                                    </div>

                                @endforeach
                                
                            @elseif ($item->opsi_jawaban == 'number')
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $item->nama }}</label>
                                        <input type="number" name="info_text[]" id="{{ $idLevelDua }}"
                                            placeholder="Masukkan informasi {{ $item->nama }}" value="{{ ($itemTextDua != null) ? $itemTextDua->opsi_text : null }}" class="form-control">
                                        <input type="hidden" name="skor_penyelia_text[]"
                                            value="{{ $itemTextDua->skor_penyelia }}">
                                        <input type="hidden" name="id_text[]" value="{{ $itemTextDua->id_item }}">
                                        <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextDua->id }}">
                                    </div>
                                @endforeach
                            @elseif ($item->opsi_jawaban == 'persen')
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $item->nama }}</label>
                                        <div class="input-group mb-3">
                                            <input type="number" step="any" name="info_text[]" id="{{ $idLevelDua }}"
                                                placeholder="Masukkan informasi {{ $item->nama }}" class="form-control"
                                                aria-label="Recipient's username" aria-describedby="basic-addon2" value="{{ ($itemTextDua != null) ? $itemTextDua->opsi_text : null }}">
                                            <input type="hidden" name="skor_penyelia_text[]"
                                                value="{{ $itemTextDua->skor_penyelia }}">
                                            <input type="hidden" name="id_text[]" value="{{ $itemTextDua->id_item }}">
                                            <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextDua->id }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">%</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif ($item->opsi_jawaban == 'file')
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $item->nama }}</label>
                                        {{-- <input type="hidden" name="opsi_jawaban[]" value="{{ $item->opsi_jawaban }}" --}}
                                            {{-- id="{{ $idLevelDua }}"> --}}
                                        <input type="hidden" name="id_text[]" value="{{ $itemTextDua->id_item }}">
                                        <input type="file" name="info_text[]" id="{{ $idLevelDua }}"
                                            placeholder="Masukkan informasi {{ $item->nama }}" value="{{ ($itemTextDua != null) ? $itemTextDua->opsi_text : null }}" class="form-control">
                                        <input type="hidden" name="skor_penyelia_text[]"
                                            value="{{ $itemTextDua->skor_penyelia }}">
                                        <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextDua->id }}">
                                    </div>
                                @endforeach
                            @elseif ($item->opsi_jawaban == 'long text')
                                @foreach ($dataDetailJawabanText as $itemTextDua)
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $item->nama }}</label>
                                        <input type="hidden" name="id_text[]" value="{{ $itemTextDua->id_item }}">
                                        <textarea name="info_text[]" rows="4" id="{{ $idLevelDua }}" class="form-control"
                                            placeholder="Masukkan informasi {{ $item->nama }}">{{ ($itemTextDua != null) ? $itemTextDua->opsi_text : null }}</textarea>
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
                            <div class="form-group col-md-6">
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
                                        <option value="{{ $itemJawaban->skor . '-' . $itemJawaban->id }}"
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
                                @endphp
                                
                                @if ($itemTiga->nama == 'Kategori Jaminan Utama')
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $itemTiga->nama }}</label>
                                        <select name="kategori_jaminan_utama" id="kategori_jaminan_utama"
                                            class="form-control" required>
                                            <option value="">-- Pilih Kategori Jaminan Utama --</option>
                                            <option value="Tanah">Tanah</option>
                                            <option value="Kendaraan Bermotor">Kendaraan Bermotor</option>
                                            <option value="Tanah dan Bangunan">Tanah dan Bangunan</option>
                                            <option value="Stock">Stock</option>
                                            <option value="Piutang">Piutang</option>
                                        </select>
                                        {{-- <input type="hidden" name="id_level[]" value="{{ $itemTiga->id }}" id="">
                                        <input type="hidden" name="opsi_jawaban[]" value="{{ $itemTiga->opsi_jawaban }}"
                                            id="">
                                        <input type="text" name="info_text[]" id="" placeholder="Masukkan informasi"
                                            class="form-control"> --}}
                                    </div>

                                    <div class="form-group col-md-6" id="select_kategori_jaminan_utama">

                                    </div>
                                @elseif ($itemTiga->nama == 'Kategori Jaminan Tambahan')
                                    <div class="form-group col-md-6">
                                        <label for="">{{ $itemTiga->nama }}</label>
                                        <select name="kategori_jaminan_tambahan" id="kategori_jaminan_tambahan"
                                            class="form-control" required>
                                            <option value="">-- Pilih Kategori Jaminan Tambahan --</option>
                                            <option value="Tanah">Tanah</option>
                                            <option value="Kendaraan Bermotor">Kendaraan Bermotor</option>
                                            <option value="Tanah dan Bangunan">Tanah dan Bangunan</option>
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
                                    <div class="form-group col-md-12">
                                        <h5>{{ $itemTiga->nama }}</h5>
                                    </div>
                                    <div id="bukti_pemilikan_jaminan_utama" class="form-group col-md-12 row">
                                        
                                    </div>
                                @elseif ($itemTiga->nama == 'Bukti Pemilikan Jaminan Tambahan')
                                    <div class="form-group col-md-12">
                                        <h5>{{ $itemTiga->nama }}</h5>
                                    </div>
                                    <div id="bukti_pemilikan_jaminan_tambahan" class="form-group col-md-12 row">

                                    </div>
                                @else
                                    @if ($itemTiga->opsi_jawaban == 'input text')
                                        <div class="form-group col-md-6">
                                            @php
                                                $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                                                    ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                                    ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                                    ->where('jawaban_text.id_jawaban', $itemTiga->id)
                                                    ->get();
                                            @endphp
                                            @foreach ($dataDetailJawabanText as $itemTextTiga)
                                                <label for="">{{ $itemTiga->nama }}</label>
                                                <input type="hidden" name="id_text[]" value="{{ $itemTextTiga->id_item }}">
                                                <input type="text" name="info_text[]" placeholder="Masukkan informasi"
                                                    class="form-control" id="{{ $idLevelTiga }}" value="{{ $itemTextTiga->opsi_text }}">
                                                <input type="hidden" name="skor_penyelia_text[]"
                                                    value="{{ $itemTextTiga->skor_penyelia }}">
                                                <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextTiga->id }}">
                                            @endforeach
                                        </div>
                                    @elseif ($itemTiga->opsi_jawaban == 'number')
                                        @foreach ($dataDetailJawabanText as $itemTextTiga)
                                            <div class="form-group col-md-6">
                                                <label for="">{{ $itemTiga->nama }}</label>
                                                <input type="hidden" name="id_text[]" value="{{ $itemTextTiga->id_item }}">
                                                <input type="number" name="info_text[]" placeholder="Masukkan informasi"
                                                    class="form-control" id="{{ $idLevelTiga }}" value="{{ $itemTextTiga->opsi_text }}">
                                                <input type="hidden" name="skor_penyelia_text[]"
                                                    value="{{ $itemTextTiga->skor_penyelia }}">
                                                <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextTiga->id }}">
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
                                                        aria-describedby="basic-addon2" value="{{ $itemTextTiga->opsi_text }}">
                                                        <input type="hidden" name="id_text[]" value="{{ $itemTextTiga->id_item }}">
                                                    <input type="hidden" name="skor_penyelia_text[]"
                                                        value="{{ $itemTextTiga->skor_penyelia }}">
                                                    <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextTiga->id }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">%</span>
                                                    </div>
                                                    <input type="hidden" name="id_text[]" value="{{ $itemTextTiga->id_item }}">
                                                </div>
                                                {{-- @endif --}}
                                            </div>
                                            @endforeach
                                    @elseif ($itemTiga->opsi_jawaban == 'file')
                                        @foreach ($dataDetailJawabanText as $itemTextTiga)
                                            <div class="form-group col-md-6">
                                                <label for="">{{ $itemTiga->nama }}</label>
                                                {{-- <input type="hidden" name="opsi_jawaban[]"
                                                    value="{{ $itemTiga->opsi_jawaban }}" id=""> --}}
                                                <input type="file" name="info_text[]" placeholder="Masukkan informasi"
                                                class="form-control" id="{{ $idLevelTiga }}" value="{{ $itemTextTiga->opsi_text }}">
                                                <input type="hidden" name="id_text[]" value="{{ $itemTextTiga->id_item }}">
                                                <input type="hidden" name="skor_penyelia_text[]"
                                                    value="{{ $itemTextTiga->skor_penyelia }}">
                                                <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextTiga->id }}">
                                            </div>
                                        @endforeach
                                    @elseif ($itemTiga->opsi_jawaban == 'long text')
                                    @foreach ($dataDetailJawabanText as $itemTextTiga)
                                            <div class="form-group col-md-6">
                                                <label for="">{{ $itemTiga->nama }}</label>
                                                <textarea name="info_text[]" rows="4" id="{{ $idLevelTiga }}" class="form-control"
                                                    placeholder="Masukkan informasi {{ $itemTiga->nama }}">{{ $itemTextTiga->opsi_text }}</textarea>
                                                    <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextTiga->id }}">
                                                    <input type="hidden" name="skor_penyelia_text[]"
                                                        value="{{ $itemTextTiga->skor_penyelia }}">
                                                    <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextTiga->id }}">
                                                
                                            <input type="hidden" name="id_text[]" value="{{ $itemTextTiga->id_item }}">
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
                                    <div class="form-group col-md-6">
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
                                                <option value="{{ $itemJawabanTiga->skor . '-' . $itemJawabanTiga->id }}"
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
                                                    <input type="text" name="info_text[]" id="{{ $idLevelEmpat }}"
                                                        placeholder="Masukkan informasi" class="form-control" value="{{ $itemTextEmpat->opsi_text }}">
                                                    <input type="hidden" name="skor_penyelia_text[]"
                                                        value="{{ $itemTextEmpat->skor_penyelia }}">
                                                    <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextEmpat->id }}">
                                                    <input type="hidden" name="id_text[]" value="{{ $itemTextEmpat->id_item }}">
                                                    
                                                </div>
                                            @endforeach
                                        @elseif ($itemEmpat->opsi_jawaban == 'number')
                                            @foreach ($dataDetailJawabanTextEmpat as $itemTextEmpat)
                                                <div class="form-group col-md-6">
                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                        <input type="number" name="info_text[]" id="{{ $idLevelEmpat }}"
                                                            placeholder="Masukkan informasi" class="form-control" value="{{ $itemTextEmpat->opsi_text }}">
                                                        <input type="hidden" name="skor_penyelia_text[]"
                                                            value="{{ $itemTextEmpat->skor_penyelia }}">
                                                        <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextEmpat->id }}">
                                                        <input type="hidden" name="id_text[]" value="{{ $itemTextEmpat->id_item }}">
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
                                                            aria-describedby="basic-addon2"value="{{ $itemTextEmpat->opsi_text }}">
                                                        <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextEmpat->id }}">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2">%</span>
                                                        </div>
                                                        <input type="hidden" name="skor_penyelia_text[]"
                                                            value="{{ $itemTextTiga->skor_penyelia }}">
                                                        <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextEmpat->id }}">
                                                        
                                                    <input type="hidden" name="id_text[]" value="{{ $itemTextEmpat->id_item }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        @elseif ($itemEmpat->opsi_jawaban == 'file')
                                            @foreach ($dataDetailJawabanTextEmpat as $itemTextEmpat)
                                                <div class="form-group col-md-6">
                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                    {{-- <input type="hidden" name="opsi_jawaban[]"
                                                        value="{{ $itemEmpat->opsi_jawaban }}" id=""> --}}
                                                        <input type="hidden" name="id_text[]" value="{{ $itemTextEmpat->id_item }}">
                                                        <input type="file" name="info_text[]" id="{{ $idLevelEmpat }}"
                                                            placeholder="Masukkan informasi" class="form-control" value="{{ $itemTextEmpat->opsi_text }}">
                                                        <input type="hidden" name="skor_penyelia_text[]"
                                                            value="{{ $itemTextEmpat->skor_penyelia }}">
                                                        <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextEmpat->id }}">
                                                </div>
                                            @endforeach
                                        @elseif ($itemEmpat->opsi_jawaban == 'long text')
                                            @foreach ($dataDetailJawabanTextEmpat as $itemTextEmpat)
                                                <div class="form-group col-md-6">
                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                    <textarea name="info_text[]" rows="4" id="{{ $idLevelEmpat }}" class="form-control"
                                                        placeholder="Masukkan informasi {{ $itemEmpat->nama }}">{{ $itemTextEmpat->opsi_text }}</textarea>
                                                    <input type="hidden" name="skor_penyelia_text[]"
                                                        value="{{ $itemTextEmpat->skor_penyelia }}">
                                                    <input type="hidden" name="id_text[]" value="{{ $itemTextEmpat->id_item }}">
                                                    <input type="hidden" name="id_jawaban_text[]" value="{{ $itemTextEmpat->id }}">
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
                                            <div class="form-group col-md-6">
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
                                                        <option value="{{ $itemJawabanEmpat->skor . '-' . $itemJawabanEmpat->id }}"
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

                </div>

            </div>
        @endforeach
        {{-- pendapat dan usulan --}}
        <div class="form-wizard" data-index='{{ count($dataAspek) + 1 }}' data-done='true'>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="">Pendapat dan Usulan</label>
                    <textarea name="komentar_staff" class="form-control @error('komentar_staff') is-invalid @enderror" id="" cols="30"
                        rows="4" placeholder="Pendapat dan Usulan Staf/Analis Kredit" required></textarea>
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
    $('#nib').hide();
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

    let urlCekSubColumn = "{{ route('cek-sub-column') }}";
    let urlGetItemByKategoriJaminanUtama =
        "{{ route('get-item-jaminan-by-kategori-jaminan-utama') }}"; 
    // jaminan tambahan
    let urlGetItemByKategori = "{{ route('get-item-jaminan-by-kategori') }}";
    let id = parseInt("{{ $dataUmum->id }}");
     // jaminan tambahan

    $('#kabupaten').change(function() {
        var kabID = $(this).val();
        if (kabID) {
            $.ajax({
                type: "GET",
                url: "/getkecamatan?kabID=" + kabID,
                dataType: 'JSON',
                success: function(res) {
                    //    console.log(res);
                    if (res) {
                        $("#kecamatan").empty();
                        $("#desa").empty();
                        $("#kecamatan").append('<option>---Pilih Kecamatan---</option>');
                        $("#desa").append('<option>---Pilih Desa---</option>');
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
        // console.log(kecID);
        if (kecID) {
            $.ajax({
                type: "GET",
                url: "/getdesa?kecID=" + kecID,
                dataType: 'JSON',
                success: function(res) {
                    //    console.log(res);
                    if (res) {
                        $("#desa").empty();
                        $("#desa").append('<option>---Pilih Desa---</option>');
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
            url: `${urlGetItemByKategoriJaminanUtama}?kategori=${kategoriJaminanUtama}&id=${id}`,
            dataType: "json",
            success: function(response) {
                // jika kategori bukan stock dan piutang
                if (kategoriJaminanUtama != 'Stock' && kategoriJaminanUtama != 'Piutang') {
                    if(response.dataDetailJawabanText.length > 0){
                        
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
                            // console.log(valOption.skor);
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
                        $.each(response.dataDetailJawabanText, function(i, valItem) {
                            console.log(valItem.nama);
                            if (valItem.nama == 'Atas Nama') {
                                $('#bukti_pemilikan_jaminan_utama').append(`
                                    <div class="form-group col-md-6 aspek_jaminan_kategori_jaminan_utama">
                                        <label>${valItem.nama}</label>
                                        <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                                        <input type="hidden" name="opsi_jawaban[]"
                                            value="${valItem.opsi_jawaban}" id="" class="input">
                                            <input type="text" name="info_text[]" placeholder="Masukkan informasi"
                                            class="form-control input" value="${valItem.opsi_text}">
                                        <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                        <input type="hidden" name="id_jawaban_text[]" value="${valItem.id_jawaban}">
                                    </div>
                                `);
                            } else {
                                if(valItem.nama == 'Foto') {
                                    $('#bukti_pemilikan_jaminan_utama').append(`
                                    <div class="form-group col-md-6 aspek_jaminan_kategori_jaminan_utama">
                                        <label>${valItem.nama}</label>
                                        <input type="hidden" name="id_item_file[]" value="${valItem.id}" id="" class="input">
                                        <input type="file" name="upload_file[]"  value="${valItem.opsi_text}" id="" class="form-control">
                                        <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                        <input type="hidden" name="id_jawaban_text[]" value="${valItem.id_jawaban}">
                                    </div>`);
                                }
                                else {
                                    $('#bukti_pemilikan_jaminan_utama').append(`
                                    <div class="form-group col-md-6 aspek_jaminan_kategori_jaminan_utama">
                                        <label>${isCheck} ${valItem.nama}</label>
                                        <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                        <input type="hidden" name="opsi_jawaban[]"
                                            value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                        <input type="text" name="info_text[]"
                                            class="form-control input" value="${valItem.opsi_text}">
                                        <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                        <input type="hidden" name="id_jawaban_text[]" value="${valItem.id_jawaban}">
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
                    else{
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
                            // console.log(valOption.skor);
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
                            console.log(valItem.nama);
                            if (valItem.nama == 'Atas Nama') {
                                $('#bukti_pemilikan_jaminan_utama').append(`
                                <div class="form-group col-md-6 aspek_jaminan_kategori_jaminan_utama">
                                    <label>${valItem.nama}</label>
                                    <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                                    <input type="hidden" name="opsi_jawaban[]"
                                        value="${valItem.opsi_jawaban}" id="" class="input">
                                    <input type="text" name="info_text[]" placeholder="Masukkan informasi"
                                        class="form-control input">
                                </div>
                            `);
                            } else {
                                if(valItem.nama == 'Foto') {
                                    $('#bukti_pemilikan_jaminan_utama').append(`
                                    <div class="form-group col-md-6 aspek_jaminan_kategori_jaminan_utama">
                                        <label>${valItem.nama}</label>
                                        <input type="hidden" name="id_item_file[]" value="${valItem.id}" id="" class="input">
                                        <input type="file" name="upload_file[]" id="" class="form-control">
                                    </div>`);
                                }
                                else {
                                    $('#bukti_pemilikan_jaminan_utama').append(`
                                    <div class="form-group col-md-6 aspek_jaminan_kategori_jaminan_utama">
                                        <label>${isCheck} ${valItem.nama}</label>
                                        <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                        <input type="hidden" name="opsi_jawaban[]"
                                            value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                        <input type="text" name="info_text[]" placeholder="Masukkan informasi ${valItem.nama}"
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
                                <input type="text" name="info_text[]" placeholder="Masukkan informasi"
                                    class="form-control input">
                                        <input type="hidden" name="skor_penyelia_text[]"
                                            value="">
                                        <input type="hidden" name="id_jawaban_text[]" value="">
                            </div>
                                `);
                        } else {
                                $('#select_kategori_jaminan_utama').append(`
                                <div class="aspek_jaminan_kategori_jaminan_utama">
                                    <label>${valItem.nama}</label>
                                    <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" >
                                    <input type="hidden" name="opsi_jawaban[]"
                                        value="${valItem.opsi_jawaban}" id="" class="input">
                                    <input type="text" name="info_text[]" placeholder="Masukkan informasi ${valItem.nama}"
                                        class="form-control input">
                                            <input type="hidden" name="skor_penyelia_text[]"
                                                value="">
                                            <input type="hidden" name="id_jawaban_text[]" value="">
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
            url: `${urlGetItemByKategori}?kategori=${kategoriJaminan}&id=${id}`,
            dataType: "json",
            success: function(response) {
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
                        // console.log(valOption.skor);
                        $('#itemByKategori').append(`
                        <option value="${valOption.skor}-${valOption.id}">
                        ${valOption.option}
                        </option>`);
                    });

                    // add item bukti pemilikan
                    var isCheck = kategoriJaminan != 'Kendaraan Bermotor' ?
                        "<input type='checkbox' class='checkKategori'>" : ""
                    var isDisabled = kategoriJaminan != 'Kendaraan Bermotor' ? 'disabled' : ''

                    if(response.dataDetailJawabanText.length > 0){

                        $.each(response.dataDetailJawabanText, function(i, valItem) {
                            if (valItem.nama == 'Atas Nama') {
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group col-md-6 aspek_jaminan_kategori">
                                        <label>${valItem.nama}</label>
                                        <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                                        <input type="hidden" name="opsi_jawaban[]"
                                            value="${valItem.opsi_jawaban}" id="" class="input">
                                        <input type="text" name="info_text[]"
                                            class="form-control input" value="${valItem.opsi_text}">
                                        <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                        <input type="hidden" name="id_jawaban_text[]" value="${valItem.id_jawaban}"
                                            
                                    </div>
                                `);
                            } else {
                                if(valItem.nama == 'Foto') {
                                    $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group col-md-6 aspek_jaminan_kategori">
                                        <label>${valItem.nama}</label>
                                        <input type="hidden" name="id_item_file[]" value="${valItem.id}" id="" class="input">
                                        <input type="file" name="info_text[]"
                                            class="form-control input" value="${valItem.opsi_text}">
                                        <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                        <input type="hidden" name="id_jawaban_text[]" value="${valItem.id_jawaban}"
                                    </div>`);
                                } else {
                                    $('#bukti_pemilikan_jaminan_tambahan').append(`
                                        <div class="form-group col-md-6 aspek_jaminan_kategori">
                                            <label>${isCheck} ${valItem.nama}</label>
                                            <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                            <input type="hidden" name="opsi_jawaban[]"
                                                value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                            <input type="text" name="info_text[]"
                                                class="form-control input" value="${valItem.opsi_text}">
                                            <input type="hidden" name="skor_penyelia_text[]" value="${(valItem.skor_penyelia != null) ? valItem.skor_penyelia : null}">
                                            <input type="hidden" name="id_jawaban_text[]" value="${valItem.id_jawaban}"
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
                    } else{
                        $.each(response.item.option, function(i, valOption) {
                        // console.log(valOption.skor);
                        $('#itemByKategori').append(`
                        <option value="${valOption.skor}-${valOption.id}">
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
                                    <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                                    <input type="hidden" name="opsi_jawaban[]"
                                        value="${valItem.opsi_jawaban}" id="" class="input">
                                    <input type="text" name="info_text[]" placeholder="Masukkan informasi"
                                        class="form-control input">
                                </div>
                            `);
                        } else {
                            if(valItem.nama == 'Foto') {
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                <div class="form-group col-md-6 aspek_jaminan_kategori">
                                    <label>${valItem.nama}</label>
                                    <input type="hidden" name="id_item_file[]" value="${valItem.id}" id="" class="input">
                                    <input type="file" name="upload_file[]" id="" class="form-control">
                                </div>`);
                            } else {
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group col-md-6 aspek_jaminan_kategori">
                                        <label>${isCheck} ${valItem.nama}</label>
                                        <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                        <input type="hidden" name="opsi_jawaban[]"
                                            value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                        <input type="text" name="info_text[]" placeholder="Masukkan informasi"
                                            class="form-control input" ${isDisabled}>
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
            }
        });
    });
    // end item kategori jaminan tambahan cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan

    // milih ijin usaha
    $('#ijin_usaha').change(function(e) {
        let ijinUsaha = $(this).val();
        if (ijinUsaha == 'nib') {
            $('#surat_keterangan_usaha').hide();
            $('#surat_keterangan_usaha_id').attr('disabled', true);
            $('#surat_keterangan_usaha_text').attr('disabled', true);
            $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);

            $('#nib').show();
            $('#nib_id').removeAttr('disabled');
            $('#nib_text').removeAttr('disabled');
            $('#nib_opsi_jawaban').removeAttr('disabled');
        } else if (ijinUsaha == 'surat_keterangan_usaha') {
            $('#nib').hide();
            $('#nib_id').attr('disabled', true);
            $('#nib_text').attr('disabled', true);
            $('#nib_opsi_jawaban').attr('disabled', true);

            $('#surat_keterangan_usaha').show();
            $('#surat_keterangan_usaha_id').removeAttr('disabled');
            $('#surat_keterangan_usaha_text').removeAttr('disabled');
            $('#surat_keterangan_usaha_opsi_jawaban').removeAttr('disabled');
        } else {
            $('#nib').hide();
            $('#nib_id').attr('disabled', true);
            $('#nib_text').attr('disabled', true);
            $('#nib_opsi_jawaban').attr('disabled', true);

            $('#surat_keterangan_usaha').hide();
            $('#surat_keterangan_usaha_id').attr('disabled', true);
            $('#surat_keterangan_usaha_text').attr('disabled', true);
            $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);
        }
    });
    // end milih ijin usaha

    //triger hitung ratio coverage
    $('#thls').change(function(e) {
        hitungRatioCoverage();
    });
    //end triger hitung ratio covarege

    //triger hitung ratio coverage
    $('#nilai_asuransi_penjaminan').change(function(e) {
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
        let nilaiAsuransi = parseInt($('#nilai_asuransi_penjaminan').val());
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
        let persentaseNetIncome = parseInt($('#persentase_net_income').val()) / 100;
        let omzetPenjualan = parseInt($('#omzet_penjualan').val());
        let rencanaPeningkatan = parseInt($('#rencana_peningkatan').val()) / 100;
        let installment = parseInt($('#installment').val());

        let repaymentCapacity = parseFloat(persentaseNetIncome * omzetPenjualan * (1 + rencanaPeningkatan) /
            (installment*12)); //cek rumusnya lagi

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
        var jumlahData = $('#jumlahData').val();
        for (let index = 0; index < jumlahData + 1; index++) {
            for (let index = 0; index <= parseInt(jumlahData); index++) {
                var selected = index == parseInt(jumlahData) ? ' selected' : ''
                $(".side-wizard li[data-index='" + index + "']").addClass('active' + selected)
                $(".side-wizard li[data-index='" + index + "'] a span i").removeClass('fa fa-ban')
                if ($(".side-wizard li[data-index='" + index + "'] a span i").html() == '' || $(
                        ".side-wizard li[data-index='" + index + "'] a span i").html() == '0%') {
                    $(".side-wizard li[data-index='" + index + "'] a span i").html('0%')
                }
            }

            var form = ".form-wizard[data-index='" + index + "']"

            var input = $(form + " input")
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

            var allInput = ttlInput + ttlSelect + ttlTextarea
            var allInputFilled = ttlInputFilled + ttlSelectFilled + ttlTextareaFilled

            var percentage = parseInt(allInputFilled / allInput * 100);
            percentage = isNaN(percentage) ? 0 : percentage;
            $(".side-wizard li[data-index='" + index + "'] a span i").html(percentage + "%")
            $(".side-wizard li[data-index='" + index + "'] input.answer").val(allInput);
            $(".side-wizard li[data-index='" + index + "'] input.answerFilled").val(allInputFilled);
            var allInputTotal = 0;
            var allInputFilledTotal = 0;
        }
        $(".side-wizard li input.answer").each(function() {
            allInputTotal += Number($(this).val());
        });
        $(".side-wizard li input.answerFilled").each(function() {
            allInputFilledTotal += Number($(this).val());
        });
        console.log(allInputTotal);
        var result = parseInt(allInputFilledTotal / allInputTotal * 100);
        $('.progress').val(result);

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
            if (indexNow == jumlahData) {

                $(".btn-next").click(function(e) {
                    if (parseInt(indexNow) != parseInt(jumlahData)) {
                        $(".btn-next").show()

                    }
                    $(".btn-simpan").show()
                    $(".progress").prop('disabled', false);
                    $(".btn-next").hide()
                });
                $(".btn-next").show()

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
                // $(".side-wizard li[data-index='"+index+"'] a span i").removeClass('fa fa-ban')
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
            }
        })

        function setPercentage(formIndex) {
            console.log(formIndex);
            var form = ".form-wizard[data-index='" + formIndex + "']"

            var input = $(form + " input")
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

            var allInput = ttlInput + ttlSelect + ttlTextarea
            var allInputFilled = ttlInputFilled + ttlSelectFilled + ttlTextareaFilled

            var percentage = parseInt(allInputFilled / allInput * 100);
            $(".side-wizard li[data-index='" + formIndex + "'] a span i").html(percentage + "%")
            $(".side-wizard li[data-index='" + formIndex + "'] input.answer").val(allInput);
            $(".side-wizard li[data-index='" + formIndex + "'] input.answerFilled").val(allInputFilled);
            var allInputTotal = 0;
            var allInputFilledTotal = 0;
            $(".side-wizard li input.answer").each(function() {
                allInputTotal += Number($(this).val());
            });
            $(".side-wizard li input.answerFilled").each(function() {
                allInputFilledTotal += Number($(this).val());
            });

            var result = parseInt(allInputFilledTotal / allInputTotal * 100);
            $('.progress').val(result);
        }

        $(".btn-next").click(function(e) {
            e.preventDefault();
            var indexNow = $(".form-wizard.active").data('index')
            var next = parseInt(indexNow) + 1
            // console.log($(".form-wizard[data-index='"+next+"']").length==1);
            // console.log($(".form-wizard[data-index='"+  +"']"));
            if ($(".form-wizard[data-index='" + next + "']").length == 1) {
                // console.log(indexNow);
                $(".form-wizard").removeClass('active')
                $(".form-wizard[data-index='" + next + "']").addClass('active')
                $(".form-wizard[data-index='" + indexNow + "']").attr('data-done', 'true')
            }


            cekWizard()
            cekBtn(true)
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
    </script>
    <script src="{{ asset('') }}js/custom.js"></script>
@endpush