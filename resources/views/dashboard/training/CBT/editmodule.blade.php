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
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#moduleDetails" style="float: right;">Change Module Details</button></h2>
                @endif
            <br>
            <div align="center">
                <h4><text class="font-weight-bold">It is very important that you delete the lesson that is before the conclusion ONLY. If not, server errors will occur when viewing the module!</text></h4>
                @foreach ($lessons as $l)
                    <div class="card p-3">
                        @if ($l->lesson == 'intro')
                        <h4>Introduction - {{$l->name}}</h4><p>{{$l->html()}}</p>
                            @if (Auth::user()->permissions >= 4)
                                <a href="{{route('cbt.lesson.edit', $l->id)}}" class="btn btn-primary btn-sm" style="float: right;">Edit</a>
                            @endif
                        @elseif ($l->lesson == 'conclusion')
                        <h4>Conclusion - {{$l->name}}</h4><p>{{$l->html()}}</p>
                            @if (Auth::user()->permissions >= 4)
                                <a href="{{route('cbt.lesson.edit', $l->id)}}" class="btn btn-primary btn-sm" style="float: right;">Edit</a>
                            @endif
                        @else
                        <h4>Lesson {{$i}} - {{$l->name}}</h4>
                        <p>{{$l->html()}}</p>
                            @if (Auth::user()->permissions >= 4)
                                <a href="{{route('cbt.lesson.edit', $l->id)}}" class="btn btn-primary btn-sm" style="float: right;">Edit</a>
                                <a href="#" class="btn btn-danger btn-sm" style="float: right;">Delete</a>
                            @endif
                            <?php
                            $i++
                            ?>
                        @endif
                    </div><hr>



                    @endforeach
                <br>
                <form action="{{route('cbt.lesson.add', $module->id)}}" method="POST">
                <input type="hidden" name="lesson" value="lesson{{$i}}">
                <button type="submit" class="btn btn-primary" style="float: center;">Add a Lesson</button>
                    @csrf
                </form>

        </div>
            <br><br>
                    {{$i}}
            </div>
        @endif
    @stop

