<div style="background:#272727; border-bottom:1px solid #1a1a1a;">
    <div class="container">
        <div class="d-flex align-items-center" style="height:38px; gap:0.25rem;">
            <a href="{{ route('training.index') }}" style="color:#fff; font-weight:700; font-size:1rem; text-decoration:none; margin-right:0.75rem; white-space:nowrap;">
                Training
            </a>
            <nav class="d-flex align-items-center" style="gap:0.1rem; flex-wrap:wrap;">
                <a href="{{ route('training.index') }}"
                   style="color:{{ Request::is('dashboard/training') ? '#fff' : 'rgba(255,255,255,0.6)' }}; font-size:0.85rem; padding:0.3rem 0.65rem; border-radius:0.3rem; text-decoration:none; {{ Request::is('dashboard/training') ? 'background:rgba(255,255,255,0.12);' : '' }}">
                    Home
                </a>

                @if(Auth::user()->instructorProfile !== null || Auth::user()->permissions >= 4)
                <a href="{{ route('training.instructors') }}"
                   style="color:{{ Request::is('dashboard/training/instructors*') ? '#fff' : 'rgba(255,255,255,0.6)' }}; font-size:0.85rem; padding:0.3rem 0.65rem; border-radius:0.3rem; text-decoration:none; {{ Request::is('dashboard/training/instructors*') ? 'background:rgba(255,255,255,0.12);' : '' }}">
                    Instructors
                </a>

                @endif

                @if(Auth::user()->permissions >= 3 || Auth::user()->instructorProfile !== null)
                <div class="dropdown">
                    <a href="#" data-toggle="dropdown"
                       style="color:{{ Request::is('dashboard/training/students*') ? '#fff' : 'rgba(255,255,255,0.6)' }}; font-size:0.85rem; padding:0.3rem 0.65rem; border-radius:0.3rem; text-decoration:none; {{ Request::is('dashboard/training/students*') ? 'background:rgba(255,255,255,0.12);' : '' }}">
                        Students <i class="fas fa-chevron-down fa-xs ml-1"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" style="min-width:160px;">
                        <a class="dropdown-item {{ Request::is('dashboard/training/students/waitlist') ? 'active' : '' }}" href="{{ route('training.students.waitlist') }}">
                            Waitlist
                        </a>
                        <a class="dropdown-item {{ Request::is('dashboard/training/students/current') ? 'active' : '' }}" href="{{ route('training.students.current') }}">
                            Linked
                        </a>
                    </div>
                </div>
                @endif

                @if(Auth::user()->permissions >= 4)
                <a href="{{ route('training.reconcile') }}"
                   style="color:{{ Request::is('dashboard/training/reconcile') ? '#fff' : 'rgba(255,255,255,0.6)' }}; font-size:0.85rem; padding:0.3rem 0.65rem; border-radius:0.3rem; text-decoration:none; {{ Request::is('dashboard/training/reconcile') ? 'background:rgba(255,255,255,0.12);' : '' }}">
                    Reconcile
                </a>
                @endif
            </nav>
        </div>
    </div>
</div>
