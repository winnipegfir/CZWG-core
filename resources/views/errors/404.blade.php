@extends('layouts.master')
@section('title', 'Uh oh!')

@section('content')
    <div class="container py-5">
        <h1 class="font-weight-bold blue-text"><i class="fa fa-cogs"></i> Error 404.</h1>
        <h4>Whatever you're looking for, we can't find it. Sorry, eh?</h4>
        <a href="{{route('index')}}" class="blue-text" style="font-size: 1.2em"> <i class="fas fa-arrow-left"></i> Back to the homepage</a>

<script>
fetch("https://quotes.winnipegfir.ca/api/quotes/random")
.then(function(response) {
  return response.json();
})
.then(function (quote) {
  document.getElementById("quote").innerHTML = quote.data.content.replace(/(?:\r\n|\r|\n)/g, '<br>'); 
  document.getElementById("name").innerHTML = "- " + (quote.data.name ?? "Anonymous");
})
.catch(function(error) {
  console.log(error);
});
</script>

@endsection