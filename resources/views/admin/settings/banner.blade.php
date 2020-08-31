@extends('layouts.master')

@section('content')
    <div class="container py-4">
        <a href="{{route('settings.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Settings</a>
        <h1 class="blue-text font-weight-bold mt-2">Banner</h1>
        <hr>
        <form method="POST" action="{{route('settings.banner.edit')}}">
            @csrf
            <p class="font-weight-bold">Banner Mode</p>
            <select name="bannerMode" id="bannerModeSelect" class="form-control">
                <option value="">Hidden</option>
                <option value="success" class="alert-success" {{$banner->bannerMode == 'success' ? 'selected=selected' : ''}}>Success</option>
                <option value="danger" class="alert-danger" {{$banner->bannerMode == 'danger' ? 'selected=selected' : ''}}>Danger</option>
                <option value="warning" class="alert-warning" {{$banner->bannerMode == 'warning' ? 'selected=selected' : ''}}>Warning</option>
                <option value="info" class="alert-info" {{$banner->bannerMode == 'info' ? 'selected=selected' : ''}}>Info</option>
                <option value="primary" class="alert-primary" {{$banner->bannerMode == 'primary' ? 'selected=selected' : ''}}>Primary</option>
                <option value="secondary" class="alert-secondary" {{$banner->bannerMode == 'secondary' ? 'selected=selected' : ''}}>Secondary</option>
                <option value="light" class="alert-light" {{$banner->bannerMode == 'light' ? 'selected=selected' : ''}}>Light</option>
                <option value="dark" class="alert-dark" {{$banner->bannerMode == 'dark' ? 'selected=selected' : ''}}>Dark</option>
                <option value="muted" class="alert-muted" {{$banner->bannerMode == 'muted' ? 'selected=selected' : ''}}>Muted</option>
                <option value="white" class="alert-white" {{$banner->bannerMode == 'white' ? 'selected=selected' : ''}}>White</option>
            </select>
            <br>
            <p class="font-weight-bold">Banner Message</p>
            <input name="bannerMessage" class="form-control" value="{{$banner->banner}}" placeholder="We are breaking the website!">
            <br>
            <p class="font-weight-bold">Banner Link</p>
            <input name="bannerLink" class="form-control" value="{{$banner->bannerLink}}" placeholder="https://winnipegfir.ca/rip (optional)">
            <br>
            <button class="btn btn-success">Submit</button>
        </form>
    </div>
@stop
