@extends('layouts.dashboard')
@section('content')
@section('title', 'Your Dashboard')

<style>
.accordion {
  background-color: white;
  color: #013162;
  cursor: pointer;
  padding: 2%;
  width: 100%;
  border: none;
  text-align: left;
  outline: none !important;
  font-size: 12px;
  transition: 0.4s;
}

.accordion:hover {
  background-color: #272727;
  color: #fff;
}

.active {
  background-color: #013162;
  color: #fff;
}

.accordion:after {
  font-family: "Font Awesome 5 Free";
  content: '\f104';
  float: right;
  font-weight: 900;
}

.active:after {
  font-family: "Font Awesome 5 Free";
  content: "\f107";
  font-weight: 900;
}

.panel {
  background-color: white;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.1s ease-out;
}
</style>

<div style="background-color: #013162">
    <div class="container py-4">
        <h1 data-step="1"
            data-intro="Welcome to your Dashboard! This is your central hub for all things Winnipeg. Here you can interact with our FIR, and manage your account."
            class="font-weight-bold white-text">Your Dashboard</h1>
        <br class="my-2">
        <div class="row">
            <div class="col">
                @if (Auth::user()->permissions >= 1 | $certification == "training")
                    <div class="card">
                        <div class="card-body">
                            <h3 class="font-weight-bold blue-text pb-2">ATC Resources</h3>
                            @if(Auth::user()->permissions >= 4)
                                <a href="{{route('atcresources.index')}}">Manage Resources</a><br></br>
                            @endif
                            <div class="list-group" style="border-radius: 0.5em !important">
                                @foreach($atcResources as $resource)
                                    @if($resource->atc_only && Auth::user()->permissions < 1)
                                        @continue
                                    @else
                                        <a href="{{$resource->url}}" target="_new"
                                           class="list-group-item list-group-item-action">
                                            {{$resource->title}}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <br>
                <div data-step="2" data-intro="Here is where you manage and view the data we store on you and your Winnipeg FIR Profile." class="card ">
                    <div class="card-body pb-0">
                        <h3 class="font-weight-bold blue-text pb-2">Profile</h3>
                        <div class="row">
                            <div class="col" data-step="3" data-intro="Here is an overview of your profile, including your CZWG roles. You can change the way your name is displayed by clicking on the 'Change display name' button. (CoC A4(b))">
                                <h5 class="font-weight-bold card-title">
                                    {{ Auth::user()->fullName('FLC') }}
                                </h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    {{Auth::user()->rating_GRP}} ({{Auth::user()->rating_short}})
                                </h6>
                                <p><a class="font-italic" style="color: blue;" data-toggle="modal" data-target="#ratingChange">Rating incorrect?</a></p>
                                Role: {{Auth::user()->permissions()}}<br/>
                                @if(Auth::user()->staffProfile)
                                    Staff Role: {{Auth::user()->staffProfile->position}}
                                @endif 
                                <hr>
                                <div data-step="4" data-intro="Here you can link your Discord account to receive reminders for training sessions, and gain access to the CZWG Discord.">
                                    <h5 class="mt-2 font-weight-bold blue-text">Discord</h5>
                                    @if (!Auth::user()->hasDiscord())
                                        <p class="mt-1"><i class="fa fa-times-circle" style="color:red"></i> You don't have a linked Discord account.</p>
                                        <a href="#" class="btn-sm btn-primary m-0" data-toggle="modal" data-target="#discordModal" class="mt-1">Link Discord account</a>
                                        <hr>
                                    @else
                                        <p class="mt-1"><i class="fa fa-check-circle" style="color:green"> </i> <img style="border-radius:50%; height: 30px;" class="img-fluid"
                                            src="{{Auth::user()->getDiscordAvatar()}}" alt="">&nbsp;&nbsp;{{Auth::user()->getDiscordUser()->username}}
                                            <span style="color: #d1d1d1;">#{{Auth::user()->getDiscordUser()->discriminator}}</span>
                                        </p>
                                        <a href="#" class="btn-sm btn-danger m-0" data-toggle="modal" data-target="#discordModal" class="mt-1">Unlink</a>   
                                        <hr>
                                    @endif
                                </div>
                            </div>
                            
                            <div data-step="5" data-intro="You can change your avatar here. Your avatar is available when people view your account. This will likely only be staff members, unless you sign up for an event or similar activity."
                                 class="col">
                                <h4 class="blue-text font-weight-bold card-title; text-center" style="padding-bottom: 2%">Avatar</h5>
                                    <div class="text-center">
                                        <img src="{{Auth::user()->avatar()}}"
                                             style="width: 125px; height: 125px; margin-bottom: 10px; border-radius: 50%;">
                                    </div>

                                    <center><a role="button" data-toggle="modal" data-target="#changeAvatar"
                                               class="btn btn-sm btn-primary" href="#">Change</a></center>
                                    @if (!Auth::user()->isAvatarDefault())
                                        <center><a role="button" class="btn btn-sm btn-danger"
                                                   href="{{route('users.resetavatar')}}">Reset</a></center>
                                @endif
                            </div>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-1">
                                &nbsp;
                                <button class="accordion">Change Display Name</button>
                                    <div class="panel">
                                        <form method="POST" action="{{route('users.changedisplayname')}}">
                                            <div class="card-body pb-0">
                                                @csrf
                                                <div class="form-group">
                                                    <h5 class="font-weight-bold blue-text">First Name</h5>
                                                    <input type="text" class="form-control" value="{{Auth::user()->display_fname}}"
                                                        name="display_fname" id="input_display_fname">
                                                    <br>   
                                                    <script>
                                                        function resetToCertFirstName() {
                                                            $("#input_display_fname").val("{{Auth::user()->fname}}")
                                                        }
                                                    </script>
                                                </div>
                                                <div class="form-group">
                                                    <h5 class="font-weight-bold blue-text">Display Options</h5>
                                                    <select name="format" class="custom-select">
                                                        <option value="showall">Show first name, last name, and CID
                                                            (e.g. {{Auth::user()->display_fname}} {{Auth::user()->lname}} {{Auth::id()}})
                                                        </option>
                                                        <option value="showfirstcid">Show first name and CID
                                                            (e.g. {{Auth::user()->display_fname}} {{Auth::id()}})
                                                        </option>
                                                        <option value="showcid">Show CID only (e.g. {{Auth::id()}})</option>
                                                    </select>
                                                    <br>
                                                    <input type="submit" class="ml-0 mt-4 btn btn-sm btn-success" value="Save">
                                                    <a class="mt-4 btn btn-sm btn-primary" role="button"
                                                    onclick="resetToCertFirstName()"><span>
                                                            Reset to CERT first name</span></a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <button class="accordion">Biography Editor</button>
                                    <div class="panel">
                                        <div class="card-body pb-0">
                                            <h5 class="font-weight-bold blue-text">Your Biography</h5>
                                                <form method="post" action="{{route('me.editbio')}}">
                                                    @csrf
                                                    <textarea name="bio" style="height: 100%;" class="form-control">{{Auth::user()->bio}}</textarea>
                                                    <br>
                                                    <p>Please ensure this complies with the VATSIM Code of Conduct.</p>
                                                    <input type="submit" class="btn btn-sm btn-success ml-0" value="Save">
                                                </form><br>
                                        </div>                     
                                    </div>
                                    <button class="accordion">Your Preferences</button>
                                        <div class="panel">
                                            <div class="card-body pb-0">
                                                <h5 class="font-weight-bold blue-text">Current Subscription Status</h5>
                                                @if (Auth::user()->gdpr_subscribed_emails == 0)
                                                    <h3>
                                                        <span class="p-2 badge badge-danger">Not subscribed</span>
                                                    </h3>
                                                @else
                                                    <h3>
                                                        <span class="p-2 badge badge-success">Subscribed</span>
                                                    </h3>
                                                @endif
                                                <hr>
                                                <h5 class="font-weight-bold">What Does This Mean?</h5>
                                                <p>
                                                    When you subscribe to our email service, you allow the Winnipeg FIR to send you 'promotional' emails as defined by the European Union GDPR.
                                                    These emails are typically not necessary to your continued participation in the FIR or holding an account with us on our system.<br/>
                                                    Some examples would include:
                                                </p>
                                                <ul style="list-style: square">
                                                    <li>Controller certifications for the month</li>
                                                    <li>News from the FIR Chief about non-critical matters</li>
                                                    <li>Updates from other staff members</li>
                                                    <li>Event notifications</li>
                                                </ul>
                                                <br>
                                                <p>To learn more about how we manage your data, please read our <a href="{{url('/privacy')}}">privacy policy!</a>
                                                <br>
                                                @if (Auth::user()->gdpr_subscribed_emails == 0)
                                                <a role="button" class="ml-0 mt-3 btn btn-sm btn-success" href="{{url('/dashboard/emailpref/subscribe')}}">Subscribe to Winnipeg Emails Now!</a>
                                                @else
                                                <a role="button" class="ml-0 mt-3 btn btn-sm btn-danger" href="{{url('/dashboard/emailpref/unsubscribe')}}">Unsubscribe</a>
                                                @endif
                                            </div>                     
                                        </div>
                                    </li>
                                </ul>
                    </div>
                </div>
                <br/>
                @if (Auth::user()->permissions >= 1)

                    <div class="card">
                        <div class="card-body">
                            <h3 class="font-weight-bold blue-text pb-2">Upcoming Events</h3>
                            <div class="list-group">
                                @if (count($confirmedevent) < 1)
                                    <h5>There are no scheduled events!</h5>
                                @else

                                    @foreach ($confirmedevent as $cevent)
                                        <h5><u><b>{{$cevent->name}}</b> on {{$cevent->start_timestamp_pretty()}}</h5></u>
                                        @foreach ($confirmedapp as $capp)
                                            @if ($cevent->id == $capp->event->id)
                                                <li>
                                                    <b>Slot:</b> {{$capp->airport}}
                                                    @if($capp->position != "Relief"){{$capp->position}} from @endif
                                                    @if($capp->position == "Relief")
                                                        <text class="text-danger">{{$capp->position}}</text>
                                                        from @endif
                                                    {{$capp->start_timestamp}}z - {{$capp->end_timestamp}}z
                                                </li>
                                                <br></br>
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endif
                                @if (count($unconfirmedapp) < 1)
                                    <p>
                                        You have
                                        <text class="text-primary"><b>no</b></text>
                                        active event applications
                                    </p>
                                @elseif (count($unconfirmedapp) == 1)
                                    <a href="" data-target="#unconfirmedEvents" data-toggle="modal"
                                       style="text-decoration:none;">
                                                <span class="blue-text">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                        <span style="color: #013162">
                                                    You have <text
                                                class="text-success"><b>{{count($unconfirmedapp)}}</b></text> active event application
                                                </span>
                                    </a>
                                @else
                                    <a href="" data-target="#unconfirmedEvents" data-toggle="modal"
                                       style="text-decoration:none;">
                                             <span class="blue-text">
                                                 <i class="fas fa-chevron-right"></i>
                                             </span>
                                        <span style="color: #013162">You have <text
                                                class="text-success"><b>{{count($unconfirmedapp)}}</b></text> active event applications</span>
                                    </a>
                                @endif

                                @if(count($confirmedevent) != 0)
                                    <a href="{{url('/dashboard/events/view')}}" style="text-align: center">View Event Rosters</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                {{--
                @if(Auth::user()->permissions >= 3)
                    <div class="card">
                        <div class="card-body">
                            <h3 class="font-weight-bold blue-text pb-2">Instructor Panel</h3>
                            @if(Auth::user()->permissions >= 4)

                                <!--All Students Admin View-->
                                @foreach($allinstructors as $instructor)
                                    <b><u>{{$instructor->full_name}}</u></b><br>


                                    @foreach ($pairs as $p)
                                        @if($instructor->cid == $p->instructor_id)

                                            {{$p->student_name}} -
                                            <a href="{{route('instructor.student.delete', [$p->student_id])}}">Delete</a>
                                            <br>

                                        @endif
                                    @endforeach<br>

                                @endforeach
                            @endif
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newStudent" style="float: left;">Add New Student</button>
                        <!--Instructor Specific Student List-->
                            @if(Auth::user()->permissions == 3)
                                <h5>List of your Students</h5><br>
                                @if (count($checkstudents) < 1)
                                    You have no students currently!
                                    <br>
                                @else
                                @foreach($pairs as $p)
                                    @if(Auth::user()->id == $p->instructor_id)
                                        {{$p->student_name}}<br>

                                    @endif
                                @endforeach
                            @endif
                            @endif


                            <ul class="list-unstyled mt-2 mb-0">

                            </ul>


                        </div>
                    </div>

                @endif
                --}}
            </div>
            @if(Auth::user()->permissions >= 1)
                <div class="col">
                    <div class="card" data-step="6"
                         data-intro="Here you can view your certification status within CZWG.">
                        <div class="card-body">
                            <h3 class="font-weight-bold blue-text pb-2">Certification and Training</h3>
                            <h5 class="card-title">Status</h5>
                            <div class="card-text">
                                <div class="d-flex flex-row justify-content-left">
                                    @if ($certification == "certified")
                                        <h3>
                            <span class="badge  badge-success rounded shadow-none">
                                <i class="fa fa-check"></i>&nbsp;
                                CZWG Certified
                            </span>
                                        </h3>
                                    @elseif ($certification == "not_certified")
                                        <h3>
                            <span class="badge badge-danger rounded shadow-none">
                                <i class="fa fa-times"></i>&nbsp;
                                Not Certified to Control
                            </span>
                                        </h3>
                                    @elseif ($certification == "training")
                                        <h3>
                            <span class="badge badge-warning rounded shadow-none">
                                <i class="fa fa-book-open"></i>&nbsp;
                                In Training
                            </span>
                                        </h3>
                                    @elseif ($certification == "home")
                                        <h3>
                            <span class="badge rounded shadow-none" style="background-color:#013162">
                                <i class="fa fa-user-check"></i>&nbsp;
                                CZWG Controller
                            </span>
                                        </h3>
                                    @elseif ($certification == "visit")
                                        <h3>
                            <span class="badge badge-info rounded shadow-none">
                                <i class="fa fa-plane"></i>&nbsp;
                                    CZWG Visiting Controller
                            </span>
                                        </h3>
                                    @elseif ($certification == "instructor")
                                        <h3>
                            <span class="badge badge-info rounded shadow-none">
                                <i class="fa fa-chalkboard-teacher"></i>&nbsp;
                                        CZWG Instructor
                            </span>
                                        </h3>
                                    @else
                                        <h3>
                            <span class="badge badge-dark rounded shadow-none">
                                <i class="fa fa-question"></i>&nbsp;
                                Unknown
                            </span>
                                        </h3>
                                    @endif
                                    @if ($active == 0)
                                        <h3>
                            <span class="badge ml-2 badge-danger rounded shadow-none">
                                <i class="fa fa-times"></i>&nbsp;
                                Inactive
                            </span>
                                        </h3>
                                    @elseif ($active == 1)
                                        <h3>
                            <span class="badge ml-2 badge-success rounded shadow-none">
                                <i class="fa fa-check"></i>&nbsp;
                                Active
                            </span>
                                        </h3>
                                    @endif
                                </div>
                                <span class="text-danger">
                        @if ($certification == "not_certified")
                                        <h5>You are not a certified controller, please contact an instructor to begin training.</h5>
                                    @endif
                                    @if ($active == 0)
                                        <h5>You are currently inactive, please contact the FIR Chief</h5>
                                        <h5>You should not control on the network while inactive.</h5>
                                    @endif
                                  </span>
                            </div>
                            <!--All users, no hours-->
                            @if (Auth::user()->rosterProfile)
                                <hr>

                                @if (Auth::user()->rosterProfile->status == "not_certified")
                                @else
                                    <h3 class="font-weight-bold blue-text pb-2">Activity</h3>

                                    <b>Monthly:</b>
                                    @if (Auth::user()->rosterProfile->currency < 0.1)
                                        <h3><span class="badge rounded shadow-none red">
                            No hours recorded
                        </span></h3>
                                    @endif
                                @endif

                            <!--Winnipeg Training Hrs-->
                                @if (Auth::user()->rosterProfile->status == "training")
                                    @if (!Auth::user()->rosterProfile->currency == 0)
                                        @if (Auth::user()->rosterProfile->currency < 2.0)
                                            <h3><span class="badge rounded shadow-none blue">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                          </span></h3>
                                        @elseif (Auth::user()->rosterProfile->currency >= 2.0)
                                            <h3><span class="badge rounded shadow-none green">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                          </span></h3>
                                        @endif
                                    @endif
                                    <p>You require <b>2 hours</b> of activity every month!</p>
                                @endif

                            <!--End Winnipeg Training Hours-->
                                <!--Winnipeg Cntrlr Hrs-->
                                @if (Auth::user()->rosterProfile->status == "home")
                                    @if (!Auth::user()->rosterProfile->currency == 0)
                                        @if (Auth::user()->rosterProfile->currency < 2.0)
                                            <h3><span class="badge rounded shadow-none blue">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                          </span></h3>
                                        @elseif (Auth::user()->rosterProfile->currency >= 2.0)
                                            <h3><span class="badge rounded shadow-none green">
                            {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                          </span></h3>
                                        @endif
                                    @endif
                                    <p>You require <b>2 hours</b> of activity every month!</p>
                                @endif
                            <!--End Winnipeg Cntrlr Hours-->

                                <!--Winnipeg Vstr Cntrlr Hrs-->
                                @if (Auth::user()->rosterProfile->status == "visit")
                                    @if (!Auth::user()->rosterProfile->currency == 0)
                                        @if (Auth::user()->rosterProfile->currency < 1.0)
                                            <h3><span class="badge rounded shadow-none blue">
                              {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                          </span></h3>
                                        @elseif (Auth::user()->rosterProfile->currency >= 1.0)
                                            <h3><span class="badge rounded shadow-none green">
                              {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                          </span></h3>
                                        @endif
                                    @endif
                                    <p>You require <b>1 hour</b> of activity every month!</p>
                                @endif

                            <!--End Winnipeg Cntrlr Hours-->

                                <!--Winnipeg Cntrlr Hrs-->
                                @if (Auth::user()->rosterProfile->status == "instructor")
                                    @if (!Auth::user()->rosterProfile->currency == 0)
                                        @if (Auth::user()->rosterProfile->currency < 3.0)
                                            <h3><span class="badge rounded shadow-none blue">
                                                {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                                            </span></h3>
                                        @elseif (Auth::user()->rosterProfile->currency >= 3.0)
                                            <h3><span class="badge rounded shadow-none green">
                                                {{decimal_to_hm(Auth::user()->rosterProfile->currency)}} hours recorded
                                            </span></h3>
                                        @endif
                                    @endif
                                    <p>You require <b>3 hours</b> of activity every month!</p>
                                @endif
                            <!--End Winnipeg Instrctr Hours-->
                                @if (Auth::user()->rosterProfile->rating == 'S1' || Auth::user()->rosterProfile->rating == 'S2' || Auth::user()->rosterProfile->rating == 'S3')
                                    <b>Rating:</b>

                                    <h3><span class="badge rounded shadow-none green">
                                    {{Auth::user()->rosterProfile->rating_hours}} {{Auth::user()->rosterProfile->rating}} hours recorded
                                </span></h3>

                                @endif


                            @endif
                            @elseif ($certification == "not_certified")
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li class="mb-2">
                                        <a href="{{route('application.list')}}" style="text-decoration:none;"><span
                                                class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                            <span
                                                class="black-text">View Your Applications</span></a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="{{route('application.list')}}" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="black-text">Training Centre</span></a>
                                    </li>
                            @endif
                            @if ($certification == "training")
                                <h5 class="card-title">Status</h5>
                                    <div class="card-text">
                                    <div class="d-flex flex-row justify-content-left">

                            <h3>
                          <span class="badge badge-warning rounded shadow-none">
                              <i class="fa fa-book-open"></i>&nbsp;
                              In Training
                          </span>
                                                </h3></div>
                                        </div>

                                    @endif

                                    @if ((isset($potentialRosterMember)) &&
                                    $potentialRosterMember->rating_hours >= 25.0)
                                        <span class="text-success">
                                          You have the required <b>25 hours</b> to begin the training for your next rating!
                                      </span>
                                    @endif
                                    {{--  Hi James! Make sure we are using isset to make sure the data is present, otherwise we will run into issues with invalid properties in Laravel!  --}}
                                    @if (isset(Auth::user()->rosterProfile->rating))
                                        @if (Auth::user()->rosterProfile->rating == 'S1' || Auth::user()->rosterProfile->rating == 'S2' || Auth::user()->rosterProfile->rating == 'S3')
                                            @if ($potentialRosterMember->rating_hours < 25.0)
                                                You require <b>25 hours</b> to begin the training for your next rating!
                                            @endif
                                        @endif
                                    @endif


                                </ul>
                        </div>
                    </div>
                    <br/>
                    <div class="card" data-step="7"
                         data-intro="This is where you'll see any training-related notifications - exams, modules and more will appear here!">
                        <div class="card-body">
                            <h3 class="font-weight-bold blue-text pb-2">1Winnipeg Training</h3>
                            <h5 class="font-weight-bold blue-text">Notifications</h5>
                            @if (count($cbtnotifications) < 1)
                                <text style="color: gray">There are no notifications to show!</text>
                            @else
                                @foreach ($cbtnotifications as $cn)
                                    <li>{{$cn->message}} <a href="{{route('cbt.notification.dismiss', $cn->id)}}"><i style="color: red" class="fas fa-times"></i></a></li>
                                @endforeach
                            @endif
                            <hr>
                            @if($yourinstructor != null && $yourinstructor->instructor != null)
                                <p class="mb-0"><b>Your Instructor:</b> {{$yourinstructor->instructor->user->fullName('FL')}}
                                    <br>
                                <b>Email:</b> <a href="mailto:{{$yourinstructor->instructor_email}}">{{$yourinstructor->instructor->email}}</a>
                                </p>
                            @else
                                @if ($certification == "training")
                                    You do not have an Instructor yet - check back soon or contact our Chief Instructor.
                                @endif
                            @endif
                            <ul class="list-unstyled mt-2 mb-0">
                                <li class="mb-2">
                                    <a href="{{route('cbt.index')}}" style="text-decoration:none;">
                                        <span class="blue-text"><i class="fas fa-chevron-right"></i></span> 
                                        &nbsp; 
                                        <span class="black-text">Training Centre</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <br/>
                    <div data-step="8"
                         data-intro="If you have any enquires or issues for the staff, feel free to make a ticket via the ticketing system."
                         class="card">
                        <div class="card-body">
                            <h3 class="font-weight-bold blue-text pb-2">Support</h3>
                            <h5 class="font-weight-bold blue-text">Tickets</h5>
                            @if (count($openTickets) < 1)
                                You have no open support tickets
                                <br>
                            @else
                                <h5 class="black-text" style="font-weight: bold">
                                    @if (count($openTickets) == 1)
                                        1 open ticket
                                    @else
                                        {{count($openTickets)}} open tickets
                                    @endif
                                </h5>
                                <div class="list-group">
                                    @foreach ($openTickets as $ticket)
                                        <a href="{{url('/dashboard/tickets/'.$ticket->ticket_id)}}"
                                           class="list-group-item list-group-item-action black-text rounded-0 "
                                           style="background-color:#d9d9d9">{{$ticket->title}}<br/>
                                            <small title="{{$ticket->updated_at}} (GMT+0, Zulu)">Last
                                                updated {{$ticket->updated_at_pretty()}}</small>
                                        </a>
                                    @endforeach
                                </div>
                                <br>
                            @endif
                            @if(Auth::user()->permissions >= 4)
                                <br>
                                <h5 class="font-weight-bold blue-text">Staff Tickets</h5>

                                @if (count($staffTickets) < 1)
                                    You have no open <b>staff</b> tickets
                                    <br>
                                @else
                                    <h5 class="black-text" style="font-weight: bold">
                                        @if (count($staffTickets) == 1)
                                            1 open staff ticket
                                        @else
                                            {{count($staffTickets)}} open staff tickets
                                        @endif
                                    </h5>
                                    <div class="list-group">
                                        @foreach ($staffTickets as $ticket)
                                            <a href="{{url('/dashboard/tickets/'.$ticket->ticket_id)}}"
                                               class="list-group-item list-group-item-action black-text rounded-0 "
                                               style="background-color:#d9d9d9">{{$ticket->title}}<br/>
                                                <small title="{{$ticket->updated_at}} (GMT+0, Zulu)">Last
                                                    updated {{$ticket->updated_at_pretty()}}</small>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                <br>
                            @endif
                            <ul class="list-unstyled mt-2 mb-0">
                                <li class="mb-2">
                                    <a href="{{route('feedback.create')}}" style="text-decoration:none;"><span
                                            class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span
                                            class="black-text">Send feedback</span></a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{route('tickets.index', ['create' => 'yes'])}}"
                                       style="text-decoration:none;"><span
                                            class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span
                                            class="black-text">Start a support ticket</span></a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{route('tickets.index')}}" style="text-decoration:none;"><span
                                            class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span
                                            class="black-text">View previous support tickets</span></a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{route('me.data')}}" style="text-decoration:none;">
                                        <span class="blue-text">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    &nbsp;
                                    <span class="black-text">
                                    Manage your data
                                </span>
                                </a>
                            </li>
                                @if(Auth::user()->permissions >= 4)
                                    <li class="mb-2">
                                        <a href="{{route('tickets.staff')}}" style="text-decoration:none;"><span
                                                class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                            <span
                                                class="black-text">View staff ticket inbox</span></a>
                                    </li>
                            @endif
                            <!--<li class="mb-2">
                            <a href="https://kb.ganderoceanic.com" target="_blank" style="text-decoration:none;"><span class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp; <span class="black-text">CZWG Knowledge Base</span></a>
                        </li>-->
                            </ul>
                        </div>
                    </div>
                    <br/>
                    @if (Auth::user()->permissions >= 4)
                        <div class="card">
                            <div class="card-body">
                                <h3 class="font-weight-bold blue-text pb-2">Staff</h3>
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li class="mb-2">
                                        <a href="{{route('training.index')}}" style="text-decoration:none;"><span
                                                class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                            <span
                                                class="black-text">1Winnipeg Training System</span></a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="{{route('roster.index')}}" style="text-decoration:none;"><span
                                                class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                            <span
                                                class="black-text">Manage Controller Roster</span></a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="{{route('events.admin.index')}}" style="text-decoration:none;"><span
                                                class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                            <span
                                                class="black-text">Manage Events</span></a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="{{route('news.index')}}" style="text-decoration:none;"><span
                                                class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                            <span
                                                class="black-text">Manage News</span></a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="{{route('staff.feedback.index')}}" style="text-decoration:none;"><span
                                                class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                            <span
                                                class="black-text">Manage Feedback</span></a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="{{(route('users.viewall'))}}" style="text-decoration:none;">
                                    <span class="blue-text">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                            &nbsp;
                                            <span class="black-text">Manage Users</span>
                                        </a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="{{(route('dashboard.upload'))}}" style="text-decoration:none;">
                                    <span class="blue-text">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                            &nbsp;
                                            <span class="black-text">File Uploader</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <br/>
                    @endif
                    @if (Auth::user()->permissions >= 5)
                        <div class="card">
                            <div class="card-body">
                                <h3 class="font-weight-bold blue-text pb-2">Site Admin</h3>
                                <ul class="list-unstyled mt-2 mb-0">
                                    <li class="mb-2">
                                        <a href="{{route('settings.index')}}" style="text-decoration:none;"><span
                                                class="blue-text"><i class="fas fa-chevron-right"></i></span> &nbsp;
                                            <span
                                                class="black-text">Settings</span></a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="{{route('network.index')}}" style="text-decoration:none;">
                                <span class="blue-text">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                                            &nbsp;
                                            <span class="black-text">
                                    View network data
                                </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        <br/>
        <a style="color: white" href="javascript:void(0);" onclick="javascript:introJs().setOption('showProgress', true).start();">Dashboard Tutorial</a>
    </div>

    <script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight) {
        panel.style.maxHeight = null;
        } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
        } 
    });
    }
    </script>

    <!--Change avatar modal-->
    <div class="modal fade" id="changeAvatar" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Change avatar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('users.changeavatar')}}" enctype="multipart/form-data" class=""
                      id="">
                    <div class="modal-body">
                        <p>Please ensure your avatar complies with the VATSIM Code of Conduct. This avatar will be
                            visible to staff members, if you place a controller booking, and if you're a staff member
                            yourself, on the staff page.</p>
                        @csrf
                        <div class="input-group pb-3">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="file">
                                <label class="custom-file-label">Choose file</label>
                            </div>
                        </div>
                        @if(Auth::user()->hasDiscord())
                            or use your Discord avatar (refreshes every 6 hours)<br/>
                            <p class="mt-1"><img style="border-radius:50%; height: 60px;" class="img-fluid"
                                                 src="{{Auth::user()->getDiscordAvatar()}}" alt=""><a
                                    href="{{route('users.changeavatar.discord')}}"
                                    class="btn btn-outline-success bg-CZQO-blue-light mt-3">Use Discord Avatar</a>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                        <input type="submit" class="btn btn-success" value="Upload">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--End change avatar modal-->

    <!--Biography modal-->

    <!--End biography modal-->

    <!-- Start Rating Change modal -->
    <div class="modal fade" id="ratingChange" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">How We Update VATSIM Ratings</h5>
                </div>
                <div class="modal-body">
                    <h5>Our website updates your VATSIM rating 2 ways:</h5>
                    The first way is on login. Everytime you login to the website, we get your rating from VATSIM connect.
                    <br><br>
                    The second way is everyday, at 00:00 Eastern, we go through our list of users and compare your rating on our website with the VATSIM API.
                    <hr>
                    <h5>How can I fix my rating?</h5>
                    <p>If you would like to fix your rating on our website, you may <a href="/logout">logout</a> and log back in, or wait until our check happens at 00:00 Eastern Time.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-light" data-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Rating Change modal -->
    <!--Change display name modal-->
    
    <!--End change display name modal-->

    <!--Link/unlink Discord modal-->
    <div class="modal fade" id="discordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            @if (!Auth::user()->hasDiscord())
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Link your Discord account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img style="height: 50px;" src="{{asset('/img/discord/CZWGplusdiscord.png')}}"
                             class="img-fluid mb-2" alt="">
                        <p>Linking your Discord account with Winnipeg FIR allows you to:</p>
                        <ul>
                            <li>Join our Discord community</li>
                            <li>Receive notifications for ticket replies, training updates, and more</li>
                            <li>Use your Discord avatar on the website</li>
                        </ul>
                        <p>To link your account, click the button below. You will be redirected to Discord to approve
                            the link. Information on data stored through Discord OAuth is available in the <a
                                href="{{route('privacy')}}">privacy policy.</a></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                        <a role="button" type="submit" href="{{route('me.discord.link')}}" class="btn btn-primary">Link
                            Account</a>
                    </div>
                </div>
            @else
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Unlink your Discord account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Fair Warning: Unlinking your account will:</p>
                        <ul>
                            <li>Remove you from the CZWG Discord, if you're a member</li>
                            <li>Remove a Discord avatar if you have it selected</li>
                            <li>Stop sending you notifications via Discord</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                        <a role="button" type="submit" href="{{route('me.discord.unlink')}}" class="btn btn-danger">Unlink
                            Account</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <script>
        //$("#discordModal").modal();
    </script>
    <!--End Discord modal-->
    <!--Join guild modal-->
    <div class="modal fade" id="joinDiscordServerModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Join the Winnipeg FIR Discord server</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Joining the Winnipeg FIR Discord server allows you to join the Winnipeg FIR controller and pilot
                        community.</p>
                    <h5>Rules</h5>
                    <ul>
                        <li>1. The VATSIM Code of Conduct applies.</li>
                        <li>2. Always show respect and common decency to fellow members.</li>
                        <li>3. Do not send server invites to servers unrelated to VATSIM without staff permission. Do
                            not send ANY invites via DMs unless asked to.
                        </li>
                        <li>4. Do not send spam in the server, including images, text, or emotes.</li>
                    </ul>
                    <p>Clicking the 'Join' button will redirect you to Discord. We require the Join Server permission to
                        add your Discord account to the server.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <a role="button" type="submit" href="{{route('me.discord.join')}}" class="btn btn-primary">Join</a>
                </div>
            </div>
        </div>
    </div>
    <!--Confirm Delete visitor button-->
    {{--
    <div class="modal fade" id="newStudent" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign Student to Instructor</h5><br>
                </div>
                <div class="modal-body">
                    <form method="POST"  class="form-group">
                    <!--deleted action="{{route('instructor.student.add.new')}}" from the form to view dashboard.-->
                        @csrf
                        <label class="form-control">Choose a Student</label>
                        <select name="student_id" id-"student_id" class="form-control">
                        @foreach ($allusers as $u)
                            <option value="{{$u->id}}">{{$u->id}} - {{$u->fullName('FL')}}</option>
                            @endforeach
                            </select>
                            <label class="form-control">Choose an Instructor</label>
                            <select name="instructor_id" id="instructor_id" class="form-control">
                                @foreach ($allinstructors as $i)
                                    <option value="{{$i->cid}}">{{$i->cid}} - {{$i->full_name}}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-success form-control" type="submit" href="#">Add Student</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
                </div>
            </div>
        </div>
    </div> --}}
    <!--end delete visitor-->

    <!--unconfirmed events modal-->
    <div class="modal fade" id="unconfirmedEvents" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Events You've Applied For</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                  @foreach ($confirmedevent as $cevent)
                  <h5><text class="font-weight-bold">{{$cevent->name}}</text> on {{$cevent->start_timestamp_pretty()}}</h5>
                        @foreach ($unconfirmedapp as $uapp)
                            @if ($cevent->name == $uapp->event->name)
                                <li>
                                    <text class="font-weight-bold"> Position Requested:</text> {{$uapp->position}} from {{$uapp->start_availability_timestamp}}z - {{$uapp->end_availability_timestamp}}z
                                </li><br>
                            @endif
                      @endforeach
                      @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
    <!--End biography modal-->

    <!--End join guild modal

{{-- <div class="modal fade" id="ctpSignUpModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Cross the Pond October 2019 Sign-up</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('ctp.signup.post')}}" method="POST">
                @csrf
            <div class="modal-body">
                <p>
                    If you wish to control Gander/Shanwick Oceanic for <a href="https://ctp.vatsim.net/">Cross the Pond Eastbound 2019</a>, you can sign up here!
                </p>
                <h5 class="font-weight-bold">Requirements</h5>
                <ul class="ml-3" style="list-style: disc">
                    <li>Be a C1 rated controller or above</li>
                    <li>A suitable amount of hours as a C1 (50+)</li>
                    <li>You <b>do not</b> have to be a Gander or Shanwick certified controller</li>
                </ul>
                <h5 class="font-weight-bold">Availability</h5>
                <p>Are you available to control CTP Eastbound on 26 October?</p>
                <select name="availability" id="" class="form-control">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="standby">As a standby controller</option>
                </select>
                <h5 class="mt-2 font-weight-bold">Times</h5>
                <p>What times are you available (in zulu)? If left blank, we will assume you are available for the entire event.</p>
                <input maxlength="191" name="times" class="form-control" type="text" placeholder="e.g. Between 1100z and 2000z">
                <p class="mt-2">By pressing the "Confirm" button below, you agree to be available to control for the periods you have typed above. If you are no longer available, please contact the FIR Chief ASAP.</p>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="Confirm">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Dismiss</button>
            </div>
            </form>
        </div>
    </div>

</div> --}}

@stop
