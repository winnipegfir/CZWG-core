@extends('layouts.master')
@section('description', 'Welcome to Winnipeg - located in the heart of Canada on the VATSIM network.')


@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('/css/home.css') }}?v=8" />

    {{-- Hero --}}
    <div class="winnipeg-blue">
        <div id="hero-panel" style="height: calc(100vh - 113px); position:relative; overflow:hidden;">
            {{-- Parallax background --}}
            <div data-jarallax data-speed="0.2" class="jarallax" style="position:absolute; top:0; left:0; right:0; bottom:0; background-image: url({{$background->url}}); {{$background->css}}"></div>
            {{-- Gradient overlay: dark left+bottom, open top-right --}}
            <div style="position:absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(to right, rgba(10,24,40,0.75) 0%, rgba(10,24,40,0.4) 55%, rgba(10,24,40,0.1) 100%); pointer-events:none;"></div>
            <div style="position:absolute; top:0; left:0; right:0; bottom:0; background: linear-gradient(to top, rgba(10,24,40,0.5) 0%, transparent 40%); pointer-events:none;"></div>
            {{-- Text content --}}
            <div style="position:absolute; top:0; left:0; right:0; bottom:0; display:flex; flex-direction:column; justify-content:center; padding-bottom:3rem;">
                <div class="container">
                    <p style="color:rgba(255,255,255,0.6); font-size:0.8rem; margin-bottom:0.4rem; letter-spacing:0.5px; text-shadow:0 1px 4px rgba(0,0,0,0.5);">
                        <i class="fas fa-camera fa-xs"></i>&nbsp; {{$background->credit}}
                    </p>
                    <h1 style="font-size:clamp(2.5rem, 6vw, 5.5rem); color:#fff; font-weight:800; line-height:1.05; margin-bottom:0.5rem; text-shadow:0 1px 6px rgba(0,0,0,0.25);">
                        <span id="hero-prefix">We Are</span> <span id="hero-word" style="color:#122b44; font-weight:900; display:inline-block; transition:opacity 0.18s ease, transform 0.18s ease;"><span id="hero-word-text" style="opacity:0;">Winnipeg</span>.</span>
                    </h1>
                    <style>
                    #hero-word.flip-out { opacity:0; transform:translateY(-6px); }
                    #hero-word.flip-in  { opacity:0; transform:translateY(6px); }
                    #hero-prefix { transition: opacity 0.18s ease; }
                    #hero-word {
                        position: relative;
                        isolation: isolate;
                    }
                    #hero-word::before {
                        content: '';
                        position: absolute;
                        inset: -15px -25px;
                        background: white;
                        border-radius: 55% 45% 38% 62% / 48% 62% 38% 52%;
                        filter: blur(18px);
                        opacity: 0.3;
                        z-index: -1;
                    }
                    </style>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var words = ['Brandon', 'Portage', 'Steinbach', 'Selkirk', 'Thompson', 'Flin Flon', 'Dauphin', 'Morden', 'Winkler', 'The Pas', 'Saskatoon', 'Regina', 'Moose Jaw', 'Swift Current', 'Prince Albert', 'Yorkton', 'North Battleford', 'Estevan', 'Weyburn', 'Thunder Bay', 'Lloydminster'];
                        var el = document.getElementById('hero-word');
                        var textEl = document.getElementById('hero-word-text');
                        var prefixEl = document.getElementById('hero-prefix');

                        @auth
                        var easterEgg = Math.random() < 0.01;
                        @endauth
                        @guest
                        var easterEgg = false;
                        @endguest

                        var first = easterEgg
                            ? '{{ Auth::check() ? e(Auth::user()->display_fname) : "" }}'
                            : words[Math.floor(Math.random() * words.length)];

                        if (easterEgg) prefixEl.textContent = 'You Are';

                        textEl.textContent = first;
                        textEl.style.opacity = '1';

                        function flipTo(word, prefix) {
                            el.classList.add('flip-out');
                            if (prefix !== undefined) prefixEl.style.opacity = '0';
                            setTimeout(function() {
                                if (prefix !== undefined) {
                                    prefixEl.textContent = prefix;
                                    prefixEl.style.opacity = '';
                                }
                                textEl.textContent = word;
                                el.classList.remove('flip-out');
                                el.classList.add('flip-in');
                                el.offsetHeight;
                                el.classList.remove('flip-in');
                            }, 180);
                        }

                        setTimeout(function() { flipTo('Winnipeg', 'We Are'); }, easterEgg ? 500 : 1200);
                    });
                    </script>
                    <a href="#mid" style="display:inline-flex; align-items:center; gap:0.5rem; color:rgba(255,255,255,0.85); font-size:1rem; text-decoration:none; padding-bottom:2px;">
                        Explore the heart of Canada&nbsp;<i class="fas fa-arrow-down fa-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Build strip data --}}
    @php
    $stripSections = [];

    // Live events
    $lItems = [];
    foreach ($liveEvents as $e) {
        $lItems[] = ['text' => $e->name, 'cat' => null, 'url' => url('/events/' . $e->slug)];
    }
    if (!empty($lItems)) {
        $stripSections[] = ['key' => 'live', 'icon' => 'fa-circle', 'label' => 'LIVE NOW', 'items' => $lItems];
    }

    // Weather
    $wItems = [];
    foreach ($weather as $w) {
        $wItems[] = ['text' => $w->raw_text ?? $w->icao, 'cat' => $w->flight_category ?? null];
    }
    if (!empty($wItems)) {
        $stripSections[] = ['key' => 'weather', 'icon' => 'fa-cloud-sun', 'label' => 'WEATHER', 'items' => $wItems];
    }

    // Online controllers
    $facilityNames = [
        'WPG'  => 'CYWG', 'CYWG' => 'CYWG',
        'CZWG' => 'CYWG', 'ZWG'  => 'CYWG',
        'CYPG' => 'CYPG', 'CYAV' => 'CYAV',
        'CYXE' => 'CYXE', 'CYQR' => 'CYQR',
        'CYQT' => 'CYQT', 'CYMJ' => 'CYMJ',
        'CYFO' => 'CYFO',
    ];
    $cItems = [];
    foreach ($finalPositions as $p) {
        $parts    = explode('_', $p->callsign);
        $prefix   = $parts[0] ?? '';
        $posType  = $parts[1] ?? '';
        $facility = $facilityNames[$prefix] ?? $prefix;
        $name     = ($p->name != $p->cid) ? $p->name : (string)$p->cid;
        $cItems[] = [
            'text'     => $name . ' · ' . $p->callsign . ' · ' . $p->frequency,
            'cat'      => null,
            'facility' => $facility,
            'pos'      => $posType,
            'name'     => $name,
            'freq'     => $p->frequency,
        ];
    }
    if (empty($cItems)) $cItems = [['text' => 'No controllers currently online', 'cat' => null]];
    $stripSections[] = ['key' => 'online', 'icon' => 'fa-headset', 'label' => 'ONLINE NOW', 'items' => $cItems];

    // Events
    $eItems = [];
    foreach ($nextEvents as $e) {
        $eItems[] = ['text' => $e->name . ' · ' . $e->start_timestamp_pretty(), 'cat' => null, 'url' => url('/events/' . $e->slug)];
    }
    if (empty($eItems)) $eItems = [['text' => 'No upcoming events scheduled', 'cat' => null, 'url' => null]];
    $stripSections[] = ['key' => 'events', 'icon' => 'fa-calendar', 'label' => 'EVENTS', 'items' => $eItems];

    // News
    $nItems = [];
    foreach ($news as $n) {
        $nItems[] = ['text' => $n->title . ' · ' . $n->posted_on_pretty(), 'cat' => null, 'url' => url('/news/' . $n->slug)];
    }
    if (!empty($nItems)) {
        $stripSections[] = ['key' => 'news', 'icon' => 'fa-newspaper', 'label' => 'NEWS', 'items' => $nItems];
    }

    // Top controllers
    $tItems = [];
    foreach ($topControllersArray as $i => $t) {
        $u = User::where('id', $t['cid'])->first();
        if ($u) $tItems[] = ['text' => ($i + 1) . '. ' . $u->fullName('FLC') . ' · ' . $t['time'], 'cat' => null];
    }
    if (!empty($tItems)) {
        $stripSections[] = ['key' => 'top', 'icon' => 'fa-award', 'label' => 'TOP THIS QUARTER', 'items' => $tItems];
    }
    @endphp

    {{-- Scroll anchor --}}
    <div id="mid" style="height:0;overflow:hidden;"></div>

    {{-- Info strip — sticky so it stops naturally at the footer --}}
    <div id="info-strip-wrap">
        <div id="info-strip">
            <div class="container info-strip-inner">
                <div id="is-badge">
                    <i class="fas" id="is-icon"></i>
                    <span id="is-label"></span>
                </div>
                <div class="is-sep"></div>
                <div id="is-content">
                    <span class="is-cat-dot" id="is-dot"></span>
                    <a id="is-text"></a>
                </div>
                <div id="is-dots"></div>
                <button id="is-expand" aria-label="Expand section">
                    <i class="fas fa-chevron-down" id="is-chevron" style="transform:rotate(180deg);transition:transform 0.25s ease;"></i>
                </button>
            </div>
        </div>
        <div id="is-panel">
            <div class="container info-strip-inner" style="height:auto; padding-top:0.6rem; padding-bottom:0.6rem;">
                <ul id="is-panel-list"></ul>
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

    <script>
    (function () {
        var sections = @json($stripSections);
        var ITEM_MS  = 4500;
        var catColors = { VFR: '#22c55e', MVFR: '#60a5fa', IFR: '#f87171', LIFR: '#c084fc' };

        var elBadge   = document.getElementById('is-badge');
        var elIcon    = document.getElementById('is-icon');
        var elLabel   = document.getElementById('is-label');
        var elContent = document.getElementById('is-content');
        var elDot     = document.getElementById('is-dot');
        var elText    = document.getElementById('is-text');
        var elDots      = document.getElementById('is-dots');
        var elExpand    = document.getElementById('is-expand');
        var elChevron   = document.getElementById('is-chevron');
        var elPanel     = document.getElementById('is-panel');
        var elPanelList = document.getElementById('is-panel-list');

        if (!sections.length) return;

        sections.forEach(function (s, i) {
            var btn = document.createElement('button');
            btn.className = 'is-nav-dot';
            btn.addEventListener('click', function () { jumpTo(i, 0); });
            elDots.appendChild(btn);
        });

        var sIdx = 0, iIdx = 0, timer = null, expanded = false;

        var posOrder = ['DEL','GND','TWR','APP','DEP','CTR','FSS','OBS'];

        function buildOnlineGrid(items) {
            var byFacility = {};
            items.forEach(function (item) {
                if (item.text === 'No controllers currently online') return;
                var f = item.facility || 'Other';
                if (!byFacility[f]) byFacility[f] = [];
                byFacility[f].push(item);
            });
            elPanelList.innerHTML = '';
            if (Object.keys(byFacility).length === 0) {
                var li = document.createElement('li');
                li.className = 'is-panel-item';
                li.textContent = 'No controllers currently online';
                elPanelList.appendChild(li);
                return;
            }
            var grid = document.createElement('div');
            grid.className = 'is-panel-grid';
            Object.keys(byFacility).sort().forEach(function (fac) {
                var controllers = byFacility[fac].slice().sort(function (a, b) {
                    return (posOrder.indexOf(a.pos) === -1 ? 99 : posOrder.indexOf(a.pos)) -
                           (posOrder.indexOf(b.pos) === -1 ? 99 : posOrder.indexOf(b.pos));
                });
                var card = document.createElement('div');
                card.className = 'is-facility-card';
                var header = document.createElement('div');
                header.className = 'is-facility-name';
                header.textContent = fac;
                card.appendChild(header);
                controllers.forEach(function (c) {
                    var row = document.createElement('div');
                    row.className = 'is-position-row';
                    var tag = document.createElement('span');
                    tag.className = 'is-pos-tag is-pos-tag--' + (c.pos || 'other').toLowerCase();
                    tag.textContent = c.pos || '—';
                    var name = document.createElement('span');
                    name.className = 'is-pos-name';
                    name.textContent = c.name;
                    var freq = document.createElement('span');
                    freq.className = 'is-pos-freq';
                    freq.textContent = c.freq;
                    row.appendChild(tag);
                    row.appendChild(name);
                    row.appendChild(freq);
                    card.appendChild(row);
                });
                grid.appendChild(card);
            });
            elPanelList.appendChild(grid);
        }

        function buildPanel() {
            var sec = sections[sIdx];
            elPanelList.innerHTML = '';
            if (sec.key === 'online') { buildOnlineGrid(sec.items); return; }
            var items = sec.items;
            items.forEach(function (item) {
                var li = document.createElement('li');
                li.className = 'is-panel-item';
                if (item.cat && catColors[item.cat]) {
                    var pill = document.createElement('span');
                    pill.className = 'is-panel-cat';
                    pill.textContent = item.cat;
                    pill.style.background = catColors[item.cat];
                    li.appendChild(pill);
                }
                var node = item.url ? document.createElement('a') : document.createElement('span');
                if (item.url) { node.href = item.url; node.className = 'is-panel-link'; }
                node.textContent = item.text;
                li.appendChild(node);
                elPanelList.appendChild(li);
            });
        }

        elExpand.addEventListener('click', function () {
            expanded = !expanded;
            if (expanded) { buildPanel(); }
            elPanel.classList.toggle('open', expanded);
            elChevron.style.transform = expanded ? '' : 'rotate(180deg)';
        });

        function updateDots() {
            elDots.querySelectorAll('.is-nav-dot').forEach(function (d, i) {
                d.classList.toggle('active', i === sIdx);
            });
        }

        function applyItem(si, ii) {
            sIdx = si; iIdx = ii;
            var sec  = sections[si];
            var item = sec.items[ii];
            elIcon.className  = 'fas ' + sec.icon;
            elLabel.textContent = sec.label;
            document.getElementById('info-strip').classList.toggle('is-live', sec.key === 'live');
            elText.textContent = item.text;
            if (item.url) {
                elText.href = item.url;
                elText.style.cursor = 'pointer';
                elText.style.textDecoration = 'underline';
                elText.style.textUnderlineOffset = '3px';
            } else {
                elText.removeAttribute('href');
                elText.style.cursor = 'default';
                elText.style.textDecoration = 'none';
            }
            var color = item.cat && catColors[item.cat];
            if (color) {
                elDot.style.background = color;
                elDot.textContent = item.cat;
                elDot.style.display = 'inline-block';
            } else {
                elDot.style.display = 'none';
            }
            updateDots();
            if (expanded) buildPanel();
        }

        function show(si, ii, animate) {
            if (!animate) { applyItem(si, ii); return; }
            var sectionChanging = si !== sIdx;
            if (sectionChanging) elBadge.classList.add('is-fade-out');
            elContent.classList.add('is-fade-out');
            setTimeout(function () {
                applyItem(si, ii);
                if (sectionChanging) elBadge.classList.remove('is-fade-out');
                elContent.classList.remove('is-fade-out');
            }, 200);
        }

        function advance() {
            var next = iIdx + 1;
            if (next >= sections[sIdx].items.length) {
                show((sIdx + 1) % sections.length, 0, true);
            } else {
                show(sIdx, next, true);
            }
        }

        function jumpTo(si, ii) {
            clearInterval(timer);
            show(si, ii, true);
            timer = setInterval(advance, ITEM_MS);
        }

        show(0, 0, false);
        timer = setInterval(advance, ITEM_MS);
    })();
    </script>
@stop
