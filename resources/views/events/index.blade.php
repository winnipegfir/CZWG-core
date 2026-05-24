@extends('layouts.master')

@section('title', 'Events - Winnipeg FIR')
@section('description', 'Check out the Winnipeg FIR events!')

@section('content')
<div style="background:#fff; min-height:calc(100vh - 60px); padding:2.5rem 0;">
    <div class="container">

        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h1 class="font-weight-bold mb-0" style="color:#122b44;">Events</h1>
                <p style="color:#6c757d; margin-bottom:0; margin-top:0.25rem;">Upcoming events in the Winnipeg FIR.</p>
            </div>
            <a href="#" data-toggle="modal" data-target="#requestModal"
               style="font-size:0.85rem; color:#122b44; text-decoration:none; border:1px solid #ced4da; border-radius:0.375rem; padding:0.4rem 0.9rem; white-space:nowrap;">
                <i class="fas fa-headset mr-1"></i> Request ATC Coverage
            </a>
        </div>
        <hr>

        @if(count($events) === 0)
            <div style="text-align:center; padding:4rem 0; color:#6c757d;">
                <i class="fas fa-calendar-alt fa-2x mb-3" style="opacity:0.3; display:block;"></i>
                <p>No upcoming events. Check back soon!</p>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                @foreach($events as $e)
                    <a href="{{ route('events.view', $e->slug) }}"
                       style="display:flex; align-items:stretch; border:1px solid #e9ecef; border-radius:0.5rem; overflow:hidden; text-decoration:none; color:inherit; transition:box-shadow 0.15s ease, transform 0.15s ease;">
                        {{-- Image or placeholder --}}
                        @if($e->image_url)
                            <div style="width:140px; flex-shrink:0; background:#f8f9fa;">
                                <img src="{{ $e->image_url }}" alt="{{ $e->name }}"
                                     style="width:140px; height:100%; object-fit:cover; display:block;">
                            </div>
                        @else
                            <div style="width:140px; flex-shrink:0; background:linear-gradient(135deg,#122b44,#1a3d5c); display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-calendar-alt fa-lg" style="color:rgba(255,255,255,0.2);"></i>
                            </div>
                        @endif
                        {{-- Details --}}
                        <div style="padding:1rem 1.25rem; flex:1; display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                            <div>
                                <div style="font-weight:700; font-size:1.05rem; color:#122b44; line-height:1.3; margin-bottom:0.25rem;">{{ $e->name }}</div>
                                <div style="font-size:0.8rem; color:#6c757d;">
                                    <i class="far fa-clock mr-1"></i>{{ $e->start_timestamp_pretty() }}
                                    @if($e->departure_icao && $e->arrival_icao)
                                        <span style="margin:0 0.5rem; opacity:0.4;">•</span>
                                        <i class="fas fa-plane mr-1" style="font-size:0.7rem;"></i>{{ $e->departure_icao }} → {{ $e->arrival_icao }}
                                    @endif
                                </div>
                            </div>
                            <div style="flex-shrink:0; font-size:0.8rem; font-weight:600; color:#122b44; white-space:nowrap;">
                                {{ $e->starts_in_pretty() }} &rarr;
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Past events --}}
        @if(count($pastEvents) > 0)
            <div style="margin-top:3rem;">
                <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#adb5bd; margin-bottom:0.75rem;">
                    Past Events
                </div>
                <div style="display:flex; flex-direction:column; gap:0;">
                    @foreach($pastEvents as $e)
                        <a href="{{ route('events.view', $e->slug) }}"
                           style="display:flex; align-items:center; justify-content:space-between; padding:0.6rem 0; border-bottom:1px solid #f1f3f5; text-decoration:none; color:inherit; gap:1rem;">
                            <div style="font-size:0.875rem; color:#6c757d; font-weight:500;">{{ $e->name }}</div>
                            <div style="font-size:0.78rem; color:#adb5bd; white-space:nowrap; flex-shrink:0;">{{ $e->start_timestamp_pretty() }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>

{{-- ATC coverage request modal --}}
<div class="modal fade" id="requestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:0.5rem;">
            <div class="modal-header" style="border-bottom:1px solid #e9ecef;">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Need ATC? We've Got You.</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="font-size:0.875rem; color:#495057;">
                <p>Winnipeg is happy to provide ATC for many events within our airspace!</p>
                <p>To request ATC for your event, contact Winnipeg's Events Coordinator by submitting a <a href="{{ route('tickets.index') }}" style="color:#122b44;">ticket</a> or via <a href="{{ route('staff') }}" style="color:#122b44;">email</a>. If the position is vacant, contact the FIR Chief instead.</p>
                <p class="mb-0">Thank you for choosing Winnipeg!</p>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e9ecef;">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
