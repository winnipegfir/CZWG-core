@extends('layouts.master')
@section('title', 'Operational Overview')
@section('content')
@php
    $formatMinutes = function ($minutes) {
        $minutes = max(0, (int) $minutes);
        return floor($minutes / 60).'h '.($minutes % 60).'m';
    };
@endphp
<style>
.ops-page{background:#f4f7fa;min-height:70vh;padding:2rem 0 4rem;color:#122b44}.ops-hero{background:linear-gradient(135deg,#081827,#17486f);color:#fff;padding:2.2rem 0}.ops-hero a{color:#9dd8ff}.ops-subtitle{color:rgba(255,255,255,.68)}.ops-panel{background:#fff;border:1px solid #e1e7ee;border-radius:12px;padding:1.25rem;box-shadow:0 5px 18px rgba(18,43,68,.05);height:100%}.ops-kicker{text-transform:uppercase;letter-spacing:.09em;font-size:.68rem;font-weight:800;color:#718096}.ops-value{font-size:1.6rem;font-weight:800;color:#122b44}.ops-meter{height:8px;background:#e8edf2;border-radius:999px;overflow:hidden}.ops-meter span{display:block;height:100%;background:linear-gradient(90deg,#2878a8,#42b883);border-radius:999px}.ops-table th{font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:#718096;border-top:0}.ops-note{background:#eaf4fb;border-left:4px solid #2878a8;border-radius:8px;padding:.85rem 1rem;color:#31576d;font-size:.84rem}.ops-empty{color:#7b8794;padding:2rem;text-align:center}.ops-range .btn.active{background:#17486f;color:#fff;border-color:#17486f}.ops-stat{border-left:3px solid #2b7da8;padding-left:.8rem}.ops-good{border-left-color:#38a169}
.ops-scroll{max-height:390px;overflow-x:auto;overflow-y:scroll;scrollbar-gutter:stable;scrollbar-color:#8393a3 #e8edf2;scrollbar-width:thin}.ops-scroll .ops-table thead th{position:sticky;top:0;background:#fff;z-index:2}.ops-scroll::-webkit-scrollbar{width:10px;height:10px}.ops-scroll::-webkit-scrollbar-track{background:#e8edf2}.ops-scroll::-webkit-scrollbar-thumb{background:#8393a3;border-radius:9px;border:2px solid #e8edf2}
.ops-history-scroll{max-height:340px}
.ops-airport-card{overflow:hidden}.ops-airport-photo{height:118px;margin:-1.25rem -1.25rem 1rem;background-position:center;background-size:cover;position:relative}.ops-airport-photo:after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(4,15,25,.12),rgba(4,15,25,.82))}.ops-airport-title{position:absolute;left:1.25rem;right:1.25rem;bottom:.8rem;z-index:1;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,.75)}.ops-airport-credit{font-size:.62rem;position:absolute;right:.55rem;top:.4rem;z-index:1;background:rgba(0,0,0,.58);padding:.16rem .35rem;border-radius:4px}.ops-airport-credit,.ops-airport-credit:hover{color:#fff}
html[data-theme="dark"] .ops-page{background:#15181d;color:#e7eaee}html[data-theme="dark"] .ops-panel{background:#20242b;border-color:#303640;box-shadow:none}html[data-theme="dark"] .ops-value{color:#f1f4f7}html[data-theme="dark"] .ops-kicker,html[data-theme="dark"] .ops-table th{color:#a5afba}html[data-theme="dark"] .ops-meter{background:#343b44}html[data-theme="dark"] .ops-note{background:#172d3c;color:#acd4eb;border-color:#4aa3d3}html[data-theme="dark"] .ops-table{color:#e3e7eb}html[data-theme="dark"] .ops-table td,html[data-theme="dark"] .ops-table th{border-color:#303640}
html[data-theme="dark"] .ops-scroll .ops-table thead th{background:#20242b}
html[data-theme="dark"] .ops-scroll{scrollbar-color:#718096 #343b44}html[data-theme="dark"] .ops-scroll::-webkit-scrollbar-track{background:#343b44}html[data-theme="dark"] .ops-scroll::-webkit-scrollbar-thumb{background:#718096;border-color:#343b44}
</style>

<div class="ops-hero">
    <div class="container-fluid px-md-5">
        <a href="{{ url('/admin/network') }}"><i class="fas fa-arrow-left mr-1"></i> Network Data</a>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mt-3">
            <div>
                <div class="ops-kicker" style="color:#7dd3fc">Winnipeg FIR</div>
                <h1 class="font-weight-bold mb-1">Operational Overview</h1>
                <p class="ops-subtitle mb-0">Airport traffic demand, ATC coverage, and position utilization.</p>
            </div>
            <div class="ops-range btn-group mt-3 mt-md-0" role="group" aria-label="Reporting period">
                @foreach([7, 30, 90] as $range)
                    <a class="btn btn-sm btn-outline-light {{ $days === $range ? 'active' : '' }}" href="{{ url('/admin/network/operations').'?days='.$range }}">{{ $range }} days</a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="ops-page">
    <div class="container-fluid px-md-5">
        <div class="ops-note mb-4">
            <strong>Operational planning only.</strong> These are five-minute VATSIM observations intended to identify coverage and training opportunities—not to rank or evaluate individual controllers. Top-down Winnipeg coverage is included where applicable. The dashboard displays up to 90 days; operational records older than {{ \App\Services\OperationalOverviewCollector::RETENTION_DAYS }} days are automatically removed.
        </div>

        @if(!empty($setupIncomplete))
            <div class="ops-panel ops-empty mb-4">
                <i class="fas fa-tools fa-2x mb-3"></i>
                <h5>Operational Overview setup is incomplete</h5>
                <p class="mb-0">The database migration has not run yet. The rest of the website remains available.</p>
            </div>
        @elseif(!$latestSample)
            <div class="ops-panel ops-empty mb-4">
                <i class="fas fa-chart-line fa-2x mb-3"></i>
                <h5>No operational samples yet</h5>
                <p class="mb-0">Collection will begin automatically when the Laravel scheduler runs.</p>
            </div>
        @else
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="font-weight-bold mb-0">Airport overview</h4>
                <small class="text-muted">Latest sample: {{ \Carbon\Carbon::parse($latestSample)->diffForHumans() }}</small>
            </div>
            <div class="row">
                @foreach($airports as $airport)
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="ops-panel ops-airport-card">
                            <div class="ops-airport-photo" style="background-image:url('{{ asset('img/operational-overview/'.$airport->photo) }}')">
                                <a class="ops-airport-credit" href="{{ $airport->photo_source }}" target="_blank" rel="noopener noreferrer">{{ $airport->photo_credit }} · {{ $airport->photo_license }}</a>
                                <div class="ops-airport-title"><div class="ops-kicker" style="color:rgba(255,255,255,.8)">{{ $airport->icao }}</div><h5 class="font-weight-bold mb-0">{{ $airport->name }}</h5></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <span class="ops-kicker">Aircraft-time with ATC</span>
                                <span class="ops-value">{{ $airport->coverage === null ? '—' : $airport->coverage.'%' }}</span>
                            </div>
                            <div class="ops-meter mb-3"><span style="width:{{ $airport->coverage ?? 0 }}%"></span></div>
                            <div class="row text-center">
                                <div class="col-6 border-right"><div class="ops-kicker">Ground aircraft-min.</div><strong>{{ number_format($airport->ground_minutes) }}</strong></div>
                                <div class="col-6"><div class="ops-kicker">Airborne aircraft-min.</div><strong>{{ number_format($airport->airborne_minutes) }}</strong></div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between small"><span class="text-muted">Time with traffic</span><strong>{{ $formatMinutes($airport->traffic_time) }}</strong></div>
                            <div class="d-flex justify-content-between small mt-1"><span class="text-muted">Time with ATC online</span><strong>{{ $formatMinutes($airport->staffed_time) }}</strong></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mt-2">
                <div class="col-xl-8 mb-4"><div class="ops-panel">
                    <div class="d-flex justify-content-between align-items-start mb-2"><div><div class="ops-kicker">Snapshot-derived flight tracking</div><h4 class="font-weight-bold mb-0">Flight-level operating time</h4></div><small class="text-muted">Newest first · scroll for more</small></div>
                    <p class="text-muted small mb-3">Each row is one observed flight near a tracked airport. Ground and airborne time are estimated from consecutive position reports; “Completed” means the historical flight ended or the live aircraft was no longer observed.</p>
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3"><div class="ops-stat"><div class="ops-kicker">Flights observed</div><div class="ops-value">{{ number_format($flightTotals->count) }}</div></div></div>
                        <div class="col-md-3 mb-3"><div class="ops-stat"><div class="ops-kicker">Avg. ground</div><div class="ops-value">{{ $flightTotals->average_ground_minutes === null ? '—' : $flightTotals->average_ground_minutes.'m' }}</div></div></div>
                        <div class="col-md-3 mb-3"><div class="ops-stat"><div class="ops-kicker">Avg. airborne nearby</div><div class="ops-value">{{ $flightTotals->average_airborne_minutes === null ? '—' : $flightTotals->average_airborne_minutes.'m' }}</div></div></div>
                        <div class="col-md-3 mb-3"><div class="ops-stat ops-good"><div class="ops-kicker">Time under ATC</div><div class="ops-value">{{ $flightTotals->coverage === null ? '—' : $flightTotals->coverage.'%' }}</div></div></div>
                    </div>
                    @if($recentFlights->isEmpty())
                        <div class="ops-empty">Flight histories appear after two live samples observe the same aircraft.</div>
                    @else
                        <div class="table-responsive ops-scroll"><table class="table ops-table mb-0"><thead><tr><th>Flight</th><th>Route</th><th>Airport</th><th>Ground</th><th>Airborne</th><th>Under ATC</th><th>Status</th></tr></thead><tbody>
                        @foreach($recentFlights as $flight)<tr><td><strong>{{ $flight->callsign }}</strong></td><td>{{ $flight->departure ?: '—' }}–{{ $flight->arrival ?: '—' }}</td><td>{{ $flight->airport }}</td><td>{{ $formatMinutes(round($flight->ground_seconds / 60)) }}</td><td>{{ $formatMinutes(round($flight->airborne_seconds / 60)) }}</td><td>{{ $flight->coverage === null ? '—' : $flight->coverage.'%' }}</td><td>{{ $flight->completed_at ? 'Completed' : ucfirst($flight->last_phase ?: 'Observed') }}</td></tr>@endforeach
                        </tbody></table></div>
                    @endif
                </div></div>
                <div class="col-xl-4 mb-4">
                    <div class="ops-panel mb-4" style="height:auto">
                        <div class="ops-kicker">Collective roster context</div><h4 class="font-weight-bold mb-3">Staffing efficiency</h4>
                        <div class="d-flex justify-content-between mb-2"><span>Controllers observed</span><strong>{{ $rosterEfficiency->controllers_observed }}</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Position-hours</span><strong>{{ number_format($rosterEfficiency->staffed_hours, 1) }}h</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Hours with relevant traffic</span><strong>{{ number_format($rosterEfficiency->active_hours, 1) }}h</strong></div>
                        <div class="d-flex justify-content-between"><span>Traffic utilization</span><strong>{{ $rosterEfficiency->traffic_utilization === null ? '—' : $rosterEfficiency->traffic_utilization.'%' }}</strong></div>
                        <hr><small class="text-muted">Observed staffing equals {{ $rosterEfficiency->requirement_delivery === null ? '—' : $rosterEfficiency->requirement_delivery.'%' }} of the observed controllers’ prorated {{ $days }}-day roster requirement ({{ number_format($rosterEfficiency->prorated_requirement_hours, 1) }}h). Planning context only.</small>
                    </div>
                    <div class="ops-panel" style="height:auto">
                        <div class="ops-kicker">Training opportunity</div><h4 class="font-weight-bold mb-3">Largest coverage gaps</h4>
                        @foreach($trainingOpportunities as $opportunity)<div class="d-flex justify-content-between mb-2"><span><strong>{{ $opportunity->airport }}</strong> {{ $opportunity->name }}</span><span>{{ $formatMinutes($opportunity->uncovered_minutes) }}</span></div>@endforeach
                        <small class="text-muted">Traffic-present time without an applicable Winnipeg position observed.</small>
                    </div>
                    <div class="ops-panel mt-4" style="height:auto">
                        <div class="ops-kicker">Traffic demand in UTC</div><h4 class="font-weight-bold mb-3">Peak traffic windows</h4>
                        @foreach($peakTimes as $peak)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><strong>{{ $peak->airport }}</strong> {{ $peak->name }}</span>
                                <strong>{{ $peak->window ?: '—' }}</strong>
                            </div>
                        @endforeach
                        <small class="text-muted">Highest rolling three-hour traffic window in the selected {{ $days }}-day period. Useful for controller staffing and event planning; times update as observations accumulate.</small>
                    </div>
                </div>
            </div>

            <div class="ops-panel mb-4" style="height:auto">
                <div class="d-flex justify-content-between align-items-start mb-2"><div><div class="ops-kicker">Existing Winnipeg activity archive</div><h4 class="font-weight-bold mb-0">Historical ATC staffing</h4></div><small class="text-muted">Newest first · scroll for more</small></div>
                <p class="text-muted small">Controller sessions retained by the website in the selected reporting period. APP/DEP/TML callsigns are labelled Terminal. This independent archive is not added to sampled utilization; old session logs contain no pilot-position history.</p>
                @if($historicalStaffing->isEmpty())
                    <div class="ops-empty py-3">No historical controller sessions were found for this reporting period.</div>
                @else
                    <div class="table-responsive ops-scroll ops-history-scroll"><table class="table ops-table mb-0"><thead><tr><th>Started (UTC)</th><th>Airport</th><th>Position</th><th>Callsign</th><th>Staffed time</th></tr></thead><tbody>
                        @foreach($historicalStaffing as $history)<tr><td>{{ $history->started_at->format('M j, Y H:i') }}Z</td><td><strong>{{ $history->airport }}</strong></td><td>{{ $history->position_type }}</td><td>{{ $history->callsign }}</td><td>{{ $formatMinutes($history->minutes) }}</td></tr>@endforeach
                    </tbody></table></div>
                @endif
            </div>

            <div class="ops-panel mb-4" style="height:auto">
                <div class="ops-kicker">Non-punitive operational record</div><h4 class="font-weight-bold mb-2">Emergency observations</h4>
                <p class="text-muted small">Records tracked aircraft observed squawking 7500, 7600, or 7700. It does not determine whether an emergency was genuine or accepted by ATC.</p>
                @if($emergencies->isEmpty())<div class="ops-empty py-3">No emergency transponder codes were observed during this period.</div>
                @else<div class="table-responsive"><table class="table ops-table mb-0"><thead><tr><th>Flight</th><th>Airport</th><th>Code</th><th>First observed</th><th>Last observed</th></tr></thead><tbody>
                    @foreach($emergencies as $emergency)<tr><td><strong>{{ $emergency->callsign }}</strong></td><td>{{ $emergency->airport }}</td><td>{{ $emergency->squawk }}</td><td>{{ $emergency->first_seen_at->format('M j, Y H:i') }}</td><td>{{ $emergency->last_seen_at->diffForHumans() }}</td></tr>@endforeach
                </tbody></table></div>@endif
            </div>

            <div class="ops-panel mt-2">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div><div class="ops-kicker">FIR-wide tracked airports</div><h4 class="font-weight-bold mb-0">Position utilization</h4></div>
                    <small class="text-muted">APP/DEP/TML are grouped as Terminal; FIR-wide Center appears once; unsupported suffixes and FMP are excluded</small>
                </div>
                @if($positionTypes->isEmpty())
                    <div class="ops-empty">No Winnipeg positions were observed during this period.</div>
                @else
                    <div class="table-responsive">
                        <table class="table ops-table mb-0">
                            <thead><tr><th>Airport</th><th>Position</th><th>Staffed time</th><th>Time with traffic</th><th>Traffic observations</th><th style="min-width:180px">Utilization</th></tr></thead>
                            <tbody>
                            @foreach($positionTypes as $position)
                                <tr>
                                    <td><strong>{{ $position->airport }}</strong></td>
                                    <td><strong>{{ $position->position_type }}</strong></td>
                                    <td>{{ $formatMinutes($position->staffed_minutes) }}</td>
                                    <td>{{ $formatMinutes($position->active_minutes) }}</td>
                                    <td>{{ number_format($position->traffic_observations) }}</td>
                                    <td><div class="d-flex align-items-center" style="gap:.6rem"><div class="ops-meter flex-grow-1"><span style="width:{{ $position->utilization ?? 0 }}%"></span></div><strong>{{ $position->utilization === null ? '—' : $position->utilization.'%' }}</strong></div></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
