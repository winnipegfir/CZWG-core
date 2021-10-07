@extends('layouts.master')
@section('title', 'PDC - Winnipeg FIR')
@section('content')

    <div class="container py-4">
        <h1 class="font-weight-bold blue-text"><strong>Pre-Departure Clearance</strong></h1>
        <p>Pre-Departure Clearances (PDCs) are issued when the Winnipeg FIR is experiencing high volumes of traffic.</p>
        <p>VATCAN PDC currently uses the private message (PM) communications protocol, with service currently limited to all VATSIM pilot clients. Airports where PDC service is currently provided in the Toronto FIR are Toronto International, Toronto Island, London International, and Ottawa International. Expansion of the service to additional airports will be notified.</p>
        <hr>
        <h3 class="font-weight-bold blue-text">Requesting & Issuing of PDC</h3>
        <p>Pilots can request a Pre-Departure Clearance from ATC by voice or by text - controllers may also elect to only use PDC during high-traffic events, as mentioned above.</p>
        <p>Each PDC is issued via private message - any VATSIM-approved pilot client can receive these messages. The format to a PDC will note similar information normally sent during a standard IFR clearance, such as an active runway, assigned SID, and more.
        <blockquote style="font-size: 1em">PDC | ACA123 YWG | A321/L | RORMA SIDPO DEGVA FELTN OTNIK BOXUM5 | USE SID DUXUS1 | DEPARTURE RUNWAY 18 | DESTINATION CYYZ | CONTACT ATC WITH IDENTIFIER - 647A | - END -</blockquote></p>
        <p>Pilots should then call ATC when ready for push and start, noting the active airport ATIS (if applicable) and the identifier assigned to their flight in the PDC.
        <blockquote style="font-size: 1em">Winnipeg Ground, ACA123, PDC Identifier 647A, ATIS T, ready for push and start.</blockquote></p>
        <p>In a case where a flight plan is invalid, or a controller requires re-routing or any anmendment to a flight plan, the PDC may also include "AMENDED ROUTE". Pilots are expected to closely read over the PDC, as it is an IFR clearance - it should be treated as such.</p>
    </div>

@endsection
