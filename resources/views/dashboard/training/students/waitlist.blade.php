@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 20px;">
        <h1>Waitlist <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newStudent" style="float: right;">Add to Waitlist</button></h1>

        <hr>
        <table id="dataTable" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">CID</th>
                    <th scope="col">Student Name</th>
                    <th scope="col">Date of Application</th>
                    <th scope="col">Entry</th>
                    <th scope="col">Email</th>
                </tr>
            </thead>
            @if (count($students) < 1)
            <font class="font-weight-bold">** There are no students in the waitlist!</b></font>
            @else
            <tbody>
            @foreach ($students as $student)
            <tr>
                <th scope="row">{{$student->user->id}}</th>
                <td>
                    <a href="{{route('training.students.view', $student->id)}}">
                        {{$student->user->fullName('FL')}}
                    </a>
                </td>
                <td>
                    {{$student->application->processed_at}}
                </td>
                <td>
                  {{$student->entry_type}}
                </td>
                <td>
                  @if (Auth::user()->permissions >= 4)
                  {{$student->user->email}}
                  @else
                  <i>Hidden for Privacy</i>
                  @endif
                </td>
            </tr>
            @endforeach
            @endif
        </table>
    </div>

    <div class="modal fade" id="newStudent" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Student to Waitlist</h5><br>
                    <h4>Note: This will create an Application and approve it for this student using your CID.</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('instructor.student.add.new')}}" class="form-group">
                        @csrf
                        <label class="form-control">Choose a Student</label>
                        <select name="student_id" id-"student_id" class="form-control">
                        @foreach ($potentialstudent as $u)
                            <option value="{{$u->id}}">{{$u->id}} - {{$u->fullName('FL')}}</option>
                            @endforeach
                            </select>
                            <input type="hidden" name="instructor" id="instructor" value="unassign">
                            </select>


                </div>
                <div class="modal-footer">
                    <button class="btn btn-success form-control" type="submit" href="#">Add Student</button>
                    <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
                  </form>
                </div>
            </div>
          </div>
        </div><br>
@stop
