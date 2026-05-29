@extends('layouts.master')

@section('title', 'Policies - Winnipeg FIR')
@section('description', 'Policies and Guidelines from the Winnipeg FIR')

@section('content')
<style>
.policies-wrap {
    background: #f6f8fa;
    padding: 2.5rem 0 3rem;
}

/* ── Hero ─────────────────────────────────── */
.policies-hero {
    margin-bottom: 2rem;
}
.policies-hero h1 {
    font-size: 2.6rem;
    font-weight: 800;
    color: #122b44;
    margin-bottom: 0.5rem;
}
.policies-hero p {
    color: #6c757d;
    font-size: 0.95rem;
    max-width: 600px;
    line-height: 1.65;
    margin: 0;
}

/* ── Admin bar ────────────────────────────── */
.policy-admin-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 10px;
    padding: 0.85rem 1.25rem;
    margin-bottom: 2rem;
}
.policy-admin-bar .admin-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(0,0,0,0.35);
    margin-right: 0.5rem;
}
.policy-admin-bar .btn {
    font-size: 0.8rem;
    padding: 0.35rem 0.85rem;
    border-radius: 6px;
    font-weight: 600;
}

/* ── Section block ────────────────────────── */
.policy-section {
    margin-bottom: 2.25rem;
}
.policy-section-title {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(0,0,0,0.4);
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.08);
    margin-bottom: 0.75rem;
}

/* ── Policy card ──────────────────────────── */
.policy-card {
    background: #fff;
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 10px;
    margin-bottom: 0.6rem;
    overflow: hidden;
    transition: box-shadow 0.15s;
}
.policy-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

.policy-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.9rem 1.25rem;
    cursor: pointer;
    gap: 1rem;
    user-select: none;
}
.policy-card-header:hover { background: rgba(18,43,68,0.02); }

.policy-card-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 0;
}
.policy-chevron {
    flex-shrink: 0;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(0,0,0,0.3);
    transition: transform 0.2s;
}
.policy-card.open .policy-chevron { transform: rotate(90deg); }

.policy-name {
    font-size: 0.925rem;
    font-weight: 600;
    color: #122b44;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.policy-staff-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: #f8d7da;
    color: #842029;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.15rem 0.55rem;
    border-radius: 999px;
    white-space: nowrap;
    flex-shrink: 0;
}

.policy-date {
    font-size: 0.78rem;
    color: rgba(0,0,0,0.35);
    white-space: nowrap;
    flex-shrink: 0;
}

.policy-card-body {
    display: none;
    border-top: 1px solid rgba(0,0,0,0.07);
    padding: 1.1rem 1.25rem 1.25rem;
}
.policy-card.open .policy-card-body { display: block; }

.policy-details {
    font-size: 0.875rem;
    color: #495057;
    line-height: 1.65;
    margin-bottom: 1rem;
}
.policy-pdf-link {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #122b44;
    text-decoration: none;
    border: 1px solid rgba(18,43,68,0.2);
    border-radius: 6px;
    padding: 0.4rem 0.85rem;
    transition: all 0.12s;
    margin-bottom: 1rem;
}
.policy-pdf-link:hover { background: rgba(18,43,68,0.05); text-decoration: none; color: #122b44; }
.policy-embed {
    width: 100%;
    height: 600px;
    border: none;
    border-radius: 6px;
    display: block;
    margin-top: 1rem;
}
.policy-admin-actions {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.07);
}
.policy-admin-actions .btn {
    font-size: 0.78rem;
    padding: 0.3rem 0.75rem;
    border-radius: 6px;
    font-weight: 600;
}

/* ── Unsectioned (admin only) ─────────────── */
.unsectioned-block {
    margin-top: 2.5rem;
    padding-top: 1.5rem;
    border-top: 2px dashed rgba(220,53,69,0.3);
}
.unsectioned-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #dc3545;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

@media (max-width: 576px) {
    .policies-hero h1 { font-size: 1.9rem; }
    .policy-card-header { padding: 0.8rem 1rem; }
    .policy-card-body { padding: 0.9rem 1rem 1rem; }
    .policy-date { display: none; }
    .policy-name { white-space: normal; }
}
</style>

<div class="policies-wrap">
    <div class="container">

        <div class="policies-hero">
            <h1>Policies</h1>
            <p>Official policies and guidelines issued by the Winnipeg FIR. Click any policy to view details and download the associated document.</p>
        </div>

        @if(Auth::check() && Auth::user()->permissions >= 4)
        <div class="policy-admin-bar">
            <span class="admin-label">Admin</span>
            <a href="#" data-toggle="modal" data-target="#addPolicyModal" class="btn btn-primary">Add Policy</a>
            <a href="#" data-toggle="modal" data-target="#addPolicySectionModal" class="btn btn-outline-secondary">Add Section</a>
            <a href="#" data-toggle="modal" data-target="#deletePolicySectionModal" class="btn btn-outline-danger">Delete Sections</a>
        </div>
        @endif

        @foreach($policySections as $s)
        <div class="policy-section">
            <div class="policy-section-title">{{ $s->section_name }}</div>
            @foreach($s->policies as $policy)
            <div class="policy-card" id="pcard-{{ $policy->id }}">
                <div class="policy-card-header" onclick="togglePolicy({{ $policy->id }})">
                    <div class="policy-card-left">
                        <span class="policy-chevron">
                            <svg width="7" height="11" viewBox="0 0 7 11" fill="none"><path d="M1 1l5 4.5L1 10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="policy-name">{{ $policy->name }}</span>
                        @if($policy->staff_only == 1)
                        <span class="policy-staff-badge"><i class="fas fa-lock fa-xs"></i> Staff Only</span>
                        @endif
                    </div>
                    <span class="policy-date">Effective {{ $policy->releaseDate }}</span>
                </div>
                <div class="policy-card-body">
                    @if(Auth::check() && Auth::user()->permissions >= 4)
                    <div class="policy-admin-actions">
                        <a href="#" data-toggle="modal" data-target="#editPolicyModal{{ $policy->id }}" class="btn btn-primary">Edit</a>
                        <a href="{{ url('/policies/'.$policy->id.'/delete') }}" class="btn btn-danger">Delete</a>
                    </div>
                    @endif
                    @if($policy->details)
                    <p class="policy-details">{{ $policy->details }}</p>
                    @endif
                    <a target="_blank" href="{{ $policy->link }}" class="policy-pdf-link">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </a>
                    @if($policy->embed == 1)
                    <iframe class="policy-embed" src="{{ $policy->link }}"></iframe>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endforeach

        @auth
        @if(Auth::user()->permissions >= 4)
        <div class="unsectioned-block">
            <div class="unsectioned-title"><i class="fas fa-exclamation-triangle fa-xs"></i> Policies without a section — staff view only</div>
            @if(count($nullPolicies) >= 1)
                @foreach($nullPolicies as $policy)
                <div class="policy-card" id="pcard-{{ $policy->id }}">
                    <div class="policy-card-header" onclick="togglePolicy({{ $policy->id }})">
                        <div class="policy-card-left">
                            <span class="policy-chevron">
                                <svg width="7" height="11" viewBox="0 0 7 11" fill="none"><path d="M1 1l5 4.5L1 10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <span class="policy-name">{{ $policy->name }}</span>
                            @if($policy->staff_only == 1)
                            <span class="policy-staff-badge"><i class="fas fa-lock fa-xs"></i> Staff Only</span>
                            @endif
                        </div>
                        <span class="policy-date">Effective {{ $policy->releaseDate }}</span>
                    </div>
                    <div class="policy-card-body">
                        <div class="policy-admin-actions">
                            <a href="#" data-toggle="modal" data-target="#editPolicyModal{{ $policy->id }}" class="btn btn-primary">Edit</a>
                            <a href="{{ url('/policies/'.$policy->id.'/delete') }}" class="btn btn-danger">Delete</a>
                        </div>
                        @if($policy->details)
                        <p class="policy-details">{{ $policy->details }}</p>
                        @endif
                        <a target="_blank" href="{{ $policy->link }}" class="policy-pdf-link">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                        @if($policy->embed == 1)
                        <iframe class="policy-embed" src="{{ $policy->link }}"></iframe>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p style="color:#6c757d;font-size:0.875rem;">All policies are assigned to a section.</p>
            @endif
        </div>
        @endif
        @endauth

    </div>
</div>

<script>
function togglePolicy(id) {
    var card = document.getElementById('pcard-' + id);
    if (card) card.classList.toggle('open');
}
</script>

{{-- ── Modals ──────────────────────────────────── --}}

@if(Auth::check() && Auth::user()->permissions >= 4)

<div class="modal fade" id="addPolicyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">New Policy</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('policies.create') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Policy Name</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Section</label>
                    <select name="section" class="form-control">
                        <option value="-1" hidden>Select a section…</option>
                        @foreach($policySections as $p)
                        <option value="{{ $p->id }}">{{ $p->section_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Details <small class="text-muted font-weight-normal">(max 250 chars)</small></label>
                    <textarea name="details" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">PDF URL</label>
                    <input type="text" name="link" class="form-control">
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Embed PDF?</label>
                    <select name="embed" class="form-control">
                        <option value="0">No — link only</option>
                        <option value="1">Yes — embed inline</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Privacy</label>
                    <select name="staff_only" class="form-control">
                        <option value="0">Public</option>
                        <option value="1">Staff only</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Publishing Notification</label>
                    <select name="email" class="form-control">
                        <option value="none">Do nothing</option>
                        <option value="all">Email all users &amp; publish news</option>
                        <option value="emailcert">Email certified controllers &amp; publish news</option>
                        <option value="newsonly">Publish as news only</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Effective Date</label>
                    <input type="datetime" id="add-date" name="date" placeholder="Choose a date" class="form-control flatpickr">
                    <script>flatpickr('#add-date', { enableTime: false, dateFormat: "Y-m-d" });</script>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success font-weight-bold">Save Policy</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>

@foreach($allPolicies as $p)
<div class="modal fade" id="editPolicyModal{{ $p->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Edit — {{ $p->name }}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ url('/policies/'.$p->id.'/edit') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Policy Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $p->name }}">
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Section</label>
                    <select name="section" class="form-control">
                        <option value="-1" hidden>Select a section…</option>
                        @foreach($policySections as $ps)
                        <option value="{{ $ps->id }}" {{ $ps->id == $p->section_id ? 'selected' : '' }}>{{ $ps->section_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Details <small class="text-muted font-weight-normal">(max 250 chars)</small></label>
                    <textarea name="details" class="form-control" rows="3">{{ $p->details }}</textarea>
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">PDF URL</label>
                    <input type="text" name="link" class="form-control" value="{{ $p->link }}">
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Embed PDF?</label>
                    <select name="embed" class="form-control">
                        <option value="1" {{ $p->embed == 1 ? 'selected' : '' }}>Yes — embed inline</option>
                        <option value="0" {{ $p->embed == 0 ? 'selected' : '' }}>No — link only</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Privacy</label>
                    <select name="staff_only" class="form-control">
                        <option value="0" {{ $p->staff_only == 0 ? 'selected' : '' }}>Public</option>
                        <option value="1" {{ $p->staff_only == 1 ? 'selected' : '' }}>Staff only</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Effective Date</label>
                    <input type="datetime" id="edit-date-{{ $p->id }}" name="date" placeholder="Choose a date" class="form-control flatpickr">
                    <script>flatpickr('#edit-date-{{ $p->id }}', { enableTime: false, dateFormat: "Y-m-d" });</script>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success font-weight-bold">Save Changes</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<div class="modal fade" id="addPolicySectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">New Policy Section</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('policysection.create') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-form-label font-weight-bold" style="font-size:0.85rem;">Section Name</label>
                    <input type="text" name="name" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success font-weight-bold">Create Section</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deletePolicySectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Delete Policy Sections</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                @foreach($policySections as $s)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid rgba(0,0,0,0.07);">
                    <span style="font-size:0.875rem;font-weight:600;color:#122b44;">{{ $s->section_name }}</span>
                    <a class="btn btn-sm btn-outline-danger font-weight-bold" href="{{ url('/policies/section/'.$s->id.'/delete/') }}">Delete</a>
                </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
            </div>
        </div>
    </div>
</div>

@endif
@endsection
