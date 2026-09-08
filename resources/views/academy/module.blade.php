@extends('layouts.master')
@section('title', $module->title.' - Training Academy')
@section('content')
@include('academy._styles')

<div class="academy-hero">
    <div class="container-fluid academy-course-shell-width">
        <a href="{{ route('academy.courses.show', $course->slug) }}" class="academy-hero-link">
            <i class="fas fa-arrow-left"></i> {{ $course->title }}
        </a>
        <h1>{{ $module->title }}</h1>
        <p class="mb-0 academy-hero-subtitle">{{ $module->description }}</p>
    </div>
</div>

<div class="academy-body academy-course-body">
    <div class="container-fluid academy-course-shell-width">
        <div class="academy-course-shell" data-academy-course-shell>
            @include('academy._course_sidebar')
            <main class="academy-course-main">
        @if($module->googleSlidesEmbedUrl())
            <div class="academy-slide-deck academy-online-slide-deck mb-4" data-academy-online-slides tabindex="0" aria-label="{{ $module->title }} slideshow">
                <div class="academy-slide-stage academy-online-slide-stage">
                    <iframe src="{{ $module->googleSlidesEmbedUrl() }}" allowfullscreen title="{{ $module->title }} presentation"></iframe>
                </div>
                <div class="academy-slide-controls">
                    <div>
                        <strong><i class="fas fa-images mr-1"></i> {{ $module->title }}</strong>
                        <span class="academy-muted ml-2">Use the presentation controls to move between slides.</span>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" data-online-slide-fullscreen>
                        <i class="fas fa-expand mr-1"></i> Full screen
                    </button>
                </div>
                <div class="academy-slide-hint">A published Google Slides deck is being used for this module. Clear its Publish to Web embed code in the Academy Editor to fall back to the locally hosted deck.</div>
            </div>
        @elseif(($module->slide_count ?? 0) > 0)
            <div class="academy-slide-deck mb-4" data-academy-slides tabindex="0" aria-label="{{ $module->title }} slideshow">
                <div class="academy-slide-stage">
                    @for($slide = 1; $slide <= $module->slide_count; $slide++)
                        @php
                            $slideBase = $module->slide_asset_path ?: 'academy-assets/slides/'.$course->slug.'/'.$module->slug;
                            $slideFile = trim($slideBase, '/').'/slide-'.str_pad($slide, 3, '0', STR_PAD_LEFT).'.jpg';
                        @endphp
                        <img
                            src="{{ asset($slideFile) }}"
                            alt="{{ $module->title }} — slide {{ $slide }} of {{ $module->slide_count }}"
                            class="academy-slide-image"
                            data-academy-slide
                            {{ $slide === 1 ? '' : 'hidden' }}
                            loading="{{ $slide === 1 ? 'eager' : 'lazy' }}"
                        >
                    @endfor
                </div>
                <div class="academy-slide-controls">
                    <button type="button" class="btn btn-outline-primary" data-slide-prev>
                        <i class="fas fa-chevron-left mr-1"></i> Previous
                    </button>
                    <span class="academy-slide-counter" data-slide-counter>1 / {{ $module->slide_count }}</span>
                    <div class="academy-slide-control-right">
                        <button type="button" class="btn btn-outline-secondary" data-slide-fullscreen>
                            <i class="fas fa-expand mr-1"></i> Full screen
                        </button>
                        <button type="button" class="btn btn-primary" data-slide-next>
                            Next <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
                <div class="academy-slide-hint">Use the Previous/Next buttons or your left and right arrow keys.</div>
            </div>
        @elseif(in_array($module->slug, ['final-self-assessment', 'final-knowledge-check']))
            <div class="academy-panel">
                <div class="academy-kicker">Self assessment</div>
                <h4>Ready to complete your self assessment?</h4>
                <p class="academy-muted mb-0">
                    This self assessment covers material from all modules in {{ $course->title }}.
                    Multiple-choice questions are marked automatically; written scenario questions are sent to an instructor for review.
                </p>
            </div>
        @else
            <div class="academy-panel text-center academy-muted">The presentation for this module has not been added yet.</div>
        @endif

        @if($module->audio_url)
            <div class="academy-panel academy-audio-panel mb-4">
                <div>
                    <div class="academy-kicker">Module audio</div>
                    <h4 class="mb-1">Module audio clip</h4>
                    <p class="academy-muted mb-0">Audio referenced by this module's presentation.</p>
                </div>
                <audio controls preload="metadata">
                    <source src="{{ asset($module->audio_url) }}" type="audio/mpeg">
                    Your browser does not support the audio player.
                </audio>
            </div>
        @endif

        @if($module->quiz && $module->quiz->published)
            <form method="POST" action="{{ route('academy.quizzes.submit', $module->quiz) }}">
                @csrf
                <div class="academy-panel">
                    <div class="academy-kicker">Self assessment</div>
                    <h4>{{ $module->quiz->title }}</h4>
                    <p class="academy-muted">
                        Passing score: {{ $module->quiz->passing_score }}% · {{ $module->quiz->questions->count() }} questions
                    </p>

                    @if($latestSubmission)
                        @if($latestSubmission->status === 'graded')
                            <div class="alert {{ $latestSubmission->passed() ? 'alert-success' : 'alert-warning' }}">
                                Latest result: <strong>{{ $latestSubmission->passed() ? 'Passed' : 'Not Passed' }}</strong> — {{ round($latestSubmission->percentage()) }}%.
                                <a href="{{ route('academy.submissions.show', $latestSubmission) }}">View result</a>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Your latest submission is <strong>awaiting instructor review</strong>.
                                <a href="{{ route('academy.submissions.show', $latestSubmission) }}">View it</a>
                            </div>
                        @endif
                    @endif

                    @foreach($module->quiz->questions as $question)
                        <div class="border rounded p-3 mb-3 academy-question-card">
                            <div class="d-flex justify-content-between align-items-start" style="gap:1rem">
                                <strong>{{ $loop->iteration }}. {{ $question->question }}</strong>
                                <span class="academy-status">{{ $question->points }} {{ Str::plural('point', $question->points) }}</span>
                            </div>

                            @if($question->type === 'written')
                                <textarea
                                    class="form-control mt-3"
                                    name="responses[{{ $question->id }}]"
                                    rows="5"
                                    required
                                    placeholder="Write your answer here…"
                                >{{ old('responses.'.$question->id) }}</textarea>
                                <small class="academy-muted">This scenario response is marked manually by an instructor.</small>
                            @else
                                @foreach($question->answers as $answer)
                                    <div class="custom-control custom-radio mt-2">
                                        <input
                                            class="custom-control-input"
                                            type="radio"
                                            required
                                            id="answer{{ $answer->id }}"
                                            name="responses[{{ $question->id }}]"
                                            value="{{ $answer->id }}"
                                            {{ old('responses.'.$question->id) == $answer->id ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label" for="answer{{ $answer->id }}">{{ $answer->answer }}</label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach

                    @if($module->quiz->questions->count())
                        <button class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane mr-1"></i> Submit self assessment
                        </button>
                    @else
                        <p class="academy-muted mb-0">Questions have not been added yet.</p>
                    @endif
                </div>
            </form>
        @endif

        <div class="academy-module-pagination">
            <div>
                @if($previousModule)
                    <a class="btn btn-outline-primary" href="{{ route('academy.modules.show', [$course->slug, $previousModule->slug]) }}"><i class="fas fa-chevron-left mr-1"></i> {{ $previousModule->title }}</a>
                @endif
            </div>
            <div>
                @if($nextModule)
                    <a class="btn btn-primary" href="{{ route('academy.modules.show', [$course->slug, $nextModule->slug]) }}">{{ $nextModule->title }} <i class="fas fa-chevron-right ml-1"></i></a>
                @endif
            </div>
        </div>
            </main>
        </div>
    </div>
</div>
@include('academy._sidebar_script')

@if(($module->slide_count ?? 0) > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-academy-slides]').forEach(function (deck) {
        var slides = Array.prototype.slice.call(deck.querySelectorAll('[data-academy-slide]'));
        var previous = deck.querySelector('[data-slide-prev]');
        var next = deck.querySelector('[data-slide-next]');
        var counter = deck.querySelector('[data-slide-counter]');
        var fullscreen = deck.querySelector('[data-slide-fullscreen]');
        var current = 0;

        function showSlide(index) {
            current = Math.max(0, Math.min(index, slides.length - 1));
            slides.forEach(function (slide, slideIndex) {
                slide.hidden = slideIndex !== current;
            });
            counter.textContent = (current + 1) + ' / ' + slides.length;
            previous.disabled = current === 0;
            next.disabled = current === slides.length - 1;
        }

        previous.addEventListener('click', function () { showSlide(current - 1); });
        next.addEventListener('click', function () { showSlide(current + 1); });
        deck.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                showSlide(current - 1);
            }
            if (event.key === 'ArrowRight') {
                event.preventDefault();
                showSlide(current + 1);
            }
        });

        fullscreen.addEventListener('click', function () {
            if (!document.fullscreenElement && deck.requestFullscreen) {
                deck.requestFullscreen();
            } else if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        });

        showSlide(0);
    });
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-academy-online-slides]').forEach(function (deck) {
        var fullscreen = deck.querySelector('[data-online-slide-fullscreen]');
        if (!fullscreen) return;
        fullscreen.addEventListener('click', function () {
            if (!document.fullscreenElement && deck.requestFullscreen) {
                deck.requestFullscreen();
            } else if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        });
    });
});
</script>
@stop
