@extends('layouts.master')
@section('title', $course->title.' - Training Academy')
@section('content')
@include('academy._styles')
<div class="academy-hero academy-course-hero"><div class="container-fluid academy-course-shell-width"><a href="{{ route('academy.index') }}" class="academy-hero-link"><i class="fas fa-arrow-left"></i> Academy</a><h1>{{ $course->title }}</h1><p class="mb-0" style="color:rgba(255,255,255,.65)">{{ $course->description }}</p></div></div>
<div class="academy-body academy-course-body">
    <div class="container-fluid academy-course-shell-width">
        <div class="academy-course-shell" data-academy-course-shell>
            @include('academy._course_sidebar')
            <main class="academy-course-main">
                <div class="academy-panel academy-course-overview">
                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:1rem">
                        <div>
                            <div class="academy-kicker">Course overview</div>
                            <h3 class="mb-1">{{ $course->title }}</h3>
                            <div class="academy-muted">Choose a module from the left to begin or continue.</div>
                        </div>
                        <span class="academy-progress academy-progress-{{ $courseProgress['status'] }}">{{ ucwords(str_replace('_',' ',$courseProgress['status'])) }}</span>
                    </div>
                </div>

                <div class="academy-panel">
                    @forelse($course->modules as $module)
                        <div class="academy-list-row">
                            <div>
                                <div class="academy-status {{ $viewedModuleIds->contains($module->id) ? 'text-success' : 'text-muted' }}">{{ $viewedModuleIds->contains($module->id) ? 'Viewed' : 'Not viewed' }}</div>
                                <h5 class="mb-1">{{ $module->title }}</h5>
                                <div class="academy-muted">{{ $module->description }}</div>
                                @if(($module->slide_count ?? 0) > 0)<div class="academy-muted mt-1"><i class="fas fa-images mr-1"></i>{{ $module->slide_count }} slides</div>@endif
                            </div>
                            <a class="btn btn-sm btn-primary" href="{{ route('academy.modules.show', [$course->slug, $module->slug]) }}">{{ $viewedModuleIds->contains($module->id) ? 'Review' : 'Open' }}</a>
                        </div>
                    @empty
                        <div class="text-center academy-muted py-4">No modules have been published in this course yet.</div>
                    @endforelse
                </div>
            </main>
        </div>
    </div>
</div>
@include('academy._sidebar_script')
@stop
