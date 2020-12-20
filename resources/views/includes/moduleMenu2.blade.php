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
      <h3>Sections</h3></div>
<br>
            @foreach ($lessons as $lessons)
            <div class="card">
              <a href="{{route('cbt.module.view', [$lessons->cbt_modules_id, $lessons->lesson])}}">
            </div><br>
            @endforeach
                        <br><br>
<br><br>    </div></div></div>
</nav>
