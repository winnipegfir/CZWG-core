@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Students — Winnipeg FIR')

@section('content')
@include('includes.trainingMenu')

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container">

    <div class="d-flex align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold mb-0" style="color:#122b44;">Linked Students</h2>
            <p class="text-muted mb-0" style="font-size:0.875rem;">{{ $students->count() }} student{{ $students->count() != 1 ? 's' : '' }}</p>
        </div>
        @if(Auth::user()->permissions >= 4)
        <button type="button" class="btn btn-sm btn-primary ml-auto" data-toggle="modal" data-target="#addLinkedStudent">
            <i class="fas fa-plus fa-xs mr-1"></i> Add Student
        </button>
        @endif
    </div>

    @if($vatcanOnlyCount > 0)
    <div style="background:#fef3c7; border:1px solid #fde68a; border-radius:0.5rem; padding:0.7rem 1rem; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; gap:1rem;">
        <span style="font-size:0.875rem; color:#92400e;">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            <strong>{{ $vatcanOnlyCount }} student{{ $vatcanOnlyCount != 1 ? 's' : '' }}</strong> {{ $vatcanOnlyCount != 1 ? 'have' : 'has' }} an instructor assigned on VATCAN but {{ $vatcanOnlyCount != 1 ? 'aren\'t' : 'isn\'t' }} in this list.
        </span>
        <a href="{{ route('training.reconcile') }}" style="font-size:0.8rem; font-weight:600; color:#92400e; white-space:nowrap;">View details &rarr;</a>
    </div>
    @endif

    @if($students->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-user-graduate fa-2x mb-2" style="color:#cbd5e1;"></i>
                <p class="mb-0">No students in this category.</p>
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
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.875rem;">
                    <thead style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                        <tr>
                            @if(Auth::user()->permissions >= 4)
                            <th style="width:36px; border-top:none;">
                                <input type="checkbox" id="selectAll" style="cursor:pointer;">
                            </th>
                            @endif
                            <th style="color:#64748b; font-weight:600; border-top:none;">Student</th>
                            <th style="color:#64748b; font-weight:600; border-top:none;">Rating</th>
                            <th style="color:#64748b; font-weight:600; border-top:none;">Type</th>
                            <th style="color:#64748b; font-weight:600; border-top:none;">Instructor</th>
                            <th style="color:#64748b; font-weight:600; border-top:none; width:120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            @if(Auth::user()->permissions >= 4)
                            <td style="vertical-align:middle;">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="row-check" style="cursor:pointer;">
                            </td>
                            @endif
                            <td style="vertical-align:middle;">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $student->user->avatar() }}" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1px solid #e2e8f0; margin-right:0.65rem; flex-shrink:0;">
                                    <div>
                                        <a href="{{ route('training.students.view', $student->id) }}" style="font-weight:600; color:#122b44; text-decoration:none;">
                                            {{ $student->user->fullName('FL') }}
                                        </a>
                                        <div style="font-size:0.75rem; color:#94a3b8;">{{ $student->user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="vertical-align:middle; color:#495057;">{{ $student->user->rating->getShortName() }}</td>
                            <td style="vertical-align:middle;">
                                @if($student->entry_type == 'New Student')
                                    <span style="background:#dbeafe; color:#1d4ed8; font-size:0.72rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Home</span>
                                @elseif($student->entry_type == 'New Visitor')
                                    <span style="background:#dcfce7; color:#15803d; font-size:0.72rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Visiting</span>
                                @elseif($student->entry_type == 'New Transfer')
                                    <span style="background:#f3e8ff; color:#7e22ce; font-size:0.72rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Transfer</span>
                                @else
                                    <span style="background:#f1f5f9; color:#64748b; font-size:0.72rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">{{ $student->entry_type }}</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle; color:#495057;">
                                @if($student->instructor)
                                    {{ $student->instructor->user->fullName('FL') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                                @if($student->mentorable)
                                    <span style="background:#dbeafe; color:#1d4ed8; font-size:0.68rem; font-weight:700; padding:0.15em 0.45em; border-radius:0.3rem; margin-left:0.35rem;" title="Open to booking with any instructor">Mentorable</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle;">
                                <div class="d-flex align-items-center" style="gap:0.35rem;">
                                    <a href="{{ route('training.students.view', $student->id) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.78rem;">View</a>
                                    @if(Auth::user()->permissions >= 4)
                                    <form method="POST" action="{{ route('training.students.remove', $student->id) }}" onsubmit="return confirm('Remove {{ $student->user->fullName('FL') }}?')">
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
</div>

@if(Auth::user()->permissions >= 4)
<div class="modal fade" id="addLinkedStudent" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Add Linked Student</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('training.students.add.linked') }}">
                @csrf
                <input type="hidden" name="add_method" id="linkedAddMethod" value="existing">
                <div class="modal-body">
                    <div class="btn-group btn-group-sm w-100 mb-3" role="group">
                        <button type="button" class="btn btn-primary" id="linkedTabExisting" onclick="setLinkedMethod('existing')">Existing User</button>
                        <button type="button" class="btn btn-outline-primary" id="linkedTabCid" onclick="setLinkedMethod('cid')">By CID</button>
                    </div>

                    <div id="linkedPanelExisting">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small">Student</label>
                            <select name="student_id" id="linkedStudentSelect" class="js-linked-select form-control">
                                @foreach($potentialstudent as $u)
                                    <option value="{{ $u->id }}">{{ $u->id }} &mdash; {{ $u->fullName('FL') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="linkedPanelCid" style="display:none;">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small">VATSIM CID</label>
                            <input type="number" name="cid_input" id="linkedCidInput" class="form-control" placeholder="e.g. 1234567">
                            <small class="text-muted">Their name will appear automatically once they log in.</small>
                        </div>
                    </div>

                    <div id="linkedMemberBanner" style="display:none;" class="py-2 mb-3"></div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Entry Type</label>
                        <select name="entry_type" id="linkedEntryType" class="form-control">
                            <option value="New Student">New Student (Home)</option>
                            <option value="New Visitor">New Visitor</option>
                            <option value="New Transfer">New Transfer</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Instructor</label>
                        <select name="instructor_id" class="form-control">
                            <option value="">— None —</option>
                            @foreach($instructors as $i)
                                <option value="{{ $i->id }}">{{ $i->user->fullName('FL') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<style>.select2-container { width: 100% !important; }</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
const LINKED_CHECK_URL = '{{ route("training.students.checkmembership") }}';

function linkedApplyMembership(data) {
    const banner = document.getElementById('linkedMemberBanner');
    const select = document.getElementById('linkedEntryType');
    select.querySelectorAll('option').forEach(o => o.disabled = false);

    if (data.status === 'error') {
        banner.className = 'alert alert-warning py-2 mb-3';
        banner.style.display = '';
        banner.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Could not verify VATCAN membership — double-check the entry type manually.';
        return;
    }
    if (data.type === 'home') {
        banner.style.display = 'none';
    } else if (data.type === 'visitor') {
        banner.className = 'alert alert-info py-2 mb-3';
        banner.style.display = '';
        banner.innerHTML = '<i class="fas fa-info-circle mr-1"></i> This person is on the CZWG <strong>visitor</strong> roster — entry type locked to <strong>New Visitor</strong>.';
        select.value = 'New Visitor';
        select.querySelectorAll('option:not([value="New Visitor"])').forEach(o => o.disabled = true);
    } else {
        banner.className = 'alert alert-warning py-2 mb-3';
        banner.style.display = '';
        banner.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> <strong>This person is not on the CZWG home roster.</strong> Are they visiting? Entry type set to <strong>New Visitor</strong>.';
        select.value = 'New Visitor';
        select.querySelectorAll('option:not([value="New Visitor"])').forEach(o => o.disabled = true);
    }
}

function linkedCheckMembership(cid) {
    if (!cid) return;
    const banner = document.getElementById('linkedMemberBanner');
    banner.className = 'alert alert-secondary py-2 mb-3';
    banner.style.display = '';
    banner.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Checking VATCAN roster…';
    fetch(LINKED_CHECK_URL + '?cid=' + cid, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(linkedApplyMembership)
        .catch(() => {
            banner.className = 'alert alert-warning py-2 mb-3';
            banner.style.display = '';
            banner.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Could not reach VATCAN — double-check the entry type manually.';
        });
}

function linkedResetBanner() {
    const banner = document.getElementById('linkedMemberBanner');
    const select = document.getElementById('linkedEntryType');
    banner.style.display = 'none';
    select.querySelectorAll('option').forEach(o => o.disabled = false);
    select.value = 'New Student';
}

$(document).ready(function () {
    $('.js-linked-select').select2({ dropdownParent: $('#addLinkedStudent'), width: '100%' });

    $('#linkedStudentSelect').on('change', function () {
        const cid = parseInt($(this).val());
        if (cid) linkedCheckMembership(cid); else linkedResetBanner();
    });
});

document.getElementById('linkedCidInput').addEventListener('blur', function () {
    const cid = parseInt(this.value);
    if (cid) linkedCheckMembership(cid); else linkedResetBanner();
});

$('#addLinkedStudent').on('hidden.bs.modal', linkedResetBanner);

function setLinkedMethod(method) {
    document.getElementById('linkedAddMethod').value = method;
    document.getElementById('linkedPanelExisting').style.display = method === 'existing' ? '' : 'none';
    document.getElementById('linkedPanelCid').style.display = method === 'cid' ? '' : 'none';
    document.getElementById('linkedTabExisting').className = method === 'existing' ? 'btn btn-primary' : 'btn btn-outline-primary';
    document.getElementById('linkedTabCid').className = method === 'cid' ? 'btn btn-primary' : 'btn btn-outline-primary';
    linkedResetBanner();
}
</script>
@endif

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

@stop
