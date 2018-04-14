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
    function filterByMember(memberId) {
      var form = document.getElementById('inform');
      form.elements["filterByMemberId"].value = memberId;
      form.submit();
      return false;
    }

    function clearFilterByMember() {
      var form = document.getElementById('inform');
      form.elements["filterByMemberId"].value = null;
      form.submit();
    }
  </script>
@endpush

@section('content')
  <!-- Header -->
  <div id='divhdr'>
    {!! Form::open(['route' => 'flights.allFlightsReport', 'method'=> 'post', 'id' => 'inform']) !!}
      <h2>All Flights Report</h2>
      <table>
        <tr>
          <td>{!! Form::label('fromdate', 'From:') !!}</td>
          <td>{!! Form::date('fromdate',  $strDateFrom, ['id' => 'fmdate']) !!}</td>
        </tr>
        <tr>
          <td>{!! Form::label('todate', 'To:') !!}</td>
          <td>{!! Form::date('todate',    $strDateTo, ['id' => 'todate']) !!}</td>
        </tr>
      </table>
      <input type='hidden' name='filterByMemberId' value={{($filterByMember) ? $filterByMember->id : null}}>
      @if($filterByMember)
        <p>
          Filtering by member: {{$filterByMember->displayname}}
          <button type='button' onClick='clearFilterByMember()'>Clear filter</button>
        </p>
      @endif
      <br/>
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
    @if ($towChargeType->isTimeBased())
      <th>TOW LAND</th>
    @endif
    <th>LAND</th>
    @if ($towChargeType->isTimeBased())
      <th>TOW DURATION</th>
    @endif
      <th>DURATION</th>
    @if ($towChargeType->isHeightBased())
      <th>HEIGHT</th>
    @endif
      <th>CHARGE</th>
      <th>COMMENTS</th>
    </tr>

    @php
      $flightsCount = 0;
      $towTotalTime = 0;
      $gliderTotalTime = 0;
    @endphp

    {{-- Render flights records --}}
    @foreach($flights as $flight)

    @php
      $flightsCount++;
      $towTotalTime += $flight->getTowDuration();
      $gliderTotalTime += $flight->getFlightDuration();
    @endphp

    <tr>
      <td>{{App\Helpers\DateTimeFormat::formatDateStr($flight->localdate)}}</td>
      <td class='right'>{{$flight->seq}}</td>
      <td>{{$flight->location}}</td>
      <td>{{$flight->launchType->name}}</td>
      <td class='right'>{{($flight->towPlane) ? $flight->towPlane->rego_short : ''}}</td>
      <td class='right'>{{$flight->glider}}</td>
      <td class='right'>{{($flight->towPilotMember) ? $flight->towPilotMember->displayname : ''}}</td>
      <td class='right'>
        @if($flight->picMember)
          <a href='' onClick='return filterByMember({{$flight->picMember->id}})'>{{$flight->picMember->displayname}}</a>
        @endif
      </td>
      <td class='right'>
        @if($flight->p2Member)
          <a href='' onClick='return filterByMember({{$flight->p2Member->id}})'>{{$flight->p2Member->displayname}}</a>
        @endif
      </td>

      <td class='right'>{{App\Helpers\DateTimeFormat::timeLocalFormat($flight->getStartDate(),
        $timezone,'H:i')}}</td>

      @if ($towChargeType->isTimeBased())
        @if ( $flight->towland > 0)
          <td class='right'>{{App\Helpers\DateTimeFormat::timeLocalFormat($flight->getTowlandDate(),
            $timezone,'H:i')}}</td>
        @else
          <td></td>
        @endif
      @endif

      <td class='right'>{{App\Helpers\DateTimeFormat::timeLocalFormat($flight->getLandDate(),
        $timezone,'H:i')}}</td>

      @if ($towChargeType->isTimeBased())
        @if (intval($flight->getTowDuration() > 0) ) {
          <td class='right'>{{App\Helpers\DateTimeFormat::duration($flight->getTowDuration())}}</td>
        @else
          <td></td>
        @endif
      @endif

      <td class='right'>{{App\Helpers\DateTimeFormat::duration($flight->getFlightDuration())}}</td>

      @if ($towChargeType->isHeightBased())
        @if ($flight->launchType == App\Models\LaunchType::towLaunchType()
              && $flight->flightType == App\Models\FlightType::glidingFlightType())
          <td class='right'>{{$flight->height}}</td>
        @else
          <td></td>
        @endif
        <td class='right'>{{$flight->billingoption->name}}</td>
      @endif

      <td>{{$flight->getFullComments()}}</td>

      {{-- link to tracks database --}}
      @if ($flight->hasTracks())
        <td>
          <a href="/MyFlightMap.php?glider={{$flight->glider}}&from={{$flight->getStartDate()->format('Y-m-d H:i:s')}}&to={{$flight->getLandDate()->format('Y-m-d H:i:s')}}&flightid={{$flight->id}}">MAP</a>
        </td>
      @endif

    </tr>
    @endforeach

    {{-- totals --}}
    <tr>
      <td colspan='11'>Total</td>
    @if ($towChargeType->isTimeBased())
      <td></td>
      <td class='right'>
        {{App\Helpers\DateTimeFormat::duration($towTotalTime)}}
      </td>
    @endif
      <td class='right'>
        {{App\Helpers\DateTimeFormat::duration($gliderTotalTime)}}
      </td>
    </tr>
    <tr>
        <td>Count</td>
        <td class='right'>{{$flights->count()}}</td>
    </tr>
  </table>
  <button onclick='printit()' id='print-button'>Print Report</button>

@endsection