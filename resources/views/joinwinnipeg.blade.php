@extends('layouts.master')
@section('title', 'Join - Winnipeg FIR')
@section('description', 'Join the Winnipeg FIR as a controller or visiting controller on VATSIM.')

@section('content')
<style>
.join-wrap {
    background: #f6f8fa;
    min-height: calc(100vh - 60px);
    padding: 2.5rem 0 3rem;
}
.join-container {
    max-width: 860px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* ── Hero ─────────────────────────────────── */
.join-hero {
    margin-bottom: 2.5rem;
}
.join-hero h1 {
    font-size: 2.6rem;
    font-weight: 800;
    color: #122b44;
    margin-bottom: 0.6rem;
}
.join-hero p {
    color: #6c757d;
    font-size: 0.95rem;
    max-width: 680px;
    line-height: 1.65;
    margin: 0;
}

/* ── Path selector ────────────────────────── */
.path-tabs {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}
.path-tab {
    flex: 1;
    min-width: 200px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    cursor: pointer;
    background: #fff;
    transition: all 0.15s;
    text-align: left;
}
.path-tab:hover { border-color: #122b44; }
.path-tab.active {
    border-color: #122b44;
    background: #122b44;
}
.path-tab-label {
    font-weight: 700;
    font-size: 0.9rem;
    color: #122b44;
    display: block;
}
.path-tab.active .path-tab-label { color: #fff; }
.path-tab-desc {
    font-size: 0.78rem;
    color: #6c757d;
    margin-top: 0.15rem;
}
.path-tab.active .path-tab-desc { color: rgba(255,255,255,0.7); }

/* ── Path content panels ─────────────────── */
.path-panel { display: none; }
.path-panel.active { display: block; }

/* ── Steps ────────────────────────────────── */
.step-group {
    margin-bottom: 0.5rem;
    position: relative;
    padding-left: 18px;
    border-left: 2px solid #dee2e6;
    margin-left: 17px;
}
.step {
    display: flex;
    gap: 1.25rem;
    padding-bottom: 2rem;
    align-items: flex-start;
    position: relative;
}
.step:last-child { padding-bottom: 0; }
.step-num {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #122b44;
    color: #fff;
    font-weight: 800;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: -36px;
    position: relative;
    z-index: 1;
}
.step-body {
    flex: 1;
    padding-top: 0.3rem;
}
.step-body h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #122b44;
    margin: 0 0 0.4rem;
}
.step-body p, .step-body ol, .step-body ul {
    font-size: 0.875rem;
    color: #495057;
    margin: 0;
    line-height: 1.7;
}
.step-body ol, .step-body ul { padding-left: 1.25rem; }
.step-body a { color: #122b44; font-weight: 600; }

/* ── Info card ────────────────────────────── */
.info-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 2rem;
}
.info-card-highlight {
    border-left: 4px solid #122b44;
    background: #f0f4f8;
    border-radius: 0 10px 10px 0;
}
.info-card p { font-size: 0.875rem; color: #495057; margin: 0; line-height: 1.65; }
.info-card strong { color: #122b44; }

/* ── CTA ──────────────────────────────────── */
.join-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #28a745;
    color: #fff;
    font-weight: 700;
    font-size: 0.9rem;
    padding: 0.65rem 1.4rem;
    border-radius: 7px;
    text-decoration: none;
    transition: background 0.15s;
    margin-top: 0.5rem;
}
.join-cta:hover { background: #218838; color: #fff; text-decoration: none; }

/* ── Footer links ─────────────────────────── */
.join-footer {
    margin-top: 2.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
}
.join-footer a {
    color: #122b44;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
}
.join-footer a:hover { text-decoration: underline; }
.join-footer .sep { color: #dee2e6; }

/* ── Section label (matches airports page) ── */
.section-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(0,0,0,0.3);
    margin-bottom: 1rem;
}

@media (max-width: 600px) {
    .join-hero h1 { font-size: 1.5rem; }
    .path-tab { min-width: 100%; }
}
</style>

<div class="join-wrap">
<div class="join-container">

    <div class="join-hero">
        <h1>Join the Winnipeg FIR</h1>
        <p>From those who fly Winnipeg to those who dedicate their time to controlling our skies — the Winnipeg FIR isn't just a place on a map. We're a community, home to a constantly-evolving training program and some of the best instructors around.</p>
    </div>

    <div class="section-label">Choose your path</div>

    <div class="path-tabs">
        <div class="path-tab active" data-path="new">
            <span class="path-tab-label">New to VATSIM</span>
            <div class="path-tab-desc">Start your ATC career from scratch</div>
        </div>
        <div class="path-tab" data-path="visit">
            <span class="path-tab-label">Visiting Controller</span>
            <div class="path-tab-desc">Already have a home FIR, ARTCC, or vACC</div>
        </div>
    </div>

    {{-- ── NEW TO VATSIM ── --}}
    <div class="path-panel active" id="path-new">

        <div class="section-label">Getting started — 3 steps</div>

        <div class="step-group">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-body">
                    <h3>Create your VATSIM account</h3>
                    <p>Register for a free VATSIM account at the <a href="http://cert.vatsim.net/vatsimnet/signup.html" target="_blank" rel="noopener noreferrer">VATSIM Registration Page</a>. Read all pages carefully. When prompted for a region, select <strong>Americas</strong>, and for division select <strong>Canada</strong>. You'll receive your CID and password by email — keep these safe.</p>
                </div>
            </div>

            <div class="step">
                <div class="step-num">2</div>
                <div class="step-body">
                    <h3>Join VATCAN &amp; complete your S1 exam</h3>
                    <p>If you skipped the division step during registration, set it now at <a href="https://cert.vatsim.net/vatsimnet/regch.php" target="_blank" rel="noopener noreferrer">cert.vatsim.net/vatsimnet/regch.php</a> — select <strong>Americas → Canada</strong>.</p>
                    <p style="margin-top:0.5rem;">Then follow the steps on the <a href="https://vatcan.ca/How-to-Become-a-Controller" target="_blank" rel="noopener noreferrer">VATCAN "How to Become a Controller" page</a> to complete your S1 exam. If you already hold an S1 rating or higher, you can skip that exam step.</p>
                </div>
            </div>

            <div class="step">
                <div class="step-num">3</div>
                <div class="step-body">
                    <h3>Request a transfer to Winnipeg</h3>
                    <ol>
                        <li><a href="https://vatcan.ca/login" target="_blank" rel="noopener noreferrer">Log in to VATCAN</a> with your VATSIM CID and password.</li>
                        <li>Go to <strong>My VATCAN → Transfer Request</strong>.</li>
                        <li>In the <strong>New FIR</strong> dropdown, select <strong>Winnipeg FIR</strong>.</li>
                        <li>Add a brief reason and click <strong>Submit</strong>.</li>
                        <li>FIR staff review transfers within 1–2 weeks.</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>

    {{-- ── VISITING CONTROLLER ── --}}
    <div class="path-panel" id="path-visit">

        <div class="info-card info-card-highlight" style="margin-bottom:1.75rem;">
            <p>Winnipeg is currently welcoming visiting applications for active controllers holding a <strong>Student 3 (S3) rating or above</strong>. Whether it's for a change of scenery, to learn a new way of controlling, or just for fun — come visit Winnipeg!</p>
        </div>

        <div class="section-label">How to apply</div>

        <div class="info-card">
            <p>Visiting applications are handled through the VATCAN website. Click below to be taken directly to the visit application page.</p>
            <a class="join-cta" href="https://vatcan.ca/my/visit" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-external-link-alt fa-xs"></i> Apply on VATCAN
            </a>
        </div>

    </div>

    <div class="join-footer">
        <a href="/training"><i class="fas fa-arrow-right fa-xs" style="margin-right:0.3rem;"></i> View wait times &amp; how our training works</a>
    </div>
    <div style="margin-top:0.75rem;">
        <a href="{{ route('staff') }}" style="font-size:0.8rem;color:#6c757d;text-decoration:none;">Questions? Contact our Chief Instructor</a>
    </div>

</div>
</div>

<script>
document.querySelectorAll('.path-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.path-tab').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.path-panel').forEach(function(p) { p.classList.remove('active'); });
        this.classList.add('active');
        var panel = document.getElementById('path-' + this.dataset.path);
        if (panel) panel.classList.add('active');
    });
});
</script>
@endsection
