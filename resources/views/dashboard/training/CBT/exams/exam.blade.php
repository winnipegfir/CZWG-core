
@extends('layouts.master')
@extends('layouts.cbt')
@section('content')

@section('title', 'Exam')

<div class="container" style="margin-top: 20px;">
<div align="center">
  <br>
  <h2 class="font-weight-bold blue-text">{{$subject->name}}</h2>
<hr>
<form action="{{route('cbt.exam.grade', $subject->id)}}" method="POST">
  <?php
  $i = 1;
  ?>
@foreach($questions as $question)
  <h5 class="font-weight-bold">{{$question->question}} </h5>
  <input type="hidden" name="question_{{$i}}" value="{{$question->id}}">
  <input type="hidden" name="a_{{$i}}" value="{{$question->answer}}">
  <input type="radio" id="1" value="1" name="{{$i}}">{{$question->option1}}<br>
  <input type="radio" id="2" value="2" name="{{$i}}">{{$question->option2}}<br>
      @if ($question->option3 != null)
  <input type="radio" id="3" value="3" name="{{$i}}">{{$question->option3}}<br>
          <hr>
          @endif
      @if ($question->option4 != null)
  <input type="radio" id="4" value="4" name="{{$i}}">{{$question->option4}}<br><br>
          @else
          <hr>
          @endif
  <?php
  $i++
  ?>
@endforeach
@csrf
<input class="btn btn-success" type="submit" value="Submit Exam">
</form>
</div>
</div>
<br>
@stop
