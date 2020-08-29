@extends('layouts.master')

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
      


      Please select a module from the side-menu.

</div></div>
<br><br><br><br>
@stop
