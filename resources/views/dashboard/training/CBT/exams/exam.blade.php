
@extends('layouts.master')
@extends('layouts.cbt')
@section('content')

<div class="container" style="margin-top: 20px;">
<div align="center">
  <br>
  <h1>Exam: {{$subject->name}} </h1>

<form action="#" method="POST">
@foreach($questions as $question)
  <h5>Question: {{$question->question}} </h5>
  <input type="radio" id="1" name="1">{{$question->option1}}<br>
  <input type="radio" id="2" name="2">{{$question->option2}}<br>
  <input type="radio" id="3" name="3">{{$question->option3}}<br>
  <input type="radio" id="4" name="4">{{$question->option4}}<br><br>
@endforeach
<input type="submit" value="Submit Exam">
</form>
</div>
</div>

@stop
