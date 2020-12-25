@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
  @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">
      <h2>Viewing {{$exam->name}} Exam</h2>
        <!--tabs: Modules, Student Progress, Add Module-->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          @if (Auth::user()->permissions >= 3)
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#modules" role="tab" aria-controls="modules" aria-selected="true">Overview</a>
            </li>
            @endif
            @if (Auth::check() && Auth::user()->permissions >= 4)
            <li class="nav-item">
                <a class="nav-link" id="addmodule-tab" data-toggle="tab" href="#addmodule" role="tab" aria-controls="addmodule" aria-selected="false">Question Bank</a>
            </li>
            @endif
          </ul>
          <!--TAB 1 : Exams : viewable by perm level 3 and up-->
          <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="modules" role="tabpanel" aria-labelledby="home-tab"><br>
        EXAM PAGE OVERVIEW
</div>

          <!--TAB 3: Add Exam : Viewable by perm level 4 and up-->
     <div class="tab-pane fade" id="addmodule" role="tabpanel" aria-labelledby="addmodule-tab"><br>
        <!--Form for creating a new Exam-->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="modules" role="tabpanel" aria-labelledby="home-tab"><br>
      <!--List of exams in table format (click to view the module), include name, # of lessons, created by who. Edit and Delete buttons for Staff/Admin-->
      <table id="dataTable" class="table table-hover">
          <thead>
              <tr>
                  <th scope="col">Question</th>
                  @if (Auth::user()->permissions >= 4)
                  <th scope="col">Actions</th>
                  @endif
              </tr>
          </thead>
          @if (count($questions) < 1)
          <font class="font-weight-bold">** There are no questions!</b></font>
          @else
          <tbody>
          @foreach ($questions as $q)
          <tr>
              <th scope="row"><a href="#/{{$exam->id}}/intro">{{$q->question}}</a></th>
              
              @if (Auth::user()->permissions >=4)
              <td>
                <a href="#">Edit</a>
               | <a href="#">Delete</a>
            </td>
            @endif

          </tr>
          @endforeach
          @endif
      </table>
    </div>
    <br><br><br><br>
    @stop
