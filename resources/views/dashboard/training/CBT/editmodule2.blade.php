@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.cbtMenu')
    @if(Auth::check())
        <div class="container" style="margin-top: 20px;">
            <div align="center">
            <h2 class="font-weight-bold blue-text">Modify {{$lesson->name}}</h2>
            <br>
            <form action="{{route('cbt.lesson.submit', $lesson->id)}}" method="POST" class="form-group">
                <label class="form-control">Name of Lesson</label>
                <input type="text" name="name" value="{{$lesson->name}}" class="form-control"><br>
                <label class="form-control">Content - HTML Accepted!</label>
                <textarea name="content" class="form-control">{{$lesson->content_html}}</textarea>
                <br>
                <button type="submit" class="btn-success form-control">Save Changes</button>
                @csrf
            </form>
            </div>
        </div>
        <br><br><br><br>
    @endif
        @stop
