<div class="table-responsive">
    <table class="table table-hover table-custom">
        <thead class="table-primary">
            <th class="text-center">#</th>
            <th class="text-center">Skema Kredit</th>
            <th class="text-center">Nominal Dari</th>
            <th class="text-center">Nominal Sampai</th>
            <th class="text-center">Operator</th>
            <th>Aksi</th>
        </thead>
        <tbody>
            @php
                $page = Request::get('page');
                $no = !$page || $page == 1 ? 1 : ($page - 1) * 10 + 1;
            @endphp
            @foreach ($data as $item)
                <tr>
                    <td class="text-center">{{ $no }}</td>
                    <td class="text-center">{{ $item->name }}</td>
                    <td class="text-center">{{ number_format($item->from, 0, '.', '.') }}</td>
                    <td class="text-center">{{ number_format($item->to, 0, '.', '.') ?? '0' }}</td>
                    <td class="text-center">{{ $item->operator }}</td>
                    <td class="text-center">
                        <div class="form-inline btn-action text-center">
                            <a href="{{ route('skema-limit.show', $item->id) }}" class="mr-2">
                                <button type="button" id="PopoverCustomT-1" class="btn btn-rgb-primary btn-sm"
                                data-toggle="tooltip" title="Detail" data-placement="top"><span
                                    class="fa fa-info-circle fa-sm"></span></button>
                            </a>
                            <a href="{{ route('skema-limit.edit', $item->id) }}" class="mr-2">
                                <button type="button" id="PopoverCustomT-1" class="btn btn-rgb-primary btn-sm"
                                    data-toggle="tooltip" title="Edit" data-placement="top"><span
                                        class="fa fa-edit fa-sm"></span></button>
                            </a>
                            <form action="{{ route('skema-limit.destroy', $item->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmDelete">
                                    <span class="fa fa-trash"></span>
                                </button>
                                @include('skema-limit.confirm-modal')
                            </form>
                        </div>
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