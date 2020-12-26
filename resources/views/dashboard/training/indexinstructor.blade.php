@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')
    @include('includes.trainingMenu')
    <div class="container" style="margin-top: 20px;">
        <h2 class="font-weight-bold blue-text">Instructor Portal</h2>
        <hr>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Your Students
                        </div>

                    <div class="card-body">
                        @if ($yourStudents !== null)
                        <div class="list-group">
                            @foreach ($yourStudents as $student)
                            <a href="{{route('training.students.view', $student->id)}}" class="list-group-item d-flex justify-content-between align-items-center">
                                {{$student->user->fullName('FLC')}}
                                {{-- <i class="text-dark">Session planned at {date}</i> --}}
                                @if ($student->status == 0)
                                <span class="badge badge-success">
                                    <h6 class="p-0 m-0">
                                        Open
                                    </h6>
                                </span>
                                @elseif ($student->status == 4)
                                <span class="badge badge-success">
                                    <h6 class="p-0 m-0">
                                        On Hold
                                    </h6>
                                </span>
                                @endif
                            </a>
                            @endforeach
                        </div>
                        @else
                        No students are allocated to you.
                        @endif
                    </div>
                </div>
            </div>
            <div class="col">
                @if (Auth::check())
                    @if (Auth::user()->permissions >= 4)
                <div class="card">
                    <div class="card-header">
                        Pending Solo Requests
                    </div>

                    <div class="card-body">
                        @if (count($soloreq) < 1)
                            There are no Solo Requests at this time! Check back later.
                        @else

                        @foreach ($soloreq as $solo)

                               <div align="center">
                              Student: {{$solo->student->user->fullName('FLC')}}<br>
                              Position: {{$solo->position}}<br>
                              Instructor: {{$solo->instructor->user->fullName('FL')}}<br>
                                   <form action="{{route('training.solo.process', $solo->student->id)}}" method="POST" class="form-control">
                                       <input type="hidden" name="position" value="{{$solo->position}}">
                                       <select name="approve" class="custom-select form-group">
                                           <option value="1">Approve</option>
                                           <option value="0">Deny</option>
                                       </select>
                                       <button type="submit" class="form-control btn btn-primary">Submit</button>
                                       @csrf
                                   </form>
                           </div>
<br><br><br><br><br>
                            <hr>
                        @endforeach

                        @endif
                    </div>
                </div>
                    @endif
                    @endif
            </div>
        </div>
        <br/>
        <h5>Training Calendar</h5>
        <br>
    </div>
@stop
