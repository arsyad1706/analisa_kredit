<?php

namespace Database\Seeders;

use App\Models\MstProdukKredit;
use App\Models\MstSkemaKredit;
use Illuminate\Database\Seeder;

class MstSkemaKreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MstSkemaKredit::create([
            'name' => 'Modal Kerja',
        ]);
        MstSkemaKredit::create([
            'name' => 'Investasi',
        ]);
        MstSkemaKredit::create([
            'name' => 'Konsumtif',
        ]);
    }
}
