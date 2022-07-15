@extends('layouts.email')

@section('to-line')
<strong>Hi there,</strong>
@stop

@section('message-content')
@if ($grade >= 80)
Your student {{$student->user->fullName('FLC')}} has passed the {{$exam->name}} with a grade of {{$grade}}%.
@endif
@if ($grade <= 79)
Your student {{$student->user->fullName('FLC')}} has failed the {{$exam->name}} with a grade of {{$grade}}%.
@endif
<br>

<br><br>
@stop

@section('footer-reason-line')
This is your student within the WinnipegFIR.
@endsection
