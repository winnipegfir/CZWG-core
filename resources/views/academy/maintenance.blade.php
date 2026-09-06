@extends('layouts.master')
@section('title', 'Academy Maintenance - Winnipeg FIR')
@section('content')
@include('academy._styles')
<div class="academy-hero">
    <div class="container">
        <div class="academy-kicker">Winnipeg Training Academy</div>
        <h1>Academy Maintenance</h1>
        <p class="mb-0" style="color:rgba(255,255,255,.72)">The Academy is temporarily unavailable while training material is being updated.</p>
    </div>
</div>
<div class="academy-body">
    <div class="container" style="max-width:760px">
        <div class="academy-panel text-center py-5">
            <i class="fas fa-tools fa-3x mb-3" style="color:#2563eb"></i>
            <h3>We’ll be back shortly</h3>
            <p class="academy-muted mb-0">Please return after maintenance is complete. Your existing Academy progress and assessment records are preserved.</p>
        </div>
    </div>
</div>
@stop
