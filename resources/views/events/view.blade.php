@extends('layouts.master')

@section('title', $event->name.' - Winnipeg FIR')
@section('description', 'View the upcoming event: '.$event->name)
@if($event->image_url)
    @section('image', $event->image_url)
@endif

@section('content')
<style>
    @keyframes live-pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }
    .event-prose h1, .event-prose h2, .event-prose h3,
    .event-prose h4, .event-prose h5, .event-prose h6 {
        color: #122b44; font-weight: 700; margin-top: 1.5rem; margin-bottom: 0.5rem;
    }
    .event-prose p { color: #343a40; line-height: 1.8; margin-bottom: 1rem; }
    .event-prose ul, .event-prose ol { color: #343a40; line-height: 1.8; margin-bottom: 1rem; padding-left: 1.5rem; }
    .event-prose li { margin-bottom: 0.3rem; }
    .event-prose a { color: #122b44; text-decoration: underline; }
    .event-prose hr { border-color: #e9ecef; margin: 1.5rem 0; }
</style>

{{-- Hero header --}}
<div style="background:#122b44; padding:2.75rem 0 2.25rem;">
    <div class="container">
        <a href="{{ route('events.index') }}" style="font-size:0.82rem; color:rgba(255,255,255,0.6); text-decoration:none; display:inline-flex; align-items:center; gap:0.35rem; margin-bottom:0.25rem;">
            <i class="fas fa-arrow-left fa-xs"></i> Events
        </a>
        <h1 style="color:#fff; font-weight:700; font-size:2rem; line-height:1.25; margin-bottom:0.75rem;">
            {{ $event->name }}
        </h1>
        <div style="display:flex; gap:1.25rem; flex-wrap:wrap; align-items:center; color:rgba(255,255,255,0.65); font-size:0.85rem;">
            <span><i class="far fa-clock mr-1"></i>{{ $event->start_timestamp_pretty() }} – {{ $event->end_timestamp_pretty() }}</span>
            @if($event->departure_icao && $event->arrival_icao)
                <span style="opacity:0.4;">•</span>
                <span><i class="fas fa-plane mr-1" style="font-size:0.75rem;"></i>{{ $event->departure_icao }} → {{ $event->arrival_icao }}</span>
            @endif
            @php
                $now = \Carbon\Carbon::now();
                $isLive = $event->start_timestamp <= $now && $event->end_timestamp >= $now;
            @endphp
            @if($isLive)
                <span style="opacity:0.4;">•</span>
                <span style="display:inline-flex; align-items:center; gap:0.4rem; background:rgba(220,38,38,0.25); color:#fca5a5; font-size:0.75rem; font-weight:700; letter-spacing:0.08em; padding:0.2rem 0.65rem; border-radius:999px;">
                    <span style="width:6px; height:6px; border-radius:50%; background:#ef4444; display:inline-block; animation:live-pulse 1.4s ease-in-out infinite;"></span>
                    LIVE NOW
                </span>
            @elseif(!$event->event_in_past())
                <span style="opacity:0.4;">•</span>
                <span style="color:#7dd3a8; font-weight:600;">{{ $event->starts_in_pretty() }}</span>
            @else
                <span style="opacity:0.4;">•</span>
                <span style="background:rgba(255,255,255,0.12); padding:0.15rem 0.55rem; border-radius:999px; font-size:0.75rem;">Past event</span>
            @endif
        </div>
    </div>
</div>

{{-- Body --}}
<div style="background:#fff; min-height:calc(100vh - 220px); padding:2.5rem 0;">
    <div class="container">
        <div class="row">

            {{-- Main content --}}
            <div class="col-md-12">

                @if($event->image_url)
                    <img src="{{ $event->image_url }}" alt="{{ $event->name }}"
                         style="width:100%; border-radius:0.5rem; margin-bottom:2rem; max-height:380px; object-fit:cover; display:block;">
                @endif

                <div class="event-prose">
                    {{ $event->html() }}
                </div>

                {{-- Controller application form --}}
                @if($event->start_timestamp > $timeNow && Auth::check() && $event->controller_applications_open && Auth::user()->rosterProfile)
                    <hr style="border-color:#e9ecef; margin:2rem 0;">
                    <h5 style="color:#122b44; font-weight:700; margin-bottom:1rem;">Apply to Control</h5>

                    @if($event->userHasApplied())
                        <div style="background:#d4edda; border:1px solid #c3e6cb; border-radius:0.375rem; padding:0.875rem 1rem; font-size:0.875rem; color:#155724;">
                            <i class="fas fa-check-circle mr-2"></i>You've already applied for this event. Check your <a href="{{ route('dashboard.index') }}" style="color:#155724; font-weight:600;">dashboard</a> for updates.
                        </div>
                    @else
                        <div style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:0.5rem; padding:1.5rem;">
                            <p style="font-size:0.875rem; color:#6c757d; margin-bottom:1.25rem;">Submit an application to the Events Coordinator to control during this event.</p>
                            <form id="app-form" method="POST" action="{{ route('events.controllerapplication.ajax') }}">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <input type="hidden" name="event_name" value="{{ $event->name }}">
                                <input type="hidden" name="event_date" value="{{ $event->start_timestamp }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Availability start (zulu)</label>
                                            <input type="text" name="availability_start" class="form-control" id="availability_start">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Availability end (zulu)</label>
                                            <input type="text" name="availability_end" class="form-control" id="availability_end">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Position requested</label>
                                    <select name="position" class="form-control custom-select" id="position">
                                        @if(Auth::user()->rating_id > 1)
                                            <option value="Delivery">Delivery</option>
                                            <option value="Ground">Ground</option>
                                            <option value="Tower">Tower</option>
                                        @endif
                                        @if(Auth::user()->rating_id > 3)
                                            <option value="Departure">Departure</option>
                                            <option value="Arrival">Arrival</option>
                                        @endif
                                        @if(Auth::user()->rating_id > 4)
                                            <option value="Centre">Centre</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Comments</label>
                                    <textarea name="comments" id="comments" rows="2" class="form-control" placeholder="Any additional information for the Events Coordinator"></textarea>
                                </div>

                                <button type="submit" class="btn" style="background:#122b44; color:#fff; border-radius:0.375rem; font-size:0.875rem; padding:0.5rem 1.5rem;">
                                    Submit Application
                                </button>
                            </form>
                            <script>
                                flatpickr('#availability_start', { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true, defaultDate: "{{ $event->flatpickr_limits()[0] }}" });
                                flatpickr('#availability_end',   { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true, defaultDate: "{{ $event->flatpickr_limits()[1] }}" });
                            </script>
                        </div>
                    @endif
                @endif

                {{-- Updates --}}
                @if(count($updates) > 0)
                    <hr style="border-color:#e9ecef; margin:2rem 0;">
                    <h5 style="color:#122b44; font-weight:700; margin-bottom:1rem;">Updates</h5>
                    @foreach($updates as $u)
                        <div style="border:1px solid #e9ecef; border-radius:0.5rem; padding:1.25rem; margin-bottom:0.75rem;">
                            <a href="{{ Request::url() }}#{{ $u->slug }}" name="{{ $u->slug }}"
                               style="font-weight:700; color:#122b44; font-size:0.95rem; text-decoration:none;">{{ $u->title }}</a>
                            <div style="font-size:0.78rem; color:#6c757d; margin:0.25rem 0 0.75rem;">
                                <i class="far fa-clock mr-1"></i>{{ $u->created_pretty() }}
                                &nbsp;·&nbsp;
                                <i class="far fa-user-circle mr-1"></i>{{ $u->author_pretty() }}
                            </div>
                            <div style="font-size:0.875rem; color:#343a40; line-height:1.7;">{{ $u->html() }}</div>
                        </div>
                    @endforeach
                @endif

            </div>


        </div>
    </div>
</div>
@stop
