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
                <p class="text-muted small">Prepare both packages in your usual working location. Publish after 0900Z on the AIRAC publication date, as specified in the team SOP. Choose immediate publishing or schedule a future release below.</p>
                <div class="border rounded p-3 mb-3" data-sector-schedule>
                    <h3 class="h6">Publication</h3>
                    <input type="hidden" name="schedule_later" value="0">
                    <div class="form-check">
                        <input id="release-schedule-later" class="form-check-input" type="checkbox" name="schedule_later" value="1" data-schedule-toggle aria-controls="release-schedule-fields" aria-expanded="false" @checked(old('schedule_later', old('publish_at') ? 1 : 0)) @disabled(!config('sector-files.scheduling_enabled'))>
                        <label class="form-check-label" for="release-schedule-later">Schedule for later (UTC / Zulu)</label>
                    </div>
                    <p class="small text-muted mt-2 mb-0" data-schedule-help>Publish immediately after both packages finish uploading.</p>
                    @if(!config('sector-files.scheduling_enabled'))
                        <p class="small text-warning mt-2 mb-0">Scheduling is not enabled yet. You can publish now; the website administrator must activate automatic publishing before scheduling is available.</p>
                    @endif
                    <div id="release-schedule-fields" class="mt-3" data-schedule-fields hidden>
                        <div class="form-row" style="max-width: 440px">
                            <div class="form-group col-sm-7">
                                <label for="release-date">Publication date (UTC)</label>
                                <input id="release-date" type="date" class="form-control" data-schedule-date disabled>
                            </div>
                            <div class="form-group col-sm-5">
                                <label for="release-time">Time (24-hour UTC)</label>
                                <div class="d-flex align-items-center">
                                    <select id="release-time" class="form-control" data-schedule-hour aria-label="UTC hour" disabled>
                                        @for($hour = 0; $hour < 24; $hour++)<option value="{{ sprintf('%02d', $hour) }}">{{ sprintf('%02d', $hour) }}</option>@endfor
                                    </select>
                                    <span class="mx-1" aria-hidden="true">:</span>
                                    <select class="form-control" data-schedule-minute aria-label="UTC minute" disabled>
                                        @for($minute = 0; $minute < 60; $minute++)<option value="{{ sprintf('%02d', $minute) }}">{{ sprintf('%02d', $minute) }}</option>@endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <p class="small mb-1" data-schedule-summary aria-live="polite"></p>
                        <p class="small text-muted mb-0">Choose the date from the calendar and the time in UTC, not local time. Follow the SOP: after 0900Z on publication day. Packages upload now and stay hidden until publication. Automatic publishing is checked every minute.</p>
                    </div>
                    <input type="hidden" name="publish_at" value="{{ old('publish_at') }}" data-schedule-value disabled>
                    <noscript><p class="text-warning mt-2">Enable JavaScript to schedule a release. With JavaScript disabled, uploads publish immediately.</p></noscript>
                </div>
                <div class="form-check mb-3"><input id="release-announce" type="checkbox" class="form-check-input" name="announce" value="1" @checked(old('announce')) @disabled(!$discordConfigured)><label for="release-announce" class="form-check-label">Post Discord announcement</label></div>
                @if(!$discordConfigured)<p class="text-muted small">Discord announcements can be enabled later by the website administrator.</p>@endif
                <button class="btn btn-primary" type="submit" data-publish-button>Upload and publish now</button>
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
<script src="{{ asset('js/sector-files-schedule.js') }}" defer></script>
@endsection
