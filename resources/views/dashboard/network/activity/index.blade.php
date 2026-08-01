@extends('layouts.master')

@section('title', 'Controller Activity — Winnipeg FIR')
@section('description', "Quarterly controller activity vs. policy requirements")

@section('content')

<div class="container-fluid roster-page-wrap">

    {{-- Back link --}}
    <a href="{{ route('network.index') }}" class="dash-back-link">
        <i class="fas fa-arrow-left"></i> Network
    </a>

    {{-- Page header --}}
    <div class="roster-page-header mt-3">
        <div>
            <h1 class="roster-page-title">Controller Activity</h1>
            <p class="roster-page-sub">
                @if ($isCustomRange)
                    {{ $rangeStart->format('M j, Y') }} &ndash; {{ $rangeEnd->format('M j, Y') }} &mdash; hours logged against each member's activity requirement
                @else
                    {{ $quarterLabel }} (current quarter) &mdash; hours logged against each member's activity requirement
                @endif
            </p>
        </div>
        <form method="GET" action="{{ route('network.activity.index') }}" class="activity-range-form">
            <div class="form-group mb-0">
                <label for="activityStart">From</label>
                <input type="date" id="activityStart" name="start" class="form-control form-control-sm" value="{{ $rangeStart->format('Y-m-d') }}">
            </div>
            <div class="form-group mb-0">
                <label for="activityEnd">To</label>
                <input type="date" id="activityEnd" name="end" class="form-control form-control-sm" value="{{ $rangeEnd->format('Y-m-d') }}">
            </div>
            <button type="submit" class="btn btn-sm bg-czqo-blue-light">Apply</button>
            @if ($isCustomRange)
                <a href="{{ route('network.activity.index') }}" class="btn btn-sm btn-light">Reset to Quarter</a>
            @endif
        </form>
    </div>

    {{-- Policy note --}}
    <div class="activity-policy-note">
        <i class="fas fa-circle-info"></i>
        <div>
            <strong>Position policy:</strong> hours only keep a controller's certification current if they're worked at
            <strong>their own rating's position, or the tier directly below it</strong> &mdash; e.g. a C1 stays current by controlling
            <strong>CTR</strong> or <strong>APP/DEP</strong>, an S3 by controlling <strong>APP/DEP</strong> or <strong>TWR</strong>, and so on.
            A position the controller is individually marked <strong>Training, Solo, or Certified</strong> on (per the roster's per-position
            endorsements) also counts, even outside that tier range &mdash; e.g. an S2 marked Training on APP/DEP gets credit for those hours too.
            <strong>Requirement / Result is judged on Qualifying Hours only</strong> &mdash; the <strong>Total Hours</strong> column is just the raw total
            for the selected date range and can include time that doesn't count. <strong>Non-FIR Hours</strong> is time logged on a position outside
            Winnipeg FIR (e.g. a visiting session at Toronto Center); it never counts toward this requirement no matter how much of it there is.
            <strong>Home controllers and instructors</strong> also need at least <strong>50% of their total logged hours</strong> to be inside Winnipeg FIR
            &mdash; 3+ qualifying hours worked mostly at a foreign FIR still shows as below requirement. Visitors have no such split requirement, just the flat hour minimum.
            Requirements are checked quarterly; use the date range above to look at a different period.
            Click <i class="fas fa-chevron-down"></i> on a row to see the breakdown by position.
            Session data is pulled live from VATSIM's own connection history, not our local activity log, so out-of-FIR sessions are counted correctly.
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="activity-summary-row">
        <div class="activity-summary-card">
            <div class="activity-summary-value">{{ $totalMembers }}</div>
            <div class="activity-summary-label">Roster Members</div>
        </div>
        <div class="activity-summary-card">
            <div class="activity-summary-value text-success">{{ $meetingRequirement }}</div>
            <div class="activity-summary-label">Meeting Requirement</div>
        </div>
        <div class="activity-summary-card">
            <div class="activity-summary-value text-danger">{{ $belowRequirement }}</div>
            <div class="activity-summary-label">Below Requirement</div>
        </div>
        @if ($dataUnavailable > 0)
            <div class="activity-summary-card">
                <div class="activity-summary-value activity-summary-value-warning"><i class="fas fa-circle-notch fa-spin" style="font-size:1.1rem;"></i> {{ $dataUnavailable }}</div>
                <div class="activity-summary-label">Still Loading</div>
            </div>
        @endif
    </div>

    {{-- Legend + Search --}}
    <div class="roster-legend-row">
        <div class="roster-legend">
            <span class="status-badge status-active">Meets requirement</span>
            <span class="status-badge status-inactive">Below requirement</span>
        </div>
        <div class="roster-search-wrap">
            <i class="fas fa-search roster-search-icon"></i>
            <input type="text" id="activitySearch" class="roster-search-input" placeholder="Search name or CID&hellip;" autocomplete="off">
        </div>
    </div>

    <h2 class="activity-section-title">Home Controllers</h2>
    @include('dashboard.network.activity._table', ['tableId' => 'homeActivityTable', 'members' => $homeMembers])

    <h2 class="activity-section-title">Visiting Controllers</h2>
    @include('dashboard.network.activity._table', ['tableId' => 'visitActivityTable', 'members' => $visitingMembers])
</div>

<style>
.activity-range-form {
    display: flex;
    align-items: flex-end;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-left: auto;
}

.activity-range-form label {
    font-size: 0.72rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.2rem;
    display: block;
}

.activity-summary-row {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.activity-summary-card {
    flex: 1 1 160px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    text-align: center;
    background: #fff;
}

.activity-summary-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #122b44;
    line-height: 1.2;
}

.activity-summary-value-warning {
    color: #92400e;
}

.activity-summary-label {
    font-size: 0.8rem;
    color: #64748b;
    margin-top: 0.2rem;
}

.activity-policy-note {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    background: #eff6ff;
    border: 1.5px solid #bfdbfe;
    border-radius: 10px;
    padding: 0.85rem 1.1rem;
    font-size: 0.85rem;
    color: #1e3a5f;
    margin-bottom: 1.25rem;
}

.activity-policy-note > i {
    color: #1d4ed8;
    margin-top: 0.15rem;
}

.activity-section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #122b44;
    margin: 1.75rem 0 0.75rem;
}

html[data-theme="dark"] .activity-section-title {
    color: #d7dade !important;
}

.activity-row {
    cursor: pointer;
}

.activity-expand-icon {
    color: #94a3b8;
    font-size: 0.75rem;
    transition: transform 0.15s;
}

.activity-row.expanded .activity-expand-icon {
    transform: rotate(180deg);
}

.activity-breakdown-row td {
    background: #f8fafc;
    padding: 0.75rem 1rem !important;
    text-align: left !important;
}

.activity-breakdown-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.activity-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
}

.activity-chip-ok {
    background: #dcfce7;
    color: #15803d;
}

.activity-chip-bad {
    background: #fee2e2;
    color: #b91c1c;
}

.activity-chip-category {
    font-weight: 400;
    opacity: 0.75;
}

.activity-status-unknown {
    background: #fef3c7;
    color: #92400e;
}

.activity-result-note {
    font-size: 0.7rem;
    color: #94a3b8;
    margin-top: 0.25rem;
    white-space: nowrap;
}

.roster-page-wrap {
    max-width: 1600px;
    margin-left: auto;
    margin-right: auto;
}

.roster-table-wrap {
    overflow-x: auto !important;
}

.roster-email-cell {
    white-space: nowrap;
    width: 1px;
}

.roster-email-copy-btn {
    border: none;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    padding: 0.2rem 0.35rem;
    border-radius: 6px;
    font-size: 0.78rem;
    line-height: 1;
}

.roster-email-copy-btn:hover {
    color: #1d4ed8;
    background: #eff6ff;
}

.roster-email-copy-btn.copied {
    color: #15803d;
}

html[data-theme="dark"] .roster-email-copy-btn {
    color: #7a828e;
}

html[data-theme="dark"] .roster-email-copy-btn:hover {
    color: #6ea8e6 !important;
    background: #17283d !important;
}

html[data-theme="dark"] .roster-email-copy-btn.copied {
    color: #5fd88a !important;
}

html[data-theme="dark"] .activity-range-form label {
    color: #9aa1ab !important;
}

html[data-theme="dark"] .activity-policy-note {
    background: #17283d !important;
    border-color: #2f537a !important;
    color: #c8ddf2 !important;
}

html[data-theme="dark"] .activity-policy-note > i {
    color: #6ea8e6 !important;
}

html[data-theme="dark"] .activity-summary-card {
    background: #1f232a !important;
    border-color: #33383f !important;
}

html[data-theme="dark"] .activity-summary-value {
    color: #d7dade !important;
}

html[data-theme="dark"] .activity-summary-value.text-success {
    color: #5fd88a !important;
}

html[data-theme="dark"] .activity-summary-value.text-danger {
    color: #f1919b !important;
}

html[data-theme="dark"] .activity-summary-value-warning {
    color: #f0c96a !important;
}

html[data-theme="dark"] .activity-summary-label {
    color: #9aa1ab !important;
}

html[data-theme="dark"] .activity-breakdown-row td {
    background: #191d23 !important;
    color: #d7dade !important;
}

html[data-theme="dark"] .activity-chip-ok {
    background: #143e24 !important;
    color: #5fd88a !important;
}

html[data-theme="dark"] .activity-chip-bad {
    background: #3a1418 !important;
    color: #f1919b !important;
}

html[data-theme="dark"] .activity-status-unknown {
    background: #3d3212 !important;
    color: #f0c96a !important;
}

html[data-theme="dark"] .activity-result-note {
    color: #9aa1ab !important;
}
</style>

<script>
$(document).ready(function () {
    // ── Auto-refresh while VATSIM data is still being pulled in ──
    // The cache-warm cron fills rows in gradually (rate-limited by VATSIM), so
    // reload periodically instead of making staff do it by hand.
    @if ($dataUnavailable > 0)
    setTimeout(function () { window.location.reload(); }, 20000);
    @endif

    // ── Expand/collapse position breakdown ─────────────────────
    $(document).on('click', '.activity-row', function (e) {
        if ($(e.target).closest('.roster-email-copy-btn').length) {
            return;
        }
        var target = $($(this).data('toggle-target'));
        target.toggle();
        $(this).toggleClass('expanded');
    });

    // ── Copy email to clipboard ─────────────────────────────────
    $(document).on('click', '.roster-email-copy-btn', function (e) {
        e.stopPropagation();
        var btn = $(this);
        var email = btn.data('email');
        navigator.clipboard.writeText(email).then(function () {
            var icon = btn.find('i');
            icon.removeClass('fa-copy').addClass('fa-check');
            btn.addClass('copied');
            setTimeout(function () {
                icon.removeClass('fa-check').addClass('fa-copy');
                btn.removeClass('copied');
            }, 1500);
        });
    });

    // ── Live search (matches against the row + its breakdown) ──
    $('#activitySearch').on('input', function () {
        var q = $(this).val().trim().toLowerCase();
        $('.roster-table-wrap').each(function () {
            var wrap = $(this);
            var visible = 0;
            wrap.find('tbody tr.activity-row').each(function () {
                var row = $(this);
                var breakdown = $(row.data('toggle-target'));
                var text = (row.text() + ' ' + breakdown.text()).toLowerCase();
                var show = !q || text.indexOf(q) > -1;
                row.toggle(show);
                // Keep breakdown row hidden unless the user has expanded it
                if (!show) {
                    breakdown.hide();
                    row.removeClass('expanded');
                }
                if (show) visible++;
            });
            wrap.find('.roster-no-results').toggle(visible === 0);
        });
    });

    // ── Column sort (keeps each row paired with its breakdown row) ──
    $(document).on('click', '.sortable-table .sortable', function () {
        var th = $(this);
        var table = th.closest('table');
        var col = parseInt(th.data('col'));
        var asc = th.data('dir') !== 'asc';

        table.find('.sortable').each(function () {
            $(this).data('dir', '');
            $(this).find('.sort-icon').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
        });

        th.data('dir', asc ? 'asc' : 'desc');
        th.find('.sort-icon').removeClass('fa-sort').addClass(asc ? 'fa-sort-up' : 'fa-sort-down');

        var tbody = table.find('tbody');
        var rows = tbody.find('tr.activity-row').toArray();
        rows.sort(function (a, b) {
            var aCell = $(a).find('td').eq(col);
            var bCell = $(b).find('td').eq(col);
            if (col === 4 || col === 5 || col === 6 || col === 7) {
                var aNum = parseFloat(aCell.data('sort'));
                var bNum = parseFloat(bCell.data('sort'));
                return asc ? aNum - bNum : bNum - aNum;
            }
            var aVal = aCell.text().trim();
            var bVal = bCell.text().trim();
            if (col === 0) {
                return asc ? parseInt(aVal) - parseInt(bVal) : parseInt(bVal) - parseInt(aVal);
            }
            return asc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
        });
        $.each(rows, function (i, row) {
            var breakdown = $($(row).data('toggle-target'));
            tbody.append(row).append(breakdown);
        });
    });

    // Default sort by total hours asc (worst first), per table
    $('.sortable-table').each(function () {
        $(this).find('.sortable[data-col="4"]').trigger('click');
    });
});
</script>

@stop
