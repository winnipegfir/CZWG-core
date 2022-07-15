
  @if (Auth::user()->permissions <= 3)
<nav class="navbar navbar-light bg-light">
    <div class="container">
    <a href="/dashboard/training/cbt">
    <img src=https://winnipegfir.ca/storage/files/uploads/1613084163.png style="height:50px">
        </a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/CBT') ? 'active' : '' }}" href="{{route('cbt.index')}}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/CBT/modules*') || Request::is('dashboard/training/CBT/modules') ? 'active' : '' }}" href="{{route('cbt.module')}}">Modules</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/CBT/exams*') || Request::is('dashboard/training/CBT/exams') ? 'active' : '' }}" href="{{route('cbt.exam')}}">Exams</a>
            </li>
        </ul>
    </div>
</nav><br/>
  @endif
  @if (Auth::user()->permissions >= 4)
  <nav class="navbar navbar-light bg-light">
      <div class="container">
      <a href="/dashboard/training/cbt">
      <img src=https://winnipegfir.ca/storage/files/uploads/1612961386.png style="height:50px">
          </a>
          <ul class="nav nav-pills">
              <li class="nav-item">
                  <a class="nav-link {{ Request::is('dashboard/training/CBT') ? 'active' : '' }}" href="{{route('cbt.index')}}">Home</a>
              </li>
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Modules</a>
                  <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                  <a class="dropdown-item" href="{{route('cbt.module')}}">Your Modules</a>
                  <a class="dropdown-item" href="{{route('cbt.module.admin')}}">Modules Admin</a>
              </li>
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Exams</a>
                  <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                  <a class="dropdown-item" href="{{route('cbt.exam')}}">Your Exams</a>
                  <a class="dropdown-item" href="{{route('cbt.exam.adminview')}}">Exam Admin</a>
              </li>
              @if (Auth::user()->permissions >= 4)
              <li class="nav-item">
                <a class="nav-link {{ Request::is('training.index') ? 'active' : '' }}" href="{{route('training.index')}}">Instructor Dashboard</a>
              </li>
              @endif
          </ul>
      </div>
  </nav><br/>
@endif
{{-- <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Dropdown</a>
    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
    <a class="dropdown-item" href="#">Action</a>
    <a class="dropdown-item" href="#">Another action</a>
    <a class="dropdown-item" href="#">Something else here</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="#">Separated link</a>
    </div>
</li> --}}
