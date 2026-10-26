@extends('layouts.master')
@section('title', 'Training - Winnipeg FIR')
@section('description', 'Learn about controller training at the Winnipeg FIR on VATSIM.')

@php
if (!$training_time) {
    $training_time = (object) [
        'colour' => 'grey',
        'wait_length' => 'N/A'
    ];
}
$statusMap = [
    'green'  => ['bg' => '#d4edda', 'border' => '#28a745', 'text' => '#155724', 'label' => 'Short Wait'],
    'yellow' => ['bg' => '#fff3cd', 'border' => '#feba00', 'text' => '#856404', 'label' => 'Moderate Wait'],
    'red'    => ['bg' => '#f8d7da', 'border' => '#dc3545', 'text' => '#721c24', 'label' => 'Long Wait'],
    'grey'   => ['bg' => '#f1f3f5', 'border' => '#adb5bd', 'text' => '#6c757d', 'label' => 'Status Unknown'],
];
$status = $statusMap[$training_time->colour] ?? $statusMap['grey'];
@endphp

@section('content')
<style>
.training-wrap {
    background: #f6f8fa;
    padding: 2.5rem 0 3rem;
}
.training-container {
    padding: 0 1rem;
}

/* ── Hero ─────────────────────────────────── */
.training-hero {
    margin-bottom: 2rem;
}
.training-hero h1 {
    font-size: 2.6rem;
    font-weight: 800;
    color: #122b44;
    margin-bottom: 0.6rem;
}
.training-hero p {
    color: #6c757d;
    font-size: 0.95rem;
    max-width: 680px;
    line-height: 1.65;
    margin: 0;
}

/* ── Wait time banner ─────────────────────── */
.wait-banner {
    border-radius: 10px;
    border: 1px solid;
    padding: 1.1rem 1.4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 2.25rem;
}
.wait-banner-left { display: flex; align-items: center; gap: 1rem; }
.wait-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}
.wait-label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    opacity: 0.65;
    display: block;
    margin-bottom: 0.1rem;
}
.wait-value {
    font-size: 1.05rem;
    font-weight: 700;
}

/* ── Content cards ────────────────────────── */
.section-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(0,0,0,0.3);
    margin-bottom: 1rem;
}
.training-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 1.4rem 1.6rem;
    margin-bottom: 1rem;
}
.training-card h3 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #122b44;
    margin: 0 0 0.6rem;
}
.training-card p {
    font-size: 0.875rem;
    color: #495057;
    line-height: 1.7;
    margin: 0;
}
.training-card p + p { margin-top: 0.6rem; }
.training-card a { color: #122b44; font-weight: 600; }

/* ── Big join CTA ────────────────────────── */
.join-cta-big {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #122b44;
    color: #fff;
    font-weight: 700;
    font-size: 1rem;
    padding: 0.9rem 1.5rem;
    border-radius: 10px;
    text-decoration: none;
    transition: background 0.15s;
    margin-bottom: 0;
}
.join-cta-big:hover { background: #1a3d5e; color: #fff; text-decoration: none; }

/* ── Footer ───────────────────────────────── */
.training-footer {
    margin-top: 1rem;
    padding-top: 0;
    border-top: none;
}
.training-footer a {
    color: #122b44;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
}
.training-footer a:hover { text-decoration: underline; }
.training-footer .muted-link {
    display: block;
    margin-top: 0.75rem;
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 400;
    text-decoration: none;
}
.training-footer .muted-link:hover { text-decoration: underline; }

/* ── Admin button ─────────────────────────── */
.admin-edit-btn {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.3rem 0.75rem;
    border-radius: 5px;
    border: 1px solid currentColor;
    background: transparent;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.15s;
}
.admin-edit-btn:hover { opacity: 1; }

@media (max-width: 600px) {
    .training-hero h1 { font-size: 1.8rem; }
}

/* Public training page refresh; wait-time colours and editor remain unchanged. */
.training-wrap .training-hero {
    position: relative;
    overflow: hidden;
    background: #122b44;
    isolation: isolate;
    border-radius: 16px;
    padding: 2.4rem 2.2rem;
    box-shadow: 0 10px 28px rgba(18, 43, 68, 0.10);
}
.training-wrap .training-hero-scope {
    position: absolute;
    inset: 0 0 0 auto;
    width: 48%;
    clip-path: polygon(24% 0, 100% 0, 100% 100%, 0 100%);
    z-index: -1;
    pointer-events: none;
}
.training-wrap .training-hero-scope img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
}
.training-wrap .training-hero-scope::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg, rgba(18,43,68,0.82), rgba(9,23,39,0.42));
}
.training-wrap .training-hero h1 {
    color: #fff;
    letter-spacing: -0.035em;
    position: relative;
    max-width: 70%;
}
.training-wrap .training-hero p { color: #d9e6ef; position: relative; max-width: 57%; }
@media (max-width: 767px) {
    .training-wrap .training-hero h1,
    .training-wrap .training-hero p { max-width: 100%; }
    .training-wrap .training-hero-scope { width: 55%; }
    .training-wrap .training-hero-scope::after {
        background: linear-gradient(105deg, rgba(18,43,68,0.95), rgba(9,23,39,0.72));
    }
}
.training-wrap .section-label { color: #52667a; }
.training-wrap .academy-feature {
    background: #fff;
    border: 1px solid #e0e7ed;
    border-left: 4px solid #248378;
    border-radius: 12px;
    padding: 1.7rem 1.8rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 18px rgba(18,43,68,0.04);
}
.training-wrap .academy-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    color: #1a675e;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    margin-bottom: 0.65rem;
}
.training-wrap .academy-feature h2 {
    color: #122b44;
    font-size: 1.45rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    margin: 0 0 0.8rem;
}
.training-wrap .academy-feature p { color: #495057; font-size: 0.925rem; line-height: 1.8; margin: 0; }
.training-wrap .academy-feature p + p { margin-top: 0.75rem; }
.training-wrap .training-card { border-radius: 12px; }
.training-wrap .join-cta-big:focus-visible { outline: 3px solid #248378; outline-offset: 3px; }
html[data-theme="dark"] .training-wrap { background: #161e28; }
html[data-theme="dark"] .training-wrap .academy-feature,
html[data-theme="dark"] .training-wrap .training-card { background: #1e2b3a; border-color: #35495c; }
html[data-theme="dark"] .training-wrap .academy-feature { border-left-color: #5fc6b5; }
html[data-theme="dark"] .training-wrap .academy-feature h2,
html[data-theme="dark"] .training-wrap .training-card h3 { color: #edf4fa; }
html[data-theme="dark"] .training-wrap .academy-feature p,
html[data-theme="dark"] .training-wrap .training-card p,
html[data-theme="dark"] .training-wrap .section-label,
html[data-theme="dark"] .training-wrap .training-footer .muted-link { color: #c2cdd8; }
html[data-theme="dark"] .training-wrap .academy-eyebrow { color: #8ad8ca; }
@media (max-width: 600px) {
    .training-wrap .training-hero { padding: 1.7rem 1.3rem; }
    .training-wrap .academy-feature { padding: 1.3rem; }
    .training-wrap .academy-feature h2 { font-size: 1.25rem; }
}
</style>

<div class="training-wrap">
<div class="container training-container">

    <div class="training-hero">
        <div class="training-hero-scope" aria-hidden="true">
            <img src="{{ asset('images/training/winnipeg-radar-scope.png') }}" alt="" width="991" height="320">
        </div>
        <h1>Controller Training</h1>
        <p>Everything you need to know about getting on the scope at the Winnipeg FIR — our training platform, wait times, and what to expect.</p>
    </div>

    {{-- Wait time banner --}}
    <div class="section-label">Current Instructor Linking Estimated Wait</div>
    <div class="wait-banner" style="background:{{ $status['bg'] }};border-color:{{ $status['border'] }};">
        <div class="wait-banner-left">
            <div class="wait-dot" style="background:{{ $status['border'] }};"></div>
            <div>
                <span class="wait-value" style="color:{{ $status['text'] }};">{{ $training_time->wait_length }}</span>
                @if(isset($training_time->updated_at) && $training_time->updated_at)
                <span style="display:block;font-size:0.72rem;color:{{ $status['text'] }};opacity:0.55;margin-top:0.15rem;">Last updated: {{ \Carbon\Carbon::parse($training_time->updated_at)->format('F j, Y') }}</span>
                @endif
            </div>
        </div>
        @if(Auth::check() && Auth::user()->permissions >= 4)
        <button class="admin-edit-btn" style="color:{{ $status['text'] }};" data-toggle="modal" data-target="#waitEdit">
            <i class="fas fa-pencil-alt fa-xs" style="margin-right:0.3rem;"></i> Edit
        </button>
        @endif
    </div>
    
    <section class="academy-feature" aria-labelledby="academy-training-title">
        <span class="academy-eyebrow"><i class="fas fa-graduation-cap" aria-hidden="true"></i> Winnipeg Academy</span>
        <h2 id="academy-training-title">Training, on Your Schedule</h2>
        <p>Winnipeg Academy brings self-paced controller learning directly into the Winnipeg FIR website. Work through your training material, build your knowledge, and prepare for practical sessions with an instructor &mdash; all in one place, at your own pace.</p>
        <p>While you wait to be linked with an instructor, you can focus on the foundations and arrive better prepared for your next step. Self-paced learning complements instructor-led training; you'll still need the required instruction and authorization before controlling live on the network.</p>
    </section>

    <div style="display:flex;gap:1rem;margin-bottom:1rem;flex-wrap:wrap;">
        <div class="training-card" style="flex:1;min-width:220px;margin-bottom:0;">
            <h3>Interested in Joining?</h3>
            <p>Ready to start your controlling career? Follow our step-by-step guide to create your VATSIM account, join VATCAN, and transfer to the Winnipeg FIR.</p>
        </div>
        <div class="training-card" style="flex:1;min-width:220px;margin-bottom:0;">
            <h3>Visiting Controllers</h3>
            <p>Visiting controllers receive training based on instructor availability. Winnipeg's home controllers hold priority, so we appreciate your patience — but we're always happy to welcome a new face.</p>
        </div>
    </div>

    <a href="{{ url('/join') }}" class="join-cta-big">
        Join the Winnipeg FIR <i class="fas fa-arrow-right fa-sm" style="margin-left:0.4rem;"></i>
    </a>

    <div class="training-footer">
        <a href="{{ route('staff') }}" class="muted-link" style="margin-top:0;">Questions? Contact our Chief Instructor</a>
    </div>

</div>
</div>

@if(Auth::check() && Auth::user()->permissions >= 4)
<div class="modal fade" id="waitEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Wait Time Editor</h5>
            </div>
            <form method="POST" action="{{ route('waittime.edit') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="font-weight-bold" style="font-size:0.875rem;">Wait Time</label>
                        <input name="waitTime" class="form-control" value="{{ $training_time->wait_length }}" placeholder="e.g. 1 Week">
                    </div>
                    <div>
                        <label class="font-weight-bold" style="font-size:0.875rem;">Status Colour</label>
                        <select name="trainingTimeColour" class="form-control">
                            <option value="green"  {{ $training_time->colour == 'green'  ? 'selected' : '' }}>Green — Short Wait</option>
                            <option value="yellow" {{ $training_time->colour == 'yellow' ? 'selected' : '' }}>Yellow — Moderate Wait</option>
                            <option value="red"    {{ $training_time->colour == 'red'    ? 'selected' : '' }}>Red — Long Wait</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
