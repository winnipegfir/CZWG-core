@extends('layouts.master')

@section('title', 'News - Winnipeg FIR')
@section('description', 'News from Winnipeg FIR')

@section('content')
    <div class="container py-4">
        <h1 class="blue-text font-weight-bold">News</h1>
        <hr>
            @foreach($news as $n)
            <div class="homepage-news blue white-text my-2 h-100" style="width:1120px; outline:1px solid black">
                <a href="{{route('news.articlepublic', $n->slug)}}">
                    @if ($n->image)
                    <div style="background-image:url({{$n->image}}); background-position: center; background-size:cover; height: 300px;" class="homepage-news-img waves-effect"></div>
                    @else
                    <div class="blue waves-effect homepage-news-img"></div>
                    @endif
                </a>
                <div class="card-body pb-2">
                    <a class="card-title font-weight-bold white-text" href="{{route('news.articlepublic', $n->slug)}}"><h4>{{$n->title}}</h4></a>
                    <small><i class="far fa-clock"></i>&nbsp;&nbsp;<span @if($n->edited) title="Last edited {{$n->edited_pretty()}}" @endif>Published {{$n->published_pretty()}}</span><br/><i class="far fa-user-circle"></i>&nbsp;&nbsp;{{$n->author_pretty()}}</small>
                </div>
            </div>
            <br>
            @endforeach
    </div>
@stop
