@extends('layouts.master')
@section('title', 'Airports - Winnipeg FIR')
@section('description', 'Winnipeg FIR\'s airports, weather, and scenery')

@section('content')
<style>
.airports-wrap {
    display: flex;
    background: #f6f8fa;
}

/* ── Sidebar ─────────────────────────────────── */
.airport-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: #fff;
    border-right: 1px solid rgba(0,0,0,0.08);
    padding: 1.5rem 0;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
}
.sidebar-province {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(0,0,0,0.35);
    padding: 1rem 1.25rem 0.35rem;
    margin-top: 0.25rem;
}
.sidebar-province:first-child { margin-top: 0; padding-top: 0.25rem; }
.sidebar-link {
    display: flex;
    align-items: baseline;
    gap: 0.55rem;
    padding: 0.5rem 1.25rem;
    font-size: 0.875rem;
    color: rgba(0,0,0,0.65);
    text-decoration: none;
    cursor: pointer;
    border-left: 3px solid transparent;
    transition: all 0.12s;
    line-height: 1.3;
}
.sidebar-link:hover { color: #122b44; background: rgba(18,43,68,0.04); text-decoration: none; }
.sidebar-link.active { color: #122b44; font-weight: 600; border-left-color: #122b44; background: rgba(18,43,68,0.06); }
.sidebar-icao {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    color: rgba(0,0,0,0.3);
    flex-shrink: 0;
}
.sidebar-link.active .sidebar-icao { color: rgba(18,43,68,0.45); }

/* ── Mobile airport picker ───────────────────── */
.airport-mobile-select-wrap {
    display: none;
    padding: 1rem 1rem 0;
}
.airport-mobile-select-wrap select {
    width: 100%;
    padding: 0.65rem 1rem;
    font-size: 0.95rem;
    border: 1px solid rgba(0,0,0,0.15);
    border-radius: 8px;
    background: #fff;
    color: #122b44;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23122b44' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.9rem center;
    cursor: pointer;
}
.airport-mobile-select-wrap select:focus {
    outline: none;
    border-color: #122b44;
    box-shadow: 0 0 0 3px rgba(18,43,68,0.1);
}

/* ── Main content ────────────────────────────── */
.airport-main {
    flex: 1;
    padding: 2rem 2.5rem;
    min-width: 0;
}
.airport-panel { display: none; }
.airport-panel.active { display: block; }

.airport-panel-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #122b44;
    margin: 0 0 0.25rem;
}
.airport-panel-sub {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 1.75rem;
}

.section-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(0,0,0,0.4);
    margin-bottom: 0.6rem;
    margin-top: 1.75rem;
    border-bottom: 1px solid rgba(0,0,0,0.07);
    padding-bottom: 0.4rem;
}
.section-label:first-of-type { margin-top: 0; }

.metar-block {
    background: #1a1a2e;
    color: #e2e8f0;
    font-family: 'Courier New', monospace;
    font-size: 0.82rem;
    line-height: 1.7;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
    margin-bottom: 0;
    word-break: break-all;
}
.metar-atis-letter {
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    border-right: 1px solid rgba(255,255,255,0.1);
    padding-right: 1.25rem;
    min-width: 48px;
}
.metar-atis-letter span:first-child {
    font-size: 0.6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    opacity: 0.5;
    margin-bottom: 0.15rem;
}
.metar-atis-letter span:last-child {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}
.metar-text { flex: 1; min-width: 0; }

.hours-list {
    color: #495057;
    font-size: 0.875rem;
    padding-left: 1.25rem;
    margin-bottom: 0;
    line-height: 2;
}
.uncontrolled-note {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: #f1f5f9;
    color: #64748b;
    font-size: 0.8rem;
    border-radius: 5px;
    padding: 0.5rem 0.9rem;
    margin-bottom: 0;
}

.scenery-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    margin-bottom: 0.75rem;
    background: #fff;
}
.badge-payware  { background:#fff3cd; color:#856404; font-size:0.68rem; font-weight:700; padding:0.2rem 0.55rem; border-radius:999px; white-space:nowrap; }
.badge-freeware { background:#d4edda; color:#155724; font-size:0.68rem; font-weight:700; padding:0.2rem 0.55rem; border-radius:999px; white-space:nowrap; }
.no-scenery {
    color: rgba(0,0,0,0.35);
    font-size: 0.85rem;
    font-style: italic;
    padding: 0.25rem 0 0;
    margin-bottom: 0;
}

/* ── Mobile ──────────────────────────────────── */
@media (max-width: 768px) {
    .airports-wrap { flex-direction: column; }
    .airport-sidebar { display: none; }
    .airport-mobile-select-wrap { display: block; }
    .airport-main { padding: 1.25rem 1rem; }
    .metar-block { font-size: 0.78rem; gap: 0.9rem; padding: 0.85rem 1rem; }
    .airport-panel-title { font-size: 1.25rem; }
}
</style>

<div class="airports-wrap">

    {{-- Mobile airport picker --}}
    <div class="airport-mobile-select-wrap">
        <select id="mobile-airport-select">
            <optgroup label="Manitoba">
                <option value="cywg">CYWG · CYAV — Winnipeg</option>
                <option value="cypg">CYPG — Portage la Prairie</option>
            </optgroup>
            <optgroup label="Saskatchewan">
                <option value="cyxe">CYXE — Saskatoon</option>
                <option value="cyqr">CYQR — Regina</option>
                <option value="cymj">CYMJ — Moose Jaw</option>
                <option value="cyvc">CYVC — La Ronge</option>
            </optgroup>
            <optgroup label="Ontario">
                <option value="cyqt">CYQT — Thunder Bay</option>
            </optgroup>
        </select>
    </div>

    {{-- Sidebar --}}
    <div class="airport-sidebar">

        <div class="sidebar-province">Manitoba</div>
        <a class="sidebar-link active" data-panel="cywg">
            <span class="sidebar-icao">CYWG · CYAV</span>Winnipeg
        </a>
        <a class="sidebar-link" data-panel="cypg">
            <span class="sidebar-icao">CYPG</span>Portage la Prairie
        </a>

        <div class="sidebar-province">Saskatchewan</div>
        <a class="sidebar-link" data-panel="cyxe">
            <span class="sidebar-icao">CYXE</span>Saskatoon
        </a>
        <a class="sidebar-link" data-panel="cyqr">
            <span class="sidebar-icao">CYQR</span>Regina
        </a>
        <a class="sidebar-link" data-panel="cymj">
            <span class="sidebar-icao">CYMJ</span>Moose Jaw
        </a>
        <a class="sidebar-link" data-panel="cyvc">
            <span class="sidebar-icao">CYVC</span>La Ronge
        </a>

        <div class="sidebar-province">Ontario</div>
        <a class="sidebar-link" data-panel="cyqt">
            <span class="sidebar-icao">CYQT</span>Thunder Bay
        </a>

    </div>

    {{-- Main content --}}
    <div class="airport-main">

        {{-- CYWG --}}
        <div class="airport-panel active" id="panel-cywg">
            <h1 class="airport-panel-title">Winnipeg</h1>
            <p class="airport-panel-sub">James Armstrong Richardson International (CYWG) · St. Andrews (CYAV)</p>
            <div class="section-label">Live Weather</div>
            @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYWG'); @endphp
            <div class="metar-block">
                @if($atisLetter)<div class="metar-atis-letter"><span>ATIS</span><span>{{ $atisLetter }}</span></div>@endif
                <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYWG') }}</div>
            </div>
            <div class="section-label">Operating Hours</div>
            <ul class="hours-list">
                <li>Tower/Terminal at Winnipeg International (CYWG) is open 24/7.</li>
                <li>Tower St. Andrews (CYAV) is open daily from 1300Z – 0400Z.</li>
            </ul>
            <div class="section-label">Scenery</div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-payware">Payware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">FSimStudios — MSFS</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">If you asked what sim developer creates the most consistently high-quality product, most people would say FSimStudios! The team based in Vancouver announced their release of Winnipeg, just in time for CTP East 2023.</p>
                <a target="_blank" href="https://store.fsimstudios.com/products/fsimstudios-winnipeg-international-airport-cywg-for-msfs" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-payware">Payware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">SimAddons — FSX, P3Dv4/v5, MSFS</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">SimAddons truly is a legend in creating scenery for Canadian airports. Their scenery for Winnipeg isn't brand new, but is still the best available for P3D and MSFS as of late 2020.</p>
                <a target="_blank" href="http://www.simaddons.com/pages/simaddons_purchase1.htm" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-freeware">Freeware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">Orbx — FSX, P3D</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">ORBX is some of the best in the business at creating scenery, and Winnipeg is one of those airports available in their freeware pack.</p>
                <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-freeware">Freeware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">ProjectSierra — P3D v4/v5</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">P3D user looking for some good freeware? ProjectSierra has updated a scenery for the sim, available at no cost.</p>
                <a target="_blank" href="https://drive.google.com/drive/u/0/folders/1BITJA-audI2-7Zmk5Vq-_e5YSOGM8049" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
        </div>

        {{-- CYPG --}}
        <div class="airport-panel" id="panel-cypg">
            <h1 class="airport-panel-title">Portage la Prairie / Southport</h1>
            <p class="airport-panel-sub">Southport Airport (CYPG)</p>
            <div class="section-label">Live Weather</div>
            @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYPG'); @endphp
            <div class="metar-block">
                @if($atisLetter)<div class="metar-atis-letter"><span>ATIS</span><span>{{ $atisLetter }}</span></div>@endif
                <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYPG') }}</div>
            </div>
            <div class="section-label">Operating Hours</div>
            <ul class="hours-list">
                <li>Tower open Mon – Fri from 1400Z – 2300Z, excluding holidays.</li>
            </ul>
            <div class="section-label">Scenery</div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-freeware">Freeware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">Orbx — FSX, P3D</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">CYPG is included in ORBX's free Global Airport Pack.</p>
                <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
        </div>

        {{-- CYXE --}}
        <div class="airport-panel" id="panel-cyxe">
            <h1 class="airport-panel-title">Saskatoon</h1>
            <p class="airport-panel-sub">John G. Diefenbaker International Airport (CYXE)</p>
            <div class="section-label">Live Weather</div>
            @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYXE'); @endphp
            <div class="metar-block">
                @if($atisLetter)<div class="metar-atis-letter"><span>ATIS</span><span>{{ $atisLetter }}</span></div>@endif
                <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYXE') }}</div>
            </div>
            <div class="section-label">Operating Hours</div>
            <ul class="hours-list">
                <li>Tower open Mon – Fri between Mar 9 – Oct 31, from 1200Z – 0445Z.</li>
                <li>Tower open Sat – Sun between Mar 9 – Oct 31, from 1245Z – 0445Z.</li>
                <li>Tower open between Nov 1 – Mar 8, from 1245Z – 0445Z.</li>
            </ul>
            <div class="section-label">Scenery</div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-payware">Payware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">SimAddons — FSX, P3Dv4/v5, MSFS</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">SimAddons is all over Canada scenery for FSX, P3D and MSFS. Their scenery for CYXE is great and models the airport perfectly.</p>
                <a target="_blank" href="http://www.simaddons.com/pages/simaddons_purchase1.htm" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-payware">Payware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">FSXCenery — FSX, P3Dv5</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">FSXCenery brings the airport to life with their scenery, capturing the unique terminal shape and classic GA ramp.</p>
                <a target="_blank" href="https://secure.simmarket.com/fsxcenery-cyxe-saskatoon-john-g.-diefenbaker-international-airport-fsx-p3dv5.phtml" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-freeware">Freeware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">Orbx — FSX, P3D</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">CYXE is covered in the ORBX freeware pack — pick it up for zero dollars.</p>
                <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
        </div>

        {{-- CYQR --}}
        <div class="airport-panel" id="panel-cyqr">
            <h1 class="airport-panel-title">Regina</h1>
            <p class="airport-panel-sub">Regina International Airport (CYQR)</p>
            <div class="section-label">Live Weather</div>
            @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYQR'); @endphp
            <div class="metar-block">
                @if($atisLetter)<div class="metar-atis-letter"><span>ATIS</span><span>{{ $atisLetter }}</span></div>@endif
                <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYQR') }}</div>
            </div>
            <div class="section-label">Operating Hours</div>
            <ul class="hours-list">
                <li>Tower open Apr 1 – Oct 31 from 1200Z – 0400Z.</li>
                <li>Tower open Nov 1 – Mar 31 from 1200Z – 0500Z.</li>
            </ul>
            <div class="section-label">Scenery</div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-payware">Payware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">FSXCenery — FSX, P3D</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">FSXCenery has Regina on lock for FSX and P3D — a fantastic addition for central Canada flying.</p>
                <a target="_blank" href="https://secure.simmarket.com/fsxcenery-cyqr-regina-international-airport-fsx-p3d.phtml" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-freeware">Freeware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">Canada4XPlane — XP10, XP11+</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">C4XP's modeling of everything from the terminal to the Regina Flying Club is extremely detailed — and it's free.</p>
                <a target="_blank" href="https://forums.x-plane.org/index.php?/files/file/50207-cyqr-regina-international-airport/" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-freeware">Freeware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">Orbx — FSX, P3D</span></div>
                <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
        </div>

        {{-- CYMJ --}}
        <div class="airport-panel" id="panel-cymj">
            <h1 class="airport-panel-title">Moose Jaw</h1>
            <p class="airport-panel-sub">Moose Jaw / 15 Wing (CYMJ)</p>
            <div class="section-label">Live Weather</div>
            @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYMJ'); @endphp
            <div class="metar-block">
                @if($atisLetter)<div class="metar-atis-letter"><span>ATIS</span><span>{{ $atisLetter }}</span></div>@endif
                <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYMJ') }}</div>
            </div>
            <div class="section-label">Operating Hours</div>
            <ul class="hours-list">
                <li>Tower/Terminal open Feb 16 – Oct 31 from 1400Z – 0030Z.</li>
                <li>Tower/Terminal open Nov 1 – Feb 15 from 1430Z – 0100Z.</li>
                <li>Tower/Terminal frequently closed on weekends.</li>
            </ul>
            <div class="section-label">Scenery</div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-freeware">Freeware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">Orbx — FSX, P3D</span></div>
                <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
        </div>

        {{-- CYVC --}}
        <div class="airport-panel" id="panel-cyvc">
            <h1 class="airport-panel-title">La Ronge</h1>
            <p class="airport-panel-sub">La Ronge (Barber Field) Airport (CYVC)</p>
            <div class="section-label">Live Weather</div>
            @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYVC'); @endphp
            <div class="metar-block">
                @if($atisLetter)<div class="metar-atis-letter"><span>ATIS</span><span>{{ $atisLetter }}</span></div>@endif
                <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYVC') }}</div>
            </div>
            <div class="section-label">Scenery</div>
            <p class="no-scenery">No dedicated third-party scenery known.</p>
        </div>

        {{-- CYQT --}}
        <div class="airport-panel" id="panel-cyqt">
            <h1 class="airport-panel-title">Thunder Bay</h1>
            <p class="airport-panel-sub">Thunder Bay International Airport (CYQT)</p>
            <div class="section-label">Live Weather</div>
            @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYQT'); @endphp
            <div class="metar-block">
                @if($atisLetter)<div class="metar-atis-letter"><span>ATIS</span><span>{{ $atisLetter }}</span></div>@endif
                <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYQT') }}</div>
            </div>
            <div class="section-label">Operating Hours</div>
            <ul class="hours-list">
                <li>Tower open daily from 1200Z – 0400Z.</li>
            </ul>
            <div class="section-label">Scenery</div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-payware">Payware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">MFSG — MSFS, P3Dv3+, FSX, FS2004</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">MFSG covers Thunder Bay across multiple simulators, featuring the most updated airport layout of any CYQT options.</p>
                <a target="_blank" href="https://secure.simmarket.com/mfsg.mhtml" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-payware">Payware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">FSXCenery — MSFS</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">FSXCenery's CYQT for MSFS — the terminal, parking area, and aprons all look outstanding.</p>
                <a target="_blank" href="https://secure.simmarket.com/fsxcenery-cyqt-thunder-bay-msfs.phtml" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
            <div class="scenery-item">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.4rem;"><span class="badge-freeware">Freeware</span><span style="font-weight:700;font-size:0.9rem;color:#122b44;">Jim Kanold — XP11+</span></div>
                <p style="font-size:0.875rem;color:#6c757d;margin-bottom:0.75rem;">This free scenery updates XP11 with CYQT's unique terminal and more.</p>
                <a target="_blank" href="https://forums.x-plane.org/index.php?/files/file/41400-thunder-bay-cyqt/" style="font-size:0.82rem;color:#122b44;font-weight:600;text-decoration:none;">View More →</a>
            </div>
        </div>

    </div>{{-- end airport-main --}}
</div>{{-- end airports-wrap --}}

<script>
function switchAirport(panelId) {
    document.querySelectorAll('.sidebar-link').forEach(function(l) { l.classList.remove('active'); });
    document.querySelectorAll('.airport-panel').forEach(function(p) { p.classList.remove('active'); });
    var link = document.querySelector('.sidebar-link[data-panel="' + panelId + '"]');
    if (link) link.classList.add('active');
    var panel = document.getElementById('panel-' + panelId);
    if (panel) panel.classList.add('active');
}

document.querySelectorAll('.sidebar-link').forEach(function(link) {
    link.addEventListener('click', function() {
        switchAirport(this.dataset.panel);
        var sel = document.getElementById('mobile-airport-select');
        if (sel) sel.value = this.dataset.panel;
    });
});

document.getElementById('mobile-airport-select').addEventListener('change', function() {
    switchAirport(this.value);
    document.querySelector('.airport-main').scrollIntoView({ behavior: 'smooth', block: 'start' });
});
</script>
@endsection
