@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    <div class="container py-4">
        <a href="{{route('users.viewall')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Users</a>
        <h1 class="blue-text font-weight-bold mt-2"><img src="{{$user->avatar()}}" style="height: 50px; width:50px;margin-right: 15px; margin-bottom: 3px; border-radius: 50%;">{{$user->fullName('FL')}}</h1>
        <hr>
        @if ($user->fname != $user->display_fname || !$user->display_last_name || $user->display_cid_only)
            <p>Note: this user's display name does not match their CERT name.</p>
        @endif
        @if($user->id == 1 || $user->id == 2)
        <div class="alert bg-czqo-blue-light">
            This account is a system account used to identify automatic actions, or to serve as a placeholder user.
        </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <h2 class="font-weight-bold blue-text pb-2">Basic Data</h2>
                <div class="card p-3">
                    <h5>Identity</h5>
                    <ul class="list-unstyled">
                        <li>CID: {{$user->id}}</li>
                        @if (Auth::user()->permissions == 5)
                        <li>CERT First Name: {{$user->fname}}</li>
                        <li>CERT Last Name: {{$user->lname}}</li>
                        @endif
                        <li>Display Name: {{$user->fullName('FLC')}}</li>
                        <li>Rating: {{$user->rating_GRP}} ({{$user->rating_short}})</li>

                    </ul>
                    <h5>Email</h5>
                    <a href="mailto:{{$user->email}}">{{$user->email}}</a>
                    <br>
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
                    </div>
                    <!--All users, no hours-->
                    @if ($user->rosterProfile)
                    <h5 class="card-title mt-2">Activity</h5>
                        @if ($user->rosterProfile->currency < 0.1)
                        <h3><span class="badge rounded shadow-none red">
                            No hours recorded
                        </span></h3>
                        @endif
<!--Winnipeg Training Hrs-->
                        @if ($user->rosterProfile->status == "training")
                        @if (!$user->rosterProfile->currency == 0)
                          @if ($user->rosterProfile->currency < 2.0)
                          <h3><span class="badge rounded shadow-none blue">
                            {{$user->rosterProfile->currency}} hours recorded
                          </span></h3>
                          @elseif ($user->rosterProfile->currency >= 2.0)
                          <h3><span class="badge rounded shadow-none green">
                            {{$user->rosterProfile->currency}} hours recorded
                          </span></h3>
                          @endif
                          @endif
                              <p>They require <b>2 hours</b> of activity every month.</p>
                        @endif
<!--End Winnipeg Training Hours-->

<!--Winnipeg Cntrlr Hrs-->
                        @if ($user->rosterProfile->status == "home")
                        @if (!$user->rosterProfile->currency == 0)
                          @if ($user->rosterProfile->currency < 2.0)
                          <h3><span class="badge rounded shadow-none blue">
                            {{$user->rosterProfile->currency}} hours recorded
                          </span></h3>
                          @elseif ($user->rosterProfile->currency >= 2.0)
                          <h3><span class="badge rounded shadow-none green">
                            {{$user->rosterProfile->currency}} hours recorded
                          </span></h3>
                          @endif
                          @endif
                              <p>They require <b>2 hours</b> of activity every month.</p>
                        @endif
<!--End Winnipeg Cntrlr Hours-->

<!--Winnipeg Vstr Cntrlr Hrs-->
                        @if ($user->rosterProfile->status == "visit")
                        @if (!$user->rosterProfile->currency == 0)
                        @if ($user->rosterProfile->currency < 1.0)
                          <h3><span class="badge rounded shadow-none blue">
                              {{Auth::user()->rosterProfile->currency}} hours recorded
                          </span></h3>
                        @elseif ($user->rosterProfile->currency >= 1.0)
                          <h3><span class="badge rounded shadow-none green">
                              {{$user->rosterProfile->currency}} hours recorded
                          </span></h3>
                        @endif
                        @endif
                        <p>They require <b>1 hour</b> of activity every month.</p>
                        @endif

<!--End Winnipeg Cntrlr Hours-->

<!--Winnipeg Cntrlr Hrs-->
                        @if ($user->rosterProfile->status == "instructor")
                        @if (!$user->rosterProfile->currency == 0)
                            @if ($user->rosterProfile->currency < 3.0)
                                <h3><span class="badge rounded shadow-none blue">
                                {{$user->rosterProfile->currency}} hours recorded
                            </span></h3>
                            @elseif ($user->rosterProfile->currency >= 3.0)
                                <h3><span class="badge rounded shadow-none green">
                                {{$user->rosterProfile->currency}} hours recorded
                                </span></h3>
                            @endif
                            @endif
                            <p>They require <b>3 hours</b> of activity every 2 month.</p>
                        @endif
<!--End Winnipeg Instrctr Hours-->

                    @endif

                </div><br>
                <h2 class="font-weight-bold blue-text pb-2">Modify User</h2>
                <div class="card p-3">
                  <div class="d-flex flex-row align-items-center">
                      <ul class="list-unstyled" style="width:500px; height: 80px">

                    <li><h5>Current Permissions Level: {{$user->permissions()}} </h5></li>
                    @if ($user->id == Auth::user()->id)
                            <a role="button" data-toggle="modal" data-target="#confirmChange" class="btn btn-sm btn-success">Update Yourself</a>
                    @elseif (Auth::user()->permissions == 5 || $user->permissions < 4 && Auth::user()->permissions > 3 || $user->permissions < 2 && Auth::user()->permissions > 2)
                    <h5 display="inline-block">Change Permissions Level:</h5>
                    <form method="post" action="{{route('edit.userpermissions', [$user->id])}}" style="position:absolute">
                        <select name="permissions" id="permissions" class="form-control" style="position:relative; width:100px; left:210px; bottom:40px">
                        <option name="guest" value="0" id="0"{{ $user->permissions == "0" ? "selected=selected" : ""}}>Guest</option>
                          <option name="controller" value="1" id="1"{{ $user->permissions == "1" ? "selected=selected" : ""}}>Controller</option>
                          <option name="mentor" value="2" id="2"{{ $user->permissions == "2" ? "selected=selected" : ""}}>Mentor</option>
                          <option name="instructor" value="3" id="3"{{ $user->permissions == "3" ? "selected=selected" : ""}}>Instructor</option>

                            @if (Auth::user()->permissions == 5)
                          <option name="staff" value="4" id="4"{{ $user->permissions == "4" ? "selected=selected" : ""}}>Staff Member</option>
                          <option name="admin" value="5" id="5"{{ $user->permissions == "5" ? "selected=selected" : ""}}>Administrator</option>
                          @endif
                        </select>
                        <li><h5 display="inline-block" style="position:relative; bottom:35px">Change Certification:</h5></li>
                        <select name="certification" id="certification" class="form-control" style="position:relative; width:100px; left:210px; bottom:75px">
                        <option name="not_certified" value="not_certified" id="not_certified"{{ $certification == "not_certified" ? "selected=selected" : ""}}>Not Certified</option>
                          <option name="training" value="training" id="training"{{ $certification == "training" ? "selected=selected" : ""}}>Training</option>
                          <option name="home" value="home" id="home"{{ $certification == "home" ? "selected=selected" : ""}}>Home</option>
                          <option name="visit" value="visit" id="visit"{{ $certification == "visit" ? "selected=selected" : ""}}>Visitor</option>
                          @if (Auth::user()->permissions >= 4)
                          <option name="instructor" value="instructor" id="instructor"{{ $certification == "instructor" ? "selected=selected" : ""}}>Instructor</option>
                          @endif
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success" style="position:relative; width:150px; left:335px; bottom:114px">Update User</button>
                      </form>
                      @endif
                      <br><br><br><br> <br><br><br><br>
                  </ul>
                </div></div>
            </div>
            <div class="col-md-6">
            <h2 class="font-weight-bold blue-text pb-2">Avatar</h2>
                <div class="card p-3">
                    <div class="d-flex flex-row align-items-center">
                        <img src="{{$user->avatar()}}" style="height: 100px; width: 100px; border-radius: 50%;">
                        <div class="ml-4">
                            <a href="#" data-toggle="modal" data-target="#changeAvatar" class="btn btn-sm btn-primary">Change</a>
                            @if(!$user->isAvatarDefault())
                            <form action="{{route('users.resetusersavatar')}}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                <input type="submit" class="btn btn-sm bg-czqo-blue-light" value="Reset">
                            </form>
                            @endif
                            <p class="mt-2 pl-1">Avatar Mode:
                                @switch($user->avatar_mode)
                                @case(0)Default
                                @break
                                @case(1)Custom Image
                                @break
                                @case(2)Discord Avatar
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div><br>
                <h2 class="font-weight-bold blue-text pb-2">Discord Info</h2>
                <div class="card p-3">
                    @if($user->hasDiscord())
                    <h5><img style="border-radius:50%; height: 30px;" class="img-fluid" src="{{$user->getDiscordAvatar()}}" alt="">&nbsp;&nbsp;{{$user->getDiscordUser()->username}}#{{$user->getDiscordUser()->discriminator}}</h5>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-center">Member of the Winnipeg Discord: <i style="margin-left: 5px;font-size: 20px;" class="{{$user->memberOfCZWGGuild() ? 'fas fa-check-circle green-text' : 'fas fa-times-circle red-text'}}"></i></li>
                    </ul>
                    <hr>
                    <h5>
                        <div class="d-flex flex-row justify-content-between align-items-center">
                            Bans
                            {{--<a href="#" class="btn btn-sm bg-czqo-blue-light">Add Ban</a>--}}
                        </div>
                    </h5>
                    @if (count($user->discordBans) < 1)
                    No bans found.
                    @else
                    <div class="list-group">
                        @foreach($user->discordBans as $ban)
                        <div class="list-group-item pr-0">
                            <div class="d-flex flex-row justify-content-between">
                                <b>From {{$ban->banStartPretty()}} to {{$ban->banEndPretty()}}</b>
                                <div class="justify-self-end">
                                    <a href="#" class="btn btn-sm bg-czqo-blue-light">View Reason</a>
                                    @if($ban->isCurrent())
                                    <a href="#" class="btn btn-sm btn-danger ">Remove Ban</a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @else
                    This user does not have a linked Discord account.
                    @endif
                </div><br>
                <h2 class="font-weight-bold blue-text pb-2">User's Attached Roles</h2>
                <div class="card p-3">
                    @if (count($roles) < 1)
                        This user has no roles attached!
                    @else
                    <ul class="list-unstyled mt-2 mb-0">
                        <li class="mb-2">
                    @foreach ($roles as $r)

                            @if ($r->role->secure == "1")
                                    <i class="fas fa-chevron-right"></i>{{$r->role->name}}
                                @role('admin')
                                <a type="button" class="btn btn-sm btn-primary" style="color: #ff6161" href="{{route('user.role.delete', [$r->role->slug, $user->id])}}"><i class="fa fa-times"></i></a><br>
                                @endrole
                                @else
                                <br>
                                    <i class="fas fa-chevron-right"></i>{{$r->role->name}}
                                    <a type="button" class="btn btn-sm btn-primary" style="color: #ff6161" href="{{route('user.role.delete', [$r->role->slug, $user->id])}}"><i class="fa fa-times"></i></a><br>
                                @endif
                                    @endforeach
                            @endif
                        </li></ul><br>

                        <a href="#" data-toggle="modal" data-target="#addRole" class="btn btn-sm bg-czqo-blue-light">Attach a Role</a>

                </div><br>
                <h2 class="font-weight-bold blue-text pb-2">User Notes</h2>
                <div class="card p-3">
                    <a href="#" data-toggle="modal" data-target="#addNoteModal" class="btn btn-sm bg-czqo-blue-light">Add Note</a>
                    <a href="#" data-toggle="modal" data-target="#viewNotesModal" class="btn btn-sm bg-primary text-light">View Notes</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">User Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('users.createnote', $user->id)}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Content</label>
                        {!! Form::textarea('content', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Confidential</label>
                        {!! Form::checkbox('confidential', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" value="Submit">Submit</button>
                    {!! Form::close() !!}
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--View Notes modal-->
    <div class="modal fade" id="viewNotesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View User Notes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @foreach($userNotes as $note)
                    <div class="list-group-item pr-0">
                        <div class="d-flex flex-row justify-content-between">
                        <ul class="list-unstyled">
                            <li><p>Author: {{$note->author_name}}</p></li>
                            <li><p>Date/Time: {{$note->timestamp}}</p></li>
                            <li><p>Notes: {{$note->html()}}</p></li>
                            <form action="{{route('users.deletenote', [$user->id, $note->id])}}" method="GET">
                            <button class="btn btn-sm btn-danger" class="mt-1">Delete</button>
                            </form>
                        </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--Change avatar modal-->
    <div class="modal fade" id="changeAvatar" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Change {{$user->fullName('F')}}'s avatar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('users.changeusersavatar')}}" enctype="multipart/form-data" class="" id="">
                <div class="modal-body">
                    <p>Abuse of this function will result in disciplinary action. This function should only be used for adjusting staff members' avatars for the staff page, or at a users request.</p>
                    @csrf
                    <div class="input-group pb-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file">
                            <input type="hidden" name="user_id" value="{{$user->id}}">
                            <label class="custom-file-label">Choose file</label>
                        </div>
                    </div>
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
    <!--Confirm change own permissions modal-->
<div class="modal fade" id="confirmChange" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Confirm Change</h5><br>
            </div>
            <div class="modal-body">
            <center><h3>Are You Sure?</h3>
            <p style="font-weight:bold; color:red">NOTE: If you demote yourself lower than staff member, you will not be able to change it back!</p></center>
            <form method="POST" action="{{route('edit.userpermissions', [$user->id])}}">
                {{ csrf_field() }}
                    <h5 display="inline-block">Change Permissions Level:</h5>
                    <form method="post" action="{{route('edit.userpermissions', [$user->id])}}" style="position:absolute">
                        <select name="permissions" id="permissions" class="form-control" style="position:relative; width:100px; left:210px; bottom:40px">
                        <option name="guest" value="0" id="0"{{ $user->permissions == "0" ? "selected=selected" : ""}}>Guest</option>
                        <option name="controller" value="1" id="1"{{ $user->permissions == "1" ? "selected=selected" : ""}}>Controller</option>
                        <option name="mentor" value="2" id="2"{{ $user->permissions == "2" ? "selected=selected" : ""}}>Mentor</option>
                        <option name="instructor" value="3" id="3"{{ $user->permissions == "3" ? "selected=selected" : ""}}>Instructor</option>
                        <option name="staff" value="4" id="4"{{ $user->permissions == "4" ? "selected=selected" : ""}}>Staff Member</option>
                        @if (Auth::user()->permissions == 5)
                        <option name="admin" value="5" id="5"{{ $user->permissions == "5" ? "selected=selected" : ""}}>Administrator</option>
                        @endif
                        </select>
                        <h5 display="inline-block" style="position:relative; bottom:35px">Change Certification:</h5>
                        <select name="certification" id="certification" class="form-control" style="position:relative; width:100px; left:210px; bottom:75px">
                        <option name="not_certified" value="not_certified" id="not_certified"{{ $certification == "not_certified" ? "selected=selected" : ""}}>Not Certified</option>
                        <option name="training" value="training" id="training"{{ $certification == "training" ? "selected=selected" : ""}}>Training</option>
                        <option name="home" value="home" id="home"{{ $certification == "home" ? "selected=selected" : ""}}>Home</option>
                        <option name="visit" value="visit" id="visit"{{ $certification == "visit" ? "selected=selected" : ""}}>Visitor</option>
                        @if (Auth::user()->permissions >= 4)
                        <option name="instructor" value="instructor" id="instructor"{{ $certification == "instructor" ? "selected=selected" : ""}}>Instructor</option>
                        @endif
                        </select>
            <div class="modal-footer">
            <button class="btn btn-success" type="submit" href="#">Submit</button>
            </form>
            <button class="btn btn-light" data-dismiss="modal" style="width:300px">Dismiss</button>
            </div>
            </div>
        </div>
    </div>
</div>
    <!--Add Role Modal-->
    <div class="modal fade" id="addRole" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add a Role to User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   <form action="{{route('user.role.add')}}" method="POST" class="form-group">
                       <select class="form-control" name="role">
                           @foreach ($allroles as $r)
                            @if($r->secure == '1')
                                @role('admin')
                               <option value="{{$r->id}}">{{$r->name}}</option>
                            @endrole
                               @else
                                   <option value="{{$r->id}}">{{$r->name}}</option>
                               @endif
                       @endforeach
                       </select>
                       <input type="hidden" name="id" value="{{$user->id}}">
                       @csrf
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Role</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>
                </div>
            </div>
        </div>
    </div>
<!--End Confirm change own permissions modal-->
    <script>
        function displayDeleteModal() {
            $('#deleteModal').modal('show')
        }
    </script>
@stop
