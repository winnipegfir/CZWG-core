@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')

    <div class="container" style="margin-top: 20px;">
    <a href="{{route('cbt.exam')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Back to Exam Centre</a>
    <br></br>
        <div class="jumbotron" style="padding-bottom: 2%; padding-top: 3%">
            <h3 class="font-weight-bold blue-text">{{$subject->name}}</h3>
            <h5><text class="font-weight-bold">Your Instructor:</text> {{$student->instructor->user->fullName('FL')}}</h5>
            <p>The passing mark for this exam is <text class="font-weight-bold text-success">80%.</text> Good luck!</p>
    <br>
            <a style="margin-left: 0%" class="btn btn-success btn-lg" href="{{route('cbt.exam.start',$subject->id)}}" role="button">Start Exam</a>
        </div>
    </div>
@stop
