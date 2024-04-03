<div class="table-responsive">
  <table class="table table-hover table-custom">
      <thead>
          <tr class="table-primary">
              <th class="text-center">#</th>
              <th>Nama</th>
              <th>Aksi</th>
          </tr>
      </thead>
      <tbody>
          @php
              $page = Request::get('page');
              $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
          @endphp
          @foreach ($dataSkemaKredit as $item)
              <tr class="border-bottom-primary">
                  <td class="text-center text-muted">{{ $no }}</td>
                  <td>{{ $item->name }}</td>
                  <td>
                      <div class="form-inline btn-action">
                          <a href="{{ route('skema-kredit.edit', $item->id) }}" class="mr-2">
                              <button type="button" id="PopoverCustomT-1" class="btn btn-rgb-primary btn-sm"
                                  data-toggle="tooltip" title="Edit" data-placement="top"><span
                                      class="fa fa-edit fa-sm"></span></button>
                          </a>
                            <button type="button" class="btn btn-rgb-danger btn-sm" data-toggle="modal" data-target="#confirmDeleteSkema{{$item->id}}">
                                <span class="fa fa-trash fa-sm"></span>
                            </button>
                            <div class="modal fade" id="confirmDeleteSkema{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteSkemaLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmDeleteSkemaLabel">Konfirmasi</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin akan menghapus data ini?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('skema-kredit.destroy', $item->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                      </div>
                  </td>
              </tr>
              @php
                  $no++;
              @endphp
          @endforeach
      </tbody>
  </table>
  <div class="pull-right">
        {{ $dataSkemaKredit->appends(Request::all())->links('vendor.pagination.custom') }}
  </div>
</div>
