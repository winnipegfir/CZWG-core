@extends('layouts.master')
@section('title', 'Your Feedback')
@section('content')


<div class="container py-4">
    <h1 class="font-weight-bold blue-text">Your Words</h1>
        <h5>This is just some of the great feedback we've received from the thousands of VATSIM users that have flown through Winnipeg!</h5>
            <hr>
                <div class="card">
                    <div class="card-body" style="background-color:#013162; color:#ffffff;">
                        <p>"James, was very friendly and welcoming and spoke very clearly while giving instructions, A+ job James!"</p>
                        <h5><strong>Controller:</strong> James White</h5>
                    </div>
                </div>
            <br>
                <div class="card">
                    <div class="card-body" style="background-color:#013162; color:#ffffff;">
                        <p>"Great and friendly controlling throughtout the Winnipeg FIR crossing, thanks!"</p>
                        <h5><strong>Controller:</strong> Dario Marnika</h5>
                    </div>
                </div>
            <br>
                <div class="card">
                    <div class="card-body" style="background-color:#013162; color:#ffffff;">
                        <p>"Did my very first VATSIM flight with Winnipeg Tower. Great experience! The controller was very helpful and patient to me, he also talked slower to a new member like me so that I could grasp and understand everything he said. Looking forward to flying with Winnipeg Tower again."</p>
                        <h5><strong>Controller:</strong> Justin Martin</h5>
                    </div>
                </div>
            <br>
                <div class="card">
                    <div class="card-body" style="background-color:#013162; color:#ffffff;">
                        <p>"Super friendly and got me a more direct route after coordinating with Montreal Center. I'll be looking forward to sharing the friendly skies with him again :)"</p>
                            <h5><strong>Controller:</strong> Justin Martin</h5>
                    </div>
                </div>
            <br>
                <div class="card">
                    <div class="card-body" style="background-color:#013162; color:#ffffff;">
                        <p>"Departed out of Winnipeg bound for Toronto as WJA4220. Nate was by far one of the best controller i've come across on the network. He was super helpful with every question I had during my flight within Winnipeg FIR."</p>
                            <h5><strong>Controller:</strong> Nate Power</h5>
                    </div>
                </div>
            <br>
                <div class="card">
                    <div class="card-body" style="background-color:#013162; color:#ffffff;">
                        <p>"This was my first experience using VATSIM. I mean my very first time using any live ATC. Jamie was a pleasure to work with and helped work my communication with ATC as well as cleared all my questions perfectly."</p>
                            <h5><strong>Controller:</strong> Jamie Harding</h5>
                    </div>
                </div>
            <br>
                <div class="card">
                    <div class="card-body" style="background-color:#013162; color:#ffffff;">
                        <p>"Top notch controller. Luke was able to figure out separation when previous sectors struggled. He made the YVR-YYZ event quick, painless, and easy."</p>
                            <h5><strong>Controller:</strong> Luke Wightman</h5>
                    </div>
                </div>
            
</div>

<div class="container py-2">
    <p>Here at Winnipeg, feedback is something we have always valued. Your suggestions, criticisms and hints make us better controllers! If you haven't yet sent us some feedback about a recent experience you've had on the network, you can do so <a href = "/feedback">HERE</a>.
</div>

@endsection