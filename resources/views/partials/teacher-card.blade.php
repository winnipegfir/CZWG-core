<div style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:0.5rem; padding:1.25rem; display:flex; align-items:flex-start; gap:1rem; height:100%;">
    <img src="{{ $t->user->avatar() }}"
         style="width:64px; height:64px; border-radius:50%; object-fit:cover; flex-shrink:0; border:2px solid #e9ecef;">
    <div style="flex:1; min-width:0;">
        <div style="font-weight:700; color:#122b44; font-size:1rem; line-height:1.2;">
            {{ $t->user->fullName('FL') }}
        </div>
        <a href="mailto:{{ $t->user->email }}" style="color:#6c757d; font-size:0.82rem; display:block; margin-bottom:0.5rem;">
            {{ $t->user->email }}
        </a>
        <div style="display:flex; flex-wrap:wrap; gap:0.35rem; align-items:center;">
            @if($t->is_local)
                <span style="background:#bbf7d0; color:#166534; font-size:0.72rem; font-weight:600; padding:0.2em 0.6em; border-radius:999px;">Local</span>
            @endif
            @if($t->is_radar)
                <span style="background:#4ade80; color:#14532d; font-size:0.72rem; font-weight:600; padding:0.2em 0.6em; border-radius:999px;">Radar</span>
            @endif
            @if($t->is_enroute)
                <span style="background:#15803d; color:#fff; font-size:0.72rem; font-weight:600; padding:0.2em 0.6em; border-radius:999px;">En-Route</span>
            @endif
            @if(Auth::check() && Auth::user()->permissions >= 4)
                <a href="{{ route('instructors.delete', [$t->id]) }}"
                   style="font-size:0.75rem; color:#dc3545; margin-left:0.25rem;"
                   onclick="return confirm('Remove this teacher?')">
                    <i class="fas fa-trash-alt fa-xs"></i> Remove
                </a>
            @endif
        </div>
    </div>
</div>
