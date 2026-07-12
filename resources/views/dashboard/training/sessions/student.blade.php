@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Book Training — Winnipeg FIR')

@section('content')

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container">

    <div class="mb-4">
        <div class="d-flex align-items-center" style="gap:0.6rem;">
            <h2 class="font-weight-bold mb-0" style="color:#122b44;">Book Training</h2>
            @if($student->mentorable)
                <span style="background:#dbeafe; color:#1d4ed8; font-size:0.75rem; font-weight:700; padding:0.25em 0.6em; border-radius:0.4rem;" title="Your instructor has marked you mentorable — you can book with any instructor.">Mentorable</span>
            @endif
        </div>
        @if($student->instructor_id && $student->instructor || $student->mentorable)
            <p class="text-muted mb-0" style="font-size:0.875rem;">
                @if($student->mentorable)
                    open to all instructors
                @else
                    with {{ $student->instructor->user ? $student->instructor->user->fullName('FL') : 'your instructor' }}
                @endif
                &mdash; sessions are booked in 1-hour windows &mdash; times shown in {{ \App\Models\Users\User::timezoneLabel($userTz) }}
                @if($userTz === 'UTC')
                    &mdash; <a href="{{ route('me.preferences') }}">set your timezone</a>
                @endif
            </p>
        @endif
    </div>

    @if (!$student->instructor_id && !$student->mentorable)
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-chalkboard-teacher fa-2x mb-2" style="color:#cbd5e1;"></i>
                <p class="mb-0">You don't have an assigned instructor yet, so there are no slots to show.</p>
            </div>
        </div>
    @else
        @php
            $instructorPalette = ['#2563eb', '#7c3aed', '#0891b2', '#db2777', '#4f46e5', '#059669', '#ea580c', '#65a30d'];
            $instructorColors = [];
            if ($student->mentorable) {
                foreach ($openSlots->pluck('instructor_id')->unique()->values() as $i => $iid) {
                    $instructorColors[$iid] = $instructorPalette[$i % count($instructorPalette)];
                }
            }
        @endphp

        <div class="card mb-4">
            <div class="card-body">
                @if($student->mentorable && count($instructorColors) > 1)
                    <div class="d-flex flex-wrap align-items-center mb-3" style="gap:0.5rem;">
                        <span class="text-muted" style="font-size:0.78rem; font-weight:600;">Show:</span>
                        @foreach($instructorColors as $iid => $color)
                            @php $iuser = optional(optional($openSlots->firstWhere('instructor_id', $iid))->instructor)->user; @endphp
                            <label style="cursor:pointer; display:inline-flex; align-items:center; gap:0.35rem; font-size:0.78rem; padding:0.25em 0.65em; border-radius:999px; border:1px solid {{ $color }}; color:{{ $color }}; margin-bottom:0;">
                                <input type="checkbox" class="instructor-filter-checkbox" value="{{ $iid }}" checked style="accent-color: {{ $color }};">
                                {{ $iuser ? $iuser->fullName('FL') : 'Instructor' }}
                            </label>
                        @endforeach
                    </div>
                @endif
                <div id="bookingCalendar"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="font-weight-bold mb-3" style="color:#122b44;">Open Slots <span class="text-muted font-weight-normal" style="font-size:0.75rem;">({{ \App\Models\Users\User::timezoneLabel($userTz) }})</span></h5>
                        @if ($openSlots->isEmpty())
                            <p class="text-muted mb-0" style="font-size:0.875rem;">No open slots right now. Check back later.</p>
                        @else
                            @foreach ($openSlots as $slot)
                                @php
                                    $startLocal = $slot->start_time->copy()->setTimezone($userTz);
                                    $endLocal = $slot->end_time->copy()->setTimezone($userTz);
                                    $hourOptions = [];
                                    $cursor = $startLocal->copy();
                                    while ($cursor->copy()->addHour()->lte($endLocal)) {
                                        $hourOptions[] = $cursor->copy();
                                        $cursor->addHour();
                                    }
                                @endphp
                                <div style="display:flex; align-items:center; padding:0.6rem 0; border-bottom:1px solid #f1f5f9;">
                                    @if($student->mentorable)
                                        <span style="width:8px; height:8px; border-radius:50%; background:{{ $instructorColors[$slot->instructor_id] ?? '#64748b' }}; margin-right:0.6rem; flex-shrink:0;"></span>
                                    @endif
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-weight:600; font-size:0.875rem; color:#122b44;">{{ $startLocal->format('D, M j') }}</div>
                                        <div style="font-size:0.78rem; color:#64748b;">
                                            {{ $startLocal->format('g:i A') }} &ndash; {{ $endLocal->format('g:i A') }}
                                            @if($student->mentorable && $slot->instructor && $slot->instructor->user)
                                                &middot; {{ $slot->instructor->user->fullName('FL') }}
                                            @endif
                                            @if($slot->note) &middot; {{ $slot->note }} @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('training.book.store') }}" class="d-flex align-items-center" style="gap:0.3rem;">
                                        @csrf
                                        <input type="hidden" name="instructor_id" value="{{ $slot->instructor_id }}">
                                        @if(count($hourOptions) > 1)
                                            <select name="start_time" class="form-control form-control-sm" style="width:auto; min-width:6.5rem; font-size:0.78rem; padding-right:1.6rem;">
                                                @foreach($hourOptions as $opt)
                                                    <option value="{{ $opt->format('Y-m-d\TH:i') }}">{{ $opt->format('g:i A') }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="hidden" name="start_time" value="{{ $startLocal->format('Y-m-d\TH:i') }}">
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
                        <h5 class="font-weight-bold mb-3" style="color:#122b44;">Your Upcoming Sessions <span class="text-muted font-weight-normal" style="font-size:0.75rem;">({{ \App\Models\Users\User::timezoneLabel($userTz) }})</span></h5>
                        @if ($myBookings->isEmpty())
                            <p class="text-muted mb-0" style="font-size:0.875rem;">You have no upcoming booked sessions.</p>
                        @else
                            @foreach ($myBookings as $slot)
                                @php $startLocal = $slot->start_time->copy()->setTimezone($userTz); $endLocal = $slot->end_time->copy()->setTimezone($userTz); @endphp
                                <div style="display:flex; align-items:center; padding:0.6rem 0; border-bottom:1px solid #f1f5f9;">
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-weight:600; font-size:0.875rem; color:#122b44;">{{ $startLocal->format('D, M j') }}</div>
                                        <div style="font-size:0.78rem; color:#64748b;">
                                            {{ $startLocal->format('g:i A') }} &ndash; {{ $endLocal->format('g:i A') }}
                                            @if($slot->note) &middot; {{ $slot->note }} @endif
                                        </div>
                                    </div>
                                    @if($slot->status === 'pending')
                                        <span class="mr-2" style="background:#fef3c7; color:#92400e; font-size:0.7rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem; white-space:nowrap;">Pending Confirmation</span>
                                    @else
                                        <span class="mr-2" style="background:#dcfce7; color:#15803d; font-size:0.7rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Booked</span>
                                    @endif
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
            <input type="hidden" name="instructor_id" id="calBookInstructorInput">
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
                        <div id="pickTimeList" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:0.5rem;"></div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $calendarEvents = [];
            foreach ($openSlots as $slot) {
                $title = 'Open';
                if ($student->mentorable && $slot->instructor && $slot->instructor->user) {
                    $title = $slot->instructor->user->fullName('FL');
                }
                if ($slot->note) {
                    $title .= ' — ' . $slot->note;
                }
                $color = $instructorColors[$slot->instructor_id] ?? '#64748b';
                $calendarEvents[] = [
                    'id' => $slot->id,
                    'title' => $title,
                    'start' => $slot->start_time->copy()->setTimezone($userTz)->format('Y-m-d\TH:i:s'),
                    'end' => $slot->end_time->copy()->setTimezone($userTz)->format('Y-m-d\TH:i:s'),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'extendedProps' => ['kind' => 'open', 'instructorId' => $slot->instructor_id],
                ];
            }
            foreach ($myBookings as $slot) {
                $title = $slot->status === 'pending' ? 'Pending' : 'Booked';
                if ($slot->note) {
                    $title .= ' — ' . $slot->note;
                }
                $color = $slot->status === 'pending' ? '#d97706' : '#16a34a';
                $calendarEvents[] = [
                    'id' => $slot->id,
                    'title' => $title,
                    'start' => $slot->start_time->copy()->setTimezone($userTz)->format('Y-m-d\TH:i:s'),
                    'end' => $slot->end_time->copy()->setTimezone($userTz)->format('Y-m-d\TH:i:s'),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'extendedProps' => ['kind' => 'booked'],
                ];
            }
        @endphp

        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var events = {!! json_encode($calendarEvents) !!};

            function visibleEvents() {
                var hidden = {};
                document.querySelectorAll('.instructor-filter-checkbox').forEach(function (cb) {
                    if (!cb.checked) hidden[cb.value] = true;
                });
                if (Object.keys(hidden).length === 0) return events;
                return events.filter(function (e) {
                    return e.extendedProps.kind !== 'open' || !hidden[String(e.extendedProps.instructorId)];
                });
            }

            var cancelUrlTemplate = "{{ route('training.book.cancel', ['id' => '__ID__']) }}";

            function pad(n) { return String(n).padStart(2, '0'); }
            function formatLocalTz(d) {
                return d.getUTCFullYear() + '-' + pad(d.getUTCMonth() + 1) + '-' + pad(d.getUTCDate()) + 'T' + pad(d.getUTCHours()) + ':' + pad(d.getUTCMinutes());
            }
            function displayLocalTz(d, opts) {
                opts = opts || { dateStyle: 'medium', timeStyle: 'short' };
                opts.timeZone = 'UTC';
                return d.toLocaleString([], opts);
            }
            function submitBooking(startDate, instructorId) {
                document.getElementById('calBookStartInput').value = formatLocalTz(startDate);
                document.getElementById('calBookInstructorInput').value = instructorId || '';
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
            function showPickTimeModal(options, instructorId) {
                var list = document.getElementById('pickTimeList');
                list.innerHTML = '';
                options.forEach(function (d) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-outline-primary btn-sm';
                    btn.style.fontSize = '0.8rem';
                    btn.style.padding = '0.5rem 0.25rem';
                    btn.textContent = displayLocalTz(d, { hour: 'numeric', minute: '2-digit' });
                    btn.onclick = function () {
                        $('#pickTimeModal').modal('hide');
                        submitBooking(d, instructorId);
                    };
                    list.appendChild(btn);
                });
                $('#pickTimeModal').modal('show');
            }

            var calendarEl = document.getElementById('bookingCalendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'UTC',
                now: function () {
                    var parts = new Intl.DateTimeFormat('en-CA', {
                        timeZone: '{{ $userTz }}',
                        hourCycle: 'h23',
                        year: 'numeric', month: '2-digit', day: '2-digit',
                        hour: '2-digit', minute: '2-digit', second: '2-digit',
                    }).formatToParts(new Date());
                    var p = {};
                    parts.forEach(function (part) { p[part.type] = part.value; });
                    return Date.UTC(p.year, p.month - 1, p.day, p.hour, p.minute, p.second);
                },
                initialView: 'timeGridWeek',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'timeGridWeek,dayGridMonth' },
                height: 'auto',
                eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
                views: {
                    dayGridMonth: { displayEventEnd: true },
                },
                nowIndicator: true,
                events: events,
                eventClick: function (info) {
                    var kind = info.event.extendedProps.kind;
                    if (kind === 'open') {
                        var instructorId = info.event.extendedProps.instructorId;
                        var options = hourlyStarts(info.event.start, info.event.end);
                        if (options.length <= 1) {
                            if (options.length === 1 && confirm('Book this slot (' + displayLocalTz(options[0]) + ')?')) {
                                submitBooking(options[0], instructorId);
                            }
                        } else {
                            showPickTimeModal(options, instructorId);
                        }
                    } else if (kind === 'booked') {
                        var when = displayLocalTz(info.event.start);
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
