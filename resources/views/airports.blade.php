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
    <h1 class="font-weight-bold blue-text">Airports</h1><br>
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
                @if(getAtisLetter('CYWG') == true)
                    <div class="col">
                        <div class="card"
                             style="background-color:#013162; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYWG" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{getAtisLetter('CYWG')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#9f9f9f; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYWG')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#9f9f9f; color:#ffffff;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYWG')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower/Terminal at Winnipeg International (CYWG) is open 24/7.</li>
            <li>Tower St. Andrews (CYAV) is open daily from 1300Z - 0400Z.</li>
        </div>

        <div class="tab-pane fade" id="cypg" role="tabpanel" aria-labelledby="cypg"><br>
        <div class="row">
                @if(getAtisLetter('CYPG') == true)
                <div class="col">
                        <div class="card"
                             style="background-color:#013162; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYPG" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{checkAtisLetter('CYPG')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#9f9f9f; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYPG')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#9f9f9f; color:#ffffff;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYPG')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower open Mon - Fri from 1400Z - 2300Z, excluding holidays.</li>
        </div>

        <div class="tab-pane fade" id="cyxe" role="tabpanel" aria-labelledby="cyxe"><br>
        <div class="row">
                @if(getAtisLetter('CYXE') == true)
                <div class="col">
                        <div class="card"
                             style="background-color:#013162; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYXE" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{checkAtisLetter('CYXE')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#9f9f9f; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYXE')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#9f9f9f; color:#ffffff;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYXE')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower open Mon - Fri between March 9th - October 31st, from 1200Z - 0445Z.</li>
            <li>Tower open Sat - Sun between March 9th - October 31st, from 1245Z - 0445Z.</li>
            <li>Tower open between November 1st - March 8th, from 1245Z - 0445Z.</li>
        </div>

        <div class="tab-pane fade" id="cyqt" role="tabpanel" aria-labelledby="cyqt"><br>
            <div class="row">
                @if(getAtisLetter('CYQT') == true)
                <div class="col">
                        <div class="card"
                             style="background-color:#013162; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYQT" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{checkAtisLetter('CYQT')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#9f9f9f; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYQT')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#9f9f9f; color:#ffffff;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYQT')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower open daily from 1200Z - 0400Z.</li>
        </div>


        <div class="tab-pane fade" id="cyqr" role="tabpanel" aria-labelledby="cyqr"><br>
        <div class="row">
                @if(getAtisLetter('CYQR') == true)
                <div class="col">
                        <div class="card"
                             style="background-color:#013162; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYQR" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{checkAtisLetter('CYQR')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#9f9f9f; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYQR')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#9f9f9f; color:#ffffff;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYQR')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower open between April 1st - October 31st, from 1200Z - 0400Z.</li>
            <li>Tower open between November 1st - March 31st, from 1200Z - 0500Z.</li>
        </div>

        <div class="tab-pane fade" id="cymj" role="tabpanel" aria-labelledby="cymj"><br>
        <div class="row">
                @if(getAtisLetter('CYMJ') == true)
                <div class="col">
                        <div class="card"
                             style="background-color:#013162; color:#ffffff; width: 25%; float:left; min-height: 100%;">
                            <div class="card-body">
                                <div class="CYMJ" style="text-align: center;">
                                    <div class="ATIS">
                                        <h5>ATIS</h5>
                                        <h1 style="font-size:45px;"><b>{{checkAtisLetter('CYMJ')}}</b></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card"
                             style="background-color:#9f9f9f; color:#ffffff; width: 175%; float: right;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYMJ')}}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col">
                        <div class="card" style="background-color:#9f9f9f; color:#ffffff;">
                            <div class="card-body">
                                <h3>Current ATIS/METAR</h3>
                                {{checkAtis('CYMJ')}}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <li>Tower/Terminal open between February 16st - October 31st, from 1400Z - 0030Z.</li>
            <li>Tower/Terminal open between November 1st - Februaty 15th, from 1430Z - 0100Z.</li>
            <li>Tower/Terminal also frequenty closed on weekends.</li>
        </div>
    </div>
</div>

@endsection
