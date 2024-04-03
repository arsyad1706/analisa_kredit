<form action="{{ route('skema-limit.store') }}" method="post">
  @csrf
  <div class="row">
      <div class="form-group col-md-6">
        <label for="">Field</label>
        <input type="text" name="operator" class="form-control" placeholder="Total" maxlength="2" disabled>
      </div>
  </div>

  <div class="row">
    <div class="form-group col-md-6">
      <label for="">Field</label>
      <select name="skema_kredit" id="field-select" class="select2 form-control" required>
        <option value="">Pilih field</option>
        <option value="">Neraca</option>
      </select>
    </div>
  </div>

  <div id="field-tambah">
    <div class="row">
      <div class="form-group col-md-2">
        <label for="">Pilih Operator</label>
        <select name="skema_kredit" id="field-operator"  class="select2 form-control" required>
          <option value="">Pilih operator</option>
          <option value="">+</option>
          <option value="">-</option>
          <option value="">*</option>
          <option value="">/</option>
        </select>
      </div>
      <div class="form-group col-md-4">
        <label for="">Field</label>
        <select name="skema_kredit" id="field2-select" class="select2 form-control" required>
          <option value="">Pilih field</option>
          <option value="">Tes</option>
        </select>
      </div>
      <div class="col-1">
        <label for="" style="color: white">Tambah</label>
        <button type="button" class="btn btn-success tambah"> Tambah </button>
      </div>
    </div>
  </div>

    <div class="alert alert-info" role="alert">
      <label for="">Preview</label>
      <div class="row">
        <div id="content-preview" class="col-md-12">
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
      // var i = 1; 
      var add = 1;

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

      $('#field-tambah').on('click', '.tambah', function(e){
        $('#field-tambah').append(`
          <div id="row-tambah" class="row">
            <div class="form-group col-md-2">
            <label for="">Pilih Operator</label>
            <select name="skema_kredit" id="field-operator${add}" class="select2 form-control" required>
              <option value="">Pilih operator</option>
              <option value="">+</option>
              <option value="">-</option>
              <option value="">*</option>
              <option value="">/</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label for="">Field</label>
            <select name="skema_kredit" id="field-select${add}" class="select2 form-control" required>
              <option value="">Pilih field</option>
              <option value="">Tes2</option>
            </select>
          </div>
          <div class="col-1">
            <label for="" style="color: white">Hapus</label>
            <button type="button" class="btn btn-danger hapus"> Hapus </button>
          </div>
          </div>
        `);
        add++
        addField();
      });

      $("#field-tambah").on("click", ".hapus", function(e){
          if(add > 1){
              $(this).parents().find("#row-tambah").remove();
              add--
            }
            addField();
          });

      $('#field-select').change(function () {
        if ($('#field-select').find(":selected").text() != "Pilih field") {
          $('#content-preview').append(`
            <label id="preview-label-select" for="">${$('#field-select').find(":selected").text()}</label>
          `);
        }else{
          $('label').remove('#preview-label-select');
        }
      });
      $('#field-operator').change(function () {
        if ($('#field-operator').find(":selected").text() != "Pilih operator") {
          $('#content-preview').append(`
            <label id="preview-label-operator" for="">${$('#field-operator').find(":selected").text()}</label>
          `);
        }else{
          $('label').remove('#preview-label-operator');
        }
      });
      $('#field2-select').change(function () {
        if ($('#field2-select').find(":selected").text() != "Pilih field") {
          $('#content-preview').append(`
            <label id="preview2-label-select" for="">${$('#field2-select').find(":selected").text()}</label>
          `);
        }else{
          $('label').remove('#preview2-label-select');
        }
      });

      function addField(){
        for (let i = 1; i <= add; i++) {
          $(`#field-operator${i}`).change(function () {
            if ($(`#field-operator${i}`).find(":selected").text() != "Pilih operator") {
              $('#content-preview').append(`
                <label id="preview-label-operator${i}" class="label-operator" for="">${$(`#field-operator${i}`).find(":selected").text()}</label>
              `);
            }else{
              $('label').remove(`#preview-label-operator${i}`);
            }
          });
          $(`#field-select${i}`).change(function () {
            if ($(`#field-select${i}`).find(":selected").text() != "Pilih field") {
              $('#content-preview').append(`
                <label id="preview-label-field${i}" class="label-field" for="">${$(`#field-select${i}`).find(":selected").text()}</label>
              `);
            }else{
              $('label').remove(`#preview-label-field${i}`);
            }
          });
        }
      }
      
  </script>
@endpush