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
                <a href="#" data-toggle="modal" data-target="#editModal" style="color: black"><i class="fa fa-pencil-alt"></i></a>&nbsp;
                <a href="{{url('/admin/feedback/controller/'.$id.'/approve')}}"><i class="fa fa-check" style={{$feedback->approval == 2 ? 'color:green' : 'color:black'}}></i></a>&nbsp;
                <a href="{{url('/admin/feedback/controller/'.$id.'/deny')}}"><i class="fa fa-times" style={{$feedback->approval == 1 ? 'color:red' : 'color:black'}}></i></a>&nbsp;
                <a href="{{url('/admin/feedback/controller/'.$id.'/delete')}}"><i class="fa fa-trash-alt" style="color: black;"></i></a>
            </div>
        </div>
        <h6 style="white-space: pre-line;">Feedback: {{$feedback->content}}</h6>
        <hr>
        <small>Submitted at: {{$feedback->created_at}}</small>

    </div><br>

    <!-- Start Edit Feedback Modal-->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Feedback ID: #{{ $id }}</h5>
                </div>
                <form method="POST" action="{{route('staff.feedback.controller.edit', $id)}}">
                    @csrf
                    <div class="modal-body">
                        <h5 class="font-weight-bold" style="color: red;">Editing controller feedback should only be used for clarity reasons.</h5>
                        <br>
                        <p class="font-weight-bold">Feedback:</p>
                        <textarea name="content" class="form-control" placeholder="What a fantastic controller!">{{$feedback->content}}</textarea>
                    </div>
                    <div class="modal-footer">
                        <input role="button" type="submit" class="btn btn-primary" value="Edit">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Edit Feedback Modal-->
@stop
