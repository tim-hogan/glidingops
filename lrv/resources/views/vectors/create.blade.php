@extends('layouts.app')

@push('styles')
@endpush

@push('scripts')
@endpush

@section('content')
  <h2>New Vector</h2>
  <!-- if there are creation errors, they will show here -->
  {{ Html::ul($errors->all()) }}

  {{ Form::open(array('url' => 'app/vectors')) }}
    <div class="row">
      <div class="col-lg-3">
        <div class="form-group">
          {{ Form::label('location', 'Location') }}
          {{ Form::text('location', Input::old('location'), array('class' => 'form-control')) }}
        </div>
        <div class="form-group">
          {{ Form::label('designation', 'Designation') }}
          {{ Form::text('designation', Input::old('designation'), array('class' => 'form-control')) }}
        </div>
        {{ Form::submit('Create Vector!', array('class' => 'btn btn-primary')) }}
        <a class="btn btn-small btn-warning" href="{{ URL::to('app/vectors') }}">Cancel</a>
      </div>
  {{ Form::close() }}
@endsection