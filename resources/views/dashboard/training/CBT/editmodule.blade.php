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
            <h2 class="font-weight-bold blue-text">{{$module->name}} Administration <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newQuestion" style="float: right;">Add Lesson</button></h2>
            <br>
                @foreach ($lessons as $l)
                    <div class="card p-3">
                        @if ($l->lesson == 'intro')
                        <h4>Introduction - {{$l->name}}</h4><p>{{$l->content_html}}</p>
                        @elseif ($l->lesson == 'conclusion')
                        <h4>Conclusion - {{$l->name}}</h4><p>{{$l->content_html}}</p>
                        @else
                        <h4>Lesson {{$i}} - {{$l->name}}</h4>
                        <p>{{$l->content_html}}</p>
                            <?php
                            $i++
                            ?>
                        @endif
                    </div><hr>



                    @endforeach
                    {{$i}}
            </div>
        @endif
    @stop

