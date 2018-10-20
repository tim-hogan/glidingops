@extends('layouts.app')

@push('styles')
  <link href="{{ asset('app/css/report.css') }}" rel="stylesheet">
@endpush

@push('scripts')
@endpush

@section('content')
  <h2>Roles/Roster report</h2>
  <!-- Header -->
  <div id='divhdr'>
    {!! Form::open(['route' => 'reports.treasurer', 'method'=> 'post', 'id' => 'inform']) !!}
      <table>
        <tr>
          <td>{!! Form::label('monthYear', 'Month:') !!}</td>
          <td><input type='month' value='{!! $monthYear !!}' name='monthYear' id='monthYear'/></td>
        </tr>
      </table>
      <br/>
      {!! Form::submit('View Report', ['name' => 'view']) !!}
    {!! Form::close() !!}
  </div>
  <div>
    {{$count}}
  </div>
@endsection