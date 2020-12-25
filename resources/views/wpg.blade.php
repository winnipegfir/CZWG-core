@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'Change is Coming...')
@section('content')

<div class="container" style="margin-top: 1%;">
    

    @if (Auth::check() && Auth::user()->permissions < 4)
    <h1 class="blue-text font-weight-bold">Change is Coming.</h1>
    <p>But... we can't tell you what those changes are yet. Check back soon!</p>
    <a href="{{route('index')}}" class="blue-text" style="font-size: 1.2em"> <i class="fas fa-arrow-left"></i> Back to Home</a>
    <br></br>
    @else
    <a href="{{route('index')}}" class="blue-text" style="font-size: 1.2em"> <i class="fas fa-arrow-left"></i> Back to Home</a>
        <img src="https://i.imgur.com/THmmkon.png" style="width: 100%">
        <br><br>
        <p>After years of being known on the VATSIM network as CZWG, we're finally changing things up. Starting in 2021 - <text class="font-weight-bold">we're changing our callsign from CZWG to WPG.</text> Here's what that means for you - whether you're a pilot, controller, or just an observer.</p>
        <hr>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pilots-tab" data-toggle="tab" href="#pilots" role="tab" aria-controls="pilots" aria-selected="true">For Pilots</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">For Winnipeg Controllers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="neighbours-tab" data-toggle="tab" href="#neighbours" role="tab" aria-controls="neighbours" aria-selected="false">For Our Neighbours</a>
        </li>
    </ul>
        
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="pilots" role="tabpanel" aria-labelledby="pilots" style="padding-left: 1.3%"><br>
            <div class="row">
                <h5>If you're a pilot who's flown in Winnipeg's airspace before, you should note a few small changes you'll probably see.</h5>
            </div><br>
            <li>Winnipeg Centre (previously shown on pilot clients as "CZWG_CTR") will now be shown as "WPG_CTR". The main frequency for CTR will not change (124.00).</li>
            <br>
            <li>You may encounter multiple Centres on at once (eg. WPG_L_CTR, WPG_H_CTR). These are new splits created to ease controller stress during high-traffic events. Controllers will advise you which CTR to contact, don't worry.</li>
        </div>

        <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home" style="padding-left: 1.3%"><br>
            <div class="row">
                <h5>So you're a Winnipeg Controller! Here's what you need to know.</h5>
            </div><br>
            <li>Centre callsigns are changing - if you're a Centre controller (or are certified or training for CTR), you'll be logging on as WPG_CTR from now on - so long, CZWG_CTR.</li>
            <br>
            <li>New sector files are here, and they are beautiful. Say goodbye to those ugly, outdated ground screens. We've rebuilt everything from the ground up.</li>
            <br>
            <li>If you're a Centre controller (or, as previously mentioned, are certified or training for CTR), you may see a few new additions to your sectors - Class G as well as many small fields are now showing!</li>
            <br>
            <li>Centre has new splits! We've finally added more splits to CTR for higher-traffic events (or if you want to go on a strange CTR, for whatever reason.) They are as follows:</li>
            <br>
            <img src="https://i.imgur.com/q9BrUaE.png"><img src="https://i.imgur.com/Wqjm8v8.png">
            <br></br>
            <p><text class="font-weight-bold">NOTE:</text> There are a handful of sectors that cover others when they are offline, as well as new details on splitting CTR - make sure to take a look at Winnipeg's <a href="{{ route('policies') }}">Standard Operating Procedures</a> for details.</p>
        </div>

        <div class="tab-pane fade" id="neighbours" role="tabpanel" aria-labelledby="neighbours" style="padding-left: 1.3%"><br>
        <div class="row">
                <h5>Hello, Neighbouring FIR/ARTCCs! (Drinks are on us for making you change things.)</h5>
            </div><br>
            <li>Whenever you're looking for Winnipeg Centre on your end - don't look for CZWG_CTR anymore. Winnipeg will now show as WPG_CTR. The main frequency is still 124.00.</li>
            <br>
            <li>Winnipeg has (finally) implemented new CTR splits for our controllers - these can all be found in Winnipeg's <a href="{{ route('policies') }}">Standard Operating Procedures, section 2.3.4.</a></li>
            <br>
            <li>Winnipeg controllers will advise all neighbouring FIR/ARTCCs of where to handoff to, if CTR is split.</li>
        </div>
    </div>
    <br>
    <p>Questions, comments or concerns? Get in touch with the Winnipeg FIR Staff <a href="{{ route('staff') }}"><text class="font-weight-bold">HERE</text></a>.</p>
</div>

@endif


@endsection