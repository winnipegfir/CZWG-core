@php
    $__bannerSettings = $__cs ?? \App\Models\Settings\CoreSettings::find(1);
    $__bannerThemes = ['winnipeg', 'prairie_gold', 'manitoba_sky', 'aurora', 'success', 'warning', 'urgent'];
    $__bannerAnimations = ['none', 'gold_swoop', 'shimmer', 'aurora', 'gentle_pulse'];
    $__bannerIcons = [
        'bullhorn' => 'fa-bullhorn',
        'star' => 'fa-star',
        'plane' => 'fa-plane',
        'info' => 'fa-info-circle',
        'calendar' => 'fa-calendar-alt',
    ];
    $__bannerTheme = in_array($__bannerSettings->bannerTheme ?? '', $__bannerThemes, true) ? $__bannerSettings->bannerTheme : 'winnipeg';
    $__bannerAnimation = in_array($__bannerSettings->bannerAnimation ?? '', $__bannerAnimations, true) ? $__bannerSettings->bannerAnimation : 'none';
    $__bannerIcon = $__bannerIcons[$__bannerSettings->bannerIcon ?? 'bullhorn'] ?? null;
    $__bannerLink = trim((string) ($__bannerSettings->bannerLink ?? ''));
    if ($__bannerLink !== '' && ! preg_match('~^(?:https?://|/(?!/))~i', $__bannerLink)) {
        $__bannerLink = '';
    }
@endphp

@if($__bannerSettings && ($__bannerSettings->bannerEnabled ?? true) && trim((string) $__bannerSettings->banner) !== '')
    <div class="site-announcement site-announcement--{{ $__bannerTheme }} site-announcement--anim-{{ $__bannerAnimation }}" role="status">
        <span class="site-announcement__effect" aria-hidden="true"></span>
        <div class="container site-announcement__inner">
            @if($__bannerIcon)<i class="fas {{ $__bannerIcon }} site-announcement__icon" aria-hidden="true"></i>@endif
            @if($__bannerLink)
                <a href="{{ $__bannerLink }}" @if($__bannerSettings->bannerOpenNewTab) target="_blank" rel="noopener" @endif>{{ $__bannerSettings->banner }}</a>
            @else
                <span>{{ $__bannerSettings->banner }}</span>
            @endif
        </div>
    </div>
@endif
