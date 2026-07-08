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
            <p class="roster-page-sub">{{ $quarterLabel }} &mdash; hours logged against each member's activity requirement</p>
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
                    <th class="sortable" data-col="4">Hours Logged <i class="fas fa-sort sort-icon"></i></th>
                    <th>Requirement</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($members as $member)
                <tr>
                    <td><strong class="roster-cid-plain">{{ $member->cid }}</strong></td>
                    <td class="roster-name">{{ $member->user ? $member->user->fullName('FL') : $member->full_name }}</td>
                    <td class="text-capitalize">{{ $member->status }}</td>
                    <td><span class="rating-badge">{{ $member->rating }}</span></td>
                    <td data-sort="{{ $member->currency }}">{{ decimal_to_hm($member->currency) }}</td>
                    <td>{{ $member->requirement === null ? 'N/A' : decimal_to_hm($member->requirement) }}</td>
                    <td>
                        @if ($member->meets_requirement === null)
                            <span class="status-badge">N/A</span>
                        @elseif ($member->meets_requirement)
                            <span class="status-badge status-active">Meets requirement</span>
                        @else
                            <span class="status-badge status-inactive">Below requirement</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No active roster members found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <p class="roster-no-results" style="display:none;">No controllers match your search.</p>
    </div>
</div>

<style>
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
</style>

<script>
$(document).ready(function () {
    // ── Live search ────────────────────────────────────────────
    $('#activitySearch').on('input', function () {
        var q = $(this).val().trim().toLowerCase();
        var rows = $('#activityTable tbody tr');
        var visible = 0;
        rows.each(function () {
            var text = $(this).text().toLowerCase();
            var show = !q || text.indexOf(q) > -1;
            $(this).toggle(show);
            if (show) visible++;
        });
        $('.roster-no-results').toggle(visible === 0);
    });

    // ── Column sort ────────────────────────────────────────────
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
        var rows = tbody.find('tr').toArray();
        rows.sort(function (a, b) {
            var aCell = $(a).find('td').eq(col);
            var bCell = $(b).find('td').eq(col);
            if (col === 4) {
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
        tbody.append(rows);
    });

    // Default sort by hours logged asc (worst first)
    $('#activityTable .sortable[data-col="4"]').trigger('click');
});
</script>

@stop
