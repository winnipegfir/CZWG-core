@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('title', 'Winnipeg FIR Staff')
@section('description', 'View the Winnipeg FIR Staff team')

@section('content')
<div style="background:#fff; min-height: calc(100vh - 60px); padding: 2.5rem 0;">
    <div class="container">

        <div class="mb-2">
            <h1 class="font-weight-bold" style="color:#122b44;">Staff</h1>
            <p style="color:#6c757d; margin-bottom:0;">Meet the team behind Winnipeg FIR.</p>
        </div>
        <hr>

        <div class="row">
            {{-- Sticky sidebar nav --}}
            <div class="col-md-3 d-none d-md-block">
                <div style="position:sticky; top:20px;">
                    <div class="list-group">
                        @foreach($groups as $g)
                            <a href="#{{ $g->slug }}" class="list-group-item list-group-item-action" style="color:#122b44; font-size:0.9rem;">
                                {{ $g->name }}
                            </a>
                        @endforeach
                        <a href="{{ url('instructors') }}" class="list-group-item list-group-item-action" style="color:#122b44; font-size:0.9rem;">
                            Instructors &amp; Mentors
                        </a>
                    </div>
                </div>
            </div>

            {{{{-- Main content --}}
            <div class="col-md-9">
                @foreach($groups as $g)
                    <div id="{{ $g->slug }}" class="mb-5">
                        <h3 class="font-weight-bold mb-1" style="color:#122b44;">{{ $g->name }}</h3>
                        @if($g->description)
                            <p style="color:#6c757d; font-size:0.9rem; margin-bottom:1.25rem;">{{ $g->description }}</p>
                        @endif
            
                        @php
                            // Group members by their position title
                            $byPosition = $g->members->groupBy('position');
                        @endphp
            
                        <div class="row">
                            @foreach($byPosition as $position => $members)
                                @php
                                    $colClass = $members->count() > 1 ? 'col-md-6' : 'col-md-12';
                                @endphp
            
                                @foreach($members as $member)
                                    <div class="{{ $colClass }} mb-3">
                                        <div style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:0.5rem; padding:1.25rem; display:flex; align-items:flex-start; gap:1rem; height:100%;">
                                            {{-- Avatar --}}
                                            @if($member->user_id == 1)
                                                <img src="https://www.drupal.org/files/profile_default.png"
                                                     style="width:64px; height:64px; border-radius:50%; object-fit:cover; flex-shrink:0; border:2px solid #e9ecef;">
                                            @else
                                                <img src="{{ $member->user->avatar() }}"
                                                     style="width:64px; height:64px; border-radius:50%; object-fit:cover; flex-shrink:0; border:2px solid #e9ecef;">
                                            @endif
            
                                            {{-- Info --}}
                                            <div style="flex:1; min-width:0;">
                                                <div style="font-weight:700; color:#122b44; font-size:1rem; line-height:1.2;">
                                                    @if($member->user_id == 1)
                                                        Vacant
                                                    @else
                                                        {{ $member->user->fullName('FL') }}
                                                    @endif
                                                </div>
                                                <div style="color:#6c757d; font-size:0.82rem; margin-bottom:0.4rem;">
                                                    {{ $member->position }}
                                                    @if($member->shortform)
                                                        <span style="margin:0 0.3rem;">·</span>{{ $member->shortform }}
                                                    @endif
                                                </div>
            
                                                @if($member->description)
                                                    <p style="font-size:0.85rem; color:#495057; margin-bottom:0.4rem; line-height:1.5;">{{ $member->description }}</p>
                                                @endif
            
                                                <div style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
                                                    @if($member->email)
                                                        <a href="mailto:{{ $member->email }}" style="font-size:0.82rem; color:#122b44;">
                                                            <i class="fas fa-envelope fa-xs mr-1"></i>{{ $member->email }}
                                                        </a>
                                                    @endif
                                                    @if($member->user_id != 1 && $member->user->bio)
                                                        <a href="#" data-toggle="modal" data-target="#viewStaffBio{{ $member->id }}"
                                                           style="font-size:0.82rem; color:#6c757d;">
                                                            <i class="fas fa-id-card fa-xs mr-1"></i>View bio
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <hr class="mb-4">
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- Bio modals --}}
@foreach($staff as $member)
    @if($member->user_id != 1)
        <div class="modal fade" id="viewStaffBio{{ $member->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom:1px solid #e9ecef;">
                        <h5 class="modal-title font-weight-bold" style="color:#122b44;">
                            {{ $member->user->fname }}'s Biography
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="color:#343a40;">
                        @if($member->user->bio)
                            {{ $member->user->bio }}
                        @else
                            <span style="color:#adb5bd;">This person hasn't written a biography yet.</span>
                        @endif
                    </div>
                    <div class="modal-footer" style="border-top:1px solid #e9ecef;">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@stop
