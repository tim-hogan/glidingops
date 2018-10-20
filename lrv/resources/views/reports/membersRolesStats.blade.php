@extends('layouts.app')

@push('styles')
  <link href="{{ asset('app/css/report.css') }}" rel="stylesheet">
@endpush


@push('scripts')
  <script>
  </script>
@endpush

@section('content')
  <h2>Roles/Roster report</h2>
  <table>
    <thead>
      <tr>
        <th>User/Role</th>
        @foreach($roleNames as $roleName)
          <th>{{$roleName}}</th>
        @endforeach
      </tr>
    </thead>

    @foreach($members as $member)
      <tr>
        <td>{{$member->displayname}}</td>
        @foreach($roleNames as $roleName)
          @if($member->roles->contains($roleName))
            <td>&#x2713;</td>
          @else
            <td></td>
          @endif
        @endforeach
      </tr>
    @endforeach
  </table>
@endsection