@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
@include('includes.trainingMenu')

<style>
.wl-badge {
    display: inline-block;
    padding: 0.2em 0.55em;
    border-radius: 0.3rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.wl-badge-home    { background:#dbeafe; color:#1d4ed8; }
.wl-badge-visit   { background:#e0f2fe; color:#0369a1; }
.wl-badge-transfer { background:#f3e8ff; color:#7e22ce; }
</style>

<div class="container" style="margin-top:1.5rem; margin-bottom:3rem;">

    <div class="d-flex align-items-center mb-1">
        <h1 class="font-weight-bold blue-text mb-0">Waitlist</h1>
        @if(Auth::user()->permissions >= 4)
        <button type="button" class="btn btn-sm btn-primary ml-auto" data-toggle="modal" data-target="#newStudent">
            <i class="fas fa-plus fa-xs mr-1"></i> Add to Waitlist
        </button>
        @endif
    </div>
    <p class="text-muted mb-3" style="font-size:0.875rem;">
        {{ $students->count() }} student{{ $students->count() != 1 ? 's' : '' }} waiting &mdash; ordered by date added.
    </p>


    @if($students->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-check-circle fa-2x mb-2" style="color:#86efac;"></i>
                <p class="mb-0">The waitlist is empty.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive" style="overflow:visible;">
                <table class="table table-hover mb-0" style="font-size:0.875rem;">
                    <thead style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                        <tr>
                            <th style="width:42px; color:#64748b; font-weight:600;">#</th>
                            <th style="color:#64748b; font-weight:600;">Name</th>
                            <th style="color:#64748b; font-weight:600;">Type</th>
                            <th style="color:#64748b; font-weight:600;">Added</th>
                            <th style="color:#64748b; font-weight:600;">Waiting</th>
                            <th style="min-width:200px; color:#64748b; font-weight:600;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        <tr>
                            <td class="text-muted font-weight-bold">{{ $i + 1 }}</td>
                            <td>
                                <a href="{{ route('training.students.view', $student->id) }}" style="color:#122b44; font-weight:600;">
                                    {{ $student->user->fullName('FL') }}
                                </a>
                                <br>
                                <span class="text-muted" style="font-size:0.78rem;">{{ $student->user->id }}</span>
                            </td>
                            <td>
                                @if($student->entry_type == 'New Student')
                                    <span class="wl-badge wl-badge-home">Home</span>
                                @elseif($student->entry_type == 'New Visitor')
                                    <span class="wl-badge wl-badge-visit">Visiting</span>
                                @elseif($student->entry_type == 'New Transfer')
                                    <span class="wl-badge wl-badge-transfer">Transfer</span>
                                @else
                                    <span class="wl-badge" style="background:#f1f5f9;color:#64748b;">{{ $student->entry_type }}</span>
                                @endif
                            </td>
                            <td style="color:#495057;">
                                @if($student->waitlist_added_at)
                                    {{ $student->waitlist_added_at->format('M j, Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="color:#495057;">
                                @if($student->waitlist_added_at)
                                    {{ $student->waitlist_added_at->diffForHumans(null, true) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle;">
                                @if(Auth::user()->permissions >= 4)
                                <form method="POST" action="{{ route('training.students.activate', $student->id) }}" class="d-flex align-items-center mb-1" style="gap:0.4rem;">
                                    @csrf
                                    <select name="instructor" class="form-control form-control-sm" style="font-size:0.8rem; max-width:160px; color:#495057; background:#fff; padding:.25rem .5rem;">
                                        <option value="unassign">No instructor</option>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}"
                                                @if($student->instructor_id == $instructor->id) selected @endif>
                                                {{ $instructor->user->fullName('FL') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-success" style="white-space:nowrap; font-size:0.8rem;">Start</button>
                                </form>
                                <div class="d-flex" style="gap:0.35rem;">
                                    <a href="{{ route('training.students.view', $student->id) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.78rem;">View</a>
                                    <form method="POST" action="{{ route('training.students.remove', $student->id) }}" onsubmit="return confirm('Remove {{ $student->user->fullName('FL') }} from the training system?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.78rem;">Remove</button>
                                    </form>
                                </div>
                                @else
                                <a href="{{ route('training.students.view', $student->id) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.78rem;">View</a>
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

{{-- Add to Waitlist Modal (staff only) --}}
@if(Auth::user()->permissions >= 4)
<div class="modal fade" id="newStudent" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Add Student to Waitlist</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('instructor.student.add.new') }}">
                @csrf
                <div class="modal-body">
                    <p class="text-muted" style="font-size:0.825rem;">This will create and approve an application for the student using your CID.</p>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Student</label>
                        <select name="student_id" class="js-example-basic-single form-control">
                            @foreach($potentialstudent as $u)
                                <option value="{{ $u->id }}">{{ $u->id }} &mdash; {{ $u->fullName('FL') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Entry Type</label>
                        <select name="entry_type" class="form-control">
                            <option value="New Student">New Student (Home)</option>
                            <option value="New Visitor">New Visitor</option>
                            <option value="New Transfer">New Transfer</option>
                        </select>
                    </div>
                    <input type="hidden" name="instructor" value="unassign">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to Waitlist</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.js-example-basic-single').select2({ dropdownParent: $('#newStudent') });
    });
</script>

@stop
