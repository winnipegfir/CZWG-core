@extends('layouts.dashboard')
@section('title', 'Academy Student Progress - Winnipeg FIR')
@section('content')
@include('academy._styles')
<style>
.academy-progress-table{min-width:900px}.academy-progress-table th{font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;color:#6b7785;border-top:0;white-space:nowrap}.academy-progress-table td{vertical-align:middle}.academy-progress-student{min-width:205px}.academy-progress-search{max-width:480px}.academy-progress-rating{display:inline-block;margin-top:.35rem;padding:.15rem .45rem;border-radius:999px;background:#edf2f7;color:#44505c;font-size:.7rem;font-weight:700;letter-spacing:.03em}.academy-course-cell{min-width:135px}.academy-progress-count{font-size:.72rem;color:#6b7785;margin-top:.3rem;white-space:nowrap}
html[data-theme="dark"] .academy-progress-table{color:#e4e7eb}html[data-theme="dark"] .academy-progress-table td,html[data-theme="dark"] .academy-progress-table th{border-color:#303640}html[data-theme="dark"] .academy-progress-table th,html[data-theme="dark"] .academy-progress-count{color:#9ba5b1}html[data-theme="dark"] .academy-progress-rating{background:#303640;color:#d8dde5}
</style>
<div class="academy-hero">
    <div class="container">
        <a href="{{ route('academy.admin.hub') }}" class="academy-hero-link"><i class="fas fa-arrow-left"></i> Academy</a>
        <div class="academy-kicker mt-3">Training Oversight</div>
        <h1>Academy Student Progress</h1>
        <p class="mb-0" style="color:rgba(255,255,255,.65)">Track module activity and course completion across the Academy.</p>
    </div>
</div>
<div class="academy-body">
    <div class="container-fluid px-lg-5">
        <div class="academy-panel">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <div>
                    <h5 class="font-weight-bold mb-1">Student progress</h5>
                    <div class="academy-muted">{{ $students->count() }} eligible Academy member{{ $students->count() === 1 ? '' : 's' }}</div>
                </div>
                <div class="academy-muted mt-2 mt-md-0">
                    <span class="academy-progress academy-progress-complete">Complete</span>
                    <span class="academy-progress academy-progress-in_progress ml-1">In progress</span>
                    <span class="academy-progress academy-progress-not_started ml-1">Not started</span>
                </div>
            </div>
            <form method="GET" action="{{ route('academy.admin.progress') }}" class="academy-progress-search mb-4">
                <div class="input-group">
                    <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Search student name or CID" aria-label="Search student name or CID">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search mr-1"></i> Search</button>
                        @if($search !== '')
                            <a href="{{ route('academy.admin.progress') }}" class="btn btn-outline-secondary">Clear</a>
                        @endif
                    </div>
                </div>
                <div class="academy-muted mt-2">Sorted by VATSIM rating so Observers and S1 trainees appear first.</div>
            </form>
            <div class="table-responsive">
                <table class="table academy-progress-table mb-0">
                    <thead>
                        <tr>
                            <th class="academy-progress-student">Student</th>
                            @foreach($courses as $course)
                                <th class="academy-course-cell">{{ $course->title }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td class="academy-progress-student">
                                    <strong>{{ $student->fullName('FL') }}</strong>
                                    <div class="academy-muted">CID {{ $student->id }}</div>
                                    <span class="academy-progress-rating">{{ $student->rating->getShortName() }}</span>
                                </td>
                                @foreach($courses as $course)
                                    @php($item = $progress[$student->id][$course->id])
                                    <td class="academy-course-cell">
                                        <span class="academy-progress academy-progress-{{ $item['status'] }}">{{ str_replace('_', ' ', $item['status']) }}</span>
                                        <div class="academy-progress-count">{{ $item['viewed'] }} / {{ $item['total'] }} modules viewed</div>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ max(1, $courses->count() + 1) }}" class="text-center academy-muted py-4">{{ $search !== '' ? 'No Academy members match your search.' : 'No Academy members found.' }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
