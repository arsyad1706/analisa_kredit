<?php

namespace App\Http\Controllers;

use App\Events\EventMonitoring;
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
use App\Models\JawabanModel;
use App\Models\JawabanTemp;
use App\Models\JawabanTempModel;
use App\Models\LogPengajuan;
use App\Models\MerkModel;
use App\Models\MstItemPerhitunganKredit;
use App\Models\MstProdukKredit;
use App\Models\MstSkemaKredit;
use App\Models\MstSkemaLimit;
use App\Models\PerhitunganKredit;
use App\Models\PeriodeAspekKeuangan;
use App\Models\PlafonUsulan;
use App\Models\TipeModel;
use App\Models\User;
use App\Repository\PengajuanDagulirRepository;
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
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Validation\Validator;
use PhpParser\Node\Expr;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class PengajuanKreditController extends Controller
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

    public function fixScore()
    {
        $pengajuan = DB::table('pengajuan')->where('tanggal', '>', '2023-07-20')->get();
        foreach ($pengajuan as $key => $value) {
            $jawaban = DB::table('jawaban')->where('id_pengajuan', $value->id)->get();
            $sum = 0;
            $sumPenyelia = 0;
            $sumPBO = 0;
            $sumPBP = 0;
            $count = 0;
            $countPenyelia = 0;
            $countPBO = 0;
            $countPBP = 0;
            $avg = 0;
            $avgPenyelia = 0;
            $avgPBO = 0;
            $avgPBP = 0;
            // return $jawaban;
            foreach ($jawaban as $key2 => $valJ) {
                if ($value->posisi == 'Proses Input Data') {
                    if ($valJ->skor) {
                        $sum += $valJ->skor;
                        $count += 1;
                    }
                }
                if ($value->posisi == 'Review Penyelia') {
                    if ($valJ->skor_penyelia) {
                        $sumPenyelia += $valJ->skor_penyelia;
                        $countPenyelia += 1;
                    }
                }
                if ($value->posisi == 'PBO') {
                    if ($valJ->skor_pbo) {
                        $sumPBO += $valJ->skor_pbo;
                        $countPBO += 1;
                    }
                }
                if ($value->posisi == 'PBP') {
                    if ($valJ->skor_pbp) {
                        $sumPBP += $valJ->skor_pbp;
                        $countPBP += 1;
                    }
                }
            }
            // $avg = $countPenyelia;
            $avg = round(($sum / $count), 2);
            $avgPenyelia = round(($sumPenyelia / $countPenyelia), 2);
            $avgPBO = round(($sumPBO / $countPBO), 2);
            $avgPBP = round(($sumPBP / $countPBP), 2);

            $avgs = [
                'avg_sistem' => $avg,
                'avg_penyelia' => $avgPenyelia,
                'avg_pbo' => $avgPBO,
                'avg_pbp' => $avgPBP,
            ];
            $value->average = $avgs;
        }
        return $pengajuan;
        // return compact('pengajuan', 'jawaban');
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
            $response = Http::withOptions(['verify' => false])->get($apiURL, [
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Session::put('is_dagulir', false);
        $param['pageTitle'] = "Dashboard";
        $id_cabang = Auth::user()->id_cabang;
        $param['cabang'] = DB::table('cabang')
            ->get();
        $role = auth()->user()->role;
        $limit = $request->has('page_length') ? $request->get('page_length') : 10;
        $page = $request->has('page') ? $request->get('page') : 1;
        if ($role == 'Staf Analis Kredit') {
            $id_staf = auth()->user()->id;
            $param['pageTitle'] = 'Tambah Pengajuan Kredit';
            $param['btnText'] = 'Tambah Pengajuan';
            $param['btnLink'] = route('pengajuan-kredit.create');
            $param['data_pengajuan'] = PengajuanModel::select(
                'pengajuan.id_staf',
                'pengajuan.id',
                'pengajuan.tanggal',
                'pengajuan.posisi',
                'pengajuan.progress_pengajuan_data',
                'pengajuan.id_staf',
                'pengajuan.id_penyelia',
                'pengajuan.id_pbo',
                'pengajuan.id_pbp',
                'pengajuan.id_pincab',
                'pengajuan.tanggal_review_penyelia',
                'pengajuan.tanggal_review_pbp',
                'pengajuan.tanggal_review_pincab',
                'pengajuan.status',
                'pengajuan.status_by_sistem',
                'pengajuan.id_cabang',
                'pengajuan.average_by_sistem',
                'pengajuan.average_by_penyelia',
                'pengajuan.average_by_pbo',
                'pengajuan.average_by_pbp',
                'pengajuan.sppk',
                'pengajuan.po',
                'pengajuan.pk',
                'calon_nasabah.nama',
                'calon_nasabah.id_user',
                'calon_nasabah.jenis_usaha',
                'calon_nasabah.id_pengajuan',
                'pengajuan.created_at',
            )->orderBy('created_at', 'desc')
                ->when($request->search, function ($query, $search) {
                    return $query->where('calon_nasabah.nama', 'like', '%' . $search . '%');
                })
                ->when($request->pss, function ($query, $pss) {
                    return $query->where('pengajuan.posisi', $pss);
                })
                ->when($request->cbg, function ($query, $cbg) {
                    return $query->where('id_cabang', $cbg);
                })
                ->when($request->tAwal && $request->tAkhir, function ($query) use ($request) {
                    return $query->whereBetween('pengajuan.tanggal', [$request->tAwal, $request->tAkhir]);
                })
                ->when($request->lanjuti, function ($query, $lanjuti) {
                    return $query->where('posisi', '!=', 'Proses Input Data');
                })
                ->when($request->sts, function ($query, $sts) {
                    if ($sts == 'Selesai' || $sts == 'Ditolak') {
                        return $query->where('pengajuan.posisi', $sts);
                    } else {
                        return $query->where('pengajuan.posisi', '<>', 'Selesai')
                            ->where('pengajuan.posisi', '<>', 'Ditolak');
                    }
                })
                ->when($request->score, function ($query, $score) {
                    return $query->whereRaw('FLOOR(pengajuan.average_by_sistem) = ?', $score)
                        ->orWhereRaw('FLOOR(pengajuan.average_by_penyelia) = ?', $score);
                })
                ->where('pengajuan.id_staf', $id_staf)
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->where('pengajuan.id_cabang', $id_cabang)
                ->paginate($limit)
                ->withQueryString();
            // return $param['data_pengajuan'];
            return view('pengajuan-kredit.list-edit-pengajuan-kredit', $param);
        } elseif ($role == 'Penyelia Kredit') {
            // $param['dataAspek'] = ItemModel::select('*')->where('level',1)->get();
            $id_penyelia = auth()->user()->id;
            $id_cabang = Auth::user()->id_cabang;
            $param['data_pengajuan'] = PengajuanModel::select(
                'pengajuan.id_penyelia',
                'pengajuan.id',
                'pengajuan.tanggal',
                'pengajuan.posisi',
                'pengajuan.progress_pengajuan_data',
                'pengajuan.id_staf',
                'pengajuan.id_penyelia',
                'pengajuan.id_pbo',
                'pengajuan.id_pbp',
                'pengajuan.id_pincab',
                'pengajuan.tanggal_review_penyelia',
                'pengajuan.tanggal_review_pbp',
                'pengajuan.tanggal_review_pincab',
                'pengajuan.status',
                'pengajuan.status_by_sistem',
                'pengajuan.id_cabang',
                'pengajuan.average_by_sistem',
                'pengajuan.average_by_penyelia',
                'pengajuan.average_by_pbo',
                'pengajuan.average_by_pbp',
                'calon_nasabah.nama',
                'calon_nasabah.jenis_usaha',
                'calon_nasabah.id_pengajuan',
                'pengajuan.created_at',
            )->orderBy('created_at', 'desc')
                ->where('pengajuan.id_penyelia', $id_penyelia)
                ->when($request->search, function ($query, $search) {
                    return $query->where('calon_nasabah.nama', 'like', '%' . $search . '%');
                })
                ->when($request->pss, function ($query, $pss) {
                    return $query->where('pengajuan.posisi', $pss);
                })
                ->when($request->cbg, function ($query, $cbg) {
                    return $query->where('id_cabang', $cbg);
                })
                ->when($request->tAwal && $request->tAkhir, function ($query) use ($request) {
                    return $query->whereBetween('pengajuan.tanggal', [$request->tAwal, $request->tAkhir]);
                })
                ->when($request->sts, function ($query, $sts) {
                    if ($sts == 'Selesai' || $sts == 'Ditolak') {
                        return $query->where('pengajuan.posisi', $sts);
                    } else {
                        return $query->where('pengajuan.posisi', '<>', 'Selesai')
                            ->where('pengajuan.posisi', '<>', 'Ditolak');
                    }
                })
                ->when($request->score, function ($query, $score) {
                    return $query->whereRaw('FLOOR(pengajuan.average_by_sistem) = ?', $score)
                        ->orWhereRaw('FLOOR(pengajuan.average_by_penyelia) = ?', $score);
                })
                ->when($request->lanjuti, function ($query, $lanjuti) {
                    return $query->where('posisi', '!=', 'Review Penyelia');
                })
                ->where('pengajuan.id_penyelia', $id_penyelia)
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->where('pengajuan.id_cabang', $id_cabang)
                ->paginate(5)
                ->withQueryString();
            return view('pengajuan-kredit.list-pengajuan-kredit', $param);
        } elseif ($role == 'PBO' || $role == 'PBP') {
            $id_data = auth()->user()->id;
            $id_cabang = Auth::user()->id_cabang;
            $param['data_pengajuan'] = PengajuanModel::select(
                'pengajuan.id',
                'pengajuan.id_pbo',
                'pengajuan.id_pbp',
                'pengajuan.tanggal',
                'pengajuan.posisi',
                'pengajuan.progress_pengajuan_data',
                'pengajuan.id_staf',
                'pengajuan.id_penyelia',
                'pengajuan.id_pbo',
                'pengajuan.id_pbp',
                'pengajuan.id_pincab',
                'pengajuan.tanggal_review_penyelia',
                'pengajuan.tanggal_review_pbp',
                'pengajuan.tanggal_review_pincab',
                'pengajuan.status',
                'pengajuan.status_by_sistem',
                'pengajuan.id_cabang',
                'pengajuan.average_by_sistem',
                'pengajuan.average_by_penyelia',
                'pengajuan.average_by_pbo',
                'pengajuan.average_by_pbp',
                'calon_nasabah.nama',
                'calon_nasabah.jenis_usaha',
                'calon_nasabah.id_pengajuan',
                'pengajuan.created_at',
            )->orderBy('created_at', 'desc')
                ->where('pengajuan.id_pbo', $id_data)
                ->orWhere('pengajuan.id_pbp', $id_data)
                ->when($request->search, function ($query, $search) {
                    return $query->where('calon_nasabah.nama', 'like', '%' . $search . '%');
                })
                ->when($request->pss, function ($query, $pss) {
                    return $query->where('pengajuan.posisi', $pss);
                })
                ->when($request->cbg, function ($query, $cbg) {
                    return $query->where('id_cabang', $cbg);
                })
                ->when($request->tAwal && $request->tAkhir, function ($query) use ($request) {
                    return $query->whereBetween('pengajuan.tanggal', [$request->tAwal, $request->tAkhir]);
                })
                ->when($request->sts, function ($query, $sts) {
                    if ($sts == 'Selesai' || $sts == 'Ditolak') {
                        return $query->where('pengajuan.posisi', $sts);
                    } else {
                        return $query->where('pengajuan.posisi', '<>', 'Selesai')
                            ->where('pengajuan.posisi', '<>', 'Ditolak');
                    }
                })
                ->when($request->score, function ($query, $score) {
                    return $query->whereRaw('FLOOR(pengajuan.average_by_sistem) = ?', $score)
                        ->orWhereRaw('FLOOR(pengajuan.average_by_penyelia) = ?', $score);
                })
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->where('pengajuan.id_cabang', $id_cabang)
                ->paginate(5)
                ->withQueryString();
            return view('pengajuan-kredit.list-pbp', $param);
        } elseif ($role == 'Pincab') {
            $id_data = auth()->user()->id;
            if ($request->pss) {
                $param['data_pengajuan'] = PengajuanModel::select(
                    'pengajuan.id',
                    'pengajuan.id_pincab',
                    'pengajuan.tanggal',
                    'pengajuan.posisi',
                    'pengajuan.progress_pengajuan_data',
                    'pengajuan.id_staf',
                    'pengajuan.id_penyelia',
                    'pengajuan.id_pbo',
                    'pengajuan.id_pbp',
                    'pengajuan.id_pincab',
                    'pengajuan.tanggal_review_penyelia',
                    'pengajuan.tanggal_review_pbp',
                    'pengajuan.tanggal_review_pincab',
                    'pengajuan.status',
                    'pengajuan.status_by_sistem',
                    'pengajuan.id_cabang',
                    'pengajuan.average_by_sistem',
                    'pengajuan.average_by_penyelia',
                    'pengajuan.average_by_pbo',
                    'pengajuan.average_by_pbp',
                    'calon_nasabah.nama',
                    'calon_nasabah.jenis_usaha',
                    'calon_nasabah.id_pengajuan'
                )->orderBy('tanggal', 'desc')
                    ->when($request->search, function ($query, $search) {
                        return $query->where('calon_nasabah.nama', 'like', '%' . $search . '%');
                    })
                    ->when($request->pss, function ($query, $pss) {
                        return $query->where('pengajuan.posisi', $pss);
                    })
                    ->when($request->cbg, function ($query, $cbg) {
                        return $query->where('id_cabang', $cbg);
                    })
                    ->when($request->tAwal && $request->tAkhir, function ($query) use ($request) {
                        return $query->whereBetween('pengajuan.tanggal', [$request->tAwal, $request->tAkhir]);
                    })
                    ->when($request->sts, function ($query, $sts) {
                        if ($sts == 'Selesai' || $sts == 'Ditolak') {
                            return $query->where('pengajuan.posisi', $sts);
                        } else {
                            return $query->where('pengajuan.posisi', '<>', 'Selesai')
                                ->where('pengajuan.posisi', '<>', 'Ditolak');
                        }
                    })
                    ->when($request->score, function ($query, $score) {
                        return $query->whereRaw('FLOOR(pengajuan.average_by_sistem) = ?', $score)
                            ->orWhereRaw('FLOOR(pengajuan.average_by_penyelia) = ?', $score);
                    })
                    ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                    ->where('pengajuan.id_cabang', Auth::user()->id_cabang)
                    ->where('pengajuan.posisi', $request->pss)
                    ->paginate(5)
                    ->withQueryString();
            } else {
                $param['data_pengajuan'] = PengajuanModel::select(
                    'pengajuan.id',
                    'pengajuan.id_pincab',
                    'pengajuan.tanggal',
                    'pengajuan.posisi',
                    'pengajuan.progress_pengajuan_data',
                    'pengajuan.id_staf',
                    'pengajuan.id_penyelia',
                    'pengajuan.id_pbo',
                    'pengajuan.id_pbp',
                    'pengajuan.id_pincab',
                    'pengajuan.tanggal_review_penyelia',
                    'pengajuan.tanggal_review_pbp',
                    'pengajuan.tanggal_review_pincab',
                    'pengajuan.status',
                    'pengajuan.status_by_sistem',
                    'pengajuan.id_cabang',
                    'pengajuan.average_by_sistem',
                    'pengajuan.average_by_penyelia',
                    'pengajuan.average_by_pbo',
                    'pengajuan.average_by_pbp',
                    'calon_nasabah.nama',
                    'calon_nasabah.jenis_usaha',
                    'calon_nasabah.id_pengajuan'
                )->orderBy('tanggal', 'desc')
                    ->where('pengajuan.id_cabang', auth()->user()->id_cabang)
                    ->when($request->search, function ($query, $search) {
                        return $query->where('calon_nasabah.nama', 'like', '%' . $search . '%');
                    })
                    ->when($request->cbg, function ($query, $cbg) {
                        return $query->where('id_cabang', $cbg);
                    })
                    ->when($request->tAwal && $request->tAkhir, function ($query) use ($request) {
                        return $query->whereBetween('pengajuan.tanggal', [$request->tAwal, $request->tAkhir]);
                    })
                    ->when($request->sts, function ($query, $sts) {
                        if ($sts == 'Selesai' || $sts == 'Ditolak') {
                            return $query->where('pengajuan.posisi', $sts);
                        } else {
                            return $query->where('pengajuan.posisi', '<>', 'Selesai')
                                ->where('pengajuan.posisi', '<>', 'Ditolak');
                        }
                    })
                    ->when($request->score, function ($query, $score) {
                        return $query->whereRaw('FLOOR(pengajuan.average_by_sistem) = ?', $score)
                            ->orWhereRaw('FLOOR(pengajuan.average_by_penyelia) = ?', $score);
                    })
                    ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                    ->where('pengajuan.id_cabang', Auth::user()->id_cabang)
                    ->whereIn('pengajuan.posisi', ['Pincab', 'Selesai', 'Ditolak'])
                    ->paginate(5)
                    ->withQueryString();
            }
            return view('pengajuan-kredit.komentar-pincab-pengajuan', $param);
        } else {
            $id_cabang = Auth::user()->id_cabang;
            $dataPengajuan = PengajuanModel::select(
                'pengajuan.id',
                'pengajuan.tanggal',
                'pengajuan.posisi',
                'pengajuan.progress_pengajuan_data',
                'pengajuan.id_staf',
                'pengajuan.id_penyelia',
                'pengajuan.id_pbo',
                'pengajuan.id_pbp',
                'pengajuan.id_pincab',
                'pengajuan.tanggal_review_penyelia',
                'pengajuan.tanggal_review_pbp',
                'pengajuan.tanggal_review_pincab',
                'pengajuan.status',
                'pengajuan.status_by_sistem',
                'pengajuan.id_cabang',
                'pengajuan.average_by_sistem',
                'pengajuan.average_by_penyelia',
                'pengajuan.created_at',
                'pengajuan.deleted_at',
                'calon_nasabah.nama',
                'calon_nasabah.jenis_usaha',
                'calon_nasabah.id_pengajuan'
            )
            ->orderByRaw('CASE WHEN deleted_at IS NOT NULL THEN 0 ELSE 1 END, created_at DESC')
                ->when($request->search, function ($query, $search) {
                    return $query->where('calon_nasabah.nama', 'like', '%' . $search . '%');
                })
                ->when($request->pss, function ($query, $pss) {
                    return $query->where('pengajuan.posisi', $pss);
                })
                ->when($request->cbg, function ($query, $cbg) {
                    return $query->where('id_cabang', $cbg);
                })
                ->when($request->tAwal && $request->tAkhir, function ($query) use ($request) {
                    return $query->whereBetween('pengajuan.tanggal', [$request->tAwal, $request->tAkhir]);
                })
                ->when($request->sts, function ($query, $sts) {
                    if ($sts == 'Selesai' || $sts == 'Ditolak') {
                        return $query->where('pengajuan.posisi', $sts);
                    } else {
                        return $query->where('pengajuan.posisi', '<>', 'Selesai')
                        ->where('pengajuan.posisi', '<>', 'Ditolak');
                    }
                })
                ->when($request->score, function ($query, $score) {
                    return $query->whereRaw('FLOOR(pengajuan.average_by_sistem) = ?', $score)
                    ->orWhereRaw('FLOOR(pengajuan.average_by_penyelia) = ?', $score);
                })
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id');


            $dataPengajuanSampah = PengajuanModel::select(
                'pengajuan.id',
                'pengajuan.tanggal',
                'pengajuan.posisi',
                'pengajuan.progress_pengajuan_data',
                'pengajuan.id_staf',
                'pengajuan.id_penyelia',
                'pengajuan.id_pbo',
                'pengajuan.id_pbp',
                'pengajuan.id_pincab',
                'pengajuan.tanggal_review_penyelia',
                'pengajuan.tanggal_review_pbp',
                'pengajuan.tanggal_review_pincab',
                'pengajuan.status',
                'pengajuan.status_by_sistem',
                'pengajuan.id_cabang',
                'pengajuan.average_by_sistem',
                'pengajuan.average_by_penyelia',
                'pengajuan.created_at',
                'pengajuan.deleted_at',
                'calon_nasabah.nama',
                'calon_nasabah.jenis_usaha',
                'calon_nasabah.id_pengajuan'
            )->onlyTrashed()
            ->orderByRaw('CASE WHEN deleted_at IS NOT NULL THEN 0 ELSE 1 END, created_at DESC')
                ->when($request->search, function ($query, $search) {
                    return $query->where('calon_nasabah.nama', 'like', '%' . $search . '%');
                })
                ->when($request->pss, function ($query, $pss) {
                    return $query->where('pengajuan.posisi', $pss);
                })
                ->when($request->cbg, function ($query, $cbg) {
                    return $query->where('id_cabang', $cbg);
                })
                ->when($request->tAwal && $request->tAkhir, function ($query) use ($request) {
                    return $query->whereBetween('pengajuan.tanggal', [$request->tAwal, $request->tAkhir]);
                })
                ->when($request->sts, function ($query, $sts) {
                    if ($sts == 'Selesai' || $sts == 'Ditolak') {
                        return $query->where('pengajuan.posisi', $sts);
                    } else {
                        return $query->where('pengajuan.posisi', '<>', 'Selesai')
                        ->where('pengajuan.posisi', '<>', 'Ditolak');
                    }
                })
                ->when($request->score, function ($query, $score) {
                    return $query->whereRaw('FLOOR(pengajuan.average_by_sistem) = ?', $score)
                    ->orWhereRaw('FLOOR(pengajuan.average_by_penyelia) = ?', $score);
                })
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id');


            if ($request->tAwal && $request->tAkhir) {
                $dataPengajuan->whereBetween('pengajuan.tanggal', [$request->tAwal, $request->tAkhir]);
                $dataPengajuanSampah->whereBetween('pengajuan.tanggal', [$request->tAwal, $request->tAkhir]);
            }

            if ($request->cbg) {
                $dataPengajuan->where('pengajuan.id_cabang', $request->cbg);
                $dataPengajuanSampah->where('pengajuan.id_cabang', $request->cbg);
            }

            // if ($param['activeTab'] === 'home-tab') {
            //     $param['data_pengajuan'] = $dataPengajuan->paginate(5, ['*'], 'page', $page)->withQueryString();
            //     $param['sampah_pengajuan'] = $dataPengajuanSampah->paginate(5, ['*'], 'page', $page)->withQueryString();
            // } elseif ($param['activeTab'] === 'profile-tab') {
            //     $param['data_pengajuan'] = $dataPengajuan->paginate(5, ['*'], 'page', $page)->withQueryString();
            //     $param['sampah_pengajuan'] = $dataPengajuanSampah->paginate(5, ['*'], 'page', $page)->withQueryString();
            // } else {
            //     $param['data_pengajuan'] = $dataPengajuan->paginate(5)->withQueryString();
            //     $param['sampah_pengajuan'] = $dataPengajuanSampah->paginate(5)->withQueryString();
            // }
            $param['data_pengajuan'] = $dataPengajuan->paginate(5)->withQueryString();
            $param['sampah_pengajuan'] = $dataPengajuanSampah->paginate(5)->withQueryString();

            return view('pengajuan-kredit.komentar-pincab-pengajuan', $param);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
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

        $selectedProdukKredit = MstProdukKredit::select('id', 'name')->where('id', $request->produk)->first();
        $param['skema_kredit'] = '';
        if ($selectedProdukKredit)
            $param['skema_kredit'] = $selectedProdukKredit->name;

        $param['produk'] = $request->produk;
        $param['skemaId'] = $request->skema;
        $param['limit'] = $request->limit;
        $param['produkKredit'] = MstProdukKredit::select('id', 'name')->get();
        $param['skemaKredit'] = MstSkemaKredit::select('id', 'name')->get();
        $param['limitKredit'] = MstSkemaLimit::select('id', 'from', 'to', 'operator')->get();
        $param['skema'] = MstProdukKredit::find($param['produk'])->name ?? null;
        $param['maxKredit'] = $request->limit ? MstSkemaLimit::where('id', $request->limit)->first() : null;

        $param['jenis_usaha'] = config('dagulir.jenis_usaha');
        $param['tipe'] = config('dagulir.tipe_pengajuan');
        // return view('pengajuan-kredit.add-pengajuan-kredit', $param);
        return view('new-pengajuan.add-pengajuan', $param);
    }

    public function checkSubColumn(Request $request)
    {
        $idItem = $this->getDataLevel($request->get('idOption'));
        $subColumn = OptionModel::select('sub_column')->where('id', $idItem[1])->first();
        return $subColumn;
    }

    public function getItemJaminanByKategoriJaminanUtama(Request $request)
    {
        $kategori = $request->get('kategori');


        if ($kategori == 'Tanah' || $kategori == 'Tanah dan Bangunan') {
            $item = ItemModel::with('option')->where('nama', $kategori)->where('id_parent', 95)->first();

            $dataDetailJawabanText = \App\Models\JawabanTextModel::where('id_pengajuan', $request->id)
                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->where('jawaban_text.id_pengajuan', $request->id)
                ->whereIn('id_jawaban', [103, 104, 107, 108, 147])
                ->orderBy('id', 'ASC')->whereIn('nama', ['SHM No', 'Atas Nama', 'SHGB No', 'Berakhir Hak (SHGB)', 'Petok / Letter C', 'Foto'])
                ->get();

            $belumDiisi = array();
            foreach ($dataDetailJawabanText as $key => $val) {
                array_push($belumDiisi, $val->id_jawaban);
            }

            $belum = ItemModel::whereNotIn('id', $belumDiisi)
                ->orderBy('id', 'ASC')
                ->whereIn('nama', ['SHM No', 'Atas Nama', 'SHGB No', 'Berakhir Hak (SHGB)', 'Petok / Letter C', 'Foto'])
                ->where('id_parent', 96);

            $detailJawabanOption = \App\Models\JawabanPengajuanModel::where('id_pengajuan', $request->id)
                ->whereIn('id_jawaban', [136, 137, 141, 142])
                ->select('id_jawaban')
                ->orderBy('id', 'DESC');

            $itemBuktiPemilikan = ItemModel::with('option');

            $itemBuktiPemilikan->whereIn('nama', ['SHM No', 'Atas Nama', 'SHGB No', 'Berakhir Hak (SHGB)', 'Petok / Letter C', 'Foto'])->where('id_parent', 96);

            $data = [
                'item' => $item,
                'belum' => $belum->get(),
                'itemBuktiPemilikan' => $itemBuktiPemilikan->get(),
                'detailJawabanOption' => $detailJawabanOption->first(),
                'dataDetailJawabanText' => $dataDetailJawabanText
            ];
        } else if ($kategori == 'Kendaraan Bermotor') {
            $item = ItemModel::with('option')->where('nama', $kategori)->where('id_parent', 95)->first();

            $dataDetailJawabanText = DB::table('jawaban_text')
                ->where('id_pengajuan', $request->id)
                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->orderBy('id', 'ASC')
                ->whereIn('nama', ['BPKB No', 'Atas Nama', 'Foto'])
                ->where('id_parent', 96)
                ->get();

            $detailJawabanOption = \App\Models\JawabanPengajuanModel::where('id_pengajuan', $request->id)
                ->whereIn('id_jawaban', [138, 139, 140])
                ->select('id_jawaban')
                ->orderBy('id', 'DESC');

            $itemBuktiPemilikan = ItemModel::with('option');

            $itemBuktiPemilikan->whereIn('nama', ['BPKB No', 'Atas Nama', 'Foto'])->where('id_parent', 96);

            $data = [
                'item' => $item,
                'itemBuktiPemilikan' => $itemBuktiPemilikan->get(),
                'detailJawabanOption' => $detailJawabanOption->first(),
                'dataDetailJawabanText' => $dataDetailJawabanText
            ];
        } else {
            $item = ItemModel::where('nama', $kategori)->where('id_parent', 95)->first();

            $dataDetailJawabanText = \App\Models\JawabanTextModel::where('id_pengajuan', $request->id)
                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                ->distinct('nama')
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->orderBy('id', 'ASC')
                ->where('nama', $kategori)
                ->get();

            $itemBuktiPemilikan = ItemModel::where('nama', $kategori);

            $data = [
                'item' => $item,
                'itemBuktiPemilikan' => $itemBuktiPemilikan->get(),
                // 'detailJawabanOption' => $detailJawabanOption->first(),
                'dataDetailJawabanText' => $dataDetailJawabanText
            ];
        }

        return json_encode($data);
    }

    public function getItemJaminanByKategoriJaminanTambahan(Request $request)
    {
        $kategori = $request->get('kategori');

        $item = ItemModel::with('option')->where('nama', $kategori)->where('id_parent', 110)->first();

        $itemBuktiPemilikan = ItemModel::with('option');
        if ($kategori == 'Tanah' || $kategori == 'Tanah dan Bangunan') {

            $dataDetailJawabanText = \App\Models\JawabanTextModel::where('id_pengajuan', $request->id)
                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->whereIn('nama', ['SHM No', 'Atas Nama', 'SHGB No', 'Berakhir Hak (SHGB)', 'Petok / Letter C', 'Foto'])
                ->where('id_parent', 114)
                ->get();

            $detailJawabanOption = \App\Models\JawabanPengajuanModel::where('id_pengajuan', $request->id)
                ->whereIn('id_jawaban', [145, 146, 150, 151])
                ->select('id_jawaban')
                ->orderBy('id', 'DESC');

            $blm = array();
            foreach ($dataDetailJawabanText as $key => $val) {
                array_push($blm, $val->id_item);
            }

            $belum = ItemModel::whereNotIn('id', $blm)
                ->orderBy('id', 'ASC')
                ->whereIn('nama', ['SHM No', 'Atas Nama', 'SHGB No', 'Berakhir Hak (SHGB)', 'Petok / Letter C', 'Foto'])
                ->where('id_parent', 114)
                ->get();

            $itemBuktiPemilikan->whereIn('nama', ['SHM No', 'Atas Nama', 'SHGB No', 'Berakhir Hak (SHGB)', 'Petok / Letter C', 'Foto']);
        } else {

            $dataDetailJawabanText = \App\Models\JawabanTextModel::where('id_pengajuan', $request->id)
                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                ->distinct()
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->where('id_parent', 114)
                ->get();
            $detailJawabanOption = \App\Models\JawabanPengajuanModel::where('id_pengajuan', $request->id)
                ->whereIn('id_jawaban', [147, 148, 149])
                ->select('id_jawaban')
                ->orderBy('id', 'DESC');

            $itemBuktiPemilikan->whereIn('nama', ['BPKB No', 'Atas Nama', 'Foto']);
            $blm = array();
            foreach ($dataDetailJawabanText as $key => $val) {
                array_push($blm, $val->id_item);
            }

            $belum = ItemModel::whereNotIn('id', $blm)
                ->orderBy('id', 'ASC')
                ->whereIn('nama', ['BPKB No', 'Atas Nama', 'Foto'])
                ->where('id_parent', 114)
                ->get();
        }

        $pengajuan =  PengajuanModel::find($request->id);
        $dataJawaban = [];
        $dataSelect = [];
        if ($pengajuan) {
            if ($pengajuan->skema_kredit != 'Dagulir') {
                $id_nasabah = CalonNasabah::where('id_pengajuan',$pengajuan->id)->first()->id;
                foreach ($itemBuktiPemilikan->where('id_parent', 114)->get() as $i) {
                    array_push($dataJawaban, edit($pengajuan->id, $i->id)?->opsi_text ?? '');
                }

                if ($id_nasabah)
                    $dataSelect = edit_select($detailJawabanOption->first()->id_jawaban, $request->id)?->id_jawaban;
            } else {

                foreach ($itemBuktiPemilikan->where('id_parent', 114)->get() as $i) {
                    array_push($dataJawaban, edit_dagulir($pengajuan->dagulir_id, $i->id)?->opsi_text ?? '');
                }

                if ($pengajuan->dagulir_id)
                    $dataSelect = temporary_select_dagulir($item->id, $pengajuan->dagulir_id)?->id_jawaban;
            }
        }else{
            if ($request->skema_kredit == 'Dagulir') {
                foreach ($itemBuktiPemilikan->where('id_parent', 114)->get() as $i) {

                    array_push($dataJawaban, temporary($request->idCalonNasabah, $i->id)?->opsi_text ?? '');
                }

                if ($request->idCalonNasabah)
                    $dataSelect = temporary_select_dagulir($item->id, $request->idCalonNasabah)?->id_jawaban;
            }else{

                foreach ($itemBuktiPemilikan->where('id_parent', 114)->get() as $i) {
                    array_push($dataJawaban, temporary_dagulir($request->idCalonNasabah, $i->id)?->opsi_text ?? '');
                }

                if ($request->idCalonNasabah)
                    $dataSelect = temporary_select_dagulir($item->id, $request->idCalonNasabah)?->id_jawaban;
            }

        }


        $data = [
            'detailJawabanOption' => $detailJawabanOption->first(),
            'dataDetailJawabanText' => $dataDetailJawabanText,
            'item' => $item,
            'belum' => $belum,
            'itemBuktiPemilikan' => $itemBuktiPemilikan->where('id_parent', 114)->get(),
            'dataJawaban' => $dataJawaban,
            'dataSelect' => $dataSelect
        ];

        return json_encode($data);
    }

    public function getEditJaminanKategori(Request $request)
    {
        $kategori = $request->get('kategori');

        if ($kategori == 'Tanah' || $kategori == 'Tanah dan Bangunan') {
            $item = ItemModel::with('option')->where('nama', $kategori)->where('id_parent', 95)->first();

            $dataDetailJawabanText = \App\Models\JawabanTextModel::where('id_pengajuan', $request->id)
                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->where('jawaban_text.id_pengajuan', $request->id)
                ->orderBy('id', 'ASC')
                ->get();

            $itemBuktiPemilikan = ItemModel::with('option');

            $itemBuktiPemilikan->whereIn('nama', ['SHM No', 'Atas Nama', 'SHGB No', 'Berakhir Hak (SHGB)', 'Petok / Letter C', 'Foto'])->where('id_parent', 96);
        } else if ($kategori == 'Kendaraan Bermotor') {
            $item = ItemModel::with('option')->where('nama', $kategori)->where('id_parent', 95)->first();

            $dataDetailJawabanText = \App\Models\JawabanTextModel::where('id_pengajuan', $request->id)
                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->groupBy('nama')
                ->orderBy('id', 'ASC')
                ->first();

            $itemBuktiPemilikan = ItemModel::with('option');

            $itemBuktiPemilikan->whereIn('nama', ['BPKB No', 'Atas Nama', 'Foto'])->where('id_parent', 96);
        } else {
            $item = ItemModel::where('nama', $kategori)->where('id_parent', 95)->first();

            $dataDetailJawabanText = \App\Models\JawabanTextModel::where('id_pengajuan', $request->id)
                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->groupBy('nama')
                ->orderBy('id', 'ASC')
                ->get();

            $itemBuktiPemilikan = ItemModel::where('nama', $kategori);
        }

        $data = [
            'item' => $item,
            'itemBuktiPemilikan' => $itemBuktiPemilikan->get(),
            'dataDetailJawabanText' => $dataDetailJawabanText
        ];

        return json_encode($data);
    }

    public function getEditJaminanKategoriTambahan(Request $request)
    {
        $kategori = $request->get('kategori');

        $item = ItemModel::with('option')->where('nama', $kategori)->where('id_parent', 110)->first();

        $itemBuktiPemilikan = ItemModel::with('option');
        if ($kategori == 'Tanah' || $kategori == 'Tanah dan Bangunan') {
            $itemBuktiPemilikan->whereIn('nama', ['SHM No', 'Atas Nama', 'SHGB No', 'Berakhir Hak (SHGB)', 'Petok / Letter C', 'Foto']);

            $dataDetailJawabanText = \App\Models\JawabanTextModel::where('id_pengajuan', $request->id)
                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->groupBy('nama')
                ->orderBy('id', 'DESC');
        } else {
            $itemBuktiPemilikan->whereIn('nama', ['BPKB No', 'Atas Nama', 'Foto']);

            $dataDetailJawabanText = \App\Models\JawabanTextModel::where('id_pengajuan', $request->id)
                ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->orderBy('id', 'DESC');
        }
        $data = [
            'dataDetailJawabanText' => $dataDetailJawabanText->where('id_parent', 114)->get(),
            'item' => $item,
            'itemBuktiPemilikan' => $itemBuktiPemilikan->where('id_parent', 114)->get(),
        ];

        return json_encode($data);
    }

    public function getIjinUsaha(Request $request)
    {
        $id = $request->get('id_pengajuan');

        $dataDetailJawabanText = \App\Models\JawabanTextModel::where('id_pengajuan', $id)
            ->select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'jawaban_text.skor_penyelia', 'item.id as id_item', 'item.nama')
            ->join('item', 'jawaban_text.id_jawaban', 'item.id')
            ->whereIn('item.nama', ['nib', 'surat keterangan usaha', 'tidak ada legalitas usaha']);

        return response()->json($dataDetailJawabanText);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $statusSlik = false;
        $find = array('Rp ', '.', ',');
        $request->validate([
            // 'upload_file.*' => 'file|max:5120',
            'name' => 'required',
            'no_telp' => 'required',
            'alamat_rumah' => 'required',
            'alamat_usaha' => 'required',
            'no_ktp' => 'required',
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
        ], [
            'required' => 'Data :attribute harus terisi.',
            'not_in' => 'kolom harus dipilih.',
        ]);

        try {
            DB::beginTransaction();
            $addPengajuan = new PengajuanModel;
            $addPengajuan->id_staf = auth()->user()->id;
            $addPengajuan->tanggal = date(now());
            $addPengajuan->id_cabang = auth()->user()->id_cabang;
            $addPengajuan->progress_pengajuan_data = $request->progress;
            $addPengajuan->skema_kredit = $request->skema_kredit;
            $addPengajuan->produk_kredit_id = $request->produk_kredit_id;
            $addPengajuan->skema_kredit_id = $request->skema_kredit_id;
            $addPengajuan->skema_limit_id = $request->skema_limit_id ?? null;
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
            $addData->telp = $request->get('no_telp');
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
            $id_calon_nasabah = $addData->id;

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
                    $data = $this->getDataLevel($mergedDataLevel[$i]);
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
                    $data = $this->getDataLevel($mergedDataLevel[$i]);
                    if (is_numeric($data[0])) {
                        if ($data[0] > 0) {
                            JawabanPengajuanModel::insert([
                                'id_pengajuan' => $id_pengajuan,
                                'id_jawaban' => $this->getDataLevel($mergedDataLevel[$i])[1],
                                'skor' => $this->getDataLevel($mergedDataLevel[$i])[0],
                            ]);
                        }
                    } else {
                        JawabanPengajuanModel::insert([
                            'id_pengajuan' => $id_pengajuan,
                            'id_jawaban' => $this->getDataLevel($mergedDataLevel[$i])[1]
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

            // dd($request->id_nasabah);
            PerhitunganKredit::where('temp_calon_nasabah_id', $tempNasabah->id)
                ->update([
                    'pengajuan_id' => $id_pengajuan,
                    'temp_calon_nasabah_id' => null
                ]);

            JawabanTemp::where('id_temporary_calon_nasabah', $tempNasabah->id)->delete();
            JawabanTempModel::where('id_temporary_calon_nasabah', $tempNasabah->id)->delete();
            $tempNasabah->delete();
            DB::table('temporary_usulan_dan_pendapat')
                ->where('id_temp', $tempNasabah->id)
                ->delete();
            DB::table('data_po_temp')->where('id_calon_nasabah_temp', $tempNasabah->id)->delete();

            // Log Pengajuan Baru
            $namaNasabah = 'undifined';
            if ($addData)
                $namaNasabah = $addData->nama;

            $this->logPengajuan->store('Staff dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' melakukan proses pembuatan data pengajuan atas nama ' . $namaNasabah . '.', $id_pengajuan, Auth::user()->id, Auth::user()->nip);

            DB::commit();
            event(new EventMonitoring('store pengajuan'));

            if (!$statusSlik){
                alert()->success('Berhasil','Data berhasil disimpan.');
                return redirect()->route('pengajuan-kredit.index')->withStatus('Data berhasil disimpan.');
            }else{
                alert()->error('Gagal','Pengajuan Ditolak');
                return redirect()->route('pengajuan-kredit.index')->withError('Pengajuan ditolak');

            }
        } catch (Exception $e) {
            return $e->getMessage();
            DB::rollBack();
            return redirect()->route('pengajuan-kredit.index')->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (QueryException $e) {
            return $e->getMessage();
            DB::rollBack();
            return redirect()->route('pengajuan-kredit.index')->withError('Terjadi kesalahan' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $param['pageTitle'] = "Dashboard";
        $param['multipleFiles'] = $this->isMultipleFiles;

        $param['dataAspek'] = ItemModel::select('*')->where('level', 1)->where('nama', '!=', 'Data Umum')->get();

        $data['dataPertanyaanSatu'] = ItemModel::select('id', 'nama', 'level', 'id_parent')->where('level', 2)->where('id_parent', 3)->get();

        $param['itemSlik'] = ItemModel::with('option')->where('nama', 'SLIK')->first();
        $param['itemSP'] = ItemModel::where('nama', 'Surat Permohonan')->first();
        $param['itemP'] = ItemModel::where('nama', 'Laporan SLIK')->first();
        $param['itemKTPSu'] = ItemModel::where('nama', 'Foto KTP Suami')->first();
        $param['itemKTPIs'] = ItemModel::where('nama', 'Foto KTP Istri')->first();
        $param['itemKTPNas'] = ItemModel::where('nama', 'Foto KTP Nasabah')->first();
        $param['itemNIB'] = ItemModel::where('nama', 'Dokumen NIB')->first();
        $param['itemNPWP'] = ItemModel::where('nama', 'Dokumen NPWP')->first();
        $param['itemSKU'] = ItemModel::where('nama', 'Dokumen Surat Keterangan Usaha')->first();

        $param['dataUmum'] = PengajuanModel::select(
            'pengajuan.id',
            'pengajuan.tanggal',
            'pengajuan.posisi',
            'pengajuan.tanggal_review_penyelia',
            'pengajuan.skema_kredit',
            'calon_nasabah.id as id_calon_nasabah',
            'calon_nasabah.nama',
            'calon_nasabah.alamat_rumah',
            'calon_nasabah.alamat_usaha',
            'calon_nasabah.no_ktp',
            'calon_nasabah.no_telp',
            'calon_nasabah.tempat_lahir',
            'calon_nasabah.tanggal_lahir',
            'calon_nasabah.status',
            'calon_nasabah.sektor_kredit',
            'calon_nasabah.jenis_usaha',
            'calon_nasabah.jumlah_kredit',
            'calon_nasabah.tujuan_kredit',
            'calon_nasabah.jaminan_kredit',
            'calon_nasabah.hubungan_bank',
            'calon_nasabah.hubungan_bank',
            'calon_nasabah.verifikasi_umum',
            'calon_nasabah.id_kabupaten',
            'calon_nasabah.id_kecamatan',
            'calon_nasabah.id_desa',
            'calon_nasabah.tenor_yang_diminta'
        )
            ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
            ->find($id);
        $param['pengajuan'] = PengajuanModel::find($id);
        $param['dataKabupaten'] = Kabupaten::all();
        $param['dataKecamatan'] = Kecamatan::where('id_kabupaten', $param['dataUmum']->id_kecamatan)->get();
        $param['dataDesa'] = Desa::where('id_kecamatan', $param['dataUmum']->id_kecamatan)->get();
        $param['duTemp'] = $param['dataUmum'];
        if ($param['dataUmum']) {
            $param['dataUmum']->tanggal_lahir = $this->formatDate($param['dataUmum']->tanggal_lahir);
        }
        $param['allKab'] = Kabupaten::get();
        $param['allKec'] = Kecamatan::where('id_kabupaten', $param['dataUmum']->id_kabupaten)->get();
        $param['allDesa'] = Desa::where('id_kecamatan', $param['dataUmum']->id_kecamatan)->get();
        $param['pendapatDanUsulanStaf'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_staff')->first();
        if ($param['dataUmum']->skema_kredit == 'KKB') {
            $param['dataMerk'] = MerkModel::all();
            $param['dataPO'] = DB::table('data_po')
                ->where('id_pengajuan', $id)
                ->first();
        }
        $param['skema'] = $param['dataUmum']->skema_kredit;

        $param['jawabanSlik'] = JawabanModel::select('id', 'id_jawaban', 'skor')
                ->where('id_pengajuan', $id)
                ->whereIn('id_jawaban', [71,72,73,74])
                ->first();
        $param['jawabanLaporanSlik'] = \App\Models\JawabanTextModel::where('id_pengajuan', $id)
                ->where('id_jawaban', 146)
                ->first();
        $param['dataPertanyaanSatu'] = ItemModel::select('id', 'nama', 'level', 'id_parent')->where('level', 2)->where('id_parent', 3)->get();
        $param['komentar'] = KomentarModel::where('id_pengajuan', $id)->first();
        // return view('pengajuan-kredit.edit-pengajuan-kredit', $param);
        return view('new-pengajuan.edit-pengajuan', $param);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        // dd($request->dataLevelDua, $request->dataLevelTiga, $request->dataLevelTiga);
        $find = array('Rp.', '.', ',');
        $request->validate([
            'name' => 'required',
            'alamat_rumah' => 'required',
            'alamat_usaha' => 'required',
            'no_ktp' => 'required|max:16',
            'no_telp' => 'required|max:13',
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
        DB::beginTransaction();
        try {
            $updatePengajuan = PengajuanModel::find($id);
            $updatePengajuan->id_cabang = auth()->user()->id_cabang;
            $updatePengajuan->progress_pengajuan_data = $request->progress;
            $updatePengajuan->save();
            $id_pengajuan = $updatePengajuan->id;

            $updateData = CalonNasabah::find($request->id_nasabah);
            $updateData->nama = $request->name;
            $updateData->alamat_rumah = $request->alamat_rumah;
            $updateData->alamat_usaha = $request->alamat_usaha;
            $updateData->no_ktp = $request->no_ktp;
            $updateData->no_telp = $request->no_telp;
            $updateData->tempat_lahir = $request->tempat_lahir;
            $updateData->tanggal_lahir = $this->formatDate($request->tanggal_lahir);
            $updateData->status = $request->status;
            $updateData->sektor_kredit = $request->sektor_kredit;
            $updateData->jenis_usaha = $request->jenis_usaha;
            $updateData->jumlah_kredit = str_replace($find, "", $request->jumlah_kredit);
            $updateData->tujuan_kredit = $request->tujuan_kredit;
            $updateData->jaminan_kredit = $request->jaminan;
            $updateData->hubungan_bank = $request->hubungan_bank;
            $updateData->verifikasi_umum = $request->hasil_verifikasi;
            $updateData->id_user = auth()->user()->id;
            $updateData->id_pengajuan = $id_pengajuan;
            $updateData->id_desa = $request->desa;
            $updateData->id_kecamatan = $request->kec;
            $updateData->id_kabupaten = $request->kabupaten;
            $updateData->tenor_yang_diminta = $request->tenor_yang_diminta;
            $updateData->save();
            $id_calon_nasabah = $updateData->id;


            // $addJawabanLevel = new JawabanPengajuanModel;
            // $addJawabanLevel->id_pengajuan = $id_pengajuan;
            $finalArray = array();
            $finalArray_text = array();
            $rata_rata = array();
            if (!isset($request->id_kategori_jaminan_tambahan)) {
                $dataJawabanText = new JawabanTextModel;
                $dataJawabanText->id_jawaban = 110;
                $dataJawabanText->id_pengajuan = $id_pengajuan;
                $dataJawabanText->opsi_text = $request->kategori_jaminan_tambahan;
                $dataJawabanText->save();
            } else {
                $dataJawabanText = JawabanTextModel::find($request->id_kategori_jaminan_tambahan);
                $dataJawabanText->opsi_text = $request->kategori_jaminan_tambahan;
                $dataJawabanText->save();
            }

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
            if ($request->isNpwp == 0) {
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

            // ijin usaha
            $jawabanIjinUsaha = JawabanTextModel::where('id_pengajuan', $id_pengajuan)->where('id_jawaban', 76)->first();
            if ($jawabanIjinUsaha) {
                $jawabanIjinUsaha->id_pengajuan = $id_pengajuan;
                $jawabanIjinUsaha->id_jawaban =  76;
                $jawabanIjinUsaha->opsi_text = $request->ijin_usaha;
                $jawabanIjinUsaha->save();
            }
            else {
                $dataJawabanText = new JawabanTextModel;
                $dataJawabanText->id_pengajuan = $id_pengajuan;
                $dataJawabanText->id_jawaban =  76;
                $dataJawabanText->opsi_text = $request->ijin_usaha;
                $dataJawabanText->save();
            }

            if (count($request->file()) > 0) {
                foreach ($request->file('update_file') as $key => $value) {
                    if (
                        str_contains($value->getMimeType(), 'text') ||
                        str_contains($value->getMimeType(), 'x-empty')
                    ) continue;

                    if ($request->id_update_file[$key] != null) {
                        $image = $value;
                        $imageName = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();

                        $imageLama = JawabanTextModel::where('id_jawaban', $request->get('id_update_file')[$key])
                            ->select('opsi_text', 'id_jawaban')
                            ->where('opsi_text', '!=', null)
                            ->orderBy('id', 'desc')
                            ->get();
                        // return $imageLama;
                        foreach ($imageLama as $imageKey => $imageValue) {
                            $pathLama = public_path() . '/upload/' . $id_pengajuan . '/' . $imageValue->id_jawaban . '/' . $imageValue->opsi_text;
                            File::delete($pathLama);
                        }

                        $filePath = public_path() . '/upload/' . $id_pengajuan . '/' . $request->id_file_text[$key];
                        // $filePath = public_path() . '/upload';
                        if (!File::isDirectory($filePath)) {
                            File::makeDirectory($filePath, 493, true);
                        }

                        $image->move($filePath, $imageName);

                        $imgUpdate = DB::table('jawaban_text');
                        $imgUpdate->where('id', $request->get('id_update_file')[$key])->update(['opsi_text' => $imageName]);
                    } else {
                        $image = $request->file('update_file')[$key];
                        $imageName = auth()->user()->id . '-' . time() . '-' . $image->getClientOriginalName();

                        $filePath = public_path() . '/upload/' . $id_pengajuan . '/' . $request->id_file_text[$key];

                        if (!File::isDirectory($filePath)) {
                            File::makeDirectory($filePath, 493, true);
                        }

                        $image->move($filePath, $imageName);

                        $dataJawabanText = new JawabanTextModel;
                        $dataJawabanText->id_pengajuan = $id_pengajuan;
                        $dataJawabanText->id_jawaban =  $request->id_file_text[$key];
                        $dataJawabanText->opsi_text = $imageName;
                        $dataJawabanText->save();
                    }
                }
            }

            // Delete multiple deleted file
            array_map(
                fn ($fileId) => JawabanTextModel::find($fileId)?->delete(),
                $request->id_delete_file ?? []
            );

            // Data KKB Handler
            if ($updatePengajuan->skema_kredit == 'KKB') {
                DB::table('data_po')
                    ->where('id_pengajuan', $id)
                    ->update([
                        // 'tahun_kendaraan' => $request->tahun,
                        // 'id_type' => $request->id_tipe,
                        // 'warna' => $request->warna,
                        // 'keterangan' => 'Pemesanan ' . $request->pemesanan,
                        // 'jumlah' => $request->sejumlah,
                        // 'harga'
                        // 'id_pengajuan' => $id_pengajuan,
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
            foreach ($request->id_jawaban_text as $key => $value) {
                if (array_key_exists($key, $request->info_text) && array_key_exists($key, $request->id_text)) {
                    if ($request->id_jawaban_text[$key] != null && $request->info_text[$key] != null) {
                        if ($request->info_text[$key] == null) continue;

                        if ($request->id_jawaban_text[$key] == null && $request->info_text[$key] != null) {
                            if (isset($request->id_text[$key]) && isset($request->info_text[$key])) {
                                // dd($request->id_jawaban_text)
                                $data_baru = new JawabanTextModel();
                                $data_baru->id_pengajuan = $id_pengajuan;
                                $data_baru->id_jawaban = $request->id_text[$key];
                                if ($request->id_text[$key] == '131' || $request->id_text[$key] == '143' || $request->id_text[$key] == '90' || $request->id_text[$key] == '138') {
                                    $data_baru->opsi_text = $request->info_text[$key];
                                } else {
                                    $data_baru->opsi_text = str_replace($find, "", $request->info_text[$key]);
                                }
                                $data_baru->skor_penyelia = null;
                                $data_baru->skor = null;
                                $data_baru->save();
                            }
                        } else {
                            $skor[$key] = $request->skor_penyelia_text[$key];
                            // ccd
                            // ddd($request->id_text[27]);
                            if (isset($request->id_text[$key]) && isset($request->info_text[$key])) {
                                $skor = array();
                                if ($request->skor_penyelia_text[$key] == 'null') {
                                    $skor[$key] = null;
                                } else {
                                    $skor[$key] = $request->skor_penyelia_text[$key];
                                }
                                array_push($finalArray_text, array(
                                    'id_pengajuan' => $id_pengajuan,
                                    'id_jawaban' => $request->id_text[$key],
                                    'opsi_text' => ($request->id_text[$key] != '131' && $request->id_text[$key] != '143' && $request->id_text[$key] != '90' && $request->id_text[$key] != '138') ? str_replace($find, "", $request->info_text[$key]) : $request->info_text[$key],
                                    'skor_penyelia' => $skor[$key],
                                    'updated_at' => date("Y-m-d H:i:s"),
                                ));
                            }
                        }
                    }
                }
            };

            // data Level dua
            if ($request->dataLevelDua != null) {
                $data = $request->dataLevelDua;
                $result_dua = array_values(array_filter($data));
                foreach ($result_dua as $key => $value) {
                    $data_level_dua = $this->getDataLevel($value);
                    $skor[$key] = $data_level_dua[0];
                    $id_jawaban[$key] = $data_level_dua[1];
                    // if ($skor[$key] != 'kosong') {
                    if ($skor[$key] != 'kosong') {
                        array_push($rata_rata, $skor[$key]);
                    } else {
                        $skor[$key] = null;
                    }
                    array_push(
                        $finalArray,
                        array(
                            'id_pengajuan' => $id_pengajuan,
                            'id_jawaban' => $id_jawaban[$key],
                            'skor' => $skor[$key],
                            'updated_at' => date("Y-m-d H:i:s"),
                        )
                    );
                }
            }

            // data level tiga
            if ($request->dataLevelTiga != null) {
                $data = $request->dataLevelTiga;
                $result_tiga = array_values(array_filter($data));
                foreach ($result_tiga as $key => $value) {
                    $data_level_tiga = $this->getDataLevel($value);
                    $skor[$key] = $data_level_tiga[0];
                    $id_jawaban[$key] = $data_level_tiga[1];
                    if ($skor[$key] != 'kosong') {
                        array_push($rata_rata, $skor[$key]);
                    } else {
                        $skor[$key] = null;
                    }
                    array_push(
                        $finalArray,
                        array(
                            'id_pengajuan' => $id_pengajuan,
                            'id_jawaban' => $id_jawaban[$key],
                            'skor' => $skor[$key],
                            'updated_at' => date("Y-m-d H:i:s"),
                        )
                    );
                }
            }

            // data level empat
            if ($request->dataLevelEmpat != null) {
                $data = $request->dataLevelEmpat;
                $result_empat = array_values(array_filter($data));
                foreach ($result_empat as $key => $value) {
                    $data_level_empat = $this->getDataLevel($value);
                    $skor[$key] = $data_level_empat[0];
                    $id_jawaban[$key] = $data_level_empat[1];
                    if ($skor[$key] != 'kosong')
                        array_push($rata_rata, $skor[$key]);
                    else if ($skor[$key] == 'kosong')
                        array_push($rata_rata, 1);
                    else
                        $skor[$key] = null;

                    array_push(
                        $finalArray,
                        array(
                            'id_pengajuan' => $id_pengajuan,
                            'id_jawaban' => $id_jawaban[$key],
                            'skor' => $skor[$key],
                            'updated_at' => date("Y-m-d H:i:s"),
                        )
                    );
                }
            }

            $average = array_sum($rata_rata) / count($rata_rata);
            $result = round($average, 2);
            $status = "";
            $updateData = PengajuanModel::find($id_pengajuan);
            if ($result > 0 && $result <= 1) {
                $status = "merah";
            } elseif ($result >= 2 && $result <= 3) {
                $status = "kuning";
            } elseif ($result > 3) {
                $status = "hijau";
            } else {
                $status = "merah";
            }

            for ($i = 0; $i < count($finalArray); $i++) {
                /*
                1. variabel a = query select k table jawaban where(id, id_jawaban)
                2. jika variabel a itu ada maka proses update
                3. jika variabel a itu null maka insert / data baru
                */
                $data = DB::table('jawaban');

                if (!empty($request->id[$i])) {
                    if (is_numeric($finalArray[$i]['skor']))
                        $data->where('id', $request->id[$i])->update($finalArray[$i]);
                    else {
                        $data->where('id', $request->id[$i])->update([
                            'id_pengajuan' => $finalArray[$i]['id_pengajuan'],
                            'id_jawaban' => $finalArray[$i]['id_jawaban'],
                            'skor' => is_numeric($finalArray[$i]['skor']) ? $finalArray[$i]['skor'] : null,
                            'updated_at' => $finalArray[$i]['updated_at'],
                        ]);
                    }
                } else {
                    $data->insert($finalArray[$i]);
                }
            }

            // dd($request->id_text, $request->id_jawaban_text);
            // dd($finalArray_text);
            // return $finalArray_text;
            for ($i = 0; $i < count($request->id_text); $i++) {
                /*
                1. variabel a = query select k table jawaban where(id, id_jawaban)
                2. jika variabel a itu ada maka proses update
                3. jika variabel a itu null maka insert / data baru
                */
                $data = DB::table('jawaban_text');
                if (array_key_exists($i, $request->id_jawaban_text)) {
                    if ($request->id_jawaban_text[$i] != null && $request->id_text[$i] != null) {
                        $data->where('id', $request->get('id_jawaban_text')[$i])->update(['opsi_text' => ($request->id_text[$i] != '131' && $request->id_text[$i] != '143' && $request->id_text[$i] != '90' && $request->id_text[$i] != '138') ? str_replace($find, "", $request->info_text[$i]) : $request->info_text[$i]]);
                    }
                }
                // if (!empty($request->id_jawaban_text[$i])) {
                // } else {
                //     $data->insert($finalArray_text[$i]);
                // }
            }

            for ($i = 0; $i < count($request->pendapat_per_aspek); $i++) {
                $data = DB::table('pendapat_dan_usulan_per_aspek');
                if ($request->id_jawaban_text[$i] != null) {
                    $data->where('id', $request->get('id_jawaban_aspek')[$i])->update(['pendapat_per_aspek' => $request->get('pendapat_per_aspek')[$i]]);
                } else {
                    $data->insert([
                        'id_pengajuan' => $id_pengajuan,
                        'id_staf' => auth()->user()->id,
                        'id_penyelia' => null,
                        'id_pincab' => null,
                        'id_aspek' => $request->get('id_aspek')[$i],
                        'pendapat_per_aspek' => $request->get('pendapat_per_aspek')[$i],
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            if ($request->get('id_komentar_staff_text') != null) {
                $id_komentar_staff = $request->get('id_komentar_staff_text');
                $updateKomentar = KomentarModel::find($id_komentar_staff);
                $updateKomentar->komentar_staff = $request->get('komentar_staff');
                $updateKomentar->update();
            } else {
                $addKomentar = new KomentarModel;
                $addKomentar->id_pengajuan = $id_pengajuan;
                $addKomentar->komentar_staff = $request->get('komentar_staff');
                $addKomentar->id_staff = Auth::user()->id;
                $addKomentar->save();
            }

            $updateData->posisi = 'Proses Input Data';
            $updateData->status_by_sistem = $status;
            $updateData->average_by_sistem = $result;
            $updateData->update();

            // Log Edit Pengajuan
            $namaNasabah = 'undifined';
            if ($updateData->nama)
                $namaNasabah = $updateData->nama;

            $this->logPengajuan->store('Staff dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' melakukan proses perubahan data pengajuan atas nama ' . $namaNasabah, $id, Auth::user()->id, Auth::user()->nip);
            // Session::put('id',$addData->id);
            DB::commit();
            return redirect()->route('pengajuan-kredit.index')->withStatus('Berhasil mengupdate data.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getkecamatan(Request $request)
    {
        $kecamatan = Kecamatan::where("id_kabupaten", $request->kabID)->pluck('id', 'kecamatan');
        return response()->json($kecamatan);
    }
    public function getdesa(Request $request)
    {
        $desa = Desa::where("id_kecamatan", $request->kecID)->pluck('id', 'desa');
        return response()->json($desa);
    }
    public function getDataLevel($data)
    {
        $data_level = explode('-', $data);
        return $data_level;
    }

    function getAnswer($id)
    {
        $answerData = \DB::select("SELECT jt.id, jt.id_pengajuan, option.id AS id_option, item.id AS id_item, item.level, item.id_parent, (SELECT i.nama FROM item AS i WHERE i.id = item.id_parent) AS parent, item.nama, jt.opsi_text FROM `jawaban_text` AS jt JOIN option ON option.id = jt.id_jawaban JOIN item ON item.id = option.id_item where jt.id_pengajuan = $id;");
        foreach ($answerData as $item) {
            // find parent
            if ($item->level == 2) {
                $parent = \DB::select("SELECT id, nama, id_parent FROM item WHERE id = $item->id_parent");
                if ($parent) {
                    if (count($parent) > 0) {
                        $item->parent = $parent[0]->nama;
                    }
                }
            } else if ($item->level == 3) {
                $parent2 = \DB::select("SELECT id, nama, id_parent FROM item WHERE id = $item->id_parent");
                if ($parent2) {
                    if (count($parent2) > 0) {
                        $id_parent = $parent2[0]->id_parent;
                        $parent = \DB::select("SELECT id, nama, id_parent FROM item WHERE id = $id_parent");
                        if ($parent) {
                            if (count($parent) > 0) {
                                $item->parent = $parent[0]->nama;
                            }
                        }
                    }
                }
            } else if ($item->level == 4) {
                $parent3 = \DB::select("SELECT id, nama, id_parent FROM item WHERE id = $item->id_parent");
                if ($parent3) {
                    if (count($parent3) > 0) {
                        $id_parent = $parent3[0]->id_parent;
                        $parent2 = \DB::select("SELECT id, nama, id_parent FROM item WHERE id = $id_parent");
                        if ($parent2) {
                            if (count($parent2) > 0) {
                                $id_parent = $parent2[0]->id_parent;
                                $parent = \DB::select("SELECT id, nama, id_parent FROM item WHERE id = $id_parent");
                                if ($parent) {
                                    if (count($parent) > 0) {
                                        $item->parent = $parent[0]->nama;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $utilityController = new UtilityController;

        $totalDataUmum = $utilityController->getTotalColumnsOfTable('calon_nasabah', ['id', 'created_at', 'updated_at']);
        $totalJawabDataUmum = $utilityController->getTotalColumnsOfTable('calon_nasabah', ['id', 'created_at', 'updated_at']);
        $totalAManagement = 0;
        $totalJawabAManagement = 0;
        $totalAHukum = 0;
        $totalJawabAHukum = 0;
        $totalAJaminan = 0;
        $totalJawabAJaminan = 0;
        $totalATeknis = 0;
        $totalJawabATeknis = 0;
        $totalAPemasaran = 0;
        $totalJawabAPemasaran = 0;
        $totalAKeuangan = 0;
        $totalJawabAKeuangan = 0;
        foreach ($answerData as $item) {
            if ($item->parent == 'Data Umum') {
                $totalDataUmum++;
                if ($item->opsi_text != null || $item->opsi_text != '') {
                    $totalJawabDataUmum++;
                }
            }
            if ($item->parent == 'Aspek Management') {
                $totalAManagement++;
                if ($item->opsi_text != null || $item->opsi_text != '') {
                    $totalJawabAManagement++;
                }
            }
            if ($item->parent == 'Aspek Hukum') {
                $totalAHukum++;
                if ($item->opsi_text != null || $item->opsi_text != '') {
                    $totalJawabAHukum++;
                }
            }
            if ($item->parent == 'Aspek Jaminan') {
                $totalAJaminan++;
                if ($item->opsi_text != null || $item->opsi_text != '') {
                    $totalJawabAJaminan++;
                }
            }
            if ($item->parent == 'Aspek Teknis & Produksi') {
                $totalATeknis++;
                if ($item->opsi_text != null || $item->opsi_text != '') {
                    $totalJawabATeknis++;
                }
            }
            if ($item->parent == 'Aspek Pemasaran') {
                $totalAPemasaran++;
                if ($item->opsi_text != null || $item->opsi_text != '') {
                    $totalJawabAPemasaran++;
                }
            }
            if ($item->parent == 'Aspek Keuangan') {
                $totalAKeuangan++;
                if ($item->opsi_text != null || $item->opsi_text != '') {
                    $totalJawabAKeuangan++;
                }
            }
        }
        // return $answerData;
        return [
            [
                'total_data' => $totalDataUmum,
                'total_answer' => $totalJawabDataUmum,
                'percentage' => $totalDataUmum > 0 && $totalJawabDataUmum > 0 ? intval(($totalJawabDataUmum / $totalDataUmum) * 100) : 0,
            ],
            [
                'total_data' => $totalAManagement,
                'total_answer' => $totalJawabAManagement,
                'percentage' => $totalAManagement > 0 && $totalJawabAManagement > 0 ? intval(($totalJawabAManagement / $totalAManagement) * 100) : 0,
            ],
            [
                'total_data' => $totalAHukum,
                'total_answer' => $totalJawabAHukum,
                'percentage' => $totalAHukum > 0 && $totalJawabAHukum > 0 ? intval(($totalJawabAHukum / $totalAHukum) * 100) : 0,
            ],
            [
                'total_data' => $totalAJaminan,
                'total_answer' => $totalJawabAJaminan,
                'percentage' => $totalAJaminan > 0 && $totalJawabAJaminan > 0 ? intval(($totalJawabAJaminan / $totalAJaminan) * 100) : 0,
            ],
            [
                'total_data' => $totalATeknis,
                'total_answer' => $totalJawabATeknis,
                'percentage' => $totalATeknis > 0 && $totalJawabATeknis > 0 ? intval(($totalJawabATeknis / $totalATeknis) * 100) : 0,
            ],
            [
                'total_data' => $totalAPemasaran,
                'total_answer' => $totalJawabAPemasaran,
                'percentage' => $totalAPemasaran > 0 && $totalJawabAPemasaran > 0 ? intval(($totalJawabAPemasaran / $totalAPemasaran) * 100) : 0,
            ],
            [
                'total_data' => $totalAKeuangan,
                'total_answer' => $totalJawabAKeuangan,
                'percentage' => $totalAKeuangan > 0 && $totalJawabAKeuangan > 0 ? intval(($totalJawabAKeuangan / $totalAKeuangan) * 100) : 0,
            ],
        ];
    }

    // get detail jawaban dan skor pengajuan
    public function getDetailJawaban($id)
    {
        Session::put('is_dagulir', false);
        if (auth()->user()->role == 'Penyelia Kredit' || auth()->user()->role == 'PBO' || auth()->user()->role == 'PBP') {
            $param['pageTitle'] = "Dashboard";
            $param['dataAspek'] = ItemModel::where('level', 1)->where('nama', '!=', 'Data Umum')->get();
            $param['itemSlik'] = ItemModel::join('option as o', 'o.id_item', 'item.id')
                ->join('jawaban as j', 'j.id_jawaban', 'o.id')
                ->join('pengajuan as p', 'p.id', 'j.id_pengajuan')
                ->where('p.id', $id)
                ->where('nama', 'SLIK')
                ->first();
            $param['itemSP'] = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();
            $param['itemCatatanSlik'] = ItemModel::where('nama', 'Catatan Slik')->first();
            $param['itemKTPSu'] = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();
            $param['itemKTPIs'] = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();

            $param['dataUmumNasabah'] = PengajuanModel::select(
                'pengajuan.id',
                'pengajuan.tanggal',
                'pengajuan.posisi',
                'pengajuan.tanggal_review_penyelia',
                'pengajuan.skema_kredit',
                'pengajuan.id_staf',
                'pengajuan.id_penyelia',
                'pengajuan.id_pbo',
                'pengajuan.id_pbp',
                'calon_nasabah.id as id_calon_nasabah',
                'calon_nasabah.nama',
                'calon_nasabah.alamat_rumah',
                'calon_nasabah.alamat_usaha',
                'calon_nasabah.no_ktp',
                'calon_nasabah.no_telp',
                'calon_nasabah.tempat_lahir',
                'calon_nasabah.tanggal_lahir',
                'calon_nasabah.status',
                'calon_nasabah.sektor_kredit',
                'calon_nasabah.jenis_usaha',
                'calon_nasabah.jumlah_kredit',
                'calon_nasabah.tujuan_kredit',
                'calon_nasabah.jaminan_kredit',
                'calon_nasabah.hubungan_bank',
                'calon_nasabah.hubungan_bank',
                'calon_nasabah.verifikasi_umum',
                'calon_nasabah.id_kabupaten',
                'calon_nasabah.id_kecamatan',
                'calon_nasabah.id_desa',
                'calon_nasabah.tenor_yang_diminta',
            )
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->find($id);
            $param['allKab'] = Kabupaten::get();
            $param['allKec'] = Kecamatan::where('id_kabupaten', $param['dataUmumNasabah']->id_kabupaten)->get();
            $param['allDesa'] = Desa::where('id_kecamatan', $param['dataUmumNasabah']->id_kecamatan)->get();
            $param['dataUmum'] = PengajuanModel::select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.tanggal_review_penyelia')
                ->find($id);
            $param['pendapatDanUsulanStaf'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_staff')->first();
            $param['pendapatDanUsulanPenyelia'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_penyelia')->first();
            if (auth()->user()->role == 'PBO' || auth()->user()->role == 'PBP')
                $param['pendapatDanUsulanPBO'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_pbo')->first();
            if (auth()->user()->role == 'PBP')
                $param['pendapatDanUsulanPBP'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_pbp')->first();
            if ($param['dataUmumNasabah']->skema_kredit == 'KKB') {
                $param['dataPO'] = DB::table('data_po')
                    ->where('id_pengajuan', $id)
                    ->first();
            }
            $param['kec_ktp'] = Kecamatan::find($param['dataUmumNasabah']->id_kecamatan)->kecamatan;
            $param['kab_ktp'] = Kabupaten::find($param['dataUmumNasabah']->id_kabupaten)->kabupaten;
            $param['desa_ktp'] = Desa::find($param['dataUmumNasabah']->id_desa)->desa;
            $param['skema'] = $param['dataUmumNasabah']->skema_kredit;
            $dokumenUsaha = DB::table('item')
                ->where('nama', 'LIKE', '%NIB%')
                ->orWhere('nama', 'LIKE', '%Surat Keterangan Usaha%')
                ->orWhere('nama', 'LIKE', '%NPWP%')
                ->pluck('id');
            $countDoc = DB::table('jawaban_text')
                    ->where('id_pengajuan', $id)
                    ->whereIn('id_jawaban', $dokumenUsaha)
                    ->count();

            $param['countIjin'] = $countDoc;

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
                $param['role'] = $role;
            } else {
                $param['role'] = '';
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
                        }
                        else {
                            $dari = "Pincab";
                        }
                    }
                } else if ($role == 'PBO') {
                    if ($userPBP) {
                        $dari = "PBP";
                    } else {
                        $dari = "Pincab";
                    }
                } else if($role == 'PBP'){
                    $dari = "Pincab";
                } else {
                    $dari = '';
                }
            }
            $param['dari'] = $dari;

            $param['pendapat'] = $this->repo->getAlasanPengembalian($id, $ke);
            return view('new-pengajuan.detail-pengajuan-jawaban', $param);
        } else {
            return redirect()->back()->withError('Tidak memiliki hak akses.');
        }
    }
    // insert komentar
    public function getInsertKomentar(Request $request)
    {
        $role = Auth::user()->role;
        if ($role == 'Penyelia Kredit' || $role == 'PBO' || $role == 'PBP') {
            try {
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
                $average = ($sum_select) / (count($request->skor_penyelia) - $totalDataNull);
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

                // Log Pengajuan review
                $nasabah = CalonNasabah::select('id', 'nama')->where('id_pengajuan', $updateData->id)->first();
                $namaNasabah = 'undifined';

                if ($nasabah)
                    $namaNasabah = $nasabah->nama;

                $this->logPengajuan->store($role . ' dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' melakukan review terhadap pengajuan atas nama ' . $namaNasabah, $updateData->id, Auth::user()->id, Auth::user()->nip);

                event(new EventMonitoring('review pengajuan'));

                return redirect()->route('pengajuan-kredit.index')->withStatus('Berhasil Mereview');
            } catch (Exception $e) {
                // return $e;
                return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
            } catch (QueryException $e) {
                // return $e;
                return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
            }
        } else {
            return redirect()->back()->withError('Tidak memiliki hak akses.');
        }

        // return $request->skor_penyelia;
    }

    // check status penyelia data pengajuan
    public function checkPenyeliaKredit(Request $request)
    {
        try {
            $statusPenyelia = PengajuanModel::find($request->id_pengajuan);
            if ($statusPenyelia) {
                $statusPenyelia->posisi = "Review Penyelia";
                $statusPenyelia->id_penyelia = $request->select_penyelia;
                if ($statusPenyelia->tanggal_review_penyelia == null) {
                    $statusPenyelia->tanggal_review_penyelia = date(now());
                }
                $statusPenyelia->update();

                // Log Pengajuan melanjutkan dan mendapatkan
                $nasabah = CalonNasabah::select('id', 'nama')->where('id_pengajuan', $request->id_pengajuan)->first();
                $namaNasabah = 'undifined';
                if ($nasabah)
                    $namaNasabah = $nasabah->nama;

                $penyelia = User::find($request->select_penyelia);
                $this->logPengajuan->store('Staff dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menindak  lanjuti pengajuan atas nama ' . $namaNasabah . ' ke penyelia dengan NIP ' . $penyelia->nip . ' atas nama ' . $this->getNameKaryawan($penyelia->nip) . ' .', $statusPenyelia->id, Auth::user()->id, Auth::user()->nip);
                $this->logPengajuan->store('Penyelia dengan NIP ' . $penyelia->nip . ' atas nama ' . $this->getNameKaryawan($penyelia->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' dari staf dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . '.', $statusPenyelia->id, $penyelia->id, $penyelia->nip);
                return redirect()->back()->withStatus('Berhasil mengganti posisi.');
            } else {
                return back()->withError('Data pengajuan tidak ditemukan.');
            }
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan');
        }
    }

    // check status pincab
    public function checkPincab($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $pengajuan = PengajuanModel::find($id);
            if ($pengajuan->skema_kredit == 'Dagulir') {
                $dagulir = PengajuanDagulir::where('id', $pengajuan->dagulir_id)->first();
                $namaNasabah = $dagulir->nama;
            } else {
                $nasabah = CalonNasabah::select('id', 'nama')->where('id_pengajuan', $id)->first();
                if ($nasabah)
                    $namaNasabah = $nasabah->nama;
            }

            if (auth()->user()->role == 'Penyelia Kredit') {
                if (auth()->user()->id_cabang == '1') {
                    $dataPenyelia = PengajuanModel::find($id);
                    $status = $dataPenyelia->status;
                    $to = $request->to;
                    if ($to == 'pbo') {
                        $userPBO = User::select('id', 'nip')
                            ->where('id_cabang', $dataPenyelia->id_cabang)
                            ->whereNotNull('nip')
                            ->where('role', 'PBO')
                            ->first();

                        if ($userPBO) {
                            if ($status != null) {
                                $dataPenyelia->id_pbo = $userPBO->id;
                                $dataPenyelia->tanggal_review_pbo = date(now());
                                $dataPenyelia->posisi = "PBO";
                                $dataPenyelia->update();
                                DB::commit();
                                // Log Pengajuan melanjutkan pbo dan mendapatkan
                                $this->logPengajuan->store('Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menindak  lanjuti pengajuan atas nama ' . $namaNasabah . ' ke PBO dengan NIP ' . $userPBO->nip . ' atas nama ' . $this->getNameKaryawan($userPBO->nip) . ' .', $id, Auth::user()->id, Auth::user()->nip);
                                $this->logPengajuan->store('PBO dengan NIP ' . $userPBO->nip . ' atas nama ' . $this->getNameKaryawan($userPBO->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' dari Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . '.', $id, $userPBO->id, $userPBO->nip);
                            } else {
                                DB::rollBack();
                                alert()->error('gagal', 'Belum di review Penyelia.');
                                return redirect()->back()->withError('Belum di review Penyelia.');
                            }
                        } else {
                            DB::rollBack();
                            alert()->error('gagal', 'User PBO tidak ditemukan pada cabang ini');
                            return back()->withError('User pbo tidak ditemukan pada cabang ini.');
                        }
                    } else if ($to == 'pbp') {
                        if ($status != null) {
                            $userPBP = User::select('id', 'nip')
                                ->where('id_cabang', $dataPenyelia->id_cabang)
                                ->where('role', 'PBP')
                                ->whereNotNull('nip')
                                ->first();
                            if ($userPBP) {
                                $dataPenyelia->id_pbp = $userPBP->id;
                                $dataPenyelia->tanggal_review_pbp = date(now());
                                $dataPenyelia->posisi = "PBP";
                                $dataPenyelia->update();
                                DB::commit();
                                // Log Pengajuan melanjutkan PBP dan mendapatkan
                                $this->logPengajuan->store('Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menindak  lanjuti pengajuan atas nama ' . $namaNasabah . ' ke PBP dengan NIP ' . $userPBP->nip . ' atas nama ' . $this->getNameKaryawan($userPBP->nip) . ' .', $id, Auth::user()->id, Auth::user()->nip);
                                $this->logPengajuan->store('PBP dengan NIP ' . $userPBP->nip . ' atas nama ' . $this->getNameKaryawan($userPBP->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' dari Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . '.', $id, $userPBP->id, $userPBP->nip);
                            } else {
                                DB::rollBack();
                                alert()->error('gagal', 'User PBP tidak ditemukan pada cabang ini');
                                return back()->withError('User pbp tidak ditemukan pada cabang ini.');
                            }
                        } else {
                            DB::rollBack();
                            alert()->error('gagal', 'Belum di review Penyelia');
                            return redirect()->back()->withError('Belum di review Penyelia.');
                        }
                    } else if ($to == 'pincab') {
                        if ($status != null) {
                            $userPincab = User::select('id', 'nip')
                                ->where('id_cabang', $dataPenyelia->id_cabang)
                                ->where('role', 'Pincab')
                                ->whereNotNull('nip')
                                ->first();
                            if ($userPincab) {
                                $dataPenyelia->id_pincab = $userPincab->id;
                                $dataPenyelia->tanggal_review_pbp = date(now());
                                $dataPenyelia->posisi = "Pincab";
                                $dataPenyelia->update();
                                DB::commit();
                                // Log Pengajuan melanjutkan PBP dan mendapatkan
                                $this->logPengajuan->store('Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menindak  lanjuti pengajuan atas nama ' . $namaNasabah . ' ke Pincab dengan NIP ' . $userPincab->nip . ' atas nama ' . $this->getNameKaryawan($userPincab->nip) . ' .', $id, Auth::user()->id, Auth::user()->nip);

                                $this->logPengajuan->store('Pincab dengan NIP ' . $userPincab->nip . ' atas nama ' . $this->getNameKaryawan($userPincab->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' dari Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . '.', $id, $userPincab->id, $userPincab->nip);
                            } else {
                                DB::rollBack();
                                alert()->error('gagal', 'User pincab tidak ditemukan pada cabang ini.');
                                return back()->withError('User pincab tidak ditemukan pada cabang ini.');
                            }
                        } else {
                            DB::rollBack();
                            alert()->error('gagal', 'Belum di review Penyelia');
                            return redirect()->back()->withError('Belum di review Penyelia.');
                        }
                    }
                } else {
                    $dataPenyelia = PengajuanModel::find($id);
                    $status = $dataPenyelia->status;
                    $to = $request->to;

                    if ($to == 'pbo') {
                        $userPBO = User::select('id', 'nip')
                            ->where('id_cabang', $dataPenyelia->id_cabang)
                            ->whereNotNull('nip')
                            ->where('role', 'PBO')
                            ->first();
                        if ($userPBO) {
                            if ($status != null) {
                                $dataPenyelia->id_pbo = $userPBO->id;
                                $dataPenyelia->tanggal_review_pbo = date(now());
                                $dataPenyelia->posisi = "PBO";
                                $dataPenyelia->update();
                                DB::commit();
                                // Log Pengajuan melanjutkan pbo dan mendapatkan
                                $this->logPengajuan->store('Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menindak  lanjuti pengajuan atas nama ' . $namaNasabah . ' ke PBO dengan NIP ' . $userPBO->nip . ' atas nama ' . $this->getNameKaryawan($userPBO->nip) . ' .', $id, Auth::user()->id, Auth::user()->nip);
                                $this->logPengajuan->store('PBO dengan NIP ' . $userPBO->nip . ' atas nama ' . $this->getNameKaryawan($userPBO->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' dari Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . '.', $id, $userPBO->id, $userPBO->nip);
                            } else {
                                DB::rollBack();
                                alert()->error('gagal', 'Belum di review Penyelia');
                                return redirect()->back()->withError('Belum di review Penyelia.');
                            }
                        }
                    } else {
                        if ($status != null) {
                            $userPincab = User::select('id', 'nip')
                                ->where('id_cabang', $dataPenyelia->id_cabang)
                                ->whereNotNull('nip')
                                ->where('role', 'Pincab')
                                ->first();
                            if ($userPincab) {
                                $dataPenyelia->id_pincab = $userPincab->id;
                                $dataPenyelia->tanggal_review_pincab = date(now());
                                $dataPenyelia->posisi = "Pincab";
                                $dataPenyelia->update();
                                DB::commit();
                                // Log Pengajuan melanjutkan PINCAB dan mendapatkan
                                $pincab = User::find($userPincab->id);
                                $this->logPengajuan->store('Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menindak  lanjuti pengajuan atas nama ' . $namaNasabah . ' ke Pincab dengan NIP ' . $pincab->nip . ' atas nama ' . $this->getNameKaryawan($pincab->nip) . ' .', $id, Auth::user()->id, Auth::user()->nip);
                                $this->logPengajuan->store('Pincab dengan NIP ' . $pincab->nip . ' atas nama ' . $this->getNameKaryawan($pincab->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' dari Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . '.', $id, $userPincab->id, $userPincab->nip);
                            } else {
                                DB::rollBack();
                                alert()->error('gagal', 'User Pincab tidak ditemukan di cabang ini.');
                                return back()->withError('User pincab tidak ditemukan di cabang ini.');
                            }
                        } else {
                            DB::rollBack();
                            alert()->error('gagal', 'Belum di review Penyelia.');
                            return redirect()->back()->withError('Belum di review Penyelia.');
                        }
                    }
                }
            } elseif (auth()->user()->role == 'PBO') {
                $dataPenyelia = PengajuanModel::find($id);
                $status = $dataPenyelia->average_by_pbo;
                // return $status;

                if ($pengajuan->tanggal_review_penyelia
                && $pengajuan->id_pbo) {
                    if ($status != null) {
                        $userPBP = User::select('id', 'nip')
                            ->where('id_cabang', $dataPenyelia->id_cabang)
                            ->where('role', 'PBP')
                            ->whereNotNull('nip')
                            ->first();
                            $userPincab = User::select('id', 'nip')
                            ->where('id_cabang', $dataPenyelia->id_cabang)
                            ->whereNotNull('nip')
                            ->where('role', 'Pincab')
                            ->first();
                        if ($userPBP) {
                            $dataPenyelia->id_pbp = $userPBP->id;
                            $dataPenyelia->tanggal_review_pbp = date(now());
                            $dataPenyelia->posisi = "PBP";
                            $dataPenyelia->update();
                            DB::commit();
                            // Log Pengajuan melanjutkan PBP dan mendapatkan
                            $this->logPengajuan->store('PBO dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menindak  lanjuti pengajuan atas nama ' . $namaNasabah . ' ke PBP dengan NIP ' . $userPBP->nip . ' atas nama ' . $this->getNameKaryawan($userPBP->nip) . ' .', $id, Auth::user()->id, Auth::user()->nip);
                            $this->logPengajuan->store('PBP dengan NIP ' . $userPBP->nip . ' atas nama ' . $this->getNameKaryawan($userPBP->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' dari PBO dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . '.', $id, $userPBP->id, $userPBP->nip);
                        } elseif (!$userPBP) {
                            $dataPenyelia->id_pincab = $userPincab->id;
                            $dataPenyelia->tanggal_review_pincab = date(now());
                            $dataPenyelia->posisi = "Pincab";
                            $dataPenyelia->update();
                            DB::commit();
                            $this->logPengajuan->store('PBO dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menindak  lanjuti pengajuan atas nama ' . $namaNasabah . ' ke Pincab dengan NIP ' . $userPincab->nip . ' atas nama ' . $this->getNameKaryawan($userPincab->nip) . ' .', $id, Auth::user()->id, Auth::user()->nip);
                            $this->logPengajuan->store('Pincab dengan NIP ' . $userPincab->nip . ' atas nama ' . $this->getNameKaryawan($userPincab->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' dari PBO dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . '.', $id, $userPincab->id, $userPincab->nip);
                        } else {
                            DB::rollBack();
                            alert()->error('gagal', 'User PBP tidak ditemukan pada cabang ini.');
                            return back()->withError('User pbp tidak ditemukan pada cabang ini.');
                        }
                    } else {
                        DB::rollBack();
                        alert()->error('gagal', 'Belum di review PBO.');
                        return redirect()->back()->withError('Belum di review PBO.');
                    }
                } else {

                    if ($status != null) {
                        $userPincab = User::select('id', 'nip')
                            ->where('id_cabang', $dataPenyelia->id_cabang)
                            ->whereNotNull('nip')
                            ->where('role', 'Pincab')
                            ->first();
                        if ($userPincab) {
                            $dataPenyelia->id_pincab = $userPincab->id;
                            $dataPenyelia->tanggal_review_pincab = date(now());
                            $dataPenyelia->posisi = "Pincab";
                            $dataPenyelia->update();
                            DB::rollBack();
                            // Log Pengajuan melanjutkan PBP dan mendapatkan
                            $this->logPengajuan->store('PBO dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menindak  lanjuti pengajuan atas nama ' . $namaNasabah . ' ke Pincab dengan NIP ' . $userPincab->nip . ' atas nama ' . $this->getNameKaryawan($userPincab->nip) . ' .', $id, Auth::user()->id, Auth::user()->nip);
                            $this->logPengajuan->store('Pincab dengan NIP ' . $userPincab->nip . ' atas nama ' . $this->getNameKaryawan($userPincab->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' dari PBO dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . '.', $id, $userPincab->id, $userPincab->nip);
                        } else {
                            DB::rollBack();
                            alert()->error('gagal', 'User Pincab tidak ditemukan pada cabang ini.');
                            return back()->withError('User pincab tidak ditemukan pada cabang ini.');
                        }
                    } else {
                        DB::rollBack();
                        alert()->error('gagal', 'Belum di review PBO');
                        return redirect()->back()->withError('Belum di review PBO.');
                    }
                }

            } elseif (auth()->user()->role == 'PBP') {
                $dataPenyelia = PengajuanModel::find($id);
                $status = $dataPenyelia->average_by_pbp;
                if ($status != null) {
                    $userPincab = User::select('id', 'nip')
                        ->where('id_cabang', $dataPenyelia->id_cabang)
                        ->whereNotNull('nip')
                        ->where('role', 'Pincab')
                        ->first();
                    if ($userPincab) {
                        $dataPenyelia->id_pincab = $userPincab->id;
                        $dataPenyelia->tanggal_review_pincab = date(now());
                        $dataPenyelia->posisi = "Pincab";
                        $dataPenyelia->update();
                        DB::commit();
                        // Log Pengajuan melanjutkan PINCAB dan mendapatkan
                        $this->logPengajuan->store('Pengguna ' . Auth::user()->name . ' menindak lanjuti pengajuan atas nama ' . $namaNasabah . ' ke PINCAB dengan NIP ' . $userPincab->nip . ' atas nama ' . $this->getNameKaryawan($userPincab->nip) . '.', $id, Auth::user()->id, Auth::user()->nip);
                        $this->logPengajuan->store('PINCAB dengan NIP ' . $userPincab->nip . ' atas nama ' . $this->getNameKaryawan($userPincab->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' untuk direview.', $id, $userPincab->id, $userPincab->nip);
                    } else {
                        DB::rollBack();
                        alert()->error('gagal', 'User Pincab tidak ditemukan pada cabang ini.');
                        return back()->withError('User pincab tidak ditemukan pada cabang ini.');
                    }
                } else {
                    DB::rollBack();
                    alert()->error('gagal', 'Belum di review PBP');
                    return redirect()->back()->withError('Belum di review PBP.');
                }
            } else {
                DB::rollBack();
                alert()->error('gagal', 'Tidak memilik hak akses');
                return redirect()->back()->withError('Tidak memiliki hak akses.');
            }
            DB::commit();

            alert()->success('Berhasil','Berhasil mengganti posisi.');
            return redirect()->back()->withStatus('Berhasil mengganti posisi.');
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
            return back()->withError('Terjadi kesalahan');
        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
            DB::rollBack();
            return back()->withError('Terjadi kesalahan pada database');
        }
    }
    // check status pincab
    public function checkPincabStatus()
    {
        if (auth()->user()->role == "Pincab") {
            $param['pageTitle'] = "Dashboard";
            $id_cabang = Auth::user()->id_cabang;
            $param['data_pengajuan'] = PengajuanModel::select(
                'pengajuan.id',
                'pengajuan.tanggal',
                'pengajuan.posisi',
                'pengajuan.status',
                'pengajuan.status_by_sistem',
                'pengajuan.id_cabang',
                'pengajuan.average_by_sistem',
                'pengajuan.average_by_penyelia',
                'calon_nasabah.nama',
                'calon_nasabah.jenis_usaha',
                'calon_nasabah.id_pengajuan'
            )
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->where('pengajuan.id_cabang', $id_cabang)
                ->get();
            return view('pengajuan-kredit.komentar-pincab-pengajuan', $param);
        } else {
            return redirect()->back()->withError('Tidak memiliki hak akses.');
        }
    }
    public function checkPincabStatusDetail($id)
    {
        Session::put('is_dagulir', false);
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

        $param['dataNasabah'] = CalonNasabah::select('calon_nasabah.*', 'kabupaten.id as kabupaten_id', 'kabupaten.kabupaten', 'kecamatan.id as kecamatan_id', 'kecamatan.id_kabupaten', 'kecamatan.kecamatan', 'desa.id as desa_id', 'desa.id_kabupaten', 'desa.id_kecamatan', 'desa.desa')
                ->join('kabupaten', 'kabupaten.id', 'calon_nasabah.id_kabupaten')
                ->join('kecamatan', 'kecamatan.id', 'calon_nasabah.id_kecamatan')
                ->join('desa', 'desa.id', 'calon_nasabah.id_desa')
                ->where('calon_nasabah.id_pengajuan', $id)
                ->first();
        // return $param['dataNasabah'];
        $param['dataKecamatan'] = Kecamatan::find($param['dataNasabah']->id_kecamatan);
        $param['dataKabupaten'] = Kabupaten::find($param['dataNasabah']->id_kabupaten);
        $param['dataDesa'] = Desa::find($param['dataNasabah']->id_desa);

        $dataUmum =  PengajuanModel::select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang', 'pengajuan.skema_kredit', 'pengajuan.average_by_sistem', 'pengajuan.average_by_penyelia', 'pengajuan.average_by_pbo', 'pengajuan.average_by_pbp', 'pengajuan.id_pbo', 'pengajuan.id_pbp')
        ->find($id);
        $param['dataUmum'] = $dataUmum;

        $param['comment'] = KomentarModel::where('id_pengajuan', $id)->first();

        $param['alasanPengembalian'] = AlasanPengembalianData::where('id_pengajuan', $id)
                                                            ->join('users', 'users.id', 'alasan_pengembalian_data.id_user')
                                                            ->select('users.nip', 'alasan_pengembalian_data.*')
                                                            ->get();
        $param['pendapatDanUsulan'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_staff', 'komentar_penyelia', 'komentar_pincab', 'komentar_pbo', 'komentar_pbp')->first();
        // return $param['dataUmum'];
        // return $param['pendapatDanUsulan'];
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
        $param['pendapatDanUsulanStaf'] = KomentarModel::select('komentar_staff')->where('id_pengajuan', $id)->first();
        $param['pendapatDanUsulanPenyelia'] = KomentarModel::where('id_pengajuan', $id)->select('komentar_penyelia')->first();
        if ($param['dataUmum']->skema_kredit == 'KKB') {
            $param['dataPO'] = DB::table('data_po')
                ->where('id_pengajuan', $id)
                ->first();
        }
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

        $param['userLogStaff'] = DB::table('pengajuan')
                    ->select('users.name', 'users.email', 'users.nip')
                    ->join('users', 'pengajuan.id_staf', 'users.id')
                    ->where('pengajuan.id', $id)
                    ->first();
        $param['userLogPenyelia'] = DB::table('pengajuan')
                    ->select('users.name', 'users.email', 'users.nip')
                    ->join('users', 'pengajuan.id_penyelia', 'users.id')
                    ->where('pengajuan.id', $id)
                    ->first();
        $param['userLogPBO'] = DB::table('pengajuan')
                    ->select('users.name', 'users.email', 'users.nip')
                    ->join('users', 'pengajuan.id_pbo', 'users.id')
                    ->where('pengajuan.id', $id)
                    ->first();
        $param['userLogPBP'] = DB::table('pengajuan')
                    ->select('users.name', 'users.email', 'users.nip')
                    ->join('users', 'pengajuan.id_pbp', 'users.id')
                    ->where('pengajuan.id', $id)
                    ->first();
        $param['userLogPincab'] = DB::table('pengajuan')
                    ->select('users.name', 'users.email', 'users.nip')
                    ->join('users', 'pengajuan.id_pincab', 'users.id')
                    ->where('pengajuan.id', $id)
                    ->first();

        $param['logPengajuan'] = $log;
        $param['rolesPemroses'] = $this->repo->getDataPemroses($dataUmum);
        return view('pengajuan-kredit.detail-komentar-pengajuan', $param);
    }
    public function checkPincabStatusDetailPost($id, Request $request)
    {
        if (auth()->user()->role == 'Pincab') {
            try {
                $idKomentar = KomentarModel::where('id_pengajuan', $id)->first();
                KomentarModel::where('id', $idKomentar->id)->update(
                    [
                        'komentar_pincab' => $request->pendapat,
                        'id_pincab' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );

                // Log Pengajuan review
                $nasabah = CalonNasabah::select('id', 'nama')->where('id_pengajuan', $id)->first();
                $nasabah->nominal_realisasi = $request->get('nominal_realisasi');
                $nasabah->jangka_waktu_realisasi = $request->get('jangka_waktu_disetujui');
                $nasabah->update();

                $namaNasabah = 'undifined';

                $pengajuan = PengajuanModel::find($id);
                $pengajuan->id_pincab = auth()->user()->id;
                $pengajuan->tanggal_review_pincab = date('Y-m-d');
                $pengajuan->posisi = "Selesai";
                $pengajuan->tanggal_review_pincab = date(now());
                $pengajuan->update();

                if ($nasabah)
                    $namaNasabah = $nasabah->nama;

                $this->logPengajuan->store('Pincab dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' melakukan review terhadap pengajuan atas nama ' . $namaNasabah, $id, Auth::user()->id, Auth::user()->nip);
                alert()->success('Berhasil','Berhasil menyetujui pengajuan.');
                return redirect('/pengajuan-kredit')->withStatus('Berhasil menambahkan komentar');
            } catch (Exception $e) {
                return $e;
                return redirect()->back()->withError('Terjadi kesalahan.');
            } catch (QueryException $e) {
                return $e;
                return redirect()->back()->withError('Terjadi kesalahan');
            }
        } else {
            alert()->error('Gagal', 'Tidak memiliki hak akses.');
            return redirect()->back()->withError('Tidak memiliki hak akses.');
        }

    }
    public function checkPincabStatusChange($id,$request)
    {
        $statusPincab = PengajuanModel::find($id);
        if (auth()->user()->role == 'Pincab') {
            $idKomentar = KomentarModel::where('id_pengajuan', $id)->first();
            KomentarModel::where('id', $idKomentar->id)->update(
                [
                    'komentar_pincab' => $request->pendapat,
                    'id_pincab' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
            $statusPincab->posisi = "Selesai";
            $statusPincab->tanggal_review_pincab = date(now());
            $statusPincab->update();

            $nasabah = CalonNasabah::select('id', 'nama')->where('id_pengajuan', $id)->first();
            $namaNasabah = 'undifined';
            if ($nasabah)
                $namaNasabah = $nasabah->nama;

            $this->logPengajuan->store('Pincab dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menyetujui pengajuan atas nama ' . $namaNasabah . '.', $id, Auth::user()->id, Auth::user()->nip);

            event(new EventMonitoring('menyetujui pengajuan'));

            return redirect()->back()->withStatus('Berhasil mengganti posisi.');

        } else {
            return redirect()->back()->withError('Tidak memiliki hak akses.');
        }
    }
    public function checkPincabStatusChangeTolak($id, Request $request)
    {
        $statusPincab = PengajuanModel::find($id);
        $komentarPincab = KomentarModel::where('id_pengajuan', $id)->first();
        if (auth()->user()->role == 'Pincab') {
            $statusPincab->posisi = "Ditolak";
            $statusPincab->tanggal_review_pincab = date(now());
            $statusPincab->update();

            $idKomentar = KomentarModel::where('id_pengajuan', $id)->first();
            KomentarModel::where('id', $idKomentar->id)->update(
                [
                    'komentar_pincab' => $request->pendapat,
                    'id_pincab' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );

            $nasabah = CalonNasabah::select('id', 'nama')->where('id_pengajuan', $id)->first();
            $namaNasabah = 'undifined';
            if ($nasabah)
                $namaNasabah = $nasabah->nama;

            $this->logPengajuan->store('Pincab dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' menolak pengajuan atas nama ' . $namaNasabah . '.', $id, Auth::user()->id, Auth::user()->nip);
            event(new EventMonitoring('tolak pengajuan'));
            alert()->success('Berhasil','Berhasil menolak pengajuan.');
            return redirect('/pengajuan-kredit')->withStatus('Berhasil menambahkan komentar');
        } else {
            return 'qwtqt';
            return redirect()->back()->withError('Tidak memiliki hak akses.');
        }
    }
    // check status staf analisa
    public function checkStafAnalisa($id)
    {
        if (auth()->user()->role == 'Staf Analis Kredit ') {
            $statusPenyelia = PengajuanModel::find($id);
            $statusPenyelia->posisi = "Review Penyelia";
            $statusPenyelia->update();
            return redirect()->back()->withStatus('Berhasil mengganti posisi.');
        } else {
            return redirect()->back()->withError('Tidak memiliki hak akses.');
        }
    }

    public function storeAspekPenyelia(Request $request)
    {
        DB::beginTransaction();
        try {

            // pendapat penyelia
            foreach ($request->get('id_aspek') as $key => $value) {
                $addPendapat = new PendapatPerAspek;
                $addPendapat->id_pengajuan = $request->get('id_pengajuan');
                $addPendapat->id_penyelia = Auth::user()->id;
                $addPendapat->id_aspek = $value;
                $addPendapat->pendapat_per_aspek = $request->get('pendapat_per_aspek')[$key];
                $addPendapat->save();
            }

            // komentar penyelia
            $idKomentar = KomentarModel::where('id_pengajuan', $request->get('id_pengajuan'))->first();
            foreach ($request->id_item as $key => $value) {
                $addDetailKomentar = new DetailKomentarModel;
                $addDetailKomentar->id_komentar = $idKomentar->id;
                $addDetailKomentar->id_user = Auth::user()->id;
                $addDetailKomentar->id_item = $value;
                $addDetailKomentar->komentar = $_POST['komentar_penyelia'][$key];
                $addDetailKomentar->save();
            }
            KomentarModel::where('id', $idKomentar->id)->update(
                [
                    'komentar_penyelia' => $request->get('komentar_penyelia_keseluruhan'),
                    'id_penyelia' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan' . $e->getMessage());
        }
    }

    public function partial(Request $request)
    {
        $find = array('.');
        DB::beginTransaction();
        try {
            $addPengajuan = new PengajuanModel;
            $addPengajuan->tanggal = date(now());
            $addPengajuan->progress_pengajuan_data = $request->progress;
            $addPengajuan->save();
            $id_pengajuan = $addPengajuan->id;

            $addData = new CalonNasabah;
            $addData->nama = $request->name;
            $addData->alamat_rumah = $request->alamat_rumah;
            $addData->alamat_usaha = $request->alamat_usaha;
            $addData->no_ktp = $request->no_ktp;
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
            $addData->skema_kredit = $request->skema_kredit;
            $addData->save();
            $id_calon_nasabah = $addData->id;

            //untuk jawaban yg teks, number, persen, long text
            foreach ($request->id_level as $key => $value) {
                $dataJawabanText = new JawabanTextModel;
                $dataJawabanText->id_pengajuan = $id_pengajuan;
                $dataJawabanText->id_jawaban = $request->get('id_level')[$key];
                $dataJawabanText->opsi_text = str_replace($find, '', $request->get('informasi')[$key]);
                // $dataJawabanText->opsi_text = $request->get('informasi')[$key] == null ? '-' : $request->get('informasi')[$key];
                $dataJawabanText->save();
            }
        } catch (Exception $e) {
            DB::rollBack();
            // return $e->getMessage();
            return redirect()->route('pengajuan-kredit.index')->withError('Terjadi kesalahan.' . $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            // return $e->getMessage();
            return redirect()->route('pengajuan-kredit.index')->withError('Terjadi kesalahan' . $e->getMessage());
        }
    }

    public function backToInputProses($id)
    {
        try {
            $statusPenyelia = PengajuanModel::find($id);
            $statusPenyelia->posisi = "Proses Input Data";
            $statusPenyelia->update();

            // Log Pengajuan Kembalikan ke staff
            $firstLog = LogPengajuan::select('user_id', 'nip')->where('id_pengajuan', $id)->orderBy('id')->first();
            if ($firstLog) {
                $nasabah = CalonNasabah::select('id', 'nama')->where('id_pengajuan', $id)->first();
                $namaNasabah = 'undifined';
                if ($nasabah)
                    $namaNasabah = $nasabah->nama;

                $this->logPengajuan->store('Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . ' mengembalikan data pengajuan atas nama ' . $namaNasabah . ' kepada staf dengan NIP ' . $firstLog->nip . ' atas nama ' . $this->getNameKaryawan($firstLog->nip) . '.', $id, Auth::user()->id, Auth::user()->nip);

                $this->logPengajuan->store('Staff dengan NIP ' . $firstLog->nip . ' atas nama ' . $this->getNameKaryawan($firstLog->nip) . ' menerima data pengajuan atas nama ' . $namaNasabah . ' dari Penyelia dengan NIP ' . Auth::user()->nip . ' atas nama ' . $this->getNameKaryawan(Auth::user()->nip) . '.', $id, $firstLog->user_id, $firstLog->nip);
            }

            return redirect()->back()->withStatus('Berhasil mengganti posisi.');

            return redirect()->back()->withStatus('Berhasil mengganti posisi.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan');
        }
    }

    public function tempNasabah(Request $request)
    {
        if (isset($request->id_nasabah)) {
            $nasabah = TemporaryService::saveNasabah(
                $request->id_nasabah,
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
                $dataSlik = $this->getDataLevel($value);
                $cek = DB::table('jawaban_temp')
                    ->where('id_temporary_calon_nasabah', $request->id_nasabah ?? $nasabah->id)
                    ->where('id_jawaban', $dataSlik[1])
                    ->count('id');
                if ($cek < 1) {
                    DB::table('jawaban_temp')
                        ->insert([
                            'id_temporary_calon_nasabah' => $request->id_nasabah ?? $nasabah->id,
                            'id_jawaban' => $dataSlik[1],
                            'skor' => $dataSlik[0],
                            'id_option' => $key,
                            'created_at' => now()
                        ]);
                } else {
                    DB::table('jawaban_temp')
                        ->where('id_temporary_calon_nasabah', $request->id_nasabah ?? $nasabah->id)
                        ->where('id_option', $key)
                        ->update([
                            'id_jawaban' => $dataSlik[1],
                            'skor' => $dataSlik[0],
                            'updated_at' => now()
                        ]);
                }
            }
        } catch (\Exception $e) {
        }

        return response()->json([
            'status' => 'ok',
            'code' => 200,
            'data' => $nasabah,
        ]);
    }

    public function saveDataPOTemp(Request $request)
    {
        try {
            $find = array('Rp ', '.');

            if ($request->id_data_po_temp) {
                $po = DB::table('data_po_temp')->where('id', $request->id_data_po_temp)->update([
                    'id_calon_nasabah_temp' => $request->id_calon_nasabah,
                    'tahun_kendaraan' => $request->tahun,
                    'merk' => $request->merk,
                    'tipe' => $request->tipe_kendaraan,
                    'warna' => $request->warna,
                    'keterangan' => 'Pemesanan ' . $request->pemesanan,
                    'jumlah' => $request->sejumlah,
                    'harga' => str_replace($find, '', $request->harga)
                ]);
            } else {
                $po = DB::table('data_po_temp')->insertGetId([
                    'id_calon_nasabah_temp' => $request->id_calon_nasabah,
                    'tahun_kendaraan' => $request->tahun,
                    'merk' => $request->merk,
                    'tipe' => $request->tipe_kendaraan,
                    'warna' => $request->warna,
                    'keterangan' => 'Pemesanan ' . $request->pemesanan,
                    'jumlah' => $request->sejumlah,
                    'harga' => str_replace($find, '', $request->harga)
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }

        return response()->json([
            'status' => 'ok',
            'code' => 200,
            'data' => $po,
        ]);
    }

    public function tempFile(Request $request)
    {
        $nasabah = CalonNasabahTemp::findOrFail($request->id_calon_nasabah);

        $data = TemporaryService::saveFile(
            $nasabah,
            $request->answer_id,
            $request->file_id,
            $request->file('file')
        );

        return response()->json([
            'statusCode' => 200,
            'data' => $data,
        ]);
    }

    public function delTempFile(Request $request)
    {
        $deleted = TemporaryService::delFile(
            JawabanTemp::find($request->answer_id)
        );

        if ($deleted) {
            return response()->json([
                'statusCode' => 200,
                'deleted' => true,
                'message' => 'Success delete the file',
            ]);
        }

        return response()->json([
            'statusCode' => 200,
            'deleted' => false,
            'message' => 'No file deleted',
        ]);
    }

    public function tempJawaban(Request $request)
    {
        $find = array('Rp ', '.');

        try {
            if ($request->kategori_jaminan_tambahan != null) {
                DB::table('temporary_calon_nasabah')
                    ->where('id', $request->idCalonNasabah)
                    ->update([
                        'jaminan_tambahan' => $request->kategori_jaminan_tambahan
                    ]);
            }
            foreach ($request->id_level as $key => $value) {
                $cekData = DB::table('temporary_jawaban_text')
                    ->where('id_temporary_calon_nasabah', $request->idCalonNasabah)
                    ->where('id_jawaban', $value)
                    ->first();
                if (!$cekData) {
                    $dataJawabanText = new JawabanTemp();
                    $dataJawabanText->id_temporary_calon_nasabah = $request->get('idCalonNasabah');
                    $dataJawabanText->id_jawaban = $request->get('id_level')[$key];
                    $dataJawabanText->id_temporary_calon_nasabah = $request->idCalonNasabah;

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
                        ->where('id_temporary_calon_nasabah', $request?->idCalonNasabah)
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
                        ->where('id_temporary_calon_nasabah', $request->idCalonNasabah)
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
                        ->where('id_temporary_calon_nasabah', $request->idCalonNasabah)
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
                        $data_level_dua = $this->getDataLevel($value);
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
                                'id_temporary_calon_nasabah' => $request?->idCalonNasabah,
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
                        $data_level_tiga = $this->getDataLevel($value);
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
                                'id_temporary_calon_nasabah' => $request?->idCalonNasabah,
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
                        $data_level_empat = $this->getDataLevel($value);
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
                                'id_temporary_calon_nasabah' => $request?->idCalonNasabah,
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
                    ->where('id_temp', $request->idCalonNasabah)
                    ->where('id_aspek', $i)
                    ->count('id');
                if ($cekUsulan < 1) {
                    DB::table('temporary_usulan_dan_pendapat')
                        ->insert([
                            'id_temp' => $request->idCalonNasabah,
                            'id_aspek' => $i,
                            'usulan' => $val,
                            'created_at' => now()
                        ]);
                } else {
                    DB::table('temporary_usulan_dan_pendapat')
                        ->where('id_temp', $request->idCalonNasabah)
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
                    ->where('id_temporary_calon_nasabah', $request->idCalonNasabah)
                    ->where('id_jawaban', $finalArray[$i]['id_jawaban'])
                    ->count('id');

                if ($cekDataSelect < 1) {
                    JawabanTempModel::insert($finalArray[$i]);
                } else {
                    for ($i = 0; $i < count($finalArray); $i++) {
                        DB::table('jawaban_temp')
                            ->where('id_option', $finalArray[$i]['id_option'])
                            ->where('id_temporary_calon_nasabah', $request?->idCalonNasabah)
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
            'nasabah' => $request->idCalonNasabah,
            'aspek' => $request->pendapat_per_aspek,
            'all' => $request->all()
        ]);
    }

    public function draftPengajuanKredit()
    {
        $param['pageTitle'] = 'Tambah Pengajuan Kredit';
        $param['btnText'] = 'Tambah Pengajuan';
        $param['btnLink'] = route('pengajuan-kredit.create');
        $id_staf = auth()->user()->id;
        // $param['data_pengajuan'] = PengajuanModel::select(
        //     'pengajuan.id',
        //     'pengajuan.tanggal',
        //     'pengajuan.posisi',
        //     'pengajuan.progress_pengajuan_data',
        //     'pengajuan.tanggal_review_penyelia',
        //     'pengajuan.tanggal_review_pbp',
        //     'pengajuan.tanggal_review_pincab',
        //     'pengajuan.status',
        //     'pengajuan.status_by_sistem',
        //     'pengajuan.id_cabang',
        //     'pengajuan.average_by_sistem',
        //     'pengajuan.average_by_penyelia',
        //     'calon_nasabah.nama',
        //     'calon_nasabah.jenis_usaha',
        //     'calon_nasabah.id_pengajuan'
        // )
        //     ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
        //     ->where('pengajuan.id_cabang', auth()->user()->id_cabang)
        //     ->paginate(5);
        $param['data_pengajuan'] = CalonNasabahTemp::where('id_user', Auth::user()->id)
            ->whereNotNull('nama')
            ->where('id_user', $id_staf)
            ->paginate(5);

        return view('pengajuan-kredit.draft_index', $param);
    }

    public function continueDraft($id)
    {
        $nasabah = CalonNasabahTemp::findOrFail($id);
        $createRoute = route('pengajuan-kredit.continue-draft');
        // dd($createRoute);

        return redirect()->to($createRoute . "?tempId={$nasabah->id}&continue=true");
    }

    public function showContinueDraft(Request $request)
    {
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
        $param['duTemp'] = TemporaryService::getNasabahData($request->tempId);

        $data['dataPertanyaanSatu'] = ItemModel::select('id', 'nama', 'level', 'id_parent')->where('level', 2)->where('id_parent', 3)->get();
        $param['dataMerk'] = MerkModel::all();
        $param['dataPO'] = DB::table('data_po_temp')->where('id_calon_nasabah_temp', $request->tempId)->first();
        // return $param['dataPO'];

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

        // dump($param['dataAspek']);
        // dump($param['itemSlik']);
        // dump($param['itemSP']);
        // dump($param['dataPertanyaanSatu']);
        // dd($param['duTemp']);
        return view('pengajuan-kredit.continue-draft', $param);
    }

    public function deleteDraft($id)
    {
        DB::beginTransaction();
        try {
            DB::table('jawaban_temp')->where('id_temporary_calon_nasabah', $id)->delete();
            DB::table('temporary_jawaban_text')->where('id_temporary_calon_nasabah', $id)->delete();
            DB::table('temporary_usulan_dan_pendapat')->where('id_temp', $id)->delete();
            DB::table('temporary_calon_nasabah')->where('id', $id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withError('Terjadi kesalahan : ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->withError('Terjadi kesalahan pada database');
        }

        return back()->withStatus('Berhasil menghapus draft');
    }

    public function postFileKKBIndex($id)
    {
        $data['id'] = $id;
        $data['pageTitle'] = 'Upload File KKB';

        return view('pengajuan-kredit.upload-file', $data);
    }

    public function postFileKKB(Request $request, $id)
    {
        $kode_cabang = DB::table('cabang')
            ->join('pengajuan', 'pengajuan.id_cabang', 'cabang.id')
            ->where('pengajuan.id', $id)
            ->select('kode_cabang')
            ->first();

        try {
            $message = null;
            switch ($request->tipe_file) {
                    // File SPPK Handler
                case 'SPPK':
                    $message = 'file SPPK.';
                    $folderSPPK = public_path() . '/upload/' . $id . '/sppk/';
                    $fileSPPK = $request->sppk;
                    $filenameSPPK = date('YmdHis') . '.' . $fileSPPK->getClientOriginalExtension();
                    $pathSPPK = realpath($folderSPPK);
                    // If it exist, check if it's a directory
                    if (!($pathSPPK !== true and is_dir($pathSPPK))) {
                        // Path/folder does not exist then create a new folder
                        mkdir($folderSPPK, 0755, true);
                    }
                    $fileSPPK->move($folderSPPK, $filenameSPPK);
                    DB::table('pengajuan')
                        ->where('id', $id)
                        ->update([
                            'sppk' => $filenameSPPK
                        ]);
                    break;

                    // No PO Handler
                case 'PO':
                    // POST data kredit & PO to API Data Warehouse
                    try {
                        $message = 'nomor PO dan file PO.';
                        $po = $request->no_po;
                        DB::table('data_po')
                            ->where('id_pengajuan', $id)
                            ->update([
                                'no_po' => $po
                            ]);

                        // File PO Handler
                        $folderPO = public_path() . '/upload/' . $id . '/po/';
                        $filePO = $request->po;
                        $filenamePO = date('YmdHis') . '.' . $filePO->getClientOriginalExtension();
                        $pathPO = realpath($folderPO);
                        // If it exist, check if it's a directory
                        if (!($pathPO !== true and is_dir($pathPO))) {
                            // Path/folder does not exist then create a new folder
                            mkdir($folderPO, 0755, true);
                        }
                        $filePO->move($folderPO, $filenamePO);
                        DB::table('pengajuan')
                            ->where('id', $id)
                            ->update([
                                'po' => $filenamePO
                            ]);

                        $getPo =
                            DB::table('data_po as dp')
                            ->join('pengajuan as p', 'p.id', 'dp.id_pengajuan')
                            ->join('calon_nasabah as cn', 'cn.id_pengajuan', 'p.id')
                            ->select('cn.tenor_yang_diminta as tenor', 'dp.harga', 'cn.jumlah_kredit')
                            ->where('dp.id_pengajuan', $id)->first();

                        // store api
                        $konfiAPI = DB::table('api_configuration')->first();
                        // $host = env('DWH_HOST');
                        $host = $konfiAPI->dwh_host;
                        // $apiURL = $host . env('DWH_STORE_KREDIT_API');
                        $apiURL = $host . $konfiAPI->dwh_store_kredit_api_url;
                        $headers = [
                            // 'mid-client-key' => env('DWH_TOKEN')
                            'mid-client-key' => $konfiAPI->dwh_token
                        ];
                        try {
                            $response = Http::timeout(60)->withHeaders($headers)->withOptions(['verify' => false])->post($apiURL, [
                                'pengajuan_id' => $id,
                                'staf_id' => Auth::user()->id,
                                'kode_cabang' => $kode_cabang->kode_cabang,
                                'nomor_po' => $po,
                                'plafon' => intval($getPo->jumlah_kredit),
                                'tenor' => intval($getPo->tenor)
                            ]);

                            $statusCode = $response->status();
                            $responseBody = json_decode($response->getBody(), true);
                            if ($responseBody) {
                                if (array_key_exists('status', $responseBody)) {
                                    if ($responseBody['status'] == 'failed') {
                                        DB::rollBack();
                                        $message = array_key_exists('message', $responseBody) ? $responseBody['message'] : '';
                                        return redirect()->route('pengajuan-kredit.index')->withStatus('Terjadi kesalahan.'.$message);
                                    }
                                }
                                else {
                                    DB::rollBack();
                                    return redirect()->route('pengajuan-kredit.index')->withStatus('Terjadi kesalahan saat mengirim data ke sistem Dashboard KKB');
                                }
                            }
                            else {
                                DB::rollBack();
                                return redirect()->route('pengajuan-kredit.index')->withStatus('Terjadi kesalahan saat mengirim data ke sistem Dashboard KKB');
                            }
                        } catch (\Illuminate\Http\Client\ConnectionException $e) {
                            DB::rollBack();
                            return redirect()->route('pengajuan-kredit.index')->withStatus('Terjadi kesalahan. ' . $e->getMessage());
                        }
                    } catch (Exception $e) {
                        DB::rollBack();
                        return redirect()->route('pengajuan-kredit.index')->withStatus('Terjadi kesalahan. ' . $e->getMessage());
                    }

                    break;

                    // File PK Handler
                case 'PK':
                    $message = 'file PK.';
                    $no_pk = $request->get('no_pk');
                    DB::table('log_cetak_kkb')
                        ->where('id_pengajuan', $id)
                        ->update([
                            'no_pk' => $no_pk
                        ]);
                    $folderPK = public_path() . '/upload/' . $id . '/pk/';
                    $filePK = $request->pk;
                    $filenamePK = date('YmdHis') . '.' . $filePK->getClientOriginalExtension();
                    $pathPK = realpath($folderPK);
                    // If it exist, check if it's a directory
                    if (!($pathPK !== true and is_dir($pathPK))) {
                        // Path/folder does not exist then create a new folder
                        mkdir($folderPK, 0755, true);
                    }
                    $filePK->move($folderPK, $filenamePK);
                    DB::table('log_cetak')
                        ->where('id_pengajuan', $id)
                        ->update([
                            'no_pk' => $noPK
                        ]);
                    DB::table('pengajuan')
                        ->where('id', $id)
                        ->update([
                            'pk' => $filenamePK,
                        ]);
                    break;
            }

            DB::commit();
            Alert()->success('Berhasil menambahkan', $message);
            return redirect()->route('pengajuan-kredit.index')->withStatus('Berhasil menambahkan ' . $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('pengajuan-kredit.index')->withStatus('Terjadi kesalahan. ' . $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('pengajuan-kredit.index')->withStatus('Terjadi kesalahan. ' . $e->getMessage());
        }
    }

    public function getMerkKendaraan()
    {
        $data = MerkModel::all();

        return response()->json([
            'merk' => $data
        ]);
    }

    public function getTipeByMerk(Request $request)
    {
        $idMerk = $request->get('id_merk');
        $data = TipeModel::where('id_merk', $idMerk)->get();

        return response()->json([
            'tipe' => $data
        ]);
    }

    public function saveSkemaKreditDraft(Request $request, $tempId) {
        try{
            DB::beginTransaction();

            DB::table('temporary_calon_nasabah')
                ->where('id', $tempId)
                ->update([
                    'skema_kredit' => $request->skema
                ]);
            DB::commit();
            return $this->continueDraft($tempId);
        } catch(Exception $e){
            return redirect()->back();
        } catch(QueryException $e){
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        $id_pengajuan = Request()->idPengajuan;
        $data = PengajuanModel::find($id_pengajuan);
        if ($data) {
            $data->delete();
            event(new EventMonitoring('delete pengajuan'));
            alert()->success('Data '.$data->nama .' berhasil dihapus.');
            return redirect()->route('pengajuan-kredit.index')->withStatus('Data '.$data->nama .' berhasil dihapus.');
        } else {
            alert()->error('Data dengan ID tersebut tidak ditemukan.');
            return redirect()->route('pengajuan-kredit.index')->withErrors('Data dengan ID tersebut tidak ditemukan.');
        }
    }

    public function restore(Request $request)
    {
        $id_pengajuan = $request->input('idPengajuan');

        $data = PengajuanModel::withTrashed()->find($id_pengajuan);

        if ($data) {
            $data->restore();
            event(new EventMonitoring('restore pengajuan'));
            alert()->success('Data '.$data->nama.' berhasil direstore.');
            return redirect()->route('pengajuan-kredit.index')->withStatus('Data '.$data->nama.' berhasil direstore.');
        } else {
            alert()->error('Data dengan ID tersebut tidak ditemukan.');
            return redirect()->route('pengajuan-kredit.index')->withErrors('Data dengan ID tersebut tidak ditemukan.');
        }
    }

    public function kembalikanDataKePosisiSebelumnya(Request $request){
        DB::beginTransaction();
        try{
            $dataPengajuan = PengajuanModel::find($request->id);
            $alasan = $request->alasan;
            $dari = '';
            $ke = '';
            if($dataPengajuan->posisi == 'Pincab'){
                $dari = 'Pincab';

                //If data pengajuan di pincab
                if($dataPengajuan->id_pbp != null){
                    // If cabang ada pbp
                    $dataPengajuan->posisi = 'PBP';
                    $ke = 'PBP';
                } else if($dataPengajuan->id_pbp == null && $dataPengajuan->id_pbo != null){
                    // if cabang tidak ada pbp & ada pbo
                    $dataPengajuan->posisi = 'PBO';
                    $ke = 'PBO';
                } else{
                    // if tidak ada pbo & pbp
                    $dataPengajuan->posisi = 'Review Penyelia';
                    $ke = 'Review Penyelia';
                }
            } else if($dataPengajuan->posisi == 'PBP'){
                $dari = 'PBP';

                // If data pengajuan di PBP
                if($dataPengajuan->id_pbo != null){
                    // If cabang ada pbo
                    $dataPengajuan->posisi = 'PBO';
                    $ke = 'PBO';
                } else{
                    // If cabang tidak ada pbo
                    $dataPengajuan->posisi = 'Review Penyelia';
                    $ke = 'Review Penyelia';
                }
            } else if($dataPengajuan->posisi == 'PBO'){
                $dari = 'PBO';
                $ke = 'Review Penyelia';

                // If data pengajuan di PBO
                $dataPengajuan->posisi = 'Review Penyelia';
            } else{
                $dari = 'Review Penyelia';
                $ke = 'Proses Input Data';

                // If data pengajuan di review penyelia
                $dataPengajuan->posisi = 'Proses Input Data';
            }

            $dataPengajuan->save();
            AlasanPengembalianData::insert([
                'id_pengajuan' => $request->id,
                'id_user' => auth()->user()->id,
                'dari' => $dari,
                'ke' => $ke,
                'alasan' => $alasan,
                'created_at' => now()
            ]);
            DB::commit();

            event(new EventMonitoring('kembalikan data pengajuan'));

            alert()->success('Berhasil mengembalikan data ke ' . $ke . '.');
            return redirect()->back()->withStatus('Berhasil mengembalikan data ke ' . $ke . '.');
        } catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan. ' . $e->getMessage());
        } catch(QueryException $e){
            DB::rollBack();
            return redirect()->back()->withError('Terjadi kesalahan. ' . $e->getMessage());
        }
    }

    public function getSkemaKredit(Request $request){
        $produkId = $request->get('produkId');
        $produk = MstProdukKredit::find($produkId);

        if($produk->name == 'KKB' || $produk->name == 'Talangan Umroh'){
            // return $produk;
            $data = MstSkemaKredit::where('name', "like", '%konsumtif%')
                ->get();
        } else {
            $data = MstSkemaKredit::where('name', "like", '%modal kerja%')
                ->orWhere('name', "like", '%investasi%')
                ->get();
        }

        return response()->json($data);
    }

    public function getSkemaLimit(Request $request){
        $skemaId = $request->get('skemaId');
        $skema = MstSkemaKredit::find($skemaId);

        if($skema->name != 'Konsumtif'){
            $data = MstSkemaLimit::where('skema_kredit_id', $skemaId)
                ->get();
        } else {
            $data = null;
        }

        return response()->json($data);
    }

    public function saveDataPerhitunganTemp(Request $request){
        $idCalonNasabah = $request->idCalonNasabah;
        $levelTiga = [];
        $levelEmpat = [];
        $parentLevelTiga = [];

        DB::beginTransaction();
        try{
            if(PerhitunganKredit::where('temp_calon_nasabah_id', $idCalonNasabah)->count() < 1){
                // Data Level Tiga
                foreach($request->inpLevelTiga as $key => $item){
                    array_push($levelTiga, [
                        'nominal' => str_replace('.', '', str_replace(['(', ')'], '-', $item)),
                        'item_perhitungan_kredit_id' => $key,
                        'temp_calon_nasabah_id' => $idCalonNasabah,
                        'created_at' => now()
                    ]);
                }

                foreach($request->inpLevelTigaParent as $key => $item){
                    array_push($parentLevelTiga, [
                        'nominal' => $key != 68 ? str_replace('.', '', str_replace(['(', ')'], '-', $item)) : $item,
                        'item_perhitungan_kredit_id' => $key,
                        'temp_calon_nasabah_id' => $idCalonNasabah,
                        'created_at' => now()
                    ]);
                }

                // Data Level Empat
                $bakiDebetId = $request->inpLevelEmpatId[0];
                $plafonId =  $request->inpLevelEmpatId[1];
                $tenorId =  $request->inpLevelEmpatId[2];
                $angsuranId =  $request->inpLevelEmpatId[3];
                $bakiDebet = [];
                $plafon = [];
                $tenor = [];
                $angsuran = [];
                $bakiDebet_ = '';
                $plafon_ = '';
                $tenor_ = '';
                $angsuran_ = '';

                foreach($request->inpLevelEmpat as $key => $item){
                    if($request->inpLevelEmpatId[$key] == $bakiDebetId){
                        array_push($bakiDebet, strval(str_replace('.', '', $item)));
                    }
                    if($request->inpLevelEmpatId[$key] == $plafonId){
                        array_push($plafon, strval(str_replace('.', '', $item)));
                    }
                    if($request->inpLevelEmpatId[$key] == $tenorId){
                        array_push($tenor, strval(str_replace('.', '', $item)));
                    }
                    if($request->inpLevelEmpatId[$key] == $angsuranId){
                        array_push($angsuran, strval(str_replace('.', '', $item)));
                    }
                }

                for($i = 0; $i < count($bakiDebet); $i++){
                    if($i == 0){
                        if(count($bakiDebet) > 1){
                            $_bakiDebet = '[' . $bakiDebet[$i] . ',';
                        } else{
                            $_bakiDebet = '[' . $bakiDebet[$i] . ']';
                        }
                    } else if($i == count($bakiDebet) -1){
                        $_bakiDebet = $bakiDebet[$i] . ']';
                    } else{
                        $_bakiDebet = $bakiDebet[$i] . ',';
                    }
                    $bakiDebet_ .= $_bakiDebet;
                }
                for($i = 0; $i < count($plafon); $i++){
                    if($i == 0){
                        if(count($plafon) > 1){
                            $_plafon = '[' . $plafon[$i] . ',';
                        } else{
                            $_plafon = '[' . $plafon[$i] . ']';
                        }
                    } else if($i == count($plafon) -1){
                        $_plafon = $plafon[$i] . ']';
                    } else{
                        $_plafon = $plafon[$i] . ',';
                    }
                    $plafon_ .= $_plafon;
                }
                for($i = 0; $i < count($tenor); $i++){
                    if($i == 0){
                        if(count($tenor) > 1){
                            $_tenor = '[' . $tenor[$i] . ',';
                        } else{
                            $_tenor = '[' . $tenor[$i] . ']';
                        }
                    } else if($i == count($tenor) -1){
                        $_tenor = $tenor[$i] . ']';
                    } else{
                        $_tenor = $tenor[$i] . ',';
                    }
                    $tenor_ .= $_tenor;
                }
                for($i = 0; $i < count($angsuran); $i++){
                    if($i == 0){
                        if(count($angsuran) > 1){
                            $_angsuran = '[' . $angsuran[$i] . ',';
                        } else{
                            $_angsuran = '[' . $angsuran[$i] . ']';
                        }
                    } else if($i == count($angsuran) -1){
                        $_angsuran = $angsuran[$i] . ']';
                    } else{
                        $_angsuran = $angsuran[$i] . ',';
                    }
                    $angsuran_ .= $_angsuran;
                }

                array_push($levelEmpat, [
                    'array_value' => $bakiDebet_,
                    'item_perhitungan_kredit_id' => $bakiDebetId,
                    'temp_calon_nasabah_id' => $idCalonNasabah,
                    'created_at' => now()
                ]);
                array_push($levelEmpat, [
                    'array_value' => $plafon_,
                    'item_perhitungan_kredit_id' => $plafonId,
                    'temp_calon_nasabah_id' => $idCalonNasabah,
                    'created_at' => now()
                ]);
                array_push($levelEmpat, [
                    'array_value' => $tenor_,
                    'item_perhitungan_kredit_id' => $tenorId,
                    'temp_calon_nasabah_id' => $idCalonNasabah,
                    'created_at' => now()
                ]);
                array_push($levelEmpat, [
                    'array_value' => $angsuran_,
                    'item_perhitungan_kredit_id' => $angsuranId,
                    'temp_calon_nasabah_id' => $idCalonNasabah,
                    'created_at' => now()
                ]);

                PerhitunganKredit::insert($levelEmpat);
                PerhitunganKredit::insert($levelTiga);
                PerhitunganKredit::insert($parentLevelTiga);
                DB::commit();

                $idTerakhir = DB::table('perhitungan_kredit')->latest('id')->first()->id;

                return response()->json([
                    'message' => 'Berhasil menambahkan perhitungan kredit.',
                    'request' => $request->all(),
                    'lastId' => $idTerakhir,
                ]);
            } else{
                // Data Level Tiga
                foreach($request->inpLevelTiga as $key => $item){
                    PerhitunganKredit::where('temp_calon_nasabah_id', $idCalonNasabah)
                        ->where('item_perhitungan_kredit_id', $key)
                        ->update([
                            'nominal' => str_replace('.', '', str_replace(['(', ')'], '-', $item)),
                            'updated_at' => now()
                        ]);
                }
                foreach($request->inpLevelTigaParent as $key => $item){
                    PerhitunganKredit::where('temp_calon_nasabah_id', $idCalonNasabah)
                        ->where('item_perhitungan_kredit_id', $key)
                        ->update([
                            'nominal' =>$key != 68 ? str_replace('.', '', str_replace(['(', ')'], '-', $item)) : $item,
                            'updated_at' => now()
                        ]);
                }

                // Data Level Empat
                $bakiDebetId = $request->inpLevelEmpatId[0];
                $plafonId =  $request->inpLevelEmpatId[1];
                $tenorId =  $request->inpLevelEmpatId[2];
                $bakiDebet = [];
                $plafon = [];
                $tenor = [];
                $bakiDebet_ = '';
                $plafon_ = '';
                $tenor_ = '';

                foreach($request->inpLevelEmpat as $key => $item){
                    if($request->inpLevelEmpatId[$key] == $bakiDebetId){
                        array_push($bakiDebet, strval(str_replace('.', '', $item)));
                    }
                    if($request->inpLevelEmpatId[$key] == $plafonId){
                        array_push($plafon, strval(str_replace('.', '', $item)));
                    }
                    if($request->inpLevelEmpatId[$key] == $tenorId){
                        array_push($tenor, strval(str_replace('.', '', $item)));
                    }
                }

                for($i = 0; $i < count($bakiDebet); $i++){
                    if($i == 0){
                        if(count($bakiDebet) > 1){
                            $_bakiDebet = '[' . $bakiDebet[$i] . ',';
                        } else{
                            $_bakiDebet = '[' . $bakiDebet[$i] . ']';
                        }
                    } else if($i == count($bakiDebet) -1){
                        $_bakiDebet = $bakiDebet[$i] . ']';
                    } else{
                        $_bakiDebet = $bakiDebet[$i] . ',';
                    }
                    $bakiDebet_ .= $_bakiDebet;
                }
                for($i = 0; $i < count($plafon); $i++){
                    if($i == 0){
                        if(count($plafon) > 1){
                            $_plafon = '[' . $plafon[$i] . ',';
                        } else{
                            $_plafon = '[' . $plafon[$i] . ']';
                        }
                    } else if($i == count($plafon) -1){
                        $_plafon = $plafon[$i] . ']';
                    } else{
                        $_plafon = $plafon[$i] . ',';
                    }
                    $plafon_ .= $_plafon;
                }
                for($i = 0; $i < count($tenor); $i++){
                    if($i == 0){
                        if(count($tenor) > 1){
                            $_tenor = '[' . $tenor[$i] . ',';
                        } else{
                            $_tenor = '[' . $tenor[$i] . ']';
                        }
                    } else if($i == count($tenor) -1){
                        $_tenor = $tenor[$i] . ']';
                    } else{
                        $_tenor = $tenor[$i] . ',';
                    }
                    $tenor_ .= $_tenor;
                }

                PerhitunganKredit::where('temp_calon_nasabah_id', $idCalonNasabah)
                    ->where('item_perhitungan_kredit_id', $bakiDebetId)
                    ->update([
                        'array_value' => $bakiDebet_,
                        'nominal' => null,
                        'updated_at' => now()
                    ]);
                PerhitunganKredit::where('temp_calon_nasabah_id', $idCalonNasabah)
                    ->where('item_perhitungan_kredit_id', $plafonId)
                    ->update([
                        'array_value' => $plafon_,
                        'nominal' => null,
                        'updated_at' => now()
                    ]);
                PerhitunganKredit::where('temp_calon_nasabah_id', $idCalonNasabah)
                    ->where('item_perhitungan_kredit_id', $tenorId)
                    ->update([
                        'array_value' => $tenor_,
                        'nominal' => null,
                        'updated_at' => now()
                    ]);
                $idTerakhir = DB::table('perhitungan_kredit')->latest('id')->first()->id;
                DB::commit();
                return response()->json([
                    'message' => 'Berhasil mengubah data.',
                    'lastId' => $idTerakhir,
                    'request' => $request->all()
                ]);
            }
        } catch(Exception $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan. ' . $e
            ]);
        } catch(QueryException $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan. ' . $e
            ]);
        }
    }

    function editPerhitunganKredit(Request $request) {
        $idCalonNasabah = $request->idNasabah;
        $levelTiga = [];
        $levelEmpat = [];
        $parentLevelTiga = [];

        DB::beginTransaction();
        try{
            if(PerhitunganKredit::where('pengajuan_id', $idCalonNasabah)->count() < 1){
                // Data Level Tiga
                foreach($request->inpLevelTiga as $key => $item){
                    array_push($levelTiga, [
                        'nominal' => str_replace('.', '', str_replace(['(', ')'], '-', $item)),
                        'item_perhitungan_kredit_id' => $key,
                        'pengajuan_id' => $idCalonNasabah,
                        'created_at' => now()
                    ]);
                }

                foreach($request->inpLevelTigaParent as $key => $item){
                    array_push($parentLevelTiga, [
                        'nominal' => $key != 68 ? str_replace('.', '', str_replace(['(', ')'], '-', $item)) : $item,
                        'item_perhitungan_kredit_id' => $key,
                        'pengajuan_id' => $idCalonNasabah,
                        'created_at' => now()
                    ]);
                }

                // Data Level Empat
                $bakiDebetId = $request->inpLevelEmpatId[0];
                $plafonId =  $request->inpLevelEmpatId[1];
                $tenorId =  $request->inpLevelEmpatId[2];
                $angsuranId =  $request->inpLevelEmpatId[3];
                $bakiDebet = [];
                $plafon = [];
                $tenor = [];
                $angsuran = [];
                $bakiDebet_ = '';
                $plafon_ = '';
                $tenor_ = '';
                $angsuran_ = '';

                foreach($request->inpLevelEmpat as $key => $item){
                    if($request->inpLevelEmpatId[$key] == $bakiDebetId){
                        array_push($bakiDebet, strval(str_replace('.', '', $item)));
                    }
                    if($request->inpLevelEmpatId[$key] == $plafonId){
                        array_push($plafon, strval(str_replace('.', '', $item)));
                    }
                    if($request->inpLevelEmpatId[$key] == $tenorId){
                        array_push($tenor, strval(str_replace('.', '', $item)));
                    }
                    if($request->inpLevelEmpatId[$key] == $angsuranId){
                        array_push($angsuran, strval(str_replace('.', '', $item)));
                    }
                }

                for($i = 0; $i < count($bakiDebet); $i++){
                    if($i == 0){
                        if(count($bakiDebet) > 1){
                            $_bakiDebet = '[' . $bakiDebet[$i] . ',';
                        } else{
                            $_bakiDebet = '[' . $bakiDebet[$i] . ']';
                        }
                    } else if($i == count($bakiDebet) -1){
                        $_bakiDebet = $bakiDebet[$i] . ']';
                    } else{
                        $_bakiDebet = $bakiDebet[$i] . ',';
                    }
                    $bakiDebet_ .= $_bakiDebet;
                }
                for($i = 0; $i < count($plafon); $i++){
                    if($i == 0){
                        if(count($plafon) > 1){
                            $_plafon = '[' . $plafon[$i] . ',';
                        } else{
                            $_plafon = '[' . $plafon[$i] . ']';
                        }
                    } else if($i == count($plafon) -1){
                        $_plafon = $plafon[$i] . ']';
                    } else{
                        $_plafon = $plafon[$i] . ',';
                    }
                    $plafon_ .= $_plafon;
                }
                for($i = 0; $i < count($tenor); $i++){
                    if($i == 0){
                        if(count($tenor) > 1){
                            $_tenor = '[' . $tenor[$i] . ',';
                        } else{
                            $_tenor = '[' . $tenor[$i] . ']';
                        }
                    } else if($i == count($tenor) -1){
                        $_tenor = $tenor[$i] . ']';
                    } else{
                        $_tenor = $tenor[$i] . ',';
                    }
                    $tenor_ .= $_tenor;
                }
                for($i = 0; $i < count($angsuran); $i++){
                    if($i == 0){
                        if(count($angsuran) > 1){
                            $_angsuran = '[' . $angsuran[$i] . ',';
                        } else{
                            $_angsuran = '[' . $angsuran[$i] . ']';
                        }
                    } else if($i == count($angsuran) -1){
                        $_angsuran = $angsuran[$i] . ']';
                    } else{
                        $_angsuran = $angsuran[$i] . ',';
                    }
                    $angsuran_ .= $_angsuran;
                }

                array_push($levelEmpat, [
                    'array_value' => $bakiDebet_,
                    'item_perhitungan_kredit_id' => $bakiDebetId,
                    'pengajuan_id' => $idCalonNasabah,
                    'created_at' => now()
                ]);
                array_push($levelEmpat, [
                    'array_value' => $plafon_,
                    'item_perhitungan_kredit_id' => $plafonId,
                    'pengajuan_id' => $idCalonNasabah,
                    'created_at' => now()
                ]);
                array_push($levelEmpat, [
                    'array_value' => $tenor_,
                    'item_perhitungan_kredit_id' => $tenorId,
                    'pengajuan_id' => $idCalonNasabah,
                    'created_at' => now()
                ]);
                array_push($levelEmpat, [
                    'array_value' => $angsuran_,
                    'item_perhitungan_kredit_id' => $angsuranId,
                    'pengajuan_id' => $idCalonNasabah,
                    'created_at' => now()
                ]);

                PerhitunganKredit::insert($levelEmpat);
                PerhitunganKredit::insert($levelTiga);
                PerhitunganKredit::insert($parentLevelTiga);
                DB::commit();

                $idTerakhir = DB::table('perhitungan_kredit')->latest('id')->first()->id;

                return response()->json([
                    'message' => 'Berhasil menambahkan perhitungan kredit.',
                    'request' => $request->all(),
                    'lastId' => $idTerakhir,
                ]);
            } else{
                // Data Level Tiga
                foreach($request->inpLevelTiga as $key => $item){
                    PerhitunganKredit::where('pengajuan_id', $idCalonNasabah)
                        ->where('item_perhitungan_kredit_id', $key)
                        ->update([
                            'nominal' => str_replace('.', '', str_replace(['(', ')'], '-', $item)),
                            'updated_at' => now()
                        ]);
                }
                foreach($request->inpLevelTigaParent as $key => $item){
                    PerhitunganKredit::where('pengajuan_id', $idCalonNasabah)
                        ->where('item_perhitungan_kredit_id', $key)
                        ->update([
                            'nominal' =>$key != 68 ? str_replace('.', '', str_replace(['(', ')'], '-', $item)) : $item,
                            'updated_at' => now()
                        ]);
                }

                // Data Level Empat
                $bakiDebetId = $request->inpLevelEmpatId[0];
                $plafonId =  $request->inpLevelEmpatId[1];
                $tenorId =  $request->inpLevelEmpatId[2];
                $bakiDebet = [];
                $plafon = [];
                $tenor = [];
                $bakiDebet_ = '';
                $plafon_ = '';
                $tenor_ = '';

                foreach($request->inpLevelEmpat as $key => $item){
                    if($request->inpLevelEmpatId[$key] == $bakiDebetId){
                        array_push($bakiDebet, strval(str_replace('.', '', $item)));
                    }
                    if($request->inpLevelEmpatId[$key] == $plafonId){
                        array_push($plafon, strval(str_replace('.', '', $item)));
                    }
                    if($request->inpLevelEmpatId[$key] == $tenorId){
                        array_push($tenor, strval(str_replace('.', '', $item)));
                    }
                }

                for($i = 0; $i < count($bakiDebet); $i++){
                    if($i == 0){
                        if(count($bakiDebet) > 1){
                            $_bakiDebet = '[' . $bakiDebet[$i] . ',';
                        } else{
                            $_bakiDebet = '[' . $bakiDebet[$i] . ']';
                        }
                    } else if($i == count($bakiDebet) -1){
                        $_bakiDebet = $bakiDebet[$i] . ']';
                    } else{
                        $_bakiDebet = $bakiDebet[$i] . ',';
                    }
                    $bakiDebet_ .= $_bakiDebet;
                }
                for($i = 0; $i < count($plafon); $i++){
                    if($i == 0){
                        if(count($plafon) > 1){
                            $_plafon = '[' . $plafon[$i] . ',';
                        } else{
                            $_plafon = '[' . $plafon[$i] . ']';
                        }
                    } else if($i == count($plafon) -1){
                        $_plafon = $plafon[$i] . ']';
                    } else{
                        $_plafon = $plafon[$i] . ',';
                    }
                    $plafon_ .= $_plafon;
                }
                for($i = 0; $i < count($tenor); $i++){
                    if($i == 0){
                        if(count($tenor) > 1){
                            $_tenor = '[' . $tenor[$i] . ',';
                        } else{
                            $_tenor = '[' . $tenor[$i] . ']';
                        }
                    } else if($i == count($tenor) -1){
                        $_tenor = $tenor[$i] . ']';
                    } else{
                        $_tenor = $tenor[$i] . ',';
                    }
                    $tenor_ .= $_tenor;
                }

                PerhitunganKredit::where('pengajuan_id', $idCalonNasabah)
                    ->where('item_perhitungan_kredit_id', $bakiDebetId)
                    ->update([
                        'array_value' => $bakiDebet_,
                        'nominal' => null,
                        'updated_at' => now()
                    ]);
                PerhitunganKredit::where('pengajuan_id', $idCalonNasabah)
                    ->where('item_perhitungan_kredit_id', $plafonId)
                    ->update([
                        'array_value' => $plafon_,
                        'nominal' => null,
                        'updated_at' => now()
                    ]);
                PerhitunganKredit::where('pengajuan_id', $idCalonNasabah)
                    ->where('item_perhitungan_kredit_id', $tenorId)
                    ->update([
                        'array_value' => $tenor_,
                        'nominal' => null,
                        'updated_at' => now()
                    ]);

                DB::commit();
                return response()->json([
                    'message' => 'Berhasil mengubah data.',
                    'request' => $request->all()
                ]);
            }
        } catch(Exception $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan. ' . $e
            ]);
        } catch(QueryException $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan. ' . $e
            ]);
        }
    }

    public function getPerhitunganKreditLev1(){
        $data = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)->where('level', 1)->get();
        return response()->json(['result' => $data]);
    }

    public function getPerhitunganKreditLev2(Request $request){
        $parent_id = $request->parent_id;
        $data = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                    ->where('level', 2)
                                                                    ->where('parent_id', $parent_id)
                                                                    ->get();
        return response()->json(['result' => $data]);
    }

    public function getDataPerhitunganKreditLev3(Request $request){
        $perhitunganKreditLev3 = \App\Models\PerhitunganKredit::rightJoin('mst_item_perhitungan_kredit', 'perhitungan_kredit.item_perhitungan_kredit_id', '=', 'mst_item_perhitungan_kredit.id')
                                            ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', 1)
                                            ->where('mst_item_perhitungan_kredit.level', 3)
                                            ->where('mst_item_perhitungan_kredit.parent_id', $request->parent_id)
                                            ->where('perhitungan_kredit.temp_calon_nasabah_id', $request->id_nasabah)
                                            ->get();
        return response()->json(['result' => $perhitunganKreditLev3]);
    }

    public function getDataPerhitunganKreditLev3Edit(Request $request){
        $perhitunganKreditLev3 = \App\Models\PerhitunganKredit::rightJoin('mst_item_perhitungan_kredit', 'perhitungan_kredit.item_perhitungan_kredit_id', '=', 'mst_item_perhitungan_kredit.id')
                                            ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', 1)
                                            ->where('mst_item_perhitungan_kredit.level', 3)
                                            ->where('mst_item_perhitungan_kredit.parent_id', $request->parent_id)
                                            ->where('perhitungan_kredit.pengajuan_id', $request->id_nasabah)
                                            ->get();
        return response()->json(['result' => $perhitunganKreditLev3]);
    }

    public function getDataPerhitunganKreditLev3NoParent($idClnNasabah){
        $data = \App\Models\MstItemPerhitunganKredit::leftJoin('perhitungan_kredit', function($join) use ($idClnNasabah) {
            $join->on('mst_item_perhitungan_kredit.id', '=', 'perhitungan_kredit.item_perhitungan_kredit_id')
                ->where('perhitungan_kredit.temp_calon_nasabah_id', '=', $idClnNasabah);
        })
        ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', '=', 1)
        ->where('mst_item_perhitungan_kredit.level', '=', 3)
        ->whereNull('mst_item_perhitungan_kredit.parent_id')
        // ->whereNull('perhitungan_kredit.temp_calon_nasabah_id')
        ->get();

        return response()->json(['result'=> $data]);
    }

    public function getDataPerhitunganKreditLev3NoParentEdit($idClnNasabah){
        $data = \App\Models\MstItemPerhitunganKredit::leftJoin('perhitungan_kredit', function($join) use ($idClnNasabah) {
            $join->on('mst_item_perhitungan_kredit.id', '=', 'perhitungan_kredit.item_perhitungan_kredit_id')
                ->where('perhitungan_kredit.pengajuan_id', '=', $idClnNasabah);
        })
        ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', '=', 1)
        ->where('mst_item_perhitungan_kredit.level', '=', 3)
        ->whereNull('mst_item_perhitungan_kredit.parent_id')
        // ->whereNull('perhitungan_kredit.temp_calon_nasabah_id')
        ->get();

        return response()->json(['result'=> $data]);
    }

    public function getDataPerhitunganKreditLev2NoParent(){
        $data = \App\Models\MstItemPerhitunganKredit::where('skema_kredit_limit_id', 1)
                                                                ->where('level', 2)
                                                                ->whereNull('parent_id')
                                                                ->get();
        return response()->json(['result'=> $data]);
    }

    public function getDataPerhitunganKreditLev3NoParent2(Request $request) {
        $perhitunganKreditLev3 = \App\Models\PerhitunganKredit::rightJoin('mst_item_perhitungan_kredit', 'perhitungan_kredit.item_perhitungan_kredit_id', '=', 'mst_item_perhitungan_kredit.id')
                                            ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', 1)
                                            ->where('mst_item_perhitungan_kredit.level', 3)
                                            ->where('mst_item_perhitungan_kredit.parent_id', $request->parent_id)
                                            ->where('perhitungan_kredit.temp_calon_nasabah_id', $request->id_nasabah)
                                            ->get();
        return response()->json(['result' => $perhitunganKreditLev3]);
    }

    public function getDataPerhitunganKreditLev3NoParent2Edit(Request $request) {
        $perhitunganKreditLev3 = \App\Models\PerhitunganKredit::rightJoin('mst_item_perhitungan_kredit', 'perhitungan_kredit.item_perhitungan_kredit_id', '=', 'mst_item_perhitungan_kredit.id')
                                            ->where('mst_item_perhitungan_kredit.skema_kredit_limit_id', 1)
                                            ->where('mst_item_perhitungan_kredit.level', 3)
                                            ->where('mst_item_perhitungan_kredit.parent_id', $request->parent_id)
                                            ->where('perhitungan_kredit.pengajuan_id', $request->id_nasabah)
                                            ->get();
        return response()->json(['result' => $perhitunganKreditLev3]);
    }

    public function saveDataPeriodeAspekKeuangan(Request $request) {
        $data = [
            'perhitungan_kredit_id' => $request->perhitungan_kredit_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ];
        PeriodeAspekKeuangan::create($data);
        $idTerakhir = DB::table('periode_aspek_keuangan')->latest('id')->first()->id;
        return response()->json([
            'success' => 'Data Berhasil di Simpan',
            'result' => $data,
            'lastId' => $idTerakhir,
        ]);
    }

    public function updateDataPeriodeAspekKeuangan(Request $request) {
        $id = $request->id;
        $data = [
            'perhitungan_kredit_id' => $request->perhitungan_kredit_id,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ];
        PeriodeAspekKeuangan::where('id', $id)->update($data);
        return response()->json([
            'success' => 'Data Berhasil di Update',
            'result' => $data,
        ]);
    }

    public function getPeriodeAspekKeuanganEdit(Request $request) {
        $pengajuan_id = $request->pengajuan_id;
        $getPeriode = \App\Models\PeriodeAspekKeuangan::join('perhitungan_kredit', 'periode_aspek_keuangan.perhitungan_kredit_id', '=', 'perhitungan_kredit.id')
                                            ->where('perhitungan_kredit.pengajuan_id', $pengajuan_id)
                                            ->select('periode_aspek_keuangan.id','periode_aspek_keuangan.perhitungan_kredit_id',
                                            'periode_aspek_keuangan.bulan','periode_aspek_keuangan.tahun')
                                            ->get();
        return response()->json(['result' => $getPeriode]);
    }

    public function getPeriodeAspekKeuanganDraft(Request $request) {
        $calon_nasabah_id = $request->calon_nasabah_id;
        $getPeriode = \App\Models\PeriodeAspekKeuangan::join('perhitungan_kredit', 'periode_aspek_keuangan.perhitungan_kredit_id', '=', 'perhitungan_kredit.id')
                                            ->where('perhitungan_kredit.temp_calon_nasabah_id', $calon_nasabah_id)
                                            ->select('periode_aspek_keuangan.id','periode_aspek_keuangan.perhitungan_kredit_id',
                                            'periode_aspek_keuangan.bulan','periode_aspek_keuangan.tahun')
                                            ->get();
        return response()->json(['result' => $getPeriode]);
    }
}
