@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.cbtMenu')
@if(Auth::check())
    <div class="container" style="margin-top: 20px;">
        <h2 class="font-weight-bold blue-text">{{$exam->name}}: Question Bank <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newQuestion" style="float: right;">Add Question</button></h2>
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
                <font class="font-weight-bold" style="color:red">There are no questions yet. Create some!</b></font>
            @else
                <tbody>
                @foreach ($questions as $q)
                    <tr>
                        <th class="align-middle" scope="row">{{$q->question}}</a></th>
                        <td class="align-middle">
                            @if ($q->answer == 1)
                                <i class="fa fa-check"></i>
                                @else
                                <i class="fa fa-times"></i>
                            @endif
                                    {{$q->option1}}</text>
                        </td>
                        <td class="align-middle">
                            @if ($q->answer == 2)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-times"></i>
                            @endif
                            {{$q->option2}}
                        </td>
                        <td class="align-middle">
                            @if ($q->answer == 3)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-times"></i>
                            @endif
                            {{$q->option3}}
                        </td>
                        <td class="align-middle">
                            @if ($q->answer == 4)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa fa-times"></i>
                            @endif
                            {{$q->option4}}
                        </td>
                        <td>
                        @if (Auth::user()->permissions >=4)
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group" role="group">
                                <a type="button" class="btn btn-sm btn-primary" data-target="#editQ-{{$q->id}}" data-toggle="modal" href="#editquestion"><i class="fa fa-edit"></i></a>
                                <a type="button" class="btn btn-sm btn-primary" href="{{route('cbt.exam.question.delete', $q->id)}}"><i class="fa fa-trash-alt" href="#"></i></a>
                            </div>
                        </div>
                        @endif
                        </td>
                    </tr>
                    <div class="modal fade" id="editQ-{{$q->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Question Editor</h5><br>
                                </div>
                                <div class="modal-body">
                                <p class="font-weight-bold">Note: For True/False answers, leave Option 3 & 4 BLANK.</p>
                                    <form method="POST" action="{{route('cbt.exam.question.update', $q->id)}}">
                                    <div class="form-group">
                                        <label>Question</label><br>
                                        <input name="question" class="form-control" value="{{$q->question}}"></input><br>
                                    <div class="form-group">
                                        <label>Option 1</label><br>
                                        <input name="option1" class="form-control" value="{{$q->option1}}"></input><br>
                                    <div class="form-group">
                                        <label>Option 2</label><br>
                                        <input name="option2" class="form-control" value="{{$q->option2}}"></input><br>
                                    <div class="form-group">
                                        <label>Option 3</label><br>
                                        <input name="option3" class="form-control" value="{{$q->option3}}"></input><br>
                                    <div class="form-group">
                                        <label>Option 4</label><br>
                                        <input name="option4" class="form-control" value="{{$q->option4}}"></input><br>
                                    <div class="form-group">
                                        <label>Correct Answer</label><br>
                                        <select class="form-control" name="answer">
                                            <option value="1" {{ $q->answer == "1" ? "selected=selected" : ""}}>Option 1</option>
                                            <option value="2" {{ $q->answer == "2" ? "selected=selected" : ""}}>Option 2</option>
                                            <option value="3" {{ $q->answer == "3" ? "selected=selected" : ""}}>Option 3</option>
                                            <option value="4" {{ $q->answer == "4" ? "selected=selected" : ""}}>Option 4</option>
                                        </select>
                                        @csrf
                                </div>
                                <div class="form-group">
                                    <label>True/False?&nbsp;</label>
                                    <input id="tf" type="checkbox">
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success form-control" type="submit" href="#" tyle="width:60%">Save Changes</button>
                                    <button class="btn btn-light" data-dismiss="modal" style="width:40%">Dismiss</button>
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
                    <h5 class="modal-title">New Question</h5><br>
                </div>
                <form method="POST" action="{{route('cbt.exam.question.add', $exam->id)}}">
                    @csrf
                    <div class="modal-body">
                        <p class="font-weight-bold">Note: For True/False answers, leave Option 3 & 4 BLANK.</p>
                        <div class="form-group">
                            <label>Question</label>
                            <input class="form-control" name="question"></input>
                        </div>
                        <div class="form-group">
                            <label>Option 1</label>
                            <input class="form-control" name="option1"></input>
                        </div>
                        <div class="form-group">
                            <label>Option 2</label>
                            <input class="form-control" name="option2"></input>
                        </div>
                        <div class="form-group" id="group3">
                            <label>Option 3</label>
                            <input class="form-control" id="option3" name="option3"></input>
                        </div>
                        <div class="form-group" id="group4">
                            <label>Option 4</label>
                            <input class="form-control" id="option4" name="option4"></input>
                        </div>
                        <div class="form-group">
                            <label>Correct Answer</label>
                            <select class="form-control" name="answer">
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3" id="answer3">Option 3</option>
                                <option value="4" id="answer4">Option 4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>True/False?&nbsp;</label>
                            <input id="tf" type="checkbox">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success form-control" type="submit" href="#" style="width:60%">Add Question</button>
                        <button class="btn btn-light" data-dismiss="modal" style="width:40%">Dismiss</button>
                    </div>
                </form>
                <script>
                    var checker = document.getElementById('tf');
                    var o3 = document.getElementById('option3');
                    var o4 = document.getElementById('option4');
                    var a3 = document.getElementById('answer3');
                    var a4 = document.getElementById('answer4');
                    var g3 = document.getElementById('group3');
                    var g4 = document.getElementById('group4');

                    checker.onchange = function () {
                        if (checker.checked) {
                            o3.disabled = true;
                            o4.disabled = true;
                            g3.hidden = true;
                            g4.hidden = true;
                            a3.hidden = true;
                            a3.disabled = true;
                            a4.hidden = true;
                            a4.disabled = true;
                        } else {
                            o3.disabled = false;
                            o4.disabled = false;
                            g3.hidden = false;
                            g4.hidden = false;
                            a3.hidden = false;
                            a3.disabled = false;
                            a4.hidden = false;
                            a4.disabled = false;
                        }
                    }
                </script>
            </div>
        </div>
    @endif
@stop
