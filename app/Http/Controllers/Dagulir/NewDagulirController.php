<?php

namespace App\Http\Controllers\Dagulir;

use App\Http\Controllers\Controller;
use App\Events\EventMonitoring;
use App\Http\Controllers\LogPengajuanController;
use App\Models\AlasanPengembalianData;
use App\Models\CalonNasabah;
use App\Models\CalonNasabahTemp;
use App\Models\Desa;
use App\Models\DetailKomentarModel;
use App\Models\ItemModel;
use App\Models\JawabanPengajuanModel;
use App\Models\JawabanTextModel;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KomentarModel;
use App\Models\OptionModel;
use App\Models\PengajuanModel;
use App\Models\JawabanSubColumnModel;
use App\Models\PendapatPerAspek;
use App\Models\DetailPendapatPerAspek;
use App\Models\JawabanTemp;
use App\Models\JawabanTempModel;
use App\Models\LogPengajuan;
use App\Models\MerkModel;
use App\Models\PengajuanDagulir;
use App\Models\TipeModel;
use App\Models\User;
use App\Services\TemporaryService;
use DateTime;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Image;
use Carbon\Carbon;
use PhpParser\Node\Expr;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class NewDagulirController extends Controller
{
    private $isMultipleFiles = [];
    private $logPengajuan;

    public function __construct()
    {
        $this->logPengajuan = new LogPengajuanController;
        $this->isMultipleFiles = [
            'Foto Usaha'
        ];
    }

    public function getUserJson($role)
    {
        $status = '';
        $req_status = 0;
        $message = '';
        $data = null;
        try {
            $data = User::select('id', 'nip', 'email', 'name')
                ->where('role', $role)
                ->whereNotNull('nip')
                ->where('id_cabang', Auth::user()->id_cabang)
                ->get();

            foreach ($data as $key => $value) {
                $karyawan = $this->getKaryawanFromAPI($value->nip);
                if (array_key_exists('nama', $karyawan)) {
                    $value->name = $karyawan['nama'];
                }
            }

            $req_status = HttpFoundationResponse::HTTP_OK;
            $status = 'success';
            $message = 'Berhasil mengambil data';
        } catch (Exception $e) {
            $req_status = HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR;
            $status = 'failed';
            $message = 'Terjadi kesalahan : ' . $e->getMessage();
        } catch (QueryException $e) {
            $req_status = HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR;
            $status = 'failed';
            $message = 'Terjadi kesalahan pada database: ' . $e->getMessage();
        } finally {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $data,
            ]);
        }
    }

    public function getKaryawanFromAPI($nip)
    {
        // retrieve from api
        $host = env('HCS_HOST');
        $apiURL = $host . '/api/karyawan';

        try {
            $response = Http::timeout(3)->withOptions(['verify' => false])->get($apiURL, [
                'nip' => $nip,
            ]);

            $statusCode = $response->status();
            $responseBody = json_decode($response->getBody(), true);

            if (array_key_exists('data', $responseBody))
                return $responseBody['data'];
            else
                return $responseBody;
            return $responseBody;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return $e->getMessage();
        }
    }

    public static function getKaryawanFromAPIStatic($nip)
    {
        // retrieve from api
        $host = env('HCS_HOST');
        $apiURL = $host . '/api/karyawan';

        try {
            $response = Http::timeout(3)->withOptions(['verify' => false])->get($apiURL, [
                'nip' => $nip,
            ]);

            $statusCode = $response->status();
            $responseBody = json_decode($response->getBody(), true);

            if ($responseBody != null) {
                if (is_array($responseBody)) {
                    if (array_key_exists('data', $responseBody))
                        return $responseBody['data']['nama'];
                    else
                        return 'undifined';
                } else
                    return 'undifined';
            } else
                return 'undifined';
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return 'undifined';
            // return $e->getMessage();
        }
    }

    public function getNameKaryawan($nip)
    {
        $host = env('HCS_HOST');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $host . '/api/v1/karyawan/' . $nip,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        if ($response != null) {
            $json = json_decode($response);

            if (isset($json->data))
                return $json->data->nama_karyawan;
        }

        return Auth::user()->name;
    }

    public function formatDate($date)
    {
        if ($date) {
            $arr = explode('-', $date);
            return $arr[2] . '-' . $arr[1] . '-' . $arr[0]; // yyyy-mm-dd
        }
    }

    public function create() {
        $param['pageTitle'] = "Dashboard";
        $param['multipleFiles'] = $this->isMultipleFiles;

        $param['dataDesa'] = Desa::all();
        $param['dataKecamatan'] = Kecamatan::all();
        $param['dataKabupaten'] = Kabupaten::all();
        $param['dataAspek'] = ItemModel::select('*')->where('level', 1)->where('nama', '!=', 'Data Umum')->get();
        $param['itemSlik'] = ItemModel::with('option')->where('nama', 'SLIK')->first();
        $param['itemSP'] = ItemModel::where('nama', 'Surat Permohonan')->first();
        $param['itemP'] = ItemModel::where('nama', 'Laporan SLIK')->first();
        $param['itemKTPSu'] = ItemModel::where('nama', 'Foto KTP Suami')->first();
        $param['itemKTPIs'] = ItemModel::where('nama', 'Foto KTP Istri')->first();
        $param['itemKTPNas'] = ItemModel::where('nama', 'Foto KTP Nasabah')->first();
        $param['itemNIB'] = ItemModel::where('nama', 'Dokumen NIB')->first();
        $param['itemNPWP'] = ItemModel::where('nama', 'Dokumen NPWP')->first();
        $param['itemSKU'] = ItemModel::where('nama', 'Dokumen Surat Keterangan Usaha')->first();

        $data['dataPertanyaanSatu'] = ItemModel::select('id', 'nama', 'level', 'id_parent')->where('level', 2)->where('id_parent', 3)->get();
        $param['dataMerk'] = MerkModel::all();
        $param['jenis_usaha'] = config('dagulir.jenis_usaha');
        $param['tipe'] = config('dagulir.tipe_pengajuan');

        return view('dagulir.pengajuan-kredit.add-pengajuan-kredit', $param);
    }

    public function store(Request $request)
    {
        $statusSlik = false;
        $find = array('Rp ', '.', ',');
        // $request->validate([
        //     'name' => 'required',
        //     'no_telp' => 'required',
        //     'alamat_rumah' => 'required',
        //     'alamat_usaha' => 'required',
        //     'no_ktp' => 'required',
        //     'kabupaten' => 'required|not_in:0',
        //     'kec' => 'required|not_in:0',
        //     'desa' => 'required|not_in:0',
        //     'tempat_lahir' => 'required',
        //     'tanggal_lahir' => 'required',
        //     'status' => 'required',
        //     'sektor_kredit' => 'required',
        //     'jenis_usaha' => 'required',
        //     'jumlah_kredit' => 'required',
        //     'tenor_yang_diminta' => 'required',
        //     'tujuan_kredit' => 'required',
        //     'jaminan' => 'required',
        //     'hubungan_bank' => 'required',
        //     'hasil_verifikasi' => 'required',
        // ], [
        //     'required' => 'Data :attribute harus terisi.',
        //     'not_in' => 'kolom harus dipilih.',
        // ]);

        DB::beginTransaction();
        try {
            $find = array('Rp.', '.', ',');

            // Jawaban untuk file
            $pengajuan = new PengajuanDagulir();
            $pengajuan->kode_pendaftaran = null;
            $pengajuan->nama = $request->get('nama_lengkap');
            $pengajuan->email = $request->get('email');
            $pengajuan->nik = $request->get('nik_nasabah');
            $pengajuan->nama_pj_ketua = $request->has('nama_pj') ? $request->get('nama_pj') : null;
            $pengajuan->tempat_lahir =  $request->get('tempat_lahir');
            $pengajuan->tanggal_lahir = $request->get('tanggal_lahir');
            $pengajuan->telp = $request->get('telp');
            $pengajuan->jenis_usaha = $request->get('jenis_usaha');
            $pengajuan->ket_agunan = $request->get('ket_agunan');
            $pengajuan->hubungan_bank = $request->get('hub_bank');
            $pengajuan->nominal = formatNumber($request->get('nominal_pengajuan'));
            $pengajuan->tujuan_penggunaan = $request->get('tujuan_penggunaan');
            $pengajuan->jangka_waktu = $request->get('jangka_waktu');
            $pengajuan->kode_bank_pusat = 1;
            $pengajuan->id_slik = (int)$request->get('id_slik');
            $pengajuan->kode_bank_cabang = auth()->user()->id_cabang;
            $pengajuan->kec_ktp = $request->get('kecamatan_sesuai_ktp');
            $pengajuan->kotakab_ktp = $request->get('kode_kotakab_ktp');
            $pengajuan->alamat_ktp = $request->get('alamat_sesuai_ktp');
            $pengajuan->kec_dom = $request->get('kecamatan_domisili');
            $pengajuan->kotakab_dom = $request->get('kode_kotakab_domisili');
            $pengajuan->alamat_dom = $request->get('alamat_domisili');
            $pengajuan->kec_usaha = $request->get('kecamatan_usaha');
            $pengajuan->kotakab_usaha = $request->get('kode_kotakab_usaha');
            $pengajuan->alamat_usaha = $request->get('alamat_usaha');
            $pengajuan->tipe = $request->get('tipe_pengajuan');
            $npwp = null;
            if ($request->upload_file) {
                if (array_key_exists('153', $request->upload_file)) {
                    $npwp = auth()->user()->id . '-' . time() . '-' . $request->upload_file[153]->getClientOriginalName();
                }
            }
            $pengajuan->npwp = $npwp;
            $pengajuan->jenis_badan_hukum = $request->get('jenis_badan_hukum');
            $pengajuan->tempat_berdiri = $request->get('tempat_berdiri');
            $pengajuan->tanggal_berdiri = $request->get('tanggal_berdiri');
            $pengajuan->tanggal = now();
            $pengajuan->user_id = Auth::user()->id;
            $pengajuan->status = 8;
            $pengajuan->status_pernikahan = $request->get('status');
            $pengajuan->nik_pasangan = $request->has('nik_pasangan') ? $request->get('nik_pasangan') : null;
            $pengajuan->created_at = now();
            $pengajuan->from_apps = 'pincetar';
            $pengajuan->save();

            $update_pengajuan = PengajuanDagulir::find($pengajuan->id);
            // Start File Slik
            if ($request->has('file_slik')) {
                $image = $request->file('file_slik');
                $fileNameSlik = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                $filePath = public_path() . '/upload/' . $pengajuan->id . '/' .$pengajuan->id_slik;
                if (!File::isDirectory($filePath)) {
                    File::makeDirectory($filePath, 493, true);
                }
                $image->move($filePath, $fileNameSlik);
            }
            // foto nasabah
            if ($request->has('foto_nasabah')) {
                $image = $request->file('foto_nasabah');
                $fileNameNasabah = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                $filePath = public_path() . '/upload/' . $pengajuan->id;
                if (!File::isDirectory($filePath)) {
                    File::makeDirectory($filePath, 493, true);
                }
                $image->move($filePath, $fileNameNasabah);
                $update_pengajuan->foto_nasabah = $fileNameNasabah;

            }
            if ($request->has('ktp_pasangan')) {
                $image = $request->file('ktp_pasangan');
                $fileNamePasangan = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                $filePath = public_path() . '/upload/' . $pengajuan->id;
                if (!File::isDirectory($filePath)) {
                    File::makeDirectory($filePath, 493, true);
                }
                $image->move($filePath, $fileNamePasangan);
                $update_pengajuan->foto_pasangan = $fileNamePasangan;

            }
            if ($request->has('ktp_nasabah')) {
                $image = $request->file('ktp_nasabah');
                $fileNameKtpNasabah = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                $filePath = public_path() . '/upload/' . $pengajuan->id;
                if (!File::isDirectory($filePath)) {
                    File::makeDirectory($filePath, 493, true);
                }
                $image->move($filePath, $fileNameKtpNasabah);
                $update_pengajuan->foto_ktp = $fileNameKtpNasabah;

            }
            // ktp nasabah
            $update_pengajuan->update();
            // End File Slik

            $addPengajuan = new PengajuanModel;
            $addPengajuan->id_staf = auth()->user()->id;
            $addPengajuan->tanggal = date(now());
            $addPengajuan->id_cabang = auth()->user()->id_cabang;
            $addPengajuan->progress_pengajuan_data = $request->progress;
            $addPengajuan->skema_kredit = 'Dagulir';
            $addPengajuan->dagulir_id = $pengajuan->id;
            $addPengajuan->save();
            $id_pengajuan = $addPengajuan->id;
            $tempNasabah = TemporaryService::getNasabahData($request->idCalonNasabah);

            $dataNasabah = $tempNasabah->toArray();
            $dataNasabah['id_pengajuan'] = $id_pengajuan;

            // jawaban ijin usaha
            JawabanTextModel::create([
                'id_pengajuan' => $id_pengajuan,
                'id_jawaban' => 76,
                'opsi_text' => $request->ijin_usaha,
                'skor_penyelia' => null,
                'skor_pbp' => null,
                'skor' => null,
            ]);

            //untuk jawaban yg teks, number, persen, long text
            foreach ($request->id_level as $key => $value) {
                if ($value != null) {
                    $dataJawabanText = new JawabanTextModel;
                    $dataJawabanText->id_pengajuan = $id_pengajuan;
                    $dataJawabanText->id_jawaban = $request->get('id_level')[$key];
                    if ($request->get('id_level')[$key] != '131' && $request->get('id_level')[$key] != '143' && $request->get('id_level')[$key] != '90' && $request->get('id_level')[$key] != '138') {
                        $dataJawabanText->opsi_text = str_replace($find, '', $request->get('informasi')[$key]);
                    } else {
                        $dataJawabanText->opsi_text = $request->get('informasi')[$key];
                    }
                    // $dataJawabanText->opsi_text = $request->get('informasi')[$key] == null ? '-' : $request->get('informasi')[$key];
                    $dataJawabanText->save();
                }
            }

            $dataJawabanText = new JawabanTextModel;
            $dataJawabanText->id_pengajuan = $id_pengajuan;
            $dataJawabanText->id_jawaban = 110;
            $dataJawabanText->opsi_text = $request->kategori_jaminan_tambahan;
            $dataJawabanText->save();

            // untuk upload file baru
            foreach ($request->upload_file as $key => $value) {
                if (is_array($value)) {
                    for ($i = 0; $i < count($value); $i++) {
                        $filename = auth()->user()->id . '-' . time() . '-' . $value[$i]->getClientOriginalName();
                        $relPath = "upload/{$id_pengajuan}/{$key}";
                        $path = public_path("upload/{$id_pengajuan}/{$key}/");

                        File::isDirectory(public_path($relPath)) or File::makeDirectory(public_path($relPath), recursive: true);
                        $value[$i]->move($path, $filename);

                        JawabanTextModel::create([
                            'id_pengajuan' => $id_pengajuan,
                            'id_jawaban' => $key,
                            'opsi_text' => $filename,
                            'skor_penyelia' => null,
                            'skor_pbp' => null,
                            'skor' => null,
                        ]);
                    }
                } else {
                    $filename = auth()->user()->id . '-' . time() . '-' . $value->getClientOriginalName();
                    $relPath = "upload/{$id_pengajuan}/{$key}";
                    $path = public_path("upload/{$id_pengajuan}/{$key}/");

                    File::isDirectory(public_path($relPath)) or File::makeDirectory(public_path($relPath), recursive: true);
                    $value->move($path, $filename);

                    JawabanTextModel::create([
                        'id_pengajuan' => $id_pengajuan,
                        'id_jawaban' => $key,
                        'opsi_text' => $filename,
                        'skor_penyelia' => null,
                        'skor_pbp' => null,
                        'skor' => null,
                    ]);
                }
            }
            //untuk upload file dari temp
            $tempFiles = JawabanTemp::where('type', 'file')->where('id_temporary_calon_nasabah', $request->id_nasabah)->get();
            foreach ($tempFiles as $tempFile) {
                if (!array_key_exists($tempFile->id_jawaban, $request->upload_file)) {
                    $tempPath = public_path("upload/temp/{$tempFile->id_jawaban}/{$tempFile->opsi_text}");
                    $newPath = str_replace('temp/', "{$id_pengajuan}/", $tempPath);
                    $relPath = "upload/{$id_pengajuan}/{$tempFile->id_jawaban}";

                    // check file exists
                    if (file_exists($tempPath)) {
                        File::isDirectory(public_path($relPath)) or File::makeDirectory(public_path($relPath), recursive: true);
                        File::move($tempPath, $newPath);
                        if ($tempFile->id_jawaban != null) {
                            JawabanTextModel::create([
                                'id_pengajuan' => $id_pengajuan,
                                'id_jawaban' => $tempFile->id_jawaban,
                                'opsi_text' => $tempFile->opsi_text,
                                'skor_penyelia' => null,
                                'skor_pbp' => null,
                                'skor' => null,
                            ]);
                        }
                    }
                }

                $tempFile->delete();
            }

            /**
             * Find score average
             * 1. declare array variable needs
             * 2. remove empty data from array
             * 3. merge array variables to one array
             * 4. sum score
             * 5. find average score
             */

            // item level 2
            $dataLev2 = [];
            if ($request->dataLevelDua != null) {
                $dataLev2 = $request->dataLevelDua;
                // remove key 80, 86, 93 & 142 from array
                // unset($dataLev2[80]);
                // unset($dataLev2[86]);
                // unset($dataLev2[93]);
                // unset($dataLev2[142]);
            }

            // item level 3
            $dataLev3 = [];
            if ($request->dataLevelTiga != null) {
                // item level 3
                $dataLev3 = $request->dataLevelTiga;
                // remove key 121, 134 & 149 from array
                // unset($dataLev3[121]);
                unset($dataLev3[134]);
                // unset($dataLev3[149]);
            }

            // item level 4
            $dataLev4 = [];
            if ($request->dataLevelEmpat != null) {
                $dataLev4 = $request->dataLevelEmpat;
            }

            $mergedDataLevel = array_merge($dataLev2, $dataLev3, $dataLev4);
            // sum score
            $totalScore = 0;
            $totalDataNull = 0;
            $arrTes = [];
            for ($i = 0; $i < count($mergedDataLevel); $i++) {
                if ($mergedDataLevel[$i]) {
                    // jika data tersedia
                    $data = getDataLevel($mergedDataLevel[$i]);
                    array_push($arrTes, $data);
                    if (is_numeric($data[0])) {
                        if ($data[0] > 0) {
                            if ($data[1] == 71 || $data[1] == 186) {
                                if ($data[0] == '1') {
                                    $statusSlik = true;
                                }
                            }
                            $totalScore += $data[0];
                        }
                        else {
                            $totalDataNull++;
                        }
                    } else
                        $totalDataNull++;
                } else
                    $totalDataNull++;
            }
            // find avg
            $avgResult = round($totalScore / (count($mergedDataLevel) - $totalDataNull), 2);
            // dd($mergedDataLevel, $totalScore, count($mergedDataLevel), $totalDataNull, count($mergedDataLevel) - $totalDataNull, $avgResult);
            // return $request;
            $status = "";
            $updateData = PengajuanModel::find($id_pengajuan);
            if ($avgResult > 0 && $avgResult <= 2) {
                $status = "merah";
            } elseif ($avgResult > 2 && $avgResult <= 3) {
                // $updateData->status = "kuning";
                $status = "kuning";
            } elseif ($avgResult > 3) {
                $status = "hijau";
            } else {
                $status = "merah";
            }

            // dd($mergedDataLevel, $totalDataNull, $totalScore, count($mergedDataLevel) - $totalDataNull, $avgResult);

            for ($i = 0; $i < count($mergedDataLevel); $i++) {
                if ($mergedDataLevel[$i] != null) {
                    $data = getDataLevel($mergedDataLevel[$i]);
                    if (is_numeric($data[0])) {
                        if ($data[0] > 0) {
                            JawabanPengajuanModel::insert([
                                'id_pengajuan' => $id_pengajuan,
                                'id_jawaban' => getDataLevel($mergedDataLevel[$i])[1],
                                'skor' => getDataLevel($mergedDataLevel[$i])[0],
                            ]);
                        }
                    } else {
                        JawabanPengajuanModel::insert([
                            'id_pengajuan' => $id_pengajuan,
                            'id_jawaban' => getDataLevel($mergedDataLevel[$i])[1]
                        ]);
                    }
                }
            }

            if (!$statusSlik) {
                $updateData->posisi = 'Proses Input Data';
                $updateData->status_by_sistem = $status;
                $updateData->average_by_sistem = $avgResult;
            } else {
                $updateData->posisi = 'Ditolak';
                $updateData->status_by_sistem = "merah";
                $updateData->average_by_sistem = "1.0";
            }
            $updateData->update();

            //save pendapat per aspek
            foreach ($request->get('id_aspek') as $key => $value) {
                if ($request->get('pendapat_per_aspek')[$key] == '') {
                    # code...
                } else {
                    $addPendapat = new PendapatPerAspek;
                    $addPendapat->id_pengajuan = $id_pengajuan;
                    $addPendapat->id_staf = Auth::user()->id;
                    $addPendapat->id_aspek = $value;
                    $addPendapat->pendapat_per_aspek = $request->get('pendapat_per_aspek')[$key];
                    $addPendapat->save();
                }
            }

            if ($request->get('komentar_staff') == '') {
                $addKomentar = new KomentarModel;
                $addKomentar->id_pengajuan = $id_pengajuan;
                $addKomentar->komentar_staff = '';
                $addKomentar->id_staff = Auth::user()->id;
                $addKomentar->save();
            } else {
                $addKomentar = new KomentarModel;
                $addKomentar->id_pengajuan = $id_pengajuan;
                $addKomentar->komentar_staff = $request->get('komentar_staff');
                $addKomentar->id_staff = Auth::user()->id;
                $addKomentar->save();
            }

            // JawabanTemp::where('id_temporary_calon_nasabah', $tempNasabah->id)->delete();
            // JawabanTempModel::where('id_temporary_calon_nasabah', $tempNasabah->id)->delete();
            // $tempNasabah->delete();
            // DB::table('temporary_usulan_dan_pendapat')
            //     ->where('id_temp', $tempNasabah->id)
            //     ->delete();
            // DB::table('data_po_temp')->where('id_calon_nasabah_temp', $tempNasabah->id)->delete();

            // Log Pengajuan Baru
            // $namaNasabah = 'undifined';
            // if ($addData)
            //     $namaNasabah = $addData->nama;

            // $this->logPengajuan->store('Staff dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' melakukan proses pembuatan data pengajuan atas nama ' . $namaNasabah . '.', $id_pengajuan, Auth::user()->id, Auth::user()->nip);

            DB::commit();
            event(new EventMonitoring('store pengajuan'));

            if (!$statusSlik)
                return redirect()->route('dagulir.pengajuan-kredit.index')->withStatus('Data berhasil disimpan.');
            else
                return redirect()->route('dagulir.pengajuan-kredit.index')->withError('Pengajuan ditolak');
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
            return redirect()->route('dagulir.pengajuan-kredit.index')->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            return $e->getMessage();
            return redirect()->route('dagulir.pengajuan-kredit.index')->withError('Terjadi kesalahan' . $e->getMessage());
        }
    }

    public function storeSipde(Request $request) {
        $pengajuan = PengajuanModel::with('pendapatPerAspek')->find($request->id_dagulir);
        $pengajuan_dagulir = PengajuanDagulir::find($pengajuan->dagulir_id);
        $data = sipde_token();
        $body = [
            "nama" => $pengajuan_dagulir->nama,
            "nik" => $pengajuan_dagulir->nik,
            "tempat_lahir" => $pengajuan_dagulir->tempat_lahir,
            "tanggal_lahir" => $pengajuan_dagulir->tanggal_lahir,
            "telp" => $pengajuan_dagulir->telp,
            "jenis_usaha" => $pengajuan_dagulir->jenis_usaha,
            "nominal_pengajuan" => formatNumber($request->nominal_realisasi),
            "tujuan_penggunaan" => $pengajuan_dagulir->tujuan_penggunaan,
            "jangka_waktu" => intval($request->get('jangka_waktu')),
            "ket_agunan" => $pengajuan_dagulir->ket_agunan,
            "kode_bank_pusat" => '01-BPR',
            "kode_bank_cabang" => $pengajuan_dagulir->kode_bank_cabang,
            "kecamatan_sesuai_ktp" => $pengajuan_dagulir->kec_ktp,
            "kode_kotakab_ktp" => $pengajuan_dagulir->kotakab_ktp,
            "alamat_sesuai_ktp" => $pengajuan_dagulir->alamat_ktp,
            "kecamatan_domisili" => $pengajuan_dagulir->kec_dom ,
            "kode_kotakab_domisili" => $pengajuan_dagulir->kotakab_dom,
            "alamat_domisili" => $pengajuan_dagulir->alamat_dom,
            "kecamatan_usaha" => $pengajuan_dagulir->kec_usaha,
            "kode_kotakab_usaha" => $pengajuan_dagulir->kotakab_usaha ,
            "alamat_usaha" => $pengajuan_dagulir->alamat_usaha,
            "tipe_pengajuan" => $pengajuan_dagulir->tipe,
            "npwp" => $pengajuan_dagulir->npwp,
            "jenis_badan_hukum" => $pengajuan_dagulir->jenis_badan_hukum,
            // "jenis_badan_hukum" => "Berbadan Hukum",
            "tempat_berdiri" => $pengajuan_dagulir->tempat_berdiri,
            "tanggal_berdiri" => $pengajuan_dagulir->tanggal_berdiri,
            "email" => $pengajuan_dagulir->email,
            "nama_pj" => $pengajuan_dagulir->nama_pj_ketua ??  null,
        ];
        $pengajuan_dagulir = Http::withHeaders([
            'Authorization' => 'Bearer ' .$data['token'],
        ])->post(config('dagulir.host').'/pengajuan.json', $body)->json();

        if (array_key_exists('data', $pengajuan_dagulir)) {
            $update_pengajuan_dagulir = PengajuanDagulir::find($pengajuan->dagulir_id);
            $update_pengajuan_dagulir->kode_pendaftaran = $pengajuan_dagulir['data']['kode_pendaftaran'];
            $update_pengajuan_dagulir->nominal_realisasi = $this->formatNumber($request->nominal_realisasi);
            $update_pengajuan_dagulir->jangka_waktu = $request->jangka_waktu;
            $update_pengajuan_dagulir->status = 1;
            $update_pengajuan_dagulir->update();

            return redirect()->route('dagulir.index')->withStatus('Berhasil mengirimkan data.');
        }
        else {
            $message = 'Terjadi kesalahan.';
            if (array_key_exists('error', $pengajuan_dagulir)) $message .= ' '.$pengajuan_dagulir['error'];

            return redirect()->route('dagulir.index')->withError($message);
        }
    }

    public function updateStatus($kode_pendaftaran, $status, $lampiran_analisa = null, $jangka_waktu, $realisasi_dana) {
        $data = sipde_token();
        $pengajuan_dagulir = Http::withHeaders([
            'Authorization' => 'Bearer ' .$data['token'],
        ])->post(env('SIPDE_HOST').'/update_status.json',[
            "kode_pendaftaran" => $kode_pendaftaran,
            "status" => $status,
            "lampiran_analisa" => $lampiran_analisa,
            "jangka_waktu" => $jangka_waktu,
            "realisasi_dana" => $realisasi_dana
        ])->json();
        return $pengajuan_dagulir;
    }
}
