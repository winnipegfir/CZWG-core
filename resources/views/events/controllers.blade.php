@extends('layouts.master')
@section('title', 'Event Rosters - Winnipeg FIR')
@section('description', 'Winnipeg FIR Event Rosters.')

@section('content')
    <div class="container py-4">
        <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Dashboard</a>
        <h1 class="blue-text font-weight-bold mt-2">Event Rosters</h1>
        <hr>
        @foreach($events as $e)
            <div class="col-sm-6">
                <div class="card">
                    <div class="h5 card-header font-weight-bold">
                        {{$e->name}} - Starting at {{$e->start_timestamp_pretty()}}
                    </div>
                    <div class="card-body">
                        @if(count($e->controllers) == 0)
                            No event roster yet!
                        @else
                            @foreach($positions as $p)
                                @if($p->hasControllers($p->position, $e->id))
                                    <h5 class="font-weight-bold">{{$p->position}}</h5>
                                    @foreach($e->controllers as $c)
                                        @if($c->position == $p->position)
                                            <p>&nbsp;&nbsp;<text class="font-weight-bold">{{$c->user->fullName('FLC')}}</text> is on {{$c->airport ? $c->airport : ''}} {{$c->position}} from {{$c->start_timestamp}}z to {{$c->end_timestamp}}z</p>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop
