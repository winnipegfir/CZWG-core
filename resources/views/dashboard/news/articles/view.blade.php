@extends('layouts.master')
@section('content')
<div class="container py-4">
    <a href="{{route('news.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> News</a>
    <h1 class="font-weight-bold blue-text">{{$article->title}}</h1>
    <h5>Published {{$article->published_pretty()}}</h5>
<form method="POST" action="{{route('news.articles.edit', [$article->id])}}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <ul class="stepper mt-0 p-0 stepper-vertical">
                <li class="active">
                    <a href="#!">
                        <span class="circle">1</span>
                        <span class="label">Primary information</span>
                    </a>
                    <div class="step-content w-75 pt-0">
                        <div class="form-group">
                            <label for="">Article title</label>
                            <input type="text" name="title" value="{{$article->title}}" id="" class="form-control" placeholder="New sector files released, etc.">
                        </div>
                        <div class="form-group">
                            <label for="">Author</label>
                            <div class="d-flex flex-row justify-content-between">
                                <select class="custom-select disabled" value="{{$article->user_id}}" name="author">
                                    @foreach ($staff as $s)
                                        <option value="{{$s->user->id}}">{{$s->user->fullName('FLC')}} ({{$s->position}})</option>
                                    @endforeach
                                    <option value="{{$article->user_id}}">{{$article->user->fullName('FLC')}}</option>
                                </select>
                                <div class="ml-3">
                                    <input type="checkbox" name="showAuthor" class="" id="defaultUnchecked">
                                    <label class="" for="defaultUnchecked">Show author publicly</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Summary</label>
                            <input type="text" name="summary" value="{{$article->summary}}" id="" class="form-control" placeholder="Short description of the article">
                        </div>
                    </div>
                </li>
                <li class="active">
                    <a href="#!">
                        <span class="circle">2</span>
                        <span class="label">Image</span>
                    </a>
                    <div class="step-content w-75 pt-0">
                        @if ($article->image)
                        <img src="{{$article->image}}" alt="" class="img-fluid w-50 img-thumbnail">
                        @else
                        No image.
                        @endif
                        <p class="mt-4">This image will be the thumbnail, as well as the photo at the top of the story.</p>
                        <div class="input-group pb-3">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="image">
                                <label class="custom-file-label">Choose image</label>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="active">
                    <a href="#!">
                        <span class="circle">3</span>
                        <span class="label">Content</span>
                    </a>
                    <div class="step-content w-75 pt-0">
                        <label for="">Use Markdown</label>
                        <textarea id="contentMD" name="content" class="w-75">{{$article->html()}}</textarea>
                        <script>
                            var simplemde = new SimpleMDE({ element: document.getElementById("contentMD"), toolbar: false });
                        </script>
                    </div>

                </li>
                <li class="active">
                    <a href="#!">
                        <span class="circle">4</span>
                        <span class="label">Options</span>
                    </a>
                    <div class="step-content w-75 pt-0">
                        <div class="form-group">
                            <div class="mr-2">
                                <input type="checkbox" checked="true"  class="" name="articleVisible" id="articleVisible">
                                <label class="" for="">Publicly visible (published)</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="d-flex flex-col">
                            Email level: asd
                            </div>
                        </div>
                        <input type="submit" value="Edit Article" class="btn btn-primary">
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
</form>
@endsection
