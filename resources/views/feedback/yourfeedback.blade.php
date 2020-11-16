@extends('layouts.master')
@section('title', 'Your Feedback - Winnipeg FIR')
@section('description', 'View the feedback that the VATSIM community has submitted to us')
@section('content')

<div class="container py-4">
    <h1 class="font-weight-bold blue-text">Your Feedback</h1>
    <h5>This is just some of the great feedback we've received from the thousands of VATSIM users that have flown through Winnipeg!</h5>
    <hr>
    @foreach($feedback as $f)
        <div class="card">
            <div class="card-body" style="background-color:#013162; color:#ffffff;">
                <p>"{{$f->content}}"</p>
                <h5><strong>Controller: </strong>{{User::where('id', $f->controller_cid)->first()->fullName('FL')}}</h5>
            </div>
        </div>
        <br>
    @endforeach
    <p>Here at Winnipeg, feedback is something we have always valued. Your suggestions, criticisms and hints make us better controllers! If you haven't yet sent us some feedback about a recent experience you've had on the network, you can do so <a href = "/feedback">HERE</a>.
</div>
@endsection
