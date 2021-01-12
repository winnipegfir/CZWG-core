@extends('layouts.email')

@section('to-line')
    <strong>Hi there,</strong>
@stop

@section('message-content')
    You have been issued a Solo Certification for {{$positions}}!
    <br>
    You may now log into the network unsupervised! This solo certification will expire thirty (30) days from today.
    <br><br>
@stop

@section('footer-reason-line')
    automated email from winnipegfir.
@endsection
