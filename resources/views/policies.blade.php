@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('title', 'Policies')
@section('description', 'Policies and Guidelines from the Winnipeg FIR')

@section('content')
    <div class="container" style="margin-top: 20px;">
        <h1 class="font-weight-bold blue-text">Policies</h1>

        @if (Auth::check() && Auth::user()->permissions >= 4)
            <hr>
            <div class="card w-75">
                <div class="card-body">
                    <h5 class="card-title font-weight-bold">Policy Admin</h5>
                    <div class="row" style="padding-left: 8px">
                        <a href="#" data-toggle="modal" data-target="#addPolicyModal" class="btn btn-primary">Add New Policy</a>
                        <a href="#" data-toggle="modal" data-target="#addPolicySectionModal" class="btn btn-light">Add New Policy Section</a>
                        <a href="#" data-toggle="modal" data-target="#deletePolicySectionModal" class="btn btn-danger">Delete Policy Sections</a>
                    </div>
                </div>
            </div>
        @endif
        @foreach($policySections as $s)
            <hr>
            <h5 class="font-weight-bold">{{$s->section_name}}</h5>
            @foreach ($s->policies as $policy)
                <div id="accordion">
                    <div aria-expanded="true"  class="card" style="background-color: lightgray">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#policy{{$policy->id}}" aria-expanded="true" aria-controls="policy{{$policy->id}}">
                                    {{ $policy->name }}
                                    @if($policy->staff_only == 1)
                                        - <b class="text-danger">This is a private staff-only policy.</b>
                                    @endif
                                </button>
                            </h5>
                        </div>
                        <div id="policy{{$policy->id}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body">
                                @if (Auth::check() && Auth::user()->permissions >= 4)
                                    <div class="row" style="padding: 10px;">
                                        <a href="#" data-toggle="modal" data-target="#editPolicyModal{{$policy->id}}" class="btn btn-primary">Edit Policy</a>
                                        <a href="{{url('/policies/'.$policy->id.'/delete')}}" class="btn btn-danger">Delete Policy</a>
                                    </div>
                                    <br>
                                @endif
                                <b>Effective Date: {{$policy->releaseDate }}</b>
                                <p>{{$policy->details}}</p>
                                <a target="_blank" href="{{$policy->link}}">Download the .PDF file HERE.</a>
                                @if ($policy->embed == 1)
                                    <iframe width="100%" style="height: 600px; border: none;" src="{{$policy->link}}"></iframe>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            @endforeach
            <br>
        @endforeach
        @auth
            @if(Auth::user()->permissions >= 4)
                <hr>
                <h5 class="font-weight-bold text-danger" >Policies without a section, staff only view</h5>
                @if(count($nullPolicies) >= 1)
                    @foreach($nullPolicies as $policy)
                        <div id="accordion">
                            <div aria-expanded="true"  class="card">
                                <div class="card-header" id="headingOne">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#policy{{$policy->id}}" aria-expanded="true" aria-controls="policy{{$policy->id}}">
                                            {{ $policy->name }}
                                            @if($policy->staff_only == 1)
                                                - <b class="text-danger">This is a private staff-only policy.</b>
                                            @endif
                                        </button>
                                    </h5>
                                </div>
                                <div id="policy{{$policy->id}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body">
                                            <div class="border" style="padding: 10px;">
                                                <a href="#" data-toggle="modal" data-target="#editPolicyModal{{$policy->id}}" class="btn btn-primary">Edit Policy</a>
                                                <a href="{{url('/policies/'.$policy->id.'/delete')}}" class="btn btn-danger">Delete Policy</a>
                                            </div>
                                            <br>
                                        <b>Effective Date: {{$policy->releaseDate }}</b>
                                        <p>{{$policy->details}}</p>
                                        <a target="_blank" href="{{$policy->link}}">Download the .PDF file HERE.</a>
                                        @if ($policy->embed == 1)
                                            <iframe width="100%" style="height: 600px; border: none;" src="{{$policy->link}}"></iframe>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                    @endforeach
                @else
                    <h6 class="font-weight-bold">🎉 Congratulations! 🎉 There are no policies without a section.</h6>
                    <br>
                @endif
            @endif
        @endauth
        <br>


        <div class="modal fade" id="addPolicyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">New Policy</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                      <form method="POST" action="{{route('policies.create')}}" class="form-group">

                          <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Policy Name</label>
                            <input type="text" name="name" class="form-control"></input>
                          </div>
                          <div class="form-group">
                              <label for="recipient-name" class="col-form-label">Policy Section</label>
                              <select name="section" class="form-control">
                                  <option value="-1" hidden>Select a policy section...</option>
                                  @foreach($policySections as $p)
                                      <option value="{{$p->id}}">{{$p->id}} - {{$p->section_name}}</option>
                                  @endforeach
                              </select>
                          </div>
                          <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Details (Max 250 Char.)</label>
                            <textarea name="details" class="form-control"></textarea>
                          </div>
                          <div class="form-group">
                            <label for="recipient-name" class="col-form-label">URL</label>
                            <input type="text" name="link" class="form-control"></input>
                          </div>
                          <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Display Options</label>
                            <select name="embed" class="form-control">
                                <option value="0">Display</option>
                                <option value="1">Do Not Display</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Privacy</label>
                            <select name="staff_only" class="form-control">
                              <option value="0">Public</option>
                              <option value="1">Private to staff only</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Publishing Notification</label>
                            <select name="email" class="form-control">
                              <option value="none">Do Nothing (Default)</option>
                              <option value="all">Email ALL Users & Publish News Article</option>
                              <option value="emailcert">Email Certified Controllers and Publish as News Article</option>
                              <option value="newsonly">Only Publish as News Article</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Effective Date</label>
                            <input type="datetime" id="date" name="date" placeholder="Choose a Date" class="form-control flatpickr"></input>
                        </div>
                        <script>
                            flatpickr('#date', {
                                enableTime: false,
                                noCalendar: false,
                                dateFormat: "Y-m-d",
                                time_24hr: true,
                            });
                        </script>
                        @csrf
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn light" data-dismiss="modal">Cancel</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(Auth::check() && Auth::user()->permissions >= 4)
        @foreach($allPolicies as $p)
            <div class="modal fade" id="editPolicyModal{{$p->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Policy - {{$p->name}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{url('/policies/'.$p->id.'/edit')}}" class="form-group">

                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Policy Name</label>
                                    <input type="text" name="name" class="form-control" value="{{$p->name}}"></input>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Policy Section</label>
                                    <select name="section" class="form-control">
                                        <option value="-1" hidden>Select a policy section...</option>
                                        @foreach($policySections as $ps)
                                            <option value="{{$ps->id}}" {{$ps->id == $p->section_id ? "selected=selected" : ""}}>{{$ps->id}} - {{$ps->section_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Details (Max 250 Char.)</label>
                                    <textarea name="details" class="form-control">{{$p->details}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">URL</label>
                                    <input type="text" name="link" class="form-control" value="{{$p->link}}"></input>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Display Options</label>
                                    <select name="embed" class="form-control">
                                        <option value="1" {{$p->embed == 1 ? "selected=selected" : ""}}>Display</option>
                                        <option value="0" {{$p->embed == 0 ? "selected=selected" : ""}}>Do Not Display</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Privacy</label>
                                    <select name="staff_only" class="form-control">
                                        <option value="0" {{$p->staff_only == 0 ? "selected=selected" : ""}}>Public</option>
                                        <option value="1" {{$p->staff_only == 1 ? "selected=selected" : ""}}>Private to staff only</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Effective Date</label>
                                    <input type="datetime" id="date" name="date" placeholder="Choose a Date" class="form-control flatpickr"></input>
                                </div>
                                <script>
                                    flatpickr('#date', {
                                        enableTime: false,
                                        noCalendar: false,
                                        dateFormat: "Y-m-d",
                                        time_24hr: true,
                                    });
                                </script>
                            @csrf
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" class="btn light" data-dismiss="modal">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="modal fade" id="addPolicySectionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{route('policysection.create')}}">
                    @CSRF
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Create Policy Section
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="col-form-label">Policy Section Name</label>
                            <input type="text" name="name" class="form-control"></input>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn light" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deletePolicySectionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Delete Policy Sections
                    </h5>
                </div>
                <div class="modal-body">
                    @foreach($policySections as $s)
                        <div class="form-group">
                            <text class="font-weight-bold">{{$s->id}} - {{$s->section_name}}</text>
                            <a class="btn btn-sm btn-danger" href="{{url('/policies/section/'.$s->id.'/delete/')}}">Delete Section</a>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn light" data-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
@stop
