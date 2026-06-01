@extends('layouts.master')

@section('navbarprim')
    @parent
@stop

@section('title', 'Edit Controller — Winnipeg FIR')
@section('description', "Winnipeg FIR's Controller Roster")

@section('content')
<div class="dash-roster-edit-wrap">

    <a href="{{ route('roster.index') }}" class="dash-back-link">
        <i class="fas fa-arrow-left"></i> Roster
    </a>

    <div class="dash-roster-edit-header">
        <div>
            <h1 class="roster-page-title mt-3 mb-1">Edit Controller</h1>
            <p class="roster-page-sub">
                <strong>{{ $roster->full_name }}</strong> &mdash; CID {{ $cid }}
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('roster.editcontroller', [$cid]) }}" class="dash-roster-edit-form">
        @csrf
        <input type="hidden" name="cid" value="{{ $cid }}">

        {{-- Position Certifications --}}
        <div class="dash-edit-card">
            <div class="dash-edit-card-header">Position Certifications</div>
            <div class="dash-edit-card-body">
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
                        <label class="dash-edit-label" for="{{ $field }}">{{ $label }}</label>
                        <select name="{{ $field }}" id="{{ $field }}" class="dash-edit-select">
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
        <div class="dash-edit-card">
            <div class="dash-edit-card-header">Remarks</div>
            <div class="dash-edit-card-body">
                <div class="form-group mb-0">
                    <textarea name="remarks" id="remarks" rows="3"
                              class="dash-edit-textarea"
                              placeholder="Optional notes…">{{ $roster->remarks }}</textarea>
                </div>
            </div>
        </div>

        {{-- Settings --}}
        <div class="dash-edit-card">
            <div class="dash-edit-card-header">Settings</div>
            <div class="dash-edit-card-body">
                <div class="form-row">
                    <div class="form-group col-6 col-md-4">
                        <label class="dash-edit-label" for="active">Active Status</label>
                        <select name="active" id="active" class="dash-edit-select">
                            <option value="1" {{ $roster->active == "1" ? "selected" : "" }}>Active</option>
                            <option value="0" {{ $roster->active == "0" ? "selected" : "" }}>Not Active</option>
                        </select>
                    </div>
                    <div class="form-group col-6 col-md-4">
                        <label class="dash-edit-label" for="rating_hours">Reset Rating Hours?</label>
                        <select name="rating_hours" id="rating_hours" class="dash-edit-select">
                            <option value="false">No</option>
                            <option value="true">Yes</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="dash-edit-actions">
            <button type="submit" class="btn btn-success px-4">Save Changes</button>
            <a href="{{ route('roster.index') }}" class="btn btn-light ml-2">Cancel</a>
        </div>
    </form>

</div>
@stop
