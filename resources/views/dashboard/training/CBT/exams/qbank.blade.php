@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">
        <h2>{{$exam->name}}'s Question Bank <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newQuestion" style="float: right;">Add Question</button></h2>
        <div align=""center">
        <table id="dataTable" class="table table-hover">
            <thead>
            <tr>
                <th scope="col">Question</th>
                <th scope="col">Option 1</th>
                <th scope="col">Option 2</th>
                <th scope="col">Option 3</th>
                <th scope="col">Option 4</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            @if (count($questions) < 1)
                <font class="font-weight-bold">** There are no questions yet. Create some!</b></font>
            @else
                <tbody>
                @foreach ($questions as $q)
                    <tr>
                        <th scope="row">{{$q->question}}</a></th>
                        <td>
                            @if ($q->answer == 1)
                                <i class="fa fa-check"></i>
                                @else
                                <i class="fa fa-times"></i>
                            @endif
                                    {{$q->option1}}</text>
                        </td>
                        <td>
                            @if ($q->answer == 2)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-times"></i>
                            @endif
                            {{$q->option2}}
                        </td>
                        <td>
                            @if ($q->answer == 3)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-times"></i>
                            @endif
                            {{$q->option3}}
                        </td>
                        <td>
                            @if ($q->answer == 4)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-times"></i>
                            @endif
                            {{$q->option4}}
                        </td>
                        <td>
                        @if (Auth::user()->permissions >=4)
                                <a href="#editquestion" data-toggle="modal" data-target="#editQ-{{$q->id}}">Edit</a>

                                | <a href="{{route('cbt.exam.question.delete', $q->id)}}">Delete</a>
                        </td>
                        @endif

                    </tr>
                    <div class="modal fade" id="editQ-{{$q->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Question</h5><br>
                                    <h4>Note: This change will be placed in the audit log!</h4>

                                </div>
                                <div class="modal-body">

                                    <form method="POST" action="#">
                                        <label>Question</label><br>
                                        <input name="question" type=""text" size="60" value="{{$q->question}}"></input><br>
                                        <label>Option 1</label><br>
                                        <input name="option1" type=""text" size="60" value="{{$q->option1}}"></input><br>
                                        <label>Option 2</label><br>
                                        <input name="option2" type=""text" size="60" value="{{$q->option2}}"></input><br>
                                        <label>Option 3</label><br>
                                        <input name="option3" type=""text" size="60" value="{{$q->option3}}"></input><br>
                                        <label>Option 4</label><br>
                                        <input name="option4" type=""text" size="60" value="{{$q->option4}}"></input><br>
                                        <label>Correct Answer</label><br>
                                        <select name="answer">
                                            <option value="1">Option 1</option>
                                            <option value="2">Option 2</option>
                                            <option value="3">Option 3</option>
                                            <option value="4">Option 4</option>
                                        </select>
                                    </form>

                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success form-control" type="submit" href="#">Save Changes</button>
                                    <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                @endforeach
            @endif
        </table>
    </div>
    </div>
    <br><br><br><br>

    <div class="modal fade" id="newQuestion" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">New Question</h5><br>
                        <h5>Note: For True/False answers, leave Option 3 & 4 BLANK.</h5>

                </div>
                <div class="modal-body">

                    <form method="POST" action="{{route('cbt.exam.question.add', $exam->id)}}">
                        <label>Question</label><br>
                        <input name="question" type=""text" size="60"></input><br>
                        <label>Option 1</label><br>
                        <input name="option1" type=""text" size="60"></input><br>
                        <label>Option 2</label><br>
                        <input name="option2" type=""text" size="60"></input><br>
                        <label>Option 3</label><br>
                        <input name="option3" type=""text" size="60"></input><br>
                        <label>Option 4</label><br>
                        <input name="option4" type=""text" size="60"></input><br>
                        <label>Correct Answer</label><br>
                        <select name="answer">
                            <option value="1">Option 1</option>
                            <option value="2">Option 2</option>
                            <option value="3">Option 3</option>
                            <option value="4">Option 4</option>
                        </select>
                        @csrf


                </div>
                <div class="modal-footer">
                    <button class="btn btn-success form-control" type="submit" href="#">Add Question</button>
                    <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
                    </form>
                </div>
            </div>
        </div>
@stop
