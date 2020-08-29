@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
  @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">
      <div align="center"><h4>Viewing {{$module->name}} Module</h4><br>
      <div class="row">
        <div class="col-md-2">
          @include('includes.moduleMenu')
        </div>
        <div class="col-md-1">
        </div>
    <div class="col-md-3">
      <div class="card">
      <p>
        <b>Students - Assigned</b><br><br>
        @if (count($assignedstudents) < 1)
        <b>No students currently assigned!</b>
        @else
        @foreach ($assignedstudents as $astudents)
          {{$astudents->student->user->fullName('FLC')}}<br>
        @endforeach
        @endif
    </div></p>
    <br>
    <div class="card">
      <p>
        <b>Students - Completed</b><br><br>
        @if (count($completedstudents) < 1)
          <b>No students completed yet!</b>
        @else
        @foreach ($completedstudents as $cstudents)
          {{$cstudents->student->user->fullName('FLC')}}<br>
        @endforeach
        @endif

      </div></p>
</div>
    <div class="col-md-3">
      <div class="card">
        <b>Actions</b><br>
        <li>
          <a href="#">Assign Student</a>
        </li>
        <li>
          <a href="../../module/view/{{$module->id}}/intro">
            View Module</a>
        </li>
        @if (Auth::user()->permissions >= 4)
        <li>
          <a href="#">Edit Module</a>
        </li>
        <li>
          <a href="#">Delete Module</a>
        </li>
        @endif


    </div>
</div></div>
<br><br><br><br>
@stop
