@extends('layouts.master')

@section('title', 'Inactivity Warnings — Winnipeg FIR')
@section('description', 'Track quarterly inactivity warnings sent to roster members')

@section('content')

<div class="container-fluid roster-page-wrap">

    <a href="{{ route('network.activity.index') }}" class="dash-back-link">
        <i class="fas fa-arrow-left"></i> Controller Activity
    </a>

    <div class="roster-page-header mt-3">
        <div>
            <h1 class="roster-page-title">Inactivity Warnings</h1>
            <p class="roster-page-sub">
                Every quarter, {{ config('app.name') }}'s currency check flags anyone below their hour requirement here.
                It does <strong>not</strong> email them directly &mdash; use this to track whether the 14-day warning
                actually went out, and how it was resolved.
            </p>
        </div>
        @if ($quarters->count() > 1)
            <form method="GET" action="{{ route('network.warnings.index') }}" class="activity-range-form">
                <div class="form-group mb-0">
                    <label for="quarterSelect">Quarter</label>
                    <select id="quarterSelect" name="quarter" class="warnings-select browser-default" onchange="this.form.submit()">
                        @foreach ($quarters as $quarter)
                            <option value="{{ $quarter }}" {{ $quarter === $selectedQuarter ? 'selected' : '' }}>{{ $quarter }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="roster-table-wrap">
        <table class="table warnings-table" id="warningsTable">
            <thead>
                <tr>
                    <th>CID</th>
                    <th>Controller</th>
                    <th>Hours</th>
                    <th>Warning Sent</th>
                    <th>Sent By</th>
                    <th>Deadline</th>
                    <th>Result</th>
                    <th>Notes</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($warnings as $warning)
                <tr>
                    <td><strong class="roster-cid-plain">{{ $warning->cid }}</strong></td>
                    <td class="roster-name">{{ $warning->member_name }}</td>
                    <td data-sort="{{ $warning->hours_logged }}">{{ decimal_to_hm($warning->hours_logged) }} / {{ decimal_to_hm($warning->hours_required) }}</td>

                    <td>
                        <form id="wform-{{ $warning->id }}" method="POST" action="{{ route('network.warnings.update', $warning) }}">
                            @csrf
                        </form>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="sent-{{ $warning->id }}"
                                   form="wform-{{ $warning->id }}" name="warning_sent" value="1"
                                   {{ $warning->warning_sent ? 'checked' : '' }}>
                            <label class="custom-control-label" for="sent-{{ $warning->id }}">Sent</label>
                        </div>
                    </td>

                    <td style="white-space:nowrap;">
                        @if ($warning->warning_sent)
                            {{ $warning->warningSentBy ? $warning->warningSentBy->fullName('FL') : 'Unknown' }}
                            <div class="activity-result-note">{{ $warning->warning_sent_at->format('M j, Y') }}</div>
                        @else
                            &mdash;
                        @endif
                    </td>

                    <td style="white-space:nowrap;">
                        @if ($warning->warning_sent && $warning->deadline)
                            {{ $warning->deadline->format('M j, Y') }}
                        @else
                            &mdash;
                        @endif
                    </td>

                    <td>
                        <select form="wform-{{ $warning->id }}" name="result" class="warnings-select browser-default">
                            @foreach (\App\Models\Network\ActivityWarning::RESULTS as $key => $label)
                                <option value="{{ $key }}" {{ $warning->result === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <textarea form="wform-{{ $warning->id }}" name="notes" class="warnings-notes" rows="1" placeholder="Notes&hellip;">{{ $warning->notes }}</textarea>
                    </td>

                    <td class="warnings-actions">
                        <button type="submit" form="wform-{{ $warning->id }}" class="btn btn-sm bg-czqo-blue-light">Save</button>
                        <form method="POST" action="{{ route('network.warnings.destroy', $warning) }}"
                              onsubmit="return confirm('Delete this warning for {{ $warning->member_name }}? This can\'t be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="warnings-delete-btn" title="Delete this entry">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">No warnings logged for this quarter.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.roster-page-wrap {
    max-width: 1600px;
    margin-left: auto;
    margin-right: auto;
}

.roster-table-wrap {
    overflow-x: auto !important;
}

.warnings-table {
    font-size: 0.9rem;
    border-collapse: collapse;
    border-spacing: 0;
    width: 100%;
    margin: 0;
}

.warnings-table thead th {
    background: #f8fafc;
    color: #64748b;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    padding: 0.7rem 1rem;
    border-bottom: 1.5px solid #e2e8f0;
    white-space: nowrap;
    text-align: left;
}

.warnings-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.1s;
}

.warnings-table tbody tr:last-child {
    border-bottom: none;
}

.warnings-table tbody tr:hover {
    background: #f8fafc;
}

.warnings-table tbody td {
    padding: 0.75rem 1rem;
    vertical-align: middle;
    text-align: left;
}

.warnings-select,
.warnings-notes {
    display: block;
    width: 100%;
    box-sizing: border-box;
    font-size: 0.85rem;
    color: #1e293b;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.4rem 0.6rem;
}

.warnings-select {
    min-width: 190px;
    height: auto;
}

.warnings-notes {
    resize: vertical;
    min-width: 180px;
}

.warnings-select:focus,
.warnings-notes:focus {
    outline: none;
    border-color: #6ea8e6;
    box-shadow: 0 0 0 3px rgba(110, 168, 230, 0.15);
}

.warnings-actions {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    white-space: nowrap;
}

.warnings-actions form {
    margin: 0;
}

.warnings-delete-btn {
    border: 1px solid #fecaca;
    background: #fff;
    color: #b91c1c;
    cursor: pointer;
    padding: 0.35rem 0.55rem;
    border-radius: 8px;
    font-size: 0.8rem;
    line-height: 1;
}

.warnings-delete-btn:hover {
    background: #fee2e2;
}

html[data-theme="dark"] .warnings-delete-btn {
    background: #191d23;
    border-color: #5c2027;
    color: #f1919b;
}

html[data-theme="dark"] .warnings-delete-btn:hover {
    background: #3a1418;
}

html[data-theme="dark"] .warnings-table thead th {
    background: #1f232a !important;
    color: #9aa1ab !important;
    border-bottom-color: #33383f !important;
}

html[data-theme="dark"] .warnings-table tbody tr {
    border-bottom-color: #262b32 !important;
}

html[data-theme="dark"] .warnings-table tbody tr:hover {
    background: #1f232a !important;
}

html[data-theme="dark"] .warnings-select,
html[data-theme="dark"] .warnings-notes {
    background: #262b32 !important;
    color: #d7dade !important;
    border-color: #3a4048 !important;
}
</style>

@stop
