@extends('layouts.email')

@section('to-line', 'Hi '. $application->user->fullName('FLC') . ',')


@section('message-content')
    <strong style="font-family: 'Open Sans', 'Segoe UI', 'Roboto', 'Verdana', 'Arial', sans-serif;">Howdy,</strong>
    <p style="font-family: 'Open Sans', 'Segoe UI', 'Roboto', 'Verdana', 'Arial', sans-serif;">
        Your application to become a visitor at Winnipeg has been submitted and is now processing. We will read it within 24 hours!<br/>
        You will be notified of any updates regarding your application, until then, sit tight!
    </p>
@stop

@section('footer-to-line', $application->user->fullName('FLC').' ('.$application->user->email.')')

@section('footer-reason-line')
    they hold an account an account on the Winnipeg FIR website and submitted an application to become a visitor.
@endsection
