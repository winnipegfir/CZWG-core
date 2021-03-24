@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
<div class="container" style="margin-top: 20px;">
    <form action="{{ route('dashboard.upload.post') }}" method="POST" enctype="multipart/form-data">
    <img src="{{ $url ?? '' }}"></img>
        @csrf
        <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Dashboard</a>
        <h1 class="font-weight-bold blue-text">Upload Files Here!</h1>
        <br>
        <div class="custom-file">
                <input type="file" name="file" class="custom-file-input" id="chooseFile">
                <label class="custom-file-label" for="chooseFile">Select a file...</label>
        </div>
        <button style="margin-left: 0px" type="submit" name="submit" class="btn btn-success">
            Upload
        </button>
    </form>
</div>
<br>
@stop
