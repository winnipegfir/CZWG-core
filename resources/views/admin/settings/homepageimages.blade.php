@extends('layouts.master')
@section('content')
    <div class="container py-4">
        <a href="{{route('settings.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Settings</a>
        <h1 class="blue-text font-weight-bold mt-2">Homepage Images</h1>
        <hr>
        <h2 class="blue-text">Current Images</h2>
        <div class="row">
            @foreach($images as $i)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{$i->url}}" target="_blank"><img src="{{$i->url}}" style="width:100%; text-align: center;"></a>
                            <div class="mt-2 d-flex flex-row justify-content-between align-items-center">
                                <div>
                                    By: {{$i->credit}}
                                </div>
                                <div class="text-center">
                                    <a href="{{route('settings.images.test', $i->id)}}" class="btn btn-sm btn-dark">Test View</a>
                                    <a href="#" data-toggle="modal" data-target="#editImageModal{{$i->id}}" role="button" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="{{route('settings.images.delete', $i->id)}}" class="btn btn-sm btn-danger">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            @endforeach
        </div>
        <hr>
        <a href="#" data-toggle="modal" data-target="#uploadImageModal" role="button" class="btn btn-primary"><h2>Upload New Image</h2></a>
    </div>

    <!-- Start Upload Image Modal -->
    <div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Image</h5>
                </div>
                <form method="POST" action="{{route('settings.images.upload')}}">
                    @csrf
                    <div class="modal-body">
                        <p class="font-weight-bold">URL</p>
                        <input name="URL" class="form-control" placeholder="https://winnipegfir.ca/path/to/very/good/picture">
                        <br>
                        <p class="font-weight-bold">Credit for Picture</p>
                        <input name="nameCredit" class="form-control" placeholder="Nate Power">
                        <br>
                        <p class="font-weight-bold">Extra CSS (optional)</p>
                        <input name="CSS" class="form-control" value="{{$i->css}}" placeholder="background-position: bottom;">
                    </div>
                    <div class="modal-footer">
                        <input role="button" type="submit" class="btn btn-success" value="Upload">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Upload Image Modal -->

    <!-- Start Edit Image Modal -->
    @foreach($images as $i)
        <div class="modal fade" id="editImageModal{{$i->id}}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Image</h5>
                    </div>
                    <form method="POST" action="{{route('settings.images.edit', $i->id)}}">
                        @csrf
                        <div class="modal-body">
                            <p class="font-weight-bold">URL</p>
                            <input name="URL" class="form-control" value="{{$i->url}}" placeholder="https://winnipegfir.ca/path/to/very/good/picture">
                            <br>
                            <p class="font-weight-bold">Credit for Picture</p>
                            <input name="nameCredit" class="form-control" value="{{$i->credit}}" placeholder="Nate Power">
                            <br>
                            <p class="font-weight-bold">Extra CSS (optional)</p>
                            <input name="CSS" class="form-control" value="{{$i->css}}" placeholder="background-position: bottom;">
                        </div>
                        <div class="modal-footer">
                            <input role="button" type="submit" class="btn btn-primary" value="Edit">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
    <!-- End Edit Image Modal -->
@stop
