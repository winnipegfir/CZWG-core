@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'New Note — ' . $student->user->fullName('FL'))

@section('content')
@include('includes.trainingMenu')

<div style="background:#f8fafc; padding:2rem 0;">
<div class="container" style="max-width:700px;">

    <a href="{{ route('training.students.view', $student->id) }}" style="font-size:0.85rem; color:#64748b; text-decoration:none;">
        <i class="fas fa-arrow-left fa-xs mr-1"></i> Back to {{ $student->user->fullName('FL') }}
    </a>

    <h2 class="font-weight-bold mt-3 mb-4" style="color:#122b44;">New Training Note</h2>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('add.trainingnote', $student->id) }}">
                @csrf
                <div class="form-group mb-3">
                    <label class="font-weight-bold small">Title</label>
                    <input type="text" name="title" class="form-control" required style="color:#495057;">
                </div>
                <div class="form-group mb-4">
                    <label class="font-weight-bold small">Content</label>
                    <textarea name="content" class="form-control" rows="8" required style="color:#495057;"></textarea>
                </div>
                <div class="d-flex" style="gap:0.5rem;">
                    <button type="submit" class="btn btn-primary btn-sm">Save Note</button>
                    <a href="{{ route('training.students.view', $student->id) }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@stop
