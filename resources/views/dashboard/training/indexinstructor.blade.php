@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Training — Winnipeg FIR')

@section('content')
@include('includes.trainingMenu')

<div style="background:#f8fafc; min-height:calc(100vh - 112px); padding:2rem 0;">
<div class="container">


    {{-- Stats row (exec only) --}}
    @if(Auth::user()->permissions >= 4)
    @php
        $waitlistCount   = \App\Models\AtcTraining\Student::where('status', 0)->count();
        $inProgressCount = \App\Models\AtcTraining\Student::where('status', 1)->count();
    @endphp
    <div class="row mb-4" style="gap:0;">
        <div class="col-md-4 mb-3">
            <div class="card h-100" style="border-left:4px solid #f59e0b;">
                <div class="card-body py-3">
                    <p class="mb-1" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#92400e;">Waitlist</p>
                    <p class="mb-0" style="font-size:1.75rem; font-weight:700; color:#122b44; line-height:1;">{{ $waitlistCount }}</p>
                    <a href="{{ route('training.students.waitlist') }}" style="font-size:0.78rem; color:#92400e;">View waitlist &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100" style="border-left:4px solid #22c55e;">
                <div class="card-body py-3">
                    <p class="mb-1" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#15803d;">In Training</p>
                    <p class="mb-0" style="font-size:1.75rem; font-weight:700; color:#122b44; line-height:1;">{{ $inProgressCount }}</p>
                    <a href="{{ route('training.students.current') }}" style="font-size:0.78rem; color:#15803d;">View students &rarr;</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">

        {{-- Your Students --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h5 class="font-weight-bold mb-0" style="color:#122b44;">Your Students</h5>
                    </div>
                    @if($yourStudents && $yourStudents->count() > 0)
                        @foreach($yourStudents as $student)
                        <a href="{{ route('training.students.view', $student->id) }}"
                           style="display:flex; align-items:center; padding:0.6rem 0; border-bottom:1px solid #f1f5f9; text-decoration:none; color:#122b44;">
                            <img src="{{ $student->user->avatar() }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; margin-right:0.75rem; border:1px solid #e2e8f0; flex-shrink:0;">
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:600; font-size:0.875rem;">{{ $student->user->fullName('FL') }}</div>
                                <div style="font-size:0.75rem; color:#64748b;">{{ $student->user->rating->getShortName() }} &middot; {{ $student->entry_type }}</div>
                            </div>
                            @if($student->status == 1)
                                <span style="background:#dcfce7; color:#15803d; font-size:0.7rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">In Training</span>
                            @elseif($student->status == 4)
                                <span style="background:#fee2e2; color:#b91c1c; font-size:0.7rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">On Hold</span>
                            @elseif($student->status == 0)
                                <span style="background:#fef3c7; color:#92400e; font-size:0.7rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Waitlist</span>
                            @endif
                        </a>
                        @endforeach
                    @else
                        <p class="text-muted mb-0" style="font-size:0.875rem;">No students are assigned to you.</p>
                    @endif
                </div>
            </div>
        </div>


    </div>
</div>
</div>
@stop
