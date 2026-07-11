@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Book Training — Winnipeg FIR')

@section('content')

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container">

    <div class="mb-4">
        <h2 class="font-weight-bold mb-0" style="color:#122b44;">Book Training</h2>
        @if($student->instructor_id && $student->instructor)
            <p class="text-muted mb-0" style="font-size:0.875rem;">with {{ $student->instructor->user ? $student->instructor->user->fullName('FL') : 'your instructor' }} &mdash; sessions are booked in 1-hour windows &mdash; all times Zulu (UTC)</p>
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
                        <h5 class="font-weight-bold mb-3" style="color:#122b44;">Open Slots <span class="text-muted font-weight-normal" style="font-size:0.75rem;">(Zulu)</span></h5>
                        @if ($openSlots->isEmpty())
                            <p class="text-muted mb-0" style="font-size:0.875rem;">No open slots right now. Check back later.</p>
                        @else
                            @foreach ($openSlots as $slot)
                                @php
                                    $hourOptions = [];
                                    $cursor = $slot->start_time->copy();
                                    while ($cursor->copy()->addHour()->lte($slot->end_time)) {
                                        $hourOptions[] = $cursor->copy();
                                        $cursor->addHour();
                                    }
                                @endphp
                                <div style="display:flex; align-items:center; padding:0.6rem 0; border-bottom:1px solid #f1f5f9;">
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-weight:600; font-size:0.875rem; color:#122b44;">{{ $slot->start_time->format('D, M j') }}</div>
                                        <div style="font-size:0.78rem; color:#64748b;">
                                            {{ $slot->start_time->format('g:i A') }} &ndash; {{ $slot->end_time->format('g:i A') }}
                                            @if($slot->note) &middot; {{ $slot->note }} @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('training.book.store') }}" class="d-flex align-items-center" style="gap:0.3rem;">
                                        @csrf
                                        @if(count($hourOptions) > 1)
                                            <select name="start_time" class="form-control form-control-sm" style="width:auto; font-size:0.78rem;">
                                                @foreach($hourOptions as $opt)
                                                    <option value="{{ $opt->format('Y-m-d\TH:i') }}">{{ $opt->format('g:i A') }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="hidden" name="start_time" value="{{ $slot->start_time->format('Y-m-d\TH:i') }}">
                                        @endif
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
                        <h5 class="font-weight-bold mb-3" style="color:#122b44;">Your Upcoming Sessions <span class="text-muted font-weight-normal" style="font-size:0.75rem;">(Zulu)</span></h5>
                        @if ($myBookings->isEmpty())
                            <p class="text-muted mb-0" style="font-size:0.875rem;">You have no upcoming booked sessions.</p>
                        @else
                            @foreach ($myBookings as $slot)
                                <div style="display:flex; align-items:center; padding:0.6rem 0; border-bottom:1px solid #f1f5f9;">
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-weight:600; font-size:0.875rem; color:#122b44;">{{ $slot->start_time->format('D, M j') }}</div>
                                        <div style="font-size:0.78rem; color:#64748b;">
                                            {{ $slot->start_time->format('g:i A') }} &ndash; {{ $slot->end_time->format('g:i A') }}
                                            @if($slot->note) &middot; {{ $slot->note }} @endif
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
        <form id="calBookForm" method="POST" action="{{ route('training.book.store') }}" style="display:none;">
            @csrf
            <input type="hidden" name="start_time" id="calBookStartInput">
        </form>
        <form id="calCancelForm" method="POST" style="display:none;">@csrf</form>

        {{-- Pick-a-time modal, used when an open block spans more than 1 hour --}}
        <div class="modal fade" id="pickTimeModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" style="color:#122b44;">Pick a Start Time</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted" style="font-size:0.8rem;">Sessions are booked in 1-hour windows.</p>
                        <div id="pickTimeList" style="display:flex; flex-direction:column; gap:0.4rem;"></div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $calendarEvents = [];
            foreach ($openSlots as $slot) {
                $title = 'Open';
                if ($slot->note) {
                    $title .= ' — ' . $slot->note;
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
                if ($slot->note) {
                    $title .= ' — ' . $slot->note;
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

            var cancelUrlTemplate = "{{ route('training.book.cancel', ['id' => '__ID__']) }}";

            function pad(n) { return String(n).padStart(2, '0'); }
            function formatZulu(d) {
                return d.getUTCFullYear() + '-' + pad(d.getUTCMonth() + 1) + '-' + pad(d.getUTCDate()) + 'T' + pad(d.getUTCHours()) + ':' + pad(d.getUTCMinutes());
            }
            function displayZulu(d, opts) {
                opts = opts || { dateStyle: 'medium', timeStyle: 'short' };
                opts.timeZone = 'UTC';
                return d.toLocaleString([], opts) + ' Z';
            }
            function submitBooking(startDate) {
                document.getElementById('calBookStartInput').value = formatZulu(startDate);
                document.getElementById('calBookForm').submit();
            }
            function hourlyStarts(start, end) {
                var options = [];
                var cursor = new Date(start);
                while (new Date(cursor.getTime() + 3600000) <= end) {
                    options.push(new Date(cursor));
                    cursor = new Date(cursor.getTime() + 3600000);
                }
                return options;
            }
            function showPickTimeModal(options) {
                var list = document.getElementById('pickTimeList');
                list.innerHTML = '';
                options.forEach(function (d) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-outline-primary';
                    btn.textContent = displayZulu(d, { hour: 'numeric', minute: '2-digit' });
                    btn.onclick = function () {
                        $('#pickTimeModal').modal('hide');
                        submitBooking(d);
                    };
                    list.appendChild(btn);
                });
                $('#pickTimeModal').modal('show');
            }

            var calendarEl = document.getElementById('bookingCalendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'UTC',
                initialView: 'timeGridWeek',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'timeGridWeek,dayGridMonth' },
                height: 'auto',
                nowIndicator: true,
                events: events,
                eventClick: function (info) {
                    var kind = info.event.extendedProps.kind;
                    if (kind === 'open') {
                        var options = hourlyStarts(info.event.start, info.event.end);
                        if (options.length <= 1) {
                            if (options.length === 1 && confirm('Book this slot (' + displayZulu(options[0]) + ')?')) {
                                submitBooking(options[0]);
                            }
                        } else {
                            showPickTimeModal(options);
                        }
                    } else if (kind === 'booked') {
                        var when = displayZulu(info.event.start);
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
