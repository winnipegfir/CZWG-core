@extends('layouts.master')

@section('title', 'Manage News - Winnipeg FIR')

@section('content')
<div style="background:#fff; min-height:calc(100vh - 60px); padding:2.5rem 0;">
    <div class="container">

        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <a href="{{ route('dashboard.index') }}" style="font-size:0.82rem; color:#6c757d; text-decoration:none;">
                    <i class="fas fa-arrow-left mr-1"></i> Dashboard
                </a>
                <h1 class="font-weight-bold mb-0 mt-1" style="color:#122b44;">News</h1>
            </div>
            <a href="{{ route('news.articles.create') }}" class="btn btn-sm" style="background:#122b44; color:#fff; border-radius:0.375rem; font-size:0.85rem; padding:0.45rem 1rem;">
                <i class="fas fa-plus mr-1"></i> New Article
            </a>
        </div>
        <hr>

        @if($articles->isEmpty())
            <div style="text-align:center; padding:4rem 0; color:#6c757d;">
                <i class="fas fa-newspaper fa-2x mb-3" style="opacity:0.3; display:block;"></i>
                No articles yet. <a href="{{ route('news.articles.create') }}" style="color:#122b44;">Create one</a>.
            </div>
        @else
            <div style="border:1px solid #e9ecef; border-radius:0.5rem; overflow:hidden;">
                <table class="table table-hover mb-0" style="font-size:0.875rem;">
                    <thead style="background:#f8f9fa;">
                        <tr>
                            <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.75rem 1rem;">Title</th>
                            <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.75rem 1rem; width:140px;">Published</th>
                            <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.75rem 1rem; width:90px;">Status</th>
                            <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.75rem 1rem; width:140px; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $a)
                            <tr style="border-bottom:1px solid #f1f3f5;">
                                <td style="padding:0.75rem 1rem; vertical-align:middle;">
                                    <a href="{{ route('news.articles.view', $a->slug) }}" style="color:#122b44; font-weight:600; text-decoration:none;">
                                        {{ $a->title }}
                                    </a>
                                    @if($a->summary)
                                        <div style="color:#6c757d; font-size:0.78rem; margin-top:0.15rem;">{{ Str::limit($a->summary, 80) }}</div>
                                    @endif
                                </td>
                                <td style="padding:0.75rem 1rem; vertical-align:middle; color:#6c757d;">
                                    {{ $a->published_pretty() }}
                                </td>
                                <td style="padding:0.75rem 1rem; vertical-align:middle;">
                                    @if($a->visible)
                                        <span style="background:#d4edda; color:#155724; font-size:0.72rem; font-weight:600; padding:0.2rem 0.55rem; border-radius:999px;">Published</span>
                                    @else
                                        <span style="background:#e9ecef; color:#6c757d; font-size:0.72rem; font-weight:600; padding:0.2rem 0.55rem; border-radius:999px;">Draft</span>
                                    @endif
                                </td>
                                <td style="padding:0.75rem 1rem; vertical-align:middle; white-space:nowrap; text-align:right;">
                                    <a href="{{ route('news.articles.view', $a->slug) }}"
                                       style="font-size:0.8rem; color:#122b44; text-decoration:none; font-weight:500; margin-right:1rem;">
                                        <i class="fas fa-edit fa-xs mr-1"></i>Edit
                                    </a>
                                    <a href="{{ route('news.articlepublic', $a->slug) }}" target="_blank"
                                       style="font-size:0.8rem; color:#6c757d; text-decoration:none; font-weight:500; margin-right:1rem;">
                                        <i class="fas fa-external-link-alt fa-xs mr-1"></i>View
                                    </a>
                                    <a href="{{ route('news.articles.delete', $a->id) }}"
                                       onclick="return confirm('Delete \'{{ addslashes($a->title) }}\'? This cannot be undone.')"
                                       style="font-size:0.8rem; color:#dc3545; text-decoration:none; font-weight:500;">
                                        <i class="fas fa-trash fa-xs mr-1"></i>Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p style="color:#6c757d; font-size:0.78rem; margin-top:0.75rem;">{{ $articles->count() }} article{{ $articles->count() === 1 ? '' : 's' }}</p>
        @endif

    </div>
</div>
@endsection
