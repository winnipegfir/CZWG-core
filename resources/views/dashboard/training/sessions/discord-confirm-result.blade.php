@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Confirm Session — Winnipeg FIR')

@section('content')

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container" style="max-width:520px;">
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas {{ $success ? 'fa-check-circle' : 'fa-exclamation-circle' }} fa-3x mb-3" style="color:{{ $success ? '#16a34a' : '#d97706' }};"></i>
            <h4 class="font-weight-bold mb-2" style="color:#122b44;">{{ $success ? 'Confirmed' : 'Heads up' }}</h4>
            <p class="text-muted mb-4">{{ $message }}</p>
            <a href="{{ route('training.sessions.index') }}" class="btn btn-sm btn-primary">Go to your sessions</a>
        </div>
    </div>
</div>
</div>

@stop
