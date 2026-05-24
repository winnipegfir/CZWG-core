@extends('layouts.master')

@section('title', 'Manage: '.$event->name.' - Winnipeg FIR')

@section('content')
<div style="background:#fff; min-height:calc(100vh - 60px); padding:2.5rem 0;">
    <div class="container">

        <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
                <a href="{{ route('events.admin.index') }}" style="font-size:0.82rem; color:#6c757d; text-decoration:none;">
                    <i class="fas fa-arrow-left mr-1"></i> Events
                </a>
                <h1 class="font-weight-bold mb-0 mt-1" style="color:#122b44;">{{ $event->name }}</h1>
            </div>
            <div style="display:flex; gap:0.75rem; align-items:center; margin-top:0.25rem;">
                <a href="{{ route('event.viewapplications', $event->id) }}"
                   style="font-size:0.85rem; color:#122b44; text-decoration:none; border:1px solid #ced4da; border-radius:0.375rem; padding:0.4rem 0.9rem; white-space:nowrap;">
                    <i class="fas fa-inbox mr-1"></i> Applications
                </a>
                <a href="{{ route('events.view', $event->slug) }}" target="_blank"
                   style="font-size:0.85rem; color:#6c757d; text-decoration:none; border:1px solid #ced4da; border-radius:0.375rem; padding:0.4rem 0.9rem; white-space:nowrap;">
                    <i class="fas fa-external-link-alt mr-1"></i> Public page
                </a>
            </div>
        </div>
        <hr>

        {{-- Edit event details --}}
        <div style="margin-bottom:2rem;">
            <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Event Details</h6>

            @if($errors->editEventErrors->any())
                <div class="alert" style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:0.375rem; color:#721c24; font-size:0.875rem; margin-bottom:1.25rem;">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1 pl-3">
                        @foreach($errors->editEventErrors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('events.admin.edit.post', $event->slug) }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Event name <span style="color:#dc3545;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $event->name) }}" class="form-control">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Start date & time (UTC) <span style="color:#dc3545;">*</span></label>
                            <input type="text" name="start" value="{{ old('start', $event->start_timestamp) }}" class="form-control" id="event_start">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">End date & time (UTC) <span style="color:#dc3545;">*</span></label>
                            <input type="text" name="end" value="{{ old('end', $event->end_timestamp) }}" class="form-control" id="event_end">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Departure ICAO <span style="color:#6c757d; font-weight:400;">(optional)</span></label>
                            <input maxlength="4" type="text" name="departure_icao" value="{{ old('departure_icao', $event->departure_icao) }}" class="form-control" placeholder="CYYC">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Arrival ICAO <span style="color:#6c757d; font-weight:400;">(optional)</span></label>
                            <input maxlength="4" type="text" name="arrival_icao" value="{{ old('arrival_icao', $event->arrival_icao) }}" class="form-control" placeholder="CYWG">
                        </div>
                    </div>
                </div>

                <script>
                    flatpickr('#event_start', { enableTime: true, noCalendar: false, dateFormat: "Y-m-d H:i", time_24hr: true });
                    flatpickr('#event_end',   { enableTime: true, noCalendar: false, dateFormat: "Y-m-d H:i", time_24hr: true });
                </script>

                <hr style="border-color:#e9ecef;">

                <div style="margin-bottom:1.5rem; margin-top:1.5rem;">
                    <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Description <span style="color:#dc3545;">*</span></h6>
                    <textarea id="contentMD" name="description" class="form-control">{{ old('description', $event->description) }}</textarea>
                    <script>
                        var simplemde = new SimpleMDE({ element: document.getElementById("contentMD") });
                    </script>
                </div>

                <hr style="border-color:#e9ecef;">

                <div style="margin-bottom:1.5rem; margin-top:1.5rem;">
                    <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.5rem;">Event Image</h6>
                    @if($event->image_url)
                        <img src="{{ $event->image_url }}" alt="{{ $event->name }}"
                             style="width:180px; height:110px; object-fit:cover; border-radius:0.375rem; display:block; margin-bottom:0.75rem;">
                    @endif
                    <p style="font-size:0.82rem; color:#6c757d; margin-bottom:1rem;">No text or logos. PNG, JPG, or GIF. Upload to replace existing image.</p>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="image" id="imageUpload" accept=".jpg,.jpeg,.png,.gif">
                            <label class="custom-file-label" for="imageUpload">Choose image</label>
                        </div>
                    </div>
                </div>

                <hr style="border-color:#e9ecef;">

                <div style="margin-bottom:1.5rem; margin-top:1.5rem;">
                    <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Options</h6>
                    <div class="form-check">
                        <input type="checkbox" name="openControllerApps" class="form-check-input" id="openControllerApps"
                               {{ $event->controller_applications_open ? 'checked' : '' }}>
                        <label class="form-check-label" for="openControllerApps" style="font-size:0.875rem; font-weight:600;">Open controller applications</label>
                        <div style="font-size:0.78rem; color:#6c757d; margin-top:0.1rem;">Allow rostered controllers to apply for positions at this event.</div>
                    </div>
                </div>

                <hr style="border-color:#e9ecef; margin-top:1.5rem;">

                <div class="d-flex align-items-center justify-content-between mt-3">
                    <button type="submit" class="btn" style="background:#122b44; color:#fff; border-radius:0.375rem; font-size:0.875rem; padding:0.5rem 1.5rem;">
                        Save Changes
                    </button>
                    <a href="{{ route('events.admin.delete', $event->slug) }}"
                       onclick="return confirm('Delete \'{{ addslashes($event->name) }}\'? This cannot be undone.')"
                       style="font-size:0.8rem; color:#dc3545; text-decoration:none; font-weight:500;">
                        <i class="fas fa-trash fa-xs mr-1"></i>Delete event
                    </a>
                </div>

            </form>
        </div>

        <script>
            document.getElementById('imageUpload').addEventListener('change', function() {
                this.nextElementSibling.textContent = this.files[0] ? this.files[0].name : 'Choose image';
            });
        </script>

        <hr style="border-color:#e9ecef; margin:2rem 0;">

        {{-- Event roster --}}
        <div style="margin-bottom:2rem;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0;">Event Roster</h6>
                <a href="#" data-toggle="modal" data-target="#confirmController"
                   style="font-size:0.8rem; color:#122b44; text-decoration:none; border:1px solid #ced4da; border-radius:0.375rem; padding:0.3rem 0.75rem;">
                    <i class="fas fa-plus fa-xs mr-1"></i> Add Controller
                </a>
            </div>

            @if(count($eventroster) < 1)
                <p style="color:#6c757d; font-size:0.875rem;">Nobody confirmed to control yet.</p>
            @else
                <div style="border:1px solid #e9ecef; border-radius:0.5rem; overflow:hidden;">
                    <table class="table table-hover mb-0" style="font-size:0.875rem;">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.6rem 1rem;">Controller</th>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.6rem 1rem;">Airport</th>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.6rem 1rem;">Position</th>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.6rem 1rem;">Hours (UTC)</th>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.6rem 1rem; text-align:right;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($eventroster as $roster)
                                <tr style="border-bottom:1px solid #f1f3f5;">
                                    <td style="padding:0.6rem 1rem; vertical-align:middle; font-weight:600; color:#122b44;">
                                        {{ $roster->user->fullName('FLC') }}
                                    </td>
                                    <td style="padding:0.6rem 1rem; vertical-align:middle; color:#495057;">
                                        {{ $roster->airport }}
                                    </td>
                                    <td style="padding:0.6rem 1rem; vertical-align:middle; color:#495057;">
                                        @if($roster->position === 'Relief')
                                            <span style="background:#fff3cd; color:#856404; font-size:0.72rem; font-weight:600; padding:0.2rem 0.55rem; border-radius:999px;">Stand-by</span>
                                        @else
                                            {{ $roster->position }}
                                        @endif
                                    </td>
                                    <td style="padding:0.6rem 1rem; vertical-align:middle; color:#6c757d; font-size:0.82rem;">
                                        {{ $roster->start_timestamp }}z – {{ $roster->end_timestamp }}z
                                    </td>
                                    <td style="padding:0.6rem 1rem; vertical-align:middle; text-align:right;">
                                        <form method="POST" action="{{ route('event.deletecontroller', $roster->user_id) }}" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $roster->event_id }}">
                                            <button type="submit" onclick="return confirm('Remove this controller from the roster?')"
                                                    style="background:none; border:none; color:#dc3545; font-size:0.8rem; font-weight:500; cursor:pointer; padding:0;">
                                                <i class="fas fa-times fa-xs mr-1"></i>Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <hr style="border-color:#e9ecef; margin:2rem 0;">

        {{-- Post update --}}
        <div style="margin-bottom:2rem;">
            <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Post an Update</h6>

            @if($errors->createUpdateErrors->any())
                <div class="alert" style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:0.375rem; color:#721c24; font-size:0.875rem; margin-bottom:1.25rem;">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->createUpdateErrors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('events.admin.update.post', $event->slug) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Title</label>
                    <input type="text" name="updateTitle" value="{{ old('updateTitle') }}" class="form-control" placeholder="e.g. Route change announcement">
                </div>
                <div class="form-group">
                    <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Content</label>
                    <textarea id="updateContent" name="updateContent" class="form-control">{{ old('updateContent') }}</textarea>
                    <script>
                        var simplemdeUpdate = new SimpleMDE({ element: document.getElementById("updateContent") });
                    </script>
                </div>
                <button type="submit" class="btn" style="background:#122b44; color:#fff; border-radius:0.375rem; font-size:0.875rem; padding:0.5rem 1.5rem;">
                    Post Update
                </button>
            </form>
        </div>

        {{-- Updates list --}}
        @if(count($updates) > 0)
            <hr style="border-color:#e9ecef; margin:2rem 0;">
            <div style="margin-bottom:2rem;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">
                    Posted Updates ({{ count($updates) }})
                </h6>
                @foreach($updates as $u)
                    <div style="border:1px solid #e9ecef; border-radius:0.5rem; padding:1.25rem; margin-bottom:0.75rem;">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div style="font-weight:700; color:#122b44; font-size:0.95rem; margin-bottom:0.2rem;">{{ $u->title }}</div>
                                <div style="font-size:0.78rem; color:#6c757d; margin-bottom:0.75rem;">
                                    <i class="far fa-clock mr-1"></i>{{ $u->created_pretty() }}
                                    &nbsp;·&nbsp;
                                    <i class="far fa-user-circle mr-1"></i>{{ $u->author_pretty() }}
                                </div>
                            </div>
                            <a href="{{ route('events.admin.update.delete', [$event->slug, $u->id]) }}"
                               onclick="return confirm('Delete this update?')"
                               style="font-size:0.8rem; color:#dc3545; text-decoration:none; font-weight:500; white-space:nowrap; margin-left:1rem;">
                                <i class="fas fa-trash fa-xs mr-1"></i>Delete
                            </a>
                        </div>
                        <div style="font-size:0.875rem; color:#343a40; line-height:1.7;">{{ $u->html() }}</div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

{{-- Add Controller modal --}}
<div class="modal fade" id="confirmController" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:0.5rem;">
            <div class="modal-header" style="border-bottom:1px solid #e9ecef;">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Add Controller to Roster</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('event.addcontroller', $event->id) }}">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <input type="hidden" name="event_name" value="{{ $event->name }}">
                <input type="hidden" name="event_date" value="{{ $event->start_timestamp }}">
                <div class="modal-body" style="font-size:0.875rem;">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Controller</label>
                        <select class="form-control custom-select" name="user_cid">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->id }} – {{ $user->fname }} {{ $user->lname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Start (zulu)</label>
                                <input type="text" name="start_timestamp" class="form-control" id="ctrl_start">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label style="font-size:0.85rem; font-weight:600; color:#343a40;">End (zulu)</label>
                                <input type="text" name="end_timestamp" class="form-control" id="ctrl_end">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Airport</label>
                                <input type="text" name="airport" class="form-control" placeholder="CYWG" id="ctrl_airport">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Position</label>
                                <select name="position" class="form-control custom-select">
                                    <option value="Delivery">Delivery</option>
                                    <option value="Ground">Ground</option>
                                    <option value="Tower">Tower</option>
                                    <option value="Departure">Departure</option>
                                    <option value="Arrival">Arrival</option>
                                    <option value="Centre">Centre</option>
                                    <option value="Relief">Relief (Stand-by)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <script>
                        flatpickr('#ctrl_start', { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true, defaultDate: "{{ $event->flatpickr_limits()[0] }}" });
                        flatpickr('#ctrl_end',   { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true, defaultDate: "{{ $event->flatpickr_limits()[1] }}" });
                    </script>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e9ecef;">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background:#122b44; color:#fff; border-radius:0.375rem;">Confirm Controller</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->editEventErrors->any())
<script>window.scrollTo(0, 0);</script>
@endif
@endsection
