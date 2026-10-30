@extends('layouts.dashboard')
@section('title', 'Sector Files')
@section('content')
<div class="container my-4">
    <h1 class="blue-text font-weight-bold">Sector Files</h1>
    <p>Publish the full install package, nav data update and release notes for Winnipeg controllers. <a href="{{ route('sector-files.index') }}">View public page</a></p>
    @if(session('sector_status')) <div class="alert alert-success" role="status">{{ session('sector_status') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if(!$configured)
        <div class="alert alert-warning">Publishing is not configured yet. Ask the website administrator to configure the sector-file repository and credentials.</div>
    @elseif($unavailable)
        <div class="alert alert-warning">Release history could not be loaded. Refresh before making further changes.</div>
    @endif
    <div class="card mb-4"><div class="card-body">
        <h2 class="h4">Publish a release</h2>
        <form method="POST" action="{{ route('sector-files.store') }}" enctype="multipart/form-data">
            @csrf
            <fieldset @disabled(!$configured || $unavailable)>
                <div class="form-group"><label for="release-title">Title</label><input id="release-title" name="title" class="form-control" maxlength="150" required value="{{ old('title') }}" placeholder="Winnipeg FIR Sector Files"></div>
                <div class="form-group"><label for="release-version">Version / AIRAC</label><input id="release-version" name="version" class="form-control" maxlength="64" pattern="[A-Za-z0-9]+([._-][A-Za-z0-9]+)*" required value="{{ old('version') }}" placeholder="2026.10"><small class="form-text text-muted">Use a unique version with letters, numbers, dots, underscores or hyphens.</small></div>
                <div class="form-group"><label for="release-notes">Release notes</label><textarea id="release-notes" name="notes" class="form-control" rows="6" maxlength="30000">{{ old('notes') }}</textarea></div>
                <div class="form-group"><label for="release-full-zip">Full Install Package (ZIP)</label><input id="release-full-zip" type="file" name="full_zip" class="form-control-file" accept=".zip,application/zip" required><small class="form-text text-muted">Everything needed for a new installation. Maximum 100 MB.</small></div>
                <div class="form-group"><label for="release-nav-zip">Nav Data Update (ZIP)</label><input id="release-nav-zip" type="file" name="nav_zip" class="form-control-file" accept=".zip,application/zip" required><small class="form-text text-muted">Updated navigation files for an existing installation. Maximum 100 MB.</small></div>
                <p class="text-muted small">Prepare both packages in your usual working location. Publish after 0900Z on the AIRAC publication date, as specified in the team SOP. Leave the publication time blank to publish immediately after both uploads succeed.</p>
                <div class="form-group">
                    <label for="release-publish-at">Publish at (UTC / Zulu) — optional</label>
                    <input id="release-publish-at" name="publish_at" type="datetime-local" class="form-control" value="{{ old('publish_at') }}" @disabled(!config('sector-files.scheduling_enabled'))>
                    <small class="form-text text-muted">Enter a UTC date and time, not your local time. Leave blank to publish now. Current server time: {{ now('UTC')->format('Y-m-d H:i') }}Z (at page load).</small>
                    @if(config('sector-files.scheduling_enabled'))
                        <small class="form-text text-muted">Packages upload now as a hidden draft. Publication is checked every minute; if the service is interrupted, it publishes when service resumes. Discord is sent after publication if selected.</small>
                    @else
                        <small class="form-text text-muted">Scheduled publishing will be available after the administrator enables the server scheduler.</small>
                    @endif
                </div>
                <div class="form-check mb-3"><input id="release-announce" type="checkbox" class="form-check-input" name="announce" value="1" @checked(old('announce')) @disabled(!$discordConfigured)><label for="release-announce" class="form-check-label">Post Discord announcement</label></div>
                @if(!$discordConfigured)<p class="text-muted small">Discord announcements can be enabled later by the website administrator.</p>@endif
                <button class="btn btn-primary" type="submit">Upload and publish / schedule</button>
            </fieldset>
        </form>
    </div></div>
    <h2 class="h4">Release history</h2>
    <p class="text-muted">Older releases appear in the public archive automatically. Archiving removes a release from the latest-release position while retaining its download.</p>
    @forelse($releases as $release)
        <div class="card mb-3"><div class="card-body">
            <h3 class="h5">{{ $release['name'] }} <span class="badge badge-secondary">{{ $release['version'] }}</span></h3>
            <p class="text-muted">{{ $release['draft'] ? 'Unpublished draft' : ($release['archived'] ? 'Archived' : 'Published') }}
                &middot; {{ \Carbon\Carbon::parse($release['published_at'] ?? $release['created_at'])->timezone('America/Winnipeg')->format('F j, Y') }}
                &middot; {{ $release['packages_complete'] ? 'Both packages uploaded' : ($release['legacy_asset'] ? 'Legacy single ZIP' : 'Incomplete package upload') }}</p>
            @if($release['draft'] && $release['schedule'])
                <p class="alert alert-info">Scheduled: {{ \Carbon\CarbonImmutable::createFromTimestamp($release['schedule']['at'], 'UTC')->format('Y-m-d H:i') }}Z
                    · Discord announcement: {{ $release['schedule']['announce'] ? 'Yes' : 'No' }}.
                    @if(!config('sector-files.scheduling_enabled')) Scheduling is currently disabled; this release will remain a draft. @endif
                </p>
            @endif
            <details class="mb-3"><summary>Edit title and notes</summary>
                <form class="mt-3" method="POST" action="{{ route('sector-files.update', $release['id']) }}">
                    @csrf @method('PATCH')
                    <div class="form-group"><label for="title-{{ $release['id'] }}">Title</label><input id="title-{{ $release['id'] }}" name="title" class="form-control" required maxlength="150" value="{{ $release['name'] }}"></div>
                    <div class="form-group"><label for="notes-{{ $release['id'] }}">Release notes</label><textarea id="notes-{{ $release['id'] }}" name="notes" class="form-control" rows="5" maxlength="30000">{{ $release['notes'] }}</textarea></div>
                    <button class="btn btn-outline-primary" type="submit">Save details</button>
                </form>
            </details>
            @if($release['draft'] && $release['packages_complete'])
                @if(config('sector-files.scheduling_enabled'))
                    <form method="POST" action="{{ route('sector-files.schedule', $release['id']) }}" class="mb-3">
                        @csrf @method('PATCH')
                        <label for="schedule-{{ $release['id'] }}">Schedule publication (UTC / Zulu)</label>
                        <input id="schedule-{{ $release['id'] }}" type="datetime-local" name="publish_at" class="form-control mb-2" required value="{{ $release['schedule'] ? \Carbon\CarbonImmutable::createFromTimestamp($release['schedule']['at'], 'UTC')->format('Y-m-d\TH:i') : '' }}">
                        @if($discordConfigured)<label><input type="checkbox" name="announce" value="1" @checked($release['schedule']['announce'] ?? false)> Post Discord announcement when published</label>@endif
                        <button class="btn btn-outline-primary" type="submit">Save schedule</button>
                    </form>
                @endif
                @if($release['schedule'])
                    <form method="POST" action="{{ route('sector-files.schedule', $release['id']) }}" class="mb-3">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-secondary" type="submit">Cancel schedule and keep draft</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('sector-files.publish', $release['id']) }}" class="mb-3">
                    @csrf
                    @if($discordConfigured)<label><input type="checkbox" name="announce" value="1"> Post Discord announcement</label>@endif
                    <button class="btn btn-primary" type="submit">Publish draft now</button>
                </form>
            @elseif(!$release['draft'])
                <form method="POST" action="{{ route('sector-files.archive', $release['id']) }}" class="mb-3">
                    @csrf @method('PATCH')<input type="hidden" name="archived" value="{{ $release['archived'] ? 0 : 1 }}">
                    <button class="btn btn-outline-secondary" type="submit">{{ $release['archived'] ? 'Restore release' : 'Archive release' }}</button>
                </form>
            @endif
            <details><summary class="text-danger">Delete release</summary>
                <p class="mt-2">This permanently deletes this release and both ZIP packages from GitHub. Its Git tag remains. Use Archive to keep the download available.</p>
                <form method="POST" action="{{ route('sector-files.destroy', $release['id']) }}">
                    @csrf @method('DELETE')
                    <label class="mr-3"><input type="checkbox" name="confirm_delete" value="1" required> Delete {{ $release['version'] }} permanently</label>
                    <button class="btn btn-danger" type="submit">Delete release and packages</button>
                </form>
            </details>
        </div></div>
    @empty
        @unless($unavailable)<p>No sector-file releases yet.</p>@endunless
    @endforelse
</div>
@endsection
