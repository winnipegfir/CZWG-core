@extends('layouts.master')
@section('title', 'Winnipeg FIR Sector Files')
@section('description', 'Current and archived Winnipeg FIR sector files and release notes')
@section('content')
<div class="container my-4">
    <h1 class="blue-text font-weight-bold">Winnipeg FIR Sector Files</h1>
    <p class="text-muted">Download the latest sector files and review changes from the Facility Engineering team.</p>
    @if($unavailable)
        <div class="alert alert-warning" role="alert">Sector files are temporarily unavailable. Please try again later.</div>
    @else
        @if($latest)
            <h2 class="h4 mt-4">Latest release</h2>
            @include('sector-files.release', ['release' => $latest, 'featured' => true])
        @else
            <p class="alert alert-info">No current release is available.</p>
        @endif
        @if(count($older))
            <h2 class="h5 mt-4">Archived releases</h2>
            <p class="text-muted">Previous versions remain available for reference.</p>
            @foreach($older as $release)
                @include('sector-files.release', ['release' => $release, 'featured' => false])
            @endforeach
        @endif
    @endif
</div>
@endsection
