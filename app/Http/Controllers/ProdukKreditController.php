<?php

namespace App\Http\Controllers;

use App\Models\MstDetailProdukKredit;
use App\Models\MstProdukKredit;
use App\Models\MstSkemaKredit;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukKreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $param;
    
    public function __construct()
    {
        $this->param['pageIcon'] = 'fa fa-database';
        $this->param['parentMenu'] = '/tipe';
        $this->param['current'] = 'Tipe';
    }

    public function index(Request $request)
    {
        $this->param['pageTitle'] = 'List Produk Kredit';
        $this->param['btnText'] = 'Tambah Produk';
        $this->param['btnLink'] = route('produk-kredit.create');
        $this->param['dataProdukKredit'] = MstProdukKredit::paginate(10);

        return \view('produk-kredit.index', $this->param);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->param['pageTitle'] = 'Tambah Produk';
        $this->param['btnText'] = 'List Produk';
        $this->param['btnLink'] = route('produk-kredit.index');
        $this->param['dataProdukKredit'] = MstProdukKredit::all();
        $this->param['dataSkemaKredit'] = MstSkemaKredit::all();
        return \view('produk-kredit.create', $this->param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:mst_produk_kredit,name',
        ], [
            'name.required' => 'Nama produk kredit harus diisi.',
            'name.unique' => 'Nama produk kredit telah digunakan.'
        ]);

        try{
            MstProdukKredit::create([
                'name' => $request->name
            ]);

            $last_produk_kredit = MstProdukKredit::latest('id')->first();
            $dataSkema = $request['skema_kredit_id'];

            for ($i=0; $i < count($dataSkema); $i++) { 
                MstDetailProdukKredit::create([
                    'produk_kredit_id' => $last_produk_kredit->id,
                    'skema_kredit_id' => $dataSkema[$i],
                    'created_at' => now(),
                ]);
            }

            return redirect()->route('produk-kredit.index')->withStatus('Data berhasil ditambahkan.');
        } catch(Exception $e){
            DB::rollBack();
            return redirect()->route('produk-kredit.index')->withStatus('Terjadi kesalahan. '.$e->getMessage());
        } catch(QueryException $e){
            DB::rollBack();
            return redirect()->route('produk-kredit.index')->withStatus('Terjadi kesalahan. '.$e->getMessage());
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
        $this->param['pageTitle'] = 'Edit Produk Kredit';
        $this->param['name'] = MstProdukKredit::find($id);
        $this->param['btnText'] = 'List Produk Kredit';
        $this->param['btnLink'] = route('produk-kredit.index');
        $this->param['dataDetailProdukKredit'] = MstDetailProdukKredit::with('skemaKredit')->where('produk_kredit_id', $id)->get();
        $dataDetailProdukKreditNot = MstDetailProdukKredit::where('produk_kredit_id', $id)->pluck('skema_kredit_id');
        $this->param['dataSkemaKredit'] = MstSkemaKredit::whereNotIn('id', $dataDetailProdukKreditNot)->get();
        return view('produk-kredit.edit', $this->param);
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
        $name = MstProdukKredit::findOrFail($id);
        $uniqueName = $request->name != '' && $request->name != $name->name ? '|unique:mst_produk_kredit,name' : '';

        $validatedData = $request->validate(
            [
                'name' => 'required'.$uniqueName,
            ],
            [
                'name.required' => 'Nama produk kredit harus diisi.',
                'name.unique' => 'Nama produk kredit telah digunakan.'
            ]
        );

        try {
            $name->name = $request->get('name');
            $name->save();
            $dataSkema = $request->input('skema_kredit_id');

            foreach ($dataSkema as $skema_kredit_id) {
                MstDetailProdukKredit::updateOrCreate(
                    ['produk_kredit_id' => $id, 'skema_kredit_id' => $skema_kredit_id],
                    ['updated_at' => now()]
                );
            }
            MstDetailProdukKredit::where('produk_kredit_id', $id)
                ->whereNotIn('skema_kredit_id', $dataSkema)
                ->delete();
        } catch (\Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }

        return redirect()->route('produk-kredit.index')->withStatus('Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            MstDetailProdukKredit::where('produk_kredit_id', $id)->delete();
            MstProdukKredit::where('id', $id)->delete();
        } catch (Exception $e) {
            return back()->withError('Terjadi kesalahan.');
        } catch (QueryException $e) {
            return back()->withError('Terjadi kesalahan.');
        }

        return redirect()->route('produk-kredit.index')->withStatus('Data berhasil dihapus.');
    }
}
