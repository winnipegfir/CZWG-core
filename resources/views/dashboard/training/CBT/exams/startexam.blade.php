@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')

    <div class="container" style="margin-top: 20px;">
      <h1>Examination Centre</h1>

      <div class="jumbotron">
              <h3>Subject Name: <b>{{$subject->name}}</b></h3>
              <h3>Your Instructor: <b>(Code relationship to grab instructor)</b></h3>

              <a class="btn btn-success btn-lg" href="{{route('cbt.exam.start',$subject->id)}}" role="button">START EXAM</a>
          </div>
@stop
