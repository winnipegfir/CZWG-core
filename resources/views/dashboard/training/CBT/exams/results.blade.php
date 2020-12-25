@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.cbtMenu')
@if (Auth::check())
    <div class="container" style="margin-top: 20px;">
@if (Auth::user()->id != $student->user_id && Auth::user()->permissions < 3)
            <h2><text class="text-danger">You are not Authorized to view this test result!</text></h2><br><br>
@else
    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading"><span class="glyphicon glyphicon-cog"></span></div>
        <div align="center">
            <h2>Student: {{$student->user->fullName('FLC')}}</h2>
        <h2>Exam: {{$exam->name}}</h2>
            <h2>Score:
                @if ($grade->grade >= 80)
                    <text class="text-success">
                        {{$grade->grade}}% (Pass)
                    </text>
                @else
                    <text class="text-danger">
                        {{$grade->grade}}% (Fail)
                    </text>
                @endif
            </h2>
        </div>

<p>
  <div class="card p-3">
    @foreach ($results as $r)
        @if ($r->user_answer == $r->cbtexamquestion->answer)
                        <i class="fa fa-check"> Question: {{$r->cbtexamquestion->question}}</i> <text class="text-success">
        @else
             <i class="fa fa-times"> Question: {{$r->cbtexamquestion->question}}</i> <text class="text-danger">
        @endif
<br>
        User Answer:
        @if ($r->user_answer == 1)
                    {{$r->cbtexamquestion->option1}}
        @elseif ($r->user_answer == 2)
                    {{$r->cbtexamquestion->option2}}
        @elseif ($r->user_answer == 3)
                    {{$r->cbtexamquestion->option3}}
        @elseif ($r->user_answer ==4)
                    {{$r->cbtexamquestion->option4}}
        @endif
        </text>
        @if ($r->user_answer != $r->cbtexamquestion->answer)
        <br>Correct Answer: <text class="text-success">
                        @if ($r->cbtexamquestion->answer == 1)
                            {{$r->cbtexamquestion->option1}}
                        @elseif ($r->cbtexamquestion->answer == 2)
                            {{$r->cbtexamquestion->option2}}
                        @elseif ($r->cbtexamquestion->answer == 3)
                            {{$r->cbtexamquestion->option3}}
                        @elseif ($r->cbtexamquestion->answer ==4)
                            {{$r->cbtexamquestion->option4}}
                        @endif
                                </text>
        @endif
                <hr>
    @endforeach

</p>
        </div>
        @endif
    </div>
    @endif
@stop
