@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Notifications — Winnipeg FIR')

@section('content')

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container">

    <div class="d-flex align-items-center mb-4">
        <h2 class="font-weight-bold mb-0" style="color:#122b44;">Notifications</h2>
        <form method="POST" action="{{ route('notifications.readall') }}" class="ml-auto">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">Mark all read</button>
        </form>
    </div>

    @if ($notifications->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-bell fa-2x mb-2" style="color:#cbd5e1;"></i>
                <p class="mb-0">You don't have any notifications yet.</p>
            </div>
        </div>
    @else
        <div class="card">
            @foreach ($notifications as $n)
                <div class="d-flex align-items-start notif-page-row" style="gap:0.75rem; padding:0.9rem 1.1rem; border-bottom:1px solid #f1f5f9;">
                    <a href="{{ route('notifications.open', $n->id) }}" class="d-flex align-items-start" style="flex:1; min-width:0; gap:0.75rem; text-decoration:none; color:inherit;">
                        <div style="width:32px; height:32px; border-radius:8px; background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas {{ $n->data['icon'] ?? 'fa-bell' }}" style="color:#2563eb; font-size:0.8rem;"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; align-items:center; gap:0.4rem; font-weight:600; font-size:0.9rem; color:#1e293b;">
                                {{ $n->data['title'] ?? '' }}
                                @unless($n->read_at)
                                    <span style="width:6px; height:6px; border-radius:50%; background:#2563eb; flex-shrink:0;"></span>
                                @endunless
                            </div>
                            <div style="font-size:0.82rem; color:#64748b;">{{ $n->data['body'] ?? '' }}</div>
                            <div style="font-size:0.72rem; color:#94a3b8; margin-top:0.2rem;">{{ $n->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('notifications.destroy', $n->id) }}" onsubmit="return confirm('Delete this notification?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm" title="Delete" style="color:#adb5bd; border:none; background:none; padding:0.2rem 0.4rem;">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
</div>

@stop
