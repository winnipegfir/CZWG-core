@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'Roster - Winnipeg FIR')
@section('description', "Winnipeg FIR's Controller Roster")

@section('content')
<div class="container" style="margin-top: 20px;">
        <h1 class="blue-text font-weight-bold">Controller Roster</h1>
        <hr>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Home Controllers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="visit-tab" data-toggle="tab" href="#visit" role="tab" aria-controls="visit" aria-selected="false">Visiting Controllers</a>
            </li>
            @if (Auth::check() && Auth::user()->permissions >= 4)
            <li class="nav-item">
                <a class="nav-link" href="{{route('roster.index')}}" style="color:brown">Edit Roster</a>
            @endif
          </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab"><br>

<!--WINNIPEG CONTROLLERS ROSTER-->
        <table id="rosterTable" class="table table-hover">
            <thead>
                <tr>
                    <th style="text-align:center" scope="col"><b>CID</b></th>
                    <th style="text-align:center" scope="col">Controller Name</th>
                    <th style="text-align:center" scope="col">Rating</th>
                    <th style="text-align:center" scope="col">DEL</th>
                    <th style="text-align:center" scope="col">GND</th>
                    <th style="text-align:center" scope="col">TWR</th>
                    <th style="text-align:center" scope="col">DEP</th>
                    <th style="text-align:center" scope="col">APP</th>
                    <th style="text-align:center" scope="col">CTR</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($roster as $controller)
                    <th style="text-align: center" scope="row"><a href="{{url('/roster/'.$controller->cid)}}" style="color: #122b44;"><b>{{$controller->cid}}</b></a></th>
                    <td align="center" >
                        {{$controller->user->fname}} {{$controller->user->lname}}
                    </td>
                    <td align="center">
                        {{$controller->user->rating->getShortName()}}
                    </td>

<!--WINNIPEG Controller Position Ratings from Db -->
<!--Delivery-->
                    @if ($controller->del == "1")
                        <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($controller->del == "2")
                        <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($controller->del == "3")
                        <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($controller->del == "4")
                        <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
<!--Ground-->
                    @if ($controller->gnd == "1")
                        <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($controller->gnd == "2")
                        <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($controller->gnd == "3")
                        <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($controller->gnd == "4")
                        <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
<!--Tower-->
                    @if ($controller->twr == "1")
                      <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($controller->twr == "2")
                      <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($controller->twr == "3")
                      <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($controller->twr == "4")
                      <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
<!--Departure-->
                    @if ($controller->dep == "1")
                        <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($controller->dep == "2")
                        <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($controller->dep == "3")
                        <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($controller->dep == "4")
                        <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
<!--Approach-->
                    @if ($controller->app == "1")
                        <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($controller->app == "2")
                        <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($controller->app == "3")
                        <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($controller->app == "4")
                        <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
<!--Centre-->
                    @if ($controller->ctr == "1")
                        <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($controller->ctr == "2")
                        <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($controller->ctr == "3")
                        <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($controller->ctr == "4")
                        <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif

<!--Active Status-->
                </tr>
            @endforeach
            </tbody>
        </table>
<br>
</div>
<div class="tab-pane fade" id="visit" role="tabpanel" aria-labelledby="visit-tab"><br>

<!--WINNIPEG VISITING CONTROLLERS ROSTER-->
        <table id="visitRosterTable" class="table table-hover">
            <thead>
                <tr>
                    <th style="text-align:center" scope="col"><b>CID</b></th>
                    <th style="text-align:center" scope="col">Controller Name</th>
                    <th style="text-align:center" scope="col">Rating</th>
                    <th style="text-align:center" scope="col">DEL</th>
                    <th style="text-align:center" scope="col">GND</th>
                    <th style="text-align:center" scope="col">TWR</th>
                    <th style="text-align:center" scope="col">DEP</th>
                    <th style="text-align:center" scope="col">APP</th>
                    <th style="text-align:center" scope="col">CTR</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($visitroster as $visitcontroller)
                <tr>
                    <th style="text-align: center" scope="row"><a href="{{url('/roster/'.$visitcontroller->cid)}}" style="color: #122b44;"><b>{{$visitcontroller->cid}}</b></a></th>
                    <td align="center" >
                        {{$visitcontroller->user->fname}} {{$visitcontroller->user->lname}}
                    </td>
                    <td align="center">
                        {{$visitcontroller->user->rating_short}}
                    </td>

<!--WINNIPEG Controller Position Ratings from Db -->
<!--Delivery-->
                    @if ($visitcontroller->del == "1")
                        <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($visitcontroller->del == "2")
                        <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($visitcontroller->del == "3")
                        <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($visitcontroller->del == "4")
                        <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
<!--Ground-->
                    @if ($visitcontroller->gnd == "1")
                        <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($visitcontroller->gnd == "2")
                        <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($visitcontroller->gnd == "3")
                        <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($visitcontroller->gnd == "4")
                        <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
<!--Tower-->
                    @if ($visitcontroller->twr == "1")
                      <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($visitcontroller->twr == "2")
                      <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($visitcontroller->twr == "3")
                      <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($visitcontroller->twr == "4")
                      <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
<!--Departure-->
                    @if ($visitcontroller->dep == "1")
                        <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($visitcontroller->dep == "2")
                        <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($visitcontroller->dep == "3")
                        <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($visitcontroller->dep == "4")
                        <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
<!--Approach-->
                    @if ($visitcontroller->app == "1")
                        <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($visitcontroller->app == "2")
                        <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($visitcontroller->app == "3")
                        <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($visitcontroller->app == "4")
                        <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
<!--Centre-->
                    @if ($visitcontroller->ctr == "1")
                        <td align="center" class="bg-danger text-white">Not Certified</td>
                    @elseif ($visitcontroller->ctr == "2")
                        <td align="center" style="background-color:#ffe401" class="text-black">Training</td>
                    @elseif ($visitcontroller->ctr == "3")
                        <td align="center" style="background-color:#e29500" class="text-white">Solo</td>
                    @elseif ($visitcontroller->ctr == "4")
                        <td align="center" class="bg-success text-white">Certified</td>
                    @else
                        <td align="center" class="bg-danger text-white">ERROR</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table><br>

        </div>
    </div>
</div>
<script>
        $(document).ready(function() {
            $.fn.dataTable.enum(['C1', 'C3', 'I1', 'I3', 'SUP', 'ADM'])
            $('#rosterTable').DataTable( {
                "order": [[ 0, "asc" ]]
            } );
        } );
    </script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/enum.js"></script>
@stop
