<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#jangka_waktu').on('change', function() {
        limitJangkaWaktu()
    })
    $('#jumlah_kredit').on('change', function() {
        limitJangkaWaktu()
    })

    function limitJangkaWaktu() {
        var nominal = $('#jumlah_kredit').val()
        nominal = nominal != '' ? nominal.replaceAll('.','') : 0
        var limit = 50000000
        if (parseInt(nominal) > limit) {
            var jangka_waktu = $('#jangka_waktu').val()
            if (jangka_waktu != '') {
                jangka_waktu = parseInt(jangka_waktu)
                if (jangka_waktu <= 36) {
                    $('.jangka_waktu_error').removeClass('hidden')
                    $('.jangka_waktu_error').html('Jangka waktu harus lebih dari 36 bulan.')
                }
                else {
                    $('.jangka_waktu_error').addClass('hidden')
                    $('.jangka_waktu_error').html('')
                }
            }
        }
        else {
            $('.jangka_waktu_error').addClass('hidden')
            $('.jangka_waktu_error').html('')
        }
    }
</script>
<script>
    //var isPincetar = "{{Request::url()}}".includes('pincetar');
    let dataAspekArr;
    const ijin_usaha = $('#ijin_usaha').val()
    if (ijin_usaha == 'nib') {
        $('#docSKU').addClass('hidden');
        $('#surat_keterangan_usaha').addClass('hidden');
        $('#npwpsku').addClass('hidden');

        $('#nib').removeClass('hidden');
        $('#npwp').removeClass('hidden');
        $('#docNIB').removeClass('hidden');
        $('#docNPWP').removeClass('hidden');
    }

    if (ijin_usaha == 'tidak_ada_legalitas_usaha') {
        $('#nib').addClass('hidden');
        $('#docNIB').addClass('hidden');
        $('#surat_keterangan_usaha').addClass('hidden');
        $('#docSKU').addClass('hidden');
        $('#npwpsku').addClass('hidden');
        $('#docNPWP').addClass('hidden');
    }

    if (ijin_usaha == 'surat_keterangan_usaha') {
        $('#npwpsku').show()
        $('#docSKU').show()
        $('#surat_keterangan_usaha').show()

        $('#nib').hide();
        $('#docNIB').hide();
        $('#docNPWP').hide();
        $('#npwp').hide();
    }
    //make input readonly
    $('#ratio_coverage').attr('readonly', true);
    $('#ratio_tenor_asuransi').attr('readonly', true);
    $('#persentase_kebutuhan_kredit').attr('readonly', true);
    $('#repayment_capacity').attr('readonly', true);

    // make select option hidden
    $('#ratio_coverage_opsi_label').hide();
    $('#ratio_tenor_asuransi_opsi_label').hide();
    $('#ratio_coverage_opsi').hide();
    $('#ratio_tenor_asuransi_opsi').hide();
    $('#persentase_kebutuhan_kredit_opsi_label').hide();
    $('#persentase_kebutuhan_kredit_opsi').hide();
    $('#repayment_capacity_opsi_label').hide();
    $('#repayment_capacity_opsi').hide();
    $("#jaminan_tambahan").hide();

    let urlCekSubColumn = "{{ route('cek-sub-column') }}";
    let urlGetItemByKategoriJaminanUtama =
        "{{ route('get-item-jaminan-by-kategori-jaminan-utama') }}"; // jaminan tambahan
    let urlGetItemByKategori = "{{ route('get-item-jaminan-by-kategori') }}"; // jaminan tambahan
    var nullValue = []

    var x = 1;

    $('#kabupaten').change(function() {
        var kabID = $(this).val();
        if (kabID) {
            $.ajax({
                type: "GET",
                url: "{{ route('getKecamatan') }}?kabID=" + kabID,
                dataType: 'JSON',
                success: function(res) {
                    //    //console.log(res);
                    if (res) {
                        $("#kecamatan").empty();
                        $("#desa").empty();
                        $("#kecamatan").append('<option value="0">---Pilih Kecamatan---</option>');
                        $("#desa").append('<option value="0">---Pilih Desa---</option>');
                        $.each(res, function(nama, kode) {
                            $('#kecamatan').append(`
                                <option value="${kode}">${nama}</option>
                            `);
                        });

                        $('#kecamatan').trigger('change');
                    } else {
                        $("#kecamatan").empty();
                        $("#desa").empty();
                    }
                }
            });
        } else {
            $("#kecamatan").empty();
            $("#desa").empty();
        }
    });

    $('#kecamatan').change(function() {
        var kecID = $(this).val();
        // //console.log(kecID);
        if (kecID) {
            $.ajax({
                type: "GET",
                url: "{{ route('getDesa') }}?kecID=" + kecID,
                dataType: 'JSON',
                success: function(res) {
                    //    //console.log(res);
                    if (res) {
                        $("#desa").empty();
                        $("#desa").append('<option value="0">---Pilih Desa---</option>');
                        $.each(res, function(nama, kode) {
                            $('#desa').append(`
                                <option value="${kode}">${nama}</option>
                            `);
                        });
                    } else {
                        $("#desa").empty();
                    }
                }
            });
        } else {
            $("#desa").empty();
        }
    });

    //cek apakah opsi yg dipilih memiliki sub column
    $('.cek-sub-column').change(function(e) {
        let idOption = $(this).val();
        let idItem = $(this).data('id_item');
        // cek option apakah ada turunan
        $(`#item${idItem}`).empty();
        $.ajax({
            type: "get",
            url: `${urlCekSubColumn}?idOption=${idOption}`,
            dataType: "json",
            success: function(response) {
                if (response.sub_column != null) {
                    $(`#item${idItem}`).append(`
                    <div class="form-group sub mt-2">
                        <label for="">${response.sub_column}</label>
                        <input type="hidden" name="id_option_sub_column[]" value="${idOption}">
                        <input type="text" name="jawaban_sub_column[]" placeholder="Masukkan informasi tambahan" class="form-input">
                    </div>
                    `);
                } else {
                    $(`#item${idItem}`).empty();
                }
            }
        });
    });

    //item kategori jaminan utama cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan
    $('#kategori_jaminan_utama').change(function(e) {
        //clear item
        $('#select_kategori_jaminan_utama').empty();

        // clear bukti pemilikan
        $('#bukti_pemilikan_jaminan_utama').empty();

        //get item by kategori
        let kategoriJaminanUtama = $(this).val();
        console.log(kategoriJaminanUtama);

        $.ajax({
            type: "get",
            url: `${urlGetItemByKategoriJaminanUtama}?kategori=${kategoriJaminanUtama}`,
            dataType: "json",
            success: function(response) {
                // jika kategori bukan stock dan piutang
                if (kategoriJaminanUtama != 'Stock' && kategoriJaminanUtama != 'Piutang') {
                    // add item by kategori
                    $('#select_kategori_jaminan_utama').append(`
                        <label for="">${response.item.nama}</label>
                        <select name="dataLevelEmpat[]" id="itemByKategoriJaminanUtama" class="form-input cek-sub-column"
                            data-id_item="${response.item.id}">
                            <option value=""> --Pilih Opsi -- </option>
                            </select>

                        <div id="item${response.item.id}">

                        </div>
                    `);
                    // add opsi dari item
                    $.each(response.item.option, function(i, valOption) {
                        // //console.log(valOption.skor);
                        $('#itemByKategoriJaminanUtama').append(`
                        <option value="${valOption.skor}-${valOption.id}">
                        ${valOption.option}
                        </option>`);
                    });

                    // add item bukti pemilikan
                    var isCheck = kategoriJaminanUtama != 'Kendaraan Bermotor' &&
                        kategoriJaminanUtama != 'Stock' && kategoriJaminanUtama != 'Piutang' ?
                        "<input type='checkbox' class='checkKategoriJaminanUtama'>" : ""
                    var isDisabled = kategoriJaminanUtama != 'Kendaraan Bermotor' &&
                        kategoriJaminanUtama != 'Stock' && kategoriJaminanUtama != 'Piutang' ?
                        'disabled' : ''
                    $.each(response.itemBuktiPemilikan, function(i, valItem) {
                        if (valItem.nama == 'Atas Nama') {
                            $('#bukti_pemilikan_jaminan_utama').append(`
                            <div class="form-group aspek_jaminan_kategori_jaminan_utama">
                                <label>${valItem.nama}</label>
                                <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                                <input type="hidden" name="opsi_jawaban[]"
                                    value="${valItem.opsi_jawaban}" id="" class="input">
                                <input type="text" name="informasi[]" placeholder="Masukkan informasi"
                                    class="form-input input">
                            </div>
                        `);
                        } else {
                            if (valItem.nama == 'Foto') {
                                $('#bukti_pemilikan_jaminan_utama').append(`
                                <div class="form-group aspek_jaminan_kategori_jaminan_utama">
                                    <label>${valItem.nama}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                    <input type="hidden" name="id_item_file[${valItem.id}]" value="${valItem.id}" id="" class="input">
                                    <input type="file" name="upload_file[${valItem.id}]" data-id="" class="form-input limit-size">
                                    <span class="text-red-500 m-0" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                                    <span class="filename" style="display: inline;"></span>
                                </div>`);
                            } else {
                                $('#bukti_pemilikan_jaminan_utama').append(`
                                <div class="form-group aspek_jaminan_kategori_jaminan_utama">
                                    <label>${isCheck} ${valItem.nama}</label>
                                    <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                    <input type="hidden" name="opsi_jawaban[]"
                                        value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                    <input type="text" name="informasi[]" placeholder="Masukkan informasi ${valItem.nama}"
                                        class="form-input input" ${isDisabled}>
                                </div>`);
                            }
                        }
                    });

                    $(".checkKategoriJaminanUtama").click(function() {
                        var input = $(this).closest('.form-group').find(".input")
                        // var input_id = $(this).closest('.form-group').find("input_id").last()
                        // var input_opsi_jawaban = $(this).closest('.form-group').find("input_opsi_jawaban").last()
                        if ($(this).is(':checked')) {
                            input.prop('disabled', false)
                            // input_id.prop('disabled',false)
                            // input_opsi_jawaban.prop('disabled',false)
                        } else {
                            input.val('')
                            input.prop('disabled', true)
                            // input_id.prop('disabled',true)
                            // input_opsi_jawaban.prop('disabled',true)
                        }
                    })
                }
                // jika kategori = stock dan piutang
                else {
                    $.each(response.itemBuktiPemilikan, function(i, valItem) {
                        if (valItem.nama == 'Atas Nama') {
                            $('#select_kategori_jaminan_utama').append(`
                            <div class="aspek_jaminan_kategori_jaminan_utama">
                                <label>${valItem.nama}</label>
                                <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input">
                                <input type="hidden" name="opsi_jawaban[]"
                                    value="${valItem.opsi_jawaban}" id="" class="input">
                                <input type="text" name="informasi[]" placeholder="Masukkan informasi"
                                    class="form-input input">
                            </div>
                        `);
                        } else {
                            $('#select_kategori_jaminan_utama').append(`
                            <div class="aspek_jaminan_kategori_jaminan_utama">
                                <label>${valItem.nama}</label>
                                <input type="hidden" name="id_level[]" value="${valItem.id}" id="" class="input" >
                                <input type="hidden" name="opsi_jawaban[]"
                                    value="${valItem.opsi_jawaban}" id="" class="input">
                                <input type="text" name="informasi[]" placeholder="Masukkan informasi ${valItem.nama}"
                                    class="form-input input">
                            </div>
                        `);
                        }
                    });
                }
            }
        });
    });
    // end item kategori jaminan utama cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan

    //item kategori jaminan tambahan cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan
    $('#kategori_jaminan_tambahan').change(function(e) {
        //clear item
        $('#select_kategori_jaminan_tambahan').empty();

        // clear bukti pemilikan
        $('#bukti_pemilikan_jaminan_tambahan').empty();

        //get item by kategori
        let kategoriJaminan = $(this).val();
        let id_dagulir_temp = $('#id_dagulir_temp').val();
        let id_nasabah = $('#id_nasabah').val()
        let skema = $('#skema_kredit').val()

        $.ajax({
            type: "get",
            url: `${urlGetItemByKategori}?kategori=${kategoriJaminan}&id=${id_dagulir_temp}`,
            dataType: "json",
            success: function(response) {
                if (kategoriJaminan != "Tidak Memiliki Jaminan Tambahan") {
                    $("#select_kategori_jaminan_tambahan").show()
                    $("#jaminan_tambahan").show()
                    // add item by kategori
                    $('#select_kategori_jaminan_tambahan').append(`
                        <div class="input-box">
                            <label for="">${response.item.nama}</label>
                            <select name="dataLevelEmpat[${response.item.id}]" id="itemByKategori" class="form-input cek-sub-column"
                                data-id_item="${response.item.id}">
                                <option value=""> --Pilih Opsi -- </option>
                                </select>

                            <div id="item${response.item.id}">

                            </div>
                        </div>
                    `);
                    // add opsi dari item
                    $.each(response.item.option, function(i, valOption) {
                        // //console.log(valOption.skor);
                        $('#itemByKategori').append(`
                        <option value="${valOption.skor}-${valOption.id}" ${(response.dataSelect == valOption.id) ? 'selected' : ''}>
                        ${valOption.option}
                        </option>`);
                    });

                    // add item bukti pemilikan
                    var isCheck = kategoriJaminan != 'Kendaraan Bermotor' ?
                        "<input type='checkbox' class='checkKategori'>" : ""
                    var isDisabled = kategoriJaminan != 'Kendaraan Bermotor' ? 'disabled' : ''
                    $.each(response.itemBuktiPemilikan, function(i, valItem) {
                        if (valItem.nama == 'Atas Nama') {
                            $('#bukti_pemilikan_jaminan_tambahan').append(`
                                <div class="form-group input-box aspek_jaminan_kategori">
                                    <label>${valItem.nama}</label>
                                    <input type="hidden" name="id_level[${valItem.id}]" value="${valItem.id}" id="" class="input">
                                    <input type="hidden" name="opsi_jawaban[${valItem.id}]"
                                        value="${valItem.opsi_jawaban}" id="" class="input">
                                    <input type="text" maxlength="255" id="atas_nama" name="informasi[${valItem.id}]" placeholder="Masukkan informasi"
                                        class="form-input input" value="${response.dataJawaban[i]}">
                                </div>
                            `);
                        } else {
                            var name_lowercase = valItem.nama.toLowerCase();
                            name_lowercase = name_lowercase.replaceAll(' ', '_')
                            if (valItem.nama == 'Foto') {
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group input-box file-wrapper item-${valItem.id}">
                                        <label for="">${valItem.nama}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                            data-title="{{$itemNIB->nama}}"
                                            data-type="image"
                                            data-filepath="{{ asset("../upload/{$pengajuan->id}/{$itemNIB->id_jawaban}/{$file}") }}"
                                            >Preview
                                        </a>
                                        <div class="input-box mb-4">
                                            <div class="flex gap-4">
                                                <input type="hidden" name="id_item_file[${valItem.id}][]"
                                                    value="${valItem.id}" id="">
                                                <input type="file" id="${valItem.nama.toString().replaceAll(" ", "_")}"
                                                    name="upload_file[${valItem.id}][]" data-id=""
                                                    placeholder="Masukkan informasi ${valItem.nama}" class="form-input limit-size">
                                                <span class="text-red-500 m-0" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                                                <span class="filename" style="display: inline;"></span>
                                                <div class="flex gap-2 multiple-action">
                                                    <button type="button" class="btn-add" data-item-id="${valItem.id}-${name_lowercase}">
                                                        <iconify-icon icon="fluent:add-16-filled" class="mt-2"></iconify-icon>
                                                    </button>
                                                    <button type="button" class="btn-minus hidden" data-item-id="${valItem.id}-${name_lowercase}">
                                                        <iconify-icon icon="lucide:minus" class="mt-2"></iconify-icon>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                            } else {
                                if (response.dataJawaban[i] != null && response.dataJawaban[
                                        i] != "") {
                                    if (kategoriJaminan != 'Kendaraan Bermotor') {
                                        isCheck =
                                            "<input type='checkbox' class='checkKategori' checked>"
                                        isDisabled = ""
                                    }
                                }
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group input-box aspek_jaminan_kategori">
                                        <label>${isCheck} ${valItem.nama}</label>
                                        <input type="hidden" name="id_level[${valItem.id}]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                        <input type="hidden" name="opsi_jawaban[${valItem.id}]"
                                            value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                        <input type="text" maxlength="255" id="${valItem.nama.toString().replaceAll(" ", "_")}" name="informasi[${valItem.id}]" placeholder="Masukkan informasi"
                                            class="form-input input" ${isDisabled} value="${response.dataJawaban[i]}">
                                    </div>
                                `);
                            }
                        }
                    });

                    $(".checkKategori").click(function() {
                        var input = $(this).closest('.form-group').find(".input")
                        // var input_id = $(this).closest('.form-group').find("input_id").last()
                        // var input_opsi_jawaban = $(this).closest('.form-group').find("input_opsi_jawaban").last()
                        if ($(this).is(':checked')) {
                            input.prop('disabled', false)
                            // input_id.prop('disabled',false)
                            // input_opsi_jawaban.prop('disabled',false)
                        } else {
                            input.val('')
                            input.prop('disabled', true)
                            // input_id.prop('disabled',true)
                            // input_opsi_jawaban.prop('disabled',true)
                        }
                    })
                } else {
                    var skor = 0;
                    var opt = 0;
                    $('#select_kategori_jaminan_tambahan').append(`
                        <div class="input-box">
                            <label for="">${response.item.nama}</label>
                            <select name="dataLevelEmpat[${response.item.id}]" id="itemByKategori" class="form-input cek-sub-column"
                                data-id_item="${response.item.id}">
                                <option value=""> --Pilih Opsi -- </option>
                                </select>

                            <div id="item${response.item.id}">

                            </div>
                        </div>
                    `);
                    // add opsi dari item
                    $.each(response.item.option, function(i, valOption) {
                        skor = valOption.skor;
                        opt = valOption.id;
                        // //console.log(valOption.skor);
                        $('#itemByKategori').append(`
                        <option value="${valOption.skor}-${valOption.id}" selected>
                        ${valOption.option}
                        </option>`);
                    });
                    $("#itemByKategori").val(skor + '-' + opt);
                    $("#select_kategori_jaminan_tambahan").hide()
                    $("#jaminan_tambahan").hide()
                }
            }
        })
    });
    $(document).ready(function() {
        //clear item
        $('#select_kategori_jaminan_tambahan').empty();

        // clear bukti pemilikan
        $('#bukti_pemilikan_jaminan_tambahan').empty();

        //get item by kategori
        let kategoriJaminan = $('#kategori_jaminan_tambahan').val();
        let id_dagulir_temp = $('#id_dagulir_temp').val()
        let skema = $('#skema').val();
        $.ajax({
            type: "get",
            url: `${urlGetItemByKategori}?kategori=${kategoriJaminan}&id=${id_dagulir_temp}&skema=`,
            dataType: "json",
            success: function(response) {
                console.log(response);
                if (kategoriJaminan != "Tidak Memiliki Jaminan Tambahan") {
                    $("#select_kategori_jaminan_tambahan").show()
                    $("#jaminan_tambahan").show()
                    // add item by kategori
                    $('#select_kategori_jaminan_tambahan').append(`
                        <div class="input-box">
                            <label for="">${response.item.nama}</label>
                            <select name="dataLevelEmpat[${response.item.id}]" id="itemByKategori" class="form-input cek-sub-column"
                                data-id_item="${response.item.id}">
                                <option value=""> --Pilih Opsi -- </option>
                                </select>

                            <div id="item${response.item.id}">

                            </div>
                        </div>
                    `);
                    // add opsi dari item
                    $.each(response.item.option, function(i, valOption) {
                        console.log(response.dataSelect);
                        $('#itemByKategori').append(`
                        <option value="${valOption.skor}-${valOption.id}" ${(response.dataSelect == valOption.id) ? 'selected' : ''}>
                        ${valOption.option}
                        </option>`);
                    });

                    // add item bukti pemilikan
                    var isCheck = kategoriJaminan != 'Kendaraan Bermotor' ?
                        "<input type='checkbox' class='checkKategori'>" : ""
                    var isDisabled = kategoriJaminan != 'Kendaraan Bermotor' ? 'disabled' : ''
                    $.each(response.itemBuktiPemilikan, function(i, valItem) {
                        if (valItem.nama == 'Atas Nama') {
                            $('#bukti_pemilikan_jaminan_tambahan').append(`
                                <div class="form-group aspek_jaminan_kategori">
                                    <label>${valItem.nama}</label>
                                    <input type="hidden" name="id_level[${valItem.id}]" value="${valItem.id}" id="" class="input">
                                    <input type="hidden" name="opsi_jawaban[${valItem.id}]"
                                        value="${valItem.opsi_jawaban}" id="" class="input">
                                    <input type="text" maxlength="255" id="atas_nama" name="informasi[${valItem.id}]" placeholder="Masukkan informasi"
                                        class="form-input input" value="${response.dataJawaban[i]}">
                                </div>
                            `);
                        } else {
                            var name_lowercase = valItem.nama.toLowerCase();
                            name_lowercase = name_lowercase.replaceAll(' ', '_')
                            var linkElement = $(".open-modal");

                            // Assuming you have the correct values for the variables
                            var itemId = `{{ $pengajuan?->id }}`; // Replace with the actual value
                            var itemNIBId = valItem.id; // Replace with the actual value
                            var file = response.dataJawaban[i]; // Replace with the actual value

                            // Construct the new filepath URL
                            var newFilepath = "{{asset('../upload')}}/" + itemId + "/" + itemNIBId + "/" + file;

                            if (valItem.nama == 'Foto') {
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group file-wrapper item-${valItem.id}">
                                        <label for="">${valItem.nama}</label><small class="text-red-500 font-bold"> (.jpg, .jpeg, .png, .webp)</small>
                                        <a class="text-theme-primary underline underline-offset-4 cursor-pointer open-modal btn-file-preview"
                                                                data-title="${valItem.nama}" data-filepath="${newFilepath}">Preview</a>
                                        <div class="input-box mb-4">
                                            <div class="flex gap-4">
                                                <input type="hidden" name="id_item_file[${valItem.id}][]"
                                                    value="${valItem.id}" id="">
                                                <input type="file" id="${valItem.nama.toString().replaceAll(" ", "_")}"
                                                    name="upload_file[${valItem.id}][]" value="${response.dataJawaban[i]}" data-id=""
                                                    placeholder="Masukkan informasi ${valItem.nama}" class="form-input limit-size">
                                                <span class="text-red-500 m-0" style="display: none">Besaran file tidak boleh lebih dari 5 MB</span>
                                                <span class="filename" style="display: inline;"></span>
                                                <div class="flex gap-2 multiple-action">
                                                    <button type="button" class="btn-add" data-item-id="${valItem.id}-${name_lowercase}">
                                                        <iconify-icon icon="fluent:add-16-filled" class="mt-2"></iconify-icon>
                                                    </button>
                                                    <button type="button" class="btn-minus hidden" data-item-id="${valItem.id}-${name_lowercase}">
                                                        <iconify-icon icon="lucide:minus" class="mt-2"></iconify-icon>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                            } else {
                                if (response.dataDetailJawabanText[i] != null && response.dataJawaban[i] != "") {
                                    if (kategoriJaminan != 'Kendaraan Bermotor') {
                                        isCheck = "<input type='checkbox' class='checkKategori' checked>"
                                        isDisabled = ""
                                    }
                                }else{
                                    isCheck = "<input type='checkbox' class='checkKategori'>"
                                    isDisabled = "disabled"
                                }
                                console.log(typeof(response.dataJawaban[i]));
                                $('#bukti_pemilikan_jaminan_tambahan').append(`
                                    <div class="form-group aspek_jaminan_kategori">
                                        <label>${isCheck} ${valItem.nama}</label>
                                        <input type="hidden" name="id_level[${valItem.id}]" value="${valItem.id}" id="" class="input" ${isDisabled}>
                                        <input type="hidden" name="opsi_jawaban[${valItem.id}]"
                                            value="${valItem.opsi_jawaban}" id="" class="input" ${isDisabled}>
                                        <input type="text" maxlength="255" id="${valItem.nama.toString().replaceAll(" ", "_")}" name="informasi[${valItem.id}]" placeholder="Masukkan informasi"
                                            class="form-input input" ${isDisabled} value="${response.dataJawaban[i]}">
                                    </div>
                                `);
                            }
                        }
                    });

                    $(".checkKategori").click(function() {
                        var input = $(this).closest('.form-group').find(".input")
                        // var input_id = $(this).closest('.form-group').find("input_id").last()
                        // var input_opsi_jawaban = $(this).closest('.form-group').find("input_opsi_jawaban").last()
                        if ($(this).is(':checked')) {
                            input.prop('disabled', false)
                            // input_id.prop('disabled',false)
                            // input_opsi_jawaban.prop('disabled',false)
                        } else {
                            input.val('')
                            input.prop('disabled', true)
                            // input_id.prop('disabled',true)
                            // input_opsi_jawaban.prop('disabled',true)
                        }
                    })
                } else {
                    var skor = 0;
                    var opt = 0;
                    $('#select_kategori_jaminan_tambahan').append(`
                        <div class="input-box">
                            <label for="">${response.item.nama}</label>
                            <select name="dataLevelEmpat[${response.item.id}]" id="itemByKategori" class="form-input cek-sub-column"
                                data-id_item="${response.item.id}">
                                <option value=""> --Pilih Opsi -- </option>
                                </select>

                            <div id="item${response.item.id}">

                            </div>
                        </div>
                    `);
                    // add opsi dari item
                    $.each(response.item.option, function(i, valOption) {
                        skor = valOption.skor;
                        opt = valOption.id;
                        // //console.log(valOption.skor);
                        $('#itemByKategori').append(`
                        <option value="${valOption.skor}-${valOption.id}" selected>
                        ${valOption.option}
                        </option>`);
                    });
                    $("#itemByKategori").val(skor + '-' + opt);
                    $("#select_kategori_jaminan_tambahan").hide()
                    $("#jaminan_tambahan").hide()
                }
            }
        })
    })
    // end item kategori jaminan tambahan cek apakah milih tanah, kendaraan bermotor, atau tanah dan bangunan

    // milih ijin usaha
    $('#npwp').hide();
    $('#npwp_id').attr('disabled', true);
    $('#npwp_text').attr('disabled', true);
    $('#npwp_file').attr('disabled', true);
    // $('#npwp_text').val('');
    $('#npwp_opsi_jawaban').attr('disabled', true);

    $('#docNPWP').hide();
    $('#docNPWP_id').attr('disabled', true);
    $('#docNPWP_text').attr('disabled', true);
    $('#docNPWP_text').val('');
    $('#docNPWP_upload_file').attr('disabled', true);
    $('#ijin_usaha').change(function(e) {
        let ijinUsaha = $(this).val();
        $('#isNpwp').prop('checked', false)
        $('#npwpsku').hide();
        if (ijinUsaha == 'nib') {
            $('#space_nib').show();
            $('#npwpsku').hide();
            $('#surat_keterangan_usaha').hide();
            $('#surat_keterangan_usaha_id').attr('disabled', true);
            $('#surat_keterangan_usaha_text').attr('disabled', true);
            $('#surat_keterangan_usaha_file').attr('disabled', true);
            // $('#surat_keterangan_usaha_text').val("");
            $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);

            $('#docSKU').hide();
            $('#docSKU_id').attr('disabled', true);
            $('#docSKU_text').attr('disabled', true);
            $('#docSKU_upload_file').attr('disabled', true);

            $('#nib').show();
            $('#nib_id').removeAttr('disabled');
            $('#nib_text').removeAttr('disabled');
            $('#nib_opsi_jawaban').removeAttr('disabled');

            $('#docNIB').show();
            $('#docNIB_id').removeAttr('disabled');
            $('#docNIB_text').removeAttr('disabled');
            $('#docNIB_upload_file').removeAttr('disabled');
            $('#file_nib').removeAttr('disabled');

            $('#npwp').show();
            console.log('show npwp')
            $('#npwp_id').removeAttr('disabled');
            $('#npwp_text').removeAttr('disabled');
            $('#npwp_opsi_jawaban').removeAttr('disabled');
            $('#npwp_file').removeAttr('disabled');

            $('#docNPWP').show();
            console.log('show npwp')
            $('#docNPWP_id').removeAttr('disabled');
            $('#docNPWP_text').removeAttr('disabled');
            $('#docNPWP_text').val('');
            $('#docNPWP_upload_file').removeAttr('disabled');
        } else if (ijinUsaha == 'surat_keterangan_usaha') {
            $('#space_nib').hide();
            $('#npwpsku').show();
            $('#nib').hide();
            $('#nib_id').attr('disabled', true);
            $('#nib_text').attr('disabled', true);
            $('#nib_file').attr('disabled', true);
            $('#file_nib').attr('disabled', true);
            $('#docNIB_file').attr('disabled', true);
            // $('#nib_text').val('');
            $('#nib_opsi_jawaban').attr('disabled', true);

            $('#docNIB').hide();
            $('#docNIB_id').attr('disabled', true);
            $('#docNIB_text').attr('disabled', true);
            $('#docNIB_upload_file').attr('disabled', true);

            $('#surat_keterangan_usaha').show();
            $('#surat_keterangan_usaha_id').removeAttr('disabled');
            $('#surat_keterangan_usaha_text').removeAttr('disabled');
            $('#surat_keterangan_usaha_file').removeAttr('disabled');
            // $('#surat_keterangan_usaha_text').val('');
            $('#surat_keterangan_usaha_opsi_jawaban').removeAttr('disabled');

            $('#docSKU').show();
            $('#docSKU_id').removeAttr('disabled');
            $('#docSKU_text').removeAttr('disabled');
            $('#docSKU_upload_file').removeAttr('disabled');

            $('#npwp').hide();
            $('#npwp_id').attr('disabled', true);
            $('#npwp_text').attr('disabled', true);
            $('#npwp_file').attr('disabled', true);
            // $('#npwp_text').val('');
            $('#npwp_opsi_jawaban').attr('disabled', true);

            $('#docNPWP').hide();
            $('#docNPWP_id').attr('disabled', true);
            $('#docNPWP_text').attr('disabled', true);
            // $('#docNPWP_text').val('');
            $('#docNPWP_upload_file').attr('disabled', true);
        } else {
            console.log('rqwqr');
            $('#space_nib').hide();
            $('#npwpsku').hide();
            $('#nib').hide();
            $('#nib_id').attr('disabled', true);
            $('#nib_text').attr('disabled', true);
            $('#file_nib').attr('disabled', true);
            $('#nib_text').val('');
            $('#nib_opsi_jawaban').attr('disabled', true);

            $('#docNIB').hide();
            $('#docNIB_id').attr('disabled', true);
            $('#docNIB_text').attr('disabled', true);
            $('#docNIB_file').attr('disabled', true);
            $('#docNIB_text').val('');
            $('#docNIB_upload_file').attr('disabled', true);

            $('#docSKU').hide();

            $('#surat_keterangan_usaha').hide();
            $('#surat_keterangan_usaha_id').attr('disabled', true);
            $('#surat_keterangan_usaha_text').attr('disabled', true);
            $('#surat_keterangan_usaha_file').attr('disabled', true);
            $('#surat_keterangan_usaha_text').val('');
            $('#surat_keterangan_usaha_opsi_jawaban').attr('disabled', true);

            $('#docSKU').hide();
            $('#docSKU_id').attr('disabled', true);
            $('#docSKU_text').attr('disabled', true);
            // $('#docSKU_text').val('');
            $('#docSKU_upload_file').attr('disabled', true);

            $('#npwp').hide();
            $('#npwp_id').attr('disabled', true);
            $('#npwp_text').attr('disabled', true);
            $('#npwp_file').attr('disabled', true);
            // $('#npwp_text').val('');
            $('#npwp_opsi_jawaban').attr('disabled', true);

            $('#docNPWP').hide();
            $('#docNPWP_id').attr('disabled', true);
            // $('#docNPWP_text').attr('disabled', true);
            $('#docNPWP_text').val('');
            $('#docNPWP_upload_file').attr('disabled', true);
        }
    });
    // end milih ijin usaha
    // Cek Npwp
    if ($("#isNpwp").is(":checked")) {
        $('#npwp').show();
        $('#npwp_id').removeAttr('disabled');
        $('#npwp_text').removeAttr('disabled');
        $('#npwp_file').removeAttr('disabled');
        $('#npwp_opsi_jawaban').removeAttr('disabled');

        $('#docNPWP').show();
        $('#docNPWP_id').removeAttr('disabled');
        $('#docNPWP_text').removeAttr('disabled');
        // $('#docNPWP_text').val('');
        $('#docNPWP_upload_file').removeAttr('disabled');
    } else {
        $('#npwp').hide();
        $('#npwp_id').attr('disabled', true);
        $('#npwp_text').attr('disabled', true);
        $('#npwp_file').attr('disabled', true);
        // $('#npwp_text').val('');
        $('#npwp_opsi_jawaban').attr('disabled', true);

        $('#docNPWP').hide();
        $('#docNPWP_id').attr('disabled', true);
        $('#docNPWP_text').attr('disabled', true);
        $('#docNPWP_text').val('');
        $('#docNPWP_upload_file').attr('disabled', true);
    }
    $('#isNpwp').change(function() {
        if ($(this).is(':checked')) {
            $('#npwp').show();
            $('#npwp_id').removeAttr('disabled');
            $('#npwp_text').removeAttr('disabled');
            $('#npwp_file').removeAttr('disabled');
            $('#npwp_opsi_jawaban').removeAttr('disabled');

            $('#docNPWP').show();
            $('#docNPWP_id').removeAttr('disabled');
            $('#docNPWP_text').removeAttr('disabled');
            // $('#docNPWP_text').val('');
            $('#docNPWP_upload_file').removeAttr('disabled');
        } else {
            $('#npwp').hide();
            $('#npwp_id').attr('disabled', true);
            $('#npwp_text').attr('disabled', true);
            $('#npwp_file').attr('disabled', true);
            $('#npwp_opsi_jawaban').attr('disabled', true);

            $('#docNPWP').hide();
            $('#docNPWP_id').attr('disabled', true);
            $('#docNPWP_text').attr('disabled', true);
            // $('#docNPWP_text').val('');
            $('#docNPWP_upload_file').attr('disabled', true);
        }
    });

    // function formatNpwp(value) {
    //     if (typeof value === 'string') {
    //         return value.replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/, '$1.$2.$3.$4-$5.$6');
    //     }
    // }
    // // NPWP format
    // $(document).on('keyup', '#npwp_text', function() {
    //     var input = $(this).val()
    //     $(this).val(formatNpwp(input))
    // })

    //triger hitung ratio coverage
    $('#thls').change(function(e) {
        hitungRatioCoverage();
    });
    //end triger hitung ratio covarege

    //triger hitung ratio coverage
    $('#nilai_pertanggungan_asuransi').change(function(e) {
        hitungRatioCoverage();
    });
    //end triger hitung ratio covarege

    //triger hitung ratio coverage
    $('#jumlah_kredit').change(function(e) {
        hitungRatioCoverage();
    });
    //end triger hitung ratio covarege

    // hitung ratio covarege
    function hitungRatioCoverage() {
        let thls = parseInt($('#thls').val().split('.').join(''));
        let nilaiAsuransi = parseInt($('#nilai_pertanggungan_asuransi').val().split('.').join(''));
        let kreditYangDiminta = parseInt($('#jumlah_kredit').val().split('.').join(''));

        let ratioCoverage = (thls + nilaiAsuransi) / kreditYangDiminta * 100; //cek rumus nya lagi
        $('#ratio_coverage').val(ratioCoverage);

        if (ratioCoverage >= 150) {
            $('#ratio_coverage_opsi_0').attr('selected', true);
            $('#ratio_coverage_opsi_1').removeAttr('selected');
            $('#ratio_coverage_opsi_2').removeAttr('selected');
            $('#ratio_coverage_opsi_3').removeAttr('selected');
        } else if (ratioCoverage >= 131 && ratioCoverage < 150) {
            $('#ratio_coverage_opsi_0').removeAttr('selected');
            $('#ratio_coverage_opsi_1').attr('selected', true);
            $('#ratio_coverage_opsi_2').removeAttr('selected');
            $('#ratio_coverage_opsi_3').removeAttr('selected');
        } else if (ratioCoverage >= 110 && ratioCoverage <= 130) {
            $('#ratio_coverage_opsi_0').removeAttr('selected');
            $('#ratio_coverage_opsi_1').removeAttr('selected');
            $('#ratio_coverage_opsi_2').attr('selected', true);
            $('#ratio_coverage_opsi_3').removeAttr('selected');
        } else if (ratioCoverage < 110 && !isNaN(ratioCoverage)) {
            $('#ratio_coverage_opsi_0').removeAttr('selected');
            $('#ratio_coverage_opsi_1').removeAttr('selected');
            $('#ratio_coverage_opsi_2').removeAttr('selected');
            $('#ratio_coverage_opsi_3').attr('selected', true);
        } else {
            $('#ratio_coverage_opsi_0').removeAttr('selected');
            $('#ratio_coverage_opsi_1').removeAttr('selected');
            $('#ratio_coverage_opsi_2').removeAttr('selected');
            $('#ratio_coverage_opsi_3').removeAttr('selected');
        }
    }
    //end hitung ratio covarege

    //triger hitung ratio Tenor Asuransi
    $('#masa_berlaku_asuransi_penjaminan').change(function(e) {
        hitungRatioTenorAsuransi();
    });
    //end triger hitung ratio Tenor Asuransi

    //triger hitung ratio Tenor Asuransi
    $('#tenor_yang_diminta').change(function(e) {
        hitungRatioTenorAsuransi();
    });
    //end triger hitung ratio Tenor Asuransi

    // hitung ratio Tenor Asuransi
    function hitungRatioTenorAsuransi() {
        let masaBerlakuAsuransi = parseInt($('#masa_berlaku_asuransi_penjaminan').val());
        let tenorYangDiminta = parseInt($('#tenor_yang_diminta').val());

        let ratioTenorAsuransi = parseInt(masaBerlakuAsuransi / tenorYangDiminta * 100); //cek rumusnya lagi

        $('#ratio_tenor_asuransi').val(ratioTenorAsuransi);

        if (ratioTenorAsuransi >= 200) {
            $('#ratio_tenor_asuransi_opsi_0').attr('selected', true);
            $('#ratio_tenor_asuransi_opsi_1').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_2').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_3').removeAttr('selected');
        } else if (ratioTenorAsuransi >= 150 && ratioTenorAsuransi < 200) {
            $('#ratio_tenor_asuransi_opsi_0').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_1').attr('selected', true);
            $('#ratio_tenor_asuransi_opsi_2').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_3').removeAttr('selected');
        } else if (ratioTenorAsuransi >= 100 && ratioTenorAsuransi < 150) {
            $('#ratio_tenor_asuransi_opsi_0').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_1').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_2').attr('selected', true);
            $('#ratio_tenor_asuransi_opsi_3').removeAttr('selected');
        } else if (ratioTenorAsuransi < 100 && !isNaN(ratioTenorAsuransi)) {
            $('#ratio_tenor_asuransi_opsi_0').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_1').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_2').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_3').attr('selected', true);
        } else {
            $('#ratio_tenor_asuransi_opsi_0').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_1').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_2').removeAttr('selected');
            $('#ratio_tenor_asuransi_opsi_3').removeAttr('selected');
        }
    }
    //end hitung ratio covarege

    //triger hitung Repayment Capacity
    $('#persentase_net_income').change(function(e) {
        hitungRepaymentCapacity();
    });
    //end triger hitung Repayment Capacity

    //triger hitung Repayment Capacity
    $('#omzet_penjualan').change(function(e) {
        hitungRepaymentCapacity();
    });
    //end triger hitung Repayment Capacity

    //triger hitung Repayment Capacity
    $('#rencana_peningkatan').change(function(e) {
        hitungRepaymentCapacity();
    });
    //end triger hitung Repayment Capacity

    //triger hitung Repayment Capacity
    $('#installment').change(function(e) {
        hitungRepaymentCapacity();
    });
    //end triger hitung Repayment Capacity

    // hitung Repayment Capacity
    function hitungRepaymentCapacity() {
        let omzetPenjualan = parseInt($('#omzet_penjualan').val().split('.').join(''));
        let persentaseNetIncome = parseInt($('#persentase_net_income').val()) / 100;
        let rencanaPeningkatan = parseInt($('#rencana_peningkatan').val()) / 100;
        let installment = parseInt($('#installment').val().split('.').join(''));

        let repaymentCapacity = parseFloat(persentaseNetIncome * omzetPenjualan * (1 + rencanaPeningkatan) /
            installment); //cek rumusnya lagi

        $('#repayment_capacity').val(repaymentCapacity.toFixed(2));

        if (repaymentCapacity > 2) {
            $('#repayment_capacity_opsi_0').attr('selected', true);
            $('#repayment_capacity_opsi_1').removeAttr('selected');
            $('#repayment_capacity_opsi_2').removeAttr('selected');
            $('#repayment_capacity_opsi_3').removeAttr('selected');
        } else if (repaymentCapacity >= 1.5 && repaymentCapacity < 2) {
            $('#repayment_capacity_opsi_0').removeAttr('selected');
            $('#repayment_capacity_opsi_1').attr('selected', true);
            $('#repayment_capacity_opsi_2').removeAttr('selected');
            $('#repayment_capacity_opsi_3').removeAttr('selected');
        } else if (repaymentCapacity >= 1.25 && repaymentCapacity < 1.5) {
            $('#repayment_capacity_opsi_0').removeAttr('selected');
            $('#repayment_capacity_opsi_1').removeAttr('selected');
            $('#repayment_capacity_opsi_2').attr('selected', true);
            $('#repayment_capacity_opsi_3').removeAttr('selected');
        } else if (repaymentCapacity < 1.25 && !isNaN(repaymentCapacity)) {
            $('#repayment_capacity_opsi_0').removeAttr('selected');
            $('#repayment_capacity_opsi_1').removeAttr('selected');
            $('#repayment_capacity_opsi_2').removeAttr('selected');
            $('#repayment_capacity_opsi_3').attr('selected', true);
        } else {
            $('#repayment_capacity_opsi_0').removeAttr('selected');
            $('#repayment_capacity_opsi_1').removeAttr('selected');
            $('#repayment_capacity_opsi_2').removeAttr('selected');
            $('#repayment_capacity_opsi_3').removeAttr('selected');
        }
    }
    //end Repayment Capacity

    $('.rupiah').keyup(function(e) {
        var input = $(this).val()
        $(this).val(formatrupiah(input))
    });

    function formatrupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }
    // End Format Rupiah

    // Limit Upload
    $('.limit-size').on('change', function() {
        var size = (this.files[0].size / 1024 / 1024).toFixed(2)
        if (size > 5) {
            $(this).next().css({
                "display": "block"
            });
            this.value = ''
        } else {
            $(this).next().css({
                "display": "none"
            });
        }
    })
    // Limit 2 MB
    $('.limit-size-2').on('change', function() {
        var size = (this.files[0].size / 1024 / 1024).toFixed(2)
        if (size > 2) {
            $(this).parent().parent().find('.error-limit').css({
                "display": "block"
            });
            this.value = ''
        } else {
            $(this).parent().parent().find('.error-limit').css({
                "display": "none"
            });
        }
    })
    // Limit Upload Slik
    $('.limit-size-slik').on('change', function() {
        var size = (this.files[0].size / 1024 / 1024).toFixed(2)
        if (size > 10) {
            $(this).next().css({
                "display": "block"
            });
            this.value = ''
        } else {
            $(this).next().css({
                "display": "none"
            });
        }
    })
    // End Limit Upload

    @if (count($errors->all()))
        Swal.fire({
            icon: 'error',
            title: 'Error Validation',
            html: `
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            @foreach ($errors->all() as $error)
            <ul>
                <li>{{ $error }}</li>
            </ul>
            @endforeach
        </div>
        `
        });
    @endif

    var slik = document.getElementById("file_slik");
    var selectedFile;

    if (slik) {
        slik.addEventListener('change', updateImageDisplaySlik);
    }

    function updateImageDisplaySlik() {
        if (slik.files.length == 0) {
            slik.files = selectedFile;
        } else {
            selectedFile = slik.files;
        }

    }

    var docNPWP = document.getElementById("npwp_file");
    var selectedFile;

    docNPWP.addEventListener('change', updateImageDisplaydocNPWP);

    function updateImageDisplaydocNPWP() {
        if (docNPWP.files.length == 0) {
            docNPWP.files = selectedFile;
        } else {
            selectedFile = docNPWP.files;
        }

    }

    var docSKU = document.getElementById("surat_keterangan_usaha_file");
    var selectedFile;

    docSKU.addEventListener('change', updateImageDisplaydocSKU);

    function updateImageDisplaydocSKU() {
        if (docSKU.files.length == 0) {
            docSKU.files = selectedFile;
        } else {
            selectedFile = docSKU.files;
        }

    }

    var docNIB = document.getElementById("file_nib");
    var selectedFile;

    docNIB.addEventListener('change', updateImageDisplaydocNIB);

    function updateImageDisplaydocNIB() {
        if (docNIB.files.length == 0) {
            docNIB.files = selectedFile;
        } else {
            selectedFile = docNIB.files;
        }

    }

    // var docKebKre = document.getElementById("perhitungan_kebutuhan_kredit");
    // var selectedFile;

    // docKebKre.addEventListener('change', updateImageDisplaydocKebKre);

    // function updateImageDisplaydocKebKre() {
    //     if (docKebKre.files.length == 0) {
    //         docKebKre.files = selectedFile;
    //     } else {
    //         selectedFile = docKebKre.files;
    //     }

    // }

    // var docKebNet = document.getElementById("perhitungan_net_income");
    // var selectedFile;

    // docKebNet.addEventListener('change', updateImageDisplaydocKebNet);

    // function updateImageDisplaydocKebNet() {
    //     if (docKebNet.files.length == 0) {
    //         docKebNet.files = selectedFile;
    //     } else {
    //         selectedFile = docKebNet.files;
    //     }

    // }

    // var docKebInstll = document.getElementById("perhitungan_installment");
    // var selectedFile;

    // docKebInstll.addEventListener('change', updateImageDisplaydocKebInstll);

    // function updateImageDisplaydocKebInstll() {
    //     if (docKebInstll.files.length == 0) {
    //         docKebInstll.files = selectedFile;
    //     } else {
    //         selectedFile = docKebInstll.files;
    //     }

    // }
</script>
<script>
    // Start Validation
    @if (count($errors->all()))
        Swal.fire({
            icon: 'error',
            title: 'Error Validation',
            html: `
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            @foreach ($errors->all() as $error)
            <ul>
                <li>{{ $error }}</li>
            </ul>
            @endforeach
        </div>
        `
        });
    @endif
    // End Validation


    $('#kabupaten').change(function() {
        var kabID = $(this).val();
        if (kabID) {
            $.ajax({
                type: "GET",
                url: "{{ url('') }}/getkecamatan?kabID=" + kabID,
                dataType: 'JSON',
                success: function(res) {
                    if (res) {
                        $("#kecamatan").empty();
                        $("#kecamatan").append('<option>---Pilih Kecamatan---</option>');
                        $.each(res, function(nama, kode) {
                            $('#kecamatan').append(`
                                <option value="${kode}">${nama}</option>
                            `);
                        });

                        $('#kecamatan').trigger('change');
                    } else {
                        $("#kecamatan").empty();
                    }
                }
            });
        } else {
            $("#kecamatan").empty();
        }
    });
    $('#kabupaten_domisili').change(function() {
        var kabID = $(this).val();
        if (kabID) {
            $.ajax({
                type: "GET",
                url: "{{ url('') }}/getkecamatan?kabID=" + kabID,
                dataType: 'JSON',
                success: function(res) {
                    if (res) {
                        $("#kecamatan_domisili").empty();
                        $("#kecamatan_domisili").append('<option>---Pilih Kecamatan---</option>');
                        $.each(res, function(nama, kode) {
                            $('#kecamatan_domisili').append(`
                                <option value="${kode}">${nama}</option>
                            `);
                        });

                        $('#kecamatan_domisili').trigger('change');
                    } else {
                        $("#kecamatan_domisili").empty();
                    }
                }
            });
        } else {
            $("#kecamatan_domisili").empty();
        }
    });
    $('#kabupaten_usaha').change(function() {
        var kabID = $(this).val();
        if (kabID) {
            $.ajax({
                type: "GET",
                url: "{{ url('') }}/getkecamatan?kabID=" + kabID,
                dataType: 'JSON',
                success: function(res) {
                    if (res) {
                        $("#kecamatan_usaha").empty();
                        $("#kecamatan_usaha").append('<option>---Pilih Kecamatan---</option>');
                        $.each(res, function(nama, kode) {
                            $('#kecamatan_usaha').append(`
                                <option value="${kode}">${nama}</option>
                            `);
                        });

                        $('#kecamatan_usaha').trigger('change');
                    } else {
                        $("#kecamatan_usaha").empty();
                    }
                }
            });
        } else {
            $("#kecamatan_usaha").empty();
        }
    });

    $('#status_nasabah').on('change', function(e){
        var status = $(this).val();
        if (status == 2) {
            $('#label-ktp-nasabah').empty();
            $('#label-ktp-nasabah').html('Foto KTP Nasabah');
            $('#nik_pasangan').removeClass('hidden');
            $('#ktp-pasangan').removeClass('hidden');
        } else {
            $('#label-ktp-nasabah').empty();
            $('#label-ktp-nasabah').html('Foto KTP Nasabah');
            $('#nik_pasangan').addClass('hidden');
            $('#ktp-pasangan').addClass('hidden');
        }
    })

    $('#tipe').on('change',function(e) {
        var tipe = $(this).val();
        if (tipe == '2' || tipe == "0" ) {
            $('#tempat_berdiri').addClass('hidden');
        }else{
            $('#tempat_berdiri').removeClass('hidden');
            //badan usaha
            if (tipe == '3') {
                $('#label_pj').html('Nama penanggung jawab');
                $('#input_pj').attr('placeholder', 'Masukkan Nama Penanggung Jawab');
            }
            // perorangan
            else if (tipe == '4') {
                $('#label_pj').html('Nama ketua');
                $('#input_pj').attr('placeholder', 'Masukkan Nama Ketua');
            }
        }
    })

    function validatePhoneNumber(input) {
        var phoneNumber = input.value.replace(/\D/g, '');

        if (phoneNumber.length > 15) {
            phoneNumber = phoneNumber.substring(0, 15);
        }

        input.value = phoneNumber;
    }

    function validateNIK(input) {
        var nikNumber = input.value.replace(/\D/g, '');

        if (nikNumber.length > 16) {
            nikNumber = nikNumber.substring(0, 16);
        }

        input.value = nikNumber;
    }

    $(document).ready(function() {
        var inputRupiah = $('.rupiah')
        $.each(inputRupiah, function(i, obj) {
            $(this).val(formatrupiah(obj.value))
        })
    })

    $('.rupiah').keyup(function(e) {
        var input = $(this).val()
        $(this).val(formatrupiah(input))
    });
    function formatrupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }

    $("#usaha").on("change", function() {
        if ($(this).val() == "tanah") {
            $("#tanah").removeClass("hidden");
            $("#kendaraan").addClass("hidden");
            $("#tanah-dan-bangunan").addClass("hidden");
            $("#form-tanah").removeClass("hidden");
            $("#form-kendaraan").addClass("hidden");
        } else if ($(this).val() == "kendaraan") {
            $("#tanah").addClass("hidden");
            $("#kendaraan").removeClass("hidden");
            $("#tanah-dan-bangunan").addClass("hidden");
            $("#form-tanah").addClass("hidden");
            $("#form-kendaraan").removeClass("hidden");
        } else if ($(this).val() == "tanah-dan-bangunan") {
            $("#tanah").addClass("hidden");
            $("#kendaraan").addClass("hidden");
            $("#tanah-dan-bangunan").removeClass("hidden");
            $("#form-tanah").removeClass("hidden");
            $("#form-kendaraan").addClass("hidden");
        } else {
            $("#form-tanah").addClass("hidden");
            $("#form-kendaraan").addClass("hidden");
            $("#tanah").addClass("hidden");
            $("#tanah-dan-bangunan").addClass("hidden");
            $("#kendaraan").addClass("hidden");
        }
    });
    $("#is-npwp").on("change", function() {
        if ($(this).is(":checked")) {
            $("#npwp").removeClass("hidden");
        } else {
            $("#npwp").addClass("hidden");
        }
    });
    $("#shm-check").on("change", function() {
        if ($(this).is(":checked")) {
            $("#no-shm-input").removeClass("disabled");
            $("#no-shm-input").prop("disabled", false);
        } else {
            $("#no-shm-input").addClass("disabled");
            $("#no-shm-input").prop("disabled", true);
        }
    });
    $("#shgb-check").on("change", function() {
        if ($(this).is(":checked")) {
            $("#no-shgb-input").removeClass("disabled");
            $("#no-shgb-input").prop("disabled", false);
        } else {
            $("#no-shgb-input").addClass("disabled");
            $("#no-shgb-input").prop("disabled", true);
        }
    });
    // petak
    $("#petak-check").on("change", function() {
        if ($(this).is(":checked")) {
            $("#no-petak-input").removeClass("disabled");
            $("#no-petak-input").prop("disabled", false);
        } else {
            $("#no-petak-input").addClass("disabled");
            $("#no-petak-input").prop("disabled", true);
        }
    });
    // ijin usaha
    $("#ijin-usaha").on("change", function() {
        if ($(this).val() == "nib") {
            $("#nib").removeClass("hidden");
            $("#label-nib").removeClass("hidden");
            $("#dokumen-nib").removeClass("hidden");
            $("#label-dokumen-nib").removeClass("hidden");
            $("#npwp").removeClass("hidden");
            $("#label-npwp").removeClass("hidden");
            $("#dokumen-npwp").removeClass("hidden");
            $("#label-dokumen-npwp").removeClass("hidden");
            $("#have-npwp").addClass("hidden");
            $("#surat-keterangan-usaha").addClass("hidden");
            $("#label-surat-keterangan-usaha").addClass("hidden");
            $("#dokumen-surat-keterangan-usaha").addClass("hidden");
            $("#label-dokumen-surat-keterangan-usaha").addClass("hidden");
        } else if ($(this).val() == "sku") {
            $("#surat-keterangan-usaha").removeClass("hidden");
            $("#label-surat-keterangan-usaha").removeClass("hidden");
            $("#label-dokumen-surat-keterangan-usaha").removeClass("hidden");
            $("#dokumen-surat-keterangan-usaha").removeClass("hidden");
            $("#have-npwp").removeClass("hidden");
            $("#npwp").addClass("hidden");
            $("#label-npwp").addClass("hidden");
            $("#label-dokumen-npwp").addClass("hidden");
            $("#dokumen-npwp").addClass("hidden");
            $("#label-nib").addClass("hidden");
            $("#nib").addClass("hidden");
            $("#label-dokumen-nib").addClass("hidden");
            $("#dokumen-nib").addClass("hidden");
        } else {
            $("#surat-keterangan-usaha").addClass("hidden");
            $("#label-surat-keterangan-usaha").addClass("hidden");
            $("#label-dokumen-surat-keterangan-usaha").addClass("hidden");
            $("#dokumen-surat-keterangan-usaha").addClass("hidden");
            $("#nib").addClass("hidden");
            $("#label-nib").addClass("hidden");
            $("#dokumen-nib").addClass("hidden");
            $("#label-dokumen-nib").addClass("hidden");
            $("#npwp").addClass("hidden");
            $("#label-npwp").addClass("hidden");
            $("#dokumen-npwp").addClass("hidden");
            $("#label-dokumen-npwp").addClass("hidden");
            $("#have-npwp").addClass("hidden");
            $("#sku").addClass("hidden");
            $("#label-sku").addClass("hidden");
            $("#dokumen-sku").addClass("hidden");
            $("#label-dokumen-sku").addClass("hidden");
        }
    });
    // npwp
    $("#is-npwp").on("change", function() {
        if ($(this).is(":checked")) {
            $("#npwp").removeClass("hidden");
            $("#label-npwp").removeClass("hidden");
            $("#dokumen-npwp").removeClass("hidden");
            $("#label-dokumen-npwp").removeClass("hidden");
        } else {
            $("#npwp").addClass("hidden");
            $("#label-npwp").addClass("hidden");
            $("#dokumen-npwp").addClass("hidden");
            $("#label-dokumen-npwp").addClass("hidden");
        }
    });

    $( document ).ready(function() {
        countFormPercentage()
    });

    function countFormPercentage() {
        $.each($('.tab-wrapper .btn-tab'), function(i, obj) {
            var tabId = $(this).data('tab')
            if (tabId) {
                var percentage = formPercentage(`${tabId}-tab`)
                $(this).find('.percentage').html(`${percentage}%`)
            }
        })
    }

    // tab
    $(".tab-wrapper .btn-tab").click(function(e) {
        e.preventDefault();
        var tabId = $(this).data("tab");
        countFormPercentage()

        $(".is-tab-content").removeClass("active");
        $(".tab-wrapper .btn-tab").removeClass(
            "active-tab"
        );
        $(".tab-wrapper .btn-tab").removeClass("active-tab");
        $(".tab-wrapper .btn-tab").removeClass("active-tab");
        $(".tab-wrapper .btn-tab").addClass("disable-tab");

        $(this).addClass("active-tab");

        if (tabId) {
            $(this).removeClass("disable-tab");
            $(this).removeClass("disable-tab");
        }

        $("#" + tabId + "-tab").addClass("active");
    });

    $(".next-tab").on("click", function(e) {
        const $activeContent = $(".is-tab-content.active");
        const $nextContent = $activeContent.next();
        const tabId = $activeContent.attr("id")
        const dataTab = tabId.replaceAll('-tab', '')
        // Set percentage
        var percentage = formPercentage(tabId)
        $('.tab-wrapper').find(`[data-tab=${dataTab}]`).find('.percentage').html(`${percentage}%`)
        // Remove class active current nav tab
        $('.tab-wrapper').find(`[data-tab=${dataTab}]`).removeClass('active-tab')

        if ($nextContent.length) {
            const dataNavTab = $nextContent.attr("id") ? $nextContent.attr("id").replaceAll('-tab', '') : null
            if (dataNavTab)
                $('.tab-wrapper').find(`[data-tab=${dataNavTab}]`).addClass('active-tab')

            $activeContent.removeClass("active");
            $nextContent.addClass("active");
        }else{
            $(".next-tab").addClass('hidden');
            $('.btn-simpan').removeClass('hidden')
        }
    });

    $(".prev-tab").on("click", function() {
        const $activeContent = $(".is-tab-content.active");
        const $prevContent = $activeContent.prev();
        const tabId = $activeContent.attr("id")
        var percentage = formPercentage(tabId)
        const dataTab = tabId.replaceAll('-tab', '')
        // Set percentage
        var percentage = formPercentage(tabId)
        $('.tab-wrapper').find(`[data-tab=${dataTab}]`).find('.percentage').html(`${percentage}%`)
        // Remove class active current nav tab
        $('.tab-wrapper').find(`[data-tab=${dataTab}]`).removeClass('active-tab')

        if ($prevContent.length) {
            const dataNavTab = $prevContent.attr("id") ? $prevContent.attr("id").replaceAll('-tab', '') : null
            if (dataNavTab)
                $('.tab-wrapper').find(`[data-tab=${dataNavTab}]`).addClass('active-tab')
            $activeContent.removeClass("active");
            $prevContent.addClass("active");
            $(".next-tab").removeClass('hidden');
            $('.btn-simpan').addClass('hidden')
        }
    });

    function formPercentage(tabId) {
        var form = `#${tabId}`;
        // var form = `#aspek-keuangan-tab`;
        var inputFile = $(form + " input[type=file]")
        var inputText = $(form + " input[type=text]")
        var inputNumber = $(form + " input[type=number]")
        var inputDate = $(form + " input[type=date]")
        var inputEmail = $(form + " input[type=email]")
        var inputHidden = $(form + " input[type=hidden]")
        var select = $(form + " select")
        var textarea = $(form + " textarea")
        var totalInput = 0;
        var totalInputChecked = 0;
        var totalInputNull = 0;
        var totalInputFilled = 0;
        var totalInputHidden = 0;
        var totalInputReadOnly = 0;
        var percent = 0;

        $.each(inputText, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            var isHidden = formGroup.css('display') == 'none'
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden)
                totalInput++
            var isNull = (v.value == '' && $.trim(v.value) == '')
            if ((isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    var val = $(this).attr('id').toString()
                    if (val != "persentase_kebutuhan_kredit_opsi" && val != "ratio_tenor_asuransi_opsi" && val != "ratio_coverage_opsi" && val != "repayment_capacity_opsi") {
                        //console.log(val)
                        nullValue.push(val.replaceAll("_", " "))
                    }
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).is(":checked") && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            } else if (!$(this).is(':checked')) {
                totalInputChecked++;
            }
        })

        $.each(inputEmail, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            var isHidden = formGroup.css('display') == 'none'
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden)
                totalInput++
            var isNull = (v.value == '' && $.trim(v.value) == '' && $.trim(v.value) == '')
            if ((isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    nullValue.push($(this).attr('id').toString().replaceAll("_", " "))
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).is(":checked") && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            } else if (!$(this).is(':checked')) {
                totalInputChecked++;
            }
        })

        $.each(inputHidden, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            if ((!$(this).prop('disabled') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputHidden++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        $.each(inputFile, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            var isHidden = formGroup.css('display') == 'none'
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden)
                totalInput++
            var isNull = (v.value == '' && $.trim(v.value) == '' && ($(this).prop('defaultValue') == '' || !$(this).prop('defaultValue')))
            if ((isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden')) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    nullValue.push($(this).attr('id').toString().replaceAll("_", " "))
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        $.each(inputNumber, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            var isHidden = formGroup.css('display') == 'none'
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden)
                totalInput++
            var isNull = (v.value == '' || v.value == '0' || $.trim(v.value) == '') && !$(this).prop('readonly') && !$(this).prop('hidden')
            if ((isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    nullValue.push($(this).attr('id').toString().replaceAll("_", " "))
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        $.each(inputDate, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            var isHidden = formGroup.css('display') == 'none'
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden)
                totalInput++
            var isNull = (v.value == '' || v.value == '0' || $.trim(v.value) == '')
            if ((isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    nullValue.push($(this).attr('id').toString().replaceAll("_", " "))
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        $.each(select, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            var isHidden = formGroup.css('display') == 'none'
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden)
                totalInput++
            var isNull = (v.value == '' || v.value == '0' || $.trim(v.value) == '')
            if ((isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    var val = $(this).attr('id').toString()
                    if (val != "persentase_kebutuhan_kredit_opsi" && val != "ratio_tenor_asuransi_opsi" && val != "ratio_coverage_opsi") {
                        nullValue.push(val.replaceAll("_", " "))
                    }
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputFilled++;

                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })

        $.each(textarea, function(i, v) {
            if ($(this).prop('readonly'))
                totalInputReadOnly++;
            var inputBox = $(this).closest('.input-box');
            var formGroup = inputBox.parent();
            var isHidden = formGroup.css('display') == 'none'
            if (!$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden)
                totalInput++
            var isNull = (v.value == '' || v.value == '0' || $.trim(v.value) == '')
            if ((isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden')) && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputNull++;
                if ($(this).attr('id') != undefined) {
                    let val =  $(this).attr('id').toString().replaceAll("_", " ");
                    if (val != 'komentar staff') {
                        nullValue.push($(this).attr('id').toString().replaceAll("_", " "))

                    }
                }
            } else if (!isNull && !$(this).prop('disabled') && !$(this).prop('readonly') && !$(this).prop('hidden') && !$(this).hasClass('hidden') && !inputBox.hasClass('hidden') && !formGroup.hasClass('hidden') && !isHidden) {
                totalInputFilled++;
                if ($(this).attr('id') != undefined) {
                    let val = $(this).attr("id").toString().replaceAll("_", " ");
                    for (var i = 0; i < nullValue.length; i++) {
                        while (nullValue[i] == val) {
                            nullValue.splice(i, 1)
                            break;
                        }
                    }
                }
            }
        })
        var totalReadHidden = (totalInputHidden + totalInputReadOnly);
        var total = totalInput + totalInputChecked;
        percent = (totalInputFilled / (totalInput - totalInputReadOnly)) * 100;

        return parseInt(percent)
    }

    $(".toggle-side").click(function(e) {
        $('.sidenav').toggleClass('hidden')
    })
    $('.owl-carousel').owlCarousel({
        margin: 10,
        autoWidth: true,
        dots: false,
        responsive: {
            0: {
                items: 3
            },
            600: {
                items: 5
            },
            1000: {
                items: 10
            }
        }
    })

    var num = 1;
    $(document).on('click', '.btn-add', function() {
        console.log($(this));
        const item_id = $(this).data('item-id');
        var item_element = $(`.${item_id}`);
        var iteration = item_element.length;
        var input = $(this).closest('.input-box');
        var multiple = input.find('.multiple-action');
        var new_multiple = multiple.html().replaceAll('hidden', '');
        input = input.html().replaceAll(multiple.html(), new_multiple);
        var parent = $(this).closest('.input-box').parent();
        var num = parent.find('.input-box').length + 1;
        num = parseInt($(".figure").text());
        $(".figure").text(num+1);
        parent.append(`
            <div class="input-box mb-4">
                ${input}
            </div>
        `);
        num = 1;
    });

    $(document).on('click', '.btn-minus', function() {
        const item_id = $(this).data('item-id');
        var item_element = $(`#${item_id}`)
        var parent = $(this).closest('.input-box')
        parent.remove()
    })
    function formatNpwp(value) {
        if (typeof value === 'string') {
            return value.replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/, '$1.$2.$3.$4-$5.$6');
        }
    }
    function autoFormatNPWP(NPWPString) {
        try {
            var cleaned = ("" + NPWPString).replace(/\D/g, "");
            var match = cleaned.match(/(\d{0,2})?(\d{0,3})?(\d{0,3})?(\d{0,1})?(\d{0,3})?(\d{0,3})$/);
            return [
                    match[1],
                    match[2] ? ".": "",
                    match[2],
                    match[3] ? ".": "",
                    match[3],
                    match[4] ? ".": "",
                    match[4],
                    match[5] ? "-": "",
                    match[5],
                    match[6] ? ".": "",
                    match[6]].join("")

        } catch(err) {
            return "";
        }
    }
    var npwp = $('#npwp_text').val();
    const NPWP = document.getElementById("npwp_text")

    NPWP.oninput = (e) => {
        e.target.value = autoFormatNPWP(e.target.value);
    }
    $('#npwp_text').val(autoFormatNPWP(npwp))
    // console.log($('#npwp_text').val().replace(/(\d{2})(\d{3})(\d{3})(\d{1})(\d{3})(\d{3})/, '$1.$2.$3.$4-$5.$6'));
    // NPWP format
    // $(document).on('keyup', '#npwp_text', function() {
    //     var input = $(this).val()
    //     $(this).val(formatNpwp(input))
    // })
</script>
<script src="{{ asset('') }}js/custom.js"></script>
