@if(\App\Services\AcademyLaunchNotice::eligible(Auth::user()))
    <span class="academy-launch-notice" data-academy-launch-notice data-endpoint="{{ route('academy.launch-notice') }}" hidden>New</span>
    @once
        <link rel="stylesheet" href="{{ asset('css/academy-launch-notice.css') }}?v=2">
        <script src="{{ asset('js/academy-launch-notice.js') }}?v=2" defer></script>
    @endonce
@endif
