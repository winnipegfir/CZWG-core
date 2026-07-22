@extends('layouts.master')
@section('title', 'Event Rosters - Winnipeg FIR')
@section('description', 'Winnipeg FIR Event Rosters.')

@php
function eventPositionIcon($position) {
    switch (strtolower($position)) {
        case 'ground': return 'fa-road';
        case 'tower': return 'fa-broadcast-tower';
        case 'departure': return 'fa-plane-departure';
        case 'arrival': return 'fa-plane-arrival';
        case 'centre': case 'center': return 'fa-satellite-dish';
        default: return 'fa-headset';
    }
}
@endphp

@section('content')
    <div class="container evroster-page-wrap">
        <a href="{{route('dashboard.index')}}" class="dash-back-link">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>

        <div class="evroster-page-header mt-3">
            <h1 class="roster-page-title">Event Rosters</h1>
            <p class="roster-page-sub">Staffing plans for upcoming Winnipeg FIR events</p>
        </div>

        @if($events->count() == 0)
            <div class="evroster-empty">
                <i class="fas fa-calendar-day"></i>
                <p>No upcoming events with rosters yet.</p>
            </div>
        @else
            <div class="evroster-grid">
                @foreach($events as $e)
                    <div class="evroster-card">
                        <div class="evroster-card-header">
                            <div class="evroster-card-title">{{$e->name}}</div>
                            <div class="evroster-card-time"><i class="far fa-clock"></i> Starting {{$e->start_timestamp_pretty()}}</div>
                        </div>

                        <div class="evroster-card-body">
                            @if(count($e->controllers) == 0)
                                <div class="evroster-none">No event roster yet!</div>
                            @else
                                @foreach($positions as $p)
                                    @if($p->hasControllers($p->position, $e->id))
                                        <div class="evroster-position-group">
                                            <div class="evroster-position-label">
                                                <i class="fas {{eventPositionIcon($p->position)}}"></i>
                                                {{$p->position}}
                                            </div>
                                            <ul class="evroster-slot-list">
                                                @foreach($e->controllers as $c)
                                                    @if($c->position == $p->position)
                                                        <li class="evroster-slot">
                                                            <span class="evroster-slot-name">{{$c->user->fullName('FLC')}}</span>
                                                            <span class="evroster-slot-meta">
                                                                @if($c->airport)
                                                                    <span class="evroster-slot-airport">{{$c->airport}}</span>
                                                                @endif
                                                                <span class="evroster-slot-time">{{$c->start_timestamp}}z&ndash;{{$c->end_timestamp}}z</span>
                                                            </span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@stop
