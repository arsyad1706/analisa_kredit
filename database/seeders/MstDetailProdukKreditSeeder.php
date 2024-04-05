<?php

namespace Database\Seeders;

use App\Models\MstDetailProdukKredit;
use App\Models\MstProdukKredit;
use App\Models\MstSkemaKredit;
use Illuminate\Database\Seeder;

class MstDetailProdukKreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skema_modal_kerja = MstSkemaKredit::select('id', 'name')->where('name', 'Modal Kerja')->first();
        $skema_investasi = MstSkemaKredit::select('id', 'name')->where('name', 'Investasi')->first();
        $skema_konsumtif = MstSkemaKredit::select('id', 'name')->where('name', 'Konsumtif')->first();

        //PKPJ
        $pkpj = MstProdukKredit::select('id', 'name')->where('name', 'PKPJ')->first();
        MstDetailProdukKredit::create([
            'produk_kredit_id' => $pkpj->id,
            'skema_kredit_id' => $skema_modal_kerja->id,
        ]);
        MstDetailProdukKredit::create([
            'produk_kredit_id' => $pkpj->id,
            'skema_kredit_id' => $skema_investasi->id,
        ]);

        // Prokesra
        $prokesra = MstProdukKredit::select('id', 'name')->where('name', 'Prokesra')->first();
        MstDetailProdukKredit::create([
            'produk_kredit_id' => $prokesra->id,
            'skema_kredit_id' => $skema_modal_kerja->id,
        ]);
        MstDetailProdukKredit::create([
            'produk_kredit_id' => $prokesra->id,
            'skema_kredit_id' => $skema_investasi->id,
        ]);

        // Kusuma
        $kusuma = MstProdukKredit::select('id', 'name')->where('name', 'Kusuma')->first();
        MstDetailProdukKredit::create([
            'produk_kredit_id' => $kusuma->id,
            'skema_kredit_id' => $skema_modal_kerja->id,
        ]);
        MstDetailProdukKredit::create([
            'produk_kredit_id' => $kusuma->id,
            'skema_kredit_id' => $skema_investasi->id,
        ]);

        // Talangan Umroh
        $umroh = MstProdukKredit::select('id', 'name')->where('name', 'Talangan Umroh')->first();
        MstDetailProdukKredit::create([
            'produk_kredit_id' => $umroh->id,
            'skema_kredit_id' => $skema_konsumtif->id,
        ]);

        // KKB
        $kkb = MstProdukKredit::select('id', 'name')->where('name', 'KKB')->first();
        MstDetailProdukKredit::create([
            'produk_kredit_id' => $kkb->id,
            'skema_kredit_id' => $skema_konsumtif->id,
        ]);
    }
}
