@extends('layouts.dashboard')
@section('title', 'Academy Management - Winnipeg FIR')
@section('content')
@include('academy._styles')
@php
    $isAcademyAdmin = Auth::user()->permissions >= 5;
    $canGradeAcademy = Auth::user()->canGradeAcademy();
@endphp
<div class="academy-hero">
    <div class="container">
        <a href="{{ route('dashboard.index') }}" class="academy-hero-link"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <div class="academy-kicker mt-3">{{ $isAcademyAdmin ? 'Administration' : 'Training Oversight' }}</div>
        <h1>Academy</h1>
        <p class="mb-0 academy-hero-subtitle">
            @if($isAcademyAdmin)
                Manage student access, grading, progress, and Academy content.
            @elseif($canGradeAcademy)
                Review student progress and grade Academy self assessments.
            @else
                Review student progress across the Academy.
            @endif
        </p>
    </div>
</div>
<div class="academy-body">
    @php $academyMode = \App\Services\AcademyVisibility::accessMode(); @endphp
    @if(\App\Services\AcademyVisibility::maintenanceMode())
    <div class="container"><div class="alert alert-danger mb-4"><strong>Academy Maintenance Mode is ON.</strong> Regular students see the maintenance screen. <a href="{{ route('settings.academy.deployment') }}">Change deployment settings</a>.</div></div>
    @elseif($academyMode === 'admin')
    <div class="container"><div class="alert alert-warning mb-4"><strong>Admin-only preview is active.</strong> The Academy is hidden from instructors, mentors, and students. <a href="{{ route('settings.academy.deployment') }}">Change deployment settings</a>.</div></div>
    @elseif($academyMode === 'staff')
    <div class="container"><div class="alert alert-info mb-4"><strong>Staff preview is active.</strong> Admins, instructors, and mentors can view the Academy; regular students cannot. <a href="{{ route('settings.academy.deployment') }}">Change deployment settings</a>.</div></div>
    @endif
    <div class="container">
        <div class="row justify-content-center">
            @if($isAcademyAdmin)
                <div class="col-md-6 col-lg-3 mb-4">
                    <a href="{{ route('academy.enrollments.index') }}" class="academy-card">
                        <div class="academy-card-body">
                            <div class="academy-icon"><i class="fas fa-user-graduate"></i></div>
                            <h5 class="font-weight-bold">Academy Enrollments</h5>
                            <p class="academy-muted mb-0">Assign or remove access to rating-specific Academy courses.</p>
                        </div>
                    </a>
                </div>
            @endif

            @if($canGradeAcademy)
                <div class="col-md-6 {{ $isAcademyAdmin ? 'col-lg-3' : 'col-lg-6' }} mb-4">
                    <a href="{{ route('academy.grading.index') }}" class="academy-card">
                        <div class="academy-card-body">
                            <div class="academy-icon"><i class="fas fa-clipboard-check"></i></div>
                            <h5 class="font-weight-bold">Academy Grading</h5>
                            <p class="academy-muted mb-0">Review written responses, set final marks, and notify students.</p>
                        </div>
                    </a>
                </div>
            @endif

            <div class="col-md-6 {{ $isAcademyAdmin ? 'col-lg-3' : ($canGradeAcademy ? 'col-lg-6' : 'col-lg-8') }} mb-4">
                <a href="{{ route('academy.admin.progress') }}" class="academy-card">
                    <div class="academy-card-body">
                        <div class="academy-icon"><i class="fas fa-chart-line"></i></div>
                        <h5 class="font-weight-bold">Student Progress</h5>
                        <p class="academy-muted mb-0">See each student's progress across published Academy courses.</p>
                    </div>
                </a>
            </div>

            @if($isAcademyAdmin)
                <div class="col-md-6 col-lg-3 mb-4">
                    <a href="{{ route('academy.admin.index') }}" class="academy-card">
                        <div class="academy-card-body">
                            <div class="academy-icon"><i class="fas fa-edit"></i></div>
                            <h5 class="font-weight-bold">Academy Editor</h5>
                            <p class="academy-muted mb-0">Edit courses, modules, presentations, and self assessments.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <a href="{{ route('settings.academy.deployment') }}" class="academy-card">
                        <div class="academy-card-body">
                            <div class="academy-icon"><i class="fas fa-sliders-h"></i></div>
                            <h5 class="font-weight-bold">Academy Deployment</h5>
                            <p class="academy-muted mb-0">Control admin-only preview, staff preview, launch permissions, and maintenance mode.</p>
                        </div>
                    </a>
                </div>
            @endif
        </div>

        <div class="academy-panel academy-access-note">
            <div class="d-flex align-items-center">
                <i class="fas fa-shield-alt mr-3 academy-access-icon"></i>
                <div>
                    @if($isAcademyAdmin)
                        <strong>Administrator access</strong>
                        <div class="academy-muted">You can use all Academy management tools, including enrollments, grading, progress, the Academy Editor, and deployment controls.</div>
                    @elseif($canGradeAcademy)
                        <strong>Instructor access</strong>
                        <div class="academy-muted">You can review student progress and grade self assessments. Enrollments and the Academy Editor are administrator-only.</div>
                    @else
                        <strong>Mentor access</strong>
                        <div class="academy-muted">You can review Academy Student Progress. Knowledge-check grading, enrollments, and the Academy Editor are restricted.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
