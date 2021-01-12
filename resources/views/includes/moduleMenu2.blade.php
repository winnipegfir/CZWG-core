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

<!--Controller Specific Module Menu-->
 
<div class="wrapper">
<nav class="sidebar">
    <div class="navbar-light bg-light">
      <div align="center">
        <div class="sidebar-header">
      <h3 style="padding-top: 7%;" class="font-weight-bold blue-text">Sections</h3></div>
<br>
          <div class="card p-2">
              <a href="{{route('cbt.module.view', [$intro->cbt_modules_id, $intro->lesson])}}">
                  {{$intro->name}} </a>
          </div><br>

            @foreach ($lessons as $lessons)
            <div class="card p-2">
              <a href="{{route('cbt.module.view', [$lessons->cbt_modules_id, $lessons->lesson])}}">
                  {{$lessons->name}} </a>
            </div><br>
            @endforeach
          <div class="card p-2">
              <a href="{{route('cbt.module.view', [$conclusion->cbt_modules_id, $conclusion->lesson])}}">
                  {{$conclusion->name}} </a>
          </div>
                        <br><br>
<br><br>    </div></div></div>
</nav>
