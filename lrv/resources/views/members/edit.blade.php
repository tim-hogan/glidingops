@extends('layouts.app')

@push('styles')
@endpush

@push('scripts')
@endpush

@section('content')
  <h2>{{ ($method === 'put') ? "{$model->displayname}" : 'Create Member' }}</h2>
  <!-- if there are creation/update errors, they will show here -->
  {{ Html::ul($errors->all()) }}

  {{ Form::model($model, ['route' => $route, 'method' => $method])  }}
    <div class="row">
      <div class="col-lg-3">
        @if ($method === 'put')
        <div class="form-group">
          {{ Form::label('id', 'Id') }}
          {{ $model->id }}
        </div>
        @endif
        <div class="form-group">
          {{ Form::label('member_id', 'Member Num') }}
          {{ Form::text('member_id', Input::old('member_id'), array('class' => 'form-control')) }}
        </div>
        {{ Form::submit('Save!', array('class' => 'btn btn-primary')) }}
        <a class="btn btn-small btn-warning" href="{{ URL::to('AllMembers') }}">Cancel</a>
      </div>
  {{ Form::close() }}
@endsection