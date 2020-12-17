@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
  @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">
      <h1 class="font-weight-bold blue-text">Winnipeg Exam Centre</h1>
      <p>Welcome to the Exam Centre! Here you can view exams that are currently assigned to you, and also review graded exams.</p>
    <hr>
    <h5><text class="font-weight-bold"class="font-weight-bold pb-2">Available Exams</text></h5>
      @if (count($exams) < 1)
        You do not have any exams assigned to you!
      @else
      @foreach ($exams as $exams)
        <li><a href="{{route('cbt.exam.begin', $exams->cbt_exam_id)}}">{{$exams->cbtexam->name}}</a></li>
      @endforeach
      @endif
    <hr>
    <h5><text class="font-weight-bold" class="font-weight-bold pb-2">Completed Exams</text></h5>
    @if (count($completedexams) < 1)
      <text class="font-weight-bold">You do not have any completed exams!</text>
      @else
      @foreach ($completedexams as $cexams)
    <li>{{$cexams->cbtexam->name}} -
@if ($cexams->grade >= 80)
<text class="text-success">{{$cexams->grade}}% (Pass)</text>
@else
<text class="text-danger">{{$cexams->grade}}% (Fail)</text>
@endif
    </li>
      @endforeach
      @endif
      <br><br>
</div>
@stop
