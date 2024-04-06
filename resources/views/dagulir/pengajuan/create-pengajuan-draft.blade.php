@push('script-inject')
    <script>
        cekStatusNikah()
        $('#jangka_waktu').on('change', function() {
            limitJangkaWaktu()
        })
        $('#jumlah_kredit').on('change', function() {
            limitJangkaWaktu()
        })

        $('#tanggal_lahir').on('change',function() {
            var tanggal  = $(this).val();
            var today = new Date();
            var birthday = new Date(tanggal);
            var year = 0;
            if (today.getMonth() < birthday.getMonth()) {
                year = 1;
            } else if ((today.getMonth() == birthday.getMonth()) && today.getDate() < birthday.getDate()) {
                year = 1;
            }
            var age = today.getFullYear() - birthday.getFullYear() - year;

            if(age < 0){
                age = 0;
            }
            if (age >= 17) {
                $('#pesan-usia').addClass('hidden');
            }else{
                $('#pesan-usia').removeClass('hidden');
            }
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
        function cekStatusNikah() {
            let value = $("#status").val();
            console.log(value);
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
                    <input type="file" name="upload_file[{{ $itemKTPIs->id }}]" data-id="" placeholder="Masukkan informasi {{ $itemKTPIs->nama }}" class="form-input limit-size" id="foto_ktp_istri">
                    <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
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
                        <input type="file" name="upload_file[{{ $itemKTPSu->id }}]" data-id="" placeholder="Masukkan informasi {{ $itemKTPSu->nama }}" class="form-input limit-size" id="foto_ktp_suami">
                        <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
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
                    <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                                data-title="Foto KTP Nasabah" data-filepath="{{asset('../upload/temp')}}/{{$itemKTPNas->id}}/{{temporary($duTemp->id, $itemKTPNas->id)?->opsi_text}}">Preview</a>
                    <input type="hidden" name="id_item_file[{{ $itemKTPNas->id }}]" value="{{ $itemKTPNas->id }}" id="">
                    <input type="file" name="upload_file[{{ $itemKTPNas->id }}]" value="{{ temporary($duTemp->id, $itemKTPNas->id)?->opsi_text }}" data-id="" placeholder="Masukkan informasi {{ $itemKTPNas->nama }}" class="form-input limit-size" id="foto_ktp_nasabah">
                    <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
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
                    <input type="file" name="upload_file[{{ $itemKTPIs->id }}]" data-id="{{ temporary($duTemp->id, $itemKTPIs->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPIs->nama }}" class="form-input limit-size" id="foto_ktp_istri">
                    <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
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
                        <input type="file" name="upload_file[{{ $itemKTPSu->id }}]" data-id="" placeholder="Masukkan informasi {{ $itemKTPSu->nama }}" class="form-input limit-size" id="foto_ktp_suami">
                        <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
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
                    <input type="file" name="upload_file[{{ $itemKTPNas->id }}]" value="{{ temporary($duTemp->id, $itemKTPNas->id)?->opsi_text }}" data-id="{{ temporary($duTemp->id, $itemKTPNas->id)?->id }}" placeholder="Masukkan informasi {{ $itemKTPNas->nama }}" class="form-input limit-size" id="foto_ktp_nasabah">
                    <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
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
    </script>
@endpush
<div class="pb-10 space-y-3">
    <h2 class="text-4xl font-bold tracking-tighter text-theme-primary">Data Umum</h2>
    <p class="font-semibold text-gray-400">Draft Pengajuan</p>
</div>
<div class="self-start bg-white w-full border">

    <div class="p-5 border-b">
        <h2 class="font-bold text-lg tracking-tighter">
            Pengajuan Masuk
        </h2>
    </div>
    <!-- data umum -->
    <div
        class="p-5 w-full space-y-5 "
        id="data-umum"
    >
        <div class="form-group-1 col-span-2">
            <div>
                <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
                    <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                        Data Diri :
                    </h2>
                </div>
            </div>
        </div>
        <input type="hidden" name="skema_kredit" id="skema_kredit" @if ($skema != null) value="{{ $skema ?? '' }}"
        @elseif($duTemp->skema_kredit != null) value="{{ $duTemp->skema_kredit ?? '' }}" @endif>
        <div class="form-group-1">
            <div class="input-box">
                <label for="">Nama Lengkap</label>
                <input type="text" name="name" id="nama" class="form-input @error('name') is-invalid @enderror"
                    placeholder="Nama sesuai dengan KTP" value="{{ $duTemp?->nama ?? "" }}"  maxlength="255">
                @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

        </div>
        <div class="form-group-3">
            <div class="input-box">
                <label for="">Kabupaten</label>
                <select name="kabupaten" class="form-select @error('kabupaten') is-invalid @enderror select2"
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
            <div class="input-box">
                <label for="">Kecamatan</label>
                <select name="kec" id="kecamatan" class="form-select @error('kec') is-invalid @enderror  select2">
                    <option value="0">---Pilih Kecamatan----</option>
                </select>
                @error('kec')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Desa</label>
                <select name="desa" id="desa" class="form-select @error('desa') is-invalid @enderror select2">
                    <option value="0">---Pilih Desa----</option>
                </select>
                @error('desa')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
        <div class="input-box">
            <label for="">No Telp</label>
            <input type="text" name="no_telp" id="nama" class="form-input @error('no_telp') is-invalid @enderror"
                placeholder="No Telp" value="{{ $duTemp?->no_telp ?? '' }}"  maxlength="255">
            @error('no_telp')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="input-box">
            <label for="">Alamat Rumah</label>
            <textarea name="alamat_rumah" class="form-textarea  @error('alamat_rumah') is-invalid @enderror"
                maxlength="255" id="alamat_rumah" cols="30" rows="4"
                placeholder="Alamat Rumah disesuaikan dengan KTP">{{ $duTemp?->alamat_rumah ?? '' }}</textarea>
            @error('alamat_rumah')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
            <hr>
        </div>

        <div class="form-group-3">
            <div class="input-box">
                <label for="">Tempat Lahir</label>
                <input type="text" maxlength="255" name="tempat_lahir" id="tempat_lahir"
                    class="form-input @error('tempat_lahir') is-invalid @enderror" placeholder="Tempat Lahir"
                    value="{{ $duTemp?->tempat_lahir ?? '' }}">
                @error('tempat_lahir')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                    class="form-input @error(
                    'tanggal_lahir') is-invalid @enderror"
                    placeholder="dd-mm-yyyy" value="{{ $duTemp->tanggal_lahir }}">
                @error('tanggal_lahir')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Status</label>
                <select name="status" id="status" class="form-input @error('status') is-invalid @enderror select2">
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
        </div>
        <div class="input-box col-md-12">
            <label for="">No. KTP</label>
            <input type="number" maxlength="16"
                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                onkeydown="return event.keyCode !== 69" name="no_ktp" class="form-input @error('no_ktp') is-invalid @enderror"
                id="no_ktp" placeholder="Masukkan 16 digit No. KTP" value="{{ $duTemp?->no_ktp ?? '' }}">
            @error('no_ktp')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="input-box" id="foto-ktp-suami">
        </div>
        <div class="input-box" id="foto-ktp-istri">
        </div>
        <div class="input-box" id="foto-ktp-nasabah">
        </div>
        <div>
            <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
                <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                    Data usaha :
                </h2>
            </div>
        </div>
        <div class="input-box ">
            <label for="">Jenis Usaha</label>
            <textarea name="jenis_usaha" class="form-textarea  @error('jenis_usaha') is-invalid @enderror"
                maxlength="255" id="" cols="30" rows="4" placeholder="Jenis Usaha secara spesifik">{{ $duTemp?->jenis_usaha ?? '' }}</textarea>
            @error('jenis_usaha')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="input-box">
            <label for="">Alamat Usaha</label>
            <textarea name="alamat_usaha" class="form-textarea @error('alamat_usaha') is-invalid @enderror"
                maxlength="255" id="alamat_usaha" cols="30" rows="4" placeholder="Alamat Usaha">{{ $duTemp?->alamat_usaha ?? '' }}</textarea>
            @error('alamat_usaha')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div>
            <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
                <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                    Slik :
                </h2>
            </div>
        </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="">{{ $itemSlik->nama }}</label>
                <select name="dataLevelDua[{{ $itemSlik->id }}]" id="dataLevelDua" class="form-select select2"
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
            <div class="input-box">
                <label for="">{{ $itemP->nama }}</label>
                @php
                    $fileLapSlik = "";
                @endphp
                @if ($jawabanLaporanSlik)
                    @if ($jawabanLaporanSlik->opsi_text)
                        @php
                            $fileLapSlik = $jawabanLaporanSlik->opsi_text;
                        @endphp
                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                            data-title="{{$itemP->nama}}" data-filepath="{{asset('../upload/temp')}}/{{$jawabanLaporanSlik->id_jawaban}}/{{$jawabanLaporanSlik->opsi_text}}" data-extension="{{ explode('.', $jawabanLaporanSlik->opsi_text)[1] }}">Preview</a>
                    @endif
                @endif
                <input type="hidden" name="id_item_file[{{ $itemP->id }}]" value="{{ $itemP->id }}" id="">
                @if ($jawabanLaporanSlik)
                    @if ($jawabanLaporanSlik->opsi_text)
                        <input type="file" name="upload_file[{{ $itemP->id }}]" id="file_slik" data-id=""
                            placeholder="Masukkan informasi {{ $itemP->nama }}" class="form-input limit-size-slik" value="{{ $jawabanLaporanSlik->opsi_text }}">
                    @else
                        <input type="file" name="upload_file[{{ $itemP->id }}]" id="file_slik" data-id=""
                        placeholder="Masukkan informasi {{ $itemP->nama }}" class="form-input limit-size-slik" value="">
                    @endif
                @else
                    <input type="file" name="upload_file[{{ $itemP->id }}]" id="file_slik" data-id=""
                    placeholder="Masukkan informasi {{ $itemP->nama }}" class="form-input limit-size-slik" value="">
                @endif
                <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 10 MB</span>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
                <span class="filename" style="display: inline;"></span>
                {{-- <span class="alert alert-danger">Maximum file upload is 5 MB</span> --}}
            </div>
        </div>
        <div class="form-group-1">
            <div class="input-box">
                <label for="">{{ $itemCatatanSlik->nama }}</label><small class="text-red-500 font-bold">*</small>
                <textarea
                    name="catatan_slik"
                    class="form-textarea"
                    placeholder="{{ $itemCatatanSlik->nama }}"
                    id=""
                >{{ edit_dagulir($duTemp->id, $itemCatatanSlik->id)?->opsi_text }}</textarea>
            </div>
            @if (isset($key) && $errors->has('dataLevelDua.' . $key))
            <div class="invalid-feedback">
                {{ $errors->first('dataLevelDua.' . $key) }}
            </div>
            @endif
        </div>
        <div>
            <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
                <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                    Data pengajuan :
                </h2>
            </div>
        </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="">{{ $itemSP->nama }}</label>
                @php
                    $fileLapPemohonan = "";
                @endphp
                @if ($itemSP)
                    @if (temporary($duTemp->id, $itemSP->id)?->opsi_text)
                        @php
                            $fileLapPemohonan = temporary($duTemp->id, $itemSP->id)?->opsi_text;
                        @endphp
                            <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                data-title="{{$itemP->nama}}" data-filepath="{{asset('../upload/temp')}}/{{$itemSP->id}}/{{temporary($duTemp->id, $itemSP->id)?->opsi_text}}" >Preview</a>
                    @endif
                @endif
                <input type="hidden" name="id_item_file[{{ $itemSP->id }}]" value="{{ $itemSP->id }}" id="">
                <input type="file" name="upload_file[{{ $itemSP->id }}]" data-id="{{ temporary($duTemp->id, $itemSP->id)?->id }}"
                    value="{{ temporary($duTemp->id, $itemSP->id)?->opsi_text }}"
                    placeholder="Masukkan informasi {{ $itemSP->nama }}" class="form-input limit-size" id="foto_sp">
                <span class="invalid-tooltip" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
                <span class="filename" style="display: inline;"></span>
            </div>
            <div class="input-box ">
                <label for="">Sektor Kredit</label>
                <select name="sektor_kredit" id="sektor_kredit"
                    class="form-select @error('sektor_kredit') is-invalid @enderror select2">
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

            <div class="input-box">
                <label for="">Jumlah Kredit yang diminta</label>
                <input type="text" name="jumlah_kredit" id="jumlah_kredit" class="form-input rupiah" value="{{ rupiah($duTemp?->jumlah_kredit) ?? '' }}">

                @error('jumlah_kredit')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Tenor Yang Diminta</label>
                <div class="flex items-center">
                    <div class="flex-1">
                        <input type="text" name="tenor_yang_diminta" id="tenor_yang_diminta"
                            class="form-input only-number @error('tenor_yang_diminta') is-invalid @enderror"
                            aria-describedby="addon_tenor_yang_diminta"  maxlength="3" value="{{ $duTemp?->tenor_yang_diminta }}" />
                    </div>
                    <div class="flex-shrink-0 mt-2.5rem">
                        <span class="form-input bg-gray-100">Bulan</span>
                    </div>
                </div>
                @error('tenor_yang_diminta')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
        <div class="input-box">
            <label for="">Tujuan Kredit</label>
            <textarea name="tujuan_kredit" class="form-textarea  @error('tujuan_kredit') is-invalid @enderror"
                maxlength="255" id="tujuan_kredit" cols="30" rows="4" placeholder="Tujuan Kredit">{{ $duTemp?->tujuan_kredit ?? '' }}</textarea>
            @error('tujuan_kredit')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="input-box">
            <label for="">Jaminan yang disediakan</label>
            <textarea name="jaminan" class="form-textarea  @error('jaminan') is-invalid @enderror" maxlength="255"
                id="" cols="30" rows="4" placeholder="Jaminan yang disediakan">{{ $duTemp?->jaminan_kredit }}</textarea>
            @error('jaminan')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="input-box">
            <label for="">Hubungan Bank</label>
            <textarea name="hubungan_bank" class="form-textarea  @error('hubungan_bank') is-invalid @enderror"
                maxlength="255" id="hubungan_bank" cols="30" rows="4" placeholder="Hubungan dengan Bank">{{ $duTemp?->hubungan_bank }}</textarea>
            @error('hubungan_bank')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="input-box">
            <label for="">Hasil Verifikasi</label>
            <textarea name="hasil_verifikasi" class="form-textarea  @error('hasil_verifikasi') is-invalid @enderror"
                maxlength="255" id="hasil_verivikasi" cols="30" rows="4"
                placeholder="Hasil Verifikasi Karakter Umum">{{ $duTemp?->verifikasi_umum }}</textarea>
            @error('hasil_verifikasi')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="flex justify-between">
            <a href="{{route('pengajuan-kredit.index')}}">
                <button type="button"
                    class="px-5 py-2 border rounded bg-white text-gray-500">
                    Kembali
                </button>
            </a>
            <button type="button"
            class="px-5 py-2 next-tab border rounded bg-theme-primary text-white"
            >
            Selanjutnya
            </button>
        </div>
    </div>
</div>
