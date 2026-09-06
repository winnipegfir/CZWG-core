@php
    $currentModuleId = isset($module) ? $module->id : null;
    $viewedIds = isset($viewedModuleIds) ? $viewedModuleIds : collect();
@endphp
<aside class="academy-course-sidebar" data-academy-course-sidebar>
    <div class="academy-course-sidebar-head">
        <div class="academy-kicker">Course navigation</div>
        <button type="button" class="academy-sidebar-collapse" data-academy-sidebar-toggle aria-label="Collapse course navigation" title="Collapse course navigation">
            <i class="fas fa-angle-double-left"></i>
        </button>
    </div>
    <div class="academy-course-sidebar-title-wrap">
        <a href="{{ route('academy.courses.show', $course->slug) }}" class="academy-course-sidebar-title">{{ $course->title }}</a>
        @if(isset($courseProgress))
            <div class="academy-course-sidebar-progress">
                <span>{{ $courseProgress['viewed'] }}/{{ $courseProgress['total'] }} viewed</span>
                <span class="academy-progress academy-progress-{{ $courseProgress['status'] }}">{{ ucwords(str_replace('_',' ',$courseProgress['status'])) }}</span>
            </div>
        @endif
    </div>

    <nav class="academy-course-nav" aria-label="{{ $course->title }} modules">
        @foreach($course->modules as $courseModule)
            @php
                $isCurrent = $currentModuleId === $courseModule->id;
                $isViewed = $viewedIds->contains($courseModule->id);
                $isAssessment = in_array($courseModule->slug, ['final-self-assessment', 'final-knowledge-check']) || ($courseModule->quiz && $courseModule->quiz->published);
            @endphp
            <a href="{{ route('academy.modules.show', [$course->slug, $courseModule->slug]) }}"
               class="academy-course-nav-item {{ $isCurrent ? 'active' : '' }}"
               title="{{ $courseModule->title }}">
                <span class="academy-course-nav-icon">
                    @if($isViewed)
                        <i class="fas fa-check-circle text-success"></i>
                    @elseif($isAssessment)
                        <i class="fas fa-clipboard-check"></i>
                    @else
                        <i class="far fa-circle"></i>
                    @endif
                </span>
                <span class="academy-course-nav-copy">
                    <span class="academy-course-nav-label">{{ $courseModule->title }}</span>
                    @if(($courseModule->slide_count ?? 0) > 0 && !$isAssessment)
                        <small>{{ $courseModule->slide_count }} slides</small>
                    @elseif($isAssessment)
                        <small>Self Assessment</small>
                    @endif
                </span>
                @if($isCurrent)<i class="fas fa-chevron-right academy-course-nav-current"></i>@endif
            </a>
        @endforeach
    </nav>
</aside>
<button type="button" class="academy-sidebar-mobile-toggle btn btn-primary" data-academy-sidebar-mobile-toggle>
    <i class="fas fa-list mr-1"></i> Modules
</button>
