@extends('layouts.app')

@push('styles')
  <link type="text/css" rel="stylesheet" href="{{ mix('assets/css/report.css') }}">
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

    function printit(){
      window.print();
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
      $towTotalTime += App\Helpers\FlightHelper::towDuration($flight);
      $gliderTotalTime += App\Helpers\FlightHelper::flightDuration($flight);
    @endphp

    <tr>
      <td>{{App\Helpers\DateTimeFormat::formatDateStr($flight->localdate)}}</td>
      <td class='right'>{{$flight->seq}}</td>
      <td>{{$flight->location}}</td>
      <td>{{$flight->launchtype_name}}</td>
      <td class='right'>{{$flight->towplane_rego_short}}</td>
      <td class='right'>{{$flight->glider}}</td>
      <td class='right'>{{$flight->towpilot_displayname}}</td>
      <td class='right'>
        @if($flight->pic)
          <a href='' onClick='return filterByMember({{$flight->pic}})'>{{$flight->pic_displayname}}</a>
        @endif
      </td>
      <td class='right'>
        @if($flight->p2)
          <a href='' onClick='return filterByMember({{$flight->p2}})'>{{$flight->p2_displayname}}</a>
        @endif
      </td>

      <td class='right'>
        {{
          App\Helpers\DateTimeFormat::timeLocalFormat(
            App\Helpers\FlightHelper::startDate($flight),$timezone,'H:i')
        }}
      </td>

      @if ($towChargeType->isTimeBased())
        @if ( App\Helpers\FlightHelper::towDuration($flight) > 0)
          <td class='right'>
          {{
            App\Helpers\DateTimeFormat::timeLocalFormat(
              App\Helpers\FlightHelper::towLandDate($flight), $timezone,'H:i')
          }}
          </td>
        @else
          <td></td>
        @endif
      @endif

      <td class='right'>
        {{
          App\Helpers\DateTimeFormat::timeLocalFormat(
            App\Helpers\FlightHelper::landDate($flight),$timezone,'H:i')
        }}
      </td>

      @if ($towChargeType->isTimeBased())
        @if (App\Helpers\FlightHelper::towDuration($flight) > 0 ) {
          <td class='right'>{{App\Helpers\DateTimeFormat::duration(App\Helpers\FlightHelper::towDuration($flight))}}</td>
        @else
          <td></td>
        @endif
      @endif

      <td class='right'>{{App\Helpers\DateTimeFormat::duration(App\Helpers\FlightHelper::flightDuration($flight))}}</td>

      @if ($towChargeType->isHeightBased())
        @if ($flight->launchtype == App\Models\LaunchType::towLaunchType()->id
              && $flight->type == App\Models\FlightType::glidingFlightType()->id)
          <td class='right'>{{$flight->height}}</td>
        @else
          <td></td>
        @endif
        <td class='right'>{{$flight->billingoption_name}}</td>
      @endif

      <td>{{App\Helpers\FlightHelper::fullComments($flight)}}</td>

      {{-- link to tracks database --}}
      @if (App\Helpers\FlightHelper::hasTracks($flight))
        <td>
          <a href="{{App\Helpers\FlightHelper::trackURI($flight)}}">MAP</a>
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