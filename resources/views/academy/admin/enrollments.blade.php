@extends('layouts.master')
@section('title', 'Academy Enrollments - Winnipeg FIR')
@section('content')
@include('academy._styles')
<style>
    .academy-roster-tools { max-width: 330px; }
    .academy-scroll-area { max-height: 430px; overflow-y: auto; border: 1px solid var(--academy-border, rgba(127,127,127,.2)); border-radius: .45rem; }
    .academy-scroll-area table { margin-bottom: 0; }
    .academy-scroll-area thead th { position: sticky; top: 0; z-index: 2; background: var(--academy-panel, #fff); box-shadow: 0 1px 0 rgba(127,127,127,.18); }
    .academy-assignment-scroll { max-height: 560px; overflow-y: auto; padding-right: .35rem; }
    [data-academy-empty] { display: none; }
    @media (max-width: 767px) { .academy-roster-tools { max-width: none; width: 100%; margin-top: .75rem; } }
    html[data-theme="dark"] .academy-scroll-area thead th { background: #20242b; }
</style>

<div class="academy-hero"><div class="container">
    <a href="{{ route('academy.admin.hub') }}" class="academy-hero-link"><i class="fas fa-arrow-left"></i> Academy</a>
    <h1>Academy Enrollments</h1>
    <p class="mb-0" style="color:rgba(255,255,255,.65)">Manage course access and synchronize rating-based assignments for Winnipeg home and visiting controllers.</p>
</div></div>

<div class="academy-body"><div class="container">
    <div class="academy-panel mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start">
            <div class="pr-3">
                <h5 class="font-weight-bold mb-1"><i class="fas fa-sync-alt mr-2"></i>VATCAN Academy Sync</h5>
                <p class="academy-muted mb-2">Pulls home and visiting controllers from the VATCAN Winnipeg roster, matches accounts by CID, and applies the correct rating ladder.</p>
                <p class="academy-muted mb-0">Visitors require S3 or higher. Current source: <strong>{{ $vatcanSource }}</strong>.</p>
            </div>
            <div class="text-lg-right mt-3 mt-lg-0">
                @if($vatcanConfigured)
                    <span class="badge badge-success px-3 py-2"><i class="fas fa-plug mr-1"></i> API configured</span>
                @else
                    <span class="badge badge-info px-3 py-2"><i class="fas fa-globe mr-1"></i> Public roster ready</span>
                @endif
            </div>
        </div>

        <hr>
        @if($lastSync)
            <div class="row mb-3">
                <div class="col-md-3 mb-2"><small class="academy-muted d-block">Last sync</small><strong>{{ $lastSync->created_at->format('M j, Y g:i A') }}</strong></div>
                <div class="col-md-2 mb-2"><small class="academy-muted d-block">Status</small><strong>{{ strtoupper($lastSync->status) }}</strong></div>
                <div class="col-md-2 mb-2"><small class="academy-muted d-block">Home controllers</small><strong>{{ $lastSync->controllers_found }}</strong></div>
                <div class="col-md-2 mb-2"><small class="academy-muted d-block">Visitors</small><strong>{{ $lastSync->visitors_ignored }}</strong></div>
                <div class="col-md-2 mb-2"><small class="academy-muted d-block">Matched accounts</small><strong>{{ $lastSync->users_matched }}</strong></div>
                <div class="col-md-1 mb-2"><small class="academy-muted d-block">Pending</small><strong>{{ $lastSync->pending_cids }}</strong></div>
            </div>
            @if($lastSync->message)<div class="academy-muted mb-3">{{ $lastSync->message }}</div>@endif
        @else
            <div class="academy-muted mb-3">No VATCAN Academy sync has been run yet.</div>
        @endif

        <form method="POST" action="{{ route('academy.enrollments.vatcan-sync') }}" onsubmit="return confirm('Run the VATCAN Academy sync now? This updates rating-based access for home and visiting controllers.');">
            @csrf
            <button class="btn btn-primary" {{ $vatcanAvailable ? '' : 'disabled' }}><i class="fas fa-sync-alt mr-1"></i> Sync VATCAN Roster Now</button>
            <small class="academy-muted ml-2">The automatic sync also runs hourly.</small>
        </form>
    </div>

    @php($courseLookup = $courses->keyBy('slug'))

    <div class="academy-panel mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div><h5 class="font-weight-bold mb-0">Current VATCAN Home-Controller Snapshot</h5><small class="academy-muted">Read from the last successful Academy sync.</small></div>
            <div class="academy-roster-tools"><input class="form-control form-control-sm" type="search" data-academy-filter="homeRosterRows" placeholder="Search name, CID, rating, or course…" aria-label="Search home controllers"></div>
        </div>
        <div class="d-flex justify-content-end mb-2"><span class="academy-muted">{{ $homeMembers->count() }} members</span></div>
        <div class="academy-scroll-area table-responsive">
            <table class="table table-sm"><thead><tr><th>CID</th><th>Name</th><th>Rating</th><th>Website Account</th><th>Academy Access</th></tr></thead>
                <tbody id="homeRosterRows">
                @foreach($homeMembers as $member)
                    <tr><td><strong>{{ $member->cid }}</strong></td><td>{{ trim(($member->first_name ?? '').' '.($member->last_name ?? '')) ?: '—' }}</td><td><span class="badge badge-secondary">{{ $member->rating_label ?: 'Unknown' }}</span></td><td>@if($member->user_id)<span class="badge badge-success">Matched</span>@else<span class="badge badge-warning">Pending first login</span>@endif</td><td>@forelse($member->entitled_course_slugs ?? [] as $slug) @php($mappedCourse = $courseLookup->get($slug))<span class="badge badge-light border mb-1">{{ $mappedCourse ? $mappedCourse->title : $slug }}</span> @empty<span class="academy-muted">No rating entitlement</span>@endforelse</td></tr>
                @endforeach
                <tr data-academy-empty><td colspan="5" class="text-center academy-muted py-4">No matching home controllers.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="academy-panel mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div><h5 class="font-weight-bold mb-0">Current VATCAN Visiting-Controller Snapshot</h5><small class="academy-muted">S3 and higher receive the rating ladder plus every Visitor Course.</small></div>
            <div class="academy-roster-tools"><input class="form-control form-control-sm" type="search" data-academy-filter="visitorRosterRows" placeholder="Search name, CID, rating, or course…" aria-label="Search visiting controllers"></div>
        </div>
        <div class="d-flex justify-content-end mb-2"><span class="academy-muted">{{ $visitorMembers->count() }} visitors</span></div>
        <div class="academy-scroll-area table-responsive">
            <table class="table table-sm"><thead><tr><th>CID</th><th>Name</th><th>Rating</th><th>Eligibility</th><th>Website Account</th><th>Academy Access</th></tr></thead>
                <tbody id="visitorRosterRows">
                @foreach($visitorMembers as $member)
                    <tr><td><strong>{{ $member->cid }}</strong></td><td>{{ trim(($member->first_name ?? '').' '.($member->last_name ?? '')) ?: '—' }}</td><td><span class="badge badge-secondary">{{ $member->rating_label ?: 'Unknown' }}</span></td><td>@if((int)$member->rating_id >= 4)<span class="badge badge-success">Eligible</span>@else<span class="badge badge-danger">Below S3</span>@endif</td><td>@if($member->user_id)<span class="badge badge-success">Matched</span>@else<span class="badge badge-warning">Pending first login</span>@endif</td><td>@forelse($member->entitled_course_slugs ?? [] as $slug) @php($mappedCourse = $courseLookup->get($slug))<span class="badge {{ $mappedCourse && $mappedCourse->visitor_only ? 'badge-info' : 'badge-light border' }} mb-1">{{ $mappedCourse ? $mappedCourse->title : $slug }}</span> @empty<span class="academy-muted">No visitor entitlement</span>@endforelse</td></tr>
                @endforeach
                <tr data-academy-empty><td colspan="6" class="text-center academy-muted py-4">No matching visiting controllers.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4"><div class="academy-panel">
            <h5 class="font-weight-bold">Manual course override</h5>
            <p class="academy-muted">Visitor Courses can only be granted to an eligible S3-or-higher visitor.</p>
            <form method="POST" action="{{ route('academy.enrollments.store') }}">@csrf
                <div class="form-group"><label>Student</label><select class="form-control" name="user_id" required><option value="">Choose a student…</option>@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->fullName('FL') }} ({{ $student->id }})</option>@endforeach</select></div>
                <div class="form-group"><label>Course</label><select class="form-control" name="course_id" required><option value="">Choose a course…</option>@foreach($courses as $course)<option value="{{ $course->id }}">{{ $course->title }}{{ $course->visitor_only ? ' — Visitor Course' : '' }}{{ $course->published ? '' : ' — draft' }}</option>@endforeach</select></div>
                <button class="btn btn-primary btn-block"><i class="fas fa-user-plus mr-1"></i> Grant manual access</button>
            </form>
        </div>
        @if(Auth::user()->permissions >= 5)<a class="btn btn-outline-primary btn-block mb-3" href="{{ route('academy.admin.index') }}"><i class="fas fa-cog mr-1"></i> Open Academy Editor</a>@endif
        </div>

        <div class="col-lg-8"><div class="academy-panel">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2"><h5 class="font-weight-bold mb-0">Active assignments</h5><span class="academy-muted">{{ $enrollments->count() }} total</span></div>
            <input class="form-control form-control-sm mb-3" type="search" data-academy-filter="activeAssignmentRows" placeholder="Search student, CID, course, or source…" aria-label="Search active assignments">
            <div id="activeAssignmentRows" class="academy-assignment-scroll">
                @forelse($enrollments as $enrollment)
                    <div class="academy-list-row" data-academy-row><div><strong>{{ $enrollment->user ? $enrollment->user->fullName('FL') : 'Deleted user' }}</strong> <span class="academy-muted">{{ $enrollment->user_id }}</span><div>{{ $enrollment->course ? $enrollment->course->title : 'Deleted course' }} @if($enrollment->course && $enrollment->course->visitor_only)<span class="badge badge-info">VISITOR COURSE</span>@endif <span class="badge badge-{{ $enrollment->source === 'vatcan' ? 'info' : 'secondary' }}">{{ strtoupper($enrollment->source ?? 'manual') }}</span></div><small class="academy-muted">Assigned {{ optional($enrollment->assigned_at)->diffForHumans() }}@if($enrollment->assigner) by {{ $enrollment->assigner->fullName('FL') }}@endif @if($enrollment->source_rating_id) · rating {{ $enrollment->source_rating_id }}@endif</small></div><form method="POST" action="{{ route('academy.enrollments.destroy', $enrollment) }}" onsubmit="return confirm('Remove this student’s access to the course?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form></div>
                @empty<div class="text-center academy-muted py-4">No active course assignments yet.</div>@endforelse
                <div data-academy-empty class="text-center academy-muted py-4">No matching active assignments.</div>
            </div>
        </div></div>
    </div>
</div></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-academy-filter]').forEach(function (input) {
        var container = document.getElementById(input.getAttribute('data-academy-filter'));
        if (!container) return;
        var rows = Array.prototype.slice.call(container.querySelectorAll('[data-academy-row], tr:not([data-academy-empty])'));
        var empty = container.querySelector('[data-academy-empty]');
        input.addEventListener('input', function () {
            var query = input.value.trim().toLowerCase();
            var visible = 0;
            rows.forEach(function (row) {
                var show = !query || row.textContent.toLowerCase().indexOf(query) !== -1;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (empty) empty.style.display = visible ? 'none' : '';
        });
        input.dispatchEvent(new Event('input'));
    });
});
</script>
@stop
