@extends('layouts.email')
@section('to-line', 'Hi '. $user->fullName('FLC') . '!')
@section('message-content')
<p>{!! nl2br(e($welcomeBody)) !!}</p>
@endsection
@section('from-line')
Thanks,<br/>
{!! nl2br(e($welcomeSignature)) !!}
@endsection
@section('footer-to-line', $user->fullName('FLC').' ('.$user->email.')')
@section('footer-reason-line', 'as they just logged into the Winnipeg FIR website for the first time.')
