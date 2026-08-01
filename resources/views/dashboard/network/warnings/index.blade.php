@extends('layouts.master')

@section('title', 'Inactivity Warnings — Winnipeg FIR')
@section('description', 'Track quarterly inactivity warnings sent to roster members')

@section('content')

<div class="container roster-page-wrap">

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
                    <select id="quarterSelect" name="quarter" class="form-control form-control-sm" onchange="this.form.submit()">
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
                    <th>Sent By / Deadline</th>
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

                    <td class="activity-result-note" style="white-space:normal;">
                        @if ($warning->warning_sent)
                            {{ $warning->warningSentBy ? $warning->warningSentBy->fullName('FL') : 'Unknown' }} on {{ $warning->warning_sent_at->format('M j, Y') }}
                            @if ($warning->deadline)
                                <br>Deadline: {{ $warning->deadline->format('M j, Y') }}
                            @endif
                        @else
                            &mdash;
                        @endif
                    </td>

                    <td>
                        <select form="wform-{{ $warning->id }}" name="result" class="form-control form-control-sm">
                            @foreach (\App\Models\Network\ActivityWarning::RESULTS as $key => $label)
                                <option value="{{ $key }}" {{ $warning->result === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <textarea form="wform-{{ $warning->id }}" name="notes" class="form-control form-control-sm" rows="1" placeholder="Notes&hellip;">{{ $warning->notes }}</textarea>
                    </td>

                    <td>
                        <button type="submit" form="wform-{{ $warning->id }}" class="btn btn-sm bg-czqo-blue-light">Save</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No warnings logged for this quarter.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
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

.warnings-table select.form-control,
.warnings-table textarea.form-control {
    display: block;
    width: 100%;
}

.warnings-table textarea.form-control {
    resize: vertical;
    min-width: 180px;
}

.warnings-table select.form-control {
    min-width: 190px;
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
</style>

@stop
