@extends('layouts.master')
@section('title', $id.' - Winnipeg FIR')
@section('description', $id.'\'s controller profile')

@section('content')
@php
$positions = [
    'del' => ['label' => 'DEL', 'full' => 'Delivery',   'color' => '#6366f1', 'cert' => $rosterMember?->del ?? 0],
    'gnd' => ['label' => 'GND', 'full' => 'Ground',     'color' => '#16a34a', 'cert' => $rosterMember?->gnd ?? 0],
    'twr' => ['label' => 'TWR', 'full' => 'Tower',      'color' => '#dc2626', 'cert' => $rosterMember?->twr ?? 0],
    'dep' => ['label' => 'DEP', 'full' => 'Departure',  'color' => '#d97706', 'cert' => $rosterMember?->dep ?? 0],
    'app' => ['label' => 'APP', 'full' => 'Arrival',    'color' => '#d97706', 'cert' => $rosterMember?->app ?? 0],
    'ctr' => ['label' => 'CTR', 'full' => 'Centre',     'color' => '#0ea5e9', 'cert' => $rosterMember?->ctr ?? 0],
];
$certInfo = fn(int $level) => match($level) {
    2 => ['label' => 'Training',  'icon' => 'fa-book',  'class' => 'cert-training'],
    3 => ['label' => 'Solo',      'icon' => 'fa-user',  'class' => 'cert-solo'],
    4 => ['label' => 'Certified', 'icon' => 'fa-check', 'class' => 'cert-certified'],
    default => ['label' => 'None','icon' => 'fa-times', 'class' => 'cert-none'],
};
$memberSince = $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M Y') : '—';
$lastOnline  = $lastSession ? \Carbon\Carbon::parse($lastSession->session_end)->diffForHumans() : 'Never';
@endphp

<style>
/* ── Profile hero ──────────────────────────────────────── */
.profile-hero {
    background: linear-gradient(135deg, #0a1828 0%, #0d1f33 60%, #122b44 100%);
    border-bottom: 1px solid rgba(255,255,255,0.08);
    padding: 2rem 0 1.75rem;
    color: #fff;
}
.profile-hero a.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    color: rgba(255,255,255,0.5);
    font-size: 0.8rem;
    text-decoration: none;
    letter-spacing: 0.05em;
    margin-bottom: 1.25rem;
    transition: color 0.15s;
}
.profile-hero a.back-link:hover { color: rgba(255,255,255,0.9); }

.profile-header {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: wrap;
}
.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.15);
    flex-shrink: 0;
    object-fit: cover;
}
.profile-name {
    font-size: clamp(1.35rem, 3vw, 1.75rem);
    font-weight: 800;
    line-height: 1.1;
    margin: 0;
}
.profile-rating {
    display: inline-block;
    background: rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.9);
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    padding: 0.18rem 0.55rem;
    border-radius: 3px;
    vertical-align: middle;
    margin-left: 0.5rem;
    position: relative;
    top: -2px;
}
.profile-role {
    color: rgba(255,255,255,0.5);
    font-size: 0.8rem;
    margin-top: 0.2rem;
}
.profile-stats {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1.25rem;
}
.profile-stat {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 6px;
    padding: 0.6rem 1rem;
    min-width: 110px;
    text-align: center;
}
.profile-stat-val {
    font-size: 1.15rem;
    font-weight: 700;
    color: #fff;
    line-height: 1.1;
}
.profile-stat-lbl {
    font-size: 0.62rem;
    color: rgba(255,255,255,0.45);
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-top: 0.15rem;
}
/* ── Content area ──────────────────────────────────────── */
.profile-body {
    background: #f6f8fa;
    padding: 1.75rem 0 2.5rem;
}
.profile-card {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 8px;
    padding: 1.25rem 1.35rem;
    margin-bottom: 1.1rem;
}
.profile-card-title {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(0,0,0,0.35);
    margin-bottom: 1rem;
}

/* ── Cert tiles ────────────────────────────────────────── */
.cert-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
}
.cert-tile {
    border-radius: 7px;
    padding: 0.6rem 0.65rem;
    border: 1px solid rgba(0,0,0,0.07);
    background: #fafafa;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.cert-tile-badge {
    flex-shrink: 0;
}
.cert-tile-info {
    min-width: 0;
}
.cert-tile-pos {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #122b44;
    line-height: 1.2;
}
.cert-tile-full {
    font-size: 0.62rem;
    color: rgba(0,0,0,0.3);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}

/* ── Heatmap ───────────────────────────────────────────── */
.heatmap-wrap {
    display: flex;
    gap: 0;
    min-width: max-content;
}
.heatmap-days-col {
    display: flex;
    flex-direction: column;
    padding-top: 18px;
    padding-right: 6px;
    gap: 2px;
}
.heatmap-day-label {
    font-size: 0.58rem;
    color: rgba(0,0,0,0.3);
    height: 11px;
    line-height: 11px;
    text-align: right;
    white-space: nowrap;
}
.heatmap-cols {
    display: flex;
    flex-direction: row;
    gap: 2px;
}
.heatmap-col {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.heatmap-month-label {
    font-size: 0.58rem;
    color: rgba(0,0,0,0.3);
    height: 16px;
    line-height: 16px;
    white-space: nowrap;
    overflow: visible;
}
.hm-cell {
    width: 11px;
    height: 11px;
    border-radius: 2px;
    cursor: default;
    flex-shrink: 0;
}
.hm-future { background: transparent; }
.hm-0  { background: #ebedf0; }
.hm-1  { background: #b6d4f5; }
.hm-2  { background: #7bb3ed; }
.hm-3  { background: #3b82c4; }
.hm-4  { background: #1a4f8a; }
.heatmap-legend {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    margin-top: 0.6rem;
    justify-content: flex-end;
}
.heatmap-legend span {
    font-size: 0.6rem;
    color: rgba(0,0,0,0.3);
}
.hm-legend-cell {
    width: 11px;
    height: 11px;
    border-radius: 2px;
    flex-shrink: 0;
}

/* ── Sessions table ────────────────────────────────────── */
.sessions-table {
    width: 100%;
    font-size: 0.8rem;
    border-collapse: collapse;
}
.sessions-table th {
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    color: rgba(0,0,0,0.35);
    padding: 0 0 0.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.08);
    text-align: left;
}
.sessions-table td {
    padding: 0.45rem 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    color: rgba(0,0,0,0.65);
    vertical-align: middle;
}
.sessions-table tr:last-child td { border-bottom: none; }
.sessions-table .cs-tag {
    display: inline-block;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    padding: 0.15rem 0.45rem;
    border-radius: 3px;
    color: #fff;
}
.sessions-empty {
    color: rgba(0,0,0,0.3);
    font-size: 0.82rem;
    text-align: center;
    padding: 1.25rem 0;
}

/* ── Responsive ────────────────────────────────────────── */
@media (max-width: 576px) {
    .cert-grid { grid-template-columns: repeat(2, 1fr); }
    .profile-stats { gap: 0.6rem; }
    .profile-stat { min-width: 85px; }
}
</style>

{{-- HERO --}}
<div class="profile-hero">
    <div class="container">
        <a href="{{ route('roster.public') }}" class="back-link">
            <i class="fas fa-arrow-left fa-xs"></i> Roster
        </a>
        <div class="profile-header">
            <img src="{{ $user->avatar() }}" class="profile-avatar" alt="">
            <div>
                <h1 class="profile-name">
                    {{ $user->fullName('FL') }}
                    <span class="profile-rating">{{ $user->rating->getShortName() }}</span>
                </h1>
                @if($user->staffProfile)
                    <div class="profile-role">
                        <i class="fas fa-star fa-xs" style="color:rgba(255,255,255,0.35); margin-right:4px;"></i>{{ $user->staffProfile->position }}
                    </div>
                @elseif($rosterMember)
                    <div class="profile-role">
                        {{ $rosterMember->visit ? 'Visiting Controller' : 'Home Controller' }}
                    </div>
                @endif
            </div>
        </div>

        <div class="profile-stats">
            @if($rosterMember)
            <div class="profile-stat">
                <div class="profile-stat-val">{{ $quarterlyHours }}</div>
                <div class="profile-stat-lbl">Hours This Quarter</div>
            </div>
            <div class="profile-stat">
                <div class="profile-stat-val">{{ $sessionCount }}</div>
                <div class="profile-stat-lbl">Sessions This Month</div>
            </div>
            @endif
            <div class="profile-stat">
                <div class="profile-stat-val">{{ $memberSince }}</div>
                <div class="profile-stat-lbl">Member Since</div>
            </div>
            @if($lastSession)
            <div class="profile-stat">
                <div class="profile-stat-val" style="font-size:0.85rem;">{{ $lastOnline }}</div>
                <div class="profile-stat-lbl">Last Online</div>
            </div>
            @endif
        </div>

    </div>
</div>

{{-- BODY --}}
<div class="profile-body">
    <div class="container">
        <div class="row">

            {{-- Left: Certifications --}}
            @if($rosterMember)
            <div class="col-md-5">
                <div class="profile-card">
                    <div class="profile-card-title">Certifications</div>
                    <div class="cert-grid">
                        @foreach($positions as $key => $pos)
                        @php $ci = $certInfo($pos['cert']); @endphp
                        <div class="cert-tile">
                            <div class="cert-tile-badge">
                                <span class="cert-badge {{ $ci['class'] }}"><i class="fas {{ $ci['icon'] }}"></i></span>
                            </div>
                            <div class="cert-tile-info">
                                <div class="cert-tile-pos">{{ $pos['label'] }}</div>
                                <div class="cert-tile-full">{{ $pos['full'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Right: Bio + Sessions --}}
            <div class="{{ $rosterMember ? 'col-md-7' : 'col-md-12' }}">

                @if($user->bio)
                <div class="profile-card">
                    <div class="profile-card-title">About</div>
                    <p style="font-size:0.9rem; color:rgba(0,0,0,0.6); margin:0; line-height:1.6;">{{ $user->bio }}</p>
                </div>
                @endif

                <div class="profile-card">
                    <div class="profile-card-title">
                        Sessions This Month
                        @if($sessionCount > 0)
                            <span style="font-weight:400; color:rgba(0,0,0,0.25); margin-left:0.4rem;">({{ $sessionCount }})</span>
                        @endif
                    </div>
                    @if($sessionCount > 0)
                    <div style="max-height: 300px; overflow-y: auto;">
                    <table class="sessions-table">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Date</th>
                                <th>Start</th>
                                <th>End</th>
                                <th style="text-align:right;">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($connections as $c)
                        @php
                            $uPos     = strrpos($c->callsign, '_');
                            $posKey   = $uPos !== false ? strtolower(substr($c->callsign, $uPos + 1)) : '';
                            $posColor = $positions[$posKey]['color'] ?? '#6b7280';
                        @endphp
                        <tr>
                            <td><span class="cs-tag" style="background:{{ $posColor }};">{{ strtoupper($c->callsign) }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($c->session_start)->format('M j') }}</td>
                            <td>{{ \Carbon\Carbon::parse($c->session_start)->format('H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($c->session_end)->format('H:i') }}</td>
                            <td style="text-align:right; font-variant-numeric:tabular-nums;">{{ $c['duration'] }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                    @else
                    <div class="sessions-empty">No sessions this month.</div>
                    @endif
                </div>

            </div>

        </div>{{-- end .row --}}

        {{-- Heatmap: full width --}}
        @if($rosterMember)
        @php
            $today     = \Carbon\Carbon::today();
            $gridStart = $today->copy()->subWeeks(51)->startOfWeek(\Carbon\Carbon::SUNDAY);
            $gridEnd   = $today->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
            $weeks = [];
            $cursor = $gridStart->copy();
            while ($cursor->lte($gridEnd)) {
                $week = [];
                for ($d = 0; $d < 7; $d++) { $week[] = $cursor->copy(); $cursor->addDay(); }
                $weeks[] = $week;
            }
            $hmLevel = fn(float $h): int => match(true) {
                $h <= 0 => 0, $h < 1 => 1, $h < 2 => 2, $h < 4 => 3, default => 4
            };
        @endphp
        <div class="profile-card">
            <div class="profile-card-title">Controlling Activity</div>
            <div class="heatmap-wrap">
                <div class="heatmap-days-col">
                    <div class="heatmap-day-label"></div>
                    <div class="heatmap-day-label">Mon</div>
                    <div class="heatmap-day-label"></div>
                    <div class="heatmap-day-label">Wed</div>
                    <div class="heatmap-day-label"></div>
                    <div class="heatmap-day-label">Fri</div>
                    <div class="heatmap-day-label"></div>
                </div>
                <div class="heatmap-cols">
                @php $prevMonth = -1; @endphp
                @foreach($weeks as $week)
                <div class="heatmap-col">
                    @php
                        $firstDay  = $week[0];
                        $showMonth = ($firstDay->month !== $prevMonth);
                        if ($showMonth) $prevMonth = $firstDay->month;
                    @endphp
                    <div class="heatmap-month-label">{{ $showMonth ? $firstDay->format('M') : '' }}</div>
                    @foreach($week as $day)
                    @php
                        $dateStr  = $day->format('Y-m-d');
                        $hrs      = $heatmapData[$dateStr] ?? 0;
                        $lvl      = $hmLevel((float)$hrs);
                        $isFuture = $day->gt($today);
                        $tip      = $isFuture ? '' : ($hrs > 0 ? $day->format('M j').': '.decimal_to_hm($hrs) : $day->format('M j').': no activity');
                    @endphp
                    <div class="hm-cell {{ $isFuture ? 'hm-future' : 'hm-'.$lvl }}"
                         @if($tip) title="{{ $tip }}" @endif></div>
                    @endforeach
                </div>
                @endforeach
                </div>
            </div>
            <div class="heatmap-legend">
                <span>Less</span>
                <div class="hm-legend-cell hm-0"></div>
                <div class="hm-legend-cell hm-1"></div>
                <div class="hm-legend-cell hm-2"></div>
                <div class="hm-legend-cell hm-3"></div>
                <div class="hm-legend-cell hm-4"></div>
                <span>More</span>
            </div>
        </div>
        @endif

    </div>{{-- end .container --}}
</div>{{-- end .profile-body --}}
@endsection
