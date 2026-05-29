@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Training — Winnipeg FIR')

@section('content')
@include('includes.trainingMenu')

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container">

    {{-- Stats row (exec only) --}}
    @if(Auth::user()->permissions >= 4)
    @php
        $waitlistCount   = \App\Models\AtcTraining\Student::whereNull('instructor_id')->count();
        $inProgressCount = \App\Models\AtcTraining\Student::whereNotNull('instructor_id')->count();
    @endphp
    <div class="row mb-4">
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
                    <p class="mb-1" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#15803d;">Linked</p>
                    <p class="mb-0" style="font-size:1.75rem; font-weight:700; color:#122b44; line-height:1;">{{ $inProgressCount }}</p>
                    <a href="{{ route('training.students.current') }}" style="font-size:0.78rem; color:#15803d;">View linked &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100" style="border-left:4px solid #6366f1;">
                <div class="card-body py-3">
                    <p class="mb-1" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#4338ca;">Longest Waiting</p>
                    @if($longestWaiting)
                        <p class="mb-0" style="font-size:1.1rem; font-weight:700; color:#122b44; line-height:1.3;">
                            {{ $longestWaiting->user ? $longestWaiting->user->fullName('FL') : 'CID ' . $longestWaiting->user_id }}
                        </p>
                        <p class="mb-0" style="font-size:0.78rem; color:#6366f1;">
                            {{ $longestWaiting->waitlist_added_at->diffForHumans() }}
                        </p>
                    @else
                        <p class="mb-0 text-muted" style="font-size:0.875rem;">No one waiting</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- Waitlist breakdown --}}
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-3" style="color:#122b44; font-size:0.95rem;">Waitlist Breakdown</h5>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <div class="d-flex align-items-center justify-content-between">
                            <span style="background:#dbeafe; color:#1d4ed8; font-size:0.72rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Home</span>
                            <span style="font-weight:700; color:#122b44;">{{ $waitlistBreakdown['home'] }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span style="background:#dcfce7; color:#15803d; font-size:0.72rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Visiting</span>
                            <span style="font-weight:700; color:#122b44;">{{ $waitlistBreakdown['visiting'] }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span style="background:#f3e8ff; color:#7e22ce; font-size:0.72rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Transfer</span>
                            <span style="font-weight:700; color:#122b44;">{{ $waitlistBreakdown['transfer'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent activity --}}
        <div class="col-md-8 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-3" style="color:#122b44; font-size:0.95rem;">Recent Activity</h5>
                    @foreach($recentActivity as $s)
                    <a href="{{ route('training.students.view', $s->id) }}"
                       style="display:flex; align-items:center; padding:0.45rem 0; border-bottom:1px solid #f1f5f9; text-decoration:none; color:#122b44;">
                        <img src="{{ $s->user ? $s->user->avatar() : asset('img/default-profile-img.jpg') }}" style="width:30px; height:30px; border-radius:50%; object-fit:cover; border:1px solid #e2e8f0; margin-right:0.65rem; flex-shrink:0;">
                        <div style="flex:1; min-width:0;">
                            <div style="font-weight:600; font-size:0.85rem;">{{ $s->user ? $s->user->fullName('FL') : 'CID ' . $s->user_id }}</div>
                            <div style="font-size:0.75rem; color:#94a3b8;">
                                {{ $s->instructor_id ? 'Linked' : 'Waitlist' }}
                                &middot; updated {{ $s->updated_at->diffForHumans() }}
                            </div>
                        </div>
                        @if($s->instructor_id)
                            <span style="background:#dcfce7; color:#15803d; font-size:0.68rem; font-weight:700; padding:0.15em 0.45em; border-radius:0.3rem; flex-shrink:0;">Linked</span>
                        @elseif($s->entry_type == 'New Visitor')
                            <span style="background:#dcfce7; color:#15803d; font-size:0.68rem; font-weight:700; padding:0.15em 0.45em; border-radius:0.3rem; flex-shrink:0;">Visiting</span>
                        @elseif($s->entry_type == 'New Transfer')
                            <span style="background:#f3e8ff; color:#7e22ce; font-size:0.68rem; font-weight:700; padding:0.15em 0.45em; border-radius:0.3rem; flex-shrink:0;">Transfer</span>
                        @else
                            <span style="background:#dbeafe; color:#1d4ed8; font-size:0.68rem; font-weight:700; padding:0.15em 0.45em; border-radius:0.3rem; flex-shrink:0;">Home</span>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        {{-- Your Students --}}
        <div class="{{ Auth::user()->permissions >= 4 ? 'col-md-6' : 'col-12' }} mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-3" style="color:#122b44;">Your Students</h5>
                    @if($yourStudents && $yourStudents->count() > 0)
                        @foreach($yourStudents as $student)
                        <a href="{{ route('training.students.view', $student->id) }}"
                           style="display:flex; align-items:center; padding:0.6rem 0; border-bottom:1px solid #f1f5f9; text-decoration:none; color:#122b44;">
                            <img src="{{ $student->user ? $student->user->avatar() : asset('img/default-profile-img.jpg') }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; margin-right:0.75rem; border:1px solid #e2e8f0; flex-shrink:0;">
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:600; font-size:0.875rem;">{{ $student->user ? $student->user->fullName('FL') : 'CID ' . $student->user_id }}</div>
                                <div style="font-size:0.75rem; color:#64748b;">{{ $student->user ? $student->user->rating->getShortName() : '—' }} &middot; {{ $student->entry_type }}</div>
                            </div>
                            @if($student->instructor_id)
                                <span style="background:#dcfce7; color:#15803d; font-size:0.7rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Linked</span>
                            @else
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
