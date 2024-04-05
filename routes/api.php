<?php

use App\Http\Controllers\Api\PengajuanAPIController;
use App\Http\Middleware\APIToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [PengajuanAPIController::class, 'login']);
Route::get('/get-session-check/{id}', [PengajuanAPIController::class, 'getSessionCheck']);

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('/profile', function(Request $request){
        return $request->user();
    });

    Route::post('/logout', [PengajuanAPIController::class, 'logout']);
});

Route::prefix('kkb')->middleware(['auth:sanctum'])->group(function(){
    Route::middleware([APIToken::class])->group(function(){
        Route::get('/get-data-pengajuan/{id}/{user_id}', [PengajuanAPIController::class, 'getDataPengajuan']);
        Route::get('/get-data-pengajuan-by-id/{id}', [PengajuanAPIController::class, 'getDataPengajuanById']);
        Route::get('/get-data-pengajuan-search/{user_id}', [PengajuanAPIController::class, 'getDataPengajuanSearch']);
        Route::get('/get-data-staf-cabang/{kode_cabang}', [PengajuanAPIController::class, 'getDataStafByCabang']);
        Route::get('/get-data-users-cabang/{kode_cabang}', [PengajuanAPIController::class, 'getDataUsersByCabang']);
        Route::get('/get-data-users/{nip}', [PengajuanAPIController::class, 'getDataUsers']);
        Route::get('/get-data-users-by-id/{id}', [PengajuanAPIController::class, 'getDataUserById']);
        Route::get('/get-cabang/{kode}', [PengajuanAPIController::class, 'getCabang']);
        Route::get('/get-cabang', [PengajuanAPIController::class, 'getAllCabang']);
    });
});
Route::prefix('kkb')->group(function(){
    Route::middleware([APIToken::class])->group(function(){
        Route::get('/get-data-pengajuan/{id}/{user_id}', [PengajuanAPIController::class, 'getDataPengajuan']);
        Route::get('/get-data-pengajuan-by-id/{id}', [PengajuanAPIController::class, 'getDataPengajuanById']);
        Route::get('/get-data-pengajuan-search/{user_id}', [PengajuanAPIController::class, 'getDataPengajuanSearch']);
        Route::get('/get-data-users-cabang/{kode_cabang}', [PengajuanAPIController::class, 'getDataUsersByCabang']);
        Route::get('/get-data-users/{nip}', [PengajuanAPIController::class, 'getDataUsers']);
        Route::get('/get-data-users-by-id/{id}', [PengajuanAPIController::class, 'getDataUserById']);
        Route::get('/get-cabang/{kode}', [PengajuanAPIController::class, 'getCabang']);
        Route::get('/get-cabang', [PengajuanAPIController::class, 'getAllCabang']);
    });
});

// Route::prefix('v1')->middleware(['auth:sanctum'])->group(function(){
//     Route::middleware([APIToken::class])->group(function(){
//         Route::get('get-sum-cabang', [PengajuanAPIController::class, 'getSumPengajuan']);
//         // Route::get('/get-posisi-pengajuan', [PengajuanAPIController::class, 'getPosisiPengajuan']);
//         Route::get('get-posisi-pengajuan', [PengajuanAPIController::class, 'getPosisiPengajuan']);
//         Route::get('get-count-pengajuan', [PengajuanAPIController::class, 'getCountPengajuan']);
//         Route::get('get-cabang', [PengajuanAPIController::class, 'getAllCabangMobile']);
//         Route::get('get-sum-skema', [PengajuanAPIController::class, 'getSumSkema']);
//         Route::get('get-list-pengajuan-by-id/{id}', [PengajuanAPIController::class, 'getListPengajuanById']);
//         Route::get('get-list-pengajuan/{user_id}', [PengajuanAPIController::class, 'getListPengajuan']);
//         Route::get('get-list-pengajuan', [PengajuanAPIController::class, 'getListPengajuanByCabang']);
//         Route::get('get-ranking-cabang', [PengajuanAPIController::class, 'rangkingCabang']);
//     });
// });
Route::prefix('v1')->group(function(){
    Route::middleware([APIToken::class])->group(function(){
        Route::get('get-sum-cabang', [PengajuanAPIController::class, 'getSumPengajuan']);
        // Route::get('/get-posisi-pengajuan', [PengajuanAPIController::class, 'getPosisiPengajuan']);
        Route::get('get-posisi-pengajuan', [PengajuanAPIController::class, 'getPosisiPengajuan']);
        Route::get('get-count-pengajuan', [PengajuanAPIController::class, 'getCountPengajuan']);
        Route::get('get-count-year-pengajuan', [PengajuanAPIController::class, 'getCountYearPengajuan']);
        Route::get('get-cabang', [PengajuanAPIController::class, 'getAllCabangMobile']);
        Route::get('get-sum-skema', [PengajuanAPIController::class, 'getSumSkema']);
        Route::get('get-list-pengajuan/{user_id}', [PengajuanAPIController::class, 'getListPengajuan']);
        Route::get('get-list-pengajuan-by-id/{id}', [PengajuanAPIController::class, 'getListPengajuanById']);
        Route::get('get-ranking-cabang', [PengajuanAPIController::class, 'rangkingCabang']);
    });
});


require __DIR__ . '/pemprov.php';
