<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PengajuanKreditController;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;
class PengajuanAPIController extends Controller
{
    private $currentMonth;
    public function __construct() {
        $this->currentMonth = date('m');
    }
    static function getKaryawan($nip){
        // retrieve from api
        $konfiAPI = DB::table('api_configuration')->first();
        $host = $konfiAPI->hcs_host;
        $apiURL = $host . '/api/karyawan';

        try {
            $response = Http::withOptions(['verify' => false])->get($apiURL, [
                'nip' => $nip,
            ]);

            $statusCode = $response->status();
            $responseBody = json_decode($response->getBody(), true);

            if ($responseBody != null) {
                if (is_array($responseBody)) {
                    if (array_key_exists('data', $responseBody))
                        return $responseBody['data'];
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

    public function login(Request $request)
    {
        // $ip = $this->get_client_ip();
        // $personalAccessToken = new PersonalAccessToken();
        // array_push($personalAccessToken->fillable, 'project');

        // $os = PHP_OS; // Get sistem operasi server
        // if (strpos($os, 'WIN') !== false) { // Windows
        //     $ip = exec('ipconfig');
        // } elseif (strpos($os, 'Darwin') !== false) { // MacOS
        //     $ip = exec('ipconfig getifaddr en0');
        // } else { // Linux
        //     $ip = exec('hostname -I');
        // }
        $ip = $request->has('ip') ? $request->get('ip') : $_SERVER['REMOTE_ADDR'];

        $user = User::select(
                    'users.*',
                    'cabang.kode_cabang'
                )
                ->where('users.email', $request['email'])
                ->orWhere('users.nip', $request['email'])
                ->leftJoin('cabang', 'cabang.id', 'users.id_cabang')
                ->first();

        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //whether ip is from remote address
        else
        {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        $device_name = gethostname();

        $detail = [
            'nip' => null,
            'nama' => null,
            'jabatan' => null,
            'nama_jabatan' => null,
            'entitas' => null,
            'bagian' => null,
        ];

        if ($user) {
            $detail = [
                'nip' => null,
                'nama' => null,
                'jabatan' => null,
                'nama_jabatan' => null,
                'entitas' => null,
                'bagian' => null,
            ];

            if ($user->role != 'Direksi') {
                // Cek User ditemukan atau tidak
                if (is_numeric($request['email'])) {
                    $cekNIPUser = User::where('nip', $request['email'])
                        ->first();
                    if ($cekNIPUser) {
                        if (!Auth::attempt(['email' => $cekNIPUser->email, 'password' => $request['password']])) {
                            return response()->json([
                                'status' => 'gagal',
                                'message' => 'Email atau NIP tidak ditemukan.',
                                'req' => $request->all()
                            ], 401);
                        }
                    } else {
                        if (!Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
                            return response()->json([
                                'status' => 'gagal',
                                'message' => 'Email atau NIP tidak ditemukan.',
                                'req' => $request->all()
                            ], 401);
                        }
                    }
                }
            }
        }
        else {
            return response()->json([
                'status' => 'gagal',
                'message' => 'User tidak ditemukan',
            ]);
        }

        // Cek Role user jika tersedia
        if($user->role == 'Administrator'){
            $current_token = DB::table('personal_access_tokens')
                                ->where('tokenable_id', $user->id)
                                ->where('project', $request->project)
                                ->first();
            if($current_token){
                $last_used = new DateTime($current_token->last_used_at);
                $now = new DateTime(date('Y-m-d H:i:s'));
                $interval = $last_used->diff($now);
                $diff_minutes = $interval->format("%i");

                if (intval($diff_minutes) > 30) {
                    DB::table('personal_access_tokens')
                        ->where('tokenable_id', $user->id)
                        ->where('project', $request->project)
                        ->delete();
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'Akun sedang digunakan di perangkat lain.'
                    ], 401);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $pat_id = explode('|', $token)[0];

            // Set device name
            DB::table('personal_access_tokens')
                ->where('id', $pat_id)
                ->update([
                    'ip_address' => $ip,
                    'project' => $request->project,
                    'device_name' => $device_name,
                ]);

            return response()->json([
                'status' => 'berhasil',
                'message' => 'berhasil login',
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'kode_cabang' => '001',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'ip_address' => $ip,
                'data' => $user->nip ? $this->getKaryawan($user->nip) : $user
            ]);
        } else if($user->role != 'Administrator'){
            if($user->nip != null || $user->role == 'Direksi'){
                $current_token = DB::table('personal_access_tokens')
                                    ->where('tokenable_id', $user->id)
                                    ->where('project', $request->project)
                                    ->first();
                if($current_token){
                    $last_used = new DateTime($current_token->last_used_at);
                    $now = new DateTime(date('Y-m-d H:i:s'));
                    $interval = $last_used->diff($now);
                    $diff_minutes = $interval->format("%i");

                    if (intval($diff_minutes) > 30) {
                        DB::table('personal_access_tokens')
                            ->where('tokenable_id', $user->id)
                            ->where('project', $request->project)
                            ->delete();
                    } else {
                        return response()->json([
                            'status' => 'gagal',
                            'message' => 'Akun sedang digunakan di perangkat lain.'
                        ], 401);
                    }
                }

                $token = $user->createToken('auth_token')->plainTextToken;
                $tokenId =  explode('|', $token);
                DB::table('personal_access_tokens')
                    ->where('id', $tokenId[0])
                    ->update([
                        'ip_address' => $ip,
                        'project' => $request->project
                    ]);

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'berhasil login',
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'kode_cabang' => $user->kode_cabang,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'ip_address' => $ip,
                    'data' => $user->nip ? $this->getKaryawan($user->nip) : $user
                ]);
            } else {
                if($user->nip != null || $user->role == 'Direksi'){
                    $current_token = DB::table('personal_access_tokens')
                                        ->where('tokenable_id', $user->id)
                                        ->where('project', $request->project)
                                        ->first();
                    if($current_token){
                        $last_used = new DateTime($current_token->last_used_at);
                        $now = new DateTime(date('Y-m-d H:i:s'));
                        $interval = $last_used->diff($now);
                        $diff_minutes = $interval->format("%i");

                        if (intval($diff_minutes) > 30) {
                            DB::table('personal_access_tokens')
                                ->where('tokenable_id', $user->id)
                                ->where('project', $request->project)
                                ->delete();
                        } else {
                            return response()->json([
                                'status' => 'gagal',
                                'message' => 'Akun sedang digunakan di perangkat lain.'
                            ], 401);
                        }
                    }
                } else {
                    if ($user->role != 'Direksi') {
                        return response()->json([
                            'status' => 401,
                            'message' => 'Belum dilakukan Pengkinian Data User untuk $request->email.\nHarap menghubungi Divisi Pemasaran atau TI & AK.',
                        ]);
                    }
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $pat_id = explode('|', $token)[0];
            $tokenId =  explode('|', $token);
            DB::table('personal_access_tokens')
                ->where('id', $tokenId[0])
                ->update([
                    'ip_address' => $ip,
                    'project' => $request->project
                ]);
            if ($user->role == 'Direksi') {
                $detail['nama'] = $user->name;
            }
            else {
                if ($user->nip) {
                    $detail = $this->getKaryawan($user->nip);
                }
                else {
                    $detail['nama'] = $user->name;
                }

                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'berhasil login',
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'kode_cabang' => $user->role == 'Administrator' ? '001' : $user->kode_cabang,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'ip_address' => $ip,
                    'data' => $detail,
                ]);
            }

            // Set device name
            DB::table('personal_access_tokens')
                ->where('id', $pat_id)
                ->update([
                    'device_name' => $device_name,
                    'ip_address' => $ip_address,
                ]);

            return response()->json([
                'status' => 'gagal',
                'message' => 'User tidak ditemukan',
            ]);
        }
    }

    public function getSessionCheck($id){
        $session = DB::table('personal_access_tokens')->where('tokenable_id', $id)->first();
        if ($session) {
            return response()->json([
                'status' => 'sukses',
                'message' => 'Berhasil mengambil sesi'
            ]);
        }
        else {
            return response()->json([
                'status' => 'gagal',
                'message' => 'Sesi anda sudah berakhir'
            ]);
        }
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function getDataPengajuan($id, $user_id)
    {
        $user = User::select('id', 'role')->find($user_id);
        $data = DB::table('pengajuan')
            ->where('skema_kredit', 'KKB')
            ->where('posisi', 'Selesai')
            ->where('pengajuan.id', $id)
            ->whereNotNull('po')
            ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
            ->join('data_po', 'data_po.id_pengajuan', 'pengajuan.id')
            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
            // ->join('mst_tipe', 'mst_tipe.id', 'data_po.id_type')
            // ->join('mst_merk', 'mst_merk.id', 'mst_tipe.id_merk')
            // ->select('pengajuan.id', 'calon_nasabah.nama', 'calon_nasabah.jumlah_kredit', 'data_po.no_po', 'calon_nasabah.tenor_yang_diminta', 'pengajuan.sppk', 'pengajuan.po', 'pengajuan.tanggal', 'pengajuan.pk', 'mst_merk.merk', 'mst_tipe.tipe', 'data_po.tahun_kendaraan', 'data_po.harga', 'data_po.jumlah AS jumlah_kendaraan')
            ->select('pengajuan.id', 'calon_nasabah.nama', 'calon_nasabah.jumlah_kredit', 'data_po.no_po', 'data_po.tipe', 'data_po.merk', 'calon_nasabah.tenor_yang_diminta', 'calon_nasabah.alamat_rumah', 'calon_nasabah.alamat_usaha', 'pengajuan.sppk', 'pengajuan.po', 'pengajuan.tanggal', 'pengajuan.pk', 'data_po.tahun_kendaraan', 'data_po.harga', 'data_po.jumlah AS jumlah_kendaraan', 'cabang.kode_cabang', 'cabang.cabang', 'cabang.alamat AS alamat_cabang');

        if ($user_id != 0) {
            if ($user->role == 'Staf Analis Kredit') {
                $data->where('id_staf', $user_id);
            }
            if ($user->role == 'Penyelia Kredit') {
                $data->where('id_penyelia', $user_id);
            }
            if ($user->role == 'PBO') {
                $data->where('id_pbo', $user_id);
            }
            if ($user->role == 'PBP') {
                $data->where('id_pbp', $user_id);
            }
            if ($user->role == 'Pincab') {
                $data->where('id_pincab', $user_id);
            }
        }

        $data = $data->first();
        if ($data) {
            return response()->json([
                'id_pengajuan' => $data->id,
                'nama' => $data->nama,
                'alamat_rumah' => $data->alamat_rumah,
                'alamat_usaha' => $data->alamat_usaha,
                'jumlah_kredit' => intval($data->jumlah_kredit),
                'no_po' => $data->no_po,
                'tenor' => intval($data->tenor_yang_diminta),
                'sppk' => $data->sppk ?? null,
                'po' => $data->po ?? null,
                'pk' => $data->pk ?? null,
                'merk' => $data->merk,
                'tipe' => $data->tipe,
                'tahun_kendaraan' => $data->tahun_kendaraan,
                'harga_kendaraan' => $data->harga,
                'jumlah_kendaraan' => $data->jumlah_kendaraan,
                'tanggal' => $data->tanggal,
                'kode_cabang' => $data->kode_cabang,
                'cabang' => $data->cabang,
                'alamat_cabang' => $data->alamat_cabang,
            ]);
        }
        else {
            return response()->json([
                'status' => 'success',
                'message' => 'Data not found'
            ]);
        }
    }

    public function getDataPengajuanById($id)
    {
        $data = DB::table('pengajuan')
            ->where('skema_kredit', 'KKB')
            ->where('posisi', 'Selesai')
            ->where('pengajuan.id', $id)
            ->whereNotNull('po')
            ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
            ->join('data_po', 'data_po.id_pengajuan', 'pengajuan.id')
            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
            ->select('pengajuan.id', 'calon_nasabah.nama', 'calon_nasabah.jumlah_kredit', 'data_po.no_po', 'data_po.tipe', 'data_po.merk', 'calon_nasabah.tenor_yang_diminta', 'calon_nasabah.alamat_rumah', 'calon_nasabah.alamat_usaha', 'pengajuan.sppk', 'pengajuan.po', 'pengajuan.tanggal', 'pengajuan.pk', 'data_po.tahun_kendaraan', 'data_po.harga', 'data_po.jumlah AS jumlah_kendaraan', 'cabang.kode_cabang', 'cabang.cabang', 'cabang.email AS email_cabang', 'cabang.alamat AS alamat_cabang');

        $data = $data->first();
        if ($data) {
            return response()->json([
                'id_pengajuan' => $data->id,
                'nama' => $data->nama,
                'alamat_rumah' => $data->alamat_rumah,
                'alamat_usaha' => $data->alamat_usaha,
                'jumlah_kredit' => intval($data->jumlah_kredit),
                'no_po' => $data->no_po,
                'tenor' => intval($data->tenor_yang_diminta),
                'sppk' => $data->sppk ?? null,
                'po' => $data->po ?? null,
                'pk' => $data->pk ?? null,
                'merk' => $data->merk,
                'tipe' => $data->tipe,
                'tahun_kendaraan' => $data->tahun_kendaraan,
                'harga_kendaraan' => $data->harga,
                'jumlah_kendaraan' => $data->jumlah_kendaraan,
                'tanggal' => $data->tanggal,
                'kode_cabang' => $data->kode_cabang,
                'cabang' => $data->cabang,
                'email_cabang' => $data->email_cabang,
                'alamat_cabang' => $data->alamat_cabang,
            ]);
        }
        else {
            return response()->json([
                'status' => 'success',
                'message' => 'Data not found'
            ]);
        }
    }

    public function getDataPengajuanSearch($user_id, Request $request)
    {
        $q = $request->get('query');
        $user = User::select('id', 'role')->find($user_id);
        $data = DB::table('pengajuan')
            ->where('skema_kredit', 'KKB')
            ->where('posisi', 'Selesai')
            ->whereNotNull('po')
            ->where('calon_nasabah.nama', 'like', "%$q%")
            ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
            ->join('data_po', 'data_po.id_pengajuan', 'pengajuan.id')
            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
            ->select('pengajuan.id', 'calon_nasabah.nama', 'calon_nasabah.jumlah_kredit', 'data_po.no_po', 'data_po.tipe', 'data_po.merk', 'calon_nasabah.tenor_yang_diminta', 'calon_nasabah.alamat_rumah', 'calon_nasabah.alamat_usaha', 'pengajuan.sppk', 'pengajuan.po', 'pengajuan.tanggal', 'pengajuan.pk', 'data_po.tahun_kendaraan', 'data_po.harga', 'data_po.jumlah AS jumlah_kendaraan', 'cabang.kode_cabang', 'cabang.cabang', 'cabang.email AS email_cabang', 'cabang.alamat AS alamat_cabang');

        if ($user_id != 0) {
            if ($user->role == 'Staf Analis Kredit') {
                $data->where('id_staf', $user_id);
            }
            if ($user->role == 'Penyelia Kredit') {
                $data->where('id_penyelia', $user_id);
            }
            if ($user->role == 'PBO') {
                $data->where('id_pbo', $user_id);
            }
            if ($user->role == 'PBP') {
                $data->where('id_pbp', $user_id);
            }
            if ($user->role == 'Pincab') {
                $data->where('id_pincab', $user_id);
            }
        }
        $data = $data->get();

        if ($data) {
            $arr = [];
            foreach ($data as $key => $value) {
                $arr_data = [
                    'id_pengajuan' => $value->id,
                    'nama' => $value->nama,
                    'alamat_rumah' => $value->alamat_rumah,
                    'alamat_usaha' => $value->alamat_usaha,
                    'jumlah_kredit' => intval($value->jumlah_kredit),
                    'no_po' => $value->no_po,
                    'tenor' => intval($value->tenor_yang_diminta),
                    'sppk' => $value->sppk ?? null,
                    'po' => $value->po ?? null,
                    'pk' => $value->pk ?? null,
                    'merk' => $value->merk,
                    'tipe' => $value->tipe,
                    'tahun_kendaraan' => $value->tahun_kendaraan,
                    'harga_kendaraan' => $value->harga,
                    'jumlah_kendaraan' => $value->jumlah_kendaraan,
                    'tanggal' => $value->tanggal,
                    'kode_cabang' => $value->kode_cabang,
                    'cabang' => $value->cabang,
                    'email_cabang' => $value->email_cabang,
                    'alamat_cabang' => $value->alamat_cabang,
                ];
                array_push($arr, $arr_data);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved',
                'total' => count($arr),
                'data' => $arr,
            ]);
        }
        else {
            return response()->json([
                'status' => 'success',
                'message' => 'Data not found'
            ]);
        }
    }

    public function getDataUserById($id)
    {
        $data = DB::table('users')
            ->select('users.*', 'c.kode_cabang')
            ->leftJoin('cabang AS c', 'c.id', 'users.id_cabang')
            ->where('users.id', $id)
            ->first();

        if ($data) {
            $detail = $this->getKaryawan($data->nip);
            $data->detail = $detail;
        }

        return response()->json($data);
    }

    public function getDataStafByCabang($kode_cabang)
    {
        $data = DB::table('users')
            ->select('users.*', 'c.kode_cabang')
            ->join('cabang AS c', 'c.id', 'users.id_cabang')
            ->where('c.kode_cabang', $kode_cabang)
            ->where('users.role', 'Staf Analis Kredit')
            ->get();

        foreach ($data as $key => $value) {
            $value->detail = $this->getKaryawan($value->nip);
        }

        return response()->json($data);
    }

    public function getDataUsersByCabang($kode_cabang)
    {
        $data = DB::table('users')
            ->select('users.*', 'c.kode_cabang')
            ->join('cabang AS c', 'c.id', 'users.id_cabang')
            ->where('c.kode_cabang', $kode_cabang)
            ->get();

        foreach ($data as $key => $value) {
            $value->detail = $this->getKaryawan($value->nip);
        }

        return response()->json($data);
    }

    public function getCabang($kode) {
        $data = DB::table('cabang')
            ->select('kode_cabang', 'cabang')
            ->where('kode_cabang', $kode)
            ->first();

        return response()->json($data);
    }

    public function getAllCabang() {
        $data = DB::table('cabang')
            ->select('kode_cabang', 'cabang')
            ->get();

        return response()->json($data);
    }

    public function getAllCabangMobile(){
        $data = DB::table('cabang')
            ->select('kode_cabang', 'cabang')
            ->get();

        return response()->json([
            'status' => 'berhasil',
            'message' => 'berhasil menampilkan data cabang.',
            'data' => $data
        ]);
    }

    public function getSumPengajuan(Request $request) {
        if ($request->all() != null){
            // return $request->all();
            $total_disetujui = DB::table('pengajuan')
                ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir') != null ? $request->get('tanggal_akhir') : now()])
                ->where('posisi', 'Selesai')
                ->whereNull('pengajuan.deleted_at')
                ->count();
            $total_ditolak = DB::table('pengajuan')
                ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir') != null ? $request->get('tanggal_akhir') : now()])
                ->where('posisi', 'Ditolak')
                ->whereNull('pengajuan.deleted_at')
                ->count();
            $total_diproses = DB::table('pengajuan')
                ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir') != null ? $request->get('tanggal_akhir') : now()])
                ->whereIn('posisi', ['Pincab','PBP','PBO','Review Penyelia','Proses Input Data'])
                ->whereNull('pengajuan.deleted_at')
                ->count();

            // $dataTertinggi = DB::table('pengajuan')
            //     ->rightJoin('cabang', function ($join) use ($request) {
            //         $join->on('cabang.id', '=', 'pengajuan.id_cabang')
            //         ->whereBetween('pengajuan.tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
            //     })
            //     ->selectRaw('IFNULL(COUNT(pengajuan.id), 0) AS total, cabang.kode_cabang, cabang.cabang')
            //     ->where('cabang.kode_cabang', '!=', '000')
            //     ->where('pengajuan.posisi', 'Selesai')
            //     ->whereNull('pengajuan.deleted_at')
            //     ->groupBy('cabang.kode_cabang', 'cabang.cabang')
            //     ->orderByRaw('total DESC')
            //     ->limit(5)
            //     ->get();

            $dataTertinggi = DB::table('cabang')
                ->leftJoin('pengajuan', function ($join) use ($request) {
                    $join->on('cabang.id', '=', 'pengajuan.id_cabang')
                        ->whereBetween('pengajuan.tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
                })
                ->selectRaw('IFNULL(COUNT(pengajuan.id), 0) AS total, cabang.kode_cabang, cabang.cabang')
                ->where('cabang.kode_cabang', '!=', '000')
                ->groupBy('cabang.kode_cabang', 'cabang.cabang')
                ->orderByRaw('total DESC, cabang.kode_cabang ASC')
                ->limit(5)
                ->get();

            // $dataTerendah = DB::table('pengajuan')
            //     ->rightJoin('cabang', function ($join) use ($request) {
            //         $join->on('cabang.id', '=', 'pengajuan.id_cabang')
            //         ->whereBetween('pengajuan.tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
            //     })
            //     ->selectRaw('IFNULL(COUNT(pengajuan.id), 0) AS total, cabang.kode_cabang, cabang.cabang')
            //     ->where('cabang.kode_cabang', '!=', '000')
            //     // ->where('pengajuan.posisi', 'Selesai')
            //     ->whereNull('pengajuan.deleted_at')
            //     ->groupBy('cabang.kode_cabang', 'cabang.cabang')
            //     ->orderByRaw('total asc')
            //     ->limit(5)
            //     ->get();
                $dataTerendah = DB::table('cabang')
                    ->leftJoin('pengajuan', function ($join) use ($request) {
                        $join->on('cabang.id', '=', 'pengajuan.id_cabang')
                            ->whereBetween('pengajuan.tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
                    })
                    ->selectRaw('IFNULL(COUNT(pengajuan.id), 0) AS total, cabang.kode_cabang, cabang.cabang')
                    ->where('cabang.kode_cabang', '!=', '000')
                    // ->where('pengajuan.posisi', 'Selesai')
                    ->whereNull('pengajuan.deleted_at')
                    ->groupBy('cabang.kode_cabang', 'cabang.cabang')
                    ->orderByRaw('total ASC, cabang.kode_cabang ASC') // Ubah ke ASC untuk mengambil data terendah
                    ->limit(5)
                    ->get();

            $message = 'berhasil menampilkan data pengajuan berdasarkan tanggal.';
        } else {
            $total_disetujui = DB::table('pengajuan')
                // ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir') != null ? $request->get('tanggal_akhir') : now()])
                ->where('posisi', 'Selesai')
                ->whereNull('pengajuan.deleted_at')
                ->count();
            $total_ditolak = DB::table('pengajuan')
                // ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir') != null ? $request->get('tanggal_akhir') : now()])
                ->where('posisi', 'Ditolak')
                ->whereNull('pengajuan.deleted_at')
                ->count();
            $total_diproses = DB::table('pengajuan')
                // ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir') != null ? $request->get('tanggal_akhir') : now()])
                ->whereIn('posisi', ['Pincab','PBP','PBO','Review Penyelia','Proses Input Data'])
                ->whereNull('pengajuan.deleted_at')
                ->count();
            $dataTertinggi = DB::table('pengajuan')
                ->selectRaw('IF(COUNT(pengajuan.id) > 0, COUNT(pengajuan.id), 0) as total, cabang.kode_cabang, cabang.cabang')
                ->rightJoin('cabang', 'cabang.id', 'pengajuan.id_cabang')
                ->whereNull('pengajuan.deleted_at')
                ->where('pengajuan.posisi', 'Selesai')
                ->where('cabang.kode_cabang', '!=', '000')
                ->groupBy('cabang.kode_cabang')
                ->orderBy('total', 'desc')
                ->limit('5')
                ->get();
            $dataTerendah = DB::table('pengajuan')
                ->selectRaw('IF(COUNT(pengajuan.id) > 0, COUNT(pengajuan.id), 0) as total, cabang.kode_cabang, cabang.cabang')
                ->rightJoin('cabang', 'cabang.id', 'pengajuan.id_cabang')
                ->whereNull('pengajuan.deleted_at')
                ->where('pengajuan.posisi', 'Selesai')
                ->where('cabang.kode_cabang', '!=', '000')
                ->groupBy('cabang.kode_cabang')
                ->orderBy('total', 'asc')
                ->limit('5')
                ->get();
            $message = 'berhasil menampilkan data pengajuan.';
        }
        // $dataKeseluruhan = DB::table('pengajuan')
        //     ->selectRaw('count(pengajuan.id) as total, cabang.kode_cabang, cabang.cabang')
        //     ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
        //     ->groupBy('cabang.kode_cabang')
        //     ->get();

        return response()->json([
            'status' => 'berhasil',
            'message' => $message,
            'total_disetujui' => $total_disetujui,
            'total_ditolak' => $total_ditolak,
            'total_diproses' => $total_diproses,
            'data' => [
                'tertinggi' => $dataTertinggi,
                'terendah' => $dataTerendah,
                // 'keseluruhan' => $dataKeseluruhan
            ]
        ], 200);

    }

    public function getSumSkema(Request $request){
        if ($request->all() == null) {
            return 'if';
            //Filter all skema without request in currentMonth
            $total = DB::table('pengajuan')
                ->selectRaw("sum(skema_kredit='PKPJ') as PKPJ,sum(skema_kredit='KKB') as KKB,sum(skema_kredit='Talangan Umroh') as Umroh,sum(skema_kredit='Prokesra') as Prokesra,sum(skema_kredit='Kusuma') as Kusuma")
                ->whereRaw('MONTH(tanggal) = ?', $this->currentMonth)
                ->whereNull('pengajuan.deleted_at')
                ->get();
            $data = DB::table('pengajuan')
                ->selectRaw("skema_kredit,sum(posisi='Selesai') as total_disetujui,sum(posisi='ditolak') as total_ditolak,sum(posisi='pincab') as posisi_pincab,sum(posisi='PBP') as posisi_pbp,sum(posisi='PBO') as posisi_pbo,sum(posisi='Review Penyelia') as posisi_penyelia,sum(posisi='Proses Input Data') as posisi_staf")
                ->whereRaw('MONTH(tanggal) = ?', $this->currentMonth)
                ->whereNull('pengajuan.deleted_at')
                ->groupBy('skema_kredit')
                ->get();
            $message = 'Berhasil Menampilkan Total Keseluruhan Skema Data Pengajuan Bulan '. date('F Y') .'.';

            return response()->json([
                'status' => 'berhasil',
                'message' => $message,
                // 'total' => $total,
                'data' => [
                    'total' => $total,
                    'posisi' => $data,
                ]
            ], 200);
        } else {
            if ($request->get('skema') != null) {
                //Skema not null
                if ($request->get('cabang') != null) {
                    if ($request->get('tanggal_awal') != null && $request->get('tanggal_akhir') != null) {
                        //With date filter
                        $data = DB::table('pengajuan')
                            ->selectRaw("cabang.kode_cabang,cabang.cabang,count(pengajuan.id) as total,sum(posisi='Selesai') as total_disetujui,sum(posisi='ditolak') as total_ditolak,sum(posisi='pincab') as posisi_pincab,sum(posisi='PBP') as posisi_pbp,sum(posisi='PBO') as posisi_pbo,sum(posisi='Review Penyelia') as posisi_penyelia,sum(posisi='Proses Input Data') as posisi_staf")
                            ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir')])
                            ->whereNull('pengajuan.deleted_at')
                            ->where('skema_kredit', $request->get('skema'))
                            ->where('cabang.kode_cabang', $request->get('cabang'))
                            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                            ->groupBy('cabang.kode_cabang')
                            ->get();
                        $message = 'Berhasil Menampilkan Total Skema Data Pengajuan Cabang '. $request->get('cabang') .'.';
                    } else {
                        //without date filter
                        $data = DB::table('pengajuan')
                            ->selectRaw("cabang.kode_cabang,cabang.cabang,count(pengajuan.id) as total,sum(posisi='Selesai') as total_disetujui,sum(posisi='ditolak') as total_ditolak,sum(posisi='pincab') as posisi_pincab,sum(posisi='PBP') as posisi_pbp,sum(posisi='PBO') as posisi_pbo,sum(posisi='Review Penyelia') as posisi_penyelia,sum(posisi='Proses Input Data') as posisi_staf")
                            ->whereRaw('MONTH(tanggal) = ?', $this->currentMonth)
                            ->whereNull('pengajuan.deleted_at')
                            ->where('skema_kredit', $request->get('skema'))
                            ->where('cabang.kode_cabang', $request->get('cabang'))
                            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                            ->groupBy('cabang.kode_cabang')
                            ->get();
                        $message = 'Berhasil Menampilkan Total Skema Data Pengajuan Cabang '. $request->get('cabang') .' Bulan '. date('F Y') .'.';
                    }
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => $message,
                        'data' => $data
                    ], 200);
                } else {
                    if ($request->get('tanggal_awal') != null && $request->get('tanggal_akhir') != null) {
                        //Only date filter
                        $data = DB::table('pengajuan')
                            ->selectRaw("cabang.kode_cabang,cabang.cabang,count(pengajuan.id) as total,sum(posisi='Selesai') as total_disetujui,sum(posisi='ditolak') as total_ditolak,sum(posisi='pincab') as posisi_pincab,sum(posisi='PBP') as posisi_pbp,sum(posisi='PBO') as posisi_pbo,sum(posisi='Review Penyelia') as posisi_penyelia,sum(posisi='Proses Input Data') as posisi_staf")
                            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                            ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir')])
                            ->whereNull('pengajuan.deleted_at')
                            ->where('skema_kredit', $request->get('skema'))
                            ->groupBy('cabang.kode_cabang')
                            ->get();
                        $dataTertinggi = DB::table('pengajuan as p')
                            ->selectRaw("IFNULL((COUNT(p.id)), 0) as total, cabang.kode_cabang, cabang.cabang")
                            ->rightJoin('cabang', function ($join) use ($request) {
                                $join->on('cabang.id', '=', 'p.id_cabang')
                                ->whereBetween('p.tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir')])
                                ->where('p.skema_kredit', $request->get('skema'));
                            })
                            // ->selectRaw("IFNULL((SELECT COUNT(pengajuan.id) FROM pengajuan WHERE pengajuan.tanggal BETWEEN '".$request->get('tanggal_awal')."' AND '".$request->get('tanggal_akhir')."' AND skema_kredit = '".$request->get('skema')."' AND id_cabang = c.id), 0) as total, c.kode_cabang, c.cabang")
                            // ->rightJoin('cabang as c', 'c.id', 'p.id_cabang')
                            // ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir')])
                            // ->where('skema_kredit', $request->get('skema'))
                            ->whereNull('p.deleted_at')
                            ->where('cabang.kode_cabang', '!=', '000')
                            ->groupBy('cabang.kode_cabang')
                            ->orderBy('total', 'desc')
                            ->limit('5')
                            ->get();
                        $dataTerendah = DB::table('pengajuan as p')
                            ->selectRaw("IFNULL((COUNT(p.id)), 0) as total, cabang.kode_cabang, cabang.cabang")
                            ->rightJoin('cabang', function ($join) use ($request) {
                                $join->on('cabang.id', '=', 'p.id_cabang')
                                ->whereBetween('p.tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir')])
                                ->where('p.skema_kredit', $request->get('skema'));
                            })
                            // ->selectRaw("IFNULL((SELECT COUNT(pengajuan.id) FROM pengajuan WHERE pengajuan.tanggal BETWEEN '".$request->get('tanggal_awal')."' AND '".$request->get('tanggal_akhir')."' AND skema_kredit = '".$request->get('skema')."' AND id_cabang = c.id), 0) as total, c.kode_cabang, c.cabang")
                            // ->rightJoin('cabang as c', 'c.id', 'p.id_cabang')
                            // ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir')])
                            // ->where('skema_kredit', $request->get('skema'))
                            ->whereNull('p.deleted_at')
                            ->where('cabang.kode_cabang', '!=', '000')
                            ->groupBy('cabang.kode_cabang')
                            ->orderBy('total', 'asc')
                            ->limit('5')
                            ->get();
                        $message = 'Berhasil Menampilkan Total Keseluruhan Skema Data Pengajuan berdasarkan skema & tanggal';
                    } else {
                        //Without date filter
                        $data = $data = DB::table('pengajuan')
                            ->selectRaw("cabang.kode_cabang,cabang.cabang,count(pengajuan.id) as total,sum(posisi='Selesai') as total_disetujui,sum(posisi='ditolak') as total_ditolak,sum(posisi='pincab') as posisi_pincab,sum(posisi='PBP') as posisi_pbp,sum(posisi='PBO') as posisi_pbo,sum(posisi='Review Penyelia') as posisi_penyelia,sum(posisi='Proses Input Data') as posisi_staf")
                            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                            ->whereRaw('MONTH(tanggal) = ?', $this->currentMonth)
                            ->whereNull('pengajuan.deleted_at')
                            ->where('skema_kredit', $request->get('skema'))
                            ->groupBy('cabang.kode_cabang')
                            ->get();
                        $dataTertinggi = DB::table('pengajuan as p')
                            ->selectRaw("IFNULL((COUNT(p.id)), 0) as total, cabang.kode_cabang, cabang.cabang")
                            ->rightJoin('cabang', function ($join) use ($request) {
                                $join->on('cabang.id', '=', 'p.id_cabang')
                                ->whereRaw('MONTH(p.tanggal) = ?', $this->currentMonth)
                                ->where('p.skema_kredit', $request->get('skema'));
                            })
                            // ->whereRaw('MONTH(tanggal) = ?', $this->currentMonth)
                            // ->where('skema_kredit', $request->get('skema'))
                            ->where('cabang.kode_cabang', '!=', '000')
                            ->whereNull('p.deleted_at')
                            ->groupBy('cabang.kode_cabang')
                            ->orderBy('total', 'desc')
                            ->limit('5')
                            ->get();
                        $dataTerendah = DB::table('pengajuan as p')
                            ->selectRaw("IFNULL((COUNT(p.id)), 0) as total, cabang.kode_cabang, cabang.cabang")
                            ->rightJoin('cabang', function ($join) use ($request) {
                                $join->on('cabang.id', '=', 'p.id_cabang')
                                ->whereRaw('MONTH(p.tanggal) = ?', $this->currentMonth)
                                ->where('p.skema_kredit', $request->get('skema'));
                            })
                            // ->whereRaw('MONTH(tanggal) = ?', $this->currentMonth)
                            // ->where('skema_kredit', $request->get('skema'))
                            ->where('cabang.kode_cabang', '!=', '000')
                            ->whereNull('p.deleted_at')
                            ->groupBy('cabang.kode_cabang')
                            ->orderBy('total', 'asc')
                            ->limit('5')
                            ->get();
                        $message = 'Berhasil Menampilkan Total Keseluruhan Skema Data Pengajuan Bulan '. date('F Y') .'.';
                    }
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => $message,
                        'data' => $data,
                        'ranking' => [
                            'tertinggi' => $dataTertinggi,
                            'terendah' => $dataTerendah
                        ]
                    ], 200);
                }
            } else {
                if ($request->get('cabang') != null) {
                    if ($request->get('tanggal_awal') != null && $request->get('tanggal_akhir') != null) {
                        //With date filter
                        $data = DB::table('pengajuan')
                            ->selectRaw("cabang.kode_cabang,cabang.cabang,sum(skema_kredit='PKPJ') as PKPJ,sum(skema_kredit='KKB') as KKB,sum(skema_kredit='Talangan Umroh') as Umroh,sum(skema_kredit='Prokesra') as Prokesra,sum(skema_kredit='Kusuma') as Kusuma")
                            ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir')])
                            ->whereNull('pengajuan.deleted_at')
                            // ->where('skema_kredit', $request->get('skema'))
                            ->where('cabang.kode_cabang', $request->get('cabang'))
                            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                            ->groupBy('cabang.kode_cabang')
                            ->get();
                        $message = 'Berhasil Menampilkan Total Skema Data Pengajuan Cabang '. $request->get('cabang') .'.';
                    } else {
                        //without date filter
                        $data = DB::table('pengajuan')
                            ->selectRaw("cabang.kode_cabang,cabang.cabang,sum(skema_kredit='PKPJ') as PKPJ,sum(skema_kredit='KKB') as KKB,sum(skema_kredit='Talangan Umroh') as Umroh,sum(skema_kredit='Prokesra') as Prokesra,sum(skema_kredit='Kusuma') as Kusuma")
                            ->whereRaw('MONTH(tanggal) = ?', $this->currentMonth)
                            ->whereNull('pengajuan.deleted_at')
                            // ->where('skema_kredit', $request->get('skema'))
                            ->where('cabang.kode_cabang', $request->get('cabang'))
                            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                            ->groupBy('cabang.kode_cabang')
                            ->get();
                        $message = 'Berhasil Menampilkan Total Skema Data Pengajuan Cabang '. $request->get('cabang') .' Bulan '. date('F Y') .'.';
                    }
                } else {
                    //Only date filter
                    $data = DB::table('pengajuan')
                        ->selectRaw("cabang.kode_cabang,cabang.cabang,sum(skema_kredit='PKPJ') as PKPJ,sum(skema_kredit='KKB') as KKB,sum(skema_kredit='Talangan Umroh') as Umroh,sum(skema_kredit='Prokesra') as Prokesra,sum(skema_kredit='Kusuma') as Kusuma")
                        ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                        ->whereBetween('tanggal', [$request->get('tanggal_awal'), $request->get('tanggal_akhir')])
                        ->whereNull('pengajuan.deleted_at')
                        // ->where('skema_kredit', $request->get('skema'))
                        ->groupBy('cabang.kode_cabang')
                        ->get();
                    $message = 'Berhasil Menampilkan Total Keseluruhan Skema Data Pengajuan.';
                }

            return response()->json([
                'status' => 'berhasil',
                'message' => $message,
                'data' => $data
            ], 200);
            }

        }

    }

    public function getPosisiPengajuan(Request $request)
    {
        $pilCabang = $request->cabang;
        $skema = $request->skema;
        $tAkhir = $request->tAkhir;
        $tAwal = $request->tAwal;
        $tanggal = $request->tAwal . ' ' . $request->tAkhir;
        $tanggalAwal = date('Y') . '-' . date('m') . '-01';
        $hari_ini = now();

        // tanggal di pilih cabang & skema tidak
        if ($tAwal != null && $tAkhir != null && $pilCabang == null && $skema == null) {
            $seluruh_data = DB::table('pengajuan')
            ->selectRaw("cabang.kode_cabang as kodeC, cabang.cabang,
                 CAST(sum(posisi='pincab') AS UNSIGNED) as pincab,
                 CAST(sum(posisi='PBP') AS UNSIGNED) as pbp,
                 CAST(sum(posisi='PBO') AS UNSIGNED) as pbo,
                 CAST(sum(posisi='Review Penyelia') AS UNSIGNED) as penyelia,
                 CAST(sum(posisi='Proses Input Data') AS UNSIGNED) as staff,
                 CAST(sum(posisi='Selesai') AS UNSIGNED) as disetujui,
                 CAST(sum(posisi='ditolak') AS UNSIGNED) as ditolak")
            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
            ->whereBetween('tanggal', [$tAwal, $tAkhir])
            ->whereNull('pengajuan.deleted_at')
            ->groupBy('cabang.kode_cabang')
            ->get();


            // $seluruh_data = DB::table('cabang AS c')
            // ->select(
            //     'c.kode_cabang AS kodeC',
            //     'c.cabang',
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Pincab' AND deleted_at is null GROUP BY id_cabang), 0) AS pincab"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'PBP' AND deleted_at is null GROUP BY id_cabang), 0) AS pbp"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'PBO' AND deleted_at is null GROUP BY id_cabang), 0) AS pbo"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Review Penyelia' AND deleted_at is null GROUP BY id_cabang), 0) AS penyelia"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Proses Input Data' AND deleted_at is null GROUP BY id_cabang), 0) AS staff"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Selesai' AND deleted_at is null GROUP BY id_cabang), 0) AS disetujui"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Ditolak' AND deleted_at is null GROUP BY id_cabang), 0) AS ditolak"),
            // )
            //     ->leftJoin('pengajuan AS p', 'c.id', 'p.id_cabang')
            //     ->where('c.kode_cabang', '!=', 000)
            //     ->groupBy('kodeC')
            //     ->get();
        }
        // tanggal dipilih cabang juga, skema tidak
        elseif ($tAwal != null && $tAkhir != null && $pilCabang != null && $skema == null) {
            $seluruh_data = DB::table('cabang AS c')
            ->select(
                'c.kode_cabang AS kodeC',
                'c.cabang',
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Pincab' AND deleted_at is null GROUP BY id_cabang), 0) AS pincab"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'PBP' AND deleted_at is null GROUP BY id_cabang), 0) AS pbp"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'PBO' AND deleted_at is null GROUP BY id_cabang), 0) AS pbo"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Review Penyelia' AND deleted_at is null GROUP BY id_cabang), 0) AS penyelia"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Proses Input Data' AND deleted_at is null GROUP BY id_cabang), 0) AS staff"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Selesai' AND deleted_at is null GROUP BY id_cabang), 0) AS disetujui"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Ditolak' AND deleted_at is null GROUP BY id_cabang), 0) AS ditolak"),
            )
                ->leftJoin('pengajuan AS p', 'c.id', 'p.id_cabang')
                ->groupBy('kodeC')
                ->where('c.id', $pilCabang)
                ->get();
        }
        // tanggal dipilih cabang & skema juga
        elseif ($tAwal != null && $tAkhir != null && $pilCabang != null && $skema != null) {
            $seluruh_data = DB::table('cabang AS c')
            ->select(
                'c.kode_cabang AS kodeC',
                'c.cabang',
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Pincab' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS pincab"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'PBP' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS pbp"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'PBO' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS pbo"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Review Penyelia' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS penyelia"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Proses Input Data' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS staff"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Selesai' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS disetujui"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Ditolak' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS ditolak"),
            )
                ->leftJoin('pengajuan AS p', 'c.id', 'p.id_cabang')
                ->groupBy('kodeC')
                ->where('c.id', $pilCabang)
                ->get();
        }
        // tanggal dipilih skema dipilih cabang tidak
        elseif ($tAwal != null && $tAkhir != null && $pilCabang == null && $skema != null) {
            $seluruh_data = DB::table('pengajuan')
            ->selectRaw("cabang.kode_cabang as kodeC, cabang.cabang,
                 CAST(sum(posisi='pincab') AS UNSIGNED) as pincab,
                 CAST(sum(posisi='PBP') AS UNSIGNED) as pbp,
                 CAST(sum(posisi='PBO') AS UNSIGNED) as pbo,
                 CAST(sum(posisi='Review Penyelia') AS UNSIGNED) as penyelia,
                 CAST(sum(posisi='Proses Input Data') AS UNSIGNED) as staff,
                 CAST(sum(posisi='Selesai') AS UNSIGNED) as disetujui,
                 CAST(sum(posisi='ditolak') AS UNSIGNED) as ditolak")
            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
            ->whereBetween('tanggal', [$tAwal, $tAkhir])
            ->whereNull('pengajuan.deleted_at')
            ->where('skema_kredit', $skema)
            ->groupBy('cabang.kode_cabang')
            ->get();
            // $seluruh_data = DB::table('cabang AS c')
            // ->select(
            //     'c.kode_cabang AS kodeC',
            //     'c.cabang',
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Pincab' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS pincab"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'PBP' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS pbp"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'PBO' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS pbo"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Review Penyelia' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS penyelia"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Proses Input Data' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS staff"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Selesai' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS disetujui"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Ditolak' AND skema_kredit = '$skema' AND deleted_at is null GROUP BY id_cabang), 0) AS ditolak"),
            // )
            //     ->leftJoin('pengajuan AS p', 'c.id', 'p.id_cabang')
            //     ->where('c.kode_cabang', '!=', 000)
            //     ->groupBy('kodeC')
            //     ->get();
        }
        // tanggal kosong cabang dipilih
        elseif($tAwal == null && $tAkhir == null && $pilCabang != null) {
            $seluruh_data = DB::table('cabang AS c')
            ->select(
                'c.kode_cabang AS kodeC',
                'c.cabang',
                    DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'Pincab' AND deleted_at is null GROUP BY id_cabang), 0) AS pincab"),
                    DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'PBP' AND deleted_at is null GROUP BY id_cabang), 0) AS pbp"),
                    DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'PBO' AND deleted_at is null GROUP BY id_cabang), 0) AS pbo"),
                    DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'Review Penyelia' AND deleted_at is null GROUP BY id_cabang), 0) AS penyelia"),
                    DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'Proses Input Data' AND deleted_at is null GROUP BY id_cabang), 0) AS staff"),
                    DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Selesai' AND deleted_at is null GROUP BY id_cabang), 0) AS disetujui"),
                    DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tAwal' AND tanggal <= '$tAkhir' AND posisi = 'Ditolak' AND deleted_at is null GROUP BY id_cabang), 0) AS ditolak"),
            )
                ->leftJoin('pengajuan AS p', 'c.id', '=', 'p.id_cabang')
                ->where('c.kode_cabang', '!=', '000')
                ->groupBy('kodeC',)
                ->where('c.id', $pilCabang)
                ->get();
        }
        // tidak milih request
        else {
            $seluruh_data = DB::table('cabang AS c')
                ->select(
                    'c.kode_cabang AS kodeC',
                    'c.cabang',
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'Pincab' AND deleted_at is null GROUP BY id_cabang), 0) AS pincab"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'PBP' AND deleted_at is null GROUP BY id_cabang), 0) AS pbp"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'PBO' AND deleted_at is null GROUP BY id_cabang), 0) AS pbo"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'Review Penyelia' AND deleted_at is null GROUP BY id_cabang), 0) AS penyelia"),
                DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'Proses Input Data' AND deleted_at is null GROUP BY id_cabang), 0) AS staff"),
                )
                ->leftJoin('pengajuan AS p', 'c.id', '=', 'p.id_cabang')
                ->where('c.kode_cabang', '!=', '000')
                ->groupBy('kodeC',)
                ->get();
        }

        return response()->json([
            'status' => 'berhasil',
            'message' => 'berhasil menampilkan data pengajuan.',
            'data' => $seluruh_data
        ]);
    }

    public function getCountPengajuan(Request $request)
    {
        $pilCabang = $request->cabang;
        $tAkhir = $request->tAkhir;
        $tAwal = $request->tAwal;
        $tanggal = $request->tAwal . ' ' . $request->tAkhir;
        $tanggalAwal = date('Y') . '-' . date('m') . '-01';
        $hari_ini = now();
        $skema = $request->skema;

        $total_setuju = 0;
        $total_ditolak = 0;
        $total_proses = 0;

        // tanggal di pilih cabang & skema tidak
        if ( $tAwal != null && $tAkhir != null && $pilCabang == null && $skema == null) {
            $total_disetujui = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->where('posisi', 'Selesai')
                ->whereNull('pengajuan.deleted_at')
                ->count();
            $total_ditolak = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->where('posisi', 'Ditolak')
                ->whereNull('pengajuan.deleted_at')
                ->count();
            $total_diproses = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->whereIn('posisi', ['Pincab','PBP','PBO','Review Penyelia','Proses Input Data'])
                ->whereNull('pengajuan.deleted_at')
                ->count();
            $data = DB::table('pengajuan')
                ->selectRaw("cabang.kode_cabang as kodeC,cabang.cabang,sum(posisi='Selesai') as total_disetujui,sum(posisi='Ditolak') as total_ditolak,sum(posisi IN ('Pincab','PBP','PBO','Review Penyelia','Proses Input Data')) as total_diproses")
                ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                ->whereBetween('tanggal', [$tAwal, $tAkhir])
                ->whereNull('pengajuan.deleted_at')
                ->groupBy('cabang.kode_cabang')
                ->get();

            return response()->json([
                'status' => 'berhasil',
                'message' => 'Berhasil Menampilkan Total Data Pengajuan.',
                'total_disetujui' => $total_disetujui,
                'total_ditolak' => $total_ditolak,
                'total_diproses' => $total_diproses,
                'data' => $data,
            ], 200);
        }
        // tanggal dipilih cabang & skema juga
        elseif ($tAwal != null && $tAkhir != null && $pilCabang != null && $skema != null) {
            $cabang = DB::table('cabang')->select('id', 'kode_cabang')->where('kode_cabang', $pilCabang)->first();
            $total_disetujui = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->where('posisi', 'Selesai')
                ->whereNull('pengajuan.deleted_at')
                ->where('id_cabang', $cabang->id)
                ->where('skema_kredit', $skema)
                ->count();
            $total_ditolak = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->where('posisi', 'Ditolak')
                ->whereNull('pengajuan.deleted_at')
                ->where('id_cabang', $cabang->id)
                ->where('skema_kredit', $skema)
                ->count();
            $total_diproses = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->whereIn('posisi', ['Pincab','PBP','PBO','Review Penyelia','Proses Input Data'])
                ->whereNull('pengajuan.deleted_at')
                ->where('id_cabang', $cabang->id)
                ->where('skema_kredit', $skema)
                ->count();
            $data = DB::table('pengajuan')
                ->selectRaw("cabang.kode_cabang as kodeC,cabang.cabang,sum(posisi='Selesai') as total_disetujui,sum(posisi='Ditolak') as total_ditolak,sum(posisi IN ('Pincab','PBP','PBO','Review Penyelia','Proses Input Data')) as total_diproses")
                ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                ->whereBetween('tanggal', [$tAwal, $tAkhir])
                ->whereNull('pengajuan.deleted_at')
                ->where('cabang.kode_cabang', $pilCabang)
                ->where('pengajuan.skema_kredit', $skema)
                ->groupBy('cabang.kode_cabang')
                ->get();

            return response()->json([
                'status' => 'berhasil',
                'message' => 'Berhasil Menampilkan Total Data Pengajuan Cabang '. $pilCabang .' dan skema '.$skema.'.',
                'total_disetujui' => $total_disetujui,
                'total_ditolak' => $total_ditolak,
                'total_diproses' => $total_diproses,
                'data' => $data,
            ], 200);
        }
        // tanggal dipilih skema juga cabang tdak
        elseif ($tAwal != null && $tAkhir != null && $pilCabang == null && $skema != null) {
            $total_disetujui = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->where('posisi', 'Selesai')
                ->whereNull('pengajuan.deleted_at')
                ->where('skema_kredit', $skema)
                ->count();
            $total_ditolak = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->where('posisi', 'Ditolak')
                ->whereNull('pengajuan.deleted_at')
                ->where('skema_kredit', $skema)
                ->count();
            $total_diproses = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->whereIn('posisi', ['Pincab','PBP','PBO','Review Penyelia','Proses Input Data'])
                ->whereNull('pengajuan.deleted_at')
                ->where('skema_kredit', $skema)
                ->count();
            $data = DB::table('pengajuan')
                ->selectRaw("cabang.kode_cabang as kodeC,cabang.cabang,sum(posisi='Selesai') as total_disetujui,sum(posisi='Ditolak') as total_ditolak,sum(posisi IN ('Pincab','PBP','PBO','Review Penyelia','Proses Input Data')) as total_diproses")
                ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                ->whereBetween('tanggal', [$tAwal, $tAkhir])
                ->whereNull('pengajuan.deleted_at')
                ->where('pengajuan.skema_kredit', $skema)
                ->groupBy('cabang.kode_cabang')
                ->get();

            return response()->json([
                'status' => 'berhasil',
                'message' => 'Berhasil Menampilkan Total Data Pengajuan skema '.$skema.'.',
                'total_disetujui' => $total_disetujui,
                'total_ditolak' => $total_ditolak,
                'total_diproses' => $total_diproses,
                'data' => $data,
            ], 200);
        }
        // tanggal dipilih cabang juga skema tidak
        elseif ($tAwal != null && $tAkhir != null && $pilCabang != null && $skema == null) {
            $cabang = DB::table('cabang')->select('id', 'kode_cabang')->where('kode_cabang', $pilCabang)->first();
            $total_disetujui = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->where('posisi', 'Selesai')
                ->whereNull('pengajuan.deleted_at')
                ->where('id_cabang', $cabang->id)
                ->count();
            $total_ditolak = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->where('posisi', 'Ditolak')
                ->whereNull('pengajuan.deleted_at')
                ->where('id_cabang', $cabang->id)
                ->count();
            $total_diproses = DB::table('pengajuan')
                ->whereBetween('tanggal', [$tAwal, $tAkhir != null ? $tAkhir : now()])
                ->whereIn('posisi', ['Pincab','PBP','PBO','Review Penyelia','Proses Input Data'])
                ->whereNull('pengajuan.deleted_at')
                ->where('id_cabang', $cabang->id)
                ->count();
            $data = DB::table('pengajuan')
                ->selectRaw("cabang.kode_cabang as kodeC,cabang.cabang,sum(posisi='Selesai') as total_disetujui,sum(posisi='Ditolak') as total_ditolak,sum(posisi IN ('Pincab','PBP','PBO','Review Penyelia','Proses Input Data')) as total_diproses")
                ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                ->whereBetween('tanggal', [$tAwal, $tAkhir])
                ->whereNull('pengajuan.deleted_at')
                ->where('cabang.kode_cabang', $pilCabang)
                ->groupBy('cabang.kode_cabang')
                ->get();

            return response()->json([
                'status' => 'berhasil',
                'message' => 'Berhasil Menampilkan Total Data Pengajuan Cabang '. $pilCabang .'.',
                'total_disetujui' => $total_disetujui,
                'total_ditolak' => $total_ditolak,
                'total_diproses' => $total_diproses,
                'data' => $data,
            ], 200);
        }
        // tanggal kosong cabang dipilih
        elseif ($tAwal == null && $tAkhir == null && $pilCabang != null) {
            $cabang = DB::table('cabang')->select('id', 'kode_cabang')->where('kode_cabang', $pilCabang)->first();
            $total_disetujui = DB::table('pengajuan')
                ->where('posisi', 'Selesai')
                ->whereNull('pengajuan.deleted_at')
                ->where('id_cabang', $cabang->id)
                ->count();
            $total_ditolak = DB::table('pengajuan')
                ->where('posisi', 'Ditolak')
                ->whereNull('pengajuan.deleted_at')
                ->where('id_cabang', $cabang->id)
                ->count();
            $total_diproses = DB::table('pengajuan')
                ->whereIn('posisi', ['Pincab','PBP','PBO','Review Penyelia','Proses Input Data'])
                ->whereNull('pengajuan.deleted_at')
                ->where('id_cabang', $cabang->id)
                ->count();
            $data = DB::table('pengajuan')
                ->selectRaw("cabang.kode_cabang as kodeC,cabang.cabang,sum(posisi='Selesai') as total_disetujui,sum(posisi='Ditolak') as total_ditolak,sum(posisi IN ('Pincab','PBP','PBO','Review Penyelia','Proses Input Data')) as total_diproses")
                ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
                ->whereBetween('tanggal', [$tanggalAwal, $hari_ini])
                ->whereNull('pengajuan.deleted_at')
                ->where('cabang.kode_cabang', $pilCabang)
                ->groupBy('cabang.kode_cabang')
                ->get();

            return response()->json([
                'status' => 'berhasil',
                'message' => 'Berhasil Menampilkan Total Data Pengajuan Cabang '. $pilCabang .'.',
                'total_disetujui' => $total_disetujui,
                'total_ditolak' => $total_ditolak,
                'total_diproses' => $total_diproses,
                'data' => $data,
            ], 200);
        }
        else {
            // $seluruh_data_proses = DB::table('cabang AS c')
            // ->select(
            //     'c.kode_cabang AS kodeC',
            //     'c.cabang',
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'Selesai' GROUP BY id_cabang), 0) AS disetujui"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi = 'Ditolak' GROUP BY id_cabang), 0) AS ditolak"),
            //     DB::raw("IFNULL((SELECT count(id) FROM pengajuan WHERE id_cabang = c.id AND tanggal >= '$tanggalAwal' AND tanggal <= '$hari_ini' AND posisi != 'Ditolak' AND posisi != 'Selesai' GROUP BY id_cabang), 0) AS diproses")
            // )
            //     ->leftJoin('pengajuan AS p', 'c.id', '=', 'p.id_cabang')
            //     ->where('c.kode_cabang', '!=', '000')
            //     ->groupBy('kodeC',)
            //     ->get();
        }
    }

    function getCountYearPengajuan(Request $request) {
        $tAwal = now()->subYear();
        $tAkhir = now();

        if (request()->has('tAwal')) {
            $tAwal = Carbon::parse(request('tAwal'))->startOfYear();
        }

        if (request()->has('tAkhir')) {
            $tAkhir = Carbon::parse(request('tAkhir'))->endOfYear();
        }

        $total_disetujui_perbulan = DB::table('pengajuan')
            ->select(DB::raw('MONTH(tanggal) as bulan'), DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal', [$tAwal, $tAkhir])
            ->where('posisi', 'Selesai')
            ->whereNull('pengajuan.deleted_at')
            ->groupBy(DB::raw('MONTH(tanggal)'))
            ->get();

        $total_ditolak_perbulan = DB::table('pengajuan')
            ->select(DB::raw('MONTH(tanggal) as bulan'), DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal', [$tAwal, $tAkhir])
            ->where('posisi', 'Ditolak')
            ->whereNull('pengajuan.deleted_at')
            ->groupBy(DB::raw('MONTH(tanggal)'))
            ->get();

        $total_diproses_perbulan = DB::table('pengajuan')
            ->select(DB::raw('MONTH(tanggal) as bulan'), DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal', [$tAwal, $tAkhir])
            ->whereIn('posisi', ['Pincab','PBP','PBO','Review Penyelia','Proses Input Data'])
            ->whereNull('pengajuan.deleted_at')
            ->groupBy(DB::raw('MONTH(tanggal)'))
            ->get();

        $dataDisetujui = [
            'January' => 0,
            'February' => 0,
            'March' => 0,
            'April' => 0,
            'May' => 0,
            'June' => 0,
            'July' => 0,
            'August' => 0,
            'September' => 0,
            'October' => 0,
            'November' => 0,
            'December' => 0,
        ];
        $dataDitolak = [
            'January' => 0,
            'February' => 0,
            'March' => 0,
            'April' => 0,
            'May' => 0,
            'June' => 0,
            'July' => 0,
            'August' => 0,
            'September' => 0,
            'October' => 0,
            'November' => 0,
            'December' => 0,
        ];
        $dataDiproses = [
            'January' => 0,
            'February' => 0,
            'March' => 0,
            'April' => 0,
            'May' => 0,
            'June' => 0,
            'July' => 0,
            'August' => 0,
            'September' => 0,
            'October' => 0,
            'November' => 0,
            'December' => 0,
        ];
        $dataKeseluruhan = [
            'January' => 0,
            'February' => 0,
            'March' => 0,
            'April' => 0,
            'May' => 0,
            'June' => 0,
            'July' => 0,
            'August' => 0,
            'September' => 0,
            'October' => 0,
            'November' => 0,
            'December' => 0,
        ];

        foreach ($total_disetujui_perbulan as $item) {
            $dataDisetujui[date('F', mktime(0, 0, 0, $item->bulan, 1))] = $item->total;
        }
        foreach ($total_ditolak_perbulan as $item) {
            $dataDitolak[date('F', mktime(0, 0, 0, $item->bulan, 1))] = $item->total;
        }
        foreach ($total_diproses_perbulan as $item) {
            $dataDiproses[date('F', mktime(0, 0, 0, $item->bulan, 1))] = $item->total;
        }
        foreach ($dataKeseluruhan as $key=> $item) {
            $disetujui = $dataDisetujui[$key];
            $ditolak = $dataDitolak[$key];
            $diproses = $dataDiproses[$key];
            $dataKeseluruhan[$key] = intval($disetujui + $ditolak + $diproses);
        }

        return response()->json([
            'status'=>"Berhasil",
            'message'=>"Berhasil menampilkan data pengajuan dalam 1 tahun",
            "data"=> [
                'data_disetujui' => $dataDisetujui,
                'data_ditolak' => $dataDitolak,
                'data_diproses' => $dataDiproses,
                'data_keseluruhan' => $dataKeseluruhan,
            ]
        ],200);

    }

    public function getListPengajuan($user_id){
        $page_length = Request()->page_length ? Request()->page_length : 5;
        $user = User::select('id', 'role')->find($user_id);

        $data = DB::table('pengajuan')
            ->where('skema_kredit', '!=', 'KKB')
            ->where('posisi', 'Selesai')
            ->whereNotNull('pk')
            ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
            ->join('log_cetak AS log', 'log.id_pengajuan', 'pengajuan.id')
            ->select(
                'pengajuan.id',
                'pengajuan.id_staf',
                'pengajuan.id_penyelia',
                'u.nip AS nip_staf',
                'u2.nip AS nip_penyelia',
                'pengajuan.tanggal as tanggal_pengajuan', 'calon_nasabah.nama', 'calon_nasabah.tanggal_lahir', 'calon_nasabah.alamat_rumah', 'calon_nasabah.no_ktp', 'calon_nasabah.jumlah_kredit', 'calon_nasabah.tenor_yang_diminta', 'pengajuan.sppk', 'pengajuan.po', 'pengajuan.pk', 'log.tgl_cetak_pk', 'log.no_pk', 'pengajuan.tanggal', 'cabang.kode_cabang', 'cabang.cabang', 'cabang.alamat AS alamat_cabang', 'pengajuan.skema_kredit',
            )
            ->leftJoin('users AS u', 'u.id', 'pengajuan.id_staf')
            ->leftJoin('users AS u2', 'u2.id', 'pengajuan.id_penyelia');
        if ($user_id != 0) {
            if ($user->role == 'Staf Analis Kredit') {
                $data->where('id_staf', $user_id);
            }
            if ($user->role == 'Penyelia Kredit') {
                $data->where('id_penyelia', $user_id);
            }
            if ($user->role == 'PBO') {
                $data->where('id_pbo', $user_id);
            }
            if ($user->role == 'PBP') {
                $data->where('id_pbp', $user_id);
            }
            if ($user->role == 'Pincab') {
                $data->where('id_pincab', $user_id);
            }
        }

        if (Request()->has('str')) {
            $searhQuery = Request()->str;
            $data->where('calon_nasabah.nama', 'LIKE', "%$searhQuery%");
        }
        if (Request()->has('tAwal')) {
            $tAwal = Request()->tAwal;
            $tAkhir = Request()->tAkhir;
            $hari_ini = now();

            if ($tAkhir != null) {
                $data->whereBetween('pengajuan.tanggal', [$tAwal, $tAkhir]);
            } else {
                $data->whereBetween('pengajuan.tanggal', [$tAwal, $hari_ini]);
            }
        }
        $data = $data->paginate($page_length);
        foreach ($data as $key => $value) {
            $value->staf = $this->getKaryawan($value->nip_staf);
            $value->karyawan = $this->getKaryawan($value->nip_penyelia);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data,
        ]);
    }

    public function getListPengajuanByCabang(Request $request){
        $page_length = Request()->page_length ? Request()->page_length : 5;
        $kode_cabang = $request->kode_cabang;
        $user_id = $request->user;

        $data = DB::table('pengajuan')
            ->where('skema_kredit', '!=', 'KKB')
            ->where('posisi', 'Selesai')
            ->whereNotNull('pk')
            ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
            ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
            ->join('log_cetak AS log', 'log.id_pengajuan', 'pengajuan.id')
            ->select(
                'pengajuan.id',
                'pengajuan.id_penyelia',
                'u.nip AS nip_staf',
                'u2.nip AS nip_penyelia',
                'pengajuan.tanggal as tanggal_pengajuan', 'calon_nasabah.nama', 'calon_nasabah.tanggal_lahir', 'calon_nasabah.alamat_rumah', 'calon_nasabah.no_ktp', 'calon_nasabah.jumlah_kredit', 'calon_nasabah.tenor_yang_diminta', 'pengajuan.sppk', 'pengajuan.po', 'pengajuan.pk', 'log.tgl_cetak_pk', 'log.no_pk', 'pengajuan.tanggal', 'cabang.kode_cabang', 'cabang.cabang', 'cabang.alamat AS alamat_cabang', 'pengajuan.skema_kredit',
            )
            ->leftJoin('users AS u', 'u.id', 'pengajuan.id_staf')
            ->leftJoin('users AS u2', 'u2.id', 'pengajuan.id_penyelia');

        if ($kode_cabang != 'all') {
            $data->where('cabang.kode_cabang', $kode_cabang);
        }

        if ($user_id != 'all') {
            $user = User::select('id', 'role')->find($user_id);
            if ($user->role == 'Staf Analis Kredit') {
                $data->where('id_staf', $user_id);
            }
            if ($user->role == 'Penyelia Kredit') {
                $data->where('id_penyelia', $user_id);
            }
            if ($user->role == 'PBO') {
                $data->where('id_pbo', $user_id);
            }
            if ($user->role == 'PBP') {
                $data->where('id_pbp', $user_id);
            }
            if ($user->role == 'Pincab') {
                $data->where('id_pincab', $user_id);
            }
        }

        if (Request()->has('str')) {
            $searhQuery = Request()->str;
            $data->where('calon_nasabah.nama', 'LIKE', "%$searhQuery%");
        }
        if (Request()->has('tAwal')) {
            $tAwal = Request()->tAwal;
            $tAkhir = Request()->tAkhir;
            $hari_ini = now();

            if ($tAkhir != null) {
                $data->whereBetween('pengajuan.tanggal', [$tAwal, $tAkhir]);
            } else {
                $data->whereBetween('pengajuan.tanggal', [$tAwal, $hari_ini]);
            }
        }
        $data = $data->paginate($page_length);
        foreach ($data as $key => $value) {
            $value->staf = $this->getKaryawan($value->nip_staf);
            $value->karyawan = $this->getKaryawan($value->nip_penyelia);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data,
        ]);
    }

    public function getListPengajuanById($id){

        $data = DB::table('pengajuan')
        ->where('skema_kredit', '!=', 'KKB')
        ->where('posisi', 'Selesai')
        ->where('pengajuan.id', $id)
        ->whereNotNull('pk')
        ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
        ->join('cabang', 'cabang.id', 'pengajuan.id_cabang')
        ->join('log_cetak AS log', 'log.id_pengajuan', 'pengajuan.id')
        ->leftJoin('users AS u', 'u.id', 'pengajuan.id_staf')
        ->leftJoin('users AS u2', 'u2.id', 'pengajuan.id_penyelia')
        ->select('pengajuan.id',
        'calon_nasabah.nama',
        'pengajuan.id_penyelia',
        'u.nip AS nip_staf',
        'u2.nip AS nip_penyelia',
        'calon_nasabah.tanggal_lahir',
        'calon_nasabah.alamat_rumah', 'calon_nasabah.no_ktp', 'calon_nasabah.jumlah_kredit', 'calon_nasabah.tenor_yang_diminta', 'pengajuan.sppk', 'pengajuan.po', 'pengajuan.pk', 'log.tgl_cetak_pk', 'log.no_pk', 'pengajuan.tanggal','cabang.kode_cabang', 'cabang.cabang', 'cabang.alamat AS alamat_cabang', 'pengajuan.skema_kredit');

        $data = $data->first();
        $data->staf = $this->getKaryawan($data->nip_penyelia);
        $data->penyelia = $this->getKaryawan($data->nip_penyelia);

        if ($data) {
            return response()->json([
                'status' => 'success',
                'message' => 'success',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Data not found'
            ]);
        }
    }

    public function rangkingCabang(Request $request) {
        $tAwal = $request->tanggal_awal ? date('Y-m-d', strtotime($request->tanggal_awal)) : null;
        $tAkhir = $request->tanggal_akhir ? date('Y-m-d', strtotime($request->tanggal_akhir)) : null;
        $total_cabang = DB::table('cabang')->where('kode_cabang', '!=', '000')->count();

        $dataTertinggi = DB::table('cabang')
            ->leftJoin('pengajuan', function ($join) use ($request) {
                $join->on('cabang.id', '=', 'pengajuan.id_cabang')
                    ->whereBetween('pengajuan.tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
            })
            ->selectRaw('IFNULL(COUNT(pengajuan.id), 0) AS total, cabang.kode_cabang, cabang.cabang')
            ->where('cabang.kode_cabang', '!=', '000')
            ->groupBy('cabang.kode_cabang', 'cabang.cabang')
            ->orderByRaw('total DESC, cabang.kode_cabang ASC')
            ->limit(5)
            ->get();

        $dataTerendah = DB::table('cabang')
            ->leftJoin('pengajuan', function ($join) use ($request) {
                $join->on('cabang.id', '=', 'pengajuan.id_cabang')
                    ->whereBetween('pengajuan.tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
            })
            ->selectRaw('IFNULL(COUNT(pengajuan.id), 0) AS total, cabang.kode_cabang, cabang.cabang')
            ->where('cabang.kode_cabang', '!=', '000')
            // ->where('pengajuan.posisi', 'Selesai')
            ->whereNull('pengajuan.deleted_at')
            ->groupBy('cabang.kode_cabang', 'cabang.cabang')
            ->orderByRaw('total ASC, cabang.kode_cabang ASC') // Ubah ke ASC untuk mengambil data terendah
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'berhasil',
            'message' => 'Berhasil mengambil data',
            'total_cabang' => $total_cabang,
            'tertinggi' => $dataTertinggi,
            'terendah' => $dataTerendah
        ], 200);
    }
}
