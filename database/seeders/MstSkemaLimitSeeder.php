<?php

namespace Database\Seeders;

use App\Models\MstSkemaKredit;
use App\Models\MstSkemaLimit;
use Illuminate\Database\Seeder;

class MstSkemaLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modalKerja = MstSkemaKredit::select('id', 'name')->where('name', 'Modal Kerja')->first();
        $investasi = MstSkemaKredit::select('id', 'name')->where('name', 'Investasi')->first();
        $konsumtif = MstSkemaKredit::select('id', 'name')->where('name', 'Konsumtif')->first();


        // Modal Kerja
        MstSkemaLimit::create([
            'skema_kredit_id' => $modalKerja->id,
            'from' => 0,
            'to' => 50000000,
            'operator' => '<='
        ]);
        MstSkemaLimit::create([
            'skema_kredit_id' => $modalKerja->id,
            'from' => 50000000,
            'to' => 350000000,
            'operator' => '-'
        ]);
        MstSkemaLimit::create([
            'skema_kredit_id' => $modalKerja->id,
            'from' => 350000000,
            'operator' => '>'
        ]);

        // Investasi
        MstSkemaLimit::create([
            'skema_kredit_id' => $investasi->id,
            'from' => 0,
            'to' => 50000000,
            'operator' => '<='
        ]);
        MstSkemaLimit::create([
            'skema_kredit_id' => $investasi->id,
            'from' => 50000000,
            'to' => 350000000,
            'operator' => '-'
        ]);
        MstSkemaLimit::create([
            'skema_kredit_id' => $investasi->id,
            'from' => 350000000,
            'operator' => '>'
        ]);
    }
}
