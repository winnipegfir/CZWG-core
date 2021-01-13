@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')
    <head>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"; rel="stylesheet" />

        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js";></script>
    </head>
    <div class="container" style="margin-top: 20px;">
        <h1 class="font-weight-bold blue-text">Students <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newStudent" style="float: right;">Add New Student</button></h1>

        <hr>
        <table id="dataTable" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">CID</th>
                    <th scope="col">Student Name</th>
                    <th scope="col">Instructor Name</th>
                </tr>
            </thead>
            @if (count($students) < 1)
            <font class="font-weight-bold">There are no students in this category!</b></font>
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
                    @if ($student->instructor !== null)
                        <a href="#">
                            {{$student->instructor->user->fullName('FLC')}}
                        </a>
                    @else
                        No instructor assigned
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
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign Student to Instructor</h5><br>
                    <h4>Note: This will create an Application and approve it for this student using your CID.</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('instructor.student.add.new')}}" class="form-group">
                        @csrf
                        <label class="form-control">Choose a Student</label>
                        <select name="student_id" id-"student_id" class="js-example-basic-single form-control" style="width:470px;">
                        @foreach ($potentialstudent as $u)
                            <option value="{{$u->id}}">{{$u->id}} - {{$u->fullName('FL')}}</option>
                            @endforeach
                            </select>
                            <label class="form-control">Choose an Instructor</label>
                            <select name="instructor" id="instructor" class="form-control">
                                @foreach ($instructors as $i)
                                    <option value="{{$i->id}}">{{$i->user->id}} - {{$i->user->fullName('FL')}}</option>
                                @endforeach
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
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
@stop
