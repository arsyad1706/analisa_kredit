<form action="{{ route('skema-kredit.update', $name->id) }}" method="POST">
  @csrf
  @method('PUT')
  <div class="form-group row col-md-6">
      <label>Produk Kredit</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Produk Kredit" value="{{old('name', $name->name)}}">
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
  </div>
  
  <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-save"></i> Simpan</button>
    <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Reset</button>
</form>
