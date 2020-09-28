@extends('layouts.master')
@section('description', '')

@section('content')
    <script src="https://unpkg.com/jarallax@1/dist/jarallax.min.js"></script>
    <script src="https://unpkg.com/jarallax@1/dist/jarallax-video.min.js"></script>
    <script src="https://unpkg.com/jarallax@1/dist/jarallax-element.min.js"></script>
    <style>
        .jarallax {
            position: relative;
            z-index: 0;
        }

        .jarallax > .jarallax-img {
            position: absolute;
            object-fit: cover;
            /* support for plugin https://github.com/bfred-it/object-fit-images */
            font-family: 'object-fit: cover;';
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
    </style>
    <div data-jarallax data-speed="0.2" class="jarallax" style="height: calc(100vh - 59px)">
        <div class="mask flex-center flex-column"
             style="position:absolute; top:0; left:0; z-index: 1; height: 100%; width: 100%; background: linear-gradient(40deg,rgba(1,45,98,.7),rgba(00,00,0,.6))!important;">
            <div class="container">
                <div class="py-5">
                    <h1 class="h1 my-4 py-2" style="font-size: 4em; color: #fff;">We Are Winnipeg.</h1>
                    <h4><a href="#blueBannerMid" id="discoverMore" class="white-text" style="transition:fade 0.4s;">Check out our new site.&nbsp;&nbsp;
                    <i class="fas fa-arrow-down"></i></a></h4>
                </div>
                <div class="container">
                    <a href="https://twitter.com/CZWGFIR"
                       class="nav-link ml-0 pl-0 waves-effect white-text waves-light">
                        <i class="fab fa-twitter fa-3x"></i>
                    </a>
                    <a href="https://www.facebook.com/CZWGFIR" class="nav-link waves-effect white-text waves-light">
                        <i class="fab fa-facebook fa-3x"></i>
                    </a>
                    <a class="nav-link waves-effect white-text waves-light" data-toggle="modal"
                       data-target="#discordTopModal">
                        <i class="fab fa-discord fa-3x"></i>
                    </a>
                </div>
                <br>
                <br>
                @if($nextEvent)
                    <div class="container white-text">
                        <p style="font-size: 1.4em;" class="font-weight-bold">
                            <a href="{{route('events.view', $nextEvent->slug)}}" class="white-text">
                                <i class="fa fa-calendar"></i>&nbsp;&nbsp;Next Event:&nbsp;{{$nextEvent->name}}
                            </a>
                        </p>
                        <p style="font-size: 1.2em;">{{$nextEvent->start_timestamp_pretty()}}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="container-fluid py-4 blue" id="blueBannerMid">
        <div class="container">
            <h1 class="font-weight-bold white-text pb-3">
                @if(Auth::check())
                    Recent FIR News
                @else
                    Welcome!
                @endif
            </h1>
            <div class="row">
                <div class="col-md-6">
                     <div class="carousel slide carousel-fade" style="height: 300px;" id="news-carousel" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @php
                                $carousel_iteration = 0;
                            @endphp
                            @foreach($news as $n)
                                <li data-target="#news-carousel" data-slide-to="{{$carousel_iteration}}" @if($carousel_iteration = 0) class="active" @endif></li>
                                @php
                                $carousel_iteration++;
                                @endphp
                            @endforeach
                        </ol>
                        <div class="carousel-inner" role="listbox">
                            @php
                            $carousel_iteration = 0;
                            @endphp
                            @foreach($news as $n)
                                <div class="carousel-item @if($carousel_iteration == 0) active @endif" style="height: 300px;">
                                    <div class="view">
                                        @if ($n->image)
                                            <img class="d-block w-100" src="{{$n->image}}" alt="{{$n->image}}">
                                        @else
                                            <div style="height:300px;" class="homepage-news-img blue waves-effect"></div>
                                        @endif
                                        <div class="mask rgba-black-light"></div>
                                    </div>
                                    <div class="carousel-caption">
                                        <h2 class="h2-responsive"><a class="white-text" href="{{route('news.articlepublic', $n->slug)}}">{{$n->title}}</a></h2>
                                        <h5>{{$n->summary}}</h5>
                                    </div>
                                </div>
                                @php
                                    $carousel_iteration++;
                                @endphp
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#news-carousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#news-carousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="white-text">Online Controllers</h3>
                    <ul class="list-unstyled ml-0 mt-3 p-0">
                        @if(count($finalPositions) < 1)
                            <li class="mb-2">
                                <div class="card shadow-none black-text blue-grey lighten-5 p-3">
                                    <div class="d-flex flex-row justify-content-between align-items-center mb-1">
                                        <h4 class="m-0">No controllers online</h4>
                                    </div>
                                </div>
                            </li>
                        @endif
                        @foreach($finalPositions as $controller)
                            <li class="mb-2">
                                <div class="card shadow-none black-text blue-grey lighten-5 p-3">
                                    <div class="d-flex flex-row justify-content-between align-items-center mb-1">
                                        <h4 class="m-0">{{$controller['callsign']}} on {{$controller['frequency']}}</h4>
                                        <span>
                                            @if($controller['realname'] == $controller['cid'])
                                                <a href="https://stats.vatsim.net/search_id.php?id={{$controller['cid']}}" style="color: #000000"; target="_blank"><i class="far fa-user-circle"></i>&nbsp;{{$controller['cid']}}</a></span>
                                            @else
                                                <a href="https://stats.vatsim.net/search_id.php?id={{$controller['cid']}}" style="color: #000000"; target="_blank"><i class="far fa-user-circle"></i>&nbsp;{{$controller['realname']}} {{$controller['cid']}}</a></span>
                                            @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="d-flex flex-row">
                        <a href="http://simaware.ca" target=“_blank” class="float-right ml-auto mr-0 white-text"
                           style="font-size: 1.2em;">View Live Map&nbsp;&nbsp;<i class="fas fa-map"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="jumbtron">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-7">
                    <h1 class="font-weight-bold blue-text">The Heart of Canada.</h1>
                    <p style="font-size: 1.2em;" class="mt-3">
                        We're here to provide the highest-quality ATC service and the best controllers.
                    </p>
                    <div class="d-flex flex-row">
                        @if(!Auth::check() || !Auth::user()->rosterProfile)
                            <a href="{{route('application.start')}}" class="btn bg-czqo-blue-light" role="button">Apply Now!</a>
                        @endif
                        <a href="{{route('yourfeedback')}}" class="btn btn-primary" role="button">See What Pilots Say About Us! </a>
                    </div>
                    <br>
                    <h1 class="font-weight-bold blue-text">Our Top 5 Controllers This Month</h1>
                    <div class="col-md-7">
                    <table id="topControllersTable" class="table table-hover">
                        <thead>
                        <tr>
                            <th style="text-align:center" scope="col"><b>Name (CID)</b></th>
                            <th style="text-align:center" scope="col"><b>Time</b></th>
                        </tr>
                        @foreach(array_slice($topControllersArray, 0, 5) as $top)
                            <tr>
                                <td align="center">
                                    {{User::where('id', $top['cid'])->firstOrFail()->fullName('FLC')}}
                                </td>
                                <td align="center">
                                    {{$top['time']}}
                                </td>
                            </tr>
                        @endforeach
                        </thead>
                    </table>
                    </div>
                    <br>
                    <h1 class="font-weight-bold blue-text">Quick Links</h1>
                    <div class="d-flex flex-row mt-3">
                        <a data-toggle="modal" data-target="#discordTopModal" href="" class="blue-text mr-1 card"
                           style="text-decoration:none">
                            <div class="blue-grey lighten-5" style="height: 50px; !important; width: 50px !important;">
                                <div class="d-flex flex-row justify-content-center align-items-center h-100">
                                    <i class="fab fa-discord fa-3x" style="vertical-align:middle;"></i>
                                </div>
                            </div>
                        </a>
                        &nbsp;&nbsp;&nbsp;
                        <a href="https://twitter.com/CZWGFIR" class="blue-text mr-1 card" style="text-decoration:none">
                            <div class="blue-grey lighten-5" style="height: 50px; !important; width: 50px !important;">
                                <div class="d-flex flex-row justify-content-center align-items-center h-100">
                                    <i class="fab fa-twitter fa-3x" style="vertical-align:middle;"></i>
                                </div>
                            </div>
                        </a>
                        &nbsp;&nbsp;&nbsp;
                        <a href="https://www.facebook.com/CZWGFIR" class="blue-text mr-1 card" style="text-decoration:none">
                            <div class="blue-grey lighten-5" style="height: 50px; !important; width: 50px !important;">
                                <div class="d-flex flex-row justify-content-center align-items-center h-100">
                                    <i class="fab fa-facebook fa-3x" style="vertical-align:middle;"></i>
                                </div>
                            </div>
                        </a>
                        &nbsp;&nbsp;&nbsp;
                        <a href="https://www.twitch.tv/CZWGFIR" class="blue-text mr-1 card" style="text-decoration:none">
                            <div class="blue-grey lighten-5" style="height: 50px; !important; width: 50px !important;">
                                <div class="d-flex flex-row justify-content-center align-items-center h-100">
                                    <i class="fab fa-twitch fa-3x" style="vertical-align:middle;"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-5">
                    <h1 class="font-weight-bold blue-text">Our Twitter</h1>
                    <a class="twitter-timeline" data-height="625" data-width="1000" data-theme="light"
                       href="https://twitter.com/CZWGFIR?ref_src=twsrc%5Etfw">Tweets by CZWGFIR</a>
                    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                </div>
            </div>
        </div>
    </div>
    <script>
        jarallax(document.querySelectorAll('.jarallax'), {
            speed: 1.5,
            videoSrc: 'mp4:{{url('/images/V')}}',
            videoLoop: true
        });
    </script>
@endsection
