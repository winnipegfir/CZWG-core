@extends('layouts.master')

@section('navbarprim')
@section('title', 'Exam Admin')

    @parent

@stop

@section('content')
  @include('includes.cbtMenu')
@if (Auth::check())
    <div class="container" style="margin-top: 20px;">
      <h2 class="font-weight-bold blue-text pb-2">Exam Centre Admin</h2>
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
            @if (count($exams) < 1)
            <text class="font-weight-bold" style="color:red">There are no exams!</b></text>
            @else
            <thead>
                <tr>
                    <th scope="col">Exam</th>
                    <th scope="col">Created on</th>
                    <th scope="col">Created by</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($exams as $exam)
            <tr>
                <th class="align-middle" scope="row">{{$exam->name}}</a></th>
                <td class="align-middle">
                    {{$exam->created_at}}
                </td>
                <td class="align-middle">
                    {{$exam->created_by}}
                </td>
              <td class="pt-3">
              @if (Auth::user()->permissions >=4)
              <div class="btn-toolbar" role="toolbar">
                <div class="btn-group" role="group">
                  <a type="button" class="btn btn-sm btn-primary" href="{{route('cbt.exam.questions', $exam->id)}}" ><i class="fa fa-question-circle"></i> Question Bank</a>
                  <a type="button" class="btn btn-sm btn-primary" style="color: #ff6161" href="{{route('cbt.exam.delete', $exam->id)}}"><i class="fa fa-times"></i> Delete</a>
                </div>
              </div>
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
          <input class="form-group form-control" name="name" id="name" type="text">
          <input class="btn btn-success" type="submit" value="Create Exam">
          @csrf
        </form>
      </div>
    </div>
    <br>
    @endif
    @stop
