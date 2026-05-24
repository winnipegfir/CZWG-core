@extends('layouts.master')

@section('title', 'Create Article - Winnipeg FIR')

@section('content')
<div style="background:#fff; min-height:calc(100vh - 60px); padding:2.5rem 0;">
    <div class="container">

        <div class="mb-2">
            <a href="{{ route('news.index') }}" style="font-size:0.82rem; color:#6c757d; text-decoration:none;">
                <i class="fas fa-arrow-left mr-1"></i> News
            </a>
            <h1 class="font-weight-bold mb-0 mt-1" style="color:#122b44;">Create Article</h1>
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

        <form method="POST" action="{{ route('news.articles.create.post') }}" enctype="multipart/form-data">
            @csrf

            {{-- Section: Primary Info --}}
                    <div style="margin-bottom:2rem;">
                        <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Primary Information</h6>

                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Title <span style="color:#dc3545;">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="e.g. New sector files released">
                        </div>

                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Summary</label>
                            <input type="text" name="summary" value="{{ old('summary') }}" class="form-control" placeholder="Short description shown on the news index">
                            <small class="form-text text-muted">Leave blank to auto-generate from the first line of content.</small>
                        </div>

                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Author <span style="color:#dc3545;">*</span></label>
                            <select class="custom-select" name="author">
                                <option value="{{ Auth::id() }}" selected>You</option>
                                @foreach($staff as $s)
                                    <option value="{{ $s->user->id }}">{{ $s->user->fullName('FLC') }} ({{ $s->position }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="showAuthor" class="form-check-input" id="showAuthor">
                            <label class="form-check-label" for="showAuthor" style="font-size:0.875rem;">Show author name publicly</label>
                        </div>
                    </div>

                    <hr style="border-color:#e9ecef;">

                    {{-- Section: Content --}}
                    <div style="margin-bottom:2rem; margin-top:2rem;">
                        <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Content <span style="color:#dc3545;">*</span></h6>
                        <textarea id="contentMD" name="content" class="form-control">{{ old('content') }}</textarea>
                        <script>
                            var simplemde = new SimpleMDE({ element: document.getElementById("contentMD") });
                        </script>
                    </div>

                    <hr style="border-color:#e9ecef;">

                    {{-- Section: Image --}}
                    <div style="margin-bottom:2rem; margin-top:2rem;">
                        <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.5rem;">Hero Image</h6>
                        <p style="font-size:0.82rem; color:#6c757d; margin-bottom:1rem;">Used as the article thumbnail and displayed at the top of the article. PNG, JPG, or GIF.</p>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="image" id="imageUpload" accept=".jpg,.jpeg,.png,.gif">
                                <label class="custom-file-label" for="imageUpload">Choose image</label>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color:#e9ecef;">

                    {{-- Section: Options --}}
                    <div style="margin-bottom:2rem; margin-top:2rem;">
                        <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Options</h6>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="articleVisible" class="form-check-input" id="articleVisible" checked>
                            <label class="form-check-label" for="articleVisible" style="font-size:0.875rem; font-weight:600;">Publicly visible</label>
                            <div style="font-size:0.78rem; color:#6c757d; margin-top:0.1rem;">Uncheck to save as a draft.</div>
                        </div>

                        <label style="font-size:0.85rem; font-weight:600; color:#343a40; margin-bottom:0.5rem; display:block;">
                            Email notification
                            <span style="font-size:0.75rem; font-weight:400; color:#6c757d; margin-left:0.5rem;">
                                — sent once when the article is published
                            </span>
                        </label>

                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            <div class="form-check">
                                <input type="radio" value="no" name="emailOption" class="form-check-input" id="emailNo" checked>
                                <label class="form-check-label" for="emailNo" style="font-size:0.875rem;">No email</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" value="controllers" name="emailOption" class="form-check-input" id="emailControllers">
                                <label class="form-check-label" for="emailControllers" style="font-size:0.875rem;">Email rostered controllers</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" value="all" name="emailOption" class="form-check-input" id="emailAll">
                                <label class="form-check-label" for="emailAll" style="font-size:0.875rem;">Email all subscribed members</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" value="allimportant" name="emailOption" class="form-check-input" id="emailAllImportant">
                                <label class="form-check-label" for="emailAllImportant" style="font-size:0.875rem;">
                                    Email all members <span style="color:#dc3545; font-size:0.78rem; font-weight:600;">Important — use sparingly</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color:#e9ecef; margin-top:1.5rem;">

                    <div class="d-flex align-items-center gap-2 mt-3">
                        <button type="submit" class="btn" style="background:#122b44; color:#fff; border-radius:0.375rem; font-size:0.875rem; padding:0.5rem 1.5rem;">
                            Publish Article
                        </button>
                        <a href="{{ route('news.index') }}" style="font-size:0.875rem; color:#6c757d; text-decoration:none; margin-left:1rem;">Cancel</a>
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
