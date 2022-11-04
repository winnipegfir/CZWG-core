@extends('layouts.master')
@section('title', 'Privacy Policy - Winnipeg FIR')
@section('navbarprim')

    @parent

@stop

@section('content')
<div class="container" style="margin-top: 20px">
    <h1 class="blue-text font-weight-bold">Privacy Policy</h1>
    <hr>
    <iframe style="border: none; margin-top: 10px; margin-bottom: 10px; width: 100%; height: 100vh;" src="https://winnipegfir.ca/storage/files/uploads/1667596406.pdf"></iframe>
    If the PDF is not displaying correctly, you can view it directly <a href="https://winnipegfir.ca/storage/files/uploads/1667596406.pdf">here.</a><br></br>
</div>
@endsection
