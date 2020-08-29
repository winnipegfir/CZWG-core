@extends('layouts.email')

@section('to-line', 'Hi '. $user->fullName('FLC') . ',')


@section('message-content')
    The Winnipeg FIR Staff have changed your status on the controller roster to:<br/>
    @switch ($controller->status)
        @case ('certified')
            <b>Certification:</b>&nbsp;CZWG Certified
        @break
        @case ('not_certified')
            <b>Certification:</b>&nbsp;Not Certified to Control
        @break
        @case ('training')
            <b>Certification:</b>&nbsp;In Training
        @break
        @case ('home')
        <b>Certification:</b>&nbsp;CZWG Controller
        @break
        @case ('visit')
        <b>Certification:</b>&nbsp;CZWG Visiting Controller
        @break
        @case ('instructor')
            <b>Certification:</b>&nbsp;CZWG Instructor
        @break
    @endswitch
    <br/>
    @if ($controller->active == 1)
        <b>Activity:</b>&nbsp;Active
    @else
        <b>Activity:</b>&nbsp;Inactive
    @endif
    <br/>
    <p>
        If you believe there is an error, or have any questions, please do not hesitate to open up a support ticket.
    </p>
@stop

@section('footer-to-line', $user->fullName('FLC').' ('.$user->email.')')

@section('footer-reason-line')
your status with the Winnipeg FIR has been changed.
@endsection
