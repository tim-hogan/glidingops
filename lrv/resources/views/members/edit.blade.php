@extends('layouts.app')

@push('styles')
@endpush

@push('scripts')
@endpush

@section('content')
  <div class='container'>
    <!-- Errors -->
    <div class="row">
      <div class="col-sm-12">
        <!-- if there are creation/update errors, they will show here -->
        {{ Html::ul($errors->all()) }}
      </div>
    </div>

    <!-- Main form -->
    <div class="row">
      <div class="col-sm-12">
        <h2>{{ ($method === 'put') ? "{$model->displayname}" : 'Create Member' }}</h2>
      </div>
    </div>
    {{ Form::model($model, ['route' => $route, 'method' => $method])  }}
      @if ($method === 'put')
      <div class="row">
        <div class="form-group">
          <div class="col-sm-2">{{ Form::label('id', 'Id') }}</div>
          <div class="col-sm-2">{{ $model->id }}</div>
          <div class="col-sm-10"></div>
        </div>
      </div>
      @endif
      <div class="row">
        <!-- <div class="form-group">
          <div class="col-sm-3">{{ Form::label('member_id', 'Member Num') }}</div>
          <div class="col-sm-9">{{ Form::text('member_id', Input::old('member_id'), array('class' => 'form-control')) }}</div>
        </div> -->
      </div>
      <div class="row">
        <div class="col-sm-2">{{ Form::submit('Save!', array('class' => 'btn btn-primary')) }}</div>
        <div class="col-sm-2"><a class="btn btn-small btn-warning" href="{{ URL::to('AllMembers') }}">Cancel</a></div>
        <div class="col-sm-10"></div>
      </div>
    {{ Form::close() }}
    <!-- Documents form -->
    <div class="row">
      <div class="col-sm-12"><h3>Documents</h3></div>
    </div>
  </div>
@endsection