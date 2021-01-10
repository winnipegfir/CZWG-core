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
            <h2 class="font-weight-bold blue-text">{{$module->name}} Administration
                @if (Auth::user()->permissions >= 4)
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#moduleDetails" style="float: right;">Edit Module Details</button>
                    <a href="{{route('cbt.module.assignall', $module->id)}}" class="btn btn-primary btn-sm" style="float: right;">Assign to ALL Students</a>
                    <a href="{{route('cbt.module.unassignall', $module->id)}}" class="btn btn-danger btn-sm" style="float: right;">Unassign ALL Students</a>
            </h2>
                @endif
            <br>
            <div align="center">
                <h4><text class="font-weight-bold">It is very important that you delete the lesson that is before the conclusion ONLY. If not, server errors will occur when viewing the module!</text></h4>

                    <div class="card p-3">
                        <h4>Introduction - {{$intro->name}}</h4><p>{{$intro->html()}}</p>
                            @if (Auth::user()->permissions >= 4)
                                <a href="{{route('cbt.lesson.edit', $intro->id)}}" class="btn btn-primary btn-sm" style="float: right;">Edit</a>
                            @endif
                    </div>
                <hr>
                        @foreach ($lessons as $l)
                    <div class="card p-3">
                            <h4>Lesson {{$i}} - {{$l->name}}</h4>
                            <p>{{$l->html()}}</p>
                            @if (Auth::user()->permissions >= 4)
                                <a href="{{route('cbt.lesson.edit', $l->id)}}" class="btn btn-primary btn-sm" style="float: right;">Edit</a>
                                <a href="#" class="btn btn-danger btn-sm" style="float: right;">Delete</a>
                            @endif
                            <?php
                            $i++
                            ?>
                    </div>
                            <hr>
                        @endforeach
                <form action="{{route('cbt.lesson.add', $module->id)}}" method="POST">
                    <input type="hidden" name="lesson" value="lesson{{$i}}">
                    <button type="submit" class="btn btn-primary" style="float: center;">Add a Lesson {{$i}}</button>
                    @csrf
                </form>
                <hr>
                <div class="card p-3">
                        <h4>Conclusion - {{$conclusion->name}}</h4><p>{{$conclusion->html()}}</p>
                            @if (Auth::user()->permissions >= 4)
                                <a href="{{route('cbt.lesson.edit', $conclusion->id)}}" class="btn btn-primary btn-sm" style="float: right;">Edit</a>
                            @endif
                </div>

                        @endif
                    </div><hr>




                <br>


        </div>
            <br><br>
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

