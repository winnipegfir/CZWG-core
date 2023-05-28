@extends('layouts.master')
@section('title', 'Training')

@section('content')

    <div class="container pt-2 pb-0">
        <h1 class="blue-text font-weight-bold">Training</h1>
    </div>
    <div class="row w-100" style="background-color: {{$training_time->colour}};{{$training_time->colour=='yellow' ? 'background-color: #feba00; color: black':'color: white'}}">
        <div class="container mb-2 mt-3 text-center">
            <h2 class="font-weight-bold mb-0">Current wait time for new students:</h2>
            <h5>{{$training_time->wait_length}}</h5>
            @if(Auth::check() && Auth::user()->permissions >= 4)
            <h3 class="ml-0 btn btn-sm btn-primary" data-toggle="modal" data-target="#waitEdit">Wait Time Editor</h3>
            @endif
        </div>
    </div>
    
    <div class="container" style="margin-top: 20px;">
        <div>
            <h3 class="font-weight-bold blue-text">Online Training</h3>
                <p>Over the past year, the Winnipeg FIR has been busy working on building a brand new training system from scratch. Once completed, Winnipeg students
                will have the ability to study for their ratings without the need for Instructors or Mentors - and can request help from their Instructor with the click of a button.
                It also includes a brand new, state-of-the-art Computer-Based Training System.</p>
                <h5 class="font-weight-bold blue-text">All In One Place.</h5>
                <p>The Winnipeg FIR has the benefit for students and instructors/mentors of all being in one place - always a click away on the menu.</p>
                <p>For updates on the our new training & CBT system, keep an eye on the <a href="https://blog.winnipegfir.ca">Winnipeg FIR Blog</a> where we post live updates on the status of this project.
            </div>
        <hr>
        <div>
            <h3 class="font-weight-bold blue-text">Waiting for Visiting Training?</h3>
                <p>Visiting controllers are provided training based on instructor availability, similar to standard home controllers and students. However, Winnipeg's home controllers always hold priority, so we ask for (and appreciate) your patience during what can sometimes be a lengthy waiting period.</p>
        </div>
        <hr>
        <div>
            <h3 class="font-weight-bold blue-text">Interested In Joining Winnipeg?</h3>
                <p>Come join the community of students, controllers and instructors in Winnipeg today by clicking <a href="{{url('/join')}}">HERE.</a></p>
        </div>
        <hr>
            <p>Questions? <a href="{{route('staff')}}">Contact our Chief Instructor!</a></p>
    </div>

@if(Auth::check() && Auth::user()->permissions >= 4)

<!-- Start Time Editor modal -->
    <div class="modal fade" id="waitEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Colour/Time Editor</h5>
                </div>
                <form method="POST" action="{{route('waittime.edit')}}">
                    @csrf
                    <div class="modal-body">
                        <h5 class="font-weight-bold blue-text mb-0">Wait Time:</h5>
                            <input name="waitTime" class="form-control" value="{{$training_time->wait_length}}" placeholder="1 Week">
                        <br>
                        <h5 class="font-weight-bold blue-text mb-0">Colour:</h5>
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
