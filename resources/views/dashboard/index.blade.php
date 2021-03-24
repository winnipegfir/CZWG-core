@extends('layouts.dashboard')
@section('content')
@section('title', 'Your Dashboard - WIP')

<style>
    .table {
        display: table;
        width: 100%;
        height 100%;
        table-layout: fixed;
    }

    .home {
        display: table-row;
        background-image: url('https://cdn.discordapp.com/attachments/598024220961931271/820022270177181797/unknown.png') !important;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .col {
        display: table-cell;
    }

    .right {
        background: rgba(1, 49, 98, 0.8);
    }

    .left {
        background-color: #373737;
        width: 20%;
        position: relative;
    }
    .card-header {
        background-color: #f2d600;
        border-radius: 5px;
        padding-bottom: 0px;
    }
    .clock {
        position: absolute;
        bottom: 0;
    }
    .accordion {
        background-color: white;
        color: #013162;
        cursor: pointer;
        padding: 2%;
        width: 100%;
        border: none;
        text-align: left;
        outline: none !important;
        font-size: 12px;
        transition: 0.4s;
    }

    .accordion:hover {
        background-color: #272727;
        color: #fff;
    }

    .active {
        background-color: #013162;
        color: #fff;
    }

    .accordion:after {
        font-family: "Font Awesome 5 Free";
        content: '\f104';
        float: right;
        font-weight: 900;
    }

    .active:after {
        font-family: "Font Awesome 5 Free";
        content: "\f107";
        font-weight: 900;
    }

    .panel {
        background-color: white;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.1s ease-out;
    }
</style>

<div class="table mb-0">
    <div class="home">
        <div class="col right p-4">
        @if(Auth::user()->permissions >= 1)
        <!--Certification Card-->
        <div class="col" style="width: 35%">
            <div class="card">
                <div class="card-header">
                    <h2 align="center" class="font-weight-bold blue-text">Your Certification</h2>
                </div>
                <div class="card-body pb-0 pt-3">
                <!--Certification Status Badges-->
                <h5 class="font-weight-bold blue-text">Status</h5>
                    <div class="d-flex flex-row">
                        @if ($certification == "certified")
                        <h3>
                            <span class="badge badge-success rounded shadow-none">
                                <i class="fa fa-check"></i>&nbsp;CZWG Certified</span>
                        </h3>
                        @elseif ($certification == "not_certified")
                        <h3>
                            <span class="badge badge-danger rounded shadow-none">
                                <i class="fa fa-times"></i>&nbsp;Not Certified to Control</span>
                        </h3>
                        @elseif ($certification == "training")
                        <h3>
                            <span class="badge badge-warning rounded shadow-none">
                                <i class="fa fa-book-open"></i>&nbsp;In Training</span>
                        </h3>
                        @elseif ($certification == "home")
                        <h3>
                            <span class="badge rounded shadow-none" style="background-color:#013162">
                                <i class="fa fa-user-check"></i>&nbsp;CZWG Controller</span>
                        </h3>
                        @elseif ($certification == "visit")
                        <h3>
                            <span class="badge badge-info rounded shadow-none">
                                <i class="fa fa-plane"></i>&nbsp;CZWG Visiting Controller</span>
                        </h3>
                        @elseif ($certification == "instructor")
                        <h3>
                            <span class="badge badge-info rounded shadow-none">
                                <i class="fa fa-chalkboard-teacher"></i>&nbsp;CZWG Instructor</span>
                        </h3>
                        @else
                        <h3>
                            <span class="badge badge-dark rounded shadow-none">
                                <i class="fa fa-question"></i>&nbsp;Unknown</span>
                        </h3>
                        @endif
                        @if ($active == 0)
                                <h3>
                        <span class="badge ml-2 badge-danger rounded shadow-none">
                            <i class="fa fa-times"></i>&nbsp;Inactive</span>
                                </h3>
                        @elseif ($active == 1)
                                <h3>
                        <span class="badge ml-2 badge-success rounded shadow-none">
                            <i class="fa fa-check"></i>&nbsp;Active</span>
                                </h3>
                        @endif
                    </div>
                    <!--If not Certified-->
                    <span class="text-danger">
                        @if ($certification == "not_certified")
                            <p>You are not a certified controller, please contact an instructor to begin training.</p>
                        @endif
                        @if ($active == 0)
                            <p>Please contact your Instructor to be re-added to Active status.</p>
                        @endif
                    </span>
                    <!--All users, no hours-->
                    @if (Auth::user()->rosterProfile)
                    @if (Auth::user()->rosterProfile->status == "not_certified")
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="{{route('application.list')}}" style="text-decoration:none;"><span
                            class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                            <span
                            class="black-text">View Your Applications</span></a>
                        </li>

                        {{--
                        <li class="mb-2">
                            <a href="{{route('application.list')}}" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="black-text">Training Centre</span></a>
                        </li> --}}
                    </ul>
                    @else
                    <hr>
                        <h5 class="font-weight-bold blue-text">Monthly Hours</h5>
                    @if (Auth::user()->rosterProfile->currency < 0.1)
                        <h3><span class="badge badge-danger rounded shadow-none">No Hours Recorded</h3>
                    @endif
                    @endif
                    <!--Winnipeg Training Hrs-->
                    @if (Auth::user()->rosterProfile->status == "training")
                    @if (!Auth::user()->rosterProfile->currency == 0)
                    @if (Auth::user()->rosterProfile->currency < 2.0)
                        <h3><span class="badge rounded shadow-none blue">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                        </span></h3>
                    @elseif (Auth::user()->rosterProfile->currency >= 2.0)
                        <h3><span class="badge badge-success rounded shadow-none">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                        </span></h3>
                    @endif
                    @endif
                        <p>You require <b>2 controlling hours</b> every month.</p>
                    @endif
                    <!--Winnipeg Home Hrs-->
                    @if (Auth::user()->rosterProfile->status == "home")
                    @if (!Auth::user()->rosterProfile->currency == 0)
                    @if (Auth::user()->rosterProfile->currency < 2.0)
                        <h3><span class="badge rounded shadow-none blue">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                        </span></h3>
                    @elseif (Auth::user()->rosterProfile->currency >= 2.0)
                        <h3><span class="badge badge-success rounded shadow-none">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                        </span></h3>
                    @endif
                    @endif
                        <p>You require <b>2 controlling hours</b> every month.</p>
                    @endif
                    <!--Winnipeg Vstr Cntrlr Hrs-->
                    @if (Auth::user()->rosterProfile->status == "visit")
                    @if (!Auth::user()->rosterProfile->currency == 0)
                    @if (Auth::user()->rosterProfile->currency < 1.0)
                        <h3><span class="badge rounded shadow-none blue">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                        </span></h3>
                    @elseif (Auth::user()->rosterProfile->currency >= 1.0)
                        <h3><span class="badge badge-success rounded shadow-none">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                        </span></h3>
                    @endif
                    @endif
                        <p>You require <b>1 controlling hour</b> every month.</p>
                    @endif
                    <!--Winnipeg Instructor Hrs-->         
                    @if (Auth::user()->rosterProfile->status == "instructor")
                    @if (!Auth::user()->rosterProfile->currency == 0)
                    @if (Auth::user()->rosterProfile->currency < 3.0)
                        <h3><span class="badge rounded shadow-none blue">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                        </span></h3>
                    @elseif (Auth::user()->rosterProfile->currency >= 3.0)
                        <h3><span class="badge rounded shadow-none green">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                        </span></h3>
                    @endif
                    @endif
                        <p>You require <b>3 controlling hours</b> every month.</p>
                    @endif
                    @elseif ($certification == "not_certified")
                        
                    @endif
                    <!--Promotion Hours Notice to be added-->

                @endif
                </div>
            </div>
        </div>
            <div class="col" style="width: 35%">
                <div class="card">
                    <div class="card-header">
                        <h2 align="center" class="font-weight-bold blue-text">1Winnipeg Training</h2>
                    </div>
                    <div class="card-body pb-0 pt-3">
                    @if (count($cbtnotifications) >= 1)
                    <h5 class="font-weight-bold blue-text">Notifications</h5>
                            @foreach ($cbtnotifications as $cn)
                                <li>{{$cn->message}} <a href="{{route('cbt.notification.dismiss', $cn->id)}}"><i style="color: red" class="fas fa-times"></i></a></li>
                            @endforeach
                            <hr>
                            @endif
                            
                            @if($yourinstructor != null && $yourinstructor->instructor != null)
                                <p class="mb-0"><b>Your Instructor:</b> {{$yourinstructor->instructor->user->fullName('FL')}}
                                    <br>
                                <b>Email:</b> <a href="mailto:{{$yourinstructor->instructor_email}}">{{$yourinstructor->instructor->email}}</a>
                                </p>
                            @endif
                            <ul class="list-unstyled mt-2 ">
                                <li class="mb-2">
                                    <a href="{{route('cbt.index')}}" style="text-decoration:none;">
                                        <span class="blue-text"><i class="fas fa-chevron-right"></i></span> 
                                        &nbsp; 
                                        <span class="black-text">Training Centre</span>
                                    </a>
                                    <br>
                                    <a href="{{route('training')}}" style="text-decoration:none;">
                                        <span class="blue-text"><i class="fas fa-chevron-right"></i></span> 
                                        &nbsp; 
                                        <span class="black-text">Current Training Wait Times</span>
                                    </a>
                                </li>
                            </ul>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h2 align="center" class="font-weight-bold blue-text">Your Settings</h2>
                    </div>
                    <div class="card-body pb-0 pt-3">              
                    </div>
                </div>
            </div>
            <br>
            <div class="col" style="width: 33%">
                <div class="card">
                    <div class="card-header">
                        <h2 align="center" class="font-weight-bold blue-text">Panel 4</h2>
                    </div>
                    <div class="card-body pb-0 pt-3">              
                    </div>
                </div>
            </div>
            <div class="col" style="width: 33%">
                <div class="card">
                    <div class="card-header">
                        <h2 align="center" class="font-weight-bold blue-text">Panel 5</h2>
                    </div>
                    <div class="card-body pb-0 pt-3">              
                    </div>
                </div>
            </div>
            <div class="col" style="width: 33%">
                <div class="card">
                    <div class="card-header">
                        <h2 align="center" class="font-weight-bold blue-text">Staff Tools</h2>
                    </div>
                    <div class="card-body pb-0 pt-3">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="{{route('roster.index')}}" style="text-decoration:none;"><span
                                        class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                    <span
                                        class="black-text">Manage Controller Roster</span></a>
                            </li>
                            <li class="mb-2">
                                <a href="{{route('events.admin.index')}}" style="text-decoration:none;"><span
                                        class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                    <span
                                        class="black-text">Manage Events</span></a>
                            </li>
                            <li class="mb-2">
                                <a href="{{route('news.index')}}" style="text-decoration:none;"><span
                                        class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                    <span
                                        class="black-text">Manage News</span></a>
                            </li>
                            <li class="mb-2">
                                <a href="{{route('staff.feedback.index')}}" style="text-decoration:none;"><span
                                        class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                    <span
                                        class="black-text">Manage Feedback</span></a>
                            </li>
                            <li class="mb-2">
                                <a href="{{(route('users.viewall'))}}" style="text-decoration:none;">
                            <span class="blue-text">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                                    &nbsp;
                                    <span class="black-text">Manage Users</span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{(route('dashboard.upload'))}}" style="text-decoration:none;">
                            <span class="blue-text">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                                    &nbsp;
                                    <span class="black-text">File Uploader</span>
                                </a>
                            </li>
                        </ul>              
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection