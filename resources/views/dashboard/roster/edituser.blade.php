@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'Edit User')
@section('description', "Winnipeg FIR's Controller Roster")

@section('content')

<div class="container" style="margin-top: 20px;">
    <a href="{{route('roster.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Roster</a>
<br>
<head>
<style>
* {
  box-sizing: border-box;
}

/* Create two equal columns that floats next to each other */
.column {
  float: left;
  width: 50%;
  padding: 10px;
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
</style>
</head>

<div align="center">
<form method="post" action="{{route('roster.editcontroller', [$cid]) }}"<br>
  <form class="form-horizontal">
<fieldset>

<!-- Form Name -->
<legend>Edit Controller on Roster</legend>


<div class="form-group">
  <label>Controller CID:</label><br>
  {{$roster->full_name." ".$cid}}<br><br>

  <!--Delivery-->

    <input type="hidden" name="cid" value="{{ $cid }}">
</div>
<div class="form-row">
  <div class="col-md-3">
  </div>
<div class="form-group col-md-2">
  <div align="center">
  <label class="control-label" for="del">Delivery</label>
    <select name="del" class="form-control">
      <option value="1"{{ $roster->del == "1" ? "selected=selected" : ""}}>Not Certified</option>
      <option value="2"{{ $roster->del == "2" ? "selected=selected" : ""}}>Training</option>
      <option value="3"{{ $roster->del == "3" ? "selected=selected" : ""}}>Solo</option>
      <option value="4"{{ $roster->del == "4" ? "selected=selected" : ""}}>Certified</option>
    </select>
</div>
</div>


<!-- Ground -->
<div class="form-group col-md-2">
  <label class="control-label" for="gnd">Ground</label>

    <select name="gnd" class="form-control">
      <option value="1"{{ $roster->gnd == "1" ? "selected=selected" : ""}}>Not Certified</option>
      <option value="2"{{ $roster->gnd == "2" ? "selected=selected" : ""}}>Training</option>
      <option value="3"{{ $roster->gnd == "3" ? "selected=selected" : ""}}>Solo</option>
      <option value="4"{{ $roster->gnd == "4" ? "selected=selected" : ""}}>Certified</option>
    </select>
</div>


<!-- Tower -->

<div class="form-group col-md-2">
  <label class="control-label" for="twr">Tower</label>
    <select name="twr" class="form-control">
      <option value="1"{{ $roster->twr == "1" ? "selected=selected" : ""}}>Not Certified</option>
      <option value="2"{{ $roster->twr == "2" ? "selected=selected" : ""}}>Training</option>
      <option value="3"{{ $roster->twr == "3" ? "selected=selected" : ""}}>Solo</option>
      <option value="4"{{ $roster->twr == "4" ? "selected=selected" : ""}}>Certified</option>
    </select>

</div>
</div>

<br><br>

<!-- Departure -->
<div class="form-row">
  <div class="col-md-3">
  </div>
<div class="form-group col-md-2">
  <label class="control-label" for="dep">Departure</label>
    <select name="dep" class="form-control">
      <option value="1"{{ $roster->dep == "1" ? "selected=selected" : ""}}>Not Certified</option>
      <option value="2"{{ $roster->dep == "2" ? "selected=selected" : ""}}>Training</option>
      <option value="3"{{ $roster->dep == "3" ? "selected=selected" : ""}}>Solo</option>
      <option value="4"{{ $roster->dep == "4" ? "selected=selected" : ""}}>Certified</option>
    </select>
  </div>

<br><br>
<!-- Approach -->
<div class="form-group col-md-2">
  <label class="control-label" for="app">Arrival</label>
    <select name="app" class="form-control">
      <option value="1"{{ $roster->app == "1" ? "selected=selected" : ""}}>Not Certified</option>
      <option value="2"{{ $roster->app == "2" ? "selected=selected" : ""}}>Training</option>
      <option value="3"{{ $roster->app == "3" ? "selected=selected" : ""}}>Solo</option>
      <option value="4"{{ $roster->app == "4" ? "selected=selected" : ""}}>Certified</option>
    </select>
  </div>

<br><br>
<!-- Center -->
<div class="form-group col-md-2">
  <label class="control-label" for="ctr">Centre</label>
    <select name="ctr" class="form-control">
      <option value="1"{{ $roster->ctr == "1" ? "selected=selected" : ""}}>Not Certified</option>
      <option value="2"{{ $roster->ctr == "2" ? "selected=selected" : ""}}>Training</option>
      <option value="3"{{ $roster->ctr == "3" ? "selected=selected" : ""}}>Solo</option>
      <option value="4"{{ $roster->ctr == "4" ? "selected=selected" : ""}}>Certified</option>

    </select>
  </div>
</div>


<!--Remarks-->
<div class="form-group">
  <label class="control-label" for="remarks">Remarks</label><br>
  <textarea name="remarks" rows="1" cols="5" class="form-control">{{ $roster->remarks }}
  </textarea>
</div>



  <!--Active Status-->
    <div class="form-row">
        <div class="col-md-4">
        </div>
        <div class="form-group col-md-2">
            <label class="control-label" for"active">Active</label><br>
            <select name="active" class="form-control" style="width:75px">
                <option value="1"{{ $roster->active == "1" ? "selected=selected" : ""}}>Active</option>
                <option value="0"{{ $roster->active == "0" ? "selected=selected" : ""}}>Not Active</option>
            </select>
        </div>
        <!-- Rating Hours-->
        <div class="form-group col-md-2">
            <label class="control-label" for "rating_hours">Reset rating hours?</label><br>
            <select style="width:75px" name="rating_hours" class="form-control">
                <option value="false">No</option>
                <option value="true">Yes</option>
            </select>
        </div>
    </div>
@csrf
<!-- Button -->
    <div class="form-group">
        <label class="col-md-4 control-label" for="submit"></label>
        <div class="col-md-4">
            <button name="submit" class="btn btn-success">Submit</button>
        </div>
    </div>
    </fieldset>
  </form>
</div>

@stop
