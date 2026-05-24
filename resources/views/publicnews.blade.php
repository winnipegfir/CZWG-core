@extends('layouts.master')

@section('title', 'News - Winnipeg FIR')
@section('description', 'News from Winnipeg FIR')

@section('content')
<style>
    .news-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: box-shadow 0.18s ease, transform 0.18s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        text-decoration: none;
        color: inherit;
    }
    .news-card:hover {
        box-shadow: 0 4px 20px rgba(18,43,68,0.12);
        transform: translateY(-2px);
        text-decoration: none;
        color: inherit;
    }
    .news-card-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }
    .news-card-img-placeholder {
        width: 100%;
        height: 180px;
        background: linear-gradient(135deg, #122b44 0%, #1a3d5c 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .news-card-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .news-card-title {
        font-weight: 700;
        font-size: 1.05rem;
        color: #122b44;
        line-height: 1.35;
        margin-bottom: 0.5rem;
    }
    .news-card-meta {
        font-size: 0.78rem;
        color: #6c757d;
        margin-bottom: 0.75rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .news-card-summary {
        font-size: 0.875rem;
        color: #495057;
        line-height: 1.6;
        flex: 1;
        margin-bottom: 1rem;
    }
    .news-card-readmore {
        font-size: 0.82rem;
        font-weight: 600;
        color: #122b44;
        text-decoration: none;
        margin-top: auto;
    }
    .news-card-readmore:hover {
        text-decoration: underline;
        color: #122b44;
    }
</style>

<div style="background:#fff; min-height: calc(100vh - 60px); padding: 2.5rem 0;">
    <div class="container">

        <div class="mb-2">
            <h1 class="font-weight-bold" style="color:#122b44;">News</h1>
            <p style="color:#6c757d; margin-bottom:0;">Latest updates and announcements from Winnipeg FIR.</p>
        </div>
        <hr>

        @if($news->isEmpty())
            <div style="text-align:center; padding: 4rem 0; color:#6c757d;">
                <i class="fas fa-newspaper fa-2x mb-3" style="opacity:0.3;"></i>
                <p>No news articles yet. Check back soon.</p>
            </div>
        @else
            <div class="row">
                @foreach($news as $n)
                    <div class="col-md-4 mb-4">
                        <a href="{{ route('news.articlepublic', $n->slug) }}" class="news-card d-flex flex-column">
                            @if($n->image)
                                <img src="{{ $n->image }}" alt="{{ $n->title }}" class="news-card-img">
                            @else
                                <div class="news-card-img-placeholder">
                                    <i class="fas fa-newspaper fa-2x" style="color:rgba(255,255,255,0.25);"></i>
                                </div>
                            @endif
                            <div class="news-card-body">
                                <div class="news-card-title">{{ $n->title }}</div>
                                <div class="news-card-meta">
                                    <span><i class="far fa-clock mr-1"></i>{{ $n->published_pretty() }}</span>
                                    <span><i class="far fa-user-circle mr-1"></i>{{ $n->author_pretty() }}</span>
                                </div>
                                @if($n->summary)
                                    <p class="news-card-summary">{{ Str::limit($n->summary, 120) }}</p>
                                @endif
                                <span class="news-card-readmore">Read article &rarr;</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@stop
