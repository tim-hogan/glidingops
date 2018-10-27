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
    <table>
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
          <td>WARNINGS</td>
        </tr>
      </thead>
      <tbody>
        @foreach($report['rows'] as $row)
          @php
            $flight = $row['flight'];
          @endphp
          <tr>
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
                @foreach($row['memberCharges'] as $memberCharge)
                  @foreach($memberCharge['charges'] as $key => $value)
                    <tr>
                      <td>{{$key}}</td>
                      <td>{{$value}}</td>
                    </tr>
                  @endforeach
                @endforeach
                </tbody>
              </table>
            </td>
            <td>{{join('\n',$row['warnings'])}}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    {{$report['count']}}
  </div>
  @endif
@endsection