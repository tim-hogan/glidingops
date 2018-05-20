@extends('layouts.app')

@push('styles')
@endpush

@push('scripts')
@endpush

@section('content')
  <div class="row">
    <div class="col-lg-3 alert alert-danger">
      You are not authorized to perform this action.
    </div>
  </div>
@endsection