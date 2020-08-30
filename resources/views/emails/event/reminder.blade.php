@extends('layouts.email')

@section('to-line')
    <strong>Hi there,</strong>
@stop

@section('message-content')
    24 hours until {{$event->name}}!
@stop

@section('footer-reason-line')
    you signed up for Winnipeg FIR event notifications.
@endsection
