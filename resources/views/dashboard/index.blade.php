@extends('layouts.dashboard')
@section('content')
@section('title', 'Your Dashboard WIP')

<style>
    .table {
        display: table;
        width: 100%;
        height 100%;
        table-layout: fixed;
    }

    .row {
        display: table-row;
        background-image: url('https://cdn.discordapp.com/attachments/598024220961931271/820022270177181797/unknown.png') !important;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .col {
        display: table-cell;
    }

    .right {
        background: rgba(1, 49, 98, 0.8);
    }

    .left {
        background-color: #fff;
        width: 30%;
    }
    .card-header {
        background-color: #f2d600;
    }
</style>

<div class="table mb-0">
    <div class="row">
        <div class="col left">
            <h1 align="center" class="font-weight-bold blue-text pt-2">Your Dashboard</h1>
        </div>
        
        <div class="col right p-4">
            <div class="col card p-0" style="width: 33%">
                <div class="card-header">
                    <h1>Your Certification</h1>
                </div>
                <div class="card-body">
                    <h1></h1>
                </div>
            </div>
            <div class="col card p-0" style="width: 33%">
                <div class="card-body">
                    <h1>Training Profile</h1>
                </div>
            </div>
            <div class="col card p-0" style="width: 33%">
                <div class="card-body">
                    <h1>Add More Here</h1>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection