@extends('layouts.app')

@push('styles')
@endpush

@push('scripts')
@endpush

@section('content')
  <h2>Vectors</h2>
  <table class="table table-striped">
    <thead class="thead-dark">
      <th>Id</th>
      <th>Location</th>
      <th>Designation</th>
    </thead>
    <tbody>
      @foreach($vectors as $vector)
      <tr>
        <td>{{$vector->id}}</td>
        <td>{{$vector->location}}</td>
        <td>{{$vector->designation}}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
@endsection