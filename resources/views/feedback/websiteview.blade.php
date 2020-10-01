@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')

    <div class="container" style="margin-top: 20px;">
        <a href="{{route('staff.feedback.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Feedback</a>
        <h1 class="blue-text font-weight-bold mt-2">{{$feedback->subject}} - Feedback ID: #{{ $id }}</h1>
        <hr>
        <div class="row">
            <div class="col-10">
                <h5>Submitter: {{$submitter->fullName('FL')}} <img src="{{$submitter->avatar()}}" style="height: 27px; width: 27px; margin-right: 7px; margin-bottom: 3px; border-radius: 50%;"></h5>
            </div>
            <div class="col-2" style="text-align: center">
                Actions:
                <br>
                <a href="{{url('/admin/feedback/website/'.$id.'/delete')}}"><i class="fa fa-trash-alt" style="color: black;"></i></a>
            </div>
        </div>
        <h6 style="white-space: pre-line;">Feedback: {{$feedback->content}}</h6>
        <hr>
        <small>Submitted at: {{$feedback->created_at}}</small>

    </div><br>
@stop
