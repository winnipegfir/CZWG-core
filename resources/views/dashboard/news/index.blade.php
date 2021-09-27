@extends('layouts.master')
@section('content')
<div class="container py-4">
    <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Dashboard</a>
    <h1 class="font-weight-bold blue-text">News</h1>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <a href="{{route('news.articles.create')}}" class="mb-3 btn btn-block btn-primary">Create Article</a>
            <table class="table dt table-hover table-bordered">
                <thead>
                    <th>Title</th>
                    <th>Actions</th>
                </thead>
                <tbody>
                    @foreach ($articles as $a)
                    <tr>
                        <td><a class="blue-text" href="{{route('news.articles.view', $a->slug)}}">{{$a->title}}</a></td>
                    <td align="center">
                      <a href="{{route('news.articles.delete', [$a->id]) }}">
                          <button class="btn btn-sm btn-danger">Delete</button>
                      </a>
                      </tr>
                    @endforeach
                </tbody>
            </table>
            <script>
                $(document).ready(function() {
                    $('.table.dt').DataTable();
                } );
            </script>
        </div>
        <div class="col-md-6">

        </div>
    </div>
</div>
@endsection
