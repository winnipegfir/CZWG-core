<!--CSS-->
<style>
.wrapper {
    display: flex;
    align-items: stretch;
}

#sidebar {
    min-width: 250px;
    max-width: 150px;
}

</style>

<div class="wrapper">
<nav class="sidebar">
    <div class="navbar-light bg-light">
      <div align="center" style="max-width: 150px;">
        <div class="sidebar-header">
          <h3 style="padding-top: 7%" class="font-weight-bold blue-text">Available Modules</h3></div>
          <br>
          @foreach ($modules as $modules)
          <div class="card p-2">
            <h5 style="margin-bottom: 5%" class="font-weight-bold">
            {{$modules->cbtmodule->name}}
                        @if($modules->completed_at != null)
            <i style="color: green" class="fas fa-check"></i>
            @endif
            </h5>
            <a class="btn-sm btn-success" href="module/view/{{$modules->cbt_module_id}}/intro">Click to Start</a>
          </div>
          <br>
          @endforeach
<br><br>

    </div></div></div>
</nav>
