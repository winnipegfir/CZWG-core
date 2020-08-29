@extends('layouts.master')

@section('content')
    <div class="container" style="margin-top: 20px;">
        <div class="container" style="margin-top: 20px;">
            <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Dashboard</a>
            <h1 class="blue-text font-weight-bold mt-2">Feedback</h1>
            <hr>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                        Controller Feedback
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Website Feedback</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    @if (count($controller_feedback) < 1)
                        <br>
                        No controller feedback.
                        <br>
                    @else
                        <br>
                        <p>Found {{count($controller_feedback) }} feedback.</p>
                        <table id="dataTable" class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">Submitter</th>
                                <th scope="col">Controller</th>
                                <th scope="col">Position</th>
                                <th scope="col">Submitted</th>
                                <th scope="col">View</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($controller_feedback as $f)
                                <tr>
                                    <th scope="row">{{User::where('id', $f->user_id)->firstOrFail()->fullName('FLC')}}</td>
                                    <td>{{User::where('id', $f->controller_cid)->firstOrFail()->fullName('FLC')}}</td>
                                    <td>{{$f->position}}</td>
                                    <td>{{$f->created_at}}</td>
                                    <td>
                                        <a href="{{url('/admin/feedback/controller/'.$f->id)}}"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <br>
                    @endif
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    @if (count($website_feedback) < 1)
                        <br>
                        No website feedback.
                        <br>
                    @else
                        <br>
                        <p>Found {{count($website_feedback) }} feedback.</p>
                        <table id="dataTable" class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Author</th>
                                <th scope="col">Title</th>
                                <th scope="col">Replies</th>
                                <th scope="col">Submitted</th>
                                <th scope="col">View</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($website_feedback as $f)
                                <tr>
                                    <th scope="row">#{{$f->ticket_id}}</th>
                                    <td>{{$f->user->fullName('FLC')}}</td>
                                    <td>{{$f->title}}</td>
                                    <td>{{$f->replies}}</td>
                                    <td>{{$f->submission_time}}</td>
                                    <td>
                                        <a href="{{url('/admin/feedback/'.$f->id)}}"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <br>
                    @endif
                </div>
            </div>
        </div>
@stop
