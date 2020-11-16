
@extends('layouts.master')
@extends('layouts.cbt')
@section('content')

<div class="container" style="margin-top: 20px;">
<div align="center">
  <br>
  <h1>Exam: {{$subject->name}} </h1>

<form action="{{route('cbt.exam.grade', $subject->id)}}" method="POST">
  <?php
  $i = 1;
  ?>
@foreach($questions as $question)
  <h5>Question: {{$question->question}} </h5>
  <input type="hidden" name="question_{{$i}}" value="{{$question->id}}">
  <input type="radio" id="1" value="1" name="{{$i}}">{{$question->option1}}<br>
  <input type="radio" id="2" value="2" name="{{$i}}">{{$question->option2}}<br>
  <input type="radio" id="3" value="3" name="{{$i}}">{{$question->option3}}<br>
  <input type="radio" id="4" value="4" name="{{$i}}">{{$question->option4}}<br><br>
  <?php
  $i++
  ?>
@endforeach
@csrf
<input type="submit" value="Submit Exam">
</form>
</div>
</div>

@stop
