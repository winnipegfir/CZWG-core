@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
  @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">
      <h1 class="font-weight-bold blue-text">Training Centre</h1>
        <p>Welcome to Winnipeg FIR's CBT brand new training system - 1Winnipeg.</p>
      <hr>
        <h5>Please select from one of the options below:<h5>
        <a class="btn btn-primary" href="{{route('cbt.module')}}">Modules</a>
        <a class="btn btn-primary" href="{{route('cbt.exam')}}">Exam Centre</a>
    </div>
    <br>
@stop
