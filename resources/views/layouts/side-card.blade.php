@php
    $dataUmum = isset($dataUmum) ? $dataUmum : null;

    $dataIndex = match ($skema ?? $dataUmum) {
        'PKPJ' => 1,
        'KKB' => 2,
        'Talangan Umroh' => 1,
        'Prokesra' => 1,
        'Kusuma' => 1,
        null => 2,
        default => 1,
    };
@endphp
<div class="row">
    @if (Auth::user()->role != 'Pincab' && Auth::user()->role != 'SPI' && auth()->user()->role != 'Kredit Umum' || auth()->user()->role != 'Direksi')
        <div class="col-md-3">
            @if (Auth::user()->role != 'Pincab')
                <div class="box-content side-wizard px-4 py-4 ">
                    <ul>
                        @if (Auth::user()->role != 'Staf Analis Kredit')
                            <li><label>RIWAYAT</label></li>
                            <li data-index='0'>
                                <input type="hidden" name="answer" class="answer">
                                <input type="hidden" name="answerFilled" class="answerFilled">
                                <a href="#"><span><i>0%</i></span> Pengembalian Data</a>
                            </li>
                        @endif
                        <li><label>DATA UMUM</label></li>
                        <li data-index='{{ Auth::user()->role != 'Staf Analis Kredit' ? 1 : 0 }}'>
                            <input type="hidden" name="answer" class="answer">
                            <input type="hidden" name="answerFilled" class="answerFilled">
                            <a href="#"><span><i>0%</i></span> Data Umum</a>
                            {{--  <a href="#"><span><i>{{ array_key_exists(0, $dataAnswer) ? $dataAnswer[0]['percentage'] : 0 }}%</i></span> Data Umum</a>  --}}
                        </li>
                        @if (($skema ?? $dataUmum?->skema_kredit) == 'KKB')
                            <li class="data-po-label"><label>DATA PO</label></li>
                            <li data-index='{{ Auth::user()->role != 'Staf Analis Kredit' ? 2 : 1 }}'>
                                <input type="hidden" name="answer" class="answer">
                                <input type="hidden" name="answerFilled" class="answerFilled">
                                <a href="#" class="data-po-link"><span><i>0%</i></span> Data PO</a>
                            </li>
                        @endif
                        <li><label>PEMBAHASAN PER ASPEK</label></li>
                        @foreach ($dataAspek as $key => $value)
                            @php
                                if (Auth::user()->role != 'Staf Analis Kredit') {
                                    $key += $dataIndex + 1;
                                }else{
                                    $key += $dataIndex;
                                }
                            @endphp
                            <li data-index='{{ $key }}'
                                class="{{ request()->routeIs('pengajuan-kredit.edit') == 'pengajuan-kredit' ? 'active' : '' }}">
                                <input type="hidden" name="answer" class="answer">
                                <input type="hidden" name="answerFilled" class="answerFilled">
                                <a href="#"><span><i class="fa fa-ban"></i></span>{{ $value->nama }}</a>
                                {{--  <a href="#"><span><i>{{ array_key_exists($key, $dataAnswer) ? $dataAnswer[$key]['percentage'] : 0 }}%</i></span>{{$value->nama}}</a>  --}}
                            </li>
                        @endforeach
                        <br>
                        <li><label> PENDAPAT dan USULAN</label></li>
                        @if (Auth::user()->role != 'Staf Analis Kredit')
                            <li class="last" data-index='{{ count($dataAspek) + $dataIndex + 1 }}'>
                                <input type="hidden" name="answer" class="answer">
                                <input type="hidden" name="answerFilled" class="answerFilled">
                                <a href="#"><span><i class="fa fa-ban"></i></span> Pendapat dan Usulan</a>
                            </li>
                        @else
                            <li class="last" data-index='{{ count($dataAspek) + $dataIndex }}'>
                                <input type="hidden" name="answer" class="answer">
                                <input type="hidden" name="answerFilled" class="answerFilled">
                                <a href="#"><span><i class="fa fa-ban"></i></span> Pendapat dan Usulan</a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    @endif
    <div class="@if (Auth::user()->role != 'Pincab' && Auth::user()->role != 'SPI' && auth()->user()->role != 'Kredit Umum' || auth()->user()->role != 'Direksi') {{ Auth::user()->role != 'Pincab' ? 'col-md-9' : 'col-md-12' }} @else col-md-12 @endif">
        <div class="box-content px-3 py-4 ">
            <div class="container cusutom">
                <div class="row row-breadcrumbs align-items-center">
                    <div class="col-md-6">
                        <h5>
                            {{ ucwords(str_replace('-', ' ', Request::segment(1))) }}</h5>
                    </div>
                    <div class="col-md-6 text-right">
                        <h6>{{ ucwords(str_replace('-', ' ', Request::segment(1))) }} / {{ $pageTitle }}</h6>
                    </div>
                </div>
                <hr class="mt-4">
                @yield('content')
            </div>
        </div>
    </div>
</div>
