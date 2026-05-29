@extends('layouts.master')
@section('title', 'PDC - Winnipeg FIR')
@section('description', 'How Pre-Departure Clearances work in the Winnipeg FIR on VATSIM.')

@section('content')
<style>
.pdc-wrap {
    background: #f6f8fa;
    padding: 2.5rem 0 3rem;
}

/* ── Hero ─────────────────────────────────── */
.pdc-hero {
    margin-bottom: 2.25rem;
}
.pdc-hero h1 {
    font-size: 2.6rem;
    font-weight: 800;
    color: #122b44;
    margin-bottom: 0.5rem;
}
.pdc-hero p {
    color: #6c757d;
    font-size: 0.95rem;
    max-width: 620px;
    line-height: 1.65;
    margin: 0;
}

/* ── Sections ─────────────────────────────── */
.pdc-section {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 12px;
    padding: 1.75rem 2rem;
    margin-bottom: 1.25rem;
}
.pdc-section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #122b44;
    margin-bottom: 0.75rem;
}
.pdc-section p {
    font-size: 0.9rem;
    color: #495057;
    line-height: 1.7;
    margin-bottom: 0.75rem;
}
.pdc-section p:last-child { margin-bottom: 0; }

/* ── Clearance blocks ─────────────────────── */
.clearance-label {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(0,0,0,0.35);
    margin-bottom: 0.4rem;
    margin-top: 1.25rem;
}
.clearance-block {
    background: #1a1a2e;
    color: #e2e8f0;
    font-family: 'Courier New', monospace;
    font-size: 0.82rem;
    line-height: 1.7;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    word-break: break-word;
}

@media (max-width: 576px) {
    .pdc-hero h1 { font-size: 1.9rem; }
    .pdc-section { padding: 1.25rem; }
    .clearance-block { font-size: 0.75rem; }
}
</style>

<div class="pdc-wrap">
    <div class="container">

        <div class="pdc-hero">
            <h1>Pre-Departure Clearance</h1>
            <p>PDCs let controllers issue IFR clearances via private message during high-traffic periods — keeping frequency congestion down and getting pilots on their way faster.</p>
        </div>

        <div class="pdc-section">
            <div class="pdc-section-title">When PDCs are used</div>
            <p>Pre-Departure Clearances are issued when the Winnipeg FIR is handling high volumes of traffic — most commonly during cross-the-pond or other major VATSIM events. Controllers may also choose to use PDC at any time at their discretion if required.</p>
            <p>Winnipeg's PDC system uses the private message (PM) protocol supported by all VATSIM-approved pilot clients.</p>
        </div>

        <div class="pdc-section">
            <div class="pdc-section-title">Requesting a PDC</div>
            <p>Pilots can request a PDC from ATC by voice or text before departure. The controller will respond with a private message containing your full IFR clearance — active runway, assigned SID, squawk code, and more.</p>

            <div class="clearance-label">Example PDC</div>
            <div class="clearance-block">PDC | ACA123 YWG | A321/L | RORMA SIDPO DEGVA FELTN OTNIK BOXUM5 | USE SID DUXUS1 | TRANSPONDER 0667 | DEPARTURE RUNWAY 18 | DESTINATION CYYZ | CONTACT ATC WITH IDENTIFIER - 647A | - END -</div>

            <p style="margin-top:1rem;">Read the PDC carefully — it is your IFR clearance and should be treated as such. If your flight plan is invalid or requires amendment, the PDC will include <strong>AMENDED ROUTE</strong>; check the routing before accepting.</p>
        </div>

        <div class="pdc-section">
            <div class="pdc-section-title">Calling ready for push</div>
            <p>Once you have your PDC and understand, note the ATIS letter, and call ATC when ready for push and start — quoting your PDC identifier.</p>

            <div class="clearance-label">Example readback</div>
            <div class="clearance-block">Winnipeg Ground, ACA123, PDC Identifier 647A, ATIS T, ready for push and start.</div>
        </div>

    </div>
</div>
@endsection
