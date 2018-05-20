@extends('layouts.app')

@push('styles')
@endpush

@push('scripts')
@endpush

@section('content')
  <h2>Vectors</h2>
  <div class="row">
    <div class="col-lg-3">
      <div class="form-group">
        {{ Form::open(array('url' => 'app/vectors', 'method' => 'GET')) }}
          {{ Form::label('location', 'Location') }}
          <div class="input-group">
            {{ Form::text('location', Input::old('location'), array('class' => 'form-control')) }}
            <span class="input-group-btn">
              {{ Form::submit('Filter', array('class' => 'btn btn-small btn-info')) }}
            </span>
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  <a class="btn btn-small btn-info" href="{{ URL::to('app/vectors/create') }}">New</a>
  <table class="table table-striped">
    <thead class="thead-dark">
      <th>Id</th>
      <th>Location</th>
      <th>Designation</th>
      <th></th>
    </thead>
    <tbody>
      @foreach($vectors as $vector)
      <tr>
        <td>{{$vector->id}}</td>
        <td>{{$vector->location}}</td>
        <td>{{$vector->designation}}</td>
        <td>
          {{ Form::open(array('url' => 'app/vectors/' . $vector->id, 'class' => 'pull-right')) }}
              {{ Form::hidden('_method', 'DELETE') }}
              {{ Form::submit('Delete', array('class' => 'btn btn-warning')) }}
          {{ Form::close() }}
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
@endsection