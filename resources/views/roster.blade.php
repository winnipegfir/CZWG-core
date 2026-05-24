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
<div class="container" style="margin-top: 28px;">
    <h1 class="blue-text font-weight-bold">Controller Roster</h1>
    <hr>

    <ul class="nav nav-tabs" id="rosterTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab">Home Controllers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="visit-tab" data-toggle="tab" href="#visit" role="tab">Visiting Controllers</a>
        </li>
        @if(Auth::check() && Auth::user()->permissions >= 4)
        <li class="nav-item">
            <a class="nav-link" href="{{route('roster.index')}}" style="color: #c0392b;">Edit Roster</a>
        </li>
        @endif
    </ul>

    <div class="tab-content" id="rosterTabContent">

        {{-- Home Controllers --}}
        <div class="tab-pane fade show active" id="home" role="tabpanel">
            <div class="roster-legend mt-3 mb-2">
                <span class="cert-badge cert-certified"><i class="fas fa-check"></i></span> Certified &nbsp;
                <span class="cert-badge cert-solo"><i class="fas fa-user"></i></span> Solo &nbsp;
                <span class="cert-badge cert-training"><i class="fas fa-book"></i></span> Training &nbsp;
                <span class="cert-badge cert-none"><i class="fas fa-times"></i></span> Not Certified
            </div>
            <table id="rosterTable" class="table roster-table">
                <thead>
                    <tr>
                        <th>CID</th>
                        <th>Controller Name</th>
                        <th>Rating</th>
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
                        <td class="font-weight-500">{{$controller->user->fullName('FL')}}</td>
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
        </div>

        {{-- Visiting Controllers --}}
        <div class="tab-pane fade" id="visit" role="tabpanel">
            <div class="roster-legend mt-3 mb-2">
                <span class="cert-badge cert-certified"><i class="fas fa-check"></i></span> Certified &nbsp;
                <span class="cert-badge cert-solo"><i class="fas fa-user"></i></span> Solo &nbsp;
                <span class="cert-badge cert-training"><i class="fas fa-book"></i></span> Training &nbsp;
                <span class="cert-badge cert-none"><i class="fas fa-times"></i></span> Not Certified
            </div>
            <table id="visitRosterTable" class="table roster-table">
                <thead>
                    <tr>
                        <th>CID</th>
                        <th>Controller Name</th>
                        <th>Rating</th>
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
                        <td class="font-weight-500">{{$vc->user->fullName('FL')}}</td>
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
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {
        $.fn.dataTable.enum(['C1', 'C3', 'I1', 'I3', 'SUP', 'ADM']);
        $('#rosterTable').DataTable({ order: [[0, 'asc']], autoWidth: false });
        $('#visitRosterTable').DataTable({ order: [[0, 'asc']], autoWidth: false });
    });
</script>
<script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/enum.js"></script>
@stop
