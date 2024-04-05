<?php

namespace App\Http\Controllers;

use App\Models\MstItemPerhitunganKredit;
use App\Models\MstSkemaLimit;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterSkemaLimitController extends Controller
{
    private $param;

    public function __construct()
    {
        $this->param['pageIcon'] = 'fa fa-database';
        $this->param['parentMenu'] = '/skema-limit';
        $this->param['current'] = 'Skema Limit';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $this->param['pageTitle'] = 'List Skema Limit';
            $this->param['btnText'] = 'Tambah Skema';
            $this->param['btnLink'] = route('skema-limit.create');
            $this->param['data'] = DB::table('mst_skema_limit')
                ->join('mst_skema_kredit', 'mst_skema_limit.skema_kredit_id', 'mst_skema_kredit.id')
                ->select('mst_skema_kredit.name', 'mst_skema_limit.*')
                ->when($request->keyword, function($query, $search){
                    return $query->where('name', "like '%" . $search . "%'");
                })
                ->paginate(10);

        } catch(Exception $e) {
            return back()->withErrors('Terjadi kesalahan. ' . $e);
        } catch(QueryException $e){
            return back()->withErrors('Terjadi kesalahan. ' . $e);
        }
        
        return \view('skema-limit.index', $this->param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->param['pageTitle'] = 'Tambah Skema Limit';
        $this->param['dataSkemaKredit'] = DB::table('mst_skema_kredit')
            ->get();
        $this->param['btnText'] = 'List Skema Limit';
        $this->param['btnLink'] = route('skema-limit.index');

        return view('skema-limit.create', $this->param);
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function formula()
    {
        $this->param['pageTitle'] = 'Formula Skema Limit';
        $this->param['btnText'] = 'Tambah';
        $this->param['btnLink'] = route('skema-limit.index');

        return view('skema-limit.formula', $this->param);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $field = array();
            $replace = array('.', ',');

            $skemaLimit = new MstSkemaLimit();
            $skemaLimit->skema_kredit_id = $request->skema_kredit;
            $skemaLimit->from = str_replace($replace, '', $request->nominal_awal);
            $skemaLimit->to = str_replace($replace, '', $request->nominal_akhir) ?? 0;
            $skemaLimit->operator = $request->operator;
            $skemaLimit->save();
            $idSkemaLimit = $skemaLimit->id;

            foreach($request->field as $key => $item){
                array_push($field, [
                    'skema_kredit_limit_id' => $idSkemaLimit,
                    'field' => $request->field[$key] ?? '-',
                    'created_at' => now()
                ]);
            }
            MstItemPerhitunganKredit::insert($field);
            DB::commit();

            return redirect()->route('skema-limit.index')->withStatus('Berhasil menambahkan skema limit.');
        } catch(Exception $e) {
            DB::rollBack();
            return back()->withErrors('Terjadi kesalahan. ' . $e);
        } catch(QueryException $e) {
            DB::rollBack();
            return back()->withErrors('Terjadi kesalahan. ' . $e);
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
        try{
            $this->param['pageTitle'] = 'Detail Skema Limit';
            $this->param['btnText'] = 'List Skema Limit';
            $this->param['btnLink'] = route('skema-limit.index');
            $this->param['data'] = DB::table('mst_skema_limit')
                ->where('id', $id)
                ->first();
            $this->param['itemPerhitunganKredit'] = DB::table('mst_item_perhitungan_kredit')
                ->where('skema_kredit_limit_id', $this->param['data']->id)
                ->get();
            $this->param['dataSkemaKredit'] = DB::table('mst_skema_kredit')
            ->get();

            return view('skema-limit.detail', $this->param);
        } catch(Exception $e){
            return back()->withErrors('Terjadi kesalahan. ' . $e->getMessage());
        } catch(QueryException $e){
            return back()->withErrors('Terjadi kesalahan. ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $this->param['pageTitle'] = 'Edit Skema Limit';
            $this->param['btnText'] = 'List Skema Limit';
            $this->param['btnLink'] = route('skema-limit.index');
            $this->param['data'] = DB::table('mst_skema_limit')
                ->where('id', $id)
                ->first();
            $this->param['itemPerhitunganKredit'] = DB::table('mst_item_perhitungan_kredit')
                ->where('skema_kredit_limit_id', $this->param['data']->id)
                ->get();
            $this->param['dataSkemaKredit'] = DB::table('mst_skema_kredit')
                ->get();

            return view('skema-limit.edit', $this->param);
        } catch(Exception $e){
            return back()->withErrors('Terjadi kesalahan. ' . $e->getMessage());
        } catch(QueryException $e){
            return back()->withErrors('Terjadi kesalahan. ' . $e->getMessage());
        }
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
        DB::beginTransaction();
        try{
            $replace = array('.', ',');
            
            $skemaLimit = MstSkemaLimit::find($id);
            $skemaLimit->skema_kredit_id = $request->skema_kredit;
            $skemaLimit->from = str_replace($replace, '', $request->nominal_awal);
            $skemaLimit->to = str_replace($replace, '', $request->nominal_akhir) ?? 0;
            $skemaLimit->operator = $request->operator;
            $skemaLimit->save();

            foreach($request->field as $key => $item){
                if($request->id_item[$key] == null ||$request->id_item[$key] == ''){
                    MstItemPerhitunganKredit::insert([
                        'field' => $request->field[$key],
                        'skema_kredit_limit_id' => $id
                    ]);
                } else {
                    MstItemPerhitunganKredit::where('id', $request->id_item[$key])
                        ->update([
                            'field' => $item
                        ]);
                }
            }
            if(isset($request->id_deleted)){
                foreach($request->id_deleted as $key => $item){
                    MstItemPerhitunganKredit::where('id', $item)
                        ->delete();
                }
            }

            DB::commit();
            return redirect()->route('skema-limit.index')->withStatus('Berhasil mengedit data.');
        } catch(Exception $e){
            DB::rollBack();
            dd($e);
            return back()->withErrors('Terjadi kesalahan. '.$e->getMessage());
        } catch(QueryException $e){
            DB::rollBack();
            dd($e);
            return back()->withErrors('Terjadi kesalahan. '.$e->getMessage());
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
        DB::beginTransaction();
        try{
            $skemaLimit = MstSkemaLimit::find($id);
            $skemaLimit->delete();
            DB::commit();

            return back()->withStatus('Berhasil menghapus data.');
        } catch(Exception $e){
            DB::rollBack();
            return back()->withErrors('Terjadi kesalahan. '.$e->getMessage());
        } catch(QueryException $e){
            DB::rollBack();
            return back()->withErrors('Terjadi kesalahan. '.$e->getMessage());
        }
    }
}
