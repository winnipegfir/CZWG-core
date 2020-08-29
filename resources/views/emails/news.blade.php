@extends('layouts.email')

@section('to-line', 'Hi '. $user->fullName('FLC') . ',')

@section('message-content')
<h2>{{$news->title}}</h2>
{{$news->html()}}
@endsection

@section('from-line')
@if ($news->show_author)
Sent by <b>{{$news->user->fullName('FLC')}} ({{$news->user->staffProfile->position}})</b>
@else
Sent by the Winnipeg FIR Staff Team
@endif
@endsection

@section('footer-to-line', $user->fullName('FLC').' ('.$user->email.')')

@section('footer-reason-line')
@if ($news->email_level == 1)
they are a controller on the Winnipeg FIR controller roster.
@elseif ($news->email_level == 2)
they hold an account on the Winnipeg FIR website and have subscribed to emails.
@elseif ($news->email_level == 3)
they hold an account on the Winnipeg FIR website.
@endif
@endsection
