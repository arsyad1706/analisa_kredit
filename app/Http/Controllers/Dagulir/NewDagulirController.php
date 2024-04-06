<?php

namespace App\Http\Controllers\Dagulir;

use App\Http\Controllers\Controller;
use App\Events\EventMonitoring;
use App\Http\Controllers\LogPengajuanController;
use App\Http\Controllers\PembayaranController;
use App\Models\AlasanPengembalianData;
use App\Models\CalonNasabah;
use App\Models\CalonNasabahTemp;
use App\Models\DanaCabang;
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
use App\Models\JawabanModel;
use App\Models\JawabanTemp;
use App\Models\JawabanTempModel;
use App\Models\LogPengajuan;
use App\Models\MasterDDLoan;
use App\Models\MerkModel;
use App\Models\MstProdukKredit;
use App\Models\MstSkemaKredit;
use App\Models\MstSkemaLimit;
use App\Models\PengajuanDagulir;
use App\Models\PengajuanDagulirTemp;
use App\Models\PerhitunganKredit;
use App\Models\PlafonUsulan;
use App\Models\TipeModel;
use App\Models\User;
use App\Repository\MasterDanaRepository;
use App\Repository\MasterItemRepository;
use App\Repository\PengajuanDagulirRepository;
use App\Repository\PengajuanRepository;
use App\Services\TemporaryService;
use Barryvdh\DomPDF\Facade\Pdf;
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
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Return_;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class NewDagulirController extends Controller
{
    private $isMultipleFiles = [];
    private $logPengajuan;
    private $repo;

    public function __construct()
    {
        $this->logPengajuan = new LogPengajuanController;
        $this->isMultipleFiles = [
            'Foto Usaha'
        ];
        $this->repo = new PengajuanDagulirRepository;
    }
    protected $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

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
        $konfiAPI = DB::table('api_configuration')->first();
        $host = $konfiAPI->hcs_host;
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
        $konfiAPI = DB::table('api_configuration')->first();
        $host = $konfiAPI->hcs_host;
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
        $konfiAPI = DB::table('api_configuration')->first();
        $host = $konfiAPI->hcs_host;
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

    public function create(Request $request) {
        $role = auth()->user()->role;
        if ($role == 'Staf Analis Kredit') {
            $param['pageTitle'] = "Dashboard";
            $param['multipleFiles'] = $this->isMultipleFiles;

            $param['dataDesa'] = Desa::all();
            $param['dataKecamatan'] = Kecamatan::all();
            $param['dataKabupaten'] = Kabupaten::all();
            $param['dataAspek'] = ItemModel::select('*')->where('level', 1)->where('nama', '!=', 'Data Umum')->get();
            $param['itemSlik'] = ItemModel::with('option')->where('nama', 'SLIK')->first();
            $param['itemSP'] = ItemModel::where('nama', 'Surat Permohonan')->first();
            $param['itemP'] = ItemModel::where('nama', 'Laporan SLIK')->first();
            $param['itemCatatanSlik'] = ItemModel::where('nama', 'Catatan Slik')->first();
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

            if ($request->has('dagulir')) {
                $param['dataUmumNasabah'] = PengajuanDagulir::select(
                                                                'pengajuan_dagulir.*',
                                                                'p.id AS id_pengajuan'
                                                            )
                                                            ->join('pengajuan AS p', 'p.dagulir_id', 'pengajuan_dagulir.id')
                                                            ->find($request->dagulir);
                $pengajuan = PengajuanModel::select(
                                            'pengajuan.id',
                                            'pengajuan.tanggal',
                                            'pengajuan.posisi',
                                            'pengajuan.tanggal_review_penyelia',
                                            'pengajuan.dagulir_id',
                                            'pengajuan.skema_kredit'
                                        )
                                        ->find($param['dataUmumNasabah']->id_pengajuan);
                $param['dataUmum'] = $pengajuan;

                $param['kec_ktp'] = Kecamatan::find($param['dataUmumNasabah']->kec_ktp)->kecamatan;
                $param['kab_ktp'] = Kabupaten::find($param['dataUmumNasabah']->kotakab_ktp)->kabupaten;
                $param['desa_ktp'] = '';
                if ($param['dataUmumNasabah']->desa_ktp != null) {
                    $param['desa_ktp'] = Desa::find($param['dataUmumNasabah']->desa_ktp)->desa;
                }
                $param['kec_dom'] = Kecamatan::find($param['dataUmumNasabah']->kec_dom)->kecamatan;
                $param['kab_dom'] = Kabupaten::find($param['dataUmumNasabah']->kotakab_dom)->kabupaten;
                $param['kec_usaha'] = Kecamatan::find($param['dataUmumNasabah']->kec_usaha)->kecamatan;
                $param['kab_usaha'] = Kabupaten::find($param['dataUmumNasabah']->kotakab_usaha)->kabupaten;
                $param['alamat_usaha'] = $param['dataUmumNasabah']->alamat_usaha;

                $param['allKab'] = Kabupaten::get();
                $param['allKec'] = Kecamatan::where('id_kabupaten', $param['dataUmumNasabah']->kotakab_ktp)->get();
                $param['allDesa'] = Desa::where('id_kecamatan', $param['dataUmumNasabah']->kec_ktp)->get();
                $param['allKecDom'] = Kecamatan::where('id_kabupaten', $param['dataUmumNasabah']->kotakab_dom)->get();
                $param['allKecUsaha'] = Kecamatan::where('id_kabupaten', $param['dataUmumNasabah']->kotakab_usaha)->get();
                $param['itemSlikData'] = ItemModel::join('option as o', 'o.id_item', 'item.id')
                    ->join('jawaban as j', 'j.id_jawaban', 'o.id')
                    ->join('pengajuan as p', 'p.id', 'j.id_pengajuan')
                    ->where('p.id', $param['dataUmumNasabah']->id_pengajuan)
                    ->where('nama', 'SLIK')
                    ->first();
            }

            return view('dagulir.pengajuan-kredit.add-pengajuan-kredit', $param);
        }
        else {
            alert()->error('Peringatan', 'Anda tidak memilik hak akses');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        if ($request->skema_kredit == 'Dagulir') {
            if ($request->has('dagulir_id') || $request->has('isDraft')) {
                $request->validate([
                    'status' => 'required|not_in:0',
                    'desa' => 'required|not_in:0',
                    // 'foto_nasabah' => 'required',
                    // 'ktp_nasabah' => 'required',
                    'hub_bank' => 'required',
                    'hasil_verifikasi' => 'required',
                ]);
            }
            else {
                $request->validate([
                    'nama_lengkap' => 'required',
                    'email' => 'required|unique:pengajuan_dagulir,email',
                    'nik_nasabah' => 'required|unique:pengajuan_dagulir,nik|max:16',
                    'tempat_lahir' => 'required',
                    'tanggal_lahir' => 'required',
                    'telp' => 'required',
                    'jenis_usaha' => 'required',
                    'nominal_pengajuan' => 'required',
                    'tujuan_penggunaan' => 'required',
                    'jangka_waktu' => 'required',
                    'status' => 'required|not_in:0',
                    'desa' => 'required|not_in:0',
                    'kecamatan_sesuai_ktp' => 'required|not_in:0',
                    'kode_kotakab_ktp' => 'required|not_in:0',
                    'alamat_sesuai_ktp' => 'required',
                    'kecamatan_domisili' => 'required|not_in:0',
                    'kode_kotakab_domisili' => 'required|not_in:0',
                    'alamat_domisili' => 'required',
                    'kecamatan_usaha' => 'required|not_in:0',
                    'kode_kotakab_usaha' => 'required|not_in:0',
                    'alamat_usaha' => 'required',
                    'tipe_pengajuan' => 'required|not_in:0',
                    'jenis_badan_hukum' => 'required|not_in:0',
                    'ket_agunan' => 'required|not_in:0',
                    'foto_nasabah' => 'required',
                    'ktp_nasabah' => 'required',
                    'hub_bank' => 'required',
                    'hasil_verifikasi' => 'required',
                ]);
            }
        }else{
            $request->validate([
                'name' => 'required',
                'alamat_rumah' => 'required',
                'alamat_usaha' => 'required',
                'no_ktp' => 'required|max:16',
                'kabupaten' => 'required|not_in:0',
                'kec' => 'required|not_in:0',
                'desa' => 'required|not_in:0',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'status' => 'required',
                'sektor_kredit' => 'required',
                'jenis_usaha' => 'required',
                'jumlah_kredit' => 'required',
                'tenor_yang_diminta' => 'required',
                'tujuan_kredit' => 'required',
                'jaminan' => 'required',
                'hubungan_bank' => 'required',
                'hasil_verifikasi' => 'required',
            ]);
        }

        $statusSlik = false;
        $find = array('Rp ', '.', ',');
        DB::beginTransaction();
        try {
            $find = array('Rp.', '.', ',');
            if ($request->skema_kredit == 'Dagulir') {

                if ($request->has('dagulir_id')) {
                    $pengajuan = PengajuanDagulir::find($request->dagulir_id);
                }
                else {
                    $pengajuan = new PengajuanDagulir();
                    $pengajuan->kode_pendaftaran = null;
                    $pengajuan->nama = $request->get('nama_lengkap');
                    $pengajuan->email = $request->get('email');
                    $pengajuan->nik = $request->get('nik_nasabah');
                    $pengajuan->tempat_lahir =  $request->get('tempat_lahir');
                    $pengajuan->tanggal_lahir = $request->get('tanggal_lahir');
                    $pengajuan->telp = $request->get('telp');
                    $pengajuan->jenis_usaha = $request->get('jenis_usaha');
                    $pengajuan->ket_agunan = $request->get('ket_agunan');
                    $pengajuan->nominal = formatNumber($request->get('nominal_pengajuan'));
                    $pengajuan->jangka_waktu = $request->get('jangka_waktu');
                    $pengajuan->kode_bank_pusat = 1;
                    $pengajuan->kode_bank_cabang = auth()->user()->id_cabang;
                    $pengajuan->kec_ktp = $request->get('kecamatan_sesuai_ktp');
                    $pengajuan->kotakab_ktp = $request->get('kode_kotakab_ktp');
                    $pengajuan->kec_dom = $request->get('kecamatan_domisili');
                    $pengajuan->kotakab_dom = $request->get('kode_kotakab_domisili');
                    $pengajuan->alamat_dom = $request->get('alamat_domisili');
                    $pengajuan->tujuan_penggunaan = $request->get('tujuan_penggunaan');
                    $pengajuan->alamat_ktp = $request->get('alamat_sesuai_ktp');
                    $pengajuan->kec_usaha = $request->get('kecamatan_usaha');
                    $pengajuan->kotakab_usaha = $request->get('kode_kotakab_usaha');
                    $pengajuan->alamat_usaha = $request->get('alamat_usaha');
                    $pengajuan->tipe = $request->get('tipe_pengajuan');
                    $npwp = null;
                    if ($request->informasi) {
                        if (array_key_exists('79', $request->informasi)) {
                            $npwp = str_replace(['.','-'], '', $request->informasi[79]);
                        }
                    } else {
                        $npwp = null;
                    }
                    $pengajuan->npwp = $npwp;
                    $pengajuan->jenis_badan_hukum = $request->get('jenis_badan_hukum');
                    $pengajuan->tanggal = now();
                    $pengajuan->status = 8;
                    $pengajuan->from_apps = 'pincetar';
                }
                $pengajuan->nama_pj_ketua = $request->has('nama_pj') ? $request->get('nama_pj') : null;
                $pengajuan->hubungan_bank = $request->get('hub_bank');
                $pengajuan->hasil_verifikasi = $request->get('hasil_verifikasi');
                $pengajuan->desa_ktp = $request->get('desa');
                $pengajuan->tempat_berdiri = $request->get('tempat_berdiri');
                $pengajuan->tanggal_berdiri = $request->get('tanggal_berdiri');
                $pengajuan->user_id = Auth::user()->id;
                $pengajuan->status_pernikahan = $request->get('status');
                $pengajuan->nik_pasangan = $request->has('nik_pasangan') ? $request->get('nik_pasangan') : null;
                $pengajuan->created_at = now();
                $pengajuan->save();

                $dagulir_id = $pengajuan->id;

                if ($request->has('dagulir_id')) {
                    $addPengajuan = PengajuanModel::select('id')
                                                ->where('dagulir_id', $request->get('dagulir_id'))
                                                ->first();
                }
                else {
                    $addPengajuan = new PengajuanModel;
                    $addPengajuan->id_staf = auth()->user()->id;
                    $addPengajuan->tanggal = date(now());
                    $addPengajuan->id_cabang = auth()->user()->id_cabang;
                    $addPengajuan->progress_pengajuan_data = $request->progress;
                    $addPengajuan->skema_kredit = 'Dagulir';
                    $addPengajuan->dagulir_id = $pengajuan->id;
                    $addPengajuan->save();
                }
                $id_pengajuan = $addPengajuan->id;

                $update_pengajuan = PengajuanDagulir::find($pengajuan->id);
                // foto nasabah
                if ($request->has('foto_nasabah')) {
                    $image = $request->file('foto_nasabah');
                    $fileNameNasabah = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                    $filePath = public_path() . '/upload/' . $id_pengajuan. '/' . $dagulir_id;
                    if (!File::isDirectory($filePath)) {
                        File::makeDirectory($filePath, 493, true);
                    }
                    $image->move($filePath, $fileNameNasabah);
                    $update_pengajuan->foto_nasabah = $fileNameNasabah;

                }
                if ($request->has('ktp_pasangan')) {
                    $image = $request->file('ktp_pasangan');
                    $fileNamePasangan = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                    $filePath = public_path() . '/upload/' . $id_pengajuan. '/' . $dagulir_id;
                    if (!File::isDirectory($filePath)) {
                        File::makeDirectory($filePath, 493, true);
                    }
                    $image->move($filePath, $fileNamePasangan);
                    $update_pengajuan->foto_pasangan = $fileNamePasangan;

                }
                if ($request->has('ktp_nasabah')) {
                    $image = $request->file('ktp_nasabah');
                    $fileNameKtpNasabah = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                    $filePath = public_path() . '/upload/' . $id_pengajuan. '/' . $dagulir_id;
                    if (!File::isDirectory($filePath)) {
                        File::makeDirectory($filePath, 493, true);
                    }
                    $image->move($filePath, $fileNameKtpNasabah);
                    $update_pengajuan->foto_ktp = $fileNameKtpNasabah;

                }
                // ktp nasabah
                $update_pengajuan->update();

                $tempNasabah = TemporaryService::getNasabahData($request->idCalonNasabah);

                $dataNasabah = $tempNasabah->toArray();
                $dataNasabah['id_pengajuan'] = $id_pengajuan;
            }else{
                $addPengajuan = new PengajuanModel;
                $addPengajuan->id_staf = auth()->user()->id;
                $addPengajuan->tanggal = date(now());
                $addPengajuan->id_cabang = auth()->user()->id_cabang;
                $addPengajuan->progress_pengajuan_data = $request->progress;
                $addPengajuan->skema_kredit = $request->skema_kredit;
                $addPengajuan->save();
                $id_pengajuan = $addPengajuan->id;
                $tempNasabah = TemporaryService::getNasabahData($request->idCalonNasabah);
                $idTempNasabah = DB::table('temporary_calon_nasabah')
                    ->where('id_user', $request->user()->id)
                    ->first('id_user');

                $dataNasabah = $tempNasabah->toArray();
                $dataNasabah['id_pengajuan'] = $id_pengajuan;

                $addData = new CalonNasabah;
                $addData->nama = $request->name;
                $addData->alamat_rumah = $request->alamat_rumah;
                $addData->alamat_usaha = $request->alamat_usaha;
                $addData->no_ktp = $request->no_ktp;
                $addData->no_telp = $request->get('no_telp');
                $addData->tempat_lahir = $request->tempat_lahir;
                $addData->tanggal_lahir = $request->tanggal_lahir;
                $addData->status = $request->status;
                $addData->sektor_kredit = $request->sektor_kredit;
                $addData->jenis_usaha = $request->jenis_usaha;
                $addData->jumlah_kredit = str_replace($find, "", $request->jumlah_kredit);
                $addData->tenor_yang_diminta = $request->tenor_yang_diminta;
                $addData->tujuan_kredit = $request->tujuan_kredit;
                $addData->jaminan_kredit = $request->jaminan;
                $addData->hubungan_bank = $request->hubungan_bank;
                $addData->verifikasi_umum = $request->hasil_verifikasi;
                $addData->id_user = auth()->user()->id;
                $addData->id_pengajuan = $id_pengajuan;
                $addData->id_desa = $request->desa;
                $addData->id_kecamatan = $request->kec;
                $addData->id_kabupaten = $request->kabupaten;
                $addData->save();
            }
            // OPSI ASPEK JAWABAN
            // Data KKB Handler
            if ($request->skema_kredit == 'KKB') {
                DB::table('data_po')
                    ->insert([
                        'id_pengajuan' => $id_pengajuan,
                        'tahun_kendaraan' => $request->tahun,
                        // 'id_type' => $request->id_tipe,
                        'merk' => $request->merk,
                        'tipe' => $request->tipe_kendaraan,
                        'warna' => $request->warna,
                        'keterangan' => 'Pemesanan ' . $request->pemesanan,
                        'jumlah' => $request->sejumlah,
                        'harga' => str_replace($find, '', $request->harga)
                    ]);
            }

            // jawaban catatan slik
            if ($request->has('catatan_slik')) {
                $itemCatatanSlik = ItemModel::where('nama', 'Catatan Slik')->first();
                JawabanTextModel::create([
                    'id_pengajuan' => $id_pengajuan,
                    'id_jawaban' => $itemCatatanSlik->id,
                    'opsi_text' => $request->catatan_slik,
                    'skor_penyelia' => null,
                    'skor_pbp' => null,
                    'skor' => null,
                ]);
            }

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
                    if (array_key_exists($key, $request->get('informasi'))) {
                        $dataJawabanText = new JawabanTextModel;
                        $dataJawabanText->id_pengajuan = $id_pengajuan;
                        $dataJawabanText->id_jawaban = $request->get('id_level')[$key];
                        if ($request->get('id_level')[$key] != '131' && $request->get('id_level')[$key] != '143' && $request->get('id_level')[$key] != '90' && $request->get('id_level')[$key] != '138') {
                            $dataJawabanText->opsi_text = str_replace($find, '', $request->get('informasi')[$key]);
                        } else {
                            $dataJawabanText->opsi_text = $request->get('informasi')[$key];
                        }
                        $dataJawabanText->save();
                    }
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
            $idTemp = $request->id_dagulir_temp;
            $tempFiles = JawabanTemp::where('type', 'file')->where('temporary_dagulir_id', $idTemp)->get();
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
            }

            // item level 3
            $dataLev3 = [];
            if ($request->dataLevelTiga != null) {
                // item level 3
                $dataLev3 = $request->dataLevelTiga;
                unset($dataLev3[134]);
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
                            if (array_key_exists(1, $data)) {
                                if ($data[1] == 71 || $data[1] == 186) {
                                    if ($data[0] == '1') {
                                        $statusSlik = true;
                                    }
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
            $status = "";
            $updateData = PengajuanModel::find($id_pengajuan);
            if ($avgResult > 0 && $avgResult <= 2) {
                $status = "merah";
            } elseif ($avgResult > 2 && $avgResult <= 3) {
                $status = "kuning";
            } elseif ($avgResult > 3) {
                $status = "hijau";
            } else {
                $status = "merah";
            }

            for ($i = 0; $i < count($mergedDataLevel); $i++) {
                if ($mergedDataLevel[$i] != null) {
                    $data = getDataLevel($mergedDataLevel[$i]);
                    if (is_numeric($data[0])) {
                        if ($data[0] > 0) {
                            if (array_key_exists(1, $data)) {
                                JawabanPengajuanModel::insert([
                                    'id_pengajuan' => $id_pengajuan,
                                    'id_jawaban' => $data[1],
                                    'skor' => $data[0],
                                ]);
                            }
                        }
                    } else {
                        if (array_key_exists(1, $data)) {
                            JawabanPengajuanModel::insert([
                                'id_pengajuan' => $id_pengajuan,
                                'id_jawaban' => $data[1]
                            ]);
                        }
                    }
                }
            }

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

            // Log Pengajuan Baru
            $namaNasabah = $request->skema_kredit == 'Dagulir' ? $pengajuan->nama : CalonNasabah::find($addData->id)->nama;
            // Delete data Draft
            if ($request->skema_kredit == 'Dagulir') {
                DB::commit();
                JawabanTemp::where('temporary_dagulir_id', $request->id_dagulir_temp)->delete();
                JawabanTempModel::where('temporary_dagulir_id', $request->id_dagulir_temp)->delete();
                PengajuanDagulirTemp::where('id', $request->id_dagulir_temp)->delete();
            } else {
                DB::commit();
                PerhitunganKredit::where('temp_calon_nasabah_id', $request->id_dagulir_temp)
                ->update([
                    'pengajuan_id' => $id_pengajuan,
                    'temp_calon_nasabah_id' => null
                ]);
                JawabanTemp::where('id_temporary_calon_nasabah', $request->id_dagulir_temp)->delete();
                JawabanTempModel::where('id_temporary_calon_nasabah', $request->id_dagulir_temp)->delete();
                CalonNasabahTemp::find($request->id_dagulir_temp)->delete();
                DB::table('temporary_usulan_dan_pendapat')
                    ->where('id_temp', $request->id_dagulir_temp)
                    ->delete();
                DB::table('data_po_temp')->where('id_calon_nasabah_temp', $request->id_dagulir_temp)->delete();
            }


            $this->logPengajuan->store('Staff dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' melakukan proses pembuatan data pengajuan atas nama ' . $namaNasabah . '.', $id_pengajuan, Auth::user()->id, Auth::user()->nip);

            if (!$statusSlik) {
                $updateData->posisi = 'Proses Input Data';
                $updateData->status_by_sistem = $status;
                $updateData->average_by_sistem = $avgResult;
            } else {

                if ($request->skema_kredit == 'Dagulir') {
                    $cetak_lampiran_analisa = $this->cetakLampiranAnalisa($id_pengajuan);
                    $lampiran_analisa = null;
                    if ($cetak_lampiran_analisa['status'] == 'success') {
                        $lampiran_analisa = "data:@application/pdf;base64,".base64_encode(file_get_contents($cetak_lampiran_analisa['filepath']));
                    }


                    if ($pengajuan->kode_pendaftaran) {
                        $kode_pendaftaran = $pengajuan->kode_pendaftaran;
                        $storeSIPDE = 'success';
                    }
                    else {
                        // HIT Pengajuan endpoint dagulir
                        $storeSIPDE = $this->storeSipde($id_pengajuan);
                        if (is_array($storeSIPDE)) {
                            $kode_pendaftaran = array_key_exists('kode_pendaftaran', $storeSIPDE) ? $storeSIPDE['kode_pendaftaran'] : false;
                        }
                    }
                    $delay = 1500000; // 1.5 sec

                    if ($kode_pendaftaran) {
                        // HIT update status survei endpoint dagulir
                        if ($pengajuan->status != 1) {
                            $survei = $this->updateStatus($kode_pendaftaran, 1);
                            if (is_array($survei)) {
                                // Fail block
                                if ($survei['message'] != 'Update Status Gagal. Anda tidak bisa mengubah status, karena status saat ini adalah SURVEY') {
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $survei);
                                    return redirect()->back();
                                }
                            }
                            else {
                                if ($survei != 200) {
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $survei);
                                    return redirect()->back()->withError($survei);
                                }
                            }
                            usleep($delay);
                        }

                        // HIT update status analisa endpoint dagulir
                        if ($pengajuan->status != 2) {
                            $analisa = $this->updateStatus($kode_pendaftaran, 2, $lampiran_analisa);
                            if (is_array($analisa)) {
                                // Fail block
                                DB::rollBack();
                                alert()->error('Peringatan(API)', $analisa);
                                return redirect()->back();
                            }
                            else {
                                if ($analisa != 200 || $analisa != '200') {
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $analisa);
                                    return redirect()->back();
                                }
                            }
                            usleep($delay);
                        }

                        // HIT update status ditolak endpoint dagulir
                        if ($pengajuan->status != 3) {
                            $ditolak = $this->updateStatus($kode_pendaftaran, 4);
                            if (is_array($ditolak)) {
                                // Fail block
                                DB::rollBack();
                                alert()->error('Peringatan(API)', $ditolak);
                                return redirect()->back();
                            }
                            else {
                                if ($ditolak != 200) {
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $ditolak);
                                    return redirect()->back();
                                }
                            }
                        }

                        $namaNasabah = 'undifined';
                        if ($pengajuan)
                            $namaNasabah = $request->skema_kredit == 'Dagulir' ? $pengajuan->nama : CalonNasabah::find($request->id_nasabah)->nama;

                        $this->logPengajuan->store('Pincab dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menolak pengajuan atas nama ' . $namaNasabah . '.', $id_pengajuan, Auth::user()->id, Auth::user()->nip);
                    }
                    else {
                        DB::rollBack();
                        alert()->error('Peringatan(API)', $storeSIPDE);
                        return redirect()->back();
                    }
                }else{
                    $updateData->posisi = 'Ditolak';
                    $updateData->status_by_sistem = "merah";
                    $updateData->average_by_sistem = "1.0";
                }
            }
            $updateData->update();
            DB::commit();
            event(new EventMonitoring('store pengajuan'));

            if (!$statusSlik){
                Alert::success('Success', 'Data berhasil disimpan.');
                if ($request->skema_kredit == 'Dagulir') {
                    return redirect()->route('dagulir.pengajuan.index');
                } else {
                    return redirect()->route('pengajuan-kredit.index');
                }

            }
            else {
                alert()->info('Informasi', 'Pengajuan ditolak.');
                if ($request->skema_kredit == 'Dagulir') {
                    return redirect()->route('dagulir.pengajuan.index');
                } else {
                    return redirect()->route('pengajuan-kredit.index');
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
            return redirect()->route('dagulir.pengajuan.index')->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            return $e;
            return redirect()->route('dagulir.pengajuan.index')->withError('Terjadi kesalahan' . $e->getMessage());
        }
    }

    public function checkPenyeliaKredit(Request $request) {
        DB::beginTransaction();
        try {
            $statusPenyelia = PengajuanModel::find($request->id_pengajuan);
            if ($statusPenyelia) {
                $statusPenyelia->posisi = "Review Penyelia";
                $statusPenyelia->id_penyelia = $request->select_penyelia;
                if (!$statusPenyelia->tanggal_review_penyelia) {
                    $statusPenyelia->tanggal_review_penyelia = date(now());
                }
                $statusPenyelia->update();

                // Log Pengajuan melanjutkan dan mendapatkan
                $dagulir = PengajuanDagulir::select('nama')->find($statusPenyelia->dagulir_id);
                $namaNasabah = 'undifined';
                if ($dagulir)
                    $namaNasabah = $dagulir->nama;

                $penyelia = User::find($request->select_penyelia);
                $this->logPengajuan->store('Staff dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menindak  lanjuti pengajuan atas nama ' . $namaNasabah . ' ke penyelia dengan NIP ' . $penyelia->nip . ' atas nama ' . $this->getNameKaryawan($penyelia->nip) . ' .', $statusPenyelia->id, Auth::user()->id, Auth::user()->nip);

                DB::commit();
                Alert::success('success', 'Berhasil mengganti posisi');
                return redirect()->back();
            } else {
                alert()->error('error','Data pengajuan tidak ditemukan.');
                return redirect()->back();
            }
        } catch (Exception $e) {
            alert()->error('error', $e->getMessage());
            DB::rollBack();
            return redirect()->back();
        } catch (QueryException $e) {
            alert()->error('error', $e->getMessage());
            DB::rollBack();
            return redirect()->back();
        }
    }

    // insert komentar
    public function getInsertKomentar(Request $request)
    {
        $role = Auth::user()->role;
        if ($role == 'Penyelia Kredit' || $role == 'PBO' || $role == 'PBP' || $role == 'Pincab') {
            DB::beginTransaction();
            try {
                if ($role == 'Pincab') {
                    $idKomentar = KomentarModel::where('id_pengajuan', $request->id_pengajuan)->first();
                    KomentarModel::where('id', $idKomentar->id)->update(
                        [
                            'komentar_pincab' => $request->komentar_pincab_keseluruhan,
                            'id_pincab' => Auth::user()->id,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]
                    );
                }
                else {
                    $finalArray = array();
                    $finalArray_text = array();
                    $totalDataNull = 0;
                    $sum_select = 0;
                    foreach ($request->skor_penyelia as $key => $value) {
                        if (is_numeric($value)) {
                            array_push($finalArray, [
                                'skor_penyelia' => $value
                            ]);
                            $sum_select += $value;
                        } else
                            $totalDataNull++;
                    }
                    $total_input_data = (count($request->skor_penyelia) - $totalDataNull);
                    // get skor ratio coverage opsi
                    $jawaban = ItemModel::select(
                                            'item.id AS item_id',
                                            'j.id_jawaban',
                                            'j.skor',
                                            'j.skor_penyelia',
                                            'j.skor_pbo',
                                            'j.skor_pbp'
                                        )
                                        ->join('option AS o', 'o.id_item', 'item.id')
                                        ->join('jawaban AS j', 'j.id_jawaban', 'o.id')
                                        ->where('j.id_pengajuan', $request->id_pengajuan)
                                        ->where('item.id', 132) // id item ratio coverage opsi
                                        ->first();
                    if ($jawaban) {
                        $sum_select += $jawaban->skor;
                        $total_input_data++;
                    }
                    $average = ($sum_select) / $total_input_data;
                    $result = round($average, 2);
                    $status = "";
                    $updateData = PengajuanModel::find($request->id_pengajuan);

                    if ($result > 0 && $result <= 2) {
                        $status = "merah";
                    } elseif ($result >= 2 && $result <= 3) {
                        $status = "kuning";
                    } elseif ($result > 3) {
                        $status = "hijau";
                    } else {
                        $status = "merah";
                    }

                    if ($role == 'Penyelia Kredit') {
                        foreach ($request->get('id_option') as $key => $value) {
                            JawabanPengajuanModel::where('id_jawaban', $value)->where('id_pengajuan', $request->get('id_pengajuan'))
                                ->update([
                                    'skor_penyelia' => $request->get('skor_penyelia')[$key] ? $request->get('skor_penyelia')[$key] : null
                                ]);
                        }
                    } else if ($role == 'PBO') {
                        foreach ($request->get('id_option') as $key => $value) {
                            JawabanPengajuanModel::where('id_jawaban', $value)->where('id_pengajuan', $request->get('id_pengajuan'))
                                ->update([
                                    'skor_pbo' => $request->get('skor_penyelia')[$key] ? $request->get('skor_penyelia')[$key] : null
                                ]);
                        }
                    } else {
                        foreach ($request->get('id_option') as $key => $value) {
                            JawabanPengajuanModel::where('id_jawaban', $value)->where('id_pengajuan', $request->get('id_pengajuan'))
                                ->update([
                                    'skor_pbp' => $request->get('skor_penyelia')[$key] ? $request->get('skor_penyelia')[$key] : null
                                ]);
                        }
                    }

                    $updateData->status = $status;
                    if ($role == 'Penyelia Kredit')
                        $updateData->average_by_penyelia = $result;
                    else if ($role == 'PBO')
                        $updateData->average_by_pbo = $result;
                    else
                        $updateData->average_by_pbp = $result;

                    $updateData->update();

                    $idKomentar = KomentarModel::where('id_pengajuan', $request->id_pengajuan)->first();

                    if ($role == 'Penyelia Kredit') {
                        KomentarModel::where('id', $idKomentar->id)->update(
                            [
                                'komentar_penyelia' => $request->komentar_penyelia_keseluruhan,
                                'id_penyelia' => Auth::user()->id,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );
                    } else if ($role == 'PBO') {
                        KomentarModel::where('id', $idKomentar->id)->update(
                            [
                                'komentar_pbo' => $request->komentar_pbo_keseluruhan,
                                'id_pbo' => Auth::user()->id,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );
                    } else {
                        KomentarModel::where('id', $idKomentar->id)->update(
                            [
                                'komentar_pbp' => $request->komentar_pbp_keseluruhan,
                                'id_pbp' => Auth::user()->id,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]
                        );
                    }

                    $countDK = DetailKomentarModel::where('id_komentar', $idKomentar->id)->count();
                    if ($countDK > 0) {
                        foreach ($request->id_item as $key => $value) {
                            $dk = DetailKomentarModel::where('id_komentar', $idKomentar->id)->where('id_user', Auth::user()->id)->where('id_item', $value)->first();
                            if ($dk) {
                                $dk->komentar = $_POST['komentar_penyelia'][$key];
                                $dk->save();
                            }
                        }
                    } else {
                        foreach ($request->id_item as $key => $value) {
                            if ($value) {
                                $dk = new DetailKomentarModel;
                                $dk->id_komentar = $idKomentar->id;
                                $dk->id_user = Auth::user()->id;
                                $dk->id_item = $value;
                                $dk->komentar = $_POST['komentar_penyelia'][$key];
                                $dk->save();
                            }
                        }
                    }

                    // pendapat penyelia
                    if ($role == 'Penyelia Kredit')
                        $countpendapat = PendapatPerAspek::where('id_pengajuan', $request->get('id_pengajuan'))->where('id_penyelia', Auth::user()->id)->count();
                    else if ($role == 'PBO')
                        $countpendapat = PendapatPerAspek::where('id_pengajuan', $request->get('id_pengajuan'))->where('id_pbo', Auth::user()->id)->count();
                    else
                        $countpendapat = PendapatPerAspek::where('id_pengajuan', $request->get('id_pengajuan'))->where('id_pbp', Auth::user()->id)->count();

                    if ($countpendapat > 0) {
                        if ($role == 'Penyelia Kredit') {
                            foreach ($request->get('id_aspek') as $key => $value) {
                                $pendapatperaspekpenyelia = PendapatPerAspek::where('id_pengajuan', $request->get('id_pengajuan'))->where('id_aspek', $value)->where('id_penyelia', Auth::user()->id)->first();
                                $pendapatperaspekpenyelia->pendapat_per_aspek = $_POST['pendapat_per_aspek'][$key];
                                $pendapatperaspekpenyelia->save();
                            }
                        } else if ($role == 'PBO') {
                            foreach ($request->get('id_aspek') as $key => $value) {
                                $pendapatperaspekpenyelia = PendapatPerAspek::where('id_pengajuan', $request->get('id_pengajuan'))->where('id_aspek', $value)->where('id_pbo', Auth::user()->id)->first();
                                $pendapatperaspekpenyelia->pendapat_per_aspek = $_POST['pendapat_per_aspek'][$key];
                                $pendapatperaspekpenyelia->save();
                            }
                        } else {
                            foreach ($request->get('id_aspek') as $key => $value) {
                                $pendapatperaspekpenyelia = PendapatPerAspek::where('id_pengajuan', $request->get('id_pengajuan'))->where('id_aspek', $value)->where('id_pbp', Auth::user()->id)->first();
                                $pendapatperaspekpenyelia->pendapat_per_aspek = $_POST['pendapat_per_aspek'][$key];
                                $pendapatperaspekpenyelia->save();
                            }
                        }
                    } else {
                        if ($role == 'Penyelia Kredit') {
                            foreach ($request->get('id_aspek') as $key => $value) {
                                $pendapatperaspekpenyelia = new PendapatPerAspek;
                                $pendapatperaspekpenyelia->id_pengajuan = $request->get('id_pengajuan');
                                $pendapatperaspekpenyelia->id_penyelia = Auth::user()->id;
                                $pendapatperaspekpenyelia->id_aspek = $value;
                                $pendapatperaspekpenyelia->pendapat_per_aspek = $request->get('pendapat_per_aspek')[$key];
                                $pendapatperaspekpenyelia->save();
                            }
                        } else if ($role == 'PBO') {
                            foreach ($request->get('id_aspek') as $key => $value) {
                                $pendapatperaspekpenyelia = new PendapatPerAspek;
                                $pendapatperaspekpenyelia->id_pengajuan = $request->get('id_pengajuan');
                                $pendapatperaspekpenyelia->id_pbo = Auth::user()->id;
                                $pendapatperaspekpenyelia->id_aspek = $value;
                                $pendapatperaspekpenyelia->pendapat_per_aspek = $request->get('pendapat_per_aspek')[$key];
                                $pendapatperaspekpenyelia->save();
                            }
                        } else {
                            foreach ($request->get('id_aspek') as $key => $value) {
                                $pendapatperaspekpenyelia = new PendapatPerAspek;
                                $pendapatperaspekpenyelia->id_pengajuan = $request->get('id_pengajuan');
                                $pendapatperaspekpenyelia->id_pbp = Auth::user()->id;
                                $pendapatperaspekpenyelia->id_aspek = $value;
                                $pendapatperaspekpenyelia->pendapat_per_aspek = $request->get('pendapat_per_aspek')[$key];
                                $pendapatperaspekpenyelia->save();
                            }
                        }
                    }
                }
                $plafonUsulan = PlafonUsulan::where('id_pengajuan', $request->id_pengajuan)->first();

                if ($plafonUsulan == null)
                    $plafonUsulan = new PlafonUsulan();
                if($role == 'Penyelia Kredit'){
                    $plafonUsulan->id_pengajuan = $request->id_pengajuan;
                    $plafonUsulan->plafon_usulan_penyelia = str_replace('.', '', $request->plafon_usulan_penyelia ?? 0);
                    $plafonUsulan->jangka_waktu_usulan_penyelia = $request->jangka_waktu_usulan_penyelia;
                    $plafonUsulan->save();
                } else if($role == 'PBO'){
                    $plafonUsulan->id_pengajuan = $request->id_pengajuan;
                    $plafonUsulan->plafon_usulan_pbo = str_replace('.', '', $request->plafon_usulan_pbo ?? 0);
                    $plafonUsulan->jangka_waktu_usulan_pbo = $request->jangka_waktu_usulan_pbo;
                    $plafonUsulan->save();
                } else if($role == 'PBP'){
                    $plafonUsulan->id_pengajuan = $request->id_pengajuan;
                    $plafonUsulan->plafon_usulan_pbp = str_replace('.', '', $request->plafon_usulan_pbp ?? 0);
                    $plafonUsulan->jangka_waktu_usulan_pbp = $request->jangka_waktu_usulan_pbp;
                    $plafonUsulan->save();
                } else{
                    $plafonUsulan->id_pengajuan = $request->id_pengajuan;
                    $plafonUsulan->plafon_usulan_pincab = str_replace('.', '', $request->plafon_usulan_pincab ?? 0);
                    $plafonUsulan->jangka_waktu_usulan_pincab = $request->jangka_waktu_usulan_pincab;
                    $plafonUsulan->save();
                }

                // Log Pengajuan review
                $dagulir = PengajuanDagulir::find($updateData->dagulir_id);
                $namaNasabah = 'undifined';

                if ($dagulir)
                    $namaNasabah = $dagulir->nama;

                $this->logPengajuan->store($role . ' dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' melakukan review terhadap pengajuan atas nama ' . $namaNasabah, $updateData->id, Auth::user()->id, Auth::user()->nip);

                DB::commit();
                event(new EventMonitoring('review pengajuan'));

                Alert::success('success', 'Berhasil Mereview');
                if ($updateData->skema_kredit == 'Dagulir') {
                    return redirect()->route('dagulir.pengajuan.index');
                } else {
                    return redirect()->route('pengajuan-kredit.index');
                }

            } catch (Exception $e) {
                DB::rollBack();
                alert()->error('error', $e->getMessage());

                return redirect()->back();
            } catch (QueryException $e) {
                DB::rollBack();
                alert()->error('error', $e->getMessage());

                return redirect()->back();
            }
        } else {
            alert()->error('error', 'Tidak memiliki hak akses.');
            return redirect()->back()->withError('Tidak memiliki hak akses.');
        }
    }

    // get detail jawaban dan skor pengajuan
    public function getDetailJawaban($id)
    {
        Session::put('is_dagulir', true);
        if (auth()->user()->role == 'Penyelia Kredit' || auth()->user()->role == 'PBO' ||
            auth()->user()->role == 'PBP' || auth()->user()->role == 'Pincab') {
            $param['pageTitle'] = "Dashboard";
            $param['dataAspek'] = ItemModel::where('level', 1)->where('nama', '!=', 'Data Umum')->get();
            $param['itemSlik'] = ItemModel::join('option as o', 'o.id_item', 'item.id')
                ->join('jawaban as j', 'j.id_jawaban', 'o.id')
                ->join('pengajuan as p', 'p.id', 'j.id_pengajuan')
                ->where('p.id', $id)
                ->where('nama', 'SLIK')
                ->first();
            $param['itemCatatanSlik'] = ItemModel::where('nama', 'Catatan Slik')->first();
            $param['itemSP'] = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();
            $param['itemKTPSu'] = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();
            $param['itemKTPIs'] = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();

            $pengajuan = PengajuanModel::select(
                'pengajuan.id',
                'pengajuan.tanggal',
                'pengajuan.posisi',
                'pengajuan.tanggal_review_penyelia',
                'pengajuan.dagulir_id',
                'pengajuan.skema_kredit'
            )
            ->find($id);
            $param['dataUmum'] = $pengajuan;
            $param['dataUmumNasabah'] = PengajuanDagulir::find($pengajuan->dagulir_id);

            $param['kec_ktp'] = Kecamatan::find($param['dataUmumNasabah']->kec_ktp)->kecamatan;
            $param['kab_ktp'] = Kabupaten::find($param['dataUmumNasabah']->kotakab_ktp)->kabupaten;
            $param['desa_ktp'] = '';
            if ($param['dataUmumNasabah']->desa_ktp != null) {
                $param['desa_ktp'] = Desa::find($param['dataUmumNasabah']->desa_ktp)->desa;
            }
            $param['kec_dom'] = Kecamatan::find($param['dataUmumNasabah']->kec_dom)->kecamatan;
            $param['kab_dom'] = Kabupaten::find($param['dataUmumNasabah']->kotakab_dom)->kabupaten;
            $param['kec_usaha'] = Kecamatan::find($param['dataUmumNasabah']->kec_usaha)->kecamatan;
            $param['kab_usaha'] = Kabupaten::find($param['dataUmumNasabah']->kotakab_usaha)->kabupaten;
            $param['alamat_usaha'] = $param['dataUmumNasabah']->alamat_usaha;

            $param['allKab'] = Kabupaten::get();
            $param['allKec'] = Kecamatan::where('id_kabupaten', $param['dataUmumNasabah']->kotakab_ktp)->get();
            $param['allDesa'] = Desa::where('id_kecamatan', $param['dataUmumNasabah']->kec_ktp)->get();
            $param['pendapatDanUsulanStaf'] = KomentarModel::select('komentar_staff')->where('id_pengajuan', $id)->first();
            $param['pendapatDanUsulanPenyelia'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_penyelia')->first();
            if (auth()->user()->role == 'PBO' || auth()->user()->role == 'PBP' || auth()->user()->role == 'Pincab')
                $param['pendapatDanUsulanPBO'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_pbo')->first();
            if (auth()->user()->role == 'PBP' || auth()->user()->role == 'Pincab')
                $param['pendapatDanUsulanPBP'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_pbp')->first();
            if (auth()->user()->role == 'Pincab')
                $param['pendapatDanUsulanPincab'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_pincab')->first();
            if ($param['dataUmumNasabah']->skema_kredit == 'KKB') {
                $param['dataPO'] = DB::table('data_po')
                    ->where('id_pengajuan', $id)
                    ->first();
            }
            $param['skema'] = $param['dataUmumNasabah']->skema_kredit;
            $dokumenUsaha = DB::table('item')
                ->where('nama', 'LIKE', '%NIB%')
                ->orWhere('nama', 'LIKE', '%Surat Keterangan Usaha%')
                ->orWhere('nama', 'LIKE', '%NPWP%')
                ->get('id');
            $countDoc = 0;
            foreach ($dokumenUsaha as $idDoc) {
                $count = DB::table('jawaban_text')
                    ->where('id_pengajuan', $id)
                    ->where('id_jawaban', $idDoc->id)
                    ->count();
                $countDoc += $count;
            }
            $param['countIjin'] = $countDoc;
            $param['alasanPengembalian'] = AlasanPengembalianData::where('id_pengajuan', $id)
                ->join('users', 'users.id', 'alasan_pengembalian_data.id_user')
                ->select('users.nip', 'alasan_pengembalian_data.*')
                ->get();
            $param['jenis_usaha'] = config('dagulir.jenis_usaha');
            $param['tipe'] = config('dagulir.tipe_pengajuan');

            $param['itemSlik'] = ItemModel::join('option as o', 'o.id_item', 'item.id')
                ->join('jawaban as j', 'j.id_jawaban', 'o.id')
                ->join('pengajuan as p', 'p.id', 'j.id_pengajuan')
                ->where('p.id', $id)
                ->where('nama', 'SLIK')
                ->first();

            $param['itemSP'] = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();
            $param['itemKTPSu'] = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();
            $param['itemKTPIs'] = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();
            $param['itemKTPNas'] = ItemModel::where('nama', 'Foto KTP Nasabah')->first();
            $param['itemNIB'] = ItemModel::where('nama', 'Dokumen NIB')->first();
            $param['itemNPWP'] = ItemModel::where('nama', 'Dokumen NPWP')->first();
            $param['itemSKU'] = ItemModel::where('nama', 'Dokumen Surat Keterangan Usaha')->first();
            $param['multipleFiles'] = $this->isMultipleFiles;
            $param['dataDesa'] = Desa::all();
            $param['dataKecamatan'] = Kecamatan::all();
            $param['dataKabupaten'] = Kabupaten::all();
            $param['dataAspek'] = ItemModel::select('*')->where('level', 1)->where('nama', '!=', 'Data Umum')->get();
            $param['plafonUsulan'] = PlafonUsulan::where('id_pengajuan', $id)->first();

            $id_user = auth()->user()->id;
            $user = DB::table('users')->where('id', $id_user)->first();
            $role = '';
            $ke = '';
            $role = auth()->user()->role;

            if ($user) {
                if ($role == 'Penyelia Kredit') {
                    $ke = 'Review Penyelia';
                } elseif ($role == 'PBO') {
                    $ke = 'PBO';
                } else {
                    $ke = 'PBP';
                }
            }
            $dari = null;
            $alasan = AlasanPengembalianData::where('id_pengajuan', $id)
                                ->join('users', 'users.id', 'alasan_pengembalian_data.id_user')
                                ->select('users.nip', 'users.id_cabang', 'alasan_pengembalian_data.*')
                                ->first();

            if ($alasan) {
                $userPBO = \App\Models\User::select('id')
                    ->where('id_cabang', $alasan->id_cabang)
                    ->where('role', 'PBO')
                    ->whereNotNull('nip')
                    ->first();

                $userPBP = \App\Models\User::select('id')
                    ->where('id_cabang', $alasan->id_cabang)
                    ->where('role', 'PBP')
                    ->whereNotNull('nip')
                    ->first();
                if ($role == 'Penyelia Kredit') {
                    if ($userPBP) {
                        $dari = "PBP";
                    } else {
                        if ($userPBO) {
                            $dari = "PBO";
                        } else {
                            $dari = "Pincab";
                        }
                    }
                } else if ($role == 'PBO') {
                    if ($userPBP) {
                        $dari = "PBP";
                    } else {
                        $dari = "Pincab";
                    }
                } else if ($role == 'PBP') {
                    $dari = "Pincab";
                } else {
                    $dari = '';
                }

            }

            $param['dari'] = $dari;
            $param['pendapat'] = $this->repo->getAlasanPengembalian($id, $ke);
            return view('dagulir.pengajuan-kredit.detail-pengajuan-jawaban', $param);
        } else {
            alert()->error('Terjadi Kesalahan', 'Tidak memiliki hak akses');
            return redirect()->back()->withError('Tidak memiliki hak akses.');
        }
    }

    public function getDetailJawabanPincab($id)
    {
        Session::put('is_dagulir', true);
        if (auth()->user()->role == 'Pincab') {
            $param['pageTitle'] = "Dashboard";

            $param['dataAspek'] = ItemModel::select('*')->where('level', 1)->where('nama', '!=', 'Data Umum')->get();
            $param['itemSlik'] = ItemModel::join('option as o', 'o.id_item', 'item.id')
                ->join('jawaban as j', 'j.id_jawaban', 'o.id')
                ->join('pengajuan as p', 'p.id', 'j.id_pengajuan')
                ->where('p.id', $id)
                ->where('nama', 'SLIK')
                ->first();
            $param['itemSP'] = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();
            $param['itemCatatanSlik'] = ItemModel::where('nama', 'Catatan Slik')->first();

            $nasabah = PengajuanModel::select('pengajuan_dagulir.*', 'kabupaten.id as kabupaten_id', 'kabupaten.kabupaten', 'kecamatan.id as kecamatan_id', 'kecamatan.id_kabupaten', 'kecamatan.kecamatan', 'desa.id as desa_id', 'desa.id_kabupaten', 'desa.id_kecamatan', 'desa.desa')
                ->join('pengajuan_dagulir', 'pengajuan_dagulir.id', 'pengajuan.dagulir_id')
                ->leftJoin('kabupaten', 'kabupaten.id', 'pengajuan_dagulir.kotakab_ktp')
                ->leftJoin('kecamatan', 'kecamatan.id', 'pengajuan_dagulir.kec_ktp')
                ->leftJoin('desa', 'desa.id', 'pengajuan_dagulir.desa_ktp')
                ->where('pengajuan.id', $id)
                ->first();
            $param['dataNasabah'] = $nasabah;
            $param['dataDesa'] = Desa::where('id', $nasabah->desa_id)->first();
            $param['dataKecamatan'] = Kecamatan::where('id', $nasabah->kec_ktp)->first();
            $param['dataKabupaten'] = Kabupaten::where('id', $nasabah->kotakab_ktp)->first();
            $param['dataKabupatenDom'] = Kabupaten::where('id', $nasabah->kotakab_dom)->first();
            $param['dataKecamatanDom'] = Kecamatan::where('id', $nasabah->kec_dom)->first();
            $param['dataKabupatenUsaha'] = Kabupaten::where('id', $nasabah->kotakab_usaha)->first();
            $param['dataKecamatanUsaha'] = Kecamatan::where('id', $nasabah->kec_usaha)->first();
            // return $param['dataKecamatanDom'];
            $param['jenis_usaha'] = config('dagulir.jenis_usaha');

            $dataUmum = PengajuanModel::select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang', 'pengajuan.skema_kredit', 'pengajuan.average_by_sistem', 'pengajuan.average_by_penyelia', 'pengajuan.average_by_pbo', 'pengajuan.average_by_pbp', 'pengajuan.id_pbo', 'pengajuan.id_pbp')
                ->find($id);
            $param['dataUmum'] = $dataUmum;
            $param['comment'] = KomentarModel::where('id_pengajuan', $id)->first();

            $param['alasanPengembalian'] = AlasanPengembalianData::where('id_pengajuan', $id)
                                                                ->join('users', 'users.id', 'alasan_pengembalian_data.id_user')
                                                                ->select('users.nip', 'alasan_pengembalian_data.*')
                                                                ->get();

            $param['pendapatDanUsulan'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_staff', 'komentar_penyelia', 'komentar_pincab', 'komentar_pbo', 'komentar_pbp')->first();
            $param['plafonUsulan'] = PlafonUsulan::where('id_pengajuan', $id)->select(
                'plafon_usulan_penyelia',
                'jangka_waktu_usulan_penyelia',
                'plafon_usulan_pbo',
                'jangka_waktu_usulan_pbo',
                'plafon_usulan_pbp',
                'jangka_waktu_usulan_pbp',
                'plafon_usulan_pincab',
                'jangka_waktu_usulan_pincab'
                )->first();
            $dokumenUsaha = DB::table('item')
                ->where('nama', 'LIKE', '%NIB%')
                ->orWhere('nama', 'LIKE', '%Surat Keterangan Usaha%')
                ->orWhere('nama', 'LIKE', '%NPWP%')
                ->get('id');
            $countDoc = 0;
            foreach ($dokumenUsaha as $idDoc) {
                $count = DB::table('jawaban_text')
                    ->where('id_pengajuan', $id)
                    ->where('id_jawaban', $idDoc->id)
                    ->count();
                $countDoc += $count;
            }
            $param['countIjin'] = $countDoc;
            $logPengajuan = DB::table('log_pengajuan')->selectRaw("DISTINCT(date(created_at)) as tgl")->where('id_pengajuan', $id)->get();
            $log = array();
            if($logPengajuan){
                foreach($logPengajuan as $item){
                    $itemLog = DB::table('log_pengajuan')
                        ->where('id_pengajuan', $id)
                        ->whereDate('created_at', $item->tgl)
                        ->get();
                    $itemsLog = array();

                    foreach($itemLog as $itemLogPengajuan){
                        array_push($itemsLog, $itemLogPengajuan);
                    }
                    array_push($log, [
                        'tgl' => $item->tgl,
                        'data' => $itemLog
                    ]);
                }
            } else {
                $log = [];
            }
            $param['logPengajuan'] = $log;
            $param['rolesPemroses'] = $this->repo->getDataPemroses($dataUmum);

            return view('dagulir.pengajuan-kredit.review-pincab-new', $param);

        } else {
            return redirect()->back()->withError('Tidak memiliki hak akses.');
        }
    }

    public function storeSipde($id_pengajuan, $plafon=null, $tenor=null) {
        DB::beginTransaction();
        try {
            $pengajuan = PengajuanModel::with('pendapatPerAspek')->find($id_pengajuan);
            $pengajuan_dagulir = PengajuanDagulir::find($pengajuan->dagulir_id);
            $data = sipde_token();
            $body = [
                "nama" => $pengajuan_dagulir->nama,
                "nik" => $pengajuan_dagulir->nik,
                "tempat_lahir" => $pengajuan_dagulir->tempat_lahir,
                "tanggal_lahir" => $pengajuan_dagulir->tanggal_lahir,
                "telp" => $pengajuan_dagulir->telp,
                "jenis_usaha" => $pengajuan_dagulir->jenis_usaha,
                "nominal_pengajuan" => $plafon ? $plafon : $pengajuan_dagulir->nominal,
                "tujuan_penggunaan" => $pengajuan_dagulir->tujuan_penggunaan,
                "jangka_waktu" => $tenor ? $tenor : $pengajuan_dagulir->jangka_waktu,
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
                "tempat_berdiri" => $pengajuan_dagulir->tempat_berdiri,
                "tanggal_berdiri" => $pengajuan_dagulir->tanggal_berdiri,
                "email" => $pengajuan_dagulir->email ? $pengajuan_dagulir->email : "",
                "nama_pj" => $pengajuan_dagulir->nama_pj_ketua ??  null,
            ];
            $pengajuan_dagulir = Http::withHeaders([
                'Authorization' => 'Bearer ' .$data['token'],
            ])->post(config('dagulir.host').'/pengajuan.json', $body)->json();

            if (array_key_exists('data', $pengajuan_dagulir)) {
                if ($pengajuan_dagulir['data']['status_code'] == 200) {
                    $update_pengajuan_dagulir = PengajuanDagulir::find($pengajuan->dagulir_id);
                    $update_pengajuan_dagulir->kode_pendaftaran = $pengajuan_dagulir['data']['kode_pendaftaran'];
                    $update_pengajuan_dagulir->status = 1;
                    $update_pengajuan_dagulir->update();

                    // Log Pengajuan review
                    $dagulir = PengajuanDagulir::find($update_pengajuan_dagulir->dagulir_id);
                    $namaNasabah = 'undifined';

                    if ($dagulir)
                        $namaNasabah = $dagulir->nama;

                    $role = Auth::user()->role;
                    $this->logPengajuan->store($role . ' dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' mengirimkan data dagulir dengan pengajuan atas nama ' . $namaNasabah, $pengajuan->id, Auth::user()->id, Auth::user()->nip);

                    DB::commit();

                    $response = [
                        'status' => 'success',
                        'kode_pendaftaran' => $pengajuan_dagulir['data']['kode_pendaftaran'],
                    ];
                    return $response;
                }
                else {
                    DB::commit();
                    return $pengajuan_dagulir['data']['message'];
                }
            }
            else {
                DB::commit();
                $message = 'Terjadi kesalahan.';
                if (array_key_exists('error', $pengajuan_dagulir)) $message .= ' '.$pengajuan_dagulir['error'];

                return $message;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    private function getStatusBody($kode_pendaftaran, $status, $lampiran_analisa = null, $jangka_waktu = null, $realisasi_dana = null) {
        $body = [];
        if ($status == 1) {
            // Survei
            $body = [
                "kode_pendaftaran" => $kode_pendaftaran,
                "status" => $status,
                "lampiran_analisa" => "",
                "jangka_waktu" => "",
                "realisasi_dana" => ""
            ];
        }
        if ($status == 2) {
            // Analisa
            $body = [
                "kode_pendaftaran" => $kode_pendaftaran,
                "status" => $status,
                "lampiran_analisa" => $lampiran_analisa,
                "jangka_waktu" => "",
                "realisasi_dana" => ""
            ];
        }
        if ($status == 3) {
            // Disetujui
            $body = [
                "kode_pendaftaran" => $kode_pendaftaran,
                "status" => $status,
                "lampiran_analisa" => "",
                "jangka_waktu" => "",
                "realisasi_dana" => "",
            ];
        }
        if ($status == 4) {
            // Ditolak
            $body = [
                "kode_pendaftaran" => $kode_pendaftaran,
                "status" => $status,
                "lampiran_analisa" => "",
                "jangka_waktu" => "",
                "realisasi_dana" => "",
            ];
        }
        if ($status == 5) {
            // REALISASI KREDIT
            $body = [
                "kode_pendaftaran" => $kode_pendaftaran,
                "status" => $status,
                "lampiran_analisa" => "",
                "jangka_waktu" => $jangka_waktu,
                "realisasi_dana" => $realisasi_dana,
            ];
        }
        if ($status == 6) {
            // SELESAI
            $body = [
                "kode_pendaftaran" => $kode_pendaftaran,
                "status" => $status,
                "lampiran_analisa" => "",
                "jangka_waktu" => "",
                "realisasi_dana" => "",
            ];
        }
        if ($status == 7) {
            // DIBATALKAN
            $body = [
                "kode_pendaftaran" => $kode_pendaftaran,
                "status" => $status,
                "lampiran_analisa" => "",
                "jangka_waktu" => "",
                "realisasi_dana" => "",
            ];
        }
        if ($status == 8) {
            // DITINDAKLANJUTI
            $body = [
                "kode_pendaftaran" => $kode_pendaftaran,
                "status" => $status,
                "lampiran_analisa" => "",
                "jangka_waktu" => "",
                "realisasi_dana" => "",
            ];
        }

        return $body;
    }

    public function  updateStatus($kode_pendaftaran, $status, $lampiran_analisa = null, $jangka_waktu = null, $realisasi_dana = null) {
        $data = sipde_token();
        $body = $this->getStatusBody($kode_pendaftaran, $status, $lampiran_analisa, $jangka_waktu, $realisasi_dana);
        if (!empty($body)) {
            $host = config('dagulir.host');
            DB::beginTransaction();
            try {
                $pengajuan_dagulir = Http::withHeaders([
                    'Authorization' => 'Bearer ' .$data['token'],
                ])->post($host.'/update_status.json', $body)->json();

                if ($pengajuan_dagulir) {
                    if (array_key_exists('data', $pengajuan_dagulir)) {
                        $response = $pengajuan_dagulir['data'];
                        // Check status code
                        if (array_key_exists('status_code', $response)) {
                            if ($response['status_code'] == 200) {
                                // Update status
                                DB::table('pengajuan_dagulir')
                                ->where('kode_pendaftaran', $kode_pendaftaran)
                                ->update([
                                    'status' => $status,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);

                                DB::commit();
                                return $response['status_code'];
                            }
                            else {
                                DB::commit();
                                return array_key_exists('message', $response) ? $response['message'] : 'failed';
                            }
                        }
                        DB::commit();
                        return $response;
                    }
                    DB::commit();
                    return $pengajuan_dagulir;
                }
                else {
                    DB::commit();
                    return $pengajuan_dagulir;
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return $e->getMessage();
            }
        }
        else {
            return 'empty body';
        }
    }

    public function getDocuments($id_pengajuan) {
        $result = [];
        ini_set('max_execution_time', '300');
        try {
            $pengajuan = PengajuanModel::with([
                                            'dagulir' => function($query) {
                                                $query->select('id', 'foto_nasabah');
                                            }
                                        ])
                                        ->select('id', 'dagulir_id', 'pk')
                                        ->find($id_pengajuan);
            $dagulir_id = $pengajuan->dagulir_id;
            // Foto nasabah
            $filename = $pengajuan->dagulir->foto_nasabah;
            $foto_nasabah = public_path("upload/$id_pengajuan/$dagulir_id/$filename");
            if (file_exists($foto_nasabah)) {
                $foto_nasabah_ext = explode('.', $filename)[1];
                $foto_nasabah_base64 = "data:@image/$foto_nasabah_ext;base64,".base64_encode(file_get_contents($foto_nasabah));
            }

            // Foto usaha
            $jawaban_usaha = JawabanTextModel::select('id', 'id_jawaban AS item_id', 'opsi_text AS file')
                                        ->where('id_pengajuan', $pengajuan->id)
                                        ->where('id_jawaban', 154) // Foto usaha item id
                                        ->orderBy('id')
                                        ->first();
            $foto_usaha_base64 = null;
            if ($jawaban_usaha) {
                $item_id = $jawaban_usaha->item_id;
                $filename = $jawaban_usaha->file;
                $foto_usaha = public_path("upload/$id_pengajuan/$item_id/$filename");
                if (file_exists($foto_usaha)) {
                    $foto_usaha_ext = explode('.', $filename)[1];
                    $foto_usaha_base64 = "data:@image/$foto_usaha_ext;base64,".base64_encode(file_get_contents($foto_usaha));
                }
            }

            // Foto agunan
            $jawaban_agunan = JawabanTextModel::select('id', 'id_jawaban AS item_id', 'opsi_text AS file')
                                        ->where('id_pengajuan', $pengajuan->id)
                                        ->where('id_jawaban', 148) // Foto agunan item id
                                        ->orderBy('id')
                                        ->first();
            $foto_agunan_base64 = null;
            if ($jawaban_agunan) {
                $item_id = $jawaban_agunan->item_id;
                $filename = $jawaban_agunan->file;
                $foto_agunan = public_path("upload/$id_pengajuan/$item_id/$filename");
                if (file_exists($foto_agunan)) {
                    $foto_agunan_ext = explode('.', $filename)[1];
                    $foto_agunan_base64 = "data:@image/$foto_agunan_ext;base64,".base64_encode(file_get_contents($foto_agunan));
                }
            }

            $result = [
                'foto_nasabah' => $foto_nasabah_base64,
                'foto_tempat_usaha' => $foto_usaha_base64,
                'foto_agunan' => $foto_agunan_base64,
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'status' => 'failed',
                'message' => $e->getMessage(),
            ];
            return $result;
        }
    }

    public function syaratDokumen($kode_pendaftaran, $file_pk_base64) {
        $dagulir = PengajuanDagulir::select('pengajuan_dagulir.id', 'p.id AS pengajuan_id')
                                    ->join('pengajuan AS p', 'p.dagulir_id', 'pengajuan_dagulir.id')
                                    ->where('pengajuan_dagulir.kode_pendaftaran', $kode_pendaftaran)
                                    ->first();
        if ($dagulir) {
            // Get foto
            $filesArr = $this->getDocuments($dagulir->pengajuan_id);
            if (array_key_exists('message', $filesArr)) {
                // Failed to get documents
                return $filesArr['message'];
            }
            else {
                if (!$filesArr['foto_nasabah']) {
                    return 'Foto nasabah tidak ditemukan';
                }
                else if (!$filesArr['foto_tempat_usaha']) {
                    return 'Foto tempat usaha tidak ditemukan';
                }
                else if (!$filesArr['foto_agunan']) {
                    return 'Foto agunan tidak ditemukan';
                }
                else {
                    $data = sipde_token();
                    $body = [
                        "kode_pendaftaran" => $kode_pendaftaran,
                        "foto_nasabah" => $filesArr['foto_nasabah'],
                        "foto_tempat_usaha" => $filesArr['foto_tempat_usaha'],
                        "foto_agunan" => $filesArr['foto_agunan'],
                        "akad_kredit" => $file_pk_base64, // PK
                    ];
                    if (!empty($body)) {
                        $host = config('dagulir.host');
                        try {
                            $upload_syarat = Http::withHeaders([
                                'Authorization' => 'Bearer ' .$data['token'],
                            ])->post($host.'/syarat_dokumen.json', $body)->json();

                            if ($upload_syarat) {
                                if (array_key_exists('data', $upload_syarat)) {
                                    $response = $upload_syarat['data'];
                                    // Check status code
                                    if (array_key_exists('status_code', $response)) {
                                        if ($response['status_code'] == 200) {
                                            // Update status
                                            return $response['status_code'];
                                        }
                                        else {
                                            return array_key_exists('message', $response) ? $response['message'] : 'failed';
                                        }
                                    }
                                    return $response;
                                }
                                if (array_key_exists('error', $upload_syarat)) {
                                    return 'API Error: '.$upload_syarat['error'];
                                }
                                return $upload_syarat;
                            }
                            else {
                                return $upload_syarat;
                            }
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return $e->getMessage();
                        }
                    }
                    else {
                        return 'empty body';
                    }
                }
            }
        }
        else {
            return 'pengajuan tidak ditemukan';
        }
    }

    public function index(Request $request)
    {
        Session::put('is_dagulir', true);
        $id_cabang = Auth::user()->id_cabang;
        $id_user = Auth::user()->id;
        $param['cabang'] = DB::table('cabang')
            ->get();
        $role = auth()->user()->role;

        $allFilter = $request->all();
        // paginate
        $search = $request->get('q');
        $limit = $request->has('page_length') ? $request->get('page_length') : 10;
        $page = $request->has('page') ? $request->get('page') : 10;
        if ($role == 'Staf Analis Kredit') {
            $pengajuan_dagulir = $this->repo->get($search,$limit,$page, 'Staf Analis Kredit', $id_user,$allFilter);
            $pengajuan_sipde = $this->repo->get($search,$limit,$page, 'Staf Analis Kredit', $id_user, $allFilter, 'sipde');
        } elseif ($role == 'Penyelia Kredit') {
            $pengajuan_dagulir = $this->repo->get($search,$limit,$page, 'Penyelia Kredit', $id_user,$allFilter);
            $pengajuan_sipde = $this->repo->get($search,$limit,$page, 'Penyelia Kredit', $id_user, $allFilter, 'sipde');
        } elseif (auth()->user()->role == 'PBO') {
            $pengajuan_dagulir = $this->repo->get($search,$limit,$page, 'PBO', $id_user,$allFilter);
            $pengajuan_sipde = $this->repo->get($search,$limit,$page, 'PBO', $id_user, $allFilter, 'sipde');
        } elseif (auth()->user()->role == 'PBP') {
            $pengajuan_dagulir = $this->repo->get($search,$limit,$page, 'PBP', $id_user,$allFilter);
            $pengajuan_sipde = $this->repo->get($search,$limit,$page, 'PBP', $id_user, $allFilter, 'sipde');
        } elseif ($role == 'Pincab') {
            $pengajuan_dagulir = $this->repo->get($search,$limit,$page, 'Pincab', $id_user,$allFilter);
            $pengajuan_sipde = $this->repo->get($search,$limit,$page, 'Pincab', $id_user, $allFilter, 'sipde');
        } else {
            $pengajuan_dagulir = $this->repo->get($search,$limit,$page, 'Administrator', $id_user,$allFilter);
            $pengajuan_sipde = $this->repo->get($search,$limit,$page, 'Administrator', $id_user, $allFilter, 'sipde');
        }
        // return $pengajuan_dagulir;
        return view('dagulir.index',[
            'data' => $pengajuan_dagulir,
            'data_sipde' => $pengajuan_sipde,
        ]);
    }

    public function sendToPincab(Request $request)
    {
        $id = $request->get('id_pengajuan');
        try {
            $pengajuan = PengajuanModel::find($id);
            $komentar = KomentarModel::where('id_pengajuan',$id)->first();
            if ($pengajuan) {
                if ($komentar->komentar_penyelia == null) {
                    alert()->warning('Waning','Data pengajuan belum di review,tidak dapat melanjutkan ke pincab.');
                    return redirect()->route('dagulir.pengajuan.index');
                }
                $pincab = User::select('id')
                        ->where('id_cabang', $pengajuan->id_cabang)
                        ->where('role', 'Pincab')
                        ->first();
                if ($pincab) {
                    $pengajuan->posisi = "Pincab";
                    $pengajuan->id_pincab = $pincab->id;
                    $pengajuan->tanggal_review_pincab = date('Y-m-d');
                    $pengajuan->update();

                    Alert::success('success', 'Berhasil mengganti posisi');
                    return redirect()->back();
                } else {
                    alert()->error('Error','User pincab tidak ditemukan pada cabang ini');

                    return redirect()->back();
                }
            } else {
                alert()->error('Error','Data pengajuan tidak ditemukan');
                return back();
            }
        } catch (Exception $e) {
            return redirect()->back();
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan');
        };
    }

    public function accPengajuan($id, Request $request)
    {
        ini_set('max_execution_time', 120);
        DB::beginTransaction();
        try {
            if (auth()->user()->role == 'Pincab') {
                $pengajuan = PengajuanModel::find($id);
                $currentPosisi = $pengajuan->posisi;
                if ($pengajuan) {
                    $idKomentar = KomentarModel::where('id_pengajuan', $pengajuan->id)->first();
                    KomentarModel::where('id', $idKomentar->id)->update(
                        [
                            'komentar_pincab' => $request->pendapat_usulan,
                            'id_pincab' => Auth::user()->id,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]
                    );
                    $pengajuan->id_pincab = auth()->user()->id;
                    $pengajuan->tanggal_review_pincab = date('Y-m-d');
                    $pengajuan->posisi = "Selesai";
                    $pengajuan->tanggal_review_pincab = date(now());
                    $pengajuan->update();

                    $plafonUsulan = PlafonUsulan::where('id_pengajuan', $id)->first();
                    $plafon_acc = intval(str_replace('.','', $request->get('nominal_disetujui')));
                    $tenor_acc = intval($request->get('jangka_waktu_disetujui'));

                    if ($plafonUsulan) {
                        $plafonUsulan->plafon_usulan_pincab = $plafon_acc;
                        $plafonUsulan->jangka_waktu_usulan_pincab = $tenor_acc;
                        $plafonUsulan->updated_at = date('Y-m-d H:i:s');
                        $plafonUsulan->update();
                    }

                    $nasabah = PengajuanDagulir::select('kode_pendaftaran', 'status', 'nama', 'email', 'nominal', 'jangka_waktu', 'from_apps')->find($pengajuan->dagulir_id);
                    $namaNasabah = 'undifined';
                    if ($nasabah)
                        $namaNasabah = $nasabah->nama;
                    if ($nasabah->from_apps == 'pincetar') {
                        $kode_pendaftaran = false;
                        if ($nasabah->kode_pendaftaran) {
                            $kode_pendaftaran = $nasabah->kode_pendaftaran;
                            $storeSIPDE = 'success';
                        }
                        else {
                            // HIT Pengajuan endpoint dagulir
                            $nasabah_nominal = $nasabah->nominal;
                            $nasabah_jangka_waktu = $nasabah->jangka_waktu;
                            $storeSIPDE = $this->storeSipde($id,$nasabah_nominal, $nasabah_jangka_waktu);
                            if (is_array($storeSIPDE)) {
                                $kode_pendaftaran = array_key_exists('kode_pendaftaran', $storeSIPDE) ? $storeSIPDE['kode_pendaftaran'] : false;
                            }
                        }
                        $delay = 1500000; // 1.5 sec
                        if ($storeSIPDE == 'success' || $kode_pendaftaran) {
                            // HIT update status survei endpoint dagulir
                            $survei = $this->updateStatus($kode_pendaftaran, 1);
                            if ($currentPosisi == 'Pincab') {
                                if (is_array($survei)) {
                                    // Fail block
                                    if ($survei['message'] != 'Update Status Gagal. Anda tidak bisa mengubah status, karena status saat ini adalah SURVEY') {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $survei);
                                        return redirect()->back();
                                    }
                                }
                                else {
                                    if ($survei != 200) {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $survei);
                                        return redirect()->back();
                                    }
                                }
                                usleep($delay);
                            }

                            // HIT update status analisa endpoint dagulir
                            if ($nasabah->status != 2) {
                                // $filename =  public_path() . "/cetak_surat/$id.pdf";
                                // $lampiran_analisa = lampiranAnalisa($filename);
                                $cetak_lampiran_analisa = $this->cetakLampiranAnalisa($id);
                                $lampiran_analisa = null;
                                if ($cetak_lampiran_analisa['status'] == 'success') {
                                    $lampiran_analisa = "data:@application/pdf;base64,".base64_encode(file_get_contents($cetak_lampiran_analisa['filepath']));
                                }

                                $analisa = $this->updateStatus($kode_pendaftaran, 2, $lampiran_analisa);
                                if (is_array($analisa)) {
                                    // Fail block
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $analisa);
                                    return redirect()->back();
                                }
                                else {
                                    if ($survei != 200 || $survei != '200') {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $analisa);
                                        return redirect()->back();
                                    }
                                }
                                usleep($delay);
                            }

                            // HIT update status disetujui endpoint dagulir
                            if ($nasabah->status != 3) {
                                $setuju = $this->updateStatus($kode_pendaftaran, 3);
                                if (is_array($setuju)) {
                                    // Fail block
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $setuju);
                                    return redirect()->back();
                                }
                                else {
                                    if ($setuju != 200) {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $setuju);
                                        return redirect()->back();
                                    }
                                }
                            }

                            DB::commit();
                            event(new EventMonitoring('menyetujui pengajuan'));
                            Alert::success('success', 'Berhasil menyetujui pengajuan');
                            return redirect()->route('dagulir.pengajuan.index')->withStatus('Berhasil menyetujui pengajuan.');
                        }
                        else {
                            DB::rollBack();
                            alert()->error('Error API', $storeSIPDE)->autoClose(5000);
                            Alert::error('Error API', $storeSIPDE);
                            return redirect()->back();

                        }
                    }
                    else {
                        // Update status
                        $kode_pendaftaran = $nasabah->kode_pendaftaran;
                        $delay = 1500000; // 1.5 sec
                        if ($kode_pendaftaran) {
                            // HIT update status survei endpoint dagulir
                            if ($nasabah->status != 1) {
                                $survei = $this->updateStatus($kode_pendaftaran, 1);
                                if ($currentPosisi == 'Pincab') {
                                    if (is_array($survei)) {
                                        // Fail block
                                        if ($survei['message'] != 'Update Status Gagal. Anda tidak bisa mengubah status, karena status saat ini adalah SURVEY') {
                                            DB::rollBack();
                                            alert()->error('Peringatan(API)', $survei);
                                            return redirect()->back();
                                        }
                                    }
                                    else {
                                        if ($survei != 200) {
                                            DB::rollBack();
                                            alert()->error('Peringatan(API)', $survei);
                                            return redirect()->back();
                                        }
                                    }
                                    usleep($delay);
                                }
                            }

                            // HIT update status analisa endpoint dagulir
                            if ($nasabah->status != 2) {
                                $cetak_lampiran_analisa = $this->cetakLampiranAnalisa($id);
                                $lampiran_analisa = null;
                                if ($cetak_lampiran_analisa['status'] == 'success') {
                                    $lampiran_analisa = "data:@application/pdf;base64,".base64_encode(file_get_contents($cetak_lampiran_analisa['filepath']));
                                }
                                $analisa = $this->updateStatus($kode_pendaftaran, 2, $lampiran_analisa);
                                if (is_array($analisa)) {
                                    // Fail block
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $analisa);
                                    return redirect()->back();
                                }
                                else {
                                    if ($survei != 200 || $survei != '200') {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $analisa);
                                        return redirect()->back();
                                    }
                                }
                                usleep($delay);
                            }

                            // HIT update status disetujui endpoint dagulir
                            if ($nasabah->status != 3) {
                                $setuju = $this->updateStatus($kode_pendaftaran, 3);
                                if (is_array($setuju)) {
                                    // Fail block
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $setuju);
                                    return redirect()->back();
                                }
                                else {
                                    if ($setuju != 200) {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $setuju);
                                        return redirect()->back();
                                    }
                                }
                            }

                            DB::commit();
                            event(new EventMonitoring('menyetujui pengajuan'));
                            Alert::success('success', 'Berhasil menyetujui pengajuan');
                            return redirect()->route('dagulir.pengajuan.index');
                        }
                        else {
                            DB::rollBack();
                            alert()->error('Peringatan', 'Kode pendaftaran tidak ditemukan');
                            return redirect()->back();
                        }
                    }
                }
                else {
                    alert()->error('Peringatan', 'Data pengajuan tidak ditemukan.');
                    return redirect()->back();
                }
            } else {
                alert()->error('Error','Tidak memiliki hak akses');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            alert()->error('Error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function decPengajuan($id, Request $request)
    {
        ini_set('max_execution_time', 120);
        DB::beginTransaction();
        try {
            $pengajuan = PengajuanModel::find($id);
            $currentPosisi = $pengajuan->posisi;
            $komentarPincab = KomentarModel::where('id_pengajuan', $id)->first();
            if (auth()->user()->role == 'Pincab') {
                if ($currentPosisi == 'Pincab') {
                    $pengajuan->posisi = "Ditolak";
                    $pengajuan->tanggal_review_pincab = date(now());
                    $pengajuan->update();

                    $plafonUsulan = PlafonUsulan::where('id_pengajuan', $id)->first();
                    $plafon_acc = intval(str_replace('.','', $request->get('nominal_disetujui')));
                    $tenor_acc = intval($request->get('jangka_waktu_disetujui'));

                    if ($plafonUsulan) {
                        $plafonUsulan->plafon_usulan_pincab = $plafon_acc;
                        $plafonUsulan->jangka_waktu_usulan_pincab = $tenor_acc;
                        $plafonUsulan->updated_at = date('Y-m-d H:i:s');
                        $plafonUsulan->update();
                    }

                    $nasabah = PengajuanDagulir::select('kode_pendaftaran', 'nama', 'status')->find($pengajuan->dagulir_id);
                    if ($nasabah->kode_pendaftaran) {
                        $kode_pendaftaran = $nasabah->kode_pendaftaran;
                        $storeSIPDE = 'success';
                    }
                    else {
                        // HIT Pengajuan endpoint dagulir
                        $storeSIPDE = $this->storeSipde($id);
                        if (is_array($storeSIPDE)) {
                            $kode_pendaftaran = array_key_exists('kode_pendaftaran', $storeSIPDE) ? $storeSIPDE['kode_pendaftaran'] : false;
                        }
                    }
                    $delay = 1500000; // 1.5 sec

                    if ($kode_pendaftaran) {
                        // HIT update status survei endpoint dagulir
                        if ($nasabah->status != 1) {
                            $survei = $this->updateStatus($kode_pendaftaran, 1);
                            if ($currentPosisi == 'Pincab') {
                                if (is_array($survei)) {
                                    // Fail block
                                    if ($survei['message'] != 'Update Status Gagal. Anda tidak bisa mengubah status, karena status saat ini adalah SURVEY') {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $survei);
                                        return redirect()->back();
                                    }
                                }
                                else {
                                    if ($survei != 200) {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $survei);
                                        return redirect()->back()->withError($survei);
                                    }
                                }
                                usleep($delay);
                            }
                        }

                        // HIT update status analisa endpoint dagulir
                        if ($nasabah->status != 2) {
                            // $filename = public_path('upload/cetak_surat/'.$pengajuan->id.'.'.'pdf');
                            $cetak_lampiran_analisa = $this->cetakLampiranAnalisa($id);
                            $lampiran_analisa = null;
                            if ($cetak_lampiran_analisa['status'] == 'success') {
                                $lampiran_analisa = "data:@application/pdf;base64,".base64_encode(file_get_contents($cetak_lampiran_analisa['filepath']));
                            }
                            $analisa = $this->updateStatus($kode_pendaftaran, 2, $lampiran_analisa);
                            if (is_array($analisa)) {
                                // Fail block
                                DB::rollBack();
                                alert()->error('Peringatan(API)', $analisa);
                                return redirect()->back();
                            }
                            else {
                                if ($analisa != 200 || $analisa != '200') {
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $analisa);
                                    return redirect()->back();
                                }
                            }
                            usleep($delay);
                        }

                        // HIT update status ditolak endpoint dagulir
                        if ($nasabah->status != 3) {
                            $ditolak = $this->updateStatus($kode_pendaftaran, 4);
                            if (is_array($ditolak)) {
                                // Fail block
                                DB::rollBack();
                                alert()->error('Peringatan(API)', $ditolak);
                                return redirect()->back();
                            }
                            else {
                                if ($ditolak != 200) {
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $ditolak);
                                    return redirect()->back();
                                }
                            }
                        }

                        $namaNasabah = 'undifined';
                        if ($nasabah)
                            $namaNasabah = $nasabah->nama;

                        $this->logPengajuan->store('Pincab dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menolak pengajuan atas nama ' . $namaNasabah . '.', $id, Auth::user()->id, Auth::user()->nip);
                    }
                    else {
                        DB::rollBack();
                        alert()->error('Peringatan(API)', $storeSIPDE);
                        return redirect()->back();
                    }

                    DB::commit();

                    event(new EventMonitoring('tolak pengajuan'));
                    Alert::success('success', 'Berhasil menolak pengajuan');
                    return redirect()->route('dagulir.pengajuan.index');
                } else {
                    alert()->error('Error','Posisi data belum sampai Pincab');
                    return redirect()->back();
                }
            } else {
                alert()->error('Error','Tidak memiliki hak akses');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            alert()->error('Error', $e->getMessage());
            return redirect()->back();
        }
    }


    function CetakPK($id) {
        $count = DB::table('log_cetak_kkb')
        ->where('id_pengajuan', $id)
        ->count('tgl_cetak_pk');
        if ($count < 1) {
            DB::table('log_cetak_kkb')
            ->where('id_pengajuan', $id)
            ->update([
                'tgl_cetak_pk' => now()
            ]);
        }

        $dataUmum = PengajuanModel::select('pengajuan.id', 'pengajuan.skema_kredit', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang', 'pengajuan.skema_kredit', 'id_pincab', 'id_penyelia')
        ->find($id);
        $param['dataUmum'] = $dataUmum;

        if ($param['dataUmum']->skema_kredit == 'Dagulir') {
            $dataNasabah = DB::table('pengajuan_dagulir')->select('pengajuan_dagulir.*', 'kabupaten.id as kabupaten_id', 'kabupaten.kabupaten', 'kecamatan.id as kecamatan_id', 'kecamatan.id_kabupaten', 'kecamatan.kecamatan', 'desa.id as desa_id', 'desa.id_kabupaten', 'desa.id_kecamatan', 'desa.desa', 'pengajuan.*')
                        ->join('kabupaten', 'kabupaten.id', 'pengajuan_dagulir.kotakab_ktp')
                        ->join('kecamatan', 'kecamatan.id', 'pengajuan_dagulir.kec_ktp')
                        ->join('desa', 'desa.id', 'pengajuan_dagulir.desa_ktp')
                        ->join('pengajuan', 'pengajuan.dagulir_id', 'pengajuan_dagulir.id')
                        ->where('pengajuan.id', $id)
                        ->first();
            $param['bulan'] = date('m', strtotime($dataNasabah->tanggal));
            $param['tahun'] = date('Y', strtotime($dataNasabah->tanggal));
        }else{
            $dataNasabah = CalonNasabah::select('calon_nasabah.*','kabupaten.id as kabupaten_id','kabupaten.kabupaten','kecamatan.id as kecamatan_id','kecamatan.id_kabupaten','kecamatan.kecamatan','desa.id as desa_id','desa.id_kabupaten','desa.id_kecamatan','desa.desa')
                        ->join('kabupaten','kabupaten.id','calon_nasabah.id_kabupaten')
                        ->join('kecamatan','kecamatan.id','calon_nasabah.id_kecamatan')
                        ->join('desa','desa.id','calon_nasabah.id_desa')
                        ->where('calon_nasabah.id_pengajuan',$id)
                        ->first();
            $param['bulan'] = date('m', strtotime($dataNasabah->tanggal_lahir));
            $param['tahun'] = date('Y', strtotime($dataNasabah->tanggal_lahir));
        }


        $param['dataNasabah'] = $dataNasabah;


        $param['dataCabang'] = DB::table('cabang')
            ->where('id', $param['dataUmum']->id_cabang)
            ->first();


        $param['tglCetak'] = DB::table('log_cetak_kkb')
        ->where('id_pengajuan', $id)
        ->first();

        $kodePincab = $param['dataUmum']->skema_kredit == 'Dagulir' ? $dataNasabah->id_pincab : $dataUmum->id_pincab;
        $kodePenyelia = $param['dataUmum']->skema_kredit == 'Dagulir' ?  $dataNasabah->id_penyelia : $dataUmum->id_penyelia;

        $param['dataPincab'] = User::where('id', $kodePincab)->get();
        $param['dataPenyelia'] = User::where('id', $kodePenyelia)->get();

        $indexBulan = intval(date('m', strtotime($param['tglCetak']->tgl_cetak_pk))) - 1;
        $param['tgl'] = date('d', strtotime($param['tglCetak']->tgl_cetak_pk)) . ' ' . $this->bulan[$indexBulan] . ' ' . date('Y', strtotime($param['tglCetak']->tgl_cetak_pk));

        $param['installment'] = DB::table('jawaban_text')
        ->where('id_pengajuan', $id)
        ->where('id_jawaban', 140)
        ->first() ?? '0';

        // return $dataNasabah;
        // return view('dagulir.cetak.cetak-pk', $param);
        $pdf = PDF::loadView('dagulir.cetak.cetak-pk', $param);
        return $pdf->download('PK-' . $dataNasabah->nama . '.pdf');
        return redirect()->back();
    }
    public function cetakSPPk($id)
    {
        $count = DB::table('log_cetak_kkb')
        ->where('id_pengajuan', $id)
            ->count('*');
        if ($count < 1) {
            DB::table('log_cetak_kkb')
            ->insert([
                'id_pengajuan' => $id,
                'tgl_cetak_sppk' => now()
            ]);
        } else {
            DB::table('log_cetak_kkb')
            ->where('id_pengajuan', $id)
                ->update([
                    'tgl_cetak_sppk' => now()
                ]);
        }

        $param['dataUmum'] = PengajuanModel::select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang', 'skema_kredit')->find($id);

        if ($param['dataUmum']->skema_kredit == 'Dagulir') {
            $dataUmum = DB::table('pengajuan_dagulir')->select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang', 'pengajuan.skema_kredit')
                        ->join('pengajuan', 'pengajuan.dagulir_id', 'pengajuan_dagulir.id')
                        ->where('pengajuan.id', $id)
                        ->first();
            $dataNasabah = DB::table('pengajuan_dagulir')->select('kabupaten.id as kabupaten_id', 'kabupaten.kabupaten', 'kecamatan.id as kecamatan_id', 'kecamatan.id_kabupaten', 'kecamatan.kecamatan', 'desa.id as desa_id', 'desa.id_kabupaten', 'desa.id_kecamatan', 'desa.desa', 'pengajuan_dagulir.*', 'pengajuan.*')
                        ->join('kabupaten', 'kabupaten.id', 'pengajuan_dagulir.kotakab_ktp')
                        ->join('kecamatan', 'kecamatan.id', 'pengajuan_dagulir.kec_ktp')
                        ->join('desa', 'desa.id', 'pengajuan_dagulir.desa_ktp')
                        ->join('pengajuan', 'pengajuan.dagulir_id', 'pengajuan_dagulir.id')
                        ->where('pengajuan.id', $id)
                        ->first();
            $param['bulan'] = date('d', strtotime($dataNasabah->tanggal));
            $param['tahun'] = date('Y', strtotime($dataNasabah->tanggal));
        }else{
            $dataUmum = PengajuanModel::find($id);

            $dataNasabah = CalonNasabah::select('calon_nasabah.*','kabupaten.id as kabupaten_id','kabupaten.kabupaten','kecamatan.id as kecamatan_id','kecamatan.id_kabupaten','kecamatan.kecamatan','desa.id as desa_id','desa.id_kabupaten','desa.id_kecamatan','desa.desa')
                        ->join('kabupaten','kabupaten.id','calon_nasabah.id_kabupaten')
                        ->join('kecamatan','kecamatan.id','calon_nasabah.id_kecamatan')
                        ->join('desa','desa.id','calon_nasabah.id_desa')
                        ->where('calon_nasabah.id_pengajuan',$id)
                        ->first();
            $param['bulan'] = date('m', strtotime($dataNasabah->tanggal_lahir));
            $param['tahun'] = date('Y', strtotime($dataNasabah->tanggal_lahir));
        }

        // return $dataNasabah;
        $param['dataNasabah'] = $dataNasabah;
        // return $dataUmum;
        $param['dataUmum'] = $dataUmum;


        $param['dataCabang'] = DB::table('cabang')
        ->where('id', $dataUmum->id_cabang)
        ->first();

        $tglCetak = DB::table('log_cetak_kkb')
        ->where('id_pengajuan', $id)
        ->first();
        $param['tglCetak'] = $tglCetak;

        $kodePincab = $param['dataUmum']->skema_kredit == 'Dagulir' ? $dataNasabah->id_pincab : $dataUmum->id_pincab;
        $kodePenyelia =$param['dataUmum']->skema_kredit == 'Dagulir' ?  $dataNasabah->id_penyelia : $dataUmum->id_pincab;

        $param['dataPincab'] = User::where('id', $kodePincab)->get();
        $param['dataPenyelia'] = User::where('id', $kodePenyelia)->get();

        $indexBulan = intval(date('m', strtotime($param['tglCetak']->tgl_cetak_sppk))) - 1;
        $param['tgl'] = date('d', strtotime($param['tglCetak']->tgl_cetak_sppk)) . ' ' . $this->bulan[$indexBulan] . ' ' . date('Y', strtotime($param['tglCetak']->tgl_cetak_sppk));

        $param['installment'] = DB::table('jawaban_text')
        ->where('id_pengajuan', $id)
        ->where('id_jawaban', 140)
        ->first() ?? '0';

        // return view('dagulir.cetak.cetak-sppk', $param);
        $pdf = PDF::loadView('dagulir.cetak.cetak-sppk', $param);
        return $pdf->download('SPPK-' . $dataNasabah->nama . '.pdf');
        return redirect()->back();
    }

    public function kembalikanDataKePosisiSebelumnya(Request $request){
        DB::beginTransaction();
        try{
            $dataPengajuan = PengajuanModel::find($request->id_pengajuan);
            $alasan = $request->alasan;
            $dari = $dataPengajuan->posisi;
            $ke = '';
            $backto = $request->backto;

            if ($backto == 'staf') {
                $ke = 'Proses Input Data';
            }
            else if ($backto == 'penyelia') {
                $ke = 'Review Penyelia';
            }
            else if ($backto == 'pbo') {
                $ke = 'PBO';
            }
            else if ($backto == 'pbp') {
                $ke = 'PBP';
            }

            if ($ke != '') {
                $dataPengajuan->posisi = $ke;
                $dataPengajuan->save();

                AlasanPengembalianData::insert([
                    'id_pengajuan' => $dataPengajuan->id,
                    'id_user' => auth()->user()->id,
                    'dari' => $dari,
                    'ke' => $ke,
                    'alasan' => $alasan,
                    'created_at' => now()
                ]);
                DB::commit();

                event(new EventMonitoring('kembalikan data pengajuan'));
                return redirect()->back()->withStatus('Berhasil mengembalikan data ke ' . $ke . '.');
            }
            else{
                return redirect()->back()->withError('Data tidak ditemukan');
            }
        } catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan. ' . $e->getMessage());
        } catch(QueryException $e){
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan. ' . $e->getMessage());
        }
    }

    public function postFileDagulir(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $message = null;
            switch ($request->tipe_file) {
                // File SPPK Handler
                case 'SPPK':
                    $message = 'File SPPK berhasil diupload';
                    $folderSPPK = public_path() . '/upload/' . $id . '/sppk/';
                    $fileSPPK = $request->sppk;
                    $filenameSPPK = date('YmdHis') . '.' . $fileSPPK->getClientOriginalExtension();
                    $pathSPPK = realpath($folderSPPK);
                    if (!($pathSPPK !== true and is_dir($pathSPPK))) {
                        mkdir($folderSPPK, 0755, true);
                    }
                    $fileSPPK->move($folderSPPK, $filenameSPPK);
                    DB::table('pengajuan')
                        ->where('id', $id)
                        ->update([
                            'sppk' => $filenameSPPK
                        ]);
                    DB::commit();
                    Alert()->success('success', $message);
                    return redirect()->route('dagulir.pengajuan.index');
                    break;
                case 'PO':
                    $message = 'File PO berhasil diupload';
                    $folderPO = public_path() . '/upload/' . $id . '/po/';
                    $filePO = $request->po;
                    $filenamePO = date('YmdHis') . '.' . $filePO->getClientOriginalExtension();
                    $pathPO = realpath($folderPO);
                    if (!($pathPO !== true and is_dir($pathPO))) {
                        mkdir($folderPO, 0755, true);
                    }
                    $filePO->move($folderPO, $filenamePO);
                    DB::table('pengajuan')
                        ->where('id', $id)
                        ->update([
                            'PO' => $filenamePO
                        ]);
                    DB::commit();
                    Alert()->success('success', $message);
                    return redirect()->route('dagulir.pengajuan.index');
                    break;
                    // File PK Handler
                case 'PK':
                    ini_set('max_execution_time', 120);
                    $message = 'File PK berhasil diupload';
                    $kode_pendaftaran = $request->get('kode_pendaftaran');

                    $plafon = PlafonUsulan::where('id_pengajuan',$id)->first();
                    $pengajuan = PengajuanModel::with('dagulir')->find($id);
                    // insert to dd loan
                    $repo = new MasterDanaRepository;
                    $data = $repo->getDari($pengajuan->dagulir->kode_bank_cabang);
                    $master_loan = MasterDDLoan::where('no_loan',$request->get('no_loan'))->first();
                    if (!isset($master_loan)) {
                        if ($pengajuan && $plafon) {
                            if ($data->dana_idle >= $plafon->plafon_usulan_pincab) {
                                $dana_cabang = DanaCabang::where('id_cabang',$pengajuan->dagulir->kode_bank_cabang)->first();
                                $current = $dana_cabang->dana_idle - $plafon->plafon_usulan_pincab;
                                if ($current >= 0) {
                                    $realisasi = $this->updateStatus($kode_pendaftaran, 5, null, $plafon->jangka_waktu_usulan_pincab, $plafon->plafon_usulan_pincab);
                                    $update_cabang = DanaCabang::where('id_cabang',$pengajuan->dagulir->kode_bank_cabang)->first();
                                    $update_cabang->dana_idle = $current;
                                    $update_cabang->update();

                                    $loan = new MasterDDLoan;
                                    $loan->id_cabang = $pengajuan->dagulir->kode_bank_cabang;
                                    $loan->no_loan = $request->get('no_loan');
                                    $loan->kode_pendaftaran = $pengajuan->dagulir->kode_pendaftaran;
                                    $loan->plafon = $plafon->plafon_usulan_pincab;
                                    $loan->jangka_waktu = $plafon->jangka_waktu_usulan_pincab;
                                    $loan->baki_debet = $plafon->plafon_usulan_pincab;
                                    $loan->save();
                                    $delay = 1500000; // 1.5 sec
                                    $kumulatif = new PembayaranController();
                                    $kumulatif->kumulatif_debitur($pengajuan->dagulir->kode_pendaftaran,0,$plafon->plafon_usulan_pincab,1);
                                    usleep($delay);
                                }else{
                                    DB::commit();
                                    alert()->error('Terjadi Kesalahan', 'Dana tidak cukup.');
                                    return redirect()->back();
                                }
                            }
                            else{
                                DB::commit();
                                alert()->error('Terjadi Kesalahan', 'Dana tidak cukup.');
                                return redirect()->back();
                            }
                        }else{
                            DB::commit();
                            alert()->error('Terjadi Kesalahan', 'Pengajuan atau dana cabang tidak ditemukan.');
                            return redirect()->back();
                        }
                    }else{
                        DB::commit();
                        alert()->warning('Peringatan', 'No Loan telah digunakan.');
                        return redirect()->back();
                    }
                    $folderPK = public_path() . '/upload/' . $id . '/pk/';
                    $filePK = $request->pk;
                    $filenamePK = date('YmdHis') . '.' . $filePK->getClientOriginalExtension();
                    $mime = $filePK->getClientMimeType();
                    $file_pk_base64 = "data:$mime;base64,".base64_encode(file_get_contents($filePK));
                    $pathPK = realpath($folderPK);
                    if (!($pathPK !== true and is_dir($pathPK))) {
                        mkdir($folderPK, 0755, true);
                    }
                    $filePK->move($folderPK, $filenamePK);

                    DB::table('log_cetak_kkb')
                    ->where('id_pengajuan', $id)
                    ->update([
                        'no_pk' => $request->get('no_pk'),
                        'no_loan' => $request->get('no_loan') ? $request->get('no_loan') : null,
                    ]);
                    DB::table('pengajuan')
                    ->where('id', $id)
                    ->update([
                        'pk' => $filenamePK,
                    ]);
                    $cek_skema = PengajuanModel::find($id);

                    if ($cek_skema->skema_kredit != 'Dagulir') {
                        DB::commit();
                        Alert::success('success', $message);
                        return redirect()->route('pengajuan-kredit.index');
                    }


                    if (is_array($realisasi)) {
                        DB::commit();
                        $loan = MasterDDLoan::where('kode_pendaftaran',$kode_pendaftaran)->first();
                        $update_cabang = DanaCabang::where('id_cabang',$pengajuan->dagulir->kode_bank_cabang)->first();
                        $current = $dana_cabang->dana_modal + $plafon->plafon_usulan_pincab;
                        $update_cabang->dana_idle = $current;
                        $update_cabang->update();
                        $loan->delete();
                        alert()->error('Terjadi Kesalahan', json_encode($realisasi));
                        return redirect()->back();
                    }
                    else {
                        if ($realisasi == 200) {
                            // Realisasi (Upload dokumen)
                            $upload = $this->syaratDokumen($kode_pendaftaran, $file_pk_base64);
                            if (!is_array($upload)) {
                                if ($upload == 200) {
                                    // Update to SELESAI
                                    $update_selesai = $this->updateStatus($kode_pendaftaran, 6);

                                    if (!is_array($update_selesai)) {
                                        if ($update_selesai == 200) {
                                            DB::commit();
                                            Alert::success('success', $message);
                                            return redirect()->route('dagulir.pengajuan.index');
                                        }
                                    }
                                    else {
                                        DB::rollBack();
                                        alert()->error('Terjadi Kesalahan', $update_selesai);
                                        return redirect()->back();
                                    }
                                }
                                else {
                                    DB::rollBack();
                                    alert()->error('Terjadi Kesalahan', $upload);
                                    return redirect()->back();
                                }
                            }
                            else {
                                DB::commit();
                                $loan = MasterDDLoan::where('kode_pendaftaran',$kode_pendaftaran)->first();
                                $update_cabang = DanaCabang::where('id_cabang',$pengajuan->dagulir->kode_bank_cabang)->first();
                                $current = $dana_cabang->dana_modal + $plafon->plafon_usulan_pincab;
                                $update_cabang->dana_idle = $current;
                                $update_cabang->update();
                                $loan->delete();
                                alert()->error('Terjadi Kesalahan', $upload);
                                return redirect()->back();
                            }
                        }
                        else {
                            DB::commit();
                            $loan = MasterDDLoan::where('kode_pendaftaran',$kode_pendaftaran)->first();
                            $update_cabang = DanaCabang::where('id_cabang',$pengajuan->dagulir->kode_bank_cabang)->first();
                            $current = $dana_cabang->dana_modal + $plafon->plafon_usulan_pincab;
                            $update_cabang->dana_idle = $current;
                            $update_cabang->update();
                            $loan->delete();
                            DB::commit();
                            alert()->error('Terjadi Kesalahan', $realisasi);
                            return redirect()->route('dagulir.pengajuan.index');
                        }
                    }
                    break;
            }
        } catch (Exception $e) {
            DB::rollBack();
            alert()->error('Terjadi Kesalahan', $e->getMessage());
            return redirect()->route('dagulir.pengajuan.index');
        } catch (QueryException $e) {
            DB::rollBack();
            alert()->error('Terjadi Kesalahan', $e->getMessage());
            return redirect()->route('dagulir.pengajuan.index');
        }
    }

    public function cetakDagulir($id)
    {
        $param['dataAspek'] = ItemModel::select('*')->where('level', 1)->where('nama', '!=', 'Data Umum')->get();
        $param['dataKeuangan'] = ItemModel::select('*')->where('level', 1)
                                ->where('nama', '!=', 'Data Umum')
                                ->where('nama','Aspek Keuangan')
                                ->first();
        $param['dataLevelKeuangan'] = ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable')
                                ->where('level', 2)
                                ->where('opsi_jawaban','!=','option')
                                ->where('opsi_jawaban','file')
                                ->where('id_parent', $param['dataKeuangan']->id)
                                ->get();

        $dataNasabah = DB::table('pengajuan_dagulir')->select('pengajuan_dagulir.*', 'kabupaten.id as kabupaten_id', 'kabupaten.kabupaten', 'kecamatan.id as kecamatan_id', 'kecamatan.id_kabupaten', 'kecamatan.kecamatan', 'desa.id as desa_id', 'desa.id_kabupaten', 'desa.id_kecamatan', 'desa.desa')
                        ->join('kabupaten', 'kabupaten.id', 'pengajuan_dagulir.kotakab_ktp')
                        ->join('kecamatan', 'kecamatan.id', 'pengajuan_dagulir.kec_ktp')
                        ->join('desa', 'desa.id', 'pengajuan_dagulir.desa_ktp')
                        ->join('pengajuan', 'pengajuan.dagulir_id', 'pengajuan_dagulir.id')
                        ->where('pengajuan.id', $id)
                        ->first();
        $param['dataNasabah'] = $dataNasabah;
        $dataUmum = PengajuanModel::select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang', 'pengajuan.skema_kredit')
        ->find($id);
        $param['dataUmum'] = $dataUmum;
        $param['komentar'] = KomentarModel::where('id_pengajuan', $id)->first();
        $param['jenis_usaha'] = config('dagulir.jenis_usaha');

        $itemSP = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();
        $param['itemSP'] = $itemSP;
        $pdf = Pdf::loadview('dagulir.cetak.cetak-surat',$param);
        $fileName = 'Surat-Analisa-'. $dataNasabah->nama. '.pdf' ;
        $filePath = public_path() . '/upload/cetak_surat/'. $param['dataUmum']->id;
        if (!File::isDirectory($filePath)) {
            File::makeDirectory($filePath, 493, true);
        }
        $pdf->save($filePath.'/'.$fileName);

        $dataLS = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable')
        ->where('level', 2)
        ->where('id_parent', $itemSP->id)
        ->where('nama', 'Laporan SLIK')
        ->get();

        $param['dataLS'] = $dataLS;

        return view('cetak.cetak-surat-dagulir', $param);
        $pdf = PDF::loadView('dagulir.cetak.cetak-surat', $param);
        return $pdf->download('Analisa-' . $dataNasabah->kode_pendaftaran . '.pdf');

    }

    public function cetakLampiranAnalisa($id_pengajuan) {
        $status = '';
        $message = '';
        $filepath = null;
        try {
            $param['dataAspek'] = ItemModel::select('*')->where('level', 1)->get();
            $param['dataKeuangan'] = ItemModel::select('*')->where('level', 1)
                                ->where('nama', '!=', 'Data Umum')
                                ->where('nama','Aspek Keuangan')
                                ->first();
            $param['dataLevelKeuangan'] = ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable')
                                ->where('level', 2)
                                ->where('opsi_jawaban','!=','option')
                                ->where('opsi_jawaban','file')
                                ->where('id_parent', $param['dataKeuangan']->id)
                                ->get();
            $dataNasabah = DB::table('pengajuan_dagulir')->select('pengajuan_dagulir.*', 'kabupaten.id as kabupaten_id', 'kabupaten.kabupaten', 'kecamatan.id as kecamatan_id', 'kecamatan.id_kabupaten', 'kecamatan.kecamatan', 'desa.id as desa_id', 'desa.id_kabupaten', 'desa.id_kecamatan', 'desa.desa')
                            ->join('kabupaten', 'kabupaten.id', 'pengajuan_dagulir.kotakab_ktp')
                            ->join('kecamatan', 'kecamatan.id', 'pengajuan_dagulir.kec_ktp')
                            ->join('desa', 'desa.id', 'pengajuan_dagulir.desa_ktp')
                            ->join('pengajuan', 'pengajuan.dagulir_id', 'pengajuan_dagulir.id')
                            ->where('pengajuan.id', $id_pengajuan)
                            ->first();
            $param['dataNasabah'] = $dataNasabah;
            $param['dataUmum'] = PengajuanModel::select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang', 'pengajuan.skema_kredit')
                                ->find($id_pengajuan);
            $param['komentar'] = KomentarModel::where('id_pengajuan', $id_pengajuan)->first();
            $param['jenis_usaha'] = config('dagulir.jenis_usaha');

            $pdf = Pdf::loadview('dagulir.pengajuan-kredit.cetak.cetak-surat',$param);

            $fileName = 'Surat-Analisa-'. $dataNasabah->nama. '.pdf' ;
            $filePath = public_path('upload/cetak_surat/'.$param['dataUmum']->id);
            if (!File::isDirectory($filePath)) {
                File::makeDirectory($filePath, 493, true);
            }
            $pdf->save($filePath.'/'.$fileName);
            $filepath = $filePath.'/'.$fileName;

            $status = 'success';
            $message = 'Successfully get file';
        } catch (\Exception $e) {
            $status = 'failed';
            $message = $e->getMessage();
        } finally {
            $data = [
                'status' => $status,
                'message' => $message,
                'filepath' => $filepath
            ];
            return $data;
        }
    }

    public function edit($id) {
        Session::put('is_dagulir', true);
        $param['pageTitle'] = "Dashboard";
        $param['multipleFiles'] = $this->isMultipleFiles;

        $param['dataDesa'] = Desa::all();
        $param['dataKecamatan'] = Kecamatan::all();
        $param['dataKabupaten'] = Kabupaten::all();
        $param['dataAspek'] = ItemModel::select('*')->where('level', 1)->where('nama', '!=', 'Data Umum')->get();
        $param['itemSlik'] = ItemModel::with('option')->where('nama', 'SLIK')->first();
        $param['itemCatatanSlik'] = ItemModel::where('nama', 'Catatan Slik')->first();
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

        $pengajuan = PengajuanModel::find($id);
        if ($pengajuan->skema_kredit == 'Dagulir') {
            $dagulir_id = $pengajuan->dagulir_id;
            $dagulir = PengajuanDagulir::find($dagulir_id);
            $param['duTemp'] = $dagulir;
            $param['dataKabupaten'] = Kabupaten::all();
            $param['dataKecamatan'] = Kecamatan::where('id_kabupaten', $dagulir->kotakab_ktp)->get();
            $param['dataDesa'] = Desa::where('id_kecamatan', $dagulir->kec_ktp)->get();
            $param['dataKecamatanDomisili'] = Kecamatan::where('id_kabupaten', $dagulir->kotakab_dom)->get();
            $param['dataKecamatanUsaha'] = Kecamatan::where('id_kabupaten', $dagulir->kotakab_usaha)->get();
        }else{
            $calon = CalonNasabah::where('id_pengajuan',$id)->first();
            $param['dataUmum'] = $calon;
            $param['dataKabupaten'] = Kabupaten::all();
            $param['dataKecamatan'] = Kecamatan::where('id_kabupaten', $calon->id_kabupaten)->get();
            $param['dataDesa'] = Desa::where('id_kecamatan', $calon->id_kecamatan)->get();
            $param['allKab'] = Kabupaten::get();
            $param['allKec'] = Kecamatan::where('id_kabupaten', $param['dataUmum']->id_kabupaten)->get();
            $param['allDesa'] = Desa::where('id_kecamatan', $param['dataUmum']->id_kecamatan)->get();
            $param['itemKTPSu'] = ItemModel::where('nama', 'Foto KTP Suami')->first();
            $param['itemKTPIs'] = ItemModel::where('nama', 'Foto KTP Istri')->first();
            $param['itemKTPNas'] = ItemModel::where('nama', 'Foto KTP Nasabah')->first();

        }
        $param['pengajuan'] = $pengajuan;
        if ($pengajuan->skema_kredit == 'KKB') {
            $param['dataMerk'] = MerkModel::all();
            $param['dataPO'] = DB::table('data_po')
                ->where('id_pengajuan', $id)
                ->first();
        }
        // return $param['pengajuan'];
        $param['jawabanSlik'] = JawabanModel::select('id', 'id_jawaban', 'skor')
                                            ->where('id_pengajuan', $id)
                                            ->whereIn('id_jawaban', [71,72,73,74])
                                            ->first();
        $param['jawabanLaporanSlik'] = \App\Models\JawabanTextModel::where('id_pengajuan', $id)
                                            ->where('id_jawaban', 146)
                                            ->first();
        $param['dataPertanyaanSatu'] = ItemModel::select('id', 'nama', 'level', 'id_parent')->where('level', 2)->where('id_parent', 3)->get();
        $param['komentar'] = KomentarModel::where('id_pengajuan', $id)->first();

        $param['pendapat'] = $this->repo->getAlasanPengembalian($id, 'Proses Input Data');
        if ($pengajuan->skema_kredit == 'Dagulir') {
            return view('dagulir.pengajuan-kredit.edit', $param);
        }else{
            return view('dagulir.pengajuan-kredit.edit-skema', $param);

        }
    }

    public function update(Request $request, $id)
    {
        $statusSlik = false;
        $find = array('Rp ', '.', ',');

        $pengajuanModel = PengajuanModel::find($id);
        DB::beginTransaction();
        $find = array('Rp.', '.', ',');
        $id_pengajuan = $pengajuanModel->id;
        $npwp = null;
        if ($pengajuanModel->skema_kredit == 'Dagulir') {
            if ($request->has('dagulir_id')) {
                $pengajuan = PengajuanDagulir::find($request->dagulir_id);

            }
            else {
                $pengajuan = PengajuanDagulir::find($pengajuanModel->dagulir_id);
                $pengajuan->nama = $request->get('nama_lengkap');
                $pengajuan->email = $request->get('email');
                $pengajuan->nik = $request->get('nik_nasabah');
                $pengajuan->tempat_lahir =  $request->get('tempat_lahir');
                $pengajuan->tanggal_lahir = $request->get('tanggal_lahir');
                $pengajuan->telp = $request->get('telp');
                $pengajuan->jenis_usaha = $request->get('jenis_usaha');
                $pengajuan->ket_agunan = $request->get('ket_agunan');
                $pengajuan->nominal = formatNumber($request->get('nominal_pengajuan'));
                $pengajuan->jangka_waktu = $request->get('jangka_waktu');
                $pengajuan->kode_bank_pusat = 1;
                $pengajuan->kode_bank_cabang = auth()->user()->id_cabang;
                $pengajuan->kec_ktp = $request->get('kecamatan_sesuai_ktp');
                $pengajuan->kotakab_ktp = $request->get('kode_kotakab_ktp');
                $pengajuan->kec_dom = $request->get('kecamatan_domisili');
                $pengajuan->kotakab_dom = $request->get('kode_kotakab_domisili');
                $pengajuan->alamat_dom = $request->get('alamat_domisili');
                $pengajuan->tujuan_penggunaan = $request->get('tujuan_penggunaan');
                $pengajuan->alamat_ktp = $request->get('alamat_sesuai_ktp');
                $pengajuan->kec_usaha = $request->get('kecamatan_usaha');
                $pengajuan->kotakab_usaha = $request->get('kode_kotakab_usaha');
                $pengajuan->alamat_usaha = $request->get('alamat_usaha');
                $pengajuan->tipe = $request->get('tipe_pengajuan');
                $npwp = null;
                if ($request->informasi) {
                    if (array_key_exists('79', $request->informasi)) {
                        $npwp = str_replace(['.','-'], '', $request->informasi[79]);
                    }
                } else {
                    $npwp = null;
                }
                $pengajuan->npwp = $npwp;
                $pengajuan->jenis_badan_hukum = $request->get('jenis_badan_hukum');
                $pengajuan->tanggal = now();
                $pengajuan->status = 8;
                $pengajuan->from_apps = 'pincetar';
            }

            $pengajuan->nama_pj_ketua = $request->has('nama_pj') ? $request->get('nama_pj') : null;
            $pengajuan->hubungan_bank = $request->get('hub_bank');
            $pengajuan->hasil_verifikasi = $request->get('hasil_verifikasi');
            $pengajuan->desa_ktp = $request->get('desa');
            $pengajuan->tempat_berdiri = $request->get('tempat_berdiri');
            $pengajuan->tanggal_berdiri = $request->get('tanggal_berdiri');
            $pengajuan->user_id = Auth::user()->id;
            $pengajuan->status_pernikahan = $request->get('status');
            $pengajuan->nik_pasangan = $request->has('nik_pasangan') ? $request->get('nik_pasangan') : null;
            $pengajuan->created_at = now();
            $pengajuan->save();

            $dagulir_id = $pengajuan->id;



            $update_pengajuan = PengajuanDagulir::find($pengajuan->id);
            // foto nasabah
            if ($request->has('foto_nasabah')) {
                // Delete current image
                $current = $update_pengajuan->foto_nasabah;
                $path_file = public_path("upload/$id_pengajuan/{$dagulir_id}/").$current;
                if (file_exists($path_file)) {
                    @unlink($path_file);
                }

                // Update new image
                $image = $request->file('foto_nasabah');
                $fileNameNasabah = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                $filePath = public_path() . '/upload/' . $id_pengajuan. '/' . $dagulir_id;
                if (!File::isDirectory($filePath)) {
                    File::makeDirectory($filePath, 493, true);
                }
                $image->move($filePath, $fileNameNasabah);
                $update_pengajuan->foto_nasabah = $fileNameNasabah;

            }
            if ($request->has('ktp_pasangan')) {
                // Delete current image
                $current = $update_pengajuan->ktp_pasangan;
                $path_file = public_path("upload/$id_pengajuan/{$dagulir_id}/").$current;
                if (file_exists($path_file)) {
                    @unlink($path_file);
                }

                // Update new image
                $image = $request->file('ktp_pasangan');
                $fileNamePasangan = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                $filePath = public_path() . '/upload/' . $id_pengajuan. '/' . $dagulir_id;
                if (!File::isDirectory($filePath)) {
                    File::makeDirectory($filePath, 493, true);
                }
                $image->move($filePath, $fileNamePasangan);
                $update_pengajuan->foto_pasangan = $fileNamePasangan;

            }
            if ($request->has('ktp_nasabah')) {
                // Delete current image
                $current = $update_pengajuan->ktp_nasabah;
                $path_file = public_path("upload/$id_pengajuan/{$dagulir_id}/").$current;
                if (file_exists($path_file)) {
                    @unlink($path_file);
                }

                // Update new image
                $image = $request->file('ktp_nasabah');
                $fileNameKtpNasabah = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                $filePath = public_path() . '/upload/' . $id_pengajuan. '/' . $dagulir_id;
                if (!File::isDirectory($filePath)) {
                    File::makeDirectory($filePath, 493, true);
                }
                $image->move($filePath, $fileNameKtpNasabah);
                $update_pengajuan->foto_ktp = $fileNameKtpNasabah;

            }
            // ktp nasabah
            $update_pengajuan->update();
        } else {
            $validateData = Validator::make($request->all(),[
                'name' => 'required',
                'alamat_rumah' => 'required',
                'alamat_usaha' => 'required',
                'no_ktp' => 'required',
                'no_telp' => 'required',
                'kabupaten' => 'required|not_in:0',
                'kec' => 'required|not_in:0',
                'desa' => 'required|not_in:0',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'status' => 'required',
                'sektor_kredit' => 'required',
                'jenis_usaha' => 'required',
                'jumlah_kredit' => 'required',
                'tujuan_kredit' => 'required',
                'jaminan' => 'required',
                'hubungan_bank' => 'required',
                'hasil_verifikasi' => 'required',
            ], [
                'required' => 'data harus terisi.',
                'not_in' => 'kolom harus dipilih.',
            ]);

            $updateData = CalonNasabah::find($request->id_nasabah);
            $updateData->nama = $request->name;
            $updateData->alamat_rumah = $request->alamat_rumah;
            $updateData->alamat_usaha = $request->alamat_usaha;
            $updateData->no_ktp = $request->no_ktp;
            $updateData->no_telp = $request->no_telp;
            $updateData->tempat_lahir = $request->tempat_lahir;
            $updateData->tanggal_lahir = $request->tanggal_lahir;
            $updateData->status = $request->status;
            $updateData->sektor_kredit = $request->sektor_kredit;
            $updateData->jenis_usaha = $request->jenis_usaha;
            $updateData->jumlah_kredit = str_replace($find, "", $request->jumlah_kredit);
            $updateData->tujuan_kredit = $request->tujuan_kredit;
            $updateData->jaminan_kredit = $request->jaminan;
            $updateData->hubungan_bank = $request->hubungan_bank;
            $updateData->verifikasi_umum = $request->hasil_verifikasi;
            $updateData->id_user = auth()->user()->id;
            $updateData->id_pengajuan = $id;
            $updateData->id_desa = $request->desa;
            $updateData->id_kecamatan = $request->kec;
            $updateData->id_kabupaten = $request->kabupaten;
            $updateData->tenor_yang_diminta = $request->tenor_yang_diminta;
            $updateData->save();

            if ($request->skema_kredit == 'KKB') {
                // $dataPO = DB::table('data_po')->where('id_pengajuan', $request->id_dagulir_temp)->first();
                // return $dataPO;
                DB::table('data_po')->where('id_pengajuan', $request->id_dagulir_temp)
                    ->update([
                        'tahun_kendaraan' => $request->tahun,
                        'merk' => $request->merk,
                        'tipe' => $request->tipe_kendaraan,
                        'warna' => $request->warna,
                        'keterangan' => 'Pemesanan ' . $request->pemesanan,
                        'jumlah' => $request->sejumlah,
                        'harga' => str_replace($find, '', $request->harga)
                    ]);
            }
        }
        try {

            $oldAnswer = JawabanTextModel::select('jawaban_text.*')
                                        ->join('item', 'item.id', 'jawaban_text.id_jawaban')
                                        ->where('jawaban_text.id_pengajuan', $id_pengajuan)
                                        ->where('item.opsi_jawaban', '!=', 'file')
                                        ->pluck('jawaban_text.id_jawaban')
                                        ->toArray();

            $oldFileAnswer = JawabanTextModel::select('jawaban_text.*')
                                        ->join('item', 'item.id', 'jawaban_text.id_jawaban')
                                        ->where('jawaban_text.id_pengajuan', $id_pengajuan)
                                        ->where('item.opsi_jawaban', 'file')
                                        ->pluck('jawaban_text.id_jawaban')
                                        ->toArray();

            // jawaban ijin usaha

            if ($request->ijin_usaha == 'tidak_ada_legalitas_usaha') {
                $dokumenUsaha = DB::table('item')
                    ->where('nama', 'LIKE', '%NIB%')
                    ->orWhere('nama', 'LIKE', '%Surat Keterangan Usaha%')
                    ->orWhere('nama', 'LIKE', '%NPWP%')
                    ->get();
                foreach ($dokumenUsaha as $idDoc) {
                    DB::table('jawaban_text')
                        ->where('id_pengajuan', $id_pengajuan)
                        ->where('id_jawaban', $idDoc->id)
                        ->delete();
                }
            }

            if ($request->isNpwp == "0") {
                $dokumenUsaha = DB::table('item')
                    ->orWhere('nama', 'LIKE', '%NPWP%')
                    ->get();
                foreach ($dokumenUsaha as $idDoc) {
                    DB::table('jawaban_text')
                        ->where('id_pengajuan', $id_pengajuan)
                        ->where('id_jawaban', $idDoc->id)
                        ->delete();
                }
            }
            if ($request->has('ijin_usaha')) {
                $answer = JawabanTextModel::where('id_pengajuan', $id_pengajuan)
                                        ->where('id_jawaban', 76)
                                        ->first();
                if ($answer) {
                    $answer->opsi_text = $request->ijin_usaha;
                    $answer->updated_at = now();
                    $answer->save();
                }
                else {
                    JawabanTextModel::create([
                                        'id_pengajuan' => $id_pengajuan,
                                        'id_jawaban' => 76,
                                        'opsi_text' => $request->ijin_usaha,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                }
            }
            // jawaban kategori jaminan tambahan
            if ($request->has('kategori_jaminan_tambahan')) {
                $answer = JawabanTextModel::where('id_pengajuan', $id_pengajuan)
                                ->where('id_jawaban', 110)
                                ->first();
                if ($answer) {
                    $answer->opsi_text = $request->kategori_jaminan_tambahan;
                    $answer->updated_at = now();
                    $answer->save();
                }
                else {
                    JawabanTextModel::create([
                                        'id_pengajuan' => $id_pengajuan,
                                        'id_jawaban' => 110,
                                        'opsi_text' => $request->kategori_jaminan_tambahan,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                }
                // $pengajuan = DB::table('pengajuan')->where('id', $request->id_dagulir_temp)->first();
                if ($pengajuanModel->skema_kredit == 'Dagulir') {
                    if ($request->has('dagulir_id')) {
                        $pengajuan = PengajuanDagulir::find($request->dagulir_id);
                    } else {
                        $pengajuan = PengajuanDagulir::find($pengajuanModel->dagulir_id);
                    }
                    $pengajuan->npwp = $npwp != null ? $npwp : null;
                    $pengajuan->jenis_badan_hukum = $request->get('jenis_badan_hukum');
                    $pengajuan->tanggal = now();
                    $pengajuan->status = 8;
                    $pengajuan->from_apps = 'pincetar';
                }
            }
            if ($pengajuanModel->skema_kredit == 'Dagulir') {
                if ($request->has('dagulir_id')) {
                    $pengajuan = PengajuanDagulir::find($request->dagulir_id);
                } else {
                    $pengajuan = PengajuanDagulir::find($pengajuanModel->dagulir_id);
                }
                $pengajuan->nama_pj_ketua = $request->has('nama_pj') ? $request->get('nama_pj') : null;
                $pengajuan->hubungan_bank = $request->get('hub_bank');
                $pengajuan->hasil_verifikasi = $request->get('hasil_verifikasi');
                $pengajuan->desa_ktp = $request->get('desa');
                $pengajuan->tempat_berdiri = $request->get('tempat_berdiri');
                $pengajuan->tanggal_berdiri = $request->get('tanggal_berdiri');
                $pengajuan->user_id = Auth::user()->id;
                $pengajuan->status_pernikahan = $request->get('status');
                $pengajuan->nik_pasangan = $request->has('nik_pasangan') ? $request->get('nik_pasangan') : null;
                $pengajuan->created_at = now();
                $pengajuan->save();


                $dagulir_id = $pengajuan->id;
                $update_pengajuan = PengajuanDagulir::find($pengajuan->id);
            } else {
                $pengajuan = CalonNasabah::find($request->id_nasabah);
                $update_pengajuan = CalonNasabah::find($pengajuan->id);
            }


            $id_pengajuan = $pengajuanModel->id;

            if ($pengajuanModel->skema_kredit == 'Dagulir') {
                // foto nasabah
                if ($request->has('foto_nasabah')) {
                    // Delete current image
                    $current = $update_pengajuan->foto_nasabah;
                    $path_file = public_path("upload/$id_pengajuan/{$dagulir_id}/").$current;
                    if (file_exists($path_file)) {
                        @unlink($path_file);
                    }

                    // Update new image
                    $image = $request->file('foto_nasabah');
                    $fileNameNasabah = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                    $filePath = public_path() . '/upload/' . $id_pengajuan. '/' . $dagulir_id;
                    if (!File::isDirectory($filePath)) {
                        File::makeDirectory($filePath, 493, true);
                    }
                    $image->move($filePath, $fileNameNasabah);
                    $update_pengajuan->foto_nasabah = $fileNameNasabah;

                }
                if ($request->has('ktp_pasangan')) {
                    // Delete current image
                    $current = $update_pengajuan->ktp_pasangan;
                    $path_file = public_path("upload/$id_pengajuan/{$dagulir_id}/").$current;
                    if (file_exists($path_file)) {
                        @unlink($path_file);
                    }

                    // Update new image
                    $image = $request->file('ktp_pasangan');
                    $fileNamePasangan = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                    $filePath = public_path() . '/upload/' . $id_pengajuan. '/' . $dagulir_id;
                    if (!File::isDirectory($filePath)) {
                        File::makeDirectory($filePath, 493, true);
                    }
                    $image->move($filePath, $fileNamePasangan);
                    $update_pengajuan->foto_pasangan = $fileNamePasangan;

                }
                if ($request->has('ktp_nasabah')) {
                    // Delete current image
                    $current = $update_pengajuan->ktp_nasabah;
                    $path_file = public_path("upload/$id_pengajuan/{$dagulir_id}/").$current;
                    if (file_exists($path_file)) {
                        @unlink($path_file);
                    }

                    // Update new image
                    $image = $request->file('ktp_nasabah');
                    $fileNameKtpNasabah = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();
                    $filePath = public_path() . '/upload/' . $id_pengajuan. '/' . $dagulir_id;
                    if (!File::isDirectory($filePath)) {
                        File::makeDirectory($filePath, 493, true);
                    }
                    $image->move($filePath, $fileNameKtpNasabah);
                    $update_pengajuan->foto_ktp = $fileNameKtpNasabah;

                }
                // ktp nasabah
                $update_pengajuan->update();
            }

            $oldAnswer = JawabanTextModel::select('jawaban_text.*')
                                        ->join('item', 'item.id', 'jawaban_text.id_jawaban')
                                        ->where('jawaban_text.id_pengajuan', $id_pengajuan)
                                        ->where('item.opsi_jawaban', '!=', 'file')
                                        ->pluck('jawaban_text.id_jawaban')
                                        ->toArray();
            $oldFileAnswer = JawabanTextModel::select('jawaban_text.*')
                                        ->join('item', 'item.id', 'jawaban_text.id_jawaban')
                                        ->where('jawaban_text.id_pengajuan', $id_pengajuan)
                                        ->where('item.opsi_jawaban', 'file')
                                        ->pluck('jawaban_text.id_jawaban')
                                        ->toArray();

            // jawaban ijin usaha
            if ($request->has('ijin_usaha')) {
                $answer = JawabanTextModel::where('id_pengajuan', $id_pengajuan)
                                        ->where('id_jawaban', 76)
                                        ->first();
                if ($answer) {
                    $answer->opsi_text = $request->ijin_usaha;
                    $answer->updated_at = now();
                    $answer->save();
                }
                else {
                    JawabanTextModel::create([
                                        'id_pengajuan' => $id_pengajuan,
                                        'id_jawaban' => 76,
                                        'opsi_text' => $request->ijin_usaha,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                }
            }
            // jawaban kategori jaminan tambahan
            if ($request->has('kategori_jaminan_tambahan')) {
                $answer = JawabanTextModel::where('id_pengajuan', $id_pengajuan)
                                ->where('id_jawaban', 110)
                                ->first();
                if ($answer) {
                    $answer->opsi_text = $request->kategori_jaminan_tambahan;
                    $answer->updated_at = now();
                    $answer->save();
                }
                else {
                    JawabanTextModel::create([
                                        'id_pengajuan' => $id_pengajuan,
                                        'id_jawaban' => 110,
                                        'opsi_text' => $request->kategori_jaminan_tambahan,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                }
            }
            //untuk jawaban yg teks, number, persen, long text
            if ($request->id_level) {
                $newAnswer = [];
                foreach ($request->id_level as $key => $value) {
                    array_push($newAnswer, $value);
                    if (in_array($value, $oldAnswer)) {
                        // Update answer
                        if ($value != null) {
                            $dataJawabanText = JawabanTextModel::where('id_pengajuan', $id_pengajuan)
                                                                ->where('id_jawaban', $value)
                                                                ->first();
                            if ($dataJawabanText) {
                                if ($request->get('informasi')[$key] != null) {
                                    if ($request->get('id_level')[$key] != '131' && $request->get('id_level')[$key] != '143' && $request->get('id_level')[$key] != '90' && $request->get('id_level')[$key] != '138') {
                                        $dataJawabanText->opsi_text = str_replace($find, '', $request->get('informasi')[$key]);
                                    } else {
                                        $dataJawabanText->opsi_text = $request->get('informasi')[$key];
                                    }
                                    $dataJawabanText->save();
                                }
                            }
                        }
                    }
                    else {
                        // Insert answer
                        if ($value != null) {
                            if ($request->get('informasi')[$key] != null) {
                                $dataJawabanText = new JawabanTextModel;
                                $dataJawabanText->id_pengajuan = $id_pengajuan;
                                $dataJawabanText->id_jawaban = $request->get('id_level')[$key];
                                if ($request->get('id_level')[$key] != '131' && $request->get('id_level')[$key] != '143' && $request->get('id_level')[$key] != '90' && $request->get('id_level')[$key] != '138') {
                                    $dataJawabanText->opsi_text = str_replace($find, '', $request->get('informasi')[$key]);
                                } else {
                                    $dataJawabanText->opsi_text = $request->get('informasi')[$key];
                                }
                                $dataJawabanText->save();
                            }
                        }
                    }
                }
            }

            // untuk upload file baru
            $newFileAnswer = [];
            if ($request->has('upload_file')) {
                foreach ($request->upload_file as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $key2 => $value2) {
                            array_push($newFileAnswer, $key);
                            if (in_array($key, $oldFileAnswer)) {
                                $answer = JawabanTextModel::where('id_pengajuan', $id_pengajuan)
                                                        ->where('id_jawaban', $key)
                                                        ->first();
                                // Delete current image
                                $current = $answer->opsi_text;
                                $path_file = public_path("upload/$id_pengajuan/{$key}/").$current;
                                if (file_exists($path_file)) {
                                    @unlink($path_file);
                                }

                                // Update new image
                                $filename = auth()->user()->id . '-' . time() . '-' . $value2->getClientOriginalName();
                                $relPath = "upload/{$id_pengajuan}/{$key}";
                                $path = public_path("upload/{$id_pengajuan}/{$key}/");

                                File::isDirectory(public_path($relPath)) or File::makeDirectory(public_path($relPath), recursive: true);
                                $value2->move($path, $filename);
                                // Update answer
                                $answer->opsi_text = $filename;
                                $answer->save();
                            }
                            else {
                                $filename = auth()->user()->id . '-' . time() . '-' . $value2->getClientOriginalName();
                                $relPath = "upload/{$id_pengajuan}/{$key}";
                                $path = public_path("upload/{$id_pengajuan}/{$key}/");

                                File::isDirectory(public_path($relPath)) or File::makeDirectory(public_path($relPath), recursive: true);
                                $value2->move($path, $filename);
                                // Insert answer
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
                    } else {
                        array_push($newFileAnswer, $key);
                        if (in_array($key, $oldFileAnswer)) {
                            $answer = JawabanTextModel::where('id_pengajuan', $id_pengajuan)
                                                        ->where('id_jawaban', $key)
                                                        ->first();
                            // Delete current image
                            $current = $answer->opsi_text;
                            $path_file = public_path("upload/$id_pengajuan/{$key}/").$current;
                            if (file_exists($path_file)) {
                                @unlink($path_file);
                            }

                            // Update new image
                            $filename = auth()->user()->id . '-' . time() . '-' . $value->getClientOriginalName();
                            $relPath = "upload/{$id_pengajuan}/{$key}";
                            $path = public_path("upload/{$id_pengajuan}/{$key}/");

                            File::isDirectory(public_path($relPath)) or File::makeDirectory(public_path($relPath), recursive: true);
                            $value->move($path, $filename);
                            // Update answer
                            $answer->opsi_text = $filename;
                            $answer->save();
                        }
                        else {
                            $filename = auth()->user()->id . '-' . time() . '-' . $value->getClientOriginalName();
                            $relPath = "upload/{$id_pengajuan}/{$key}";
                            $path = public_path("upload/{$id_pengajuan}/{$key}/");

                            File::isDirectory(public_path($relPath)) or File::makeDirectory(public_path($relPath), recursive: true);
                            $value->move($path, $filename);
                            // Insert answer
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
                }
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
            }

            // item level 3
            $dataLev3 = [];
            if ($request->dataLevelTiga != null) {
                // item level 3
                $dataLev3 = $request->dataLevelTiga;
                unset($dataLev3[134]);
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
            $oldIdScore = JawabanModel::where('id_pengajuan', $id_pengajuan)->pluck('id_jawaban')->toArray();
            $newIdScore = [];
            for ($i = 0; $i < count($mergedDataLevel); $i++) {
                if ($mergedDataLevel[$i]) {
                    // jika data tersedia
                    $data = getDataLevel($mergedDataLevel[$i]);
                    array_push($arrTes, $data);
                    if (is_numeric($data[0])) {
                        if ($data[0] > 0) {
                            if (array_key_exists(1, $data)) {
                                if ($data[1] == 71 || $data[1] == 186) {
                                    if ($data[0] == '1') {
                                        $statusSlik = true;
                                    }
                                }
                            }
                            $totalScore += $data[0];
                        }
                        else {
                            $totalDataNull++;
                        }
                    } else
                        $totalDataNull++;
                    if (isset($data[1])) {
                        array_push($newIdScore, $data[1]);
                    }
                } else
                    $totalDataNull++;
            }
            // find avg
            $avgResult = round($totalScore / (count($mergedDataLevel) - $totalDataNull), 2);
            $status = "";
            $updateData = PengajuanModel::find($id_pengajuan);
            if ($avgResult > 0 && $avgResult <= 2) {
                $status = "merah";
            } elseif ($avgResult > 2 && $avgResult <= 3) {
                $status = "kuning";
            } elseif ($avgResult > 3) {
                $status = "hijau";
            } else {
                $status = "merah";
            }

            for ($i = 0; $i < count($mergedDataLevel); $i++) {
                if ($mergedDataLevel[$i] != null) {
                    $data = getDataLevel($mergedDataLevel[$i]);
                    if (is_numeric($data[0])) {
                        if ($data[0] > 0) {
                            if (array_key_exists(1, $data)) {
                                $answer = JawabanPengajuanModel::where('id_pengajuan', $id_pengajuan)
                                                                ->where('id_jawaban', $data[1])
                                                                ->first();
                                if ($answer) {
                                    $answer->skor = $data[0];
                                    $answer->save();
                                }
                                else {
                                    JawabanPengajuanModel::insert([
                                        'id_pengajuan' => $id_pengajuan,
                                        'id_jawaban' => $data[1],
                                        'skor' => $data[0],
                                    ]);
                                }
                            }
                        }
                    } else {
                        if (array_key_exists(1, $data)) {
                            $answer = JawabanPengajuanModel::where('id_pengajuan', $id_pengajuan)
                                                            ->where('id_jawaban', $data[1])
                                                            ->first();
                            if ($answer) {
                                $answer->id_jawaban = $data[1];
                                $answer->save();
                            }
                            else {
                                JawabanPengajuanModel::insert([
                                    'id_pengajuan' => $id_pengajuan,
                                    'id_jawaban' => $data[1]
                                ]);
                            }
                        }
                    }
                }
            }
            // Delete unuse score
            for ($i=0; $i < count($oldIdScore); $i++) {
                if (!in_array($oldIdScore[$i], $newIdScore)) {
                    JawabanPengajuanModel::where('id_pengajuan', $id_pengajuan)
                                        ->where('id_jawaban', $oldIdScore[$i])
                                        ->delete();
                }
            }
            if (!$statusSlik) {
                $updateData->posisi = 'Proses Input Data';
                $updateData->status_by_sistem = $status;
                $updateData->average_by_sistem = $avgResult;
            } else {
                if ($pengajuanModel->skema_kredit == 'Dagulir') {
                    $cetak_lampiran_analisa = $this->cetakLampiranAnalisa($id_pengajuan);
                    $lampiran_analisa = null;
                    if ($cetak_lampiran_analisa['status'] == 'success') {
                        $lampiran_analisa = "data:@application/pdf;base64,".base64_encode(file_get_contents($cetak_lampiran_analisa['filepath']));
                    }

                    $updateData->posisi = 'Ditolak';
                    $updateData->status_by_sistem = "merah";
                    $updateData->average_by_sistem = "1.0";

                    if ($pengajuan->kode_pendaftaran) {
                        $kode_pendaftaran = $pengajuan->kode_pendaftaran;
                        $storeSIPDE = 'success';
                    }
                    else {

                            // HIT Pengajuan endpoint dagulir
                            $storeSIPDE = $this->storeSipde($id_pengajuan);
                            if (is_array($storeSIPDE)) {
                                $kode_pendaftaran = array_key_exists('kode_pendaftaran', $storeSIPDE) ? $storeSIPDE['kode_pendaftaran'] : false;
                            }

                    }
                        $delay = 1500000; // 1.5 sec
                        if ($kode_pendaftaran) {
                            // HIT update status survei endpoint dagulir
                            if ($pengajuan->status != 1) {
                                $survei = $this->updateStatus($kode_pendaftaran, 1);
                                if (is_array($survei)) {
                                    // Fail block
                                    if ($survei['message'] != 'Update Status Gagal. Anda tidak bisa mengubah status, karena status saat ini adalah SURVEY') {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $survei);
                                        return redirect()->back();
                                    }
                                }
                                else {
                                    if ($survei != 200) {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $survei);
                                        return redirect()->back()->withError($survei);
                                    }
                                }
                                usleep($delay);
                            }

                            // HIT update status analisa endpoint dagulir
                            if ($pengajuan->status != 2) {
                                $analisa = $this->updateStatus($kode_pendaftaran, 2, $lampiran_analisa);
                                if (is_array($analisa)) {
                                    // Fail block
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $analisa);
                                    return redirect()->back();
                                }
                                else {
                                    if ($analisa != 200 || $analisa != '200') {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $analisa);
                                        return redirect()->back();
                                    }
                                }
                                usleep($delay);
                            }

                            // HIT update status ditolak endpoint dagulir
                            if ($pengajuan->status != 3) {
                                $ditolak = $this->updateStatus($kode_pendaftaran, 4);
                                if (is_array($ditolak)) {
                                    // Fail block
                                    DB::rollBack();
                                    alert()->error('Peringatan(API)', $ditolak);
                                    return redirect()->back();
                                }
                                else {
                                    if ($ditolak != 200) {
                                        DB::rollBack();
                                        alert()->error('Peringatan(API)', $ditolak);
                                        return redirect()->back();
                                    }
                                }
                            }

                            $namaNasabah = 'undifined';
                            if ($pengajuan)
                                $namaNasabah = $pengajuan->nama;

                            $this->logPengajuan->store('Pincab dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menolak pengajuan atas nama ' . $namaNasabah . '.', $id_pengajuan, Auth::user()->id, Auth::user()->nip);
                        }
                    else {
                        DB::rollBack();
                        alert()->error('Peringatan(API)', $storeSIPDE);
                        return redirect()->back();
                    }
                }else{
                    $updateData->posisi = 'Ditolak';
                    $updateData->status_by_sistem = "merah";
                    $updateData->average_by_sistem = "1.0";
                }
            }
            $updateData->update();
            //save pendapat per aspek
            foreach ($request->get('id_aspek') as $key => $value) {
                if ($request->get('pendapat_per_aspek')[$key] != '') {
                    $addPendapat = PendapatPerAspek::where('id_pengajuan', $id_pengajuan)
                                                    ->whereNotNull('id_staf')
                                                    ->where('id_aspek', $value)
                                                    ->first();
                    $addPendapat->pendapat_per_aspek = $request->get('pendapat_per_aspek')[$key];
                    $addPendapat->save();
                }
            }

            if ($request->get('komentar_staff') != '') {
                $addKomentar = KomentarModel::where('id_pengajuan', $id_pengajuan)
                                            ->first();
                $addKomentar->komentar_staff = $request->get('komentar_staff');
                $addKomentar->save();
            }

            // Log Pengajuan Baru
            $namaNasabah = $pengajuanModel->skema_kredit == 'Dagulir' ? $pengajuan->nama : CalonNasabah::find($request->id_nasabah)->nama;

            $this->logPengajuan->store('Staff dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' memperbarui data pengajuan atas nama ' . $namaNasabah . '.', $id_pengajuan, Auth::user()->id, Auth::user()->nip);

            DB::commit();
            event(new EventMonitoring('store pengajuan'));

            if (!$statusSlik){
                Alert::success('Success', 'Data berhasil disimpan.');
                if ($pengajuanModel->skema_kredit == 'Dagulir') {
                    return redirect()->route('dagulir.pengajuan.index');
                } else {
                    return redirect()->route('pengajuan-kredit.index');
                }

            }
            else {
                alert()->info('Informasi', 'Pengajuan ditolak.');
                if ($pengajuanModel->skema_kredit == 'Dagulir') {
                    return redirect()->route('dagulir.pengajuan.index');
                } else {
                    return redirect()->route('pengajuan-kredit.index');
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $e;;
            return redirect()->route('dagulir.pengajuan.index')->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('dagulir.pengajuan.index')->withError('Terjadi kesalahan' . $e->getMessage());
        }
    }

    // pembatalan
    public function confirmPengajuan(Request $request) {
        DB::beginTransaction();
        try {
            $pengajuan = PengajuanModel::with('dagulir')->find($request->get('id_pengajuan'));
            $message = 'Berhasil melakukan pembatalan';
            $status_sipde = $this->updateStatus($pengajuan->dagulir->kode_pendaftaran,7);
            if (!is_array($status_sipde)) {
                if ($status_sipde == 200) {
                    DB::commit();
                    Alert::success('success', $message);
                    return redirect()->route('dagulir.pengajuan.index');
                }
                DB::commit();
                alert()->error('Terjadi Kesalahan', $status_sipde);
                return redirect()->route('dagulir.pengajuan.index');
            }
            else {
                DB::rollBack();
                alert()->error('Terjadi Kesalahan', $status_sipde);
                return redirect()->route('dagulir.pengajuan.index');
            }
        } catch (Exception $e) {
            DB::rollBack();
            alert()->error('Error','Terjadi Kesalahan');
            return redirect()->route('dagulir.index');
        }
    }
    public function indexTemp(Request $request){
        Session::put('is_dagulir', true);
        $id_user = Auth::user()->id;
        $param['cabang'] = DB::table('cabang')
            ->get();
        // paginate
        $search = $request->get('q');
        $limit = $request->has('page_length') ? $request->get('page_length') : 10;
        $page = $request->has('page') ? $request->get('page') : 1;
        $data = $this->repo->getDraftData($search, $limit, $page, $id_user);
        return view('dagulir.pengajuan-kredit.index-draft', compact('data'));
    }

    public function continueDraft($id,Request $request)
    {
        Session::put('is_dagulir', false);
        if ($request->has('skema_kredit')) {
            $nasabah = CalonNasabahTemp::findOrFail($id);
        }else{
            $nasabah = PengajuanDagulirTemp::find($id);
        }
        $createRoute = route('dagulir.temp.continue-draft');
        // dd($createRoute);

        return redirect()->to($createRoute . "?tempId={$nasabah->id}&continue=true&skema_kredit={$request->skema_kredit}");
    }

    public function showContinueDraft(Request $request)
    {
        Session::put('is_dagulir', true);
        $param['pageTitle'] = "Dashboard";
        $param['multipleFiles'] = $this->isMultipleFiles;

        $param['dataDesa'] = Desa::all();
        $param['dataKecamatan'] = Kecamatan::all();
        $param['dataKabupaten'] = Kabupaten::all();
        $param['dataAspek'] = ItemModel::select('*')->where('level', 1)->where('nama', '!=', 'Data Umum')->get();
        $param['itemSlik'] = ItemModel::with('option')->where('nama', 'SLIK')->first();
        $param['itemSP'] = ItemModel::where('nama', 'Surat Permohonan')->first();
        $param['itemP'] = ItemModel::where('nama', 'Laporan SLIK')->first();
        $param['itemCatatanSlik'] = ItemModel::where('nama', 'Catatan Slik')->first();

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
        if ($request->skema_kredit == null) {
            $param['duTemp'] = TemporaryService::getNasabahDataDagulir($request->tempId);
            $param['jawabanLaporanSlik'] =  JawabanTemp::
                                        where('temporary_dagulir_id', $request->tempId)
                                        ->where('id_jawaban', 146)
                                        ->first();
        }else{
            // $param['duTemp'] = TemporaryService::getNasabahDataDagulir($request->tempId);
            $param['duTemp'] = TemporaryService::getNasabahData($request->tempId);
            $param['dataPO'] = DB::table('data_po_temp')->where('id_calon_nasabah_temp', $request->tempId)->first();
            $param['jawabanLaporanSlik'] =JawabanTemp::
                                                where('id_temporary_calon_nasabah', $request->tempId)
                                                // where('temporary_dagulir_id', $request->tempId)
                                                ->where('id_jawaban', 146)
                                                ->first();
        }
        $data['dataPertanyaanSatu'] = ItemModel::select('id', 'nama', 'level', 'id_parent')->where('level', 2)->where('id_parent', 3)->get();
        if ($request->skema_kredit != 'Dagulir') {
            $param['skema'] = $request->skema ?? $param['duTemp']?->skema_kredit;
            $produkKredit = DB::table('temporary_calon_nasabah')
                ->where('id', $request->tempId)
                ->first();
            $param['produk'] = $produkKredit->produk_id ?? $request->produk;
            $param['skemaId'] = $produkKredit->skema_kredit_id ?? $request->skema;
            $param['limit'] = $produkKredit->skema_limit_id ?? $request->limit;
            $param['produkKredit'] = MstProdukKredit::select('id', 'name')->get();
            $param['skemaKredit'] = MstSkemaKredit::select('id', 'name')->get();
            $param['limitKredit'] = MstSkemaLimit::select('id', 'from', 'to', 'operator')->get();
            $param['maxKredit'] = $param['limit'] ? MstSkemaLimit::where('id', $param['limit'])->first() : null;
        }
        $param['jenis_usaha'] = config('dagulir.jenis_usaha');
        $param['tipe'] = config('dagulir.tipe_pengajuan');
        if ($request->skema_kredit == 'Dagulir' || $request->skema_kredit == null) {
            return view('dagulir.pengajuan-kredit.continue-draft', $param);
        }else{
            return view('dagulir.pengajuan-kredit.continue-draft-skema', $param);
        }
    }

    public function tempDagulir(Request $request){
        if ($request->skema_kredit == 'Dagulir') {
            if(isset($request->id_dagulir_temp)){
                $dagulir = TemporaryService::saveDagulir(
                    $request->id_dagulir_temp,
                    TemporaryService::convertDagulirReq($request)
                );
            } else{
                $dagulir = TemporaryService::saveDagulir(
                    null,
                    TemporaryService::convertDagulirReq($request)
                );
            }

            try {
                foreach ($request->dataLevelDua as $key => $value) {
                    $dataSlik = getDataLevel($value);
                    $cek = DB::table('jawaban_temp')
                        ->where('temporary_dagulir_id', $request->id_dagulir_temp ?? $dagulir->id)
                        ->where('id_jawaban', $dataSlik[1])
                        ->count('id');
                    if ($cek < 1) {
                        DB::table('jawaban_temp')
                            ->insert([
                                'temporary_dagulir_id' => $request->id_dagulir_temp ?? $dagulir->id,
                                'id_jawaban' => $dataSlik[1],
                                'skor' => $dataSlik[0],
                                'id_option' => $key,
                                'created_at' => now()
                            ]);
                    } else {
                        DB::table('jawaban_temp')
                            ->where('temporary_dagulir_id', $request->id_dagulir_temp ?? $dagulir->id)
                            ->where('id_option', $key)
                            ->update([
                                'id_jawaban' => $dataSlik[1],
                                'skor' => $dataSlik[0],
                                'updated_at' => now()
                            ]);
                    }
                }
            } catch (\Exception $e) {
                return $e;
            }

            return response()->json([
                'status' => 'ok',
                'code' => 200,
                'data' => $dagulir,
            ]);
        } else {
            if (isset($request->id_dagulir_temp)) {
                $nasabah = TemporaryService::saveNasabah(
                    $request->id_dagulir_temp,
                    TemporaryService::convertNasabahReq($request)
                );
            } else {
                $nasabah = TemporaryService::saveNasabah(
                    null,
                    TemporaryService::convertNasabahReq($request)
                );
            }

            try {
                foreach ($request->dataLevelDua as $key => $value) {
                    $dataSlik = getDataLevel($value);
                    $cek = DB::table('jawaban_temp')
                        ->where('id_temporary_calon_nasabah', $request->id_dagulir_temp ?? $nasabah->id)
                        ->where('id_jawaban', $dataSlik[1])
                        ->count('id');
                    if ($cek < 1) {
                        DB::table('jawaban_temp')
                            ->insert([
                                'id_temporary_calon_nasabah' => $request->id_dagulir_temp ?? $nasabah->id,
                                'id_jawaban' => $dataSlik[1],
                                'skor' => $dataSlik[0],
                                'id_option' => $key,
                                'created_at' => now()
                            ]);
                    } else {
                        DB::table('jawaban_temp')
                            ->where('id_temporary_calon_nasabah', $request->id_dagulir_temp ?? $nasabah->id)
                            ->where('id_option', $key)
                            ->update([
                                'id_jawaban' => $dataSlik[1],
                                'skor' => $dataSlik[0],
                                'updated_at' => now()
                            ]);
                    }
                }
            } catch (Exception $e) {
                return $e;
            } catch (QueryException $e){
                return $e;
            }

            return response()->json([
                'status' => 'ok',
                'code' => 200,
                'data' => $nasabah,
            ]);

        }

    }

    public function tempJawaban(Request $request)
    {
        if ($request->skema_kredit == 'Dagulir') {
            $find = array('Rp ', '.');

            try {
                if ($request->kategori_jaminan_tambahan != null) {
                    DB::table('temporary_calon_nasabah')
                        ->where('id', $request->id_dagulir_temp)
                        ->update([
                            'jaminan_tambahan' => $request->kategori_jaminan_tambahan
                        ]);
                }
                foreach ($request->id_level as $key => $value) {
                    $cekData = DB::table('temporary_jawaban_text')
                        ->where('temporary_dagulir_id', $request->id_dagulir_temp)
                        ->where('id_jawaban', $value)
                        ->first();
                    if (!$cekData) {
                        $dataJawabanText = new JawabanTemp();
                        $dataJawabanText->temporary_dagulir_id = $request->get('id_dagulir_temp');
                        $dataJawabanText->id_jawaban = $request->get('id_level')[$key];
                        $dataJawabanText->temporary_dagulir_id = $request->id_dagulir_temp;

                        $dataJawabanText->opsi_text = $request->get('informasi')[$key];
                        if ($value != '131' && $value != '143' && $value != '90' && $value != '138') {
                            $dataJawabanText->opsi_text = str_replace($find, '', $request->get('informasi')[$key]);
                        } else {
                            $dataJawabanText->opsi_text = $request->get('informasi')[$key];
                        }

                        $dataJawabanText->save();
                    } else {
                        $data = DB::table('temporary_jawaban_text')
                            ->where('id_jawaban', $request->get('id_level')[$key])
                            ->where('temporary_dagulir_id', $request?->id_dagulir_temp)
                            ->update([
                                'opsi_text' => ($value != '131' && $value != '143' && $value != '90' && $value != '138') ? str_replace($find, '', $request->get('informasi')[$key]) : $request->get('informasi')[$key],
                            ]);
                    }
                }
            } catch (Exception $e) {
                // DB::rollBack();
                // return response()->json([
                //     'status' => 'failed',
                //     'message' => $e->getMessage(),
                // ]);
            } catch (QueryException $e) {
                // DB::rollBack();
                // return response()->json([
                //     'status' => 'failed',
                //     'message' => $e->getMessage(),
                // ]);
            }

            $finalArray = array();
            $rata_rata = array();

            if(isset($request->kategori_jaminan_tambahan)){
                JawabanTemp::insert([
                    'temporary_dagulir_id' => $request->get('id_dagulir_temp'),
                    'id_jawaban' => 76,
                    'opsi_text' => $request->kategori_jaminan_tambahan
                ]);
            }

            try {
                if ($request->ijin_usaha == 'tidak_ada_legalitas_usaha') {
                    $dokumenUsaha = DB::table('item')
                        ->where('nama', 'LIKE', '%NIB%')
                        ->orWhere('nama', 'LIKE', '%Surat Keterangan Usaha%')
                        ->orWhere('nama', 'LIKE', '%NPWP%')
                        ->get();
                    foreach ($dokumenUsaha as $idDoc) {
                        DB::table('temporary_jawaban_text')
                            ->where('temporary_dagulir_id', $request->id_dagulir_temp)
                            ->where('id_jawaban', $idDoc->id)
                            ->delete();
                    }
                }
                if ($request->isNpwp == 0) {
                    $dokumenUsaha = DB::table('item')
                        ->orWhere('nama', 'LIKE', '%NPWP%')
                        ->get();
                    foreach ($dokumenUsaha as $idDoc) {
                        DB::table('temporary_jawaban_text')
                            ->where('temporary_dagulir_id', $request->id_dagulir_temp)
                            ->where('id_jawaban', $idDoc->id)
                            ->delete();
                    }
                }

                $finalArray = array();
                $rata_rata = array();
                // data Level dua
                if ($request->dataLevelDua != null) {
                    $data = $request->dataLevelDua;
                    foreach ($data as $key => $value) {
                        if ($value != null) {
                            $data_level_dua = getDataLevel($value);
                            $skor[$key] = $data_level_dua[0];
                            $id_jawaban[$key] = $data_level_dua[1];
                            //jika skor nya tidak kosong
                            if ($skor[$key] != 'kosong') {
                                if ($id_jawaban[$key] == 66 || $id_jawaban[$key] == 187) {
                                    if ($skor[$key] == 1) {
                                        $statusSlik = true;
                                    }
                                }
                                array_push($rata_rata, $skor[$key]);
                            } else {
                                $skor[$key] = NULL;
                            }
                            array_push(
                                $finalArray,
                                array(
                                    'temporary_dagulir_id' => $request?->id_dagulir_temp,
                                    'id_jawaban' => $id_jawaban[$key],
                                    'skor' => $skor[$key],
                                    'id_option' => $key,
                                    'created_at' => date("Y-m-d H:i:s"),
                                )
                            );
                        }
                    }
                } else {
                }
            } catch (Exception $e) {
                return $e;
            }

            try {
                // data level tiga
                if ($request->dataLevelTiga != null) {
                    $dataLevelTiga = $request->dataLevelTiga;
                    foreach ($dataLevelTiga as $key => $value) {
                        if ($value != null) {
                            $data_level_tiga = getDataLevel($value);
                            $skor[$key] = $data_level_tiga[0];
                            $id_jawaban[$key] = $data_level_tiga[1];
                            //jika skor nya tidak kosong
                            if ($skor[$key] != 'kosong') {
                                array_push($rata_rata, $skor[$key]);
                            } else {
                                $skor[$key] = NULL;
                            }
                            array_push(
                                $finalArray,
                                array(
                                    'temporary_dagulir_id' => $request?->id_dagulir_temp,
                                    'id_jawaban' => $id_jawaban[$key],
                                    'skor' => $skor[$key],
                                    'id_option' => $key,
                                    'created_at' => date("Y-m-d H:i:s"),
                                )
                            );
                        }
                    }
                } else {
                }
            } catch (Exception $e) {
                return $e;
            }

            try {
                // data level empat
                if ($request->dataLevelEmpat != null) {
                    $dataLevelEmpat = $request->dataLevelEmpat;
                    foreach ($dataLevelEmpat as $key => $value) {
                        if ($value != null) {
                            $data_level_empat = getDataLevel($value);
                            $skor[$key] = $data_level_empat[0];
                            $id_jawaban[$key] = $data_level_empat[1];
                            //jika skor nya tidak kosong
                            if ($skor[$key] != 'kosong') {
                                array_push($rata_rata, $skor[$key]);
                            } else {
                                $skor[$key] = NULL;
                            }
                            array_push(
                                $finalArray,
                                array(
                                    'temporary_dagulir_id' => $request?->id_dagulir_temp,
                                    'id_jawaban' => $id_jawaban[$key],
                                    'skor' => $skor[$key],
                                    'id_option' => $key,
                                    'created_at' => date("Y-m-d H:i:s"),
                                )
                            );
                        }
                    }
                } else {
                }
            } catch (Exception $e) {
                return $e;
            }

            try {
                foreach ($request->pendapat_per_aspek as $i => $val) {
                    $cekUsulan = DB::table('temporary_usulan_dan_pendapat')
                        ->where('temporary_dagulir_id', $request->id_dagulir_temp)
                        ->where('id_aspek', $i)
                        ->count('id');
                    if ($cekUsulan < 1) {
                        DB::table('temporary_usulan_dan_pendapat')
                            ->insert([
                                'temporary_dagulir_id' => $request->id_dagulir_temp,
                                'id_aspek' => $i,
                                'usulan' => $val,
                                'created_at' => now()
                            ]);
                    } else {
                        DB::table('temporary_usulan_dan_pendapat')
                            ->where('temporary_dagulir_id', $request->id_dagulir_temp)
                            ->where('id_aspek', $i)
                            ->update([
                                'usulan' => $val,
                                'updated_at' => now()
                            ]);
                    }
                }
            } catch (Exception $e) {
                return $e;
            }

            try {
                for ($i = 0; $i < count($finalArray); $i++) {
                    $cekDataSelect = DB::table('jawaban_temp')
                        ->where('temporary_dagulir_id', $request->id_dagulir_temp)
                        ->where('id_jawaban', $finalArray[$i]['id_jawaban'])
                        ->count('id');

                    if ($cekDataSelect < 1) {
                        JawabanTempModel::insert($finalArray[$i]);
                    } else {
                        for ($i = 0; $i < count($finalArray); $i++) {
                            DB::table('jawaban_temp')
                                ->where('id_option', $finalArray[$i]['id_option'])
                                ->where('temporary_dagulir_id', $request?->id_dagulir_temp)
                                ->update([
                                    'skor' => $finalArray[$i]['skor'],
                                    'id_jawaban' => $finalArray[$i]['id_jawaban']
                                ]);
                        }
                    }
                }
            } catch (Exception $e) {
                return $e;
            }

            return response()->json([
                'status' => 'ok',
                'nasabah' => $request->id_dagulir_temp,
                'aspek' => $request->pendapat_per_aspek,
                'all' => $request->all()
            ]);
        }else{
            $find = array('Rp ', '.');

            try {
                if ($request->kategori_jaminan_tambahan != null) {
                    DB::table('temporary_calon_nasabah')
                        ->where('id', $request->id_dagulir_temp)
                        ->update([
                            'jaminan_tambahan' => $request->kategori_jaminan_tambahan
                        ]);
                }
                foreach ($request->id_level as $key => $value) {
                    $cekData = DB::table('temporary_jawaban_text')
                        ->where('id_temporary_calon_nasabah', $request->id_dagulir_temp)
                        ->where('id_jawaban', $value)
                        ->first();
                    if (!$cekData) {
                        $dataJawabanText = new JawabanTemp();
                        $dataJawabanText->id_temporary_calon_nasabah = $request->get('id_dagulir_temp');
                        $dataJawabanText->id_jawaban = $request->get('id_level')[$key];
                        $dataJawabanText->id_temporary_calon_nasabah = $request->id_dagulir_temp;

                        $dataJawabanText->opsi_text = $request->get('informasi')[$key];
                        if ($value != '131' && $value != '143' && $value != '90' && $value != '138') {
                            $dataJawabanText->opsi_text = str_replace($find, '', $request->get('informasi')[$key]);
                        } else {
                            $dataJawabanText->opsi_text = $request->get('informasi')[$key];
                        }

                        $dataJawabanText->save();
                    } else {
                        $data = DB::table('temporary_jawaban_text')
                            ->where('id_jawaban', $request->get('id_level')[$key])
                            ->where('id_temporary_calon_nasabah', $request?->id_dagulir_temp)
                            ->update([
                                'opsi_text' => ($value != '131' && $value != '143' && $value != '90' && $value != '138') ? str_replace($find, '', $request->get('informasi')[$key]) : $request->get('informasi')[$key],
                            ]);
                    }
                }
            } catch (Exception $e) {
                // DB::rollBack();
                // return response()->json([
                //     'status' => 'failed',
                //     'message' => $e->getMessage(),
                // ]);
            } catch (QueryException $e) {
                // DB::rollBack();
                // return response()->json([
                //     'status' => 'failed',
                //     'message' => $e->getMessage(),
                // ]);
            }

            $finalArray = array();
            $rata_rata = array();

            try {
                if ($request->ijin_usaha == 'tidak_ada_legalitas_usaha') {
                    $dokumenUsaha = DB::table('item')
                        ->where('nama', 'LIKE', '%NIB%')
                        ->orWhere('nama', 'LIKE', '%Surat Keterangan Usaha%')
                        ->orWhere('nama', 'LIKE', '%NPWP%')
                        ->get();
                    foreach ($dokumenUsaha as $idDoc) {
                        DB::table('temporary_jawaban_text')
                            ->where('id_temporary_calon_nasabah', $request->id_dagulir_temp)
                            ->where('id_jawaban', $idDoc->id)
                            ->delete();
                    }
                }
                if ($request->isNpwp == 0) {
                    $dokumenUsaha = DB::table('item')
                        ->orWhere('nama', 'LIKE', '%NPWP%')
                        ->get();
                    foreach ($dokumenUsaha as $idDoc) {
                        DB::table('temporary_jawaban_text')
                            ->where('id_temporary_calon_nasabah', $request->id_dagulir_temp)
                            ->where('id_jawaban', $idDoc->id)
                            ->delete();
                    }
                }

                $finalArray = array();
                $rata_rata = array();
                // data Level dua
                if ($request->dataLevelDua != null) {
                    $data = $request->dataLevelDua;
                    foreach ($data as $key => $value) {
                        if ($value != null) {
                            $data_level_dua = getDataLevel($value);
                            $skor[$key] = $data_level_dua[0];
                            $id_jawaban[$key] = $data_level_dua[1];
                            //jika skor nya tidak kosong
                            if ($skor[$key] != 'kosong') {
                                if ($id_jawaban[$key] == 66 || $id_jawaban[$key] == 187) {
                                    if ($skor[$key] == 1) {
                                        $statusSlik = true;
                                    }
                                }
                                array_push($rata_rata, $skor[$key]);
                            } else {
                                $skor[$key] = NULL;
                            }
                            array_push(
                                $finalArray,
                                array(
                                    'id_temporary_calon_nasabah' => $request?->id_dagulir_temp,
                                    'id_jawaban' => $id_jawaban[$key],
                                    'skor' => $skor[$key],
                                    'id_option' => $key,
                                    'created_at' => date("Y-m-d H:i:s"),
                                )
                            );
                        }
                    }
                } else {
                }
            } catch (Exception $e) {
            }

            try {
                // data level tiga
                if ($request->dataLevelTiga != null) {
                    $dataLevelTiga = $request->dataLevelTiga;
                    foreach ($dataLevelTiga as $key => $value) {
                        if ($value != null) {
                            $data_level_tiga = getDataLevel($value);
                            $skor[$key] = $data_level_tiga[0];
                            $id_jawaban[$key] = $data_level_tiga[1];
                            //jika skor nya tidak kosong
                            if ($skor[$key] != 'kosong') {
                                array_push($rata_rata, $skor[$key]);
                            } else {
                                $skor[$key] = NULL;
                            }
                            array_push(
                                $finalArray,
                                array(
                                    'id_temporary_calon_nasabah' => $request?->id_dagulir_temp,
                                    'id_jawaban' => $id_jawaban[$key],
                                    'skor' => $skor[$key],
                                    'id_option' => $key,
                                    'created_at' => date("Y-m-d H:i:s"),
                                )
                            );
                        }
                    }
                } else {
                }
            } catch (Exception $e) {
            }

            try {
                // data level empat
                if ($request->dataLevelEmpat != null) {
                    $dataLevelEmpat = $request->dataLevelEmpat;
                    foreach ($dataLevelEmpat as $key => $value) {
                        if ($value != null) {
                            $data_level_empat = getDataLevel($value);
                            $skor[$key] = $data_level_empat[0];
                            $id_jawaban[$key] = $data_level_empat[1];
                            //jika skor nya tidak kosong
                            if ($skor[$key] != 'kosong') {
                                array_push($rata_rata, $skor[$key]);
                            } else {
                                $skor[$key] = NULL;
                            }
                            array_push(
                                $finalArray,
                                array(
                                    'id_temporary_calon_nasabah' => $request?->id_dagulir_temp,
                                    'id_jawaban' => $id_jawaban[$key],
                                    'skor' => $skor[$key],
                                    'id_option' => $key,
                                    'created_at' => date("Y-m-d H:i:s"),
                                )
                            );
                        }
                    }
                } else {
                }
            } catch (Exception $e) {
            }

            try {
                foreach ($request->pendapat_per_aspek as $i => $val) {
                    $cekUsulan = DB::table('temporary_usulan_dan_pendapat')
                        ->where('id_temp', $request->id_dagulir_temp)
                        ->where('id_aspek', $i)
                        ->count('id');
                    if ($cekUsulan < 1) {
                        DB::table('temporary_usulan_dan_pendapat')
                            ->insert([
                                'id_temp' => $request->id_dagulir_temp,
                                'id_aspek' => $i,
                                'usulan' => $val,
                                'created_at' => now()
                            ]);
                    } else {
                        DB::table('temporary_usulan_dan_pendapat')
                            ->where('id_temp', $request->id_dagulir_temp)
                            ->where('id_aspek', $i)
                            ->update([
                                'usulan' => $val,
                                'updated_at' => now()
                            ]);
                    }
                }
            } catch (Exception $e) {
            }

            try {
                for ($i = 0; $i < count($finalArray); $i++) {
                    $cekDataSelect = DB::table('jawaban_temp')
                        ->where('id_temporary_calon_nasabah', $request->id_dagulir_temp)
                        ->where('id_jawaban', $finalArray[$i]['id_jawaban'])
                        ->count('id');

                    if ($cekDataSelect < 1) {
                        JawabanTempModel::insert($finalArray[$i]);
                    } else {
                        for ($i = 0; $i < count($finalArray); $i++) {
                            DB::table('jawaban_temp')
                                ->where('id_option', $finalArray[$i]['id_option'])
                                ->where('id_temporary_calon_nasabah', $request?->id_dagulir_temp)
                                ->update([
                                    'skor' => $finalArray[$i]['skor'],
                                    'id_jawaban' => $finalArray[$i]['id_jawaban']
                                ]);
                        }
                    }
                }
            } catch (Exception $e) {
            }

            return response()->json([
                'status' => 'ok',
                'nasabah' => $request->id_dagulir_temp,
                'aspek' => $request->pendapat_per_aspek,
                'all' => $request->all()
            ]);
        }
    }

    public function tempFile(Request $request)
    {
        if ($request->skema_kredit == 'Dagulir') {
            $dagulir = PengajuanDagulirTemp::find($request->id_dagulir_temp);
            $temp = DB::table('temporary_jawaban_text')
                        ->where('temporary_dagulir_id', $request->dagulir_temp)
                        ->where('id_jawaban', $request->answer_id)
                        ->first();
            if (!$temp) {
                // Belum pernah save temp
                $data = TemporaryService::saveFileDagulir(
                    $dagulir,
                    $request->answer_id,
                    $request->file_id,
                    $request->file('file')
                );
            }
            else {
                // Sudah pernah save temp
                $req_file = $request->file('file');
                $current_filename = $temp->opsi_text;
                $aID = $request->answer_id;
                $path = public_path("upload/temp/{$aID}/");
                if ($current_filename != $req_file->getClientOriginalName()) {
                    // Update file
                    @unlink($path . $temp->opsi_text);
                    DB::table('temporary_jawaban_text')
                        ->where('temporary_dagulir_id'. $request->dagulir_temp)
                        ->where('id_jawaban', $request->answer_id)
                        ->update(['opsi_text' => $req_file->getClientOriginalName()]);
                    $data = [
                        'filename' => $req_file->getClientOriginalName(),
                        'file_id' => $temp->id,
                    ];
                }
                else {
                    $data = [
                        'filename' => $temp->opsi_text,
                        'file_id' => $temp->id,
                    ];
                }
            }
            return response()->json([
                'statusCode' => 200,
                'data' => $data,
            ]);
        }else{
            $nasabah = CalonNasabahTemp::findOrFail($request->id_dagulir_temp);
            $temp = DB::table('temporary_jawaban_text')
                    ->where('id_temporary_calon_nasabah', $request->id_dagulir_temp)
                    ->where('id_jawaban', $request->answer_id)
                    ->first();
                    if (!$temp) {
                        // Belum pernah save temp
                        $data = TemporaryService::saveFile(
                            $nasabah,
                            $request->answer_id,
                            $request->file_id,
                            $request->file('file')
                        );
                    }else {
                        // Sudah pernah save temp
                        $req_file = $request->file('file');
                        $current_filename = $temp->opsi_text;
                        $aID = $request->answer_id;
                        $path = public_path("upload/temp/{$aID}/");
                        if ($current_filename != $req_file->getClientOriginalName()) {
                            // Update file
                            @unlink($path . $temp->opsi_text);
                            DB::table('temporary_jawaban_text')
                                ->where('id_temporary_calon_nasabah'. $request->id_dagulir_temp)
                                ->where('id_jawaban', $request->answer_id)
                                ->update(['opsi_text' => $req_file->getClientOriginalName()]);
                            $data = [
                                'filename' => $req_file->getClientOriginalName(),
                                'file_id' => $temp->id,
                            ];
                        }
                        else {
                            $data = [
                                'filename' => $temp->opsi_text,
                                'file_id' => $temp->id,
                            ];
                        }
                    }

            return response()->json([
                'statusCode' => 200,
                'data' => $data,
            ]);
        }
    }

    public function tempFileDataUmum(Request $request){
        $dagulir = PengajuanDagulirTemp::find($request->id_dagulir_temp);
        $data = TemporaryService::saveFileDagulirDataUmum($dagulir, $request, $request->file('file'));

        return response()->json([
            'statusCode' => 200,
            'data' => $data
        ]);
    }

    public function listDraftDagulir(){
        return view('dagulir.pengajuan-kredit.index-draft');
    }

    public function deleteDraft($id, Request $request){
        DB::beginTransaction();
        try{
            if ($request->has('skema_kredit')) {
                DB::table('jawaban_temp')->where('id_temporary_calon_nasabah', $id)->delete();
                DB::table('temporary_jawaban_text')->where('id_temporary_calon_nasabah', $id)->delete();
                DB::table('temporary_usulan_dan_pendapat')->where('id_temp', $id)->delete();
                DB::table('temporary_calon_nasabah')->where('id', $id)->delete();
            }else{
                $pengajuanTemp = PengajuanDagulirTemp::find($id);
                $pengajuanTemp->delete();
            }
            DB::commit();

            Alert::success('Berhasil', 'Berhasil menghapus data draft');
            return redirect()->back();
        } catch (Exception $e){
            DB::rollBack();
            Alert::error('Terjadi kesalahan', $e->getMessage());
            return redirect()->back();
        } catch(QueryException $e){
            DB::rollBack();
            Alert::error('Terjadi kesalahan', $e->getMessage());
            return redirect()->back();
        }
    }
}
