@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">

    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading"><span class="glyphicon glyphicon-cog"></span></div>
        <div class="center">
        <p>Exam: {{$exam->name}}</p>
        <p>Score: {{$grade->grade}}%

</p>
<p>
  <div class="card p-3">
    @foreach ($results as $r)
        @if ($r->user_answer == $r->cbtexamquestion->answer)
                        <i class="fa fa-check"> Question: {{$r->cbtexamquestion->question}}</i>
        @else
             <i class="fa fa-times"> Question: {{$r->cbtexamquestion->question}}</i>
        @endif

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

        @if ($r->user_answer != $r->cbtexamquestion->answer)
        <br>Correct Answer:
                        @if ($r->cbtexamquestion->answer == 1)
                            {{$r->cbtexamquestion->option1}}
                        @elseif ($r->cbtexamquestion->answer == 2)
                            {{$r->cbtexamquestion->option2}}
                        @elseif ($r->cbtexamquestion->answer == 3)
                            {{$r->cbtexamquestion->option3}}
                        @elseif ($r->cbtexamquestion->answer ==4)
                            {{$r->cbtexamquestion->option4}}
                        @endif
        @endif
                <hr>
    @endforeach

</p>
        </div>
    </div>
@stop
