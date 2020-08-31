@extends('layouts.email')

@section('to-line')
    <strong>Hi there,</strong>
@stop

@section('message-content')
    24 hours until <a href="{{url('/events/'.$event->slug)}}">{{$event->name}}!</a> Look below for the positions you are scheduled for:
    <br>
    @foreach($positions as $p)
        <br>
        <b>{{$p->airport}} {{$p->position}}</b> | {{$p->start_timestamp}} to {{$p->end_timestamp}}
        <br>
    @endforeach
    <br>
    If you are unable to make one of your scheduled positions, or the event, please contact our <a href="{{url('staff')}}">events coordinator</a>. We look forward to seeing you at the event!
    <br><br>
    <a href="{{url('/events/'.$event->slug)}}">View the event by clicking here.</a>
@stop

@section('footer-reason-line')
    you signed up for Winnipeg FIR event notifications.
@endsection
