<?php

namespace App\Http\Controllers\Api\Pemprov;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use Doctrine\DBAL\Query\QueryException;
use Exception;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function listCabang(){
        try{
            $data = Cabang::select('id', 'cabang')
                ->orderBy('id', 'asc')
                ->get();
            return response()->json([
                'data' => $data
            ], 200);
        } catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        } catch(QueryException $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getCabangById(Request $request){
        try{
            $id = $request->get('id');
            $data = Cabang::select('id', 'cabang')
                ->where('id', $id)
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'data' => $data
            ]);
        } catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        } catch(QueryException $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
