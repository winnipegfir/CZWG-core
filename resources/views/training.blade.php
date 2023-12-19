@extends('layouts.master')
@section('title', 'Training')

@section('content')

    <div class="container pt-2 pb-0">
        <h1 class="blue-text font-weight-bold">Controller Training</h1>
    </div>
    <div class="row w-100" style="background-color: {{$training_time->colour}};{{$training_time->colour=='yellow' ? 'background-color: #feba00; color: black':'color: white'}}">
        <div class="container mb-2 mt-3 text-center">
            <h2 class="font-weight-bold mb-0">Current Estimated Instructor Linking Wait time:</h2>
            <h5>{{$training_time->wait_length}}</h5>
            @if(Auth::check() && Auth::user()->permissions >= 4)
            <h3 class="ml-0 btn btn-sm btn-primary" data-toggle="modal" data-target="#waitEdit">Wait Time Editor</h3>
            @endif
        </div>
    </div>
    
    <div class="container" style="margin-top: 20px;">
        <div>
            <h3 class="font-weight-bold blue-text">Training, on Your Schedule.</h3>
                <p>For many years, VATSIM controllers-to-be have had the same pathway to getting on the scope - join their FIR of choice, then wait for an instructor to become available. This causes bottlenecks, and we wanted to find a way to fix that. That's when the Winnipeg team created <a href="https://training.winnipegfir.ca">Winnipeg365</a> - a brand new, state of the art and robust online training platform, built for both instructors <i>and</i> students.
                The best feature of Winnipeg365? As a new student, you don't have to sit around and wait.</p>
                <p>Once you are accepted into the FIR (typically only taking a few days), you will automatically be enrolled into the system - and will be eligible to get right into the fundamentals. From the laws of aviation, to taxiing and aircraft for the first time, Winnipeg365 has it all. And while you'll still have to wait for an instructor before heading onto the network to control, this platform speeds that up for everyone - and we all love when things are done quickly and properly!</p>
        </div>
        <hr>
        <div>
            <h3 class="font-weight-bold blue-text">Interested In Joining Winnipeg?</h3>
                <p>Come join the community of students, controllers and instructors in Winnipeg today by clicking <a href="{{url('/join')}}">here.</a></p>
        </div>
        <hr>
        <div>
            <h4 class="font-weight-bold blue-text">Looking to Visit?</h4>
                <p>Visiting controllers are provided training based on instructor availability, similar to standard home controllers and students. However, Winnipeg's home controllers always hold priority, so we ask for (and appreciate) your patience during what can sometimes be a lengthy waiting period. That said, we're always happy to welcome a new face to our team - even if you're not a Winnipeg home controller! Head <a href="{{url('/join')}}">here</a> for more info.</p>
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
