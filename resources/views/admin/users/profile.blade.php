@extends('layouts.master')
@section('navbarprim')
    @parent
@stop

@section('content')
@php
$statusMap = [
    'home'          => ['label' => 'Home Controller',     'icon' => 'fa-user-check',          'color' => '#122b44',  'text' => '#fff'],
    'visit'         => ['label' => 'Visiting Controller', 'icon' => 'fa-plane',               'color' => '#0ea5e9',  'text' => '#fff'],
    'training'      => ['label' => 'In Training',         'icon' => 'fa-book-open',           'color' => '#d97706',  'text' => '#fff'],
    'instructor'    => ['label' => 'Instructor',          'icon' => 'fa-chalkboard-teacher',  'color' => '#7c3aed',  'text' => '#fff'],
    'certified'     => ['label' => 'CZWG Certified',      'icon' => 'fa-check',               'color' => '#16a34a',  'text' => '#fff'],
    'not_certified' => ['label' => 'Not Certified',       'icon' => 'fa-times',               'color' => '#dc2626',  'text' => '#fff'],
];
$status = $statusMap[$certification] ?? ['label' => 'Unknown', 'icon' => 'fa-question', 'color' => '#6b7280', 'text' => '#fff'];

$reqHours = $user->rosterProfile?->status !== null && $user->rosterProfile?->status !== 'not_certified' ? 3 : null;
@endphp

<style>
.au-hero {
    background: linear-gradient(135deg, #0a1828 0%, #0d1f33 60%, #122b44 100%);
    border-bottom: 1px solid rgba(255,255,255,0.08);
    padding: 1.75rem 0 1.5rem;
    color: #fff;
}
.au-hero a.back-link {
    display: inline-flex; align-items: center; gap: 0.4rem;
    color: rgba(255,255,255,0.45); font-size: 0.78rem;
    text-decoration: none; margin-bottom: 1rem;
    transition: color 0.15s;
}
.au-hero a.back-link:hover { color: rgba(255,255,255,0.9); }
.au-header { display: flex; align-items: center; gap: 1.1rem; flex-wrap: wrap; }
.au-avatar { width: 72px; height: 72px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.15); object-fit: cover; flex-shrink: 0; }
.au-name { font-size: 1.5rem; font-weight: 800; line-height: 1.1; margin: 0; }
.au-sub { color: rgba(255,255,255,0.45); font-size: 0.8rem; margin-top: 0.2rem; }
.au-badge {
    display: inline-flex; align-items: center; gap: 0.3rem;
    font-size: 0.65rem; font-weight: 700; letter-spacing: 0.08em;
    padding: 0.25rem 0.6rem; border-radius: 4px; margin-right: 0.35rem; margin-top: 0.5rem;
}
.au-system-alert {
    background: rgba(234,179,8,0.15); border: 1px solid rgba(234,179,8,0.3);
    color: #fbbf24; border-radius: 6px; padding: 0.55rem 0.85rem;
    font-size: 0.8rem; margin-top: 0.85rem;
    display: flex; align-items: center; gap: 0.5rem;
}
.au-body { background: #f6f8fa; padding: 1.75rem 0 2.5rem; }
.au-card {
    background: #fff; border: 1px solid rgba(0,0,0,0.08);
    border-radius: 8px; padding: 1.25rem 1.35rem; margin-bottom: 1.1rem;
}
.au-card-title {
    font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: rgba(0,0,0,0.3); margin-bottom: 1rem;
}
.au-row { display: flex; align-items: baseline; gap: 0.5rem; padding: 0.3rem 0; border-bottom: 1px solid rgba(0,0,0,0.05); font-size: 0.875rem; }
.au-row:last-child { border-bottom: none; }
.au-row-lbl { color: rgba(0,0,0,0.35); font-size: 0.72rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; width: 130px; flex-shrink: 0; }
.au-row-val { color: rgba(0,0,0,0.75); flex: 1; }
.au-status-pill {
    display: inline-flex; align-items: center; gap: 0.3rem;
    font-size: 0.68rem; font-weight: 700; padding: 0.22rem 0.6rem;
    border-radius: 20px; letter-spacing: 0.04em;
}
.au-activity-bar-track {
    height: 6px; background: rgba(0,0,0,0.07); border-radius: 3px; overflow: hidden; flex: 1;
}
.au-activity-bar-fill { height: 100%; border-radius: 3px; transition: width 0.5s ease; }
.au-form-row { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 0.85rem; }
.modal .au-form-group select { color: #1a2a3a !important; border: 1px solid #ced4da !important; border-radius: 4px !important; }
.au-form-group { flex: 1; min-width: 160px; }
.au-form-group label { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase; color: rgba(0,0,0,0.4); display: block; margin-bottom: 0.35rem; }
.au-form-group select option { color: #1a2a3a !important; background: #fff !important; }
.au-form-group select,
.au-form-group select:focus {
    width: 100%;
    color: #1a2a3a !important;
    background: #fff !important;
    border: 1px solid #ced4da !important;
    border-bottom: 1px solid #ced4da !important;
    border-radius: 4px !important;
    padding: 0.3rem 0.6rem !important;
    font-size: 0.875rem !important;
    appearance: auto !important;
    box-shadow: none !important;
    outline: none !important;
}
.au-note-item { padding: 0.75rem 0; border-bottom: 1px solid rgba(0,0,0,0.06); }
.au-note-item:last-child { border-bottom: none; }
.au-note-meta { font-size: 0.7rem; color: rgba(0,0,0,0.35); margin-bottom: 0.2rem; }
.au-note-body { font-size: 0.85rem; color: rgba(0,0,0,0.7); }
.au-disc-row { display: flex; align-items: center; gap: 0.6rem; padding: 0.35rem 0; font-size: 0.875rem; }
</style>

{{-- HERO --}}
<div class="au-hero">
    <div class="container">
        <a href="{{ route('users.viewall') }}" class="back-link">
            <i class="fas fa-arrow-left fa-xs"></i> All Users
        </a>
        <div class="au-header">
            <img src="{{ $user->avatar() }}" class="au-avatar" alt="">
            <div>
                <h1 class="au-name" style="display:flex; align-items:center; gap:0.4rem;">
                    {{ $user->fullName('FL') }}
                    @if($user->fname != $user->display_fname || !$user->display_last_name || $user->display_cid_only)
                    <span title="Display name does not match their CERT name"
                          style="font-size:0.7rem; font-weight:600; background:rgba(249,115,22,0.2); color:#fb923c; border:1px solid rgba(249,115,22,0.35); border-radius:999px; padding:0.1em 0.5em; white-space:nowrap;">
                        <i class="fas fa-exclamation-triangle fa-xs"></i> Name mismatch
                    </span>
                    @endif
                </h1>
                <div class="au-sub">CID {{ $user->id }} &nbsp;·&nbsp; {{ $user->rating->getLongName() }}</div>
                <div style="margin-top:0.4rem;">
                    <span class="au-badge" style="background:{{ $status['color'] }}; color:{{ $status['text'] }};">
                        <i class="fas {{ $status['icon'] }} fa-xs"></i> {{ $status['label'] }}
                    </span>
                    @if($active == 1)
                        <span class="au-badge" style="background:#dcfce7; color:#15803d;">
                            <i class="fas fa-check fa-xs"></i> Active
                        </span>
                    @elseif($active == 0 && $certification !== null)
                        <span class="au-badge" style="background:#fee2e2; color:#b91c1c;">
                            <i class="fas fa-times fa-xs"></i> Inactive
                        </span>
                    @endif
                    <span class="au-badge" style="background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.8);">
                        <i class="fas fa-shield-alt fa-xs"></i> {{ $user->permissions() }}
                    </span>
                </div>
            </div>
        </div>
        @if($user->id == 1 || $user->id == 2)
        <div class="au-system-alert">
            <i class="fas fa-info-circle"></i> This is a system account used for automatic actions or as a placeholder.
        </div>
        @endif
    </div>
</div>

{{-- BODY --}}
<div class="au-body">
    <div class="container">
        <div class="row">

            {{-- Left --}}
            <div class="col-md-7">

                {{-- Identity --}}
                <div class="au-card">
                    <div class="au-card-title">Identity</div>
                    <div class="au-row">
                        <span class="au-row-lbl">CID</span>
                        <span class="au-row-val">{{ $user->id }}</span>
                    </div>
                    @if(Auth::user()->permissions == 5)
                    <div class="au-row">
                        <span class="au-row-lbl">CERT Name</span>
                        <span class="au-row-val">{{ $user->fname }} {{ $user->lname }}</span>
                    </div>
                    @endif
                    <div class="au-row">
                        <span class="au-row-lbl">Display Name</span>
                        <span class="au-row-val">{{ $user->fullName('FLC') }}</span>
                        @if($user->display_fname !== $user->fname || !$user->display_last_name || $user->display_cid_only)
                        <form action="{{ route('users.resetusersname') }}" method="POST" style="display:inline; margin-left:0.5rem;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Reset</button>
                        </form>
                        @endif
                    </div>
                    <div class="au-row">
                        <span class="au-row-lbl">Rating</span>
                        <span class="au-row-val">{{ $user->rating->getLongName() }} ({{ $user->rating->getShortName() }})</span>
                    </div>
                    <div class="au-row">
                        <span class="au-row-lbl">Email</span>
                        <span class="au-row-val"><a href="mailto:{{ $user->email }}" style="color:#122b44;">{{ $user->email }}</a></span>
                    </div>
                </div>

                {{-- Activity --}}
                @if($user->rosterProfile && $reqHours !== null)
                @php
                    $hrs = $user->rosterProfile->currency;
                    $pct = min(100, round(($hrs / $reqHours) * 100));
                    $met = $hrs >= $reqHours;
                @endphp
                <div class="au-card">
                    <div class="au-card-title">Activity This Quarter</div>
                    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:0.75rem;">
                        <span style="font-size:1.4rem; font-weight:800; color:#122b44; font-variant-numeric:tabular-nums;">{{ decimal_to_hm($hrs) }}</span>
                        <span style="font-size:0.78rem; color:rgba(0,0,0,0.4);">of {{ decimal_to_hm($reqHours) }} required</span>
                        @if($met)
                        <span class="au-badge" style="background:#dcfce7; color:#15803d; margin:0;"><i class="fas fa-check fa-xs"></i> Requirement met</span>
                        @else
                        <span class="au-badge" style="background:#fee2e2; color:#b91c1c; margin:0;"><i class="fas fa-times fa-xs"></i> Requirement not met</span>
                        @endif
                    </div>
                    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.5rem;">
                        <div class="au-activity-bar-track">
                            <div class="au-activity-bar-fill" style="width:{{ $pct }}%; background:{{ $met ? '#16a34a' : '#ef4444' }};"></div>
                        </div>
                        <span style="font-size:0.72rem; color:rgba(0,0,0,0.35); width:32px; text-align:right;">{{ $pct }}%</span>
                    </div>
                    <div style="font-size:0.7rem; color:rgba(0,0,0,0.35);">
                        Quarter ends {{ \Carbon\Carbon::now()->endOfQuarter()->format('M j, Y') }}
                    </div>
                    @if($totalVatsimHours !== null)
                    @php
                        $effectiveTotal = max($totalVatsimHours, $hrs);
                        $outsideHrs = max(0, $effectiveTotal - $hrs);
                        $firRatio = $effectiveTotal > 0 ? min(1, $hrs / $effectiveTotal) : null;
                        $meetsFirReq = $firRatio !== null && $firRatio >= 0.5;
                    @endphp
                    <div style="margin-top:0.6rem; padding-top:0.5rem; border-top:1px solid #f1f5f9; display:flex; gap:0;">
                        <div style="flex:1; text-align:center;">
                            <div style="font-size:0.82rem; font-weight:700; color:#122b44;">{{ decimal_to_hm($hrs) }}</div>
                            <div style="font-size:0.65rem; color:rgba(0,0,0,0.35); margin-top:0.1rem;">In FIR</div>
                        </div>
                        <div style="width:1px; background:#f1f5f9;"></div>
                        <div style="flex:1; text-align:center;">
                            <div style="font-size:0.82rem; font-weight:700; color:#122b44;">{{ decimal_to_hm($outsideHrs) }}</div>
                            <div style="font-size:0.65rem; color:rgba(0,0,0,0.35); margin-top:0.1rem;">Outside FIR</div>
                        </div>
                        <div style="width:1px; background:#f1f5f9;"></div>
                        <div style="flex:1; text-align:center;">
                            <div style="font-size:0.82rem; font-weight:700; color:#122b44;">{{ decimal_to_hm($effectiveTotal) }}</div>
                            <div style="font-size:0.65rem; color:rgba(0,0,0,0.35); margin-top:0.1rem;">Total VATSIM</div>
                        </div>
                        @if($firRatio !== null)
                        <div style="width:1px; background:#f1f5f9;"></div>
                        <div style="flex:1; text-align:center;">
                            <div style="font-size:0.82rem; font-weight:700; color:{{ $meetsFirReq ? '#15803d' : '#b91c1c' }};">
                                {{ round($firRatio * 100) }}%
                            </div>
                            <div style="font-size:0.65rem; color:rgba(0,0,0,0.35); margin-top:0.1rem;">
                                <i class="fas {{ $meetsFirReq ? 'fa-check' : 'fa-times' }} fa-xs"></i> FIR ratio
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endif

                {{-- Modify --}}
                @if($user->id == Auth::user()->id)
                <div class="au-card">
                    <div class="au-card-title">Modify User</div>
                    <p style="font-size:0.85rem; color:rgba(0,0,0,0.5); margin:0 0 0.75rem;">
                        You are editing your own account.
                        <strong style="color:#b91c1c;">Demoting yourself below Staff will lock you out.</strong>
                    </p>
                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#confirmChange">
                        <i class="fas fa-edit fa-xs"></i> Edit My Account
                    </button>
                </div>
                @elseif(Auth::user()->permissions == 5 || ($user->permissions < 4 && Auth::user()->permissions > 3))
                <div class="au-card">
                    <div class="au-card-title">Modify User</div>
                    <form method="POST" action="{{ route('edit.userpermissions', [$user->id]) }}">
                        @csrf
                        <div class="au-form-row">
                            <div class="au-form-group">
                                <label>Permissions Level</label>
                                <select name="permissions" class="form-control form-control-sm">
                                    <option value="0" {{ $user->permissions == 0 ? 'selected' : '' }}>Guest</option>
                                    <option value="1" {{ $user->permissions == 1 ? 'selected' : '' }}>Controller</option>
                                    <option value="2" {{ $user->permissions == 2 ? 'selected' : '' }}>Mentor</option>
                                    <option value="3" {{ $user->permissions == 3 ? 'selected' : '' }}>Instructor</option>
                                    @if(Auth::user()->permissions == 5)
                                    <option value="4" {{ $user->permissions == 4 ? 'selected' : '' }}>Staff Member</option>
                                    <option value="5" {{ $user->permissions == 5 ? 'selected' : '' }}>Administrator</option>
                                    @endif
                                </select>
                            </div>
                            <div class="au-form-group">
                                <label>Certification Status</label>
                                <select name="certification" class="form-control form-control-sm">
                                    <option value="not_certified" {{ $certification == 'not_certified' ? 'selected' : '' }}>Not Certified</option>
                                    <option value="training"      {{ $certification == 'training'      ? 'selected' : '' }}>Training</option>
                                    <option value="home"          {{ $certification == 'home'          ? 'selected' : '' }}>Home Controller</option>
                                    <option value="visit"         {{ $certification == 'visit'         ? 'selected' : '' }}>Visiting Controller</option>
                                    @if(Auth::user()->permissions >= 4)
                                    <option value="instructor"    {{ $certification == 'instructor'    ? 'selected' : '' }}>Instructor</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save fa-xs"></i> Save Changes
                        </button>
                    </form>
                </div>
                @endif

            </div>

            {{-- Right --}}
            <div class="col-md-5">

                {{-- Avatar --}}
                <div class="au-card">
                    <div class="au-card-title">Avatar</div>
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <img src="{{ $user->avatar() }}" style="width:64px; height:64px; border-radius:50%; object-fit:cover; flex-shrink:0;">
                        <div>
                            <div style="font-size:0.75rem; color:rgba(0,0,0,0.35); margin-bottom:0.5rem;">
                                Mode:
                                @switch($user->avatar_mode)
                                    @case(0) Default initials @break
                                    @case(1) Custom image @break
                                    @case(2) Discord avatar @break
                                @endswitch
                            </div>
                            <button data-toggle="modal" data-target="#changeAvatar" class="btn btn-sm btn-outline-secondary mr-1">
                                <i class="fas fa-upload fa-xs"></i> Change
                            </button>
                            @if(!$user->isAvatarDefault())
                            <form action="{{ route('users.resetusersavatar') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Reset</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Discord --}}
                <div class="au-card">
                    <div class="au-card-title">Discord</div>
                    @if($user->hasDiscord())
                    <div class="au-disc-row">
                        <img src="{{ $user->getDiscordAvatar() }}" style="width:28px; height:28px; border-radius:50%;">
                        <span style="font-size:0.875rem; font-weight:600;">{{ $user->getDiscordUser()->username }}</span>
                    </div>
                    <div class="au-disc-row" style="margin-top:0.25rem;">
                        <span style="font-size:0.78rem; color:rgba(0,0,0,0.4);">Guild member</span>
                        @if($user->memberOfCZWGGuild())
                            <span style="color:#16a34a;"><i class="fas fa-check-circle"></i></span>
                        @else
                            <span style="color:#dc2626;"><i class="fas fa-times-circle"></i></span>
                        @endif
                    </div>
                    @if(count($user->discordBans) > 0)
                    <div style="margin-top:0.75rem; border-top:1px solid rgba(0,0,0,0.06); padding-top:0.75rem;">
                        <div style="font-size:0.68rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:rgba(0,0,0,0.3); margin-bottom:0.5rem;">Bans</div>
                        @foreach($user->discordBans as $ban)
                        <div style="font-size:0.8rem; padding:0.35rem 0; border-bottom:1px solid rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center;">
                            <span style="color:rgba(0,0,0,0.6);">{{ $ban->banStartPretty() }} → {{ $ban->banEndPretty() }}</span>
                            <div>
                                <a href="#" class="btn btn-xs btn-outline-secondary" style="font-size:0.65rem; padding:0.15rem 0.4rem;">Reason</a>
                                @if($ban->isCurrent())
                                <a href="#" class="btn btn-xs btn-outline-danger" style="font-size:0.65rem; padding:0.15rem 0.4rem;">Remove</a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @else
                    <span style="font-size:0.85rem; color:rgba(0,0,0,0.35);">No linked Discord account.</span>
                    @endif
                </div>

                {{-- Notes --}}
                <div class="au-card">
                    <div class="au-card-title" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem;">
                        <span>User Notes</span>
                        <button data-toggle="modal" data-target="#addNoteModal" class="btn btn-sm btn-outline-secondary" style="font-size:0.7rem; padding:0.2rem 0.55rem;">
                            <i class="fas fa-plus fa-xs"></i> Add
                        </button>
                    </div>
                    @forelse($userNotes as $note)
                    <div class="au-note-item">
                        <div class="au-note-meta">{{ $note->author_name }} &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($note->timestamp)->format('M j, Y') }}</div>
                        <div class="au-note-body">{{ $note->html() }}</div>
                        <form action="{{ route('users.deletenote', [$user->id, $note->id]) }}" method="GET" style="margin-top:0.4rem;">
                            <button class="btn btn-xs btn-outline-danger" style="font-size:0.65rem; padding:0.15rem 0.4rem;">Delete</button>
                        </form>
                    </div>
                    @empty
                    <span style="font-size:0.85rem; color:rgba(0,0,0,0.3);">No notes on this user.</span>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Add Note Modal --}}
<div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Note</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('users.createnote', $user->id) }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="small font-weight-bold">Content</label>
                    {!! Form::textarea('content', null, ['class' => 'form-control', 'rows' => 4]) !!}
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="confidential" id="confidential">
                    <label class="form-check-label small" for="confidential">Confidential</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Note</button>
            </div>
            </form>
        </div>
    </div>
</div>

{{-- Change Avatar Modal --}}
<div class="modal fade" id="changeAvatar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change {{ $user->fullName('F') }}'s Avatar</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('users.changeusersavatar') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p class="small text-muted">Only use this for staff page photos or at the user's request.</p>
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="file">
                    <label class="custom-file-label">Choose file</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Upload</button>
            </div>
            </form>
        </div>
    </div>
</div>

{{-- Confirm self-edit Modal --}}
<div class="modal fade" id="confirmChange" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Your Own Account</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('edit.userpermissions', [$user->id]) }}">
            @csrf
            <div class="modal-body">
                <div class="alert alert-danger py-2 small">
                    <strong>Warning:</strong> Demoting yourself below Staff Member will lock you out permanently.
                </div>
                <div class="au-form-row" style="margin-top:0.75rem;">
                    <div class="au-form-group">
                        <label>Permissions Level</label>
                        <select name="permissions" class="form-control form-control-sm">
                            <option value="0" {{ $user->permissions == 0 ? 'selected' : '' }}>Guest</option>
                            <option value="1" {{ $user->permissions == 1 ? 'selected' : '' }}>Controller</option>
                            <option value="2" {{ $user->permissions == 2 ? 'selected' : '' }}>Mentor</option>
                            <option value="3" {{ $user->permissions == 3 ? 'selected' : '' }}>Instructor</option>
                            <option value="4" {{ $user->permissions == 4 ? 'selected' : '' }}>Staff Member</option>
                            @if(Auth::user()->permissions == 5)
                            <option value="5" {{ $user->permissions == 5 ? 'selected' : '' }}>Administrator</option>
                            @endif
                        </select>
                    </div>
                    <div class="au-form-group">
                        <label>Certification Status</label>
                        <select name="certification" class="form-control form-control-sm">
                            <option value="not_certified" {{ $certification == 'not_certified' ? 'selected' : '' }}>Not Certified</option>
                            <option value="training"      {{ $certification == 'training'      ? 'selected' : '' }}>Training</option>
                            <option value="home"          {{ $certification == 'home'          ? 'selected' : '' }}>Home Controller</option>
                            <option value="visit"         {{ $certification == 'visit'         ? 'selected' : '' }}>Visiting Controller</option>
                            @if(Auth::user()->permissions >= 4)
                            <option value="instructor"    {{ $certification == 'instructor'    ? 'selected' : '' }}>Instructor</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Save Changes</button>
            </div>
            </form>
        </div>
    </div>
</div>
@stop
