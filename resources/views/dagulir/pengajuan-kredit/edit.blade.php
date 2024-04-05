@include('components.new.modal.loading')
@extends('layouts.tailwind-template')
@section('modal')
    @include('dagulir.pengajuan-kredit.modal.modal-photo')
@endsection
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
$skema = 'Dagulir';
$dataIndex = match ($skema) {
    'PKPJ' => 1,
    'KKB' => 2,
    'Talangan Umroh' => 1,
    'Prokesra' => 1,
    'Kusuma' => 1,
    'Dagulir' => 1,
    null => 1,
};
@endphp

@section('content')

<section class="">
    <nav class="w-full bg-white p-3  top-[4rem] border sticky">
        <div class="owl-carousel owl-theme tab-wrapper">
            <button data-toggle="tab" data-tab="dagulir" class="btn btn-tab active-tab font-semibold">
                <span class="percentage">0%</span> Data Umum
            </button>
            @if ($pengajuan->skema_kredit == 'KKB')
            <button data-toggle="tab" data-tab="data-po" class="btn btn-tab font-semibold">
                <span class="percentage">0%</span> Data PO
            </button>
            @endif
            @foreach ($dataAspek as $item)
                @php
                    $title = str_replace('&', 'dan', strtolower($item->nama));
                    $title = str_replace(' ', '-', strtolower($title));
                @endphp
                <button data-toggle="tab" data-tab="{{$title}}" class="btn btn-tab font-semibold"><span class="percentage">0%</span> {{$item->nama}}</button>
            @endforeach
            <button data-toggle="tab" data-tab="pendapat-dan-usulan" class="btn btn-tab font-semibold">Pendapat dan Usulan</button>
        </div>
    </nav>
    <div class="p-3">
        <div class="body-pages">
            <form action="{{ route('dagulir.pengajuan.update', $pengajuan?->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id_dagulir_temp" id="id_dagulir_temp" value="{{ $pengajuan?->id }}">
                <input type="hidden" name="skema_kredit" id="skema_kredit" value="{{ $pengajuan?->skema_kredit }}">
                <div class="mt-3 container mx-auto">
                    <div id="dagulir-tab" class="is-tab-content active">
                        @if ($pengajuan->skema_kredit != 'Dagulir')
                            @include('dagulir.pengajuan.edit-dagulir-analisis')
                        @else
                            @include('dagulir.pengajuan.edit-dagulir')
                        @endif
                    </div>
                    @if ($pengajuan->skema_kredit == 'KKB')
                        @include('dagulir.pengajuan.edit-data-po')
                    @endif
                    @foreach ($dataAspek as $key => $value)
                        @php
                            $title_id = str_replace('&', 'dan', strtolower($value->nama));
                            $title_id = str_replace(' ', '-', strtolower($title_id));
                            $title_tab = "$title_id-tab";
                            $key += $dataIndex;
                            // check level 2
                            $dataLevelDua = \App\Models\ItemModel::where('level', 2)
                            ->where('id_parent', $value->id)
                            ->orderBy('sequence')
                            ->get();
                            // check level 4
                            $dataLevelEmpat = \App\Models\ItemModel::where('level', 4)
                            ->where('id_parent', $value->id)
                            ->get();
                            $nib = edit_dagulir($pengajuan->id, 77)?->opsi_text;
                            $sku = edit_dagulir($pengajuan->id, 78)?->opsi_text;
                            $npwp = edit_dagulir($pengajuan->id, 79)?->opsi_text;
                        @endphp

                        <div id="{{ $title_tab }}" class="is-tab-content">
                            <div class="pb-10 space-y-3">
                                <h2 class="text-4xl font-bold tracking-tighter text-theme-primary">{{$value->nama}}</h2>
                            </div>
                            <div class="self-start bg-white w-full border">
                                <div
                                    class="p-5 w-full space-y-5"
                                    id="{{$title_id}}">
                                    <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                                    @foreach ($dataLevelDua as $item)
                                        @php
                                            $idLevelDua = str_replace(' ', '_', strtolower($item->nama));
                                        @endphp
                                        {{-- item ijin usaha --}}
                                        @if ($item->nama == 'Ijin Usaha')
                                            <div class="form-group">
                                                <div class="input-box">
                                                    <label for="">{{ $item->nama }}</label>
                                                    <select name="ijin_usaha" id="ijin_usaha" class="form-input">
                                                        <option value="">-- Pilih Ijin Usaha --</option>
                                                        <option value="nib" {{ $nib !='' ? 'selected' : '' }}>NIB</option>
                                                        <option value="surat_keterangan_usaha" {{ $sku !='' ? 'selected' : '' }}>Surat Keterangan Usaha</option>
                                                        <option value="tidak_ada_legalitas_usaha" {{ $nib=='' && $sku=='' && $npwp=='' ? 'selected' : ''
                                                    }}>Tidak Ada Legalitas Usaha</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group"  >
                                                <div class="input-box" id="npwpsku">
                                                    <label for="">NPWP</label>
                                                    <br>
                                                    <div class="flex gap-2 rounded p-2 w-full">
                                                        <input
                                                            type="checkbox"
                                                            name="form-input"
                                                            class="form-check cursor-pointer"
                                                            id="isNpwp"
                                                            @if ($npwp != '' || $npwp != null) checked @endif
                                                        />
                                                        <input type="hidden" name="isNpwp" id="statusNpwp" class="form-control"
                                                            value="{{ $npwp != null ? '1' : '0' }}">
                                                        <label
                                                            for="isNpwp"
                                                            class="font-semibold cursor-pointer text-theme-text"
                                                            >Memiliki NPWP</label
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="form-group" id="space_nib"></div> --}}
                                            <div class="form-group" id="nib">
                                                <div class="input-box">
                                                    <label for="">NIB</label>
                                                    <input type="hidden" name="id_level[77]" value="77" id="nib_id">
                                                    <input type="hidden" name="opsi_jawaban[77]" value="input text" id="nib_opsi_jawaban">
                                                    <input type="text" maxlength="255" name="informasi[77]" id="nib_text"
                                                        placeholder="Masukkan informasi" class="form-input"value="{{ edit_text_dagulir($pengajuan->id, 77)?->opsi_text }}">

                                                </div>
                                            </div>
                                            <div class="form-group" id="docNIB">
                                                <div class="input-box">
                                                    <label for="">{{ $itemNIB->nama }}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                                    @php
                                                        $file = edit_text_dagulir($pengajuan->id, $itemNIB->id)?->opsi_text;
                                                    @endphp
                                                    @if ($file)
                                                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                            data-title="{{$itemNIB->nama}}"
                                                            data-type="image"
                                                            data-filepath="{{ asset("../upload/{$pengajuan->id}/{$itemNIB->id}/{$file}") }}"
                                                            >Preview
                                                        </a>
                                                    @endif
                                                    <input type="hidden" name="id_item_file[{{ $itemNIB->id }}]" value="{{ $itemNIB->id }}"
                                                        id="docNIB_id">
                                                    <input type="file" name="upload_file[{{ $itemNIB->id }}]" data-id="{{ edit_text_dagulir($pengajuan->id, $itemNIB->id)?->id }}"
                                                        placeholder="Masukkan informasi {{ $itemNIB->nama }}" class="form-input limit-size"
                                                        id="file_nib"
                                                        value="{{$file}}">
                                                    <span class="text-red-500 m-0" style="display: none" id="docNIB_text">Besaran file
                                                        tidak boleh lebih dari 5 MB</span>
                                                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('dataLevelTiga.' . $key) }}
                                                        </div>
                                                    @endif
                                                    {{--  <span class="filename" style="display: inline;">{{ edit_text_dagulir($pengajuan->id, $itemNIB->id)?->opsi_text }}</span>  --}}
                                                </div>
                                            </div>

                                            <div class="form-group" id="surat_keterangan_usaha">
                                                <div class="input-box">
                                                    <label for="">Surat Keterangan Usaha</label>
                                                    <input type="hidden" name="id_level[78]" value="78" id="surat_keterangan_usaha_id">
                                                    <input type="hidden" name="opsi_jawaban[78]" value="input text"
                                                        id="surat_keterangan_usaha_opsi_jawaban">
                                                    <input type="text" maxlength="255" name="informasi[78]" id="surat_keterangan_usaha_text"
                                                        placeholder="Masukkan informasi" class="form-input" value="{{ edit_text_dagulir($pengajuan->id, 78)?->opsi_text }}">
                                                </div>
                                            </div>

                                            <div class="form-group" id="docSKU">
                                                <div class="input-box">
                                                    <label for="">{{ $itemSKU->nama }}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                                    @php
                                                        $file = edit_text_dagulir($pengajuan->id, $itemSKU->id)?->opsi_text;
                                                    @endphp
                                                    @if ($file)
                                                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                            data-title="Dokumen Surat Keterangan Usaha"
                                                            data-type="image"
                                                            data-filepath="{{ asset("../upload/{$pengajuan->id}/{$itemSKU->id}/{$file}") }}"
                                                            >Preview
                                                        </a>
                                                    @endif
                                                    <input type="hidden" name="id_item_file[{{ $itemSKU->id }}]" value="{{ $itemSKU->id }}"
                                                        id="docSKU_id">
                                                    <input type="file" name="upload_file[{{ $itemSKU->id }}]"
                                                        id="surat_keterangan_usaha_file"
                                                        data-id="{{ edit_text_dagulir($pengajuan->id, $itemSKU->id)?->id }}"
                                                        placeholder="Masukkan informasi {{ $itemSKU->nama }}"
                                                        class="form-input limit-size"
                                                        accept="image/png, image/jpg, image/jpeg, image/webp"
                                                        value="{{$file}}">
                                                    <span class="text-red-500 m-0" style="display: none" id="docSKU_text">Besaran file
                                                        tidak boleh lebih dari 5 MB</span>
                                                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('dataLevelTiga.' . $key) }}
                                                        </div>
                                                    @endif
                                                    {{--  <span class="filename" style="display: inline;">{{ edit_text_dagulir($pengajuan->id, $itemSKU->id)?->opsi_text }}</span>  --}}
                                                </div>
                                            </div>
                                        @elseif($item->nama == 'NPWP')
                                            <div class="form-group" id="npwp">
                                                <div class="input-box">

                                                    <label for="">NPWP</label>
                                                    <input type="hidden" name="id_level[79]" value="79" id="npwp_id">
                                                    <input type="hidden" name="opsi_jawaban[79]" value="input text" id="npwp_opsi_jawaban">
                                                    <input type="text" maxlength="20" name="informasi[79]" id="npwp_text"
                                                        placeholder="Masukkan informasi" class="form-input" value="{{ edit_text_dagulir($pengajuan->id, 79)?->opsi_text }}">
                                                </div>
                                            </div>
                                            <div class="form-group" id="docNPWP">
                                                <div class="input-box">
                                                    <label for="">{{ $itemNPWP->nama }}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                                    @php
                                                        $file = edit_text_dagulir($pengajuan->id, $itemNPWP ->id)?->opsi_text;
                                                    @endphp
                                                    @if ($file)
                                                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                            data-title="{{ $itemNPWP->nama }}"
                                                            data-type="image"
                                                            data-filepath="{{ asset("../upload/{$pengajuan->id}/{$itemNPWP->id}/{$file}") }}"
                                                            >Preview
                                                        </a>
                                                    @endif
                                                    <input type="hidden" name="id_item_file[{{ $itemNPWP->id }}]" value="{{ $itemNPWP->id }}"
                                                        id="docNPWP_id">
                                                    <input type="file" name="upload_file[{{ $itemNPWP->id }}]" id="npwp_file" data-id="{{ edit_text_dagulir($pengajuan->id, $itemNPWP->id)?->id }}"
                                                        placeholder="Masukkan informasi {{ $itemNPWP->nama }}" class="form-input limit-size"
                                                        value="{{$file}}">
                                                    <span class="text-red-500 m-0" style="display: none" id="docNPWP_text">Besaran file
                                                        tidak boleh lebih dari 5 MB</span>
                                                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('dataLevelTiga.' . $key) }}
                                                        </div>
                                                    @endif
                                                    {{--  <span class="filename" style="display: inline;">{{ edit_text_dagulir($pengajuan->id, $itemNPWP->id)?->opsi_text }}</span>  --}}
                                                </div>
                                            </div>
                                        @else
                                            @if ($item->opsi_jawaban == 'input text')
                                                <div class="form-group">
                                                    <div class="input-box">
                                                        <label for="">{{ $item->nama }}</label>
                                                        <div class="flex items-center">
                                                            <div class="flex-1">
                                                                <input type="hidden" name="opsi_jawaban[{{ $item->id }}]"
                                                                    value="{{ $item->opsi_jawaban }}" id="">
                                                                <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}"
                                                                    id="">
                                                                <input type="text" maxlength="255" name="informasi[{{ $item->id }}]"
                                                                    id="{{ $idLevelDua }}" placeholder="Masukkan informasi {{ $item->nama }}" value="{{ edit_text_dagulir($pengajuan->id, $item->id)?->opsi_text }}"
                                                                    class="form-input {{$item->is_rupiah ? 'rupiah' : ''}}" >
                                                            </div>
                                                            @if ($item->suffix)
                                                                <div class="flex-shrink-0  mt-2.5rem">
                                                                    <span class="form-input bg-gray-100">{{$item->suffix}}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif ($item->opsi_jawaban == 'number')
                                                @if ($item->nama == 'Repayment Capacity')
                                                    {{-- table Aspek Keuangan --}}
                                                        <div class="col-span-full form-group col-md-12">
                                                            <button class="px-5 py-2 border rounded bg-theme-primary text-white btn btn-danger" type="button" id="btn-perhitungan">Perhitungan</button>
                                                        </div>
                                                        <div class="col-span-full form-group col-md-12" id="perhitungan_kredit_with_value">
                                                        </div>
                                                        @php
                                                        $getPeriode = \App\Models\PeriodeAspekKeuangan::join('perhitungan_kredit', 'periode_aspek_keuangan.perhitungan_kredit_id', '=', 'perhitungan_kredit.id')
                                                                ->where('perhitungan_kredit.pengajuan_id', $pengajuan->id)
                                                                ->select('periode_aspek_keuangan.id','periode_aspek_keuangan.perhitungan_kredit_id',
                                                                'periode_aspek_keuangan.bulan','periode_aspek_keuangan.tahun')
                                                                ->get();
                                                            function bulan($value){
                                                                    if ($value == 1) {
                                                                        echo "Januari";
                                                                    }else if($value == 2){
                                                                        echo "Februari";
                                                                    }else if($value == 3){
                                                                        echo "Maret";
                                                                    }else if($value == 4){
                                                                        echo "April";
                                                                    }else if($value == 5){
                                                                        echo "Mei";
                                                                    }else if($value == 6){
                                                                        echo "Juni";
                                                                    }else if($value == 7){
                                                                        echo "Juli";
                                                                    }else if($value == 8){
                                                                        echo "Agustus";
                                                                    }else if($value == 9){
                                                                        echo "September";
                                                                    }else if($value == 10){
                                                                        echo "Oktober";
                                                                    }else if($value == 11){
                                                                        echo "November";
                                                                    }else{
                                                                        echo "Desember";
                                                                    }
                                                            }
                                                        @endphp
                                                        @if(!$getPeriode->isEmpty())
                                                            <div class="col-span-full col-md-12" id="perhitungan_kredit_with_value_without_update">
                                                                <h5 class="font-bold">Periode : {{ bulan($getPeriode[0]->bulan) - $getPeriode[0]->tahun }}</h5>
                                                                @php
                                                                    $lev1 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)->where('level', 1)->get();

                                                                    $lev1Count = 0;
                                                                @endphp
                                                                @foreach ($lev1 as $itemAspekKeuangan)
                                                                    @php
                                                                    $lev1Count += 1;
                                                                    $lev2 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                        ->where('level', 2)
                                                                        ->where('parent_id', $itemAspekKeuangan->id)
                                                                        ->get();
                                                                    @endphp
                                                                    @if ($lev1Count > 1)
                                                                        @if ($itemAspekKeuangan->field != "Laba Rugi")
                                                                            <div class="row">
                                                                                @foreach ($lev2 as $itemAspekKeuangan2)
                                                                                    @php
                                                                                        $perhitunganKreditLev3 = \App\Models\PerhitunganKredit::rightJoin('mst_item_perhitungan_kredit', 'perhitungan_kredit.item_perhitungan_kredit_id', '=', 'mst_item_perhitungan_kredit.id')
                                                                                                ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', 1)
                                                                                                ->where('mst_item_perhitungan_kredit.level', 3)
                                                                                                ->where('mst_item_perhitungan_kredit.parent_id', $itemAspekKeuangan2->id)
                                                                                                ->where('perhitungan_kredit.pengajuan_id', $pengajuan->id)
                                                                                                ->get();
                                                                                    @endphp
                                                                                    @if ($itemAspekKeuangan2->field == "Perputaran Usaha")
                                                                                        <div class="form-group col-md-12">
                                                                                            <div class="card">
                                                                                                <h5 class="card-header">{{ $itemAspekKeuangan2->field }}</h5>
                                                                                                <div class="card-body">
                                                                                                    <table class="tables table table-bordered">
                                                                                                        @foreach ($perhitunganKreditLev3 as $itemAspekKeuangan3)
                                                                                                            @if ($itemAspekKeuangan3->field == "Perputaran Usaha")
                                                                                                                <tr>
                                                                                                                    <td width="47%">{{ $itemAspekKeuangan3->field }}</td>
                                                                                                                    <td width="6%" style="text-align: center">:</td>
                                                                                                                    @if ($itemAspekKeuangan3->add_on == "Bulan")
                                                                                                                        <td>{{ $itemAspekKeuangan3->nominal }} {{ $itemAspekKeuangan3->add_on }}</td>
                                                                                                                    @endif
                                                                                                                </tr>
                                                                                                            @endif
                                                                                                        @endforeach
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @elseif ($itemAspekKeuangan2->field == "Kebutuhan Modal Kerja" || $itemAspekKeuangan2->field == "Modal Kerja Sekarang")
                                                                                        <div class="form-group col-md-6">
                                                                                            <div class="card">
                                                                                                <h5 class="card-header">{{ $itemAspekKeuangan2->field }}</h5>
                                                                                                <div class="card-body">
                                                                                                    <table class="tables table table-bordered">
                                                                                                        @foreach ($perhitunganKreditLev3 as $itemAspekKeuangan3)
                                                                                                            @if ($itemAspekKeuangan2->field == "Kebutuhan Modal Kerja" || $itemAspekKeuangan2->field == "Modal Kerja Sekarang")
                                                                                                                <tr>
                                                                                                                    <td>{{ $itemAspekKeuangan3->field }}</td>
                                                                                                                    <td style="text-align: center">:</td>
                                                                                                                    <td class="text-{{ $itemAspekKeuangan3->align }}">Rp {{ rupiah($itemAspekKeuangan3->nominal) }}</td>
                                                                                                                </tr>
                                                                                                            @endif
                                                                                                        @endforeach
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endforeach
                                                                            </div>
                                                                        @else
                                                                            <div class="card">
                                                                                <h5 class="card-header">{{ $itemAspekKeuangan->field }}</h5>
                                                                                <div class="card-body">
                                                                                    <table class="tables table table-bordered">
                                                                                        @php $lev2Count = 0; @endphp
                                                                                        @foreach ($lev2 as $itemAspekKeuangan2)
                                                                                            @php
                                                                                            $lev2Count += 1;
                                                                                            $perhitunganKreditLev3 = \App\Models\PerhitunganKredit::rightJoin('mst_item_perhitungan_kredit', 'perhitungan_kredit.item_perhitungan_kredit_id', '=', 'mst_item_perhitungan_kredit.id')
                                                                                                ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', 1)
                                                                                                ->where('mst_item_perhitungan_kredit.level', 3)
                                                                                                ->where('mst_item_perhitungan_kredit.parent_id', $itemAspekKeuangan2->id)
                                                                                                ->where('perhitungan_kredit.pengajuan_id', $pengajuan->id)
                                                                                                ->get();
                                                                                            $fieldValues = [];
                                                                                            @endphp
                                                                                            <tr>
                                                                                                <th>{{ $itemAspekKeuangan2->field }}</th>
                                                                                                <td></td>
                                                                                                @if ($lev2Count > 1)
                                                                                                    <th colspan="2"></th>
                                                                                                @else
                                                                                                    <th>Sebelum Kredit</th>
                                                                                                    <th>Sesudah Kredit</th>
                                                                                                @endif
                                                                                            </tr>
                                                                                            @foreach ($perhitunganKreditLev3 as $itemAspekKeuangan3)
                                                                                                @php
                                                                                                $fieldValue = $itemAspekKeuangan3->field;
                                                                                                $nominal = $itemAspekKeuangan3->nominal;
                                                                                                @endphp
                                                                                                @if (!in_array($fieldValue, $fieldValues))
                                                                                                    <tr>
                                                                                                        <td>{{ $fieldValue }}</td>
                                                                                                        <td style="text-align: center">:</td>
                                                                                                        <td class="text-{{ $itemAspekKeuangan3->align }}">Rp {{ rupiah($nominal) }}</td>
                                                                                                        <td class="text-{{ $itemAspekKeuangan3->align }}">
                                                                                                            @foreach ($perhitunganKreditLev3 as $item3)
                                                                                                                @if ($item3->field == $fieldValue)
                                                                                                                    {{-- @if ($item3->nominal != $nominal) --}}
                                                                                                                    @if ($loop->iteration % 2 == 0)
                                                                                                                        Rp {{ rupiah($item3->nominal) }}<br>
                                                                                                                    @endif
                                                                                                                @endif
                                                                                                            @endforeach
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    @php
                                                                                                    $fieldValues[] = $fieldValue;
                                                                                                    @endphp
                                                                                                @endif
                                                                                            @endforeach
                                                                                        @endforeach
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <br>
                                                                        @endif
                                                                    @else
                                                                    <div class="card">
                                                                        <h5 class="card-header">{{ $itemAspekKeuangan->field }}</h5>
                                                                        <div class="card-body">
                                                                            <div class="row">
                                                                                @foreach ($lev2 as $itemAspekKeuangan2)
                                                                                    @php
                                                                                    $perhitunganKreditLev3 = \App\Models\PerhitunganKredit::rightJoin('mst_item_perhitungan_kredit', 'perhitungan_kredit.item_perhitungan_kredit_id', '=', 'mst_item_perhitungan_kredit.id')
                                                                                        ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', 1)
                                                                                        ->where('mst_item_perhitungan_kredit.level', 3)
                                                                                        ->where('mst_item_perhitungan_kredit.parent_id', $itemAspekKeuangan2->id)
                                                                                        ->where('perhitungan_kredit.pengajuan_id', $pengajuan->id)
                                                                                        ->get();
                                                                                    @endphp
                                                                                    <div class="form-group col-md-6">
                                                                                        <table class="tables table table-bordered">
                                                                                            <tr>
                                                                                                <th colspan="2">{{ $itemAspekKeuangan2->field }}</th>
                                                                                            </tr>
                                                                                            @foreach ($perhitunganKreditLev3 as $itemAspek3)
                                                                                            @if ($itemAspek3->field != "Total Angsuran")
                                                                                                @if ($itemAspek3->field == "Total")
                                                                                                    <table class="tables table table-bordered">
                                                                                                        <div class="d-flex w-100" style="padding: 0">
                                                                                                            <div class="w-100">
                                                                                                                <hr style="border: none; height: 1px; color: #333; background-color: #333;">
                                                                                                            </div>
                                                                                                            <div class="w-0 ms-2">
                                                                                                                +
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <tr>
                                                                                                            <td width='57%'>{{ $itemAspek3->field }}</td>
                                                                                                            <td class="text-{{ $itemAspek3->align }}">Rp {{ rupiah($itemAspek3->nominal) }}</td>
                                                                                                        </tr>
                                                                                                    </table>
                                                                                                @else
                                                                                                    <tr>
                                                                                                        <td width='57%'>{{ $itemAspek3->field }}</td>
                                                                                                        <td class="text-{{ $itemAspek3->align }}">Rp {{ rupiah($itemAspek3->nominal) }}</td>
                                                                                                    </tr>
                                                                                                @endif
                                                                                            @endif
                                                                                            @endforeach
                                                                                        </table>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <br>
                                                                    @endif
                                                                @endforeach
                                                                @foreach ($lev1 as $itemAspekKeuangan)
                                                                    @php
                                                                    $lev1Count += 1;
                                                                    $lev2 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                        ->where('level', 2)
                                                                        ->where('parent_id', $itemAspekKeuangan->id)
                                                                        ->get();
                                                                    @endphp
                                                                    @if ($lev1Count > 1)
                                                                        @if ($itemAspekKeuangan->field != "Laba Rugi")
                                                                            <div class="row">
                                                                                @foreach ($lev2 as $itemAspekKeuangan2)
                                                                                    @php
                                                                                        $perhitunganKreditLev3 = \App\Models\PerhitunganKredit::rightJoin('mst_item_perhitungan_kredit', 'perhitungan_kredit.item_perhitungan_kredit_id', '=', 'mst_item_perhitungan_kredit.id')
                                                                                                ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', 1)
                                                                                                ->where('mst_item_perhitungan_kredit.level', 3)
                                                                                                ->where('mst_item_perhitungan_kredit.parent_id', $itemAspekKeuangan2->id)
                                                                                                ->where('perhitungan_kredit.pengajuan_id', $pengajuan->id)
                                                                                                ->get();
                                                                                    @endphp
                                                                                    @if ($itemAspekKeuangan2->field == "Maksimal Pembiayaan")
                                                                                        <div class="form-group col-md-12">
                                                                                            <div class="card">
                                                                                                <h5 class="card-header">{{ $itemAspekKeuangan2->field }}</h5>
                                                                                                <div class="card-body">
                                                                                                    <table class="tables table table-bordered">
                                                                                                        @foreach ($perhitunganKreditLev3 as $itemAspekKeuangan3)
                                                                                                            @if ($itemAspekKeuangan2->field == "Maksimal Pembiayaan")
                                                                                                                @if ($itemAspekKeuangan3->field != "Kebutuhan Kredit")
                                                                                                                    <tr>
                                                                                                                        <td width="47%">{{ $itemAspekKeuangan3->field }}</td>
                                                                                                                        <td width="6%" style="text-align: center">:</td>
                                                                                                                        <td class="text-{{ $itemAspekKeuangan3->align }}">Rp {{ rupiah($itemAspekKeuangan3->nominal) }}</td>
                                                                                                                    </tr>
                                                                                                                @else
                                                                                                                    <table class="tables table table-borderless" style="margin: 0 auto; padding: 0 auto;">
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
                                                                                                                    <table class="tables table table-bordered">
                                                                                                                        <tr>
                                                                                                                            <td width="47%">{{ $itemAspekKeuangan3->field }}</td>
                                                                                                                            <td width="6%" style="text-align: center">:</td>
                                                                                                                            <td class="text-{{ $itemAspekKeuangan3->align }}">Rp {{ rupiah($itemAspekKeuangan3->nominal) }}</td>
                                                                                                                        </tr>
                                                                                                                    </table>
                                                                                                                @endif
                                                                                                            @endif
                                                                                                        @endforeach
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        @elseif ($itemAspekKeuangan2->field == "Plafon dan Tenor")
                                                                                        <div class="form-group col-md-12">
                                                                                            <div class="card">
                                                                                                <h5 class="card-header">{{ $itemAspekKeuangan2->field }}</h5>
                                                                                                <div class="card-body">
                                                                                                    <table class="tables table table-bordered">
                                                                                                        @foreach ($perhitunganKreditLev3 as $itemAspekKeuangan3)
                                                                                                            @if ($itemAspekKeuangan2->field == "Plafon dan Tenor")
                                                                                                                @if ($itemAspekKeuangan3->field == "Plafon usulan" || $itemAspekKeuangan3->field == "Bunga Anuitas Usulan (P.a)")
                                                                                                                    <tr>
                                                                                                                        <td width="47%">{{ $itemAspekKeuangan3->field }}</td>
                                                                                                                        <td width="6%" style="text-align: center">:</td>
                                                                                                                        @if ($itemAspekKeuangan3->add_on == "Bulan" || $itemAspekKeuangan3->add_on == "%")
                                                                                                                            <td class="text-{{ $itemAspekKeuangan3->align }}">{{ $itemAspekKeuangan3->nominal }} {{ $itemAspekKeuangan3->add_on }}</td>
                                                                                                                        @else
                                                                                                                            <td class="text-{{ $itemAspekKeuangan3->align }}">Rp {{ rupiah($itemAspekKeuangan3->nominal) }}</td>
                                                                                                                        @endif
                                                                                                                    </tr>
                                                                                                                @endif
                                                                                                            @endif
                                                                                                        @endforeach
                                                                                                        @foreach ($perhitunganKreditLev3 as $itemAspekKeuangan3)
                                                                                                            @if ($itemAspekKeuangan2->field == "Plafon dan Tenor")
                                                                                                                @if ($itemAspekKeuangan3->field == "Plafon usulan" || $itemAspekKeuangan3->field == "Bunga Anuitas Usulan (P.a)")
                                                                                                                @else
                                                                                                                <tr>
                                                                                                                    <td width="47%">{{ $itemAspekKeuangan3->field }}</td>
                                                                                                                    <td width="6%" style="text-align: center">:</td>
                                                                                                                    @if ($itemAspekKeuangan3->add_on == "Bulan" || $itemAspekKeuangan3->add_on == "%")
                                                                                                                        <td class="text-{{ $itemAspekKeuangan3->align }}">{{ $itemAspekKeuangan3->nominal }} {{ $itemAspekKeuangan3->add_on }}</td>
                                                                                                                    @endif
                                                                                                                </tr>
                                                                                                                @endif
                                                                                                            @endif
                                                                                                        @endforeach
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="" id="peringatan-pengajuan">
                                                                <div class="form-group col-md-12">
                                                                <div class="alert alert-info" role="alert">
                                                                    Perhitungan kredit masih belum ditambahkan, silahkan klik button Perhitungan.
                                                                </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    {{-- End --}}
                                                @else
                                                    @if ($item->nama == 'Omzet Penjualan' || $item->nama == 'Installment')
                                                        @if ($value->nama != 'Aspek Keuangan')
                                                            <div class="form-group">
                                                                <div class="input-box">
                                                                    <label for="">{{ $item->nama }}(Perbulan)</label>
                                                                    <input type="hidden" name="opsi_jawaban[{{ $item->id }}]"
                                                                        value="{{ $item->opsi_jawaban }}" id="">
                                                                    <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}"
                                                                        id="">
                                                                    <input type="text" maxlength="255" step="any"
                                                                        name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                                                                        placeholder="Masukkan informasi {{ $item->nama }}" class="form-input rupiah"
                                                                        value="{{ edit_text_dagulir($pengajuan->id, $item->id)?->opsi_text }}">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        @if ($value->nama != 'Aspek Keuangan')
                                                            <div class="form-group">
                                                                <div class="input-box">
                                                                    <label for="">{{ $item->nama }}</label>
                                                                    <input type="hidden" name="opsi_jawaban[{{ $item->id }}]"
                                                                        value="{{ $item->opsi_jawaban }}" id="">
                                                                    <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}"
                                                                        id="">
                                                                    <input type="text" maxlength="255" step="any"
                                                                        name="informasi[{{ $item->id }}]" id="{{ $idLevelDua }}"
                                                                        placeholder="Masukkan informasi {{ $item->nama }}" class="form-input rupiah"
                                                                        value="{{ edit_text_dagulir($pengajuan->id, $item->id)?->opsi_text }}">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif
                                            @elseif ($item->opsi_jawaban == 'persen')
                                                @if ($value->nama != 'Aspek Keuangan')
                                                    <div class="form-group">
                                                        <div class="input-box">
                                                            <label for="">{{ $item->nama }}</label>
                                                            <div class="flex items-center">
                                                                <div class="flex-1">
                                                                    <input type="hidden" name="opsi_jawaban[{{ $item->id }}]"
                                                                        value="{{ $item->opsi_jawaban }}" id="">
                                                                    <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}"
                                                                        id="">
                                                                    <input type="number" step="any" name="informasi[{{ $item->id }}]"
                                                                        id="{{ $idLevelDua }}" placeholder="Masukkan informasi {{ $item->nama }}"
                                                                        class="form-input" aria-label="Recipient's username" aria-describedby="basic-addon2"
                                                                        value="{{ edit_text_dagulir($pengajuan->id, $item->id)?->opsi_text }}" onkeydown="return event.keyCode !== 69">
                                                                </div>
                                                                @if ($item->suffix)
                                                                    <div class="flex-shrink-0  mt-2.5rem">
                                                                        <span class="form-input bg-gray-100">{{$item->suffix}}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif ($item->opsi_jawaban == 'file')
                                                @if ($value->nama != 'Aspek Keuangan')
                                                    <div class="form-group">
                                                        <div class="input-box">
                                                            <label for="">{{ $item->nama }}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp, .pdf)</small>
                                                            @php
                                                                $file = edit_text_dagulir($pengajuan->id, $item->id)?->opsi_text;
                                                                $is_multiple = $item->is_multiple;
                                                                $files = edit_text_dagulir($pengajuan->id, $item->id, $is_multiple);
                                                            @endphp
                                                            <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                                data-title="{{$item->nama}}"
                                                                data-type="image"
                                                                data-filepath="{{ asset("../upload/{$pengajuan->id}/{$item->id}/{$file}") }}"
                                                                >Preview
                                                            </a>
                                                            {{-- @if ($file)
                                                            @endif --}}
                                                            {{-- <input type="hidden" name="opsi_jawaban[]" value="{{ $item->opsi_jawaban }}" --}} {{--
                                                                            id="{{ $idLevelDua }}"> --}}
                                                            <input type="hidden" name="id_item_file[{{ $item->id }}]" value="{{ $item->id }}"
                                                                id="">
                                                            <input type="file" name="upload_file[{{ $item->id }}]" id="{{ $idLevelDua }}"
                                                                data-id="{{ edit_text_dagulir($pengajuan->id, $item->id)?->id }}" placeholder="Masukkan informasi {{ $item->nama }}"
                                                                value="{{ $file }}"
                                                                class="form-input limit-size">
                                                            <span class="text-red-500 m-0" style="display: none">Maximum upload file size is 15
                                                                MB</span>
                                                            {{--  <span class="filename" style="display: inline;">{{ edit_text_dagulir($pengajuan->id, $item->id)?->opsi_text }}</span>  --}}
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif ($item->opsi_jawaban == 'long text')
                                                <div class="form-group">
                                                    <div class="input-box">
                                                        <label for="">{{ $item->nama }}</label>
                                                        <input type="hidden" name="opsi_jawaban[{{ $item->id }}]"
                                                            value="{{ $item->opsi_jawaban }}" id="">
                                                        <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}"
                                                            id="">
                                                        <textarea name="informasi[{{ $item->id }}]" rows="4" id="{{ $idLevelDua }}" maxlength="255"
                                                            class="form-input" placeholder="Masukkan informasi {{ $item->nama }}">{{ edit_text_dagulir($pengajuan->id, $item->id)?->opsi_text }}</textarea>
                                                    </div>
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
                                                $dataLevelTiga = \App\Models\ItemModel::where('level', 3)
                                                    ->where('id_parent', $item->id)
                                                    ->get();
                                            @endphp

                                            @foreach ($dataOption as $itemOption)
                                                @if ($itemOption->option == '-')
                                                    <div class="form-group-1 col-span-2">
                                                        <div>
                                                            <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
                                                                <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                                                                    {{$item->nama}} :
                                                                </h2>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach

                                            @if (count($dataJawaban) != 0)
                                                <div
                                                    class="{{ $idLevelDua == 'persentase_kebutuhan_kredit_opsi' || $idLevelDua == 'repayment_capacity_opsi' ? '' : 'form-group' }}">
                                                    <div class="input-box">
                                                        <label for="" id="{{ $idLevelDua . '_label' }}">{{ $item->nama }}</label>

                                                        <select name="dataLevelDua[{{ $item->id }}]" id="{{ $idLevelDua }}"
                                                            class="form-select cek-sub-column" data-id_item={{ $item->id }}>
                                                            <option value=""> --Pilih Opsi-- </option>
                                                            @foreach ($dataJawaban as $key => $itemJawaban)
                                                                <option id="{{ $idLevelDua . '_' . $key }}"
                                                                    value="{{ ($itemJawaban->skor == null ? 'kosong' : $itemJawaban->skor) . '-' . $itemJawaban->id }}" {{ edit_select_dagulir($itemJawaban->id, $pengajuan->id)?->id_jawaban == $itemJawaban->id ? 'selected' : '' }}>
                                                                    {{ $itemJawaban->option }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div id="item{{ $item->id }}">

                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- item level 3 --}}
                                            @foreach ($dataLevelTiga as $keyTiga => $itemTiga)
                                                @php
                                                    $idLevelTiga = str_replace(' ', '_', strtolower($itemTiga->nama));
                                                    $jaminanTambahan = edit_text_dagulir($pengajuan?->id, 110)?->opsi_text;
                                                @endphp
                                                @if ($itemTiga->nama == 'Kategori Jaminan Utama')
                                                @elseif ($itemTiga->nama == 'Kategori Jaminan Tambahan')
                                                    <div class="form-group ">
                                                        <div class="input-box">
                                                            <label for="">{{ $itemTiga->nama }}</label>
                                                            <select name="kategori_jaminan_tambahan" id="kategori_jaminan_tambahan" class="form-input">
                                                                <option value="" selected>-- Pilih Kategori Jaminan Tambahan --</option>
                                                                <option value="Tidak Memiliki Jaminan Tambahan" {{ $jaminanTambahan == 'Tidak Memiliki Jaminan Tambahan' ? 'selected' : '' }}>Tidak Memiliki Jaminan Tambahan
                                                                </option>
                                                                <option value="Tanah" {{ $jaminanTambahan == 'Tanah' ? 'selected' : '' }}>Tanah</option>
                                                                <option value="Kendaraan Bermotor" {{ $jaminanTambahan == 'Kendaraan Bermotor' ? 'selected' : '' }}>Kendaraan Bermotor</option>
                                                                <option value="Tanah dan Bangunan" {{ $jaminanTambahan == 'Tanah dan Bangunan' ? 'selected' : '' }}>Tanah dan Bangunan</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" id="select_kategori_jaminan_tambahan"></div>
                                                @elseif ($itemTiga->nama == 'Bukti Pemilikan Jaminan Utama')

                                                @elseif ($itemTiga->nama == 'Bukti Pemilikan Jaminan Tambahan')
                                                    <div class="form-group-1 col-span-2" id="jaminan_tambahan">
                                                        <div>
                                                            <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
                                                                <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                                                                    {{$itemTiga->nama}} :
                                                                </h2>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="bukti_pemilikan_jaminan_tambahan" class="form-group-1 row col-span-2 grid grid-cols-2">

                                                    </div>
                                                @else
                                                    @if ($itemTiga->opsi_jawaban == 'input text')
                                                        <div class="form-group">
                                                            <div class="input-box">
                                                                <label for="">{{ $itemTiga->nama }}</label>
                                                                <div class="flex items-center">
                                                                    <div class="flex-1">
                                                                        <input type="hidden" name="id_level[{{ $itemTiga->id }}]" value="{{ $itemTiga->id }}"
                                                                            id="">
                                                                        <input type="hidden" name="opsi_jawaban[{{ $itemTiga->id }}]"
                                                                            value="{{ $itemTiga->opsi_jawaban }}" id="">
                                                                        <input type="text" maxlength="255" name="informasi[{{ $itemTiga->id }}]"
                                                                            placeholder="Masukkan informasi" id="{{ $idLevelTiga }}"
                                                                            value="{{ edit_text_dagulir($pengajuan->id, $itemTiga->id)?->opsi_text }}" class="form-input {{$itemTiga->is_rupiah ? 'rupiah' : ''}}">
                                                                    </div>
                                                                    @if ($itemTiga->suffix)
                                                                        <div class="flex-shrink-0 mt-2.5rem">
                                                                            <span class="form-input bg-gray-100">{{$itemTiga->suffix}}</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @elseif ($itemTiga->opsi_jawaban == 'number')
                                                        <div class="form-group">
                                                            <div class="input-box">
                                                                <label for="">{{ $itemTiga->nama }}</label>
                                                                <div class="flex items-center">
                                                                    <div class="flex-1">
                                                                        <input type="hidden" name="opsi_jawaban[{{ $itemTiga->id }}]"
                                                                            value="{{ $itemTiga->opsi_jawaban }}" id="">
                                                                        <input type="hidden" name="id_level[{{ $itemTiga->id }}]" value="{{ $itemTiga->id }}"
                                                                            id="">
                                                                        <input type="text" step="any" name="informasi[{{ $itemTiga->id }}]"
                                                                            id="{{ $idLevelTiga }}" placeholder="Masukkan informasi {{ $itemTiga->nama }}"
                                                                            class="form-input rupiah" value="{{ edit_text_dagulir($pengajuan->id, $itemTiga->id)?->opsi_text }}">
                                                                    </div>
                                                                    @if ($itemTiga->suffix)
                                                                        <div class="flex-shrink-0 mt-2.5rem">
                                                                            <span class="form-input bg-gray-100">{{$itemTiga->suffix}}</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif ($itemTiga->opsi_jawaban == 'persen')
                                                        <div class="form-group">
                                                            @if ($itemTiga->nama == 'Ratio Tenor Asuransi')
                                                            @else
                                                                <div class="input-box">
                                                                    <label for="">{{ $itemTiga->nama }}</label>
                                                                    <div class="flex items-center">
                                                                        <div class="flex-1">
                                                                            <input type="hidden" name="opsi_jawaban[{{ $itemTiga->id }}]"
                                                                                value="{{ $itemTiga->opsi_jawaban }}" id="">
                                                                            <input type="hidden" name="id_level[{{ $itemTiga->id }}]"
                                                                                value="{{ $itemTiga->id }}" id="">
                                                                                <input type="number" step="any" name="informasi[{{ $itemTiga->id }}]"
                                                                                    id="{{ $idLevelTiga }}"
                                                                                    placeholder="Masukkan informasi {{ $itemTiga->nama }}"
                                                                                    class="form-input {{$itemTiga->readonly ? 'bg-gray-100' : ''}}"
                                                                                    value="{{ edit_text_dagulir($pengajuan->id, $itemTiga->id)?->opsi_text }}">
                                                                        </div>
                                                                        @if ($itemTiga->suffix)
                                                                            <div class="flex-shrink-0 mt-2.5rem">
                                                                                <span class="form-input bg-gray-100">{{$itemTiga->suffix}}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @elseif ($itemTiga->opsi_jawaban == 'file')
                                                        <div class="form-group file-wrapper item-{{ $itemTiga->id }}">
                                                            <div class="input-box">
                                                                <label for="">{{ $itemTiga->nama }}</label>
                                                                @php
                                                                    $is_multiple = $itemTiga->is_multiple;
                                                                    $files = edit_text_dagulir($pengajuan->id, $itemTiga->id, $is_multiple);
                                                                @endphp
                                                                @if ($is_multiple)
                                                                    @foreach ($files as $key => $f)
                                                                        @if ($f)
                                                                            <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                                                data-title="{{$itemTiga->nama}}"
                                                                                data-type="image"
                                                                                data-filepath="{{ asset("../upload/{$pengajuan?->id}/{$itemTiga->id}/{$f->opsi_text}") }}"
                                                                                >Preview
                                                                            </a>
                                                                        @endif
                                                                        <div class="input-box mb-4">
                                                                            <div class="flex gap-4">
                                                                                <input type="hidden" name="id_item_file[{{ $itemTiga->id }}][]"
                                                                                    value="{{ $itemTiga->id }}" id="">
                                                                                <input type="file" name="upload_file[{{ $itemTiga->id }}][]"
                                                                                    id="{{ $idLevelTiga }}" data-id="{{ $f->id }}"
                                                                                    placeholder="Masukkan informasi {{ $itemTiga->nama }}"
                                                                                    class="form-input limit-size file-usaha" accept="image/*"
                                                                                    value="{{$f->opsi_text}}">
                                                                                <span class="text-red-500 m-0" style="display: none">Maximum upload
                                                                                    file size is 15 MB</span>
                                                                                <div class="flex gap-2 multiple-action">
                                                                                    <button type="button" class="btn-add" data-item-id="{{$itemTiga->id}}-{{strtolower(str_replace(' ', '_', $itemTiga->nama))}}">
                                                                                        <iconify-icon icon="fluent:add-16-filled" class="mt-2"></iconify-icon>
                                                                                    </button>
                                                                                    @if ($key > 0)
                                                                                        <button type="button" class="btn-minus" data-item-id="{{$itemTiga->id}}-{{strtolower(str_replace(' ', '_', $itemTiga->nama))}}">
                                                                                            <iconify-icon icon="lucide:minus" class="mt-2"></iconify-icon>
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    @if ($files)
                                                                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                                            data-title="{{$itemTiga->nama}}"
                                                                            data-type="image"
                                                                            data-filepath="{{ asset("../upload/{$pengajuan?->id}/{$itemTiga->id}/{$files->opsi_text}") }}"
                                                                            >Preview
                                                                        </a>
                                                                    @endif
                                                                    <div class="input-box mb-4">
                                                                        <div class="flex gap-4">
                                                                            <input type="hidden" name="id_item_file[{{ $itemTiga->id }}][]"
                                                                                value="{{ $itemTiga->id }}" id="">
                                                                            <input type="file" name="upload_file[{{ $itemTiga->id }}][]"
                                                                                id="{{ $idLevelTiga }}" data-id="{{ edit_text_dagulir($pengajuan->id, $itemTiga->id)?->id }}"
                                                                                placeholder="Masukkan informasi {{ $itemTiga->nama }}"
                                                                                class="form-input limit-size file-usaha" accept="image/*"
                                                                                value="{{$files->opsi_text}}">
                                                                            <span class="text-red-500 m-0" style="display: none">Maximum upload
                                                                                file size is 15 MB</span>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @elseif ($itemTiga->opsi_jawaban == 'long text')
                                                        <div class="form-group">
                                                            <div class="input-box">
                                                                <label for="">{{ $itemTiga->nama }}</label>
                                                                <input type="hidden" name="opsi_jawaban[{{ $itemTiga->id }}]"
                                                                    value="{{ $itemTiga->opsi_jawaban }}" id="">
                                                                <input type="hidden" name="id_level[{{ $itemTiga->id }}]"
                                                                    value="{{ $itemTiga->id }}" id="">
                                                                <textarea name="informasi[{{ $itemTiga->id }}]" rows="4" id="{{ $idLevelTiga }}" maxlength="255"
                                                                    class="form-input" placeholder="Masukkan informasi {{ $itemTiga->nama }}">{{ edit_text_dagulir($pengajuan->id, $itemTiga->id)?->opsi_text }}</textarea>
                                                            </div>
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
                                                        $dataLevelEmpat = \App\Models\ItemModel::where('level', 4)
                                                            ->where('id_parent', $itemTiga->id)
                                                            ->get();
                                                    @endphp

                                                    @foreach ($dataOptionTiga as $itemOptionTiga)
                                                        @if ($itemOptionTiga->option == '-')
                                                            <div class="form-group-1 col-span-2">
                                                                <div>
                                                                    <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
                                                                        <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                                                                            {{$itemTiga->nama}} :
                                                                        </h2>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                    @if (count($dataJawabanLevelTiga) != 0)
                                                        @if ($itemTiga->nama != 'Pengikatan Jaminan Utama')
                                                            <div
                                                                class="{{ $idLevelTiga == 'ratio_tenor_asuransi_opsi' || $idLevelTiga == 'ratio_coverage_opsi' ? '' : 'form-group' }}">
                                                                <div class="input-box">
                                                                    <label for=""
                                                                        id="{{ $idLevelTiga . '_label' }}">{{ $itemTiga->nama }}</label>

                                                                    <select name="dataLevelTiga[{{ $itemTiga->id }}]" id="{{ $idLevelTiga }}"
                                                                        class="form-input cek-sub-column" data-id_item={{ $itemTiga->id }}>
                                                                        <option value=""> --Pilih Opsi-- </option>
                                                                        @foreach ($dataJawabanLevelTiga as $key => $itemJawabanTiga)
                                                                            <option id="{{ $idLevelTiga . '_' . $key }}"
                                                                                value="{{ ($itemJawabanTiga->skor == null ? 'kosong' : $itemJawabanTiga->skor) . '-' . $itemJawabanTiga->id }}" {{ edit_select_dagulir($itemJawabanTiga->id, $pengajuan->id)?->id_jawaban == $itemJawabanTiga->id ? 'selected' : '' }}>
                                                                                {{ $itemJawabanTiga->option }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div id="item{{ $itemTiga->id }}">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif

                                                    {{-- item level 4 --}}
                                                    @foreach ($dataLevelEmpat as $keyEmpat => $itemEmpat)
                                                        @php
                                                            $idLevelEmpat = str_replace(' ', '_', strtolower($itemEmpat->nama));
                                                        @endphp

                                                        @if ($itemEmpat->opsi_jawaban == 'input text')
                                                            <div class="form-group">
                                                                <div class="input-box">
                                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                                    <input type="hidden" name="id_level[{{ $itemEmpat->id }}]"
                                                                        value="{{ $itemEmpat->id }}" id="">
                                                                    <input type="hidden" name="opsi_jawaban[{{ $itemEmpat->id }}]"
                                                                        value="{{ $itemEmpat->opsi_jawaban }}" id="">
                                                                    @if ($itemEmpat->nama == 'Masa Berlaku Asuransi Penjaminan')
                                                                        <div class="input-group">
                                                                            <input type="text" maxlength="255"
                                                                                name="informasi[{{ $itemEmpat->id }}]"
                                                                                id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan_/_ht' ? '' : $idLevelEmpat }}"
                                                                                placeholder="Masukkan informasi"
                                                                                class="form-input only-number"
                                                                                value="{{ edit_text_dagulir($pengajuan->id, $itemEmpat->id)?->opsi_text }}">
                                                                            <div class="input-group-append">
                                                                                <div class="input-group-text" id="addon_tenor_yang_diminta">
                                                                                    Bulan</div>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <input type="text" maxlength="255" name="informasi[{{ $itemEmpat->id }}]"
                                                                            id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan_/_ht' ? '' : $idLevelEmpat }}"
                                                                            placeholder="Masukkan informasi" value="{{ edit_text_dagulir($pengajuan->id, $itemEmpat->id)?->opsi_text }}"
                                                                            class="form-input  {{$itemEmpat->is_rupiah ? 'rupiah' : ''}}">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @elseif ($itemEmpat->opsi_jawaban == 'number')
                                                            <div class="form-group">
                                                                <div class="input-box">
                                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                                    <div class="flex items-center">
                                                                        <div class="flex-1">
                                                                            <input type="hidden" name="opsi_jawaban[{{ $itemEmpat->id }}]"
                                                                                value="{{ $itemEmpat->opsi_jawaban }}" id="">
                                                                            <input type="hidden" name="id_level[{{ $itemEmpat->id }}]"
                                                                                value="{{ $itemEmpat->id }}" id="">
                                                                            <input type="text" step="any" name="informasi[{{ $itemEmpat->id }}]"
                                                                                id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan_/_ht' ? 'nilai_asuransi_penjaminan' : $idLevelEmpat }}"
                                                                                placeholder="Masukkan informasi {{ $itemEmpat->nama }}"
                                                                                class="form-input only-number" value="{{ edit_text_dagulir($pengajuan->id, $itemEmpat->id)?->opsi_text }}">
                                                                        </div>
                                                                        @if ($itemEmpat->suffix)
                                                                            <div class="flex-shrink-0 mt-2.5rem">
                                                                                <span class="form-input bg-gray-100">{{$itemEmpat->suffix}}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @elseif ($itemEmpat->opsi_jawaban == 'persen')
                                                            <div class="form-group">
                                                                <div class="input-box">
                                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                                    <div class="flex items-center">
                                                                        <div class="flex-1">
                                                                            <input type="hidden" name="opsi_jawaban[{{ $itemEmpat->id }}]"
                                                                                value="{{ $itemEmpat->opsi_jawaban }}" id="">
                                                                            <input type="hidden" name="id_level[{{ $itemEmpat->id }}]"
                                                                                value="{{ $itemEmpat->id }}" id="">
                                                                            <input type="number" step="any" name="informasi[{{ $itemEmpat->id }}]"
                                                                                id="{{ $idLevelEmpat }}"
                                                                                placeholder="Masukkan informasi {{ $itemEmpat->nama }}" class="form-input"
                                                                                aria-label="Recipient's username" aria-describedby="basic-addon2"
                                                                                value="{{ edit_text_dagulir($pengajuan->id, $itemEmpat->id)?->opsi_text }}">
                                                                        </div>
                                                                        @if ($itemEmpat->suffix)
                                                                            <div class="flex-shrink-0 mt-2.5rem">
                                                                                <span class="form-input bg-gray-100">{{$itemEmpat->suffix}}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @elseif ($itemEmpat->opsi_jawaban == 'file')
                                                            <div class="form-group">
                                                                <div class="input-box">
                                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                                    {{-- <input type="hidden" name="opsi_jawaban[]" value="{{ $itemEmpat->opsi_jawaban }}" id=""> --}}
                                                                    <input type="hidden" name="id_item_file[{{ $itemEmpat->id }}]"
                                                                        value="{{ $itemEmpat->id }}" id="">
                                                                    <input type="file" id="{{ $idLevelEmpat }}"
                                                                        name="upload_file[{{ $itemEmpat->id }}]" data-id="{{ edit_text_dagulir($pengajuan->id, $itemEmpat->id)?->id }}"
                                                                        placeholder="Masukkan informasi {{ $itemEmpat->nama }}"
                                                                        class="form-input limit-size">
                                                                    <span class="text-red-500 m-0" style="display: none">Maximum upload file
                                                                        size is 5 MB</span>
                                                                    {{--  <span class="filename" style="display: inline;">{{ edit_text_dagulir($pengajuan->id, $itemEmpat->id)?->opsi_text }}</span>  --}}
                                                                </div>
                                                            </div>
                                                        @elseif ($itemEmpat->opsi_jawaban == 'long text')
                                                            <div class="form-group">
                                                                <div class="input-box">
                                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                                    <input type="hidden" name="opsi_jawaban[{{ $itemEmpat->id }}]"
                                                                        value="{{ $itemEmpat->opsi_jawaban }}" id="">
                                                                    <input type="hidden" name="id_level[{{ $itemEmpat->id }}]"
                                                                        value="{{ $itemEmpat->id }}" id="">
                                                                    <textarea name="informasi[{{ $itemEmpat->id }}]" rows="4" id="{{ $idLevelEmpat }}" maxlength="255"
                                                                        class="form-input" placeholder="Masukkan informasi {{ $itemEmpat->nama }}">{{ edit_text_dagulir($pengajuan->id, $itemEmpat->id)?->opsi_text }}</textarea>
                                                                </div>
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
                                                                <div class="form-group-1">
                                                                    <h6>{{ $itemEmpat->nama }}</h6>
                                                                </div>
                                                            @endif
                                                        @endforeach

                                                        @if (count($dataJawabanLevelEmpat) != 0)
                                                            <div class="form-group">
                                                                <div class="input-box">
                                                                    <label for="">{{ $itemEmpat->nama }}</label>
                                                                    <select name="dataLevelEmpat[{{ $itemEmpat->id }}]" id="{{ $idLevelEmpat }}"
                                                                        class="form-input cek-sub-column" data-id_item={{ $itemEmpat->id }}>
                                                                        <option value=""> --Pilih Opsi -- </option>
                                                                        @foreach ($dataJawabanLevelEmpat as $itemJawabanEmpat)
                                                                            <option id="{{ $idLevelEmpat . '_' . $key }}"
                                                                                value="{{ ($itemJawabanEmpat->skor == null ? 'kosong' : $itemJawabanEmpat->skor) . '-' . $itemJawabanEmpat->id }}" {{ edit_select_dagulir($itemEmpat->id, $pengajuan->id)?->id_jawaban == $itemJawabanEmpat->id ?
                                                                                    'selected' : '' }}>
                                                                                {{ $itemJawabanEmpat->option }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div id="item{{ $itemEmpat->id }}">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                    </div>

                                    <div class="form-group-1">
                                        <hr style="border: 0.2px solid #E3E6EA;">
                                        <label for="">Pendapat dan Usulan {{ $value->nama }}</label>
                                        <input type="hidden" name="id_aspek[{{ $value->id }}]" value="{{ $value->id }}">
                                        <textarea name="pendapat_per_aspek[{{ $value->id }}]"
                                            class="form-input @error('pendapat_per_aspek') is-invalid @enderror" id="" maxlength="255"
                                            cols="30" rows="4" placeholder="Pendapat Per Aspek">{{ edit_usulan_dagulir($value->id, $pengajuan->id)?->pendapat_per_aspek }}</textarea>
                                        @error('pendapat_per_aspek')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="flex justify-between">
                                        <button type="button"
                                          class="px-5 py-2 border rounded bg-white text-gray-500"
                                        >
                                          Kembali
                                        </button>
                                        <div>
                                          <button type="button"
                                          class="px-5 prev-tab py-2 border rounded bg-theme-secondary text-white"
                                        >
                                          Sebelumnya
                                        </button>
                                        <button type="button"
                                          class="px-5 next-tab py-2 border rounded bg-theme-primary text-white"
                                        >
                                          Selanjutnya
                                        </button>
                                        <button type="submit" class="px-5 py-2 border rounded bg-green-600 text-white btn-simpan hidden" id="submit">Simpan </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div id="pendapat-dan-usulan-tab" class="is-tab-content">
                        <div class="pb-10 space-y-3">
                            <h2 class="text-4xl font-bold tracking-tighter text-theme-primary">Pendapat dan Usulan</h2>
                        </div>
                        <div class="self-start bg-white w-full border">
                            <div class="p-5 border-b">
                                <h2 class="font-bold text-lg tracking-tighter">
                                    Pendapat dan Usulan
                                </h2>
                            </div>
                            <!-- pendapat-dan-usulan -->
                            <div class="p-5 space-y-5">
                                <div class="form-group-1">
                                    <div class="input-box">
                                        <label for="">Pendapat dan Usulan</label>
                                        <textarea name="komentar_staff" class="form-textarea"
                                            placeholder="Pendapat dan Usulan" id="">{{$komentar?->komentar_staff}}</textarea>
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <button class="px-5 py-2 border rounded bg-white text-gray-500">
                                        Kembali
                                    </button>
                                    <div>
                                        <button class="px-5 py-2 border rounded bg-theme-secondary text-white">
                                            Sebelumnya
                                        </button>
                                        <button class="px-5 py-2 border rounded bg-theme-primary text-white" type="submit">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@push('script-inject')
@include('dagulir.pengajuan-kredit.scripts.edit')
@include('pengajuan-kredit.partials.create-save-script')
@include('pengajuan-kredit.modal.perhitungan-modal-edit')
<script>
    function stringContainsValueFromArray(inputString, searchArray) {
        for (let i = 0; i < searchArray.length; i++) {
            if (inputString.includes(searchArray[i])) {
            return true; // Return true if a match is found
            }
        }
        return false; // Return false if no match is found
    }
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
                                                    result = formatrupiah(parseInt(result).toString())
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
                                            result = result < 0 ? `(${formatrupiah(parseInt(result).toString())})` : formatrupiah(parseInt(result).toString())
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

    function formatBulan(value) {
        switch (value) {
            case 1:
                return "Januari";
            case 2:
                return "Februari";
            case 3:
                return "Maret";
            case 4:
                return "April";
            case 5:
                return "Mei";
            case 6:
                return "Juni";
            case 7:
                return "Juli";
            case 8:
                return "Agustus";
            case 9:
                return "September";
            case 10:
                return "Oktober";
            case 11:
                return "November";
            default:
                return "Desember";
        }
    }

    $('#btn-perhitungan').on('click', function() {
        $('#loading-simpan-perhitungan').hide();
        $('#perhitunganModalAfterLoading').show();
        // $("#perhitunganModalEdit").modal('show')
        $("#perhitunganModalEdit").removeClass('hidden')
        calcForm()
    });

    var indexBtnSimpan = 0;
    var selectValueElementBulan;
    var selectElementTahun;
    $('#btnEditPerhitungan').on('click', function() {
        indexBtnSimpan += 1;
        let data = {
            idNasabah: {{ $pengajuan->id }}
        }
        $("#perhitunganModalEdit input").each(function(){
            let input = $(this);

            data[input.attr("name")] = input.val();
            data['idNasabah'] = {{ $pengajuan->id }}
        });

        $('#peringatan-pengajuan').empty();
        $('#perhitungan_kredit_with_value_without_update').empty();
        // $('#loading-simpan-perhitungan').show();

        var selectElementBulan = $("#periode").find(":selected").text();
        selectValueElementBulan = $("#periode").val();
        selectElementTahun = $("#periode_tahun").find(":selected").text();
        var titlePeriode = ``;

        if (indexBtnSimpan == 1) {
            $('#perhitungan_kredit_with_value').append(`
                <br>
                <h5 class="font-bold">Periode : ${selectElementBulan} - ${selectElementTahun}</h5>
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
                <h5 class="font-bold">Periode : ${selectElementBulan} - ${selectElementTahun}</h5>
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
                    url: "{{ route('pengajuan-kredit.get-data-perhitungan-kredit-lev3-edit') }}",
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
                            // $('.modal').modal('hide');
                            $('#perhitunganModalEdit').addClass('hidden')
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
                    url: "{{ route('pengajuan-kredit.edit-perhitungan-kredit') }}",
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
                console.log(res2)

                const resPeriode = await $.ajax({
                    url: '{{ route('get-periode-perhitungan-kredit-edit') }}?pengajuan_id=' + res1.request.idNasabah,
                    type: "GET",
                    beforeSend: function() {
                        $('#loading-simpan-perhitungan').show();
                        $('#perhitunganModalAfterLoading').hide();
                    },
                });
                console.log(resPeriode);
                if (resPeriode.length === 0) {
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
                        success: function (response) {
                            console.log(response);
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }else{
                    $.ajax({
                        url: '{{ route("pengajuan-kredit.update-data-periode-aspek-keuangan") }}?id' + resPeriode.result[0].id,
                        type: 'PUT',
                        data: {
                            perhitungan_kredit_id: resPeriode.result[0].perhitungan_kredit_id,
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

                const resPeriode2 = await $.ajax({
                    url: '{{ route('get-periode-perhitungan-kredit-edit') }}?pengajuan_id=' + res1.request.idNasabah,
                    type: "GET",
                    beforeSend: function() {
                        $('#loading-simpan-perhitungan').show();
                        $('#perhitunganModalAfterLoading').hide();
                    },
                });
                console.log(resPeriode2);

                var lev1Count = 0;
                for (const element of res2.result) {
                    lev1Count += 1;
                    titlePeriode = `Periode : ${formatBulan(resPeriode2.result[0].bulan)} - ${resPeriode2.result[0].tahun}`;
                    if (lev1Count > 1) {
                        if (element.field == "Laba Rugi") {
                            $('#row_perhitungan_kredit').append(`
                                <div class="form-group col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">${element.field}</h5>
                                        <div class="card-body">
                                            <table class="tables table table-bordered" id="lev1_count_dua">
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
                                                    <table class="tables table table-bordered" id="${uniqueTableId2}">
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
                                                        <table class="tables table table-bordered" id="table_max_pembiayaan">
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
                                                        <table class="tables table table-bordered" id="table_plafon">
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
                                    <table class="tables table table-bordered" id="${uniqueTableId}">
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
                                    <table class="tables table table-bordered" id="total_lev1${element2.id}">
                                    </table>
                                </div>
                            `);
                        }

                        const res4 = await getDataPerhitunganKreditLev2(element2, res1.request.idNasabah);
                        console.log("RES 4 = " + res1.request.idNasabah);
                        console.log(res4);

                        var angsuranPokokSetiapBulanCount = 0;
                        var lev3Count = 0;
                        var maxRowCount = 0;
                        var lengthPlafonUsulan = 0;
                        if (lev1Count > 1) {
                            var displayedFieldValues = {};
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
                                                    <table class="tables table table-bordered">
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
                $('#perhitunganModalEdit').addClass('hidden');
                // $('.modal').modal('hide');
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
</script>
@endpush
