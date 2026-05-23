@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('title', 'Edit Controller - Winnipeg FIR')
@section('description', "Winnipeg FIR's Controller Roster")

@section('content')
<style>
.edit-form select.form-control {
    display: block !important;
    width: 100% !important;
    height: calc(1.5em + .75rem + 2px) !important;
    padding: .375rem .75rem !important;
    font-size: .875rem !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;
    color: #495057 !important;
    background-color: #fff !important;
    background-clip: padding-box !important;
    border: 1px solid #ced4da !important;
    border-radius: .25rem !important;
    box-shadow: none !important;
    -webkit-appearance: auto !important;
    appearance: auto !important;
    border-bottom: 1px solid #ced4da !important;
    margin-bottom: 0 !important;
}
.edit-form select.form-control:focus {
    border-color: #80bdff !important;
    outline: 0 !important;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
}
.edit-form .form-group label {
    margin-bottom: .4rem !important;
    font-size: .875rem !important;
}
</style>
<div class="container" style="margin-top: 28px; max-width: 720px;">
    <a href="{{route('roster.index')}}" class="blue-text" style="font-size: 1.1em;">
        <i class="fas fa-arrow-left"></i> Roster
    </a>

    <h1 class="blue-text font-weight-bold mt-3 mb-1">Edit Controller</h1>
    <p class="text-muted mb-4">
        <strong>{{$roster->full_name}}</strong> &mdash; CID {{$cid}}
    </p>

    <form method="POST" action="{{route('roster.editcontroller', [$cid])}}" class="edit-form">
        @csrf
        <input type="hidden" name="cid" value="{{ $cid }}">

        {{-- Cert levels --}}
        <div class="card mb-4">
            <div class="card-header font-weight-bold">Position Certifications</div>
            <div class="card-body">
                <div class="form-row">
                    @foreach([
                        'del' => 'Delivery',
                        'gnd' => 'Ground',
                        'twr' => 'Tower',
                        'dep' => 'Departure',
                        'app' => 'Approach',
                        'ctr' => 'Centre',
                    ] as $field => $label)
                    <div class="form-group col-6 col-md-4">
                        <label class="font-weight-500 text-dark" for="{{$field}}">{{$label}}</label>
                        <select name="{{$field}}" id="{{$field}}" class="form-control form-control-sm">
                            <option value="1" {{ $roster->$field == "1" ? "selected" : "" }}>Not Certified</option>
                            <option value="2" {{ $roster->$field == "2" ? "selected" : "" }}>Training</option>
                            <option value="3" {{ $roster->$field == "3" ? "selected" : "" }}>Solo</option>
                            <option value="4" {{ $roster->$field == "4" ? "selected" : "" }}>Certified</option>
                        </select>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Remarks --}}
        <div class="card mb-4">
            <div class="card-header font-weight-bold">Remarks</div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <textarea name="remarks" id="remarks" rows="3" class="form-control" placeholder="Optional notes...">{{ $roster->remarks }}</textarea>
                </div>
            </div>
        </div>

        {{-- Status & admin options --}}
        <div class="card mb-4">
            <div class="card-header font-weight-bold">Settings</div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-6 col-md-4">
                        <label class="font-weight-500 text-dark" for="active">Active Status</label>
                        <select name="active" id="active" class="form-control form-control-sm">
                            <option value="1" {{ $roster->active == "1" ? "selected" : "" }}>Active</option>
                            <option value="0" {{ $roster->active == "0" ? "selected" : "" }}>Not Active</option>
                        </select>
                    </div>
                    <div class="form-group col-6 col-md-4">
                        <label class="font-weight-500 text-dark" for="rating_hours">Reset Rating Hours?</label>
                        <select name="rating_hours" id="rating_hours" class="form-control form-control-sm">
                            <option value="false">No</option>
                            <option value="true">Yes</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <button type="submit" class="btn btn-success px-4">Save Changes</button>
            <a href="{{route('roster.index')}}" class="btn btn-light ml-2">Cancel</a>
        </div>
    </form>
</div>
@stop
