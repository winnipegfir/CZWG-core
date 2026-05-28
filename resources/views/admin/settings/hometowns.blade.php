@extends('layouts.master')
@section('content')
<style>
.ht-hero {
    background: linear-gradient(135deg, #0a1828 0%, #0d1f33 60%, #122b44 100%);
    padding: 1.75rem 0 1.5rem;
    color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.ht-hero a.back-link {
    display: inline-flex; align-items: center; gap: 0.4rem;
    color: rgba(255,255,255,0.45); font-size: 0.78rem;
    text-decoration: none; margin-bottom: 0.75rem; transition: color 0.15s;
}
.ht-hero a.back-link:hover { color: rgba(255,255,255,0.9); }
.ht-hero h1 { font-size: 1.6rem; font-weight: 800; margin: 0 0 0.2rem; }
.ht-hero p { color: rgba(255,255,255,0.45); font-size: 0.82rem; margin: 0; }

.ht-body { background: #f6f8fa; padding: 1.75rem 0 3rem; }
.ht-card {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 10px;
    overflow: hidden;
}
.ht-card-header {
    padding: 0.85rem 1.25rem;
    border-bottom: 1px solid rgba(0,0,0,0.07);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ht-card-header-title {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(0,0,0,0.3);
}
.ht-town-row {
    display: flex;
    align-items: center;
    padding: 0.6rem 1.25rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    gap: 0.75rem;
}
.ht-town-row:last-child { border-bottom: none; }
.ht-town-row:hover { background: #fafafa; }
.ht-town-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1a2a3a;
    flex: 1;
}
.ht-icao {
    font-family: monospace;
    font-size: 0.75rem;
    font-weight: 700;
    background: rgba(18,43,68,0.07);
    color: #122b44;
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    letter-spacing: 0.05em;
    min-width: 52px;
    text-align: center;
}
.ht-icao-empty {
    min-width: 52px;
    text-align: center;
    color: rgba(0,0,0,0.2);
    font-size: 0.75rem;
}
.ht-remove {
    background: none;
    border: none;
    color: rgba(0,0,0,0.2);
    font-size: 0.75rem;
    cursor: pointer;
    padding: 0.2rem 0.3rem;
    border-radius: 4px;
    transition: color 0.15s, background 0.15s;
    flex-shrink: 0;
}
.ht-remove:hover { color: #dc2626; background: rgba(220,38,38,0.07); }

.ht-add-card {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 10px;
    padding: 1.25rem 1.35rem;
    position: sticky;
    top: 1rem;
}
.ht-add-card h6 {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(0,0,0,0.3);
    margin-bottom: 1rem;
}
.ht-form-group { margin-bottom: 0.85rem; }
.ht-form-group label {
    font-size: 0.72rem;
    font-weight: 600;
    color: rgba(0,0,0,0.5);
    display: block;
    margin-bottom: 0.3rem;
}
</style>

<div class="ht-hero">
    <div class="container">
        <a href="{{ route('settings.index') }}" class="back-link">
            <i class="fas fa-arrow-left fa-xs"></i> Settings
        </a>
        <h1>Homepage Towns</h1>
        <p>Towns that cycle through the "We Are <em>X</em>." hero text — {{ $towns->count() }} in rotation.</p>
    </div>
</div>

<div class="ht-body">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success mb-3 py-2">{{ session('success') }}</div>
        @endif
        <div class="row">
            <div class="col-md-8">
                <div class="ht-card">
                    <div class="ht-card-header">
                        <span class="ht-card-header-title">Town</span>
                        <span class="ht-card-header-title">ICAO</span>
                    </div>
                    @foreach($towns as $town)
                    <div class="ht-town-row">
                        <span class="ht-town-name">{{ $town->name }}</span>
                        @if($town->icao)
                            <span class="ht-icao">{{ $town->icao }}</span>
                        @else
                            <span class="ht-icao-empty">—</span>
                        @endif
                        <a href="{{ route('settings.towns.delete', $town->id) }}"
                           class="ht-remove"
                           onclick="return confirm('Remove {{ $town->name }}?')"
                           title="Remove">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-md-4">
                <div class="ht-add-card">
                    <h6>Add Town</h6>
                    <form method="POST" action="{{ route('settings.towns.add') }}">
                        @csrf
                        <div class="ht-form-group">
                            <label>Town Name <span class="text-danger">*</span></label>
                            <input name="name" class="form-control form-control-sm" placeholder="e.g. Portage la Prairie" required>
                        </div>
                        <div class="ht-form-group">
                            <label>ICAO <span style="color:rgba(0,0,0,0.3); font-weight:400;">(optional)</span></label>
                            <input name="icao" class="form-control form-control-sm" placeholder="e.g. CYPG" maxlength="4" style="text-transform:uppercase; font-family:monospace;">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary btn-block">
                            <i class="fas fa-plus fa-xs"></i> Add Town
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
