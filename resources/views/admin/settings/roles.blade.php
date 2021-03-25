@extends('layouts.master')
@section('content')
    <div class="container py-4">
        <a href="{{route('settings.index')}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> Settings</a>
        <h1 class="blue-text font-weight-bold mt-2">User Roles <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newRole" style="float: right;">Add New Role</button></h1>
        <hr>
        <p>Here you can add and modify roles that can be attached to users.</p>
        @if (count($roles) < 1)
            <p>No Roles Added Yet!</p>
        @else
            <table class="table dt table-hover">
                <thead>
                <tr>
                    <th scope="col">Role Name</th>
                    <th scope="col">Role Assign Protection</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <th scope="row">{{$role->name}}</th>
                        <td>
                            @if($role->protected == '0')
                                Instructors and above
                            @elseif($role->protected == '1')
                                Staff and above
                            @elseif($role->protected == '2')
                                Admin Only
                            @endif
                        </td>
                        <td>
                            <a type="button" class="btn btn-sm btn-primary" style="color: #ff6161" href="{{route('roles.delete', $role->name)}}"><i class="fa fa-times"></i> Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
        <br/>
    </div>
    <script>
        $(document).ready(function() {
            $('.table.dt').DataTable();
        } );
    </script>
    <div class="modal fade" id="newRole" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add a new User Role</h5>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('roles.add')}}" class="form-group">
                        @csrf
                        <div class="form-group">
                            <label>Role Name</label>
                            <input class="form-control" name="name"></input>
                        </div>
                        <div class="form-group">
                            <label>Protect the role?</label>
                            <select name="secure" class="form-control">
                                <option value="0">Instructor and above can Assign Role</option>
                                <option value="1">Staff and above can Assign Role</option>
                                <option value="2">Only Admin can Assign Role</option>
                            </select>
                        </div>


                </div>
                <div class="modal-footer">
                    <button class="btn btn-success form-control" type="submit" href="#">Add Role</button>
                    <button class="btn btn-light" data-dismiss="modal" style="width:375px">Dismiss</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
