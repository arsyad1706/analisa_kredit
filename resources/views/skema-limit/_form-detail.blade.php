<form action="{{ route('skema-limit.update', $data->id) }}" method="post">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="form-group col-md-6">
            <label for="">Skema Kredit</label>
            <select name="skema_kredit" id="" class="select2 from-control" disabled>
                <option value="">Pilih Skema Kredit</option>
                @foreach ($dataSkemaKredit as $item)
                    <option value="{{ $item->id }}" {{ $data->skema_kredit_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="">Operator</label>
            <input type="text" name="operator" class="form-control" placeholder="Masukkan operator" maxlength="2" value="{{ old('data', $data->operator) }}" disabled>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label for="">Nominal(dari)</label>
            <input type="text" class="form-control rupiah" name="nominal_awal" placeholder="Masukkan nominal" value="{{ number_format($data->from, 0, '.', '.') }}" disabled>
        </div>
        <div class="form-group col-md-6">
            <label for="">Nominal(sampai)</label>
            <input type="text" class="form-control rupiah" name="nominal_akhir" placeholder="Masukkan nominal" value="{{ number_format($data->to, 0, '.', '.') ?? '0' }}" disabled>
        </div>
    </div>

    <hr>
    <p>Detail Item</p>

    <div id="row-field">
        @foreach ($itemPerhitunganKredit as $item)
            <div id="parent-row" class="row">
                <div class="col-md-12">
                    <label for="">Field</label>
                </div>
                <div class="form-group col-md-6">
                    <input type="text" maxlength="50" name="field[]" class="form-control" placeholder="Masukkan nama field" value="{{ $item->field }}" disabled>
                </div>
            </div>
        @endforeach
    </div>
</form>

