@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')

    <div class="container" style="margin-top: 20px;">
        <a href="{{route('staff.feedback.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Feedback</a>
        <h1 class="blue-text font-weight-bold mt-2">Controller Feedback ID: #{{ $id }}</h1>
        <hr>
        <div class="row">
            <div class="col-3">
                <h5>Submitter: {{$submitter->fullName('FL')}} <img src="{{$submitter->avatar()}}" style="height: 27px; width: 27px; margin-right: 7px; margin-bottom: 3px; border-radius: 50%;"></h5>
            </div>
            <div class="col-3">
                <h5>Controller: {{$controller->fullName('FL')}} <img src="{{$controller->avatar()}}" style="height: 27px; width: 27px; margin-right: 7px; margin-bottom: 3px; border-radius: 50%;"></h5>
            </div>
            <div class="col-3">
                <h5>Position: {{$feedback->position}}</h5>
            </div>
            <div class="col-3" style="text-align: center">
                {{$feedback->approval == 0 ? 'Actions (needs your attention):' : 'Actions:'}}
                <br>
                <a href="{{url('/admin/feedback/controller/'.$id.'/approve')}}"><i class="fa fa-check" style={{$feedback->approval == 2 ? 'color:green' : 'color:black'}}></i></a>&nbsp;
                <a href="{{url('/admin/feedback/controller/'.$id.'/deny')}}"><i class="fa fa-times" style={{$feedback->approval == 1 ? 'color:red' : 'color:black'}}></i></a>&nbsp;
                <a href="{{url('/admin/feedback/controller/'.$id.'/delete')}}"><i class="fa fa-trash-alt" style="color: black;"></i></a>
            </div>
        </div>
        <h6 style="white-space: pre-line;">Feedback: {{$feedback->content}}</h6>
        <hr>
        <small>Submitted at: {{$feedback->created_at}}</small>

    </div><br>
@stop
