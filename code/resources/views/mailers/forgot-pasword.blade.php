@extends('base-mailer')

@section('body')

    <p>
        It looks like you have forgotten your password! Not a big deal. Simply follow this <a href="https://wwww.projectathenia.com/forgot-password?token={{ $token }}">link</a>!
    </p>

    <p>
        Your password reset token will expire in 20 minutes.
    </p>

    <p>
        Thanks,
    </p>

    <p>
        Project Athenia
    </p>

@endsection