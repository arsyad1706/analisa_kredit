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
        id="data-umum">
    <div>
        <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
            <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
               Data Diri :
            </h2>
        </div>
    </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="">Nama Lengkap</label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukkan Nama"
                    name="nama_lengkap"
                    value="{{ old('nama_lengkap', $duTemp?->nama ?? '') }}"
                />
            </div>
            <div class="input-box">
                <label for="">Email</label>
                <input
                type="email"
                class="form-input"
                placeholder="Masukkan Email"
                name="email"
                value="{{ old('email', $duTemp?->email ?? '') }}"
                />
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
                    value="{{ old('tempat_lahir', $duTemp?->tempat_lahir ?? '') }}"
                />
            </div>
            <div class="input-box">
                <label for="">Tanggal lahir</label>
                <input
                    type="date"
                    class="form-input"
                    placeholder="Masukkan Tanggal Lahir"
                    name="tanggal_lahir"
                    value="{{ old('tanggal_lahir', $duTemp?->tanggal_lahir ?? '') }}"
                />
            </div>
            <div class="input-box">
                <label for="">Telp</label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukkan Nomor Telepon"
                    name="telp"
                    oninput="validatePhoneNumber(this)"
                    value="{{ old('telp', $duTemp?->telp ?? '') }}"
                />
            </div>
            <div class="input-box">
                <label for="ktp_nasabah" id="foto-nasabah">Foto Nasabah</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                @if ($duTemp->foto_nasabah)
                    <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                        data-title="Foto Nasabah" data-filepath="{{asset('../upload')}}/{{$duTemp?->id}}/{{$duTemp->id}}/{{$duTemp->foto_nasabah}}">Preview</a>
                @endif
                <div class="flex gap-4">
                    <input type="file" name="foto_nasabah" class="form-input limit-size-2" value="{{$duTemp->foto_nasabah}}"/>
                </div>
                {{--  <span class="filename" style="display: inline;">{{ $duTemp?->foto_nasabah }}</span>  --}}
                <span class="error-limit text-red-500" style="display: none; margin-top: 0;">Maximum upload file
                    size is 2 MB</span>
            </div>
            <div class="input-box">
                <label for="">Status</label>
                <select name="status" id="status_nasabah" class="form-select">
                    <option value="0" {{ $duTemp?->status_pernikahan == 0 ? 'selected' : '' }}>Pilih Status</option>
                    <option value="1" {{ $duTemp?->status_pernikahan == 1 ? 'selected' : '' }}>Belum Menikah</option>
                    <option value="2" {{ $duTemp?->status_pernikahan == 2 ? 'selected' : '' }}>Menikah</option>
                    <option value="3" {{ $duTemp?->status_pernikahan == 3 ? 'selected' : '' }}>Duda</option>
                    <option value="4" {{ $duTemp?->status_pernikahan == 4 ? 'selected' : '' }}>Janda</option>
                </select>
            </div>
            <div class="input-box">
                <label for="">NIK</label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukan NIK"
                    name="nik_nasabah"
                    oninput="validateNIK(this)"
                    value="{{ old('nik', $duTemp?->nik ?? '') }}"
                />
            </div>
            <div class="input-box" id="ktp-nasabah">
                <label for="ktp_nasabah" id="label-ktp-nasabah">Foto KTP Nasabah</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                @if ($duTemp->foto_ktp)
                    <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                        data-title="Foto KTP Nasabah" data-filepath="{{asset('../upload')}}/{{$duTemp?->id}}/{{$duTemp->id}}/{{$duTemp->foto_ktp}}">Preview</a>
                @endif
                <div class="flex gap-4">
                    <input type="file" name="ktp_nasabah" class="form-input limit-size-2" data-id="{{ $duTemp?->id }}" value="{{$duTemp?->foto_ktp}}"/>
                </div>
                {{--  <span class="filename" style="display: inline;">{{ $duTemp->foto_ktp }}</span>  --}}
                <span class="text-red-500" style="display: none; margin-top: 0;">Maximum upload file
                    size is 2 MB</span>
            </div>
            <div class="input-box hidden" id="nik_pasangan">
                <label for="">NIK Pasangan</label>
                <input
                    type="text"
                    class="form-input"
                    placeholder="Masukan NIK Pasangan"
                    name="nik_pasangan"
                    value="{{ old('nik_pasangan') }}"
                    oninput="validateNIK(this)"
                />
            </div>
            <div class="input-box hidden" id="ktp-pasangan">
                <label for="ktp_pasangan" id="">Foto KTP Pasangan</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                @if ($duTemp->foto_pasangan)
                    <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                        data-title="Foto KTP Pasangan" data-filepath="{{asset('../upload')}}/{{$duTemp?->id}}/{{$duTemp->id}}/{{$duTemp->foto_pasangan}}">Preview</a>
                @endif
                <div class="flex gap-4">
                    <input type="file" name="ktp_pasangan" class="form-input limit-size-2" data-id="{{ $duTemp->id }}" value="{{$duTemp->foto_pasangan}}"/>
                </div>
                {{--  <span class="filename" style="display: inline;">{{ $duTemp?->foto_pasangan }}</span>  --}}
                <span class="text-red-500" style="display: none; margin-top: 0;">Maximum upload file
                    size is 2 MB</span>
            </div>
        </div>
        <div class="form-group-3">
                <div class="input-box">
                    <label for="">Kota / Kabupaten KTP</label>
                    <select name="kode_kotakab_ktp" class="form-select @error('kabupaten') is-invalid @enderror select2"
                        id="kabupaten">
                        <option value="0"> --- Pilih Kabupaten --- </option>
                        @foreach ($dataKabupaten as $item)
                            <option value="{{ $item->id }}" {{ $item->id == $duTemp?->kotakab_ktp ? 'selected' : '' }}>{{ $item->kabupaten }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-box">
                    <label for="">Kecamatan KTP</label>
                    <select name="kecamatan_sesuai_ktp" id="kecamatan" class="form-select @error('kec') is-invalid @enderror select2">
                        <option value="0"> --- Pilih Kecamatan --- </option>
                        @foreach ($dataKecamatan as $item)
                            <option value="{{ $item->id }}" {{ $item->id == $duTemp?->kec_ktp ? 'selected' : '' }}>{{ $item->kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-box">
                    <label for="">Desa KTP</label>
                    <select name="desa" id="desa" class="form-select @error('desa') is-invalid @enderror select2">
                        <option value="0"> --- Pilih Desa --- </option>
                        @foreach ($dataDesa as $item)
                            <option value="{{ $item->id }}" {{ $item->id == $duTemp?->desa_ktp ? 'selected' : '' }}>{{ $item->desa }}</option>
                        @endforeach
                    </select>
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
                >{{ $duTemp?->alamat_ktp }}</textarea>
            </div>
        </div>
        <div>
            <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
                <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                   File Slik :
                </h2>
            </div>
        </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="">{{ $itemSlik->nama }}</label>
                <select name="dataLevelDua[{{ $itemSlik->id }}]" id="dataLevelDua" class="form-select"
                    data-id_item={{ $itemSlik->id }}>
                    <option value=""> --Pilih Data -- </option>
                    @foreach ($itemSlik->option as $itemJawaban)
                    <option value="{{ $itemJawaban->skor . '-' . $itemJawaban->id }}" {{ temporary_select_dagulir($itemSlik->id,
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
                <label for="">{{ $itemP->nama }}</label><small class="text-red-500 font-bold"> (.pdf)</small>
                @php
                    $fileLapSlik = "";
                @endphp
                @if ($jawabanLaporanSlik)
                    @if ($jawabanLaporanSlik->opsi_text)
                        @php
                            $fileLapSlik = $jawabanLaporanSlik->opsi_text;
                        @endphp
                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                            data-title="{{$itemP->nama}}" data-filepath="{{asset('../upload')}}/{{$duTemp?->id}}/{{$jawabanLaporanSlik->id_jawaban}}/{{$jawabanLaporanSlik->opsi_text}}">Preview</a>
                    @endif
                @endif
                <input type="hidden" name="id_item_file[{{ $itemP->id }}]" value="{{ $itemP->id }}" id="">
                <input type="file" name="upload_file[{{ $itemP->id }}]" id="file_slik" data-id="{{ temporary_dagulir($duTemp->id, $itemP->id)?->id }}"
                    placeholder="Masukkan informasi {{ $itemP->nama }}" class="form-input limit-size-slik" value="{{$fileLapSlik}}">
                @if (isset($key) && $errors->has('dataLevelDua.' . $key))
                <div class="invalid-feedback">
                    {{ $errors->first('dataLevelDua.' . $key) }}
                </div>
                @endif
                {{--  <span class="filename" style="display: inline;">{{ edit_dagulir($duTemp->id, $itemP->id)?->opsi_text }}</span>  --}}
                {{-- <span class="alert alert-danger">Maximum file upload is 5 MB</span> --}}
            </div>
            <span class="text-red-500 m-0" style="display: none">Maximum upload file
                size is 10 MB</span>
        </div>
        <div>
            <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
                <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                   Data Diri :
                </h2>
            </div>
        </div>
        <div class="form-group-2">
            <div class="input-box">
                <label for="">Alamat Usaha</label>
                <textarea
                    name="alamat_usaha"
                    class="form-textarea"
                    placeholder="Alamat Usaha"
                    id=""
                >{{ $duTemp?->alamat_usaha ?? null }}</textarea>
            </div>
            <div class="input-box">
                <label for="">Jenis Usaha</label>
                <textarea
                    name="alamat_usaha"
                    class="form-textarea"
                    placeholder="Alamat Usaha"
                    id=""
                >{{ $duTemp?->jenis_usaha ?? null }}</textarea>
            </div>
        </div>
        <div>
            <div class="p-2 border-l-4 border-theme-primary bg-gray-100">
                <h2 class="font-semibold text-sm tracking-tighter text-theme-text">
                   Data Pengajuan :
                </h2>
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
                    value="{{ $duTemp?->nominal }}"
                />
            </div>
            <div class="input-box">
                <label for="">Tenor</label>
                <div class="flex items-center">
                    <div class="flex-1">
                        <input
                            type="number"
                            class="w-full form-input"
                            placeholder="Masukan Jangka Waktu"
                            name="jangka_waktu"
                            id="jangka_waktu"
                            aria-label="Jangka Waktu"
                            value="{{ $duTemp?->jangka_waktu ?? null }}"
                            aria-describedby="basic-addon2"
                        />
                        <span class="jangka_waktu_error text-red-400 hidden"></span>
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
                    value="{{ $duTemp?->tujuan_penggunaan ?? null }}"
                />
            </div>
            <div class="input-box">
                <label for="">Jaminan yang disediakan</label>
                <select name="ket_agunan" id="" class="form-select">
                    <option value="0" >Pilih Jaminan</option>
                    <option value="shm" {{ $duTemp?->ket_agunan == 'shm' ? 'selected' : '' }}>SHM</option>
                    <option value="bpkb" {{ $duTemp?->ket_agunan == 'bpkb' ? 'selected' : '' }}>BPKB</option>
                    <option value="shgb" {{ $duTemp?->ket_agunan == 'shgb' ? 'selected' : '' }}>SHGB</option>
                    <option value="lainnya" {{ $duTemp?->ket_agunan == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
        </div>

        <div class="form-group-2" id="form_tipe_pengajuan">

            <div class="input-box">
                <label for="">Jenis badan hukum</label>
                <select name="jenis_badan_hukum" id="jenis_badan_hukum" class="form-select">
                    <option value="0">Jenis Badan Hukum</option>
                    <option value="Berbadan Hukum" {{ $duTemp?->jenis_badan_hukum == 'Berbadan Hukum' ? 'selected' : '' }}>Berbadan Hukum</option>
                    <option value="Tidak Berbadan Hukum" {{ $duTemp?->jenis_badan_hukum == 'Tidak Berbadan Hukum' ? 'selected' : '' }}>Tidak Berbadan Hukum</option>
                </select>
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
                >{{ $duTemp?->hubungan_bank ?? null }}</textarea>
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
                >{{ $duTemp?->hasil_verifikasi ?? null }}</textarea>
            </div>
        </div>
        <div class="flex justify-between">
            <a href="{{route('dagulir.pengajuan.index')}}">
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