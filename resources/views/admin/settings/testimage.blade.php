@extends('layouts.master')

@section('content')
    <style>
        .winnipeg-blue {
            background-color: #013162;
        }

        .corner {
            border-radius: 5px;
        }
    </style>

    <div class="winnipeg-blue">
    <div data-jarallax data-speed="0.2" class="jarallax" style="height: calc(100vh - 59px)">
        <div class="mask flex-center flex-column"
             style="z-index: 1; width: 100%; background-image: url({{$image->url}}); {{$image->css}}">
            <div class="container" style="padding-bottom: 20em">
                <div class="py-5">
                    <div>
                        <div class="winnipeg-blue corner" style="width: max-content;">
                            <h1 style="font-size: 6em; color: #fff; padding: 10px;">We Are Winnipeg.</h1>
                        </div>
                        <div class="winnipeg-blue corner" style="width: max-content;">
                            <h6 style="font-size: 1.25em; color: #fff; padding: 5px;">Screenshot by {{$image->credit}}</h6>
                        </div>
                        <br>
                        <div class="winnipeg-blue corner" style="width: max-content;">
                            <h4 style="font-size: 2em; color: #fff; padding: 5px;"><a href="#mid" id="discoverMore" class="white-text">Explore Central Canada Below</a>&nbsp;&nbsp;<i class="fas fa-arrow-down"></i></h4>
                        </div>
                        <div class="winnipeg-blue corner" style="width: max-content;">
                            <h6 style="font-size: 1em; color: #fff; padding: 5px;"><a href="{{url('admin/settings/images')}}" class="white-text">Go Back to Images</a></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
