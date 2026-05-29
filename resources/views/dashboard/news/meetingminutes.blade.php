@extends('layouts.master')
@section('title', 'Meeting Minutes - Winnipeg FIR')
@section('description', 'View Winnipeg FIR staff meeting minutes')

@section('content')
<style>
.minutes-wrap {
    background: #f6f8fa;
    padding: 2.5rem 0 3rem;
}

/* ── Hero ─────────────────────────────────── */
.minutes-hero {
    margin-bottom: 2rem;
}
.minutes-hero h1 {
    font-size: 2.6rem;
    font-weight: 800;
    color: #122b44;
    margin-bottom: 0.5rem;
}
.minutes-hero p {
    color: #6c757d;
    font-size: 0.95rem;
    max-width: 560px;
    line-height: 1.65;
    margin: 0;
}

/* ── Admin bar ────────────────────────────── */
.minutes-admin-bar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 10px;
    padding: 0.85rem 1.25rem;
    margin-bottom: 1.5rem;
}
.minutes-admin-bar .admin-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(0,0,0,0.35);
    margin-right: 0.25rem;
}

/* ── Minutes list ─────────────────────────── */
.minutes-list {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}
.minutes-item {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    transition: box-shadow 0.15s;
}
.minutes-item:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

.minutes-item-left {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    min-width: 0;
}
.minutes-icon {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    background: rgba(18,43,68,0.06);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #122b44;
    font-size: 0.85rem;
}
.minutes-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #122b44;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.minutes-author {
    font-size: 0.75rem;
    color: rgba(0,0,0,0.35);
    margin-top: 0.1rem;
}

.minutes-item-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}
.minutes-view-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: #122b44;
    text-decoration: none;
    border: 1px solid rgba(18,43,68,0.2);
    border-radius: 6px;
    padding: 0.35rem 0.8rem;
    transition: all 0.12s;
    white-space: nowrap;
}
.minutes-view-btn:hover { background: rgba(18,43,68,0.05); text-decoration: none; color: #122b44; }

.minutes-delete-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: #dc3545;
    text-decoration: none;
    border: 1px solid rgba(220,53,69,0.25);
    border-radius: 6px;
    padding: 0.35rem 0.8rem;
    transition: all 0.12s;
    white-space: nowrap;
}
.minutes-delete-btn:hover { background: rgba(220,53,69,0.05); text-decoration: none; color: #dc3545; }

.minutes-empty {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 10px;
    padding: 2.5rem;
    text-align: center;
    color: rgba(0,0,0,0.3);
    font-size: 0.9rem;
    font-style: italic;
}

@media (max-width: 576px) {
    .minutes-hero h1 { font-size: 1.9rem; }
    .minutes-title { white-space: normal; }
    .minutes-author { display: none; }
}
</style>

<div class="minutes-wrap">
    <div class="container">

        <div class="minutes-hero">
            <h1>Meeting Minutes</h1>
            <p>Records from Winnipeg FIR staff meetings, available for all members to review.</p>
        </div>

        @if(Auth::check() && Auth::user()->permissions == 5)
        <div class="minutes-admin-bar">
            <span class="admin-label">Admin</span>
            <a href="#" data-toggle="modal" data-target="#uploadModal" class="btn btn-primary btn-sm font-weight-bold">Upload Minutes</a>
        </div>
        @endif

        @if(count($minutes) >= 1)
        <div class="minutes-list">
            @foreach($minutes as $m)
            <div class="minutes-item">
                <div class="minutes-item-left">
                    <div class="minutes-icon"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <div class="minutes-title">{{ $m->title }}</div>
                        @if(Auth::check() && Auth::user()->permissions >= 4)
                        <div class="minutes-author">Uploaded by {{ \App\Models\Users\User::find($m->user_id)->fullName('FLC') }}</div>
                        @endif
                    </div>
                </div>
                <div class="minutes-item-right">
                    <a target="_blank" href="{{ $m->link }}" class="minutes-view-btn">
                        <i class="fas fa-external-link-alt fa-xs"></i> View
                    </a>
                    @if(Auth::check() && Auth::user()->permissions == 5)
                    <a href="{{ route('meetingminutes.delete', $m->id) }}" class="minutes-delete-btn">
                        <i class="fas fa-times fa-xs"></i> Delete
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="minutes-empty">No meeting minutes have been posted yet.</div>
        @endif

    </div>
</div>

@if(Auth::check() && Auth::user()->permissions == 5)
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Upload Meeting Minutes</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('meetingminutes.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold" style="font-size:0.85rem;">Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. April 2025 Staff Meeting">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold" style="font-size:0.85rem;">File</label>
                    <input type="file" name="file" class="form-control-file">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success font-weight-bold">Upload</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
