@extends('layouts.master')
@section('title', 'A Letter from the Chief - ' . $year)
@section('content')

    <div class="container py-4">
        <a href="{{route('index')}}" class="blue-text" style="font-size: 1.2em"> <i class="fas fa-arrow-left"></i> Back to the Winnipeg FIR</a>
        <br>

        @yield('letter')

        <br>
        <div class="row pl-2">
            @if(view()->exists('yearend.yearend'.$year-1))
                <a class="btn btn-primary" href="/yearend/{{$year-1}}"><i class="fas fa-arrow-left"></i> Read Nate's {{$year-1}} Letter</a>
            @endif
            @if(view()->exists('yearend.yearend'.$year+1))
                <a class="btn btn-primary" href="/yearend/{{$year+1}}">Read Nate's {{$year+1}} Letter <i class="fas fa-arrow-right"></i></a>
            @endif
        </div>
    </div>
@endsection
