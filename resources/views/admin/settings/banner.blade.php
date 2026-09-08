@extends('layouts.master')
@section('title', 'Banner Appearance - Winnipeg FIR')
@section('content')
<style>
.banner-settings-hero{background:linear-gradient(135deg,#081827,#122b44);color:#fff;padding:2.4rem 0}.banner-settings-hero h1{font-weight:800;margin:.35rem 0}.banner-settings-back{color:rgba(255,255,255,.62)!important}.banner-settings-back:hover{color:#fff!important;text-decoration:none}.banner-settings-page{background:#f5f7fa;min-height:70vh;padding:2rem 0 4rem}.banner-settings-card{background:#fff;border:1px solid #e3e8ef;border-radius:12px;padding:1.4rem;margin-bottom:1rem}.banner-settings-card h5{font-weight:800;color:#122b44}.banner-preview-shell{border:1px solid #dfe5eb;border-radius:10px;overflow:hidden;background:#122b44}.banner-preview-nav{height:58px;display:flex;align-items:center;padding:0 1.25rem;color:rgba(255,255,255,.72);font-size:.78rem}.banner-preview-nav strong{color:#fff;margin-right:auto}.banner-preview-nav span{margin-left:1.1rem}.banner-theme-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.65rem}.banner-choice{position:relative}.banner-choice input{position:absolute;opacity:0}.banner-choice label{display:block;border:2px solid #e0e5eb;border-radius:10px;padding:.8rem;cursor:pointer;margin:0;transition:.15s}.banner-choice input:checked+label{border-color:#3977a6;box-shadow:0 0 0 3px rgba(57,119,166,.12)}.banner-swatch{height:28px;border-radius:6px;margin-bottom:.5rem}.banner-choice-title{font-weight:700;font-size:.82rem}.banner-choice-note{font-size:.7rem;color:#74808d}.swatch-winnipeg{background:linear-gradient(110deg,#081827,#122b44 58%,#173b5c)}.swatch-prairie_gold{background:linear-gradient(105deg,#b68022,#e0b85d 52%,#c89535)}.swatch-manitoba_sky{background:linear-gradient(110deg,#176899,#2f86bd 55%,#70b9df)}.swatch-aurora{background:linear-gradient(110deg,#071b2c,#0e625e 50%,#173c66)}.swatch-success{background:#17623a}.swatch-warning{background:#f2c45d}.swatch-urgent{background:#a8323c}html[data-theme="dark"] .banner-settings-page{background:#16181d;color:#e4e7eb}html[data-theme="dark"] .banner-settings-card{background:#20242b;border-color:#303640}html[data-theme="dark"] .banner-settings-card h5{color:#edf0f3}html[data-theme="dark"] .banner-choice label{border-color:#3a424d;background:#1a1e24}html[data-theme="dark"] .banner-choice input:checked+label{border-color:#70a9d3}@media(max-width:575px){.banner-theme-grid{grid-template-columns:1fr}}
</style>

<div class="banner-settings-hero"><div class="container"><a href="{{ route('settings.index') }}" class="banner-settings-back"><i class="fas fa-arrow-left"></i> Settings</a><h1>Banner Appearance</h1><p class="mb-0" style="color:rgba(255,255,255,.65)">Control the announcement strip displayed above the website navigation.</p></div></div>

<div class="banner-settings-page"><div class="container">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><strong>Please check the banner settings.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form method="POST" action="{{ route('settings.banner.edit') }}">@csrf
        <div class="banner-settings-card">
            <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="mb-1">Live preview</h5><small class="text-muted">Changes below update this preview immediately. Click Save to publish them.</small></div><div class="custom-control custom-switch"><input class="custom-control-input" type="checkbox" name="bannerEnabled" value="1" id="bannerEnabled" {{ old('bannerEnabled', $banner->bannerEnabled ?? true) ? 'checked' : '' }}><label class="custom-control-label" for="bannerEnabled">Show banner</label></div></div>
            <div class="banner-preview-shell">
                <div id="bannerPreview" class="site-announcement site-announcement--{{ old('bannerTheme', $banner->bannerTheme ?? 'winnipeg') }} site-announcement--anim-{{ old('bannerAnimation', $banner->bannerAnimation ?? 'gold_swoop') }}">
                    <span class="site-announcement__effect" aria-hidden="true"></span>
                    <div class="container site-announcement__inner"><i id="bannerPreviewIcon" class="fas fa-bullhorn site-announcement__icon"></i><span id="bannerPreviewText">{{ old('bannerMessage', $banner->banner ?: 'We’re hiring! Join the Winnipeg FIR team.') }}</span></div>
                </div>
                <div class="banner-preview-nav"><strong>WINNIPEG FIR</strong><span>News</span><span>Events</span><span>ATC</span><span>Academy</span></div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7"><div class="banner-settings-card h-100">
                <h5>Banner content</h5>
                <div class="form-group"><label for="bannerMessage">Message</label><input id="bannerMessage" name="bannerMessage" class="form-control" maxlength="240" value="{{ old('bannerMessage', $banner->banner) }}" placeholder="We’re hiring! Join the Winnipeg FIR team."><small class="form-text text-muted">Maximum 240 characters.</small></div>
                <div class="form-group"><label for="bannerLink">Optional link</label><input id="bannerLink" name="bannerLink" class="form-control" maxlength="2048" value="{{ old('bannerLink', $banner->bannerLink) }}" placeholder="/join or https://example.com"><small class="form-text text-muted">Use a website path beginning with / or a complete HTTPS link.</small></div>
                <div class="custom-control custom-checkbox mb-3"><input type="checkbox" class="custom-control-input" name="bannerOpenNewTab" value="1" id="bannerOpenNewTab" {{ old('bannerOpenNewTab', $banner->bannerOpenNewTab ?? false) ? 'checked' : '' }}><label class="custom-control-label" for="bannerOpenNewTab">Open link in a new tab</label></div>
                <div class="form-row"><div class="form-group col-md-6"><label for="bannerIcon">Icon</label><select id="bannerIcon" name="bannerIcon" class="form-control">@foreach(['bullhorn'=>'Bullhorn','star'=>'Star','plane'=>'Aircraft','info'=>'Information','calendar'=>'Calendar','none'=>'No icon'] as $value=>$label)<option value="{{ $value }}" {{ old('bannerIcon', $banner->bannerIcon ?? 'bullhorn') === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div><div class="form-group col-md-6"><label for="bannerAnimation">Animation</label><select id="bannerAnimation" name="bannerAnimation" class="form-control">@foreach(['none'=>'None','gold_swoop'=>'Winnipeg Colour Swoosh — every 8 seconds','shimmer'=>'Soft Shimmer','aurora'=>'Northern Lights','gentle_pulse'=>'Gentle Pulse'] as $value=>$label)<option value="{{ $value }}" {{ old('bannerAnimation', $banner->bannerAnimation ?? 'gold_swoop') === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select><small class="form-text text-muted">The Winnipeg swoosh sends a soft gold-and-sky highlight across the banner without moving its message.</small></div></div>
            </div></div>

            <div class="col-lg-5"><div class="banner-settings-card h-100"><h5>Winnipeg palette</h5><p class="text-muted small">Choose the colour treatment for the banner.</p><div class="banner-theme-grid">
                @foreach(['winnipeg'=>['Winnipeg Navy','Navy with gold accent'],'prairie_gold'=>['Prairie Gold','Warm gold and navy'],'manitoba_sky'=>['Manitoba Sky','Clear blue gradient'],'aurora'=>['Northern Lights','Teal and midnight blue'],'success'=>['Success','Positive green'],'warning'=>['Notice','High-visibility gold'],'urgent'=>['Urgent','Important red']] as $value=>$details)
                    <div class="banner-choice"><input type="radio" name="bannerTheme" value="{{ $value }}" id="theme_{{ $value }}" {{ old('bannerTheme', $banner->bannerTheme ?? 'winnipeg') === $value ? 'checked' : '' }}><label for="theme_{{ $value }}"><span class="banner-swatch swatch-{{ $value }} d-block"></span><span class="banner-choice-title d-block">{{ $details[0] }}</span><span class="banner-choice-note">{{ $details[1] }}</span></label></div>
                @endforeach
            </div></div></div>
        </div>

        <div class="d-flex justify-content-end mt-3"><button class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Save Banner Appearance</button></div>
    </form>
</div></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var preview = document.getElementById('bannerPreview');
    var message = document.getElementById('bannerMessage');
    var animation = document.getElementById('bannerAnimation');
    var icon = document.getElementById('bannerIcon');
    var enabled = document.getElementById('bannerEnabled');
    var iconClasses = {bullhorn:'fa-bullhorn',star:'fa-star',plane:'fa-plane',info:'fa-info-circle',calendar:'fa-calendar-alt'};
    function refreshPreview() {
        var theme = document.querySelector('input[name="bannerTheme"]:checked');
        preview.className = 'site-announcement site-announcement--' + (theme ? theme.value : 'winnipeg') + ' site-announcement--anim-' + animation.value;
        preview.style.display = enabled.checked ? '' : 'none';
        document.getElementById('bannerPreviewText').textContent = message.value || 'Your announcement will appear here.';
        var iconElement = document.getElementById('bannerPreviewIcon');
        iconElement.className = iconClasses[icon.value] ? 'fas ' + iconClasses[icon.value] + ' site-announcement__icon' : '';
        iconElement.style.display = iconClasses[icon.value] ? '' : 'none';
        message.required = enabled.checked;
    }
    document.querySelectorAll('input[name="bannerTheme"], #bannerAnimation, #bannerIcon, #bannerEnabled, #bannerMessage').forEach(function (field) {
        field.addEventListener(field.type === 'text' ? 'input' : 'change', refreshPreview);
    });
    refreshPreview();
});
</script>
@stop
