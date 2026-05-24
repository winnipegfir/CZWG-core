@extends('layouts.master')
@section('title', 'Airports - Winnipeg FIR')
@section('description', 'Winnipeg FIR\'s airports, weather, and scenery')

@section('content')
<style>
    .airport-tabs .nav-link {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.6rem 1.1rem;
        border: none;
        border-bottom: 2px solid transparent;
        border-radius: 0;
        background: none;
    }
    .airport-tabs .nav-link:hover { color: #122b44; }
    .airport-tabs .nav-link.active { color: #122b44; border-bottom-color: #122b44; }
    .airport-tabs { border-bottom: 1px solid #e9ecef; }

    .metar-block {
        background: #1a1a2e;
        color: #e2e8f0;
        font-family: 'Courier New', monospace;
        font-size: 0.82rem;
        line-height: 1.7;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
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
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        opacity: 0.5;
        font-family: inherit;
        margin-bottom: 0.15rem;
    }
    .metar-atis-letter span:last-child {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        font-family: inherit;
    }
    .metar-text { flex: 1; }
    .scenery-item {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        margin-bottom: 0.75rem;
    }
    .badge-payware  { background:#fff3cd; color:#856404; font-size:0.7rem; font-weight:700; padding:0.2rem 0.55rem; border-radius:999px; }
    .badge-freeware { background:#d4edda; color:#155724; font-size:0.7rem; font-weight:700; padding:0.2rem 0.55rem; border-radius:999px; }
    .badge-new      { background:#122b44; color:#fff;    font-size:0.7rem; font-weight:700; padding:0.2rem 0.55rem; border-radius:999px; }
</style>

<div style="background:#fff; min-height:calc(100vh - 60px); padding:2.5rem 0;">
    <div class="container">

        <div class="mb-2">
            <h1 class="font-weight-bold mb-0" style="color:#122b44;">Airports</h1>
            <p style="color:#6c757d; margin-bottom:0; margin-top:0.25rem;">Live weather and scenery recommendations for key airports in the Winnipeg FIR.</p>
        </div>

        <ul class="nav airport-tabs mt-3" id="airportTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#cywg" role="tab">CYWG / CYAV<span class="d-none d-md-inline"> — Winnipeg</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#cypg" role="tab">CYPG<span class="d-none d-md-inline"> — Southport</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#cyxe" role="tab">CYXE<span class="d-none d-md-inline"> — Saskatoon</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#cyqt" role="tab">CYQT<span class="d-none d-md-inline"> — Thunder Bay</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#cyqr" role="tab">CYQR<span class="d-none d-md-inline"> — Regina</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#cymj" role="tab">CYMJ<span class="d-none d-md-inline"> — Moose Jaw</span></a>
            </li>
        </ul>

        <div class="tab-content mt-4">

            {{-- CYWG --}}
            <div class="tab-pane fade show active" id="cywg" role="tabpanel">

                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Live Weather</h6>
                @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYWG'); @endphp
                <div class="metar-block" style="margin-bottom:1.5rem;">
                    @if($atisLetter)
                        <div class="metar-atis-letter">
                            <span>ATIS</span>
                            <span>{{ $atisLetter }}</span>
                        </div>
                    @endif
                    <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYWG') }}</div>
                </div>


                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Operating Hours</h6>
                <ul style="color:#495057; font-size:0.875rem; padding-left:1.25rem; margin-bottom:1.5rem; line-height:1.9;">
                    <li>Tower/Terminal at Winnipeg International (CYWG) is open 24/7.</li>
                    <li>Tower St. Andrews (CYAV) is open daily from 1300Z – 0400Z.</li>
                </ul>

                <hr style="border-color:#e9ecef;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem; margin-top:1.5rem;">Scenery</h6>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-payware">Payware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">FSimStudios — MSFS</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">If you asked what sim developer creates the most consistently high-quality product, most people would say FSimStudios! The team based in Vancouver announced their release of Winnipeg, just in time for CTP East 2023.</p>
                    <a target="_blank" href="https://store.fsimstudios.com/products/fsimstudios-winnipeg-international-airport-cywg-for-msfs" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-payware">Payware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">SimAddons — FSX, P3Dv4/v5, MSFS</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">SimAddons truly is a legend in creating scenery for Canadian airports. Their scenery for Winnipeg isn't brand new, but is still the best available for P3D and MSFS as of late 2020.</p>
                    <a target="_blank" href="http://www.simaddons.com/pages/simaddons_purchase1.htm" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-freeware">Freeware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">Orbx — FSX, P3D</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">ORBX is some of the best in the business at creating scenery, and Winnipeg is one of those airports available in their freeware pack. Get it now and upgrade Winnipeg for no cost.</p>
                    <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-freeware">Freeware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">ProjectSierra — P3D v4/v5</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">P3D user looking for some good freeware? ProjectSierra has updated a scenery for the sim, available at no cost.</p>
                    <a target="_blank" href="https://drive.google.com/drive/u/0/folders/1BITJA-audI2-7Zmk5Vq-_e5YSOGM8049" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>
            </div>

            {{-- CYPG --}}
            <div class="tab-pane fade" id="cypg" role="tabpanel">

                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Live Weather</h6>
                @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYPG'); @endphp
                <div class="metar-block" style="margin-bottom:1.5rem;">
                    @if($atisLetter)
                        <div class="metar-atis-letter">
                            <span>ATIS</span>
                            <span>{{ $atisLetter }}</span>
                        </div>
                    @endif
                    <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYPG') }}</div>
                </div>

                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Operating Hours</h6>
                <ul style="color:#495057; font-size:0.875rem; padding-left:1.25rem; margin-bottom:1.5rem; line-height:1.9;">
                    <li>Tower open Mon – Fri from 1400Z – 2300Z, excluding holidays.</li>
                </ul>

                <hr style="border-color:#e9ecef;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem; margin-top:1.5rem;">Scenery</h6>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-freeware">Freeware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">Orbx — FSX, P3D</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">ORBX is some of the best in the business at creating scenery, and CYPG is one of many airports included in their free Global Airport Pack. Snag it now and get an enhanced experience at Southport.</p>
                    <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>
            </div>

            {{-- CYXE --}}
            <div class="tab-pane fade" id="cyxe" role="tabpanel">

                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Live Weather</h6>
                @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYXE'); @endphp
                <div class="metar-block" style="margin-bottom:1.5rem;">
                    @if($atisLetter)
                        <div class="metar-atis-letter">
                            <span>ATIS</span>
                            <span>{{ $atisLetter }}</span>
                        </div>
                    @endif
                    <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYXE') }}</div>
                </div>

                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Operating Hours</h6>
                <ul style="color:#495057; font-size:0.875rem; padding-left:1.25rem; margin-bottom:1.5rem; line-height:1.9;">
                    <li>Tower open Mon – Fri between Mar 9 – Oct 31, from 1200Z – 0445Z.</li>
                    <li>Tower open Sat – Sun between Mar 9 – Oct 31, from 1245Z – 0445Z.</li>
                    <li>Tower open between Nov 1 – Mar 8, from 1245Z – 0445Z.</li>
                </ul>

                <hr style="border-color:#e9ecef;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem; margin-top:1.5rem;">Scenery</h6>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-payware">Payware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">SimAddons — FSX, P3Dv4/v5, MSFS</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">SimAddons, as usual, is all over Canada scenery for FSX, P3D and MSFS. Their scenery for CYXE is great and models the airport perfectly for any pilot.</p>
                    <a target="_blank" href="http://www.simaddons.com/pages/simaddons_purchase1.htm" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-payware">Payware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">FSXCenery — FSX, P3Dv5</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">With their unique terminal shape and classic General Aviation ramp, Saskatoon is welcoming to both major airlines and small private pilots. FSXCenery brings the airport to life with their scenery.</p>
                    <a target="_blank" href="https://secure.simmarket.com/fsxcenery-cyxe-saskatoon-john-g.-diefenbaker-international-airport-fsx-p3dv5.phtml" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-freeware">Freeware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">Orbx — FSX, P3D</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">ORBX is some of the best in the business at creating scenery. Like most airports, CYXE is covered in their freeware pack. Pick it up for zero dollars and upgrade your sim.</p>
                    <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>
            </div>

            {{-- CYQT --}}
            <div class="tab-pane fade" id="cyqt" role="tabpanel">

                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Live Weather</h6>
                @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYQT'); @endphp
                <div class="metar-block" style="margin-bottom:1.5rem;">
                    @if($atisLetter)
                        <div class="metar-atis-letter">
                            <span>ATIS</span>
                            <span>{{ $atisLetter }}</span>
                        </div>
                    @endif
                    <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYQT') }}</div>
                </div>

                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Operating Hours</h6>
                <ul style="color:#495057; font-size:0.875rem; padding-left:1.25rem; margin-bottom:1.5rem; line-height:1.9;">
                    <li>Tower open daily from 1200Z – 0400Z.</li>
                </ul>

                <hr style="border-color:#e9ecef;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem; margin-top:1.5rem;">Scenery</h6>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-payware">Payware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">MFSG — MSFS, P3Dv3+, FSX, FS2004</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">It's not easy to find a developer who covers a handful of simulators — but that's what MFSG did with their wonderful Thunder Bay scenery, featuring the most updated airport layout of any CYQT options.</p>
                    <a target="_blank" href="https://secure.simmarket.com/mfsg.mhtml" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-payware">Payware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">SimAddons — FSX, P3Dv4/v5, MSFS</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">SimAddons is here with Thunder Bay too — they've had this out for some time, but the scenery still matches with the current setup at CYQT.</p>
                    <a target="_blank" href="http://www.simaddons.com/pages/simaddons_purchase1.htm" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-payware">Payware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">FSXCenery — MSFS</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">FSXCenery recently published their CYQT for MSFS package — the terminal, parking area, and small aprons scattered across the airport all look outstanding.</p>
                    <a target="_blank" href="https://secure.simmarket.com/fsxcenery-cyqt-thunder-bay-msfs.phtml" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-payware">Payware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">FSXCenery — FSX, P3D</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">It isn't quite up to their MSFS standards, but FSXCenery did a great job modeling Thunder Bay for FSX/P3D. Pick it up for a low cost and get flying.</p>
                    <a target="_blank" href="https://secure.simmarket.com/fsxcenery-cyqt-thunder-bay-fsx-p3d-(de_13122).phtml" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-freeware">Freeware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">Orbx — FSX, P3D</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">Thunder Bay is just another one on ORBX's list — and at no cost, it's worth grabbing if you want to up your game.</p>
                    <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-freeware">Freeware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">Jim Kanold — XP11+</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">This free scenery updates XP11 with CYQT's unique terminal and more — a solid pick for X-Plane users flying into Thunder Bay.</p>
                    <a target="_blank" href="https://forums.x-plane.org/index.php?/files/file/41400-thunder-bay-cyqt/" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>
            </div>

            {{-- CYQR --}}
            <div class="tab-pane fade" id="cyqr" role="tabpanel">

                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Live Weather</h6>
                @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYQR'); @endphp
                <div class="metar-block" style="margin-bottom:1.5rem;">
                    @if($atisLetter)
                        <div class="metar-atis-letter">
                            <span>ATIS</span>
                            <span>{{ $atisLetter }}</span>
                        </div>
                    @endif
                    <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYQR') }}</div>
                </div>

                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Operating Hours</h6>
                <ul style="color:#495057; font-size:0.875rem; padding-left:1.25rem; margin-bottom:1.5rem; line-height:1.9;">
                    <li>Tower open Apr 1 – Oct 31 from 1200Z – 0400Z.</li>
                    <li>Tower open Nov 1 – Mar 31 from 1200Z – 0500Z.</li>
                </ul>

                <hr style="border-color:#e9ecef;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem; margin-top:1.5rem;">Scenery</h6>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-payware">Payware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">FSXCenery — FSX, P3D</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">FSXCenery has Regina on lock for FSX and P3D — their scenery models the airport great and is a fantastic addition for central Canada flying.</p>
                    <a target="_blank" href="https://secure.simmarket.com/fsxcenery-cyqr-regina-international-airport-fsx-p3d.phtml" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-freeware">Freeware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">Canada4XPlane — XP10, XP11+</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">Flying to Regina in X-Plane with this scenery from C4XP is a must. Their modeling of everything from the terminal to the Regina Flying Club is extremely detailed — and it's free.</p>
                    <a target="_blank" href="https://forums.x-plane.org/index.php?/files/file/50207-cyqr-regina-international-airport/" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-freeware">Freeware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">Orbx — FSX, P3D</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">CYQR is covered in the ORBX freeware pack — it's free, it's a nice upgrade. Go get it today.</p>
                    <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>
            </div>

            {{-- CYMJ --}}
            <div class="tab-pane fade" id="cymj" role="tabpanel">
 
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Live Weather</h6>
                @php $atisLetter = \App\Classes\WeatherHelper::getAtisLetter('CYMJ'); @endphp
                <div class="metar-block" style="margin-bottom:1.5rem;">
                    @if($atisLetter)
                        <div class="metar-atis-letter">
                            <span>ATIS</span>
                            <span>{{ $atisLetter }}</span>
                        </div>
                    @endif
                    <div class="metar-text">{{ \App\Classes\WeatherHelper::getAtis('CYMJ') }}</div>
                </div>

                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Operating Hours</h6>
                <ul style="color:#495057; font-size:0.875rem; padding-left:1.25rem; margin-bottom:1.5rem; line-height:1.9;">
                    <li>Tower/Terminal open Feb 16 – Oct 31 from 1400Z – 0030Z.</li>
                    <li>Tower/Terminal open Nov 1 – Feb 15 from 1430Z – 0100Z.</li>
                    <li>Tower/Terminal frequently closed on weekends.</li>
                </ul>

                <hr style="border-color:#e9ecef;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem; margin-top:1.5rem;">Scenery</h6>

                <div class="scenery-item">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                        <span class="badge-freeware">Freeware</span>
                        <span style="font-weight:700; font-size:0.9rem; color:#122b44;">Orbx — FSX, P3D</span>
                    </div>
                    <p style="font-size:0.875rem; color:#6c757d; margin-bottom:0.75rem;">Moose Jaw is limited for scenery options, but they've got ORBX on their side with their freeware pack — a worthwhile download.</p>
                    <a target="_blank" href="https://orbxdirect.com/product/ftx-global-airport-pack" style="font-size:0.82rem; color:#122b44; font-weight:600; text-decoration:none;">View More →</a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
