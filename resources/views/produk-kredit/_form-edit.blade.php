<form action="{{ route('produk-kredit.update', $name->id) }}" method="POST">
  @csrf
  @method('PUT')
  <div class="row">
    <div class="form-group row col-md-6">
        <label>Produk Kredit</label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Produk Kredit" value="{{old('name', $name->name)}}">
          @error('name')
              <div class="invalid-feedback">
                  {{ $message }}
              </div>
          @enderror
    </div>
    <div class="form-group col-md-6">
      <label>Skema Kredit</label>
      <select id="skemaKredit" name="skema_kredit_id[]" class="form-control" multiple="multiple" required>
          @foreach ($dataDetailProdukKredit as $item)
          <option value='{{$item->skema_kredit_id}}' selected>{{$item->skemaKredit->name}}</option>
          @endforeach
          @foreach ($dataSkemaKredit as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option> 
            @endforeach
      </select>
    </div>
  </div>
  <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-save"></i> Simpan</button>
    <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Reset</button>
</form>

<script>
  $(document).ready(function () {
    $("#skemaKredit").select2({
      placeholder: "Silahkan Pilih"
    });
    
      // $('#reset').on('click', function() {
      //     $('#skemaKredit').val(null);
      // })
  });
  </script>