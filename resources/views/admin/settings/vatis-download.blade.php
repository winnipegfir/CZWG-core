@extends('layouts.master')

@section('content')
<div class="container py-4">
    <a href="{{ route('settings.index') }}" class="blue-text" style="font-size:1.2em;"><i class="fas fa-arrow-left"></i> Settings</a>
    <h1 class="blue-text font-weight-bold mt-2">vATIS Download</h1>
    <p class="text-muted">Update the Winnipeg FIR vATIS facility-file link displayed under Dashboard ATC Resources.</p>
    <hr>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('settings.vatis-download.save') }}">
        @csrf
        <div class="card mb-4">
            <div class="card-body">
                <div class="form-group mb-0">
                    <label for="vatis_url">Facility-file download URL</label>
                    <input type="url" class="form-control" id="vatis_url" name="vatis_url" maxlength="2048" required value="{{ old('vatis_url', optional($vatisResource)->url) }}" placeholder="https://.../Winnipeg-vATIS.json">
                    <small class="form-text text-muted">Upload the JSON file to an accessible location first, then paste its direct download link here. Saving updates the existing dashboard resource or creates it if missing.</small>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save vATIS link</button>
    </form>
</div>
@stop
