@extends('layouts.master')
@section('title', 'Privacy Policy')
@section('navbarprim')

    @parent

@stop

@section('content')
<div class="container" style="margin-top: 20px">
    <h1 class="blue-text font-weight-bold">Privacy Policy</h1>
    <hr>
    <iframe style="border: none; margin-top: 10px; margin-bottom: 10px; width: 100%; height: 100vh;" src="https://winnipegfir.ca/wp-content/uploads/2020/08/CZWGFIR_Privacy.pdf"></iframe>
    If the PDF is not displaying correctly, you can view it directly <a href="https://winnipegfir.ca/wp-content/uploads/2020/08/CZWGFIR_Privacy.pdf">here.</a><br></br>
</div>
@endsection