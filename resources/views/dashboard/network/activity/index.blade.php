@extends('layouts.master')

@section('title', 'Controller Activity — Winnipeg FIR')
@section('description', "Quarterly controller activity vs. policy requirements")

@section('content')

<div class="container roster-page-wrap">

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
            <strong>Requirement / Result is judged on Qualifying Hours only</strong> &mdash; the <strong>Total Hours</strong> column is just the raw total
            for the selected date range and can include time that doesn't count. <strong>Non-FIR Hours</strong> is time logged on a position outside
            Winnipeg FIR (e.g. a visiting session at Toronto Center); it never counts toward this requirement no matter how much of it there is.
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
                <div class="activity-summary-value" style="color:#92400e;">{{ $dataUnavailable }}</div>
                <div class="activity-summary-label">Data Unavailable</div>
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

    <div class="roster-table-wrap">
        <table class="table roster-table sortable-table" id="activityTable">
            <thead>
                <tr>
                    <th class="sortable" data-col="0">CID <i class="fas fa-sort sort-icon"></i></th>
                    <th class="sortable" data-col="1">Controller Name <i class="fas fa-sort sort-icon"></i></th>
                    <th class="sortable" data-col="2">Status <i class="fas fa-sort sort-icon"></i></th>
                    <th class="sortable" data-col="3">Rating <i class="fas fa-sort sort-icon"></i></th>
                    <th class="sortable" data-col="4">Total Hours <i class="fas fa-sort sort-icon"></i></th>
                    <th class="sortable" data-col="5">Qualifying Hours <i class="fas fa-sort sort-icon"></i></th>
                    <th class="sortable" data-col="6">Non-FIR Hours <i class="fas fa-sort sort-icon"></i></th>
                    <th>Requirement</th>
                    <th>Result</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($members as $member)
                <tr class="activity-row" data-toggle-target="#breakdown-{{ $member->id }}">
                    <td><strong class="roster-cid-plain">{{ $member->cid }}</strong></td>
                    <td class="roster-name">{{ $member->user ? trim($member->user->fname.' '.$member->user->lname) : $member->full_name }}</td>
                    <td class="text-capitalize">{{ $member->status }}</td>
                    <td><span class="rating-badge">{{ $member->rating_short_name ?? 'N/A' }}</span></td>
                    <td data-sort="{{ $member->total_logged_hours }}">{{ decimal_to_hm($member->total_logged_hours) }}</td>
                    <td data-sort="{{ $member->qualifying_hours }}">{{ decimal_to_hm($member->qualifying_hours) }}</td>
                    <td data-sort="{{ $member->non_fir_hours }}">{{ decimal_to_hm($member->non_fir_hours) }}</td>
                    <td>{{ $member->requirement === null ? 'N/A' : decimal_to_hm($member->requirement) }}</td>
                    <td>
                        @if ($member->vatsim_data_unavailable)
                            <span class="status-badge activity-status-unknown" title="Couldn't reach VATSIM's session history for this CID. Reload to retry.">
                                <i class="fas fa-triangle-exclamation"></i> Data unavailable
                            </span>
                        @elseif ($member->meets_requirement === null)
                            <span class="status-badge">N/A</span>
                        @elseif ($member->meets_requirement)
                            <span class="status-badge status-active">Meets requirement</span>
                        @else
                            <span class="status-badge status-inactive">Below requirement</span>
                        @endif
                        @if (! $member->vatsim_data_unavailable && $member->non_fir_hours > 0)
                            <div class="activity-result-note">{{ decimal_to_hm($member->non_fir_hours) }} non-FIR, didn't count</div>
                        @endif
                    </td>
                    <td><i class="fas fa-chevron-down activity-expand-icon"></i></td>
                </tr>
                <tr class="activity-breakdown-row" id="breakdown-{{ $member->id }}" style="display:none;">
                    <td colspan="10">
                        @if ($member->vatsim_data_unavailable)
                            <span class="text-muted" style="font-size:0.85rem;"><i class="fas fa-triangle-exclamation text-warning"></i> Couldn't fetch this controller's session history from VATSIM (their CID may currently be online, or the request timed out). Reload the page to retry.</span>
                        @elseif (empty($member->position_breakdown))
                            <span class="text-muted" style="font-size:0.85rem;">No sessions logged in this date range.</span>
                        @else
                            <div class="activity-breakdown-chips">
                                @foreach ($member->position_breakdown as $callsign => $data)
                                    <span class="activity-chip {{ $data['qualifies'] ? 'activity-chip-ok' : 'activity-chip-bad' }}">
                                        <i class="fas {{ $data['qualifies'] ? 'fa-check' : 'fa-xmark' }}"></i>
                                        {{ $callsign }} <span class="activity-chip-category">({{ $data['category'] }})</span>: {{ decimal_to_hm($data['hours']) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">No active roster members found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <p class="roster-no-results" style="display:none;">No controllers match your search.</p>
    </div>
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
</style>

<script>
$(document).ready(function () {
    // ── Expand/collapse position breakdown ─────────────────────
    $(document).on('click', '.activity-row', function () {
        var target = $($(this).data('toggle-target'));
        target.toggle();
        $(this).toggleClass('expanded');
    });

    // ── Live search (matches against the row + its breakdown) ──
    $('#activitySearch').on('input', function () {
        var q = $(this).val().trim().toLowerCase();
        var visible = 0;
        $('#activityTable tbody tr.activity-row').each(function () {
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
        $('.roster-no-results').toggle(visible === 0);
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
            if (col === 4 || col === 5 || col === 6) {
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

    // Default sort by total hours asc (worst first)
    $('#activityTable .sortable[data-col="4"]').trigger('click');
});
</script>

@stop
