@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
<div class="container" style="margin-top: 20px;">
    <form action="{{ route('dashboard.image.upload') }}" method="POST" enctype="multipart/form-data">
    <img src="{{ $url ?? '' }}"></img>
        @csrf
        <h1>Upload Images Here!</h1>
        <div class="row">
            <div class="col-md-6">
                <input type="file" name="image" class="form-control">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-success">Upload</button>
               <br></br><br>
            </div>
        </div>
    </form>    
</div>
@stop