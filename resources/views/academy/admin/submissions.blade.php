@extends('layouts.master')
@section('title', 'Academy Grading')
@section('content')
@include('academy._styles')
<style>
.academy-grade-pill{display:inline-flex;align-items:center;padding:.22rem .58rem;border-radius:999px;font-size:.72rem;font-weight:800}.academy-grade-pending{background:#fef3c7;color:#92400e}.academy-grade-pass{background:#dcfce7;color:#166534}.academy-grade-fail{background:#fee2e2;color:#991b1b}html[data-theme="dark"] .academy-grade-pending{background:#493718;color:#fde68a}html[data-theme="dark"] .academy-grade-pass{background:#123a27;color:#86efac}html[data-theme="dark"] .academy-grade-fail{background:#431c1c;color:#fecaca}
</style>
<div class="academy-hero">
    <div class="container">
        <a href="{{ route('academy.admin.hub') }}" class="academy-hero-link"><i class="fas fa-arrow-left"></i> Academy</a>
        <h1>Academy Grading</h1>
        <p class="mb-0" style="color:rgba(255,255,255,.65)">Instructor review of written knowledge-check responses and final marks.</p>
    </div>
</div>
<div class="academy-body">
    <div class="container">
        <div class="academy-panel">
            @forelse($submissions as $submission)
                <div class="academy-list-row">
                    <div>
                        <strong>{{ $submission->user ? $submission->user->fullName('FL') : 'Deleted user' }}</strong>
                        <div>{{ $submission->quiz->module->course->title }} · {{ $submission->quiz->module->title }}</div>
                        <small class="academy-muted">Submitted {{ optional($submission->submitted_at)->diffForHumans() }}</small>
                        <div class="mt-1">
                            @if($submission->status === 'graded')
                                <span class="academy-grade-pill {{ $submission->passed() ? 'academy-grade-pass' : 'academy-grade-fail' }}">{{ $submission->passed() ? 'PASSED' : 'NOT PASSED' }}</span>
                                <span class="academy-muted ml-1">{{ $submission->finalScore() }}/{{ $submission->maximum_score }} · {{ round($submission->percentage()) }}%</span>
                            @else
                                <span class="academy-grade-pill academy-grade-pending">AWAITING REVIEW</span>
                                <span class="academy-muted ml-1">Automatic: {{ $submission->automatic_score }}/{{ $submission->maximum_score }}</span>
                            @endif
                        </div>
                    </div>
                    <a class="btn btn-sm btn-primary" href="{{ route('academy.grading.show', $submission) }}">{{ $submission->status === 'graded' ? 'Review / Regrade' : 'Grade' }}</a>
                </div>
            @empty
                <div class="text-center academy-muted py-4">No submissions yet.</div>
            @endforelse
            <div class="mt-3">{{ $submissions->links() }}</div>
        </div>
    </div>
</div>
@stop
