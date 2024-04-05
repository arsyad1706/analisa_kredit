<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalonNasabah;
use App\Models\ItemModel;
use App\Models\KomentarModel;
use App\Models\PengajuanModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
// use Facade\FlareClient\Stacktrace\File;
use Spatie\PdfToImage\Pdf as PdfToImage;
use File;

class CetakSuratController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $param;
    protected $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    public function index()
    {
        $param['nasabah'] = CalonNasabah::get();
        return view('cetak.cetak-surat', $this->param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        // $this->params['nasabah'] = CalonNasabah::find($id);
        // return view('cetak.cetak-surat', $this->params);
        echo "asd";
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
        //
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

    public function cetak($id)
    {
        $param['dataAspek'] = ItemModel::select('*')->where('level',1)->get();
        $dataNasabah = CalonNasabah::select('calon_nasabah.*','kabupaten.id as kabupaten_id','kabupaten.kabupaten','kecamatan.id as kecamatan_id','kecamatan.id_kabupaten','kecamatan.kecamatan','desa.id as desa_id','desa.id_kabupaten','desa.id_kecamatan','desa.desa')
                    ->join('kabupaten','kabupaten.id','calon_nasabah.id_kabupaten')
                    ->join('kecamatan','kecamatan.id','calon_nasabah.id_kecamatan')
                    ->join('desa','desa.id','calon_nasabah.id_desa')
                    ->where('calon_nasabah.id_pengajuan',$id)
                    ->first();
        $param['dataNasabah'] = $dataNasabah;
        $dataUmum = PengajuanModel::select('pengajuan.id','pengajuan.tanggal','pengajuan.posisi','pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang', 'pengajuan.skema_kredit')
                        ->find($id);
        $param['dataUmum'] = $dataUmum;
        $param['itemKTPNas'] = ItemModel::where('nama', 'Foto KTP Nasabah')->first();
        $param['komentar'] = KomentarModel::where('id_pengajuan', $id)->first();
        $itemSP = ItemModel::where('level', 1)->where('nama', '=', 'Data Umum')->first();
        $param['itemSP'] = $itemSP;

        $dataLS = \App\Models\ItemModel::select('id', 'nama', 'opsi_jawaban', 'level', 'id_parent', 'status_skor', 'is_commentable')
        ->where('level', 2)
        ->where('id_parent', $itemSP->id)
        ->where('nama', 'Laporan SLIK')
        ->get();

        foreach ($dataLS as $key => $value) {
            if ($value->opsi_jawaban == 'file') {
                $dataDetailJawabanText = \App\Models\JawabanTextModel::select('jawaban_text.id', 'jawaban_text.id_pengajuan', 'jawaban_text.id_jawaban', 'jawaban_text.opsi_text', 'item.id as id_item', 'item.nama')
                ->join('item', 'jawaban_text.id_jawaban', 'item.id')
                ->where('jawaban_text.id_pengajuan', $dataUmum->id)
                ->where('jawaban_text.id_jawaban', $value->id)
                ->get();

                foreach ($dataDetailJawabanText as $key => $value2) {
                    $pdfPath = asset('upload/' . $dataUmum->id . '/' . $value2->id_item . '/' . $value2->opsi_text);
                }
            }
        }

        // $fileName = 'Surat Analisa.' . 'pdf';
        // $filePath = public_path() . '/upload/cetak_surat/' . $param['dataUmum']->id;
        // if (!File::isDirectory($filePath)) {
        //     File::makeDirectory($filePath, 493, true);
        // }
        // $pdf = PDF::loadView('cetak.cetak-surat-kusuma', $param);
        // $pdf->save($filePath . '/' . $fileName);


        return view('cetak.cetak-surat-kusuma', $param);

    }

    public function cetakSPPk($id)
    {
        $count = DB::table('log_cetak_kkb')
            ->where('id_pengajuan', $id)
            ->count('*');
        if($count < 1){
            DB::table('log_cetak_kkb')
                ->insert([
                    'id_pengajuan' => $id,
                    'tgl_cetak_sppk' => now()
                ]);
        } else{
            DB::table('log_cetak_kkb')
                ->where('id_pengajuan', $id)
                ->update([
                    'tgl_cetak_sppk' => now()
                ]);
        }
        $dataNasabah = CalonNasabah::select('calon_nasabah.*', 'kabupaten.id as kabupaten_id', 'kabupaten.kabupaten', 'kecamatan.id as kecamatan_id', 'kecamatan.id_kabupaten', 'kecamatan.kecamatan', 'desa.id as desa_id', 'desa.id_kabupaten', 'desa.id_kecamatan', 'desa.desa', 'pengajuan.*')
        ->join('kabupaten', 'kabupaten.id', 'calon_nasabah.id_kabupaten')
        ->join('kecamatan', 'kecamatan.id', 'calon_nasabah.id_kecamatan')
        ->join('desa', 'desa.id', 'calon_nasabah.id_desa')
        ->join('pengajuan', 'pengajuan.id', 'calon_nasabah.id_pengajuan')
        ->where('calon_nasabah.id_pengajuan', $id)
        ->first();

        $param['dataNasabah'] = $dataNasabah;

        $param['dataUmum'] = PengajuanModel::select('pengajuan.id','pengajuan.tanggal','pengajuan.posisi','pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang')
            ->find($id);

        $param['dataCabang'] = DB::table('cabang')
            ->where('id', $param['dataUmum']->id_cabang)
            ->first();

        $param['tglCetak'] = DB::table('log_cetak_kkb')
            ->where('id_pengajuan', $id)
            ->first();

        $kodePincab = $param['dataNasabah']['id_pincab'];
        $kodePenyelia = $param['dataNasabah']['id_penyelia'];
        $param['dataPincab'] = User::where('id', $kodePincab)->get();
        $param['dataPenyelia'] = User::where('id', $kodePenyelia)->get();

        $indexBulan = intval(date('m', strtotime($param['tglCetak']->tgl_cetak_sppk))) - 1;
        $param['tgl'] = date('d', strtotime($param['tglCetak']->tgl_cetak_sppk)) . ' ' . $this->bulan[$indexBulan] . ' ' . date('Y', strtotime($param['tglCetak']->tgl_cetak_sppk));

        $param['installment'] = DB::table('jawaban_text')
            ->where('id_pengajuan', $id)
            ->where('id_jawaban', 140)
            ->first() ?? '0';

        // return view('cetak.cetak-sppk', $param);
        $pdf = PDF::loadView('dagulir.cetak.cetak-sppk', $param);
        return $pdf->download('SPPK-' . $dataNasabah->nama . '.pdf');
    }

    public function cetakPO($id)
    {
        $count = DB::table('log_cetak_kkb')
            ->where('id_pengajuan', $id)
            ->count('*');
        if($count < 1){
            DB::table('log_cetak_kkb')
                ->insert([
                    'id_pengajuan' => $id,
                    'tgl_cetak_po' => now()
                ]);
        } else {
            DB::table('log_cetak_kkb')
                ->where('id_pengajuan', $id)
                ->update([
                    'tgl_cetak_po' => now()
                ]);
        }

        $dataNasabah = CalonNasabah::select('calon_nasabah.*','kabupaten.id as kabupaten_id','kabupaten.kabupaten','kecamatan.id as kecamatan_id','kecamatan.id_kabupaten','kecamatan.kecamatan','desa.id as desa_id','desa.id_kabupaten','desa.id_kecamatan','desa.desa','pengajuan.*')
            ->join('kabupaten','kabupaten.id','calon_nasabah.id_kabupaten')
            ->join('kecamatan','kecamatan.id','calon_nasabah.id_kecamatan')
            ->join('desa','desa.id','calon_nasabah.id_desa')
            ->join('pengajuan','pengajuan.id','calon_nasabah.id_pengajuan')
            ->where('calon_nasabah.id_pengajuan',$id)
            ->first();
        $param['dataNasabah'] = $dataNasabah;

        $param['dataUmum'] = PengajuanModel::select('pengajuan.id','pengajuan.tanggal','pengajuan.posisi','pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang')
            ->find($id);


        $param['tglCetak'] = DB::table('log_cetak_kkb')
            ->where('id_pengajuan', $id)
            ->first();

        $param['dataCabang'] = DB::table('cabang')
            ->where('id', $param['dataUmum']->id_cabang)
            ->first();

        $kodePincab = $param['dataNasabah']['id_pincab'];
        $param['dataPincab'] = User::where('id', $kodePincab)->get();

        $indexBulan = intval(date('m', strtotime($param['tglCetak']->tgl_cetak_po))) - 1;
        $param['tgl'] = date('d', strtotime($param['tglCetak']->tgl_cetak_po)) . ' ' . $this->bulan[$indexBulan] . ' ' . date('Y', strtotime($param['tglCetak']->tgl_cetak_po));

        $param['dataKendaraan'] = DB::table('data_po')
            ->where('id_pengajuan', $id)
            ->select('tipe', 'merk')
            ->first();
        $param['dataPO'] = DB::table('data_po')
            ->where('id_pengajuan', $id)
            ->first();
        // return view('cetak.cetak-po', $param);
        $pdf = PDF::loadView('cetak.cetak-po', $param);
        return $pdf->download('PO-' . $dataNasabah->nama . '.pdf');
    }

    public function cetakPk($id)
    {
        $count = DB::table('log_cetak_kkb')
            ->where('id_pengajuan', $id)
            ->count('tgl_cetak_pk');
        if($count < 1){
            DB::table('log_cetak_kkb')
                ->where('id_pengajuan', $id)
                ->update([
                    'tgl_cetak_pk' => now()
                ]);
        }

        $dataNasabah = CalonNasabah::select('calon_nasabah.*', 'kabupaten.id as kabupaten_id', 'kabupaten.kabupaten', 'kecamatan.id as kecamatan_id', 'kecamatan.id_kabupaten', 'kecamatan.kecamatan', 'desa.id as desa_id', 'desa.id_kabupaten', 'desa.id_kecamatan', 'desa.desa')
        ->join('kabupaten', 'kabupaten.id', 'calon_nasabah.id_kabupaten')
        ->join('kecamatan', 'kecamatan.id', 'calon_nasabah.id_kecamatan')
        ->join('desa', 'desa.id', 'calon_nasabah.id_desa')
        ->where('calon_nasabah.id_pengajuan', $id)
        ->first();

        $param['dataNasabah'] = $dataNasabah;


        $param['tglCetak'] = DB::table('log_cetak_kkb')
            ->where('id_pengajuan', $id)
            ->first();

        $param['dataUmum'] = PengajuanModel::select('pengajuan.id','pengajuan.tanggal','pengajuan.posisi','pengajuan.tanggal_review_penyelia', 'pengajuan.id_cabang')
            ->find($id);

        $param['dataCabang'] = DB::table('cabang')
            ->where('id', $param['dataUmum']->id_cabang)
            ->first();

        $indexBulan = intval(date('m', strtotime($param['tglCetak']->tgl_cetak_pk))) - 1;
        $param['tgl'] = date('d', strtotime($param['tglCetak']->tgl_cetak_pk)) . ' ' . $this->bulan[$indexBulan] . ' ' . date('Y', strtotime($param['tglCetak']->tgl_cetak_pk));


        // return view('dagulir.cetak.cetak-pk', $param);
        $pdf = PDF::loadView('dagulir.cetak.cetak-pk', $param);
        return $pdf->download('PK-' . $dataNasabah->nama . '.pdf');
    }
}
