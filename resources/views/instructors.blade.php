@extends('layouts.master')

@section('title', 'Instructing Staff - Winnipeg FIR')
@section('description', 'The Winnipeg Instructors and Mentors!')

@section('content')
<div style="background:#fff; min-height: calc(100vh - 60px); padding: 2.5rem 0;">
    <div class="container">

        <a href="{{ route('staff') }}" style="color:#6c757d; font-size:0.9rem; text-decoration:none;">
            <i class="fas fa-arrow-left fa-xs mr-1"></i> Staff
        </a>

        <div class="mt-2 mb-2">
            <h1 class="font-weight-bold" style="color:#122b44;">Instructors &amp; Mentors</h1>
            <p style="color:#6c757d; margin-bottom:0;">The training team responsible for developing controllers across Winnipeg FIR.</p>
        </div>
        <hr>

        @php
            $instructors = \App\Models\Teacher::where('is_instructor', 1)->get();
            $mentors     = \App\Models\Teacher::where('is_instructor', 0)->get();
        @endphp

        {{-- Instructors --}}
        <h4 class="font-weight-bold mt-4 mb-3" style="color:#122b44;">Instructors</h4>
        @if($instructors->isEmpty())
            <p style="color:#adb5bd;">No instructors listed.</p>
        @else
            <div class="row">
                @foreach($instructors as $t)
                    <div class="col-md-6 mb-3">
                        @include('partials.teacher-card', ['t' => $t])
                    </div>
                @endforeach
            </div>
        @endif

        <hr class="my-4">

        {{-- Mentors --}}
        <h4 class="font-weight-bold mb-3" style="color:#122b44;">Mentors</h4>
        @if($mentors->isEmpty())
            <p style="color:#adb5bd;">No mentors listed.</p>
        @else
            <div class="row">
                @foreach($mentors as $t)
                    <div class="col-md-6 mb-3">
                        @include('partials.teacher-card', ['t' => $t])
                    </div>
                @endforeach
            </div>
        @endif

        @if(Auth::check() && Auth::user()->permissions >= 4)
            <div class="mt-3">
                <button class="btn btn-sm" style="background:#122b44; color:#fff;" data-target="#addTeacher" data-toggle="modal">
                    <i class="fas fa-plus fa-xs mr-1"></i> Add Teacher
                </button>
            </div>
        @endif

    </div>
</div>

{{-- Add Teacher Modal --}}
<div class="modal fade" id="addTeacher" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:1px solid #e9ecef;">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Add Instructor / Mentor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('instructors.store') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-size:0.85rem; color:#6c757d;">Controller</label>
                        <select class="js-example-basic-single form-control" style="width:100%" name="newteacher">
                            @foreach(\App\Models\AtcTraining\RosterMember::all() as $user)
                                <option value="{{ $user->cid }}">{{ $user->cid }} &mdash; {{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" name="is_instructor" id="isInstructor">
                        <label class="form-check-label" for="isInstructor">Instructor <span style="color:#6c757d; font-size:0.82rem;">(unchecked = Mentor)</span></label>
                    </div>
                    <p class="font-weight-bold mb-1" style="font-size:0.9rem; color:#122b44;">Specialties</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name="is_local" id="isLocal">
                        <label class="form-check-label" for="isLocal">Local</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name="is_radar" id="isRadar">
                        <label class="form-check-label" for="isRadar">Radar</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name="is_enroute" id="isEnroute">
                        <label class="form-check-label" for="isEnroute">En-Route</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e9ecef;">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background:#122b44; color:#fff;">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
