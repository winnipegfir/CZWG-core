@extends('layouts.master')
@section('title', 'Your Modules')
@section('navbarprim')

    @parent

@stop

@section('content')
  @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">
      <div class="row">
        <div class="col-md-2">
          @include('includes.moduleMenu')
        </div>
    <div class="col-md-10">
      <!--Admin Functions-->
      


      Please select a module from the side-menu. A <i style="color: green" class="fas fa-check"></i> indicates you have previously completed the module.

</div></div>
<br><br><br><br>
@stop
