@extends('layouts.master')

@section('title', __('Server Error'))

@section('content')
    <div class="container py-5">
        <h1 class="font-weight-bold"><i class="fa fa-cogs"></i>  Uh oh... (500 Server Error)</h1>
        <div class="mt-4">
            <h3>It appears our server didn't like that very much...</h3>
            <h5>Please submit a ticket to our <a href="{{url('/dashboard/tickets?create=yes')}}">webmaster</a> with:</h5>
            <ul class="h5">
                <li>What you were trying to access</li>
                <li>What URL you were on</li>
                <li>Any other relevent information</li>
            </ul>
        </div>
    </div>
@stop
