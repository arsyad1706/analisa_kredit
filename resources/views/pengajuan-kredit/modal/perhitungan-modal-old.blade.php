<!-- Modal -->
<div
  class="modal fade"
  id="perhitunganModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="modelTitleId"
  aria-hidden="true"
>
  <div
    class="modal-dialog modal-dialog-scrollable modal-lg"
    role="document"
  >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Perhitungan</h5>
        <button
          type="button"
          class="close"
          data-dismiss="modal"
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- form -->
        <form
          method=""
          action=""
        >
          <div class="row">
            <div class="col-md-6">
              <!-- pilih bulan -->
              <div class="form-group mb-4">
                <label
                  for="inputHarta"
                  class="font-weight-semibold"
                  >Pilih Bulan</label
                >
                <select
                  name=""
                  style="width: 100%; height: 40px"
                  class="select-date"
                  id=""
                >
                  <option selected>--Pilih Bulan--</option>
                  <option value="1">Januari</option>
                  <option value="2">Februari</option>
                  <option value="3">Maret</option>
                  <option value="4">April</option>
                  <option value="5">Mei</option>
                  <option value="6">Juni</option>
                  <option value="7">Juli</option>
                  <option value="8">Agustus</option>
                  <option value="9">September</option>
                  <option value="10">Oktober</option>
                  <option value="11">November</option>
                  <option value="12">Desember</option>
                </select>
              </div>
              <!-- form bagian neraca -->
              <div class="head">
                <h4
                  class="mb-4 font-weight-bold"
                  style="letter-spacing: -1px"
                >
                  Neraca
                </h4>
              </div>

              <!-- form bagian aktiva -->

              <div class="card">
                <h5 class="card-header">Aktiva</h5>

                <div class="card-body">
                  <div class="form-group">
                    <label
                      for="inputHarta"
                      class="font-weight-semibold"
                      >Harta</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="harta"
                      id="inputHarta"
                    />
                  </div>
                  <div class="form-group">
                    <label
                      for="inputKas"
                      class="font-weight-semibold"
                      >Kas / Bank</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="kas"
                      id="inputKas"
                    />
                  </div>
                  <div class="form-group">
                    <label
                      for="inputPersedian"
                      class="font-weight-semibold"
                      >Persediaan</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="persediaan"
                      id="inputPersedian"
                    />
                  </div>
                  <div class="form-group">
                    <label
                      for="inputPiutang"
                      class="font-weight-semibold"
                      >Piutang</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="piutang"
                      id="inputPiutang"
                    />
                  </div>
                  <div class="form-group">
                    <label for="inputInventaris">Inventaris</label>
                    <input
                      type="text"
                      class="form-control"
                      name="inventaris"
                      id="inputInventaris"
                    />
                  </div>
                </div>
              </div>
              <!-- form bagian pasiva -->
              <div class="card mt-5">
                <h5 class="card-header">Pasiva</h5>
                <div class="card-body">
                  <div class="form-group">
                    <label for="inputKewajiban">Kewajiban</label>
                    <input
                      type="text"
                      class="form-control"
                      name="kewajiban"
                      id="inputKewajiban"
                    />
                  </div>
                  <div class="form-group">
                    <label for="inputUangDagang">Utang Dagang</label>
                    <input
                      type="text"
                      class="form-control"
                      name="uang-dagang"
                      id="inputUangDagang"
                    />
                  </div>
                  <div class="form-group">
                    <label for="inputUangBank">Utang Bank</label>
                    <div class="input-group">
                      <input
                        type="text"
                        class="form-control"
                        name="uang-bank"
                        id="inputUangBank"
                      />

                      <div class="input-group-prepend">
                        <a
                          class="btn btn-danger"
                          data-toggle="collapse"
                          href="#collapseExample"
                          role="button"
                          aria-expanded="false"
                          aria-controls="collapseExample"
                        >
                          Tampilkan
                          <i class="bi bi-caret-down"></i>
                        </a>
                      </div>
                    </div>

                    <div
                      class="collapse mt-4"
                      id="collapseExample"
                    >
                      <div class="table-responsive">
                        <table
                          class="table"
                          id="table_item"
                          style="box-sizing: border-box"
                        >
                          <thead>
                            <tr>
                              <th scope="col">Baki debet</th>
                              <th scope="col">Plafon</th>
                              <th scope="col">Tenor</th>
                              <th scope="col">Aksi</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>
                                <input
                                  class="form-control"
                                  type="text"
                                  name="baki_debet"
                                />
                              </td>
                              <td>
                                <input
                                  class="form-control"
                                  type="text"
                                  name="plafon"
                                />
                              </td>
                              <td>
                                <input
                                  class="form-control"
                                  type="text"
                                  name="tenor"
                                />
                              </td>
                              <td>
                                <button
                                  id="btnTambah"
                                  class="btn btn-success"
                                  type="button"
                                >
                                  +
                                </button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputModal">Modal</label>
                    <input
                      type="text"
                      class="form-control"
                      name="modal"
                      id="inputModal"
                    />
                  </div>
                  <div class="form-group">
                    <label for="inputLaba">Laba</label>
                    <input
                      type="text"
                      class="form-control"
                      name="laba"
                      id="inputLaba"
                    />
                  </div>
                </div>
              </div>
              <div class="form-row mt-4">
                <div class="col">
                  <div class="form-group">
                    <label
                      for="inputPendapatanBersih"
                      class="font-weight-semibold"
                      >Pendapatan bersih</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="kas"
                      id="inputPendapatanBersih"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="form-group">
                    <label
                      for="inputPendapatanBersihPerBulan"
                      class="font-weight-semibold"
                      >Pendapatan Bersih Setiap Bulan</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="pendapatan_bersih_perbulan"
                      id="inputPendapatanBersihPerBulan"
                    />
                  </div>
                </div>
              </div>
              <div class="form-row">
                <div class="col">
                  <div class="form-group">
                    <label
                      for="inputAngsuranPokok"
                      class="font-weight-semibold"
                      >Angsuran Pokok Setiap Bulan</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="angsuran_pokok_perbulan"
                      id="inputAngsuranPokok"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="form-group">
                    <label
                      for="inputSisaPendapatanPerbulan"
                      class="font-weight-semibold"
                      >Sisa Pendapatan Setiap Bulan</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="sisa_pendapatan_perbulan"
                      id="inputSisaPendapatanPerbulan"
                    />
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="col">
                  <div class="form-group">
                    <label
                      for="inputPerputaranUsaha"
                      class="font-weight-semibold"
                      >Perputaran Usaha</label
                    >
                    <div class="input-group">
                      <input
                        type="text"
                        class="form-control"
                        name="perputaran_usaha"
                        id="inputPerputaranUsaha"
                      />
                      <div class="input-group-append">
                        <span
                          class="input-group-text"
                          id="basic-addon2"
                          >Bulan</span
                        >
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="form-group">
                    <label
                      for="inputKeuntunganUsaha"
                      class="font-weight-semibold"
                      >Keuntungan Usaha</label
                    >
                    <div class="input-group">
                      <input
                        type="text"
                        class="form-control"
                        name="keuntungan_usaha"
                        id="inputKeuntunganUsaha"
                      />
                      <div class="input-group-append">
                        <span
                          class="input-group-text"
                          id="basic-addon2"
                          >%</span
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <!-- form bagian laba rugi -->
              <div class="head mt-4">
                <h4
                  class="mb-4 font-weight-bold"
                  style="letter-spacing: -1px"
                >
                  Laba rugi
                </h4>
              </div>

              <!-- form bagian sebelum kredit -->
              <div class="card mb-4">
                <h5 class="card-header">Sebelum Kredit</h5>
                <div class="card-body">
                  <div class="form-group">
                    <label
                      for="inputHasilPenjualan"
                      class="font-weight-semibold"
                      >Hasil Penjualan</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="hasil_penjualan"
                      id="inputHasilPenjualan"
                    />
                  </div>
                  <div class="form-group">
                    <label
                      for="pend_sampingan"
                      class="font-weight-semibold"
                      >Pend.sampingan</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="pend_sampingan"
                      id="pend_sampingan"
                    />
                  </div>
                  <div class="form-group">
                    <label
                      for="inputTotal"
                      class="font-weight-semibold"
                      >Total</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="total"
                      id="inputTotal"
                    />
                  </div>
                </div>
              </div>
              <!-- form bagian sesudah kredit -->
              <div class="card mb-4">
                <h5 class="card-header">Sesudah Kredit</h5>
                <div class="card-body">
                  <div class="form-row">
                    <div class="col">
                      <div class="form-group">
                        <label
                          for="inputBiayaUsaha"
                          class="font-weight-semibold"
                          >Biaya Usaha</label
                        >
                        <input
                          type="text"
                          class="form-control"
                          name="biaya_usaha"
                          id="biayaUsaha"
                        />
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <label
                          for="inputBiayaRumahTangga"
                          class="font-weight-semibold"
                          >Biaya Rumah Tangga</label
                        >
                        <input
                          type="text"
                          class="form-control"
                          name="biaya_rumah_tangga"
                          id="inputBiayaRumahTangga"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label
                      for="inputBiayaBungaPinjaman"
                      class="font-weight-semibold"
                      >Biaya Bunga Pinjaman</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="biaya_bunga_pinjaman"
                      id="inputBiayaBungaPinjaman"
                    />
                  </div>
                  <div class="form-group">
                    <label
                      for="inputBiayaLain"
                      class="font-weight-semibold"
                      >Biaya Lain-lain</label
                    >
                    <input
                      type="text"
                      class="form-control"
                      name="biaya_lain_lain"
                      id="inputBiayaLain"
                    />
                  </div>
                </div>
              </div>

              <!-- Kebutuhan Modal Kerja  -->
              <div class="card mt-4 mb-4">
                <h5 class="card-header">Kebutuhan Modal Kerja</h5>

                <div class="card-body">
                  <div class="form-row">
                    <div class="col">
                      <div class="form-group">
                        <label
                          for="inputPersediaan"
                          class="font-weight-semibold"
                          >Persediaan</label
                        >
                        <div class="input-group">
                          <input
                            type="text"
                            disabled
                            class="form-control"
                            name="Persediaan"
                            id="inputPersediaan"
                          />
                          <div class="input-group-append">
                            <span
                              class="input-group-text"
                              id="basic-addon2"
                              >%</span
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <label
                          for="inputPiutang"
                          class="font-weight-semibold"
                          >Piutang</label
                        >
                        <div class="input-group">
                          <input
                            type="text"
                            disabled
                            class="form-control"
                            name="piutang"
                            id="inputPiutang"
                          />
                          <div class="input-group-append">
                            <span
                              class="input-group-text"
                              id="basic-addon2"
                              >%</span
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="col">
                      <div class="form-group">
                        <label
                          for="inputKas"
                          class="font-weight-semibold"
                          >Kas</label
                        >
                        <div class="input-group">
                          <input
                            type="text"
                            disabled
                            class="form-control"
                            name="kas"
                            id="inputPersediaan"
                          />
                          <div class="input-group-append">
                            <span
                              class="input-group-text"
                              id="basic-addon2"
                              >%</span
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <label
                          for="inputKas"
                          class="font-weight-semibold"
                          >Total</label
                        >
                        <div class="input-group">
                          <input
                            type="text"
                            disabled
                            class="form-control"
                            name="kas"
                            id="inputPersediaan"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!--  Modal Kerja Sekarang  -->
              <div class="card mb-4">
                <h5 class="card-header">Modal Kerja Sekarang</h5>

                <div class="card-body">
                  <div class="form-row">
                    <div class="col">
                      <div class="form-group">
                        <label
                          for="inputPersediaan"
                          class="font-weight-semibold"
                          >Persediaan</label
                        >
                        <div class="input-group">
                          <input
                            type="text"
                            disabled
                            class="form-control"
                            name="Persediaan"
                            id="inputPersediaan"
                          />
                          <div class="input-group-append">
                            <span
                              class="input-group-text"
                              id="basic-addon2"
                              >%</span
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <label
                          for="inputPiutang"
                          class="font-weight-semibold"
                          >Piutang</label
                        >
                        <div class="input-group">
                          <input
                            type="text"
                            disabled
                            class="form-control"
                            name="piutang"
                            id="inputPiutang"
                          />
                          <div class="input-group-append">
                            <span
                              class="input-group-text"
                              id="basic-addon2"
                              >%</span
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="col">
                      <div class="form-group">
                        <label
                          for="inputKas"
                          class="font-weight-semibold"
                          >Kas</label
                        >
                        <div class="input-group">
                          <input
                            type="text"
                            disabled
                            class="form-control"
                            name="kas"
                            id="inputPersediaan"
                          />
                          <div class="input-group-append">
                            <span
                              class="input-group-text"
                              id="basic-addon2"
                              >%</span
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <label
                          for="inputKas"
                          class="font-weight-semibold"
                          >Total</label
                        >
                        <div class="input-group">
                          <input
                            type="text"
                            disabled
                            class="form-control"
                            name="kas"
                            id="inputPersediaan"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-row">
                <div class="col">
                  <div class="form-group">
                    <label
                      for="inputKebutuhanModal"
                      class="font-weight-semibold"
                      >Kebutuhan Modal</label
                    >
                    <div class="input-group">
                      <input
                        type="text"
                        disabled
                        class="form-control"
                        name="kebutuhan_modal"
                        id="inputKebutuhanModal"
                      />
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="form-group">
                    <label
                      for="inputUtangBank"
                      class="font-weight-semibold"
                      >Utang Bank</label
                    >
                    <div class="input-group">
                      <input
                        type="text"
                        disabled
                        class="form-control"
                        name="utang_bank"
                        id="inputUtangBank"
                      />
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label
                  for="inputKebutuhanKredit"
                  class="font-weight-semibold"
                  >Kebutuhan Kredit</label
                >
                <div class="input-group">
                  <input
                    type="text"
                    disabled
                    class="form-control"
                    name="kebutuhan_kredit"
                    id="inputKebutuhanKredit"
                  />
                </div>
              </div>
              <div class="form-group">
                <label
                  for="inputDibulatkan"
                  class="font-weight-semibold"
                  >Dibulatkan</label
                >
                <div class="input-group">
                  <input
                    type="text"
                    disabled
                    class="form-control"
                    name="dibulatkan"
                    id="inputDibulatkan"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- end form kerja sekarang -->

          <!-- end modal -->
        </form>
      </div>
      <!-- button wrapper -->
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-dismiss="modal"
        >
          Batal
        </button>
        <button
          type="button"
          class="btn btn-danger"
        >
          Simpan
        </button>
      </div>
    </div>
  </div>
</div>

  <script>
    var selectDate = $(".select-date");
    selectDate.select2();
    $("#btnTambah").on("click", function (e) {
      var number = $("#table_item tbody").children().length + 1;

      var template = `<tr>
                          <td>
                            <input
                                  class="form-control"
                                  type="text"
                                  name="baki_debet[]"
                            />
                          </td>
                              <td>
                                <input
                                  class="form-control"
                                  type="text"
                                  name="plafon[]"
                                />
                              </td>
                              <td>
                                <input
                                  class="form-control"
                                  type="tenor[]"
                                />
                              </td>
                              <td>
                                <button
                                
                                  class="btn-minus btn btn-danger"
                                  type="button"
                                >
                                  -
                                </button>
                             </td>
                        </tr>`;

      $("#table_item tbody").append(template);
    });

    $("#table_item").on("click", ".btn-minus", function () {
      $(this).closest("tr").remove();
    });
  </script>
  <style>
    .modal-lg {
      max-width: 90% !important;
    }
  </style>
