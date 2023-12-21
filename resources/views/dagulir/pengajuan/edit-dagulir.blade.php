@push('script-inject')
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
    </script>
@endpush
<div class="pb-10 space-y-3">
    <h2 class="text-4xl font-bold tracking-tighter text-theme-primary">Dagulir</h2>
    <p class="font-semibold text-gray-400">Edit Pengajuan</p>
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
        <div class="form-group-2">
            <div class="input-box">
                <label for="">Nama Lengkap</label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukkan Nama"
                    name="nama_lengkap"
                    value="{{ old('nama_lengkap', $data->nama) }}"
                />
                @error('nama_lengkap')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Email</label>
                <input
                type="email"
                class="form-input"
                placeholder="Masukkan Email"
                name="email"
                value="{{ old('email', $data->email) }}"
                />
                @error('email')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="">Tempat lahir</label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukkan Tempat Lahir"
                    name="tempat_lahir"
                    value="{{ old('tempat_lahir', $data->tempat_lahir) }}"
                />
                @error('tempat_lahir')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Tanggal lahir</label>
                <input
                    type="date"
                    class="form-input"
                    placeholder="Masukkan Tanggal Lahir"
                    name="tanggal_lahir"
                    value="{{ old('tanggal_lahir', $data->tanggal_lahir) }}"
                />
                @error('tanggal_lahir')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Telp</label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukkan Nomor Telepon"
                    name="telp"
                    oninput="validatePhoneNumber(this)"
                    value="{{ old('telp', $data->telp) }}"
                />
                @error('telp')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Jenis Usaha</label>
                <select name="jenis_usaha" id="" class="form-select">
                    <option value="">Pilih Jenis Usaha</option>
                    @foreach ($jenis_usaha as $key => $value)
                        <option value="{{ $key }}" {{ old('jenis_usaha', $data->jenis_usaha) == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @error('jenis_usaha')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="ktp_nasabah" id="foto-nasabah">Foto Nasabah</label>
                @if ($data->foto_nasabah)
                    <a class="text-theme-primary underline underline-offset-4 cursor-pointer btn-file-preview"
                        data-title="Foto Nasabah" data-filepath="{{asset('../upload')}}/{{$id_pengajuan}}/{{$data->id}}/{{$data->foto_nasabah}}">Preview</a>
                @endif
                <div class="flex gap-4">
                    <input type="file" name="foto_nasabah" class="form-input limit-size-2"
                        value="{{old('foto_nasabah', $data->foto_nasabah)}}" />
                </div>
                <span class="error-limit text-red-500" style="display: none; margin-top: 0;">Maximum upload file
                    size is 2 MB</span>
                @error('foto_nasabah')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Status</label>
                <select name="status" id="status_nasabah" class="form-select">
                    <option value="0" {{ old('status_nasabah', $data->status_pernikahan) == 0 ? 'selected' : '' }}>Pilih Status</option>
                    <option value="1" {{ old('status_nasabah', $data->status_pernikahan) == 1 ? 'selected' : '' }}>Belum Menikah</option>
                    <option value="2" {{ old('status_nasabah', $data->status_pernikahan) == 2 ? 'selected' : '' }}>Menikah</option>
                    <option value="3" {{ old('status_nasabah', $data->status_pernikahan) == 3 ? 'selected' : '' }}>Duda</option>
                    <option value="4" {{ old('status_nasabah', $data->status_pernikahan) == 4 ? 'selected' : '' }}>Janda</option>
                </select>
                @error('status')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">NIK</label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukan NIK"
                    name="nik_nasabah"
                    oninput="validateNIK(this)"
                    value="{{ old('nik_nasabah', $data->nik) }}"
                />
                @error('nik_nasabah')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box" id="ktp-nasabah">
                <label for="ktp_nasabah" id="label-ktp-nasabah">Foto KTP Nasabah</label>
                @if ($data->foto_ktp)
                    <a class="text-theme-primary underline underline-offset-4 cursor-pointer btn-file-preview"
                        data-title="Foto Nasabah" data-filepath="{{asset('../upload')}}/{{$id_pengajuan}}/{{$data->id}}/{{$data->foto_ktp}}">Preview</a>
                @endif
                <div class="flex gap-4">
                    <input type="file" name="ktp_nasabah" class="form-input limit-size-2"
                        value="{{old('ktp_nasabah', $data->foto_ktp)}}"/>
                </div>
                <span class="text-red-500" style="display: none; margin-top: 0;">Maximum upload file
                    size is 2 MB</span>
                @error('ktp_nasabah')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box hidden" id="nik_pasangan">
                <label for="">NIK Pasangan</label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukan NIK Pasangan"
                    name="nik_pasangan"
                    value="{{ old('nik_pasangan', $data->nik_pasangan) }}"
                    oninput="validateNIK(this)"
                />
                @error('nik_pasangan')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box hidden" id="ktp-pasangan">
                <label for="ktp_pasangan" id="">Foto KTP Pasangan</label>
                @if ($data->foto_pasangan)
                    <a class="text-theme-primary underline underline-offset-4 cursor-pointer btn-file-preview"
                        data-title="Foto KTP Pasangan" data-filepath="{{asset('../upload')}}/{{$id_pengajuan}}/{{$data->id}}/{{$data->foto_pasangan}}">Preview</a>
                @endif
                <div class="flex gap-4">
                    <input type="file" name="ktp_pasangan" class="form-input limit-size-2"
                        value="{{old('ktp_pasangan', $data->foto_pasangan)}}"/>
                </div>
                <span class="text-red-500" style="display: none; margin-top: 0;">Maximum upload file
                    size is 2 MB</span>
                @error('ktp_pasangan')
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="">{{ $itemSlik->nama }}</label>
                <select name="dataLevelDua[{{ $itemSlik->id }}]" id="dataLevelDua" class="form-select"
                    data-id_item={{ $itemSlik->id }}>
                    <option value=""> --Pilih Data -- </option>
                    @foreach ($itemSlik->option as $itemJawaban)
                    <option value="{{ $itemJawaban->skor . '-' . $itemJawaban->id }}"
                        {{ old("dataLevelDua[$itemSlik->id]", $jawabanSlik->skor.'-'.$jawabanSlik->id_jawaban) == $itemJawaban->skor . '-' . $itemJawaban->id ? 'selected' : '' }}>
                        {{ $itemJawaban->option }}</option>
                    @endforeach
                </select>
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
                @error("dataLevelDua[{{ $itemSlik->id }}]")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">{{ $itemP->nama }}</label>
                @if ($jawabanLaporanSlik)
                    @if ($jawabanLaporanSlik->opsi_text)
                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer btn-file-preview"
                            data-title="{{$itemP->nama}}" data-filepath="{{asset('../upload')}}/{{$id_pengajuan}}/{{$jawabanLaporanSlik->id_jawaban}}/{{$jawabanLaporanSlik->opsi_text}}">Preview</a>
                    @endif
                @endif
                <input type="hidden" name="id_item_file[{{ $itemP->id }}]" value="{{ $itemP->id }}" id="">
                <input type="file" name="upload_file[{{ $itemP->id }}]" id="file_slik" data-id=""
                    placeholder="Masukkan informasi {{ $itemP->nama }}" class="form-input limit-size-slik"
                    value="{{ $jawabanLaporanSlik?->opsi_text }}">
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
                <span class="filename" style="display: inline;"></span>
                @error("upload_file[{{ $itemP->id }}]")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <span class="text-red-500 m-0" style="display: none">Maximum upload file
                size is 10 MB</span>
        </div>
        <div class="form-group-3">
                <div class="input-box">
                    <label for="">Kota / Kabupaten KTP</label>
                    <select name="kode_kotakab_ktp" class="form-select @error('kabupaten') is-invalid @enderror select2"
                        id="kabupaten">
                        <option value="0"> --- Pilih Kabupaten --- </option>
                        @foreach ($dataKabupaten as $item)
                            <option value="{{ $item->id }}" {{ old('kode_kotakab_ktp', $data->kotakab_ktp) == $item->id ? 'selected' : '' }}>{{ $item->kabupaten }}</option>
                        @endforeach
                    </select>
                    @error("kode_kotakab_ktp")
                        <span class="text-red-500">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-box">
                    <label for="">Kecamatan KTP</label>
                    <select name="kecamatan_sesuai_ktp" id="kecamatan" class="form-select @error('kec') is-invalid @enderror select2">
                        <option value="0"> --- Pilih Kecamatan --- </option>
                        @foreach ($dataKecamatan as $item)
                            <option value="{{ $item->id }}" {{ old('kecamatan_sesuai_ktp', $data->kec_ktp) == $item->id ? 'selected' : '' }}>{{ $item->kecamatan }}</option>
                        @endforeach
                    </select>
                    @error("kecamatan_sesuai_ktp")
                        <span class="text-red-500">{{$message}}</span>
                    @enderror
                </div>
                <div class="input-box">
                    <label for="">Desa KTP</label>
                    <select name="desa" id="desa" class="form-select @error('desa') is-invalid @enderror select2">
                        <option value="0"> --- Pilih Desa --- </option>
                        @foreach ($dataDesa as $item)
                            <option value="{{ $item->id }}" {{ old('desa', $data->desa_ktp) == $item->id ? 'selected' : '' }}>{{ $item->desa }}</option>
                        @endforeach
                    </select>
                    @error("desa")
                        <span class="text-red-500">{{$message}}</span>
                    @enderror
                </div>
        </div>
        <div class="form-group-1">
            <div class="input-box">
                <label for="">Alamat KTP</label>
                <textarea
                    name="alamat_sesuai_ktp"
                    class="form-textarea"
                    placeholder="Alamat KTP"
                    id=""
                >{{ old('alamat_sesuai_ktp', $data->alamat_ktp) }}</textarea>
                @error("alamat_sesuai_ktp")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="">Kota / Kabupaten Domisili</label>
                <select name="kode_kotakab_domisili" class="form-select @error('kabupaten_domisili') is-invalid @enderror select2"
                    id="kabupaten_domisili">
                    <option value="0"> --- Pilih Kabupaten --- </option>
                    @foreach ($dataKabupaten as $item)
                        <option value="{{ $item->id }}" {{ old('kode_kotakab_domisili', $data->kotakab_dom) == $item->id ? 'selected' : '' }}>{{ $item->kabupaten }}</option>
                    @endforeach
                </select>
                @error("kode_kotakab_domisili")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Kecamatan Domisili</label>
                <select name="kecamatan_domisili" id="kecamatan_domisili" class="form-select @error('kecamatan_domisili') is-invalid @enderror select2">
                    <option value="0"> --- Pilih Kecamatan --- </option>
                    @foreach ($dataKecamatanDomisili as $item)
                        <option value="{{ $item->id }}" {{ old('kecamatan_domisili', $data->kec_dom) == $item->id ? 'selected' : '' }}>{{ $item->kecamatan }}</option>
                    @endforeach
                </select>
                @error("kecamatan_domisili")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="form-group-1">
            <div class="input-box">
                <label for="">Alamat Domisili</label>
                <textarea
                    name="alamat_domisili"
                    class="form-textarea"
                    placeholder="Alamat Domisili"
                    id=""
                >{{ old('alamat_domisili', $data->alamat_dom) }}</textarea>
                @error("alamat_domisili")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>

        </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="">Kota / Kabupaten Usaha</label>
                <select name="kode_kotakab_usaha" class="form-select @error('kabupaten_usaha') is-invalid @enderror select2"
                    id="kabupaten_usaha">
                    <option value="0"> --- Pilih Kabupaten --- </option>
                    @foreach ($dataKabupaten as $item)
                        <option value="{{ $item->id }}" {{ old('kode_kotakab_usaha', $data->kotakab_usaha ) == $item->id ? 'selected' : '' }}>{{ $item->kabupaten }}</option>
                    @endforeach
                </select>
                @error("kode_kotakab_usaha")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Kecamatan Usaha</label>
                <select name="kecamatan_usaha" id="kecamatan_usaha" class="form-select @error('kecamatan_usaha') is-invalid @enderror select2">
                    <option value="0"> --- Pilih Kecamatan --- </option>
                    @foreach ($dataKecamatanUsaha as $item)
                        <option value="{{ $item->id }}" {{ old('kecamatan_usaha', $data->kec_usaha) == $item->id ? 'selected' : '' }}>{{ $item->kecamatan }}</option>
                    @endforeach
                </select>
                @error("kecamatan_usaha")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="form-group-1">
            <div class="input-box">
                <label for="">Alamat Usaha</label>
                <textarea
                    name="alamat_usaha"
                    class="form-textarea"
                    placeholder="Alamat Usaha"
                    id=""
                >{{ old('alamat_usaha', $data->alamat_usaha) }}</textarea>
                @error("alamat_usaha")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>

        <div class="form-group-2">
            <div class="input-box">
                <label for="">Plafon</label>
                <input
                    type="text"
                    class="form-input rupiah"
                    placeholder="Plafon"
                    name="nominal_pengajuan"
                    id="jumlah_kredit"
                    value="{{ old('nominal_pengajuan', $data->nominal) }}"
                />
                @error("nominal_pengajuan")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Jangka Waktu</label>
                <div class="flex items-center">
                    <div class="flex-1">
                        <input
                            type="number"
                            class="w-full form-input"
                            placeholder="Masukan Jangka Waktu"
                            name="jangka_waktu"
                            id="jangka_waktu"
                            aria-label="Jangka Waktu"
                            value="{{ old('jangka_waktu', $data->jangka_waktu) }}"
                            aria-describedby="basic-addon2"
                        />
                        <span class="jangka_waktu_error text-red-500 hidden"></span>
                        @error("jangka_waktu")
                            <span class="text-red-500">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="flex-shrink-0 mt-2.5rem">
                        <span class="form-input bg-gray-100">Bulan</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="">Tujuan Penggunaan</label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukan Tujuan Penggunaan"
                    name="tujuan_penggunaan"
                    value="{{ old('tujuan_penggunaan', $data->tujuan_penggunaan) }}"
                />
                @error("tujuan_penggunaan")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Jaminan yang disediakan</label>
                <select name="ket_agunan" id="" class="form-select">
                    <option value="0" >Pilih Jaminan</option>
                    <option value="shm" {{ old('ket_agunan', $data->ket_agunan) == 'shm' ? 'selected' : '' }}>SHM</option>
                    <option value="bpkb" {{ old('ket_agunan', $data->ket_agunan) == 'bpkb' ? 'selected' : '' }}>BPKB</option>
                    <option value="shgb" {{ old('ket_agunan', $data->ket_agunan) == 'shgb' ? 'selected' : '' }}>SHGB</option>
                    <option value="lainnya" {{ old('ket_agunan', $data->ket_agunan) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error("ket_agunan")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>

        <div class="form-group-2" id="form_tipe_pengajuan">
            <div class="input-box">
                <label for="">Tipe Pengajuan</label>
                <select name="tipe_pengajuan" id="tipe" class="form-select">
                    <option value="0">Tipe Pengajuan</option>
                    @foreach ($tipe as $key => $value)
                    <option value="{{ $key }}" {{ old('tipe_pengajuan', $data->tipe) == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @error("tipe_pengajuan")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Jenis badan hukum</label>
                <select name="jenis_badan_hukum" id="jenis_badan_hukum" class="form-select">
                    <option value="0">Jenis Badan Hukum</option>
                    <option value="Berbadan Hukum" {{ old('jenis_berbadan', $data->jenis_badan_hukum) == 'Berbadan Hukum' ? 'selected' : '' }}>Berbadan Hukum</option>
                    <option value="Tidak Berbadan Hukum" {{ old('jenis_berbadan', $data->jenis_badan_hukum) == 'Tidak Berbadan Hukum' ? 'selected' : '' }}>Tidak Berbadan Hukum</option>
                </select>
                @error("jenis_badan_hukum")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="form-group-3 hidden" id="tempat_berdiri">
            <div id="nama_pj" class="input-box">
                <label for="" id="label_pj"></label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukkan disini .."
                    name="nama_pj"
                    value="{{ old('nama_pj', $data->nama_pj_ketua) }}"
                />
                @error("nama_pj")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Tempat Berdiri</label>
                <input
                    type="text"
                    class="form-input"
                    name="tempat_berdiri"
                    placeholder="Masukkan disini"
                    value="{{ old('tempat_berdiri', $data->tempat_berdiri) }}"
                />
                @error("tempat_berdiri")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
            <div class="input-box">
                <label for="">Tanggal Berdiri</label>
                <div class="input-grouped">
                    <input
                    type="date"
                    class="form-input"
                    name="tanggal_berdiri"
                    value="{{ old('tanggal_berdiri', $data->tanggal_berdiri) }}"
                    />
                    @error("tanggal_berdiri")
                        <span class="text-red-500">{{$message}}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group-1">
            <div class="input-box">
                <label for="">Hubungan Bank</label>
                <textarea
                    name="hub_bank"
                    class="form-textarea"
                    placeholder="Hubungan Bank"
                    id=""
                >{{ old('hub_bank', $data->hubungan_bank) }}</textarea>
                @error("hub_bank")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="form-group-1">
            <div class="input-box">
                <label for="">Hasil Verifikasi</label>
                <textarea
                    name="hasil_verifikasi"
                    class="form-textarea"
                    placeholder="Hasil Verikasi"
                    id=""
                >{{ old('hasil_verifikasi', $data->hasil_verifikasi) }}</textarea>
                @error("hasil_verifikasi")
                    <span class="text-red-500">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="flex justify-between">
            <button type="button"
            class="px-5 py-2 border rounded bg-white text-gray-500"
            >
            Kembali
            </button>
            <button type="button"
            class="px-5 py-2 next-tab border rounded bg-theme-primary text-white"
            >
            Selanjutnya
            </button>
        </div>
    </div>
</div>

@push('script-inject')
    <script>
        $('.btn-file-preview').on('click', function() {
            const title = $(this).data('title')
            const filepath = $(this).data('filepath')

            $('#modal-file-preview #title').html(title)
            $('#modal-file-preview #file').attr('src', filepath)
            $('#modal-file-preview').removeClass('hidden')
        })
    </script>
@endpush
