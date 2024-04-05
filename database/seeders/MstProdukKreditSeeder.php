<?php

namespace Database\Seeders;

use App\Models\MstProdukKredit;
use Illuminate\Database\Seeder;

class MstProdukKreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item = ['PKPJ','KKB','Talangan Umroh','Prokesra','Kusuma'];
        for ($i=0; $i < count($item); $i++) { 
            MstProdukKredit::create([
                'name' => $item[$i]
            ]);
        }
    }
}
