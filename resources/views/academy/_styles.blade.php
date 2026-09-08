<style>
.academy-hero{background:linear-gradient(135deg,#081827,#122b44);color:#fff;padding:2.4rem 0}.academy-kicker{text-transform:uppercase;letter-spacing:.14em;font-size:.72rem;color:#7dd3fc;font-weight:700}.academy-hero h1{font-weight:800;margin:.35rem 0}.academy-body{background:#f5f7fa;min-height:60vh;padding:2rem 0 4rem}.academy-card{display:block;background:#fff;border:1px solid #e3e8ef;border-radius:12px;height:100%;color:#122b44;overflow:hidden;transition:.15s}.academy-card:hover{color:#122b44;text-decoration:none;border-color:#8ca0b5;box-shadow:0 5px 18px rgba(18,43,68,.08);transform:translateY(-2px)}.academy-card-body{padding:1.2rem}.academy-thumb{height:145px;background:linear-gradient(135deg,#0b2238,#16466d);background-size:cover;background-position:center;position:relative}.academy-thumb:after{content:'';position:absolute;inset:0;background:linear-gradient(0deg,rgba(4,16,28,.5),rgba(4,16,28,.05))}.academy-thumb-icon{position:absolute;left:1.15rem;bottom:1rem;z-index:1;color:#fff;font-size:1.45rem}.academy-card-locked{cursor:default;filter:saturate(.7)}.academy-card-locked:hover{transform:none;box-shadow:none;border-color:#e3e8ef}.academy-card-locked .academy-thumb:after{background:rgba(8,22,35,.58)}.academy-lock{position:absolute;right:1rem;top:1rem;z-index:2;background:rgba(8,20,32,.82);color:#fff;border-radius:999px;padding:.35rem .65rem;font-size:.72rem;font-weight:700}.academy-lock-overlay{position:absolute;inset:0;z-index:1;display:flex;align-items:center;justify-content:center;pointer-events:none}.academy-lock-overlay i{font-size:2rem;color:rgba(255,255,255,.9);filter:drop-shadow(0 2px 4px rgba(0,0,0,.35))}.academy-icon{width:42px;height:42px;border-radius:10px;background:#eaf2fa;display:flex;align-items:center;justify-content:center;margin-bottom:1rem}.academy-muted{color:#6b7785;font-size:.88rem}.academy-panel{background:#fff;border:1px solid #e3e8ef;border-radius:12px;padding:1.4rem;margin-bottom:1rem}.academy-status{font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;font-weight:700}.slides-frame{position:relative;padding-top:56.25%;background:#0b1724;border-radius:12px;overflow:hidden}.slides-frame iframe{position:absolute;inset:0;width:100%;height:100%;border:0}.academy-list-row{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 0;border-bottom:1px solid #edf0f4}.academy-list-row:last-child{border-bottom:0}.academy-actions{display:flex;gap:.45rem;flex-wrap:wrap}.academy-progress{display:inline-flex;align-items:center;white-space:nowrap;border-radius:999px;padding:.28rem .58rem;font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em}.academy-progress-not_started{background:#e9eef3;color:#5d6b78}.academy-progress-in_progress{background:#fff1c2;color:#8a5a00}.academy-progress-complete{background:#d9f7e6;color:#087443}

/* Academy dark mode follows the existing site-wide theme toggle. */
html[data-theme="dark"] .academy-hero {
    background: linear-gradient(135deg, #080f18 0%, #0b1724 55%, #10283d 100%);
    border-bottom: 1px solid rgba(255,255,255,.07);
}
html[data-theme="dark"] .academy-body {
    background: #16181d;
    color: #e4e7eb;
}
html[data-theme="dark"] .academy-card,
html[data-theme="dark"] .academy-panel {
    background: #20242b;
    border-color: #303640;
    color: #e4e7eb;
    box-shadow: 0 8px 22px rgba(0,0,0,.14);
}
html[data-theme="dark"] a.academy-card:hover {
    color: #fff;
    border-color: #4c6175;
    box-shadow: 0 9px 25px rgba(0,0,0,.28);
}
html[data-theme="dark"] .academy-card-locked,
html[data-theme="dark"] .academy-card-locked:hover {
    background: #1c2026;
    border-color: #2a3038;
    color: #aeb5bd;
    box-shadow: none;
}
html[data-theme="dark"] .academy-muted,
html[data-theme="dark"] .academy-panel .text-muted,
html[data-theme="dark"] .academy-card .text-muted,
html[data-theme="dark"] .form-text.text-muted {
    color: #9ba5b1 !important;
}
html[data-theme="dark"] .academy-list-row {
    border-bottom-color: #303640;
}
html[data-theme="dark"] .academy-panel .border {
    border-color: #363d47 !important;
}
html[data-theme="dark"] .academy-panel hr {
    border-top-color: #363d47;
}
html[data-theme="dark"] .academy-panel label,
html[data-theme="dark"] .academy-panel h4,
html[data-theme="dark"] .academy-panel h5,
html[data-theme="dark"] .academy-panel h6,
html[data-theme="dark"] .academy-card h5,
html[data-theme="dark"] .academy-list-row strong {
    color: #edf0f3;
}
html[data-theme="dark"] .academy-panel .form-control,
html[data-theme="dark"] .academy-panel select.form-control,
html[data-theme="dark"] .academy-panel textarea.form-control {
    background-color: #171a1f;
    border-color: #3a424d;
    color: #e4e7eb;
}
html[data-theme="dark"] .academy-panel .form-control:focus {
    background-color: #171a1f;
    border-color: #6596bd;
    color: #fff;
    box-shadow: 0 0 0 .2rem rgba(83,135,176,.18);
}
html[data-theme="dark"] .academy-panel .form-control::placeholder {
    color: #697480;
}
html[data-theme="dark"] .academy-icon {
    background: #182b3b;
    color: #8dc8f0;
}
html[data-theme="dark"] .academy-lock {
    background: rgba(8,13,19,.9);
    border: 1px solid rgba(255,255,255,.12);
}
html[data-theme="dark"] .academy-progress-not_started { background:#273442; color:#bac5cf; }
html[data-theme="dark"] .academy-progress-in_progress { background:#493a13; color:#ffd974; }
html[data-theme="dark"] .academy-progress-complete { background:#123f2e; color:#75e3ae; }

/* Shared Academy theme details */
.academy-hero-link{color:rgba(255,255,255,.62)!important}.academy-hero-link:hover{color:#fff!important;text-decoration:none}.academy-hero-subtitle{color:rgba(255,255,255,.68)}.academy-access-icon{color:#7b8794}.academy-body .border{border-color:#e3e8ef!important}.academy-body .custom-control-label,.academy-body label{color:inherit}.academy-body .table{color:#273444}.academy-body .form-control-file{max-width:100%}

html[data-theme="dark"] .academy-body .border,
html[data-theme="dark"] .academy-body .rounded.border {
    border-color:#363d47!important;
}
html[data-theme="dark"] .academy-body .table {
    color:#e4e7eb!important;
    background:transparent!important;
}
html[data-theme="dark"] .academy-body .table tbody tr:hover {
    color:#fff!important;
    background:rgba(255,255,255,.035)!important;
}
html[data-theme="dark"] .academy-body .custom-control-label,
html[data-theme="dark"] .academy-body label,
html[data-theme="dark"] .academy-body strong,
html[data-theme="dark"] .academy-body h1,
html[data-theme="dark"] .academy-body h2,
html[data-theme="dark"] .academy-body h3,
html[data-theme="dark"] .academy-body h4,
html[data-theme="dark"] .academy-body h5,
html[data-theme="dark"] .academy-body h6 {
    color:#edf0f3;
}
html[data-theme="dark"] .academy-body .form-control-file {
    color:#c8ced6;
}
html[data-theme="dark"] .academy-body .custom-control-label::before {
    background-color:#171a1f;
    border-color:#4a535f;
}
html[data-theme="dark"] .academy-body .custom-control-input:checked~.custom-control-label::before {
    background-color:#3f7fae;
    border-color:#3f7fae;
}
html[data-theme="dark"] .academy-body .btn-outline-primary {
    color:#8fc5ef;
    border-color:#4c7596;
}
html[data-theme="dark"] .academy-body .btn-outline-primary:hover {
    color:#fff;
    background:#285d85;
    border-color:#285d85;
}
html[data-theme="dark"] .academy-body .btn-outline-danger {
    color:#f29ba3;
    border-color:#8e454c;
}
html[data-theme="dark"] .academy-body .btn-outline-danger:hover {
    color:#fff;
    background:#88363e;
    border-color:#88363e;
}
html[data-theme="dark"] .academy-access-icon { color:#9ba5b1; }
html[data-theme="dark"] a.academy-card { color:#e4e7eb!important; }
html[data-theme="dark"] a.academy-card:hover { color:#fff!important; }
html[data-theme="dark"] .academy-hero .academy-hero-link { color:rgba(255,255,255,.62)!important; }
html[data-theme="dark"] .academy-hero .academy-hero-link:hover { color:#fff!important; }


/* Static Academy slide decks */
.academy-slide-deck{background:#fff;border:1px solid #e3e8ef;border-radius:12px;overflow:hidden;outline:none;box-shadow:0 6px 20px rgba(18,43,68,.06)}
.academy-slide-deck:focus{box-shadow:0 0 0 .2rem rgba(0,123,255,.14),0 6px 20px rgba(18,43,68,.06)}
.academy-slide-stage{background:#0b1724;display:flex;align-items:center;justify-content:center;min-height:220px;aspect-ratio:16/9}
.academy-slide-image{display:block;width:100%;height:100%;object-fit:contain;background:#0b1724}
.academy-online-slide-stage{position:relative;padding:0;overflow:hidden}.academy-online-slide-stage iframe{width:100%;height:100%;min-height:0;border:0;background:#0b1724}.academy-online-slide-deck:fullscreen .academy-online-slide-stage{flex:1}.academy-online-slide-deck:fullscreen iframe{height:calc(100vh - 86px)}
.academy-slide-controls{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.85rem 1rem;background:#fff;border-top:1px solid #e3e8ef}
.academy-slide-control-right{display:flex;align-items:center;gap:.5rem}
.academy-slide-counter{font-weight:800;color:#34495e;white-space:nowrap}
.academy-slide-hint{padding:0 1rem .85rem;background:#fff;color:#6b7785;font-size:.8rem;text-align:center}
.academy-slide-deck button:disabled{opacity:.45;cursor:not-allowed}
.academy-audio-panel{display:flex;align-items:center;justify-content:space-between;gap:1.5rem}
.academy-audio-panel audio{width:min(520px,100%)}
.academy-question-card{background:#fff}
.academy-slide-deck:fullscreen{background:#080f18;border:0;border-radius:0;padding:1rem;display:flex;flex-direction:column}
.academy-slide-deck:fullscreen .academy-slide-stage{flex:1;min-height:0;aspect-ratio:auto}
.academy-slide-deck:fullscreen .academy-slide-image{width:auto;max-width:100%;height:auto;max-height:calc(100vh - 135px)}
.academy-slide-deck:fullscreen .academy-slide-controls,.academy-slide-deck:fullscreen .academy-slide-hint{flex:0 0 auto}
@media (max-width:767.98px){.academy-slide-controls{flex-wrap:wrap}.academy-slide-counter{order:-1;width:100%;text-align:center}.academy-slide-control-right{margin-left:auto}.academy-audio-panel{align-items:stretch;flex-direction:column}.academy-slide-hint{display:none}}

html[data-theme="dark"] .academy-slide-deck{background:#20242b;border-color:#303640;box-shadow:0 8px 22px rgba(0,0,0,.18)}
html[data-theme="dark"] .academy-slide-controls,html[data-theme="dark"] .academy-slide-hint{background:#20242b;border-color:#303640}
html[data-theme="dark"] .academy-slide-counter{color:#e4e7eb}
html[data-theme="dark"] .academy-slide-hint{color:#9ba5b1}
html[data-theme="dark"] .academy-slide-deck .btn-outline-secondary{color:#c3cbd4;border-color:#56616e}
html[data-theme="dark"] .academy-slide-deck .btn-outline-secondary:hover{color:#fff;background:#4a5561;border-color:#4a5561}
html[data-theme="dark"] .academy-question-card{background:#1c2026}

</style>
<style>
/* v26 LMS-style course workspace */
.academy-course-shell-width{max-width:1600px;padding-left:24px;padding-right:24px}
.academy-course-body{padding-top:1.25rem}
.academy-course-shell{display:grid;grid-template-columns:280px minmax(0,1fr);gap:1.25rem;align-items:start;position:relative}
.academy-course-main{min-width:0}
.academy-course-sidebar{position:sticky;top:1rem;background:#fff;border:1px solid #e3e8ef;border-radius:12px;overflow:hidden;box-shadow:0 6px 20px rgba(18,43,68,.05);z-index:20}
.academy-course-sidebar-head{display:flex;align-items:center;justify-content:space-between;padding:.9rem 1rem .55rem}
.academy-sidebar-collapse{border:0;background:transparent;color:#73808c;padding:.25rem .4rem;border-radius:6px}
.academy-sidebar-collapse:hover{background:#edf2f7;color:#122b44}
.academy-course-sidebar-title-wrap{padding:0 1rem .9rem;border-bottom:1px solid #edf0f4}
.academy-course-sidebar-title{display:block;color:#122b44;font-weight:800;line-height:1.25;margin-bottom:.65rem}.academy-course-sidebar-title:hover{text-decoration:none;color:#2563eb}
.academy-course-sidebar-progress{display:flex;align-items:center;justify-content:space-between;gap:.5rem;color:#6b7785;font-size:.72rem}
.academy-course-nav{padding:.45rem}
.academy-course-nav-item{display:flex;align-items:center;gap:.7rem;padding:.72rem .65rem;border-radius:9px;color:#344454;min-height:54px;position:relative}
.academy-course-nav-item:hover{text-decoration:none;color:#122b44;background:#f3f6f9}
.academy-course-nav-item.active{background:#eaf2ff;color:#174ea6;font-weight:700}
.academy-course-nav-icon{width:20px;text-align:center;flex:0 0 20px}
.academy-course-nav-copy{min-width:0;display:flex;flex-direction:column}.academy-course-nav-label{line-height:1.2}.academy-course-nav-copy small{color:#7b8794;margin-top:.2rem;font-size:.7rem}
.academy-course-nav-current{margin-left:auto;font-size:.7rem}
.academy-sidebar-mobile-toggle{display:none;position:fixed;left:1rem;bottom:1rem;z-index:1040;box-shadow:0 5px 18px rgba(0,0,0,.22)}
.academy-sidebar-collapsed{grid-template-columns:74px minmax(0,1fr)}
.academy-sidebar-collapsed .academy-course-sidebar-head .academy-kicker,.academy-sidebar-collapsed .academy-course-sidebar-title-wrap,.academy-sidebar-collapsed .academy-course-nav-copy,.academy-sidebar-collapsed .academy-course-nav-current{display:none}
.academy-sidebar-collapsed .academy-course-sidebar-head{justify-content:center;padding:.65rem}.academy-sidebar-collapsed .academy-sidebar-collapse i{transform:rotate(180deg)}
.academy-sidebar-collapsed .academy-course-nav-item{justify-content:center;padding:.72rem .4rem}.academy-sidebar-collapsed .academy-course-nav-icon{font-size:1.05rem}
.academy-course-main .academy-slide-deck{width:100%}
.academy-course-main .academy-online-slide-stage{aspect-ratio:16/9;min-height:0}
.academy-module-pagination{display:flex;justify-content:space-between;gap:1rem;margin-top:1rem;align-items:center}.academy-module-pagination>div{max-width:48%}
html[data-theme="dark"] .academy-course-sidebar{background:#20242b;border-color:#303640;box-shadow:0 8px 22px rgba(0,0,0,.18)}
html[data-theme="dark"] .academy-course-sidebar-title-wrap{border-color:#303640}html[data-theme="dark"] .academy-course-sidebar-title{color:#edf0f3}
html[data-theme="dark"] .academy-course-nav-item{color:#c6ced6}html[data-theme="dark"] .academy-course-nav-item:hover{background:#292f37;color:#fff}html[data-theme="dark"] .academy-course-nav-item.active{background:#17344d;color:#a9d8ff}
html[data-theme="dark"] .academy-sidebar-collapse{color:#9ba5b1}html[data-theme="dark"] .academy-sidebar-collapse:hover{background:#2b323b;color:#fff}
@media(max-width:991.98px){
    .academy-course-shell-width{padding-left:15px;padding-right:15px}
    .academy-course-shell{display:block}
    .academy-course-sidebar{position:fixed;top:0;left:0;bottom:0;width:min(330px,88vw);border-radius:0;transform:translateX(-105%);transition:transform .22s ease;z-index:1050;overflow-y:auto}
    .academy-sidebar-mobile-open .academy-course-sidebar{transform:translateX(0)}
    .academy-sidebar-mobile-open:after{content:"";position:fixed;inset:0;background:rgba(0,0,0,.48);z-index:1045}
    .academy-sidebar-mobile-toggle{display:block}
    .academy-sidebar-collapse{display:none}
    .academy-module-pagination{padding-bottom:3rem}
}
@media(max-width:575.98px){.academy-module-pagination{align-items:stretch;flex-direction:column}.academy-module-pagination>div{max-width:none}.academy-module-pagination .btn{width:100%}}
</style>
