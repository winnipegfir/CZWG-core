@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Instructors — Winnipeg FIR')

@section('content')
@include('includes.trainingMenu')

<div style="background:#f8fafc; min-height:calc(100vh - 112px); padding:2rem 0;">
<div class="container">

    <div class="d-flex align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold mb-0" style="color:#122b44;">Instructors</h2>
            <p class="text-muted mb-0" style="font-size:0.875rem;">{{ $instructors->count() }} active instructor{{ $instructors->count() != 1 ? 's' : '' }}</p>
        </div>
        @if(Auth::user()->permissions >= 4)
        <button type="button" class="btn btn-primary btn-sm ml-auto" data-toggle="modal" data-target="#addInstructorModal">
            <i class="fas fa-plus fa-xs mr-1"></i> Add Instructor
        </button>
        @endif
    </div>


    <div class="card" style="overflow:hidden;">
        @if($instructors->isEmpty())
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-chalkboard-teacher fa-2x mb-2" style="color:#cbd5e1;"></i>
                <p class="mb-0">No instructors have been added yet.</p>
            </div>
        @else
            @foreach($instructors as $instructor)
            <div style="display:flex; align-items:center; padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9;">
                <img src="{{ $instructor->user->avatar() }}" style="width:44px; height:44px; border-radius:50%; object-fit:cover; border:1px solid #e2e8f0; flex-shrink:0; margin-right:1rem;">
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:600; color:#122b44; font-size:0.9rem;">{{ $instructor->user->fullName('FLC') }}</div>
                    <div style="font-size:0.78rem; color:#64748b; margin-top:0.1rem;">
                        {{ $instructor->qualification ?? 'Instructor' }}
                        @if($instructor->email)
                            &nbsp;&middot;&nbsp;<a href="mailto:{{ $instructor->email }}" style="color:#64748b;">{{ $instructor->email }}</a>
                        @endif
                    </div>
                </div>
                <div class="mr-3 text-right" style="flex-shrink:0;">
                    @if($instructor->students->count() > 0)
                        <span style="background:#dbeafe; color:#1d4ed8; font-size:0.72rem; font-weight:700; padding:0.2em 0.6em; border-radius:0.3rem;">
                            {{ $instructor->students->count() }} student{{ $instructor->students->count() != 1 ? 's' : '' }}
                        </span>
                    @else
                        <span style="background:#f1f5f9; color:#94a3b8; font-size:0.72rem; font-weight:700; padding:0.2em 0.6em; border-radius:0.3rem;">
                            No students
                        </span>
                    @endif
                </div>
                @if($instructor->students->count() > 0)
                <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.78rem; flex-shrink:0;"
                        data-toggle="modal" data-target="#students{{ $instructor->id }}">
                    View
                </button>
                @endif
                @if(Auth::user()->permissions >= 4 && $instructor->students->count() == 0)
                <form method="POST" action="{{ route('training.instructors.remove', $instructor->id) }}"
                      onsubmit="return confirm('Remove {{ $instructor->user->fullName('FL') }} as an instructor?')" style="flex-shrink:0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.78rem;">Remove</button>
                </form>
                @endif
            </div>
            @endforeach
        @endif
    </div>

</div>
</div>

{{-- Student list modals --}}
@foreach($instructors as $instructor)
@if($instructor->students->count() > 0)
<div class="modal fade" id="students{{ $instructor->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:1px solid #e9ecef;">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">{{ $instructor->user->fullName('FL') }}'s Students</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0">
                @foreach($instructor->students as $s)
                <a href="{{ route('training.students.view', $s->id) }}"
                   style="display:flex; align-items:center; padding:0.75rem 1.25rem; border-bottom:1px solid #f1f5f9; text-decoration:none; color:#122b44;">
                    <img src="{{ $s->user->avatar() }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; margin-right:0.75rem; border:1px solid #e2e8f0;">
                    <div style="flex:1;">
                        <div style="font-weight:600; font-size:0.875rem;">{{ $s->user->fullName('FL') }}</div>
                        <div style="font-size:0.75rem; color:#64748b;">{{ $s->user->rating->getShortName() }}</div>
                    </div>
                    @if($s->instructor_id)
                        <span style="background:#dcfce7; color:#15803d; font-size:0.7rem; font-weight:700; padding:0.2em 0.5em; border-radius:0.3rem;">Linked</span>
                    @else
                        <span style="background:#fef3c7; color:#92400e; font-size:0.7rem; font-weight:700; padding:0.2em 0.5em; border-radius:0.3rem;">Waitlist</span>
                    @endif
                </a>
                @endforeach
            </div>
            <div class="modal-footer" style="border-top:1px solid #e9ecef;">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

{{-- Add Instructor Modal --}}
@if(Auth::user()->permissions >= 4)
<div class="modal fade" id="addInstructorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:1px solid #e9ecef;">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Add Instructor</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('training.instructors.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Controller</label>
                        <select name="cid" class="form-control">
                            @foreach($potentialinstructor as $i)
                                <option value="{{ $i->cid }}">{{ $i->cid }} &mdash; {{ $i->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Qualification</label>
                        <input type="text" name="qualification" class="form-control" placeholder="e.g. Assessor">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Public Email</label>
                        <input type="email" name="email" class="form-control" required>
                        <small class="text-muted">Visible to students — keeps their CERT email private.</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e9ecef;">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Add Instructor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@stop
