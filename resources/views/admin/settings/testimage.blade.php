@extends('layouts.master')

@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}" />

    <div class="winnipeg-blue">
    <div data-jarallax data-speed="0.2" class="jarallax" style="height: calc(100vh - 59px)">
        <div class="mask flex-center flex-column"
             style="z-index: 1; width: 100%; background-image: url({{$image->url}}); {{$image->css}}">
            <div class="container" style="padding-bottom: 20em">
                <div class="py-5">
                    <div>
                        <br>
                        <h1 style="font-size: 6em; color: #fff">
                            <span class="winnipeg-blue corner" style="padding: 1%">We Are Winnipeg.</span>
                        </h1>
                        <h6 style="font-size: 1.25em; color: #fff;">
                            <span class="winnipeg-blue corner" style="padding: 0.5%">Screenshot by {{$image->credit}}</span>
                        </h6>
                        <br>
                        <h4 style="font-size: 2em; color: #fff;">
                            <span class="white corner" style="padding: 0.5%"><a href="#mid" id="discoverMore" class="blue-text">Check out our new homepage below!&nbsp;&nbsp;<i class="fas fa-arrow-down"></i></a></span>
                        </h4>
                        <div class="winnipeg-blue corner" style="width: max-content;">
                            <h6 style="font-size: 1em; color: #fff; padding: 5px;"><a href="{{url('admin/settings/images')}}" class="white-text">Go Back to Images</a></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
