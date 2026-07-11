@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Book Training — Winnipeg FIR')

@section('content')

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container">

    <div class="mb-4">
        <h2 class="font-weight-bold mb-0" style="color:#122b44;">Book Training</h2>
        @if($student->instructor_id && $student->instructor)
            <p class="text-muted mb-0" style="font-size:0.875rem;">with {{ $student->instructor->user ? $student->instructor->user->fullName('FL') : 'your instructor' }} &mdash; click an open slot on the calendar to book it</p>
        @endif
    </div>

    @if (!$student->instructor_id)
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-chalkboard-teacher fa-2x mb-2" style="color:#cbd5e1;"></i>
                <p class="mb-0">You don't have an assigned instructor yet, so there are no slots to show.</p>
            </div>
        </div>
    @else
        <div class="card mb-4">
            <div class="card-body">
                <div id="bookingCalendar"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="font-weight-bold mb-3" style="color:#122b44;">Open Slots</h5>
                        @if ($openSlots->isEmpty())
                            <p class="text-muted mb-0" style="font-size:0.875rem;">No open slots right now. Check back later.</p>
                        @else
                            @foreach ($openSlots as $slot)
                                <div style="display:flex; align-items:center; padding:0.6rem 0; border-bottom:1px solid #f1f5f9;">
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-weight:600; font-size:0.875rem; color:#122b44;">{{ $slot->start_time->format('D, M j') }}</div>
                                        <div style="font-size:0.78rem; color:#64748b;">
                                            {{ $slot->start_time->format('g:i A') }} &ndash; {{ $slot->end_time->format('g:i A') }}
                                            @if($slot->type) &middot; {{ $slot->type }} @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('training.book.store', $slot->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary py-0 px-2" style="font-size:0.78rem;">Book</button>
                                    </form>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="font-weight-bold mb-3" style="color:#122b44;">Your Upcoming Sessions</h5>
                        @if ($myBookings->isEmpty())
                            <p class="text-muted mb-0" style="font-size:0.875rem;">You have no upcoming booked sessions.</p>
                        @else
                            @foreach ($myBookings as $slot)
                                <div style="display:flex; align-items:center; padding:0.6rem 0; border-bottom:1px solid #f1f5f9;">
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-weight:600; font-size:0.875rem; color:#122b44;">{{ $slot->start_time->format('D, M j') }}</div>
                                        <div style="font-size:0.78rem; color:#64748b;">
                                            {{ $slot->start_time->format('g:i A') }} &ndash; {{ $slot->end_time->format('g:i A') }}
                                            @if($slot->type) &middot; {{ $slot->type }} @endif
                                        </div>
                                    </div>
                                    <span class="mr-2" style="background:#dcfce7; color:#15803d; font-size:0.7rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Booked</span>
                                    <form method="POST" action="{{ route('training.book.cancel', $slot->id) }}" onsubmit="return confirm('Cancel this session?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.78rem;">Cancel</button>
                                    </form>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden forms used by the calendar's click actions --}}
        <form id="calBookForm" method="POST" style="display:none;">@csrf</form>
        <form id="calCancelForm" method="POST" style="display:none;">@csrf</form>

        @php
            $calendarEvents = [];
            foreach ($openSlots as $slot) {
                $title = 'Open';
                if ($slot->type) {
                    $title .= ' — ' . $slot->type;
                }
                $calendarEvents[] = [
                    'id' => $slot->id,
                    'title' => $title,
                    'start' => $slot->start_time->toIso8601String(),
                    'end' => $slot->end_time->toIso8601String(),
                    'backgroundColor' => '#64748b',
                    'borderColor' => '#64748b',
                    'extendedProps' => ['kind' => 'open'],
                ];
            }
            foreach ($myBookings as $slot) {
                $title = 'Booked';
                if ($slot->type) {
                    $title .= ' — ' . $slot->type;
                }
                $calendarEvents[] = [
                    'id' => $slot->id,
                    'title' => $title,
                    'start' => $slot->start_time->toIso8601String(),
                    'end' => $slot->end_time->toIso8601String(),
                    'backgroundColor' => '#16a34a',
                    'borderColor' => '#16a34a',
                    'extendedProps' => ['kind' => 'booked'],
                ];
            }
        @endphp

        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var events = {!! json_encode($calendarEvents) !!};

            var bookUrlTemplate = "{{ route('training.book.store', ['id' => '__ID__']) }}";
            var cancelUrlTemplate = "{{ route('training.book.cancel', ['id' => '__ID__']) }}";

            var calendarEl = document.getElementById('bookingCalendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'timeGridWeek,dayGridMonth' },
                height: 'auto',
                nowIndicator: true,
                events: events,
                eventClick: function (info) {
                    var kind = info.event.extendedProps.kind;
                    var when = info.event.start.toLocaleString();
                    if (kind === 'open') {
                        if (confirm('Book this slot (' + when + ')?')) {
                            document.getElementById('calBookForm').action = bookUrlTemplate.replace('__ID__', info.event.id);
                            document.getElementById('calBookForm').submit();
                        }
                    } else if (kind === 'booked') {
                        if (confirm('Cancel this session (' + when + ')?')) {
                            document.getElementById('calCancelForm').action = cancelUrlTemplate.replace('__ID__', info.event.id);
                            document.getElementById('calCancelForm').submit();
                        }
                    }
                },
            });
            calendar.render();
        });
        </script>
    @endif

</div>
</div>
@stop
