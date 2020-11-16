@extends('layouts.master')
@section('title', 'Training')

@section('content')

    <div class="container" style="margin-top: 20px;">
        <h1 class="blue-text font-weight-bold">Training</h1>
            <p>Winnipeg always holds training to the highest standards. As a Winnipeg FIR student, the training we provide is always extremely professional and precise.</p>
        <hr>
        <div>
            <h3>The 1Winnipeg System</h3>
                <p>Over the past year, the Winnipeg FIR has been busy working on building a brand new training system from scratch - the 1Winnipeg. Once completed, Winnipeg students
                will have the ability to study for their ratings without the need for Instructors or Mentors - and can request help from their Instructor with the click of a button.
                It also includes a brand new, state-of-the-art Computer-Based Training System.</p>
                <p>For updates on the 1Winnipeg Training & CBT System, keep an eye on the <a href="https://blog.winnipegfir.ca">Winnipeg FIR Blog</a> where we post live updates on the status of this project.
            </div>
        <hr>
        <h3>Training Wait Time</h3>
        <div class="row" style="padding-left:8px">
            <h3 class="btn btn-{{$training_time->colour}}" style="color:white" data-toggle="modal" data-target="#waitTime"><b>Estimated Wait Time:</b> {{$training_time->wait_length}}</h3>
                <div class="row" style="padding-left:8px">
                    <h3 class="btn btn-primary" style="color:white" data-toggle="modal" data-target="#waitList"><b>Students On Waitlist:</b> {{count($waitlist)}}</h3>
                    @if(Auth::check() && Auth::user()->permissions >= 4)
                    <h3 class="btn btn-primary" data-toggle="modal" data-target="#waitEdit">Wait Time Editor</h3>
                    @endif
                </div>
        </div>
        <hr>
        <div>
            <h3>Sound Like a Deal?</h3>
                <p>Come join the community of students, controllers and instructors in Winnipeg today by clicking <a href="{{url('/join')}}">HERE.</a></p>
        </div>
        <hr>
            <p>Questions? <a href="{{route('staff')}}">Contact our Chief Instructor!</a></p>
    </div>

<!-- Start Waitlist modal -->
    <div class="modal fade" id="waitList" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Our Waitlist</h5>
                </div>
                    <div class="modal-body">
                    Our waitlist updates live whenever any students are added to our wait list. Check back here for updates on what our wait time, and our wait list is like!.
                    </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
<!-- End Waitlist modal -->

<!-- Start Wait Time modal -->
<div class="modal fade" id="waitTime" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">The Wait Time Calculation</h5>
                </div>
                    <div class="modal-body">
                    Our wait time tracker is calculated based on the FIR's current Instructor and Mentor numbers, as well as the amount of students in training and awaiting training.
                    </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
<!-- End Wait Time modal -->

@if(Auth::check() && Auth::user()->permissions >= 4)

<!-- Start Time Editor modal -->
    <div class="modal fade" id="waitEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Wait Time Editor</h5>
                </div>
                <form method="POST" action="{{route('waittime.edit')}}">
                    @csrf
                    <div class="modal-body">
                        <h4>Wait Time:</h4>
                            <input name="waitTime" class="form-control" value="{{$training_time->wait_length}}" placeholder="1 Week">
                        <br>
                        <h4>Colour:</h4>
                        <select name="trainingTimeColour" id="trainingTimeColourSelect" class="form-control">
                            <option value="green" class="btn-green" {{$training_time->colour == 'green' ? 'selected=selected' : ''}}>Green</option>
                            <option value="yellow" class="btn-yellow" {{$training_time->colour == 'yellow' ? 'selected=selected' : ''}}>Yellow</option>
                            <option value="red" class="btn-red" {{$training_time->colour == 'red' ? 'selected=selected' : ''}}>Red</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Edit</button>
                        <button class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- End Time Editor modal -->

@endif
@endsection