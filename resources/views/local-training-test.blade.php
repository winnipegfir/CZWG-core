@extends('layouts.master')

@section('title', 'Local Training Test — Winnipeg FIR')

@section('content')
<div class="container py-5" style="max-width:900px;">
    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div>
                    <div class="text-uppercase text-muted font-weight-bold" style="font-size:.7rem; letter-spacing:.08em;">Local Preview Only</div>
                    <h1 class="font-weight-bold mb-1" style="color:#122b44; font-size:1.8rem;">Training Booking Test</h1>
                    <p class="text-muted mb-0">Use these accounts to verify which availability each role can see and book.</p>
                </div>
                <a href="{{ route('local.training.test.setup') }}" class="btn btn-primary mt-3 mt-sm-0"><i class="fas fa-sync-alt mr-1"></i> Create / Reset Test Data</a>
            </div>

            <div class="alert alert-info" style="font-size:.85rem;">
                Run the setup first. It creates one instructor slot and one mentor slot for tomorrow in UTC. Running it again resets only these marked test slots.
            </div>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <p><a href="{{ route('local.login') }}" class="btn btn-outline-primary">Sign In as Administrator / Edit Sweatbox</a></p>
            <form method="POST" action="{{ route('local.training.test.rating') }}" class="border rounded p-3 mb-3">
                @csrf
                <label for="test-rating">Mentorable test student's current rating</label>
                @php $testRating = (int) optional(\App\Models\Users\User::find(9999996))->rating_id; @endphp
                <select id="test-rating" name="rating_id" class="form-control mb-2">
                    @foreach([2 => 'S1 — S3 mentor allowed', 3 => 'S2 — S3 mentor allowed', 4 => 'S3 — S3 mentor blocked', 5 => 'C1 — S3 mentor blocked'] as $value => $label)
                        <option value="{{ $value }}" {{ $testRating === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm" type="submit">Apply Test Rating</button>
                <small class="d-block mt-2">Run setup first. This changes only the mentorable test account and keeps it mentorable. Instructor availability remains allowed. Resetting test data returns it to S1 and clears the marked test bookings. Existing bookings are not cancelled by this control.</small>
            </form>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <h5 class="font-weight-bold">Assigned Student</h5>
                        <p class="text-muted" style="font-size:.85rem;">Should see and book the <strong>instructor slot only</strong>. The mentor slot must remain hidden.</p>
                        <a href="{{ route('local.login.student.assigned') }}" class="btn btn-outline-primary btn-sm">Sign In as Assigned Student</a>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <h5 class="font-weight-bold">Mentorable Student</h5>
                        <p class="text-muted" style="font-size:.85rem;">At S1/S2, both instructor and S3 mentor slots should appear. At S3/C1, only the instructor slot should appear. Set the rating above before signing in.</p>
                        <a href="{{ route('local.login.student.mentorable') }}" class="btn btn-outline-primary btn-sm">Sign In as Mentorable Student</a>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <h5 class="font-weight-bold">Instructor</h5>
                        <p class="text-muted" style="font-size:.85rem;">View the instructor slot, confirm student bookings, and inspect on-site notifications.</p>
                        <a href="{{ route('local.login.instructor') }}" class="btn btn-outline-secondary btn-sm">Sign In as Instructor</a>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <h5 class="font-weight-bold">Mentor</h5>
                        <p class="text-muted" style="font-size:.85rem;">This S3 mentor can assist lower-rated, mentorable students. View the slot and confirm a booking.</p>
                        <a href="{{ route('local.login.mentor') }}" class="btn btn-outline-secondary btn-sm">Sign In as Mentor</a>
                    </div>
                </div>
            </div>

            <p class="text-muted mb-0" style="font-size:.78rem;">These shortcuts are unavailable outside the local environment and are not included in production routes.</p>
        </div>
    </div>
</div>
@endsection
