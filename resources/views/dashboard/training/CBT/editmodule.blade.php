<?php
    $i = 1
        ?>
@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.cbtMenu')
    @if(Auth::check())
        <div class="container" style="margin-top: 20px;">
            <h2 class="font-weight-bold blue-text">Editing {{$module->name}}:
                @if (Auth::user()->permissions >= 4)
                <hr>
                    <button type="button" class="btn btn-sm btn-grey" data-toggle="modal" data-target="#moduleDetails" style="float: right;">Edit Module Details</button>
                    <a href="{{route('cbt.module.assignall', $module->id)}}" class="btn btn-success btn-sm" style="float: right;">Assign to ALL Students</a>
                    <a href="{{route('cbt.module.unassignall', $module->id)}}" class="btn btn-danger btn-sm" style="float: right;">Unassign ALL Students</a><br>
            </h2>
                @endif
            <br>
            <div align="center">
                    <div class="card p-3">
                        <h4 style="margin-bottom:0%" class="font-weight-bold">Introduction - {{$intro->name}}</h4><p>{{$intro->html()}}</p>
                            @if (Auth::user()->permissions >= 4)
                            <div class="col" style="padding-bottom: 3.5%">
                                <a href="{{route('cbt.lesson.edit', $intro->id)}}" class="btn btn-primary btn-sm" style="width: 75px;">Edit</a>
                            </div>
                            @endif
                    </div>
                <br><hr><br>
                        @foreach ($lessons as $l)
                    <div class="card p-3">
                        <h4 style="margin-bottom:0%" class="font-weight-bold">Lesson {{$i}} - {{$l->name}}</h4>
                            <p>{{$l->html()}}</p>
                            @if (Auth::user()->permissions >= 4)
                            <div class="col" style="padding-bottom: 3.5%">
                                <a href="{{route('cbt.lesson.edit', $l->id)}}" class="btn btn-primary btn-sm" style="width: 75px;">Edit</a>
                                <a href="{{route('cbt.lesson.delete', $l->id)}}" class="btn btn-danger btn-sm" style="width: 100px;">Delete</a>
                            </div>
                            @endif
                        <?php
                        $i++
                        ?>
                    </div>
                <br><br>
                    @endforeach
                <form action="{{route('cbt.lesson.add', $module->id)}}" method="POST">
                    <input type="hidden" name="lesson" value="lesson{{$i}}">
                    <button type="submit" class="btn btn-success" style="float: center;">Add Lesson #{{$i}}</button>
                    @csrf
                </form>
                <br><hr><br>
                <div class="card p-3">
                    <h4 style="margin-bottom:0%" class="font-weight-bold">Conclusion - {{$conclusion->name}}</h4><p>{{$conclusion->html()}}</p>
                        @if (Auth::user()->permissions >= 4)
                        <div class="col" style="padding-bottom: 3.5%">
                            <a href="{{route('cbt.lesson.edit', $conclusion->id)}}" class="btn btn-primary btn-sm" style="width: 75px;">Edit</a>
                        </div>
                        @endif
                </div>
                @endif
            </div>
        <br>
        </div>
                    {{$i}}
        <div class="modal fade" id="moduleDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Module Details</h5><br>
                    </div>
                    <form method="POST" action="{{route('cbt.edit.moduledetails', $module->id)}}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Module Name</label>
                                <input class="form-control" name="name" value="{{$module->name}}"></input>
                            </div>
                            <div class="form-group">
                                <label>Exam Assign</label>
                                <select class="form-control" name="exam">
                                    <option value="0" {{ $module->cbt_exam_id == NULL ? "selected=selected" : ""}}>No Exam</option>
                                    @foreach ($exam as $exam)
                                        <option value="{{$exam->id}}" {{ $module->cbt_exam_id == $exam->id ? "selected=selected" : ""}}>{{$exam->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success form-control" type="submit" href="#" style="width:60%">Save Changes</button>
                            <button class="btn btn-light" data-dismiss="modal" style="width:40%">Dismiss</button>
                        </div>
                    </form>
                </div>
            </div>

    @stop

