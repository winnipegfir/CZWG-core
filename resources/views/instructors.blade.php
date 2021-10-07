@extends('layouts.master')

@section('title', 'Instructing Staff - Winnipeg FIR')
@section('description', 'The Winnipeg Instructors and Mentors!')

@section('content')
    <div class="container" style="margin-top: 20px;">
        <a href="{{route('staff')}}" class="blue-text" style="font-size: 1.2em"> <i class="fas fa-arrow-left"></i> Staff</a>
        <div class="col p-0">

            <h2 class="blue-text font-weight-bold">Instructors</h2>
            <div class="row">
                @foreach (\App\Models\Teacher::where('is_instructor', 1)->get() as $instructor)
                    <div class="col col-6 mb-3">
                        <div class="card card-body">
                            <div class="d-flex flex-row">
                                <img src="{{$instructor->user->avatar()}}" style="border-radius: 50%; width: 100px; height: 100px; margin-right: 4%;">
                                <div class="d-flex flex-column">
                                    <h3 class="mb-1 font-weight-bold blue-text">{{$instructor->user->fullName('FL')}}</h3>
                                    <a href="mailto:{{$instructor->user->email}}"><h5 class="blue-text">{{$instructor->user->email}}</h5></a>
                                    <div class="row pl-3">
                                        @if($instructor->is_local)
                                            <button class="btn btn-sm btn-local ml-0">Local</button>
                                        @endif
                                        @if($instructor->is_radar)
                                            <button class="btn btn-sm btn-radar ml-0">Radar</button>
                                        @endif
                                        @if($instructor->is_enroute)
                                            <button class="btn btn-sm btn-enroute ml-0">En-Route</button>
                                        @endif
                                        @if (Auth::check() && Auth::user()->permissions >= 4)
                                            <a href="{{route('instructors.delete', [$instructor->id]) }}">
                                                <button class="ml-0 btn btn-sm btn-danger">Delete</button>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <br>

            <h2 class="blue-text font-weight-bold">Mentors</h2>
            <div class="row">
                @foreach (\App\Models\Teacher::where('is_instructor', 0)->get() as $instructor)
                    <div class="col col-6 mb-3">
                        <div class="card card-body">
                            <div class="d-flex flex-row">
                                <img src="{{$instructor->user->avatar()}}" style="border-radius: 50%; width: 100px; height: 100px; margin-right: 4%;">
                                <div class="d-flex flex-column">
                                    <h3 class="mb-1 font-weight-bold blue-text">{{$instructor->user->fullName('FL')}}</h3>
                                    <a href="mailto:{{$instructor->user->email}}"><h5 class="blue-text">{{$instructor->user->email}}</h5></a>
                                    <div class="row pl-3">
                                        @if($instructor->is_local)
                                            <button class="btn btn-sm btn-local ml-0">Local</button>
                                        @endif
                                        @if($instructor->is_radar)
                                            <button class="btn btn-sm btn-radar ml-0">Radar</button>
                                        @endif
                                        @if($instructor->is_enroute)
                                            <button class="btn btn-sm btn-enroute ml-0">En-Route</button>
                                        @endif
                                        @if (Auth::check() && Auth::user()->permissions >= 4)
                                            <a href="{{route('instructors.delete', [$instructor->id]) }}">
                                                <button class="ml-0 btn btn-sm btn-danger">Delete</button>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <br>

            @if (Auth::check() && Auth::user()->permissions >= 4)
                <button class="ml-0 btn btn-primary" data-target="#addTeacher" data-toggle="modal">Add Teacher</button>
        @endif
        <!--Create teacher modal-->
            <div class="modal fade" id="addTeacher" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Add Instructor/Mentor</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('instructors.store') }}" method="post">
                            @csrf
                            <div class="modal-body pt-0 pb-0">
                                <select class="js-example-basic-single form-control" style="width:100%" name="newteacher">
                                    @foreach (\App\Models\AtcTraining\RosterMember::all() as $user)
                                        <option value="{{$user->cid}}">{{$user->cid}} - {{$user->full_name}}</option>
                                    @endforeach
                                </select>
                                <div class="pt-3 form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_instructor" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">Instructor</label>
                                </div>
                                <br>
                                <h5 class="font-weight-bold blue-text">Specialties</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_local" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">Local</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_radar" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">Radar</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_enroute" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">En-Route</label>
                                </div>
                                <div class="form-group">
                                    <div class="col-ml-0">
                                        <button name="submit" class="m-0 mt-3 btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
@stop
