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
.ts-badge-booked    { background:#dcfce7; color:#15803d; }
.ts-badge-cancelled { background:#fee2e2; color:#b91c1c; }
.ts-reassign select { font-size:0.8rem; padding:0.15rem 0.4rem; height:auto; }
</style>

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container">

    <div class="mb-4">
        <h2 class="font-weight-bold mb-0" style="color:#122b44;">All Sessions</h2>
        <p class="text-muted mb-0" style="font-size:0.875rem;">
            {{ $sessions->count() }} session{{ $sessions->count() != 1 ? 's' : '' }} across every instructor
        </p>
    </div>

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
                            <th style="color:#64748b; font-weight:600; border-top:none;">When</th>
                            <th style="color:#64748b; font-weight:600; border-top:none;">Type</th>
                            <th style="color:#64748b; font-weight:600; border-top:none;">Status</th>
                            <th style="color:#64748b; font-weight:600; border-top:none; min-width:280px;">Instructor / Student</th>
                            <th style="border-top:none;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sessions as $session)
                            <tr>
                                <td style="vertical-align:middle;">
                                    <span style="color:#122b44; font-weight:600;">{{ $session->start_time->format('D, M j') }}</span>
                                    <br>
                                    <span class="text-muted" style="font-size:0.78rem;">{{ $session->start_time->format('g:i A') }} &ndash; {{ $session->end_time->format('g:i A') }}</span>
                                </td>
                                <td style="vertical-align:middle; color:#495057;">{{ $session->type ?? '—' }}</td>
                                <td style="vertical-align:middle;">
                                    @if ($session->status === 'booked')
                                        <span class="ts-badge ts-badge-booked">Booked</span>
                                    @elseif ($session->status === 'open')
                                        <span class="ts-badge ts-badge-open">Open</span>
                                    @else
                                        <span class="ts-badge ts-badge-cancelled">Cancelled</span>
                                    @endif
                                </td>
                                <td style="vertical-align:middle;">
                                    <form method="POST" action="{{ route('training.sessions.admin.reassign', $session->id) }}" class="ts-reassign d-flex align-items-center flex-wrap" style="gap:0.3rem;">
                                        @csrf
                                        <select name="instructor_id" class="form-control">
                                            @foreach ($instructors as $instructor)
                                                <option value="{{ $instructor->id }}" @selected($session->instructor_id === $instructor->id)>
                                                    {{ $instructor->user ? $instructor->user->fullName('FL') : $instructor->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <select name="student_id" class="form-control">
                                            <option value="">Unbooked</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}" @selected($session->student_id === $student->id)>
                                                    {{ $student->user ? $student->user->fullName('FL') : 'CID ' . $student->user_id }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:0.78rem;">Save</button>
                                    </form>
                                </td>
                                <td style="vertical-align:middle;">
                                    <div class="d-flex align-items-center" style="gap:0.35rem;">
                                        @if ($session->status !== 'cancelled')
                                            <form method="POST" action="{{ route('training.sessions.admin.cancel', $session->id) }}" onsubmit="return confirm('Cancel this session?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.78rem;">Cancel</button>
                                            </form>
                                        @endif
                                        @if ($session->status === 'open')
                                            <form method="POST" action="{{ route('training.sessions.admin.destroy', $session->id) }}" onsubmit="return confirm('Delete this slot?')">
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
    @endif

</div>
</div>
@stop
