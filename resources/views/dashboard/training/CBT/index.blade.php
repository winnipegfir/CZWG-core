@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
  @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">
      <h1 class="font-weight-bold blue-text">Winnipeg's Computer Based Training</h1>
      Welcome to Winnipeg FIR's CBT system. This is a training platform used for new and current controllers to learn specific things about Winnipeg <figure> 
      and there will be examinations afterwards.
    </div>
    <br>
@stop
