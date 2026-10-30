<article class="card mb-4 {{ ($featured ?? false) ? 'border-primary shadow' : '' }}">
    <div class="card-body {{ ($featured ?? false) ? 'p-4' : '' }}">
        @if($featured ?? false)<span class="badge badge-primary mb-3">CURRENT RELEASE</span>@endif
        <h2 class="h4 blue-text font-weight-bold">{{ $release['name'] }}</h2>
        <p class="text-muted">Version {{ $release['version'] }} &middot;
            <time datetime="{{ $release['published_at'] }}">{{ \Carbon\Carbon::parse($release['published_at'])->timezone('America/Winnipeg')->format('F j, Y') }}</time>
            @if($release['archived']) <span class="badge badge-secondary">Archived</span> @endif
        </p>
        <div class="mb-3" style="white-space:pre-wrap; overflow-wrap:anywhere;">{{ $release['notes'] ?: 'No release notes provided.' }}</div>
        @if($release['packages_complete'])
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h3 class="h5">Full Install Package</h3>
                    <p class="text-muted">For a new installation: includes the complete sector-file package.</p>
                    <a class="btn btn-primary {{ ($featured ?? false) ? 'btn-lg' : '' }}" href="{{ $release['full_asset']['browser_download_url'] }}" rel="noopener">Download Full Install <span class="sr-only">version {{ $release['version'] }}</span></a>
                    <div class="small text-muted mt-2">ZIP &middot; {{ number_format($release['full_asset']['size'] / 1048576, 1) }} MB</div>
                </div>
                <div class="col-md-6 mb-3">
                    <h3 class="h5">Nav Data Update</h3>
                    <p class="text-muted">For an existing installation: includes the updated navigation files.</p>
                    <a class="btn btn-outline-primary {{ ($featured ?? false) ? 'btn-lg' : '' }}" href="{{ $release['nav_asset']['browser_download_url'] }}" rel="noopener">Download Nav Data <span class="sr-only">version {{ $release['version'] }}</span></a>
                    <div class="small text-muted mt-2">ZIP &middot; {{ number_format($release['nav_asset']['size'] / 1048576, 1) }} MB</div>
                </div>
            </div>
        @elseif($release['legacy_asset'])
            <p class="text-muted">This older release has a single package. Check its notes before installing.</p>
            <a class="btn btn-outline-primary" href="{{ $release['legacy_asset']['browser_download_url'] }}" rel="noopener">Download Legacy ZIP <span class="sr-only">version {{ $release['version'] }}</span></a>
        @endif
    </div>
</article>
