@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.cbtMenu')
@if (Auth::check())
    <div class="container" style="margin-top: 20px;">
@if (Auth::user()->id != $student->user_id && Auth::user()->permissions < 3)
            <h2><text class="text-danger">You are not authorized to view this test result!</text></h2><br><br>
@else
<a href="/dashboard/training/students/current" class="blue-text" style="font-size: 1.2em"> <i class="fas fa-arrow-left"></i> Back to Students</a>
    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading"><span class="glyphicon glyphicon-cog"></span></div>
        <div align="center">
        <h2><u> <text class="font-weight-bold blue-text">Exam:</text> {{$exam->name}}</h2></u>
        <h4> <text class="font-weight-bold blue-text">Student:</text> {{$student->user->fullName('FLC')}}</h4>
        <h4> <text class="font-weight-bold blue-text">Score:</text>
            @if ($grade->grade >= 80)
                <text class="text-success font-weight-bold">
                    {{$grade->grade}}% (Pass)
                </text>
            @else
                <text class="text-danger font-weight-bold">
                    {{$grade->grade}}% (Fail)
                </text>
            @endif
        </h4>
        </div>
        <br>
    
    @foreach ($results as $r) 
    <div class="card p-3" >      
        @if ($r->user_answer == $r->cbtexamquestion->answer)
            <h5 class="font-weight-bold blue-text"> <u>{{$r->cbtexamquestion->question}}</u></h5> 
        @else
            <h5 class="font-weight-bold blue-text"> <u>{{$r->cbtexamquestion->question}}</u></h5>
        @endif      
        <p style="margin-bottom: 0%">
            <text class="font-weight-bold">User Answer:</text>
                @if ($r->user_answer == 1)
                    {{$r->cbtexamquestion->option1}}
                @elseif ($r->user_answer == 2)
                    {{$r->cbtexamquestion->option2}}
                @elseif ($r->user_answer == 3)
                    {{$r->cbtexamquestion->option3}}
                @elseif ($r->user_answer ==4)
                    {{$r->cbtexamquestion->option4}}
                @endif
            
            @if ($r->user_answer == $r->cbtexamquestion->answer)
            <br>
            <text class="text-success font-weight-bold">Correct!</text>

        @endif
        <br>
        @if ($r->user_answer != $r->cbtexamquestion->answer)
            <text class="font-weight-bold"><text class="text-danger">Wrong!</text> Correct Answer:</text>
                @if ($r->cbtexamquestion->answer == 1)
                    {{$r->cbtexamquestion->option1}}
                @elseif ($r->cbtexamquestion->answer == 2)
                    {{$r->cbtexamquestion->option2}}
                @elseif ($r->cbtexamquestion->answer == 3)
                    {{$r->cbtexamquestion->option3}}
                @elseif ($r->cbtexamquestion->answer ==4)
                    {{$r->cbtexamquestion->option4}})
                @endif
                <text class="font-weight-bold"></text>
        @endif
    </div>
    <br>
    @endforeach
    </div>
    @endif
    @endif
</div>
<br>
    
@stop
