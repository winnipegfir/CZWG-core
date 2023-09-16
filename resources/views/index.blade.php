@extends('layouts.master')
@section('description', 'Welcome to Winnipeg - located in the heart of Canada on the VATSIM network.')


@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}" />
    <div class="winnipeg-blue">
        <div data-jarallax data-speed="0.2" class="jarallax" style="height: calc(100vh - 59px)">
            <div class="mask flex-center flex-column"
                 style="z-index: 1; width: 100%; background-image: url({{$background->url}}); {{$background->css}}">
                <div class="container" style="padding-bottom: 20em">
                    <div class="py-5">
                        <div>
                            <br>
                            <h1 style="font-size: 7em; color: #fff">
                                <span class="winnipeg-blue corner" style="padding: 1%">We Are <text class="font-weight-bold">Winnipeg.</text></span>
                            </h1>
                            <h6 style="font-size: 1.25em; color: #fff;">
                                <span class="winnipeg-blue corner" style="padding: 0.5%"><i class="fas fa-chevron-right" style="font-size: .75em"></i> Screenshot by {{$background->credit}}</span>
                            </h6>
                            <br>
                            <h4 style="font-size: 2em; color: #fff;">
                                <span class="white corner" style="padding: 0.5%"><a href="#mid" id="discoverMore" class="blue-text">Come explore the heart of Canada.&nbsp;<i class="fas fa-arrow-down"></i></a></span>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container" id="mid">
            <div class="row py-3" style="padding-bottom: 0px !important;">
                <div class="col-md-6" style="padding-top: 2%">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header" style="color: #122b44;">
                            <h2 class="font-weight-bold" style="text-align: center; padding-top:1%"><i class="fas fa-newspaper"></i>&nbsp;&nbsp;Recent News</h2>
                        </div>
                        <div class="card-body" style="padding-bottom:2%">
                            @foreach($news as $n)
                                <h5><span class="badge winnipeg-blue">{{$n->posted_on_pretty()}}</span>&nbsp;&nbsp;<a href="{{url('/news').'/'.$n->slug}}" style="color: black;"><text class="align-middle">{{$n->title}}</text></h5></a>
                            @endforeach
                        </div>
                        <div class="card-footer pb-1">
                            <a href="{{url('/news')}}"><h6 style="text-align: center; color: #122b44"><i class="fas fa-eye"></i>&nbsp;View all news</h6></a>
                        </div>
                    </div>
                </div>
                <br>
                <div class="col-md-6" style="padding-top: 2%">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header" style="color: #122b44;">
                            <h2 class="font-weight-bold" style="text-align: center; padding-top:1%"><i class="fas fa-calendar"></i>&nbsp;&nbsp;Upcoming Events</h2>
                        </div>
                        <div class="card-body" style="padding-bottom:2%">
                            @if(count($nextEvents) == 0)
                                <h5 style="text-align: center;">Stay tuned here for upcoming events!</h5>
                            @endif
                            @foreach($nextEvents as $e)
                                <h5><a href="{{url('/events').'/'.$e->slug}}" style="color: black;"><text class="align-middle">{{$e->name}}</text></a>&nbsp;&nbsp;<span class="float-right badge winnipeg-blue">{{$e->start_timestamp_pretty()}}</span></h5>
                            @endforeach
                        </div>
                        <div class="card-footer pb-1">
                            <a href="{{url('/events')}}"><h6 style="text-align: center; color: #122b44"><i class="fas fa-eye"></i>&nbsp;View all events</h6></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row py-3" style="padding-bottom: 0px !important; min-height: 100%">
                <div class="col-md-6" style="padding-top: 2%">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header" style="color: #122b44;">
                            <h2 class="font-weight-bold" style="text-align: center; padding-top:1%"><i class="fas fa-award"></i>&nbsp;&nbsp;Top Controllers this Month</h2>
                        </div>
                        <div class="card-body" style="padding-bottom:2%">
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
                <div class="col-md-6" style="padding-top: 2%">
                    <div class="card card-background" style="min-height: 100%">
                        <div class="card-header" style="color: #122b44;">
                            <h2 class="font-weight-bold" style="text-align: center; padding-top:1%"><i class="fas fa-user"></i>&nbsp;&nbsp;Online Controllers</h2>
                        </div>
                        <div class="card-body" style="padding-bottom:2%">
                            @if(count($finalPositions) == 0)
                                <h5 style="text-align: center;">No controllers online.</h5>
                            @endif
                            @foreach($finalPositions as $p)
                                <h5>
                                    <div style="float: left;">
                                        <a href="https://stats.vatsim.net/search_id.php?id={{$p->cid}}" target="_blank" style="color: black;">
                                            @if($p->name == $p->cid)
                                                <i class="fas fa-user-circle"></i>&nbsp;{{$p->name}}
                                            @else
                                                <i class="fas fa-user-circle"></i>&nbsp;{{$p->name}} {{$p->cid}}
                                            @endif
                                        </a>
                                    </div>
                                    <div style="float: right;">
                                    <span class="badge winnipeg-blue">
                                        {{$p->callsign}} on {{$p->frequency}}
                                    </span>
                                    </div>
                                </h5>
                                <br>
                            @endforeach
                        </div>
                        <div class="card-footer pb-1">
                            <a href="https://map.vatsim.net" target="_blank"><h6 style="text-align: center; color: #122b44"><i class="fas fa-map"></i>&nbsp;Live VATSIM Map</h6></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row py-3">
                <div class="col-md-12" style="padding-top: 2%">
                    <div class="card card-background" style="width: 100%">
                        <div class="card-header" style="color: #122b44;">
                            <h2 class="font-weight-bold" style="text-align: center; padding-top:1%"><i class="fas fa-sun"></i>&nbsp;&nbsp;Weather</h2>
                        </div>
                        <div class="card-body" style="padding-bottom:0%">
                            <div style="float: left;">
                                @foreach($weather as $w)
                                    <h5><text class="align-middle font-weight-bold">{{$w->icao}} - {{$w->station->name}}&nbsp;&nbsp;</text>
                                        <span class="badge {{$w->flight_category}}">{{$w->flight_category}}</span>
                                    @if(Carbon\Carbon::make($w->observed) < Carbon\Carbon::now()->subHours(2))
                                        <span class="badge grey">OUTDATED</span>
                                    @endif
                                    </h5>
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
            <div class="row">
                <div class="col-md-12 mb-3" style="padding-top: 2%">
                    <div class="card">
                        <div class="card-body" style="padding: 0%">
                            <a href="https://ctp.vatsim.net/branding" a target=”_blank”><img src="https://winnipegfir.ca/storage/files/uploads/1694846546.png" style="width:100%"></a>
                        </div>
                    </div>
                </div>
            </div>
                            
            <br>
        </div>
    </div>
@stop
