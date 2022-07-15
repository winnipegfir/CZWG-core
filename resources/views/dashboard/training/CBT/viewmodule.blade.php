@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
  @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">
      <div class="row">
        <div class="col-md-2">
          @include('includes.moduleMenu2')
        </div>

    <div class="col-md-10">
        <div align="center">
          <h2 class="font-weight-bold blue-text"> {{$currentlesson->name}}</h2>
        </div>
        <br>


      {{$currentlesson->html()}}

      <br><br>

    @if($currentlesson->lesson == 'conclusion')
        <a class="btn btn-success" href="{{route('cbt.module.complete', $currentlesson->cbt_modules_id)}}">Click To Mark Module As Completed!</a>
    @endif


  </div>
</div>
<br>
<br>
@stop
