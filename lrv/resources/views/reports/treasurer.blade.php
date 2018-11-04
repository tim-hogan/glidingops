@extends('layouts.app')

@push('styles')
  <link href="{{ asset('app/css/report.css') }}" rel="stylesheet">
@endpush

@push('scripts')
@endpush

@section('content')
  <h2>Select Month and Year</h2>
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
      {!! Form::submit('View Report', ['name' => 'view', 'class' => 'btn btn-primary btn-sm']) !!}
    {!! Form::close() !!}
  </div>
  @if (isset($report))
  <div>
    <h2>Treasurer's report - {{(new DateTime($monthYear))->format('F Y')}}</h2>
    <h3>Uncharged flights</h3>
    <table class='table table-bordered table-condensed table-striped'>
      <thead>
        <tr>
          <td>ID</td>
          <td>DATE</td>
          <td>LOCATION</td>
          <td>GLIDER</td>
          <td>PIC</td>
          <td>P2</td>
          <td>DURATION</td>
          <td>GHARGE</td>
          <td>COMMENTS</td>
          <td>WARNINGS</td>
        </tr>
      </thead>
      <tbody>
        @foreach($report['unchargedFlights'] as $row)
          @php
            $flight = $row['flight'];
            $warnings = collect($row['warnings']);
            $rowClass = ($warnings->isEmpty()) ? '' : 'danger';
          @endphp
          <tr class='{{$rowClass}}'>
            <td>{{$flight->id}}</td>
            <td>{{(new DateTime($flight->localdate))->format('d/m/Y')}}</td>
            <td>{{$flight->location}}</td>
            <td>{{$flight->glider}}</td>
            <td>{{($flight->picMember) ? $flight->picMember->displayname : ''}}</td>
            <td>{{($flight->p2Member) ? $flight->p2Member->displayname : ''}}</td>
            <td>{{App\Helpers\DateTimeFormat::duration($flight->getFlightDuration())}}</td>
            <td>{{$flight->billingOption->name}}</td>
            <td>{{$flight->comments}}</td>
            <td>{{$warnings->implode('\n')}}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <h3>Charged flights</h3>
    @foreach($report['chargedFlights'] as $memberDisplayName => $rows)
    <h3><strong>{{$memberDisplayName}}</strong></h3>
    <table class='table table-bordered table-condensed table-striped'>
      <thead>
        <tr>
          <td>ID</td>
          <td>DATE</td>
          <td>LOCATION</td>
          <td>GLIDER</td>
          <td>PIC</td>
          <td>P2</td>
          <td>DURATION</td>
          <td>GHARGE</td>
          <td>COMMENTS</td>
          <td>CHARGES</td>
          <td>TOTAL</td>
          <td>WARNINGS</td>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $row)
          @php
            $flight = $row['flight'];
            $warnings = collect($row['warnings']);
            $rowClass = ($warnings->isEmpty()) ? '' : 'danger';
            $charges = collect($row['charges']);
          @endphp
          <tr class='{{$rowClass}}'>
            <td>{{$flight->id}}</td>
            <td>{{(new DateTime($flight->localdate))->format('d/m/Y')}}</td>
            <td>{{$flight->location}}</td>
            <td>{{$flight->glider}}</td>
            <td>{{($flight->picMember) ? $flight->picMember->displayname : ''}}</td>
            <td>{{($flight->p2Member) ? $flight->p2Member->displayname : ''}}</td>
            <td>{{App\Helpers\DateTimeFormat::duration($flight->getFlightDuration())}}</td>
            <td>{{$flight->billingOption->name}}</td>
            <td>{{$flight->comments}}</td>
            <td>
              <table>
                <tbody>
                  @foreach($charges as $key => $value)
                    <tr>
                      <td>{{$key}}</td>
                      <td>${{$value}}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </td>
            <td>${{$charges->sum()}}</td>
            <td>{{$warnings->implode('\n')}}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    @endforeach
    {{$report['count']}}
  </div>
  @endif
@endsection