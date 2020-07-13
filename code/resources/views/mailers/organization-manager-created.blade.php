@extends('base-mailer')

@section('body')

    {{-- Enter email content below. You can embed the variable $organization_name, $organization_role, and $temp_password which will be null if this is an existing user. --}}

    <p>You have been added to the organization {{$organization_name}} on Jengu as a {{$organization_role}}!@if($temp_password) Please follow <a href="https://accept.jengu.app">this link</a> to accept your invitation. Your temporary password is...@endif</p>

    @if($temp_password)
        <h3>{{$temp_password}}</h3>

        <p>You will need to confirm your account with this email address, and enter a password before you can sign into the app.</p>
    @endif

@endsection