@extends('layouts.master')
@section('content')
    <div class="container py-4">
        <h1 data-step="1" data-intro="Welcome to the CZWG Dashboard! This is your central hub for all things Winnipeg. Here you can interact with our FIR, and manage your account." class="blue-text font-weight-bold">Dashboard</h1>
        <br class="my-2">
        <div class="row">
            <div class="col">
                <div data-step="2"
                     data-intro="Here is where you manage and view the data we store on you and your CZWG profile."
                     class="card ">
                    <div class="card-body">
                        <h3 class="font-weight-bold blue-text pb-2">Your Account</h3>
                        <div class="row">
                            <div class="col" data-step="3"
                                 data-intro="Here is an overview of your profile, including your CZWG roles. You can change the way your name is displayed by clicking on the 'Change display name' button. (CoC A4(b))">
                                <h5 class="card-title">
                                    {{ Auth::user()->fullName('FLC') }}
                                </h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    {{Auth::user()->rating_GRP}} ({{Auth::user()->rating_short}})
                                </h6>
                                <p><a class="font-italic" style="color: blue;" data-toggle="modal" data-target="#ratingChange">Rating incorrect?</a></p>
                                Role: {{Auth::user()->permissions()}}<br/>
                                <br/>
                                <div data-step="4" data-intro="Here you can link your Discord account to receive reminders for training sessions, and gain access to the CZWG Discord.">
                                    <h5 class="mt-2">Discord</h5>
                                    @if (!Auth::user()->hasDiscord())
                                        <p class="mt-1">You don't have a linked Discord account.</p>
                                        <a href="#" data-toggle="modal" data-target="#discordModal" class="mt-1">Link a
                                            Discord account</a>
                                    @else
                                        <p class="mt-1"><img style="border-radius:50%; height: 30px;" class="img-fluid" src="{{Auth::user()->getDiscordAvatar()}}" alt="">&nbsp;&nbsp;{{Auth::user()->getDiscordUser()->username}}
                                            <span style="color: #d1d1d1;">#{{Auth::user()->getDiscordUser()->discriminator}}</span>
                                        </p>
                                        @if(!Auth::user()->memberOfCZWGGuild())
                                            <a href="#" data-toggle="modal" data-target="#joinDiscordServerModal"
                                               class="mt-1">Join The Discord</a><br>
                                        @endif
                                        <a href="#" data-toggle="modal" data-target="#discordModal"
                                           class="mt-1">Unlink</a>
                                    @endif
                                </div>

                            </div>
                            <div data-step="5" data-intro="You can change your avatar here. Your avatar is available when people view your account. This will likely only be staff members, unless you sign up for an event or similar activity." class="col">
                                <h4 class="card-title; text-center">Avatar</h5><br>
                                    <div class="text-center">
                                        <img src="{{Auth::user()->avatar()}}" style="width: 125px; height: 125px; margin-bottom: 10px; border-radius: 50%;">
                                    </div>
                                    <center><a role="button" data-toggle="modal" data-target="#changeAvatar" class="btn btn-sm btn-primary" href="#">Change</a></center>
                                    @if (!Auth::user()->isAvatarDefault())
                                        <center><a role="button" class="btn btn-sm btn-danger" href="{{route('users.resetavatar')}}">Reset</a></center>
                                @endif
                            </div>
                        </div>
                        <ul class="list-unstyled mt-2 mb-0">
                            <li class="mb-2">
                                <a href="" data-target="#changeDisplayNameModal" data-toggle="modal"
                                   style="text-decoration:none;">
                                <span class="blue-text">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                                    &nbsp;
                                    <span class="black-text">
                                    Change display name
                                </span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="" data-target="#viewBio" data-toggle="modal" style="text-decoration:none;">
                                <span class="blue-text">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                                    &nbsp;
                                    <span class="black-text">
                                    Your biography
                                </span>
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{route('me.preferences')}}" style="text-decoration:none;">
                                <span class="blue-text">
                                    <i class="fas fa-chevron-right"></i>

                                    &nbsp;
                                    <span class="black-text">
                                    Manage preferences
                                </span>
                                </a>
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
                        </ul>
                    </div>
                </div>
                <br/>
            </div>
            <div class="col">
                {{-- Tickets --}}
                <div data-step="6" data-intro="If you have any enquires or issues for the staff, feel free to make a ticket via the ticketing system." class="card">
                    <div class="card-body">
                        <h3 class="font-weight-bold blue-text pb-2">Support</h3>
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
                    </div>
                </div>
                <br>
                <div class="card">
                    <div class="card-body">
                        <h3 class="font-weight-bold blue-text pb-2">Upcoming Events</h3>
                        @if (count($confirmedevent) < 1)
                            <h5>There are no scheduled events!</h5>
                        @else
                            @foreach($confirmedevent as $e)
                                <h5><b>{{$e->name}}</b> on {{$e->start_timestamp_pretty()}}</h5>
                                <br>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <a href="javascript:void(0);" onclick="javascript:introJs().setOption('showProgress', true).start();">View the tutorial</a>
    </div>

    <!-- Start Rating Change modal -->
    <div class="modal fade" id="ratingChange" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Why is my rating wrong?</h5>
                </div>
                <div class="modal-body">
                    <h5>Our website updates your VATSIM rating 2 ways:</h5>
                    The first way is on login. Everytime you login to the website, we get your rating from VATSIM connect.
                    <br><br>
                    The second way is everyday, at 12am Eastern, we go through our list of users and compare your rating on our website with the VATSIM API.
                    <hr>
                    <h5>How can I fix my rating?</h5>
                    <p>If you would like to fix your rating on our website, you may <a href="/logout">logout</a> and log back in, or wait until our check happens at 12am Eastern.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-light" data-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Rating Change modal -->

    <!--Change display name modal-->
    <div class="modal fade" id="changeDisplayNameModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Display Name</h5><br>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{route('users.changedisplayname')}}">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" value="{{Auth::user()->display_fname}}"
                                   name="display_fname" id="input_display_fname">
                            <br>
                            <a class="btn btn-sm btn-primary" role="button"
                               onclick="resetToCertFirstName()"><span>
                                    Reset to CERT first name</span></a>
                            <script>
                                function resetToCertFirstName() {
                                    $("#input_display_fname").val("{{Auth::user()->fname}}")
                                }
                            </script>
                        </div>
                        <div class="form-group">
                            <label>Format</label>
                            <select name="format" class="custom-select">
                                <option value="showall">Show first name, last name, and CID
                                    (e.g. {{Auth::user()->display_fname}} {{Auth::user()->lname}} {{Auth::id()}})
                                </option>
                                <option value="showfirstcid">Show first name and CID
                                    (e.g. {{Auth::user()->display_fname}} {{Auth::id()}})
                                </option>
                                <option value="showcid">Show CID only (e.g. {{Auth::id()}})</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-success" value="Save Changes">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--End change display name modal-->


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
    <div class="modal fade" id="viewBio" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">View your biography</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (Auth::user()->bio)
                        {{Auth::user()->bio}}
                    @else
                        You have no biography.
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button>
                    <a href="{{route('me.editbio')}}" class="btn btn-primary" role="button">Edit Biography</a>
                </div>
            </div>
        </div>
    </div>
    <!--End biography modal-->
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

    <div class="modal fade" id="joinDiscordServerModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Join the Winnipeg FIR Discord!</h5>
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

@endsection
