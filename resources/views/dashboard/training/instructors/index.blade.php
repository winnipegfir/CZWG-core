@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 20px;">
        <h1>Instructors</h1>
        <hr>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Current Instructors
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($instructors as $instructor)
                            <a href="#" data-toggle="modal" data-target="#students{{$instructor->id}}" class="list-group-item d-flex justify-content-between align-items-center">
                                {{$instructor->user->fullName('FLC')}}
                                @if(count($instructor->students) < 1)
                                    <span class="badge badge-light badge-pill">
                                        <h6 class="p-0 m-0">
                                             No students
                                        </h6>
                                    </span>
                                @else
                                    <span class="badge badge-primary badge-pill">
                                        <h6 class="p-0 m-0">
                                            {{count($instructor->students)}} Student(s)
                                        </h6>
                                    </span>
                                @endif
                            </a>
                            <div class="modal fade" id="students{{$instructor->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Students assigned to: {{$instructor->user->fullName('FLC')}}</h5><br>

                                        </div>
                                        <div class="modal-body">
                                            @foreach ($instructor->students as $students)
                                            <li>{{$students->user->fullName('FLC')}} ({{$students->user->rating_short}})</li>
                                            @endforeach

                                        </div>
                                        <div class="modal-footer">

                                            <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>

                                        </div>
                                    </div>
                                  </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @if (Auth::user()->permissions >= 4)
            <div class="col">
                <div class="card">
                    <div class="card-header">Actions</div>
                    <div class="card-body">
                        <a href="#" data-toggle="modal" data-target="#addInstructorModal" class="card-link">Add Instructor</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <br><br>
    <div class="modal fade" id="addInstructorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add an instructor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('training.instructors.add')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Choose an Instructor</label>
                        <select name="cid" id="cid" class="form-control">
                            @foreach ($potentialinstructor as $i)
                                <option value="{{$i->cid}}">{{$i->cid}} - {{$i->full_name}}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="form-group">
                        <label class="form-label">Qualification</label>
                        <input required type="text" name="qualification" id="searchBox" class="form-control" placeholder="e.g. Assessor">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input required type="email" name="email" class="form-control">
                        <small>This email will be publically available, so the instructor's CERT email remains hidden.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Add">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        //$('#addInstructorModal').modal('show')
    </script>
@stop
