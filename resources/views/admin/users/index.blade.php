@extends('layouts.master')

@section('navbarprim')

    @parent

@stop

@section('content')

<style>
.um-stat-grid { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.75rem; }
.um-stat-card {
    background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 0.6rem;
    padding: 0.85rem 1.2rem; min-width: 140px; flex: 1;
    display: flex; flex-direction: column; gap: 0.15rem;
}
.um-stat-label { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: rgba(0,0,0,0.38); }
.um-stat-value { font-size: 1.6rem; font-weight: 800; color: #122b44; line-height: 1.1; }
.um-stat-sub { font-size: 0.72rem; color: rgba(0,0,0,0.4); }
</style>

    <div class="container py-4">
        <a href="{{route('dashboard.index')}}" class="blue-text" style="font-size: 1.2em;"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <h1 class="blue-text font-weight-bold mt-2">Users</h1>

        @php
            $permLabels = [
                0 => 'Guest',
                1 => 'Controller / Trainee',
                2 => 'Mentor',
                3 => 'Instructor',
                4 => 'Staff Member',
                5 => 'Administrator',
            ];
        @endphp

        <div class="um-stat-grid">
            <div class="um-stat-card">
                <div class="um-stat-label">Total Users</div>
                <div class="um-stat-value">{{ $users->count() }}</div>
            </div>
            @foreach ($permLabels as $level => $label)
                @php $count = $permissionCounts->get($level, 0); @endphp
                @if ($count > 0)
                <div class="um-stat-card">
                    <div class="um-stat-label">{{ $label }}</div>
                    <div class="um-stat-value">{{ $count }}</div>
                </div>
                @endif
            @endforeach
        </div>

        @if ($users->isEmpty())
            <div class="alert alert-danger">No users found</div>
        @else
            <table id="dataTable" class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">CID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Rating</th>
                    <th scope="col">Permission</th>
                    <th scope="col">Options</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <th scope="row"><b>{{$user->id}}</b></th>
                        <td>{{$user->fullName('FL')}}</td>
                        <td>{{$user->rating->getShortName()}}</td>
                        <td>{{$user->permissions()}}</td>
                        <td>
                            <a class="blue-text" href="{{route('users.viewprofile', $user->id)}}"><i class="fa fa-eye"></i> View User</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <script>
        $(document).ready(function() {
            $.fn.dataTable.enum(['C1', 'C3', 'I1', 'I3', 'SUP', 'ADM'])
            $('#dataTable').DataTable( {
                "order": [[ 0, "asc" ]]
            } );
        } );
    </script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/enum.js"></script>
@stop
