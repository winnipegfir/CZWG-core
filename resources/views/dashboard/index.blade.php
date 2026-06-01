@extends('layouts.dashboard')
@section('content')
@section('title', 'Dashboard - Winnipeg FIR')

<style>
.dash-accordion-group {
    border: 1px solid #dee2e6;
    border-radius: 0.35rem;
    overflow: hidden;
}
.dash-accordion-btn {
    background-color: white;
    color: #122b44;
    cursor: pointer;
    padding: 0.65rem 0.85rem;
    width: 100%;
    border: none;
    border-top: 1px solid #dee2e6;
    text-align: left;
    outline: none !important;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.15s;
    margin: 0;
    display: block;
}
.dash-accordion-btn:first-child {
    border-top: none;
}
.dash-accordion-btn:hover,
.dash-accordion-btn.is-open {
    background-color: #f0f4f8;
}
.dash-accordion-btn::after {
    font-family: "Font Awesome 5 Free";
    content: '\f107';
    float: right;
    font-weight: 900;
    color: #64748b;
}
.dash-accordion-btn.is-open::after {
    content: '\f106';
}
.dash-panel {
    background-color: #f8fafc;
    border-top: 1px solid #dee2e6;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.15s ease-out;
    margin: 0;
}
.dash-card-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}
.dash-card-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800 !important;
    color: #122b44;
}
.dash-card-header i {
    color: #122b44;
    font-size: 1rem;
    width: 18px;
    text-align: center;
}
.dash-cert-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3em 0.75em;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}
.dash-cert-badge i { font-size: 0.75rem; }
.dash-cert-certified  { background: #dcfce7; color: #15803d; }
.dash-cert-training   { background: #fef3c7; color: #92400e; }
.dash-cert-danger     { background: #fee2e2; color: #b91c1c; }
.dash-cert-home       { background: #dbeafe; color: #1d4ed8; }
.dash-cert-visit      { background: #e0f2fe; color: #0369a1; }
.dash-cert-instructor { background: #f3e8ff; color: #7e22ce; }
.dash-cert-unknown    { background: #f1f5f9; color: #64748b; }
.dash-active-badge    { background: #dcfce7; color: #15803d; padding: 0.25em 0.65em; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
.dash-inactive-badge  { background: #fee2e2; color: #b91c1c; padding: 0.25em 0.65em; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
.dash-nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0;
    color: #122b44;
    font-size: 0.9rem;
    text-decoration: none !important;
    border-bottom: 1px solid #f1f5f9;
}
.dash-nav-link:last-child { border-bottom: none; }
.dash-nav-link:hover { color: #2980b9; }
.dash-nav-link i { color: #122b44; width: 14px; font-size: 0.75rem; }
.dash-ticket-item {
    display: block;
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.35rem;
    margin-bottom: 0.4rem;
    color: #122b44;
    font-size: 0.875rem;
    text-decoration: none !important;
    background: #f8fafc;
}
.dash-ticket-item:hover { background: #f0f4f8; color: #122b44; }
.dash-hours-bar {
    height: 8px;
    border-radius: 999px;
    background: #e2e8f0;
    margin: 0.5rem 0;
    overflow: hidden;
}
.dash-hours-fill {
    height: 100%;
    border-radius: 999px;
    transition: width 0.4s ease;
}
.dash-panel input.form-control,
.dash-panel select.form-control,
.dash-panel textarea.form-control {
    display: block !important;
    width: 100% !important;
    height: auto !important;
    min-height: calc(1.5em + .75rem + 2px) !important;
    padding: .375rem .75rem !important;
    font-size: .875rem !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;
    color: #495057 !important;
    background-color: #fff !important;
    border-top: 1px solid #ced4da !important;
    border-right: 1px solid #ced4da !important;
    border-left: 1px solid #ced4da !important;
    border-bottom: 1px solid #ced4da !important;
    border-radius: .25rem !important;
    box-shadow: none !important;
    -webkit-appearance: auto !important;
    appearance: auto !important;
    margin-bottom: 0 !important;
    background-image: none !important;
}
.dash-panel input.form-control:focus,
.dash-panel select.form-control:focus,
.dash-panel textarea.form-control:focus {
    border-top: 1px solid #80bdff !important;
    border-right: 1px solid #80bdff !important;
    border-left: 1px solid #80bdff !important;
    border-bottom: 1px solid #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
    outline: 0 !important;
}
.dash-panel .btn-outline-secondary {
    color: #6c757d !important;
    border-color: #6c757d !important;
    background: transparent !important;
    box-shadow: none !important;
}
</style>

{{-- Page header + background --}}
<div style="background: linear-gradient(135deg, #0d2035 0%, #122b44 100%); min-height: calc(100vh - 60px); padding-bottom: 3rem;">
    <div style="padding: 1.75rem 0 1.25rem;">
        <div class="container">
            <h1 class="font-weight-bold white-text mb-0">
                Dashboard
            </h1>
            <p class="mb-0" style="color: rgba(255,255,255,0.55); font-size: 0.9rem;">
                Welcome back, {{ Auth::user()->fullName('F') }}
            </p>
        </div>
    </div>

<div class="container py-3">
    <div class="row">

        {{-- LEFT COLUMN --}}
        <div class="col-md-6">

            {{-- ATC Resources --}}
            @if (Auth::user()->permissions >= 1 || $certification == "training")
            <div class="card mb-3">
                <div class="card-body">
                    <div class="dash-card-header">
                        <i class="fas fa-book-open"></i>
                        <h3>ATC Resources</h3>
                        @if(Auth::user()->permissions >= 4)
                            <a href="{{route('atcresources.index')}}" class="ml-auto" style="font-size:0.8rem;">
                                <i class="fa fa-edit"></i> Manage
                            </a>
                        @endif
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($atcResources as $resource)
                            @if($resource->atc_only && Auth::user()->permissions < 1)
                                @continue
                            @endif
                            <div class="list-group-item px-0 py-2 d-flex align-items-center" style="font-size:0.875rem;">
                                <span class="mr-auto">{{$resource->title}}</span>
                                <a href="{{$resource->url}}" target="_blank"
                                   class="ml-3 flex-shrink-0"
                                   title="Open resource"
                                   style="color:#64748b; font-size:1rem;">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Profile --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="dash-card-header">
                        <i class="fas fa-user"></i>
                        <h3>Profile</h3>
                    </div>

                    <div class="row align-items-start">
                        <div class="col">
                            <p class="font-weight-bold mb-1" style="font-size: 1rem;">{{ Auth::user()->fullName('FLC') }}</p>
                            <p class="text-muted mb-1" style="font-size: 0.875rem;">
                                {{Auth::user()->rating->getLongName()}} ({{Auth::user()->rating->getShortName()}})
                            </p>
                            <p class="mb-1" style="font-size:0.875rem;">
                                <a class="font-italic" style="color:#2980b9; font-size:0.8rem;" data-toggle="modal" data-target="#ratingChange">Rating incorrect?</a>
                            </p>
                            <p class="mb-1" style="font-size:0.875rem;">Role: {{Auth::user()->permissions()}}</p>
                            @if(Auth::user()->staffProfile)
                                <p class="mb-1" style="font-size:0.875rem;">Staff: {{Auth::user()->staffProfile->position}}</p>
                            @endif
                            <hr class="my-2">
                            <div>
                                <p class="font-weight-600 mb-1" style="font-size:0.85rem; color:#122b44;"><i class="fab fa-discord" style="color:#7289da;"></i> Discord</p>
                                @if (!Auth::user()->hasDiscord())
                                    <p class="mb-1 text-muted" style="font-size:0.8rem;"><i class="fa fa-times-circle text-danger"></i> No linked Discord account</p>
                                    <a href="#" class="btn btn-sm btn-primary py-0 px-2" style="font-size:0.8rem;" data-toggle="modal" data-target="#discordModal">Link Discord</a>
                                @else
                                    <p class="mb-1" style="font-size:0.8rem;">
                                        <i class="fa fa-check-circle text-success"></i>
                                        <img style="border-radius:50%; height:22px;" src="{{Auth::user()->getDiscordAvatar()}}" alt="">
                                        &nbsp;{{Auth::user()->getDiscordUser()->username}}
                                    </p>
                                    @if(!Auth::user()->memberOfCZWGGuild())
                                        <a href="#" data-toggle="modal" data-target="#joinDiscordServerModal" class="btn btn-sm btn-primary py-0 px-2 mr-1" style="font-size:0.8rem;">Join Server</a>
                                    @endif
                                    <a href="#" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.8rem;" data-toggle="modal" data-target="#discordModal">Unlink</a>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto text-center">
                            <img src="{{Auth::user()->avatar()}}" style="width:90px; height:90px; border-radius:50%; object-fit:cover; margin-bottom:8px; border:2px solid #e2e8f0;">
                            <br>
                            <a role="button" data-toggle="modal" data-target="#changeAvatar" class="btn btn-sm btn-primary py-0 px-2 mb-1" style="font-size:0.8rem;">Change</a>
                            @if (!Auth::user()->isAvatarDefault())
                                <br><a role="button" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.8rem;" href="{{route('users.resetavatar')}}">Reset</a>
                            @endif
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="dash-accordion-group">
                        <button class="dash-accordion-btn">
                            <i class="fas fa-pen fa-xs mr-2" style="color:#94a3b8;"></i> Change Display Name
                        </button>
                        <div class="dash-panel">
                            <div class="p-3">
                                <form method="POST" action="{{route('users.changedisplayname')}}">
                                    @csrf
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">First Name</label>
                                        <input type="text" class="form-control form-control-sm" value="{{Auth::user()->display_fname}}" name="display_fname" id="input_display_fname">
                                        <script>function resetToCertFirstName() { $("#input_display_fname").val("{{Auth::user()->fname}}") }</script>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">Display Format</label>
                                        <select name="format" class="form-control form-control-sm">
                                            <option value="showall">First name, last name, and CID</option>
                                            <option value="showfirstcid">First name and CID</option>
                                            <option value="showcid">CID only</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success mr-1">Save</button>
                                    <a class="btn btn-sm btn-outline-secondary" role="button" onclick="resetToCertFirstName()">Reset to CERT name</a>
                                </form>
                            </div>
                        </div>

                        <button class="dash-accordion-btn">
                            <i class="fas fa-align-left fa-xs mr-2" style="color:#94a3b8;"></i> Biography
                        </button>
                        <div class="dash-panel">
                            <div class="p-3">
                                <form method="post" action="{{route('me.editbio')}}">
                                    @csrf
                                    <textarea name="bio" class="form-control form-control-sm mb-2" rows="3">{{Auth::user()->bio}}</textarea>
                                    <p class="small text-muted mb-2">Please ensure this complies with the VATSIM Code of Conduct.</p>
                                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                                </form>
                            </div>
                        </div>

                        <button class="dash-accordion-btn">
                            <i class="fas fa-envelope fa-xs mr-2" style="color:#94a3b8;"></i> Email Preferences
                        </button>
                        <div class="dash-panel">
                            <div class="p-3">
                                @if (Auth::user()->gdpr_subscribed_emails == 0)
                                    <span class="dash-cert-badge dash-cert-danger mb-2 d-inline-block">Not subscribed</span>
                                @else
                                    <span class="dash-cert-badge dash-cert-certified mb-2 d-inline-block">Subscribed</span>
                                @endif
                                <p class="small text-muted mb-2">Promotional emails include event notifications, controller certifications, and FIR news. <a href="{{url('/privacy')}}">Privacy policy.</a></p>
                                @if (Auth::user()->gdpr_subscribed_emails == 0)
                                    <a class="btn btn-sm btn-success" href="{{url('/dashboard/emailpref/subscribe')}}">Subscribe</a>
                                @else
                                    <a class="btn btn-sm btn-outline-danger" href="{{url('/dashboard/emailpref/unsubscribe')}}">Unsubscribe</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upcoming Events --}}
            @if (Auth::user()->permissions >= 1)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="dash-card-header">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>Upcoming Events</h3>
                    </div>
                    @if (count($confirmedevent) < 1)
                        <p class="text-muted mb-0" style="font-size:0.875rem;">No scheduled events.</p>
                    @else
                        @foreach ($confirmedevent as $cevent)
                            <p class="font-weight-bold mb-1" style="font-size:0.9rem;">{{$cevent->name}} <span class="text-muted font-weight-normal" style="font-size:0.8rem;">â€” {{$cevent->start_timestamp_pretty()}}</span></p>
                            @foreach ($confirmedapp as $capp)
                                @if ($cevent->id == $capp->event->id)
                                    <p class="mb-1 ml-2" style="font-size:0.8rem;">
                                        <i class="fas fa-map-marker-alt fa-xs text-muted"></i>
                                        {{$capp->airport}}
                                        @if($capp->position != "Relief") {{$capp->position}} @endif
                                        from
                                        @if($capp->position == "Relief")<span class="text-danger">{{$capp->position}}</span> from @endif
                                        {{$capp->start_timestamp}}z â€“ {{$capp->end_timestamp}}z
                                    </p>
                                @endif
                            @endforeach
                        @endforeach
                    @endif

                    @if (count($unconfirmedapp) >= 1)
                        <a href="" data-target="#unconfirmedEvents" data-toggle="modal" class="dash-nav-link mt-2">
                            <i class="fas fa-chevron-right"></i>
                            You have <strong class="mx-1 text-success">{{count($unconfirmedapp)}}</strong> pending event application{{ count($unconfirmedapp) != 1 ? 's' : '' }}
                        </a>
                    @else
                        <p class="mb-0 mt-2 text-muted" style="font-size:0.8rem;">No active event applications.</p>
                    @endif

                    @if(count($confirmedevent) != 0)
                        <a href="{{url('/dashboard/events/view')}}" class="dash-nav-link mt-1">
                            <i class="fas fa-chevron-right"></i> View Event Rosters
                        </a>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT COLUMN --}}
        @if(Auth::user()->permissions >= 1)
        <div class="col-md-6">

            {{-- Certification & Training Status --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="dash-card-header">
                        <i class="fas fa-id-badge"></i>
                        <h3>Certification &amp; Training</h3>
                    </div>

                    <div class="d-flex flex-wrap gap-1 mb-2" style="gap:0.4rem;">
                        @if ($certification == "certified")
                            <span class="dash-cert-badge dash-cert-certified"><i class="fa fa-check"></i> CZWG Certified</span>
                        @elseif ($certification == "not_certified")
                            <span class="dash-cert-badge dash-cert-danger"><i class="fa fa-times"></i> Not Certified</span>
                        @elseif ($certification == "training")
                            <span class="dash-cert-badge dash-cert-training"><i class="fa fa-book-open"></i> In Training</span>
                        @elseif ($certification == "home")
                            <span class="dash-cert-badge dash-cert-home"><i class="fa fa-user-check"></i> CZWG Controller</span>
                        @elseif ($certification == "visit")
                            <span class="dash-cert-badge dash-cert-visit"><i class="fa fa-plane"></i> Visiting Controller</span>
                        @elseif ($certification == "instructor")
                            <span class="dash-cert-badge dash-cert-instructor"><i class="fa fa-chalkboard-teacher"></i> Instructor</span>
                        @else
                            <span class="dash-cert-badge dash-cert-unknown"><i class="fa fa-question"></i> Unknown</span>
                        @endif

                        @if ($active == 0)
                            <span class="dash-inactive-badge"><i class="fa fa-times"></i> Inactive</span>
                        @elseif ($active == 1)
                            <span class="dash-active-badge"><i class="fa fa-check"></i> Active</span>
                        @endif
                    </div>

                    @if ($certification == "not_certified")
                        <p class="text-danger mb-1" style="font-size:0.85rem;">You are not certified to control. Contact an instructor to begin training.</p>
                    @endif
                    @if ($active == 0)
                        <p class="text-danger mb-1" style="font-size:0.85rem;">You are listed as inactive. Contact staff to be added to the active roster.</p>
                    @endif

                    @if (Auth::user()->rosterProfile && Auth::user()->rosterProfile->status != "not_certified")
                        @php
                            $hours = Auth::user()->rosterProfile->currency ?? 0;
                            $reqHours = match(Auth::user()->rosterProfile->status) {
                                'training'   => 2,
                                'home'       => 2,
                                'visit'      => 1,
                                'instructor' => 3,
                                default      => 3,
                            };
                            $pct = min(100, ($hours / $reqHours) * 100);
                            if ($hours >= $reqHours) {
                                $barColor = '#22c55e'; $bgColor = '#dcfce7'; $textColor = '#15803d'; $label = 'Requirement met';
                            } elseif ($hours < 0.1) {
                                $barColor = '#ef4444'; $bgColor = '#fee2e2'; $textColor = '#b91c1c'; $label = 'No hours recorded';
                            } else {
                                $barColor = '#f59e0b'; $bgColor = '#fef3c7'; $textColor = '#92400e'; $label = decimal_to_hm($hours).' recorded';
                            }
                        @endphp
                        <div style="background:{{$bgColor}}; border-radius:0.5rem; padding:0.75rem 1rem; margin-top:0.75rem;">
                            <div class="d-flex align-items-baseline justify-content-between mb-2">
                                <span style="font-size:0.78rem; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:{{$textColor}};">Activity This Quarter</span>
                            </div>
                            <div class="d-flex align-items-baseline" style="gap:0.35rem; margin-bottom:0.5rem;">
                                <span style="font-size:1.5rem; font-weight:700; color:{{$textColor}}; line-height:1;">
                                    {{ $hours < 0.1 ? '0:00' : decimal_to_hm($hours) }}
                                </span>
                                <span style="font-size:0.8rem; color:{{$textColor}}; opacity:0.7;">/ {{ decimal_to_hm($reqHours) }}</span>
                            </div>
                            <div style="height:6px; border-radius:999px; background:rgba(0,0,0,0.1); overflow:hidden;">
                                <div style="height:100%; width:{{$pct}}%; background:{{$barColor}}; border-radius:999px; transition:width 0.4s ease;"></div>
                            </div>
                            @if($hours >= $reqHours)
                                <p class="mb-0 mt-1" style="font-size:0.75rem; color:{{$textColor}};">
                                    <i class="fas fa-check-circle"></i> Currency requirement met
                                </p>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

            {{-- Training --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="dash-card-header">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Training</h3>
                    </div>
                    <a href="https://training.winnipegfir.ca" target="_blank" class="dash-nav-link">
                        <i class="fas fa-arrow-right"></i>
                        <strong>Winnipeg365</strong> &mdash; Training Portal
                    </a>

                    @if($yourinstructor != null && $yourinstructor->status == 0)
                        <hr class="my-2">
                        <div style="background:#fef3c7; border-radius:0.5rem; padding:0.75rem 1rem;">
                            <p class="mb-1" style="font-size:0.78rem; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:#92400e;">Waitlist Status</p>
                            @if($waitlistPosition)
                                @php
                                    $waitlistTypeLabel = match($yourinstructor->entry_type) {
                                        'New Student' => 'Home',
                                        'New Visitor' => 'Visitor',
                                        'New Transfer' => 'Transfer',
                                        default => $yourinstructor->entry_type,
                                    };
                                @endphp
                                <p class="mb-0" style="font-size:1.4rem; font-weight:700; color:#92400e; line-height:1.2;">
                                    #{{ $waitlistPosition }}
                                    <span style="font-size:0.8rem; font-weight:400; color:#b45309;"> among {{ $waitlistTypeLabel }} students
                                        @if($waitlistTypeTotal) ({{ $waitlistTypeTotal }} total)@endif
                                    </span>
                                </p>
                                @if($yourinstructor->waitlist_added_at)
                                    <p class="mb-0 mt-1" style="font-size:0.78rem; color:#b45309;">
                                        Waiting since {{ $yourinstructor->waitlist_added_at->format('M j, Y') }}
                                        &mdash; {{ $yourinstructor->waitlist_added_at->diffForHumans() }}
                                    </p>
                                @endif
                            @else
                                <p class="mb-0" style="font-size:0.875rem; color:#92400e;">You are on the waitlist. Contact staff if you have questions.</p>
                            @endif
                        </div>
                    @elseif($yourinstructor != null && $yourinstructor->instructor != null)
                        <hr class="my-2">
                        <p class="mb-0" style="font-size:0.875rem;">
                            <strong>Your Instructor:</strong> {{$yourinstructor->instructor->user->fullName('FL')}}<br>
                            <strong>Email:</strong> <a href="mailto:{{$yourinstructor->instructor->email}}">{{$yourinstructor->instructor->email}}</a>
                        </p>
                    @elseif ($certification == "training")
                        <p class="text-muted mb-0 mt-2" style="font-size:0.875rem;">No instructor assigned yet. Contact staff if you have questions.</p>
                    @endif
                </div>
            </div>

            {{-- ATC Bookings — hidden until API key is available --}}
            @if(false && Auth::user()->permissions >= 1)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="dash-card-header">
                        <i class="fas fa-calendar-check"></i>
                        <h3>ATC Bookings</h3>
                    </div>
                    @if($myBookings->isEmpty())
                        <p class="text-muted mb-0" style="font-size:0.875rem;">No upcoming bookings.</p>
                    @else
                        @foreach($myBookings->take(3) as $b)
                        @php
                            $start = \Carbon\Carbon::parse($b['start']);
                            $end   = \Carbon\Carbon::parse($b['end']);
                        @endphp
                        <div class="d-flex align-items-center py-2" style="border-bottom:1px solid #f1f5f9;">
                            <div style="flex:1;">
                                <div style="font-weight:600; font-size:0.875rem; color:#122b44;">{{ $b['callsign'] }}</div>
                                <div style="font-size:0.75rem; color:#64748b;">
                                    {{ $start->format('M j') }} &middot; {{ $start->format('H:i') }}z &ndash; {{ $end->format('H:i') }}z
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @if($myBookings->count() > 3)
                            <p class="mb-0 mt-2" style="font-size:0.78rem; color:#64748b;">+{{ $myBookings->count() - 3 }} more</p>
                        @endif
                    @endif
                    <a href="{{ route('bookings.index') }}" class="dash-nav-link mt-2">
                        <i class="fas fa-chevron-right"></i> View
                    </a>
                </div>
            </div>
            @endif

            {{-- Support --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="dash-card-header">
                        <i class="fas fa-life-ring"></i>
                        <h3>Support</h3>
                    </div>

                    @if (count($openTickets) >= 1)
                        <p class="font-weight-bold mb-2" style="font-size:0.85rem; color:#122b44;">
                            {{count($openTickets)}} open ticket{{ count($openTickets) != 1 ? 's' : '' }}
                        </p>
                        @foreach ($openTickets as $ticket)
                            <a href="{{url('/dashboard/tickets/'.$ticket->ticket_id)}}" class="dash-ticket-item">
                                {{$ticket->title}}
                                <br><small class="text-muted">Updated {{$ticket->updated_at_pretty()}}</small>
                            </a>
                        @endforeach
                        <hr class="my-2">
                    @endif

                    @if(Auth::user()->permissions >= 4 && count($staffTickets) >= 1)
                        <p class="font-weight-bold mb-2" style="font-size:0.85rem; color:#122b44;">
                            {{count($staffTickets)}} open staff ticket{{ count($staffTickets) != 1 ? 's' : '' }}
                        </p>
                        @foreach ($staffTickets as $ticket)
                            <a href="{{url('/dashboard/tickets/'.$ticket->ticket_id)}}" class="dash-ticket-item">
                                {{$ticket->title}}
                                <br><small class="text-muted">Updated {{$ticket->updated_at_pretty()}}</small>
                            </a>
                        @endforeach
                        <hr class="my-2">
                    @endif

                    <a href="{{route('feedback.create')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Send feedback</a>
                    <a href="{{route('tickets.index', ['create' => 'yes'])}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Start a support ticket</a>
                    <a href="{{route('tickets.index')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> View previous tickets</a>
                    <a href="{{route('me.data')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Manage your data</a>
                    @if(Auth::user()->permissions >= 4)
                        <a href="{{route('tickets.staff')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Staff ticket inbox</a>
                    @endif
                </div>
            </div>

            {{-- Instructor --}}
            @if(Auth::user()->instructorProfile !== null && Auth::user()->permissions < 4)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="dash-card-header">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <h3>Instructor</h3>
                    </div>
                    <a href="{{route('training.index')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Training Portal</a>
                </div>
            </div>
            @endif

            {{-- Staff --}}
            @if (Auth::user()->permissions >= 4)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="dash-card-header">
                        <i class="fas fa-tools"></i>
                        <h3>Staff</h3>
                    </div>
                    <a href="https://training.winnipegfir.ca" target="_blank" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Winnipeg365</a>
                    <a href="{{route('training.index')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Training Management</a>
                    <a href="{{route('roster.index')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Manage Controller Roster</a>
                    <a href="{{route('events.admin.index')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Manage Events</a>
                    <a href="{{route('news.index')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Manage News</a>
                    <a href="{{route('staff.feedback.index')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Manage Feedback</a>
                    <a href="{{route('users.viewall')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Manage Users</a>
                    <a href="{{route('dashboard.upload')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> File Uploader</a>
                </div>
            </div>
            @endif

            {{-- Site Admin --}}
            @if (Auth::user()->permissions >= 5)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="dash-card-header">
                        <i class="fas fa-cog"></i>
                        <h3>Site Admin</h3>
                    </div>
                    <a href="{{route('settings.index')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> Settings</a>
                    <a href="{{route('network.index')}}" class="dash-nav-link"><i class="fas fa-chevron-right"></i> View network data</a>
                </div>
            </div>
            @endif

        </div>
        @endif

    </div>

</div>
</div>{{-- end blue background --}}

<script>
document.querySelectorAll('.dash-accordion-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var group = this.closest('.dash-accordion-group');
        var panel = this.nextElementSibling;
        var isOpen = this.classList.contains('is-open');

        // Close all in this group
        group.querySelectorAll('.dash-accordion-btn').forEach(function(b) {
            b.classList.remove('is-open');
            b.nextElementSibling.style.maxHeight = null;
        });

        // Open clicked one if it was closed
        if (!isOpen) {
            this.classList.add('is-open');
            panel.style.maxHeight = panel.scrollHeight + 'px';
        }
    });
});
</script>

{{-- Modals (unchanged) --}}

<div class="modal fade" id="changeAvatar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change avatar</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="post" action="{{route('users.changeavatar')}}" enctype="multipart/form-data">
                <div class="modal-body">
                    <p>Please ensure your avatar complies with the VATSIM Code of Conduct.</p>
                    @csrf
                    <div class="input-group pb-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file">
                            <label class="custom-file-label">Choose file</label>
                        </div>
                    </div>
                    @if(Auth::user()->hasDiscord())
                        or use your Discord avatar (refreshes every 6 hours)<br/>
                        <p class="mt-1">
                            <img style="border-radius:50%; height:60px;" src="{{Auth::user()->getDiscordAvatar()}}" alt="">
                            <a href="{{route('users.changeavatar.discord')}}" class="btn btn-outline-success mt-3">Use Discord Avatar</a>
                        </p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
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
                <h5>Our website updates your VATSIM rating 2 ways:</h5>
                On login, and daily at 00:00 Eastern via the VATSIM API.
                <hr>
                <h5>How can I fix my rating?</h5>
                <p><a href="/logout">Logout</a> and log back in, or wait until 00:00 Eastern.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary" data-dismiss="modal">Dismiss</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="discordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        @if (!Auth::user()->hasDiscord())
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Link your Discord account</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <img style="height:50px;" src="{{asset('/img/discord/CZWGplusdiscord.png')}}" class="img-fluid mb-2" alt="">
                    <p>Linking your Discord account allows you to join our community, receive notifications, and use your Discord avatar.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <a href="{{route('me.discord.link')}}" class="btn btn-primary">Link Account</a>
                </div>
            </div>
        @else
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Unlink your Discord account</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Unlinking will remove you from the CZWG Discord, remove your Discord avatar, and stop Discord notifications.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <a href="{{route('me.discord.unlink')}}" class="btn btn-danger">Unlink Account</a>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="joinDiscordServerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Join the Winnipeg FIR Discord server</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Joining allows you to connect with the CZWG controller and pilot community.</p>
                <h5>Rules</h5>
                <ul>
                    <li>The VATSIM Code of Conduct applies.</li>
                    <li>Always show respect and common decency.</li>
                    <li>No unsolicited server invites or DMs.</li>
                    <li>No spam.</li>
                </ul>
                <p>Clicking 'Join' will redirect you to Discord.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                <a href="{{route('me.discord.join')}}" class="btn btn-primary">Join</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unconfirmedEvents" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Events You've Applied For</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                @foreach ($confirmedevent as $cevent)
                    <p class="font-weight-bold mb-1">{{$cevent->name}} <span class="font-weight-normal text-muted">â€” {{$cevent->start_timestamp_pretty()}}</span></p>
                    @foreach ($unconfirmedapp as $uapp)
                        @if ($cevent->name == $uapp->event->name)
                            <p class="mb-1 ml-2" style="font-size:0.875rem;">
                                <strong>Position Requested:</strong> {{$uapp->position}}
                                from {{$uapp->start_availability_timestamp}}z â€“ {{$uapp->end_availability_timestamp}}z
                            </p>
                        @endif
                    @endforeach
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
            </div>
        </div>
    </div>
</div>

@stop
