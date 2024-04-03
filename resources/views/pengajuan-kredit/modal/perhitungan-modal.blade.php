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
    .select2 {
        width: 100% !important;
    }

}
</style>
@php
  $lev1 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)->where('level', 1)->get();
@endphp
<div class="modal fade" id="perhitunganModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div id="loading-simpan-perhitungan" style="display: none;" class="text-center">
    <img src="{{ asset('img/loading3.gif') }}" alt="Loading..." style="width: 100px; height: 100px;">
  </div>
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" id="perhitunganModalAfterLoading">
          <div class="modal-header">
              <h5 class="modal-title">Perhitungan</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <!-- form -->
              <form id="form-perhitungan" method="" action="">
                {{--  <div class="row">  --}}
                  <!-- content -->
                    <div class="row">
                      <!-- pilih bulan -->
                      <div class="col-md-6">
                        <div class="form-group mb-4">
                          <label for="inputHarta" class="font-weight-semibold">Pilih Periode :</label>
                          <select name="" style="width: 100%; height: 40px" class="select-date" id="periode" onchange="calcForm()">
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
                      <div class="col-md-6">
                          <div class="form-group mb-4">
                              @php
                                  $start_year = 2010;
                                  $end_year = date('Y');
                              @endphp
                              <label for="periode_tahun" class="font-weight-semibold">Pilih Periode Tahun :</label>
                              <select name="periode_tahun" id="periode_tahun" style="width: 100%; height: 40px" class="select-date">
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
                      <div class="card mb-4">
                        <h4 class="card-header">{{ $item->field }}</h4>
                        <div class="card-body">
                          @php
                            $lev2 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                        ->where('level', 2)
                                                                        ->where('parent_id', $item->id)
                                                                        ->get();
                          @endphp
                          <div class="row grid grid-cols-2">
                            @foreach ($lev2 as $item2)
                              @php
                                $lev3 = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                            ->where('level', 3)
                                                                            ->where('parent_id', $item2->id)
                                                                            ->get();
                              @endphp
                              <div class="col-md-6">
                                <div class="card mb-3">
                                    <h5 class="card-header">{{$item2->field}}</h5>
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
                          </div>
                        </div>
                      </div>
                      {{-- <div class="head">
                          <h4 class="mb-4 font-weight-bold" style="letter-spacing: -1px">
                              {{$item->field}}
                          </h4>
                      </div> --}}
                      <!-- form bagian level 2 -->
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
                                      <input type="{{ $item3NoParent->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3NoParent->id}}" name="inpLevelTigaParent[{{$item3NoParent->id}}]"
                                      id="inp_{{$item3NoParent->id}}" data-formula="{{$item3NoParent->formula}}" data-detail="{{$item3NoParent->have_detail}}"
                                      @if ($item3NoParent->readonly) readonly @endif onkeyup="calcForm()"/>
                                        @if ($item3NoParent->add_on)
                                          <div class="input-group-append">
                                              <span class="input-group-text" id="basic-addon2">{{$item3NoParent->add_on}}</span>
                                          </div>
                                        @endif
                                    </div>
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
                                        <input type="{{ $item3NoParent->is_hidden ? 'hidden' : 'text' }}" class="form-control rupiah inp_{{$item3NoParent->id}}"  name="inpLevelTigaParent[{{$item3NoParent->id}}]"
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
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                  Batal
              </button>
              <button type="button" class="btn btn-danger" id="btnSimpanPerhitungan">
                  Simpan
              </button>
          </div>
      </div>
  </div>
</div>

<script>
  var selectDate = $(".select-date");
  selectDate.select2();
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
</script>
<style>
  .modal-lg {
    max-width: 90% !important;
  }
</style>
