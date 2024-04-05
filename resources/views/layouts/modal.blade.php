<!-- Modal -->
@foreach ($data_pengajuan as $item)
    <div class="modal fade" id="exampleModal-{{$item->id_pengajuan}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah data pengajuan <strong>disetujui/ditolak</strong>
            </div>
            <div class="modal-footer">
                <a type="button" href="{{ route('pengajuan.change.pincab.status.tolak',$item->id_pengajuan) }}" class="btn btn-danger text-white" >Ditolak</a>
                <a type="button" href="{{ route('pengajuan.change.pincab.status',$item->id_pengajuan) }}" class="btn btn-info text-white">Disetujui</a>
            </div>
            </div>
        </div>
    </div>
@endforeach
{{-- <a href="{{ route('pengajuan.change.pincab.status',$item->id_pengajuan) }}" class="btn btn-warning">Disetujui / Ditolak</a> --}}

