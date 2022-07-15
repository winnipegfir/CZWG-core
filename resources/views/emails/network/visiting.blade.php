@extends('layouts.email')

@section('message-content')
    <style>
        .border {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>

    <h2>Visiting Violations!</h2>
    <p>Here is a list of controllers who have committed less than 50% of their controlling at other facilites in the past month:</p>

    <table style="width: 100%">
        <thead>
        <tr>
            <th class="border">Name</th>
            <th class="border">% on Winnipeg Positions</th>
        </tr>
        </thead>
        @foreach($members as $m)
            <tr>
                <td class="border" style="text-align: center">{{$m['name']}}</td>
                <td class="border" style="text-align: center">{{$m['percentage'] * 100}}%</td>
            </tr>
        @endforeach
    </table>
@endsection

@section('footer-reason-line')
    you are a staff member.
@endsection
