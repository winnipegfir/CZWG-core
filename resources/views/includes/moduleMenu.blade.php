<!--CSS-->
<style>
.wrapper {
    display: flex;
    align-items: stretch;
}

#sidebar {
    min-width: 250px;
    max-width: 250px;
}

</style>
@if (Auth::user()->permissions < 3)
<!--Controller Specific Module Menu-->

<div class="wrapper">
<nav class="sidebar">
    <div class="navbar-light bg-light">
      <div align="center">
        <div class="sidebar-header">
      <h3>Modules</h3></div>
<br>
            @foreach ($modules as $modules)
            <div class="card">
              <a href="module/view/{{$modules->cbt_module_id}}/intro">
              {{$modules->cbtmodule->name}}<br>
              Click to Start!</a>
            </div><br>
            @endforeach
<br><br>

    </div></div></div>
</nav>
@endif

@if (Auth::user()->permissions >= 3)
<!--View All Module Menu-->

<div class="wrapper">
<nav class="sidebar">
    <div class="navbar-light bg-light">
      <div align="center">
        <div class="sidebar-header">
      <h3>All Modules</h3></div>
<br>
            @if (Auth::user()->permissions >= 4)
            <div class="card">
              <a href="#">Add New Module</a>
            </div>
            <hr>
            @endif
            @foreach ($modules as $modules)
            <div class="card">
              <a href="{{route('cbt.module.view.admin', $modules->id)}}">
              {{$modules->name}}<br>
              Click to View!</a>
            </div><br>
            @endforeach
<br><br>

    </div></div></div>
</nav>
@endif
