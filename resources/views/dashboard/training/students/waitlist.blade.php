@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('content')
@include('includes.trainingMenu')

<style>
.wl-badge {
    display: inline-block;
    padding: 0.2em 0.55em;
    border-radius: 0.3rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.wl-badge-home     { background:#dbeafe; color:#1d4ed8; }
.wl-badge-visit    { background:#dcfce7; color:#15803d; }
.wl-badge-transfer { background:#f3e8ff; color:#7e22ce; }
.wl-filter-tabs { display:flex; gap:0.4rem; margin-bottom:1rem; }
.wl-filter-tab {
    padding:0.3rem 0.85rem;
    border-radius:999px;
    font-size:0.8rem;
    font-weight:600;
    text-decoration:none;
    background:#f1f5f9;
    color:#475569;
    border:1px solid #e2e8f0;
    transition:background 0.15s;
}
.wl-filter-tab:hover { background:#e2e8f0; color:#1e293b; text-decoration:none; }
.wl-filter-tab.active { background:#122b44; color:#fff; border-color:#122b44; }
</style>

<div class="container" style="margin-top:1.5rem; margin-bottom:3rem;">

    <div class="d-flex align-items-center mb-1">
        <h1 class="font-weight-bold blue-text mb-0">Waitlist</h1>
        @if(Auth::user()->permissions >= 4)
        <button type="button" class="btn btn-sm btn-primary ml-auto" data-toggle="modal" data-target="#newStudent">
            <i class="fas fa-plus fa-xs mr-1"></i> Add to Waitlist
        </button>
        @endif
    </div>
    <p class="text-muted mb-3" style="font-size:0.875rem;">
        {{ $students->count() }} student{{ $students->count() != 1 ? 's' : '' }} waiting &mdash; ordered by date added.
    </p>

    <div class="wl-filter-tabs">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="wl-filter-tab {{ $filter === 'all' ? 'active' : '' }}">All</a>
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'home']) }}" class="wl-filter-tab {{ $filter === 'home' ? 'active' : '' }}">Home</a>
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'visiting']) }}" class="wl-filter-tab {{ $filter === 'visiting' ? 'active' : '' }}">Visiting</a>
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'transfer']) }}" class="wl-filter-tab {{ $filter === 'transfer' ? 'active' : '' }}">Transfer</a>
    </div>


    @if($students->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-check-circle fa-2x mb-2" style="color:#86efac;"></i>
                <p class="mb-0">The waitlist is empty.</p>
            </div>
        </div>
    @else
        @if(Auth::user()->permissions >= 4)
        <form method="POST" action="{{ route('training.students.bulkremove') }}" id="bulkForm">
            @csrf
        </form>
        <div id="bulkBar" style="display:none; background:#fee2e2; border:1px solid #fecaca; border-radius:0.5rem; padding:0.6rem 1rem; margin-bottom:0.75rem; align-items:center; gap:0.75rem;">
            <span id="bulkCount" style="font-size:0.875rem; color:#b91c1c; font-weight:600;"></span>
            <button type="button" class="btn btn-sm btn-danger py-0 px-3" style="font-size:0.8rem;" onclick="submitBulk()">Remove Selected</button>
            <button type="button" class="btn btn-sm btn-light py-0 px-2" style="font-size:0.8rem;" onclick="clearSelection()">Clear</button>
        </div>
        @endif
        <div class="card">
            <div class="table-responsive" style="overflow:visible;">
                <table class="table table-hover mb-0" style="font-size:0.875rem;">
                    <thead style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                        <tr>
                            @if(Auth::user()->permissions >= 4)
                            <th style="width:36px;">
                                <input type="checkbox" id="selectAll" style="cursor:pointer;">
                            </th>
                            @endif
                            <th style="width:42px; color:#64748b; font-weight:600;">#</th>
                            <th style="color:#64748b; font-weight:600;">Name</th>
                            <th style="color:#64748b; font-weight:600;">Type</th>
                            <th style="color:#64748b; font-weight:600;">Added</th>
                            <th style="color:#64748b; font-weight:600;">Waiting</th>
                            <th style="width:100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        <tr>
                            @if(Auth::user()->permissions >= 4)
                            <td style="vertical-align:middle;">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="row-check" style="cursor:pointer;">
                            </td>
                            @endif
                            <td class="text-muted font-weight-bold">{{ $i + 1 }}</td>
                            <td>
                                <a href="{{ route('training.students.view', $student->id) }}" style="color:#122b44; font-weight:600;">
                                    {{ $student->user ? $student->user->fullName('FL') : 'CID ' . $student->user_id }}
                                </a>
                                <br>
                                <span class="text-muted" style="font-size:0.78rem;">{{ $student->user_id }}</span>
                            </td>
                            <td>
                                @if($student->entry_type == 'New Student')
                                    <span class="wl-badge wl-badge-home">Home</span>
                                @elseif($student->entry_type == 'New Visitor')
                                    <span class="wl-badge wl-badge-visit">Visiting</span>
                                @elseif($student->entry_type == 'New Transfer')
                                    <span class="wl-badge wl-badge-transfer">Transfer</span>
                                @else
                                    <span class="wl-badge" style="background:#f1f5f9;color:#64748b;">{{ $student->entry_type }}</span>
                                @endif
                            </td>
                            <td style="color:#495057;">
                                @if($student->waitlist_added_at)
                                    {{ $student->waitlist_added_at->format('M j, Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="color:#495057;">
                                @if($student->waitlist_added_at)
                                    {{ $student->waitlist_added_at->diffForHumans(null, true) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle;">
                                <div class="d-flex align-items-center" style="gap:0.35rem;">
                                    <a href="{{ route('training.students.view', $student->id) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.78rem;">View</a>
                                    @if(Auth::user()->permissions >= 4)
                                    <form method="POST" action="{{ route('training.students.remove', $student->id) }}" onsubmit="return confirm('Remove {{ $student->user ? $student->user->fullName('FL') : 'CID ' . $student->user_id }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:0.78rem;">Remove</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@if(Auth::user()->permissions >= 4)
<script>
const selectAll = document.getElementById('selectAll');
const bulkBar = document.getElementById('bulkBar');
const bulkCount = document.getElementById('bulkCount');

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    bulkBar.style.display = checked.length > 0 ? 'flex' : 'none';
    bulkCount.textContent = checked.length + ' student' + (checked.length !== 1 ? 's' : '') + ' selected';
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(c => c.checked = false);
    if (selectAll) selectAll.checked = false;
    updateBulkBar();
}

function submitBulk() {
    const checked = document.querySelectorAll('.row-check:checked');
    if (!checked.length) return;
    if (!confirm('Remove ' + checked.length + ' student' + (checked.length !== 1 ? 's' : '') + ' from the training system?')) return;
    const form = document.getElementById('bulkForm');
    form.querySelectorAll('input[name="student_ids[]"]').forEach(i => i.remove());
    checked.forEach(c => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = c.value;
        form.appendChild(input);
    });
    form.submit();
}

if (selectAll) {
    selectAll.addEventListener('change', function() {
        document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
        updateBulkBar();
    });
}
document.querySelectorAll('.row-check').forEach(c => c.addEventListener('change', updateBulkBar));
</script>
@endif

{{-- Add to Waitlist Modal (staff only) --}}
@if(Auth::user()->permissions >= 4)
<div class="modal fade" id="newStudent" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Add Student to Waitlist</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('instructor.student.add.new') }}">
                @csrf
                <input type="hidden" name="add_method" id="addMethod" value="existing">
                <div class="modal-body">
                    <div class="btn-group btn-group-sm w-100 mb-3" role="group">
                        <button type="button" class="btn btn-primary" id="tabExisting" onclick="setAddMethod('existing')">Existing User</button>
                        <button type="button" class="btn btn-outline-primary" id="tabCid" onclick="setAddMethod('cid')">By CID</button>
                    </div>

                    <div id="panelExisting">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small">Student</label>
                            <select name="student_id" class="js-example-basic-single form-control">
                                @foreach($potentialstudent as $u)
                                    <option value="{{ $u->id }}">{{ $u->id }} &mdash; {{ $u->fullName('FL') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="panelCid" style="display:none;">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small">VATSIM CID</label>
                            <input type="number" name="cid_input" class="form-control" placeholder="e.g. 1234567">
                            <small class="text-muted">Their name will appear automatically once they log in.</small>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Entry Type</label>
                        <select name="entry_type" class="form-control">
                            <option value="New Student">New Student (Home)</option>
                            <option value="New Visitor">New Visitor</option>
                            <option value="New Transfer">New Transfer</option>
                        </select>
                    </div>
                    <input type="hidden" name="instructor" value="unassign">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to Waitlist</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<style>.select2-container { width: 100% !important; }</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.js-example-basic-single').select2({ dropdownParent: $('#newStudent'), width: '100%' });
    });

    function setAddMethod(method) {
        document.getElementById('addMethod').value = method;
        document.getElementById('panelExisting').style.display = method === 'existing' ? '' : 'none';
        document.getElementById('panelCid').style.display = method === 'cid' ? '' : 'none';
        document.getElementById('tabExisting').className = method === 'existing' ? 'btn btn-primary' : 'btn btn-outline-primary';
        document.getElementById('tabCid').className = method === 'cid' ? 'btn btn-primary' : 'btn btn-outline-primary';
    }
</script>

@stop
