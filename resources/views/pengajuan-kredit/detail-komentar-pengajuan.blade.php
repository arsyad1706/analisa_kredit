@include('components.new.modal.loading')
@extends('layouts.tailwind-template')
@section('content')
    <section class="p-5 overflow-y-auto mt-5">
        <div class="container mx-auto mb-5">
            <div class="head space-y-5 w-full font-poppins">
                <div class="heading flex-auto">
                    <p class="text-theme-primary font-semibold font-poppins text-xs">
                        Dagulir
                    </p>
                    <h2 class="font-bold tracking-tighter text-2xl text-theme-text">
                        Review
                    </h2>
                </div>
            </div>
        </div>
        @if (session('error'))
            <strong>Terjadi kesalahan!</strong> {{ session('error') }}
        @endif

        <div class="body-pages review-pincab">
            <div class="container mx-auto p-3 bg-white">
                <div class="accordion-section">
                    <div class="accordion-header rounded pl-3 border border-theme-primary/5 relative mb-4">
                        <div class="flex justify-between gap-3">
                        <div class="flex justify-start gap-3">
                            <button class="p-2 rounded-full bg-theme-primary w-10 h-10 text-white">
                                <h2 class="text-lg">1</h2>
                            </button>
                            <h3 class="font-bold text-lg tracking-tighter mt-[6px]">Pemroses Data</h3>
                        </div>
                            <div class="transform accordion-icon mr-2 mt-1">
                                <iconify-icon icon="uim:angle-down" class="text-3xl"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-content p-3">
                        @include('dagulir.pengajuan-kredit.review.log')
                    </div>
                </div>
                <div class="accordion-section">
                    <div class="accordion-header rounded pl-3 border border-theme-primary/5 relative mb-4">
                        <div class="flex justify-between gap-3">
                        <div class="flex justify-start gap-3">
                            <button class="p-2 rounded-full bg-theme-primary w-10 h-10 text-white">
                                <h2 class="text-lg">2</h2>
                            </button>
                            <h3 class="font-bold text-lg tracking-tighter mt-[6px]">Data Umum</h3>
                        </div>
                            <div class="transform accordion-icon mr-2 mt-1">
                                <iconify-icon icon="uim:angle-down" class="text-3xl"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-content p-3">
                        @include('dagulir.pengajuan-kredit.review.data-umum-analis')
                    </div>
                </div>
                @if ($dataUmum->skema_kredit == 'KKB')
                    <div class="accordion-section">
                        <div class="accordion-header rounded pl-3 border border-theme-primary/5 relative mb-4">
                            <div class="flex justify-between gap-3">
                            <div class="flex justify-start gap-3">
                                <button class="p-2 rounded-full bg-theme-primary w-10 h-10 text-white">
                                    <h2 class="text-lg">3</h2>
                                </button>
                                <h3 class="font-bold text-lg tracking-tighter mt-[6px]">Data PO</h3>
                            </div>
                                <div class="transform accordion-icon mr-2 mt-1">
                                    <iconify-icon icon="uim:angle-down" class="text-3xl"></iconify-icon>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-content p-3">
                            <div class="form-group-1 col-span-2">
                                <div>
                                    <div class="p-2 border-l-8 border-theme-primary bg-gray-100">
                                        <h2 class="font-semibold text-lg tracking-tighter text-theme-text">
                                            Jenis Kendaraan Roda 2 :
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group-2">
                                <div class="field-review">
                                    <div class="field-name">
                                        <label for="">Merk Kendaraan</label>
                                    </div>
                                    <div class="field-answer">
                                        <p>{{ $dataPO?->merk ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="field-review">
                                    <div class="field-name">
                                        <label for="">Tipe Kendaraan</label>
                                    </div>
                                    <div class="field-answer">
                                        <p>{{ $dataPO?->tipe ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="field-review">
                                    <div class="field-name">
                                        <label for="">Tahun</label>
                                    </div>
                                    <div class="field-answer">
                                        <p>{{ $dataPO?->tahun_kendaraan ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="field-review">
                                    <div class="field-name">
                                        <label for="">Warna</label>
                                    </div>
                                    <div class="field-answer">
                                        <p>{{ $dataPO?->warna ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group-1 col-span-2">
                                <div>
                                    <div class="p-2 border-l-8 border-theme-primary bg-gray-100">
                                        <h2 class="font-semibold text-lg tracking-tighter text-theme-text">
                                            Keterangan :
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group-2">
                                <div class="field-review">
                                    <div class="field-name">
                                        <label for="">Pemesanan</label>
                                    </div>
                                    <div class="field-answer">
                                        <p>{{ $pemesanan ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="field-review">
                                    <div class="field-name">
                                        <label for="">Sejumlah</label>
                                    </div>
                                    <div class="field-answer">
                                        <p>{{ $dataPO?->jumlah ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="field-review">
                                    <div class="field-name">
                                        <label for="">Harga</label>
                                    </div>
                                    <div class="field-answer">
                                        <p>{{ $dataPO->harga ? 'Rp '. number_format($dataPO->harga, 2, ',', '.') :'' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @php
                if ($dataUmum->skema_kredit == 'KKB') {
                    $no_aspek = 3;
                } else {
                    $no_aspek = 2;
                }
            @endphp
            @foreach ($dataAspek as $itemAspek)
                @php
                    $no_aspek++;
                    // check level 2
                    $dataLevelDua = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable', 'is_rupiah')
                        ->where('level', 2)
                        ->where('id_parent', $itemAspek->id)
                        ->get();
                    // check level 4
                    $dataLevelEmpat = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable', 'is_rupiah')
                        ->where('level', 4)
                        ->where('id_parent', $itemAspek->id)
                        ->get();
                    $pendapatUsulanStaf = \App\Models\PendapatPerAspek::select('*')
                        ->where('id_staf', '!=', null)
                        ->where('id_aspek', $itemAspek->id)
                        ->where('id_pengajuan', $dataUmum->id)
                        ->get();
                    $pendapatUsulanPenyelia = \App\Models\PendapatPerAspek::select('*')
                        ->where('id_penyelia', '!=', null)
                        ->where('id_aspek', $itemAspek->id)
                        ->where('id_pengajuan', $dataUmum->id)
                        ->get();
                    $userPBO = \App\Models\User::select('id')
                                                ->where('id_cabang', $dataUmum->id_cabang)
                                                ->where('role', 'PBO')
                                                ->first();
                    $userPBP = \App\Models\User::select('id')
                                                ->where('id_cabang', $dataUmum->id_cabang)
                                                ->where('role', 'PBP')
                                                ->first();

                    if ($dataUmum->id_pbo) {
                        $pendapatUsulanPBO = \App\Models\PendapatPerAspek::select('*')
                            ->where('id_pbo', '!=', null)
                            ->where('id_aspek', $itemAspek->id)
                            ->where('id_pengajuan', $dataUmum->id)
                            ->get();
                    }
                    if ($dataUmum->id_pbp) {
                        $pendapatUsulanPBP = \App\Models\PendapatPerAspek::select('*')
                            ->where('id_pbp', '!=', null)
                            ->where('id_aspek', $itemAspek->id)
                            ->where('id_pengajuan', $dataUmum->id)
                            ->get();
                    }
                @endphp
                <div class="accordion-section">
                    <div class="accordion-header rounded pl-3 border border-theme-primary/5 relative">
                        <div class="flex justify-between gap-3">
                        <div class="flex justify-start gap-3">
                            <button class="p-2 rounded-full bg-theme-primary w-10 h-10 text-white">
                                <h2 class="text-lg">{{$no_aspek}}</h2>
                            </button>
                            <h3 class="font-bold text-lg tracking-tighter mt-[6px]">{{ $itemAspek->nama }}</h3>
                        </div>
                            <div class="transform accordion-icon mr-2 mt-1">
                                <iconify-icon icon="uim:angle-down" class="text-3xl"></iconify-icon>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-content p-5">
                        <div class="space-y-2">
                            @foreach ($dataLevelDua as $item)
                                @if ($item->opsi_jawaban != 'option')
                                    @php
                                        $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama', 'item.status_skor', 'item.is_commentable', 'is_rupiah')
                                            ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                            ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                            ->where('jawaban_text.id_jawaban', $item->id)
                                            ->get();
                                    @endphp
                                    @if ($item->nama == 'Ijin Usaha' && $countIjin == 0)
                                        <div class="form-group-1">
                                            <div class="field-review">
                                                <div class="field-name">
                                                    <label for="">Ijin Usaha</label>
                                                </div>
                                                <div class="field-answer">
                                                   <p> Tidak ada legalitas usaha </p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @foreach ($dataDetailJawabanText as $itemTextDua)
                                            @php
                                                $getKomentar = \App\Models\DetailKomentarModel::select('detail_komentar.id', 'detail_komentar.id_komentar', 'detail_komentar.id_user', 'detail_komentar.id_item', 'detail_komentar.komentar')
                                                    ->where('detail_komentar.id_item', $itemTextDua->id_item)
                                                    ->get();
                                            @endphp
                                            @if ($item->nama == 'Ijin Usaha' && $itemTextDua->opsi_text == 'nib' || $item->nama == 'Ijin Usaha' && $itemTextDua->opsi_text == 'surat_keterangan_usaha')
                                            @else
                                                <div class="form-group-1">
                                                    <div class="field-review">
                                                        <div class="field-name">
                                                        <label for="">{{ $item->nama }}</label>
                                                        </div>

                                                        <div class="field-answer">
                                                            @if ($item->opsi_jawaban == 'file')
                                                                @php
                                                                    $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text);
                                                                @endphp
                                                                @if ($file_parts['extension'] == 'pdf')
                                                                    <iframe src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}" width="100%" height="700px"></iframe>
                                                                @else
                                                                    <img src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $item->id . '/' . $itemTextDua->opsi_text }}" alt="" width="700px">
                                                                @endif
                                                                @elseif ($item->opsi_jawaban == 'number' && $item->id != 143)
                                                                <p class="">
                                                                    @if ($item->is_rupiah == 1)
                                                                        Rp. {{ number_format((int) $itemTextDua->opsi_text, 0, ',', '.') }}
                                                                    @else
                                                                        {{ $itemTextDua->opsi_text }}
                                                                    @endif
                                                                </p>
                                                            @else
                                                                @if ($item->opsi_jawaban == "persen")
                                                                    <p>{{ round(floatval($itemTextDua->opsi_text),2) }}</p>
                                                                @elseif($item->is_rupiah == 1)
                                                                    <p>Rp. {{ number_format($itemTextDua->opsi_text, 0, '.', '.') }}</p>
                                                                @else
                                                                    <p>{{ $itemTextDua->opsi_text }}</p>

                                                                    {{-- @if (is_numeric($itemJawaban->option) && strlen($itemJawaban->option) > 3)
                                                                        <p class="">{{ $itemTextDua->is_rupiah ? 'Rp. ' . number_format($itemTextDua->opsi_text, 0, '.', '.') : $itemTextDua->opsi_text }}</p>
                                                                    @else
                                                                        <p class="">{{ $itemTextDua->is_rupiah ? 'Rp. ' . number_format((int) $itemTextDua->opsi_text, 0, '.', '.') : $itemTextDua->opsi_text }} {{$itemTextDua->opsi_jawaban == 'persen' ? '%' : ''}}</p>
                                                                    @endif --}}
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($itemTextDua->status_skor == 1)
                                                <div class="form-group-1">
                                                    <div class="field-review">
                                                        <div class="field-name">
                                                            <label for="">{{ $item->nama }}</label>
                                                        </div>
                                                    </div>
                                                </div>
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
                                    $dataLevelTiga = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'is_hide', 'is_rupiah')
                                        ->where('level', 3)
                                        ->where('id_parent', $item->id)
                                        ->get();
                                @endphp

                                @if ($item->id_parent == 10 && $item->nama != 'Hubungan Dengan Supplier')
                                    <div class="form-group-1 col-span-2">
                                        <div>
                                            <div class="w-full p-2 border-l-8 border-theme-primary bg-gray-100">
                                                <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                                                    {{ $item->nama }} :
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @foreach ($dataOption as $itemOption)
                                    @if ($itemOption->option == '-')
                                        @if (!$item->is_hide)
                                            @if ($item->nama != "Ijin Usaha")
                                                @if ($item->id == 24 || $item->id == 37 || $item->id == 45)
                                                @else
                                                    <div class="row col-span-2">
                                                        <div class="form-group-1">
                                                            <div class="form-group-1 col-span-2 pl-2">
                                                                <div>
                                                                    <div class="p-2 border-l-8 border-theme-primary bg-gray-100">
                                                                        <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                                                                            {{ $item->nama }} :
                                                                        </h2>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if ($item->nama == 'Ijin Usaha' && $countIjin == 0)
                                                            <div class="bg-blue-50 border-b border-gray-500 text-gray-700 px-4 py-3 flex items-center" role="alert">
                                                                <span class="text-sm font-semibold text-gray-400 mx-3">Jawaban : </span>
                                                                <h4 class="font-bold">Tidak ada legalitas usaha</h4>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                                @if (count($dataJawaban) != 0)
                                    @if ($item->nama == 'Persentase Kebutuhan Kredit Opsi' || $item->nama == 'Repayment Capacity Opsi')
                                        @if ($item->nama == "Persentase Kebutuhan Kredit Opsi")
                                            {{-- table Aspek Keuangan --}}
                                                @php
                                                    $getPeriode = \App\Models\PeriodeAspekKeuangan::join('perhitungan_kredit', 'periode_aspek_keuangan.perhitungan_kredit_id', '=', 'perhitungan_kredit.id')
                                                            ->where('perhitungan_kredit.pengajuan_id', $dataUmum->id)
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
                                                    <h5 class="font-bold text-lg">Periode : {{ bulan($getPeriode[0]->bulan) - $getPeriode[0]->tahun }}</h5>
                                                    <div class="" id="perhitungan_kredit_with_value_without_update">
                                                        @php
                                                            $lev1 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)->where('level', 1)->get();
                                                            function rupiah($angka){
                                                                $format_rupiah = number_format($angka, 2, ',', '.');
                                                                $format_rupiah = rtrim($format_rupiah, '0');
                                                                $format_rupiah = str_replace(',', '', $format_rupiah);
                                                                echo $format_rupiah;
                                                            }
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
                                                                                        ->where('perhitungan_kredit.pengajuan_id', $dataUmum->id)
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
                                                                                    ->where('perhitungan_kredit.pengajuan_id', $dataUmum->id)
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
                                                                                ->where('perhitungan_kredit.pengajuan_id', $dataUmum->id)
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
                                                                                    ->where('perhitungan_kredit.pengajuan_id', $dataUmum->id)
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
                                                            Perhitungan kredit masih belum ditambahkan.
                                                        </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            {{-- End Aspek Keuangan --}}
                                        @endif
                                    @else
                                        <div class="form-group-1">
                                            <div class="field-review">
                                                <div class="field-name">
                                                    <label for="">{{ $item->nama }}</label>
                                                </div>
                                                <div class="field-answer space-y-5">
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
                                                                @endphp
                                                                @if (is_numeric($itemJawaban->option) && strlen($itemJawaban->option) > 3)
                                                                    <p>{{ $itemJawaban->option }}</p>
                                                                    <input type="hidden" name="id[]" value="{{ $itemAspek->id }}">
                                                                @else
                                                                    <p>{{ $itemJawaban->option }}</p>
                                                                    <input type="hidden" name="id[]" value="{{ $itemAspek->id }}">
                                                                @endif
                                                                @foreach ($dataDetailJawabanskor as $key => $item)
                                                                    @if ($item->skor_penyelia != null && $item->skor_penyelia != '')
                                                                        <div class="field-review">
                                                                            <div class="field-name">
                                                                                <h6>Skor</h6>
                                                                            </div>
                                                                            <div class="field-answer space-y-5">
                                                                                <p>
                                                                                    <span class="field-skor">{{ $item->skor_penyelia }}</span>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="field-review">
                                                                            <div class="field-name">
                                                                                <h6>Komentar Penyelia</h6>
                                                                            </div>
                                                                            <div class="field-answer">
                                                                                <h6>
                                                                                    @if (count($getKomentarPenyelia) > 0)
                                                                                        {{ strlen($getKomentarPenyelia[$key]?->komentar) > 0 ? $getKomentarPenyelia[$key]?->komentar ?? '-' : '-' }}
                                                                                    @endif
                                                                                </h6>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($item->nama == 'Persentase Kebutuhan Kredit Opsi')

                                    @else
                                        {{--  skor  --}}
                                    @endif
                                @endif
                                <div class="form-group-1">
                                    @foreach ($dataLevelTiga as $key => $itemTiga)
                                        @if (!$itemTiga->is_hide)
                                            @if ($itemTiga->opsi_jawaban != 'option')
                                                @php
                                                    $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama', 'is_rupiah')
                                                        ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                                        ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                                        ->where('jawaban_text.id_jawaban', $itemTiga->id)
                                                        ->get();

                                                        $jumlahDataDetailJawabanText = $dataDetailJawabanText ? count($dataDetailJawabanText) : 0;
                                                @endphp
                                                @foreach ($dataDetailJawabanText as $itemTextTiga)
                                                    @if ($itemTextTiga->nama != 'Ratio Tenor Asuransi')
                                                        @if ($itemTextTiga->opsi_text == "Tanah" || $itemTextTiga->opsi_text == "Kendaraan Bermotor" || $itemTextTiga->opsi_text == "Tanah dan Bangunan")
                                                        @else
                                                            <div class="field-review">
                                                                <div class="field-name">
                                                                    @if ($itemTiga->opsi_jawaban == 'file')
                                                                        @if ($jumlahDataDetailJawabanText > 1)
                                                                            <h6 for="">{{ $itemTextTiga->nama }} {{$loop->iteration}}</h6>
                                                                        @else
                                                                            <h6 for="">{{ $itemTextTiga->nama }}</h6>
                                                                        @endif
                                                                    @else
                                                                            <h6 for="">{{ $itemTextTiga->nama }}</h6>
                                                                    @endif
                                                                </div>
                                                                <div class="field-answer">
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
                                                                    @elseif ($itemTextTiga->is_rupiah)
                                                                        <p>Rp. {{ number_format((int) $itemTextTiga->opsi_text, 0, ',', '.') }}</p>
                                                                    @else
                                                                        <p>{{ $itemTiga->opsi_jawaban == 'persen' ? $itemTextTiga->opsi_text : $itemTextTiga->opsi_text  }}{{ $itemTiga->opsi_jawaban == 'persen' ? '%' : '' }}</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <input type="hidden" class="form-input mb-3" placeholder="Masukkan komentar"
                                                            name="komentar_penyelia" value="{{ $itemTextTiga->nama }}" disabled>
                                                        <input type="hidden" class="form-input mb-3" placeholder="Masukkan komentar"
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
                                                // $dataLevelEmpat2 = \App\Models\JawabanTextModel::select('jawaban_text.*','item.*')
                                                //     ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                                //     ->where('item.level', 4)
                                                //     ->where('item.id_parent', $itemTiga->id)
                                                //     ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                                //     ->get();
                                                // check jawaban kelayakan
                                                $checkJawabanKelayakan = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor')
                                                    ->where('id_pengajuan', $dataUmum->id)
                                                    ->whereIn('id_jawaban', ['183', '184'])
                                                    ->first();
                                            @endphp
                                            @foreach ($dataOptionTiga as $itemOptionTiga)
                                                @if ($itemOptionTiga->option == '-')
                                                    @if ($itemTiga->id != 110)
                                                        <div class="col-span-2 pl-2 p-0">
                                                            <div>
                                                                <div class="p-2 border-l-8 border-theme-primary bg-gray-100">
                                                                    <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                                                                        {{$itemTiga->nama}} :
                                                                    </h2>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                                {{-- @if (!$itemTiga->is_hide)
                                                    @if ($itemOptionTiga->option == '-')
                                                        @if (isset($checkJawabanKelayakan))
                                                        @else
                                                            <div class="row">
                                                                <div class="form-group-1">
                                                                    <h5> {{ $itemTiga->nama }}</h5>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif --}}
                                            @endforeach

                                            @if (count($dataJawabanLevelTiga) != 0)
                                                @if ($itemTiga->nama == 'Ratio Tenor Asuransi Opsi')
                                                @else
                                                    @if (isset($checkJawabanKelayakan))
                                                        @if ($itemTiga->nama != 'Kelayakan Usaha')
                                                        {{-- @else --}}
                                                            {{-- <div class="row col-span-2">
                                                                <div class="form-group-1">
                                                                    <h6 class="font-medium text-sm" for="">{{ $itemTiga->nama }}</h6>
                                                                </div>
                                                            </div> --}}
                                                        @else
                                                        @endif
                                                    @else
                                                        @if ($itemTiga->nama != 'Kelayakan Usaha')
                                                            {{-- <div class="row col-span-2">
                                                                <div class="form-group-1">
                                                                    <h6 class="font-medium text-sm" for="">{{ $itemTiga->nama }}</h6>
                                                                </div>
                                                            </div> --}}
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
                                                                        @php
                                                                            $dataDetailJawabanTiga = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                                                ->where('id_pengajuan', $dataUmum->id)
                                                                                ->where('id_jawaban', $itemJawabanLevelTiga->id)
                                                                                ->get();
                                                                            $getKomentarPenyelia3 = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                                                ->where('id_item', $itemJawabanLevelTiga->id_item)
                                                                                ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                                                                ->where('detail_komentar.id_user', $comment->id_penyelia)
                                                                                ->get();
                                                                        @endphp
                                                                        <div class="field-review">
                                                                            @if ($itemTiga->nama != 'Ratio Coverage Opsi')
                                                                                <div class="field-name">
                                                                                    <h6>{{ $itemTiga->nama }}</h6>
                                                                                </div>
                                                                                <div class="field-answer">
                                                                                    <p>{{ $itemJawabanLevelTiga->option }}</p>
                                                                                    @if ($dataDetailJawabanTiga != null)
                                                                                        @foreach ($dataDetailJawabanTiga as $key => $item)
                                                                                            @if ($item->skor_penyelia != null && $item->skor_penyelia != '')
                                                                                                <div class="field-review">
                                                                                                    <div class="field-name">
                                                                                                        <h6>Skor</h6>
                                                                                                    </div>
                                                                                                    <div class="field-answer space-y-5">
                                                                                                        <p>
                                                                                                            <span class="field-skor">{{ $item->skor_penyelia }}</span>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="field-review">
                                                                                                    <div class="field-name">
                                                                                                        <h6>Komentar Penyelia</h6>
                                                                                                    </div>
                                                                                                    <div class="field-answer">
                                                                                                        <h6>
                                                                                                            @if (count($getKomentarPenyelia3) > 0)
                                                                                                                {{ strlen($getKomentarPenyelia3[$key]?->komentar) > 0 ? $getKomentarPenyelia3[$key]?->komentar ?? '-' : '-' }}
                                                                                                            @endif
                                                                                                        </h6>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    @endif
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
                                                                            </div>
                                                                        </div>
                                                                @endif
                                                            @endif
                                                            {{--  @endif  --}}
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @endif
                                            <div class="form-group-1">
                                                @foreach ($dataLevelEmpat as $keyEmpat => $itemEmpat)
                                                    @if (!$itemEmpat->is_hide)
                                                            @if ($itemEmpat->opsi_jawaban != 'option')
                                                                @php
                                                                    $dataDetailJawabanTextEmpat = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama', 'item.is_rupiah')
                                                                        ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                                                                        ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                                                                        ->where('jawaban_text.id_jawaban', $itemEmpat->id)
                                                                        ->get();
                                                                @endphp
                                                                @foreach ($dataDetailJawabanTextEmpat as $itemTextEmpat)
                                                                    <div class="field-review">
                                                                        <div class="field-name">
                                                                            @if ($itemEmpat->opsi_jawaban == 'file')
                                                                                @if ($jumlahDataDetailJawabanText > 1)
                                                                                    <h6 for="">{{ $itemTextEmpat->nama }} {{$loop->iteration}}</h6>
                                                                                @else
                                                                                    <h6 for="">{{ $itemTextEmpat->nama }}</h6>
                                                                                @endif
                                                                            @else
                                                                                    <h6 for="">{{ $itemTextEmpat->nama }}</h6>
                                                                            @endif
                                                                        </div>
                                                                        <div class="field-answer">
                                                                            @if ($itemEmpat->opsi_jawaban == 'file')
                                                                                @php
                                                                                    $file_parts = pathinfo(asset('..') . '/upload/' . $dataUmum->id . '/' . $itemEmpat->id . '/' . $itemTextEmpat->opsi_text);
                                                                                @endphp
                                                                                @if ($file_parts['extension'] == 'pdf')
                                                                                    <iframe
                                                                                        style="border: 5px solid #dc3545;"
                                                                                        src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $itemEmpat->id . '/' . $itemTextEmpat->opsi_text }}"
                                                                                        width="100%" height="800px"></iframe>
                                                                                @else
                                                                                    <img style="border: 5px solid #dc3545;" src="{{ asset('..') . '/upload/' . $dataUmum->id . '/' . $itemEmpat->id . '/' . $itemTextEmpat->opsi_text }}"
                                                                                        alt="" width="800px">
                                                                                @endif
                                                                                {{-- Rupiah data tiga --}}
                                                                            @elseif ($itemTextEmpat->is_rupiah)
                                                                                <p>Rp. {{ number_format((int) $itemTextEmpat->opsi_text, 0, ',', '.') }}</p>
                                                                            @else
                                                                                <p>{{ $itemEmpat->opsi_jawaban == 'persen' ? $itemTextEmpat->opsi_text : $itemTextEmpat->opsi_text  }}{{ $itemEmpat->opsi_jawaban == 'persen' ? '%' : '' }} {{ $itemEmpat->id == 130 ? 'Bulan' : ''}}</p>
                                                                            @endif
                                                                        </div>

                                                                        <input type="hidden" class="form-input mb-3"
                                                                            placeholder="Masukkan komentar" name="komentar_penyelia"
                                                                            value="{{ $itemTextEmpat->nama }}" disabled>
                                                                        <input type="hidden" class="form-input mb-3"
                                                                            placeholder="Masukkan komentar" name="komentar_penyelia"
                                                                            value="{{ $itemTextEmpat->opsi_text }}" disabled>
                                                                        <input type="hidden" name="id_jawaban_text[]"
                                                                            value="{{ $itemTextEmpat->id }}">
                                                                        <input type="hidden" name="id[]" value="{{ $itemTextEmpat->id_item }}">
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
                                                                    {{-- <div class="row">
                                                                        <div class="form-group-1 mb-0">
                                                                            <label for="">{{ $itemEmpat->nama }}</label>
                                                                        </div>
                                                                    </div> --}}
                                                                @endif
                                                            @endif
                                                            {{-- Data jawaban Level Empat --}}
                                                            @if (count($dataJawabanLevelEmpat) != 0)
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
                                                                            @php
                                                                                $dataDetailJawabanEmpat = \App\Models\JawabanPengajuanModel::select('id', 'id_jawaban', 'skor', 'skor_penyelia')
                                                                                    ->where('id_pengajuan', $dataUmum->id)
                                                                                    ->where('id_jawaban', $itemJawabanLevelEmpat->id)
                                                                                    ->get();
                                                                                $getKomentarPenyeliaEmpat = \App\Models\DetailKomentarModel::join('komentar', 'komentar.id', 'detail_komentar.id_komentar')
                                                                                    ->where('detail_komentar.id_item', $itemJawabanLevelEmpat->id_item)
                                                                                    ->where('komentar.id_pengajuan', $comment->id_pengajuan)
                                                                                    ->where('detail_komentar.id_user', $comment->id_penyelia)
                                                                                    ->first();
                                                                            @endphp
                                                                                <div class="col-span-2">
                                                                                    @if ($itemEmpat->nama != "Tidak Memiliki Jaminan Tambahan")
                                                                                        <div class="field-review">
                                                                                            <div class="field-name">
                                                                                                <h6>{{ $itemEmpat->nama }}</h6>
                                                                                            </div>
                                                                                            <div class="field-answer space-y-5">
                                                                                                <p>
                                                                                                    {{ $itemJawabanLevelEmpat->option }}
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" class="form-input mb-3"
                                                                                placeholder="Masukkan komentar" name="komentar_penyelia"
                                                                                value="{{ $itemJawabanLevelEmpat->option }}" disabled>
                                                                            <input type="hidden" name="id[]"
                                                                                value="{{ $itemEmpat->id }}">
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif

                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                @php
                                    $no = 0;
                                    $plafon_usulan = DB::table('plafon_usulan')->where('id_pengajuan', $dataUmum->id)->first();
                                @endphp
                            @endforeach
                            <div class="space-y-5 border">
                                <div class="head p-3 bg-gray-50 border-b flex gap-5">
                                    <iconify-icon icon="tabler:message" class="text-2xl"></iconify-icon>
                                    <h2 class="font-bold text-lg tracking-tighter">Pendapat dan Usulan</h2>
                                </div>
                                <div class="pl-5 pb-4 divide-y">
                                    @foreach ($pendapatUsulanStaf as $itemStaf)
                                    <div class="form-group-1">
                                        <div class="field-review">
                                            <div class="field-name">
                                                <h6>Staf</h6>
                                            </div>
                                            <div class="field-answer">
                                                <h6>{{ $itemStaf->pendapat_per_aspek }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @foreach ($pendapatUsulanPenyelia as $itemPenyelia)
                                    <div class="form-group-1">
                                        <div class="field-review">
                                            <div class="field-name">
                                                <h6>Penyelia</h6>
                                            </div>
                                            <div class="field-answer">
                                                <h6>{{ $itemPenyelia->pendapat_per_aspek }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @if ($dataUmum->id_pbo != null)
                                        @foreach ($pendapatUsulanPBO as $itemPBO)
                                            <div class="form-group-1">
                                                <div class="field-review">
                                                    <div class="field-name">
                                                        <h6> PBO</h6>
                                                    </div>
                                                    <div class="field-answer">
                                                        <h6>{{ $itemPBO->pendapat_per_aspek }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if ($dataUmum->id_pbp != null)
                                        @foreach ($pendapatUsulanPBP as $itemPBP)
                                            <div class="form-group-1">
                                                <div class="field-review">
                                                    <div class="field-name">
                                                        <h6>PBP</h6>
                                                    </div>
                                                    <div class="field-answer">
                                                        <h6>{{ $itemPBP->pendapat_per_aspek }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="accordion-section">
                <div class="accordion-header rounded pl-3 border border-theme-primary/5 relative">
                    <div class="flex justify-between gap-3">
                    <div class="flex justify-start gap-3">
                        <button class="p-2 rounded-full bg-theme-primary w-10 h-10 text-white">
                            <h2 class="text-lg">{{$no_aspek+1}}</h2>
                        </button>
                        <h3 class="font-bold text-lg tracking-tighter mt-[6px]">Pendapat dan Usulan</h3>
                    </div>
                        <div class="transform accordion-icon mr-2 mt-1">
                            <iconify-icon icon="uim:angle-down" class="text-3xl"></iconify-icon>
                        </div>
                    </div>
                </div>
                <div class="accordion-content p-3">
                    <div class="divide-y-2 divide-red-800">
                        @if($pendapatDanUsulan->komentar_staff)
                            <div class="p-4">
                                <div class="form-group-1">
                                    <div class="field-review">
                                        <div class="field-name">
                                            <label for="">Staff Kredit</label>
                                        </div>
                                        <div class="field-answer">
                                            <p>{{ $pendapatDanUsulan->komentar_staff }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @if($pendapatDanUsulan->komentar_penyelia)
                    <div class="p-4">
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">Penyelia Kredit</label>
                                </div>
                                <div class="field-answer">
                                    <p>{{ $pendapatDanUsulan->komentar_penyelia }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">Tenor</label>
                                </div>
                                <div class="field-answer">
                                    <p>{{ $plafonUsulan?->jangka_waktu_usulan_penyelia }} Bulan</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">Plafon</label>
                                </div>
                                <div class="field-answer">
                                    <p>Rp. {{ number_format($plafonUsulan?->plafon_usulan_penyelia, 0, ',', '.')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($pendapatDanUsulan->komentar_pbo)
                    <div class="p-4">
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">PBO Kredit</label>
                                </div>
                                <div class="field-answer">
                                    <p>{{ $pendapatDanUsulan->komentar_pbo }}</p>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">Tenor</label>
                                </div>
                                <div class="field-answer">
                                    <p>{{ $plafonUsulan->jangka_waktu_usulan_pbo }} Bulan</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">Plafon</label>
                                </div>
                                <div class="field-answer">
                                    <p>Rp. {{ number_format($plafonUsulan->plafon_usulan_pbo, 0, ',', '.')}}</p>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    @endif
                    {{-- @if($pendapatDanUsulan->komentar_pbo)
                    <div class="p-4">
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">PBO kredit</label>
                                </div>
                                <div class="field-answer">
                                    <p>{{ $pendapatDanUsulan->komentar_pbo }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">Tenor</label>
                                </div>
                                <div class="field-answer">
                                    <p>{{ $plafonUsulan->jangka_waktu_usulan_pbp }} Bulan</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">Plafon</label>
                                </div>
                                <div class="field-answer">
                                    <p>Rp. {{ number_format($plafonUsulan->plafon_usulan_pbp, 0, ',', '.')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif --}}
                    @if($pendapatDanUsulan->komentar_pbp)
                    <div class="p-4">
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">PBP kredit</label>
                                </div>
                                <div class="field-answer">
                                    <p>{{ $pendapatDanUsulan->komentar_pbp }}</p>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">Tenor</label>
                                </div>
                                <div class="field-answer">
                                    <p>{{ $plafonUsulan->jangka_waktu_usulan_pbp }} Bulan</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">Plafon</label>
                                </div>
                                <div class="field-answer">
                                    <p>Rp. {{ number_format($plafonUsulan->plafon_usulan_pbp, 0, ',', '.')}}</p>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                    @endif
                    <div class="p-4">
                        <div class="form-group-1">
                            <div class="field-review">
                                <div class="field-name">
                                    <label for="">Pimpinan Cabang</label>
                                </div>
                                <div class="field-answer">
                                    <p>
                                        <textarea class="form-textarea w-full" placeholder="isi pendapat..." name="pendapat_usulan" id="pendapat_usulan"></textarea>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="button" id="btn-dec"
                    class="px-5 next-tab py-2 border rounded bg-theme-primary text-white">Tolak</button>
                <button type="button" id="btn-acc"
                    class="px-5 py-2 border rounded bg-green-600 text-white btn-simpan">Setujui</button>
            </div>
        </div>
    </section>

    {{--  Decline Modal  --}}
    <div class="modal-layout hidden" id="modal-confirm">
        <div class="modal modal-sm bg-white">
            <form id="logout-form" action="{{ route('pengajuan.change.pincab.status.tolak', $dataUmum->id) }}" method="POST">
                @csrf
                <input type="hidden" name="pendapat" id="pendapat">
                <div class="modal-head">
                    <div class="title">
                        <h2 class="font-bold text-2xl tracking-tighter text-theme-text">
                            Konfirmasi
                        </h2>
                    </div>
                    <button data-dismiss-id="modal-confirm">
                        <iconify-icon icon="iconamoon:close-bold" class="text-2xl"></iconify-icon>
                    </button>
                </div>
                <div class="modal-body space-y-5">
                    <div class="space-y-3">
                        <p>Anda yakin akan menolak pengajuan ini?</p>
                    </div>
                </div>
                <div class="modal-footer justify-end">
                    <button class="btn-cancel" type="button" data-dismiss-id="modal-confirm">
                        Batal
                    </button>
                    <button type="submit" class="btn-submit btn-simpan-confirm">Tolak</button>
                </div>
            </form>
        </div>
    </div>
    {{--  Approve Modal  --}}
    <div class="modal-layout hidden" id="modal-approve">
        <div class="modal modal-sm bg-white">
            <form id="logout-form" action="{{ route('pengajuan.check.pincab.status.detail.post', $dataUmum->id) }}" method="POST">
                @csrf

                <input type="hidden" name="pendapat" id="pendapat">
                <div class="modal-head">
                    <div class="title">
                        <h2 class="font-bold text-2xl tracking-tighter text-theme-text">
                            Konfirmasi
                        </h2>
                    </div>
                    <button data-dismiss-id="modal-approve">
                        <iconify-icon icon="iconamoon:close-bold" class="text-2xl"></iconify-icon>
                    </button>
                </div>
                <div class="modal-body space-y-5">
                    <div class="space-y-3">
                        <p>Lengkapi form dibawah ini untuk menyetujui pengajuan.</p>
                    </div>
                    <div class="space-y-3">
                        <div class="form-group-1">
                            <div class="input-box">
                                <label for="">Plafon Disetujui</label>
                                <input type="text" class="form-input rupiah" name="nominal_disetujui" id="nominal_disetujui" required>
                            </div>
                        </div>
                        <div class="form-group-1">
                            <div class="input-box">
                                <label for="">Tenor Disetujui (Bulan)</label>
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <input type="number" class="form-input" name="jangka_waktu_disetujui" id="jangka_waktu_disetujui" required>
                                    </div>
                                    <div class="flex-shrink-0  mt-2.5rem">
                                        <span class="form-input bg-gray-100">Bulan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-end">
                    <button class="btn-cancel" type="button" data-dismiss-id="modal-approve">
                        Batal
                    </button>
                    <button type="submit" class="btn-submit btn-simpan-aprrove">Setujui</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script-inject')
    <script>
        $('.btn-simpan-aprrove').on('click', function(){
            $("#modal-approve").addClass("hidden");
            $("#preload-data").removeClass("hidden");
        })
        $('.btn-simpan-confirm').on('click', function(){
            $("#modal-confirm").addClass("hidden");
            $("#preload-data").removeClass("hidden");
        })

        $(".accordion-header").click(function() {
            // Toggle the visibility of the next element with class 'accordion-content'
            $(this).next(".accordion-content").slideToggle();
        });

        $(document).on('click', '#btn-dec', function() {
            $('#modal-confirm').removeClass('hidden')
        })

        $(document).on('click', '#btn-acc', function() {
            $('#modal-approve').removeClass('hidden')
            var pendapat = $('#pendapat_usulan').val()
            console.log(pendapat);
            if (pendapat) {
                $('#modal-approve #pendapat').val(pendapat)
            }
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

        $('#modal-approve #nominal_disetujui').on('change', function() {
            limitJangkaWaktu()
        })
        $('#modal-approve #jangka_waktu_disetujui').on('change', function() {
            limitJangkaWaktu()
        })

        function limitJangkaWaktu() {
            var nominal = $('#modal-approve #nominal_disetujui').val()
            nominal = nominal != '' ? nominal.replaceAll('.','') : 0
            var limit = 100000000
            if (parseInt(nominal) <= limit) {
                var jangka_waktu = $('#modal-approve #jangka_waktu_disetujui').val()
                if (jangka_waktu != '') {
                    jangka_waktu = parseInt(jangka_waktu)
                    if (jangka_waktu > 36) {
                        $('.jangka_waktu_error').removeClass('hidden')
                        $('.jangka_waktu_error').html('Jangka waktu maksimal 36 bulan.')
                    }
                    else {
                        $('.jangka_waktu_error').addClass('hidden')
                        $('.jangka_waktu_error').html('')
                    }
                }
            }
            else if (parseInt(nominal) > limit) {
                var jangka_waktu = $('#modal-approve #jangka_waktu_disetujui').val()
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
    </script>
    <script>
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'maxWidth': 5000,
        })
    </script>
@endpush
