@extends('layouts.master')
@section('content')
    <style>
        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 25%;
        }
    </style>
    <div class="container py-4">
        <h1 class="font-weight-bold blue-text">All about Nate!</h1>
        <hr>
        <img class="center"
             src="https://media-exp1.licdn.com/dms/image/C5603AQHyYM77iig0hA/profile-displayphoto-shrink_200_200/0?e=1599091200&v=beta&t=59YCsGa6fdyXwfvjWr50UL0XFtabA0ESuRZ1b8O3KLA"></img>
        <br>
        <h3 class="blue-text">This fucker really has nothing to do. Here's what he's done here, though!</h3><br>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="cywg-tab" data-toggle="tab" href="#cywg" role="tab" aria-controls="cywg"
                   aria-selected="true">VATSIM</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cypg-tab" data-toggle="tab" href="#cypg" role="tab" aria-controls="cypg"
                   aria-selected="false">Flying</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cyxe-tab" data-toggle="tab" href="#cyxe" role="tab" aria-controls="cyxe"
                   aria-selected="false">Truck Simulator</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cyqt-tab" data-toggle="tab" href="#cyqt" role="tab" aria-controls="cyqt"
                   aria-selected="false">Streaming</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cyqr-tab" data-toggle="tab" href="#cyqr" role="tab" aria-controls="cyqr"
                   aria-selected="false">Youtube</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" id="cywg" role="tabpanel" aria-labelledby="cywg"><br>
                <h5>Nate has been on the Virtual Air Traffic Simulation Network (also known as VATSIM to most people)
                    for over 6 years!</h5>
                <br>
                <p>Nate has {{$atcTime}} hours as a VATSIM controller and {{$pilotTime}} as a pilot! This comes to a total of {{$totalTime}}, Nate really has put in a lot of time eh? He started on the VATSIM network like most people do, flying. During this time, aviation started to get a strong love for all things aviation, which he carries on to this very day. But, one day, he decided that he wanted to enhance his VATSIM experience by becoming a controller, of course. Nate started in the Moncton/Gander FIR because it was where he lives.</p>
                <p>Once Nate was done with Moncton/Gander FIR, he eventually moved over to Winnipeg (where he is today, obviously). During this time, he quickly became an important member in the FIR, as he was a mentor, an instructor and was eventually appointed to the role of Chief Instructor. When he was appointed, Karl Sawatzky (the FIR Chief at the time) said "[Nate] has an impressive knowledge about Air Traffic Services, is an exemplary instructor, and most importantly, a strong leader."</p>
                <p>In late 2016, Nate joined the VATCAN staff team by accepting the VATCAN Events and Communications Director (VATCAN5) role. While he held this position, Nate stayed as an instructor and controller in the Winnipeg FIR. </p>
                <p>In the summer of 2017, Nate was selected for the role of Director of Oceanic Operations (now known as the Gander Oceanic OCA Chief). During his short time during this role, which was less than a year, Nate was instrumential in the development of the OCA, such as re-working training, developing OTS marking sheets, and co-ordinating successful CTP's. The training system that Nate and his team developed is still in use today.</p>
                <p>In mid-2018, Nate accepted the role of being VATCAN's Division Director (VATCAN1). While having some struggles, he completely re-worked the Welcome Team, and started the process in reviving the Montreal FIR.</p>
                <p>Once he stepped down from VATCAN1, he came back to Winnipeg, and became the FIR Chief. Ever since, the FIR has continued to go from strength to strength, Nate has done things such as re-branding the FIR, creating a new and modern Winnipeg website, and re-working the Winnipeg FIR SOP's. Nate is the first Winnipeg Chief ever to bring a whole staff team to Winnipeg, and development of the FIR keeps growing. Nate continues to show great activity, lots of hours controlling and flying, is always in the TeamSpeak, and continues to train students.</p>
            </div>
            <div class="tab-pane fade" id="cypg" role="tabpanel" aria-labelledby="cypg"><br>
                <h5>Nate has been on the Virtual Air Traffic Simulation Network (also known as VATSIM to most people)
                    for over 6 years!</h5>
                <br>
            </div>
            <div class="tab-pane fade" id="cyxe" role="tabpanel" aria-labelledby="cyxe"><br>
                <h5>Nate has been on the Virtual Air Traffic Simulation Network (also known as VATSIM to most people)
                    for over 6 years!</h5>
                <br>
            </div>
            <div class="tab-pane fade" id="cyqt" role="tabpanel" aria-labelledby="cyqt"><br>
                <h5>Nate has been on the Virtual Air Traffic Simulation Network (also known as VATSIM to most people)
                    for over 6 years!</h5>
                <br>
            </div>
            <div class="tab-pane fade" id="cyqr" role="tabpanel" aria-labelledby="cyqr"><br>
                <h5>Nate has been on the Virtual Air Traffic Simulation Network (also known as VATSIM to most people)
                    for over 6 years!</h5>
                <br>
            </div>
        </div>
    </div>
@endsection
