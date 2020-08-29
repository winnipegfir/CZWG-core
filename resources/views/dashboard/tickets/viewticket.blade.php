@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')

    <div class="container" style="margin-top: 20px;">
            <a href="{{route('tickets.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Back</a>
        <h1 class="blue-text font-weight-bold mt-2">Ticket #{{ $ticket->ticket_id }}</h1>
        <hr>
        <h2>{{$ticket->title}}</h4>
        <p>
            Status:
            @if ($ticket->status == 0)
                Open
            @elseif ($ticket->status == 1)
                Closed
            @else
                On Hold
            @endif
            <br/>
            Staff Member: {{$ticket->staff_member->user->fullName('FLC')}} ({{$ticket->staff_member->position}})
            <br/>
            Submitted by {{$ticket->user->fullName('FLC')}} on <span title="{{$ticket->submission_time}}">{{$ticket->submission_time_pretty()}}</span><br/>
            Last updated <span title="{{$ticket->updated_at}}">{{$ticket->updated_at_pretty()}}</span>
        </p>
        <h3>Message</h3>
        <div class="markdown border p-3">
            {{$ticket->html()}}
        </div>
        <br/>
        <h3>Replies</h3>
        @if (count($replies) < 1)
            No replies yet!<br>
        @else
            <div class="list-group">
                @foreach ($replies as $reply)
                    <div class="list-group-item" @if ($reply->user_id == 1) style="background-color: #bfe0fb;" @endif">
                    <h6>{{$reply->user->fullName('FLC')}} on <span title="{{$reply->submission_time}}">{{$reply->submission_time_pretty()}}</span></h6>
                        <div id="replyContent{{$reply->id}}" class="text markdown">
                            {{$reply->html()}}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <br/>
        @if ($ticket->status != 1)
        <h5>Write a reply</h5>
            {!! Form::open(['route' => ['tickets.reply', $ticket->ticket_id]]) !!}
            {!! Form::textarea('message', null, ['class' => 'form-control', 'id' => 'addReplyMessage']) !!}
            <script>
                var simplemde = new SimpleMDE({ element: document.getElementById("addReplyMessage") });
            </script>
            <br/>
        <div class="row">
            {!! Form::submit('Reply', ['class' => 'btn btn-success']) !!}
            @if(Auth::user()->permissions >= 4)
                <a role="button" data-toggle="modal" data-target="#closeTicket" class="btn btn-outline-danger">Close Ticket</a>
            @endif
            {!! Form::close() !!}
        @endif
        <br>
        </div>
    </div>
@stop

@if(Auth::user()->permissions >= 4)
{{-- Start Close Ticket Modal --}}
<div class="modal fade" id="closeTicket" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Close Ticket?</h5>
            </div>
            <div class="modal-body">
                <p class="font-weight-bold">If you'd like, leave a comment below:</p>
                {!! Form::open(['route' => ['tickets.closeticket', $ticket->ticket_id]]) !!}
                {!! Form::textarea('message', null, ['class' => 'form-control', 'id' => 'addReplyMessage']) !!}
                <hr>
                <div class="container py-0 row">
                    {!! Form::submit('Close er up', ['class' => 'btn btn-outline-danger']) !!}
                    {!! Form::close() !!}
                    <button class="btn btn-light" data-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- End Close Ticket Modal --}}
@endif
