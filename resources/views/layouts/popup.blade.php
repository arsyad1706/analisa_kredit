<div class="modal-layout-no-backdrop hidden" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal modal-sm bg-white"" role="document">
        <form action="" >
            <div class="modal-content">
                <div class="modal-head">
                    <div class="title">
                        <h5 class="font-bold text-2xl tracking-tighter text-theme-text" id="exampleModalLabel">PRODUK & SKEMA KREDIT</h5>
                    </div>
                    <button data-dismiss-id="exampleModal">
                        <iconify-icon
                            icon="iconamoon:close-bold"
                            class="text-2xl"
                        ></iconify-icon>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group-1">
                        <div class="input-box m-0">
                            <label for="produk">Pilih Produk Kredit</label>
                            <select class="form-select" id="produk" name="produk">
                                <option value="">- Pilih Produk Kredit -</option>
                                @foreach ($produkKredit as $item)
                                    <option value="{{$item->id}}" {{$produk == $item->name ? 'selected' : ''}}>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-box m-0">
                            <label for="skema">Pilih Skema Kredit</label>
                            <select class="form-select" id="skema" name="skema">
                                <option value="">- Pilih Skema Kredit -</option>
                                @foreach ($skemaKredit as $item)
                                    <option value="{{$item->id}}" {{$skema == $item->name ? 'selected' : ''}}>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-box m-0">
                            <label for="limit">Pilih Limit</label>
                            <select class="form-select" id="limit" name="limit">
                                <option value="">- Pilih Limit -</option>
                                @foreach ($limitKredit as $item)
                                    <option value="{{$item->id}}">{{number_format($item->from,0,',','.')}} {{$item->operator}} {{number_format($item->to,0,',','.')}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="flex justify-start items-end ml-4 pb-4">
                        <button type="submit" class="px-7 py-2 rounded font-semibold flex gap-3 bg-theme-primary border text-white mt-2 btn btn-primary" id="btnSkema" onclick="$('#exampleModal').modal('hide')">Simpan</button>
                    </div>
            </div>
        </form>
    </div>
</div>
