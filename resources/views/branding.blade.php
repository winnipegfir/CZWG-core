@extends('layouts.master')
@section('title', 'Branding - Winnipeg FIR')
@section('description', 'Winnipeg FIR Branding guidelines and logos')
@section('content')

<div class="container py-5">

    {{-- Header --}}
    <div class="mb-4">
        <h1 class="font-weight-bold blue-text mb-1">Branding</h1>
        <p class="text-muted mb-0">Official logos, wordmarks, and colour palette for Winnipeg FIR (CZWG). Please review our <a href="/policies">Branding Guidelines</a> before using any assets.</p>
    </div>

    {{-- Wordmarks & Letterheads --}}
    <h5 class="font-weight-bold blue-text text-uppercase tracking-wide mb-3">
        <i class="fas fa-font mr-2"></i>Wordmarks &amp; Letterheads
    </h5>
    <div class="row mb-5">
        @php
        $wordmarks = [
            ['url' => 'https://winnipegfir.ca/storage/files/uploads/1667585131.png', 'label' => 'Blue Wordmark',    'bg' => '#f8f9fa', 'dark' => false],
            ['url' => 'https://winnipegfir.ca/storage/files/uploads/1667585041.png', 'label' => 'White Wordmark',   'bg' => '#272727', 'dark' => true],
            ['url' => 'https://winnipegfir.ca/storage/files/uploads/1667584624.png', 'label' => 'Blue Letterhead',  'bg' => '#ffffff', 'dark' => false],
            ['url' => 'https://winnipegfir.ca/storage/files/uploads/1667584791.png', 'label' => 'White Letterhead', 'bg' => '#272727', 'dark' => true],
        ];
        @endphp

        @foreach($wordmarks as $asset)
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-img-top d-flex align-items-center justify-content-center p-3"
                     style="background-color: {{ $asset['bg'] }}; min-height: 160px;">
                    <img src="{{ $asset['url'] }}" class="img-fluid" style="max-height: 120px; object-fit: contain;" alt="{{ $asset['label'] }}">
                </div>
                <div class="card-body d-flex flex-column align-items-center py-3">
                    <p class="mb-2 font-weight-bold text-center" style="font-size: .9rem;">{{ $asset['label'] }}</p>
                    <a href="{{ $asset['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary mt-auto">
                        <i class="fas fa-download mr-1"></i>Download
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Icons --}}
    <h5 class="font-weight-bold blue-text text-uppercase mb-3">
        <i class="fas fa-image mr-2"></i>Icons
    </h5>
    <div class="row mb-5">
        @php
        $icons = [
            ['url' => 'https://winnipegfir.ca/storage/files/uploads/1667583981.png', 'label' => 'Blue "W" Icon',  'bg' => '#f8f9fa', 'dark' => false],
            ['url' => 'https://winnipegfir.ca/storage/files/uploads/1667584116.png', 'label' => 'White "W" Icon', 'bg' => '#272727', 'dark' => true],
        ];
        @endphp

        @foreach($icons as $asset)
        <div class="col-md-2 col-sm-4 col-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-img-top d-flex align-items-center justify-content-center p-3"
                     style="background-color: {{ $asset['bg'] }}; min-height: 140px;">
                    <img src="{{ $asset['url'] }}" class="img-fluid" style="max-height: 100px; object-fit: contain;" alt="{{ $asset['label'] }}">
                </div>
                <div class="card-body d-flex flex-column align-items-center py-3">
                    <p class="mb-2 font-weight-bold text-center" style="font-size: .9rem;">{{ $asset['label'] }}</p>
                    <a href="{{ $asset['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary mt-auto">
                        <i class="fas fa-download mr-1"></i>Download
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Colour Palette --}}
    <h5 class="font-weight-bold blue-text text-uppercase mb-3">
        <i class="fas fa-palette mr-2"></i>Official Colour Palette
    </h5>
    <div class="row">
        @php
        $colours = [
            [
                'name'    => 'Deep Sky Blue',
                'hex'     => '#122b44',
                'pantone' => '560 C',
                'cmyk'    => 'C 20  M 10  Y 0  K 73',
                'text'    => '#ffffff',
            ],
            [
                'name'    => 'Classic Winnipeg Blue',
                'hex'     => '#013162',
                'pantone' => '648 C',
                'cmyk'    => 'C 38  M 19  Y 0  K 62',
                'text'    => '#ffffff',
            ],
            [
                'name'    => 'Ice White',
                'hex'     => '#ffffff',
                'pantone' => '115-1 U',
                'cmyk'    => 'C 0  M 0  Y 0  K 0',
                'text'    => '#212529',
            ],
            [
                'name'    => 'Prairie Gold',
                'hex'     => '#feba00',
                'pantone' => '7548 C',
                'cmyk'    => 'C 0  M 27  Y 100  K 0',
                'text'    => '#212529',
            ],
        ];
        @endphp

        @foreach($colours as $colour)
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card shadow-sm border-0 overflow-hidden" style="border-radius: .5rem;">
                <div style="background-color: {{ $colour['hex'] }}; height: 100px;
                     border-bottom: {{ $colour['hex'] === '#ffffff' ? '1px solid #dee2e6' : 'none' }};"></div>
                <div class="card-body py-3">
                    <p class="font-weight-bold mb-1" style="font-size: .95rem;">{{ $colour['name'] }}</p>
                    <p class="mb-0 text-muted" style="font-size: .82rem;">
                        <span class="badge badge-secondary mr-1">HEX</span>
                        <code>{{ $colour['hex'] }}</code>
                    </p>
                    <p class="mb-0 text-muted" style="font-size: .82rem;">
                        <span class="badge badge-secondary mr-1">PMS</span>
                        {{ $colour['pantone'] }}
                    </p>
                    <p class="mb-0 text-muted" style="font-size: .82rem;">
                        <span class="badge badge-secondary mr-1">CMYK</span>
                        {{ $colour['cmyk'] }}
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>

@endsection
