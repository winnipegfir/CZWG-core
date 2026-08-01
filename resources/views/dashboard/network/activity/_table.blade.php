{{-- Expects: $tableId, $members --}}
<div class="roster-table-wrap">
    <table class="table roster-table sortable-table" id="{{ $tableId }}">
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
                <th class="sortable" data-col="7">Result <i class="fas fa-sort sort-icon"></i></th>
                <th>Email</th>
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
                @php
                    // Worst-first rank, matching the default hours sort.
                    if ($member->vatsim_data_unavailable) {
                        $resultRank = 1;
                    } elseif (! $member->has_any_endorsement || $member->meets_requirement === null) {
                        $resultRank = 2;
                    } elseif (! $member->meets_requirement) {
                        $resultRank = 0;
                    } else {
                        $resultRank = 3;
                    }
                @endphp
                <td data-sort="{{ $resultRank }}">
                    @if ($member->vatsim_data_unavailable)
                        <span class="status-badge activity-status-unknown" title="VATSIM limits how fast we can pull session history, so this fills in gradually. No action needed.">
                            <i class="fas fa-circle-notch fa-spin"></i> Loading&hellip;
                        </span>
                    @elseif (! $member->has_any_endorsement)
                        <span class="status-badge" title="Not endorsed for any position yet, so there's no activity requirement to meet.">Not yet endorsed</span>
                    @elseif ($member->meets_requirement === null)
                        <span class="status-badge">N/A</span>
                    @elseif ($member->meets_requirement)
                        <span class="status-badge status-active">Meets requirement</span>
                    @else
                        <span class="status-badge status-inactive">Below requirement</span>
                    @endif
                    @if (! $member->vatsim_data_unavailable && $member->fails_percentage_only)
                        <div class="activity-result-note">Only {{ round($member->fir_percentage * 100) }}% in-FIR, needs 50%</div>
                    @elseif (! $member->vatsim_data_unavailable && $member->non_fir_hours > 0)
                        <div class="activity-result-note">{{ decimal_to_hm($member->non_fir_hours) }} non-FIR, didn't count</div>
                    @elseif (! $member->vatsim_data_unavailable && $member->off_tier_hours > 0)
                        <div class="activity-result-note">{{ decimal_to_hm($member->off_tier_hours) }} at wrong position tier, didn't count</div>
                    @endif
                </td>
                <td class="roster-email-cell">
                    @if ($member->user && $member->user->email)
                        <button type="button" class="roster-email-copy-btn" title="{{ $member->user->email }} (click to copy)" data-email="{{ $member->user->email }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    @else
                        <span class="text-muted">&mdash;</span>
                    @endif
                </td>
                <td><i class="fas fa-chevron-down activity-expand-icon"></i></td>
            </tr>
            <tr class="activity-breakdown-row" id="breakdown-{{ $member->id }}" style="display:none;">
                <td colspan="11">
                    @if ($member->vatsim_data_unavailable)
                        <span class="text-muted" style="font-size:0.85rem;"><i class="fas fa-circle-notch fa-spin"></i> Still pulling this controller's session history from VATSIM &mdash; it's rate-limited, so this fills in over the next few minutes.</span>
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
                <td colspan="11" class="text-center text-muted py-4">No roster members found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <p class="roster-no-results" style="display:none;">No controllers match your search.</p>
</div>
