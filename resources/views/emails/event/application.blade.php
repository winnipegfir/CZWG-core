@extends('layouts.email')

@section('to-line')
    <strong>Hi there,</strong>
@stop

@section('message-content')
    Thanks for signing up for <a href="{{url('/events/'.$event->slug)}}">{{$event->name}}!</a>
    <br><br>
    You requested {{$app->position}} and are available between {{$app->start_availability_timestamp}} to {{$app->end_availability_timestamp}}. If you need to make amendments, <a href="{{url('/staff')}}">please contact the Events Coordinator.</a>
    <br><br>
    Event Information Below:
    <br><br>
    {{$event->description}}
    @if($event->departure_icao)
        <br><br>
        Departure{{$event->departure_icao}}
    @endif
    @if($event->arrival_icao)
        <br><br>
        Arrival:{{$event->arrival_icao}}
    @endif
    <br>
    <br>
    <a href="{{url('/events/'.$event->slug)}}">View the event by clicking here.</a>
@stop

@section('footer-reason-line')
    you signed up for Winnipeg FIR event notifications.
@endsection
