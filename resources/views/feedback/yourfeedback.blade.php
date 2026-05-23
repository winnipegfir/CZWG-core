@extends('layouts.master')
@section('title', 'Your Feedback - Winnipeg FIR')
@section('description', 'View the feedback that the VATSIM community has submitted to us')
@section('content')

<div style="background:#fff; min-height: calc(100vh - 60px); padding: 2.5rem 0;">
    <div class="container">

        <div class="mb-4">
            <h1 class="font-weight-bold" style="color:#122b44;">Your Feedback</h1>
            <p style="color:#6c757d; margin-bottom:0;">
                A sample of the great feedback we've received from the thousands of VATSIM pilots who've flown through Winnipeg FIR.
            </p>
        </div>
        <hr>

        @if(count($feedback) == 0)
            <div class="card" style="border:1px solid #e9ecef;">
                <div class="card-body text-center py-5">
                    <i class="fas fa-comment-slash fa-2x mb-3" style="color:#ced4da;"></i>
                    <p style="color:#adb5bd; margin:0;">No approved feedback yet.</p>
                </div>
            </div>
        @endif

        <div class="row">
            @foreach($feedback as $f)
                @php $controller = User::where('id', $f->controller_cid)->first(); @endphp
                <div class="col-md-6 mb-4">
                    <div class="h-100" style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:0.5rem; padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                        <div style="flex:1;">
                            <i class="fas fa-quote-left fa-lg mb-2" style="color:#ced4da;"></i>
                            <p style="color:#343a40; font-size:0.97rem; line-height:1.65; margin:0;">{{ $f->content }}</p>
                        </div>
                        <div style="border-top:1px solid #e9ecef; padding-top:0.85rem; display:flex; align-items:center; gap:0.6rem;">
                            <i class="fas fa-user-circle" style="color:#adb5bd; font-size:1.4rem;"></i>
                            <div>
                                <div style="color:#122b44; font-weight:600; font-size:0.9rem;">
                                    {{ $controller ? $controller->fullName('FL') : 'Unknown Controller' }}
                                </div>
                                @if($f->position)
                                    <div style="color:#6c757d; font-size:0.78rem;">{{ $f->position }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <p style="color:#6c757d; font-size:0.92rem; margin-top:0.5rem;">
            Feedback is something we've always valued here at Winnipeg FIR. Your suggestions, criticisms, and kind words make us better controllers.
            Haven't flown through recently? <a href="/feedback" style="color:#122b44;">Submit your feedback here.</a>
        </p>

    </div>
</div>

@endsection
