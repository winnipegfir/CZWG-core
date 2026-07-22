@extends('layouts.master')
@section('title', 'Event Rosters - Winnipeg FIR')
@section('description', 'Winnipeg FIR Event Rosters.')

@php
function eventPositionIcon($position) {
    switch (strtolower($position)) {
        case 'ground': return 'fa-road';
        case 'tower': return 'fa-broadcast-tower';
        case 'departure': return 'fa-plane-departure';
        case 'arrival': return 'fa-plane-arrival';
        case 'centre': case 'center': return 'fa-satellite-dish';
        default: return 'fa-headset';
    }
}

/**
 * Build one timeline row per position category for an event: the list of
 * staffed segments (as % offsets across the event's duration) plus whatever
 * gaps are left uncovered, so the timeline view can render blank spots.
 */
function eventTimelineRows($event, $positions)
{
    $eventStart = \Carbon\Carbon::parse($event->start_timestamp);
    $eventEnd = \Carbon\Carbon::parse($event->end_timestamp);
    $totalMinutes = max(1, $eventStart->diffInMinutes($eventEnd));

    $rows = [];

    foreach ($positions as $p) {
        $segments = [];

        foreach ($event->controllers as $c) {
            if ($c->position !== $p->position) {
                continue;
            }

            $segStart = $c->startAtUtc();
            $segEnd = $c->endAtUtc();

            if (! $segStart || ! $segEnd) {
                continue;
            }

            $offsetMin = ($segStart->timestamp - $eventStart->timestamp) / 60;
            $durMin = ($segEnd->timestamp - $segStart->timestamp) / 60;

            $left = max(0, min(100, $offsetMin / $totalMinutes * 100));
            $width = max(1.5, min(100 - $left, $durMin / $totalMinutes * 100));

            $segments[] = [
                'left' => $left,
                'width' => $width,
                'name' => $c->user->fullName('FLC'),
                'airport' => $c->airport,
                'start' => $c->start_timestamp,
                'end' => $c->end_timestamp,
            ];
        }

        usort($segments, fn ($a, $b) => $a['left'] <=> $b['left']);

        // Walk the sorted segments to find uncovered gaps in the row.
        $gaps = [];
        $cursor = 0;
        foreach ($segments as $s) {
            if ($s['left'] > $cursor + 0.5) {
                $gaps[] = ['left' => $cursor, 'width' => $s['left'] - $cursor];
            }
            $cursor = max($cursor, $s['left'] + $s['width']);
        }
        if ($cursor < 100 - 0.5) {
            $gaps[] = ['left' => $cursor, 'width' => 100 - $cursor];
        }

        $rows[] = [
            'position' => $p->position,
            'segments' => $segments,
            'gaps' => $gaps,
            'unstaffed' => count($segments) === 0,
        ];
    }

    return ['rows' => $rows, 'start' => $eventStart, 'end' => $eventEnd, 'totalMinutes' => $totalMinutes];
}

function eventTimelineTicks($eventStart, $totalMinutes)
{
    $step = $totalMinutes > 300 ? 60 : ($totalMinutes > 120 ? 30 : 15);
    $ticks = [];
    for ($m = 0; $m <= $totalMinutes; $m += $step) {
        $ticks[] = [
            'left' => $m / $totalMinutes * 100,
            'label' => $eventStart->copy()->addMinutes($m)->format('H:i'),
        ];
    }

    return $ticks;
}
@endphp

@section('content')
    <div class="container evroster-page-wrap">
        <a href="{{route('dashboard.index')}}" class="dash-back-link">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>

        <div class="evroster-page-header mt-3">
            <h1 class="roster-page-title">Event Rosters</h1>
            <p class="roster-page-sub">Staffing plans for upcoming Winnipeg FIR events</p>
        </div>

        @if($events->count() == 0)
            <div class="evroster-empty">
                <i class="fas fa-calendar-day"></i>
                <p>No upcoming events with rosters yet.</p>
            </div>
        @else
            <div class="roster-controls">
                <div class="roster-pills" role="tablist">
                    <button class="roster-pill active" data-panel="cards" role="tab" aria-selected="true">
                        <i class="fas fa-th-large"></i> Cards
                    </button>
                    <button class="roster-pill" data-panel="timeline" role="tab" aria-selected="false">
                        <i class="fas fa-stream"></i> Timeline
                    </button>
                </div>
            </div>

            {{-- ── Card view ─────────────────────────────────────────── --}}
            <div id="panel-cards" class="evroster-panel">
                <div class="evroster-grid">
                    @foreach($events as $e)
                        <div class="evroster-card">
                            <div class="evroster-card-header">
                                <div class="evroster-card-title">{{$e->name}}</div>
                                <div class="evroster-card-time"><i class="far fa-clock"></i> Starting {{$e->start_timestamp_pretty()}}</div>
                            </div>

                            <div class="evroster-card-body">
                                @if(count($e->controllers) == 0)
                                    <div class="evroster-none">No event roster yet!</div>
                                @else
                                    @foreach($positions as $p)
                                        @if($p->hasControllers($p->position, $e->id))
                                            <div class="evroster-position-group">
                                                <div class="evroster-position-label">
                                                    <i class="fas {{eventPositionIcon($p->position)}}"></i>
                                                    {{$p->position}}
                                                </div>
                                                <ul class="evroster-slot-list">
                                                    @foreach($e->controllers as $c)
                                                        @if($c->position == $p->position)
                                                            <li class="evroster-slot">
                                                                <span class="evroster-slot-name">{{$c->user->fullName('FLC')}}</span>
                                                                <span class="evroster-slot-meta">
                                                                    @if($c->airport)
                                                                        <span class="evroster-slot-airport">{{$c->airport}}</span>
                                                                    @endif
                                                                    <span class="evroster-slot-time">{{$c->start_timestamp}}z&ndash;{{$c->end_timestamp}}z</span>
                                                                </span>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Timeline view ─────────────────────────────────────── --}}
            <div id="panel-timeline" class="evroster-panel" style="display:none;">
                <div class="evtimeline-legend">
                    <span><i class="evtl-swatch evtl-swatch-staffed"></i> Staffed</span>
                    <span><i class="evtl-swatch evtl-swatch-gap"></i> Gap / not covered</span>
                </div>

                <div class="evtimeline-wrap">
                    @foreach($events as $e)
                        @php $tl = eventTimelineRows($e, $positions); @endphp
                        <div class="evtimeline-event">
                            <div class="evtimeline-event-header">
                                <div class="evroster-card-title">{{$e->name}}</div>
                                <div class="evroster-card-time">
                                    <i class="far fa-clock"></i>
                                    {{$tl['start']->format('M j, Y H:i')}}z &ndash; {{$tl['end']->format('H:i')}}z
                                    @if(now()->between($tl['start'], $tl['end']))
                                        <span class="evtl-live-badge">LIVE NOW</span>
                                    @endif
                                </div>
                            </div>

                            @if(count($e->controllers) == 0)
                                <div class="evroster-none px-3 pb-3">No event roster yet!</div>
                            @else
                                <div class="evtimeline-body">
                                    <div class="evtimeline-axis">
                                        <div class="evtl-label-spacer"></div>
                                        <div class="evtl-ticks">
                                            @foreach(eventTimelineTicks($tl['start'], $tl['totalMinutes']) as $tick)
                                                <span class="evtl-tick" style="left: {{$tick['left']}}%;">{{$tick['label']}}z</span>
                                            @endforeach
                                        </div>
                                    </div>

                                    @foreach($tl['rows'] as $row)
                                        <div class="evtl-row @if($row['unstaffed']) evtl-row-unstaffed @endif">
                                            <div class="evtl-row-label">
                                                <i class="fas {{eventPositionIcon($row['position'])}}"></i>
                                                {{$row['position']}}
                                            </div>
                                            <div class="evtl-track">
                                                @if($row['unstaffed'])
                                                    <div class="evtl-gap" style="left: 0%; width: 100%;">
                                                        <span>Not staffed</span>
                                                    </div>
                                                @else
                                                    @foreach($row['gaps'] as $gap)
                                                        <div class="evtl-gap" style="left: {{$gap['left']}}%; width: {{$gap['width']}}%;"></div>
                                                    @endforeach
                                                    @foreach($row['segments'] as $seg)
                                                        <div class="evtl-seg" style="left: {{$seg['left']}}%; width: {{$seg['width']}}%;"
                                                             title="{{$seg['name']}} &mdash; {{$seg['airport'] ? $seg['airport'].' ' : ''}}{{$row['position']}} ({{$seg['start']}}z&ndash;{{$seg['end']}}z)">
                                                            <span>{{$seg['name']}}</span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <script>
                $(document).ready(function () {
                    $('.roster-pill[data-panel]').on('click', function () {
                        var panel = $(this).data('panel');
                        $('.roster-pill[data-panel]').removeClass('active').attr('aria-selected', 'false');
                        $(this).addClass('active').attr('aria-selected', 'true');
                        $('.evroster-panel').hide();
                        $('#panel-' + panel).show();
                    });
                });
            </script>
        @endif
    </div>
@stop
