@extends('layouts.master')
@section('description', 'Welcome to Winnipeg - located in the heart of Canada on the VATSIM network.')


@section('content')
    <style>
        .winnipeg-blue {
            background-color: #013162;
        }
        .card {
            background: #f5f5f5;
        }
        .card-body {
            background-color: #ffffff;
        }
        .corner {
            border-radius: 5px;
        }
        .VFR {
            background-color: green;
        }
        .IFR {
            background-color: red;
        }
        .SVFR {
            background-color: orange;
        }

        .MVFR {
            background-color: lightskyblue;
        }
    </style>

    <div class="winnipeg-blue">
        <div data-jarallax data-speed="0.2" class="jarallax" style="height: calc(100vh - 59px)">
            <div class="mask flex-center flex-column"
                 style="z-index: 1; width: 100%; background-image: url({{$background->url}}); {{$background->css}}">
                <div class="container" style="padding-bottom: 20em">
                    <div class="py-5">
                        <div>
                            <div class="winnipeg-blue corner" style="width: max-content;">
                                <h1 style="font-size: 6em; color: #fff; padding: 10px;">We Are Winnipeg.</h1>
                            </div>
                            <div class="winnipeg-blue corner" style="width: max-content;">
                                <h6 style="font-size: 1.25em; color: #fff; padding: 5px;">Screenshot by {{$background->credit}}</h6>
                            </div>
                            <br>
                            <div class="winnipeg-blue corner" style="width: max-content;">
                                <h4 style="font-size: 2em; color: #fff; padding: 5px;"><a href="#mid" id="discoverMore" class="white-text">Explore Central Canada Below</a>&nbsp;&nbsp;<i class="fas fa-arrow-down"></i></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container" id="mid">
            <div class="row py-3" style="padding-bottom: 0px !important;">
                <div class="col-md-6" style="padding-top: 20px">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header" style="color: #013162;">
                            <h2 class="font-weight-bold" style="text-align: center"><i class="fas fa-newspaper"></i>&nbsp;&nbsp;Recent News</h2>
                        </div>
                        <div class="card-body">
                            @foreach($news as $n)
                                <h5><span class="badge winnipeg-blue">{{$n->posted_on_pretty()}}</span>&nbsp;&nbsp;<a href="{{url('/news').'/'.$n->slug}}" style="color: black;"><text class="align-middle">{{$n->title}}</text></h5></a>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <a href="{{url('/news')}}"><h6 style="text-align: center; color: #013162"><i class="fas fa-eye"></i>&nbsp;View all news</h6></a>
                        </div>
                    </div>
                </div>
                <br>
                <div class="col-md-6" style="padding-top: 20px">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header" style="color: #013162;">
                            <h2 class="font-weight-bold" style="text-align: center"><i class="fas fa-calendar"></i>&nbsp;&nbsp;Upcoming Events</h2>
                        </div>
                        <div class="card-body" style="background-color: #fff">
                            @if(count($nextEvents) == 0)
                                <h5 style="text-align: center;">Stay tuned here for upcoming events!</h5>
                            @endif
                            @foreach($nextEvents as $e)
                                <h5><a href="{{url('/events').'/'.$e->slug}}" style="color: black;"><text class="align-middle">{{$e->name}}</text></a>&nbsp;&nbsp;<span class="float-right badge winnipeg-blue">{{$e->start_timestamp_pretty()}}</span></h5>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <a href="{{url('/events')}}"><h6 style="text-align: center; color: #013162"><i class="fas fa-eye"></i>&nbsp;View all events</h6></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row py-3" style="padding-bottom: 0px !important; min-height: 100%">
                <div class="col-md-6" style="padding-top: 20px">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header" style="color: #013162;">
                            <h2 class="font-weight-bold" style="text-align: center"><i class="fas fa-award"></i>&nbsp;&nbsp;Top Controllers this Month</h2>
                        </div>
                        <div class="card-body">
                            @if(count($topControllersArray) == 0)
                                <h5 style="text-align: center;">No data yet.</h5>
                            @endif
                            @foreach($topControllersArray as $t)
                                @if($t['time'] != 0)
                                    <h2>
                                        <span class="badge badge-light w-100" style="background-color: {{$t['colour']}} !important;">
                                            <div style="float: left;">
                                                {{User::where('id', $t['cid'])->first()->fullName('FLC')}}
                                            </div>
                                            <div style="float: right;">
                                                {{$t['time']}}
                                            </div>
                                        </span>
                                    </h2>
                                @endif
                            @endforeach
                        </div>
                        <div class="card-footer">
                        </div>
                    </div>
                </div>
                <div class="col-md-6" style="padding-top: 20px">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header" style="color: #013162;">
                            <h2 class="font-weight-bold" style="text-align: center"><i class="fas fa-user"></i>&nbsp;&nbsp;Online Controllers</h2>
                        </div>
                        <div class="card-body">
                            @if(count($finalPositions) == 0)
                                <h5 style="text-align: center;">No controllers online.</h5>
                            @endif
                            @foreach($finalPositions as $p)
                                <h5>
                                    <div style="float: left;">
                                        <a href="https://stats.vatsim.net/search_id.php?id={{$p['cid']}}" target="_blank" style="color: black;">
                                            @if($p['realname'] == $p['cid'])
                                                <i class="fas fa-user-circle"></i>&nbsp;{{$p['realname']}}
                                            @else
                                                <i class="fas fa-user-circle"></i>&nbsp;{{$p['realname']}} {{$p['cid']}}
                                            @endif
                                        </a>
                                    </div>
                                    <div style="float: right;">
                                    <span class="badge winnipeg-blue">
                                        {{$p['callsign']}} on {{$p['frequency']}}
                                    </span>
                                    </div>
                                </h5>
                                <br>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <a href="https://map.vatsim.net" target="_blank"><h6 style="text-align: center; color: #013162"><i class="fas fa-map"></i>&nbsp;Live VATSIM Map</h6></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row py-3">
                <div class="col-md-12" style="padding-top: 20px">
                    <div class="card card-background" style="width: 100%">
                        <div class="card-header" style="color: #013162;">
                            <h2 class="font-weight-bold" style="text-align: center"><i class="fas fa-sun"></i>&nbsp;&nbsp;Weather</h2>
                        </div>
                        <div class="card-body">
                            <div style="float: left;">
                                @foreach($weather as $w)
                                    <h5><text class="align-middle font-weight-bold">{{$w->icao}} - {{$w->station->name}}&nbsp;&nbsp;</text><span class="badge {{$w->flight_category}}">{{$w->flight_category}}</span></h5>
                                    {{$w->raw_text}}
                                    <br><br>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer">
                        </div>
                    </div>
                </div>
            </div>
            <br>
        </div>
    </div>
@stop
