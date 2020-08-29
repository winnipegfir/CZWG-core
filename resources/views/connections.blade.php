@extends('layouts.master')
@section('title', $id.' Connections - Winnipeg FIR ')
@section('description', $id.'\'s user connections.')

@section('content')
    <div class="container py-4">
        <a href="{{url('/roster/'.$id)}}" class="blue-text" style="font-size: 1.2em;"> <i class="fas fa-arrow-left"></i> {{$user->fullName('FL')}}'s profile</a>
        <h1 class="blue-text font-weight-bold mt-2">Monthly Connections for {{$id}}</h1>
        <hr>
        <table id="connectionsTable" class="table dt table-hover">
            <thead>
            <tr>
                <th style="text-align:center; background-color: lightgray;" scope="col"><b>Date</b></th>
                <th style="text-align:center; background-color: lightgray;" scope="col"><b>Position</b></th>
                <th style="text-align:center; background-color: lightgray;" scope="col"><b>Session Start</b></th>
                <th style="text-align:center; background-color: lightgray;" scope="col"><b>Session End</b></th>
                <th style="text-align:center; background-color: lightgray;" scope="col"><b>Duration</b></th>
            </tr>
            </thead>
            @foreach($connections as $c)
                <tr>
                    <td style="text-align: center">
                        {{$c['date']}}
                    </td>
                    <td style="text-align: center">
                        {{$c['callsign']}}
                    </td>
                    <td style="text-align: center">
                        {{$c['session_start']}}
                    </td>
                    <td style="text-align: center">
                        {{$c['session_end']}}
                    </td>
                    <td style="text-align: center">
                        {{$c['duration']}}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    <script>
        $(document).ready(function() {
            $('#connectionsTable').DataTable({ "order": [[ 0, "desc" ]]});
        } );
    </script>
@stop
