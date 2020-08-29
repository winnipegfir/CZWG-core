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
@if (Auth::user()->permissions <= 2)
            @foreach ($lessons as $lessons)
            <div class="card">
              <a href="{{route('cbt.module.view', [$lessons->cbt_modules_id, $lessons->lesson])}}">
              @if ($currentlesson != 'conclusion')

<!--TODO: Make Conclusion NOT CLICKABLE if all available lessons are not completed yet-->
              @if ($update->{$lessons->lesson} == 1)
              <text class="text-success">
              {{$lessons->name}}<br></a>
            </text>
            @else
            <text class="text-primary">
            {{$lessons->name}}<br></a>
          </text>
          @if ($update === 'conclusion')
            Conclusion
          @endif
              @endif
              @endif
            </div><br>
            @endforeach
            @endif
            @if (Auth::user()->permissions >= 3)
                        @foreach ($lessons as $lessons)
                        <div class="card">
                          <a href="{{route('cbt.module.view', [$lessons->cbt_modules_id, $lessons->lesson])}}">
                        
                        {{$lessons->name}}<br></a>

                      </text>
                        </div><br>

                        @endforeach
                        @endif
                        <br><br>
<br><br>    </div></div></div>
</nav>
