@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 20px;">
        <a href="{{route('training.students.view', $student->id)}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Back to {{$student->user->fullName('FLC')}}'s Profile</a>
        <div class="container" style="margin-top: 20px;">
                <h1 class="blue-text font-weight-bold">New Training Note for {{$student->user->fullName('FLC')}} </h1>
                <hr>
                <form method="POST" action="{{route('add.trainingnote', $student->id)}}" class="form-group">
                    @csrf
                    <label class="form-control">Title</label>
                    <input type="text" name="title" class="form-control"></input>
                        <label class="form-control">Content</label>
                        <script>
                            tinymce.init({
                                selector: '#content',
                                menubar: 'false',
                                setup : function(ed) {
                                    ed.on('blur', function(e) {
                                        showSaveButton();
                                    });
                                },
                            });
                        </script>
                        <textarea id="content" name="content" class="form-control"></textarea><br>
                        <input type="submit" style="margin-left: 0"class="btn btn-success" value="Add Training Note"></input>
                </form>
                </div>
      <br>
      @stop
