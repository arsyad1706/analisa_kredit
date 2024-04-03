@extends('layouts.template')

@section('content')
    @include('components.notification')
    <div class="row justify-content-between">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <a href="{{ $btnLink }}" class="btn btn-primary px-3">
                        <i class="fa fa-plus-circle"></i>
                        {{ $btnText }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
        </div>
    </div>
    @include('skema-limit._table')
@endsection