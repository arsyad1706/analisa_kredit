<?php

namespace App\Http\Controllers;

use App\Models\PengajuanModel;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Exports\DataNominatif;
use App\Repository\DashboardRepository;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;

class DashboardController extends Controller
{
    private $repo;

    public function __construct()
    {
        $this->repo = new DashboardRepository;
    }

    public function index(Request $request)
    {
        $param['pageTitle'] = "Analisa Kredit";
        if (Auth::user()->password_change_at == null) {
            return redirect()->route('change_password');
        }
        $param['cabangs'] = DB::table('cabang')
            ->get();
        $id_cabang = Auth::user()->id_cabang;
        $current_cabang = DB::table('cabang')->select('kode_cabang')->where('id', $id_cabang)->first();
        $kode_cabang = '-';
        if ($current_cabang)
            $kode_cabang = $current_cabang->kode_cabang;
        $param['kode_cabang'] = $kode_cabang;

        if (Auth::user()->role == "Staf Analis Kredit") {
            $id_data = auth()->user()->id;
            $param['data'] = PengajuanModel::select('pengajuan.id', 'pengajuan.id_staf', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.progress_pengajuan_data', 'pengajuan.tanggal_review_penyelia', 'pengajuan.tanggal_review_pincab', 'pengajuan.status', 'pengajuan.status_by_sistem', 'pengajuan.id_cabang', 'pengajuan.average_by_sistem', 'pengajuan.average_by_penyelia', 'calon_nasabah.nama', 'calon_nasabah.jenis_usaha', 'calon_nasabah.sektor_kredit', 'calon_nasabah.jumlah_kredit', 'calon_nasabah.id_pengajuan')
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->where('pengajuan.id_cabang', $id_cabang)
                ->where('pengajuan.posisi', 'Proses Input Data')
                ->where('pengajuan.id_staf', $id_data)
                ->get();
            // dd($param['data']);
        } elseif (Auth::user()->role == "Penyelia Kredit") {
            $id_data = auth()->user()->id;
            $param['data'] = PengajuanModel::select('pengajuan.id_penyelia', 'pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.progress_pengajuan_data', 'pengajuan.tanggal_review_penyelia', 'pengajuan.tanggal_review_pincab', 'pengajuan.status', 'pengajuan.status_by_sistem', 'pengajuan.id_cabang', 'pengajuan.average_by_sistem', 'pengajuan.average_by_penyelia', 'calon_nasabah.nama', 'calon_nasabah.jenis_usaha', 'calon_nasabah.sektor_kredit', 'calon_nasabah.jumlah_kredit', 'calon_nasabah.id_pengajuan')
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->where('pengajuan.id_cabang', $id_cabang)
                ->where('pengajuan.posisi', '=', 'Review Penyelia')
                ->where('pengajuan.id_penyelia', $id_data)
                ->get();
            // dd($param['data']);
        } elseif (Auth::user()->role == "PBO") {
            $id_data = auth()->user()->id;
            $param['data'] = PengajuanModel::select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.progress_pengajuan_data', 'pengajuan.tanggal_review_penyelia', 'pengajuan.tanggal_review_pincab', 'pengajuan.status', 'pengajuan.status_by_sistem', 'pengajuan.id_cabang', 'pengajuan.average_by_sistem', 'pengajuan.average_by_penyelia', 'calon_nasabah.nama', 'calon_nasabah.jenis_usaha', 'calon_nasabah.sektor_kredit', 'calon_nasabah.jumlah_kredit', 'calon_nasabah.id_pengajuan')
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->where('pengajuan.id_cabang', $id_cabang)
                ->where('pengajuan.posisi', '=', 'PBO')
                ->where('pengajuan.id_pbo', $id_data)
                ->get();
        } elseif (Auth::user()->role == "PBP") {
            $id_data = auth()->user()->id;
            $param['data'] = PengajuanModel::select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.progress_pengajuan_data', 'pengajuan.tanggal_review_penyelia', 'pengajuan.tanggal_review_pincab', 'pengajuan.status', 'pengajuan.status_by_sistem', 'pengajuan.id_cabang', 'pengajuan.average_by_sistem', 'pengajuan.average_by_penyelia', 'calon_nasabah.nama', 'calon_nasabah.jenis_usaha', 'calon_nasabah.sektor_kredit', 'calon_nasabah.jumlah_kredit', 'calon_nasabah.id_pengajuan')
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->where('pengajuan.id_cabang', $id_cabang)
                ->where('pengajuan.posisi', '=', 'PBP')
                ->where('pengajuan.id_pbp', $id_data)
                ->get();
        } elseif (Auth::user()->role == "Pincab") {
            $id_data = auth()->user()->id;
            $param['data'] = PengajuanModel::select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.progress_pengajuan_data', 'pengajuan.tanggal_review_penyelia', 'pengajuan.tanggal_review_pincab', 'pengajuan.status', 'pengajuan.status_by_sistem', 'pengajuan.id_cabang', 'pengajuan.average_by_sistem', 'pengajuan.average_by_penyelia', 'calon_nasabah.nama', 'calon_nasabah.jenis_usaha', 'calon_nasabah.sektor_kredit', 'calon_nasabah.jumlah_kredit', 'calon_nasabah.id_pengajuan')
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->where('pengajuan.id_cabang', $id_cabang)
                ->where('pengajuan.posisi', '=', 'Pincab')
                ->where('pengajuan.id_pincab', $id_data)
                ->get();
        } else {
            $param['data'] = PengajuanModel::select('pengajuan.id', 'pengajuan.tanggal', 'pengajuan.posisi', 'pengajuan.progress_pengajuan_data', 'pengajuan.tanggal_review_penyelia', 'pengajuan.tanggal_review_pincab', 'pengajuan.status', 'pengajuan.status_by_sistem', 'pengajuan.id_cabang', 'pengajuan.average_by_sistem', 'pengajuan.average_by_penyelia', 'calon_nasabah.nama', 'calon_nasabah.jenis_usaha', 'calon_nasabah.sektor_kredit', 'calon_nasabah.jumlah_kredit', 'calon_nasabah.id_pengajuan')
                ->join('calon_nasabah', 'calon_nasabah.id_pengajuan', 'pengajuan.id')
                ->get();
        }

        $param['dataCard'] = $this->repo->getCount($request);
        $param['dataYear'] = $this->repo->getDataYear();
        $param['dataPosisi'] = $this->repo->getDataPosisi($request);
        $param['dataSkema'] = $this->repo->getDataSkema($request);
        $param['dataRangking'] = $this->repo->getRangking($request);

        return view('dashboard', $param);
    }

    public static function getDataKaryawan()
    {
        $konfiAPI = DB::table('api_configuration')->first();
        $host = $konfiAPI->hcs_host;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $host . '/api/v1/karyawan/' . auth()->user()->nip,
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
        $json = json_decode($response);

        if ($json) {
            if ($json->data)
                $data = [
                    'nama' => $json->data->nama_karyawan,
                    'cabang' => $json->data->nama_cabang,
                ];
                return $data;
        }

        $cabang = Cabang::select('kode_cabang', 'cabang AS nama_cabang')
                        ->where('id', Auth::user()->id_cabang)
                        ->first();
        $data = [
            'nama' => Auth::user()->name,
            'cabang' => $cabang ? $cabang->nama_cabang : 'undifined',
        ];

        return $data;
    }

    public static function getKaryawan()
    {
        $konfiAPI = DB::table('api_configuration')->first();
        $host = $konfiAPI->hcs_host;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $host . '/api/v1/karyawan/' . auth()->user()->nip,
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
        $json = json_decode($response);

        if ($json) {
            if ($json->data)
                return $json->data->nama_karyawan;
        }
        return Auth::user()->name;
    }

    function compare_some_objects($a, $b) { // Make sure to give this a more meaningful name!
        return $b['total'] - $a['total'];
    }

    public function cetak(Request $request)
    {
        if (!$request->has('tAwal') || !$request->has('tAkhir') || !$request->has('cabang') || !$request->has('export') || !$request->has('k_tanggal')) {
            return redirect()->route('dashboard.index')->withError('Terjadi Kesalahan');
        }
        try {
            $param['tAwal'] = $request->tAwal;
            $param['tAkhir'] = $request->tAkhir;
            $pilCabang = $request->cabang;
            $tAkhir = Carbon::parse($request->tAkhir)->format('Y-m-d');
            $tAwal = Carbon::parse($request->tAwal)->format('Y-m-d');
            $jExport = $request->export;
            $type = $request->k_tanggal;

            // New code
            $seluruh_data = DB::table('cabang AS c')
                                ->select(
                                    'c.kode_cabang AS kodeC',
                                    'c.cabang',
                                    DB::raw("IFNULL(SUM(CASE WHEN p.posisi = 'Selesai' THEN 1 ELSE 0 END),0) AS disetujui"),
                                    DB::raw("IFNULL(SUM(CASE WHEN p.posisi = 'Ditolak' THEN 1 ELSE 0 END),0) AS ditolak"),
                                    DB::raw("IFNULL(SUM(CASE WHEN p.posisi = 'Pincab' THEN 1 ELSE 0 END),0) AS pincab"),
                                    DB::raw("IFNULL(SUM(CASE WHEN p.posisi = 'PBP' THEN 1 ELSE 0 END),0) AS pbp"),
                                    DB::raw("IFNULL(SUM(CASE WHEN p.posisi = 'PBO' THEN 1 ELSE 0 END),0) AS pbo"),
                                    DB::raw("IFNULL(SUM(CASE WHEN p.posisi = 'Review Penyelia' THEN 1 ELSE 0 END),0) AS penyelia"),
                                    DB::raw("IFNULL(SUM(CASE WHEN p.posisi = 'Proses Input Data' THEN 1 ELSE 0 END),0) AS staff"),
                                    DB::raw("(IFNULL(SUM(CASE WHEN p.posisi IN ('Proses Input Data', 'Review Penyelia', 'PBO', 'PBP', 'Pincab') THEN 1 ELSE 0 END),0)) AS diproses"),
                                    DB::raw("SUM(1) AS total")
                                )
                                ->leftJoin('pengajuan AS p', function ($join) {
                                    $join->on('c.id', '=', 'p.id_cabang')
                                        ->whereNull('p.deleted_at');
                                });
                if ($type == 'kustom') {
                    $seluruh_data->whereBetween('p.tanggal', [$tAwal, $tAkhir]);
                }
                $seluruh_data
                    ->where('c.kode_cabang', '!=', 000)
                    ->groupBy('kodeC')
                    ->orderBy('total', 'desc');

            $seluruh_data_proses = DB::table('cabang AS c')
                                ->select(
                                    'c.kode_cabang AS kodeC',
                                    'c.cabang',
                                    DB::raw("SUM(IF(p.posisi = 'Selesai', 1,0)) AS disetujui"),
                                    DB::raw("SUM(IF(p.posisi = 'Ditolak', 1,0)) AS ditolak"),
                                    DB::raw("(SUM(IF(p.posisi = 'Proses Input Data', 1,0))+
                                            SUM(IF(p.posisi = 'Review Penyelia', 1,0))+
                                            SUM(IF(p.posisi = 'PBO', 1,0))+
                                            SUM(IF(p.posisi = 'PBP', 1,0))+
                                            SUM(IF(p.posisi = 'Pincab', 1,0))) AS diproses"),
                                    DB::raw("(SUM(IF(p.posisi = 'Selesai', 1,0))+
                                            SUM(IF(p.posisi = 'Ditolak', 1,0))+
                                            (
                                                SUM(IF(p.posisi = 'Proses Input Data', 1,0))+
                                                SUM(IF(p.posisi = 'Review Penyelia', 1,0))+
                                                SUM(IF(p.posisi = 'PBO', 1,0))+
                                                SUM(IF(p.posisi = 'PBP', 1,0))+
                                                SUM(IF(p.posisi = 'Pincab', 1,0))
                                            )) AS total"),
                                );
                                if ($type == 'kustom') {
                                    $seluruh_data_proses->whereBetween('p.tanggal', [$tAwal, $tAkhir]);
                                }
                                $seluruh_data_proses->leftJoin('pengajuan AS p', 'c.id', 'p.id_cabang')
                                ->where('c.kode_cabang', '!=', 000)
                                ->whereNull('p.deleted_at')
                                ->groupBy('kodeC')
                                ->orderBy('total', 'desc');
            if ($pilCabang != 'semua') {
                $seluruh_data = $seluruh_data->where('c.id', $pilCabang);
                $seluruh_data_proses = $seluruh_data_proses->where('c.id', $pilCabang);
            }

            $seluruh_data = $seluruh_data->get();
            $seluruh_data_proses = $seluruh_data_proses->get();
            // End new code
            if ($jExport == 'pdf') {
                $param['data'] = $seluruh_data;
                $param['data2'] = $seluruh_data_proses;

                $pdf = PDF::loadView('modal.DataNominatif', $param);
                // return view('modal.DataNominatif', $param);
                // Return hasil PDF untuk diunduh atau ditampilkan
                if ($type != "kesuluruhan") {
                    // return "Tanggal";
                    if ($pilCabang == 'semua') {
                        return $pdf->download('Data Nominatif Kategori berdasarkan tanggal ' . $request->tAwal . ' sampai dengan ' . $request->tAkhir . ' Semua Cabang' . '.pdf');
                    } else {
                        $name_cabang = cabang::select('cabang')->where('id', $pilCabang)->first();
                        return $pdf->download('Data Nominatif Kategori berdasarkan tanggal ' . $request->tAwal . ' sampai dengan ' . $request->tAkhir . ' cabang ' . $name_cabang->cabang . '.pdf');
                    }
                } else {
                    // return "Keseluruhan";
                    if ($pilCabang == 'semua') {
                        return $pdf->download('Data Nominatif Kategori keseluruhan Semua Cabang' . '.pdf');
                    } else {
                        $name_cabang = cabang::select('cabang')->where('id', $pilCabang)->first();
                        return $pdf->download('Data Nominatif Kategori keseluruhan cabang ' . $name_cabang->cabang . '.pdf');
                    }
                }
            } else {
                if ($type != "kesuluruhan") {
                    if ($pilCabang == 'semua') {
                        return Excel::download(new DataNominatif($seluruh_data, $seluruh_data_proses), 'Data Nominatif Kategori berdasarkan tanggal ' . $request->tAwal . ' sampai dengan ' . $request->tAkhir . ' Semua Cabang' . '.xlsx');
                    } else {
                        $name_cabang = cabang::select('cabang')->where('id', $pilCabang)->first();
                        return Excel::download(new DataNominatif($seluruh_data, $seluruh_data_proses), 'Data Nominatif Kategori berdasarkan tanggal ' . $request->tAwal . ' sampai dengan ' . $request->tAkhir . ' cabang ' . $name_cabang->cabang .'.xlsx');
                    }
                } else {
                    if ($pilCabang == 'semua') {
                        return Excel::download(new DataNominatif($seluruh_data, $seluruh_data_proses), 'Data Nominatif Kategori keseluruhan Semua Cabang' . '.xlsx');
                    } else {
                        $name_cabang = cabang::select('cabang')->where('id', $pilCabang)->first();
                        return Excel::download(new DataNominatif($seluruh_data, $seluruh_data_proses), 'Data Nominatif Kategori keseluruhan cabang ' . $name_cabang->cabang . '.xlsx');
                    }
                }
            }

        } catch (Exception $th) {
            return $th;
        } catch (QueryException $th){
            return $th;
        }
    }


}
