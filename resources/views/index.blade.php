@extends('layouts.master')
@section('description', 'Welcome to Winnipeg - located in the heart of Canada on the VATSIM network.')


@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}" />
    <div class="winnipeg-blue">
        <div style="height: calc(100vh - 59px); position:relative; overflow:hidden;">
            {{-- Parallax background --}}
            <div data-jarallax data-speed="0.2" class="jarallax" style="position:absolute; top:0; left:0; right:0; bottom:0; background-image: url({{$background->url}}); {{$background->css}}"></div>
            {{-- Gradient overlay: dark left+bottom, open top-right --}}
            <div style="position:absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(to right, rgba(10,24,40,0.75) 0%, rgba(10,24,40,0.4) 55%, rgba(10,24,40,0.1) 100%); pointer-events:none;"></div>
            <div style="position:absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(to top, rgba(10,24,40,0.5) 0%, transparent 40%); pointer-events:none;"></div>
            {{-- Text content --}}
            <div style="position:absolute; top:0; left:0; right:0; bottom:0; display:flex; flex-direction:column; justify-content:center; padding-bottom:3rem;">
                <div class="container">
                    <p style="color:rgba(255,255,255,0.6); font-size:0.8rem; margin-bottom:0.4rem; letter-spacing:0.5px; text-shadow:0 1px 4px rgba(0,0,0,0.5);">
                        <i class="fas fa-camera fa-xs"></i>&nbsp; {{$background->credit}}
                    </p>
                    <h1 style="font-size:clamp(2.5rem, 6vw, 5.5rem); color:#fff; font-weight:800; line-height:1.05; margin-bottom:0.5rem; text-shadow:0 1px 6px rgba(0,0,0,0.25);">
                        We Are <span id="hero-word" style="color:#122b44; font-weight:900; display:inline-block; transition:opacity 0.18s ease, transform 0.18s ease;"><span id="hero-word-text" style="opacity:0;">Winnipeg</span></span>.
                    </h1>
                    <style>
                    #hero-word.flip-out { opacity:0; transform:translateY(-6px); }
                    #hero-word.flip-in  { opacity:0; transform:translateY(6px); }
                    #hero-word {
                        position: relative;
                        isolation: isolate;
                    }
                    #hero-word::before {
                        content: '';
                        position: absolute;
                        inset: -15px -25px;
                        background: white;
                        border-radius: 55% 45% 38% 62% / 48% 62% 38% 52%;
                        filter: blur(18px);
                        opacity: 0.3;
                        z-index: -1;
                    }
                    </style>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var words = ['Brandon', 'Portage', 'Steinbach', 'Selkirk', 'Thompson', 'Flin Flon', 'Dauphin', 'Morden', 'Winkler', 'The Pas', 'Saskatoon', 'Regina', 'Moose Jaw', 'Swift Current', 'Prince Albert', 'Yorkton', 'North Battleford', 'Estevan', 'Weyburn', 'Thunder Bay', 'Lloydminster'];
                        var el = document.getElementById('hero-word');
                        var textEl = document.getElementById('hero-word-text');
                        var first = words[Math.floor(Math.random() * words.length)];

                        textEl.textContent = first;
                        textEl.style.opacity = '1';

                        function flipTo(word) {
                            el.classList.add('flip-out');
                            setTimeout(function() {
                                textEl.textContent = word;
                                el.classList.remove('flip-out');
                                el.classList.add('flip-in');
                                el.offsetHeight;
                                el.classList.remove('flip-in');
                            }, 180);
                        }

                        setTimeout(function() { flipTo('Winnipeg'); }, 1200);
                    });
                    </script>
                    <a href="#mid" style="display:inline-flex; align-items:center; gap:0.5rem; color:rgba(255,255,255,0.85); font-size:1rem; text-decoration:none; padding-bottom:2px;">
                        Explore the heart of Canada&nbsp;<i class="fas fa-arrow-down fa-xs"></i>
                    </a>
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
                            <h2 class="font-weight-bold" style="text-align: center; padding-top:1%"><i class="fas fa-award"></i>&nbsp;&nbsp;Top Controllers this Quarter</h2>
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
                            
            <br>
        </div>
    </div>
@stop
