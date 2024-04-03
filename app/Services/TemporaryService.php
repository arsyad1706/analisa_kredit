<?php
namespace App\Services;

use App\Http\Controllers\PengajuanKreditController;
use App\Models\CalonNasabahTemp;
use App\Models\JawabanTemp;
use App\Models\PengajuanDagulirTemp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class TemporaryService
{
    public static function getNasabahData($tempId): CalonNasabahTemp
    {
        $tempId = $tempId ?? session('nId');

        if(!$tempId) {
            $nasData = CalonNasabahTemp::whereNull('nama')->first();

            if(!$nasData) {
                $nasData = CalonNasabahTemp::create([
                    'id_user' => Auth::user()->id,
                ]);
            }

            session()->put('nId', $nasData->id);
            return $nasData;
        }

        if($nasData = CalonNasabahTemp::find($tempId)) {
            $controller = new PengajuanKreditController;
            // $nasData->tanggal_lahir = $controller->formatDate($nasData->tanggal_lahir);
            return $nasData;
        };

        return CalonNasabahTemp::create([
            'id_user' => Auth::user()->id,
        ]);
    }

    public static function saveNasabah(int|null $id, array $data): CalonNasabahTemp
    {
        if($id != null){
            $nasabah = CalonNasabahTemp::find($id);
            $nasabah->update($data);
        } else{
            $nasabah = CalonNasabahTemp::create(
                $data
            );
        }

        return $nasabah;
    }

    public static function saveFile(CalonNasabahTemp $nasabah, int $aID, int|null $fID, UploadedFile $file)
    {
        $exist = JawabanTemp::find($fID);

        $path = public_path("upload/temp/{$aID}/");
        $name = auth()->user()->id . '-' . time() . '-' . $file->getClientOriginalName();

        if($exist) {
            @unlink($path . $exist->opsi_text);
            $exist->update(['opsi_text' => $name]);
        } else {
            $exist = JawabanTemp::create([
                'id_temporary_calon_nasabah' => $nasabah->id,
                'id_jawaban' => $aID,
                'opsi_text' => $name,
                'type' => 'file',
            ]);
        }

        $file->move($path, $name);
        return [
            'filename' => $name,
            'file_id' => $exist->id,
        ];
    }

    public static function delFile(JawabanTemp|null $answer)
    {
        if(!$answer) return false;

        $path = public_path("upload/temp/{$answer->id_jawaban}/{$answer->opsi_text}");

        @unlink($path);
        $answer->delete();

        return true;
    }

    public static function convertNasabahReq(Request $request): array
    {
        $controller = new PengajuanKreditController;
        return [
            'nama' => $request->name,
            'alamat_rumah' => $request->alamat_rumah,
            'alamat_usaha' => $request->alamat_usaha,
            'no_ktp' => $request->no_ktp,
            'no_telp' => $request->no_telp,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'status' => $request->status,
            'sektor_kredit' => $request->sektor_kredit,
            'jenis_usaha' => $request->jenis_usaha,
            'jumlah_kredit' => str_replace(['.', ',00'], '', $request->jumlah_kredit),
            'tujuan_kredit' => $request->tujuan_kredit,
            'jaminan_kredit' => $request->jaminan,
            'hubungan_bank' => $request->hubungan_bank,
            'verifikasi_umum' => $request->hasil_verifikasi,
            'id_user' => $request->user()->id,
            'id_kabupaten' => intval($request->kabupaten),
            'id_kecamatan' => intval($request->kec),
            'id_desa' => intval($request->desa),
            'tenor_yang_diminta' => intval($request->tenor_yang_diminta),
            'skema_kredit' => $request->skema_kredit,
            'produk_kredit_id' => $request->produk_kredit_id,
            'skema_kredit_id' => $request->skema_kredit_id,
            'skema_limit_id' => $request->skema_limit_id ?? null
        ];
    }

    // Dagulir
    public static function convertDagulirReq(Request $request): array
    {
        $npwp = null;
        return [
            'nama' => $request->get('nama_lengkap'),
            'email' => $request->get('email'),
            'nik' => $request->get('nik_nasabah'),
            'nama_pj_ketua' => $request->has('nama_pj') ? $request->get('nama_pj') : null,
            'tempat_lahir' =>  $request->get('tempat_lahir'),
            'tanggal_lahir' => $request->get('tanggal_lahir'),
            'telp' => $request->get('telp'),
            'jenis_usaha' => $request->get('jenis_usaha'),
            'ket_agunan' => $request->get('ket_agunan'),
            'hubungan_bank' => $request->get('hub_bank'),
            'hasil_verifikasi' => $request->get('hasil_verifikasi'),
            'nominal' => formatNumber($request->get('nominal_pengajuan')),
            'tujuan_penggunaan' => $request->get('tujuan_penggunaan'),
            'jangka_waktu' => $request->get('jangka_waktu'),
            'kode_bank_pusat' => 1,
            'kode_bank_cabang' => auth()->user()->id_cabang,
            'desa_ktp' => $request->get('desa'),
            'kec_ktp' => $request->get('kecamatan_sesuai_ktp') == '---Pilih Kecamatan---' ? null : $request->get('kecamatan_sesuai_ktp'),
            'kotakab_ktp' => $request->get('kode_kotakab_ktp'),
            'alamat_ktp' => $request->get('alamat_sesuai_ktp'),
            'kec_dom' => $request->get('kecamatan_domisili') == '---Pilih Kecamatan---' ? null : $request->get('kecamatan_domisili'),
            'kotakab_dom' => $request->get('kode_kotakab_domisili'),
            'alamat_dom' => $request->get('alamat_domisili'),
            'kec_usaha' => $request->get('kecamatan_usaha') == '---Pilih Kecamatan---' ? null : $request->get('kecamatan_usaha'),
            'kotakab_usaha' => $request->get('kode_kotakab_usaha'),
            'alamat_usaha' => $request->get('alamat_usaha'),
            'tipe' => $request->get('tipe_pengajuan'),
            'npwp' => $npwp,
            'jenis_badan_hukum' => $request->get('jenis_badan_hukum'),
            'tempat_berdiri' => $request->get('tempat_berdiri'),
            'tanggal_berdiri' => $request->get('tanggal_berdiri'),
            'tanggal' => now(),
            'user_id' => Auth::user()->id,
            'status' => 8,
            'status_pernikahan' => $request->get('status'),
            'nik_pasangan' => $request->has('nik_pasangan') ? $request->get('nik_pasangan') : null,
            'created_at' => now()
        ];
    }

    public static function saveDagulir(int|null $id, array $data): PengajuanDagulirTemp
    {
        if($id != null){
            $dagulir = PengajuanDagulirTemp::where('id', $id)
                ->update($data);
            $dagulir = PengajuanDagulirTemp::find($id);
        } else{
            $dagulir = PengajuanDagulirTemp::insertGetId(
                $data
            );
            $dagulir = PengajuanDagulirTemp::find($dagulir);
        }

        return $dagulir;
    }

    public static function getNasabahDataDagulir($tempId): PengajuanDagulirTemp
    {
        $tempId = $tempId ?? session('nId');

        if(!$tempId) {
            $nasData = PengajuanDagulirTemp::whereNull('nama')->first();

            if(!$nasData) {
                $nasData = PengajuanDagulirTemp::insert([
                    'id_user' => Auth::user()->id,
                ]);
            }

            session()->put('nId', $nasData->id);
            return $nasData;
        }

        if($nasData = PengajuanDagulirTemp::find($tempId)) {
            $controller = new PengajuanKreditController;
            $nasData->tanggal_lahir = $controller->formatDate($nasData->tanggal_lahir);
            return $nasData;
        };

        return PengajuanDagulirTemp::create([
            'id_user' => Auth::user()->id,
        ]);
    }

    public static function saveFileDagulir(PengajuanDagulirTemp $dagulir, int $aID, int|null $fID, UploadedFile $file)
    {
        $exist = JawabanTemp::find($fID);

        $path = public_path("upload/temp/{$aID}/");
        $name = auth()->user()->id . '-' . time() . '-' . $file->getClientOriginalName();

        if($exist) {
            @unlink($path . $exist->opsi_text);
            $exist->update(['opsi_text' => $name]);
        } else {
            $exist = JawabanTemp::create([
                'temporary_dagulir_id' => $dagulir->id,
                'id_jawaban' => $aID,
                'opsi_text' => $name,
                'type' => 'file',
            ]);
        }

        $file->move($path, $name);
        return [
            'filename' => $name,
            'file_id' => $exist->id,
        ];
    }

    public static function delFileDagulir(JawabanTemp|null $answer)
    {
        if(!$answer) return false;

        $path = public_path("upload/temp/{$answer->id_jawaban}/{$answer->opsi_text}");

        @unlink($path);
        $answer->delete();

        return true;
    }

    public static function saveFileDagulirDataUmum(PengajuanDagulirTemp $dagulir, Request $request, UploadedFile $file)
    {
        $filename = $filename = auth()->user()->id . '-' . time() . '-' . $file->getClientOriginalName();
        $tipe = $request->tipe;
        $path = public_path() . "/upload/temp/{$request->id_dagulir_temp}/";
        $field = '';
        if($tipe == 'ktp_nasabah')
            $field = 'foto_ktp';
        else if($tipe == 'ktp_pasangan')
            $field = 'foto_pasangan';
        else if($tipe == 'foto_nasabah')
            $field = 'foto_nasabah';

        if($dagulir->$tipe != null){
            @unlink($path . $dagulir->$tipe);
            $dagulir->update([$field => $filename]);
        } else {
            $dagulir
                ->where('id', $dagulir->id)
                ->update([$field => $filename]);
        }

        $file->move($path, $filename);
        return [
            'filename' => $filename,
            'file_id' => $dagulir->id
        ];
    }

    public static function delFileDagulirDataUmum(JawabanTemp|null $answer)
    {
        if(!$answer) return false;

        $path = public_path("upload/temp/{$answer->id_jawaban}/{$answer->opsi_text}");

        @unlink($path);
        $answer->delete();

        return true;
    }
}
