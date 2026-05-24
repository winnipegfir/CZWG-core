@extends('layouts.master')

@section('title', 'Manage Events - Winnipeg FIR')

@section('content')
<div style="background:#fff; min-height:calc(100vh - 60px); padding:2.5rem 0;">
    <div class="container">

        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <a href="{{ route('dashboard.index') }}" style="font-size:0.82rem; color:#6c757d; text-decoration:none;">
                    <i class="fas fa-arrow-left mr-1"></i> Dashboard
                </a>
                <h1 class="font-weight-bold mb-0 mt-1" style="color:#122b44;">Events</h1>
            </div>
            <a href="{{ route('events.admin.create') }}" class="btn btn-sm" style="background:#122b44; color:#fff; border-radius:0.375rem; font-size:0.85rem; padding:0.45rem 1rem;">
                <i class="fas fa-plus mr-1"></i> New Event
            </a>
        </div>
        <hr>

        @if($events->isEmpty())
            <div style="text-align:center; padding:4rem 0; color:#6c757d;">
                <i class="fas fa-calendar-alt fa-2x mb-3" style="opacity:0.3; display:block;"></i>
                No events yet. <a href="{{ route('events.admin.create') }}" style="color:#122b44;">Create one</a>.
            </div>
        @else
            <div style="border:1px solid #e9ecef; border-radius:0.5rem; overflow:hidden;">
                <table class="table table-hover mb-0" style="font-size:0.875rem;">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.75rem 1rem;">Event</th>
                            <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.75rem 1rem; width:200px;">Date</th>
                            <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.75rem 1rem; width:100px;">Status</th>
                            <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.75rem 1rem; width:120px; text-align:right; white-space:nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events->sortByDesc('start_timestamp') as $e)
                            <tr style="border-bottom:1px solid #f1f3f5;">
                                <td style="padding:0.75rem 1rem; vertical-align:middle;">
                                    <a href="{{ route('events.admin.view', $e->slug) }}" style="color:#122b44; font-weight:600; text-decoration:none;">
                                        {{ $e->name }}
                                    </a>
                                    @if($e->departure_icao || $e->arrival_icao)
                                        <div style="color:#6c757d; font-size:0.78rem; margin-top:0.15rem;">
                                            {{ $e->departure_icao }}{{ $e->departure_icao && $e->arrival_icao ? ' → ' : '' }}{{ $e->arrival_icao }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding:0.75rem 1rem; vertical-align:middle; color:#6c757d;">
                                    {{ $e->start_timestamp_pretty() }}
                                </td>
                                <td style="padding:0.75rem 1rem; vertical-align:middle;">
                                    @if($e->event_in_past())
                                        <span style="background:#e9ecef; color:#6c757d; font-size:0.72rem; font-weight:600; padding:0.2rem 0.55rem; border-radius:999px;">Past</span>
                                    @else
                                        <span style="background:#d4edda; color:#155724; font-size:0.72rem; font-weight:600; padding:0.2rem 0.55rem; border-radius:999px;">Upcoming</span>
                                    @endif
                                </td>
                                <td style="padding:0.75rem 1rem; vertical-align:middle; text-align:right; white-space:nowrap;">
                                    <a href="{{ route('events.admin.view', $e->slug) }}"
                                       style="font-size:0.8rem; color:#122b44; text-decoration:none; font-weight:500; margin-right:1rem;">
                                        <i class="fas fa-edit fa-xs mr-1"></i>Manage
                                    </a>
                                    <a href="{{ route('events.admin.delete', $e->slug) }}"
                                       onclick="return confirm('Delete \'{{ addslashes($e->name) }}\'? This cannot be undone.')"
                                       style="font-size:0.8rem; color:#dc3545; text-decoration:none; font-weight:500;">
                                        <i class="fas fa-trash fa-xs mr-1"></i>Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p style="color:#6c757d; font-size:0.78rem; margin-top:0.75rem;">{{ $events->count() }} event{{ $events->count() === 1 ? '' : 's' }}</p>
        @endif

    </div>
</div>
@endsection
