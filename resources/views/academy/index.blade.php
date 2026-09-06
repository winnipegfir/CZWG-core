@extends('layouts.master')
@section('title', 'Training Academy - Winnipeg FIR')
@section('content')
@include('academy._styles')
<div class="academy-hero"><div class="container"><div class="academy-kicker">Winnipeg FIR</div><h1>Training Academy</h1><p class="mb-0" style="color:rgba(255,255,255,.65)">Courses and learning resources for Winnipeg controllers.</p></div></div>
<div class="academy-body"><div class="container">
    @if($courses->isEmpty())
        <div class="academy-panel text-center py-5"><i class="fas fa-book-open fa-2x mb-3" style="color:#9aa8b5"></i><h5>No courses published yet</h5><p class="academy-muted mb-0">Academy content will appear here when it is ready.</p></div>
    @else
        <div class="row">
        @foreach($courses as $course)
            <div class="col-md-6 col-lg-4 mb-3">
                @if($course->can_access)<a class="academy-card" href="{{ route('academy.courses.show', $course->slug) }}">@else<div class="academy-card academy-card-locked" aria-label="{{ $course->title }} — locked">@endif
                    <div class="academy-thumb" @if($course->thumbnail) style="background-image:url('{{ $course->thumbnail }}')" @endif>
                        <div class="academy-thumb-icon"><i class="fas {{ $course->icon }}"></i></div>
                        @unless($course->can_access)
                            <span class="academy-lock"><i class="fas fa-lock mr-1"></i> Locked</span>
                            <div class="academy-lock-overlay" aria-hidden="true"><i class="fas fa-lock"></i></div>
                        @endunless
                    </div>
                    <div class="academy-card-body"><div class="d-flex justify-content-between align-items-start"><h5 class="font-weight-bold">{{ $course->title }}</h5>@if($course->can_access)<span class="academy-progress academy-progress-{{ $course->student_progress['status'] }}">{{ ucwords(str_replace('_',' ',$course->student_progress['status'])) }}</span>@endif</div><p class="academy-muted">{{ $course->description }}</p>
                    @if($course->can_access)<small>{{ $course->modules_count }} module{{ $course->modules_count === 1 ? '' : 's' }} <i class="fas fa-arrow-right ml-1"></i></small>@else<small class="text-muted"><i class="fas fa-lock mr-1"></i> Not currently assigned to you.</small>@endif</div>
                @if($course->can_access)</a>@else</div>@endif
            </div>
        @endforeach
        </div>
    @endif
</div></div>
@stop
