@extends('layouts.master')
@section('navbarprim') @parent @stop
@section('title', 'Sweatbox Files — Winnipeg FIR')

@section('content')
@include('includes.trainingMenu')

@php
    /*
     * Add uploaded files below using the link returned by the admin File Uploader.
     * Example:
     * [
     *     'name' => 'CYWG Ground — Basic Taxi',
     *     'description' => 'Basic ground-control training scenario.',
     *     'updated' => 'September 3, 2026',
     *     'url' => '/storage/files/uploads/1234567890.txt',
     * ],
     */
    $legacySweatboxFiles = [
        'CYWG_GND' => [
            [
                'name' => '02 - Sweatbox Loading Guide.pdf',
                'description' => 'Instructions for loading and using the ground Sweatbox exercises.',
                'updated' => 'September 2, 2026',
                'url' => asset('downloads/sweatbox/CYWG_GND/CZWG Sweatbox Courseware v2025.01/02 - Sweatbox Loading Guide.pdf'),
            ],
            [
                'name' => 'Ex S1G01 Instructor Script.pdf',
                'description' => 'Instructor script for exercise S1G01.',
                'updated' => 'September 2, 2026',
                'url' => asset('downloads/sweatbox/CYWG_GND/CZWG Sweatbox Courseware v2025.01/Ex S1G01 Instructor Script.pdf'),
            ],
            [
                'name' => 'Ex S1G01.txt',
                'description' => 'Sweatbox scenario file for exercise S1G01.',
                'updated' => 'September 2, 2026',
                'url' => asset('downloads/sweatbox/CYWG_GND/CZWG Sweatbox Courseware v2025.01/Ex S1G01.txt'),
            ],
            [
                'name' => 'Ex S1G02 Instructor Script.pdf',
                'description' => 'Instructor script for exercise S1G02.',
                'updated' => 'September 2, 2026',
                'url' => asset('downloads/sweatbox/CYWG_GND/CZWG Sweatbox Courseware v2025.01/Ex S1G02 Instructor Script.pdf'),
            ],
            [
                'name' => 'Ex S1G02.txt',
                'description' => 'Sweatbox scenario file for exercise S1G02.',
                'updated' => 'September 2, 2026',
                'url' => asset('downloads/sweatbox/CYWG_GND/CZWG Sweatbox Courseware v2025.01/Ex S1G02.txt'),
            ],
        ],
        'CYWG_TWR' => [
            [
                'name' => 'TWRTrainerSetup.exe',
                'description' => 'Tower Trainer installation program for Windows.',
                'updated' => 'September 3, 2026',
                'url' => asset('downloads/sweatbox/CYWG_TWR/TWRTrainerSetup.exe'),
            ],
            [
                'name' => 'cywg.apt',
                'description' => 'CYWG airport file for Tower Trainer.',
                'updated' => 'September 3, 2026',
                'url' => asset('downloads/sweatbox/CYWG_TWR/TWRTrainer files/cywg.apt'),
            ],
            [
                'name' => 'cywg_twr_1.air',
                'description' => 'CYWG tower traffic scenario for Tower Trainer.',
                'updated' => 'September 3, 2026',
                'url' => asset('downloads/sweatbox/CYWG_TWR/TWRTrainer files/cywg_twr_1.air'),
            ],
        ],
        'CYWG_TML' => [
            [
                'name' => 'CYWG_TML.txt',
                'description' => 'Sweatbox scenario file for CYWG Terminal training.',
                'updated' => 'September 3, 2026',
                'url' => asset('downloads/sweatbox/CYWG_TML/CYWG_TML.txt'),
            ],
        ],
    ];

    $positionDetails = [
        'CYWG_GND' => ['label' => 'Ground', 'icon' => 'fa-route'],
        'CYWG_TWR' => ['label' => 'Tower', 'icon' => 'fa-broadcast-tower'],
        'CYWG_TML' => ['label' => 'Terminal', 'icon' => 'fa-plane-arrival'],
    ];

    $totalFiles = $sweatboxFiles->sum(fn ($files) => $files->count());
@endphp

<style>
    .sweatbox-position-tabs {
        gap: .5rem;
    }

    .sweatbox-position-tabs .nav-link {
        border: 1px solid rgba(148, 163, 184, .35);
        border-radius: .5rem;
        color: inherit;
        font-weight: 700;
        padding: .7rem 1rem;
    }

    .sweatbox-position-tabs .nav-link.active {
        background: #1572b6;
        border-color: #1572b6;
        color: #fff;
        box-shadow: 0 4px 12px rgba(21, 114, 182, .22);
    }

    .sweatbox-file-row {
        border-top: 1px solid rgba(148, 163, 184, .28);
        padding: 1rem 0;
    }

    .sweatbox-file-row:first-child {
        border-top: 0;
        padding-top: 0;
    }

    .sweatbox-file-icon {
        align-items: center;
        background: rgba(21, 114, 182, .13);
        border-radius: .45rem;
        color: #2196f3;
        display: flex;
        flex-shrink: 0;
        height: 42px;
        justify-content: center;
        width: 42px;
    }

    @media (max-width: 575.98px) {
        .sweatbox-position-tabs .nav-item {
            width: 100%;
        }

        .sweatbox-position-tabs .nav-link {
            text-align: left;
        }

        .sweatbox-download {
            margin-top: .75rem;
            width: 100%;
        }
    }
</style>

<div style="background:#f8fafc; padding:2rem 0; min-height:65vh;">
    <div class="container">
        <div class="card mb-4 overflow-hidden" style="border:1px solid rgba(33,150,243,.22); background:linear-gradient(120deg, #0b2942 0%, #103b5c 55%, #0c304b 100%); color:#fff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center mr-3">
                        <div style="width:52px; height:52px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.18); border-radius:.65rem; margin-right:1rem; flex-shrink:0;">
                            <i class="fas fa-headset" style="font-size:1.35rem;"></i>
                        </div>
                        <div>
                            <div style="font-size:.7rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase; color:#8fcfff;">Training Resources</div>
                            <h1 class="font-weight-bold mb-1" style="font-size:1.75rem; color:#fff;">Sweatbox Files</h1>
                            <p class="mb-0" style="font-size:.9rem; color:rgba(255,255,255,.78);">Current Winnipeg FIR scenarios, courseware and supporting files.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap mt-3 mt-md-0" style="gap:.5rem;">
                        <div style="background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.16); border-radius:.5rem; padding:.55rem .85rem;">
                            <span style="font-size:1.1rem; font-weight:700;">{{ $totalFiles }}</span>
                            <span style="font-size:.78rem; color:rgba(255,255,255,.75);"> available files</span>
                        </div>
                        @if(Auth::user()->permissions >= 5)
                            <a href="{{ route('dashboard.upload') }}" class="btn btn-sm btn-light" target="_blank"><i class="fas fa-upload mr-1"></i> File Uploader</a>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addSweatboxFileModal"><i class="fas fa-plus mr-1"></i> Add File</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body p-4">
                <ul class="nav nav-pills sweatbox-position-tabs mb-4" id="sweatboxTabs" role="tablist">
                    @foreach($sweatboxFiles as $position => $files)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                               id="{{ strtolower($position) }}-tab"
                               data-toggle="pill"
                               href="#{{ strtolower($position) }}"
                               role="tab"
                               aria-controls="{{ strtolower($position) }}"
                               aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <i class="fas {{ $positionDetails[$position]['icon'] }} mr-2"></i>
                                {{ $position }}
                                <span class="badge badge-light ml-2">{{ count($files) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content" id="sweatboxTabContent">
                    @foreach($sweatboxFiles as $position => $files)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                             id="{{ strtolower($position) }}"
                             role="tabpanel"
                             aria-labelledby="{{ strtolower($position) }}-tab">
                            <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                <div>
                                    <h2 class="font-weight-bold mb-1" style="font-size:1.15rem; color:#2196f3;">{{ $positionDetails[$position]['label'] }} Files</h2>
                                    <p class="text-muted mb-0" style="font-size:.82rem;">{{ count($files) }} {{ count($files) === 1 ? 'download' : 'downloads' }} available for {{ $position }}.</p>
                                </div>
                                <span class="badge badge-primary mt-2 mt-sm-0" style="padding:.45rem .65rem;">{{ $position }}</span>
                            </div>

                            @forelse($files as $file)
                                @php
                                    $extension = strtoupper(pathinfo($file->name, PATHINFO_EXTENSION));
                                    $fileIcon = $extension === 'PDF' ? 'fa-file-pdf' : ($extension === 'EXE' ? 'fa-windows' : 'fa-file-code');
                                    $downloadUrl = \Illuminate\Support\Str::startsWith($file->file_url, ['http://', 'https://'])
                                        ? $file->file_url
                                        : asset(ltrim($file->file_url, '/'));
                                @endphp
                                <div class="sweatbox-file-row">
                                    <div class="d-flex align-items-center flex-wrap flex-sm-nowrap">
                                        <div class="sweatbox-file-icon mr-3">
                                            <i class="fas {{ $fileIcon }}"></i>
                                        </div>
                                        <div style="flex:1; min-width:180px;">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <span class="font-weight-bold mr-2" style="font-size:.9rem;">{{ $file->name }}</span>
                                                <span class="badge badge-secondary">{{ $extension }}</span>
                                            </div>
                                            <div class="text-muted mt-1" style="font-size:.8rem; line-height:1.4;">{{ $file->description }}</div>
                                            <div class="mt-1" style="color:#94a3b8; font-size:.72rem;"><i class="far fa-clock mr-1"></i>Updated {{ $file->updated_on->format('F j, Y') }}</div>
                                        </div>
                                        @if(Auth::user()->permissions >= 5)
                                            <button type="button" class="btn btn-sm btn-outline-secondary sweatbox-download ml-sm-3" data-toggle="modal" data-target="#editSweatboxFileModal{{ $file->id }}"><i class="fas fa-edit mr-1"></i> Edit</button>
                                        @endif
                                        <a href="{{ $downloadUrl }}" class="btn btn-sm btn-primary sweatbox-download ml-sm-2" download aria-label="Download {{ $file->name }}">
                                            <i class="fas fa-download mr-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="far fa-folder-open mb-2" style="font-size:1.5rem; color:#94a3b8;"></i>
                                    <p class="text-muted mb-0" style="font-size:.85rem;">No files available yet.</p>
                                </div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-0" role="note" style="background:#122b44; border-left:4px solid #2196f3; border-radius:.45rem; color:#fff; padding:.9rem 1rem; box-shadow:0 3px 10px rgba(15,23,42,.14);">
            <i class="fas fa-shield-alt mr-3" style="color:#70bfff; font-size:1.1rem;"></i>
            <div style="font-size:.82rem; line-height:1.4; color:#fff;">
                <strong style="color:#fff;">Official training resources.</strong>
                Files are maintained by Winnipeg FIR Training Administration. For any support, contact the Chief Instructor.
            </div>
        </div>

        @if(Auth::user()->permissions >= 5)
            @foreach($sweatboxFiles->flatten() as $file)
                <div class="modal fade" id="editSweatboxFileModal{{ $file->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('training.sweatbox-files.update', $file->id) }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title font-weight-bold">Edit Sweatbox File</h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    @include('dashboard.training.partials.sweatbox-file-fields', ['sweatboxFile' => $file])
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="if(confirm('Remove this listing? The uploaded file itself will remain stored.')) document.getElementById('deleteSweatboxFile{{ $file->id }}').submit();">Delete Listing</button>
                                    <div><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button> <button type="submit" class="btn btn-primary">Save Changes</button></div>
                                </div>
                            </form>
                            <form id="deleteSweatboxFile{{ $file->id }}" method="POST" action="{{ route('training.sweatbox-files.destroy', $file->id) }}" class="d-none">@csrf @method('DELETE')</form>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="modal fade" id="addSweatboxFileModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('training.sweatbox-files.store') }}">
                            @csrf
                            <div class="modal-header"><h5 class="modal-title font-weight-bold">Add Sweatbox File</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                            <div class="modal-body">
                                <div class="alert alert-info py-2" style="font-size:.8rem;">Upload the file using <a href="{{ route('dashboard.upload') }}" target="_blank" class="font-weight-bold">File Uploader</a>, then paste its returned link below.</div>
                                @include('dashboard.training.partials.sweatbox-file-fields', ['sweatboxFile' => null])
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add File</button></div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@stop
