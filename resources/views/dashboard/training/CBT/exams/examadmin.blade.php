@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
  @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">
      <h2>Exam Centre Admin</h2>
        <!--tabs: Modules, Student Progress, Add Module-->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          @if (Auth::user()->permissions >= 3)
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#modules" role="tab" aria-controls="modules" aria-selected="true">View Exams</a>
            </li>
            @endif
            @if (Auth::check() && Auth::user()->permissions >= 4)
            <li class="nav-item">
                <a class="nav-link" id="addmodule-tab" data-toggle="tab" href="#addmodule" role="tab" aria-controls="addmodule" aria-selected="false">Add an Exam</a>
            </li>
            @endif
          </ul>
          <!--TAB 1 : Exams : viewable by perm level 3 and up-->
          <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="modules" role="tabpanel" aria-labelledby="home-tab"><br>
        <!--List of exams in table format (click to view the module), include name, # of lessons, created by who. Edit and Delete buttons for Staff/Admin-->
        <table id="dataTable" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Exam</th>
                    <th scope="col">Created on</th>
                    <th scope="col">Created by</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            @if (count($exams) < 1)
            <font class="font-weight-bold">** There are no exams!</b></font>
            @else
            <tbody>
            @foreach ($exams as $exam)
            <tr>
                <th scope="row"><a href="#/{{$exam->id}}/intro">{{$exam->name}}</a></th>
                <td>
                    {{$exam->created_at}}
                </td>
                <td>
                    {{$exam->created_by}}
                </td>
                <td>
                  <a href="{{route('cbt.exam.questions', $exam->id)}}">Question Bank</a>
              @if (Auth::user()->permissions >=4)
                 | <a href="#">Delete</a>
              </td>
              @endif

            </tr>
            @endforeach
            @endif
        </table>
      </div>


          <!--TAB 3: Add Exam : Viewable by perm level 4 and up-->
     <div class="tab-pane fade" id="addmodule" role="tabpanel" aria-labelledby="addmodule-tab"><br>
        <!--Form for creating a new Exam-->
        <form action="{{route('cbt.exam.add')}}" method="POST">
          <label class="form-group">Name of Exam</label><br>
          <input name="name" id="name" class="form-group" type="text"><br>
          <input type="submit" value="Create Exam">
          @csrf
        </form>



    </div>
    <br><br><br><br>
    @stop
