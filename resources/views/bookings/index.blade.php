@extends('layouts.master')
@section('title', 'ATC Bookings — Winnipeg FIR')
@section('description', 'Upcoming ATC position bookings for the Winnipeg FIR on VATSIM.')

@section('content')
<div style="background:#f6f8fa; padding:2.5rem 0 3rem;">
<div class="container">

    <div class="d-flex align-items-center mb-4">
        <div>
            <h1 class="font-weight-bold mb-0" style="color:#122b44; font-size:2rem;">ATC Bookings</h1>
            <p class="text-muted mb-0" style="font-size:0.875rem;">Upcoming Winnipeg FIR controller bookings. All times in UTC.</p>
        </div>
        @if(Auth::check() && Auth::user()->permissions >= 1)
        <button class="btn btn-primary btn-sm ml-auto" data-toggle="modal" data-target="#newBookingModal">
            <i class="fas fa-plus fa-xs mr-1"></i> New Booking
        </button>
        @endif
    </div>

    {{-- Your upcoming bookings --}}
    @if(Auth::check() && $myBookings->isNotEmpty())
    <div class="card mb-4" style="border-left:4px solid #6366f1;">
        <div class="card-body py-3">
            <p class="mb-2" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6366f1;">Your Upcoming Bookings</p>
            @foreach($myBookings as $b)
            @php
                $start = \Carbon\Carbon::parse($b['start']);
                $end   = \Carbon\Carbon::parse($b['end']);
            @endphp
            <div class="d-flex align-items-center py-2" style="border-bottom:1px solid #f1f5f9; gap:1rem;">
                <span style="font-weight:700; color:#122b44; font-size:0.9rem; min-width:140px;">{{ $b['callsign'] }}</span>
                <span style="font-size:0.85rem; color:#495057;">
                    {{ $start->format('M j') }} &middot; {{ $start->format('H:i') }}z &ndash; {{ $end->format('H:i') }}z
                </span>
                <div class="ml-auto d-flex" style="gap:0.4rem;">
                    <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.78rem;"
                        data-toggle="modal" data-target="#editBooking{{ $b['id'] }}">Edit</button>
                    <form method="POST" action="{{ route('bookings.destroy', $b['id']) }}"
                          onsubmit="return confirm('Cancel this booking?')" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.78rem;">Cancel</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- All bookings --}}
    <div class="card">
        @if($bookings->isEmpty())
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-calendar fa-2x mb-2" style="color:#cbd5e1;"></i>
                <p class="mb-0">No upcoming bookings.</p>
            </div>
        @else
        @php $currentDate = null; @endphp
        <div class="table-responsive">
            <table class="table mb-0" style="font-size:0.875rem;">
                <thead style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                    <tr>
                        <th style="color:#64748b; font-weight:600; border-top:none;">Callsign</th>
                        <th style="color:#64748b; font-weight:600; border-top:none;">Controller</th>
                        <th style="color:#64748b; font-weight:600; border-top:none;">Date</th>
                        <th style="color:#64748b; font-weight:600; border-top:none;">Start</th>
                        <th style="color:#64748b; font-weight:600; border-top:none;">End</th>
                        <th style="color:#64748b; font-weight:600; border-top:none;">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $b)
                    @php
                        $start    = \Carbon\Carbon::parse($b['start']);
                        $end      = \Carbon\Carbon::parse($b['end']);
                        $mins     = $start->diffInMinutes($end);
                        $duration = floor($mins / 60) . 'h ' . ($mins % 60) . 'm';
                        $isMe     = Auth::check() && Auth::id() == $b['cid'];
                        $controller = \App\Models\Users\User::find($b['cid']);
                    @endphp
                    <tr style="{{ $isMe ? 'background:#f0f4ff;' : '' }}">
                        <td style="font-weight:700; color:#122b44; vertical-align:middle;">
                            {{ $b['callsign'] }}
                            @if($isMe)
                                <span style="background:#e0e7ff; color:#4338ca; font-size:0.65rem; font-weight:700; padding:0.1em 0.4em; border-radius:0.25rem; margin-left:0.25rem;">You</span>
                            @endif
                        </td>
                        <td style="vertical-align:middle; color:#495057;">
                            {{ $controller ? $controller->fullName('FL') : 'CID ' . $b['cid'] }}
                        </td>
                        <td style="vertical-align:middle; color:#495057;">{{ $start->format('M j, Y') }}</td>
                        <td style="vertical-align:middle; color:#495057;">{{ $start->format('H:i') }}z</td>
                        <td style="vertical-align:middle; color:#495057;">{{ $end->format('H:i') }}z</td>
                        <td style="vertical-align:middle; color:#94a3b8; font-size:0.8rem;">{{ $duration }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
</div>

{{-- New Booking Modal --}}
@if(Auth::check() && Auth::user()->permissions >= 1)
<div class="modal fade" id="newBookingModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">New Booking</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('bookings.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Callsign</label>
                        <div class="d-flex align-items-center" style="gap:0.5rem;">
                            <input type="text" name="airspace" class="form-control" placeholder="CYWG" style="text-transform:uppercase;" maxlength="10" required>
                            <span class="font-weight-bold text-muted">_</span>
                            <input type="text" name="position" class="form-control" placeholder="TWR" style="text-transform:uppercase;" maxlength="10" required>
                        </div>
                        <small class="text-muted">e.g. CYWG + TWR → CYWG_TWR</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Start (UTC)</label>
                        <input type="datetime-local" name="start" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">End (UTC)</label>
                        <input type="datetime-local" name="end" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Edit Booking Modals --}}
@foreach($myBookings as $b)
<div class="modal fade" id="editBooking{{ $b['id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Edit Booking — {{ $b['callsign'] }}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('bookings.update', $b['id']) }}">
                @csrf @method('PUT')
                @php
                    $parts = explode('_', $b['callsign'], 2);
                    $airspace = $parts[0] ?? '';
                    $position = $parts[1] ?? '';
                @endphp
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Callsign</label>
                        <div class="d-flex align-items-center" style="gap:0.5rem;">
                            <input type="text" name="airspace" class="form-control" value="{{ $airspace }}" style="text-transform:uppercase;" maxlength="10" required>
                            <span class="font-weight-bold text-muted">_</span>
                            <input type="text" name="position" class="form-control" value="{{ $position }}" style="text-transform:uppercase;" maxlength="10" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Start (UTC)</label>
                        <input type="datetime-local" name="start" class="form-control"
                            value="{{ \Carbon\Carbon::parse($b['start'])->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">End (UTC)</label>
                        <input type="datetime-local" name="end" class="form-control"
                            value="{{ \Carbon\Carbon::parse($b['end'])->format('Y-m-d\TH:i') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
// Force uppercase on callsign inputs
document.querySelectorAll('input[name="airspace"], input[name="position"]').forEach(function(el) {
    el.addEventListener('input', function() { this.value = this.value.toUpperCase(); });
});
</script>
@endsection
