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
$lastOnline  = $lastSession ? \Carbon\Carbon::parse($lastSession->session_end)->format('M j, Y') : 'Never';
@endphp

<style>
/* ── Profile hero ──────────────────────────────────────── */
.profile-hero {
    background: linear-gradient(135deg, #080f1a 0%, #0d2035 50%, #122b44 100%);
    border-bottom: 1px solid rgba(255,255,255,0.05);
    padding: 2rem 0 1.75rem;
    color: #fff;
    position: relative;
    overflow: hidden;
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
    width: 100%;
}
.heatmap-days-col {
    display: flex;
    flex-direction: column;
    padding-top: 18px;
    padding-right: 6px;
    gap: 2px;
    flex-shrink: 0;
}
.heatmap-day-label {
    font-size: 0.58rem;
    color: rgba(0,0,0,0.3);
    height: var(--hm-cell, 11px);
    line-height: var(--hm-cell, 11px);
    text-align: right;
    white-space: nowrap;
}
.heatmap-month-row {
    display: flex;
    gap: 2px;
    height: 16px;
    margin-bottom: 2px;
    font-size: 0.58rem;
    color: rgba(0,0,0,0.3);
}
.heatmap-month-row .hm-month-label {
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    line-height: 16px;
    padding: 0 4px;
    box-sizing: border-box;
}
.hm-month-spacer, .hm-col-spacer {
    width: 8px;
    flex-shrink: 0;
    flex-grow: 0;
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
    width: var(--hm-cell, 14px);
    flex-shrink: 0;
}
.hm-cell {
    width: var(--hm-cell, 14px);
    height: var(--hm-cell, 14px);
    border-radius: 2px;
    cursor: default;
    flex-shrink: 0;
}
.hm-future { background: #f3f4f6; }
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
    <div style="position:absolute; inset:0; pointer-events:none; overflow:hidden;">
        <img src="{{ $user->avatar() }}"
             style="position:absolute; left:50%; top:50%; transform:translate(-50%,-50%);
                    width:140%; height:140%; object-fit:cover;
                    filter:blur(50px); opacity:0.09;">
    </div>
    <div class="container" style="position:relative;">
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
                <div class="profile-stat-lbl">Sessions This Quarter</div>
            </div>
            @endif
            @if($lastSession)
            <div class="profile-stat">
                <div class="profile-stat-val">{{ $lastOnline }}</div>
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

            {{-- Left: Certifications + Heatmap --}}
            @if($rosterMember)
            @php
                $today      = \Carbon\Carbon::today();
                $qFirstMonth = (int)ceil($today->month / 3) * 3 - 2;
                $qStart     = \Carbon\Carbon::create($today->year, $qFirstMonth, 1);
                $gridStart  = $qStart->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                $gridEnd    = $today->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
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
                $monthGroups = [];
                foreach ($weeks as $week) {
                    $key = $week[0]->format('Y-n');
                    if (!isset($monthGroups[$key])) {
                        $monthGroups[$key] = ['label' => $week[0]->format('M'), 'count' => 0];
                    }
                    $monthGroups[$key]['count']++;
                }
            @endphp
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

                <div class="profile-card">
                    <div class="profile-card-title">Activity This Quarter</div>
                    <div class="heatmap-wrap" id="heatmap-container">
                        <div class="heatmap-days-col">
                            <div class="heatmap-day-label"></div>
                            <div class="heatmap-day-label">Mon</div>
                            <div class="heatmap-day-label"></div>
                            <div class="heatmap-day-label">Wed</div>
                            <div class="heatmap-day-label"></div>
                            <div class="heatmap-day-label">Fri</div>
                            <div class="heatmap-day-label"></div>
                        </div>
                        <div style="display:flex; flex-direction:column; flex:1; min-width:0;">
                            <div class="heatmap-month-row" id="heatmap-month-row">
                            @foreach($monthGroups as $mg)
                                @if(!$loop->first)<div class="hm-month-spacer"></div>@endif
                                <div class="hm-month-label" data-weeks="{{ $mg['count'] }}">{{ $mg['label'] }}</div>
                            @endforeach
                            </div>
                            <div class="heatmap-cols">
                            @php $hmPrevMonth = -1; @endphp
                            @foreach($weeks as $week)
                                @php
                                    $isNewMonth = $week[0]->month !== $hmPrevMonth;
                                    if ($isNewMonth) $hmPrevMonth = $week[0]->month;
                                @endphp
                                @if($isNewMonth && !$loop->first)
                                    <div class="hm-col-spacer"></div>
                                @endif
                                <div class="heatmap-col">
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
                    </div>
                    <script>
                    (function() {
                        function syncHeatmap() {
                            var container = document.getElementById('heatmap-container');
                            if (!container) return;
                            var dayCol  = container.querySelector('.heatmap-days-col');
                            var cols    = container.querySelectorAll('.heatmap-col');
                            var spacers = container.querySelectorAll('.hm-col-spacer');
                            var nCols   = cols.length;
                            var nSpc    = spacers.length;
                            var available = container.clientWidth - (dayCol ? dayCol.offsetWidth : 0);
                            var totalGaps = (nCols + nSpc - 1) * 2;
                            var spacerPx  = nSpc * 8;
                            var size = Math.floor((available - totalGaps - spacerPx) / nCols);
                            size = Math.min(20, Math.max(8, size));
                            container.style.setProperty('--hm-cell', size + 'px');
                            // Set month label widths to match: N weeks * (size+2) - 2
                            container.querySelectorAll('.hm-month-label').forEach(function(el) {
                                var n = parseInt(el.dataset.weeks, 10);
                                el.style.width = (n * (size + 2) - 2) + 'px';
                            });
                        }
                        window.addEventListener('load', syncHeatmap);
                        window.addEventListener('resize', syncHeatmap);
                    })();
                    </script>
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
                    <div class="profile-card-title">Recent Sessions</div>
                    @if($sessionCount > 0)
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
                        @foreach($connections->take(5) as $c)
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
                    @else
                    <div class="sessions-empty">No recent sessions.</div>
                    @endif
                </div>

            </div>

        </div>{{-- end .row --}}


    </div>{{-- end .container --}}
</div>{{-- end .profile-body --}}
@endsection
