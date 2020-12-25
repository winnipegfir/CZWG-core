@extends('layouts.master')
@section('title', $id.' - Winnipeg FIR ')
@section('description', $id.'\'s user profile')

<style>
    .flex-container {
        display: flex;
        position: relative;
        align-items: center;
        align-items: center;
        justify-content: center;
        margin: 0px;
    }
</style>

@section('content')
    <div class="container py-4">
        <a href="{{route('roster.public')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Roster</a>
        <h1 class="blue-text font-weight-bold mt-2">Controller Details for {{$id}}</h1>
        <hr>
        <div class="row">
            <div class="col-md-8">
                <div class="row" style="padding-left: 20px">
                    <h2>
                        <img src="{{$user->avatar()}}" style="height: 85px; width: 85px; margin-right: 10px; margin-bottom: 3px; border-radius: 50%;">{{$user->fullName('FLC')}} ({{$user->rating_short}})
                        @if($user->staffProfile)
                            - {{$user->staffProfile->position}}
                        @endif
                    </h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card" style="background-color: #013162; color: #ffffff; float: right;">
                    <div class="flex-container card-body" style="padding-top: 10%;">
                        <h3 style="text-align: center;">&nbsp;Monthly Hours: <b>{{$monthlyHours}}&nbsp;</b></h3>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            @if($rosterMember)
                <div class="col-md-6">
                    <h4 class="font-weight-bold" style="color: #013162; text-align: center;">Certifications</h4>
                    <table id="certificationTable" class="table table-hover">
                        <thead>
                        <tr>
                            <th style="text-align:center; background-color: lightgray;" scope="col"><b>Position</b></th>
                            <th style="text-align:center; background-color: lightgray;" scope="col"><b>Certification</b></th>
                            <th style="text-align:center; background-color: lightgray;" scope="col"><b>Time this Month</b></th>
                        </tr>

                        <!-- Delivery -->
                        <tr>
                            <th scope="row" style="text-align: center;"><b>Delivery</b></th>
                            @if ($rosterMember->del == "1")
                                <td align="center" class="bg-danger text-white">Not Certified</td>
                            @elseif ($rosterMember->del == "2")
                                <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                            @elseif ($rosterMember->del == "3")
                                <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                            @elseif ($rosterMember->del == "4")
                                <td align="center" class="bg-success text-white">Certified</td>
                            @else
                                <td align="center" class="bg-danger text-white">ERROR</td>
                            @endif
                            <td align="center">{{$time['del']}}</td>
                        </tr>

                        <!-- Ground -->
                        <tr>
                            <th scope="row" style="text-align: center;"><b>Ground</b></th>
                            @if ($rosterMember->gnd == "1")
                                <td align="center" class="bg-danger text-white">Not Certified</td>
                            @elseif ($rosterMember->gnd == "2")
                                <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                            @elseif ($rosterMember->gnd == "3")
                                <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                            @elseif ($rosterMember->gnd == "4")
                                <td align="center" class="bg-success text-white">Certified</td>
                            @else
                                <td align="center" class="bg-danger text-white">ERROR</td>
                            @endif
                            <td align="center">{{$time['gnd']}}</td>
                        </tr>

                        <!-- Tower -->
                        <tr>
                            <th scope="row" style="text-align: center;"><b>Tower</b></th>
                            @if ($rosterMember->twr == "1")
                                <td align="center" class="bg-danger text-white">Not Certified</td>
                            @elseif ($rosterMember->twr == "2")
                                <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                            @elseif ($rosterMember->twr == "3")
                                <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                            @elseif ($rosterMember->twr == "4")
                                <td align="center" class="bg-success text-white">Certified</td>
                            @else
                                <td align="center" class="bg-danger text-white">ERROR</td>
                            @endif
                            <td align="center">{{$time['twr']}}</td>
                        </tr>

                        <!-- Departure -->
                        <tr>
                            <th scope="row" style="text-align: center;"><b>Departure</b></th>
                            @if ($rosterMember->dep == "1")
                                <td align="center" class="bg-danger text-white">Not Certified</td>
                            @elseif ($rosterMember->dep == "2")
                                <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                            @elseif ($rosterMember->dep == "3")
                                <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                            @elseif ($rosterMember->dep == "4")
                                <td align="center" class="bg-success text-white">Certified</td>
                            @else
                                <td align="center" class="bg-danger text-white">ERROR</td>
                            @endif
                            <td align="center">{{$time['dep']}}</td>
                        </tr>

                        <!-- Arrival -->
                        <tr>
                            <th scope="row" style="text-align: center;"><b>Arrival</b></th>
                            @if ($rosterMember->app == "1")
                                <td align="center" class="bg-danger text-white">Not Certified</td>
                            @elseif ($rosterMember->app == "2")
                                <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                            @elseif ($rosterMember->app == "3")
                                <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                            @elseif ($rosterMember->app == "4")
                                <td align="center" class="bg-success text-white">Certified</td>
                            @else
                                <td align="center" class="bg-danger text-white">ERROR</td>
                            @endif
                            <td align="center">{{$time['app']}}</td>
                        </tr>

                        <!-- Centre -->
                        <tr>
                            <th scope="row" style="text-align: center;"><b>Centre</b></th>
                            @if ($rosterMember->ctr == "1")
                                <td align="center" class="bg-danger text-white">Not Certified</td>
                            @elseif ($rosterMember->ctr == "2")
                                <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                            @elseif ($rosterMember->ctr == "3")
                                <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                            @elseif ($rosterMember->ctr == "4")
                                <td align="center" class="bg-success text-white">Certified</td>
                            @else
                                <td align="center" class="bg-danger text-white">ERROR</td>
                            @endif
                            <td align="center">{{$time['ctr']}}</td>
                        </tr>
                        </thead>
                    </table>
                </div>
            @endif
            <div class="col-md-6">
                <h4 class="font-weight-bold" style="color: #013162; text-align: center;">Recent Connections</h4>
                <table id="connectionsTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th style="text-align:center; background-color: lightgray;" scope="col"><b>Callsign</b></th>
                        <th style="text-align:center; background-color: lightgray;" scope="col"><b>Duration</b></th>
                    </tr>
                    </thead>
                    @if(count($connections) == 0)
                        <tr>
                            <td colspan="2" style="text-align: center">
                                No connection history this month.
                            </td>
                        </tr>
                    @else
                        @foreach($connections->take(3) as $c)
                            <tr>
                                <td style="text-align: center">
                                    {{strtoupper($c['callsign'])}}
                                </td>
                                <td style="text-align: center">
                                    {{$c['duration']}}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" style="text-align: center">
                                <a href="/roster/{{$id}}/connections" style="color: #013162;">View all their connections this month here.</a>
                            </td>
                        </tr>
                    @endif
                </table>
            @if(!$rosterMember)
            </div>
            <div class="col-md-6" style="padding-top: 29px;">
            @endif
                @if($user->bio)
                    <div class="card" style="text-align: center; background-color: lightgray; width: 100%;{{$rosterMember ? 'float:right;' : 'float:left;'}}">
                        <div class="flex-container card-body" style="padding-top: 4%;">
                            <h5 class="font-italic">{{$user->bio}}</h5>
                        </div>
                    </div>
                    <br>
                @endif
            </div>
        </div>
    </div>
@endsection
