@extends('layouts.master')

@section('content')
    <div class="container py-4">
        <h1 class="font-weight-bold blue-text">Submit Feedback</h1>
        <p style="font-size: 1.2em;">
            Have feedback for the Winnipeg FIR? This is the place to submit it!
        </p>
        <hr>
        @if($errors->createFeedbackErrors->any())
            <div class="alert alert-danger">
                <h4>Error</h4>
                <ul class="pl-0 ml-0" style="list-style:none;">
                    @foreach ($errors->createFeedbackErrors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{route('feedback.create.post')}}" method="POST">
            @csrf
            <ul class="mt-0 pt-0 pl-0 stepper stepper-vertical">
                <li class="active">
                    <a href="#!">
                        <span class="circle">1</span>
                        <span class="label">Type of feedback</span>
                    </a>
                    <div class="step-content w-75 grey lighten-3">
                        <p>Please select the type of feedback you are submitting.</p>
                        <select name="feedbackType" id="feedbackTypeSelect" class="form-control">
                            <option value="0" hidden>Please select one...</option>
                            <option value="controller">Controller Feedback</option>
                            <option value="website">Website Feedback</option>
                        </select>
                    </div>
                </li>
                <li class="active">
                    <a href="#!">
                        <span class="circle">2</span>
                        <span class="label">Your message</span>
                    </a>
                    <div id="typeNotSelected" class="step-content w-75 grey lighten-3">
                        Please select a feedback type first!
                    </div>
                    <div id="typeSelected" class="step-content w-75 grey lighten-3" style="display:none">
                        <div class="md-form" id="controllerCidGroup" style="display:none">
                            <div>
                                <p>Controller's Name/CID</p>
                            </div>
                            <select name="controllerCid" class="form-control">
                                <option id="0" value="0" hidden>Select a controller...</option>
                            @foreach($controllers as $c)
                            <option name="controllerName" value={{$c->cid}} id={{$c->cid}}>
                                @if($c->user->fullName('FL') == $c->cid)
                                    {{$c->cid}}
                                @else
                                {{$c->user->fullName('FL')}} - {{$c->cid}}
                                @endif</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="md-form" id="positionGroup" style="display:none">
                            <p>Position</p>
                            <input type="name" name="position" class="form-control" placeholder="CZWG_CTR">
                        </div>
                        <div class="md-form" id="subjectGroup" style="display:none">
                            <p>Subject</p>
                            <input type="text" name="subject" class="form-control">
                        </div>
                        <div id="contentGroup">
                            <p>Your Feedback</p>
                            <textarea class="form-control" name="content" class="w-75"></textarea>
                        </div>
                    </div>
                </li>
            </ul>
            <button class="btn btn-success" style="font-size: 1.1em; font-weight: 600;"><i class="fas fa-check"></i>&nbsp;&nbsp;Submit Feedback</button>
        </form>
    </div>
    <script>
        /*
        Show/hide message form bsaed on whether the user has selected a feedback type
        */
        $("#feedbackTypeSelect").on('change', function() {
            if (this.value) {
                $("#typeNotSelected").hide();
                $("#typeSelected").show();
            }
        })

        /*
        Feedback type select to disable/enable the CID field and subject field
         */
        $('#feedbackTypeSelect').on('change', function() {
            //Figure out what it is
            if (this.value == 'controller') {
                //Enable CID disable subject
                $("#controllerCidGroup").show();
                $("#positionGroup").show();
                $("#subjectGroup").hide();
            } else {
                //Maybe not
                $("#controllerCidGroup").hide();
                $("#positionGroup").hide()
                $("#subjectGroup").show();
            }
        })
    </script>
@endsection
