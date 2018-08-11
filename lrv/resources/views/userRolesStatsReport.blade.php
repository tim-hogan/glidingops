@extends('layouts.app')

@push('styles')
  <style>
  </style>
@endpush


@push('scripts')
  <script>
  </script>
@endpush

@section('content')
  <h2>Roles/Roster report</h2>
  <table>
    <tr>
      <th>User/Role</th>
      @foreach(array_values($roleNames) as $roleName)
        <th>{{$roleName}}</th>
      @endforeach
    </tr>

    @php
      $currentMember = new stdClass();
      $currentMember->id = null;
    @endphp
    @foreach($members as $member)
      @if($member->id != $currentMember->id)
        @if($currentMember->id != null)
          <tr>
            <td>{{$member->displayname}}</td>
            @foreach(array_values($roleNames) as $roleName)
              @if(in_array($roleName, $currentMember->roles))
                <td>&#x2713;</td>
              @else
                <td></td>
              @endif
            @endforeach
          </tr>
        @endif
        @php
          $currentMember = $member;
          $currentMember->roles = [];
        @endphp
      @endif
      @php
        $currentMember->roles[] = $member->roleName;
      @endphp
    @endforeach
  </table>
@endsection