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
    </div>

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
                                    <span style="background:#e0f2fe; color:#0369a1; font-size:0.72rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem;">Visiting</span>
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
