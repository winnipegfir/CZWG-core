@extends('layouts.master')

@section('title', 'Edit Article - Winnipeg FIR')

@section('content')
<div style="background:#fff; min-height:calc(100vh - 60px); padding:2.5rem 0;">
    <div class="container">

        <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
                <a href="{{ route('news.index') }}" style="font-size:0.82rem; color:#6c757d; text-decoration:none;">
                    <i class="fas fa-arrow-left mr-1"></i> News
                </a>
                <h1 class="font-weight-bold mb-0 mt-1" style="color:#122b44; font-size:1.6rem;">{{ $article->title }}</h1>
                <p style="color:#6c757d; font-size:0.82rem; margin-top:0.25rem; margin-bottom:0;">
                    {{ $article->published_pretty() }}
                    @if($article->edited) &nbsp;·&nbsp; Edited {{ $article->edited_pretty() }} @endif
                    &nbsp;·&nbsp;
                    @if($article->visible)
                        <span style="color:#155724; font-weight:600;">Published</span>
                    @else
                        <span style="color:#6c757d; font-weight:600;">Draft</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('news.articlepublic', $article->slug) }}" target="_blank"
               style="font-size:0.82rem; color:#6c757d; text-decoration:none; white-space:nowrap; margin-top:0.25rem;">
                <i class="fas fa-external-link-alt fa-xs mr-1"></i> View live
            </a>
        </div>
        <hr>

        @if($errors->createArticleErrors->any())
            <div class="alert" style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:0.375rem; color:#721c24; font-size:0.875rem; margin-bottom:1.5rem;">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1 pl-3">
                    @foreach($errors->createArticleErrors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('news.articles.edit', $article->id) }}" enctype="multipart/form-data">
            @csrf

            {{-- Section: Primary Info --}}
                    <div style="margin-bottom:2rem;">
                        <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Primary Information</h6>

                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Title <span style="color:#dc3545;">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $article->title) }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Summary</label>
                            <input type="text" name="summary" value="{{ old('summary', $article->summary) }}" class="form-control" placeholder="Short description shown on the news index">
                        </div>

                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Author</label>
                            <select class="custom-select" name="author">
                                @foreach($staff as $s)
                                    <option value="{{ $s->user->id }}" {{ $article->user_id == $s->user->id ? 'selected' : '' }}>
                                        {{ $s->user->fullName('FLC') }} ({{ $s->position }})
                                    </option>
                                @endforeach
                                @if(!$staff->contains(fn($s) => $s->user->id === $article->user_id))
                                    <option value="{{ $article->user_id }}" selected>{{ $article->user->fullName('FLC') }}</option>
                                @endif
                            </select>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="showAuthor" class="form-check-input" id="showAuthor" {{ $article->show_author ? 'checked' : '' }}>
                            <label class="form-check-label" for="showAuthor" style="font-size:0.875rem;">Show author name publicly</label>
                        </div>
                    </div>

                    <hr style="border-color:#e9ecef;">

                    {{-- Section: Content --}}
                    <div style="margin-bottom:2rem; margin-top:2rem;">
                        <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Content <span style="color:#dc3545;">*</span></h6>
                        <textarea id="contentMD" name="content" class="form-control">{{ old('content', $article->content) }}</textarea>
                        <script>
                            var simplemde = new SimpleMDE({ element: document.getElementById("contentMD") });
                        </script>
                    </div>

                    <hr style="border-color:#e9ecef;">

                    {{-- Section: Image --}}
                    <div style="margin-bottom:2rem; margin-top:2rem;">
                        <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.75rem;">Hero Image</h6>

                        @if($article->image)
                            <div style="margin-bottom:1rem;">
                                <img src="{{ $article->image }}" alt="" style="max-width:240px; border-radius:0.375rem; border:1px solid #e9ecef; display:block;">
                                <p style="font-size:0.78rem; color:#6c757d; margin-top:0.4rem;">Current image. Upload a new one to replace it.</p>
                            </div>
                        @endif

                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="image" id="imageUpload" accept=".jpg,.jpeg,.png,.gif">
                                <label class="custom-file-label" for="imageUpload">{{ $article->image ? 'Replace image' : 'Choose image' }}</label>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color:#e9ecef;">

                    {{-- Section: Options --}}
                    <div style="margin-bottom:2rem; margin-top:2rem;">
                        <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Options</h6>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="articleVisible" class="form-check-input" id="articleVisible" {{ $article->visible ? 'checked' : '' }}>
                            <label class="form-check-label" for="articleVisible" style="font-size:0.875rem; font-weight:600;">Publicly visible</label>
                            <div style="font-size:0.78rem; color:#6c757d; margin-top:0.1rem;">Uncheck to hide from public view.</div>
                        </div>
                    </div>

                    <hr style="border-color:#e9ecef; margin-top:1.5rem;">

                    <div class="d-flex align-items-center mt-3">
                        <button type="submit" class="btn" style="background:#122b44; color:#fff; border-radius:0.375rem; font-size:0.875rem; padding:0.5rem 1.5rem;">
                            Save Changes
                        </button>
                        <a href="{{ route('news.index') }}" style="font-size:0.875rem; color:#6c757d; text-decoration:none; margin-left:1rem;">Cancel</a>
                        <a href="{{ route('news.articles.delete', $article->id) }}"
                           onclick="return confirm('Delete this article? This cannot be undone.')"
                           style="font-size:0.82rem; color:#dc3545; text-decoration:none; margin-left:auto;">
                            <i class="fas fa-trash fa-xs mr-1"></i> Delete article
                        </a>
                    </div>

        </form>
    </div>
</div>

<script>
    document.getElementById('imageUpload').addEventListener('change', function() {
        var label = this.nextElementSibling;
        label.textContent = this.files[0] ? this.files[0].name : 'Choose image';
    });
</script>
@endsection
