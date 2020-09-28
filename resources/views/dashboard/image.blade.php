@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
<div class="container" style="margin-top: 20px;">
    <form action="{{ route('dashboard.image.upload') }}" method="POST" enctype="multipart/form-data">
    <img src="{{ $url ?? '' }}"></img>
        @csrf
        <h1>Upload Files Here!</h1>
        <div class="custom-file">
                <input type="file" name="file" class="custom-file-input" id="chooseFile">
                <label class="custom-file-label" for="chooseFile">Select file</label>
            </div>
            <button type="submit" name="submit" class="btn btn-success">
                Upload File
            </button>
        </div>
    </form>    
</div>
<br>
@stop