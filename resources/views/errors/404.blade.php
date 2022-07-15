@extends('layouts.master')
@section('title', 'Uh oh!')

@section('content')
    <div class="container py-5">
        <h1 class="font-weight-bold blue-text"><i class="fa fa-cogs"></i> Error 404.</h1>
        <h4>Whatever you're looking for, we can't find it.</h4>
        <p>In the meantime, here's a quote from our <a href="https://quotes.winnipegfir.ca/">Quotes Website!</a></p>
        
        <div class="card">
          <div class="card-body">
          <h4 class="blue-text" id="quote"></h4>
          <p class="mb-0" id="name"></p>
          </div>
        </div>
    </div>

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