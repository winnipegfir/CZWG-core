@extends('layouts.email')

@section('message-content')
<h2>Inactivity Alert!</h2>
<p>Here is a list of controllers who have <b>not</b> achieved the minimum hours required this month:</p>

@foreach($members as $m)
    {{$m['name']}} | Email: {{$m['email']}} | Activity: {{$m['activity']}}/{{$m['requirement']}}<br>
    <br>
@endforeach
<br>
<b>Either an email or a ticket should be sent to the member alerting them of their inactivity.</b>
@endsection

@section('from-line')
Sent automatically by ActivityBot.
@endsection

@section('footer-reason-line')
you are a staff member.
@endsection
