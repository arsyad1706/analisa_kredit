<form action="{{ route('skema-limit.store') }}" method="post">
    @csrf
    <div class="row">
        <div class="form-group col-md-6">
            <label for="">Skema Kredit</label>
            <select name="skema_kredit" id="" class="select2 from-control" required>
                <option value="">Pilih Skema Kredit</option>
                @foreach ($dataSkemaKredit as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="">Operator</label>
            <input type="text" name="operator" class="form-control" placeholder="Masukkan operator" maxlength="2" required>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label for="">Nominal(dari)</label>
            <input type="text" class="form-control rupiah" name="nominal_awal" placeholder="Masukkan nominal" required>
        </div>
        <div class="form-group col-md-6">
            <label for="">Nominal(sampai)</label>
            <input type="text" class="form-control rupiah" name="nominal_akhir" placeholder="Masukkan nominal">
        </div>
    </div>

    <hr>
    <p>Detail Item</p>

    <div id="row-field">
        <div id="parent-row" class="row">
            <div class="col-md-12">
                <label for="">Field</label>
            </div>
            <div class="form-group col-md-6">
                <input type="text" maxlength="50" name="field[]" class="form-control" placeholder="Masukkan nama field" required>
            </div>
            <div class="form-group col-md-6">
                <button type="button" class="btn btn-success plus"> <i class="fa fa-plus"></i> </button>
                <button type="button" class="btn btn-danger minus"> <i class="fa fa-minus"></i> </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-primary">
                <i class="fa fa-save"></i>
                Simpan
            </button>
        </div>
    </div>
</form>

@push('custom-script')
    <script>
        var i = 1;

        function formatRupiah(angka, prefix){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
 
			// tambahkan titik jika yang di input sudah menjadi angka ribuan
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}
 
			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
		}

        $(".rupiah").keyup(function(e){
            var value = $(this).val();
            $(this).val(formatRupiah(value));
        });

        $("#row-field").on('click', '.plus', function(e){
            $("#row-field").append(`
            <div id="parent-row" class="row">
                <div class="col-md-12">
                    <label for="">Field</label>
                </div>
                <div class="form-group col-md-6">
                    <input type="text" maxlength="50" name="field[]" class="form-control" placeholder="Masukkan nama field" required>
                </div>
                <div class="form-group col-md-6">
                    <button type="button" class="btn btn-success plus"> <i class="fa fa-plus"></i> </button>
                    <button type="button" class="btn btn-danger minus"> <i class="fa fa-minus"></i> </button>
                </div>    
            </div>
            `);
            i++
        });

        $("#row-field").on("click", ".minus", function(e){
            if(i > 1){
                console.log(i);
                $(this).parents().find("#parent-row").remove()
                i--
            }
        });
    </script>
@endpush