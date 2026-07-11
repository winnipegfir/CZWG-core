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
            <p class="text-muted mb-0" style="font-size:0.875rem;">{{ $slots->count() }} slot{{ $slots->count() != 1 ? 's' : '' }} posted</p>
        </div>
        <button type="button" class="btn btn-sm btn-primary ml-auto" data-toggle="modal" data-target="#addSlot">
            <i class="fas fa-plus fa-xs mr-1"></i> Add Slot
        </button>
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
                            <th style="color:#64748b; font-weight:600; border-top:none;">When</th>
                            <th style="color:#64748b; font-weight:600; border-top:none;">Type</th>
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
                                <td style="vertical-align:middle; color:#495057;">{{ $slot->type ?? '—' }}</td>
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
                                        <form method="POST" action="{{ route('training.sessions.cancel', $slot->id) }}" onsubmit="return confirm('Cancel this session?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.78rem;">Cancel</button>
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
            <form method="POST" action="{{ route('training.sessions.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Start</label>
                        <input type="datetime-local" name="start_time" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">End</label>
                        <input type="datetime-local" name="end_time" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Type (optional)</label>
                        <input type="text" name="type" class="form-control" placeholder="e.g. S1 Practical">
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

@stop
