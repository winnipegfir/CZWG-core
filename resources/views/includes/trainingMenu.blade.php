
<nav class="navbar navbar-light bg-light">
    <div class="container">
    <a href="/dashboard/training">
        <img src=https://site-dev.winnipegfir.ca/images/uploads/1594001053.png style="width:150px;height:72px;">
    </a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training') ? 'active' : '' }}" href="{{route('training.index')}}">Home</a>
            </li>
            @if (Auth::user()->instructorProfile !== null || Auth::user()->permissions >= 4)
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/instructors/*') || Request::is('dashboard/training/instructors') ? 'active' : '' }}" href="{{route('training.instructors')}}">Instructors</a>
            </li>
            <li>
                <a class="nav-link {{Request::is(route('training.instructingsessions.index')) ? 'active' : ''}}" href="{{route('training.instructingsessions.index')}}">Instructing Sessions</a>
            </li>
            @if (Auth::user()->permissions >= 4)
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ Request::is('dashboard/training/students/*') || Request::is('dashboard/training/students') ? 'active' : '' }}" style="text-color:white" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Students</a>
                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                <a class="dropdown-item {{ Request::is('dashboard/training/students/current') ? 'active' : '' }}" href="{{route('training.students.current')}}">In Progress</a>
                <a class="dropdown-item {{ Request::is('dashboard/training/students/completed') ? 'active' : '' }}" href="{{route('training.students.completed')}}">Completed</a>
                <a class="dropdown-item {{ Request::is('dashboard/training/students/new') ? 'active' : '' }}" href="{{route('training.students.new')}}">New/Waiting</a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard/training/applications/*') || Request::is('dashboard/training/applications') ? 'active' : '' }}" href="{{route('training.applications')}}">
                    Applications
                    @if (count(\App\Models\AtcTraining\Application::where('status', 0)->get()) >= 1)
                        <span class="badge-pill {{ Request::is('dashboard/training/applications/*') || Request::is('dashboard/training/applications') ? 'badge-light text-primary' : 'badge-primary' }}">{{count(\App\Models\AtcTraining\Application::where('status', 0)->get())}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item dropdown">
<a class="nav-link {{ Request::is('dashboard/training/cbt') ? 'active' : '' }}" href="{{route('cbt.index')}}">CBT System</a>
              @endif
            @endif
        </ul>
    </div>
</nav><br/>

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
