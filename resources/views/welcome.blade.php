@extends('backend.app')
@section('content')
<div class="row" style="margin-top: -200px;">
    <div class="col-md-12 text-white">
        <div class="row">
            <div class="col-12 col-xl-8 mb-xl-0">
                <h3 class="font-weight-bold">Dashboard</h3>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 mt-4">
        <div class="card w-100">
            <div class="card-body">
                <button type="button" class="btn btn-primary btn-md mb-4" data-toggle="modal" data-target="#modal">
                    Tambah
                </button>

            </div>
        </div>
    </div>
</div>


@endsection
@push('script')

@endpush