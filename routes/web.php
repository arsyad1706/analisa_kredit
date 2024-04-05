<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanKreditController;
use \App\Http\Controllers\KabupatenController;
use \App\Http\Controllers\KecamatanController;
use \App\Http\Controllers\DesaController;
use \App\Http\Controllers\CabangController;
use App\Http\Controllers\MasterItemController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\MerkController;
use \App\Http\Controllers\TipeController;
use \App\Http\Controllers\CetakSuratController;
use App\Http\Controllers\DashboardDireksiController;
use \App\Http\Controllers\LogPengajuanController;
use \App\Http\Controllers\Dagulir\DagulirController;
use App\Http\Controllers\Dagulir\master\KonfirgurasiApi;
use App\Http\Controllers\Dagulir\master\NewCabangController;
use App\Http\Controllers\Dagulir\master\NewDesaController;
use App\Http\Controllers\Dagulir\master\NewItemController;
use App\Http\Controllers\Dagulir\master\NewKabupatenController;
use App\Http\Controllers\Dagulir\master\NewKecamatanController;
use App\Http\Controllers\Dagulir\master\NewMerkController;
use App\Http\Controllers\Dagulir\master\NewTipeController;
use App\Http\Controllers\Dagulir\NewDagulirController;
use App\Http\Controllers\Dagulir\master\NewUserController;
use App\Http\Controllers\DashboardDetailController;
use App\Http\Controllers\GetTokenRequestController;
use App\Http\Controllers\KreditProgram\DashboardKreditProgramController;
use App\Http\Controllers\KreditProgram\MasterDanaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\UploadOnedriveController;
use App\Models\PengajuanModel;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\MasterSkemaLimitController;
use App\Http\Controllers\ProdukKreditController;
use App\Http\Controllers\SkemaKreditController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('test-pengajuan', function () {
    $data = PengajuanModel::where('skema_kredit','')->update(['skema_kredit' => 'Kusuma']);
    return $data;
});
Route::get('/fixScore', [PengajuanKreditController::class, 'fixScore']);
Route::get('/', function () {
    return redirect()->route('login');
});
// one drive
Route::get('/upload-onedrive', [UploadOnedriveController::class, 'index'])->name('upload-onedrive.index');
Route::post('/upload-onedrive', [UploadOnedriveController::class, 'store'])->name('upload-onedrive.store');
Route::get('/onedrive', [UploadOnedriveController::class, 'getAuthorizationToken'])->name('upload-onedrive.test');
Route::get('callback', [UploadOnedriveController::class,'callback']);
Route::get('respone-onedrive-get', [UploadOnedriveController::class,'fileUploadWithChunk']);

Route::get('tes-skor', [PengajuanKreditController::class, 'tesskor'])->name('tesskor');
Route::post('tes-skor', [PengajuanKreditController::class, 'countScore'])->name('tesskor.store');


Route::get('/coming-soon', function(){
    return view('under-construction.index');
})->name('coming-soon');

Route::middleware(['auth', 'verified'])->group(function () {
    // monitor log
    Route::get('/log',[UploadOnedriveController::class,'monitorLog'])->name('monitor.log');

    Route::get('/detail-pengajuan-new/tes', function () {
        return view('dagulir.pengajuan-kredit.detail-pengajuan-jawaban-new');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/detail', [DashboardDetailController::class, 'index'])->name('dashboard-detail');
    Route::get('/dashboard/detail/skema', [DashboardDetailController::class, 'index'])->name('dashboard-detail-skema');
    Route::get('/dashboard/detail/disetujui', [DashboardDetailController::class, 'detailDisetujui'])->name('dashboard-detail-disetujui');
    Route::get('/dashboard/detail/ditolak', [DashboardDetailController::class, 'detailDitolak'])->name('dashboard-detail-ditolak');
    Route::get('/dashboard/detail/diproses', [DashboardDetailController::class, 'detailDiproses'])->name('dashboard-detail-diproses');
    Route::get('/dashboard/detail/rank-cabang', [DashboardDetailController::class, 'detailRankCabang'])->name('dashboard-detail-rank-cabang');
    Route::get('/dashboard/detail/chart-posisi', [DashboardDetailController::class, 'detailPieChartPosisi'])->name('dashboard-detail-chart-posisi');
    Route::get('/dashboard/detail/chart-skema', [DashboardDetailController::class, 'detailPieChartSkema'])->name('dashboard-detail-chart-Skema');
    Route::get('/dashboard/detail/chart-posisi-two', [DashboardDetailController::class, 'detailPieChartPosisiTwo'])->name('dashboard-detail-chart-posisi-two');
    Route::get('/dashboard/detail/chart-skema-two', [DashboardDetailController::class, 'detailPieChartSkemaTwo'])->name('dashboard-detail-chart-Skema-two');

    Route::get('/print-data-nominatif', [DashboardController::class, 'cetak'])->name('print_data_nominatif');

    Route::get('/direksi', [DashboardDireksiController::class, 'index'])->name('dashboard_direksi');


    Route::prefix('pengajuan-dagulir')->name('dagulir.')->group(function () {
        Route::get('/', [DagulirController::class, 'index'])->name('index');
        // edit
        Route::get('edit/{id}',[NewDagulirController::class,'edit'])->name('edit');
        // create
        Route::get('create',[DagulirController::class,'create'])->name('create');
        Route::get('get-data-dagulir/{kode_pendaftaran}',[DagulirController::class,'getPengajuanDagulir'])->name('get-data-dagulir');
        Route::post('create/store',[DagulirController::class,'store'])->name('post');
        // Update Review
        Route::post('review/post',[DagulirController::class,'updateReview'])->name('review-post');
        // Review Penyelia
        Route::get('jawaban-pengajuan/{id}', [NewDagulirController::class, "getDetailJawaban"])->name('detailjawaban');
        // Route::get('list-draft-dagulir', [NewDagulirController::class, "listDraftDagulir"])->name('draft.listDraftDagulir');
        Route::post('jawaban-pengajuan/update/{id}', [DagulirController::class, "updateReviewPenyelia"])->name('updatePenyelia');
        // Send to pinca
        Route::get('review-pincab-new', function() {
            return view('dagulir.pengajuan-kredit.review-pincab-new');
        });
        Route::get('review-jawaban-new', function() {
            return view('dagulir.pengajuan-kredit.detail-pengajuan-jawaban-new');
        });


        // Route::get('pincab-kredit/{id}', [DagulirController::class, "sendToPincab"])->name('check.pincab');
        Route::post('pincab-kredit', [NewDagulirController::class, "sendToPincab"])->name('check.pincab');
        // Review Pincab
        Route::get('jawaban-pengajuan-pincab/{id}', [NewDagulirController::class, "getDetailJawabanPincab"])->name('detailjawaban_pincab');
        Route::post('jawaban-pengajuan-pincab/update/{id}', [DagulirController::class, "updateReviewPincab"])->name('updateReviewPincab');
        // Approval Pincab

        // Route::get('acc-pincab/{id}', [DagulirController::class, "accPengajuan"])->name('acc_pincab');
        Route::post('acc-pincab/{id}', [NewDagulirController::class, "accPengajuan"])->name('acc_pincab');
        // Route::get('dec-pincab/update/{id}', [DagulirController::class, "decPengajuan"])->name('dec_pincab');
        Route::get('dec-pincab/update/{id}', [NewDagulirController::class, "decPengajuan"])->name('dec_pincab');
        // Kirim Dagulir
        Route::post('kirim-sipde', [DagulirController::class, "storeSipde"])->name('store-sipde');

        // Pengajuan
        Route::resource('pengajuan', NewDagulirController::class);
        // check penyelia
        Route::post('pengajuan-kredit/penyelia-kredit', [NewDagulirController::class, "checkPenyeliaKredit"])->name('pengajuan.check.penyeliakredit');
        Route::post('pengajuan-kredit/jawaban-pengajuan', [NewDagulirController::class, "getInsertKomentar"])->name('pengajuan.insertkomentar');
        Route::get('pengajuan-kredit/jawaban-pengajuan/{id}', [NewDagulirController::class, "getDetailJawaban"])->name('pengajuan.detailjawaban');

        // Cetak PDF
        Route::get('pengajuan-kredit/cetak-surat/{id}',[NewDagulirController::class,"CetakPK"])->name('pengajuan.cetak-pdf');

        // Kembalikan posisi
        Route::post('/pengajuan-kredit/kembalikan-ke-posisi-sebelumnya', [NewDagulirController::class, 'kembalikanDataKePosisiSebelumnya'])->name('pengajuan-kredit.kembalikan-ke-posisi-sebelumnya');
        Route::get('pengajuan-kredit/cetak-surat/{id}',[NewDagulirController::class,"CetakPDF"])->name('pengajuan.cetak-pdf');
        Route::post('post-file/{id}', [NewDagulirController::class, 'postFileDagulir'])->name('post-file-dagulir');
        Route::get('/cetak-sppk/{id}', [NewDagulirController::class, 'cetakSPPK'])->name('cetak-sppk-dagulir');
        Route::get('/cetak-pk/{id}', [NewDagulirController::class, 'cetakPK'])->name('cetak-pk-dagulir');
        Route::get('cetak-surat/{id}', [NewDagulirController::class, 'cetakDagulir'])->name('cetak-surat');

        Route::middleware(['Admin'])->prefix('master')->name('master.')->group(function () {
            Route::resource('kabupaten', NewKabupatenController::class);
            Route::get('get-kabupaten',[NewKecamatanController::class,'kabupaten'])->name('get.kabupaten');
            Route::get('get-kecamatan',[NewKecamatanController::class,'kecamatan'])->name('get.kecamatan');
            Route::resource('kecamatan', NewKecamatanController::class);
            Route::resource('desa', NewDesaController::class);
            Route::resource('cabang', NewCabangController::class);

            Route::resource('user', NewUserController::class);
            Route::resource('merk', NewMerkController::class);
            Route::resource('tipe', NewTipeController::class);
            Route::get('konfigurasi-api/login', [KonfirgurasiApi::class,'signin'])->name('api-konfigurasi.signin');
            Route::resource('konfigurasi-api', KonfirgurasiApi::class);
            Route::resource('master-item', NewItemController::class);
            Route::get('add-edit-item', [NewItemController::class, 'addEditItem'])->name('add-edit-item');
            Route::get('data-item-satu', [NewItemController::class, 'dataItemSatu'])->name('getItemSatu');
            Route::get('data-item-tiga', [NewItemController::class, 'dataItemtiga'])->name('getItemTiga');
            Route::get('data-item-empat', [NewItemController::class, 'dataItemEmpat'])->name('getItemEmpat');


            Route::get('/reset-sessions', [NewUserController::class, 'indexSession'])->name('index-session');
            Route::post('/reset-session-post', [NewUserController::class, 'resetSession'])->name('reset-session');

            Route::get('/reset-api-sessions', [NewUserController::class, 'indexAPISession'])->name('index-api-session');
            Route::post('/reset-api-session/post', [NewUserController::class, 'resetAPISession'])->name('reset-api-session');

            Route::post('/reset-password/{id}', [NewUserController::class, 'resetPassword'])->name('reset-password');
        });

        Route::get('pengajuan-kredir/cetak-surat/{id}',[NewDagulirController::class,"CetakPDF"])->name('pengajuan.cetak-pdf');
        Route::prefix('/temp')
            ->name('temp.')
            ->group(function(){
                Route::post('pengajuan-kredit/dagulir', [NewDagulirController::class, "tempDagulir"])->name('dagulir');
                Route::post('pengajuan-kredit/tempFile', [NewDagulirController::class, "tempFile"])->name('file');
                Route::post('pengajuan-kredit/tempFileDataUmum', [NewDagulirController::class, "tempFileDataUmum"])->name('fileDataUmum');
                Route::post('pengajuan-kredit/tempJawaban', [NewDagulirController::class, "tempJawaban"])->name('jawaban');
                Route::get('pengajuan-kredit/continue-draft/{id}', [NewDagulirController::class, 'continueDraft'])->name('continue');
                Route::get('pengajuan-kredit/lanjutkan-draft', [NewDagulirController::class, 'showContinueDraft'])->name('continue-draft');
                Route::get('pengajuan-kredit/list-draft-dagulir', [NewDagulirController::class, "indexTemp"])->name('list-draft-dagulir');
                Route::post('pengajuan-kredit/deleteDraft/{id}', [NewDagulirController::class, 'deleteDraft'])->name('deleteDraft');
            });

        Route::prefix('/notification')
            ->name('notification.')
            ->group(function() {
                Route::get('', [NotificationController::class, 'index'])->name('index');
                Route::post('/hapus', [NotificationController::class, 'delete'])->name('delete');
                Route::post('/get-detail', [NotificationController::class, 'getDetail'])->name('getDetail');
            });

        // Pembatalan Pengajuan
        Route::post('pembatalan-pengajuan',[NewDagulirController::class,'confirmPengajuan'])->name('konfirmasi-pembatalan');

    });
    Route::middleware(['auth'])->group(function () {
            // Dashboard Dana
            Route::get('dashboard-dana',[DashboardKreditProgramController::class,'index'])->name('dana.dashboard');
            // Master Dana
            Route::prefix('master-dana')->group(function () {
                // master dana modal
                Route::get('/',[MasterDanaController::class,'index'])->name('master-dana.index');
                Route::post('update/{id}',[MasterDanaController::class,'update'])->name('master-dana.update');
                // master dana cabang
                Route::get('dana-cabang',[MasterDanaController::class,'danaCabang'])->name('master-dana.cabang.index');
                Route::post('store-cabang',[MasterDanaController::class,'storeCabang'])->name('master-dana.store-cabang');
                Route::post('store-dana',[MasterDanaController::class,'storeDana'])->name('master-dana.store-dana');
                // master alih dana
                Route::get('alih-dana',[MasterDanaController::class,'alihDana'])->name('master-dana.alih-dana');
                Route::post('alih-dana/post',[MasterDanaController::class,'alihDanaPost'])->name('master-dana.alih-dana.post');
            });
            // Get data dari dana cabang
            Route::get('get-data-dari',[MasterDanaController::class,'getDari'])->name('master-dana.dari');
            Route::get('get-data-ke',[MasterDanaController::class,'getKe'])->name('master-dana.ke');
            Route::get('get-cabang-lawan',[MasterDanaController::class,'cabangLawan'])->name('master-dana.lawan');
    });

    // check Pincab
    Route::post('pengajuan-kredit/pincabStatusDetailPost/{id}', [PengajuanKreditController::class, "checkPincabStatusDetailPost"])->name('pengajuan.check.pincab.status.detail.post');
    Route::get('pengajuan-kredit/pincabStatusDetail/{id}', [PengajuanKreditController::class, "checkPincabStatusDetail"])->name('pengajuan.check.pincab.status.detail');
    Route::post('pengajuan-kredit/pincabStatusChangeTolak/{id}', [PengajuanKreditController::class, "checkPincabStatusChangeTolak"])->name('pengajuan.change.pincab.status.tolak');
    Route::get('pengajuan-kredit/pincabStatusChange/{id}', [PengajuanKreditController::class, "checkPincabStatusChange"])->name('pengajuan.change.pincab.status');
    Route::get('pengajuan-kredit/pincabStatus', [PengajuanKreditController::class, "checkPincabStatus"])->name('pengajuan.check.pincab.status');
    Route::get('pengajuan-kredit/pincab/{id}', [PengajuanKreditController::class, "checkPincab"])->name('pengajuan.check.pincab');
    Route::post('post-file/{id}', [PengajuanKreditController::class, 'postFileKKB'])->name('post-file-kkb');
    // cek -sub-column option
    Route::get('pengajuan-kredit/cek-sub-column', [PengajuanKreditController::class, "checkSubColumn"])->name('cek-sub-column');

    // cek get-item-jaminan-by-kategori jaminan utama
    Route::get('pengajuan-kredit/get-item-jaminan-by-kategori-jaminan-utama', [PengajuanKreditController::class, "getItemJaminanByKategoriJaminanUtama"])->name('get-item-jaminan-by-kategori-jaminan-utama');

    // cek get-item-jaminan-by-kategori jaminan tambahan
    Route::get('pengajuan-kredit/get-item-jaminan-by-kategori', [PengajuanKreditController::class, "getItemJaminanByKategoriJaminanTambahan"])->name('get-item-jaminan-by-kategori');
    // check Staf analisa
    Route::get('pengajuan-kredit/staf-analisa/{id}', [PengajuanKreditController::class, "checkStafAnalisa"])->name('pengajuan.check.stafanalisa');
    // check penyelia
    Route::post('pengajuan-kredit/penyelia-kredit', [PengajuanKreditController::class, "checkPenyeliaKredit"])->name('pengajuan.check.penyeliakredit');
    Route::post('pengajuan-kredit/jawaban-pengajuan', [PengajuanKreditController::class, "getInsertKomentar"])->name('pengajuan.insertkomentar');
    Route::get('pengajuan-kredit/jawaban-pengajuan/{id}', [PengajuanKreditController::class, "getDetailJawaban"])->name('pengajuan.detailjawaban');
    Route::post('pengajuan-kredit/jawaban-pengajuan-penyelia', [PengajuanKreditController::class, "storeAspekPenyelia"])->name('pengajuan.insertkomentarPenyelia');
    Route::get('pengajuan-kredit/update-posisi/{id}', [PengajuanKreditController::class, "backToInputProses"])->name('pengajuan.backToInputProses');

    Route::get('getkecamatan', [PengajuanKreditController::class, "getkecamatan"])->name('getKecamatan');
    Route::get('getdesa', [PengajuanKreditController::class, "getdesa"])->name('getDesa');

    Route::post('pengajuan-kredit/temp/nasabah', [PengajuanKreditController::class, 'tempNasabah'])
        ->name('pengajuan-kredit.temp.nasabah');

    Route::post('pengajuan-kredit/temp/file', [PengajuanKreditController::class, 'tempFile'])
        ->name('pengajuan-kredit.temp.file');

    Route::delete('pengajuan-kredit/temp/file', [PengajuanKreditController::class, 'delTempFile']);

    Route::post('pengajuan-kredit/temp/jawaban', [PengajuanKreditController::class, 'tempJawaban'])
    ->name('pengajuan-kredit.temp.jawaban');
    Route::post('pengajuan-kredit/temp/data-po', [PengajuanKreditController::class, 'saveDataPOTemp'])->name('pengajuan-kredit.save-data-po');

    Route::resource('pengajuan-kredit', PengajuanKreditController::class);

    Route::delete('/delete-pengajuan-kredit/{id}', [PengajuanKreditController::class, 'delete'])->name('delete-pengajuan-kredit');
    Route::put('/restore-pengajuan-kredit', [PengajuanKreditController::class, 'restore'])->name('restore-pengajuan-kredit');

    // Route::post('pengajuan-kredit/create', PengajuanKreditController::class);
    Route::get('pengajuan-kredit/continue-draft', [PengajuanKreditController::class, 'continueDraft'])->name('pengajuan-kredit.continue');
    Route::get('lanjutkan-draft', [PengajuanKreditController::class, 'showContinueDraft'])->name('pengajuan-kredit.continue-draft');
    Route::get('user-json/{role}', [PengajuanKreditController::class, 'getUserJson'])->name('get_user_json');
        Route::post('post-skema-kredit/{tempId}', [PengajuanKreditController::class, 'saveSkemaKreditDraft'])->name('save-skema-kredit-draft');

    // master item
    Route::get('/master-item/addEditItem', [MasterItemController::class, 'addEditItem']);
    Route::get('data-item-satu', [MasterItemController::class, 'dataItemSatu'])->name('getItemSatu');
    Route::get('data-item-tiga', [MasterItemController::class, 'dataItemtiga'])->name('getItemTiga');
    Route::get('data-item-empat', [MasterItemController::class, 'dataItemEmpat'])->name('getItemEmpat');

    Route::group(['middleware' => 'Admin'], function () {
        Route::resource('kabupaten', KabupatenController::class);
        Route::resource('kecamatan', KecamatanController::class);
        Route::resource('desa', DesaController::class);
        Route::resource('cabang', CabangController::class);
        Route::resource('user', UserController::class);
        Route::resource('merk', MerkController::class);
        Route::resource('tipe', TipeController::class);
        Route::resource('master-item', MasterItemController::class);
        Route::resource('produk-kredit', ProdukKreditController::class);
        Route::resource('skema-kredit', SkemaKreditController::class);
        //Route::get('')
        Route::resource('skema-limit', MasterSkemaLimitController::class);
        Route::get('/skema-limit/formula/{id}', [MasterSkemaLimitController::class, 'formula'])->name('get-formula');
        Route::get('/reset-sessions', [UserController::class, 'indexSession'])->name('index-session');
        Route::post('/reset-session/{id}', [UserController::class, 'resetSession'])->name('reset-session');
        Route::get('/reset-api-sessions', [UserController::class, 'indexAPISession'])->name('index-api-session');
        Route::post('/reset-api-session/{id}', [UserController::class, 'resetAPISession'])->name('reset-api-session');
    });


    Route::get('change-password', [UserController::class, 'changePassword'])->name('change_password');
    Route::put('change-password/{id}', [UserController::class, 'updatePassword'])->name('update_password');
    Route::post('reset-password/{id}', [UserController::class, 'resetPassword'])->name('reset-password');

    // Cetak Surat
    Route::get('cetak-surat/{id}', [CetakSuratController::class, 'cetak'])->name('cetak');

    // cetak Data Nominatif
    // Route::gety('cetak-surat', CetakSuratController::class);

    Route::get('/pengajuan-kredit/get-item-jaminan-by-kategori-jaminan-utama-edit', [PengajuanKreditController::class, 'getEditJaminanKategori'])->name('get-item-jaminan-by-kategori-jaminan-utama-edit');
    Route::get('/pengajuan-kredit/get-item-jaminan-by-kategori-jaminan-tambahan-edit', [PengajuanKreditController::class, 'getEditJaminanKategoriTambahan'])->name('get-item-jaminan-by-kategori-edit');
    Route::get('/pengajuan-kredit/get-ijin-usaha', [PengajuanKreditController::class, 'getIjinUsaha'])->name('get-ijin-usaha');

    Route::get('/draft-pengajuan-kredit', [PengajuanKreditController::class, 'draftPengajuanKredit'])->name('pengajuan-kredit-draft');
    Route::get('/draft/continue/{id}', [PengajuanKreditController::class, 'continueDraft'])->name('draft.continue');
    Route::delete('/draft/delete/{id}', [PengajuanKreditController::class, 'deleteDraft'])->name('draft.destroy');

    // Cetak SPPK, PO, PK
    Route::get('/cetak-sppk/{id}', [CetakSuratController::class, 'cetakSPPK'])->name('cetak-sppk');
    Route::get('/cetak-po/{id}', [CetakSuratController::class, 'cetakPO'])->name('cetak-po');
    Route::get('/cetak-pk/{id}', [CetakSuratController::class, 'cetakPK'])->name('cetak-pk');

    Route::get('/get-merk-kendaraan', [PengajuanKreditController::class, 'getMerkKendaraan'])->name('get-merk-kendaraan');
    Route::get('/get-tipe-kendaraan', [PengajuanKreditController::class, 'getTipeByMerk'])->name('get-tipe-kendaraan');

    Route::post('/pengajuan-kredit/kembalikan-ke-posisi-sebelumnya', [PengajuanKreditController::class, 'kembalikanDataKePosisiSebelumnya'])->name('pengajuan-kredit.kembalikan-ke-posisi-sebelumnya');

    // Pembayaran
    Route::get('pembayaran',[PembayaranController::class,'index'])->name('pembayaran.index');
    Route::post('pembayaran/upload',[PembayaranController::class,'upload'])->name('pembayaran.upload');
    Route::post('pembayaran/upload-data',[PembayaranController::class,'upload_data'])->name('pembayaran.upload-data');
    Route::get('pembayaran/check-data',[PembayaranController::class,'store'])->name('pembayaran.store');
    Route::post('pembayaran/proses-data',[PembayaranController::class,'proses_data'])->name('pembayaran.proses');

    Route::get('/get-skema-kredit', [PengajuanKreditController::class, 'getSkemaKredit'])->name('get-skema-kredit');
    Route::get('/get-skema-limit', [PengajuanKreditController::class, 'getSkemaLimit'])->name('get-skema-limit');

    Route::post('/save-data-perhitungan-temp', [PengajuanKreditController::class, 'saveDataPerhitunganTemp'])->name('pengajuan-kredit.save-data-perhitungan-temp');
    Route::post('/edit-perhitungan-kredit', [PengajuanKreditController::class, 'editPerhitunganKredit'])->name('pengajuan-kredit.edit-perhitungan-kredit');
    Route::get('/get-perhitungan-kredit-lev1', [PengajuanKreditController::class, 'getPerhitunganKreditLev1'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev1');
    Route::get('/get-perhitungan-kredit-lev2', [PengajuanKreditController::class, 'getPerhitunganKreditLev2'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev2');
    Route::get('/get-perhitungan-kredit-lev3', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev3'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev3');
    Route::get('/get-perhitungan-kredit-lev3-edit', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev3Edit'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev3-edit');
    Route::get('/get-perhitungan-kredit-lev3-noparent/{idClnNasabah}', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev3NoParent']);
    Route::get('/get-perhitungan-kredit-lev3-noparent-edit/{idClnNasabah}', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev3NoParentEdit']);
    Route::get('/get-perhitungan-kredit-lev2-noparent', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev2NoParent'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev2-noparent');
    Route::get('/get-perhitungan-kredit-lev3-noparent2', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev3NoParent2'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev3-noparent2');
    Route::get('/get-perhitungan-kredit-lev3-noparent2-edit', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev3NoParent2Edit'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev3-noparent2-edit');
    Route::post('/save-data-periode-aspek-keuangan', [PengajuanKreditController::class, 'saveDataPeriodeAspekKeuangan'])->name('pengajuan-kredit.save-data-periode-aspek-keuangan');
    Route::put('/update-data-periode-aspek-keuangan', [PengajuanKreditController::class, 'updateDataPeriodeAspekKeuangan'])->name('pengajuan-kredit.update-data-periode-aspek-keuangan');
    Route::get('/get-periode-perhitungan-kredit-edit', [PengajuanKreditController::class, 'getPeriodeAspekKeuanganEdit'])->name('get-periode-perhitungan-kredit-edit');
    Route::get('/get-periode-perhitungan-kredit-draft', [PengajuanKreditController::class, 'getPeriodeAspekKeuanganDraft'])->name('get-periode-perhitungan-kredit-draft');
});
Route::get('test',[PembayaranController::class,'checkPembayaran']);

require __DIR__ . '/auth.php';
