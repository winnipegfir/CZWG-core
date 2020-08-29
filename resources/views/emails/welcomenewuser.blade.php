@extends('layouts.email')
@section('to-line', 'Hi '. $user->fullName('FLC') . '!')
@section('message-content')
<p>Welcome to the Winnipeg FIR, we're very excited that you're here!</p>
<p>Welcome to Winnipeg! This is the home to all things Winnipeg - from controller files, to roster info, to training, contact info and more. Thanks for stopping by!</p>
@endsection
@section('from-line')
Thanks,<br/>
<b>Nate Power</b><br>
<b>Winnipeg FIR Chief (WPG1)</b>
@endsection
@section('footer-to-line', $user->fullName('FLC').' ('.$user->email.')')
@section('footer-reason-line', 'as they just logged into the Winnipeg FIR website for the first time.')
