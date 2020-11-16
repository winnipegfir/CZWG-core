@extends('layouts.master')
@section('content')
<div class="container py-4">
    <a href="{{route('events.admin.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Events</a>
    <h1 class="font-weight-bold blue-text">Managing: "{{$event->name}}"</h1>
    <hr>
    <div class="row">
        <div class="col-md-3">
          <h4 class="font-weight-bold blue-text">Actions</h4>
          <ul class="list-unstyled mt-3 mb-0" style="font-size: 1.05em;">
              <li class="mb-2">
                  <a href="" data-toggle="modal" data-target="#editEvent" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">Edit event details</span></a>
              </li>
              <li class="mb-2">
                  <a href="" data-toggle="modal" data-target="#createUpdate" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">Create update</span></a>
              </li>
              <li class="mb-2">
                  <a href="" data-toggle="modal" data-target="#confirmController" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">Add Controller to Event Roster</span></a>
              </li>
               <li class="mb-2">
                  <a href="{{route('event.viewapplications', [$event->id]) }}" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">View Controller Applications</span></a>
              </li>
              <li class="mb-2">
                  <a href="" data-toggle="modal" data-target="#deleteEvent" style="text-decoration:none;"><span class="red-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">Delete event</span></a>
              </li>
          </ul>
                {{-- <li class="mb-2">
                    <a href="" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="text-body">Export controller applications</span></a>
                </li> --}}

            </ul>
        </div>
        <div class="col-md-9">
          <h4 class="font-weight-bold blue-text">Details</h4>
          <div class="row">
              <div class="col-md-6">
                  <table class="table table-borderless table-striped">
                      <tbody>
                          <tr>
                              <td>Start Time</td>
                              <td>{{$event->start_timestamp_pretty()}}</td>
                          </tr>
                          <tr>
                              <td>End Time</td>
                              <td>{{$event->end_timestamp_pretty()}}</td>
                          </tr>
                          <tr>
                              <td>Departure Airport</td>
                              <td>{{$event->departure_icao}}</td>
                          </tr>
                          <tr>
                              <td>Arrival Airport</td>
                              <td>{{$event->arrival_icao}}</td>
                          </tr>
                      </tbody>
                  </table>
              </div>
              <div class="col-md-6">
                  @if ($event->image_url)
                  <img src="{{$event->image_url}}" alt="" class="img-fluid w-50 img-thumbnail">
                  @else
                  No image.
                  @endif
              </div>
          </div>
            <hr>
          <h4 class="font-weight-bold blue-text">Description</h4>
            {{$event->html()}}<hr>
          <h4 class="font-weight-bold blue-text">Updates</h4>
            @if (count($updates) == 0)
                None yet!
            @else
                @foreach($updates as $u)
                    <div class="card p-3">
                        <h4>{{$u->title}}</h4>
                        <div class="d-flex flex-row align-items-center">
                            <i class="far fa-clock"></i>&nbsp;&nbsp;Created {{$u->created_pretty()}}</span>&nbsp;&nbsp;•&nbsp;&nbsp;<i class="far fa-user-circle"></i>&nbsp;&nbsp;{{$u->author_pretty()}}&nbsp;&nbsp;•&nbsp;&nbsp;<a href="{{route('events.admin.update.delete', [$event->slug, $u->id])}}" class="red-text">Delete</a>
                        </div>
                        <hr>
                        {{$u->html()}}
                    </div>
                    <br>
                @endforeach
            @endif<hr>
           <h4 class="font-weight-bold blue-text mt-3">Event Roster</h4>
            @if (count($eventroster) < 1)
                Nobody is confirmed to control yet!
            @else
                    <div class="card p-3">
                    @foreach($positions as $position)
                        <h5>{{$position->position}}</h5>
                        @foreach($eventroster as $roster)
                            @if($roster->position == $position->position && $roster->position != "Relief")
                            <form method="POST" action="{{route('event.deletecontroller', [$roster->user_id])}}">
                                <text class="font-weight-bold">{{$roster->user->fullName('FLC')}}</text> is controlling {{$roster->airport}} {{$roster->position}} from {{$roster->start_timestamp}}z to {{$roster->end_timestamp}}z. <a target="_parent"><button class="btn btn-sm btn-danger" type="submit">Delete</button></a><br>
                            <input type="hidden" name="id" value="{{$roster->event_id}}"></input>
                            @csrf
                            </form>
                            @endif
                            @if($roster->position == "Relief" && $position->position == "Relief")
                            <form method="POST" action="{{route('event.deletecontroller', [$roster->user_id])}}">
                                <text class="font-weight-bold">{{$roster->user->fullName('FLC')}}</text> is on Stand-by available from {{$roster->start_timestamp}}z to {{$roster->end_timestamp}}z. <input class="btn btn-sm btn-danger" type="submit" value="Delete" style="color: red"><br>
                                @csrf
                            </form>
                            @endif
                        @endforeach
                    @endforeach
                  </div>
            @endif
        </div>
    </div>
</div>

<!--Delete event modal-->
<div class="modal fade" id="deleteEvent" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Are you sure?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>This will soft delete the event, so it still exists in the database but cannot be viewed. Have a funny GIF too.</p>
                <img src="https://tenor.com/view/bartsimpson-boot-simpsons-thesimpsons-homer-gif-9148667.gif" alt="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                <a href="{{route('events.admin.delete', $event->slug)}}" role="button" class="btn btn-danger">Delete Event</a>
            </div>
            </form>
        </div>
    </div>
</div>
<!--End delete event modal-->

<!--Edit event modal-->
<div class="modal fade" id="editEvent" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Edit {{$event->name}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{route('events.admin.edit.post', $event->slug)}}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($errors->editEventErrors->any())
                    <div class="alert alert-danger">
                        <h4>There were errors editing the event</h4>
                        <ul class="pl-0 ml-0 list-unstyled">
                            @foreach ($errors->editEventErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <ul class="stepper mt-0 p-0 stepper-vertical">
                        <li class="active">
                            <a href="#!">
                                <span class="circle">1</span>
                                <span class="label">Primary information</span>
                            </a>
                            <div class="step-content w-75 pt-0">
                                <div class="form-group">
                                    <label for="">Event name</label>
                                    <input type="text" name="name" id="" class="form-control" value="{{$event->name}}">
                                </div>
                                <div class="form-group">
                                    <label for="">Start date and time</label>
                                    <input type="datetime" name="start" value="{{$event->start_timestamp}}" placeholder="Put event start date/time here" class="form-control flatpickr" id="event_start">
                                </div>
                                <div class="form-group">
                                    <label for="">End date and time</label>
                                    <input type="datetime" name="end" value="{{$event->end_timestamp}}" placeholder="Put event end date/time here" class="form-control flatpickr" id="event_end">
                                </div>
                                <div class="form-group">
                                    <label for="">Departure airport ICAO (optional)</label>
                                    <input maxlength="4" type="text" value="{{$event->departure_icao}}" name="departure_icao" id="" class="form-control" placeholder="CYYC">
                                </div>
                                <div class="form-group">
                                    <label for="">Arrival airport ICAO (optional)</label>
                                    <input maxlength="4" type="text" value="{{$event->arrival_icao}}" name="arrival_icao" id="" class="form-control" placeholder="EIDW">
                                </div>
                                <script>
                                    flatpickr('#event_start', {
                                        enableTime: true,
                                        noCalendar: false,
                                        dateFormat: "Y-m-d H:i",
                                        time_24hr: true,
                                    });
                                    flatpickr('#event_end', {
                                        enableTime: true,
                                        noCalendar: false,
                                        dateFormat: "Y-m-d H:i",
                                        time_24hr: true,
                                    });
                                </script>
                            </div>
                        </li>
                        <li class="active">
                            <a href="#!">
                                <span class="circle">2</span>
                                <span class="label">Description</span>
                            </a>
                            <div class="step-content w-75 pt-0">
                                <div class="form-group">
                                    <label for="">Use Markdown</label>
                                    <textarea id="contentMD" name="description" class="w-75">{{$event->description}}</textarea>
                                    <script>
                                        var simplemde = new SimpleMDE({ element: document.getElementById("contentMD"), toolbar: false });
                                    </script>
                                </div>
                            </div>
                        </li>
                        <li class="active">
                            <a href="#!">
                                <span class="circle">3</span>
                                <span class="label">Image</span>
                            </a>
                            <div class="step-content w-75 pt-0">
                                @if ($event->image_url)
                                <img src="{{$event->image_url}}" alt="" class="img-fluid w-50 img-thumbnail">
                                @else
                                No image.
                                @endif
                                <p>An image can be displayed for the event. Please ensure we have the right to use the image, and that it is of an acceptable resolution. Make sure the image has no text or logos on it.</p>
                                <div class="input-group pb-3">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image">
                                        <label class="custom-file-label">Choose image</label>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="active">
                            <a href="#!">
                                <span class="circle">4</span>
                                <span class="label">Options</span>
                            </a>
                            <div class="step-content w-75 pt-0">
                                <div class="form-group">
                                    <div class="mr-2">
                                        <input type="checkbox" class="" name="openControllerApps" id="openControllerApps" {{ $event->controller_applications_open == "1" ? "checked=checked" : ""}}>
                                        <label class="" for="">Open controller applications</label>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->editEventErrors->any())
<script>
    $("#editEvent").modal('show');
</script>
@endif

<!--End edit event modal-->

<!--create update modal-->
<div class="modal fade" id="createUpdate" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Create event update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{route('events.admin.update.post', $event->slug)}}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($errors->createUpdateErrors->any())
                    <div class="alert alert-danger">
                        <ul class="pl-0 ml-0 list-unstyled">
                            @foreach ($errors->createUpdateErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="">Title</label>
                        <input type="text" name="updateTitle" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Use Markdown</label>
                        <textarea id="updateContent" name="updateContent"></textarea>
                        <script>
                            var simplemde = new SimpleMDE({ element: document.getElementById("updateContent"), toolbar: false });
                        </script>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <button type="submit" class="btn btn-success">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@if($errors->createUpdateErrors->any())
<script>
    $("#createUpdate").modal('show');
</script>
@endif

<!--End app update modal-->
<!--Add Confirmed controller modal-->
<div class="modal fade" id="confirmController" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Controller to Event {{$event->name}}:</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

              <div align="center" class="modal-body">
  <form id="app-form" method="POST" action="{{ route('event.addcontroller', [$event->id] )}}">
                    <div class="form-group row">
                        <label for="dropdown" class="col-sm-4 col-form-label text-md-right">Pick a controller.</label>

                        <select class="custom-select" name="user_cid">
                          @foreach($users as $user)
                          <option value="{{ $user->id}}">{{$user->id}} - {{$user->fname}} {{$user->lname}}</option>
                          @endforeach
                        </select>

                        <div class="col-md-12">

                            <td align="center">
                              <input type="hidden" name="event_id" value="{{$event->id}}">
                              <input type="hidden" name="event_name" value="{{$event->name}}">
                              <input type="hidden" name="event_date" value="{{$event->start_timestamp}}">
                              <label for="">Start Time (zulu)</label>
                              <input type="datetime" name="start_timestamp" class="form-control flatpickr" value="" id="start_timestamp">
                              <label class="mt-2" for="">End Time (zulu)</label>
                              <input type="datetime" name="end_timestamp" class="form-control flatpickr" value="" id="end_timestamp">
                              <label class="mt-2" for="">Airport (ex. CYWG)</label>
                              <input type="text" name="airport" class="form-control" id="airport">
                              <label class="mt-2" for="">Position</label>
                              <select name="position" class="form-control" id="position">
                                <option value="Delivery">Delivery</option>
                                <option value="Ground">Ground</option>
                                <option value="Tower">Tower</option>
                                <option value="Departure">Departure</option>
                                <option value="Arrival">Arrival</option>
                                <option value="Centre">Centre</option>
                                <option value="Relief">Relief</option>
                              </select>
                                  @csrf
                            </td>

                             <script>
                                 flatpickr('#start_timestamp', {
                                     enableTime: true,
                                     noCalendar: true,
                                     dateFormat: "H:i",
                                     time_24hr: true,
                                     defaultDate: "{{$event->flatpickr_limits()[0]}}"
                                 });
                                 flatpickr('#end_timestamp', {
                                     enableTime: true,
                                     noCalendar: true,
                                     dateFormat: "H:i",
                                     time_24hr: true,
                                     defaultDate: "{{$event->flatpickr_limits()[1]}}"
                                 });
                             </script>
                        </div>
                    </div>
            </div>

            <div align="center" class="modal-footer">
              <div align="center"><button type="submit" class="btn btn-success">Confirm Controller</button></div>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button></form>
            </div>
        </div>
    </div>
</div>

<!--End confirmed controller modal-->
