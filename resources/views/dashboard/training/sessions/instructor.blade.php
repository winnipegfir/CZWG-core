@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Training Slots — Winnipeg FIR')

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
.ts-badge-booked    { background:#dcfce7; color:#15803d; }
.ts-badge-cancelled { background:#fee2e2; color:#b91c1c; }
</style>

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container">

    <div class="d-flex align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold mb-0" style="color:#122b44;">Training Slots</h2>
            <p class="text-muted mb-0" style="font-size:0.875rem;">{{ $slots->count() }} slot{{ $slots->count() != 1 ? 's' : '' }} posted &mdash; click an empty time to add a slot, click a slot to manage it &mdash; all times Zulu (UTC)</p>
        </div>
        <button type="button" class="btn btn-sm btn-primary ml-auto" data-toggle="modal" data-target="#addSlot">
            <i class="fas fa-plus fa-xs mr-1"></i> Add Slot
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div id="slotsCalendar"></div>
        </div>
    </div>

    @if($slots->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-calendar-plus fa-2x mb-2" style="color:#cbd5e1;"></i>
                <p class="mb-0">You haven't posted any slots yet.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.875rem;">
                    <thead style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                        <tr>
                            <th style="color:#64748b; font-weight:600; border-top:none;">When (Zulu)</th>
                            <th style="color:#64748b; font-weight:600; border-top:none;">Note</th>
                            <th style="color:#64748b; font-weight:600; border-top:none;">Status</th>
                            <th style="color:#64748b; font-weight:600; border-top:none;">Student</th>
                            <th style="width:100px; border-top:none;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($slots as $slot)
                            <tr>
                                <td style="vertical-align:middle;">
                                    <span style="color:#122b44; font-weight:600;">{{ $slot->start_time->format('D, M j') }}</span>
                                    <br>
                                    <span class="text-muted" style="font-size:0.78rem;">{{ $slot->start_time->format('g:i A') }} &ndash; {{ $slot->end_time->format('g:i A') }}</span>
                                </td>
                                <td style="vertical-align:middle; color:#495057;">{{ $slot->note ?? '—' }}</td>
                                <td style="vertical-align:middle;">
                                    @if($slot->status === 'booked')
                                        <span class="ts-badge ts-badge-booked">Booked</span>
                                    @else
                                        <span class="ts-badge ts-badge-open">Open</span>
                                    @endif
                                </td>
                                <td style="vertical-align:middle; color:#495057;">
                                    {{ $slot->student && $slot->student->user ? $slot->student->user->fullName('FL') : '—' }}
                                </td>
                                <td style="vertical-align:middle;">
                                    @if ($slot->status === 'open')
                                        <form method="POST" action="{{ route('training.sessions.destroy', $slot->id) }}" onsubmit="return confirm('Remove this slot?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.78rem;">Remove</button>
                                        </form>
                                    @elseif ($slot->status === 'booked')
                                        <form method="POST" action="{{ route('training.sessions.cancel', $slot->id) }}" onsubmit="return confirm('Cancel this session? The booking record will be kept as cancelled.')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning py-0 px-2" style="font-size:0.78rem;">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
</div>

{{-- Add Slot Modal --}}
<div class="modal fade" id="addSlot" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Add a Training Slot</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('training.sessions.store') }}" id="addSlotForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Start <span class="text-muted font-weight-normal">(Zulu / UTC)</span></label>
                        <input type="datetime-local" name="start_time" id="addSlotStart" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">End <span class="text-muted font-weight-normal">(Zulu / UTC)</span></label>
                        <input type="datetime-local" name="end_time" id="addSlotEnd" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Note (optional)</label>
                        <input type="text" name="note" class="form-control" placeholder="e.g. S1 Practical">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden forms used by the calendar's click actions --}}
<form id="calRemoveForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>
<form id="calCancelForm" method="POST" style="display:none;">@csrf</form>

@php
    $calendarEvents = [];
    foreach ($slots as $slot) {
        $studentName = $slot->student && $slot->student->user ? $slot->student->user->fullName('FL') : null;
        $title = ($slot->status === 'booked' ? ($studentName ?: 'Booked') : 'Open');
        if ($slot->note) {
            $title .= ' — ' . $slot->note;
        }
        $color = $slot->status === 'booked' ? '#16a34a' : '#64748b';
        $calendarEvents[] = [
            'id' => $slot->id,
            'title' => $title,
            'start' => $slot->start_time->toIso8601String(),
            'end' => $slot->end_time->toIso8601String(),
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => [
                'status' => $slot->status,
                'student' => $studentName,
            ],
        ];
    }
@endphp

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var events = {!! json_encode($calendarEvents) !!};

    var removeUrlTemplate = "{{ route('training.sessions.destroy', ['id' => '__ID__']) }}";
    var cancelUrlTemplate = "{{ route('training.sessions.cancel', ['id' => '__ID__']) }}";

    function pad(n) { return String(n).padStart(2, '0'); }
    function formatZulu(d) {
        return d.getUTCFullYear() + '-' + pad(d.getUTCMonth() + 1) + '-' + pad(d.getUTCDate()) + 'T' + pad(d.getUTCHours()) + ':' + pad(d.getUTCMinutes());
    }
    function displayZulu(d) {
        return formatZulu(d).replace('T', ' ') + 'Z';
    }

    var calendarEl = document.getElementById('slotsCalendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'UTC',
        initialView: 'timeGridWeek',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'timeGridWeek,dayGridMonth' },
        height: 'auto',
        eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
        views: {
            dayGridMonth: { displayEventEnd: true },
        },
        nowIndicator: true,
        events: events,
        dateClick: function (info) {
            var start = info.date;
            var end = new Date(start.getTime() + 3600000);
            document.getElementById('addSlotStart').value = formatZulu(start);
            document.getElementById('addSlotEnd').value = formatZulu(end);
            $('#addSlot').modal('show');
        },
        eventClick: function (info) {
            var status = info.event.extendedProps.status;
            var when = displayZulu(info.event.start);
            if (status === 'open') {
                if (confirm('Remove this open slot (' + when + ')?')) {
                    document.getElementById('calRemoveForm').action = removeUrlTemplate.replace('__ID__', info.event.id);
                    document.getElementById('calRemoveForm').submit();
                }
            } else if (status === 'booked') {
                var student = info.event.extendedProps.student || 'this student';
                if (confirm('Cancel the session with ' + student + ' (' + when + ')?')) {
                    document.getElementById('calCancelForm').action = cancelUrlTemplate.replace('__ID__', info.event.id);
                    document.getElementById('calCancelForm').submit();
                }
            }
        },
    });
    calendar.render();
});
</script>

@stop
