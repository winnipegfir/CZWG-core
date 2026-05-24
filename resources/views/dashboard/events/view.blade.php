@extends('layouts.master')

@section('title', $event->name.' - Winnipeg FIR')

@section('content')
<div style="background:#fff; min-height:calc(100vh - 60px); padding:2.5rem 0;">
    <div class="container">

        <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
                <a href="{{ route('events.admin.index') }}" style="font-size:0.82rem; color:#6c757d; text-decoration:none;">
                    <i class="fas fa-arrow-left mr-1"></i> Events
                </a>
                <h1 class="font-weight-bold mb-0 mt-1" style="color:#122b44; font-size:1.6rem;">{{ $event->name }}</h1>
                <p style="color:#6c757d; font-size:0.82rem; margin-top:0.25rem; margin-bottom:0;">
                    {{ $event->start_timestamp_pretty() }}
                    &nbsp;·&nbsp;
                    @if($event->event_in_past())
                        <span style="color:#6c757d; font-weight:600;">Past</span>
                    @else
                        <span style="color:#155724; font-weight:600;">Upcoming</span>
                    @endif
                </p>
            </div>
            <div style="display:flex; gap:0.5rem; margin-top:0.25rem; flex-shrink:0;">
                <a href="{{ route('events.view', $event->slug) }}" target="_blank"
                   style="font-size:0.82rem; color:#6c757d; text-decoration:none; white-space:nowrap; padding:0.3rem 0.75rem; border:1px solid #e9ecef; border-radius:0.375rem;">
                    <i class="fas fa-external-link-alt fa-xs mr-1"></i> View live
                </a>
                <button data-toggle="modal" data-target="#editEvent"
                        style="font-size:0.82rem; color:#122b44; background:#fff; border:1px solid #ced4da; border-radius:0.375rem; padding:0.3rem 0.85rem; cursor:pointer; white-space:nowrap;">
                    <i class="fas fa-edit fa-xs mr-1"></i> Edit
                </button>
                <button data-toggle="modal" data-target="#createUpdate"
                        style="font-size:0.82rem; color:#122b44; background:#fff; border:1px solid #ced4da; border-radius:0.375rem; padding:0.3rem 0.85rem; cursor:pointer; white-space:nowrap;">
                    <i class="fas fa-plus fa-xs mr-1"></i> Update
                </button>
                <button data-toggle="modal" data-target="#deleteEvent"
                        style="font-size:0.82rem; color:#dc3545; background:#fff; border:1px solid #f5c6cb; border-radius:0.375rem; padding:0.3rem 0.85rem; cursor:pointer; white-space:nowrap;">
                    <i class="fas fa-trash fa-xs mr-1"></i> Delete
                </button>
            </div>
        </div>
        <hr>

        {{-- Details --}}
        <div style="margin-bottom:2rem;">
            <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Details</h6>
            <div class="row" style="font-size:0.875rem;">
                <div class="col-sm-3">
                    <div style="margin-bottom:0.75rem;">
                        <div style="color:#6c757d; font-size:0.78rem; margin-bottom:0.15rem;">Start</div>
                        <div style="color:#343a40; font-weight:600;">{{ $event->start_timestamp_pretty() }}</div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div style="margin-bottom:0.75rem;">
                        <div style="color:#6c757d; font-size:0.78rem; margin-bottom:0.15rem;">End</div>
                        <div style="color:#343a40; font-weight:600;">{{ $event->end_timestamp_pretty() }}</div>
                    </div>
                </div>
                @if($event->departure_icao || $event->arrival_icao)
                <div class="col-sm-3">
                    <div style="margin-bottom:0.75rem;">
                        <div style="color:#6c757d; font-size:0.78rem; margin-bottom:0.15rem;">Route</div>
                        <div style="color:#343a40; font-weight:600;">
                            {{ $event->departure_icao ?: '—' }} → {{ $event->arrival_icao ?: '—' }}
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-sm-3">
                    <div style="margin-bottom:0.75rem;">
                        <div style="color:#6c757d; font-size:0.78rem; margin-bottom:0.15rem;">Controller Apps</div>
                        <div style="font-weight:600; color:{{ $event->controller_applications_open ? '#155724' : '#6c757d' }};">
                            {{ $event->controller_applications_open ? 'Open' : 'Closed' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr style="border-color:#e9ecef;">

        {{-- Description --}}
        <div style="margin-bottom:2rem; margin-top:2rem;">
            <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Description</h6>
            @if($event->image_url)
                <img src="{{ $event->image_url }}" alt="{{ $event->name }}"
                     style="width:100%; max-height:280px; object-fit:cover; border-radius:0.5rem; margin-bottom:1.25rem; border:1px solid #e9ecef; display:block;">
            @endif
            <div style="font-size:0.9rem; line-height:1.75; color:#343a40;">{{ $event->html() }}</div>
        </div>

        <hr style="border-color:#e9ecef;">

        {{-- Updates --}}
        <div style="margin-bottom:2rem; margin-top:2rem;">
            <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Updates</h6>
            @if(count($updates) === 0)
                <p style="color:#6c757d; font-size:0.875rem;">No updates posted.</p>
            @else
                @foreach($updates as $u)
                    <div style="border:1px solid #e9ecef; border-radius:0.5rem; padding:1rem 1.25rem; margin-bottom:0.75rem;">
                        <div class="d-flex align-items-start justify-content-between">
                            <div style="font-weight:700; color:#122b44; font-size:0.9rem;">{{ $u->title }}</div>
                            <a href="{{ route('events.admin.update.delete', [$event->slug, $u->id]) }}"
                               onclick="return confirm('Delete this update?')"
                               style="font-size:0.78rem; color:#dc3545; text-decoration:none; flex-shrink:0; margin-left:1rem;">
                                <i class="fas fa-trash fa-xs mr-1"></i>Delete
                            </a>
                        </div>
                        <div style="font-size:0.78rem; color:#6c757d; margin:0.25rem 0 0.75rem;">
                            <i class="far fa-clock mr-1"></i>{{ $u->created_pretty() }} &nbsp;·&nbsp; {{ $u->author_pretty() }}
                        </div>
                        <div style="font-size:0.875rem; color:#495057; line-height:1.6;">{{ $u->html() }}</div>
                    </div>
                @endforeach
            @endif
        </div>

        <hr style="border-color:#e9ecef;">

        {{-- Controller Applications --}}
        <div style="margin-top:2rem;">
            <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">
                Controller Applications
                @if(count($applications) > 0)
                    <span style="background:#122b44; color:#fff; font-size:0.68rem; padding:0.1rem 0.45rem; border-radius:999px; margin-left:0.35rem; font-weight:700;">{{ count($applications) }}</span>
                @endif
            </h6>
            @if(count($applications) === 0)
                <p style="color:#6c757d; font-size:0.875rem;">No applications yet.</p>
            @else
                <div style="border:1px solid #e9ecef; border-radius:0.5rem; overflow:hidden;">
                    <table class="table table-hover mb-0" style="font-size:0.875rem;">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.65rem 1rem;">Applicant</th>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.65rem 1rem;">Availability</th>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.65rem 1rem;">Comments</th>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.65rem 1rem; text-align:right; width:80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $a)
                                <tr style="border-bottom:1px solid #f1f3f5;">
                                    <td style="padding:0.65rem 1rem; vertical-align:middle;">
                                        <div style="font-weight:600; color:#122b44;">{{ $a->user->fullName('FLC') }}</div>
                                        <div style="font-size:0.78rem; color:#6c757d;">{{ $a->user->rating->getLongName() }} &nbsp;·&nbsp; {{ $a->user->division_name }}</div>
                                        <a href="mailto:{{ $a->user->email }}" style="font-size:0.75rem; color:#6c757d; text-decoration:none;">{{ $a->user->email }}</a>
                                    </td>
                                    <td style="padding:0.65rem 1rem; vertical-align:middle; color:#495057; font-size:0.82rem;">
                                        {{ $a->start_availability_timestamp }}<br>{{ $a->end_availability_timestamp }}
                                    </td>
                                    <td style="padding:0.65rem 1rem; vertical-align:middle; color:#495057; font-size:0.82rem;">
                                        {{ $a->comments ?: '—' }}
                                    </td>
                                    <td style="padding:0.65rem 1rem; vertical-align:middle; text-align:right; white-space:nowrap;">
                                        <a href="{{ route('events.admin.controllerapps.delete', [$event->slug, $a->user_id]) }}"
                                           onclick="return confirm('Remove this application?')"
                                           style="font-size:0.8rem; color:#dc3545; text-decoration:none; font-weight:500;">
                                            <i class="fas fa-times fa-xs mr-1"></i>Remove
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Delete modal --}}
<div class="modal fade" id="deleteEvent" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:0.5rem;">
            <div class="modal-header" style="border-bottom:1px solid #e9ecef;">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Delete event?</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="font-size:0.875rem; color:#495057;">
                <p class="mb-0">This will soft-delete <strong>{{ $event->name }}</strong>. It will no longer be publicly visible but remains in the database.</p>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e9ecef;">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                <a href="{{ route('events.admin.delete', $event->slug) }}" class="btn btn-danger btn-sm">Delete Event</a>
            </div>
        </div>
    </div>
</div>

{{-- Edit modal --}}
<div class="modal fade" id="editEvent" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius:0.5rem;">
            <div class="modal-header" style="border-bottom:1px solid #e9ecef;">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Edit Event</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('events.admin.edit.post', $event->slug) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding:1.5rem;">
                    @if($errors->editEventErrors->any())
                        <div class="alert" style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:0.375rem; color:#721c24; font-size:0.875rem; margin-bottom:1.25rem;">
                            <ul class="mb-0 pl-3">
                                @foreach($errors->editEventErrors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Event name</label>
                        <input type="text" name="name" value="{{ $event->name }}" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Start (UTC)</label>
                                <input type="text" name="start" value="{{ $event->start_timestamp }}" class="form-control" id="edit_event_start">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:0.85rem; font-weight:600; color:#343a40;">End (UTC)</label>
                                <input type="text" name="end" value="{{ $event->end_timestamp }}" class="form-control" id="edit_event_end">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Departure ICAO</label>
                                <input maxlength="4" type="text" name="departure_icao" value="{{ $event->departure_icao }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Arrival ICAO</label>
                                <input maxlength="4" type="text" name="arrival_icao" value="{{ $event->arrival_icao }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Description</label>
                        <textarea id="editContentMD" name="description" class="form-control">{{ $event->description }}</textarea>
                        <script>var simplemdeEdit = new SimpleMDE({ element: document.getElementById("editContentMD") });</script>
                    </div>
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Replace image</label>
                        @if($event->image_url)
                            <div style="margin-bottom:0.75rem;">
                                <img src="{{ $event->image_url }}" style="max-width:180px; border-radius:0.375rem; border:1px solid #e9ecef; display:block;">
                            </div>
                        @endif
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="image" id="editImageUpload" accept=".jpg,.jpeg,.png,.gif">
                                <label class="custom-file-label" for="editImageUpload">{{ $event->image_url ? 'Replace image' : 'Choose image' }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="openControllerApps" class="form-check-input" id="editOpenControllerApps" {{ $event->controller_applications_open ? 'checked' : '' }}>
                        <label class="form-check-label" for="editOpenControllerApps" style="font-size:0.875rem; font-weight:600;">Open controller applications</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e9ecef;">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background:#122b44; color:#fff; border-radius:0.375rem;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Create update modal --}}
<div class="modal fade" id="createUpdate" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius:0.5rem;">
            <div class="modal-header" style="border-bottom:1px solid #e9ecef;">
                <h5 class="modal-title font-weight-bold" style="color:#122b44;">Post Update</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="{{ route('events.admin.update.post', $event->slug) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding:1.5rem;">
                    @if($errors->createUpdateErrors->any())
                        <div class="alert" style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:0.375rem; color:#721c24; font-size:0.875rem; margin-bottom:1.25rem;">
                            <ul class="mb-0 pl-3">
                                @foreach($errors->createUpdateErrors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Title <span style="color:#dc3545;">*</span></label>
                        <input type="text" name="updateTitle" class="form-control" placeholder="e.g. Positions now confirmed">
                    </div>
                    <div class="form-group mb-0">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Content <span style="color:#dc3545;">*</span></label>
                        <textarea id="updateContent" name="updateContent" class="form-control"></textarea>
                        <script>var simplemdeUpdate = new SimpleMDE({ element: document.getElementById("updateContent") });</script>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e9ecef;">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background:#122b44; color:#fff; border-radius:0.375rem;">Post Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->editEventErrors->any())
    <script>$("#editEvent").modal('show');</script>
@endif
@if($errors->createUpdateErrors->any())
    <script>$("#createUpdate").modal('show');</script>
@endif

<script>
    flatpickr('#edit_event_start', { enableTime: true, noCalendar: false, dateFormat: "Y-m-d H:i", time_24hr: true });
    flatpickr('#edit_event_end',   { enableTime: true, noCalendar: false, dateFormat: "Y-m-d H:i", time_24hr: true });
    document.getElementById('editImageUpload').addEventListener('change', function() {
        this.nextElementSibling.textContent = this.files[0] ? this.files[0].name : 'Choose image';
    });
</script>
@endsection
