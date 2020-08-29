@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')

    <div class="container" style="margin-top: 20px;">
        <a href="{{route('staff.feedback.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Feedback</a>
        <h1 class="blue-text font-weight-bold mt-2">Controller Feedback ID: #{{ $id }}</h1>
        <hr>
        <h4>Submitter: {{$submitter}} <img src="{{$submitterAvatar}}" style="height: 27px; width: 27px; margin-right: 7px; margin-bottom: 3px; border-radius: 50%;"></img>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Controller: {{$controller}} <img src="{{$controllerAvatar}}" style="height: 27px; width: 27px; margin-right: 7px; margin-bottom: 3px; border-radius: 50%;"></img>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Position: {{$position}}</h4>
        <h5 style="white-space: pre-line;">Feedback: {{$content}}</h5>
        <small>Submitted at: {{$submitted}}</small>

    </div>
@stop
