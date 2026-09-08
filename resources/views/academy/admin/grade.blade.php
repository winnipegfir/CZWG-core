@extends('layouts.master')
@section('title', 'Grade Academy Submission')
@section('content')
@include('academy._styles')
@php
    $automaticMax = $submission->responses->filter(function ($r) { return $r->question->type !== 'written'; })->sum(function ($r) { return $r->question->points; });
    $writtenMax = $submission->responses->filter(function ($r) { return $r->question->type === 'written'; })->sum(function ($r) { return $r->question->points; });
    $writtenAwarded = $submission->responses->filter(function ($r) { return $r->question->type === 'written'; })->sum(function ($r) { return (int) $r->awarded_points; });
    $calculatedScore = (int) $submission->automatic_score + (int) $writtenAwarded;
    $initialFinalScore = old('final_score', $submission->final_score);
@endphp
<style>
.academy-grade-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:18px}.academy-grade-stat{border:1px solid #e2e8f0;border-radius:10px;padding:14px;background:#f8fafc}.academy-grade-stat-label{font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;font-weight:700}.academy-grade-stat-value{font-size:1.35rem;font-weight:800;margin-top:3px}.academy-final-box{border:2px solid #c7d2fe;border-radius:12px;padding:18px;background:#eef2ff}.academy-result-badge{display:inline-flex;align-items:center;padding:.3rem .7rem;border-radius:999px;font-size:.78rem;font-weight:800}.academy-result-pass{background:#dcfce7;color:#166534}.academy-result-fail{background:#fee2e2;color:#991b1b}.academy-discretion-note{font-size:.82rem;color:#64748b}.academy-answer-auto{display:flex;justify-content:space-between;gap:12px;align-items:center}.academy-auto-correct{color:#15803d;font-weight:700}.academy-auto-wrong{color:#b91c1c;font-weight:700}
html[data-theme="dark"] .academy-grade-stat{background:#20252d;border-color:#343b46}html[data-theme="dark"] .academy-grade-stat-label,html[data-theme="dark"] .academy-discretion-note{color:#a7b0bd}html[data-theme="dark"] .academy-final-box{background:#202538;border-color:#475569}html[data-theme="dark"] .academy-result-pass{background:#123a27;color:#86efac}html[data-theme="dark"] .academy-result-fail{background:#431c1c;color:#fecaca}
@media(max-width:767px){.academy-grade-summary{grid-template-columns:repeat(2,minmax(0,1fr))}}
</style>
<div class="academy-hero">
    <div class="container">
        <a href="{{ route('academy.grading.index') }}" class="academy-hero-link"><i class="fas fa-arrow-left"></i> Grading queue</a>
        <h1>Grade Submission</h1>
        <p class="mb-0" style="color:rgba(255,255,255,.65)">{{ $submission->user->fullName('FL') }} · {{ $submission->quiz->module->course->title }}</p>
    </div>
</div>
<div class="academy-body">
    <div class="container">
        <form method="POST" action="{{ route('academy.grading.update', $submission) }}" id="academyGradeForm">
            @csrf

            <div class="academy-panel">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                    <div>
                        <h4 class="font-weight-bold mb-1">{{ $submission->quiz->title }}</h4>
                        <div class="academy-muted">Passing mark: {{ $submission->quiz->passing_score }}% · Maximum: {{ $submission->maximum_score }} points</div>
                    </div>
                    @if($submission->status === 'graded')
                        <div class="academy-muted mt-2 mt-md-0">Last graded {{ optional($submission->graded_at)->diffForHumans() }} @if($submission->grader) by {{ $submission->grader->fullName('FL') }} @endif</div>
                    @endif
                </div>

                <div class="academy-grade-summary">
                    <div class="academy-grade-stat">
                        <div class="academy-grade-stat-label">Automatic</div>
                        <div class="academy-grade-stat-value">{{ (int) $submission->automatic_score }}/{{ $automaticMax }}</div>
                    </div>
                    <div class="academy-grade-stat">
                        <div class="academy-grade-stat-label">Written</div>
                        <div class="academy-grade-stat-value"><span id="writtenScoreLive">{{ $writtenAwarded }}</span>/{{ $writtenMax }}</div>
                    </div>
                    <div class="academy-grade-stat">
                        <div class="academy-grade-stat-label">Tallied total</div>
                        <div class="academy-grade-stat-value"><span id="calculatedScoreLive">{{ $calculatedScore }}</span>/{{ $submission->maximum_score }}</div>
                    </div>
                    <div class="academy-grade-stat">
                        <div class="academy-grade-stat-label">Tallied %</div>
                        <div class="academy-grade-stat-value"><span id="calculatedPercentLive">{{ $submission->maximum_score ? round($calculatedScore / $submission->maximum_score * 100) : 0 }}</span>%</div>
                    </div>
                </div>
            </div>

            @foreach($submission->responses as $response)
                <div class="academy-panel">
                    <strong>{{ $loop->iteration }}. {{ $response->question->question }}</strong>
                    <div class="mt-3 p-3 border rounded">
                        {{ $response->question->type === 'written' ? $response->written_response : optional($response->answer)->answer }}
                    </div>

                    @if($response->question->type === 'written')
                        @if($response->question->rubric)
                            <div class="mt-3"><strong>Staff rubric:</strong><div style="white-space:pre-line">{{ $response->question->rubric }}</div></div>
                        @endif
                        <div class="form-row mt-3">
                            <div class="form-group col-md-3">
                                <label>Points (max {{ $response->question->points }})</label>
                                <input class="form-control js-written-score" type="number" min="0" max="{{ $response->question->points }}" name="scores[{{ $response->id }}]" value="{{ old('scores.'.$response->id, $response->awarded_points) }}" required>
                            </div>
                            <div class="form-group col-md-9">
                                <label>Instructor feedback</label>
                                <textarea class="form-control" name="feedback[{{ $response->id }}]" rows="2">{{ old('feedback.'.$response->id, $response->grader_feedback) }}</textarea>
                            </div>
                        </div>
                    @else
                        <div class="academy-answer-auto mt-2">
                            <div class="academy-muted">Automatically marked: {{ $response->awarded_points }}/{{ $response->question->points }}</div>
                            <div class="{{ (int) $response->awarded_points === (int) $response->question->points ? 'academy-auto-correct' : 'academy-auto-wrong' }}">
                                {{ (int) $response->awarded_points === (int) $response->question->points ? 'Correct' : 'Incorrect' }}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="academy-panel academy-final-box">
                <h5 class="font-weight-bold mb-2">Final mark</h5>
                <p class="academy-discretion-note mb-3">The Academy tallies the automatic and written-question points for you. If professional judgement warrants a different final mark, enter an override below. Leave it blank to use the tallied score.</p>
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4 mb-md-0">
                        <label for="finalScoreOverride"><strong>Final score override</strong> (optional)</label>
                        <div class="input-group">
                            <input id="finalScoreOverride" class="form-control" type="number" min="0" max="{{ $submission->maximum_score }}" name="final_score" value="{{ $initialFinalScore }}" placeholder="Use tallied score">
                            <div class="input-group-append"><span class="input-group-text">/ {{ $submission->maximum_score }}</span></div>
                        </div>
                    </div>
                    <div class="col-md-5 mb-3 mb-md-0">
                        <div class="academy-grade-stat-label">Final result preview</div>
                        <div class="mt-1"><strong><span id="finalScoreLive">{{ $initialFinalScore !== null && $initialFinalScore !== '' ? $initialFinalScore : $calculatedScore }}</span>/{{ $submission->maximum_score }} (<span id="finalPercentLive">{{ $submission->maximum_score ? round((($initialFinalScore !== null && $initialFinalScore !== '' ? $initialFinalScore : $calculatedScore) / $submission->maximum_score) * 100) : 0 }}</span>%)</strong></div>
                        <div class="mt-1"><span id="resultBadge" class="academy-result-badge"></span></div>
                    </div>
                    <div class="col-md-3 text-md-right">
                        <button class="btn btn-primary btn-lg">Save grade & notify</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
(function () {
    var automatic = {{ (int) $submission->automatic_score }};
    var maximum = {{ (int) $submission->maximum_score }};
    var passing = {{ (int) $submission->quiz->passing_score }};
    var scoreInputs = Array.prototype.slice.call(document.querySelectorAll('.js-written-score'));
    var override = document.getElementById('finalScoreOverride');
    var writtenLive = document.getElementById('writtenScoreLive');
    var calculatedLive = document.getElementById('calculatedScoreLive');
    var calculatedPercentLive = document.getElementById('calculatedPercentLive');
    var finalScoreLive = document.getElementById('finalScoreLive');
    var finalPercentLive = document.getElementById('finalPercentLive');
    var resultBadge = document.getElementById('resultBadge');

    function numberValue(el) {
        var value = parseInt(el.value, 10);
        return isNaN(value) ? 0 : value;
    }

    function refresh() {
        var written = scoreInputs.reduce(function (sum, input) { return sum + numberValue(input); }, 0);
        var calculated = automatic + written;
        var overrideValue = override && override.value !== '' ? numberValue(override) : null;
        var finalScore = overrideValue === null ? calculated : overrideValue;
        var calculatedPercent = maximum ? Math.round(calculated / maximum * 100) : 0;
        var finalPercent = maximum ? Math.round(finalScore / maximum * 100) : 0;

        writtenLive.textContent = written;
        calculatedLive.textContent = calculated;
        calculatedPercentLive.textContent = calculatedPercent;
        finalScoreLive.textContent = finalScore;
        finalPercentLive.textContent = finalPercent;

        var passed = finalPercent >= passing;
        resultBadge.textContent = passed ? 'PASS — course will be complete' : 'NOT PASSED — course remains in progress';
        resultBadge.className = 'academy-result-badge ' + (passed ? 'academy-result-pass' : 'academy-result-fail');
    }

    scoreInputs.forEach(function (input) { input.addEventListener('input', refresh); });
    if (override) { override.addEventListener('input', refresh); }
    refresh();
})();
</script>
@stop
