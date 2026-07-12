@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', ($student->user ? $student->user->fullName('FL') : 'CID ' . $student->user_id) . ' — Training')

@section('content')
@include('includes.trainingMenu')

<div style="background:#f8fafc; min-height:calc(100vh - 112px); padding:2rem 0;">
<div class="container">


    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <img src="{{ $student->user ? $student->user->avatar() : asset('img/default-profile-img.jpg') }}" style="width:56px; height:56px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0; margin-right:1rem; flex-shrink:0;">
        <div>
            <h2 class="font-weight-bold mb-0" style="color:#122b44; line-height:1.2;">{{ $student->user ? $student->user->fullName('FLC') : 'CID ' . $student->user_id }}</h2>
            <p class="mb-0 text-muted" style="font-size:0.875rem;">
                {{ $student->user ? $student->user->rating->getLongName() . ' (' . $student->user->rating->getShortName() . ')' : 'Rating unknown' }}
                &nbsp;&middot;&nbsp;
                @if($student->entry_type == 'New Student') Home Student
                @elseif($student->entry_type == 'New Visitor') Visiting Controller
                @elseif($student->entry_type == 'New Transfer') Transfer Controller
                @else {{ $student->entry_type }}
                @endif
            </p>
        </div>
        <div class="ml-auto text-right">
            @if($student->instructor_id)
                <span style="background:#dcfce7; color:#15803d; font-size:0.8rem; font-weight:700; padding:0.3em 0.75em; border-radius:0.4rem;">Linked</span>
            @else
                <span style="background:#fef3c7; color:#92400e; font-size:0.8rem; font-weight:700; padding:0.3em 0.75em; border-radius:0.4rem;">Waitlisted</span>
            @endif
            @if($student->mentorable)
                <span class="d-block mt-1" style="background:#dbeafe; color:#1d4ed8; font-size:0.75rem; font-weight:700; padding:0.25em 0.6em; border-radius:0.4rem;">Mentorable</span>
            @endif
        </div>
    </div>

    <div class="row">

        {{-- LEFT: Notes + Solo --}}
        <div class="col-md-8">

            {{-- Training Notes (VATCAN) --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h5 class="font-weight-bold mb-0" style="color:#122b44;">Training Notes</h5>
                    </div>
                    @if($notesError)
                        <p class="text-muted mb-0" style="font-size:0.875rem;"><i class="fas fa-exclamation-circle text-warning mr-1"></i>{{ $notesError }}</p>
                    @elseif(empty($notes))
                        <p class="text-muted mb-0" style="font-size:0.875rem;">No training notes yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size:0.85rem;">
                                <thead style="background:#f8fafc;">
                                    <tr>
                                        <th style="color:#64748b; font-weight:600; border-top:none;">Position</th>
                                        <th style="color:#64748b; font-weight:600; border-top:none;">Instructor</th>
                                        <th style="color:#64748b; font-weight:600; border-top:none;">Date</th>
                                        <th style="color:#64748b; font-weight:600; border-top:none;">Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notes as $note)
                                    <tr>
                                        <td style="color:#122b44; font-weight:600;">
                                            {{ $note['position_trained'] ?? '—' }}
                                            @if(isset($note['ots_pass']) && $note['ots_pass'])
                                                <span style="background:#dcfce7; color:#15803d; font-size:0.68rem; font-weight:700; padding:0.1em 0.4em; border-radius:0.25rem; margin-left:0.3rem;">OTS Pass</span>
                                            @endif
                                        </td>
                                        <td style="color:#495057;">{{ $note['instructor_name'] ?? '—' }}</td>
                                        <td style="color:#94a3b8;">{{ $note['friendly_time'] ?? '—' }}</td>
                                        <td style="color:#495057; font-size:0.8rem; max-width:300px;">{{ $note['training_note'] ?? '—' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>



        </div>

        {{-- RIGHT: Info + Actions --}}
        <div class="col-md-4">

            {{-- Primary Info --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-3" style="color:#122b44;">Details</h5>

                    @if(Auth::user()->permissions >= 4)
                    <p class="mb-1" style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#94a3b8;">Entry Type</p>
                    <form action="{{ route('training.students.entrytype', $student->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="d-flex" style="gap:0.5rem;">
                            <select name="entry_type" class="form-control form-control-sm flex-grow-1" style="font-size:0.8rem; color:#495057; background:#fff; padding:.25rem .5rem;">
                                <option value="New Student" {{ $student->entry_type == 'New Student' ? 'selected' : '' }}>Home Student</option>
                                <option value="New Visitor" {{ $student->entry_type == 'New Visitor' ? 'selected' : '' }}>Visiting Controller</option>
                                <option value="New Transfer" {{ $student->entry_type == 'New Transfer' ? 'selected' : '' }}>Transfer Controller</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-success flex-shrink-0" style="font-size:0.8rem;">Save</button>
                        </div>
                    </form>
                    @endif

                    <p class="mb-1" style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#94a3b8;">Instructor</p>
                    <p class="mb-3" style="font-size:0.875rem; color:#122b44;">
                        @if($vatcanUserError)
                            <span class="text-muted" style="font-size:0.8rem;"><i class="fas fa-exclamation-circle text-warning mr-1"></i>{{ $vatcanUserError }}</span>
                        @elseif($vatcanUser && !empty($vatcanUser['instructor']))
                            @php $instructorUser = \App\Models\Users\User::find($vatcanUser['instructor']); @endphp
                            {{ $instructorUser ? $instructorUser->fullName('FL') : 'CID ' . $vatcanUser['instructor'] }}
                        @else
                            <span class="text-muted">Not assigned</span>
                        @endif
                    </p>

                    <p class="mb-1" style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#94a3b8;">Waitlist Since</p>
                    @if(Auth::user()->permissions >= 4)
                    <form action="{{ route('training.students.waitlistdate', $student->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="d-flex" style="gap:0.5rem;">
                            <input type="date" name="waitlist_added_at" class="form-control form-control-sm flex-grow-1"
                                value="{{ $student->waitlist_added_at ? $student->waitlist_added_at->format('Y-m-d') : '' }}"
                                style="font-size:0.8rem; color:#495057;">
                            <button type="submit" class="btn btn-sm btn-success flex-shrink-0" style="font-size:0.8rem;">Save</button>
                        </div>
                    </form>
                    @elseif($student->waitlist_added_at)
                    <p class="mb-3" style="font-size:0.875rem; color:#122b44;">
                        {{ $student->waitlist_added_at->format('M j, Y') }}
                        <span class="text-muted">({{ $student->waitlist_added_at->diffForHumans() }})</span>
                    </p>
                    @endif

                </div>
            </div>

            {{-- Remove Student --}}
            @if(Auth::user()->permissions >= 4)
            <div class="card mb-4" style="border-color:#fee2e2;">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-1" style="color:#b91c1c;">Remove Student</h5>
                    <p class="text-muted mb-2" style="font-size:0.78rem;">Permanently removes this student from the training system.</p>
                    <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#removeStudentModal" style="font-size:0.8rem;">
                        Remove from System
                    </button>
                </div>
            </div>
            @endif

            {{-- Actions --}}
            @if(Auth::user()->permissions >= 3 || Auth::user()->instructorProfile)
            <div class="card mb-4">
                <div class="card-body">
                    @if(Auth::user()->permissions >= 4)
                    <h5 class="font-weight-bold mb-1" style="color:#122b44;">Instructor</h5>
                    <p class="text-muted mb-2" style="font-size:0.78rem;">Assigning an instructor links the student. Removing one returns them to the waitlist.</p>
                    <form action="{{ route('training.students.assigninstructor', $student->id) }}" method="POST">
                        @csrf
                        <div class="d-flex" style="gap:0.5rem;">
                            <select name="instructor" class="form-control form-control-sm flex-grow-1" style="font-size:0.8rem; color:#495057; background:#fff; padding:.25rem .5rem;">
                                <option value="unassign">Unlink student</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ $student->instructor_id == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->user->fullName('FL') }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-success flex-shrink-0" style="font-size:0.8rem;">Save</button>
                        </div>
                    </form>
                    @endif

                    <p class="mb-1" style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#94a3b8;">Mentorable</p>
                    <p class="text-muted mb-2" style="font-size:0.78rem;">If enabled, this student's bookable slots include every instructor, not just their assigned one.</p>
                    <form action="{{ route('training.students.mentorable', $student->id) }}" method="POST">
                        @csrf
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="mentorableSwitch" name="mentorable" value="1" onchange="this.form.submit()" {{ $student->mentorable ? 'checked' : '' }}>
                            <label class="custom-control-label" for="mentorableSwitch" style="font-size:0.85rem;">Mentorable</label>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
</div>

@if(Auth::user()->permissions >= 4)
<div class="modal fade" id="removeStudentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:1px solid #e9ecef;">
                <h5 class="modal-title font-weight-bold" style="color:#b91c1c;">Remove Student</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p style="font-size:0.875rem;">Are you sure you want to remove <strong>{{ $student->user ? $student->user->fullName('FL') : 'CID ' . $student->user_id }}</strong> from the training system? This cannot be undone.</p>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e9ecef;">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('training.students.remove', $student->id) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@stop
