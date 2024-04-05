@include('components.new.modal.loading')
@extends('layouts.tailwind-template')

@section('modal')
    @include('dagulir.pengajuan-kredit.modal.modal-photo-draft')
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
$skema = $skema != null ? $skema : 'Dagulir';
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
<div class="modal-layout-no-backdrop hidden" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal modal-sm bg-white"" role="document">
        <form action="" >
            <div class="modal-content">
                <div class="modal-head">
                    <div class="title">
                        <h5 class="font-bold text-2xl tracking-tighter text-theme-text" id="exampleModalLabel">PRODUK & SKEMA KREDIT</h5>
                    </div>
                    <button data-dismiss-id="exampleModal">
                        <iconify-icon
                            icon="iconamoon:close-bold"
                            class="text-2xl"
                        ></iconify-icon>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group-1">
                        <div class="input-box m-0">
                            <label for="produk">Pilih Produk Kredit</label>
                            <select class="form-select" id="produk" name="produk">
                                <option value="">- Pilih Produk Kredit -</option>
                                @foreach ($produkKredit as $item)
                                    <option value="{{$item->id}}" {{$produk == $item->name ? 'selected' : ''}}>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-box m-0">
                            <label for="skema">Pilih Skema Kredit</label>
                            <select class="form-select" id="skema" name="skema">
                                <option value="">- Pilih Skema Kredit -</option>
                                @foreach ($skemaKredit as $item)
                                    <option value="{{$item->id}}" {{$skema == $item->name ? 'selected' : ''}}>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-box m-0">
                            <label for="limit">Pilih Limit</label>
                            <select class="form-select" id="limit" name="limit">
                                <option value="">- Pilih Limit -</option>
                                @foreach ($limitKredit as $item)
                                    <option value="{{$item->id}}">{{number_format($item->from,0,',','.')}} {{$item->operator}} {{number_format($item->to,0,',','.')}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="flex justify-start items-end ml-4 pb-4">
                        <button type="submit" class="px-7 py-2 rounded font-semibold flex gap-3 bg-theme-primary border text-white mt-2 btn btn-primary" id="btnSkema" onclick="$('#exampleModal').modal('hide')">Simpan</button>
                    </div>
            </div>
        </form>
    </div>
</div>

<section class="">
    <nav class="w-full bg-white p-3  top-[4rem] border sticky">
        <div class="owl-carousel owl-theme tab-wrapper">
            <button data-toggle="tab" data-tab="dagulir" class="btn btn-tab active-tab font-semibold">
                <span class="percentage">0%</span> Data Umum
            </button>
            @if ($skema == 'KKB' || $duTemp?->skema_kredit == 'KKB')
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
            <form action="{{ route('dagulir.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_dagulir_temp" id="id_dagulir_temp" value="{{ $duTemp?->id }}">
                <input type="hidden" name="isDraft" value="1">
                <div class="mt-3 container mx-auto">
                    <div id="dagulir-tab" class="is-tab-content active">
                        @if ($skema == 'Dagulir')
                            @include('dagulir.pengajuan.create-dagulir-draft')
                        @else
                            @include('dagulir.pengajuan.create-pengajuan-draft')
                        @endif
                    </div>
                    @if ($skema == 'KKB' || $duTemp?->skema_kredit == 'KKB')
                        @include('dagulir.pengajuan.draft-data-po')
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
                            $nib = $skema != 'Dagulir' ? temporary($duTemp->id, 77)?->opsi_text : temporary_dagulir($duTemp->id, 77)?->opsi_text;
                            $sku = $skema != 'Dagulir' ? temporary($duTemp->id, 78)?->opsi_text : temporary_dagulir($duTemp->id, 78)?->opsi_text;
                            $npwp = $skema != 'Dagulir' ? temporary($duTemp->id, 79)?->opsi_text : temporary_dagulir($duTemp->id, 79)?->opsi_text;
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
                                            <div class="form-group" id="npwpsku" style="{{ strlen($npwp) == 0 ? 'display: none' : '' }}" >
                                                <div class="input-box">
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
                                                        <input type="hidden" name="isNpwp" id="statusNpwp" class="form-input"
                                                            value="{{ $npwp != null ? '1' : '0' }}">
                                                        <label
                                                            for="isNpwp"
                                                            class="font-semibold cursor-pointer text-theme-text"
                                                            >Memiliki NPWP</label
                                                        >
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group" id="space_nib"></div>
                                            <div class="form-group" id="nib">
                                                <div class="input-box">
                                                    <label for="">NIB</label>
                                                    <input type="hidden" name="id_level[77]" value="77" id="nib_id">
                                                    <input type="hidden" name="opsi_jawaban[77]" value="input text" id="nib_opsi_jawaban">
                                                    <input type="text" maxlength="255" name="informasi[77]" id="nib_text"
                                                        placeholder="Masukkan informasi" class="form-input"value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, 77)?->opsi_text : temporary_dagulir($duTemp->id, 77)?->opsi_text }}">

                                                </div>
                                            </div>
                                            <div class="form-group" id="docNIB">
                                                <div class="input-box">
                                                    <label for="">{{ $itemNIB->nama }}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                                    @php
                                                        $docNIB = $skema != 'Dagulir' ?  temporary($duTemp->id, $itemNIB->id)?->id : temporary_dagulir($duTemp->id, $itemNIB->id)
                                                    @endphp
                                                    @if ($docNIB)
                                                        @if (temporary_dagulir($duTemp->id, $itemNIB->id)?->opsi_text != null)
                                                            <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                                data-title="{{ $itemNIB->nama }}" data-filepath="{{asset('../upload/temp')}}/{{temporary_dagulir($duTemp->id, $itemNIB->id)?->id_jawaban}}/{{temporary_dagulir($duTemp->id, $itemNIB->id)?->opsi_text}}" data-extension="{{ explode('.', temporary_dagulir($duTemp->id, $itemNIB->id)?->opsi_text)[1] }}">Preview</a>
                                                        @endif
                                                    @endif
                                                    <input type="hidden" name="id_item_file[{{ $itemNIB->id }}]" value="{{ $itemNIB->id }}"
                                                        id="docNIB_id">
                                                    <input type="file" name="upload_file[{{ $itemNIB->id }}]" data-id="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemNIB->id)?->id : temporary_dagulir($duTemp->id, $itemNIB->id)?->id }}"
                                                        placeholder="Masukkan informasi {{ $itemNIB->nama }}" class="form-input limit-size"
                                                        id="file_nib" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemNIB->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemNIB->id)?->opsi_text}}">
                                                    <span class="text-red-500 m-0" style="display: none" id="docNIB_text">Besaran file
                                                        tidak boleh lebih dari 5 MB</span>
                                                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('dataLevelTiga.' . $key) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group" id="surat_keterangan_usaha">
                                                <div class="input-box">
                                                    <label for="">Surat Keterangan Usaha</label>
                                                    <input type="hidden" name="id_level[78]" value="78" id="surat_keterangan_usaha_id">
                                                    <input type="hidden" name="opsi_jawaban[78]" value="input text"
                                                        id="surat_keterangan_usaha_opsi_jawaban">
                                                    <input type="text" maxlength="255" name="informasi[78]" id="surat_keterangan_usaha_text"
                                                        placeholder="Masukkan informasi" class="form-input" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, 78)?->opsi_text : temporary_dagulir($duTemp->id, 78)?->opsi_text }}">
                                                </div>
                                            </div>

                                            <div class="form-group" id="docSKU">
                                                <div class="input-box">
                                                    <label for="">{{ $itemSKU->nama }}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                                    @php
                                                        $sku_file = $skema != 'Dagulir' ?  temporary($duTemp->id, $itemSKU->id)?->id : temporary_dagulir($duTemp->id, $itemSKU->id)
                                                    @endphp
                                                    @if ($sku_file)
                                                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                            data-title="{{ $itemSKU->nama }}" data-filepath="{{asset('../upload/temp')}}/{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemSKU->id)?->id_jawaban : temporary_dagulir($duTemp->id, $itemSKU->id)?->id_jawaban}}/{{ $skema != 'Dagulir' ?  temporary($duTemp->id, $itemSKU->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemSKU->id)?->opsi_text}}" data-extension="{{ explode('.', $skema != 'Dagulir' ? temporary($duTemp->id, $itemSKU->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemSKU->id)?->opsi_text)[1] }}">Preview</a>
                                                    @endif
                                                    <input type="hidden" name="id_item_file[{{ $itemSKU->id }}]" value="{{ $itemSKU->id }}"
                                                        id="docSKU_id">
                                                    <input type="file" name="upload_file[{{ $itemSKU->id }}]" id="surat_keterangan_usaha_file"
                                                        data-id="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemSKU->id)?->id : temporary_dagulir($duTemp->id, $itemSKU->id)?->id }}" placeholder="Masukkan informasi {{ $itemSKU->nama }}"
                                                        class="form-input limit-size" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemSKU->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemSKU->id)?->opsi_text}}">
                                                    <span class="text-red-500 m-0" style="display: none" id="docSKU_text">Besaran file
                                                        tidak boleh lebih dari 5 MB</span>
                                                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('dataLevelTiga.' . $key) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($item->nama == 'NPWP')
                                            <div class="form-group" id="npwp">
                                                <div class="input-box">

                                                    <label for="">NPWP</label>
                                                    <input type="hidden" name="id_level[79]" value="79" id="npwp_id">
                                                    <input type="hidden" name="opsi_jawaban[79]" value="input text" id="npwp_opsi_jawaban">
                                                    <input type="text" maxlength="20" name="informasi[79]" id="npwp_text"
                                                        placeholder="Masukkan informasi" class="form-input" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, 79)?->opsi_text : temporary_dagulir($duTemp->id, 79)?->opsi_text }}">
                                                </div>
                                            </div>
                                            <div class="form-group" id="docNPWP">
                                                <div class="input-box">
                                                    <label for="">{{ $itemNPWP->nama }}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                                    @php
                                                        $fileDocNPWP = $skema != 'Dagulir' ? temporary($duTemp->id, $itemNPWP->id) : temporary_dagulir($duTemp->id, $itemNPWP->id);
                                                    @endphp
                                                    @if ($fileDocNPWP)
                                                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                            data-title="{{ $itemNPWP->nama }}" data-filepath="{{asset('../upload/temp')}}/{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemNPWP->id)?->id_jawaban : temporary_dagulir($duTemp->id, $itemNPWP->id)?->id_jawaban }}/{{$skema != 'Dagulir' ? temporary($duTemp->id, $itemNPWP->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemNPWP->id)?->opsi_text}}" data-extension="{{ explode('.',$skema != 'Dagulir' ? temporary($duTemp->id, $itemNPWP->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemNPWP->id)?->opsi_text)[1] }}">Preview</a>
                                                    @endif
                                                    <input type="hidden" name="id_item_file[{{ $itemNPWP->id }}]" value="{{ $itemNPWP->id }}"
                                                        id="docNPWP_id">
                                                    <input type="file" name="upload_file[{{ $itemNPWP->id }}]" id="npwp_file" data-id="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemNPWP->id)?->id : temporary_dagulir($duTemp->id, $itemNPWP->id)?->id }}"
                                                        placeholder="Masukkan informasi {{ $itemNPWP->nama }}" class="form-input limit-size" value="{{  $skema != 'Dagulir' ? temporary($duTemp->id, $itemNPWP->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemNPWP->id)?->opsi_text}}">
                                                    <span class="text-red-500 m-0" style="display: none" id="docNPWP_text">Besaran file
                                                        tidak boleh lebih dari 5 MB</span>
                                                    @if (isset($key) && $errors->has('dataLevelTiga.' . $key))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('dataLevelTiga.' . $key) }}
                                                        </div>
                                                    @endif
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
                                                                        id="{{ $idLevelDua }}" placeholder="Masukkan informasi {{ $item->nama }}" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $item->id)?->opsi_text : temporary_dagulir($duTemp->id, $item->id)?->opsi_text }}"
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
                                                                    ->where('perhitungan_kredit.temp_calon_nasabah_id', $duTemp->id)
                                                                    ->select('periode_aspek_keuangan.*', 'perhitungan_kredit.*')
                                                                    ->get();
                                                                    function formatBulan($value){
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
                                                                    <h5 class="font-bold">Periode : {{ formatBulan($getPeriode[0]->bulan) - $getPeriode[0]->tahun }}</h5>
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
                                                                                                                        ->where('perhitungan_kredit.temp_calon_nasabah_id', $duTemp->id)
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
                                                                                                                                                    ->where('perhitungan_kredit.temp_calon_nasabah_id', $duTemp->id)
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
                                                                                                                ->where('perhitungan_kredit.temp_calon_nasabah_id', $duTemp->id)
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
                                                                                                                                                ->where('perhitungan_kredit.temp_calon_nasabah_id', $duTemp->id)
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
                                                        <div class="form-group">
                                                            <div class="input-box">
                                                                <label for="">{{ $item->nama }}</label>
                                                                <input type="hidden" name="opsi_jawaban[{{ $item->id }}]"
                                                                    value="{{ $item->opsi_jawaban }}" id="">
                                                                <input type="hidden" name="id_level[{{ $item->id }}]" value="{{ $item->id }}"
                                                                    id="">
                                                                <input type="text" maxlength="255" name="informasi[{{ $item->id }}]"
                                                                    id="{{ $idLevelDua }}" placeholder="Masukkan informasi {{ $item->nama }}"
                                                                    class="form-input" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $item->id)?->opsi_text : temporary_dagulir($duTemp->id, $item->id)?->opsi_text }}">
                                                            </div>
                                                        </div>
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
                                                                            value="{{ $skema != 'Dagulir' ?  temporary($duTemp->id, $item->id)?->opsi_text : temporary_dagulir($duTemp->id, $item->id)?->opsi_text }}">
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
                                                                            value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $item->id)?->opsi_text : temporary_dagulir($duTemp->id, $item->id)?->opsi_text }}">
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
                                                                            value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $item->id)?->opsi_text : temporary_dagulir($duTemp->id, $item->id)?->opsi_text }}" onkeydown="return event.keyCode !== 69">
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
                                                        <div class="input-box">
                                                                <div class="form-group">
                                                                <label for="">{{ $item->nama }}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp, .pdf)</small>
                                                                @php
                                                                    $cek_file = $skema != 'Dagulir' ? temporary($duTemp->id, $item->id) : temporary_dagulir($duTemp->id, $item->id)
                                                                @endphp
                                                                @if ($cek_file)
                                                                    <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                                        data-title="{{ $item->nama }}" data-filepath="{{asset('../upload/temp')}}/{{$skema != 'Dagulir' ? temporary($duTemp->id, $item->id)?->id_jawaban : temporary_dagulir($duTemp->id, $item->id)?->id_jawaban}}/{{$skema != 'Dagulir' ? temporary($duTemp->id, $item->id)?->opsi_text : temporary_dagulir($duTemp->id, $item->id)?->opsi_text}}" data-extension="{{ explode('.', $skema != 'Dagulir' ? temporary($duTemp->id, $item->id)?->opsi_text :  temporary_dagulir($duTemp->id, $item->id)?->opsi_text)[1] }}">Preview</a>
                                                                @endif
                                                                {{-- <input type="hidden" name="opsi_jawaban[]" value="{{ $item->opsi_jawaban }}" --}} {{--
                                                                                id="{{ $idLevelDua }}"> --}}
                                                                <input type="hidden" name="id_item_file[{{ $item->id }}]" value="{{ $item->id }}"
                                                                    id="">
                                                                <input type="file" name="upload_file[{{ $item->id }}]" id="{{ $idLevelDua }}"
                                                                    data-id="{{ temporary_dagulir($duTemp->id, $item->id)?->id }}" placeholder="Masukkan informasi {{ $item->nama }}"
                                                                    class="form-input limit-size" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $item->id)?->opsi_text : temporary_dagulir($duTemp->id, $item->id)?->opsi_text}}">
                                                                <span class="text-red-500 m-0" style="display: none">Maximum upload file size is 15
                                                                    MB</span>
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
                                                                class="form-input" placeholder="Masukkan informasi {{ $item->nama }}">{{ $skema != 'Dagulir' ? temporary($duTemp->id, $item->id)?->opsi_text : temporary_dagulir($duTemp->id, $item->id)?->opsi_text }}</textarea>
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
                                                                    value="{{ ($itemJawaban->skor == null ? 'kosong' : $itemJawaban->skor) . '-' . $itemJawaban->id }}"
                                                                    @if ($skema != 'Dagulir')
                                                                        {{ temporary_select($item->id, $duTemp->id)?->id_jawaban == $itemJawaban->id ? 'selected' : '' }}
                                                                    @else
                                                                        {{ temporary_select_dagulir($item->id, $duTemp->id)?->id_jawaban == $itemJawaban->id ? 'selected' : '' }}
                                                                    @endif
                                                                    >
                                                                    {{ $itemJawaban->option }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div id="item{{ $item->id }}">

                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                                @foreach ($dataLevelTiga as $keyTiga => $itemTiga)
                                                    @php
                                                        $idLevelTiga = str_replace(' ', '_', strtolower($itemTiga->nama));
                                                        $jaminanTambahan = $skema != 'Dagulir' ? $duTemp?->jaminan_tambahan : temporary_dagulir($duTemp?->id, 76)?->opsi_text;
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
                                                                                value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemTiga->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemTiga->id)?->opsi_text }}" class="form-input {{$itemTiga->is_rupiah ? 'rupiah' : ''}}">
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
                                                                                class="form-input rupiah" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemTiga->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemTiga->id)?->opsi_text }}">
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
                                                                                        value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemTiga->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemTiga->id)?->opsi_text }}">
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
                                                                    <label for="">{{ $itemTiga->nama }}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                                                    @php
                                                                        $cek_file_tiga = $skema != 'Dagulir' ? temporary($duTemp->id, $itemTiga->id) : temporary_dagulir($duTemp->id, $itemTiga->id)
                                                                    @endphp
                                                                    @if ($cek_file_tiga)
                                                                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                                            data-title="{{ $itemTiga->nama }}" data-filepath="{{asset('../upload/temp')}}/{{$skema != 'Dagulir' ? temporary($duTemp->id, $itemTiga->id)?->id_jawaban : temporary_dagulir($duTemp->id, $itemTiga->id)?->id_jawaban}}/{{$skema != 'Dagulir' ? temporary($duTemp->id, $itemTiga->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemTiga->id)?->opsi_text}}" data-extension="{{ explode('.', $skema != 'Dagulir' ? temporary($duTemp->id, $itemTiga->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemTiga->id)?->opsi_text)[1] }}">Preview</a>
                                                                    @endif
                                                                    <div class="input-box mb-4">
                                                                        <div class="flex gap-4">
                                                                            <input type="hidden" name="id_item_file[{{ $itemTiga->id }}][]"
                                                                                value="{{ $itemTiga->id }}" id="">
                                                                            <input type="file" name="upload_file[{{ $itemTiga->id }}][]"
                                                                                id="{{ $idLevelTiga }}" data-id="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemTiga->id)?->id : temporary_dagulir($duTemp->id, $itemTiga->id)?->id }}"
                                                                                placeholder="Masukkan informasi {{ $itemTiga->nama }}"
                                                                                class="form-input limit-size file-usaha" accept="image/*" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemTiga->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemTiga->id)?->opsi_text}}">
                                                                            <span class="text-red-500 m-0" style="display: none">Maximum upload
                                                                                file size is 15 MB</span>
                                                                            @if ($itemTiga->is_multiple)
                                                                                <div class="flex gap-2 multiple-action">
                                                                                    <button type="button" class="btn-add" data-item-id="{{$itemTiga->id}}-{{strtolower(str_replace(' ', '_', $itemTiga->nama))}}">
                                                                                        <iconify-icon icon="fluent:add-16-filled" class="mt-2"></iconify-icon>
                                                                                    </button>
                                                                                    <button type="button" class="btn-minus hidden" data-item-id="{{$itemTiga->id}}-{{strtolower(str_replace(' ', '_', $itemTiga->nama))}}">
                                                                                        <iconify-icon icon="lucide:minus" class="mt-2"></iconify-icon>
                                                                                    </button>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
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
                                                                        class="form-input" placeholder="Masukkan informasi {{ $itemTiga->nama }}">{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemTiga->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemTiga->id)?->opsi_text }}</textarea>
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
                                                        {{-- @foreach ($dataOptionEmpat as $itemOptionEmpat)
                                                                @if ($itemOptionEmpat->option == '-')
                                                                <div class="form-group-1">
                                                                    <h5>{{ $itemTiga->nama }}</h5>
                                                                </div>
                                                                @endif
                                                                @endforeach --}}
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
                                                                                    value="{{ ($itemJawabanTiga->skor == null ? 'kosong' : $itemJawabanTiga->skor) . '-' . $itemJawabanTiga->id }}"
                                                                                    @if ($skema != 'Dagulir')
                                                                                    {{ temporary_select($itemTiga->id, $duTemp->id)?->id_jawaban == $itemJawabanTiga->id ?
                                                                                        'selected' : '' }}
                                                                                    @else
                                                                                    {{ temporary_select_dagulir($itemTiga->id, $duTemp->id)?->id_jawaban == $itemJawabanTiga->id ? 'selected' : '' }}
                                                                                    @endif
                                                                                        >
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
                                                                                    value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemEmpat->id)?->opsi_text }}">
                                                                                <div class="input-group-append">
                                                                                    <div class="input-group-text" id="addon_tenor_yang_diminta">
                                                                                        Bulan</div>
                                                                                </div>
                                                                            </div>
                                                                        @else
                                                                            <input type="text" maxlength="255" name="informasi[{{ $itemEmpat->id }}]"
                                                                                id="{{ $idLevelEmpat == 'nilai_asuransi_penjaminan_/_ht' ? '' : $idLevelEmpat }}"
                                                                                placeholder="Masukkan informasi" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemEmpat->id)?->opsi_text }}"
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
                                                                                    class="form-input only-number" value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id)?->opsi_text :temporary_dagulir($duTemp->id, $itemEmpat->id)?->opsi_text }}">
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
                                                                                    value="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemEmpat->id)?->opsi_text }}">
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
                                                                        <label for="">{{ $itemEmpat->nama }} joioj</label>
                                                                        @php
                                                                            $file_empat = $skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id) : temporary_dagulir($duTemp->id, $itemEmpat->id)
                                                                        @endphp
                                                                        @if ($file_empat)
                                                                            <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                                                data-title="{{ $itemEmpat->nama }}" data-filepath="{{asset('../upload/temp')}}/{{$skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id)?->id_jawaban : temporary_dagulir($duTemp->id, $itemEmpat->id)?->id_jawaban}}/{{$skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemEmpat->id)?->opsi_text}}" data-extension="{{ explode('.', $skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemEmpat->id)?->opsi_text)[1] }}">Preview</a>
                                                                        @endif
                                                                        {{-- <input type="hidden" name="opsi_jawaban[]" value="{{ $itemEmpat->opsi_jawaban }}" id=""> --}}
                                                                        <input type="hidden" name="id_item_file[{{ $itemEmpat->id }}]"
                                                                            value="{{ $itemEmpat->id }}" id="">
                                                                        <input type="file" id="{{ $idLevelEmpat }}"
                                                                            name="upload_file[{{ $itemEmpat->id }}]" data-id="{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id)?->id : temporary_dagulir($duTemp->id, $itemEmpat->id)?->id }}"
                                                                            placeholder="Masukkan informasi {{ $itemEmpat->nama }}"
                                                                            class="form-input limit-size" value="{{$skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemEmpat->id)?->opsi_text}}">
                                                                        <span class="text-red-500 m-0" style="display: none">Maximum upload file
                                                                            size is 5 MB</span>
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
                                                                            class="form-input" placeholder="Masukkan informasi {{ $itemEmpat->nama }}">{{ $skema != 'Dagulir' ? temporary($duTemp->id, $itemEmpat->id)?->opsi_text : temporary_dagulir($duTemp->id, $itemEmpat->id)?->opsi_text }}</textarea>
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
                                                                                    value="{{ ($itemJawabanEmpat->skor == null ? 'kosong' : $itemJawabanEmpat->skor) . '-' . $itemJawabanEmpat->id }}"
                                                                                    @if ($skema != 'Dagulir')
                                                                                    {{ temporary_select($itemEmpat->id, $duTemp->id)?->id_jawaban == $itemJawabanEmpat->id ?
                                                                                        'selected' : '' }}
                                                                                    @else
                                                                                    {{ temporary_select_dagulir($itemEmpat->id, $duTemp->id)?->id_jawaban == $itemJawabanEmpat->id ?
                                                                                        'selected' : '' }}
                                                                                    @endif
                                                                                   >
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
                                            cols="30" rows="4" placeholder="Pendapat Per Aspek">{{ $skema != 'Dagulir' ? temporary_usulan($value->id, $duTemp->id)?->usulan : temporary_usulan_dagulir($value->id, $duTemp->id)?->usulan }}</textarea>
                                        @error('pendapat_per_aspek')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="flex justify-between">
                                        <button type="button"
                                          class="px-5 py-2 border rounded bg-white text-gray-500 btnKembali"
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
                                            placeholder="Pendapat dan Usulan" id="" required></textarea>
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <a href="{{route('dagulir.pengajuan.index')}}">
                                        <button class="px-5 py-2 border rounded bg-white text-gray-500 btnKembali">
                                            Kembali
                                        </button>
                                    </a>
                                    <div>
                                        <button class="px-5 py-2 border rounded bg-theme-secondary text-white">
                                            Sebelumnya
                                        </button>
                                        <button class="px-5 py-2 border rounded bg-theme-primary text-white btn-simpan-data" type="submit">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#jangka_waktu').on('change', function() {
        limitJangkaWaktu()
    })
    $('#jumlah_kredit').on('change', function() {
        limitJangkaWaktu()
    })

    function limitJangkaWaktu() {
        var nominal = $('#jumlah_kredit').val()
        nominal = nominal != '' ? nominal.replaceAll('.','') : 0
        var limit = 50000000
        if (parseInt(nominal) > limit) {
            var jangka_waktu = $('#jangka_waktu').val()
            if (jangka_waktu != '') {
                jangka_waktu = parseInt(jangka_waktu)
                if (jangka_waktu <= 36) {
                    $('.jangka_waktu_error').removeClass('hidden')
                    $('.jangka_waktu_error').html('Jangka waktu harus lebih dari 36 bulan.')
                }
                else {
                    $('.jangka_waktu_error').addClass('hidden')
                    $('.jangka_waktu_error').html('')
                }
            }
        }
        else {
            $('.jangka_waktu_error').addClass('hidden')
            $('.jangka_waktu_error').html('')
        }
    }

    cekStatusNikah()
</script>
<script>
    //var isPincetar = "{{Request::url()}}".includes('pincetar');
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
            <input type="file" name="upload_file[{{ $itemKTPIs->id }}]" id="Foto_KTP_Istri" data-id="{{ temporary($duTemp->id, $itemKTPIs->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPIs->nama }}" class="form-input limit-size">
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
            <input type="file" name="upload_file[{{ $itemKTPSu->id }}]" id="Foto_KTP_Suami" data-id="{{ temporary($duTemp->id, $itemKTPSu->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPSu->nama }}" class="form-input limit-size">
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
            <input type="file" name="upload_file[{{ $itemKTPNas->id }}]" data-id="{{ temporary($duTemp->id, $itemKTPNas->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPNas->nama }}" class="form-input limit-size">
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
        $("#foto-ktp-istri").removeClass('form-group');
        $("#foto-ktp-suami").removeClass('form-group');
        $("#foto-ktp-nasabah").removeClass('form-group');

        if (value == "menikah") {
            $("#foto-ktp-istri").addClass('form-group')
            $("#foto-ktp-suami").addClass('form-group')
            $("#foto-ktp-istri").append(`
                <label for="">{{ $itemKTPIs->nama }}</label>
                <input type="hidden" name="id_item_file[{{ $itemKTPIs->id }}]" value="{{ $itemKTPIs->id }}" id="">
                <input type="file" name="upload_file[{{ $itemKTPIs->id }}]" data-id="" placeholder="Masukkan informasi {{ $itemKTPIs->nama }}" class="form-input limit-size" id="foto_ktp_istri">
                <span class="text-red-500 m-0" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                    <div class="invalid-feedback">
                        {{ $errors->first('dataLevelDua.' . $key) }}
                    </div>
                @endif
            `)
            $("#foto-ktp-suami").append(`
                    <label for="">{{ $itemKTPSu->nama }}</label>
                    <input type="hidden" name="id_item_file[{{ $itemKTPSu->id }}]" value="{{ $itemKTPSu->id }}" id="">
                    <input type="file" name="upload_file[{{ $itemKTPSu->id }}]" data-id="" placeholder="Masukkan informasi {{ $itemKTPSu->nama }}" class="form-input limit-size" id="foto_ktp_suami">
                    <span class="text-red-500 m-0" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                    @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                        <div class="invalid-feedback">
                            {{ $errors->first('dataLevelDua.' . $key) }}
                        </div>
                    @endif
            `);
        } else {
            $("#foto-ktp-nasabah").addClass('form-group-1')
            $("#foto-ktp-nasabah").append(`
                @isset($itemKTPNas)
                <label for="">{{ $itemKTPNas->nama }}</label>
                <input type="hidden" name="id_item_file[{{ $itemKTPNas->id }}]" value="{{ $itemKTPNas->id }}" id="">
                <input type="file" name="upload_file[{{ $itemKTPNas->id }}]" data-id="" placeholder="Masukkan informasi {{ $itemKTPNas->nama }}" class="form-input limit-size" id="foto_ktp_nasabah">
                <span class="text-red-500 m-0" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                    <div class="invalid-feedback">
                        {{ $errors->first('dataLevelDua.' . $key) }}
                    </div>
                @endif
                @endisset
            `)
        }
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
    @if ($skema != null)

        $('#kabupaten').change(function() {
            var kabID = $(this).val();
            if (kabID) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('getKecamatan') }}?kabID=" + kabID,
                    dataType: 'JSON',
                    success: function(res) {
                        console.log('kecamatan');
                        console.log(res);
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
    @endif

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
                        <input type="text" name="jawaban_sub_column[]" placeholder="Masukkan informasi tambahan" class="form-input">
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
                        <select name="dataLevelEmpat[]" id="itemByKategoriJaminanUtama" class="form-input cek-sub-column"
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
                            <div class="form-group aspek_jaminan_kategori_jaminan_utama">
                                <label>${valItem.nama}</label>
                                <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                                <input type="hidden" name="opsi_jawaban[]"
                                    value="${valItem.opsi_jawaban}" id="" class="input">
                                <input type="text" name="informasi[]" placeholder="Masukkan informasi"
                                    class="form-input input">
                            </div>
                        `);
                        } else {
                            if (valItem.nama == 'Foto') {
                                $('#bukti_pemilikan_jaminan_utama').append(`
                                <div class="form-group aspek_jaminan_kategori_jaminan_utama">
                                    <label>${valItem.nama}</label>
                                    <input type="hidden" name="id_item_file[${valItem.id}]" value="${valItem.id}" id="" class="input">
                                    <input type="file" name="upload_file[${valItem.id}]" data-id="" class="form-input limit-size">
                                    <span class="text-red-500 m-0" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                                </div>`);
                            } else {
                                $('#bukti_pemilikan_jaminan_utama').append(`
                                <div class="form-group aspek_jaminan_kategori_jaminan_utama">
                                    <label>${isCheck} ${valItem.nama}</label>
                                    <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                    <input type="hidden" name="opsi_jawaban[]"
                                        value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                    <input type="text" name="informasi[]" placeholder="Masukkan informasi ${valItem.nama}"
                                        class="form-input input" ${isDisabled}>
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
                                    class="form-input input">
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
                                    class="form-input input">
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
                        <select name="dataLevelEmpat[${response.item.id}]" id="itemByKategori" class="form-input cek-sub-column"
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
                                <div class="form-group input-box aspek_jaminan_kategori">
                                    <label>${valItem.nama}</label>
                                    <input type="hidden" name="id_level[${valItem.id}]" value="${valItem.id}" id="" class="input">
                                    <input type="hidden" name="opsi_jawaban[${valItem.id}]"
                                        value="${valItem.opsi_jawaban}" id="" class="input">
                                    <input type="text" maxlength="255" id="atas_nama" name="informasi[${valItem.id}]" placeholder="Masukkan informasi"
                                        class="form-input input" value="${response.dataJawaban[i]}">
                                </div>
                            `);
                        } else {
                            var name_lowercase = valItem.nama.toLowerCase();
                            name_lowercase = name_lowercase.replaceAll(' ', '_')
                            if (valItem.nama == 'Foto') {
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                @forelse (temporary_dagulir($duTemp->id, 148, true) as $tempData)
                                <div class="form-group input-box file-wrapper item-${valItem.id}">
                                    <label for="">${valItem.nama}</label>
                                    @if($tempData)
                                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                            data-title="${valItem.nama}" data-filepath="{{asset('../upload/temp')}}/{{$tempData->id_jawaban}}/{{$tempData->opsi_text}}" data-extension="{{ explode('.', $tempData->opsi_text)[1] }}">Preview</a>
                                    @endif
                                    <div class="input-box mb-4">
                                        <div class="flex gap-4">
                                            <input type="hidden" name="id_item_file[${valItem.id}][]" value="${valItem.id}" id="">
                                            <input type="file" name="upload_file[${valItem.id}][]" data-id="{{ $tempData->id }}"
                                                placeholder="Masukkan informasi ${valItem.nama}"
                                                class="form-input limit-size" id="${valItem.nama.toString().replaceAll(" ", "_").toLowerCase()}"
                                                value="{{$tempData->opsi_text}}">
                                                <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                                                <div class="flex gap-2 multiple-action">
                                                    <button type="button" class="btn-add" data-item-id="${valItem.id}-${name_lowercase}">
                                                        <iconify-icon icon="fluent:add-16-filled" class="mt-2"></iconify-icon>
                                                    </button>
                                                    <button type="button" class="btn-minus hidden" data-item-id="${valItem.id}-${name_lowercase}">
                                                        <iconify-icon icon="lucide:minus" class="mt-2"></iconify-icon>
                                                    </button>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="input-box form-group file-wrapper item-${valItem.id}">
                                    <label for="">${valItem.nama}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                    <div class="input-box mb-4">
                                        <div class="flex gap-4">
                                            <input type="hidden" name="id_item_file[${valItem.id}][]" value="${valItem.id}" id="">
                                            <input type="file" name="upload_file[${valItem.id}][]" data-id=""
                                                placeholder="Masukkan informasi ${valItem.nama}"
                                                class="form-input limit-size" id="${valItem.nama.toString().replaceAll(" ", "_").toLowerCase()}">
                                                <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                                                <div class="flex gap-2 multiple-action">
                                                    <button type="button" class="btn-add" data-item-id="${valItem.id}-${name_lowercase}">
                                                        <iconify-icon icon="fluent:add-16-filled" class="mt-2"></iconify-icon>
                                                    </button>
                                                    <button type="button" class="btn-minus hidden" data-item-id="${valItem.id}-${name_lowercase}">
                                                        <iconify-icon icon="lucide:minus" class="mt-2"></iconify-icon>
                                                    </button>
                                                </div>
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
                                }
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group input-box aspek_jaminan_kategori">
                                        <label>${isCheck} ${valItem.nama}</label>
                                        <input type="hidden" name="id_level[${valItem.id}]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                        <input type="hidden" name="opsi_jawaban[${valItem.id}]"
                                            value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                        <input type="text" maxlength="255" id="${valItem.nama.toString().replaceAll(" ", "_")}" name="informasi[${valItem.id}]" placeholder="Masukkan informasi"
                                            class="form-input input" ${isDisabled} value="${response.dataJawaban[i]}">
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
                        <div class="input-box">
                            <label for="">${response.item.nama}</label>
                            <select name="dataLevelEmpat[${response.item.id}]" id="itemByKategori" class="form-input cek-sub-column"
                                data-id_item="${response.item.id}">
                                <option value=""> --Pilih Opsi -- </option>
                                </select>

                            <div id="item${response.item.id}">

                            </div>
                        </div
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
            $('#space_nib').show();
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
            $('#space_nib').hide();
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
            $('#space_nib').hide();
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
            $('#space_nib').show();
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

            $('#docSKU').hide();

            $('#surat_keterangan_usaha').hide();
            $('#surat_keterangan_usaha_id').attr('disabled', true);
            $('#surat_keterangan_usaha_text').attr('disabled', true);
            $('#surat_keterangan_usaha_text').val('');
            $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);

            $('#npwp').hide();
            $('#npwp_id').attr('disabled', true);
            $('#npwp_text').attr('disabled', true);
            $('#npwp_opsi_jawaban').attr('disabled', true);

            $('#docNPWP').hide();
            $('#docNPWP_id').attr('disabled', true);
            $('#docNPWP_text').attr('disabled', true);
            $('#docNPWP_text').val('');
            $('#docNPWP_upload_file').attr('disabled', true);
        }
    });
    // end milih ijin usaha

    // Cek Npwp
    $('#isNpwp').change(function() {
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

    function formatNpwp(value) {
        if (typeof value === 'string') {
            return value.replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/, '$1.$2.$3.$4-$5.$6');
        }
    }
    // NPWP format
    $(document).on('keyup', '#npwp_text', function() {
        var input = $(this).val()
        $(this).val(formatNpwp(input))
    })

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
        let kreditYangDiminta = parseInt($('#jumlah_kredit').val().split('.').join(''));

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
    // Limit 2 MB
    $('.limit-size-2').on('change', function() {
        var size = (this.files[0].size / 1024 / 1024).toFixed(2)
        if (size > 2) {
            $(this).parent().parent().find('.error-limit').css({
                "display": "block"
            });
            this.value = ''
        } else {
            $(this).parent().parent().find('.error-limit').css({
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

    var slik = document.getElementById("file_slik");
    var selectedFile;

    if (slik) {
        slik.addEventListener('change', updateImageDisplaySlik);
    }

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

    var docKebKre = document.getElementById("perhitungan_kebutuhan_kredit");
    var selectedFile;

    docKebKre.addEventListener('change', updateImageDisplaydocKebKre);

    function updateImageDisplaydocKebKre() {
        if (docKebKre.files.length == 0) {
            docKebKre.files = selectedFile;
        } else {
            selectedFile = docKebKre.files;
        }

    }

    var docKebNet = document.getElementById("perhitungan_net_income");
    var selectedFile;

    docKebNet.addEventListener('change', updateImageDisplaydocKebNet);

    function updateImageDisplaydocKebNet() {
        if (docKebNet.files.length == 0) {
            docKebNet.files = selectedFile;
        } else {
            selectedFile = docKebNet.files;
        }

    }

    var docKebInstll = document.getElementById("perhitungan_installment");
    var selectedFile;

    docKebInstll.addEventListener('change', updateImageDisplaydocKebInstll);

    function updateImageDisplaydocKebInstll() {
        if (docKebInstll.files.length == 0) {
            docKebInstll.files = selectedFile;
        } else {
            selectedFile = docKebInstll.files;
        }

    }
</script>
<script>
    // Start Validation
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
    // End Validation

    @if ($skema == null)
        $('#kabupaten').change(function() {
            var kabID = $(this).val();
            if (kabID) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('') }}/getkecamatan?kabID=" + kabID,
                    dataType: 'JSON',
                    success: function(res) {
                        if (res) {
                            $("#kecamatan").empty();
                            $("#kecamatan").append('<option>---Pilih Kecamatan---</option>');
                            $.each(res, function(nama, kode) {
                                $('#kecamatan').append(`
                                    <option value="${kode}" ${kode == {{ $duTemp?->kec_ktp ?? 'null' }} ? 'selected' : '' }>${nama}</option>
                                `);
                            });

                            $('#kecamatan').trigger('change');
                        } else {
                            $("#kecamatan").empty();
                        }
                    }
                });
            } else {
                $("#kecamatan").empty();
            }
        });
        $('#kabupaten_domisili').change(function() {
            var kabID = $(this).val();
            if (kabID) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('') }}/getkecamatan?kabID=" + kabID,
                    dataType: 'JSON',
                    success: function(res) {
                        if (res) {
                            $("#kecamatan_domisili").empty();
                            $("#kecamatan_domisili").append('<option>---Pilih Kecamatan---</option>');
                            $.each(res, function(nama, kode) {
                                $('#kecamatan_domisili').append(`
                                    <option value="${kode}" ${kode == {{ $duTemp?->kec_dom ?? null }} ? 'selected' : ''}>${nama}</option>
                                `);
                            });

                            $('#kecamatan_domisili').trigger('change');
                        } else {
                            $("#kecamatan_domisili").empty();
                        }
                    }
                });
            } else {
                $("#kecamatan_domisili").empty();
            }
        });
        $('#kabupaten_usaha').change(function() {
            var kabID = $(this).val();
            if (kabID) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('') }}/getkecamatan?kabID=" + kabID,
                    dataType: 'JSON',
                    success: function(res) {
                        if (res) {
                            $("#kecamatan_usaha").empty();
                            $("#kecamatan_usaha").append('<option>---Pilih Kecamatan---</option>');
                            $.each(res, function(nama, kode) {
                                $('#kecamatan_usaha').append(`
                                    <option value="${kode}" ${kode == {{ $duTemp?->kec_usaha ?? null }} ? 'selected' : ''}>${nama}</option>
                                `);
                            });

                            $('#kecamatan_usaha').trigger('change');
                        } else {
                            $("#kecamatan_usaha").empty();
                        }
                    }
                });
            } else {
                $("#kecamatan_usaha").empty();
            }
        });

        $('#status_nasabah').on('change', function(e){
            var status = $(this).val();
            if (status == 2) {
                $('#label-ktp-nasabah').empty();
                $('#label-ktp-nasabah').html('Foto KTP Nasabah');
                $('#nik_pasangan').removeClass('hidden');
                $('#ktp-pasangan').removeClass('hidden');
            } else {
                $('#label-ktp-nasabah').empty();
                $('#label-ktp-nasabah').html('Foto KTP Nasabah');
                $('#nik_pasangan').addClass('hidden');
                $('#ktp-pasangan').addClass('hidden');
            }
        })

        $('#tipe').on('change',function(e) {
            var tipe = $(this).val();
            if (tipe == '2' || tipe == "0" ) {
                $('#tempat_berdiri').addClass('hidden');
            }else{
                $('#tempat_berdiri').removeClass('hidden');
                //badan usaha
                if (tipe == '3') {
                    $('#label_pj').html('Nama penanggung jawab');
                    $('#input_pj').attr('placeholder', 'Masukkan Nama Penanggung Jawab');
                }
                // perorangan
                else if (tipe == '4') {
                    $('#label_pj').html('Nama ketua');
                    $('#input_pj').attr('placeholder', 'Masukkan Nama Ketua');
                }
            }
        })
    @endif

    function validatePhoneNumber(input) {
        var phoneNumber = input.value.replace(/\D/g, '');

        if (phoneNumber.length > 15) {
            phoneNumber = phoneNumber.substring(0, 15);
        }

        input.value = phoneNumber;
    }

    function validateNIK(input) {
        var nikNumber = input.value.replace(/\D/g, '');

        if (nikNumber.length > 16) {
            nikNumber = nikNumber.substring(0, 16);
        }

        input.value = nikNumber;
    }

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

    $( document ).ready(function() {
        countFormPercentage()
        $(".rupiah").trigger('keyup')
        $("#ijin_usaha").trigger('change')
        $("#kabupaten_domisili").trigger('change')
        $("#kabupaten_usaha").trigger('change')
    });

    function countFormPercentage() {
        $.each($('.tab-wrapper .btn-tab'), function(i, obj) {
            var tabId = $(this).data('tab')
            if (tabId) {
                var percentage = formPercentage(`${tabId}-tab`)
                $(this).find('.percentage').html(`${percentage}%`)
            }
        })
    }

    // tab
    $(".tab-wrapper .btn-tab").click(function(e) {
        e.preventDefault();
        var tabId = $(this).data("tab");
        countFormPercentage()

        $(".is-tab-content").removeClass("active");
        $(".tab-wrapper .btn-tab").removeClass(
            "active-tab"
        );
        $(".tab-wrapper .btn-tab").removeClass("active-tab");
        $(".tab-wrapper .btn-tab").removeClass("active-tab");
        $(".tab-wrapper .btn-tab").addClass("disable-tab");

        $(this).addClass("active-tab");

        if (tabId) {
            $(this).removeClass("disable-tab");
            $(this).removeClass("disable-tab");
        }

        $("#" + tabId + "-tab").addClass("active");
    });

    $(".btnKembali").on("click", function(){
        const $activeContent = $(".is-tab-content.active");
        const $nextContent = $activeContent.next();
        const tabId = $activeContent.attr("id")
        const dataTab = tabId.replaceAll('-tab', '')
        if(tabId == 'dagulir-tab'){
            saveDataUmum()
        } else{
            saveDataTemporary(tabId)
        }
    })

    $(".next-tab").on("click", function(e) {
        const $activeContent = $(".is-tab-content.active");
        const $nextContent = $activeContent.next();
        const tabId = $activeContent.attr("id")
        const dataTab = tabId.replaceAll('-tab', '')
        if(tabId == 'dagulir-tab'){
            saveDataUmum()
        } else{
            saveDatatemporary_dagulir(tabId)
        }
        // Set percentage
        var percentage = formPercentage(tabId)
        $('.tab-wrapper').find(`[data-tab=${dataTab}]`).find('.percentage').html(`${percentage}%`)
        // Remove class active current nav tab
        $('.tab-wrapper').find(`[data-tab=${dataTab}]`).removeClass('active-tab')

        if ($nextContent.length) {
            const dataNavTab = $nextContent.attr("id") ? $nextContent.attr("id").replaceAll('-tab', '') : null
            if (dataNavTab)
                $('.tab-wrapper').find(`[data-tab=${dataNavTab}]`).addClass('active-tab')

            $activeContent.removeClass("active");
            $nextContent.addClass("active");
        }else{
            $(".next-tab").addClass('hidden');
            $('.btn-simpan').removeClass('hidden')
        }
    });

    $(".prev-tab").on("click", function() {
        const $activeContent = $(".is-tab-content.active");
        const $prevContent = $activeContent.prev();
        const tabId = $activeContent.attr("id")
        var percentage = formPercentage(tabId)
        const dataTab = tabId.replaceAll('-tab', '')
        // Set percentage
        var percentage = formPercentage(tabId)
        $('.tab-wrapper').find(`[data-tab=${dataTab}]`).find('.percentage').html(`${percentage}%`)
        // Remove class active current nav tab
        $('.tab-wrapper').find(`[data-tab=${dataTab}]`).removeClass('active-tab')

        if ($prevContent.length) {
            const dataNavTab = $prevContent.attr("id") ? $prevContent.attr("id").replaceAll('-tab', '') : null
            if (dataNavTab)
                $('.tab-wrapper').find(`[data-tab=${dataNavTab}]`).addClass('active-tab')
            $activeContent.removeClass("active");
            $prevContent.addClass("active");
            $(".next-tab").removeClass('hidden');
            $('.btn-simpan').addClass('hidden')
        }
    });

    function formPercentage(tabId) {
        var form = `#${tabId}`;
        // var form = `#aspek-keuangan-tab`;
        var inputFile = $(form + " input[type=file]")
        var inputText = $(form + " input[type=text]")
        var inputNumber = $(form + " input[type=number]")
        var inputDate = $(form + " input[type=date]")
        var InputEmail = $(form + " input[type=email]")
        var inputHidden = $(form + " input[type=hidden]")
        var select = $(form + " select")
        var textarea = $(form + " textarea")
        var totalInput = 0;
        var totalInputChecked = 0;
        var totalInputNull = 0;
        var totalInputFilled = 0;
        var totalInputHidden = 0;
        var totalInputReadOnly = 0;
        var percent = 0;

        $.each(inputText, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden'))
                totalInput++
            var isNull = (v.value == '' || $.trim(v.value) == '' || $.trim(v.value) == '')
            if ((v.value == '' && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    nullValue.push($(this).attr('id').toString().replaceAll("_", " "))
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).is(":checked") && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            } else if (!$(this).is(':checked')) {
                totalInputChecked++;
            }
        })

        $.each(InputEmail, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden'))
                totalInput++
            var isNull = (v.value == '' || $.trim(v.value) == '' || $.trim(v.value) == '')
            if ((v.value == '' && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    nullValue.push($(this).attr('id').toString().replaceAll("_", " "))
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).is(":checked") && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            } else if (!$(this).is(':checked')) {
                totalInputChecked++;
            }
        })

        $.each(inputHidden, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            if ((!$(this).prop('disabled') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputHidden++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        $.each(inputFile, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden'))
                totalInput++
            // var isNull = (v.value == '' || v.value == '0' || $.trim(v.value) == '')
            var isNull = (v.value == '' || $.trim(v.value) == '')
            if ((v.value == '' && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    nullValue.push($(this).attr('id').toString().replaceAll("_", " "))
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        $.each(inputNumber, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden'))
                totalInput++
            var isNull = (v.value == '' || v.value == '0' || $.trim(v.value) == '') && !$(this).prop('readonly') && !$(this).prop('hidden')
            if ((v.value == '' && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    nullValue.push($(this).attr('id').toString().replaceAll("_", " "))
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        $.each(inputDate, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden'))
                totalInput++
            var isNull = (v.value == '' || v.value == '0' || $.trim(v.value) == '')
            if ((v.value == '' && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    nullValue.push($(this).attr('id').toString().replaceAll("_", " "))
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        $.each(select, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden'))
                totalInput++
            var isNull = (v.value == '' || v.value == '0' || $.trim(v.value) == '')
            if ((isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    var val = $(this).attr('id').toString()
                    if (val != "persentase_kebutuhan_kredit_opsi" && val != "ratio_tenor_asuransi_opsi" && val != "ratio_coverage_opsi" && val != "repayment_capacity_opsi") {
                        //console.log(val)
                        nullValue.push(val.replaceAll("_", " "))
                    }
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputFilled++;

                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        $.each(textarea, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden'))
                totalInput++
            var isNull = (v.value == '' || v.value == '0' || $.trim(v.value) == '')
            if ((v.value == '' && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    let val =  $(this).attr('id').toString().replaceAll("_", " ");
                    if (val != 'komentar staff') {
                        nullValue.push($(this).attr('id').toString().replaceAll("_", " "))

                    }
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        var totalReadHidden = (totalInputHidden + totalInputReadOnly);
        var total = totalInput + totalInputChecked;
        percent = (totalInputFilled / (totalInput - totalInputReadOnly)) * 100

        return parseInt(percent)
    }

    $(".toggle-side").click(function(e) {
        $('.sidenav').toggleClass('hidden')
    })
    $('.owl-carousel').owlCarousel({
        margin: 10,
        autoWidth: true,
        dots: false,
        responsive: {
            0: {
                items: 3
            },
            600: {
                items: 5
            },
            1000: {
                items: 10
            }
        }
    })

    $("#usaha").on("change", function() {
        if ($(this).val() == "tanah") {
            $("#tanah").removeClass("hidden");
            $("#kendaraan").addClass("hidden");
            $("#tanah-dan-bangunan").addClass("hidden");
            $("#form-tanah").removeClass("hidden");
            $("#form-kendaraan").addClass("hidden");
        } else if ($(this).val() == "kendaraan") {
            $("#tanah").addClass("hidden");
            $("#kendaraan").removeClass("hidden");
            $("#tanah-dan-bangunan").addClass("hidden");
            $("#form-tanah").addClass("hidden");
            $("#form-kendaraan").removeClass("hidden");
        } else if ($(this).val() == "tanah-dan-bangunan") {
            $("#tanah").addClass("hidden");
            $("#kendaraan").addClass("hidden");
            $("#tanah-dan-bangunan").removeClass("hidden");
            $("#form-tanah").removeClass("hidden");
            $("#form-kendaraan").addClass("hidden");
        } else {
            $("#form-tanah").addClass("hidden");
            $("#form-kendaraan").addClass("hidden");
            $("#tanah").addClass("hidden");
            $("#tanah-dan-bangunan").addClass("hidden");
            $("#kendaraan").addClass("hidden");
        }
    });
    $("#is-npwp").on("change", function() {
        if ($(this).is(":checked")) {
            $("#npwp").removeClass("hidden");
        } else {
            $("#npwp").addClass("hidden");
        }
    });
    $("#shm-check").on("change", function() {
        if ($(this).is(":checked")) {
            $("#no-shm-input").removeClass("disabled");
            $("#no-shm-input").prop("disabled", false);
        } else {
            $("#no-shm-input").addClass("disabled");
            $("#no-shm-input").prop("disabled", true);
        }
    });
    $("#shgb-check").on("change", function() {
        if ($(this).is(":checked")) {
            $("#no-shgb-input").removeClass("disabled");
            $("#no-shgb-input").prop("disabled", false);
        } else {
            $("#no-shgb-input").addClass("disabled");
            $("#no-shgb-input").prop("disabled", true);
        }
    });
    // petak
    $("#petak-check").on("change", function() {
        if ($(this).is(":checked")) {
            $("#no-petak-input").removeClass("disabled");
            $("#no-petak-input").prop("disabled", false);
        } else {
            $("#no-petak-input").addClass("disabled");
            $("#no-petak-input").prop("disabled", true);
        }
    });
    // ijin usaha
    $("#ijin-usaha").on("change", function() {
        console.log($(this).val());
        if ($(this).val() == "nib") {
            $("#nib").removeClass("hidden");
            $("#label-nib").removeClass("hidden");
            $("#dokumen-nib").removeClass("hidden");
            $("#label-dokumen-nib").removeClass("hidden");
            $("#npwp").removeClass("hidden");
            $("#label-npwp").removeClass("hidden");
            $("#dokumen-npwp").removeClass("hidden");
            $("#label-dokumen-npwp").removeClass("hidden");
            $("#have-npwp").addClass("hidden");
            $("#surat-keterangan-usaha").addClass("hidden");
            $("#label-surat-keterangan-usaha").addClass("hidden");
            $("#dokumen-surat-keterangan-usaha").addClass("hidden");
            $("#label-dokumen-surat-keterangan-usaha").addClass("hidden");
        } else if ($(this).val() == "sku") {
            $("#surat-keterangan-usaha").removeClass("hidden");
            $("#label-surat-keterangan-usaha").removeClass("hidden");
            $("#label-dokumen-surat-keterangan-usaha").removeClass("hidden");
            $("#dokumen-surat-keterangan-usaha").removeClass("hidden");
            $("#have-npwp").removeClass("hidden");
            $("#npwp").addClass("hidden");
            $("#label-npwp").addClass("hidden");
            $("#label-dokumen-npwp").addClass("hidden");
            $("#dokumen-npwp").addClass("hidden");
            $("#label-nib").addClass("hidden");
            $("#nib").addClass("hidden");
            $("#label-dokumen-nib").addClass("hidden");
            $("#dokumen-nib").addClass("hidden");
        } else {
            $("#surat-keterangan-usaha").addClass("hidden");
            $("#label-surat-keterangan-usaha").addClass("hidden");
            $("#label-dokumen-surat-keterangan-usaha").addClass("hidden");
            $("#dokumen-surat-keterangan-usaha").addClass("hidden");
            $("#nib").addClass("hidden");
            $("#label-nib").addClass("hidden");
            $("#dokumen-nib").addClass("hidden");
            $("#label-dokumen-nib").addClass("hidden");
            $("#npwp").addClass("hidden");
            $("#label-npwp").addClass("hidden");
            $("#dokumen-npwp").addClass("hidden");
            $("#label-dokumen-npwp").addClass("hidden");
            $("#have-npwp").addClass("hidden");
            $("#sku").addClass("hidden");
            $("#label-sku").addClass("hidden");
            $("#dokumen-sku").addClass("hidden");
            $("#label-dokumen-sku").addClass("hidden");
        }
    });
    // npwp
    $("#is-npwp").on("change", function() {
        if ($(this).is(":checked")) {
            $("#npwp").removeClass("hidden");
            $("#label-npwp").removeClass("hidden");
            $("#dokumen-npwp").removeClass("hidden");
            $("#label-dokumen-npwp").removeClass("hidden");
        } else {
            $("#npwp").addClass("hidden");
            $("#label-npwp").addClass("hidden");
            $("#dokumen-npwp").addClass("hidden");
            $("#label-dokumen-npwp").addClass("hidden");
        }
    });
    var num = 1;
    $(document).on('click', '.btn-add', function() {
        console.log($(this));
        const item_id = $(this).data('item-id');
        var item_element = $(`.${item_id}`);
        var iteration = item_element.length;
        var input = $(this).closest('.input-box');
        var multiple = input.find('.multiple-action');
        var new_multiple = multiple.html().replaceAll('hidden', '');
        input = input.html().replaceAll(multiple.html(), new_multiple);
        var parent = $(this).closest('.input-box').parent();
        var num = parent.find('.input-box').length + 1;
        num = parseInt($(".figure").text());
        $(".figure").text(num+1);
        parent.append(`
            <div class="input-box mb-4">
                ${input}
            </div>
        `);
        num = 1;
    });

    $(document).on('click', '.btn-minus', function() {
        const item_id = $(this).data('item-id');
        var item_element = $(`#${item_id}`)
        var parent = $(this).closest('.input-box')
        parent.remove()
    })
    function formatNpwp(value) {
        if (typeof value === 'string') {
            return value.replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/, '$1.$2.$3.$4-$5.$6');
        }
    }
    // NPWP format
    $(document).on('keyup', '#npwp_text', function() {
        var input = $(this).val()
        $(this).val(formatNpwp(input))
    })
    $(".btn-simpan-data").on('click', function(e) {
        console.log(nullValue);
        if ($('#komentar_staff').val() == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "Field Pendapat dan usulan harus diisi"
            })
            e.preventDefault()
        }
        else {
            const ijinUsaha = $("#ijin_usaha").val();
            const value = 'aspek keuangan';
            dataValue = nullValue.filter(item => item !== value)
            if (dataValue.length > 0 ) {
                let message = "";
                $.each(dataValue, (i, v) => {
                    var item = v;
                    if (v == 'dataLevelDua')
                        item = 'slik';

                    if (v == 'itemByKategori')
                        item = 'jaminan tambahan';

                    if (v == 'itemByKategori'){
                        if($("#kategori_jaminan_tambahan").val() == "Tidak Memiliki Jaminan Tambahan"){
                            for(var j = 0; j < dataValue.length; j++){
                                while(dataValue[j] == v){
                                    dataValue.splice(j, 1)
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
                                for(var j = 0; j < dataValue.length; j++){
                                    while(dataValue[j] == v){
                                        dataValue.splice(j, 1)
                                    }
                                }
                            }
                        }
                    }

                    if (item.includes('text'))
                        item = item.replaceAll('text', '');

                    message += item != '' ? `<li class="text-left">Field `+item +` harus diisi.</li>` : ''
                })
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: '<ul>'+message+'</ul>'
                })
                e.preventDefault()
            } else {
                $("#preload-data").removeClass("hidden");
            }
        }
    })
</script>
<script src="{{ asset('') }}js/custom.js"></script>
@include('dagulir.partials.save-script')
@include('pengajuan-kredit.modal.perhitungan-modal-draft')
<script>
    var indexBtnSimpan = 0;
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
                                                console.log('hasil formula')
                                                console.log(formula)
                                                var result = calculateFormula(formula)
                                                console.log(result)
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
    function stringContainsValueFromArray(inputString, searchArray) {
            for (let i = 0; i < searchArray.length; i++) {
                if (inputString.includes(searchArray[i])) {
                return true; // Return true if a match is found
                }
            }
            return false; // Return false if no match is found
        }

    $('#btn-perhitungan').on('click', function() {
        $('#loading-simpan-perhitungan').hide();
        $('#perhitunganModalAfterLoading').show();
        // $("#perhitunganModalEdit").modal('show')
        $("#perhitunganModal").removeClass('hidden')
        calcForm();
     });

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

    var indexBtnSimpan = 0;
    $("#btnSimpanPerhitungan").on('click',function(e){
        console.log('test');
        indexBtnSimpan += 1;
        let data = {
            idCalonNasabah: $("idCalonNasabah").val()
        }
        $("#perhitunganModal input").each(function(){
            let input = $(this);

            data[input.attr("name")] = input.val();
            data['idCalonNasabah'] = $("#idCalonNasabah").val();
        });

        $('#peringatan-pengajuan').empty();
        $('#perhitungan_kredit_with_value_without_update').empty();
        // $('#loading-simpan-perhitungan').show();

        var selectElementBulan = $("#periode").find(":selected").text();
        var selectValueElementBulan = $("#periode").val();
        var selectElementTahun = $("#periode_tahun").find(":selected").text();
        var titlePeriode = ``;

        if (indexBtnSimpan == 1) {
            $('#perhitungan_kredit_with_value').append(`
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
                            $('.modal-layout').addClass('hidden');
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
                console.log('--- RES 1---');
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

                const resPeriode = await $.ajax({
                    url: '{{ route('get-periode-perhitungan-kredit-draft') }}?calon_nasabah_id=' + res1.request.idCalonNasabah,
                    type: "GET",
                    beforeSend: function() {
                        $('#loading-simpan-perhitungan').show();
                        $('#perhitunganModalAfterLoading').hide();
                    },
                });

                console.log(resPeriode);
                if (resPeriode.result.length === 0) {
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
                        url: '{{ route('pengajuan-kredit.update-data-periode-aspek-keuangan') }}?id' + resPeriode.result[0].id,
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
                    url: '{{ route('get-periode-perhitungan-kredit-draft') }}?calon_nasabah_id=' + res1.request.idCalonNasabah,
                    type: "GET",
                    beforeSend: function() {
                        $('#loading-simpan-perhitungan').show();
                        $('#perhitunganModalAfterLoading').hide();
                    },
                });

                var lev1Count = 0;
                for (const element of res2.result) {
                    lev1Count += 1;
                    // titlePeriode = `Periode : ${formatBulan(resPeriode2.result[0].bulan)} - ${resPeriode2.result[0].tahun}`;
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
                $('.modal-layout').addClass('hidden');
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
