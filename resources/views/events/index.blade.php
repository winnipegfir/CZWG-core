@extends('layouts.master')
@section('title', 'Events - Winnipeg FIR')
@section('description', 'Check out the Winnipeg FIR events!')
@section('content')
    <div class="container py-4">
        <div class="d-flex flex-row justify-content-between align-items-center mb-0">
            <h1 class="blue-text font-weight-bold">Upcoming Events</h1>
            <a href="#" class="btn btn-link float-right mx-0 px-0" data-toggle="modal" data-target="#requestModal">Need ATC Coverage? Click Here!</a>
        </div>
        <hr class="mt-0">
        <ul class="list-unstyled">
            @if (count($events) == 0)
            <li>No Events... Stay tuned!</li>
            @endif
            @foreach($events as $e)
            <div class="card my-2" style="@if($e->image_url) background-image:url({{$e->image_url}}); background-size: cover; background-position: center; color: white; @endif">
                <div class="card" style="@if($e->image_url) background-color: rgb(0, 0, 0, 0.6) @endif">
                    <div class="p-3">
                        <a href="{{route('events.view', $e->slug)}}">
                            <h3 style="@if($e->image_url) color: white @else color: #122b44 @endif">{{$e->name}}</h3>
                        </a>
                        <h5>{{$e->start_timestamp_pretty()}} to {{$e->end_timestamp_pretty()}}</h5>
                        @if ($e->departure_icao && $e->arrival_icao)
                            <h5 class="font-weight-bold">{{$e->departure_icao_data()['name']}} ({{$e->departure_icao_data()['icao']}})&nbsp;&nbsp;<i class="fas fa-plane"></i>&nbsp;&nbsp;{{$e->arrival_icao_data()['name']}} ({{$e->arrival_icao_data()['icao']}})</h5>
                        @endif
                        @if (!$e->event_in_past())
                        Starts {{$e->starts_in_pretty()}}
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </ul>
        <h5><a data-toggle="collapse" data-target="#pastEvents">Show Past Events <i class="fas fa-caret-down"></i></a></h5>
        <div class="collapse" id="pastEvents">
            <ul class="list-unstyled">
                @if (count($pastEvents) == 0)
                <li>No past events.</li>
                @endif
                @foreach($pastEvents as $e)
                <div class="card my-2">
                    <div class="d-flex flex-row justify-content-between">
                        <div class="p-3">
                            <a href="{{route('events.view', $e->slug)}}">
                                <h3 style="color: #122b44">{{$e->name}}</h3>
                            </a>
                            <h5>{{$e->start_timestamp_pretty()}} to {{$e->end_timestamp_pretty()}}</h5>                       
                            @if ($e->departure_icao && $e->arrival_icao)
                                <h5 class="font-weight-bold">{{$e->departure_icao_data()['name']}} ({{$e->departure_icao_data()['icao']}})&nbsp;&nbsp;<i class="fas fa-plane"></i>&nbsp;&nbsp;{{$e->arrival_icao_data()['name']}} ({{$e->arrival_icao_data()['icao']}})</h5>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </ul>
        </div>
    </div>
    <!-- ATC coverage request modal-->
    <div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Need ATC? We've Got You.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Winnipeg is happy to provide ATC for many events within our airspace!</p>
                    <p>To request ATC for your event, we recommend contacting Winnipeg's Events Coordinator by submitting a <a href="{{route('tickets.index')}}">ticket</a> or via <a href="{{route('staff')}}">email.</a> If the position is vacant, instead contact the FIR Chief.</p>
                    <p>Thank you for choosing Winnipeg!</p>
                </div>
            </div>
        </div>
    </div>
@endsection
