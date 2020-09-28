@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'ATC Roster - Winnipeg FIR')
@section('description', "Winnipeg FIR's Controller Roster")

@section('content')

    <div class="container" style="margin-top: 20px;">
        <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i
                class="fas fa-arrow-left"></i> Dashboard</a>
        <div class="container" style="margin-top: 20px;">
            <h1 class="blue-text font-weight-bold">Controller Roster</h1>
            <hr>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                       aria-controls="home" aria-selected="true">Home Controllers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="visit-tab" data-toggle="tab" href="#visit" role="tab" aria-controls="visit"
                       aria-selected="false">Visiting Controllers</a>
                </li>
            </ul>
            <hr>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row">
                        <div class="col-md-3">
                            <h4 class="font-weight-bold blue-text">Actions</h4>
                            <ul class="list-unstyled mt-2 mb-0">
                                <li class="mb-2">
                                    <a href="" data-target="#addController" data-toggle="modal"
                                       style="text-decoration:none;">
                        <span class="blue-text">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                                        &nbsp;
                                        <span class="black-text">
                            Add a controller to roster
                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>


                    <!--WINNIPEG CONTROLLERS ROSTER-->
                    <table id="rosterTable" class="table table-hover" style="position:relative; left:-70px; top:2px; width:116%;">
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
                            <th style="text-align:center" scope="col">Remarks</th>
                            <th style="text-align:center" scope="col">Status</th>
                            <th style="text-align:center" width="18%" class="text-danger" scope="col"><b>Actions</b>
                            </th>

                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($roster as $controller)
                            <tr>
                                <th scope="row"><b>{{$controller->cid}}</b></th>
                                <td align="center">
                                    {{$controller->user->fullName('FL')}}
                                </td>
                                <td align="center">
                                    {{$controller->user->rating_short}}
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
                            <!--Remarks-->
                                <td align="center">
                                    {{$controller->remarks}}
                                </td>
                                <!--Active Status-->
                                @if ($controller->active == "0")
                                    <td align="center" class="bg-danger text-white">Not Active</td>
                                @elseif ($controller->active == "1")
                                    <td align="center" class="bg-success text-white">Active</td>
                                @else
                                    <td align="center" class="bg-danger text-white">ERROR</td>
                            @endif
                            <!--Edit controller-->
                                <td align="center" style="width=100px">
                                    <a href="{{route('roster.editcontrollerform', [$controller->cid]) }}">
                                        <button class="btn btn-sm btn-info"
                                                style="vertical-align:top; float:left;">Edit
                                        </button>
                                    </a>

                                    </li>
                                    </ul>


                                    <!--END OF EDIT CONTROLLER-->
                                    <!--DELETE CONTROLLER-->
                                    <!--Confirm Delete controller button-->
                                    <div class="modal fade" id="deleteController{{$controller->id}}" tabindex="-1"
                                         role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Confirm
                                                        Deletion</h5><br>
                                                </div>
                                                <div class="modal-body">
                                                    <p style="font-weight:bold">Name: {{$controller->user->fullName('FL')}}</p>
                                                    <p style="font-weight:bold">CID: {{$controller->cid}}</p>
                                                    <h3 style="font-weight:bold; color:red">Are you sure you want to do
                                                        this?</h3>
                                                    <p style="font-weight:bold">Deleting this member from the roster
                                                        will delete their session logs.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="GET"
                                                          action="{{ route('roster.deletecontroller', [$controller->id]) }}">
                                                        {{ csrf_field() }}
                                                        <button class="btn btn-danger" type="submit" href="#">Delete
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-light" data-dismiss="modal"
                                                            style="width:375px">Dismiss
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end delete controller-->
                                    <a role="button" data-toggle="modal"
                                       data-target="#deleteController{{$controller->id}}"
                                       class="btn btn-sm btn-danger"
                                       style="vertical-align:bottom; float:right;">Delete</a>
                </div>
            </div>
            </td>
            </tr>

            @endforeach
            </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="visit" role="tabpanel" aria-labelledby="visit-tab">   
            <div class="row">
                <div class="col-md-3">
                    <h4 class="font-weight-bold blue-text">Actions</h4>
                    <ul class="list-unstyled mt-2 mb-0">
                        <li class="mb-2">
                            <a href="" data-target="#addVisitController" data-toggle="modal"
                               style="text-decoration:none;">
                                <span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span
                                    class="black-text">Add controller to roster</span></a>
                        </li>

                    </ul>
                </div>
                <!--WINNIPEG VISITING CONTROLLERS ROSTER-->
                <table id="rosterVisitTable" class="table table-hover">
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
                        <th style="text-align:center" scope="col">Remarks</th>
                        <th style="text-align:center" scope="col">Status</th>
                        <th style="text-align:center" width="18%" class="text-danger" scope="col"><b>Actions</b></th>

                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($visitroster2 as $visitcontroller)
                        <tr>
                            <th scope="row"><b>{{$visitcontroller->cid}}</b></th>
                            <td align="center">
                                {{$visitcontroller->user->fullName('FL')}}
                            </td>
                            <td align="center">
                                {{$visitcontroller->user->rating_short}}
                            </td>

                            <!--WINNIPEG Controller Position Ratings from Db -->
                            <!--Delivery-->
                            @if ($visitcontroller->del == "1")
                                <td align="center" class="bg-danger text-white">Not Certified</td>
                            @elseif ($visitcontroller->del == "2")
                                <td align="center" style="background-color:#ffe401" class="text-white">Training</td>
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
                        <!--Remarks-->
                            <td align="center">
                                {{$visitcontroller->remarks}}
                            </td>
                            <!--Active Status-->
                            @if ($visitcontroller->active == "0")
                                <td align="center" class="bg-danger text-white">Not Active</td>
                            @elseif ($visitcontroller->active == "1")
                                <td align="center" class="bg-success text-white">Active</td>
                            @else
                                <td align="center" class="bg-danger text-white">ERROR</td>
                        @endif
                            <td align="center">
                                <a href="{{route('roster.editcontrollerform', [$visitcontroller->cid]) }}">
                                    <button class="btn btn-sm btn-info" style="vertical-align:top; float:left;">
                                        Edit
                                    </button>
                                </a>


                                <!--Delete controller-->
                                <!--Confirm Delete visitor button-->
                                <div class="row">
                                    <div class="modal fade" id="deleteVisitController{{$visitcontroller->id}}" tabindex="-1"
                                         role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Confirm Deletion</h5>
                                                    <br>
                                                </div>
                                                <div class="modal-body">
                                                    <p style="font-weight:bold">Name: {{$visitcontroller->user->fullName('FL')}}</p>
                                                    <p style="font-weight:bold">CID: {{$visitcontroller->cid}}</p>
                                                    <h3 style="font-weight:bold; color:red">Are you sure you want to do
                                                        this?</h3>
                                                    <p style="font-weight:bold">Deleting this member from the roster will
                                                        delete their session logs.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="GET"
                                                          action="{{ route('roster.deletecontroller', [$visitcontroller->id]) }}">
                                                        {{ csrf_field() }}
                                                        <button class="btn btn-danger" type="submit" href="#">Delete
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-light" data-dismiss="modal" style="width:375px">
                                                        Dismiss
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end delete visitor-->

                                    <a role="button" data-toggle="modal"
                                       data-target="#deleteVisitController{{$visitcontroller->id}}"
                                       class="btn btn-sm btn-danger" style="vertical-align:bottom; float:right;">Delete</a>
                                </div>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>

                <script>
                    $(document).ready(function () {
                        $('#rosterTable', '#rosterVisitTable').DataTable({
                            "order": [[0, "asc"]]
                        });
                    });
                </script>

            </div>
        </div>
    </div>

    <!--MODALS-->

    <!--Add Winnipeg controller modal-->
    <div class="modal fade" id="addController" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Winnipeg Controller to roster</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div align="center" class="modal-body">

                    <div class="form-group row">
                        <label for="dropdown"
                               class="col-sm-4 col-form-label text-md-right">{{ __('Controllers') }}</label>

                        <div class="col-md-12">
                            <form method="POST" action="{{ route('roster.addcontroller' )}}">
                                <select class="custom-select" name="newcontroller">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id}}">{{$user->id}}
                                            - {{$user->fname}} {{$user->lname}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('dropdown'))
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('dropdown') }}</strong>
                                </span>
                                @endif
                                <td align="center">
                                    @csrf
                                    <p class="font-weight-bold"> *This will add them to the Home Controller Roster*</p>
                                    <button type="submit" class="btn btn-success">Add User</button>

                                </td>
                            </form>
                        </div>
                    </div>

                </div>

                <div align="center" class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End add Winnipeg controller modal-->

    <!--Add Visitor controller modal-->
    <div class="modal fade" id="addVisitController" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Visiting Controller to roster</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div align="center" class="modal-body">

                    <div class="form-group row">
                        <label for="dropdown"
                               class="col-sm-4 col-form-label text-md-right">{{ __('Controllers') }}</label>

                        <div class="col-md-12">
                            <form method="POST" action="{{ route('roster.addvisitcontroller' )}}">
                                <select class="custom-select" name="newcontroller">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id}}">{{$user->id}}
                                            - {{$user->fname}} {{$user->lname}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('dropdown'))
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('dropdown') }}</strong>
                                </span>
                                @endif
                                <br>
                                <br>

                                <td align="center">
                                    @csrf
                                    <p class="font-weight-bold"> *This will add them to the Visiting Controller Roster*</p>
                                    <button type="submit" class="btn btn-success">Add User</button>

                                </td>
                            </form>
                        </div>
                    </div>

                </div>

                <div align="center" class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--End add Visitor controller modal-->

    <!--Edit Controller Modal-->
    <div class="modal fade" id="editControllerModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Controller</h5><br>
                    <b>Warning:</b><br>May not function correctly, still in development.
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form method="POST" action="/dashboard/roster" id="editControllerForm">
                    <div class="modal-body">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <style>
                            * {
                                box-sizing: border-box;
                            }

                            /* Create two equal columns that floats next to each other */
                            .column {
                                float: left;
                                width: 50%;
                                padding: 10px;
                            }

                            /* Clear floats after the columns */
                            .row:after {
                                content: "";
                                display: table;
                                clear: both;
                            }
                        </style>
                        </head>

                        <div align="center">
                            <div class="form-group">
                                <label for="cid">CID:</label><br>
                                <input type="text" id="id" class="form-control" readonly>
                            </div>
                            <br>
                            <div align="center" class="row">
                                <div align="center" class="column">
                                    <label for="del">Delivery:</label><br>
                                    <select style="width: 100px;" id="del" name="del">
                                        <option value="1">Not Certified</option>
                                        <option value="2">Training</option>
                                        <option value="3">Solo</option>
                                        <option value="4">Certified</option>
                                    </select>

                                    <br><br>
                                    <label for="gnd">Ground:</label><br>
                                    <select style="width: 100px;" id="gnd" name="gnd">
                                        <option value="1">Not Certified</option>
                                        <option value="2">Training</option>
                                        <option value="3">Solo</option>
                                        <option value="4">Certified</option>
                                    </select>
                                    <br><br>
                                    <label for="twr">Tower:</label><br>
                                    <select style="width: 100px;" id="twr" name="twr" style="width: 120px;">
                                        <option value="1">Not Certified</option>
                                        <option value="2">Training</option>
                                        <option value="3">Solo</option>
                                        <option value="4">Certified</option>
                                    </select>
                                </div>
                                <div align="center" class="column">
                                    <label for="dep">Departure:</label><br>
                                    <select style="width: 100px;" id="dep" name="dep">
                                        <option value="1">Not Certified</option>
                                        <option value="2">Training</option>
                                        <option value="3">Solo</option>
                                        <option value="4">Certified</option>
                                    </select>
                                    <br><br>
                                    <label for="app">Approach:</label><br>
                                    <select style="width: 100px;" id="app" name="app">
                                        <option value="1">Not Certified</option>
                                        <option value="2">Training</option>
                                        <option value="3">Solo</option>
                                        <option value="4">Certified</option>
                                    </select><br>
                                    <br>
                                    <label for="ctr">Center:</label><br>
                                    <select style="width: 100px;" id="ctr" name="ctr">
                                        <option value="1">Not Certified</option>
                                        <option value="2">Training</option>
                                        <option value="3">Solo</option>
                                        <option value="4">Certified</option>
                                    </select><br>
                                    <br>
                                </div>

                                <label style="width: 100px;" for="remarks">Remarks</label><br>
                                <textarea id="remarks" rows="4" cols="30">
                </textarea>
                                <br><br><br>
                                <input type="submit" value="Edit Controller" style="btn btn-info">
                </form>
            </div>
        </div>
    </div>

    <div align="center" class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
        </form>
    </div>
    </div>
    </div>
    </div>

    <!--Edit Winnipeg controller modal-->
    <!--Confirm Delete controller button-->
    <div class="modal fade" id="deleteController" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Confirm Deletion</h5><br>
                </div>
                <div class="modal-body">
                    <p style="font-weight:bold">Name: {{$controller->full_name}}</p>
                    <p style="font-weight:bold">CID: {{$controller->cid}}</p>
                    <h3 style="font-weight:bold; color:red">Are you sure you want to do this?</h3>
                    <p style="font-weight:bold">Deleting this member from the roster will delete their session logs.</p>
                </div>
                <div class="modal-footer">
                    <form method="GET" action="{{ route('roster.deletecontroller', [$controller->id]) }}">
                        {{ csrf_field() }}
                        <button class="btn btn-danger" type="submit" href="#">Delete</button>
                    </form>
                    <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
    <!--end delete controller-->

    <div class="modal fade" id="joinDiscordServerModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Joining the Winnipeg FIR Discord server allows you to join the Winnipeg FIR controller and pilot
                        community.</p>
                    <h5>Rules</h5>
                    <ul>
                        <li>1. The VATSIM Code of Conduct applies.</li>
                        <li>2. Always show respect and common decency to fellow members.</li>
                        <li>3. Do not send server invites to servers unrelated to VATSIM without staff permission. Do
                            not send ANY invites via DMs unless asked to.
                        </li>
                        <li>4. Do not send spam in the server, including images, text, or emotes.</li>
                    </ul>
                    <p>Clicking the 'Join' button will redirect you to Discord. We require the Join Server permission to
                        add your Discord account to the server.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <a role="button" type="submit" href="{{route('me.discord.join')}}" class="btn btn-primary">Join</a>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!--SCRIPTS-->


    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#rosterTable').DataTable();

            //Start Edit Record
            table.on('click', '.editcontroller', function () {
                $tr = $(this).closest('tr');
                if ($(tr).hasClass('child')) {
                    $tr = $tr.prev('.parent');
                }
                var data = table.row($tr).data();
                console.log(data);
                $('#remarks').val(data[9]);

                $(#editControllerForm
                ').attr('
                action
                ', ' / dashboard / roster / '+data[0]);
                $('#editControllerModal').modal('show');
            });
            //End Edit Record

        });
    </script>

@stop
