@extends('layouts.master')

@section('title', 'Settings - Winnipeg FIR')

@section('content')
<div style="background:#fff; min-height:calc(100vh - 60px); padding:2.5rem 0;">
    <div class="container">

        <div class="mb-2">
            <h1 class="font-weight-bold mb-0" style="color:#122b44;">Settings</h1>
        </div>
        <hr>

        @if($errors->any())
            <div class="alert" style="background:#f8d7da; border:1px solid #f5c6cb; border-radius:0.375rem; color:#721c24; font-size:0.875rem; margin-bottom:1.5rem;">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {!! Form::open(['route' => 'coresettings.store']) !!}

        {{-- Site banner --}}
        <div style="margin-bottom:2rem;">
            <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Site Banner</h6>
            <p style="font-size:0.82rem; color:#6c757d; margin-bottom:1rem;">Leave the text blank to hide the banner entirely.</p>

            <div class="form-group">
                <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Banner text</label>
                {!! Form::text('banner', $settings->banner, ['class' => 'form-control', 'placeholder' => 'e.g. Cross the Pond registration is now open!']) !!}
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Type</label>
                        {!! Form::select('bannerMode', [
                            'info'    => 'Info (blue)',
                            'success' => 'Success (green)',
                            'warning' => 'Warning (yellow)',
                            'danger'  => 'Danger (red)',
                        ], $settings->bannerMode, ['class' => 'form-control custom-select']) !!}
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Link URL <span style="color:#6c757d; font-weight:400;">(optional)</span></label>
                        {!! Form::text('bannerLink', $settings->bannerLink, ['class' => 'form-control', 'placeholder' => 'https://...']) !!}
                    </div>
                </div>
            </div>
        </div>

        <hr style="border-color:#e9ecef;">

        {{-- System info --}}
        <div style="margin-bottom:2rem; margin-top:2rem;">
            <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">System</h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">System name</label>
                        {!! Form::text('sys_name', $settings->sys_name, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Release</label>
                        {!! Form::text('release', $settings->release, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Build</label>
                        {!! Form::text('sys_build', $settings->sys_build, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group" style="max-width:200px;">
                <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Copyright year</label>
                {!! Form::text('copyright_year', $settings->copyright_year, ['class' => 'form-control']) !!}
            </div>
        </div>

        <hr style="border-color:#e9ecef;">

        {{-- Contact emails --}}
        <div style="margin-bottom:2rem; margin-top:2rem;">
            <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Contact Emails</h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">FIR Chief</label>
                        {!! Form::text('emailfirchief', $settings->emailfirchief, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Deputy FIR Chief</label>
                        {!! Form::text('emaildepfirchief', $settings->emaildepfirchief, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Chief Instructor</label>
                        {!! Form::text('emailcinstructor', $settings->emailcinstructor, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Events Coordinator</label>
                        {!! Form::text('emaileventc', $settings->emaileventc, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Facility Engineer</label>
                        {!! Form::text('emailfacilitye', $settings->emailfacilitye, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-size:0.85rem; font-weight:600; color:#343a40;">Webmaster</label>
                        {!! Form::text('emailwebmaster', $settings->emailwebmaster, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>

        <hr style="border-color:#e9ecef; margin-top:1.5rem;">

        <div class="mt-3">
            {!! Form::submit('Save Settings', ['class' => 'btn', 'style' => 'background:#122b44; color:#fff; border-radius:0.375rem; font-size:0.875rem; padding:0.5rem 1.5rem;']) !!}
        </div>

        {!! Form::close() !!}

        <hr style="border-color:#e9ecef; margin:2.5rem 0;">

        {{-- Maintenance mode IPs --}}
        <div style="margin-bottom:2rem;">
            <h6 style="color:#6c757d; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:1rem;">Maintenance Mode Exempt IPs</h6>

            @if(count($ips) > 0)
                <div style="border:1px solid #e9ecef; border-radius:0.5rem; overflow:hidden; margin-bottom:1rem;">
                    <table class="table table-hover mb-0" style="font-size:0.875rem;">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.6rem 1rem;">Label</th>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.6rem 1rem;">IP Address</th>
                                <th style="color:#495057; font-weight:600; border-bottom:1px solid #e9ecef; padding:0.6rem 1rem; text-align:right;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ips as $i)
                                <tr style="border-bottom:1px solid #f1f3f5;">
                                    <td style="padding:0.6rem 1rem; vertical-align:middle; color:#343a40;">{{ $i->label }}</td>
                                    <td style="padding:0.6rem 1rem; vertical-align:middle; color:#6c757d; font-family:monospace;">{{ $i->ipv4 }}</td>
                                    <td style="padding:0.6rem 1rem; vertical-align:middle; text-align:right;">
                                        <a href="{{ route('coresettings.exemptips.delete', $i->id) }}"
                                           onclick="return confirm('Remove this IP?')"
                                           style="font-size:0.8rem; color:#dc3545; font-weight:500; text-decoration:none;">
                                            <i class="fas fa-times fa-xs mr-1"></i>Remove
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color:#6c757d; font-size:0.875rem;">No exempt IPs configured.</p>
            @endif

            <form action="{{ route('coresettings.exemptips.add') }}" method="POST">
                @csrf
                <div class="form-row align-items-end" style="gap:0.5rem;">
                    <div class="col-md-4">
                        <label style="font-size:0.82rem; font-weight:600; color:#343a40;">Label</label>
                        <input type="text" name="label" class="form-control" placeholder="e.g. FIR Chief">
                    </div>
                    <div class="col-md-4">
                        <label style="font-size:0.82rem; font-weight:600; color:#343a40;">IP Address (IPv4)</label>
                        <input type="text" name="ipv4" class="form-control" placeholder="192.168.1.1">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn" style="background:#122b44; color:#fff; border-radius:0.375rem; font-size:0.875rem; padding:0.5rem 1rem;">Add IP</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
