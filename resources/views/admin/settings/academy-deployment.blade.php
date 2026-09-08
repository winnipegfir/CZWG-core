@extends('layouts.dashboard')
@section('title', 'Academy Deployment - Winnipeg FIR')
@section('content')
@include('academy._styles')
<style>
.academy-mode-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.8rem}.academy-mode{position:relative;display:block;border:2px solid #dbe3eb;border-radius:12px;padding:1rem;cursor:pointer;height:100%;transition:.15s}.academy-mode:hover{border-color:#94a9bd}.academy-mode input{position:absolute;opacity:0;pointer-events:none}.academy-mode:has(input:checked){border-color:#2563eb;box-shadow:0 0 0 2px rgba(37,99,235,.12)}.academy-mode-title{font-weight:800;margin-bottom:.25rem}.academy-mode-copy{font-size:.84rem;color:#6b7785}.academy-deploy-switch{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:1.15rem;border:1px solid #e3e8ef;border-radius:12px;margin-top:1rem}.academy-toggle{position:relative;display:inline-block;width:52px;height:30px;flex:0 0 52px}.academy-toggle input{opacity:0;width:0;height:0}.academy-toggle-slider{position:absolute;inset:0;cursor:pointer;background:#9ca3af;border-radius:999px;transition:.2s}.academy-toggle-slider:before{content:"";position:absolute;width:22px;height:22px;left:4px;top:4px;background:white;border-radius:50%;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.25)}.academy-toggle input:checked + .academy-toggle-slider{background:#dc2626}.academy-toggle input:checked + .academy-toggle-slider:before{transform:translateX(22px)}.academy-current{border-radius:12px;padding:1rem 1.1rem;font-weight:700}.academy-current.admin{background:#eaf2ff;color:#174ea6}.academy-current.staff{background:#eef8ff;color:#075985}.academy-current.normal{background:#e9f8ef;color:#116530}.academy-current.maintenance{background:#fff0f0;color:#a61b1b}@media(max-width:767px){.academy-mode-grid{grid-template-columns:1fr}}
html[data-theme="dark"] .academy-mode{border-color:#34495e;background:#102233}html[data-theme="dark"] .academy-mode-copy{color:#9fb0c1}html[data-theme="dark"] .academy-mode:has(input:checked){border-color:#60a5fa}html[data-theme="dark"] .academy-deploy-switch{border-color:#34495e;background:#102233}
</style>
@php
    $mode = $coreSettings->academy_access_mode ?? (($coreSettings->academy_preview_mode ?? true) ? (($coreSettings->academy_staff_access_enabled ?? false) ? 'staff' : 'admin') : 'normal');
    $maintenance = (bool) ($coreSettings->academy_maintenance_mode ?? false);
@endphp
<div class="academy-hero"><div class="container"><a href="{{ route('academy.admin.hub') }}" class="academy-hero-link"><i class="fas fa-arrow-left"></i> Academy</a><div class="academy-kicker mt-3">Master View</div><h1>Academy Deployment</h1><p class="mb-0" style="color:rgba(255,255,255,.65)">Choose exactly who can see the Academy while it is being tested, launched, or maintained.</p></div></div>
<div class="academy-body"><div class="container"><div class="row"><div class="col-lg-8">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="academy-panel">
<div class="academy-kicker mb-2">Access mode</div><h4 class="mb-2">Who can view the Academy?</h4><p class="academy-muted mb-4">Choose one mode. These options are mutually exclusive, so there is no overlap or conflicting switch state.</p>
<form method="POST" action="{{ route('settings.academy.deployment.save') }}">@csrf
<div class="academy-mode-grid">
<label class="academy-mode"><input type="radio" name="academy_access_mode" value="admin" {{ $mode === 'admin' ? 'checked' : '' }}><div class="academy-mode-title"><i class="fas fa-lock mr-1"></i> Admin Only</div><div class="academy-mode-copy">Private development mode. Only administrators can view and work inside the Academy.</div></label>
<label class="academy-mode"><input type="radio" name="academy_access_mode" value="staff" {{ $mode === 'staff' ? 'checked' : '' }}><div class="academy-mode-title"><i class="fas fa-user-shield mr-1"></i> Admin + Instructor/Mentor</div><div class="academy-mode-copy">Staff preview. Admins, instructors and mentors can view it; regular students cannot.</div></label>
<label class="academy-mode"><input type="radio" name="academy_access_mode" value="normal" {{ $mode === 'normal' ? 'checked' : '' }}><div class="academy-mode-title"><i class="fas fa-users mr-1"></i> Normal FIR Permissions</div><div class="academy-mode-copy">Official launch mode. Academy visibility follows the role, enrollment and published-course rules.</div></label>
</div>
<div class="academy-deploy-switch"><div><h5 class="mb-1">Academy Maintenance Mode</h5><div class="academy-muted">Use this while updating a live Academy. Regular students see a maintenance page instead of changing course material. Administrators can always continue editing; enabled Academy staff can still access staff tools.</div></div><label class="academy-toggle"><input type="checkbox" name="academy_maintenance_mode" value="1" {{ $maintenance ? 'checked' : '' }}><span class="academy-toggle-slider"></span></label></div>
<button class="btn btn-primary mt-4"><i class="fas fa-save mr-1"></i> Save Academy status</button>
</form></div></div>
<div class="col-lg-4"><div class="academy-panel"><div class="academy-kicker">Current status</div>
@if($maintenance)<div class="academy-current maintenance mb-3"><i class="fas fa-tools mr-1"></i> MAINTENANCE MODE</div>@endif
@if($mode === 'admin')<div class="academy-current admin"><i class="fas fa-lock mr-1"></i> ADMIN-ONLY PREVIEW</div><p class="academy-muted mt-3">Only administrators can see the Academy. Best for private development.</p>
@elseif($mode === 'staff')<div class="academy-current staff"><i class="fas fa-user-shield mr-1"></i> STAFF PREVIEW</div><p class="academy-muted mt-3">Admins, instructors and mentors can preview. Students remain locked out.</p>
@else<div class="academy-current normal"><i class="fas fa-check-circle mr-1"></i> LIVE — NORMAL FIR PERMISSIONS</div><p class="academy-muted mt-3">The Academy is released. Published courses display according to established Academy permissions and enrollments.</p>@endif
<hr><p class="academy-muted mb-0"><strong>Role protections remain enforced:</strong> mentors cannot grade; instructors can grade and view progress; Enrollments and Academy Editor stay administrator-only.</p></div></div></div></div></div>
@endsection
