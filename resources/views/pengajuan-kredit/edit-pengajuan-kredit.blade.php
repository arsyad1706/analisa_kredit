@extends('layouts.template')
@section('content')
@include('components.notification')
<style>
    .form-wizard .sub label:not(.info){
        font-weight: 400;
    }
    .form-wizard h4{
        color: #1f1d62;
        font-weight: 600 !important;
        font-size: 20px;
        /* border-bottom: 1px solid #dc3545; */
    }
    .form-wizard h5{
        color: #1f1d62;
        font-weight: 600 !important;
        font-size: 18px;
        /* border-bottom: 1px solid #dc3545; */
    }
    .form-wizard h6{
        color: #c2c7cf;
        font-weight: 600 !important;
        font-size: 16px;
        /* border-bottom: 1px solid #dc3545; */
    }
</style>
<form id="pengajuan_kredit" action="{{ route('pengajuan-kredit.store') }}" method="post">
    @csrf
    <div class="form-wizard active" data-index='0' data-done='true'>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="">Nama Lengkap</label>
                <input type="text" name="name" id="nama" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$dataUmum->nama) }}" placeholder="Nama sesuai dengan KTP">
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
                        <option value="{{ old('id',$item->id) }}" {{ old('id',$item->id) == $dataUmum->id_kabupaten ? 'selected' : '' }}>{{ $item->kabupaten }}</option>
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
                        <option value="{{ $kec->id }}" {{$kec->id == $dataUmum->id_kecamatan ? 'selected' : ''}}>{{ $kec->kecamatan }}</option>
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
                <select name="desa" id="desa" class="form-control @error('desa') is-invalid @enderror select2" >
                    <option value="">---Pilih Desa----</option>
                    @foreach ($allDesa as $desa)
                        <option value="{{ $desa->id }}" {{$desa->id == $dataUmum->id_kecamatan ? 'selected' : ''}}>{{ $desa->desa }}</option>
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
                <textarea name="alamat_rumah" class="form-control @error('alamat_rumah') is-invalid @enderror"  id="" cols="30" rows="4" placeholder="Alamat Rumah disesuaikan dengan KTP">{{ old('alamat_rumah',$dataUmum->alamat_rumah) }}</textarea>
                @error('alamat_rumah')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                <hr>
            </div>
            <div class="form-group col-md-12">
                <label for="">Alamat Usaha</label>
                <textarea name="alamat_usaha" class="form-control @error('alamat_usaha') is-invalid @enderror"  id="" cols="30" rows="4" placeholder="Alamat Usaha disesuaikan dengan KTP">{{ old('alamat_usaha',$dataUmum->alamat_usaha) }}</textarea>
                @error('alamat_usaha')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">No. KTP</label>
                <input type="text" name="no_ktp" class="form-control @error('no_ktp') is-invalid @enderror" id="" value="{{ old('no_ktp',$dataUmum->no_ktp) }}" placeholder="Masukkan 16 digit No. KTP">
                @error('no_ktp')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="">Tempat</label>
                <input type="text" name="tempat_lahir" id="" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir',$dataUmum->tempat_lahir) }}" placeholder="Tempat Lahir">
                @error('tempat_lahir')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir',$dataUmum->tanggal_lahir) }}" placeholder="Tempat Lahir">
                @error('tanggal_lahir')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="">Status</label>
                <select name="status" id="" class="form-control @error('status') is-invalid @enderror select2" >
                    <option value=""> --Pilih Status --</option>
                    <option value="menikah" {{ old('status',$dataUmum->status) == "menikah" ? 'selected' : '' }}>Menikah</option>
                    <option value="belum menikah" {{ old('status',$dataUmum->status) == "belum menikah" ? 'selected' : '' }}>Belum Menikah</option>
                    <option value="duda" {{ old('status',$dataUmum->status) == "duda" ? 'selected' : '' }}>Duda</option>
                    <option value="janda" {{ old('status',$dataUmum->status) == "janda" ? 'selected' : '' }}>Janda</option>
                </select>
                @error('status')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Sektor Kredit</label>
                <select name="sektor_kredit" id="" class="form-control @error('sektor_kredit') is-invalid @enderror select2" >
                    <option value=""> --Pilih Sektor Kredit -- </option>
                    <option value="perdagangan" {{ old('sektor_kredit',$dataUmum->sektor_kredit) == "perdagangan" ? 'selected' : '' }}>Perdagangan</option>
                    <option value="perindustrian" {{ old('sektor_kredit',$dataUmum->sektor_kredit) == "perindustrian" ? 'selected' : '' }}>Perindustrian</option>
                    <option value="dll" {{ old('sektor_kredit',$dataUmum->sektor_kredit) == "dll" ? 'selected' : '' }}>dll</option>
                </select>
                @error('sektor_kredit')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Jenis Usaha</label>
                <textarea name="jenis_usaha" class="form-control @error('jenis_usaha') is-invalid @enderror" id="" cols="30" rows="4" placeholder="Jenis Usaha secara spesifik">{{ old('jenis_usaha',$dataUmum->jenis_usaha) }}</textarea>
                @error('jenis_usaha')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Jumlah Kredit yang diminta</label>
                <textarea name="jumlah_kredit" class="form-control @error('jumlah_kredit') is-invalid @enderror"  id="" cols="30" rows="4" placeholder="Jumlah Kredit">{{ old('jumlah_kredit',$dataUmum->jumlah_kredit) }}</textarea>
                @error('jumlah_kredit')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Tujuan Kredit</label>
                <textarea name="tujuan_kredit" class="form-control @error('tujuan_kredit') is-invalid @enderror" id="" cols="30" rows="4" placeholder="Tujuan Kredit">{{ old('tujuan_kredit',$dataUmum->tujuan_kredit) }}</textarea>
                @error('tujuan_kredit')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Jaminan yang disediakan</label>
                <textarea name="jaminan" class="form-control @error('jaminan') is-invalid @enderror"  id="" cols="30" rows="4" placeholder="Jaminan yang disediakan">{{ old('jaminan',$dataUmum->jaminan_kredit) }}</textarea>
                @error('jaminan')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Hubungan Bank</label>
                <textarea name="hubungan_bank" class="form-control @error('hubungan_bank') is-invalid @enderror"  id="" cols="30" rows="4" placeholder="Hubungan dengan Bank">{{ old('hubungan_bank',$dataUmum->hubungan_bank) }}</textarea>
                @error('hubungan_bank')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group col-md-12">
                <label for="">Hasil Verifikasi</label>
                <textarea name="hasil_verifikasi" class="form-control @error('hasil_verifikasi') is-invalid @enderror" id="" cols="30" rows="4" placeholder="Hasil Verifikasi Karakter Umum">{{ old('hasil_verifikasi',$dataUmum->verifikasi_umum) }}</textarea>
                @error('hasil_verifikasi')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
    </div>
    <input type="text" id="jumlahData" name="jumlahData" hidden value="{{ count($dataAspek) }}">
    @foreach ($dataAspek as $key => $value)
        @php
            $key += 1;
            // check level 2
            $dataLevelDua = \App\Models\ItemModel::select('id','nama','level','id_parent')->where('level',2)->where('id_parent',$value->id)->get();

            // check level 4
            $dataLevelEmpat = \App\Models\ItemModel::select('id','nama','level','id_parent')->where('level',4)->where('id_parent',$value->id)->get();
        @endphp
        {{-- level level 2 --}}
        <div class="form-wizard" data-index='{{ $key }}' data-done='true'>
            <div class="row">
                @foreach ($dataLevelDua as $item)
                    @php
                        $dataJawaban = \App\Models\OptionModel::where('option',"!=","-")->where('id_item',$item->id)->get();
                        $dataOption = \App\Models\OptionModel::where('option',"=","-")->where('id_item',$item->id)->get();
                        // check level 3
                        $dataLevelTiga = \App\Models\ItemModel::select('id','nama','level','id_parent')->where('level',3)->where('id_parent',$item->id)->get();
                    @endphp
                    @foreach ($dataOption as $itemOption)
                        @if ($itemOption->option == "-")
                            <div class="form-group col-md-12">
                                <h4>{{ $item->nama }}</h4>
                            </div>
                        @endif
                    @endforeach

                    @if (count($dataJawaban) != 0)
                        <div class="form-group col-md-6">
                            <label for="">{{  $item->nama }}</label>
                            <select name="dataLevelDua[]" id="dataLevelDua" class="form-control">
                                    <option value=""> --Pilih Data -- </option>
                                @foreach ($dataJawaban as $key => $itemJawaban)
                                    @php
                                        $dataDetailJawaban = \App\Models\JawabanPengajuanModel::select('id_jawaban')->where('id_pengajuan',$dataUmum->id)->get();
                                    @endphp
                                    @foreach ($dataDetailJawaban as $key => $itemDetail)
                                        @if ($itemDetail->id_jawaban == $itemJawaban->id)
                                            <option value="{{ $itemJawaban->skor."-".$itemJawaban->id }}" selected >{{ $itemJawaban->option }}</option>
                                        @else
                                        @endif
                                    @endforeach
                                        <option value="{{ $itemJawaban->skor."-".$itemJawaban->id }}" >{{ $itemJawaban->option }}</option>
                                @endforeach
                                </select>

                            @if(isset($key)&&$errors->has('dataLevelDua.'.$key))
                                <div class="invalid-feedback">
                                    {{ $errors->first('dataLevelDua.'.$key) }}
                                </div>
                            @endif
                        </div>

                    @endif
                    @foreach ($dataLevelTiga as $keyTiga => $itemTiga)
                        @php
                            // check  jawaban level tiga
                            $dataJawabanLevelTiga = \App\Models\OptionModel::where('option',"!=","-")->where('id_item',$itemTiga->id)->get();
                            $dataOptionTiga = \App\Models\OptionModel::where('option',"=","-")->where('id_item',$itemTiga->id)->get();
                            // check level empat
                            $dataLevelEmpat = \App\Models\ItemModel::select('id','nama','level','id_parent')->where('level',4)->where('id_parent',$itemTiga->id)->get();
                        @endphp

                        @foreach ($dataOptionTiga as $itemOptionTiga)
                          @if ($itemOptionTiga->option == "-")
                              <div class="form-group col-md-12">
                                  <h5>{{ $itemTiga->nama }}</h5>
                              </div>
                          @endif
                        @endforeach
                        @if (count($dataJawabanLevelTiga) != 0)
                            <div class="form-group col-md-6">
                                <label for="">{{ $itemTiga->nama }}</label>
                                <select name="dataLevelTiga[]" id="" class="form-control">
                                    <option value=""> --Pilih Opsi-- </option>
                                    @foreach ($dataJawabanLevelTiga as $itemJawabanTiga)
                                        <option value="{{ $itemJawabanTiga->skor.'-'.$itemJawabanTiga->id }}" {{ $itemJawabanTiga->id == 18 ? 'selected' : '' }}>{{ $itemJawabanTiga->option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @foreach ($dataLevelEmpat as $keyEmpat => $itemEmpat)
                            @php
                                // check level empat
                                $dataJawabanLevelEmpat = \App\Models\OptionModel::where('option',"!=","-")->where('id_item',$itemEmpat->id)->get();
                                $dataOptionEmpat = \App\Models\OptionModel::where('option',"=","-")->where('id_item',$itemEmpat->id)->get();
                            @endphp

                            @foreach ($dataOptionEmpat as $itemOptionEmpat)
                             @if ($itemOptionEmpat->option == "-")
                                 <div class="form-group col-md-12">
                                     <h6>{{ $itemEmpat->nama }}</h6>
                                 </div>
                             @endif
                            @endforeach

                            @if (count($dataJawabanLevelEmpat) != 0)
                                <div class="form-group col-md-6">
                                    <label for="">{{ $itemEmpat->nama }}</label>
                                    <option value=""> --Pilih Opsi -- </option>
                                    <select name="dataLevelEmpat[]" id="" class="form-control">
                                        @foreach ($dataJawabanLevelEmpat as $itemJawabanEmpat)
                                            <option value="{{ $itemJawabanEmpat->skor.'-'.$itemJawabanEmpat->id }}" >{{ $itemJawabanEmpat->option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                @endforeach
            </div>
            {{-- @php
                echo "<pre>";
                print_r($dataLevelSatu);
                echo "</pre>";
            @endphp --}}
        </div>

    @endforeach
    <div class="row form-group">
        <div class="col text-right">
            <button class="btn btn-default btn-prev"><span class="fa fa-chevron-left"></span> Sebelumnya</button>
            <button class="btn btn-danger btn-next">Selanjutnya <span class="fa fa-chevron-right"></span></button>
            <button type="submit" class="btn btn-info btn-simpan" id="submit">Simpan <span class="fa fa-save"></span></button>
            {{-- <button class="btn btn-info ">Simpan <span class="fa fa-chevron-right"></span></button> --}}
        </div>
    </div>
</form>

@endsection

@push('custom-script')
<script>
        $('#kabupaten').change(function(){
    var kabID = $(this).val();
        if(kabID){
            $.ajax({
            type:"GET",
            url:"/getkecamatan?kabID="+kabID,
            dataType: 'JSON',
            success:function(res){
                //    console.log(res);
                if(res){
                    $("#kecamatan").empty();
                    $("#desa").empty();
                    $("#kecamatan").append('<option>---Pilih Kecamatan---</option>');
                    $("#desa").append('<option>---Pilih Desa---</option>');
                    $.each(res,function(nama,kode){
                        $("#kecamatan").append('<option value="'+kode+'">'+nama+'</option>');
                    });
                }else{
                $("#kecamatan").empty();
                $("#desa").empty();
                }
            }
            });
        }else{
            $("#kecamatan").empty();
            $("#desa").empty();
        }
    });

    $('#kecamatan').change(function(){
        var kecID = $(this).val();
        // console.log(kecID);
        if(kecID){
            $.ajax({
            type:"GET",
            url:"/getdesa?kecID="+kecID,
            dataType: 'JSON',
            success:function(res){
                //    console.log(res);
                if(res){
                    $("#desa").empty();
                    $("#desa").append('<option>---Pilih Desa---</option>');
                    $.each(res,function(nama,kode){
                        $("#desa").append('<option value="'+kode+'">'+nama+'</option>');
                    });
                }else{
                $("#desa").empty();
                }
            }
            });
        }else{
            $("#desa").empty();
        }
    });
</script>
<script>
    var jumlahData = $('#jumlahData').val();
    // console.log(jumlahData);
    function cekBtn(){
        var indexNow = $(".form-wizard.active").data('index')
        var prev = parseInt(indexNow) - 1
        var next = parseInt(indexNow) + 1

        $(".btn-prev").hide()
        $(".btn-simpan").hide()

        if($(".form-wizard[data-index='"+prev+"']").length==1){
            $(".btn-prev").show()
        }
        if (indexNow == jumlahData) {

            $(".btn-next").hide()
            $(".btn-simpan").show()

            // $('#pengajuan_kredit').on('submit',function(e){
            //     e.preventDefault();
            //     let name = $('#nama').val();
            //     console.log(name);
            //     $.ajax({
            //         url: "/pengajuan-kredit",
            //         type:"POST",
            //         data:{
            //             "_token": "{{ csrf_token() }}",
            //         },
            //     })
            //     // console.log('berhasil');
            // });

        }else{
            $(".btn-next").show()
            $(".btn-simpan").hide()

        }
    }
    function cekWizard(isNext=false){
        var indexNow = $(".form-wizard.active").data('index')
        // console.log(indexNow);
        if(isNext){
            $(".side-wizard li").removeClass('active')
        }

        $(".side-wizard li").removeClass('selected')

        for (let index = 0; index <= parseInt(indexNow); index++) {
            var selected = index==parseInt(indexNow) ? ' selected' : ''
            $(".side-wizard li[data-index='"+index+"']").addClass('active'+selected)
            $(".side-wizard li[data-index='"+index+"'] a span i").removeClass('fa fa-ban')
            if($(".side-wizard li[data-index='"+index+"'] a span i").html()=='' || $(".side-wizard li[data-index='"+index+"'] a span i").html()=='0%'){
                $(".side-wizard li[data-index='"+index+"'] a span i").html('0%')
            }
        }

    }
    cekBtn()
    cekWizard()

    $(".side-wizard li a").click(function(){
        var dataIndex = $(this).closest('li').data('index')
        if($(this).closest('li').hasClass('active')){
            $(".form-wizard").removeClass('active')
            $(".form-wizard[data-index='"+dataIndex+"']").addClass('active')
            cekWizard()
        }
    })

    function setPercentage(formIndex){
        // console.log(formIndex);
        var form = ".form-wizard[data-index='"+formIndex+"']"

        var input = $(form+" input")
        var select = $(form+" select")
        var textarea = $(form+" textarea")

        var ttlInput = 0;
        var ttlInputFilled=0;
        $.each(input, function(i,v){
            ttlInput++
            if(v.value!=''){
                ttlInputFilled++
            }
        })
        var ttlSelect = 0;
        var ttlSelectFilled=0;
        $.each(select, function(i,v){
            ttlSelect++
            if(v.value!=''){
                ttlSelectFilled++
            }
        })

        var ttlTextarea = 0;
        var ttlTextareaFilled=0;
        $.each(textarea, function(i,v){
            ttlTextarea++
            if(v.value!=''){
                ttlTextareaFilled++
            }
        })

        var allInput = ttlInput + ttlSelect + ttlTextarea
        var allInputFilled = ttlInputFilled + ttlSelectFilled + ttlTextareaFilled

        var percentage = parseInt(allInputFilled/allInput * 100);
        $(".side-wizard li[data-index='"+formIndex+"'] a span i").html(percentage+"%")
    }

    $(".btn-next").click(function(e){
        e.preventDefault();
        var indexNow = $(".form-wizard.active").data('index')
        var next = parseInt(indexNow) + 1
        // console.log($(".form-wizard[data-index='"+next+"']").length==1);
        // console.log($(".form-wizard[data-index='"+  +"']"));
        if($(".form-wizard[data-index='"+next+"']").length==1){
            // console.log(indexNow);
            $(".form-wizard").removeClass('active')
            $(".form-wizard[data-index='"+next+"']").addClass('active')
            $(".form-wizard[data-index='"+indexNow+"']").attr('data-done','true')
        }


        cekWizard()
        cekBtn(true)
        setPercentage(indexNow)
    })
    setPercentage(0)

    $(".btn-prev").click(function(e){
        event.preventDefault(e);
        var indexNow = $(".form-wizard.active").data('index')
        var prev = parseInt(indexNow) - 1
        if($(".form-wizard[data-index='"+prev+"']").length==1){
            $(".form-wizard").removeClass('active')
            $(".form-wizard[data-index='"+prev+"']").addClass('active')
        }
        cekWizard()
        cekBtn()
        e.preventDefault();
    })

</script>
@endpush
