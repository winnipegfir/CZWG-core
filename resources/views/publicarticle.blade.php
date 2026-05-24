@extends('layouts.master')

@section('title', $article->title.' - Winnipeg FIR')
@section('description', $article->summary)
@if($article->image)
    @section('image', $article->image)
@endif

@section('content')
<style>
    .article-prose h1, .article-prose h2, .article-prose h3,
    .article-prose h4, .article-prose h5, .article-prose h6 {
        color: #122b44;
        font-weight: 700;
        margin-top: 1.75rem;
        margin-bottom: 0.6rem;
    }
    .article-prose p {
        color: #343a40;
        line-height: 1.8;
        margin-bottom: 1.1rem;
    }
    .article-prose ul, .article-prose ol {
        color: #343a40;
        line-height: 1.8;
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }
    .article-prose li {
        margin-bottom: 0.3rem;
    }
    .article-prose a {
        color: #122b44;
        text-decoration: underline;
    }
    .article-prose blockquote {
        border-left: 4px solid #122b44;
        padding: 0.5rem 1rem;
        margin: 1.25rem 0;
        color: #6c757d;
        background: #f8f9fa;
        border-radius: 0 0.25rem 0.25rem 0;
    }
    .article-prose hr {
        border-color: #e9ecef;
        margin: 2rem 0;
    }
    .article-prose code {
        background: #f1f3f5;
        padding: 0.1rem 0.35rem;
        border-radius: 0.2rem;
        font-size: 0.875rem;
        color: #122b44;
    }
    .article-prose pre {
        background: #f1f3f5;
        padding: 1rem;
        border-radius: 0.375rem;
        overflow-x: auto;
    }
</style>

{{-- Hero header --}}
<div style="background:#122b44; padding: 2.75rem 0 2.25rem;">
    <div class="container">
        <a href="{{ route('news') }}" style="font-size:0.82rem; color:rgba(255,255,255,0.6); text-decoration:none; display:inline-flex; align-items:center; gap:0.35rem; margin-bottom:0.25rem;">
            <i class="fas fa-arrow-left fa-xs"></i> Back to News
        </a>
        <h1 style="color:#fff; font-weight:700; font-size:2rem; line-height:1.25; margin-bottom:0.5rem; max-width:760px;">
            {{ $article->title }}
        </h1>
        <div style="display:flex; gap:1.25rem; flex-wrap:wrap; align-items:center; color:rgba(255,255,255,0.65); font-size:0.85rem;">
            <span>
                <i class="far fa-clock mr-1"></i>
                <span @if($article->edited) title="Last edited {{ $article->edited_pretty() }}" @endif>
                    {{ $article->published_pretty() }}
                </span>
            </span>
            <span style="opacity:0.4;">•</span>
            <span>
                <i class="far fa-user-circle mr-1"></i>
                {{ $article->author_pretty() }}
            </span>
            @if($article->edited)
                <span style="opacity:0.4;">•</span>
                <span title="{{ $article->edited_pretty() }}">
                    <i class="fas fa-pencil-alt mr-1" style="font-size:0.75rem;"></i>Edited
                </span>
            @endif
        </div>
    </div>
</div>

{{-- Article body --}}
<div style="background:#fff; min-height:calc(100vh - 220px); padding: 2.5rem 0;">
    <div class="container">

        @if(!$article->visible)
            <div class="alert" style="background:#fff3cd; border:1px solid #ffc107; border-radius:0.375rem; color:#856404; font-size:0.875rem; margin-bottom:1.5rem;">
                <i class="fas fa-eye-slash mr-2"></i>This article is not visible to the public.
            </div>
        @endif

        @if($article->image)
            <img src="{{ $article->image }}" alt="{{ $article->title }}"
                 style="width:100%; border-radius:0.5rem; margin-bottom:2rem; max-height:420px; object-fit:cover; display:block;">
        @endif

        <div class="article-prose">
            {{ $article->html() }}
        </div>

    </div>
</div>
@stop
