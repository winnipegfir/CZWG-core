@extends('layouts.master')

@section('title', 'Create Event - Winnipeg FIR')

@section('content')
<div style="background:#fff; min-height:calc(100vh - 60px); padding:2.5rem 0;">
    <div class="container">

        <div class="mb-2">
            <a href="{{ route('events.admin.index') }}" style="font-size:0.82rem; color:#6c757d; text-decoration:none;">
                <i class="fas fa-arrow-left mr-1"></i> Events
            </a>
            <h1 class="font-weight-bold mb-0 mt-1" style="color:#122b44;">Create Event</h1>
        </div>
        <hr>

        @if($errors->createEventErrors->any())
            <div class="alert" style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:0.375rem; color:#721c24; font-size:0.875rem; margin-bottom:1.5rem;">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1 pl-3">
                    @foreach($errors->createEventErrors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('events.admin.create.post') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom:2rem;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Primary Information</h6>

                <div class="form-group">
                    <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Event name <span style="color:#dc3545;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="e.g. Fly-In Friday: CYWG Spotlight">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Start date & time (UTC) <span style="color:#dc3545;">*</span></label>
                            <input type="text" name="start" value="{{ old('start') }}" class="form-control" id="event_start" placeholder="YYYY-MM-DD HH:MM">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">End date & time (UTC) <span style="color:#dc3545;">*</span></label>
                            <input type="text" name="end" value="{{ old('end') }}" class="form-control" id="event_end" placeholder="YYYY-MM-DD HH:MM">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Departure ICAO <span style="color:#6c757d; font-weight:400;">(optional)</span></label>
                            <input maxlength="4" type="text" name="departure_icao" value="{{ old('departure_icao') }}" class="form-control" placeholder="CYYC">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Arrival ICAO <span style="color:#6c757d; font-weight:400;">(optional)</span></label>
                            <input maxlength="4" type="text" name="arrival_icao" value="{{ old('arrival_icao') }}" class="form-control" placeholder="CYWG">
                        </div>
                    </div>
                </div>

                <script>
                    flatpickr('#event_start', { enableTime: true, noCalendar: false, dateFormat: "Y-m-d H:i", time_24hr: true });
                    flatpickr('#event_end',   { enableTime: true, noCalendar: false, dateFormat: "Y-m-d H:i", time_24hr: true });
                </script>
            </div>

            <hr style="border-color:#e9ecef;">

            <div style="margin-bottom:2rem; margin-top:2rem;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Description <span style="color:#dc3545;">*</span></h6>
                <textarea id="contentMD" name="description" class="form-control">{{ old('description') }}</textarea>
                <script>
                    var simplemde = new SimpleMDE({ element: document.getElementById("contentMD") });
                </script>
            </div>

            <hr style="border-color:#e9ecef;">

            <div style="margin-bottom:2rem; margin-top:2rem;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:0.5rem;">Event Image</h6>
                <p style="font-size:0.82rem; color:#6c757d; margin-bottom:1rem;">No text or logos. PNG, JPG, or GIF.</p>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image" id="imageUpload" accept=".jpg,.jpeg,.png,.gif">
                        <label class="custom-file-label" for="imageUpload">Choose image</label>
                    </div>
                </div>
            </div>

            <hr style="border-color:#e9ecef;">

            <div style="margin-bottom:2rem; margin-top:2rem;">
                <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Options</h6>
                <div class="form-check">
                    <input type="checkbox" name="openControllerApps" class="form-check-input" id="openControllerApps">
                    <label class="form-check-label" for="openControllerApps" style="font-size:0.875rem; font-weight:600;">Open controller applications</label>
                    <div style="font-size:0.78rem; color:#6c757d; margin-top:0.1rem;">Allow rostered controllers to apply for positions at this event.</div>
                </div>
            </div>

            <hr style="border-color:#e9ecef; margin-top:1.5rem;">

            <div class="d-flex align-items-center mt-3">
                <button type="submit" class="btn" style="background:#122b44; color:#fff; border-radius:0.375rem; font-size:0.875rem; padding:0.5rem 1.5rem;">
                    Create Event
                </button>
                <a href="{{ route('events.admin.index') }}" style="font-size:0.875rem; color:#6c757d; text-decoration:none; margin-left:1rem;">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
    document.getElementById('imageUpload').addEventListener('change', function() {
        this.nextElementSibling.textContent = this.files[0] ? this.files[0].name : 'Choose image';
    });
</script>
@endsection
