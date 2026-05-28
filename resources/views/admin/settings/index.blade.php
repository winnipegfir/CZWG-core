@extends('layouts.master')
@section('content')
<style>
.settings-hero {
    background: linear-gradient(135deg, #0a1828 0%, #0d1f33 60%, #122b44 100%);
    padding: 2rem 0 1.75rem;
    color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.settings-hero a.back-link {
    display: inline-flex; align-items: center; gap: 0.4rem;
    color: rgba(255,255,255,0.45); font-size: 0.78rem;
    text-decoration: none; margin-bottom: 0.75rem; transition: color 0.15s;
}
.settings-hero a.back-link:hover { color: rgba(255,255,255,0.9); }
.settings-hero h1 { font-size: 1.75rem; font-weight: 800; margin: 0; }

.settings-body { background: #f6f8fa; padding: 2rem 0 3rem; }

.setting-card {
    display: block;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 10px;
    padding: 1.35rem 1.4rem 1.1rem;
    text-decoration: none;
    color: inherit;
    height: 100%;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.setting-card:hover {
    border-color: #122b44;
    box-shadow: 0 2px 12px rgba(18,43,68,0.1);
    text-decoration: none;
    color: inherit;
}
.setting-card-icon {
    width: 36px; height: 36px;
    background: rgba(18,43,68,0.08);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: #122b44;
    font-size: 0.9rem;
    margin-bottom: 0.85rem;
}
.setting-card-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #122b44;
    margin-bottom: 0.3rem;
}
.setting-card-desc {
    font-size: 0.8rem;
    color: rgba(0,0,0,0.45);
    line-height: 1.5;
    margin: 0;
}
</style>

<div class="settings-hero">
    <div class="container">
        <a href="{{ route('dashboard.index') }}" class="back-link">
            <i class="fas fa-arrow-left fa-xs"></i> Dashboard
        </a>
        <h1>Settings</h1>
    </div>
</div>

<div class="settings-body">
    <div class="container">
        <div class="row">

            <div class="col-md-3 col-sm-6 mb-3">
                <a href="{{ route('settings.siteinformation') }}" class="setting-card">
                    <div class="setting-card-icon"><i class="fas fa-info-circle"></i></div>
                    <div class="setting-card-title">Site Information</div>
                    <p class="setting-card-desc">Manage the website version, copyright, and system name.</p>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <a href="{{ route('settings.emails') }}" class="setting-card">
                    <div class="setting-card-icon"><i class="fas fa-envelope"></i></div>
                    <div class="setting-card-title">Emails</div>
                    <p class="setting-card-desc">Set staff email addresses for notifications and correspondence.</p>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <a href="{{ route('settings.banner') }}" class="setting-card">
                    <div class="setting-card-icon"><i class="fas fa-bullhorn"></i></div>
                    <div class="setting-card-title">Banner</div>
                    <p class="setting-card-desc">Set a site-wide banner for maintenance notices or announcements.</p>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <a href="{{ route('settings.staff') }}" class="setting-card">
                    <div class="setting-card-icon"><i class="fas fa-users"></i></div>
                    <div class="setting-card-title">Staff</div>
                    <p class="setting-card-desc">Manage the staff list shown on the public staff page.</p>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <a href="{{ route('settings.images') }}" class="setting-card">
                    <div class="setting-card-icon"><i class="fas fa-image"></i></div>
                    <div class="setting-card-title">Homepage Images</div>
                    <p class="setting-card-desc">Add, remove, or edit the rotating background photos on the homepage.</p>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <a href="{{ route('settings.towns') }}" class="setting-card">
                    <div class="setting-card-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="setting-card-title">Homepage Towns</div>
                    <p class="setting-card-desc">Manage the towns that cycle through the "We Are X." hero text.</p>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <a href="{{ route('settings.auditlog') }}" class="setting-card">
                    <div class="setting-card-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div class="setting-card-title">Audit Log</div>
                    <p class="setting-card-desc">View a log of all core system events and admin actions.</p>
                </a>
            </div>

        </div>
    </div>
</div>
@stop
