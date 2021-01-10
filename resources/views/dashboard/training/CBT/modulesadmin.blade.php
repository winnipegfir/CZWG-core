@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
  @include('includes.cbtMenu')

    <div class="container" style="margin-top: 20px;">
      <h2 class="font-weight-bold blue-text pb-2">Modules Admin</h2>
        <!--tabs: Modules, Student Progress, Add Module-->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          @if (Auth::user()->permissions >= 2)
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#modules" role="tab" aria-controls="modules" aria-selected="true">Modules</a>
            </li>
            @endif
            @if (Auth::check() && Auth::user()->permissions >= 4)
            <li class="nav-item">
                <a class="nav-link" id="addmodule-tab" data-toggle="tab" href="#addmodule" role="tab" aria-controls="addmodule" aria-selected="false">Add Module</a>
            </li>
            @endif
          </ul>
          <!--TAB 1 : Modules : viewable by perm level 2 and up-->
          <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="modules" role="tabpanel" aria-labelledby="home-tab"><br>
        <!--List of modules in table format (click to view the module), include name, # of lessons, created by who. Edit and Delete buttons for Staff/Admin-->
        <table id="dataTable" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Module</th>
                    <th scope="col">Created on</th>
                    <th scope="col">Last Update By</th>
                    <th scope="col">Exam Assign</th>
                    @if (Auth::user()->permissions >=4)
                    <th scope="col">Actions</th>
                    @endif
                </tr>
            </thead>
            @if (count($modules) < 1)
            <font class="font-weight-bold">** There are no modules!</b></font>
            @else
            <tbody>
            @foreach ($modules as $module)
            <tr>
                <th scope="row">{{$module->name}}</th>
                <td>
                    {{$module->created_at}}
                </td>
                <td>
                    {{$module->user->fullName('FL')}}
                </td>
                <td>
                    @if ($module->cbt_exam_id == NULL)
                        No Exam
                    @else
                        <!--Make relation to show exam name work--!>
                    {{$module->cbtexam->name}}
                    @endif
                </td>
              @if (Auth::user()->permissions >=3)
              <td>
                <a href="{{route('cbt.module.edit', $module->id)}}">View/Edit</a> @endif |
                  @if (Auth::user()->permissions >= 4)
                  <a href="{{route('cbt.module.delete', $module->id)}}">Delete</a>
              </td>
              @endif

            </tr>
            @endforeach
            @endif
        </table>
      </div>

          <!--TAB 3: Add Module : Viewable by perm level 4 and up-->
     <div class="tab-pane fade" id="addmodule" role="tabpanel" aria-labelledby="addmodule-tab"><br>
        <form action="{{route('cbt.module.add')}}" method="POST" class="form-group">
         @csrf
         <label class="form-control">Name of Module</label>
         <input type="text" class="form-control" name="name">
         <label class="form-control">Exam to follow module</label>
         <select class="form-control" name="exam">
             <option value="0">No Exam</option>
             @foreach ($exam as $exam)
                 <option value="{{$exam->id}}">{{$exam->name}}</option>
             @endforeach
         </select>
         <input class="btn btn-success" type="submit" value="Create Module">
         </form>
  </div>
</div>
<br>
@stop
