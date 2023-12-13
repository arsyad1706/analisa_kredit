<?php
namespace App\Repository;

use App\Models\Kecamatan;
use App\Models\PengajuanDagulir;
use Illuminate\Database\Eloquent\Builder;

class PengajuanDagulirRepository
{
    function get($search, $limit=10, $page=1, $role) {
        if ($role == 'Staf Analis Kredit') {
            $data = PengajuanDagulir::with('pengajuan')->where(function($query) use ($search) {
                $query->where('kode_pendaftaran','like', "%$search%")
                        ->orWhere('nama','like', "%$search%")
                        ->orWhere('kode_pendaftaran','like', "%$search%");
            })
            ->latest()
            ->paginate($limit);
        }else if ($role == 'Penyelia Kredit') {
            $data = PengajuanDagulir::whereHas('pengajuan', function (Builder $query) {
                        $query->where('pengajuan.id_penyelia', auth()->user()->id);
               })
                ->where(function($query) use ($search) {
                    $query->where('kode_pendaftaran','like', "%$search%")
                            ->orWhere('nama','like', "%$search%")
                            ->orWhere('kode_pendaftaran','like', "%$search%");
                })
            ->latest()
            ->paginate($limit);
        }
        return $data;
    }

    function detail($id) {
        $data = PengajuanDagulir::with('pengajuan',
                                'kec_ktp:id,kecamatan',
                                'kotakab_ktp:id,kabupaten',
                                'kec_dom:id,kecamatan',
                                'kotakab_dom:id,kabupaten',
                                'kec_usaha:id,kecamatan',
                                'kotakab_usaha:id,kabupaten')
                ->where('id',$id)->first();
        return $data;
    }
}
