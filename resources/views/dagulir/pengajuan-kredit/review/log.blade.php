@php
    function getKaryawan($nip){
        $konfiAPI = DB::table('api_configuration')->first();
        $host = $konfiAPI->hcs_host;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $host . '/api/v1/karyawan/' . $nip,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        $json = json_decode($response);

        if ($json) {
            if ($json->data)
                return $json->data->nama_karyawan;
        }
    }
@endphp
<div class="space-y-5">
    @foreach ($rolesPemroses['roles'] as $key => $itemRole)
        @php
            $log = DB::table('log_pengajuan')
                ->where('id_pengajuan', $dataUmum->id)
                ->where('role', $itemRole)
                ->join('users', 'users.id', 'log_pengajuan.user_id')
                ->first();
            $avg = $dataUmum->average_by_sistem;
            if ($itemRole == 'Staf Analis Kredit') {
                $avg = $dataUmum->average_by_sistem;
            } elseif ($itemRole == 'Penyelia Kredit') {
                $avg = $dataUmum->average_by_penyelia;
            } elseif ($itemRole == 'PBO') {
                $avg = $dataUmum->average_by_pbo ? $dataUmum->average_by_pbo : $dataUmum->average_by_penyelia;
            } elseif ($itemRole == 'PBP') {
                if ($dataUmum->average_by_pbp && !$dataUmum->average_by_pbo && !$dataUmum->average_by_penyelia)
                    $avg = $dataUmum->average_by_pbp;
                else if (!$dataUmum->average_by_pbp && $dataUmum->average_by_pbo && !$dataUmum->average_by_penyelia)
                    $avg = $dataUmum->average_by_pbo;
                else if (!$dataUmum->average_by_pbp && !$dataUmum->average_by_pbo && $dataUmum->average_by_penyelia)
                    $avg = $dataUmum->average_by_penyelia;
            } elseif ($itemRole == 'Pincab') {
                $avg = $dataUmum->average_by_penyelia;
            }

            if ($avg > 0 && $avg <= 2) {
                $status = 'merah';
            } elseif ($avg > 2 && $avg <= 3) {
                $status = 'kuning';
            } elseif ($avg > 3) {
                $status = 'hijau';
            } else {
                $status = 'merah';
            }

            if (!$log) {
                $user = DB::table('pengajuan')
                    ->join('users', 'users.id', 'pengajuan.' . $rolesPemroses['idRoles'][$key])
                    ->select('users.role', 'users.nip', 'users.email', 'users.nip as nip_users')
                    ->where('pengajuan.id', $dataUmum->id)
                    ->first();
            } else {
                $user = DB::table('log_pengajuan')
                    ->join('users', 'users.id', 'log_pengajuan.user_id')
                    ->select('users.role', 'users.nip', 'users.email', 'log_pengajuan.nip as nip_users')
                    ->where('log_pengajuan.id_pengajuan', $dataUmum->id)
                    ->where('users.role', $itemRole)
                    ->first();
            }


        @endphp
        <div class="field-review">
            <div class="field-name">
                <label for="">{{ $itemRole }}</label>
            </div>
            <div class="field-answer">
                @if ($itemRole == 'Pincab')
                    <p>{{ $user ? $user->nip_users . ' - ' . getKaryawan($user->nip) . ' ' . '(' . $user->email . ')' : '-' }}</p>
                @else
                    <p>
                        {{ $user ? $user->nip_users . ' - ' . getKaryawan($user->nip) . ' ' . '(' . $user->email . ')' : '-' }}
                        <span>Skor</span>
                        @if ($status == 'hijau')
                            <span class="text-green-500">{{ $avg }}</span>
                        @elseif ($status == 'kuning')
                            <span class="text-yellow-500">{{ $avg }}</span>
                        @elseif ($status == 'merah')
                            <font class="text-red-500">
                                {{ $avg }}
                            </font>
                        @else
                            <font class="text-red-500">
                                {{ $avg }}
                            </font>
                        @endif
                    </p>
                @endif
            </div>
        </div>
    @endforeach
    {{-- <div class="field-review">
        <div class="field-name">
            <label for="">Staf Analis Kredit</label>
        </div>
        <div class="field-answer">
            <p>-</p>
        </div>
    </div>
    <div class="field-review">
        <div class="field-name">
            <label for="">Penyelia Kredit</label>
        </div>
        <div class="field-answer">
            <p>-</p>
        </div>
    </div>
    <div class="field-review">
        <div class="field-name">
            <label for="">Pincab</label>
        </div>
        <div class="field-answer">
            <p>-</p>
        </div>
    </div> --}}
</div>
