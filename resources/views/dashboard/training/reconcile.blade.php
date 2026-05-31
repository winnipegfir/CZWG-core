@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'VATCAN Reconciliation — Training')

@section('content')
@include('includes.trainingMenu')

<div style="background:#f8fafc; min-height:calc(100vh - 112px); padding:2rem 0;">
<div class="container">

    <div class="mb-4">
        <h2 class="font-weight-bold mb-1" style="color:#122b44;">VATCAN Reconciliation</h2>
        <p class="text-muted mb-0" style="font-size:0.875rem;">Compares VATCAN instructor assignments against our linked students list.</p>
    </div>

    @if($error)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle mr-1"></i> Could not reach VATCAN API: {{ $error }}
        </div>
    @else

    {{-- On VATCAN but not linked in our system --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="font-weight-bold mb-1" style="color:#122b44;">
                Linked on VATCAN — Not in Our System
                <span style="background:#fee2e2; color:#b91c1c; font-size:0.75rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem; margin-left:0.4rem;">{{ $onVatcanNotLinked->count() }}</span>
            </h5>
            <p class="text-muted mb-3" style="font-size:0.8rem;">These people have an instructor assigned on VATCAN but are not in our linked students list.</p>

            @if($onVatcanNotLinked->isEmpty())
                <p class="text-muted mb-0" style="font-size:0.875rem;">All clear — no discrepancies.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:0.85rem;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th style="color:#64748b; font-weight:600; border-top:none;">Name</th>
                                <th style="color:#64748b; font-weight:600; border-top:none;">CID</th>
                                <th style="color:#64748b; font-weight:600; border-top:none;">Rating</th>
                                <th style="color:#64748b; font-weight:600; border-top:none;">Type</th>
                                <th style="color:#64748b; font-weight:600; border-top:none;">VATCAN Instructor CID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($onVatcanNotLinked as $member)
                            <tr>
                                <td style="color:#122b44; font-weight:600;">{{ $member['first_name'] }} {{ $member['last_name'] }}</td>
                                <td style="color:#64748b;">{{ $member['cid'] }}</td>
                                <td style="color:#64748b;">{{ $member['rating'] }}</td>
                                <td>
                                    @if($member['flag_is_visitor'])
                                        <span style="background:#dcfce7; color:#15803d; font-size:0.7rem; font-weight:700; padding:0.15em 0.45em; border-radius:0.3rem;">Visitor</span>
                                    @else
                                        <span style="background:#dbeafe; color:#1d4ed8; font-size:0.7rem; font-weight:700; padding:0.15em 0.45em; border-radius:0.3rem;">Home</span>
                                    @endif
                                </td>
                                <td style="color:#64748b;">{{ $member['instructor'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Linked in our system but no VATCAN instructor --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="font-weight-bold mb-1" style="color:#122b44;">
                Linked in Our System — Not on VATCAN
                <span style="background:#fef3c7; color:#92400e; font-size:0.75rem; font-weight:700; padding:0.2em 0.55em; border-radius:0.3rem; margin-left:0.4rem;">{{ $linkedNotOnVatcan->count() }}</span>
            </h5>
            <p class="text-muted mb-3" style="font-size:0.8rem;">These students have an instructor in our system but no instructor assignment on VATCAN.</p>

            @if($linkedNotOnVatcan->isEmpty())
                <p class="text-muted mb-0" style="font-size:0.875rem;">All clear — no discrepancies.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:0.85rem;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th style="color:#64748b; font-weight:600; border-top:none;">Student</th>
                                <th style="color:#64748b; font-weight:600; border-top:none;">Instructor (Our DB)</th>
                                <th style="color:#64748b; font-weight:600; border-top:none;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($linkedNotOnVatcan as $student)
                            <tr>
                                <td style="color:#122b44; font-weight:600;">
                                    {{ $student->user ? $student->user->fullName('FL') : 'CID ' . $student->user_id }}
                                    <span style="font-size:0.78rem; color:#94a3b8; font-weight:400;">{{ $student->user_id }}</span>
                                </td>
                                <td style="color:#64748b;">
                                    {{ $student->instructor && $student->instructor->user ? $student->instructor->user->fullName('FL') : '—' }}
                                </td>
                                <td>
                                    <a href="{{ route('training.students.view', $student->id) }}" style="font-size:0.8rem; color:#122b44;">View &rarr;</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @endif

</div>
</div>
@stop
