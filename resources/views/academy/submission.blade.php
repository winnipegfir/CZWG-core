@extends('layouts.master')
@section('title', 'Assessment Result - Training Academy')
@section('content')
@include('academy._styles')
<style>
.academy-result-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:16px 0}.academy-result-stat{border:1px solid #e2e8f0;border-radius:10px;padding:14px;background:#f8fafc}.academy-result-label{font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:700}.academy-result-value{font-size:1.35rem;font-weight:800;margin-top:3px}.academy-result-pass{border-color:#bbf7d0;background:#f0fdf4}.academy-result-fail{border-color:#fecaca;background:#fef2f2}html[data-theme="dark"] .academy-result-stat{background:#20252d;border-color:#343b46}html[data-theme="dark"] .academy-result-label{color:#a7b0bd}html[data-theme="dark"] .academy-result-pass{background:#123a27;border-color:#166534}html[data-theme="dark"] .academy-result-fail{background:#431c1c;border-color:#7f1d1d}@media(max-width:767px){.academy-result-summary{grid-template-columns:1fr}}
</style>
<div class="academy-hero">
    <div class="container">
        <a href="{{ route('academy.modules.show', [$submission->quiz->module->course->slug, $submission->quiz->module->slug]) }}" class="academy-hero-link"><i class="fas fa-arrow-left"></i> {{ $submission->quiz->module->title }}</a>
        <h1>Self Assessment Submission</h1>
    </div>
</div>
<div class="academy-body">
    <div class="container">
        <div class="academy-panel">
            <h4 class="font-weight-bold">{{ $submission->quiz->title }}</h4>

            @if($submission->status === 'graded')
                @php($passed = $submission->passed())
                <div class="academy-result-summary">
                    <div class="academy-result-stat">
                        <div class="academy-result-label">Final score</div>
                        <div class="academy-result-value">{{ $submission->finalScore() }}/{{ $submission->maximum_score }}</div>
                    </div>
                    <div class="academy-result-stat">
                        <div class="academy-result-label">Percentage</div>
                        <div class="academy-result-value">{{ round($submission->percentage()) }}%</div>
                    </div>
                    <div class="academy-result-stat {{ $passed ? 'academy-result-pass' : 'academy-result-fail' }}">
                        <div class="academy-result-label">Result</div>
                        <div class="academy-result-value">{{ $passed ? 'Passed' : 'Not Passed' }}</div>
                    </div>
                </div>

                @if($passed)
                    <div class="alert alert-success mb-3"><strong>Course complete.</strong> You met the {{ $submission->quiz->passing_score }}% passing requirement.</div>
                @else
                    <div class="alert alert-warning mb-3"><strong>Course remains in progress.</strong> The passing requirement is {{ $submission->quiz->passing_score }}%. Review the material and try the self assessment again when ready.</div>
                @endif

                @if($submission->final_score !== null && $submission->finalScore() !== $submission->calculatedScore())
                    <div class="academy-muted mb-3">Question tally: {{ $submission->calculatedScore() }}/{{ $submission->maximum_score }}. The instructor recorded a final mark of {{ $submission->finalScore() }}/{{ $submission->maximum_score }}.</div>
                @endif
                @if($submission->grader)
                    <div class="academy-muted mb-3">Graded by {{ $submission->grader->fullName('FL') }} {{ optional($submission->graded_at)->diffForHumans() }}.</div>
                @endif
            @else
                <div class="alert alert-warning">Your multiple-choice questions have been marked automatically. An instructor must review the written responses before your final result is recorded.</div>
            @endif

            @foreach($submission->responses as $response)
                <div class="border rounded p-3 mb-3">
                    <strong>{{ $loop->iteration }}. {{ $response->question->question }}</strong>
                    <div class="mt-2">Your answer: {{ $response->question->type === 'written' ? $response->written_response : optional($response->answer)->answer }}</div>
                    @if($submission->status === 'graded')
                        <div class="academy-muted mt-2">Question score: {{ $response->awarded_points ?? 0 }}/{{ $response->question->points }}</div>
                        @if($response->grader_feedback)
                            <div class="mt-2"><strong>Instructor feedback:</strong> {{ $response->grader_feedback }}</div>
                        @endif
                        @if($response->question->explanation)
                            <div class="mt-2"><strong>Review:</strong> {{ $response->question->explanation }}</div>
                        @endif
                    @endif
                </div>
            @endforeach

            @if($submission->status === 'graded')
                <div class="academy-muted">Academy course status: <strong>{{ $courseProgress['status'] === 'complete' ? 'Complete' : 'In Progress' }}</strong></div>
            @endif
        </div>
    </div>
</div>
@stop
