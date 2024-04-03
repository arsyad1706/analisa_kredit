<?php

namespace Database\Seeders;

use App\Models\MstItemPerhitunganKredit;
use App\Models\MstSkemaLimit;
use Illuminate\Database\Seeder;

class MstItemPerhitunganMK50Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modalKerja = MstSkemaLimit::select(
            'mst_skema_limit.*',
            'skema.name'
        )
        ->join('mst_skema_kredit AS skema', 'skema.id', 'mst_skema_limit.skema_kredit_id')
        ->where('skema.name', 'Modal Kerja')
        ->where('mst_skema_limit.from', 0)
        ->where('mst_skema_limit.to', 50000000)
        ->first();

        $lev1 = [
            'Neraca',
            'Laba Rugi',
        ];

        $skemaLimit = MstSkemaLimit::select(
                                        'mst_skema_limit.id',
                                    )
                                    ->join('mst_skema_kredit AS kredit', 'kredit.id', 'mst_skema_limit.skema_kredit_id')
                                    ->where('kredit.name', 'Modal Kerja')
                                    ->where('mst_skema_limit.from', 0)
                                    ->where('mst_skema_limit.to', 50000000)
                                    ->first();
        
        for ($i=0; $i < count($lev1); $i++) { 
            $data = MstItemPerhitunganKredit::create([
                'skema_kredit_limit_id' => $skemaLimit->id,
                'field' => $lev1[$i],
                'inputable' => false,
                'level' => 1
            ]);

            if ($lev1[$i] == 'Neraca') {
                $neracaId = $data->id;
                $aktiva = MstItemPerhitunganKredit::create([
                    'skema_kredit_limit_id' => $skemaLimit->id,
                    'field' => 'Aktiva',
                    'inputable' => false,
                    'level' => 2,
                    'parent_id' => $neracaId,
                ]);
                $pasiva = MstItemPerhitunganKredit::create([
                    'skema_kredit_limit_id' => $skemaLimit->id,
                    'field' => 'Pasiva',
                    'inputable' => false,
                    'level' => 2,
                    'parent_id' => $neracaId,
                ]);

                $aktivaChilds = [
                    'Harga',
                    'Kas / Bank',
                    'Persediaan',
                    'Piutang',
                    'Inventaris'
                ];
                
                for ($j=0; $j < count($aktivaChilds); $j++) { 
                    MstItemPerhitunganKredit::create([
                        'skema_kredit_limit_id' => $skemaLimit->id,
                        'field' => $aktivaChilds[$j],
                        'inputable' => true,
                        'level' => 3,
                        'parent_id' => $aktiva->id,
                    ]);
                }
                
                $pasivaChilds = [
                    'Kewajiban',
                    'Utang Dagang',
                    'Utang Bank',
                    'Modal',
                    'Laba'
                ];

                $childUtangBank = [
                    'Baki Debet',
                    'Plafon',
                    'Tenor'
                ];

                for ($j=0; $j < count($pasivaChilds); $j++) { 
                    $pasivaField = MstItemPerhitunganKredit::create([
                        'skema_kredit_limit_id' => $skemaLimit->id,
                        'field' => $pasivaChilds[$j],
                        'inputable' => $pasivaChilds[$j] == 'Utang Bank' ? false : true,
                        'readonly' => $pasivaChilds[$j] == 'Utang Bank',
                        'level' => 3,
                        'parent_id' => $pasiva->id,
                        'have_detail' => $pasivaChilds[$j] == 'Utang Bank'
                    ]);
                    if ($pasivaChilds[$j] == 'Utang Bank') {
                        for ($k=0; $k < count($childUtangBank); $k++) { 
                            MstItemPerhitunganKredit::create([
                                'skema_kredit_limit_id' => $skemaLimit->id,
                                'field' => $childUtangBank[$k],
                                'inputable' => true,
                                'level' => 4,
                                'parent_id' => $pasivaField->id,
                            ]);
                        }
                    }
                }
            }

            if ($lev1[$i] == 'Laba Rugi') {
                $labaRugiId = $data->id;
                
                $sebelum = MstItemPerhitunganKredit::create([
                    'skema_kredit_limit_id' => $skemaLimit->id,
                    'field' => 'Penerimaan Sebelum Kredit',
                    'inputable' => false,
                    'level' => 2,
                    'parent_id' => $labaRugiId,
                ]);
                
                $sebelumChilds = [
                    'Hasil Penjualan',
                    'Pend. Sampingan',
                    'Total'
                ];

                for ($j=0; $j < count($sebelumChilds); $j++) { 
                    MstItemPerhitunganKredit::create([
                        'skema_kredit_limit_id' => $skemaLimit->id,
                        'field' => $sebelumChilds[$j],
                        'inputable' => true,
                        'level' => 3,
                        'parent_id' => $sebelum->id,
                    ]);
                }

                $sesudah = MstItemPerhitunganKredit::create([
                    'skema_kredit_limit_id' => $skemaLimit->id,
                    'field' => 'Penerimaan Sesudah Kredit',
                    'inputable' => false,
                    'level' => 2,
                    'parent_id' => $labaRugiId,
                ]);
                
                for ($j=0; $j < count($sebelumChilds); $j++) { 
                    MstItemPerhitunganKredit::create([
                        'skema_kredit_limit_id' => $skemaLimit->id,
                        'field' => $sebelumChilds[$j],
                        'inputable' => true,
                        'level' => 3,
                        'parent_id' => $sesudah->id,
                    ]);
                }

                $pengeluaranChilds = [
                    'Biaya Usaha',
                    'Biaya Rumah Tangga',
                    'Biaya Bunga Pinjaman',
                    'Biaya Lain-lain',
                    'Total'
                ];

                $pengeluaranSebelumKredit = MstItemPerhitunganKredit::create([
                    'skema_kredit_limit_id' => $skemaLimit->id,
                    'field' => 'Pengeluaran Sebelum Kredit',
                    'inputable' => false,
                    'level' => 2,
                    'parent_id' => $labaRugiId,
                ]);
                
                for ($j=0; $j < count($pengeluaranChilds); $j++) { 
                    MstItemPerhitunganKredit::create([
                        'skema_kredit_limit_id' => $skemaLimit->id,
                        'field' => $pengeluaranChilds[$j],
                        'inputable' => true,
                        'level' => 3,
                        'parent_id' => $pengeluaranSebelumKredit->id,
                    ]);
                }

                $pengeluaranSesudahKredit = MstItemPerhitunganKredit::create([
                    'skema_kredit_limit_id' => $skemaLimit->id,
                    'field' => 'Pengeluaran Sesudah Kredit',
                    'inputable' => false,
                    'level' => 2,
                    'parent_id' => $labaRugiId,
                ]);

                for ($j=0; $j < count($pengeluaranChilds); $j++) { 
                    MstItemPerhitunganKredit::create([
                        'skema_kredit_limit_id' => $skemaLimit->id,
                        'field' => $pengeluaranChilds[$j],
                        'inputable' => true,
                        'level' => 3,
                        'parent_id' => $pengeluaranSesudahKredit->id,
                    ]);
                }
            }
        }

        $lev3 = [
            'Pendapatan Bersih',
            'Pendapatan Bersih Setiap Bulan',
            'Angsuran Pokok Setiap Bulan',
            'Sisa Pendapatan Setiap Bulan',
            'Perputaran Usaha',
            'Keuntungan Usaha',
        ];

        for ($i=0; $i < count($lev3); $i++) { 
            $addOn = '';
            if ($lev3[$i] == 'Perputaran Usaha')
                $addOn = 'Bulan';
            if ($lev3[$i] == 'Keuntungan Usaha')
                $addOn = '%';
            
            MstItemPerhitunganKredit::create([
                'skema_kredit_limit_id' => $skemaLimit->id,
                'field' => $lev3[$i],
                'inputable' => true,
                'level' => 3,
                'add_on' => $addOn != '' ? $addOn : null,
            ]);
        }

        $kebutuhanModalKerja = MstItemPerhitunganKredit::create([
            'skema_kredit_limit_id' => $skemaLimit->id,
            'field' => 'Kebutuhan Modal Kerja',
            'inputable' => false,
            'level' => 2,
        ]);

        $kebutuhanModalKerjaFields = [
            'Persediaan',
            'Piutang',
            'Kas',
            'Total'
        ];

        for ($i=0; $i < count($kebutuhanModalKerjaFields); $i++) { 
            $addOn = '';
            if ($kebutuhanModalKerjaFields[$i] != 'Total')
                $addOn = '%';
            
            MstItemPerhitunganKredit::create([
                'skema_kredit_limit_id' => $skemaLimit->id,
                'field' => $kebutuhanModalKerjaFields[$i],
                'inputable' => true,
                'readonly' => true,
                'level' => 3,
                'add_on' => $addOn != '' ? $addOn : null,
                'parent_id' => $kebutuhanModalKerja->id
            ]);
        }

        $modalKerjaSekarang = MstItemPerhitunganKredit::create([
            'skema_kredit_limit_id' => $skemaLimit->id,
            'field' => 'Modal Kerja Sekarang',
            'inputable' => false,
            'level' => 2,
        ]);

        $modalKerjaSekarangFields = [
            'Persediaan',
            'Piutang',
            'Kas',
            'Total'
        ];

        for ($i=0; $i < count($modalKerjaSekarangFields); $i++) { 
            $addOn = '';
            if ($modalKerjaSekarangFields[$i] != 'Total')
                $addOn = '%';
            
            MstItemPerhitunganKredit::create([
                'skema_kredit_limit_id' => $skemaLimit->id,
                'field' => $modalKerjaSekarangFields[$i],
                'inputable' => true,
                'readonly' => true,
                'level' => 3,
                'add_on' => $addOn != '' ? $addOn : null,
                'parent_id' => $modalKerjaSekarang->id
            ]);
        }

        $lev3 = [
            'Kebutuhan Modal',
            'Utang Bank',
            'Kebutuhan Kredit',
            'Dibulatkan'
        ];

        for ($i=0; $i < count($lev3); $i++) {
            MstItemPerhitunganKredit::create([
                'skema_kredit_limit_id' => $skemaLimit->id,
                'field' => $lev3[$i],
                'inputable' => true,
                'readonly' => true,
                'level' => 3,
            ]);
        }
    }
}
