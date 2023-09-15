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
use \App\Http\Controllers\LogPengajuanController;
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
Route::get('/fixScore', [PengajuanKreditController::class, 'fixScore']);
Route::get('/', function () {
    return redirect()->route('login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/print-data-nominatif', [DashboardController::class, 'cetak'])->name('print_data_nominatif');
    // check Pincab
    Route::post('pengajuan-kredit/pincabStatusDetailPost', [PengajuanKreditController::class, "checkPincabStatusDetailPost"])->name('pengajuan.check.pincab.status.detail.post');
    Route::get('pengajuan-kredit/pincabStatusDetail/{id}', [PengajuanKreditController::class, "checkPincabStatusDetail"])->name('pengajuan.check.pincab.status.detail');
    Route::get('pengajuan-kredit/pincabStatusChangeTolak/{id}', [PengajuanKreditController::class, "checkPincabStatusChangeTolak"])->name('pengajuan.change.pincab.status.tolak');
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

    Route::get('/get-skema-kredit', [PengajuanKreditController::class, 'getSkemaKredit'])->name('get-skema-kredit');
    Route::get('/get-skema-limit', [PengajuanKreditController::class, 'getSkemaLimit'])->name('get-skema-limit');

    Route::post('/save-data-perhitungan-temp', [PengajuanKreditController::class, 'saveDataPerhitunganTemp'])->name('pengajuan-kredit.save-data-perhitungan-temp');
    Route::get('/get-perhitungan-kredit-lev1', [PengajuanKreditController::class, 'getPerhitunganKreditLev1'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev1');
    Route::get('/get-perhitungan-kredit-lev2/{parent_id}', [PengajuanKreditController::class, 'getPerhitunganKreditLev2'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev2');
    Route::get('/get-perhitungan-kredit-lev3', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev3'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev3');
    Route::get('/get-perhitungan-kredit-lev3-noparent/{idClnNasabah}', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev3NoParent']);
    Route::get('/get-perhitungan-kredit-lev2-noparent', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev2NoParent'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev2-noparent');
    Route::get('/get-perhitungan-kredit-lev3-noparent2', [PengajuanKreditController::class, 'getDataPerhitunganKreditLev3NoParent2'])->name('pengajuan-kredit.get-data-perhitungan-kredit-lev3-noparent2');
    Route::post('/save-data-periode-aspek-keuangan', [PengajuanKreditController::class, 'saveDataPeriodeAspekKeuangan'])->name('pengajuan-kredit.save-data-periode-aspek-keuangan');
});

require __DIR__ . '/auth.php';
