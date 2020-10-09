@extends('layouts.master')

@section('title', $event->name.' - Winnipeg FIR')
@section('description', 'View the upcoming event: '.$event->name)
@if ($event->departure_icao && $event->arrival_icao) {{$event->departure_icao_data()['name']}} ({{$event->departure_icao}}) to {{$event->arrival_icao_data()['name']}} ({{$event->arrival_icao}}). @endif Starting {{$event->start_timestamp_pretty()}}
@endsection
@if($event->image_url)
@section('image')
{{$event->image_url}}
@endsection
@endif

@section('content')


      <div class="text-white text-left py-1 px-4" style="background-color:#013162">
          <div class="container">
              <div align="center" class="py-5">
                  <h1 align="center" class="h1" style="font-size: 4em;">{{$event->name}}</h1>
                  <h4>{{$event->start_timestamp_pretty()}} - {{$event->end_timestamp_pretty()}}</h4>
                  @if ($event->departure_icao && $event->arrival_icao)
                  <h3>{{$event->departure_icao_data()['name']}} ({{$event->departure_icao}})&nbsp;&nbsp;<i class="fas fa-plane"></i>&nbsp;&nbsp;{{$event->arrival_icao_data()['name']}} ({{$event->arrival_icao}})</h3>
                  @endif
              </div>
          </div>
      </div>

      <div class="container py-4">
        @if ($event->image_url != null)
      <img src="{{$event->image_url}}" alt="" title="" width="100%" height="50%">

      @else
      &nbsp
      @endif

      </div>
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                <h4>Share This</h4>
                <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u{{Request::url()}}"><i class="fab blue-text fa-facebook fa-3x"></i></a>
                &nbsp;
                <a target="_blank" href="https://twitter.com/intent/tweet?text={{$event->name}} - Winnipeg FIR VATSIM {{Request::url()}}"><i class="fab blue-text fa-twitter fa-3x"></i></a>
                &nbsp;
                <a target="_blank" href="http://www.reddit.com/submit?url={{Request::url()}}&title={{$event->name}} - Winnipeg FIR VATSIM"><i class="fab blue-text fa-reddit fa-3x"></i></a>
                <hr>
                <h4 class="mt-2">Start Time</h4>
                <p>{{$event->start_timestamp_pretty()}}</p>
                <hr>
                <h4>End Time</h4>
                <p>{{$event->end_timestamp_pretty()}}</p>
                <hr>
                @if (!$event->departure_icao)
                @else
                <h4>Departure Airport</h4>
                <ul class="list-unstyled">
                    <li>{{$event->departure_icao_data()['name']}}</li>
                    <li>{{$event->departure_icao}}</li>
                </ul>

                <hr>
                @endif
                @if (!$event->arrival_icao)
                @else
                <h4>Arrival Airport</h4>
                <ul class="list-unstyled">
                    <li>{{$event->arrival_icao_data()['name']}}</li>
                    <li>{{$event->arrival_icao}}</li>

                </ul>
                @endif
            </div>
            <div class="col-md-9">
                {{$event->html()}}
                @if($event->start_timestamp > $timeNow)
                @if (Auth::check() && $event->controller_applications_open && Auth::user()->rosterProfile)
                <hr>
                <h3>Apply to Control</h3>
                @if (Auth::check() && $event->userHasApplied())
                    <h5 class="font-weight-bold">You have already applied for this event. Check your <a href="{{route('dashboard.index')}}">dashboard</a> for more info regarding your application!</h5>
                @endif
                @if(Auth::check() && !$event->userHasApplied())
                <br>
                <div class="card p-3">
                    <form id="app-form" method="POST" action="{{route('events.controllerapplication.ajax')}}">
                        @csrf
                        <input type="hidden" name="event_id" value="{{$event->id}}">
                        <input type="hidden" name="event_name" value="{{$event->name}}">
                        <input type="hidden" name="event_date" value="{{$event->start_timestamp}}">
                        <p>Submit an application to the Events Coordinator to control during this event through this form.</p>
                        <label for="">Availability start time (zulu)</label>
                        <input type="datetime" name="availability_start" class="form-control flatpickr" id="availability_start">
                        <label class="mt-2" for="">Availability end time (zulu)</label>
                        <input type="datetime" name="availability_end" class="form-control flatpickr" id="availability_end">
                        <label class="mt-2" for="">Position Requested</label>
                        <select name="position" class="form-control" id="position">
                          @if(Auth::user()->rating_id > 1)
                          <option value="Delivery">Delivery</option>
                          <option value="Ground">Ground</option>
                          <option value="Tower">Tower</option>
                          @endif
                          @if(Auth::user()->rating_id > 3)
                          <option value="Departure">Departure</option>
                          <option value="Arrival">Arrival</option>
                          @endif
                          @if(Auth::user()->rating_id > 4)
                          <option value="Centre">Centre</option>
                          @endif
                        </select>
                        <label for="" class="mt-2">Comments</label>
                        <textarea name="comments" id="comments" rows="2" class="md-textarea form-control"></textarea>
                        <input type="submit" id="app-form-submit" class="btn btn-outline-submit mt-3" value="Submit">
                    </form>
                    <script>
                        flatpickr('#availability_start', {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: "H:i",
                            time_24hr: true,
                            defaultDate: "{{$event->flatpickr_limits()[0]}}"
                        });
                        flatpickr('#availability_end', {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: "H:i",
                            time_24hr: true,
                            defaultDate: "{{$event->flatpickr_limits()[1]}}"
                        });
                    </script>
                </div>
                @endif
                @endif
                @endif


                <hr>
                @if (count($updates) == 0)
                @elseif (count($updates) >0 )
                    <h4 class="font-weight-bold blue-text">Updates</h4>
                        @foreach($updates as $u)
                            <div class="card p-3">
                                <a href="{{Request::url()}}#{{$u->slug}}" name={{$u->slug}}> <h4>{{$u->title}}</h4></a>
                                    <div class="d-flex flex-row align-items-center">
                                        <i class="far fa-clock"></i>&nbsp;&nbsp;Posted {{$u->created_pretty()}}</span>&nbsp;&nbsp;•&nbsp;&nbsp;<i class="far fa-user-circle"></i>&nbsp;&nbsp;{{$u->author_pretty()}}
                                    </div>
                                <hr>
                                {{$u->html()}}
                            </div>
                        <br>
                @endforeach
                @endif
            </div>
        </div>
    </div>
@stop
