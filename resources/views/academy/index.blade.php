@extends('layouts.master')
@section('title', 'Training Academy - Winnipeg FIR')
@section('content')
@include('academy._styles')
@php
    $academyTimezone = Auth::user()->timezone ?: 'America/Winnipeg';
    $academyHour = now($academyTimezone)->hour;
    $academyGreeting = $academyHour < 12
        ? 'Good morning'
        : ($academyHour < 17 ? 'Good afternoon' : 'Good evening');
    $academyFirstName = trim((string) (Auth::user()->display_fname ?: Auth::user()->fname ?: 'Controller'));
@endphp
<div class="academy-hero"><div class="container"><div class="academy-kicker">Winnipeg FIR Training Academy</div><h1><span id="academy-local-greeting">{{ $academyGreeting }}</span>, <span class="academy-greeting-name">{{ $academyFirstName }}</span>.</h1><p class="mb-0" style="color:rgba(255,255,255,.65)">Continue your training and pick up where you left off.</p></div></div>
<div class="academy-body"><div class="container">
    @if($courses->isEmpty())
        <div class="academy-panel text-center py-5"><i class="fas fa-book-open fa-2x mb-3" style="color:#9aa8b5"></i><h5>No courses published yet</h5><p class="academy-muted mb-0">Academy content will appear here when it is ready.</p></div>
    @else
        <div class="academy-course-toolbar">
            <div class="academy-course-filter" role="group" aria-label="Filter Academy courses">
                <button type="button" class="active" data-academy-filter="all" aria-pressed="true">All Courses</button>
                <button type="button" data-academy-filter="mine" aria-pressed="false">My Courses</button>
            </div>
        </div>
        <div class="row">
        @foreach($courses as $course)
            <div class="col-md-6 col-lg-4 mb-3 academy-course-item" data-academy-course data-assigned="{{ $course->is_assigned ? '1' : '0' }}">
                @if($course->can_access)<a class="academy-card" href="{{ route('academy.courses.show', $course->slug) }}">@else<div class="academy-card academy-card-locked" aria-label="{{ $course->title }} — locked">@endif
                    <div class="academy-thumb" @if($course->thumbnail) style="background-image:url('{{ $course->thumbnail }}')" @endif>
                        <div class="academy-thumb-icon"><i class="fas {{ $course->icon }}"></i></div>
                        @unless($course->can_access)
                            <span class="academy-lock"><i class="fas fa-lock mr-1"></i> Locked</span>
                            <div class="academy-lock-overlay" aria-hidden="true"><i class="fas fa-lock"></i></div>
                        @endunless
                    </div>
                    <div class="academy-card-body"><div class="d-flex justify-content-between align-items-start"><h5 class="font-weight-bold">{{ $course->title }}</h5>@if($course->can_access)<span class="academy-progress academy-progress-{{ $course->student_progress['status'] }}">{{ ucwords(str_replace('_',' ',$course->student_progress['status'])) }}</span>@endif</div><p class="academy-muted">{{ $course->description }}</p>
                    @if($course->can_access && !empty($course->student_progress['review_pending']))
                        <div class="academy-review-pending mb-2"><i class="fas fa-hourglass-half"></i><span>Self Assessment submitted for instructor review</span></div>
                    @endif
                    @if($course->can_access)<small>{{ $course->modules_count }} module{{ $course->modules_count === 1 ? '' : 's' }} <i class="fas fa-arrow-right ml-1"></i></small>@else<small class="text-muted"><i class="fas fa-lock mr-1"></i> Not currently assigned to you.</small>@endif</div>
                @if($course->can_access)</a>@else</div>@endif
            </div>
        @endforeach
        </div>
        <div class="academy-course-filter-empty academy-panel text-center" data-academy-filter-empty hidden>
            <i class="fas fa-book-open mb-2" aria-hidden="true"></i>
            <h5>No courses are currently assigned to you.</h5>
            <p class="academy-muted mb-0">Choose All Courses to return to the complete list.</p>
        </div>
    @endif
</div></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var greeting = document.getElementById('academy-local-greeting');
    if (greeting) {
        var localHour = new Date().getHours();
        greeting.textContent = localHour < 12
            ? 'Good morning'
            : (localHour < 17 ? 'Good afternoon' : 'Good evening');
    }

    var courseItems = Array.prototype.slice.call(document.querySelectorAll('[data-academy-course]'));
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    courseItems.forEach(function (item, index) {
        item.classList.add('academy-reveal-pending');
        item.style.transitionDelay = reduceMotion ? '0ms' : ((index % 3) * 55) + 'ms';
    });

    if (reduceMotion || !('IntersectionObserver' in window)) {
        courseItems.forEach(function (item) { item.classList.add('academy-reveal-visible'); });
    } else {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                entry.target.classList.toggle('academy-reveal-visible', entry.isIntersecting);
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -20px 0px' });
        courseItems.forEach(function (item) { observer.observe(item); });
    }

    var filterButtons = document.querySelectorAll('[data-academy-filter]');
    var emptyState = document.querySelector('[data-academy-filter-empty]');

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var filter = button.getAttribute('data-academy-filter');
            var visibleCount = 0;

            filterButtons.forEach(function (candidate) {
                var selected = candidate === button;
                candidate.classList.toggle('active', selected);
                candidate.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });

            courseItems.forEach(function (item) {
                var visible = filter === 'all' || item.getAttribute('data-assigned') === '1';
                item.classList.toggle('academy-course-filtered', !visible);
                if (visible) visibleCount++;
            });

            if (emptyState) {
                emptyState.hidden = visibleCount !== 0;
            }
        });
    });
});
</script>
@stop
