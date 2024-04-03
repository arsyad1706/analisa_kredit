<div class="table-responsive">
    <table class="table table-hover table-custom">
        <thead>
            <tr class="table-primary">
                <th class="text-center">#</th>
                <th>Id Address</th>
                <th>Email</th>
                <th>Nama</th>
                <th>Role</th>
                <th>Cabang</th>
                <th>Lama Login</th>
                <th>IP</th>
                <th>Perangkat</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach ($data as $key => $item)
                {{-- @php
                    $cabang = '-';
                    if ($item->id_cabang) {
                        $dataCabang = DB::table('cabang')
                            ->where('id', $item->id_cabang)
                            ->first();
                        $cabang = $dataCabang->cabang;
                    }
                    // hitung berapa lama login
                    $start = strtotime($item->created_at);
                    $end = strtotime(date('Y-m-d H:i:s'));

                    // convert seconds to hours
                    $hours = intval(($end - $start)/3600);
                    // convert seconds to minutes
                    $mins = (int)(($end - $start) / 60);
                    // formating seconds
                    $secs = explode('.', number_format((float)(($end - $start) / 60), 2))[1];
                    $secs = $secs > 60 ? 0 : $secs;
                @endphp --}}
                @php
                    $cabang = '-';
                    if ($item->id_cabang) {
                        $dataCabang = DB::table('cabang')
                            ->where('id', $item->id_cabang)
                            ->first();
                        $cabang = $dataCabang != null ? $dataCabang->cabang : '-';
                    }

                    // Waktu login pengguna
                    $startTime = new DateTime($item->created_at);

                    // Waktu saat ini
                    $endTime = new DateTime('now');

                    // Hitung perbedaan waktu
                    $interval = $endTime->diff($startTime);

                    // Format waktu
                    $hours = $interval->h;
                    $minutes = $interval->i;
                    $seconds = $interval->s;
                @endphp

                <tr class="border-bottom-primary">
                    <td class="text-center text-muted">{{ $no }}</td>
                    <td>{{ $item->ip_address }}</td>
                    <td>{{ $item->email }}</td>
                    <td>
                        {{ $item->karyawan ? $item->karyawan['nama'] : $item->name }}
                    </td>
                    <td>{{ $item->role }}</td>
                    <td>{{ $cabang }}</td>
                    <td>
                        <span class="clock_{{$item->id}}"></span>
                        <script>
                            currentTime({{$hours}}, {{$minutes}}, {{$seconds}}, "clock_{{$item->id}}")
                            function currentTime(h, m, s, widget_id) {
                                let hh = parseInt(h);
                                let mm = parseInt(m);
                                let ss = parseInt(s);
                                ss++;

                                if (ss > 59) {
                                    mm++;
                                    ss = 0;
                                }

                                if (mm > 59) {
                                    hh++;
                                    mm = 0;
                                }

                                hh = (hh < 10) ? "0" + hh : hh;
                                mm = (mm < 10) ? "0" + mm : mm;
                                ss = (ss < 10) ? "0" + ss : ss;

                                let time = hh + ":" + mm + ":" + ss;
                                document.querySelector(`.${widget_id}`).innerHTML = time;
                                var t = setTimeout(function(){ currentTime(hh, mm, ss, `${widget_id}`) }, 1000);
                            }
                        </script>
                    </td>
                    <td>{{ $item->ip_address }}</td>
                    <td>{{ $item->device_name }}</td>
                    <td><h5 class="badge badge-info">Aktif</h5></td>
                    <td>
                        @if (auth()->user()->id != $item->user_id)
                            <form action="{{ route('reset-session', $item->user_id) }}" method="post">
                                @csrf
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmResetSession-{{$key}}">
                                    Reset
                                </button>
                                <div class="modal fade" id="confirmResetSession-{{$key}}" tabindex="-1" role="dialog" aria-labelledby="confirmResetSessionLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin akan mereset sesi ini?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" id="btn-hapus" class="btn btn-danger">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </td>
                </tr>
                @php
                    $no++;
                @endphp
            @endforeach
        </tbody>
    </table>
    <div class="pull-right">
        {{ $data->appends(Request::all())->links('vendor.pagination.custom') }}
    </div>
</div>
