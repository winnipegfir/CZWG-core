@extends('layouts.master')
@section('title', 'You found an Easter Egg!')
@section('content')

<div class="container py-4">
    <h1><strong>Congrats! You've found an Easter Egg.</strong></h1>
        <p>We don't really have anything to give you as a gift, but please feel free to click on good ol' Bill below this text for a free whonking.</p>
    <hr>
        <img src="https://static.wikia.nocookie.net/animalcrossing/images/c/c1/Bill_NH.png" onclick="clickBill()">
    <br></br>
        <a href="{{route('index')}}" class="blue-text" style="font-size: 1.2em"> <i class="fas fa-arrow-left"></i> Take Me Home (Country Roads)</a>
</div>

<script>
function clickBill() {
  var txt;
  if (confirm("Whonk!")) {
  }
  document.getElementById("bill").innerHTML = txt;
}
</script>

@endsection
