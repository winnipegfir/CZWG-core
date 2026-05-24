@extends('layouts.master')

@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}?v=4" />

    <div class="winnipeg-blue">
        <div id="hero-panel" style="height: calc(100vh - 113px); position:relative; overflow:hidden;">
            <div data-jarallax data-speed="0.2" class="jarallax" style="position:absolute; top:0; left:0; right:0; bottom:0; background-image: url({{$image->url}}); {{$image->css}}"></div>
            <div style="position:absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(to right, rgba(10,24,40,0.75) 0%, rgba(10,24,40,0.4) 55%, rgba(10,24,40,0.1) 100%); pointer-events:none;"></div>
            <div style="position:absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(to top, rgba(10,24,40,0.5) 0%, transparent 40%); pointer-events:none;"></div>
            <div style="position:absolute; top:0; left:0; right:0; bottom:0; display:flex; flex-direction:column; justify-content:center; padding-bottom:3rem;">
                <div class="container">
                    <p style="color:rgba(255,255,255,0.6); font-size:0.8rem; margin-bottom:0.4rem; letter-spacing:0.5px; text-shadow:0 1px 4px rgba(0,0,0,0.5);">
                        <i class="fas fa-camera fa-xs"></i>&nbsp; {{$image->credit}}
                    </p>
                    <h1 style="font-size:clamp(2.5rem, 6vw, 5.5rem); color:#fff; font-weight:800; line-height:1.05; margin-bottom:0.5rem; text-shadow:0 1px 6px rgba(0,0,0,0.25);">
                        We Are <span style="color:#122b44; font-weight:900;">Winnipeg.</span>
                    </h1>
                    <div style="display:inline-flex; gap:1rem; align-items:center; margin-top:0.5rem;">
                        <a href="{{ url('admin/settings/images') }}" style="display:inline-flex; align-items:center; gap:0.4rem; color:rgba(255,255,255,0.7); font-size:0.9rem; text-decoration:none;">
                            <i class="fas fa-arrow-left fa-xs"></i>&nbsp;Back to Images
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="info-strip-wrap">
        <div id="info-strip">
            <div class="info-strip-inner">
                <div id="is-badge" style="display:flex; align-items:center; gap:0.45rem; background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.95); padding:0.28rem 0.8rem; border-radius:3px; font-size:0.67rem; font-weight:700; letter-spacing:0.11em; text-transform:uppercase; white-space:nowrap; flex-shrink:0;">
                    <i class="fas fa-image"></i>
                    <span>PREVIEW MODE</span>
                </div>
                <div class="is-sep"></div>
                <div id="is-content" style="flex:1; color:rgba(255,255,255,0.78); font-size:0.875rem;">
                    This is how this image will appear on the homepage.
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        var STRIP_H = 54;
        function sizeHero() {
            var hero = document.getElementById('hero-panel');
            var content = document.getElementById('czqoContent');
            if (!hero || !content) return;
            var topOffset = content.getBoundingClientRect().top;
            hero.style.height = 'calc(100vh - ' + topOffset + 'px - ' + STRIP_H + 'px)';
        }
        document.addEventListener('DOMContentLoaded', sizeHero);
        window.addEventListener('resize', sizeHero);
        sizeHero();
    })();
    </script>
@stop
