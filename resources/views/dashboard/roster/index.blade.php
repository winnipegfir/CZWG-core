@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('title', 'ATC Roster — Winnipeg FIR')
@section('description', "Winnipeg FIR's Controller Roster")

@php
function adminCertBadge($level) {
    switch((string)$level) {
        case '2': return '<span class="cert-badge cert-training"><i class="fas fa-book"></i></span>';
        case '3': return '<span class="cert-badge cert-solo"><i class="fas fa-user"></i></span>';
        case '4': return '<span class="cert-badge cert-certified"><i class="fas fa-check"></i></span>';
        default:  return '<span class="cert-badge cert-none"><i class="fas fa-times"></i></span>';
    }
}
@endphp

@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<div class="container roster-page-wrap">

    {{-- Back link --}}
    <a href="{{ route('dashboard.index') }}" class="dash-back-link">
        <i class="fas fa-arrow-left"></i> Dashboard
    </a>

    {{-- Page header --}}
    <div class="roster-page-header mt-3">
        <div>
            <h1 class="roster-page-title">Controller Roster</h1>
        </div>
        <button id="addControllerBtn"
                class="btn btn-sm btn-success ml-auto"
                data-toggle="modal"
                data-target="#addController">
            <i class="fas fa-plus mr-1"></i>
            <span id="addControllerBtnLabel">Add to Home Roster</span>
        </button>
    </div>

    {{-- Tab pills --}}
    <div class="roster-controls">
        <div class="roster-pills" role="tablist">
            <button class="roster-pill active" data-panel="home" role="tab" aria-selected="true">
                Home <span class="pill-count">{{ $roster->count() }}</span>
            </button>
            <button class="roster-pill" data-panel="visit" role="tab" aria-selected="false">
                Visiting <span class="pill-count">{{ $visitroster2->count() }}</span>
            </button>
        </div>
    </div>

    {{-- Legend + Search --}}
    <div class="roster-legend-row">
        <div class="roster-legend">
            <span class="cert-badge cert-certified"><i class="fas fa-check"></i></span> Certified
            <span class="cert-badge cert-solo"><i class="fas fa-user"></i></span> Solo
            <span class="cert-badge cert-training"><i class="fas fa-book"></i></span> Training
            <span class="cert-badge cert-none"><i class="fas fa-times"></i></span> Not Certified
        </div>
        <div class="roster-search-wrap">
            <i class="fas fa-search roster-search-icon"></i>
            <input type="text" id="rosterSearch" class="roster-search-input" placeholder="Search name or CID&hellip;" autocomplete="off">
        </div>
    </div>

    {{-- Home Controllers --}}
    <div id="panel-home" class="roster-panel">
        <div class="roster-table-wrap">
            <table class="table roster-table sortable-table" id="rosterTable">
                <thead>
                    <tr>
                        <th class="sortable" data-col="0">CID <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-col="1">Controller Name <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-col="2">Rating <i class="fas fa-sort sort-icon"></i></th>
                        <th>DEL</th>
                        <th>GND</th>
                        <th>TWR</th>
                        <th>DEP</th>
                        <th>APP</th>
                        <th>CTR</th>
                        <th class="sortable" data-col="9">Remarks <i class="fas fa-sort sort-icon"></i></th>
                        <th>Status</th>
                        <th class="text-danger" style="white-space:nowrap;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($roster as $controller)
                    <tr>
                        <td><strong class="roster-cid-plain">{{$controller->cid}}</strong></td>
                        <td class="roster-name">{{$controller->user->fullName('FL')}}</td>
                        <td><span class="rating-badge">{{$controller->user->rating->getShortName()}}</span></td>
                        <td>{!! adminCertBadge($controller->del) !!}</td>
                        <td>{!! adminCertBadge($controller->gnd) !!}</td>
                        <td>{!! adminCertBadge($controller->twr) !!}</td>
                        <td>{!! adminCertBadge($controller->dep) !!}</td>
                        <td>{!! adminCertBadge($controller->app) !!}</td>
                        <td>{!! adminCertBadge($controller->ctr) !!}</td>
                        <td class="roster-remarks">{{$controller->remarks}}</td>
                        <td>
                            @if ($controller->active == "1")
                                <span class="status-badge status-active">Active</span>
                            @else
                                <span class="status-badge status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{route('roster.editcontrollerform', [$controller->cid])}}" class="btn btn-sm dash-roster-edit-btn">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </a>
                            <button class="btn btn-sm dash-roster-delete-btn"
                                    data-toggle="modal"
                                    data-target="#deleteController{{$controller->id}}">
                                <i class="fas fa-trash-alt"></i>
                            </button>

                            <div class="modal fade" id="deleteController{{$controller->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title font-weight-bold text-danger">Remove from Roster</h5>
                                        </div>
                                        <div class="modal-body pt-2">
                                            <p class="mb-1"><strong>{{$controller->user->fullName('FL')}}</strong></p>
                                            <p class="text-muted mb-3" style="font-size:0.85rem;">CID {{$controller->cid}}</p>
                                            <p class="mb-0" style="font-size:0.85rem;">This will also delete their session logs. This cannot be undone.</p>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <form method="GET" action="{{ route('roster.deletecontroller', [$controller->id]) }}">
                                                {{ csrf_field() }}
                                                <button class="btn btn-sm btn-danger" type="submit">Remove</button>
                                            </form>
                                            <button class="btn btn-sm btn-light" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <p class="roster-no-results" style="display:none;">No controllers match your search.</p>
        </div>
    </div>

    {{-- Visiting Controllers --}}
    <div id="panel-visit" class="roster-panel" style="display:none;">
        <div class="roster-table-wrap">
            <table class="table roster-table sortable-table" id="rosterVisitTable">
                <thead>
                    <tr>
                        <th class="sortable" data-col="0">CID <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-col="1">Controller Name <i class="fas fa-sort sort-icon"></i></th>
                        <th class="sortable" data-col="2">Rating <i class="fas fa-sort sort-icon"></i></th>
                        <th>DEL</th>
                        <th>GND</th>
                        <th>TWR</th>
                        <th>DEP</th>
                        <th>APP</th>
                        <th>CTR</th>
                        <th class="sortable" data-col="9">Remarks <i class="fas fa-sort sort-icon"></i></th>
                        <th>Status</th>
                        <th class="text-danger" style="white-space:nowrap;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($visitroster2 as $visitcontroller)
                    <tr>
                        <td><strong class="roster-cid-plain">{{$visitcontroller->cid}}</strong></td>
                        <td class="roster-name">{{$visitcontroller->user->fullName('FL')}}</td>
                        <td><span class="rating-badge">{{$visitcontroller->user->rating->getShortName()}}</span></td>
                        <td>{!! adminCertBadge($visitcontroller->del) !!}</td>
                        <td>{!! adminCertBadge($visitcontroller->gnd) !!}</td>
                        <td>{!! adminCertBadge($visitcontroller->twr) !!}</td>
                        <td>{!! adminCertBadge($visitcontroller->dep) !!}</td>
                        <td>{!! adminCertBadge($visitcontroller->app) !!}</td>
                        <td>{!! adminCertBadge($visitcontroller->ctr) !!}</td>
                        <td class="roster-remarks">{{$visitcontroller->remarks}}</td>
                        <td>
                            @if ($visitcontroller->active == "1")
                                <span class="status-badge status-active">Active</span>
                            @else
                                <span class="status-badge status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{route('roster.editcontrollerform', [$visitcontroller->cid])}}" class="btn btn-sm dash-roster-edit-btn">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </a>
                            <button class="btn btn-sm dash-roster-delete-btn"
                                    data-toggle="modal"
                                    data-target="#deleteVisitController{{$visitcontroller->id}}">
                                <i class="fas fa-trash-alt"></i>
                            </button>

                            <div class="modal fade" id="deleteVisitController{{$visitcontroller->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title font-weight-bold text-danger">Remove from Roster</h5>
                                        </div>
                                        <div class="modal-body pt-2">
                                            <p class="mb-1"><strong>{{$visitcontroller->user->fullName('FL')}}</strong></p>
                                            <p class="text-muted mb-3" style="font-size:0.85rem;">CID {{$visitcontroller->cid}}</p>
                                            <p class="mb-0" style="font-size:0.85rem;">This will also delete their session logs. This cannot be undone.</p>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <form method="GET" action="{{ route('roster.deletecontroller', [$visitcontroller->id]) }}">
                                                {{ csrf_field() }}
                                                <button class="btn btn-sm btn-danger" type="submit">Remove</button>
                                            </form>
                                            <button class="btn btn-sm btn-light" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <p class="roster-no-results" style="display:none;">No controllers match your search.</p>
        </div>
    </div>

</div>

{{-- Add Home Controller Modal --}}
<div class="modal fade" id="addController" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Add to Home Roster</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('roster.addcontroller') }}">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-500 mb-1" style="font-size:0.875rem;">Select Controller</label>
                        <select class="js-example-basic-single form-control" style="width:100%" name="newcontroller">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{$user->id}} — {{$user->fname}} {{$user->lname}}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('dropdown'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('dropdown') }}</strong>
                        </span>
                    @endif
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">Add to Roster</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Add Visiting Controller Modal --}}
<div class="modal fade" id="addVisitController" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Add to Visiting Roster</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('roster.addvisitcontroller') }}">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-500 mb-1" style="font-size:0.875rem;">Select Controller</label>
                        <select class="js-example-basic-single form-control" style="width:100%" name="newcontroller">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{$user->id}} — {{$user->fname}} {{$user->lname}}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('dropdown'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('dropdown') }}</strong>
                        </span>
                    @endif
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">Add to Roster</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('.js-example-basic-single').select2();

    // ── Tab switching ──────────────────────────────────────────
    var addLabels = { home: 'Add to Home Roster', visit: 'Add to Visiting Roster' };
    var addTargets = { home: '#addController', visit: '#addVisitController' };

    $('.roster-pill').on('click', function () {
        var panel = $(this).data('panel');
        $('.roster-pill').removeClass('active').attr('aria-selected', 'false');
        $(this).addClass('active').attr('aria-selected', 'true');
        $('.roster-panel').hide();
        $('#panel-' + panel).show();
        $('#addControllerBtnLabel').text(addLabels[panel]);
        $('#addControllerBtn').attr('data-target', addTargets[panel]);
        applySearch($('#rosterSearch').val());
    });

    // ── Live search ────────────────────────────────────────────
    $('#rosterSearch').on('input', function () {
        applySearch($(this).val());
    });

    function applySearch(q) {
        q = q.trim().toLowerCase();
        var panel = $('.roster-panel:visible');
        var rows = panel.find('tbody tr');
        var visible = 0;
        rows.each(function () {
            var text = $(this).text().toLowerCase();
            var show = !q || text.indexOf(q) > -1;
            $(this).toggle(show);
            if (show) visible++;
        });
        panel.find('.roster-no-results').toggle(visible === 0);
    }

    // ── Column sort ────────────────────────────────────────────
    $(document).on('click', '.sortable-table .sortable', function () {
        var th = $(this);
        var table = th.closest('table');
        var col = parseInt(th.data('col'));
        var asc = th.data('dir') !== 'asc';

        table.find('.sortable').each(function () {
            $(this).data('dir', '');
            $(this).find('.sort-icon').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
        });

        th.data('dir', asc ? 'asc' : 'desc');
        th.find('.sort-icon').removeClass('fa-sort').addClass(asc ? 'fa-sort-up' : 'fa-sort-down');

        var tbody = table.find('tbody');
        var rows = tbody.find('tr').toArray();
        rows.sort(function (a, b) {
            var aVal = $(a).find('td').eq(col).text().trim();
            var bVal = $(b).find('td').eq(col).text().trim();
            if (col === 0) {
                return asc ? parseInt(aVal) - parseInt(bVal) : parseInt(bVal) - parseInt(aVal);
            }
            return asc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
        });
        tbody.append(rows);
    });

    // Default sort by CID asc
    $('#rosterTable .sortable[data-col="0"]').trigger('click');
    $('#rosterVisitTable .sortable[data-col="0"]').trigger('click');
});
</script>

@stop
