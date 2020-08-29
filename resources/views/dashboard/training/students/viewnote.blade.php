@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')

    <div class="container" style="margin-top: 20px;">
      {{$note->title}}<br>
      {{$note->content}}<br>
      {{$note->instructor->user->fullName('FLC')}}<br>
      <br>
      @stop
