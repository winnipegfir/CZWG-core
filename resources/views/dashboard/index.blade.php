@extends('layouts.dashboard')
@section('content')
@section('title', 'Dashboard - Winnipeg FIR')

<style>
/* ── Layout ─────────────────────────────────────────────── */
.db-page { background: #f1f5f9; min-height: calc(100vh - 60px); }

/* ── Hero ───────────────────────────────────────────────── */
.db-hero {
    background: linear-gradient(135deg, #080f1a 0%, #0d2035 50%, #122b44 100%);
    padding: 2.25rem 0 2rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    position: relative; overflow: hidden;
}
.db-hero-inner { display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
.db-avatar-wrap { position: relative; flex-shrink: 0; }
.db-avatar {
    width: 82px; height: 82px; border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.15);
    object-fit: cover; display: block;
}
.db-avatar-btn {
    position: absolute; bottom: 0; right: 0;
    width: 26px; height: 26px; border-radius: 50%;
    background: #fff; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 1px 4px rgba(0,0,0,0.35); padding: 0;
}
.db-avatar-btn i { font-size: 0.5rem; color: #334155; }
.db-identity { flex: 1; min-width: 0; }
.db-name { font-size: 1.55rem; font-weight: 800; color: #fff; margin: 0 0 0.15rem; line-height: 1.2; }
.db-sub { font-size: 0.82rem; color: rgba(255,255,255,0.45); margin: 0 0 0.6rem; }
.db-badge {
    display: inline-flex; align-items: center; gap: 0.28rem;
    padding: 0.2em 0.6em; border-radius: 999px;
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.02em;
}
.db-quiet-link { font-size: 0.68rem; color: rgba(255,255,255,0.28); text-decoration: none !important; }
.db-quiet-link:hover { color: rgba(255,255,255,0.55); }

/* Activity widget in hero */
.db-activity {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 0.75rem; padding: 1.1rem 1.25rem;
    min-width: 210px;
}
.db-activity-label {
    font-size: 0.62rem; font-weight: 700; letter-spacing: 0.09em;
    text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 0.45rem;
}
.db-activity-hours { font-size: 1.8rem; font-weight: 800; color: #fff; line-height: 1; }
.db-activity-of { font-size: 0.78rem; color: rgba(255,255,255,0.35); margin-left: 0.25rem; }
.db-activity-track {
    height: 5px; border-radius: 999px;
    background: rgba(255,255,255,0.1); overflow: hidden; margin: 0.5rem 0 0.35rem;
}
.db-activity-fill { height: 100%; border-radius: 999px; transition: width 0.5s ease; }
.db-activity-meta { display: flex; justify-content: space-between; align-items: center; }
.db-activity-end { font-size: 0.65rem; color: rgba(255,255,255,0.3); }
.db-activity-met { font-size: 0.65rem; color: #4ade80; font-weight: 700; }

/* ── Alert strip ─────────────────────────────────────────── */
.db-alert {
    display: flex; align-items: flex-start; gap: 0.75rem;
    border-radius: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 0.5rem;
}
.db-alert-icon { font-size: 0.95rem; flex-shrink: 0; margin-top: 2px; }
.db-alert-title { font-size: 0.84rem; font-weight: 600; line-height: 1.3; }
.db-alert-body  { font-size: 0.76rem; margin-top: 0.1rem; }

/* ── Cards ───────────────────────────────────────────────── */
.db-card {
    background: #fff; border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    padding: 1.25rem; margin-bottom: 1rem;
}
.db-card-label {
    font-size: 0.65rem; font-weight: 700; letter-spacing: 0.09em;
    text-transform: uppercase; color: #94a3b8; margin-bottom: 1rem;
}

/* ── Tile grid (quick actions) ───────────────────────────── */
.db-tile-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.6rem; }
.db-tile-grid-3 { grid-template-columns: repeat(3, 1fr); }
.db-tile {
    display: flex; flex-direction: column; gap: 0.4rem;
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.6rem;
    padding: 0.85rem 0.9rem; text-decoration: none !important; color: #122b44 !important;
    transition: background 0.12s, box-shadow 0.12s, transform 0.12s;
    text-align: left;
}
.db-tile:hover {
    background: #f0f4f8; border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07); transform: translateY(-1px);
}
.db-tile-icon {
    width: 30px; height: 30px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.82rem;
}
.db-tile-label { font-size: 0.8rem; font-weight: 600; line-height: 1.25; color: #1e293b; }
.db-tile-sub   { font-size: 0.68rem; color: #94a3b8; }

/* ── Mobile ──────────────────────────────────────────────── */
@media (max-width: 640px) {
    .db-hero { padding: 1.5rem 0 1.25rem; }
    .db-hero-inner { gap: 1rem; }
    .db-avatar { width: 64px; height: 64px; }
    .db-avatar-btn { width: 22px; height: 22px; }
    .db-name { font-size: 1.25rem; }
    .db-sub { font-size: 0.75rem; margin-bottom: 0.4rem; }
    .db-activity { min-width: 0; width: 100%; box-sizing: border-box; }
    .db-tile-grid-3 { grid-template-columns: repeat(2, 1fr); }
}

/* Resource rows */
.db-resource-row {
    display: flex; align-items: center;
    padding: 0.55rem 0; border-bottom: 1px solid #f1f5f9;
    font-size: 0.875rem; color: #334155;
}
.db-resource-row:last-child { border-bottom: none; }

/* Event rows */
.db-event-row {
    padding: 0.65rem 0; border-bottom: 1px solid #f1f5f9;
}
.db-event-row:last-child { border-bottom: none; }

/* Inline account row (Discord etc.) */
.db-acct-row {
    display: flex; align-items: center; gap: 0.65rem;
    padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9; font-size: 0.82rem;
}
.db-acct-row:last-child { border-bottom: none; }
</style>

<div class="db-page">

{{-- ═══════════════════════════════ HERO ═══════════════════════════════ --}}
<div class="db-hero">
    <div style="position:absolute; inset:0; pointer-events:none; overflow:hidden;">
        <img src="{{ Auth::user()->avatar() }}"
             style="position:absolute; left:50%; top:50%; transform:translate(-50%, -50%);
                    width:110%; height:200%; object-fit:cover;
                    filter:blur(45px); opacity:0.08;">
    </div>
    <div class="container" style="position:relative;">
        <div class="db-hero-inner">

            {{-- Identity --}}
            <div class="db-avatar-wrap">
                <img src="{{ Auth::user()->avatar() }}" class="db-avatar" alt="">
                <button class="db-avatar-btn" data-toggle="modal" data-target="#changeAvatar" title="Change avatar">
                    <i class="fas fa-pen"></i>
                </button>
            </div>
            <div class="db-identity">
                <h1 class="db-name" style="display:flex; align-items:center; gap:0.5rem;">
                    {{ Auth::user()->fullName('F') }}
                    <button data-toggle="modal" data-target="#changeDisplayNameModal"
                        style="background:none; border:none; padding:0; cursor:pointer; opacity:0.35; line-height:1;"
                        title="Change display name">
                        <i class="fas fa-pen" style="font-size:0.75rem; color:#fff;"></i>
                    </button>
                </h1>
                <p class="db-sub" style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    {{ Auth::user()->id }}
                    &middot;
                    {{ Auth::user()->rating->getShortName() }}
                    @if(Auth::user()->staffProfile)
                        &middot; {{ Auth::user()->staffProfile->position }}
                    @endif
                </p>
                <div style="display:flex; flex-wrap:wrap; gap:0.4rem; align-items:center;">
                    @if($certification == 'home')
                        <span class="db-badge" style="background:rgba(34,197,94,0.18); color:#4ade80;"><i class="fa fa-user-check fa-xs"></i> CZWG Controller</span>
                    @elseif($certification == 'certified')
                        <span class="db-badge" style="background:rgba(34,197,94,0.18); color:#4ade80;"><i class="fa fa-check fa-xs"></i> Certified</span>
                    @elseif($certification == 'training')
                        <span class="db-badge" style="background:rgba(251,191,36,0.18); color:#fbbf24;"><i class="fa fa-book-open fa-xs"></i> In Training</span>
                    @elseif($certification == 'visit')
                        <span class="db-badge" style="background:rgba(96,165,250,0.18); color:#93c5fd;"><i class="fa fa-plane fa-xs"></i> Visiting Controller</span>
                    @elseif($certification == 'instructor')
                        <span class="db-badge" style="background:rgba(167,139,250,0.18); color:#c4b5fd;"><i class="fa fa-chalkboard-teacher fa-xs"></i> Instructor</span>
                    @elseif($certification == 'not_certified')
                        <span class="db-badge" style="background:rgba(248,113,113,0.18); color:#fca5a5;"><i class="fa fa-times fa-xs"></i> Not Certified</span>
                    @endif

                    @if($active == 1)
                        <span class="db-badge" style="background:rgba(34,197,94,0.1); color:#4ade80;"><i class="fa fa-circle fa-xs" style="font-size:0.4rem;"></i> Active</span>
                    @elseif($active === 0 && $certification)
                        <span class="db-badge" style="background:rgba(248,113,113,0.18); color:#fca5a5;"><i class="fa fa-circle fa-xs" style="font-size:0.4rem;"></i> Inactive</span>
                    @endif

                    <a href="#" data-toggle="modal" data-target="#ratingChange" class="db-quiet-link">Rating wrong?</a>
                </div>
            </div>

            {{-- Hero right widget: activity takes priority; waitlist only if not on roster --}}
            @if($yourinstructor && $yourinstructor->status == 0 && !(Auth::user()->rosterProfile && Auth::user()->rosterProfile->status != 'not_certified'))
                @php
                    $wlLabel = match($yourinstructor->entry_type) {
                        'New Student'  => 'Home',
                        'New Visitor'  => 'Visitor',
                        'New Transfer' => 'Transfer',
                        default        => $yourinstructor->entry_type,
                    };
                @endphp
                <div class="db-activity">
                    <div class="db-activity-label">Training Waitlist</div>
                    @if($waitlistPosition)
                        <div>
                            <span class="db-activity-hours">#{{ $waitlistPosition }}</span>
                            <span class="db-activity-of">of {{ $waitlistTypeTotal }}</span>
                        </div>
                        <div style="font-size:0.72rem; color:rgba(255,255,255,0.4); margin-top:0.3rem;">{{ $wlLabel }} queue</div>
                        @if($yourinstructor->waitlist_added_at)
                            <div style="font-size:0.65rem; color:rgba(255,255,255,0.3); margin-top:0.35rem;">
                                Since {{ $yourinstructor->waitlist_added_at->format('M j, Y') }} &mdash; {{ $yourinstructor->waitlist_added_at->diffForHumans() }}
                            </div>
                        @endif
                    @else
                        <div style="font-size:0.9rem; color:rgba(255,255,255,0.6); margin-top:0.25rem;">On the waitlist</div>
                    @endif
                </div>
            @elseif(!(Auth::user()->rosterProfile && Auth::user()->rosterProfile->status != 'not_certified'))
                <div class="db-activity" style="text-align:center;">
                    <div class="db-activity-label" style="margin-bottom:0.6rem;">Looking to Conrtol?</div>
                    <div style="font-size:0.82rem; color:rgba(255,255,255,0.45); margin-bottom:0.85rem; line-height:1.4;">
                        Join the Winnipeg FIR and start your ATC training journey.
                    </div>
                    <a href="{{ route('join.public') }}"
                       style="display:inline-block; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); color:#fff; font-size:0.78rem; font-weight:600; padding:0.4em 1em; border-radius:999px; text-decoration:none; transition:background 0.15s;"
                       onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                        Apply to join &rarr;
                    </a>
                </div>
            @elseif(Auth::user()->rosterProfile && Auth::user()->rosterProfile->status != 'not_certified')
                @php
                    $hours    = Auth::user()->rosterProfile->currency ?? 0;
                    $reqHours = 3;
                    $pct      = min(100, ($hours / $reqHours) * 100);
                    $met      = $hours >= $reqHours;
                    $fillColor = $met ? '#4ade80' : ($pct > 0 ? '#fbbf24' : '#f87171');
                @endphp
                <div class="db-activity">
                    <div class="db-activity-label">Activity This Quarter</div>
                    <div style="display:flex; align-items:baseline; gap:1rem; flex-wrap:wrap;">
                        <div>
                            <span class="db-activity-hours">{{ $hours < 0.1 ? '0:00' : decimal_to_hm($hours) }}</span>
                            <span class="db-activity-of">/ {{ decimal_to_hm($reqHours) }}</span>
                        </div>
                        @if($totalVatsimHours !== null)
                        @php $outsideHours = max(0, $totalVatsimHours - $hours); @endphp
                        <div style="font-size:0.7rem; color:rgba(255,255,255,0.4); white-space:nowrap;">
                            <span style="color:rgba(255,255,255,0.75); font-weight:600;">{{ decimal_to_hm($hours) }}</span> FIR
                            &nbsp;·&nbsp;
                            <span style="color:rgba(255,255,255,0.75); font-weight:600;">{{ decimal_to_hm($outsideHours) }}</span> outside
                            &nbsp;·&nbsp;
                            <span style="color:rgba(255,255,255,0.75); font-weight:600;">{{ decimal_to_hm($totalVatsimHours) }}</span> total
                        </div>
                        @endif
                    </div>
                    <div class="db-activity-track">
                        <div class="db-activity-fill" style="width:{{ $pct }}%; background:{{ $fillColor }};"></div>
                    </div>
                    <div class="db-activity-meta">
                        <span class="db-activity-end">Ends {{ \Carbon\Carbon::now()->endOfQuarter()->format('M j, Y') }}</span>
                        @if($met)
                            <span class="db-activity-met"><i class="fas fa-check-circle fa-xs"></i> Requirement met</span>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>{{-- end hero --}}

{{-- ═══════════════════════════════ CONTENT ══════════════════════════════ --}}
<div class="container py-4">

    {{-- ── Alerts ── --}}
    @php
        $hasAlerts = ($active === 0 && $certification)
            || count($openTickets) > 0
            || count($unconfirmedapp) > 0
            || (Auth::user()->permissions >= 4 && count($staffTickets) > 0);
    @endphp
    @if($hasAlerts)
    <div style="margin-bottom:1.5rem;">

        @if($active === 0 && $certification)
        <div class="db-alert" style="background:#fef2f2; border:1px solid #fecaca;">
            <i class="fas fa-exclamation-circle db-alert-icon" style="color:#ef4444;"></i>
            <div>
                <div class="db-alert-title" style="color:#b91c1c;">You are listed as inactive</div>
                <div class="db-alert-body" style="color:#dc2626;">Contact staff to be added back to the active roster.</div>
            </div>
        </div>
        @endif

        @if(count($openTickets) > 0)
        <div class="db-alert" style="background:#eff6ff; border:1px solid #bfdbfe;">
            <i class="fas fa-ticket-alt db-alert-icon" style="color:#2563eb;"></i>
            <div style="flex:1;">
                <div class="db-alert-title" style="color:#1d4ed8;">
                    {{ count($openTickets) }} open support ticket{{ count($openTickets) != 1 ? 's' : '' }}
                </div>
                @foreach($openTickets as $ticket)
                    <a href="{{ url('/dashboard/tickets/'.$ticket->ticket_id) }}" class="db-alert-body" style="color:#2563eb; display:block;">
                        {{ $ticket->title }} <span style="color:#93c5fd;">&middot; {{ $ticket->updated_at_pretty() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        @if(count($unconfirmedapp) > 0)
        <div class="db-alert" style="background:#f0fdf4; border:1px solid #bbf7d0;">
            <i class="fas fa-calendar-check db-alert-icon" style="color:#16a34a;"></i>
            <div>
                <div class="db-alert-title" style="color:#15803d;">
                    {{ count($unconfirmedapp) }} pending event application{{ count($unconfirmedapp) != 1 ? 's' : '' }}
                </div>
                <a href="" data-target="#unconfirmedEvents" data-toggle="modal" class="db-alert-body" style="color:#16a34a;">View applications</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->permissions >= 4 && count($staffTickets) > 0)
        <div class="db-alert" style="background:#fdf4ff; border:1px solid #e9d5ff;">
            <i class="fas fa-inbox db-alert-icon" style="color:#9333ea;"></i>
            <div style="flex:1;">
                <div class="db-alert-title" style="color:#7e22ce;">
                    {{ count($staffTickets) }} open staff ticket{{ count($staffTickets) != 1 ? 's' : '' }}
                </div>
                <a href="{{ route('tickets.staff') }}" class="db-alert-body" style="color:#9333ea;">Open staff inbox &rarr;</a>
            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- ── Main grid ── --}}
    <div class="row">

        {{-- LEFT ─ tile navigation --}}
        <div class="col-lg-8">

            {{-- Quick Actions --}}
            <div class="db-card">
                <div class="db-card-label">Quick Actions</div>
                <div class="db-tile-grid db-tile-grid-3">
                    <a href="https://training.winnipegfir.ca" target="_blank" class="db-tile">
                        <div class="db-tile-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-graduation-cap"></i></div>
                        <span class="db-tile-label">Winnipeg365 Training</span>
                    </a>
                    <a href="{{ route('tickets.index', ['create' => 'yes']) }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-plus-circle"></i></div>
                        <span class="db-tile-label">New Ticket</span>
                    </a>
                    <a href="{{ route('feedback.create') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#fdf4ff; color:#9333ea;"><i class="fas fa-comment-alt"></i></div>
                        <span class="db-tile-label">Send Feedback</span>
                    </a>
                    <a href="{{ route('tickets.index') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#fff7ed; color:#ea580c;"><i class="fas fa-inbox"></i></div>
                        <span class="db-tile-label">My Tickets</span>
                    </a>
                    @if(count($confirmedevent) > 0)
                    <a href="{{ url('/dashboard/events/view') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-calendar-alt"></i></div>
                        <span class="db-tile-label">Event Rosters</span>
                    </a>
                    @endif
                    @if(false)
                    <a href="#" class="db-tile">
                        <div class="db-tile-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-calendar-check"></i></div>
                        <span class="db-tile-label">ATC Bookings</span>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Instructor tools (instructor role, not full staff) --}}
            @if(Auth::user()->instructorProfile !== null && Auth::user()->permissions < 4)
            <div class="db-card">
                <div class="db-card-label">Instructor</div>
                <div class="db-tile-grid db-tile-grid-3">
                    <a href="{{ route('training.index') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-chalkboard-teacher"></i></div>
                        <span class="db-tile-label">Training Management</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- Staff --}}
            @if(Auth::user()->permissions >= 4)
            <div class="db-card">
                <div class="db-card-label">Staff</div>
                <div class="db-tile-grid db-tile-grid-3">
                    <a href="{{ route('training.index') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-graduation-cap"></i></div>
                        <span class="db-tile-label">Training Management</span>
                    </a>
                    <a href="{{ route('roster.index') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-users"></i></div>
                        <span class="db-tile-label">Manage Roster</span>
                    </a>
                    <a href="{{ route('events.admin.index') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#fdf4ff; color:#9333ea;"><i class="fas fa-calendar-alt"></i></div>
                        <span class="db-tile-label">Manage Events</span>
                    </a>
                    <a href="{{ route('news.index') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#fff7ed; color:#ea580c;"><i class="fas fa-newspaper"></i></div>
                        <span class="db-tile-label">Manage News</span>
                    </a>
                    <a href="{{ route('staff.feedback.index') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#fffbeb; color:#d97706;"><i class="fas fa-comment-alt"></i></div>
                        <span class="db-tile-label">Feedback Inbox</span>
                    </a>
                    <a href="{{ route('users.viewall') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#f8fafc; color:#64748b;"><i class="fas fa-user-cog"></i></div>
                        <span class="db-tile-label">Manage Users</span>
                    </a>
                    <a href="{{ route('dashboard.upload') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-upload"></i></div>
                        <span class="db-tile-label">File Uploader</span>
                    </a>
                    @if(Auth::user()->permissions >= 5)
                    <a href="{{ route('settings.index') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#fdf4ff; color:#9333ea;"><i class="fas fa-cog"></i></div>
                        <span class="db-tile-label">Site Settings</span>
                    </a>
                    <a href="{{ route('network.index') }}" class="db-tile">
                        <div class="db-tile-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-network-wired"></i></div>
                        <span class="db-tile-label">Network Data</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif


        </div>{{-- end left col --}}

        {{-- RIGHT ─ info & context --}}
        <div class="col-lg-4">

            {{-- ATC Resources --}}
            @if((Auth::user()->rosterProfile && Auth::user()->rosterProfile->status != 'not_certified') || Auth::user()->permissions >= 4)
            <div class="db-card">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.9rem;">
                    <div class="db-card-label" style="margin-bottom:0;">ATC Resources</div>
                    @if(Auth::user()->permissions >= 4)
                        <a href="{{ route('atcresources.index') }}" style="font-size:0.72rem; color:#94a3b8; text-decoration:none;">
                            <i class="fa fa-edit fa-xs"></i> Manage
                        </a>
                    @endif
                </div>
                @forelse($atcResources as $resource)
                    @if(!$resource->atc_only || Auth::user()->permissions >= 1)
                    <div class="db-resource-row" style="{{ $loop->last ? 'border-bottom:none;' : '' }}">
                        <span style="flex:1;">{{ $resource->title }}</span>
                        <a href="{{ $resource->url }}" target="_blank"
                           style="color:#94a3b8; font-size:0.8rem; margin-left:0.75rem; text-decoration:none;">
                            <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </div>
                    @endif
                @empty
                    <p style="font-size:0.82rem; color:#94a3b8; margin:0;">No resources yet.</p>
                @endforelse

                @if($yourinstructor && $yourinstructor->status != 0 && $yourinstructor->instructor != null)
                <div style="display:flex; align-items:center; gap:0.75rem; margin-top:0.9rem; padding-top:0.9rem; border-top:1px solid #f1f5f9;">
                    <div style="width:34px; height:34px; border-radius:8px; background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-chalkboard-teacher" style="color:#2563eb; font-size:0.82rem;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.7rem; font-weight:700; letter-spacing:0.07em; text-transform:uppercase; color:#94a3b8; margin-bottom:0.15rem;">Your Instructor</div>
                        <div style="font-size:0.875rem; font-weight:600; color:#1e293b;">{{ $yourinstructor->instructor->user->fullName('FL') }}</div>
                        <div style="font-size:0.72rem; color:#94a3b8;">{{ $yourinstructor->instructor->email }}</div>
                    </div>
                </div>
                @elseif($certification == 'training' && !($yourinstructor && $yourinstructor->status == 0))
                <div style="margin-top:0.9rem; padding-top:0.9rem; border-top:1px solid #f1f5f9; font-size:0.82rem; color:#94a3b8;">
                    No instructor assigned yet. Contact staff if you have questions.
                </div>
                @endif
            </div>
            @endif

            @if(false)
            {{-- ATC Bookings (hidden until VATSIM_BOOKING_KEY is available) --}}
            {{-- To enable: remove @if(false)/@endif, replace $previewBookings with $myBookings, update href to route('bookings.index') --}}
            @php
                $previewBookings = collect([
                    ['callsign' => 'CZWG_CTR', 'start' => '2026-06-03 14:00:00', 'end' => '2026-06-03 17:00:00'],
                    ['callsign' => 'CYWG_APP', 'start' => '2026-06-05 22:00:00', 'end' => '2026-06-05 23:30:00'],
                ]);
            @endphp
            <div class="db-card">
                <div class="db-card-label">ATC Bookings</div>
                @foreach($previewBookings as $b)
                @php $start = \Carbon\Carbon::parse($b['start']); $end = \Carbon\Carbon::parse($b['end']); @endphp
                <div class="db-resource-row">
                    <div style="flex:1;">
                        <div style="font-weight:600; font-size:0.875rem; color:#1e293b;">{{ $b['callsign'] }}</div>
                        <div style="font-size:0.72rem; color:#64748b;">{{ $start->format('M j') }} &middot; {{ $start->format('H:i') }}z &ndash; {{ $end->format('H:i') }}z</div>
                    </div>
                </div>
                @endforeach
                <a href="#" style="font-size:0.78rem; color:#2563eb; text-decoration:none; display:inline-block; margin-top:0.65rem;">
                    Manage bookings &rarr;
                </a>
            </div>
            @endif

            {{-- Upcoming Events --}}
            <div class="db-card">
                <div class="db-card-label">Upcoming Events</div>
                @if(count($confirmedevent) < 1)
                    <p style="font-size:0.875rem; color:#94a3b8; margin:0;">No scheduled events right now.</p>
                @else
                    @foreach($confirmedevent as $cevent)
                    <div class="db-event-row">
                        <div style="font-weight:700; font-size:0.9rem; color:#1e293b;">{{ $cevent->name }}</div>
                        <div style="font-size:0.76rem; color:#64748b; margin-top:0.15rem;">{{ $cevent->start_timestamp_pretty() }}</div>
                        @foreach($confirmedapp as $capp)
                            @if($cevent->id == $capp->event->id)
                            <div style="font-size:0.76rem; color:#475569; margin-top:0.3rem; padding-left:0.6rem; border-left:2px solid #e2e8f0;">
                                <i class="fas fa-map-marker-alt fa-xs" style="color:#94a3b8;"></i>
                                {{ $capp->airport }}
                                @if($capp->position != 'Relief') {{ $capp->position }} @endif
                                {{ $capp->start_timestamp }}z &ndash; {{ $capp->end_timestamp }}z
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @endforeach
                    <a href="{{ url('/dashboard/events/view') }}" style="font-size:0.78rem; color:#2563eb; text-decoration:none; display:inline-block; margin-top:0.65rem;">
                        View all event rosters &rarr;
                    </a>
                @endif
            </div>

            {{-- Account --}}
            <div class="db-card">
                <div class="db-card-label">Account</div>

                {{-- Discord --}}
                <div style="display:flex; align-items:center; gap:0.75rem; padding:0.6rem 0; border-bottom:1px solid #f1f5f9;">
                    <div style="width:32px; height:32px; border-radius:8px; background:#eef2ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        @if(Auth::user()->hasDiscord())
                            <img src="{{ Auth::user()->getDiscordAvatar() }}" style="width:32px; height:32px; border-radius:8px; object-fit:cover;">
                        @else
                            <i class="fab fa-discord" style="color:#7289da; font-size:0.85rem;"></i>
                        @endif
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:0.82rem; font-weight:600; color:#1e293b;">Discord</div>
                        <div style="font-size:0.72rem; color:#94a3b8;">
                            @if(Auth::user()->hasDiscord()){{ Auth::user()->getDiscordUser()->username }}@else Not linked @endif
                        </div>
                    </div>
                    @if(Auth::user()->hasDiscord())
                        <a href="#" data-toggle="modal" data-target="#discordModal" style="font-size:0.72rem; color:#ef4444; text-decoration:none; white-space:nowrap;">Unlink</a>
                    @else
                        <a href="#" data-toggle="modal" data-target="#discordModal" style="font-size:0.72rem; color:#2563eb; text-decoration:none; white-space:nowrap;">Link &rarr;</a>
                    @endif
                </div>

                @if(Auth::user()->hasDiscord() && !Auth::user()->memberOfCZWGGuild())
                <div style="padding:0.5rem 0.65rem; background:#eef2ff; border-radius:0.4rem; font-size:0.72rem; color:#4338ca; display:flex; align-items:center; gap:0.5rem; margin:0.4rem 0;">
                    <i class="fab fa-discord fa-xs"></i>
                    <span style="flex:1;">You're not in the CZWG Discord server.</span>
                    <a href="#" data-toggle="modal" data-target="#joinDiscordServerModal" style="color:#4338ca; font-weight:600; text-decoration:none; white-space:nowrap;">Join &rarr;</a>
                </div>
                @endif

                {{-- Emails --}}
                <div style="display:flex; align-items:center; gap:0.75rem; padding:0.6rem 0; border-bottom:1px solid #f1f5f9;">
                    <div style="width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; {{ Auth::user()->gdpr_subscribed_emails ? 'background:#f0fdf4;' : 'background:#fef2f2;' }}">
                        <i class="fas fa-envelope" style="font-size:0.82rem; {{ Auth::user()->gdpr_subscribed_emails ? 'color:#16a34a;' : 'color:#ef4444;' }}"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:0.82rem; font-weight:600; color:#1e293b;">Email notifications</div>
                        <div style="font-size:0.72rem; color:#94a3b8;">{{ Auth::user()->gdpr_subscribed_emails ? 'Subscribed' : 'Not subscribed' }}</div>
                    </div>
                    @if(Auth::user()->gdpr_subscribed_emails == 0)
                        <a href="{{ url('/dashboard/emailpref/subscribe') }}" style="font-size:0.72rem; color:#16a34a; text-decoration:none; white-space:nowrap;">Subscribe &rarr;</a>
                    @else
                        <a href="{{ url('/dashboard/emailpref/unsubscribe') }}" style="font-size:0.72rem; color:#ef4444; text-decoration:none; white-space:nowrap;">Unsubscribe</a>
                    @endif
                </div>

                {{-- Biography --}}
                <div style="display:flex; align-items:center; gap:0.75rem; padding:0.6rem 0; border-bottom:1px solid #f1f5f9;">
                    <div style="width:32px; height:32px; border-radius:8px; background:#f8fafc; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-align-left" style="color:#94a3b8; font-size:0.82rem;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:0.82rem; font-weight:600; color:#1e293b;">Biography</div>
                    </div>
                    <button data-toggle="modal" data-target="#bioModal" style="background:none; border:none; padding:0; font-size:0.72rem; color:#2563eb; cursor:pointer; white-space:nowrap;">Edit &rarr;</button>
                </div>

                {{-- My Data --}}
                <div style="display:flex; align-items:center; gap:0.75rem; padding:0.6rem 0;">
                    <div style="width:32px; height:32px; border-radius:8px; background:#f8fafc; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-database" style="color:#94a3b8; font-size:0.82rem;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:0.82rem; font-weight:600; color:#1e293b;">My Data</div>
                    </div>
                    <a href="{{ route('me.data') }}" style="font-size:0.72rem; color:#2563eb; text-decoration:none; white-space:nowrap;">Manage &rarr;</a>
                </div>

            </div>

        </div>{{-- end right col --}}

    </div>
</div>
</div>{{-- end db-page --}}

{{-- ═══════════════════════════════ MODALS ════════════════════════════════ --}}

<div class="modal fade" id="changeAvatar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Avatar</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="post" action="{{ route('users.changeavatar') }}" enctype="multipart/form-data">
                <div class="modal-body">
                    <p class="small text-muted">Please ensure your avatar complies with the VATSIM Code of Conduct.</p>
                    @csrf
                    <div class="input-group pb-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file">
                            <label class="custom-file-label">Choose file</label>
                        </div>
                    </div>
                    @if(Auth::user()->hasDiscord())
                        <p class="small mb-1">Or use your Discord avatar (refreshes every 6 hours):</p>
                        <div class="d-flex align-items-center gap-2">
                            <img style="border-radius:50%; height:48px;" src="{{ Auth::user()->getDiscordAvatar() }}" alt="">
                            <a href="{{ route('users.changeavatar.discord') }}" class="btn btn-sm btn-outline-secondary ml-2">Use Discord Avatar</a>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-success" value="Upload">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ratingChange" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">How We Update VATSIM Ratings</h5>
            </div>
            <div class="modal-body">
                <p class="mb-1">Your rating updates in two ways: on every login, and nightly at 00:00 Eastern via the VATSIM API.</p>
                <hr>
                <p class="mb-0">To fix it now: <a href="/logout">log out</a> and log back in, or wait until 00:00 Eastern.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary" data-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changeDisplayNameModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Display Name</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('users.changedisplayname') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">First Name</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->display_fname }}" name="display_fname" id="input_display_fname">
                        <script>function resetToCertFirstName() { $("#input_display_fname").val("{{ Auth::user()->fname }}") }</script>
                        <a class="btn btn-sm btn-outline-secondary mt-2" role="button" onclick="resetToCertFirstName()">Reset to CERT name</a>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Display Format</label>
                        <select name="format" class="form-control">
                            <option value="showall">First name, last name, and CID</option>
                            <option value="showfirstcid">First name and CID</option>
                            <option value="showcid">CID only</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-success" value="Save">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bioModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Biography</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="post" action="{{ route('me.editbio') }}">
                @csrf
                <div class="modal-body">
                    <textarea name="bio" class="form-control mb-2" rows="5">{{ Auth::user()->bio }}</textarea>
                    <p class="small text-muted mb-0">Please ensure this complies with the VATSIM Code of Conduct.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="discordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        @if(!Auth::user()->hasDiscord())
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Link Discord Account</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <img style="height:44px;" src="{{ asset('/img/discord/CZWGplusdiscord.png') }}" class="img-fluid mb-3" alt="">
                    <p class="mb-1">Linking your Discord account lets you:</p>
                    <ul class="small">
                        <li>Join the CZWG Discord community</li>
                        <li>Receive ticket, training, and event notifications</li>
                        <li>Use your Discord avatar on this site</li>
                    </ul>
                    <p class="small text-muted mb-0">See the <a href="{{ url('/privacy') }}">privacy policy</a> for details on data stored.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <a href="{{ route('me.discord.link') }}" class="btn btn-primary">Link Account</a>
                </div>
            </div>
        @else
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Unlink Discord Account</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Unlinking will remove you from the CZWG Discord, remove your Discord avatar, and stop Discord notifications.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <a href="{{ route('me.discord.unlink') }}" class="btn btn-danger">Unlink</a>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="joinDiscordServerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Join the CZWG Discord</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Clicking Join will redirect you to Discord. The VATSIM Code of Conduct applies; please show respect, avoid spam, and don't send unsolicited DMs or server invites.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <a href="{{ route('me.discord.join') }}" class="btn btn-primary">Join</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unconfirmedEvents" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Your Pending Event Applications</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                @foreach($confirmedevent as $cevent)
                    @foreach($unconfirmedapp as $uapp)
                        @if($cevent->name == $uapp->event->name)
                            <p class="font-weight-bold mb-1">{{ $cevent->name }} <span class="font-weight-normal text-muted">&mdash; {{ $cevent->start_timestamp_pretty() }}</span></p>
                            <p class="mb-2 ml-2" style="font-size:0.875rem;">
                                <strong>Requested:</strong> {{ $uapp->position }}
                                &mdash; {{ $uapp->start_availability_timestamp }}z &ndash; {{ $uapp->end_availability_timestamp }}z
                            </p>
                        @endif
                    @endforeach
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@stop
