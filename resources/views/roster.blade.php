@extends('layouts.master')

@section('title', 'Roster - Winnipeg FIR')
@section('description', "Winnipeg FIR's Controller Roster")

@php
function certBadge($level) {
    switch((string)$level) {
        case '2': return '<span class="cert-badge cert-training"><i class="fas fa-book"></i></span>';
        case '3': return '<span class="cert-badge cert-solo"><i class="fas fa-user"></i></span>';
        case '4': return '<span class="cert-badge cert-certified"><i class="fas fa-check"></i></span>';
        default:  return '<span class="cert-badge cert-none"><i class="fas fa-times"></i></span>';
    }
}
@endphp

@section('content')
<div class="roster-page-wrap">

    {{-- Page header --}}
    <div class="roster-page-header">
        <div>
            <h1 class="roster-page-title">Controller Roster</h1>
        </div>
        @if(Auth::check() && Auth::user()->permissions >= 4)
        <a href="{{route('roster.index')}}" class="btn btn-sm btn-outline-danger ml-auto">
            <i class="fas fa-edit mr-1"></i> Edit Roster
        </a>
        @endif
    </div>

    {{-- Tab pills --}}
    <div class="roster-controls">
        <div class="roster-pills" role="tablist">
            <button class="roster-pill active" data-panel="home" role="tab" aria-selected="true">
                Home <span class="pill-count">{{ $roster->count() }}</span>
            </button>
            <button class="roster-pill" data-panel="visit" role="tab" aria-selected="false">
                Visiting <span class="pill-count">{{ $visitroster->count() }}</span>
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
                        <th data-col="0" class="sortable">CID <i class="fas fa-sort sort-icon"></i></th>
                        <th data-col="1" class="sortable">Controller Name <i class="fas fa-sort sort-icon"></i></th>
                        <th data-col="2" class="sortable">Rating <i class="fas fa-sort sort-icon"></i></th>
                        <th>DEL</th>
                        <th>GND</th>
                        <th>TWR</th>
                        <th>DEP</th>
                        <th>APP</th>
                        <th>CTR</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($roster as $controller)
                    <tr>
                        <td><a href="{{url('/roster/'.$controller->cid)}}" class="roster-cid">{{$controller->cid}}</a></td>
                        <td class="roster-name">{{$controller->user->fullName('FL')}}</td>
                        <td><span class="rating-badge">{{$controller->user->rating->getShortName()}}</span></td>
                        <td>{!! certBadge($controller->del) !!}</td>
                        <td>{!! certBadge($controller->gnd) !!}</td>
                        <td>{!! certBadge($controller->twr) !!}</td>
                        <td>{!! certBadge($controller->dep) !!}</td>
                        <td>{!! certBadge($controller->app) !!}</td>
                        <td>{!! certBadge($controller->ctr) !!}</td>
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
            <table class="table roster-table sortable-table" id="visitRosterTable">
                <thead>
                    <tr>
                        <th data-col="0" class="sortable">CID <i class="fas fa-sort sort-icon"></i></th>
                        <th data-col="1" class="sortable">Controller Name <i class="fas fa-sort sort-icon"></i></th>
                        <th data-col="2" class="sortable">Rating <i class="fas fa-sort sort-icon"></i></th>
                        <th>DEL</th>
                        <th>GND</th>
                        <th>TWR</th>
                        <th>DEP</th>
                        <th>APP</th>
                        <th>CTR</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($visitroster as $vc)
                    <tr>
                        <td><a href="{{url('/roster/'.$vc->cid)}}" class="roster-cid">{{$vc->cid}}</a></td>
                        <td class="roster-name">{{$vc->user->fullName('FL')}}</td>
                        <td><span class="rating-badge">{{$vc->user->rating_short}}</span></td>
                        <td>{!! certBadge($vc->del) !!}</td>
                        <td>{!! certBadge($vc->gnd) !!}</td>
                        <td>{!! certBadge($vc->twr) !!}</td>
                        <td>{!! certBadge($vc->dep) !!}</td>
                        <td>{!! certBadge($vc->app) !!}</td>
                        <td>{!! certBadge($vc->ctr) !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <p class="roster-no-results" style="display:none;">No controllers match your search.</p>
        </div>
    </div>

</div>

<script>
$(document).ready(function () {

    // ── Tab switching ──────────────────────────────────────────
    $('.roster-pill').on('click', function () {
        var panel = $(this).data('panel');
        $('.roster-pill').removeClass('active').attr('aria-selected', 'false');
        $(this).addClass('active').attr('aria-selected', 'true');
        $('.roster-panel').hide();
        $('#panel-' + panel).show();
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
            // Numeric sort for CID column
            if (col === 0) {
                return asc ? parseInt(aVal) - parseInt(bVal) : parseInt(bVal) - parseInt(aVal);
            }
            return asc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
        });
        tbody.append(rows);
    });

    // Default sort home table by CID asc
    $('#rosterTable .sortable[data-col="0"]').trigger('click');
    $('#visitRosterTable .sortable[data-col="0"]').trigger('click');
});
</script>
@stop
