@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 20px;">
        <b><h1>Student: {{$student->user->fullName('FLC')}}</h1></b>
        <h4>  @switch ($student->user->rating_short)
          @case('INA')
          Inactive (INA)
          @break
          @case('OBS')
          Pilot/Observer (OBS)
          @break
          @case('S1')
          Ground Controller (S1)
          @break
          @case('S2')
          Tower Controller (S2)
          @break
          @case('S3')
          TMA Controller (S3)
          @break
          @case('C1')
          Enroute Controller (C1)
          @break
          @case('C3')
          Senior Controller (C3)
          @break
          @case('I1')
          Instructor (I1)
          @break
          @case('I3')
          Senior Instructor (I3)
          @break
          @case('SUP')
          Supervisor (SUP)
          @break
          @case('ADM')
          Administrator (ADM)
          @break
          @endswitch</h4>
        <hr>
        <div class="row">
            <div class="col">
                <h3 class="font-weight-bold blue-text pb-2">Training Notes <a class ="btn btn-sm btn-primary"href="{{route('view.add.note', $student->id)}}" style="float: right;">New Training Note</a></h3>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                              <table id="dataTable" class="table table-hover">
                                  <thead>
                                      <tr>
                                          <th scope="col">Title</th>
                                          <th scope="col">Published on</th>
                                          <th scope="col">Published By</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                  @foreach ($student->trainingNotes as $notes)
                                  <tr>
                                      <th scope="row"><a href="{{route('trainingnote.view', $notes->id)}}">{{$notes->title}}</a></th>
                                      <td>
                                        {{$notes->created_at}}
                                      </td>
                                      <td>
                                          <a href="{{route('training.students.view', $student->id)}}">
                                              {{$notes->instructor->user->fullName('FLC')}}
                                          </a>
                                      </td>
                                  </tr>
                                  @endforeach
                              </table>
                        </div>
                    </div>
                  </div>
                </div>
            <br>
            <h3 class="font-weight-bold blue-text pb-2">Pending/Approved Requests</h3>
                <div class="card">
                    <div class="card-body">
                        @if (count($solo) < 1)
                            <text class="font-weight-bold">This student has no requests created</text>
                        @else
                            @foreach ($solo as $s)
                                @if ($s->approved == 1)
                                    <li>{{$s->position}} Solo -
                                    <text class="text-success"> Approved</text></li>
                                @else
                                    <li>{{$s->position}} Solo -
                                        <text class="text-danger"> Pending Approval</text></li>
                            @endif
                            @endforeach
                            @endif
                            <a class="btn-sm btn-primary" href="#solorequest" data-toggle="modal" data-target="#solorequest" style="float: right;">Solo Request</a>
                    </div>
                </div>
                <br>
            <h3 class="font-weight-bold blue-text pb-2">CBT Progression</h3>
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <h5>Modules</h5>
                    @if (count($modules) < 1)
                        Student does not have any module history!
                      @else
                        @foreach ($modules as $module)
                              <li>{{$module->cbtmodule->name}}  -
                              @if ($module->started_at == null)
                                   <text class="text-danger">Not Started</text>
                              @elseif ($module->completed_at == null)
                                   <text class="text-primary">In Progress</text>
                              @elseif ($module->completed_at != null)
                                   <text class="text-success">Completed</text>
                              @endif
                                  <a href="{{route('cbt.module.unassign', $module->id)}}">(Unassign)</a></li>
                          @endforeach
                      @endif
                      <br>
                      <a class="btn-sm btn-primary" href="#assignModule" data-toggle="modal" data-target="#assignModule" style="float: left;">Assign Module</a>
                  </div>
                  <div class="col">
                    <h5>Exams</h5>
                      @if (count($openexams) < 1 && count($completedexams) < 1)
                          <text class="font-weight-bold">Student does not have any exam history!</text>
                      @else
                          @foreach ($openexams as $oe)
                              <li>{{$oe->cbtexam->name}} -
                              @if ($oe->started_at != null)
                                  <text class="text-warning">In Progress</text>
                                  @else
                                  Not Started
                                  <a href="{{route('cbt.exam.unassign', $oe->id)}}"> (Unassign)</a>
                                  @endif
                              </li>


                          @endforeach

                          @foreach ($completedexams as $cexams)
                                  <li><a href="{{route('cbt.exam.results', [$cexams->cbtexam->id, $student->id])}}">{{$cexams->cbtexam->name}}</a> -
                                  @if ($cexams->grade >= 80)
                                      <text class="text-success">
                                          {{$cexams->grade}}% (Pass)
                                      </text>
                                  @else
                                      <text class="text-danger">
                                          {{$cexams->grade}}% (Fail)
                                      </text>
                                  @endif
                              </li>
                          @endforeach
                      @endif
                      <br>
                      <a class="btn-sm btn-primary" href="#assignexam" data-toggle="modal" data-target="#assignExam" style="float: right;">Assign Exam</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <h3 class="font-weight-bold blue-text pb-2">Primary Info</h3>
                <div class="card">
                    <div class="card-body">
                        <h5>Training Status</h5>
                        @if ($student->status == 0)
                        <span class="btn btn-sm btn-primary">
                            <h3 class="p-0 m-0">
                                Waitlisted
                            </h3>
                        </span><br></br>
                        The student's training is 'Waitlisted'. This means the student has an accepted application and has not begun training.
                        @elseif ($student->status == 1)
                        <span class="btn btn-sm btn-success">
                            <h3 class="p-0 m-0">
                                In Progress
                            </h3>
                        </span><br></br>
                        The student has an assigned instructor and training is in progress.
                        @elseif ($student->status == 2)
                        <span class="btn btn-sm btn-danger">
                            <h3 class="p-0 m-0">
                                Completed
                            </h3>
                        </span><br></br>
                        The student's training was completed successfully.
                        @else
                        <span class="badge badge-danger">
                            <h3 class="p-0 m-0">
                                Closed
                            </h3>
                        </span><br/>
                        The student's training was closed.
                        @endif
                        <h5 class="mt-3">Assigned Instructor</h5>
                        @if ($student->instructor)
                        <a href="#">
                            {{$student->instructor->user->fullName('FLC')}}
                        </a>
                        @else
                            No instructor assigned
                        @endif
                        <h5 class="mt-3">Application</h5>
                        @if ($student->application != null)
                        Accepted at {{$student->application->processed_at}} by {{\App\Models\Users\User::find($student->application->processed_by)->fullName('FLC')}}
                        @if (Auth::user()->permissions >= 3)
                        <br/>
                        <a href="{{route('training.viewapplication', $student->application->application_id)}}">View application here</a>
                        @endif
                        @endif
                    </div>
                </div>


        <br/>
        <br/>

        <h3 class="font-weight-bold blue-text pb-2">Instructing Sessions</h3>
            <div class="card">
                <div class="card-body">
                    @if (count($student->instructingSessions) >= 1)
                    @else
                    None found!
                    @endif
                </div>
            </div>
            <br><br>

            <h3 class="font-weight-bold blue-text pb-2">Actions</h3>
                <div class="card">
                    <div class="card-body">
                        <h6>Change Status</h6>
                        <form action="{{route('training.students.setstatus', $student->id)}}" method="POST">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col">
                                    <select name="status" required class="custom-select">
                                        <option selected="" value="" hidden>Please choose one..</option>
                                        <option value="1">In Progress</option>
                                        <option value="2">Completed</option>
                                        <option value="0">Waitlist</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <input type="submit" value="Save" class="btn btn-sm btn-success"></input>
                                </div>
                            </div>
                        </form>
                        <br/>
                        <h6>Instructor</h6>
                        <form action="{{route('training.students.assigninstructor', $student->id)}}" method="POST">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col">
                                    <select name="instructor" required class="custom-select">
                                        <option value="" selected="" hidden>Please choose one..</option>
                                        @foreach ($instructors as $instructor)
                                        <option value="{{$instructor->id}}">{{$instructor->user->fullName('FLC')}}</option>
                                        @endforeach
                                        <option value="unassign">No instructor/unassign</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <input type="submit" value="Save" class="btn btn-sm btn-success"></input>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
<br><br>
      </div>
    </div>
  </div>
    <div class="modal fade" id="assignExam" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div align="center" class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign an Exam</h5>
                </div>
                <div class="modal-body">
                <p><i>Note: if re-assigning an exam, old answers and result will be deleted!</i></p>
                    <form method="POST" action="{{route('cbt.exam.assign')}}">
                        <select name="examid" class="custom-select">
                            @foreach ($exams as $e)
                                <option value="{{$e->id}}">{{$e->name}}</option>
                            @endforeach
                        </select>
    @csrf
                </div>
                <input type="hidden" value="{{$student->id}}" name="studentid">
                <div class="modal-footer">
                    <button class="btn btn-success form-control" type="submit" href="#">Assign</button>
                    <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="assignModule" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div align="center" class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign a Module</h5>
                </div>
                <div class="modal-body">

                    <form method="POST" action="{{route('cbt.module.assign')}}">
                        <select name="moduleid" class="custom-select">
                            @foreach ($modules2 as $m)
                                <option value="{{$m->id}}">{{$m->name}}</option>
                            @endforeach
                        </select>
                    @csrf
                </div>
                <input type="hidden" value="{{$student->id}}" name="studentid">
                <div class="modal-footer">
                    <button class="btn btn-success form-control" type="submit" href="#">Assign</button>
                    <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>


    <div class="modal fade" id="solorequest" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div align="center" class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Solo Request</h5>
                    </div>

                <div class="modal-body">
                    <form method="POST" action="{{route('training.solo.request')}}">
                        <p>This will generate a request to the CI for a solo certification.</p>
                            <select class="custom-select" name="position">
                            @if ($student->user->rating_short >= '1')
                                <option value="Delivery">Delivery Solo</option>
                                <option value="Ground">Ground Solo</option>
                                <option value="Tower">Tower Solo</option>
                            @endif
                            @if ($student->user->rating_short  >= '5')
                                <option value="Departure">Departure Solo</option>
                                <option value="Arrival">Arrival Solo</option>
                            @endif
                            @if ($student->user->rating_short  >= '6')
                                <option value="Centre">Centre Solo</option>
                            @endif
                        </select>
                    </form>
                    @csrf
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success form-control" type="submit" href="#">Send Request</button>
                    <button class="btn btn-light" data-dismiss="modal" style="width: 40%">Dismiss</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
{{--
  <div class="modal fade" id="newNote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
       aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">Assign Student to Instructor</h5><br>
              </div>
              <div class="modal-body">
                  <form method="POST" action="{{ route('add.trainingnote') }}" class="form-group">
                      @csrf
                      <label class="form-control">Title</label>
                      <input type="text" name="title" class="form-control"></input>
                          <label class="form-control">Content</label>
                          <textarea name="content" class="form-control"></textarea>
                          <input type="hidden" name="student" value="{{$student->id}}"></input>
                          <input type="submit" class="btn btn-success" value="Add Training Note"></input>
                  </form>
              </div>
              <div class="modal-footer">
                  <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
              </div>
          </div>
        </div>
      </div> --}}
@stop
