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
</style>

<div class="training-wrap">
<div class="container training-container">

    <div class="training-hero">
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
    
    <h3 style="font-size:1.05rem;font-weight:700;color:#122b44;margin:0 0 0.6rem;">Training, on Your Schedule</h3>
    <p style="font-size:0.875rem;color:#495057;line-height:1.7;margin:0;">For many years, VATSIM controllers-to-be had the same pathway to getting on the scope — join a FIR, then wait for an instructor to become available. That causes bottlenecks, and the Winnipeg team built <a href="https://training.winnipegfir.ca" target="_blank" rel="noopener noreferrer" style="color:#122b44;font-weight:600;">Winnipeg365</a> to fix that: a state-of-the-art online training platform built for both students and instructors.</p>
    <p style="font-size:0.875rem;color:#495057;line-height:1.7;margin:0.6rem 0 2rem;">Once accepted into the FIR — typically only a few days — you're automatically enrolled and can get right into the fundamentals: the laws of aviation, taxiing, aircraft types, and more. You'll still need to wait for an instructor before controlling live on the network, but Winnipeg365 speeds that process up for everyone.</p>

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
