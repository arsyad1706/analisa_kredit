@foreach ($data_pengajuan as $item)    
<form action="{{ route('post-file-kkb', $item->id_pengajuan) }}" method="POST" enctype="multipart/form-data">
    <div class="modal fade" id="uploadPKModal-{{$item->id_pengajuan}}" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black" id="uploadModalLabel">Upload File PK</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <a target="_blank" href="{{ route('cetak-pk',$item->id_pengajuan)}}">
                    <button class="btn btn-primary">Cetak file PK</button>
                </a>
                <hr>
                @csrf
                <div class="alert alert-primary" role="alert">
                    No PK belum diinputkan.
                </div>
                <input type="hidden" name="tipe_file" value="PK">
                <div class="form-group">
                    <label for="no_pk">No PK</label>
                    <input type="text" class="form-control" name="no_pk" required>
                </div>
                <div class="form-group">
                    <label for="pk">File PK</label>
                    <input type="file" name="pk" id="pk" class="form-control file" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-info text-white" type="submit">Simpan</button>
            </div>
        </div>
    </div>
</div>
</form>
@endforeach