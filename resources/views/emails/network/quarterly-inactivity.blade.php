@extends('layouts.email')

@section('message-content')
<h2>Inactivity Alert!</h2>
<p>Here is a list of controllers who have <b>not</b> achieved the minimum hours required this quarter.</p>
<p>The Winnipeg FIR currently requires the max amount of time per member as required by VATSIM GCAP s. 9.4(c)(i).</p>

@foreach($members as $m)
    {{$m['name']}} | Email: {{$m['email']}} | Activity: {{$m['activity']}}/{{$m['requirement']}}<br>
    <br>
@endforeach
<br>
<b>The user's above should be alerted of their inactivity. This is NOT automatically sent to them.</b>
@endsection

@section('from-line')
Sent automatically by ActivityBot.
@endsection

@section('footer-reason-line')
you are a staff member.
@endsection
