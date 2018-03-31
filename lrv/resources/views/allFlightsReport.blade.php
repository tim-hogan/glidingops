@extends('layouts.app')

@push('styles')
  <style>
    @media print {
        th {font-size: 10px;padding-left: 1em;}
        td {font-size: 10px;padding-left: 1em;}
        th.thname {font-size: 12px;padding-left: 0em;text-align: left;}
        h1 {font-size: 18px;}
        h2 {font-size: 16px;}
        h3 {font-size: 14px;}
        .indent1 {padding-left: 2em;}
        #print-button {display: none;}
        #divhdr {display: none;}
        @page {size: landscape;}
    }
    @media screen {
         th {font-size: 12px;padding-left: 20px;}
         td {font-size: 12px;padding-left: 20px;}
         th.thname {font-size: 14px;padding-left: 0px;text-align: left;}
         h1 {font-size: 20px;}
         h2 {font-size: 18px;}
         h3 {font-size: 16px;}
         .indent1 {padding-left: 20px;}
    }
    body {margin: 0px;font-family: Arial, Helvetica, sans-serif;}
    table {border-collapse: collapse;}
    .right {text-align: right;}
    .left {text-align: left;}
    .bordertop {border-top: black;border-top-style: solid;border-top-width: 1px;}
  </style>
@endpush

@push('scripts')
  <script>
  </script>
@endpush

@section('content')
  All your {{ sizeof($flights) }} flights will go here.

  <!-- Header -->
  <div id='divhdr'>
    {!! Form::open(['route' => 'flights.allFlightsReport', 'method'=> 'post', 'id' => 'inform']) !!}
      <h2>All Flights Report</h2>
      <table>
        <tr>
          <td>{!! Form::label('fromdate', 'From:') !!}</td>
          <td>{!! Form::date('fromdate',  $strDateFrom, ['id' => 'fmdate']) !!}</td></tr>
        <tr>
          <td>{!! Form::label('todate', 'To:') !!}</td>
          <td>{!! Form::date('todate',    $strDateTo, ['id' => 'todate']) !!}</td></tr>
      </table>
      {!! Form::submit('View Report', ['name' => 'view']) !!}
    {!! Form::close() !!}
  </div>
  <h2>Flights</h2>
  <table>
    <tr>
      <th>DATE</th>
      <th>SEQ</th>
      <th>LOCATION</th>
      <th>LAUNCH TYPE</th>
      <th>TOW</th>
      <th>GLIDER</th>
      <th>TOWY</th>
      <th>PIC</th>
      <th>P2</th>
      <th>TAKE OFF</th>
    </tr>
  </table>
@endsection