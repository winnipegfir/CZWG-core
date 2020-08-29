@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    <div class="container" style="margin-top: 20px;">
        <a href="{{route('settings.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Settings</a>
        <h1 class="blue-text font-weight-bold mt-2">Staff</h1>
        <hr>
            <button class="btn btn-primary" href="" data-toggle="modal" data-target="#addStaff">Add Staff Member</button>
        <div class="row">
            @foreach ($groups as $g)
        <div class="container">
            <hr>
            <h3 class="blue-text font-weight-bold mt-2">{{$g->name}}</h3>
        </div>
            @foreach ($g->members as $s)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">{{$s->position}} ({{$s->shortform}})</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5>
                                    {{$s->user->fullName('FLC')}}
                                    @if ($s->user->id == 1)
                                    (Vacant)
                                    @endif
                                </h5>
          <form method="POST" action="{{route('settings.staff.editmember', $s->id)}}" id="edit">

                                <label>User ID (CID)</label>
                                <input required type="text" name="cid" class="form-control form-control-sm" value="{{$s->user_id}}">
                            </div>
                            <div class="col">
                                <div class="text-center">
                                    <img src="{{$s->user->avatar()}}" style="width: 75px; height: 75px; margin-bottom: 10px; border-radius: 50%;">
                                </div>
                            </div>
                        </div>
                        <br/>
                        <label>Description</label>
                        <textarea class="form-control" name="description">{{$s->description}}</textarea>
                        <br/>
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="{{$s->email}}">
                        <br/>

                        <div class="row">
                            <div class="col-lg-4">

                                    @csrf
                        <button class="btn btn-sm btn-success" type="submit" href="#">Update</button>


                            </div>
</form>
                            <div class="col-lg-4">
                              <form method="POST" action="{{route('settings.staff.deletemember', $s->id)}}" id="delete">
                                    @csrf
                                    <button class="btn btn-sm btn-danger" type="submit" href="#">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endforeach
        </div>
    </div>
    <!--Add Staff modal-->
    <div class="modal fade" id="addStaff" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="exampleModalLongTitle">Add Staff Member</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                  <div align="center" class="modal-body">
                  <p>To set a position to vacant, set it to user ID 1.</p>
                        <div class="form-group row">
                            <label for="dropdown" class="col-sm-4 col-form-label text-md-right">{{ __('') }}</label>

                            <div class="col-md-12">
                                <form method="POST" action="{{ route('settings.staff.addmember')}}">
                                  <label class="form-control">Select User</label>
                                <select class="custom-select" name="newstaff">
                                  @foreach($users as $user)
                                  <option value="{{ $user->id}}">{{$user->id}} - {{$user->fname}} {{$user->lname}}</option>
                                  @endforeach
                                </select>

                                @if ($errors->has('dropdown'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('dropdown') }}</strong>
                                    </span>
                                @endif
                                <label class="form-control">Position Title</label>
                                <input type="text" class="form-control" name="position">
                                <label class="form-control">Staff Number (ZWG_)</label>
                                <input type="text" class="form-control" name="shortform">
                                <label class="form-control">Select a group</label>
                                <select class="custom-select form-group" name="group">
                                  @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                  @endforeach
                                </select>

                                <td align="center">
                                      @csrf
                                      <button type="submit" class="btn btn-success">Add Staff</button>
                                </td>
                                </form>
                            </div>
                        </div>

                </div>

                <div align="center" class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Dismiss</button></form>
                </div>
            </div>
        </div>
    </div>
    <!--End add Staff modal-->
@stop
