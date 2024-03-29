@extends('layouts.master')
@section('title', 'Airports - Winnipeg FIR')
@section('description', 'Winnipeg FIR\'s weather and airports')
@section('content')

    <style>
        .CYWG{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .ATIS{
            margin:auto;
        }
    </style>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<div class="container py-4">
    <h1 class="font-weight-bold blue-text">Airports</h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="cywg-tab" data-toggle="tab" href="#cywg" role="tab" aria-controls="cywg" aria-selected="true">Winnipeg (CYWG/CYAV)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cypg-tab" data-toggle="tab" href="#cypg" role="tab" aria-controls="cypg" aria-selected="false">Southport (CYPG)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyxe-tab" data-toggle="tab" href="#cyxe" role="tab" aria-controls="cyxe" aria-selected="false">Saskatoon (CYXE)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyqt-tab" data-toggle="tab" href="#cyqt" role="tab" aria-controls="cyqt" aria-selected="false">Thunder Bay (CYQT)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cyqr-tab" data-toggle="tab" href="#cyqr" role="tab" aria-controls="cyqr" aria-selected="false">Regina (CYQR)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cymj-tab" data-toggle="tab" href="#cymj" role="tab" aria-controls="cymj" aria-selected="false">Moose Jaw (CYMJ)</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="cywg" role="tabpanel" aria-labelledby="cywg"><br>
        <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYWG') == true)
                    <div class="col">
                        <div class="card"
                             style="background-color:#122b44; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYWG" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYWG')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#272727; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYWG')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#272727; color:#ffffff;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYWG')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
                <li>Tower/Terminal at Winnipeg International (CYWG) is open 24/7.</li>
                <li>Tower St. Andrews (CYAV) is open daily from 1300Z - 0400Z.</li>
            <hr>
                <h1 class="font-weight-bold blue-text">Scenery</h1>
                    <h4>
                        <span class="badge rounded p-1 shadow-none green">NEW</span>
                        <span class="badge rounded p-1 shadow-none orange">Payware</span><text class="font-weight-bold">
                        <text class="font-weight-bold"> FSimStudios - For MSFS</text>
                    </h4>
                    <p class="mb-0">If you asked what sim developer creates the most consistently high-quality product, most people would say FSimStudios! The team based in Vancouver announced their release of Winnipeg, just in time for CTP East 2023..</p>
                    <a style="margin-left: -0.1%" target=”_blank” href="https://store.fsimstudios.com/products/fsimstudios-winnipeg-international-airport-cywg-for-msfs "class="btn btn-sm btn-primary">Now Available</a>
                <hr>
                    <h4>
                        <span class="badge rounded p-1 shadow-none orange">Payware</span><text class="font-weight-bold"> SimAddons - For FSX, P3Dv4, v5 & MSFS</text>
                    </h4>
                    <p class="mb-0">SimAddons truly is a legend in creating scenery for Canadian airports, and the Winnipeg FIR has quite a lot of scenery from the team across the pond. Their scenery for Winnipeg isn't brand new, but is still the best available for P3D and MSFS as of late 2020.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="http://www.simaddons.com/pages/simaddons_purchase1.htm"class="btn btn-sm btn-primary">View More</a>
                <hr>
                    <h4>
                        <span class="badge rounded p-1 shadow-none purple">Freeware</span><text class="font-weight-bold"> Orbx - for FSX & P3D</text>
                    </h4>
                    <p class="mb-0">ORBX is some of the best in the business at creating scenery, and Winnipeg is one of those airports available in their freeware pack. Get it now for your sim and upgrade Winnipeg for no cost.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack"class="btn btn-sm btn-primary">View More</a>
                <hr>
                    <h4>
                        <span class="badge rounded p-1 shadow-none purple">Freeware</span><text class="font-weight-bold"> ProjectSierra - for P3D v4/v5</text>
                    </h4>
                    <p class="mb-0">P3D user who's looking for some good freeware? ProjectSierra has updated a scenery for the sim, and is available at no cost - pick it up and enjoy!.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://drive.google.com/drive/u/0/folders/1BITJA-audI2-7Zmk5Vq-_e5YSOGM8049"class="btn btn-sm btn-primary">View More</a>
        </div>

        <div class="tab-pane fade" id="cypg" role="tabpanel" aria-labelledby="cypg"><br>
        <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYPG') == true)
                <div class="col">
                        <div class="card"
                             style="background-color:#122b44; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYPG" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYPG')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#272727; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYPG')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#272727; color:#ffffff;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYPG')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower open Mon - Fri from 1400Z - 2300Z, excluding holidays.</li>
            <hr>
            <h1 class="font-weight-bold blue-text">Scenery</h1>
                    <h4>
                        <span class="badge rounded p-1 shadow-none purple">Freeware</span><text class="font-weight-bold"> Orbx - for FSX & P3D</text>
                    </h4>
                    <p class="mb-0">ORBX is some of the best in the business at creating scenery, and CYPG is one of many airports included in their free Global Airport Pack. Snag it now and get an enhanced experience at Southport.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack"class="btn btn-sm btn-primary">View More</a>
        </div>

        <div class="tab-pane fade" id="cyxe" role="tabpanel" aria-labelledby="cyxe"><br>
        <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYXE') == true)
                <div class="col">
                        <div class="card"
                             style="background-color:#122b44; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYXE" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYXE')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#272727; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYXE')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#272727; color:#ffffff;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYXE')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower open Mon - Fri between March 9th - October 31st, from 1200Z - 0445Z.</li>
            <li>Tower open Sat - Sun between March 9th - October 31st, from 1245Z - 0445Z.</li>
            <li>Tower open between November 1st - March 8th, from 1245Z - 0445Z.</li>
            <hr>
                <h1 class="font-weight-bold blue-text">Scenery</h1>
                <h4>
                    <span class="badge rounded p-1 shadow-none orange">Payware</span><text class="font-weight-bold"> SimAddons - For FSX, P3Dv4, v5 & MSFS</text>
                </h4>
                    <p class="mb-0">SimAddons, as usual, is all over Canada scenery for FSX, P3D and MSFS. No different in Saskatoon - their scenery for CYXE is great and models the airport perfectly for any pilot.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="http://www.simaddons.com/pages/simaddons_purchase1.htm"class="btn btn-sm btn-primary">View More</a>
                <hr>
                <h4>
                    <span class="badge rounded p-1 shadow-none orange">Payware</span><text class="font-weight-bold"> FSXCenery - For FSX & P3Dv5</text>
                </h4>
                    <p class="mb-0">With their unique terminal shape and classic General Aviation ramp, Saskatoon is welcoming to both major airlines and small private pilots. FSXCenery brings the airport to life with their scenery.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://secure.simmarket.com/fsxcenery-cyxe-saskatoon-john-g.-diefenbaker-international-airport-fsx-p3dv5.phtml"class="btn btn-sm btn-primary">View More</a>
                <hr>
                <h4>
                    <span class="badge rounded p-1 shadow-none purple">Freeware</span><text class="font-weight-bold"> Orbx - for FSX & P3D</text>
                </h4>
                    <p class="mb-0">ORBX is some of the best in the business at creating scenery. Like most airports, CYXE is covered in their freeware pack. Pick it up for zero dollars and upgrade your sim.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack"class="btn btn-sm btn-primary">View More</a>
        </div>

        <div class="tab-pane fade" id="cyqt" role="tabpanel" aria-labelledby="cyqt"><br>
            <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYQT') == true)
                <div class="col">
                        <div class="card"
                             style="background-color:#122b44; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYQT" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYQT')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#272727; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYQT')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#272727; color:#ffffff;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYQT')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower open daily from 1200Z - 0400Z.</li>
            <hr>
            <h1 class="font-weight-bold blue-text">Scenery</h1> 
                <h4>
                    <span class="badge rounded p-1 shadow-none green">NEW</span>
                    <span class="badge rounded p-1 shadow-none orange">Payware</span>
                    <text class="font-weight-bold"> MFSG - for MSFS, P3Dv3+, FSX, FS2004 </text>
                </h4>
                    <p class="mb-0">It's not easy to find a developer who covers a handful of simulators - but that's what MFSG did with their wonderful Thunder Bay scenery! Check out their rendition, featuring the most updated airport layout of any CYQT options.</p>
                    <a style="margin-left: -0.1%" data-target="_blank" href="https://secure.simmarket.com/mfsg.mhtml"class="btn btn-sm btn-primary">View More</a>
                <hr> 
                <h4>
                    <span class="badge rounded p-1 shadow-none orange">Payware</span><text class="font-weight-bold"> SimAddons - For FSX, P3Dv4, v5 & MSFS</text>
                </h4>
                    <p class="mb-0">SimAddons is here with Thunder Bay too, of course - they've had this out for some time, but the scenery still matches with the current setup in CYQT.</p>
                    <a style="margin-left: -0.1%" data-target="_blank" href="http://www.simaddons.com/pages/simaddons_purchase1.htm"class="btn btn-sm btn-primary">View More</a>
                <hr>     
                <h4>
                    <span class="badge rounded p-1 shadow-none orange">Payware</span><text class="font-weight-bold"> FSXCenery - For MSFS</text>
                </h4>       
                    <p class="mb-0">FSXCenery just recently published their CYQT for MSFS package, and it looks outstanding - the terminal, the parking area, the small aprons scattered across the airport - they did it all.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://secure.simmarket.com/fsxcenery-cyqt-thunder-bay-msfs.phtml"class="btn btn-sm btn-primary">View More</a>
                <hr>
                <h4>
                    <span class="badge rounded p-1 shadow-none orange">Payware</span><text class="font-weight-bold"> FSXCenery - For FSX & P3D</text>
                </h4>
                    <p class="mb-0">It isn't quite up to their MFSF standards, but FSXCenery certainly did a great job modeling Thunder Bay for this FSX/P3D scenery. Pick it up for a low cost and get flying!</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://secure.simmarket.com/fsxcenery-cyqt-thunder-bay-fsx-p3d-(de_13122).phtml"class="btn btn-sm btn-primary">View More</a>
                <hr>
                <h4>
                    <span class="badge rounded p-1 shadow-none purple">Freeware</span><text class="font-weight-bold"> Orbx - for FSX & P3D</text>
                </h4>
                    <p class="mb-0">ORBX is some of the best in the business at creating scenery. Thunder Bay is just another one on their list for the team - and at no cost, it's worth grabbing if you want to up your game.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack"class="btn btn-sm btn-primary">View More</a>
                <hr>
                <h4>
                    <span class="badge rounded p-1 shadow-none purple">Freeware</span><text class="font-weight-bold"> Jim Kanold - For XP11+</text>
                </h4>
                    <p class="mb-0">Hiding on the eastern side of Winnipeg's airspace, this small city in Ontario is home to a major hub for small airlines and private pilots. This free scenery will update XP11 to give it the unique terminal, among other things..</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://forums.x-plane.org/index.php?/files/file/41400-thunder-bay-cyqt/"class="btn btn-sm btn-primary">View More</a>
            </div>

        <div class="tab-pane fade" id="cyqr" role="tabpanel" aria-labelledby="cyqr"><br>
        <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYQR') == true)
                <div class="col">
                        <div class="card"
                             style="background-color:#122b44; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYQR" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYQR')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#272727; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYQR')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#272727; color:#ffffff;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYQR')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower open between April 1st - October 31st, from 1200Z - 0400Z.</li>
            <li>Tower open between November 1st - March 31st, from 1200Z - 0500Z.</li>
            <hr>
            <h1 class="font-weight-bold blue-text">Scenery</h1>
                <h4>
                    <span class="badge rounded p-1 shadow-none orange">Payware</span><text class="font-weight-bold"> FSXCenery - For FSX & P3D</text>
                </h4>
                    <p class="mb-0">FSXCenery has Regina on lock for FSX and P3D - their scenery just released for the simulators models the airport great - and is a fantastic addition to any simulator for central Canada flying.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://secure.simmarket.com/fsxcenery-cyqr-regina-international-airport-fsx-p3d.phtml"class="btn btn-sm btn-primary">View More</a>
                <hr>
                <h4>
                    <span class="badge rounded p-1 shadow-none purple">Freeware</span><text class="font-weight-bold"> Canada4XPlane - For XP10 & XP11+</text>
                </h4>
                    <p class="mb-0">If you're a private pilot, flying to Regina with this scenery from C4XP is a MUST. Their modeling of everything from the terminal to the Regina Flying Club is just extremely detailed - and it's free.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://forums.x-plane.org/index.php?/files/file/50207-cyqr-regina-international-airport/" class="btn btn-sm btn-primary">View More</a>
                <hr>
                <h4>
                    <span class="badge rounded p-1 shadow-none purple">Freeware</span><text class="font-weight-bold"> Orbx - for FSX & P3D</text>
                </h4>
                    <p class="mb-0">ORBX is some of the best in the business at creating scenery. You shouldn't be shocked that CYQR is covered in their freeware pack - it's free, it's a nice upgrade. Go get it today.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack"class="btn btn-sm btn-primary">View More</a>
        </div>

        <div class="tab-pane fade" id="cymj" role="tabpanel" aria-labelledby="cymj"><br>
        <div class="row">
                @if(\App\Classes\WeatherHelper::getAtisLetter('CYMJ') == true)
                <div class="col">
                        <div class="card"
                             style="background-color:#122b44; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYMJ" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{\App\Classes\WeatherHelper::getAtisLetter('CYMJ')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#272727; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYMJ')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#272727; color:#ffffff;">
                            <div class="card-body">
                                <h3 class="font-weight-bold">Current ATIS/METAR</h3>
                                {{\App\Classes\WeatherHelper::getAtis('CYMJ')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower/Terminal open between February 16st - October 31st, from 1400Z - 0030Z.</li>
            <li>Tower/Terminal open between November 1st - Februaty 15th, from 1430Z - 0100Z.</li>
            <li>Tower/Terminal also frequenty closed on weekends.</li>
            <hr>
            <h1 class="font-weight-bold blue-text">Scenery</h1>
                <h4>
                    <span class="badge rounded p-1 shadow-none purple">Freeware</span><text class="font-weight-bold"> Orbx - for FSX & P3D</text>
                </h4>
                    <p class="mb-0">ORBX is some of the best in the business at creating scenery. Moose Jaw is limited for scenery - but they've got ORBX on their side with their freeware pack - and it's a worthwile download.</p>
                    <a style="margin-left: -0.1%" target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack"class="btn btn-sm btn-primary">View More</a>
        </div>
    </div>
</div>

@endsection
