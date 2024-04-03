{{--  Modal perhitungan aspek keuangan  --}}
{{-- <!-- Modal --> --}}
<style>
    #loading-simpan-perhitungan {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
  }
  </style>
  @php
    $lev1 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)->where('level', 1)->orderBy('sequence', 'asc')->get();
  @endphp
  <div class="modal-layout hidden fade" id="perhitunganModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div id="loading-simpan-perhitungan" style="display: none;" class="text-center">
      <img src="{{ asset('img/loading3.gif') }}" alt="Loading..." style="width: 100px; height: 100px;">
    </div>
    <div class="modal modal-sm bg-white modal-dialog modal-lg" role="document">
        <div class="modal-content" id="perhitunganModalAfterLoading">
            <div class="modal-head">
                <div class="title">
                    <h5 class="modal-title font-bold">Perhitungan</h5>
                </div>
                <button data-dismiss-id="perhitunganModal">
                    <iconify-icon icon="iconamoon:close-bold" class="text-2xl"></iconify-icon>
                </button>
            </div>
            <div class="modal-body">
                <!-- form -->
                <form id="form-perhitungan" method="" action="">
                  {{--  <div class="row">  --}}
                    <!-- content -->
                      <div class="grid grid-cols-2 gap-2 row">
                        <!-- pilih bulan -->
                        <div class="input-box col-md-6">
                          <div class="form-group mb-4">
                            <label for="inputHarta" class="font-weight-semibold">Pilih Periode Bulan:</label>
                            <select name="" style="width: 100%; height: 40px" class="form-input" id="periode" onchange="calcForm()">
                                  <option selected>--Pilih Bulan--</option>
                                  <option value="1">Januari</option>
                                  <option value="2">Februari</option>
                                  <option value="3">Maret</option>
                                  <option value="4">April</option>
                                  <option value="5">Mei</option>
                                  <option value="6">Juni</option>
                                  <option value="7">Juli</option>
                                  <option value="8">Agustus</option>
                                  <option value="9">September</option>
                                  <option value="10">Oktober</option>
                                  <option value="11">November</option>
                                  <option value="12">Desember</option>
                              </select>
                          </div>
                        </div>
                        <!-- end pilih bulan -->
                        <!-- pilih tahun -->
                        <div class="input-box col-md-6">
                            <div class="form-group mb-4">
                                @php
                                    $start_year = 2010;
                                    $end_year = date('Y');
                                @endphp
                                <label for="periode_tahun" class="font-weight-semibold">Pilih Periode Tahun :</label>
                                <select name="periode_tahun" id="periode_tahun" style="width: 100%; height: 40px" class="form-input">
                                    <option selected>--Pilih Tahun--</option>
                                    @for ($i=0;$start_year <= $end_year;$i--)
                                      @php
                                        $year = $end_year--;
                                      @endphp
                                    <option value="{{$year}}">{{$year}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <!-- end pilih tahun -->
                      </div>
                      <!-- form bagian level 1 -->
                      @foreach ($lev1 as $item)
                        <div class="card @if(!$item->is_card_show) border-0 p-0 @else mb-4 @endif">
                          @if ($item->is_card_show)
                            <h4 class="card-header">{{ $item->field }}</h4>
                          @endif
                          <div class="card-body @if(!$item->is_card_show && !$item->is_two_columns) p-0 @endif">
                            @if ($item->is_two_columns)
                                @php
                                  $lev2 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                              ->where('level', 2)
                                                                              ->where('parent_id', $item->id)
                                                                              ->get();
                                @endphp
                                <div class="grid grid-cols-2 gap-2 row">
                                  @foreach ($lev2 as $key2 => $item2)
                                    @php
                                      $lev3 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                                  ->where('level', 3)
                                                                                  ->where('parent_id', $item2->id)
                                                                                  ->get();
                                    @endphp
                                    <div class="col-md-6 @if($key2 == 0 && !$item->is_card_show) pl-0 @elseif($key2 != 0 && !$item->is_card_show) pr-0 @endif">
                                        <div class="card mb-3">
                                          <h5 class="card-header">{{$item2->field}}</h5>
                                          <div class="input-box card-body">
                                            <!-- form bagian level 3 -->
                                            @foreach ($lev3 as $item3)
                                              @if (!$item3->is_hidden)
                                                <div class="form-group">
                                                    <label for="inp_{{$item3->id}}" class="font-weight-semibold">{{$item3->field}}</label>
                                                    <div class="relative input-group">
                                                        <input type="{{ $item3->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3->id}}" name="inpLevelTiga[{{$item3->id}}]"
                                                            id="inp_{{$item3->id}}" data-formula="{{$item3->formula}}" data-detail="{{$item3->have_detail}}"
                                                            @if ($item3->readonly) readonly @endif onkeyup="calcForm()"/>
                                                        @if ($item3->have_detail)
                                                            @php
                                                            $lev4 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                                                        ->where('level', 4)
                                                                                                        ->where('parent_id', $item3->id)
                                                                                                        ->get();
                                                            @endphp
                                                            <div class="absolute end-0 flex items-center ps-3.5 input-group-prepend" style="top: 0">
                                                                <a class="px-5 py-2 border rounded bg-theme-primary text-white btn btn-danger toggleButton" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" id="toggleButton">
                                                                    Tampilkan
                                                                    <i class="bi bi-caret-down"></i>
                                                                </a>
                                                            </div>
                                                            <div class="collapse mt-4 hidden" id="collapseExample">
                                                                <div class="table-responsive">
                                                                    <table class="tables table" id="table_item" style="box-sizing: border-box">
                                                                        <thead>
                                                                            <tr>
                                                                                @foreach ($lev4 as $item4)
                                                                                <th scope="col">{{$item4->field}}</th>
                                                                                @endforeach
                                                                                <th scope="col">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                            @php
                                                                                $indexInpLevelEmpatId = 0;
                                                                                $indexInpLevelEmpat = 0;
                                                                            @endphp
                                                                                @foreach ($lev4 as $item4)
                                                                                <td id="detail-item">
                                                                                    <input type="hidden" name="inpLevelEmpatId[{{ $indexInpLevelEmpatId++ }}]" value="{{ $item4->id }}">
                                                                                    <input class="form-control rupiah inp_{{$item4->id}}" type="@if(!$item4->is_hidden) text @else hidden @endif" name="inpLevelEmpat[{{ $indexInpLevelEmpat++ }}]"
                                                                                        id="inp_{{$item4->id}}" data-formula="{{$item4->formula}}" data-level="{{$item4->level}}" onkeyup="calcForm()" @if ($item4->readonly) readonly @endif/>
                                                                                </td>
                                                                                @endforeach
                                                                                <td>
                                                                                    <button class="btn-add-2 btn btn-success" type="button">
                                                                                        +
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($item3->add_on)
                                                            <div class="input-group-append">
                                                                <span class="input-group-text" id="basic-addon2">{{$item3->add_on}}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                              @else
                                                <input type="{{ $item3->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3->id}}" name="inpLevelTiga[{{$item3->id}}]"
                                                  id="inp_{{$item3->id}}" data-formula="{{$item3->formula}}" data-detail="{{$item3->have_detail}}"
                                                  @if ($item3->readonly) readonly @endif onkeyup="calcForm()"/>
                                              @endif
                                            @endforeach
                                            <!-- end form bagian level 3 -->
                                          </div>
                                        </div>
                                    </div>
                                  @endforeach
                                </div>
                            @elseif ($item->field == 'Laba Rugi')
                              @php
                                $lev2 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                      ->where('level', 2)
                                      ->where('parent_id', $item->id)
                                      ->get();
                                $levelTigaShow = array();
                                $levelTigaHidden = array();
                                $levelTigaLabelTotal = array();
                              @endphp
                              <div class="row">
                                @foreach ($lev2 as $item2)
                                  <div class="col-md-12">
                                    <div class="card mb-3">
                                      <h5 class="card-header">{{ $item2->field }}</h5>
                                      <div class="card-body">
                                        <div class="table-responsive">
                                          <table class="tables table">
                                            <thead>
                                              <tr>
                                                <th style="width: 40%;" scope="col">Field</th>
                                                <th style="width: 30%;" scope="col">Sebelum Kredit</th>
                                                <th style="width: 30%;" scope="col">Sesudah Kredit</th>
                                              </tr>
                                            </thead>
                                            <tbody>
                                                @php

                                                  $lev3Hidden = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                        ->where('level', 3)
                                                        ->where('parent_id', $item2->id)
                                                        ->where('is_label_hidden', 1)
                                                        ->get();
                                                  $lev3Show = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                        ->where('level', 3)
                                                        ->where('parent_id', $item2->id)
                                                        ->where('is_label_hidden', 0)
                                                        ->get();

                                                  unset($levelTigaShow);
                                                  unset($levelTigaHidden);
                                                  unset($levelTigaLabelTotal);
                                                  $levelTigaShow = array();
                                                  $levelTigaHidden = array();
                                                  $levelTigaLabelTotal = array();
                                                  if (count($lev3Hidden) > 0) {
                                                      foreach ($lev3Hidden as $key3Hidden => $value) {
                                                          array_push($levelTigaHidden, $value);
                                                          array_push($levelTigaLabelTotal, $value->field == 'Total' ? ' ' . str_replace('Sebelum Kredit', '', $item2->field) : '');
                                                      }
                                                  }

                                                  if (count($lev3Show) > 0) {
                                                      foreach ($lev3Show as $key3Show => $value) {
                                                          array_push($levelTigaShow, $value);
                                                      }
                                                  }
                                                @endphp
                                              @foreach ($levelTigaShow as $key => $item3)
                                                  <tr>
                                                    <td>{{ $item3->field . $levelTigaLabelTotal[$key] }}</td>
                                                    <td>
                                                      <input type="{{ $levelTigaHidden[$key]->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$levelTigaHidden[$key]->id}}" name="inpLevelTiga[{{$levelTigaHidden[$key]->id}}]"
                                                              id="inp_{{$levelTigaHidden[$key]->id}}" data-formula="{{$levelTigaHidden[$key]->formula}}" data-detail="{{$levelTigaHidden[$key]->have_detail}}"
                                                              @if ($levelTigaHidden[$key]->readonly) readonly @endif onkeyup="calcForm()"/>
                                                    </td>
                                                    <td>
                                                      <input type="{{ $item3->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3->id}}" name="inpLevelTiga[{{$item3->id}}]"
                                                              id="inp_{{$item3->id}}" data-formula="{{$item3->formula}}" data-detail="{{$item3->have_detail}}"
                                                              @if ($item3->readonly) readonly @endif onkeyup="calcForm()"/>
                                                    </td>
                                                  </tr>
                                              @endforeach
                                            </tbody>
                                          </table>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                @endforeach
                                {{-- <div class="col-md-12 table-responsive">
                                  <table class="table" style="position: sticky;top: 0">
                                    <thead>
                                      <tr>
                                        <th style="position: sticky; top: 0;" scope="col">Field</th>
                                        <th style="position: sticky; top: 0;" scope="col">Sebelum Kredit</th>
                                        <th style="position: sticky; top: 0;" scope="col">Sesudah Kredit</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      @foreach ($lev2 as $key => $item2)
                                          @php
                                            $lev3Hidden = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                  ->where('level', 3)
                                                  ->where('parent_id', $item2->id)
                                                  ->where('is_label_hidden', 1)
                                                  ->get();
                                            $lev3Show = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                  ->where('level', 3)
                                                  ->where('parent_id', $item2->id)
                                                  ->where('is_label_hidden', 0)
                                                  ->get();

                                            if (count($lev3Hidden) > 0) {
                                                foreach ($lev3Hidden as $key3Hidden => $value) {
                                                    array_push($levelTigaHidden, $value);
                                                    array_push($levelTigaLabelTotal, $value->field == 'Total' ? ' ' . str_replace('Sebelum Kredit', '', $item2->field) : '');
                                                }
                                            }

                                            if (count($lev3Show) > 0) {
                                                foreach ($lev3Show as $key3Show => $value) {
                                                    array_push($levelTigaShow, $value);
                                                }
                                            }
                                          @endphp
                                      @endforeach
                                      @foreach ($levelTigaShow as $key => $item3)
                                          <tr>
                                            <td>{{ $item3->field . $levelTigaLabelTotal[$key] }}</td>
                                            <td>
                                              <input type="{{ $levelTigaHidden[$key]->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$levelTigaHidden[$key]->id}}" name="inpLevelTiga[{{$levelTigaHidden[$key]->id}}]"
                                                      id="inp_{{$levelTigaHidden[$key]->id}}" data-formula="{{$levelTigaHidden[$key]->formula}}" data-detail="{{$levelTigaHidden[$key]->have_detail}}"
                                                      @if ($levelTigaHidden[$key]->readonly) readonly @endif onkeyup="calcForm()"/>
                                            </td>
                                            <td>
                                              <input type="{{ $item3->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3->id}}" name="inpLevelTiga[{{$item3->id}}]"
                                                      id="inp_{{$item3->id}}" data-formula="{{$item3->formula}}" data-detail="{{$item3->have_detail}}"
                                                      @if ($item3->readonly) readonly @endif onkeyup="calcForm()"/>
                                            </td>
                                          </tr>
                                      @endforeach
                                    </tbody>
                                  </table>
                                </div> --}}
                              </div>
                            @else
                              @php
                                  $lev2 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                      ->where('level', 2)
                                      ->where('parent_id', $item->id)
                                      ->get();
                              @endphp

                              <div class="row">
                                  @foreach ($lev2 as $key => $item2)
                                    @php
                                      $lev3 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                                  ->where('level', 3)
                                                                                  ->where('parent_id', $item2->id)
                                                                                  ->orderBy('sequence', 'asc')
                                                                                  ->get();
                                    @endphp
                                    <div class="col-md-12">
                                      <div class="card mb-3">
                                        <h5 class="card-header">{{ $item2->field }}</h5>
                                        <div class="card-body">
                                          <div class="grid grid-cols-2 gap-2 row m-0">
                                            @foreach ($lev3 as $item3)
                                              @if (!$item3->is_hidden)
                                                  <div class=" @if($item2->inline) col @elseif( !$item->is_card_show && count($lev3) > 1) col-md-6 @else col-span-full @endif">
                                                    <div class="input-box form-group">
                                                        <label for="inp_{{$item3->id}}" class="font-weight-semibold">{{$item3->field}}</label>
                                                        <div class="flex items-center input-group">

                                                          <input type="{{ $item3->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3->id}} {{ str_replace(' ', '_', strtolower($item3->field)) }}" name="inpLevelTiga[{{$item3->id}}]"
                                                            id="inp_{{$item3->id}}" data-formula="{{$item3->formula}}" data-detail="{{$item3->have_detail}}"
                                                            @if ($item3->readonly) readonly @endif onkeyup="calcForm()"/>
                                                            @if ($item2->inline && $item3->add_on)
                                                                <b class="my-auto ml-4 text-lg">{{ $item3->add_on }}</b>
                                                            @endif
                                                          @if ($item3->have_detail)
                                                            @php
                                                              $lev4 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                                                          ->where('level', 4)
                                                                                                          ->where('parent_id', $item3->id)
                                                                                                          ->get();
                                                            @endphp
                                                            <div class="input-group-prepend">
                                                                <a class="px-5 py-2 border rounded bg-theme-primary text-white btn btn-danger toggleButton" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" id="toggleButton">
                                                                    Tampilkan
                                                                    <i class="bi bi-caret-down"></i>
                                                                </a>
                                                            </div>
                                                            <div class="collapse hidden mt-4" id="collapseExample">
                                                                <div class="table-responsive">
                                                                    <table class="tables table" id="table_item" style="box-sizing: border-box">
                                                                        <thead>
                                                                            <tr>
                                                                                @foreach ($lev4 as $item4)
                                                                                  <th scope="col">{{$item4->field}}</th>
                                                                                @endforeach
                                                                                <th scope="col">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                              @php
                                                                                  $indexInpLevelEmpatId = 0;
                                                                                  $indexInpLevelEmpat = 0;
                                                                              @endphp
                                                                                @foreach ($lev4 as $item4)
                                                                                  <td id="detail-item">
                                                                                      <input type="hidden" name="inpLevelEmpatId[{{ $indexInpLevelEmpatId++ }}]" value="{{ $item4->id }}">
                                                                                      <input class="form-control rupiah inp_{{$item4->id}}" type="@if(!$item4->is_hidden) text @else hidden @endif" name="inpLevelEmpat[{{ $indexInpLevelEmpat++ }}]"
                                                                                        id="inp_{{$item4->id}}" data-formula="{{$item4->formula}}" data-level="{{$item4->level}}" onkeyup="calcForm()" @if ($item4->readonly) readonly @endif/>
                                                                                  </td>
                                                                                @endforeach
                                                                                <td>
                                                                                    <button class="btn-add-2 btn btn-success" type="button">
                                                                                        +
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                          @endif
                                                          @if ($item3->add_on && !$item2->inline)
                                                            <div class="input-group-append">
                                                                <span class="form-input bg-gray-100 input-group-text" id="basic-addon2">{{$item3->add_on}}</span>
                                                            </div>
                                                          @endif
                                                        </div>
                                                        <div class="info_{{ str_replace(' ', '_', strtolower($item3->field)) }}"></div>
                                                    </div>
                                                  </div>
                                                  {{-- @if ($item3->field == 'Utang Bank')
                                                    <div class="col-md-11 ">
                                                      <hr style="border: none; height: 1px; color: #333; background-color: #333;">
                                                    </div>
                                                    <div class="col-md-1 justify-content-center text-center">
                                                      <h6 class="mx-auto">+</h6>
                                                    </div>
                                                  @endif --}}
                                              @else
                                                <input type="{{ $item3->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3->id}}" name="inpLevelTiga[{{$item3->id}}]"
                                                  id="inp_{{$item3->id}}" data-formula="{{$item3->formula}}" data-detail="{{$item3->have_detail}}"
                                                  @if ($item3->readonly) readonly @endif onkeyup="calcForm()"/>
                                              @endif
                                            @endforeach
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  @endforeach
                              </div>
                            @endif
                          </div>
                        </div>
                        {{-- <div class="head">
                            <h4 class="mb-4 font-weight-bold" style="letter-spacing: -1px">
                                {{$item->field}}
                            </h4>
                        </div> --}}
                        <!-- form bagian level 2 -->
                        @php
                          $lev2 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                      ->where('level', 2)
                                                                      ->where('parent_id', $item->id)
                                                                      ->get();
                        @endphp
                        {{-- <div class="row">
                          @foreach ($lev2 as $item2)
                            @php
                              $lev3 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                          ->where('level', 3)
                                                                          ->where('parent_id', $item2->id)
                                                                          ->get();
                            @endphp
                            <div class="col-md-6">
                              <div class="card mb-4">
                                  <div class="card-header">
                                    <h5>{{$item2->field}}</h5><h5 id="tampilPeriode"></h5>
                                  </div>
                                  <div class="card-body">
                                    <!-- form bagian level 3 -->
                                    @foreach ($lev3 as $item3)
                                      @if (!$item3->is_hidden)
                                        <div class="form-group">
                                            <label for="inp_{{$item3->id}}" class="font-weight-semibold">{{$item3->field}}</label>
                                            <div class="input-group">
                                              <input type="{{ $item3->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3->id}}" name="inpLevelTiga[{{$item3->id}}]"
                                                id="inp_{{$item3->id}}" data-formula="{{$item3->formula}}" data-detail="{{$item3->have_detail}}"
                                                @if ($item3->readonly) readonly @endif onkeyup="calcForm()"/>
                                              @if ($item3->have_detail)
                                                @php
                                                  $lev4 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                                              ->where('level', 4)
                                                                                              ->where('parent_id', $item3->id)
                                                                                              ->get();
                                                @endphp
                                                <div class="input-group-prepend">
                                                    <a class="btn btn-danger" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false"
                                                        aria-controls="collapseExample">
                                                        Tampilkan
                                                        <i class="bi bi-caret-down"></i>
                                                    </a>
                                                </div>
                                                <div class="collapse mt-4" id="collapseExample">
                                                    <div class="table-responsive">
                                                        <table class="table" id="table_item" style="box-sizing: border-box">
                                                            <thead>
                                                                <tr>
                                                                    @foreach ($lev4 as $item4)
                                                                      <th scope="col">{{$item4->field}}</th>
                                                                    @endforeach
                                                                    <th scope="col">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                  @php
                                                                      $indexInpLevelEmpatId = 0;
                                                                      $indexInpLevelEmpat = 0;
                                                                  @endphp
                                                                    @foreach ($lev4 as $item4)
                                                                      <td id="detail-item">
                                                                          <input type="hidden" name="inpLevelEmpatId[{{ $indexInpLevelEmpatId++ }}]" value="{{ $item4->id }}">
                                                                          <input class="form-control rupiah inp_{{$item4->id}}" type="@if(!$item4->is_hidden) text @else hidden @endif" name="inpLevelEmpat[{{ $indexInpLevelEmpat++ }}]"
                                                                            id="inp_{{$item4->id}}" data-formula="{{$item4->formula}}" data-level="{{$item4->level}}" onkeyup="calcForm()" @if ($item4->readonly) readonly @endif/>
                                                                      </td>
                                                                    @endforeach
                                                                    <td>
                                                                        <button class="btn-add-2 btn btn-success" type="button">
                                                                            +
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                              @endif
                                              @if ($item3->add_on)
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">{{$item3->add_on}}</span>
                                                </div>
                                              @endif
                                            </div>
                                        </div>
                                      @else
                                        <input type="{{ $item3->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3->id}}" name="inpLevelTiga[{{$item3->id}}]"
                                          id="inp_{{$item3->id}}" data-formula="{{$item3->formula}}" data-detail="{{$item3->have_detail}}"
                                          @if ($item3->readonly) readonly @endif onkeyup="calcForm()"/>
                                      @endif
                                    @endforeach
                                    <!-- end form bagian level 3 -->
                                  </div>
                                </div>
                              </div>
                          @endforeach
                        </div> --}}
                        <!-- end form bagian level 2 -->
                      @endforeach
                      <!-- end form bagian level 1 -->
                      <!-- form bagian level 3 no parent -->
                      @php
                        $lev3NoParent = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                            ->where('level', 3)
                                                                            ->whereNull('parent_id')
                                                                            ->get();
                      @endphp
                      <div class="form-row">
                          @foreach ($lev3NoParent as $item3NoParent)
                            @if ($item3NoParent->is_hidden)
                              <input type="{{ $item3NoParent->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3NoParent->id}}" name="inpLevelTigaParent[{{$item3NoParent->id}}]"
                                id="inp_{{$item3NoParent->id}}" data-formula="{{$item3NoParent->formula}}" data-detail="{{$item3NoParent->have_detail}}"
                                @if ($item3NoParent->readonly) readonly @endif onkeyup="calcForm()"/>
                            @else
                              <div class="col-6">
                                  <div class="form-group form-field">
                                      <label for="inp_{{$item3NoParent->id}}" class="font-weight-semibold">{{$item3NoParent->field}}</label>
                                      <div class="input-group">
                                        <input type="{{ $item3NoParent->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3NoParent->id}} {{ str_replace(' ', '_', strtolower($item3NoParent->field)) }}" name="inpLevelTigaParent[{{$item3NoParent->id}}]"
                                        id="inp_{{$item3NoParent->id}}" data-formula="{{$item3NoParent->formula}}" data-detail="{{$item3NoParent->have_detail}}"
                                        @if ($item3NoParent->readonly) readonly @endif onkeyup="calcForm()"/>
                                          @if ($item3NoParent->add_on)
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">{{$item3NoParent->add_on}}</span>
                                            </div>
                                          @endif
                                      </div>
                                      <div class="info_{{ str_replace(' ', '_', strtolower($item3NoParent->field)) }}"></div>
                                  </div>
                              </div>
                            @endif
                          @endforeach
                      </div>
                      <!-- end form bagian level 3 no parent -->
                      <!-- form bagian level 2 no parent -->
                        @php
                          $lev2NoParent = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                      ->where('level', 2)
                                                                      ->whereNull('parent_id')
                                                                      ->get();
                        @endphp
                        @foreach ($lev2NoParent as $item2NoParent)
                          @php
                            $lev3NoParent = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                        ->where('level', 3)
                                                                        ->where('parent_id', $item2NoParent->id)
                                                                        ->get();
                          @endphp
                          <div class="card mb-4">
                              <h5 class="card-header">{{$item2NoParent->field}}</h5>
                              <div class="card-body">
                                <!-- form bagian level 3 -->
                                @if ($item2NoParent->is_form)
                                  @foreach ($lev3NoParent as $item3NoParent)
                                    <div class="form-group form-field">
                                        <label for="inp_{{$item3NoParent->id}}" class="font-weight-semibold">{{$item3NoParent->field}}</label>
                                        <div class="input-group">
                                          <input type="{{ $item3NoParent->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3NoParent->id}} {{ str_replace(' ', '_', $item3NoParent->field) }}"  name="inpLevelTigaParent[{{$item3NoParent->id}}]"
                                          id="inp_{{$item3NoParent->id}}" data-formula="{{$item3NoParent->formula}}" data-detail="{{$item3NoParent->have_detail}}"
                                          @if ($item3NoParent->readonly) readonly @endif onkeyup="calcForm()"/>
                                          @if ($item3NoParent->add_on)
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">{{$item3NoParent->add_on}}</span>
                                            </div>
                                          @endif
                                        </div>
                                    </div>
                                  @endforeach
                                @else
                                  <table>
                                    @foreach ($lev3NoParent as $item3NoParent)
                                      <tr>
                                        <td class="pr-4">
                                          <label for="inp_{{$item3NoParent->id}}" class="font-weight-semibold">{{$item3NoParent->field}}</label>
                                        </td>
                                        <td width="20">
                                          <label class="font-weight-semibold">:</label>
                                        </td>
                                        <td>
                                          <input type="hidden" name="inpLevelTigaParent[{{$item3NoParent->id}}]"
                                            id="inp_{{$item3NoParent->id}}" data-formula="{{$item3NoParent->formula}}" data-detail="{{$item3NoParent->have_detail}}"
                                            @if ($item3NoParent->readonly) readonly @endif onkeyup="calcForm()">
                                          <label class="font-weight-normal" id="inp_{{$item3NoParent->id}}_label">0</label>
                                        </td>
                                        {{--  <div class="form-group form-field">
                                            <label for="inp_{{$item3NoParent->id}}" class="font-weight-semibold">{{$item3NoParent->field}}</label>
                                            <label id="inp_{{$item3NoParent->id}}"></label>
                                            <input type="hidden" name="inp_{{$item3NoParent->id}}" id="inp_{{$item3NoParent->id}}">
                                        </div>  --}}
                                      </tr>
                                    @endforeach
                                </table>
                                @endif
                                <!-- end form bagian level 3 -->
                              </div>
                          </div>
                        @endforeach
                        <!-- end form bagian level 2 no parent -->
                      <!-- form bagian level 3 no parent -->
                      @php
                        $lev3NoParent = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                            ->where('level', 3)
                                                                            ->where('parent_id', 0)
                                                                            ->get();
                      @endphp
                      <div class="form-row">
                          @foreach ($lev3NoParent as $item3NoParent)
                            <div class="col-6">
                                <div class="form-group form-field">
                                    <label for="inp_{{$item3NoParent->id}}" class="font-weight-semibold">{{$item3NoParent->field}}</label>
                                    <input type="{{ $item3NoParent->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3NoParent->id}}" name="inp_{{$item3NoParent->id}}"
                                        id="inp_{{$item3NoParent->id}}" data-formula="{{$item3NoParent->formula}}"
                                        @if ($item3NoParent->readonly) readonly @endif onkeyup="calcForm()"/>
                                </div>
                            </div>
                          @endforeach
                      </div>
                      <!-- end form bagian level 3 no parent -->
                  {{--  </div>  --}}
                </form>
                <!-- end form -->
            </div>
            <!-- button wrapper -->
            <div class="modal-footer flex justify-end">
                <div>
                    <button type="button" class="btn-cancel" data-dismiss-id="perhitunganModal">
                        Batal
                    </button>
                </div>
                <div>
                    <button type="button" class="px-7 py-3 rounded font-semibold flex gap-3 bg-theme-primary border text-white btn btn-primary btn btn-danger" id="btnSimpanPerhitungan">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
   <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
  <script>
    var selectDate = $(".select-date");
    selectDate.select2({
        dropdownParent: $('#perhitunganModal')
    });
    $(".btn-add-2").on("click", function(e) {
        var indexInpLevelEmpatId = {{ $indexInpLevelEmpatId }};
        var indexInpLevelEmpat = {{ $indexInpLevelEmpat }};
        var allId = [];
        var name = [];
        var allIdHidden = [];
        var allFormula = [];
        var allLevel = [];

        $('#detail-item input').each(function() {
          if($(this).attr("type") != "hidden"){
            // console.log("TEST attr");
            var id = $(this).attr('id')
            allId.push(id)
            name.push($(this).attr("name"))
            allFormula.push($(this).data('formula'))
            allLevel.push($(this).data('level'))
          }
        })
        $('#detail-item input[type=hidden]').each(function() {
          var value = $(this).val()
          allIdHidden.push(value)
        })
        var content = `<tr>`
        $.each(allId, function(i, item) {
          content += `<td>
            <input type="hidden" name="inpLevelEmpatId[${indexInpLevelEmpatId++}]" value="${allIdHidden[i]}"/>
            <input
                    class="form-control rupiah ${item}"
                    type="text"
                    name="inpLevelEmpat[${indexInpLevelEmpat++}]"
                    id="${item}"
                    data-formula="${allFormula[i]}"
                    data-level="${allLevel[i]}"
                    onkeyup="calcForm()"
            `;
            if(item == 'inp_58'){
              content += ` readonly
                />
                </td>
              `
            } else {
              content += `
                />
                </td>
              `
            }

        })
        content += `<td>
                                <button
                                    class="btn-minus btn btn-danger"
                                    type="button"
                                >
                                    -
                                </button>
                            </td>
                        </tr>`

        $("#table_item tbody").append(content);
        calcForm()
    });

    $("#table_item").on("click", ".btn-minus", function() {
        $(this).closest("tr").remove();
        calcForm()
    });

    $("#form-perhitungan .form-control").keyup(function() {
        var id = $(this).attr('id');
        calcForm()
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

    $("#table_item").on("keyup", ".rupiah", function(){
        var input = $(this).val()
        $(this).val(formatrupiah(input))
        calcForm()
    })

    function cekPlafon(){
      var plafonUsulan = parseInt($(".plafon_usulan").val() ? $(".plafon_usulan").val().replaceAll(".", "") : 0);
      var plafonDataUmum  = parseInt($("#jumlah_kredit").val() ? $("#jumlah_kredit").val().replaceAll(".", "") : 0);
      var higher = 0;
      var lower = 0;
      var showHigher = '';
      var showLower = '';
      if(plafonUsulan > 0 && plafonDataUmum > 0){
        console.log(`plafonUsulan: ${plafonUsulan}`);
        console.log(`plafonDataUmum: ${plafonDataUmum}`);
        if(plafonUsulan > plafonDataUmum){
          higher = plafonUsulan;
          lower = plafonDataUmum;
          showHigher = "Plafond Usulan"
          showLower = "Plafond Pengajuan"
          var selisih = higher - lower;
          var persenPlafon = Math.round(eval((selisih / lower) * 100));
          $(".info_plafon_usulan").empty()
          $(".info_plafon_usulan").append(`
            <div class="alert alert-info" role="alert">
                ${showHigher} Lebih Besar ${persenPlafon}% Daripada ${showLower} Sebesar Rp.${formatrupiah(selisih.toString())}
            </div>
          `);
        } else if(plafonUsulan < plafonDataUmum) {
          higher = plafonDataUmum;
          lower = plafonUsulan;
          showHigher = "Plafond Pengajuan"
          showLower = "Plafond Usulan"
          var selisih = higher - lower;
          var persenPlafon = Math.round(eval((selisih / lower) * 100));
          $(".info_plafon_usulan").empty()
          $(".info_plafon_usulan").append(`
            <div class="alert alert-info" role="alert">
                ${showHigher} Lebih Besar ${persenPlafon}% Daripada ${showLower} Sebesar Rp.${formatrupiah(selisih.toString())}
            </div>
          `);
        } else if(plafonUsulan == plafonDataUmum){
          $(".info_plafon_usulan").empty()
        }
      } else{
        $(".info_plafon_usulan").empty();
      }
    }

    function cekTenor(){
      var jangkaWaktuKredit = parseInt($(".jangka_waktu_kredit").val() != 0 ? $(".jangka_waktu_kredit").val() : 0);
      var jangkaWaktuUsulan = parseInt($(".jangka_waktu_usulan").val() != 0 ? $(".jangka_waktu_usulan").val() : 0);

      if(jangkaWaktuKredit > 0 && jangkaWaktuUsulan > 0){
        if(jangkaWaktuUsulan < jangkaWaktuKredit){
          console.log(`usulan: ${jangkaWaktuUsulan}`);
          console.log(`Kredit: ${jangkaWaktuKredit}`);
          $(".info_jangka_waktu_usulan").empty()
          $(".info_jangka_waktu_usulan").append(`
          <div class="alert alert-danger" role="alert">
              Jangka Waktu Usulan Tidak Boleh Lebih Kecil Daripada Jangka Waktu Kredit
          </div>
          `)
          $("#btnSimpanPerhitungan").attr("disabled", true)
        } else{
          $(".info_jangka_waktu_usulan").empty()
          $("#btnSimpanPerhitungan").removeAttr("disabled")
        }
      } else {
        $(".info_jangka_waktu_usulan").empty()
        $("#btnSimpanPerhitungan").removeAttr("disabled")
      }
    }
    document.getElementById('toggleButton').addEventListener('click', function() {
        var table = document.getElementById('collapseExample');
        if (table.classList.contains('hidden')) {
            table.classList.remove('hidden');
            table.classList.add('block');
            table.classList.remove('collapse');
        } else {
            table.classList.add('hidden');
        }
    });
    document.addEventListener('click', function(e) {
        var navsearch = document.querySelector('.collapse');
        // if (!navsearch.contains(e.target) && navsearch.classList.contains('block')) {
        //     navsearch.classList.removeClass('hidden');
        // }
    });
    $("[data-dismiss-id]").on("click", function () {
        var dismissId = $(this).data("dismiss-id");
        console.log(dismissId)
        setTimeout(function () {
            $(".modal").css("animation", "swipe-out 0.2s ease-in-out");
            $(".modal-layout").css("animation", "opacity-out 0.2s cubic-bezier(0.17, 0.67, 0.83, 0.67)");
        }, 200);
        setTimeout(function () {
            $("#" + dismissId).addClass("hidden");
        }, 400);
        })
  </script>
  <style>
    .modal-lg {
      max-width: 90% !important;
    }
  </style>
