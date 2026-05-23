@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('title', 'ATC Roster - Winnipeg FIR')
@section('description', "Winnipeg FIR's Controller Roster")

@php
function adminCertBadge($level) {
    switch((string)$level) {
        case '2': return '<span class="cert-badge cert-training"><i class="fas fa-book"></i></span>';
        case '3': return '<span class="cert-badge cert-solo"><i class="fas fa-user"></i></span>';
        case '4': return '<span class="cert-badge cert-certified"><i class="fas fa-check"></i></span>';
        default:  return '<span class="cert-badge cert-none"><i class="fas fa-times"></i></span>';
    }
}
@endphp

@section('content')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <div class="container" style="margin-top: 20px;">
        <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
        <div class="container" style="margin-top: 20px;">
            <h1 class="blue-text font-weight-bold">Controller Roster</h1>
            <hr>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                       aria-controls="home" aria-selected="true">Home Controllers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="visit-tab" data-toggle="tab" href="#visit" role="tab"
                       aria-controls="visit" aria-selected="false">Visiting Controllers</a>
                </li>
            </ul>
            <hr>

            <div class="tab-content" id="myTabContent">

                {{-- Home Controllers --}}
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <h4 class="font-weight-bold blue-text">Actions</h4>
                            <ul class="list-unstyled mt-2 mb-0">
                                <li class="mb-2">
                                    <a href="" data-target="#addController" data-toggle="modal" style="text-decoration:none;">
                                        <span class="blue-text"><i class="fas fa-chevron-right"></i></span>
                                        &nbsp;<span class="black-text">Add a controller to roster</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="roster-legend mb-2">
                        <span class="cert-badge cert-certified"><i class="fas fa-check"></i></span> Certified &nbsp;
                        <span class="cert-badge cert-solo"><i class="fas fa-user"></i></span> Solo &nbsp;
                        <span class="cert-badge cert-training"><i class="fas fa-book"></i></span> Training &nbsp;
                        <span class="cert-badge cert-none"><i class="fas fa-times"></i></span> Not Certified
                    </div>

                    <div class="table-responsive">
                    <table id="rosterTable" class="table table-sm roster-table">
                        <thead>
                            <tr>
                                <th>CID</th>
                                <th>Controller Name</th>
                                <th>Rating</th>
                                <th>DEL</th>
                                <th>GND</th>
                                <th>TWR</th>
                                <th>DEP</th>
                                <th>APP</th>
                                <th>CTR</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th class="text-danger">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($roster as $controller)
                            <tr>
                                <td><strong>{{$controller->cid}}</strong></td>
                                <td>{{$controller->user->fullName('FL')}}</td>
                                <td><span class="rating-badge">{{$controller->user->rating->getShortName()}}</span></td>
                                <td class="text-center">{!! adminCertBadge($controller->del) !!}</td>
                                <td class="text-center">{!! adminCertBadge($controller->gnd) !!}</td>
                                <td class="text-center">{!! adminCertBadge($controller->twr) !!}</td>
                                <td class="text-center">{!! adminCertBadge($controller->dep) !!}</td>
                                <td class="text-center">{!! adminCertBadge($controller->app) !!}</td>
                                <td class="text-center">{!! adminCertBadge($controller->ctr) !!}</td>
                                <td>{{$controller->remarks}}</td>
                                <td class="text-center">
                                    @if ($controller->active == "1")
                                        <span class="status-badge status-active">Active</span>
                                    @else
                                        <span class="status-badge status-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center" style="white-space:nowrap;">
                                    <a href="{{route('roster.editcontrollerform', [$controller->cid])}}">
                                        <button class="btn btn-sm btn-info">Edit</button>
                                    </a>
                                    <a role="button" data-toggle="modal"
                                       data-target="#deleteController{{$controller->id}}"
                                       class="btn btn-sm btn-danger">Delete</a>

                                    <div class="modal fade" id="deleteController{{$controller->id}}" tabindex="-1"
                                         role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Deletion</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="font-weight-bold">Name: {{$controller->user->fullName('FL')}}</p>
                                                    <p class="font-weight-bold">CID: {{$controller->cid}}</p>
                                                    <h3 class="font-weight-bold text-danger">Are you sure you want to do this?</h3>
                                                    <p class="font-weight-bold">Deleting this member from the roster will delete their session logs.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="GET" action="{{ route('roster.deletecontroller', [$controller->id]) }}">
                                                        {{ csrf_field() }}
                                                        <button class="btn btn-danger" type="submit">Delete</button>
                                                    </form>
                                                    <button class="btn btn-light" data-dismiss="modal">Dismiss</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

                {{-- Visiting Controllers --}}
                <div class="tab-pane fade" id="visit" role="tabpanel" aria-labelledby="visit-tab">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <h4 class="font-weight-bold blue-text">Actions</h4>
                            <ul class="list-unstyled mt-2 mb-0">
                                <li class="mb-2">
                                    <a href="" data-target="#addVisitController" data-toggle="modal" style="text-decoration:none;">
                                        <span class="blue-text"><i class="fas fa-chevron-right"></i></span>
                                        &nbsp;<span class="black-text">Add controller to roster</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="roster-legend mb-2">
                        <span class="cert-badge cert-certified"><i class="fas fa-check"></i></span> Certified &nbsp;
                        <span class="cert-badge cert-solo"><i class="fas fa-user"></i></span> Solo &nbsp;
                        <span class="cert-badge cert-training"><i class="fas fa-book"></i></span> Training &nbsp;
                        <span class="cert-badge cert-none"><i class="fas fa-times"></i></span> Not Certified
                    </div>

                    <div class="table-responsive">
                    <table id="rosterVisitTable" class="table table-sm roster-table">
                        <thead>
                            <tr>
                                <th>CID</th>
                                <th>Controller Name</th>
                                <th>Rating</th>
                                <th>DEL</th>
                                <th>GND</th>
                                <th>TWR</th>
                                <th>DEP</th>
                                <th>APP</th>
                                <th>CTR</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th class="text-danger">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($visitroster2 as $visitcontroller)
                            <tr>
                                <td><strong>{{$visitcontroller->cid}}</strong></td>
                                <td>{{$visitcontroller->user->fullName('FL')}}</td>
                                <td><span class="rating-badge">{{$visitcontroller->user->rating->getShortName()}}</span></td>
                                <td class="text-center">{!! adminCertBadge($visitcontroller->del) !!}</td>
                                <td class="text-center">{!! adminCertBadge($visitcontroller->gnd) !!}</td>
                                <td class="text-center">{!! adminCertBadge($visitcontroller->twr) !!}</td>
                                <td class="text-center">{!! adminCertBadge($visitcontroller->dep) !!}</td>
                                <td class="text-center">{!! adminCertBadge($visitcontroller->app) !!}</td>
                                <td class="text-center">{!! adminCertBadge($visitcontroller->ctr) !!}</td>
                                <td>{{$visitcontroller->remarks}}</td>
                                <td class="text-center">
                                    @if ($visitcontroller->active == "1")
                                        <span class="status-badge status-active">Active</span>
                                    @else
                                        <span class="status-badge status-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center" style="white-space:nowrap;">
                                    <a href="{{route('roster.editcontrollerform', [$visitcontroller->cid])}}">
                                        <button class="btn btn-sm btn-info">Edit</button>
                                    </a>
                                    <a role="button" data-toggle="modal"
                                       data-target="#deleteVisitController{{$visitcontroller->id}}"
                                       class="btn btn-sm btn-danger">Delete</a>

                                    <div class="modal fade" id="deleteVisitController{{$visitcontroller->id}}" tabindex="-1"
                                         role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Deletion</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="font-weight-bold">Name: {{$visitcontroller->user->fullName('FL')}}</p>
                                                    <p class="font-weight-bold">CID: {{$visitcontroller->cid}}</p>
                                                    <h3 class="font-weight-bold text-danger">Are you sure you want to do this?</h3>
                                                    <p class="font-weight-bold">Deleting this member from the roster will delete their session logs.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="GET" action="{{ route('roster.deletecontroller', [$visitcontroller->id]) }}">
                                                        {{ csrf_field() }}
                                                        <button class="btn btn-danger" type="submit">Delete</button>
                                                    </form>
                                                    <button class="btn btn-light" data-dismiss="modal">Dismiss</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Add Home Controller Modal --}}
    <div class="modal fade" id="addController" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Controller to Home Roster</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <form method="POST" action="{{ route('roster.addcontroller') }}">
                        @csrf
                        <div class="form-group">
                            <select class="js-example-basic-single form-control" style="width:100%" name="newcontroller">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{$user->id}} - {{$user->fname}} {{$user->lname}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('dropdown'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('dropdown') }}</strong>
                            </span>
                        @endif
                        <p class="font-weight-bold">This user will be added to the Home Roster.</p>
                        <div class="text-center pb-2">
                            <button type="submit" class="btn btn-success">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Visiting Controller Modal --}}
    <div class="modal fade" id="addVisitController" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Controller to Visiting Roster</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <form method="POST" action="{{ route('roster.addvisitcontroller') }}">
                        @csrf
                        <div class="form-group">
                            <select class="js-example-basic-single form-control" style="width:100%" name="newcontroller">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{$user->id}} - {{$user->fname}} {{$user->lname}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('dropdown'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('dropdown') }}</strong>
                            </span>
                        @endif
                        <p class="font-weight-bold">This user will be added to the Visiting Roster.</p>
                        <div class="text-center pb-2">
                            <button type="submit" class="btn btn-success">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#rosterTable').DataTable({ order: [[0, 'asc']], autoWidth: false });
            $('#rosterVisitTable').DataTable({ order: [[0, 'asc']], autoWidth: false });
            $('.js-example-basic-single').select2();
        });
    </script>

@stop
