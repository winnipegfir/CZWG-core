@extends('layouts.master')

@section('content')
<div class="container py-4">
    <a href="{{ route('settings.index') }}" class="blue-text" style="font-size:1.2em;"><i class="fas fa-arrow-left"></i> Settings</a>
    <h1 class="blue-text font-weight-bold mt-2">Welcome Email</h1>
    <p class="text-muted">Manage the automatic email sent when a user accepts the privacy policy for the first time.</p>
    <hr>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('settings.welcome-email.save') }}">
        @csrf
        <div class="card mb-4">
            <div class="card-body">
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" class="custom-control-input" id="welcome_email_enabled" name="welcome_email_enabled" value="1" {{ old('welcome_email_enabled', $coreSettings->welcome_email_enabled ?? true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="welcome_email_enabled">Send this email on a user's first login</label>
                </div>

                <div class="form-group">
                    <label for="welcome_email_subject">Subject</label>
                    <input class="form-control" id="welcome_email_subject" name="welcome_email_subject" maxlength="180" required value="{{ old('welcome_email_subject', $coreSettings->welcome_email_subject ?? 'Welcome to Winnipeg, {first_name}!') }}">
                </div>

                <div class="form-group">
                    <label for="welcome_email_body">Message</label>
                    <textarea class="form-control" id="welcome_email_body" name="welcome_email_body" rows="8" required>{{ old('welcome_email_body', $coreSettings->welcome_email_body ?? "Welcome to the Winnipeg FIR, we're very excited that you're here!\n\nWelcome to Winnipeg! This is the home to all things Winnipeg - from controller files, to roster info, to training, contact info and more. Thanks for stopping by!") }}</textarea>
                </div>

                <div class="form-group mb-0">
                    <label for="welcome_email_signature">Signature</label>
                    <textarea class="form-control" id="welcome_email_signature" name="welcome_email_signature" rows="3" required>{{ old('welcome_email_signature', $coreSettings->welcome_email_signature ?? 'Winnipeg FIR Staff') }}</textarea>
                    <small class="form-text text-muted">Available placeholders: <code>{first_name}</code>, <code>{full_name}</code>, and <code>{cid}</code>. Plain text and line breaks are supported.</small>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save welcome email</button>
    </form>
</div>
@stop
