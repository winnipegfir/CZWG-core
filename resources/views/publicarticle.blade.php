@extends('layouts.master')

@section('title', $article->title.' - ')
@section('description', $article->summary)

@section('content')
    <div class="card card-image blue rounded-0">

    </div>
        <div class="text-white text-left py-1 px-4" style="background-color:#013162">
            <div class="container">
                <div class="py-5">
                    <h1 align="center" class="h1" style="font-size: 3em;">{{$article->title}}</h1>
                    <center><i class="far fa-clock"></i>&nbsp;&nbsp;<span @if($article->edited) title="Last edited {{$article->edited_pretty()}}" @endif>Published {{$article->published_pretty()}}</span>&nbsp;&nbsp;•&nbsp;&nbsp;<i class="far fa-user-circle"></i>&nbsp;&nbsp;by {{$article->author_pretty()}}</center>
                </div>
            </div>
        </div>
    </div>
    <div class="container py-4">
      <img src="{{$article->image}}" alt="" title="" width="100%" height="50%">
        <hr>
        @if(!$article->visible)
        @if(!Auth::user()->permissions >= 4)
            <?php abort(403, "Hidden");?>
        @endif
        <div class="alert bg-czqo-blue-light">
            This article is not visible to the public.
        </div>
        @endif
        {{$article->html()}}
    </div>
@stop
