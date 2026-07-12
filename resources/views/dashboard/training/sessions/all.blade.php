@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'All Sessions — Winnipeg FIR')

@section('content')
@include('includes.trainingMenu')

<style>
.ts-badge {
    display: inline-block;
    padding: 0.2em 0.55em;
    border-radius: 0.3rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.ts-badge-open      { background:#f1f5f9; color:#64748b; }
.ts-badge-pending   { background:#fef3c7; color:#92400e; }
.ts-badge-booked    { background:#dcfce7; color:#15803d; }
.ts-badge-cancelled { background:#fee2e2; color:#b91c1c; }
.ts-reassign select { font-size:0.8rem; padding:0.15rem 0.4rem; height:auto; }
.ts-sortable:hover { color:#122b44 !important; }
.ts-sort-arrow { font-size:0.7rem; opacity:0.5; }
</style>

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container">

    <div class="mb-4">
        <h2 class="font-weight-bold mb-0" style="color:#122b44;">All Sessions</h2>
        <p class="text-muted mb-0" style="font-size:0.875rem;">
            {{ $sessions->count() }} session{{ $sessions->count() != 1 ? 's' : '' }} across every instructor &mdash; click a session on the calendar to jump to it below &mdash; times shown in {{ \App\Models\Users\User::timezoneLabel($userTz) }}
            @if($userTz === 'UTC')
                &mdash; <a href="{{ route('me.preferences') }}">set your timezone</a>
            @endif
        </p>
    </div>

    @if (!$sessions->isEmpty())
        <div class="card mb-4">
            <div class="card-body">
                <div id="allSessionsCalendar"></div>
            </div>
        </div>
    @endif

    @if ($sessions->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-calendar fa-2x mb-2" style="color:#cbd5e1;"></i>
                <p class="mb-0">No sessions have been created yet.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.875rem;">
                    <thead style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                        <tr>
                            <th class="ts-sortable" data-sort="when" style="color:#64748b; font-weight:600; border-top:none; cursor:pointer; user-select:none;">When ({{ \App\Models\Users\User::timezoneLabel($userTz) }}) <span class="ts-sort-arrow"></span></th>
                            <th class="ts-sortable" data-sort="status" style="color:#64748b; font-weight:600; border-top:none; cursor:pointer; user-select:none;">Status <span class="ts-sort-arrow"></span></th>
                            <th class="ts-sortable" data-sort="who" style="color:#64748b; font-weight:600; border-top:none; min-width:280px; cursor:pointer; user-select:none;">Instructor / Student <span class="ts-sort-arrow"></span></th>
                            <th style="border-top:none;"></th>
                        </tr>
                    </thead>
                    <tbody id="sessionsTableBody">
                        @foreach ($sessions as $session)
                            <tr id="session-row-{{ $session->id }}" style="transition:background-color 0.6s ease;"
                                data-when="{{ $session->start_time->timestamp }}"
                                data-status="{{ $session->status }}"
                                data-who="{{ strtolower(($session->instructor && $session->instructor->user ? $session->instructor->user->fullName('FL') : 'zzz') . ' ' . ($session->student && $session->student->user ? $session->student->user->fullName('FL') : '')) }}">
                                <td style="vertical-align:middle;">
                                    @php $startLocal = $session->start_time->copy()->setTimezone($userTz); $endLocal = $session->end_time->copy()->setTimezone($userTz); @endphp
                                    <span style="color:#122b44; font-weight:600;">{{ $startLocal->format('D, M j') }}</span>
                                    <br>
                                    <span class="text-muted" style="font-size:0.78rem;">{{ $startLocal->format('g:i A') }} &ndash; {{ $endLocal->format('g:i A') }}</span>
                                </td>
                                <td style="vertical-align:middle;">
                                    @if ($session->status === 'booked')
                                        <span class="ts-badge ts-badge-booked">Booked</span>
                                    @elseif ($session->status === 'pending')
                                        <span class="ts-badge ts-badge-pending">Pending</span>
                                    @elseif ($session->status === 'open')
                                        <span class="ts-badge ts-badge-open">Open</span>
                                    @else
                                        <span class="ts-badge ts-badge-cancelled">Cancelled</span>
                                    @endif
                                </td>
                                <td style="vertical-align:middle;">
                                    <span style="color:#122b44; font-weight:600;">{{ $session->instructor && $session->instructor->user ? $session->instructor->user->fullName('FL') : ($session->instructor ? $session->instructor->email : 'Unassigned') }}</span>
                                    @if ($session->student)
                                        <br><span class="text-muted" style="font-size:0.78rem;">{{ $session->student->user ? $session->student->user->fullName('FL') : 'Unknown' }}</span>
                                    @endif
                                </td>
                                <td style="vertical-align:middle;">
                                    <div class="d-flex align-items-center" style="gap:0.35rem;">
                                        <a href="#" data-toggle="modal" data-target="#editSession{{ $session->id }}"
                                           class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.78rem;">Edit</a>
                                        @if ($session->status === 'booked' || $session->status === 'pending')
                                            <form method="POST" action="{{ route('training.sessions.admin.cancel', $session->id) }}" onsubmit="return confirm('Cancel this session? The booking record will be kept as cancelled.')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning py-0 px-2" style="font-size:0.78rem;">Cancel</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('training.sessions.admin.destroy', $session->id) }}" onsubmit="return confirm('Delete this slot permanently?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.78rem;">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @foreach ($sessions as $session)
            {{-- Edit Session modal --}}
            <div class="modal fade" id="editSession{{ $session->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold" style="color:#122b44;">Edit Session</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('training.sessions.admin.update', $session->id) }}" class="mb-4 pb-4" style="border-bottom:1px solid #e9ecef;">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small">Start <span class="text-muted font-weight-normal">({{ \App\Models\Users\User::timezoneLabel($userTz) }})</span></label>
                                    <input type="datetime-local" name="start_time" class="form-control" value="{{ $session->start_time->copy()->setTimezone($userTz)->format('Y-m-d\TH:i') }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small">End <span class="text-muted font-weight-normal">({{ \App\Models\Users\User::timezoneLabel($userTz) }})</span></label>
                                    <input type="datetime-local" name="end_time" class="form-control" value="{{ $session->end_time->copy()->setTimezone($userTz)->format('Y-m-d\TH:i') }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small">Note (optional)</label>
                                    <input type="text" name="note" class="form-control" value="{{ $session->note }}" placeholder="e.g. S1 Practical">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Save Times</button>
                            </form>

                            <form method="POST" action="{{ route('training.sessions.admin.reassign', $session->id) }}" class="ts-reassign mb-0">
                                @csrf
                                <label class="font-weight-bold small d-block mb-2">Instructor / Student</label>
                                <div class="form-group mb-2">
                                    <select name="instructor_id" class="form-control">
                                        @foreach ($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" @selected($session->instructor_id === $instructor->id)>
                                                {{ $instructor->user_id }} &ndash; {{ $instructor->user ? $instructor->user->fullName('FL') : $instructor->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <select name="student_id" class="form-control">
                                        <option value="">Unbooked</option>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}" data-instructor="{{ $student->instructor_id }}" @selected($session->student_id === $student->id)>
                                                {{ $student->user_id }} &ndash; {{ $student->user ? $student->user->fullName('FL') : 'Unknown' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($session->status === 'pending')
                                    <div class="text-muted mb-2" style="font-size:0.75rem;">Saving confirms this pending session.</div>
                                @endif
                                <button type="submit" class="btn btn-outline-primary btn-sm">Save Assignment</button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

</div>
</div>

@if (!$sessions->isEmpty())
    @php
        $statusColors = ['open' => '#64748b', 'pending' => '#d97706', 'booked' => '#16a34a', 'cancelled' => '#b91c1c'];
        $calendarEvents = [];
        foreach ($sessions as $session) {
            $who = $session->instructor && $session->instructor->user ? $session->instructor->user->fullName('FL') : 'Unknown';
            if ($session->student && $session->student->user) {
                $who .= ' / ' . $session->student->user->fullName('FL');
            }
            $color = $statusColors[$session->status] ?? '#64748b';
            $calendarEvents[] = [
                'id' => $session->id,
                'title' => ucfirst($session->status) . ' — ' . $who,
                'start' => $session->start_time->copy()->setTimezone($userTz)->format('Y-m-d\TH:i:s'),
                'end' => $session->end_time->copy()->setTimezone($userTz)->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => ['status' => $session->status],
            ];
        }
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var events = {!! json_encode($calendarEvents) !!};

        var calendarEl = document.getElementById('allSessionsCalendar');
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
                var row = document.getElementById('session-row-' + info.event.id);
                if (row) {
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    row.style.backgroundColor = '#fef3c7';
                    setTimeout(function () { row.style.backgroundColor = ''; }, 1500);
                }
            },
        });
        calendar.render();
    });
    </script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.ts-reassign').forEach(function (form) {
        var instructorSelect = form.querySelector('select[name="instructor_id"]');
        var studentSelect = form.querySelector('select[name="student_id"]');
        var initialStudentValue = studentSelect.value;

        function filterStudents(resetSelection) {
            var instructorId = instructorSelect.value;
            Array.prototype.forEach.call(studentSelect.options, function (opt) {
                if (opt.value === '') {
                    opt.hidden = false;
                    return;
                }
                var matches = opt.getAttribute('data-instructor') === instructorId;
                var isUnchangedSelection = !resetSelection && opt.value === initialStudentValue;
                opt.hidden = !matches && !isUnchangedSelection;
            });
            if (resetSelection && studentSelect.selectedOptions[0] && studentSelect.selectedOptions[0].hidden) {
                studentSelect.value = '';
            }
        }

        filterStudents(false);
        instructorSelect.addEventListener('change', function () { filterStudents(true); });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var statusOrder = { open: 0, pending: 1, booked: 2, cancelled: 3 };
    var tbody = document.getElementById('sessionsTableBody');
    var currentSort = { key: 'when', dir: 'desc' };

    function sortRows(key, dir) {
        var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
        rows.sort(function (a, b) {
            var av = a.getAttribute('data-' + key);
            var bv = b.getAttribute('data-' + key);
            if (key === 'when') {
                av = parseInt(av, 10);
                bv = parseInt(bv, 10);
            } else if (key === 'status') {
                av = statusOrder[av] !== undefined ? statusOrder[av] : 99;
                bv = statusOrder[bv] !== undefined ? statusOrder[bv] : 99;
            }
            if (av < bv) return dir === 'asc' ? -1 : 1;
            if (av > bv) return dir === 'asc' ? 1 : -1;
            return 0;
        });
        rows.forEach(function (row) { tbody.appendChild(row); });
    }

    document.querySelectorAll('.ts-sortable').forEach(function (th) {
        th.addEventListener('click', function () {
            var key = th.getAttribute('data-sort');
            var dir = (currentSort.key === key && currentSort.dir === 'asc') ? 'desc' : 'asc';
            currentSort = { key: key, dir: dir };

            document.querySelectorAll('.ts-sort-arrow').forEach(function (arrow) { arrow.textContent = ''; });
            th.querySelector('.ts-sort-arrow').textContent = dir === 'asc' ? '▲' : '▼';

            sortRows(key, dir);
        });
    });
});
</script>

@stop
